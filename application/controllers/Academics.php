<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academics extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Academic_year_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Subject_model');
        $this->load->model('Staff_model');
        $this->load->model('Class_teacher_model');
        $this->load->model('Subject_teacher_model');
        $this->load->model('Period_model');
        $this->load->model('Timetable_model');
        $this->load->model('Academic_calendar_model');
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

        $total_classes  = $this->db->where('status', 1)->count_all_results('tbl_classes');
        $total_sections = $this->db->where('status', 1)->count_all_results('tbl_sections');
        $total_subjects = $this->db->where('status', 1)->count_all_results('tbl_subjects');
        $assigned_teachers = $this->db->where('class_teacher_id IS NOT NULL', NULL, FALSE)->where('status', 1)->count_all_results('tbl_sections');

        // Classes summary with sections and students
        $classes_summary = $this->db->query("
            SELECT c.class_id, c.class_name, 
                   (SELECT COUNT(*) FROM tbl_sections sec WHERE sec.class_id = c.class_id AND sec.status = 1) as section_count,
                   (SELECT COUNT(*) FROM tbl_students st WHERE st.class_id = c.class_id AND st.status = 1) as student_count
            FROM tbl_classes c
            WHERE c.status = 1
            ORDER BY c.class_id ASC
        ")->result();

        // Calendar Highlights
        $calendar_events = $this->db->where('status', 1)->order_by('start_date', 'ASC')->limit(6)->get('tbl_academic_calendar')->result();

        $this->render('pages/academics/overview', array(
            'title'             => 'Academic Management Overview',
            'page_key'          => 'academics',
            'breadcrumb'        => array('Academic Management', 'Overview'),
            'active_year'       => $active_year,
            'total_classes'     => $total_classes,
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
                $this->form_validation->set_rules('end_date', 'End Date', 'required');

                if ($this->form_validation->run() === TRUE) {
                    $isActive = ($this->input->post('is_active') == '1') ? 1 : 0;
                    $this->Academic_year_model->insert(array(
                        'year_name'  => $this->input->post('year_name'),
                        'start_date' => $this->input->post('start_date'),
                        'end_date'   => $this->input->post('end_date'),
                        'is_active'  => $isActive,
                        'status'     => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Academic Year added successfully!');
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = $this->input->post('academic_year_id');
                $isActive = ($this->input->post('is_active') == '1') ? 1 : 0;
                $this->Academic_year_model->update($id, array(
                    'year_name'  => $this->input->post('year_name'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date'   => $this->input->post('end_date'),
                    'is_active'  => $isActive,
                    'updated_at' => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Academic Year updated successfully!');
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
        if (!empty($id)) {
            $this->Academic_year_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Academic Year deactivated.');
        }
        redirect('academics/years');
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
                        'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                        'class_name'       => $this->input->post('class_name'),
                        'class_code'       => $this->input->post('class_code') ?: strtoupper(substr($this->input->post('class_name'), 0, 4)),
                        'capacity'         => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                        'description'      => $this->input->post('description'),
                        'status'           => 1,
                        'created_at'       => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Class added successfully!');
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = $this->input->post('class_id');
                $this->Class_model->update($id, array(
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'class_name'       => $this->input->post('class_name'),
                    'class_code'       => $this->input->post('class_code'),
                    'capacity'         => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                    'description'      => $this->input->post('description'),
                    'updated_at'       => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Class updated successfully!');
            }
            redirect('academics/classes');
        }

        $year_id = $this->input->get('year_id');
        $classes = $this->Class_model->get_all($year_id);
        $years   = $this->Academic_year_model->get_all();

        $this->render('pages/academics/classes', array(
            'title'      => 'Classes',
            'page_key'   => 'classes',
            'breadcrumb' => array('Academic Management', 'Classes'),
            'classes'    => $classes,
            'years'      => $years,
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
       3. Sections / Divisions Management
       ========================================================================= */
    public function sections()
    {
        $this->require_permission('academics.view');
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add') {
                $this->require_permission('academics.create');
                $this->form_validation->set_rules('class_id', 'Class', 'required');
                $this->form_validation->set_rules('section_name', 'Section Name', 'required|trim');

                if ($this->form_validation->run() === TRUE) {
                    $class_id = $this->input->post('class_id');
                    $section_name = trim($this->input->post('section_name'));

                    if ($this->Section_model->check_duplicate($class_id, $section_name)) {
                        $this->session->set_flashdata('error', 'Section "' . $section_name . '" already exists in the selected class.');
                    } else {
                        $this->Section_model->insert(array(
                            'class_id'     => $class_id,
                            'section_name' => $section_name,
                            'room_no'      => $this->input->post('room_no'),
                            'capacity'     => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                            'description'  => $this->input->post('description'),
                            'status'       => 1,
                            'created_at'   => date('Y-m-d H:i:s')
                        ));
                        $this->session->set_flashdata('success', 'Section created successfully!');
                    }
                } else {
                    $this->session->set_flashdata('error', validation_errors());
                }
            } elseif ($action === 'edit') {
                $this->require_permission('academics.edit');
                $id = $this->input->post('section_id');
                $class_id = $this->input->post('class_id');
                $section_name = trim($this->input->post('section_name'));

                if ($this->Section_model->check_duplicate($class_id, $section_name, $id)) {
                    $this->session->set_flashdata('error', 'Section "' . $section_name . '" already exists in this class.');
                } else {
                    $this->Section_model->update($id, array(
                        'class_id'     => $class_id,
                        'section_name' => $section_name,
                        'room_no'      => $this->input->post('room_no'),
                        'capacity'     => $this->input->post('capacity') ? intval($this->input->post('capacity')) : 40,
                        'description'  => $this->input->post('description'),
                        'updated_at'   => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Section updated successfully!');
                }
            }
            redirect('academics/sections');
        }

        $class_id = $this->input->get('class_id');
        $sections = $this->Section_model->get_all($class_id);
        $classes  = $this->Class_model->get_all();

        $this->render('pages/academics/sections', array(
            'title'      => 'Sections / Divisions',
            'page_key'   => 'sections',
            'breadcrumb' => array('Academic Management', 'Sections / Divisions'),
            'sections'   => $sections,
            'classes'    => $classes,
        ));
    }

    public function delete_section($id = NULL)
    {
        $this->require_permission('academics.delete');
        if (!empty($id)) {
            $this->Section_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Section deactivated.');
        }
        redirect('academics/sections');
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
            $year_id    = $this->input->post('academic_year_id') ?: 1;
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $staff_id   = $this->input->post('staff_id');

            if (empty($class_id) || empty($section_id) || empty($staff_id)) {
                $this->session->set_flashdata('error', 'Please select academic year, class, section, and teacher.');
            } else {
                $res = $this->Class_teacher_model->assign($year_id, $class_id, $section_id, $staff_id);
                if ($res) {
                    $this->session->set_flashdata('success', 'Class Teacher assigned successfully!');
                } else {
                    $this->session->set_flashdata('error', 'Failed to assign class teacher. Only teaching faculty can be assigned.');
                }
            }
            redirect('academics/class_teachers');
        }

        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id'),
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'staff_id'         => $this->input->get('staff_id'),
        );

        $assignments = $this->Class_teacher_model->get_all($filters);
        $years       = $this->Academic_year_model->get_all();
        $classes     = $this->Class_model->get_all();
        $sections    = $this->Section_model->get_all();
        $teachers    = $this->Staff_model->get_teachers();

        $this->render('pages/academics/class_teachers', array(
            'title'       => 'Class Teachers',
            'page_key'    => 'class-teachers',
            'breadcrumb'  => array('Academic Management', 'Class Teachers'),
            'assignments' => $assignments,
            'years'       => $years,
            'classes'     => $classes,
            'sections'    => $sections,
            'teachers'    => $teachers,
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
            $year_id    = $this->input->post('academic_year_id') ?: 1;
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $subject_id = $this->input->post('subject_id');
            $staff_id   = $this->input->post('staff_id');

            if (empty($class_id) || empty($section_id) || empty($subject_id) || empty($staff_id)) {
                $this->session->set_flashdata('error', 'Please fill all required assignment fields.');
            } else {
                $res = $this->Subject_teacher_model->assign($year_id, $class_id, $section_id, $subject_id, $staff_id);
                if ($res) {
                    $this->session->set_flashdata('success', 'Subject Teacher assigned successfully!');
                } else {
                    $this->session->set_flashdata('error', 'Failed to assign subject teacher. Only teaching faculty can be assigned.');
                }
            }
            redirect('academics/subject_teachers');
        }

        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id'),
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'subject_id'       => $this->input->get('subject_id'),
            'staff_id'         => $this->input->get('staff_id'),
        );

        $assignments = $this->Subject_teacher_model->get_all($filters);
        $years       = $this->Academic_year_model->get_all();
        $classes     = $this->Class_model->get_all();
        $sections    = $this->Section_model->get_all();
        $subjects    = $this->Subject_model->get_all();
        $teachers    = $this->Staff_model->get_teachers();

        $this->render('pages/academics/subject_teachers', array(
            'title'       => 'Subject Teachers',
            'page_key'    => 'subject-teachers',
            'breadcrumb'  => array('Academic Management', 'Subject Teachers'),
            'assignments' => $assignments,
            'years'       => $years,
            'classes'     => $classes,
            'sections'    => $sections,
            'subjects'    => $subjects,
            'teachers'    => $teachers,
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
            redirect('academics/calendar');
        }

        $years = $this->Academic_year_model->get_all();
        $active_year = $this->Academic_year_model->get_active_year();
        $selected_year = $this->input->get('academic_year_id') ?: ($active_year ? $active_year->academic_year_id : 1);
        $selected_type = $this->input->get('event_type') ?: '';
        $selected_month = $this->input->get('month') ?: date('n');
        $selected_cal_year = $this->input->get('year') ?: date('Y');

        $filters = array(
            'academic_year_id' => $selected_year,
            'event_type'       => $selected_type,
        );

        $events = $this->Academic_calendar_model->get_all($filters);
        $upcoming = $this->Academic_calendar_model->get_upcoming(5, $selected_year);

        $this->render('pages/academics/calendar', array(
            'title'             => 'Academic Calendar',
            'page_key'          => 'academic-calendar',
            'breadcrumb'        => array('Academic Management', 'Academic Calendar'),
            'events'            => $events,
            'upcoming'          => $upcoming,
            'years'             => $years,
            'selected_year'     => $selected_year,
            'selected_type'     => $selected_type,
            'selected_month'    => $selected_month,
            'selected_cal_year' => $selected_cal_year,
        ));
    }

    public function delete_calendar_event($id = NULL)
    {
        $this->require_permission('academics.delete');
        if (!empty($id)) {
            $this->Academic_calendar_model->soft_delete($id);
            $this->session->set_flashdata('success', 'Calendar event removed.');
        }
        redirect('academics/calendar');
    }

    /* =========================================================================
       9. Dynamic AJAX Dropdowns & Helpers
       ========================================================================= */
    public function ajax_get_sections($class_id = NULL)
    {
        header('Content-Type: application/json');
        if (empty($class_id)) {
            echo json_encode(array());
            return;
        }
        $sections = $this->Section_model->get_all($class_id);
        echo json_encode($sections);
    }

    public function ajax_get_subjects($class_id = NULL)
    {
        header('Content-Type: application/json');
        $subjects = $this->Subject_model->get_all($class_id);
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
