<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academics extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Academic_year_model');
        $this->load->model('Academic_group_model');
        $this->load->model('Class_model');
        $this->load->model('Division_model');
        $this->load->model('Section_model');
        $this->load->model('Subject_model');
        $this->load->model('Staff_model');
        $this->load->model('Class_teacher_model');
        $this->load->model('Subject_teacher_model');
        $this->load->model('Period_model');
        $this->load->model('Timetable_model');
        $this->load->model('Academic_calendar_model');
        $this->load->model('Setting_model');
        $this->load->library('form_validation');
    }

    /* =========================================================================
       1. Academic Management Overview
       ========================================================================= */
    public function index()
    {
        $this->overview();
    }

    public function overview()
    {
        $this->require_permission('academics.view');
        $active_year = $this->Academic_year_model->get_active();
        $year_id     = $active_year ? (int)$active_year->academic_year_id : NULL;

        $total_classes     = $this->db->where('status', 1)->where('is_deleted', 'n')->count_all_results('tbl_classes');
        $total_divisions   = $this->db->where('status', 1)->where('is_deleted', 'n')->count_all_results('tbl_divisions');
        $total_sections    = $total_divisions;
        $total_subjects    = $this->db->where('status', 1)->where('is_deleted', 'n')->count_all_results('tbl_subjects');
        $assigned_teachers = $this->db->where('class_teacher_id IS NOT NULL', NULL, FALSE)->where('status', 1)->where('is_deleted', 'n')->count_all_results('tbl_divisions');

        // Classes summary with divisions and students
        $classes_summary = $this->db->query("
            SELECT c.class_id, c.class_name, 
                   (SELECT COUNT(*) FROM tbl_divisions d WHERE d.class_id = c.class_id AND d.status = 1 AND d.is_deleted = 'n') AS division_count,
                   (SELECT COUNT(*) FROM tbl_students st WHERE st.class_id = c.class_id AND st.status = 1 AND st.is_deleted = 'n') AS student_count
            FROM tbl_classes c
            WHERE c.status = 1 AND c.is_deleted = 'n'
            ORDER BY c.class_id ASC
        ")->result();

        // Maintain backward compatibility for any views or helpers
        foreach ($classes_summary as &$cs) {
            $cs->section_count = $cs->division_count;
        }

        // Calendar Highlights
        $calendar_events = $this->db->where('status', 1)->order_by('start_date', 'ASC')->limit(6)->get('tbl_academic_calendar')->result();

        $this->render('pages/academics/overview', array(
            'title'             => 'Academic Management Overview',
            'page_key'          => 'academics',
            'breadcrumb'        => array('Academic Management', 'Overview'),
            'active_year'       => $active_year,
            'total_classes'     => $total_classes,
            'total_divisions'   => $total_divisions,
            'total_sections'    => $total_sections,
            'total_subjects'    => $total_subjects,
            'assigned_teachers' => $assigned_teachers,
            'classes_summary'   => $classes_summary,
            'calendar_events'   => $calendar_events,
        ));
    }

    /* =========================================================================
       1. Academic Year Management
       ========================================================================= */
    public function years()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->require_permission('academics.create');
                $this->form_validation->set_rules('year_name', 'Year Name', 'required|trim');
                $this->form_validation->set_rules('start_date', 'Start Date', 'required');
                $this->form_validation->set_rules('end_date', 'End Date', 'required|callback_validate_academic_dates');

                if ($this->form_validation->run() === TRUE) {
                    $year_name  = trim(preg_replace('/\s+/', ' ', (string)$this->input->post('year_name')));
                    $start_date = $this->input->post('start_date');
                    $end_date   = $this->input->post('end_date');

                    // Strict date order validation: End Date must be greater than Start Date
                    if (strtotime($end_date) <= strtotime($start_date)) {
                        $this->session->set_flashdata('error', 'End Date must be greater than Start Date.');
                        redirect('academics/years');
                        return;
                    }

                    // Prevent duplicate academic year creation before INSERT
                    if ($this->Academic_year_model->is_year_name_exists($year_name)) {
                        $this->session->set_flashdata('error', "Academic year {$year_name} already exists.");
                        redirect('academics/years');
                        return;
                    }

                    $isActive = ($this->input->post('is_active') == '1') ? 1 : 0;
                    $insert_id = $this->Academic_year_model->insert(array(
                        'year_name'  => $year_name,
                        'start_date' => $start_date,
                        'end_date'   => $end_date,
                        'is_active'  => $isActive,
                        'status'     => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ));

                    if ($insert_id === FALSE) {
                        $this->session->set_flashdata('error', "Academic year {$year_name} already exists.");
                    } else {
                        $this->session->set_flashdata('success', 'Academic Year added successfully!');
                    }
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = (int)$this->input->post('academic_year_id');

                $this->form_validation->set_rules('academic_year_id', 'Academic Year ID', 'required|integer');
                $this->form_validation->set_rules('year_name', 'Year Name', 'required|trim');
                $this->form_validation->set_rules('start_date', 'Start Date', 'required');
                $this->form_validation->set_rules('end_date', 'End Date', 'required|callback_validate_academic_dates');

                if ($this->form_validation->run() === TRUE) {
                    $year_name  = trim(preg_replace('/\s+/', ' ', (string)$this->input->post('year_name')));
                    $start_date = $this->input->post('start_date');
                    $end_date   = $this->input->post('end_date');

                    // Strict date order validation: End Date must be greater than Start Date
                    if (strtotime($end_date) <= strtotime($start_date)) {
                        $this->session->set_flashdata('error', 'End Date must be greater than Start Date.');
                        redirect('academics/years');
                        return;
                    }

                    // Check duplicate for edit (excluding current record)
                    if ($this->Academic_year_model->is_year_name_exists($year_name, $id)) {
                        $this->session->set_flashdata('error', "Academic year {$year_name} already exists.");
                        redirect('academics/years');
                        return;
                    }

                    $isActive = ($this->input->post('is_active') == '1') ? 1 : 0;
                    $updated = $this->Academic_year_model->update($id, array(
                        'year_name'  => $year_name,
                        'start_date' => $start_date,
                        'end_date'   => $end_date,
                        'is_active'  => $isActive,
                        'updated_at' => date('Y-m-d H:i:s')
                    ));

                    if ($updated === FALSE) {
                        $this->session->set_flashdata('error', "Academic year {$year_name} already exists.");
                    } else {
                        $this->session->set_flashdata('success', 'Academic Year updated successfully!');
                    }
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            }
            redirect('academics/years');
        }

        $years = $this->Academic_year_model->get_all();

        $this->render('pages/academics/years', array(
            'title'      => 'Academic Years',
            'page_key'   => 'academic-years',
            'breadcrumb' => array('Academic Management', 'Academic Year'),
            'years'      => $years,
        ));
    }

    public function switch_year()
    {
        $year_id = (int)($this->input->post('academic_year_id') ?: $this->input->get('academic_year_id'));

        if (!can_change_academic_year()) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'          => false,
                        'message'         => 'You do not have permission to change the active academic year.',
                        'csrf_token_name' => $this->security->get_csrf_token_name(),
                        'csrf_hash'       => $this->security->get_csrf_hash(),
                    ]));
                return;
            }
            $this->session->set_flashdata('error', 'You do not have permission to change the academic year.');
            $redirect_url = $this->input->post('redirect_url') ?: ($this->input->server('HTTP_REFERER') ?: 'dashboard');
            redirect($redirect_url);
            return;
        }

        if ($year_id <= 0) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'          => false,
                        'message'         => 'Please provide a valid academic year ID.',
                        'csrf_token_name' => $this->security->get_csrf_token_name(),
                        'csrf_hash'       => $this->security->get_csrf_hash(),
                    ]));
                return;
            }
            $this->session->set_flashdata('error', 'Please provide a valid academic year ID.');
            $redirect_url = $this->input->post('redirect_url') ?: ($this->input->server('HTTP_REFERER') ?: 'dashboard');
            redirect($redirect_url);
            return;
        }

        $year = $this->Academic_year_model->get_by_id($year_id);
        if (!$year || $year->is_deleted === 'y' || (int)$year->status !== 1) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'          => false,
                        'message'         => 'The selected academic year is invalid or inactive.',
                        'csrf_token_name' => $this->security->get_csrf_token_name(),
                        'csrf_hash'       => $this->security->get_csrf_hash(),
                    ]));
                return;
            }
            $this->session->set_flashdata('error', 'Invalid academic year selected.');
            $redirect_url = $this->input->post('redirect_url') ?: ($this->input->server('HTTP_REFERER') ?: 'dashboard');
            redirect($redirect_url);
            return;
        }

        set_current_academic_year($year_id);
        $selected_date = get_academic_year_default_date($year);

        if ($this->input->is_ajax_request()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'             => true,
                    'message'            => 'Active academic year switched to ' . $year->year_name,
                    'academic_year_id'   => (int)$year->academic_year_id,
                    'academic_year_name' => $year->year_name,
                    'year_name'          => $year->year_name,
                    'start_date'         => $year->start_date,
                    'end_date'           => $year->end_date,
                    'selected_date'      => $selected_date,
                    'csrf_token_name'    => $this->security->get_csrf_token_name(),
                    'csrf_hash'          => $this->security->get_csrf_hash(),
                ]));
            return;
        }

        $this->session->set_flashdata('success', 'Academic year context changed to ' . $year->year_name);
        $redirect_url = $this->input->post('redirect_url') ?: ($this->input->server('HTTP_REFERER') ?: 'dashboard');
        redirect($redirect_url);
    }

    public function set_active_year($id = NULL)
    {
        $this->require_permission('academics.edit');
        if (!empty($id)) {
            $this->Academic_year_model->set_active($id);
            set_current_academic_year((int)$id);
            $this->session->set_flashdata('success', 'Active academic session updated!');
        }
        redirect('academics/years');
    }

    public function delete_year($id = NULL)
    {
        $this->require_permission('academics.delete');
        $id = (int)$id;
        if ($id <= 0) {
            $this->session->set_flashdata('error', 'Invalid Academic Year selected.');
            redirect('academics/years');
            return;
        }

        $year = $this->Academic_year_model->get_by_id($id);
        if (!$year) {
            $this->session->set_flashdata('error', 'Academic Year not found.');
            redirect('academics/years');
            return;
        }

        // Active Academic Year must NOT be deleted
        if ((int)$year->is_active === 1) {
            $this->session->set_flashdata('error', 'Cannot delete the active academic year. Please set another academic year as active first.');
            redirect('academics/years');
            return;
        }

        // Check whether there are dependent records in any child tables
        $dependencies = $this->Academic_year_model->get_dependencies($id);
        if (!empty($dependencies)) {
            $details = [];
            foreach ($dependencies as $label => $count) {
                $details[] = "{$label}: {$count}";
            }
            $msg = "Cannot delete this academic year because it is being used by existing records (" . implode(', ', $details) . "). Please remove or reassign the dependent records first.";
            $this->session->set_flashdata('error', $msg);
            redirect('academics/years');
            return;
        }

        // Permanently delete from database when no dependencies exist
        $deleted = $this->Academic_year_model->permanent_delete($id);
        if ($deleted) {
            $this->session->set_flashdata('success', 'Academic Year permanently deleted.');
        } else {
            $this->session->set_flashdata('error', 'Cannot delete this academic year because it is being used by existing records. Please remove or reassign the dependent records first.');
        }
        redirect('academics/years');
    }

    /**
     * Form validation callback: Verify that End Date is strictly greater than Start Date.
     *
     * @param string $end_date
     * @return bool
     */
    public function validate_academic_dates($end_date)
    {
        $start_date = $this->input->post('start_date');
        if (!empty($start_date) && !empty($end_date)) {
            if (strtotime($end_date) <= strtotime($start_date)) {
                $this->form_validation->set_message('validate_academic_dates', 'End Date must be greater than Start Date.');
                return FALSE;
            }
        }
        return TRUE;
    }

    /* =========================================================================
       1B. Academic Groups Management (Super Admin Only for Management)
       ========================================================================= */
    public function academic_groups()
    {
        $this->require_permission('academics.view');
        $is_super_admin = $this->rbac->is_super_admin();

        if ($this->input->method() === 'post') {
            if (!$is_super_admin) {
                show_error('Access restricted. Only Super Admin can modify Academic Groups.', 403, '403 Forbidden');
                return;
            }

            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->form_validation->set_rules('group_name', 'Group Name', 'required|trim');
                if ($this->form_validation->run() === TRUE) {
                    $name = trim($this->input->post('group_name'));
                    if ($this->Academic_group_model->check_duplicate($name)) {
                        $this->session->set_flashdata('error', 'Academic Group "' . $name . '" already exists.');
                    } else {
                        $this->Academic_group_model->insert(array(
                            'group_name'    => $name,
                            'description'   => $this->input->post('description', TRUE),
                            'display_order' => (int)$this->input->post('display_order') ?: 0,
                            'status'        => 1
                        ));
                        $this->session->set_flashdata('success', 'Academic Group created successfully!');
                    }
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $id = (int)$this->input->post('academic_group_id');
                $this->form_validation->set_rules('group_name', 'Group Name', 'required|trim');
                if ($this->form_validation->run() === TRUE) {
                    $name = trim($this->input->post('group_name'));
                    if ($this->Academic_group_model->check_duplicate($name, $id)) {
                        $this->session->set_flashdata('error', 'Academic Group "' . $name . '" already exists.');
                    } else {
                        $this->Academic_group_model->update($id, array(
                            'group_name'    => $name,
                            'description'   => $this->input->post('description', TRUE),
                            'display_order' => (int)$this->input->post('display_order') ?: 0,
                        ));
                        $this->session->set_flashdata('success', 'Academic Group updated successfully!');
                    }
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            }
            redirect('academics/academic_groups');
            return;
        }

        $groups = $this->Academic_group_model->get_all(true);

        $this->render('pages/academics/academic_groups', array(
            'title'          => 'Academic Groups',
            'page_key'       => 'academic-groups',
            'breadcrumb'     => array('Academic Management', 'Academic Groups'),
            'groups'         => $groups,
            'is_super_admin' => $is_super_admin,
        ));
    }

    public function delete_academic_group($id = NULL)
    {
        if (!$this->rbac->is_super_admin()) {
            show_error('Access restricted to Super Admin only.', 403, '403 Forbidden');
            return;
        }
        if (!empty($id)) {
            $this->Academic_group_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Academic Group deactivated.');
        }
        redirect('academics/academic_groups');
    }

    public function toggle_group_status($id = NULL, $status = 1)
    {
        if (!$this->rbac->is_super_admin()) {
            show_error('Access restricted to Super Admin only.', 403, '403 Forbidden');
            return;
        }
        if (!empty($id)) {
            $this->Academic_group_model->set_status($id, (int)$status);
            $msg = (int)$status === 1 ? 'Academic Group enabled.' : 'Academic Group disabled.';
            $this->session->set_flashdata('success', $msg);
        }
        redirect('academics/academic_groups');
    }

    /**
     * AJAX endpoint: Get standard allowed class options for an Academic Group from DB.
     */
    public function ajax_get_group_classes()
    {
        $this->require_permission('academics.view');
        $group_id = (int)$this->input->get_post('academic_group_id');
        $classes = $group_id ? $this->Academic_group_model->get_allowed_classes($group_id) : [];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'          => true,
                'classes'         => $classes,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * AJAX endpoint: Get active classes configured under an Academic Group.
     */
    public function ajax_get_classes_by_group()
    {
        $this->require_permission('academics.view');
        $group_id = (int)$this->input->get_post('academic_group_id');
        $year_id  = $this->input->get_post('academic_year_id');
        $classes  = $group_id ? $this->Class_model->get_by_group($group_id, $year_id) : $this->Class_model->get_all($year_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'          => true,
                'classes'         => $classes,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /* =========================================================================
       2. Classes Management
       ========================================================================= */
    public function classes()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->require_permission('academics.create');
                $this->form_validation->set_rules('class_name', 'Class Name', 'required|trim');
                if ($this->form_validation->run() === TRUE) {
                    $this->Class_model->insert(array(
                        'academic_year_id'  => $this->input->post('academic_year_id') ?: 1,
                        'academic_group_id' => $this->input->post('academic_group_id') ? (int)$this->input->post('academic_group_id') : NULL,
                        'class_name'        => $this->input->post('class_name'),
                        'class_code'        => $this->input->post('class_code') ?: strtoupper(substr($this->input->post('class_name'), 0, 4)),
                        'capacity'          => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                        'description'       => $this->input->post('description'),
                        'status'            => 1,
                        'created_at'        => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Class added successfully!');
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = $this->input->post('class_id');
                $this->Class_model->update($id, array(
                    'academic_year_id'  => $this->input->post('academic_year_id') ?: 1,
                    'academic_group_id' => $this->input->post('academic_group_id') ? (int)$this->input->post('academic_group_id') : NULL,
                    'class_name'        => $this->input->post('class_name'),
                    'class_code'        => $this->input->post('class_code'),
                    'capacity'          => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                    'description'       => $this->input->post('description'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Class updated successfully!');
            }
            redirect('academics/classes');
        }

        $year_id = $this->input->get('year_id');
        $classes = $this->Class_model->get_all($year_id);
        $years   = $this->Academic_year_model->get_all();

        $groups = $this->Academic_group_model->get_all();
        $this->render('pages/academics/classes', array(
            'title'      => 'Classes',
            'page_key'   => 'classes',
            'breadcrumb' => array('Academic Management', 'Classes'),
            'classes'    => $classes,
            'years'      => $years,
            'groups'     => $groups,
        ));
    }

    public function delete_class($id = NULL)
    {
        $this->require_permission('academics.delete');
        if (!empty($id)) {
            $this->Class_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Class record deactivated.');
        }
        redirect('academics/classes');
    }

    /* =========================================================================
       3. Divisions Management
       ========================================================================= */
    public function divisions()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->require_permission('academics.create');
                $this->form_validation->set_rules('class_id', 'Class', 'required');
                $name_field = $this->input->post('division_name') !== NULL ? 'division_name' : 'section_name';
                $this->form_validation->set_rules($name_field, 'Division Name', 'required|trim');

                if ($this->form_validation->run() === TRUE) {
                    $class_id = $this->input->post('class_id');
                    $division_name = trim($this->input->post($name_field));

                    if ($this->Division_model->check_duplicate($class_id, $division_name)) {
                        $this->session->set_flashdata('error', 'Division "' . $division_name . '" already exists in the selected class.');
                    } else {
                        $this->Division_model->insert(array(
                            'class_id'      => $class_id,
                            'division_name' => $division_name,
                            'room_no'       => $this->input->post('room_no'),
                            'capacity'      => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                            'description'   => $this->input->post('description'),
                            'status'        => 1,
                            'created_at'    => date('Y-m-d H:i:s')
                        ));
                        $this->session->set_flashdata('success', 'Division created successfully!');
                    }
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = $this->input->post('division_id') ?: $this->input->post('section_id');
                $class_id = $this->input->post('class_id');
                $name_field = $this->input->post('division_name') !== NULL ? 'division_name' : 'section_name';
                $division_name = trim($this->input->post($name_field));

                if ($this->Division_model->check_duplicate($class_id, $division_name, $id)) {
                    $this->session->set_flashdata('error', 'Division "' . $division_name . '" already exists in this class.');
                } else {
                    $this->Division_model->update($id, array(
                        'class_id'      => $class_id,
                        'division_name' => $division_name,
                        'room_no'       => $this->input->post('room_no'),
                        'capacity'      => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                        'description'   => $this->input->post('description'),
                        'updated_at'    => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Division updated successfully!');
                }
            }
            redirect('academics/divisions');
        }

        $class_id  = $this->input->get('class_id');
        $divisions = $this->Division_model->get_all($class_id);
        $classes   = $this->Class_model->get_all();

        $hierarchy = $this->Division_model->get_hierarchy(null, $class_id);
        $groups    = $this->Academic_group_model->get_all();
        $this->render('pages/academics/divisions', array(
            'title'      => 'Divisions',
            'page_key'   => 'divisions',
            'breadcrumb' => array('Academic Management', 'Divisions'),
            'divisions'  => $divisions,
            'sections'   => $divisions,
            'classes'    => $classes,
            'hierarchy'  => $hierarchy,
            'groups'     => $groups,
        ));
    }

    public function sections()
    {
        $this->divisions();
    }

    public function delete_division($id = NULL)
    {
        $this->require_permission('academics.delete');
        if (!empty($id)) {
            $this->Division_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Division deactivated.');
        }
        redirect('academics/divisions');
    }

    public function delete_section($id = NULL)
    {
        $this->delete_division($id);
    }

    /**
     * AJAX endpoint: calculate next division letter for a class (starts from 'B')
     */
    public function get_next_division_ajax()
    {
        $this->require_permission('academics.view');
        $class_id = (int)$this->input->get_post('class_id');
        $next_name = 'B';
        if ($class_id > 0) {
            $next_name = $this->Division_model->get_next_division_name($class_id);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'          => true,
                'class_id'        => $class_id,
                'next_division'   => $next_name,
                'next_section'    => $next_name,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    public function get_next_section_ajax()
    {
        return $this->get_next_division_ajax();
    }

    /* =========================================================================
       4. Subjects Management
       ========================================================================= */
    public function subjects()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->require_permission('academics.create');
                $this->form_validation->set_rules('subject_name', 'Subject Name', 'required|trim');
                if ($this->form_validation->run() === TRUE) {
                    $this->Subject_model->insert(array(
                        'class_id'     => $this->input->post('class_id') ?: NULL,
                        'subject_name' => $this->input->post('subject_name'),
                        'subject_code' => $this->input->post('subject_code') ?: strtoupper(substr($this->input->post('subject_name'), 0, 4)),
                        'subject_type' => $this->input->post('subject_type') ?: 'Core',
                        'description'  => $this->input->post('description'),
                        'status'       => 1,
                        'created_at'   => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Subject created successfully!');
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = $this->input->post('subject_id');
                $this->Subject_model->update($id, array(
                    'class_id'     => $this->input->post('class_id') ?: NULL,
                    'subject_name' => $this->input->post('subject_name'),
                    'subject_code' => $this->input->post('subject_code'),
                    'subject_type' => $this->input->post('subject_type') ?: 'Core',
                    'description'  => $this->input->post('description'),
                    'updated_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Subject updated successfully!');
            }
            redirect('academics/subjects');
        }

        $class_id = $this->input->get('class_id');
        $subjects = $this->Subject_model->get_all($class_id);
        $classes  = $this->Class_model->get_all();

        $this->render('pages/academics/subjects', array(
            'title'      => 'Subjects',
            'page_key'   => 'subjects',
            'breadcrumb' => array('Academic Management', 'Subjects'),
            'subjects'   => $subjects,
            'classes'    => $classes,
        ));
    }

    public function delete_subject($id = NULL)
    {
        $this->require_permission('academics.delete');
        if (!empty($id)) {
            $this->Subject_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Subject deactivated.');
        }
        redirect('academics/subjects');
    }

    /* =========================================================================
       5. Class Teachers Assignment
       ========================================================================= */
    public function class_teachers()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('academics.edit');
            $year_id     = (int)$this->input->post('academic_year_id');
            $class_id    = (int)$this->input->post('class_id');
            $division_id = (int)($this->input->post('division_id') ?: $this->input->post('section_id'));
            $staff_id    = (int)$this->input->post('staff_id');

            if (empty($year_id) || empty($class_id) || empty($division_id) || empty($staff_id)) {
                $this->session->set_flashdata('error', 'Please select academic year, class, division, and teacher.');
                redirect('academics/class_teachers');
            }

            // 1. Validate Academic Year exists and is active/not deleted
            $year = $this->Academic_year_model->get_by_id($year_id);
            if (!$year || $year->status != 1 || $year->is_deleted !== 'n') {
                $this->session->set_flashdata('error', 'Invalid Academic Session selected.');
                redirect('academics/class_teachers');
            }

            // 2. Validate Class exists and is not deleted
            $class = $this->Class_model->get_by_id($class_id);
            if (!$class || $class->status != 1 || $class->is_deleted !== 'n') {
                $this->session->set_flashdata('error', 'Invalid Class selected.');
                redirect('academics/class_teachers');
            }

            // 3. Validate Division exists, is not deleted, and belongs to the selected Class
            $division = $this->Division_model->get_by_id($division_id);
            if (!$division || $division->status != 1 || $division->is_deleted !== 'n' || (int)$division->class_id !== $class_id) {
                $this->session->set_flashdata('error', 'Selected Division does not belong to the selected Class.');
                redirect('academics/class_teachers');
            }

            // 4. Validate Teacher exists, is active, and is teaching faculty
            $staff = $this->db->where('staff_id', $staff_id)->where('is_deleted', 'n')->get('tbl_staff')->row();
            if (!$staff || $staff->status != 1 || (strtolower($staff->staff_type) !== 'teacher' && strtolower($staff->category) !== 'teaching' && strtolower($staff->category) !== 'teacher')) {
                $this->session->set_flashdata('error', 'Failed to assign class teacher. Only active teaching faculty can be assigned.');
                redirect('academics/class_teachers');
            }

            $res = $this->Class_teacher_model->assign($year_id, $class_id, $division_id, $staff_id);
            if ($res) {
                $this->session->set_flashdata('success', 'Class Teacher assigned successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to assign class teacher. Only teaching faculty can be assigned.');
            }
            redirect('academics/class_teachers');
        }

        $open_assign   = (string)$this->input->get('open_assign') === '1';
        $req_year_id   = $this->input->get('academic_year_id');
        $req_class_id  = $this->input->get('class_id');
        $req_div_id    = $this->input->get('division_id') ?: $this->input->get('section_id');

        // Check if opening assign modal with valid context
        $modal_open = false;
        $modal_divisions = array();
        if ($open_assign && !empty($req_class_id) && !empty($req_div_id)) {
            $chk_div = $this->Division_model->get_by_id($req_div_id);
            if ($chk_div && (int)$chk_div->class_id === (int)$req_class_id) {
                $modal_open = true;
                $modal_divisions = $this->Division_model->get_all($req_class_id);
            }
        }

        $selected_year_id     = $this->input->get('academic_year_id') ?: '';
        $selected_class_id    = $this->input->get('class_id') ?: '';
        $selected_division_id = ($this->input->get('division_id') ?: $this->input->get('section_id')) ?: '';
        $selected_staff_id    = ($this->input->get('staff_id') ?: $this->input->get('teacher_id')) ?: '';

        // Validate that class belongs to academic year if both are specified
        if (!empty($selected_year_id) && !empty($selected_class_id)) {
            $chk_cls = $this->Class_model->get_by_id($selected_class_id);
            if (!$chk_cls || (int)$chk_cls->academic_year_id !== (int)$selected_year_id) {
                // Class does not belong to the selected academic year: reset dependent filters
                $selected_class_id = '';
                $selected_division_id = '';
            }
        }

        // Backend validation of division filter
        if (!empty($selected_division_id) && !empty($selected_class_id)) {
            $chk_div = $this->Division_model->get_by_id($selected_division_id);
            if (!$chk_div || (int)$chk_div->class_id !== (int)$selected_class_id || $chk_div->status != 1 || $chk_div->is_deleted !== 'n') {
                // Invalid or mismatched division: neutralize to prevent leaking cross-division records
                $selected_division_id = -1;
            }
        } elseif (!empty($selected_division_id) && empty($selected_class_id)) {
            $chk_div = $this->Division_model->get_by_id($selected_division_id);
            if (!$chk_div || $chk_div->status != 1 || $chk_div->is_deleted !== 'n') {
                $selected_division_id = -1;
            }
        }

        if ($open_assign) {
            $filters = array(
                'academic_year_id' => $req_year_id,
                'class_id'         => $req_class_id,
            );
        } else {
            $filters = array(
                'academic_year_id' => $selected_year_id,
                'class_id'         => $selected_class_id,
                'division_id'      => $selected_division_id,
                'section_id'       => $selected_division_id,
                'staff_id'         => $selected_staff_id,
            );
        }

        $assignments = $this->Class_teacher_model->get_all($filters);
        $years       = $this->Academic_year_model->get_all();
        $classes     = $this->Class_model->get_all(!empty($selected_year_id) ? (int)$selected_year_id : 'all');
        $divisions   = $this->Division_model->get_all();
        $teachers    = $this->Staff_model->get_teachers();

        // If a class is selected, divisions in the filter dropdown must correspond strictly to that class
        $filter_divisions = !empty($selected_class_id) ? $this->Division_model->get_all($selected_class_id) : array();

        $this->render('pages/academics/class_teachers', array(
            'title'                => 'Class Teachers',
            'page_key'             => 'class-teachers',
            'breadcrumb'           => array('Academic Management', 'Class Teachers'),
            'assignments'          => $assignments,
            'years'                => $years,
            'classes'              => $classes,
            'divisions'            => $divisions,
            'filter_divisions'     => $filter_divisions,
            'sections'             => $divisions,
            'teachers'             => $teachers,
            'selected_year_id'     => $selected_year_id,
            'selected_class_id'    => $selected_class_id,
            'selected_division_id' => ($selected_division_id == -1 ? '' : $selected_division_id),
            'selected_staff_id'    => $selected_staff_id,
            'selected_teacher_id'  => $selected_staff_id,
            'modal_open'           => $modal_open,
            'modal_year_id'        => $req_year_id,
            'modal_class_id'       => $req_class_id,
            'modal_division_id'    => $req_div_id,
            'modal_divisions'      => $modal_divisions,
        ));
    }

    public function delete_class_teacher($id = NULL)
    {
        $this->require_permission('academics.edit');
        if (!empty($id)) {
            $this->Class_teacher_model->delete($id);
            $this->session->set_flashdata('success', 'Class teacher assignment removed.');
        }
        redirect('academics/class_teachers');
    }

    /* =========================================================================
       6. Subject Teachers Assignment
       ========================================================================= */
    public function subject_teachers()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('academics.edit');
            $year_id     = (int)($this->input->post('academic_year_id') ?: 1);
            $class_id    = (int)$this->input->post('class_id');
            $division_id = (int)($this->input->post('division_id') ?: $this->input->post('section_id'));
            $subject_id  = (int)$this->input->post('subject_id');
            $staff_id    = (int)$this->input->post('staff_id');

            if (empty($class_id) || empty($division_id) || empty($subject_id) || empty($staff_id)) {
                $this->session->set_flashdata('error', 'Please fill all required assignment fields.');
                redirect('academics/subject_teachers');
            }

            // Backend validation
            $year = $this->Academic_year_model->get_by_id($year_id);
            if (!$year || $year->status != 1 || $year->is_deleted !== 'n') {
                $this->session->set_flashdata('error', 'Invalid Academic Session selected.');
                redirect('academics/subject_teachers');
            }

            $class = $this->Class_model->get_by_id($class_id);
            if (!$class || $class->status != 1 || $class->is_deleted !== 'n') {
                $this->session->set_flashdata('error', 'Invalid Class selected.');
                redirect('academics/subject_teachers');
            }

            $division = $this->Division_model->get_by_id($division_id);
            if (!$division || $division->status != 1 || $division->is_deleted !== 'n' || (int)$division->class_id !== $class_id) {
                $this->session->set_flashdata('error', 'Selected Division does not belong to the selected Class.');
                redirect('academics/subject_teachers');
            }

            $subject = $this->Subject_model->get_by_id($subject_id);
            if (!$subject || $subject->status != 1 || (!empty($subject->class_id) && (int)$subject->class_id !== $class_id)) {
                $this->session->set_flashdata('error', 'Selected Subject does not belong to the selected Class.');
                redirect('academics/subject_teachers');
            }

            $staff = $this->db->where('staff_id', $staff_id)->where('is_deleted', 'n')->get('tbl_staff')->row();
            if (!$staff || $staff->status != 1 || (strtolower($staff->staff_type) !== 'teacher' && strtolower($staff->category) !== 'teaching' && strtolower($staff->category) !== 'teacher')) {
                $this->session->set_flashdata('error', 'Failed to assign subject teacher. Only active teaching faculty can be assigned.');
                redirect('academics/subject_teachers');
            }

            $res = $this->Subject_teacher_model->assign($year_id, $class_id, $division_id, $subject_id, $staff_id);
            if ($res) {
                $this->session->set_flashdata('success', 'Subject Teacher assigned successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to assign subject teacher. Only teaching faculty can be assigned.');
            }
            redirect('academics/subject_teachers');
        }

        $raw_year_id = $this->input->get('academic_year_id');
        if ($raw_year_id !== null && $raw_year_id !== '') {
            $selected_year_id = (int)$raw_year_id;
        } elseif ($raw_year_id === '') {
            // Explicitly requested 'All Academic Years'
            $selected_year_id = '';
        } else {
            // Default when not specified in query params:
            // Preserve existing default behavior (Active Academic Year)
            $active_year = $this->Academic_year_model->get_active_year();
            $selected_year_id = $active_year ? (int)$active_year->academic_year_id : (!empty($this->academic_year_id) ? (int)$this->academic_year_id : '');
        }

        $selected_class_id    = $this->input->get('class_id') ?: '';
        $selected_division_id = ($this->input->get('division_id') ?: $this->input->get('section_id')) ?: '';
        $selected_subject_id  = $this->input->get('subject_id') ?: '';
        $selected_staff_id    = ($this->input->get('staff_id') ?: $this->input->get('teacher_id')) ?: '';

        // Backend validation of division filter
        if (!empty($selected_division_id) && !empty($selected_class_id)) {
            $chk_div = $this->Division_model->get_by_id($selected_division_id);
            if (!$chk_div || (int)$chk_div->class_id !== (int)$selected_class_id || $chk_div->status != 1 || $chk_div->is_deleted !== 'n') {
                // Invalid or mismatched division: neutralize to prevent leaking cross-division records
                $selected_division_id = -1;
            }
        } elseif (!empty($selected_division_id) && empty($selected_class_id)) {
            $chk_div = $this->Division_model->get_by_id($selected_division_id);
            if (!$chk_div || $chk_div->status != 1 || $chk_div->is_deleted !== 'n') {
                $selected_division_id = -1;
            }
        }

        // Backend validation of subject filter
        $filter_subjects = array();
        if (!empty($selected_class_id)) {
            $filter_subjects = $this->Subject_model->get_for_class($selected_year_id, (int)$selected_class_id);
            if (!empty($selected_subject_id)) {
                $subject_belongs = false;
                foreach ($filter_subjects as $fs) {
                    if ((int)$fs->subject_id === (int)$selected_subject_id) {
                        $subject_belongs = true;
                        break;
                    }
                }
                if (!$subject_belongs) {
                    $selected_subject_id = -1; // Neutralize mismatched subject filter
                }
            }
        } elseif (!empty($selected_subject_id)) {
            // When no class is selected, subject filtering is not applicable
            $selected_subject_id = -1;
        }

        $filters = array(
            'academic_year_id' => $selected_year_id,
            'class_id'         => $selected_class_id,
            'division_id'      => $selected_division_id,
            'section_id'       => $selected_division_id,
            'subject_id'       => ($selected_subject_id == -1 ? -1 : $selected_subject_id),
            'staff_id'         => $selected_staff_id,
        );

        $assignments = $this->Subject_teacher_model->get_all($filters);
        $years       = $this->Academic_year_model->get_all();
        $classes     = $this->Class_model->get_all();
        $divisions   = $this->Division_model->get_all();
        $teachers    = $this->Staff_model->get_teachers();

        // If a class is selected, divisions in the filter dropdown must correspond to that class
        $filter_divisions = !empty($selected_class_id) ? $this->Division_model->get_all($selected_class_id) : array();

        $this->render('pages/academics/subject_teachers', array(
            'title'                => 'Subject Teachers',
            'page_key'             => 'subject-teachers',
            'breadcrumb'           => array('Academic Management', 'Subject Teachers'),
            'assignments'          => $assignments,
            'years'                => $years,
            'classes'              => $classes,
            'divisions'            => $divisions,
            'filter_divisions'     => $filter_divisions,
            'filter_subjects'      => $filter_subjects,
            'subjects'             => $this->Subject_model->get_all(),
            'teachers'             => $teachers,
            'selected_year_id'     => $selected_year_id,
            'selected_class_id'    => $selected_class_id,
            'selected_division_id' => ($selected_division_id == -1 ? '' : $selected_division_id),
            'selected_subject_id'  => ($selected_subject_id == -1 ? '' : $selected_subject_id),
            'selected_staff_id'    => $selected_staff_id,
            'selected_teacher_id'  => $selected_staff_id,
        ));
    }

    public function delete_subject_teacher($id = NULL)
    {
        $this->require_permission('academics.edit');
        if (!empty($id)) {
            $this->Subject_teacher_model->delete($id);
            $this->session->set_flashdata('success', 'Subject teacher assignment removed.');
        }
        redirect('academics/subject_teachers');
    }

    /* =========================================================================
       7. Timetable & Periods Management
       ========================================================================= */
    public function timetable()
    {
        $this->require_permission('timetable.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('timetable.edit');
            $action = $this->input->post('action');
            if ($action === 'save_entry') {
                $ttId = $this->input->post('timetable_id');
                $entryData = array(
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'class_id'         => $this->input->post('class_id'),
                    'section_id'       => $this->input->post('section_id'),
                    'day'              => $this->input->post('day'),
                    'period_id'        => $this->input->post('period_id'),
                    'subject_id'       => $this->input->post('subject_id'),
                    'teacher_id'       => $this->input->post('teacher_id')
                );

                $result = $this->Timetable_model->save_entry($entryData, $ttId ?: NULL);
                if ($result['success']) {
                    $this->session->set_flashdata('success', 'Timetable period scheduled successfully!');
                } else {
                    $this->session->set_flashdata('error', 'Timetable Conflict: ' . $result['message']);
                }
            } elseif ($action === 'add_period') {
                $this->Period_model->insert(array(
                    'period_name'  => $this->input->post('period_name'),
                    'start_time'   => $this->input->post('start_time'),
                    'end_time'     => $this->input->post('end_time'),
                    'period_order' => $this->input->post('period_order') ? intval($this->input->post('period_order')) : 1,
                    'status'       => 1,
                    'created_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Period slot created successfully!');
            } elseif ($action === 'edit_period') {
                $pId = $this->input->post('period_id');
                $this->Period_model->update($pId, array(
                    'period_name'  => $this->input->post('period_name'),
                    'start_time'   => $this->input->post('start_time'),
                    'end_time'     => $this->input->post('end_time'),
                    'period_order' => $this->input->post('period_order') ? intval($this->input->post('period_order')) : 1,
                    'updated_at'   => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Period slot updated successfully!');
            }
            redirect('academics/timetable?academic_year_id=' . $this->input->post('academic_year_id') . '&class_id=' . $this->input->post('class_id') . '&section_id=' . $this->input->post('section_id'));
        }

        $years    = $this->Academic_year_model->get_all();
        $classes  = $this->Class_model->get_all();
        $sections = $this->Section_model->get_all();
        $periods  = $this->Period_model->get_all();
        $subjects = $this->Subject_model->get_all();
        $teachers = $this->Staff_model->get_teachers();

        $active_year = $this->Academic_year_model->get_active_year();
        $default_year_id = $active_year ? $active_year->academic_year_id : (isset($years[0]) ? $years[0]->academic_year_id : 1);

        $selected_year    = $this->input->get('academic_year_id') ?: $default_year_id;
        $selected_class   = $this->input->get('class_id') ?: (isset($classes[0]) ? $classes[0]->class_id : 1);

        // Filter sections for the selected class
        $class_sections = array();
        foreach ($sections as $s) {
            if ($s->class_id == $selected_class) {
                $class_sections[] = $s;
            }
        }
        $selected_section = $this->input->get('section_id') ?: (isset($class_sections[0]) ? $class_sections[0]->section_id : (isset($sections[0]) ? $sections[0]->section_id : 1));

        $entries = $this->Timetable_model->get_entries(array(
            'academic_year_id' => $selected_year,
            'class_id'         => $selected_class,
            'section_id'       => $selected_section
        ));

        // Build Day x Period matrix
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $grid = array();
        foreach ($days as $d) {
            $grid[$d] = array();
            foreach ($periods as $p) {
                $grid[$d][$p->period_id] = NULL;
            }
        }
        foreach ($entries as $e) {
            if (isset($grid[$e->day]) && array_key_exists($e->period_id, $grid[$e->day])) {
                $grid[$e->day][$e->period_id] = $e;
            }
        }

        $this->render('pages/academics/timetable', array(
            'title'            => 'Timetable',
            'page_key'         => 'timetable',
            'breadcrumb'       => array('Academic Management', 'Timetable'),
            'years'            => $years,
            'classes'          => $classes,
            'sections'         => $sections,
            'class_sections'   => $class_sections,
            'periods'          => $periods,
            'subjects'         => $subjects,
            'teachers'         => $teachers,
            'days'             => $days,
            'grid'             => $grid,
            'selected_year'    => $selected_year,
            'selected_class'   => $selected_class,
            'selected_section' => $selected_section,
        ));
    }

    public function delete_timetable_entry($id = NULL)
    {
        $this->require_permission('timetable.edit');
        $year_id = $this->input->get('academic_year_id') ?: 1;
        $class_id = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');
        if (!empty($id)) {
            $this->Timetable_model->delete_entry($id);
            $this->session->set_flashdata('success', 'Timetable entry removed.');
        }
        redirect('academics/timetable?academic_year_id=' . $year_id . '&class_id=' . $class_id . '&section_id=' . $section_id);
    }

    public function delete_period($id = NULL)
    {
        $this->require_permission('timetable.edit');
        if (!empty($id)) {
            $this->Period_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Period slot deleted.');
        }
        redirect('academics/timetable');
    }

    /* =========================================================================
       8. Academic Calendar Management
       ========================================================================= */
    public function calendar()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->require_permission('academics.create');
                $this->form_validation->set_rules('title', 'Event / Holiday Title', 'required|trim');
                $this->form_validation->set_rules('start_date', 'Start Date', 'required');

                if ($this->form_validation->run() === TRUE) {
                    $this->Academic_calendar_model->insert(array(
                        'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                        'title'            => $this->input->post('title'),
                        'event_type'       => $this->input->post('event_type') ?: 'Event',
                        'start_date'       => $this->input->post('start_date'),
                        'end_date'         => $this->input->post('end_date') ?: $this->input->post('start_date'),
                        'audience'         => $this->input->post('audience') ?: 'Whole School',
                        'venue'            => $this->input->post('venue'),
                        'description'      => $this->input->post('description'),
                        'status'           => 1,
                        'created_at'       => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Academic calendar event added successfully!');
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = $this->input->post('calendar_id');
                $this->Academic_calendar_model->update($id, array(
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'title'            => $this->input->post('title'),
                    'event_type'       => $this->input->post('event_type') ?: 'Event',
                    'start_date'       => $this->input->post('start_date'),
                    'end_date'         => $this->input->post('end_date') ?: $this->input->post('start_date'),
                    'audience'         => $this->input->post('audience') ?: 'Whole School',
                    'venue'            => $this->input->post('venue'),
                    'description'      => $this->input->post('description'),
                    'updated_at'       => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Academic calendar event updated successfully!');
            }

            $redirect_params = array();
            if ($this->input->post('redirect_academic_year')) $redirect_params['academic_year_id'] = $this->input->post('redirect_academic_year');
            if ($this->input->post('redirect_month')) $redirect_params['month'] = $this->input->post('redirect_month');
            if ($this->input->post('redirect_year')) $redirect_params['year'] = $this->input->post('redirect_year');
            if ($this->input->post('redirect_view')) $redirect_params['view_mode'] = $this->input->post('redirect_view');
            $qs = !empty($redirect_params) ? ('?' . http_build_query($redirect_params)) : '';
            redirect('academics/calendar' . $qs);
            return;
        }

        $years = $this->Academic_year_model->get_all();
        $active_year = $this->Academic_year_model->get_active_year();
        $selected_year = (int)($this->input->get('academic_year_id') ?: ($active_year ? $active_year->academic_year_id : 1));
        $selected_type = trim($this->input->get('event_type') ?: '');
        $selected_month = (int)($this->input->get('month') ?: date('n'));
        $selected_cal_year = (int)($this->input->get('year') ?: date('Y'));
        $view_mode = trim($this->input->get('view_mode') ?: ($this->input->get('view') ?: ''));

        if ($selected_month < 1 || $selected_month > 12) {
            $selected_month = (int)date('n');
        }
        if ($selected_cal_year < 1970 || $selected_cal_year > 2099) {
            $selected_cal_year = (int)date('Y');
        }

        $filters = array(
            'academic_year_id' => $selected_year,
            'event_type'       => $selected_type,
        );

        $events = $this->Academic_calendar_model->get_all($filters);
        $upcoming = $this->Academic_calendar_model->get_upcoming(5, $selected_year);
        $settings = $this->Setting_model->get_settings();
        $selected_year_obj = $this->Academic_year_model->get_by_id($selected_year);

        $this->render('pages/academics/calendar', array(
            'title'             => 'Academic Calendar',
            'page_key'          => 'academic-calendar',
            'breadcrumb'        => array('Academic Management', 'Academic Calendar'),
            'events'            => $events,
            'upcoming'          => $upcoming,
            'years'             => $years,
            'selected_year'     => $selected_year,
            'selected_year_obj' => $selected_year_obj,
            'selected_type'     => $selected_type,
            'selected_month'    => $selected_month,
            'selected_cal_year' => $selected_cal_year,
            'view_mode'         => $view_mode,
            'settings'          => $settings,
        ));
    }

    public function delete_calendar_event($id = NULL)
    {
        $this->require_permission('academics.delete');
        if (!empty($id)) {
            $this->Academic_calendar_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Calendar event removed.');
        }

        $redirect_params = array();
        if ($this->input->get('academic_year_id')) $redirect_params['academic_year_id'] = $this->input->get('academic_year_id');
        if ($this->input->get('month')) $redirect_params['month'] = $this->input->get('month');
        if ($this->input->get('year')) $redirect_params['year'] = $this->input->get('year');
        if ($this->input->get('view_mode')) $redirect_params['view_mode'] = $this->input->get('view_mode');
        $qs = !empty($redirect_params) ? ('?' . http_build_query($redirect_params)) : '';
        redirect('academics/calendar' . $qs);
    }

    /**
     * Download / Print Academic Calendar PDF
     */
    public function calendar_pdf()
    {
        $this->require_permission('academics.view');
        $active_year = $this->Academic_year_model->get_active_year();
        $selected_year_id = (int)($this->input->get('academic_year_id') ?: ($active_year ? $active_year->academic_year_id : 1));
        $selected_year = $this->Academic_year_model->get_by_id($selected_year_id);
        if (!$selected_year) {
            $selected_year = $active_year;
            $selected_year_id = $selected_year ? (int)$selected_year->academic_year_id : 1;
        }

        $settings = $this->Setting_model->get_settings();

        // Logo resolution
        $logo_url = '';
        if ($settings && !empty($settings->logo)) {
            if (file_exists(FCPATH . 'uploads/settings/' . $settings->logo)) {
                $logo_url = base_url('uploads/settings/' . $settings->logo);
            } elseif (file_exists(FCPATH . 'uploads/id_card/' . $settings->logo)) {
                $logo_url = base_url('uploads/id_card/' . $settings->logo);
            }
        }
        if (empty($logo_url) && $settings && !empty($settings->school_logo)) {
            if (file_exists(FCPATH . 'uploads/id_card/' . $settings->school_logo)) {
                $logo_url = base_url('uploads/id_card/' . $settings->school_logo);
            } elseif (file_exists(FCPATH . 'uploads/settings/' . $settings->school_logo)) {
                $logo_url = base_url('uploads/settings/' . $settings->school_logo);
            }
        }
        if (empty($logo_url) && file_exists(FCPATH . 'assets/logo.png')) {
            $logo_url = base_url('assets/logo.png');
        }

        $events = $this->Academic_calendar_model->get_all(array(
            'academic_year_id' => $selected_year_id
        ));

        // Group events chronologically by month
        $events_by_month = array();
        foreach ($events as $ev) {
            $month_key = date('Y-m', strtotime($ev->start_date));
            if (!isset($events_by_month[$month_key])) {
                $events_by_month[$month_key] = array(
                    'label'     => date('F Y', strtotime($ev->start_date)),
                    'month_num' => (int)date('n', strtotime($ev->start_date)),
                    'year_num'  => (int)date('Y', strtotime($ev->start_date)),
                    'events'    => array()
                );
            }
            $events_by_month[$month_key]['events'][] = $ev;
        }

        // Calculate summary counters
        $total_events     = count($events);
        $total_holidays   = count(array_filter($events, function($e) { return $e->event_type === 'Holiday'; }));
        $total_exams      = count(array_filter($events, function($e) { return $e->event_type === 'Exam'; }));
        $total_breaks     = count(array_filter($events, function($e) { return $e->event_type === 'Term Break'; }));
        $total_activities = count(array_filter($events, function($e) { return in_array($e->event_type, array('Activity', 'Event')); }));
        $total_meetings   = count(array_filter($events, function($e) { return $e->event_type === 'Meeting'; }));

        $data = array(
            'title'            => 'Academic Calendar - ' . ($selected_year ? $selected_year->year_name : 'Annual Calendar'),
            'settings'         => $settings,
            'logo_url'         => $logo_url,
            'academic_year'    => $selected_year,
            'events'           => $events,
            'events_by_month'  => $events_by_month,
            'total_events'     => $total_events,
            'total_holidays'   => $total_holidays,
            'total_exams'      => $total_exams,
            'total_breaks'     => $total_breaks,
            'total_activities' => $total_activities,
            'total_meetings'   => $total_meetings,
            'autoprint'        => (int)$this->input->get('autoprint'),
            'autodownload'     => (int)$this->input->get('download')
        );

        $this->load->view('pages/academics/calendar_pdf', $data);
    }

    public function download_calendar_pdf()
    {
        $this->calendar_pdf();
    }

    /* =========================================================================
       9. Dynamic AJAX Dropdowns & Helpers
       ========================================================================= */
    public function ajax_get_divisions($class_id = NULL)
    {
        header('Content-Type: application/json');
        if (empty($class_id)) {
            $class_id = $this->input->get('class_id');
        }
        if (empty($class_id)) {
            echo json_encode(array());
            return;
        }
        $divisions = $this->Division_model->get_all($class_id);
        echo json_encode($divisions);
    }

    public function ajax_get_sections($class_id = NULL)
    {
        $this->ajax_get_divisions($class_id);
    }

    public function ajax_get_subjects($class_id = NULL)
    {
        header('Content-Type: application/json');
        if (empty($class_id)) {
            $class_id = $this->input->get('class_id');
        }
        if (empty($class_id)) {
            echo json_encode(array());
            return;
        }
        $academic_year_id = $this->input->get('academic_year_id');
        $subjects = $this->Subject_model->get_for_class($academic_year_id, (int)$class_id);
        echo json_encode($subjects);
    }

    public function ajax_get_teachers_for_subject()
    {
        header('Content-Type: application/json');
        $year_id    = $this->input->get('academic_year_id') ?: 1;
        $class_id   = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');
        $subject_id = $this->input->get('subject_id');

        $assigned = array();
        if ($class_id && $section_id && $subject_id) {
            $assigned = $this->Subject_teacher_model->get_teachers_by_subject($year_id, $class_id, $section_id, $subject_id);
        }

        // If no assigned teacher for this specific subject/class/section, return all active teachers
        if (empty($assigned)) {
            $teachers = $this->Staff_model->get_teachers();
            echo json_encode(array('source' => 'all', 'teachers' => $teachers));
        } else {
            echo json_encode(array('source' => 'assigned', 'teachers' => $assigned));
        }
    }

    public function ajax_get_timetable_entry($id = NULL)
    {
        header('Content-Type: application/json');
        if (empty($id)) {
            echo json_encode(array('success' => FALSE));
            return;
        }
        $entry = $this->Timetable_model->get_by_id($id);
        if ($entry) {
            echo json_encode(array('success' => TRUE, 'entry' => $entry));
        } else {
            echo json_encode(array('success' => FALSE));
        }
    }

    public function ajax_get_calendar_event($id = NULL)
    {
        header('Content-Type: application/json');
        if (empty($id)) {
            echo json_encode(array('success' => FALSE));
            return;
        }
        $event = $this->Academic_calendar_model->get_by_id($id);
        if ($event) {
            echo json_encode(array('success' => TRUE, 'event' => $event));
        } else {
            echo json_encode(array('success' => FALSE));
        }
    }
}
