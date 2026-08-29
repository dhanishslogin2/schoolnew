<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Student_model');
        $this->load->model('Student_academic_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Academic_year_model');
    }

    /* =========================================================================
       1. Student Management Overview
       ========================================================================= */
    public function index()
    {
        $this->overview();
    }

    public function overview()
    {
        $this->require_permission('students.view');
        $year_id = $this->input->get('academic_year_id') ?: $this->academic_year_id;
        $stats = $this->Student_model->get_dashboard_stats($year_id);

        $this->render('pages/students/overview', array(
            'title'      => 'Student Management Overview',
            'page_key'   => 'students',
            'breadcrumb' => array('Student Management', 'Overview'),
            'stats'      => $stats,
        ));
    }

    public function list_students()
    {
        $this->require_permission('students.view');
        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id') ?: $this->academic_year_id,
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'gender'           => $this->input->get('gender'),
            'status'           => $this->input->get('status'),
            'search'           => $this->input->get('search'),
        );

        $students = $this->Student_model->get_all($filters);
        $classes  = $this->Class_model->get_all($filters['academic_year_id']);
        $sections = $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();

        $this->render('pages/students/index', array(
            'title'    => 'Student Directory',
            'page_key' => 'student-directory',
            'students' => $students,
            'classes'  => $classes,
            'sections' => $sections,
            'years'    => $years,
            'filters'  => $filters,
        ));
    }

    public function ajax_list()
    {
        $this->require_permission('students.view');

        $draw   = (int)$this->input->post('draw');
        $start  = (int)$this->input->post('start');
        $length = (int)$this->input->post('length');
        $order  = $this->input->post('order');
        $search = $this->input->post('search');

        $order_col_idx = isset($order[0]['column']) ? (int)$order[0]['column'] : 0;
        $order_dir     = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $search_val    = isset($search['value']) ? trim($search['value']) : '';

        $filters = array(
            'academic_year_id' => $this->input->post('academic_year_id') ?: ($this->input->get('academic_year_id') ?: $this->academic_year_id),
            'class_id'         => $this->input->post('class_id') ?: $this->input->get('class_id'),
            'section_id'       => $this->input->post('section_id') ?: $this->input->get('section_id'),
            'gender'           => $this->input->post('gender') ?: $this->input->get('gender'),
            'status'           => $this->input->post('status') !== NULL ? $this->input->post('status') : $this->input->get('status'),
            'search'           => $search_val,
        );

        $records_total    = $this->Student_model->get_datatables_count_all($filters);
        $records_filtered = $this->Student_model->count_filtered($filters);
        $students         = $this->Student_model->get_datatables_data($filters, $length, $start, $order_col_idx, $order_dir);

        $data = array();
        foreach ($students as $st) {
            $nameParts = explode(' ', trim($st->first_name . ' ' . $st->last_name));
            $initials = '';
            foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
            if (strlen($initials) > 2) $initials = substr($initials, 0, 2);

            $statusBadge = ($st->status == 1)
                ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span>'
                : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Inactive</span>';

            $classDisplay = trim(($st->class_name ?: '') . ' ' . ($st->section_name ?: ''));

            $admissionCol = '<a href="' . site_url('students/profile/' . $st->student_id) . '" class="text-primary font-medium hover:underline">' . html_escape($st->admission_number) . '</a>';
            if (!empty($st->roll_number)) {
                $admissionCol .= ' <span class="text-[11px] text-on-surface-variant ml-1 font-mono">#' . html_escape($st->roll_number) . '</span>';
            }

            $studentCol = '<div class="flex items-center gap-2.5">' .
                '<div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-[11px] font-semibold shrink-0">' . html_escape($initials) . '</div>' .
                '<div>' .
                    '<div class="font-medium text-on-surface">' . html_escape($st->first_name . ' ' . $st->last_name) . '</div>' .
                    '<div class="text-[12px] text-on-surface-variant">' . html_escape($classDisplay . ' · ' . $st->gender) . '</div>' .
                '</div>' .
            '</div>';

            $actionsCol = '<div class="flex items-center justify-end gap-1.5">' .
                '<a href="' . site_url('students/profile/' . $st->student_id) . '" title="View Profile" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span></a>' .
                '<a href="' . site_url('students/edit/' . $st->student_id) . '" title="Edit Student" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">edit</span></a>' .
                '<a href="' . site_url('students/id_cards?student_id=' . $st->student_id) . '" title="ID Card" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">badge</span></a>' .
            '</div>';

            $data[] = array(
                $admissionCol,
                $studentCol,
                html_escape($classDisplay),
                html_escape($st->gender),
                school_date($st->date_of_birth),
                html_escape($st->guardian_name ?: '—'),
                html_escape($st->guardian_phone ?: '—'),
                $statusBadge,
                $actionsCol
            );
        }

        $output = array(
            "draw"            => $draw,
            "recordsTotal"    => $records_total,
            "recordsFiltered" => $records_filtered,
            "data"            => $data,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /* =========================================================================
       2. Student Registration Wizard
       ========================================================================= */

    /**
     * Alias — /students/register maps here
     */
    public function register()
    {
        $this->add();
    }

    /**
     * Main wizard dispatcher.
     *
     * GET  students/add          → Step 1 (Student Details)
     * GET  students/add?step=2   → Step 2 (Academic Details) — guarded
     * GET  students/add?step=3   → Step 3 (Parent / Guardian) — guarded
     *
     * The wizard stores intermediate data in the CI session under the key
     * 'student_registration_wizard'.  No database rows are written until the
     * final Save Student action (wizard_save).
     */
    public function add()
    {
        $this->require_permission('students.create');

        $step    = (int)($this->input->get('step') ?: 1);
        $wizard  = $this->session->userdata('student_registration_wizard') ?: array();

        // ── Guard: disallow skipping ahead ────────────────────────────────────
        if ($step === 2 && empty($wizard['student_details'])) {
            redirect('students/add');
            return;
        }
        if ($step === 3 && (empty($wizard['student_details']) || empty($wizard['academic_details']))) {
            if (empty($wizard['student_details'])) {
                redirect('students/add');
            } else {
                redirect('students/add?step=2');
            }
            return;
        }
        if ($step < 1 || $step > 3) {
            redirect('students/add');
            return;
        }

        // ── Initialise a wizard token on first visit ───────────────────────────
        if (empty($wizard['wizard_token'])) {
            $wizard['wizard_token'] = sha1(uniqid('wiz_', TRUE));
            $this->session->set_userdata('student_registration_wizard', $wizard);
        }

        $classes       = $this->Class_model->get_all($this->academic_year_id);
        $sections      = $this->Section_model->get_all();
        $academic_years = $this->Academic_year_model->get_all();

        $this->render('pages/students/add', array(
            'title'          => 'Student Registration',
            'page_key'       => 'student-registration',
            'breadcrumb'     => array('Student Management', 'Student Registration'),
            'classes'        => $classes,
            'sections'       => $sections,
            'academic_years' => $academic_years,
            'current_step'   => $step,
            'wizard'         => $wizard,
        ));
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Wizard Step 1 — Validate & save Student Details to session
       POST students/wizard_step1
    ───────────────────────────────────────────────────────────────────────── */
    public function wizard_step1()
    {
        $this->require_permission('students.create');

        if ($this->input->method() !== 'post') {
            redirect('students/add');
            return;
        }

        $this->form_validation->set_rules('admission_number', 'Admission Number', 'required|trim');
        $this->form_validation->set_rules('first_name',       'First Name',        'required|trim');

        if ($this->form_validation->run() !== TRUE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => FALSE,
                    'errors'  => $this->_collect_validation_errors(),
                )));
            return;
        }

        // Store step-1 data in session (no DB write)
        $wizard = $this->session->userdata('student_registration_wizard') ?: array();

        // Initialise token if somehow missing
        if (empty($wizard['wizard_token'])) {
            $wizard['wizard_token'] = sha1(uniqid('wiz_', TRUE));
        }

        $wizard['student_details'] = array(
            'admission_number' => $this->input->post('admission_number', TRUE),
            'first_name'       => $this->input->post('first_name',       TRUE),
            'last_name'        => $this->input->post('last_name',         TRUE),
            'gender'           => $this->input->post('gender',            TRUE) ?: 'Male',
            'date_of_birth'    => $this->input->post('date_of_birth',     TRUE) ?: date('Y-m-d'),
            'blood_group'      => $this->input->post('blood_group',       TRUE),
        );

        $this->session->set_userdata('student_registration_wizard', $wizard);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'  => TRUE,
                'redirect' => site_url('students/add?step=2'),
            )));
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Wizard TC Upload — separate multipart file-only endpoint
       POST students/wizard_tc_upload
       Returns JSON { success, temp_path, display_name, error }
    ───────────────────────────────────────────────────────────────────────── */
    public function wizard_tc_upload()
    {
        $this->require_permission('students.create');

        if ($this->input->method() !== 'post') {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array('success' => FALSE, 'error' => 'Invalid request.')));
            return;
        }

        if (empty($_FILES['tc_document']['name'])) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array('success' => FALSE, 'error' => 'No file received.')));
            return;
        }

        // ── Security: whitelist extensions + MIME types ───────────────────────
        $allowed_ext  = array('pdf', 'jpg', 'jpeg', 'png');
        $allowed_mime = array('application/pdf', 'image/jpeg', 'image/jpg', 'image/png');

        $orig_name = $_FILES['tc_document']['name'];
        $ext       = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array('success' => FALSE, 'error' => 'Invalid file type. Allowed: PDF, JPG, PNG.')));
            return;
        }

        // MIME type check via finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $_FILES['tc_document']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed_mime)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array('success' => FALSE, 'error' => 'File content does not match allowed types.')));
            return;
        }

        // Size limit: 2MB
        $max_size = 2 * 1024 * 1024;
        if ($_FILES['tc_document']['size'] > $max_size) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array('success' => FALSE, 'error' => 'File too large. Maximum size is 2 MB.')));
            return;
        }

        if ($_FILES['tc_document']['error'] !== UPLOAD_ERR_OK) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array('success' => FALSE, 'error' => 'Upload error. Please try again.')));
            return;
        }

        // ── Safe filename + temp storage ──────────────────────────────────────
        $safe_name  = 'tc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $temp_dir   = FCPATH . 'uploads/tc_temp/';

        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, TRUE);
        }

        if (!move_uploaded_file($_FILES['tc_document']['tmp_name'], $temp_dir . $safe_name)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array('success' => FALSE, 'error' => 'Failed to store file. Please try again.')));
            return;
        }

        // Clean up any previously uploaded temp TC for this wizard session
        $wizard = $this->session->userdata('student_registration_wizard') ?: array();
        $old_ad = isset($wizard['academic_details']) ? $wizard['academic_details'] : array();
        if (!empty($old_ad['tc_temp_path'])) {
            $old_file = FCPATH . $old_ad['tc_temp_path'];
            if (is_file($old_file)) {
                @unlink($old_file);
            }
        }

        $temp_path = 'uploads/tc_temp/' . $safe_name;

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(array(
                         'success'      => TRUE,
                         'temp_path'    => $temp_path,
                         'display_name' => $orig_name,
                     )));
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Wizard Step 2 — Validate & save Academic Details to session
       POST students/wizard_step2
    ───────────────────────────────────────────────────────────────────────── */
    public function wizard_step2()
    {
        $this->require_permission('students.create');

        if ($this->input->method() !== 'post') {
            redirect('students/add?step=2');
            return;
        }

        $wizard = $this->session->userdata('student_registration_wizard') ?: array();
        if (empty($wizard['student_details'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success'  => FALSE,
                    'redirect' => site_url('students/add'),
                    'message'  => 'Please complete Step 1 first.',
                )));
            return;
        }

        // ── Server-side validation ────────────────────────────────────────────
        $this->form_validation->set_rules('class_id', 'Class', 'required');

        $no_prev_school = ($this->input->post('no_previous_school') == '1');

        if (!$no_prev_school) {
            $this->form_validation->set_rules('prev_school_name',  'Previous School Name',    'trim|required');
            $this->form_validation->set_rules('prev_school_board',  'Previous School Board',   'trim');
            $this->form_validation->set_rules('prev_class',         'Previous Class',          'trim');
            $this->form_validation->set_rules('prev_academic_year', 'Previous Academic Year',  'trim');
            $this->form_validation->set_rules('prev_percentage',
                'Previous Percentage',
                'trim|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
            );
        }

        if ($this->form_validation->run() !== TRUE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => FALSE,
                    'errors'  => $this->_collect_validation_errors(),
                )));
            return;
        }

        // ── Build academic_details sub-array ──────────────────────────────────
        $academic_details = array(
            'academic_year_id'   => $this->academic_year_id,
            'class_id'           => (int)$this->input->post('class_id'),
            'section_id'         => (int)$this->input->post('section_id') ?: 0,
            'roll_number'        => $this->input->post('roll_number', TRUE),
            'no_previous_school' => $no_prev_school ? 1 : 0,
        );

        // ── Previous school fields (only when applicable) ─────────────────────
        if (!$no_prev_school) {
            $academic_details['prev_school'] = array(
                'school_name'            => $this->input->post('prev_school_name',    TRUE),
                'school_address'         => $this->input->post('prev_school_address', TRUE),
                'school_board'           => $this->input->post('prev_school_board',   TRUE),
                'previous_class'         => $this->input->post('prev_class',           TRUE),
                'previous_academic_year' => $this->input->post('prev_academic_year',  TRUE),
                'date_of_leaving'        => $this->input->post('date_of_leaving',     TRUE) ?: NULL,
                'reason_for_leaving'     => $this->input->post('reason_for_leaving',  TRUE),
                'tc_number'              => $this->input->post('tc_number',            TRUE),
                'previous_percentage'    => strlen($this->input->post('prev_percentage')) > 0
                                               ? (float)$this->input->post('prev_percentage')
                                               : NULL,
                'tc_temp_path'           => $this->input->post('tc_temp_path', TRUE),
            );
        } else {
            $academic_details['prev_school'] = array();
            // Clean up any temp TC file from a previous attempt
            $old_ad = isset($wizard['academic_details']) ? $wizard['academic_details'] : array();
            if (!empty($old_ad['tc_temp_path'])) {
                $old_file = FCPATH . $old_ad['tc_temp_path'];
                if (is_file($old_file)) {
                    @unlink($old_file);
                }
            }
        }

        // ── Academic activities ───────────────────────────────────────────────
        $raw_academic = $this->input->post('academic_activities');
        $activities   = array();
        if (!empty($raw_academic) && is_array($raw_academic)) {
            foreach ($raw_academic as $act) {
                if (!empty($act['activity_name'])) {
                    $activities[] = array(
                        'category'        => 'Academic',
                        'activity_type'   => isset($act['activity_type'])   ? $act['activity_type']   : 'Achievement',
                        'activity_name'   => isset($act['activity_name'])   ? $act['activity_name']   : '',
                        'description'     => isset($act['description'])     ? $act['description']     : '',
                        'level'           => isset($act['level'])           ? $act['level']           : '',
                        'position_result' => isset($act['position_result']) ? $act['position_result'] : '',
                        'year'            => isset($act['year']) && is_numeric($act['year'])
                                                ? (int)$act['year'] : NULL,
                    );
                }
            }
        }
        $academic_details['activities'] = $activities;

        // ── Extracurricular activities ────────────────────────────────────────
        $raw_extra     = $this->input->post('extracurricular');
        $extracurricular = array();
        if (!empty($raw_extra) && is_array($raw_extra)) {
            foreach ($raw_extra as $act) {
                if (!empty($act['activity_name'])) {
                    $extracurricular[] = array(
                        'category'        => 'Extracurricular',
                        'activity_type'   => isset($act['activity_type'])   ? $act['activity_type']   : 'Sports',
                        'activity_name'   => isset($act['activity_name'])   ? $act['activity_name']   : '',
                        'description'     => isset($act['description'])     ? $act['description']     : '',
                        'level'           => isset($act['level'])           ? $act['level']           : '',
                        'position_result' => isset($act['position_result']) ? $act['position_result'] : '',
                        'year'            => isset($act['year']) && is_numeric($act['year'])
                                                ? (int)$act['year'] : NULL,
                    );
                }
            }
        }
        $academic_details['extracurricular'] = $extracurricular;

        $wizard['academic_details'] = $academic_details;
        $this->session->set_userdata('student_registration_wizard', $wizard);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'  => TRUE,
                'redirect' => site_url('students/add?step=3'),
            )));
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Wizard Final Save — Validate Step 3, merge all data, run DB transaction
       POST students/wizard_save
    ───────────────────────────────────────────────────────────────────────── */
    public function wizard_save()
    {
        $this->require_permission('students.create');

        if ($this->input->method() !== 'post') {
            redirect('students/add?step=3');
            return;
        }

        $wizard = $this->session->userdata('student_registration_wizard') ?: array();

        // Guard: require all prior steps
        if (empty($wizard['student_details']) || empty($wizard['academic_details'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success'  => FALSE,
                    'redirect' => site_url('students/add'),
                    'message'  => 'Registration session expired. Please start again.',
                )));
            return;
        }

        // Anti-duplicate: check wizard token consumed flag
        if (!empty($wizard['submitted'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success'  => FALSE,
                    'message'  => 'This registration has already been submitted.',
                )));
            return;
        }

        $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'required|trim');

        if ($this->form_validation->run() !== TRUE) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => FALSE,
                    'errors'  => $this->_collect_validation_errors(),
                )));
            return;
        }

        // Mark submitted immediately to prevent duplicate concurrent posts
        $wizard['submitted'] = TRUE;
        $this->session->set_userdata('student_registration_wizard', $wizard);

        $parent_details = array(
            'guardian_name'     => $this->input->post('guardian_name',     TRUE),
            'guardian_relation' => $this->input->post('guardian_relation', TRUE) ?: 'Father',
            'guardian_phone'    => $this->input->post('guardian_phone',    TRUE),
            'guardian_email'    => $this->input->post('guardian_email',    TRUE),
            'address'           => $this->input->post('address',           TRUE),
        );

        // ── Merge all three steps ──────────────────────────────────────────────
        $sd = $wizard['student_details'];
        $ad = $wizard['academic_details'];

        $student_data = array(
            'admission_number'  => $sd['admission_number'],
            'first_name'        => $sd['first_name'],
            'last_name'         => $sd['last_name']         ?: '',
            'gender'            => $sd['gender']            ?: 'Male',
            'date_of_birth'     => $sd['date_of_birth']     ?: date('Y-m-d'),
            'blood_group'       => $sd['blood_group']       ?: '',
            'academic_year_id'  => $ad['academic_year_id']  ?: $this->academic_year_id,
            'class_id'          => $ad['class_id']          ?: 1,
            'section_id'        => $ad['section_id']        ?: 1,
            'roll_number'       => $ad['roll_number']       ?: '',
            'guardian_name'     => $parent_details['guardian_name'],
            'guardian_relation' => $parent_details['guardian_relation'],
            'guardian_phone'    => $parent_details['guardian_phone']    ?: '',
            'guardian_email'    => $parent_details['guardian_email']    ?: '',
            'address'           => $parent_details['address']           ?: '',
            'status'            => 1,
            'is_deleted'        => 'n',
            'created_at'        => date('Y-m-d H:i:s'),
        );

        // ── Database transaction ──────────────────────────────────────────────
        $this->db->trans_start();

        // 1. Insert core student record
        $this->db->insert('tbl_students', $student_data);
        $new_id = (int)$this->db->insert_id();

        if ($new_id > 0) {
            $no_prev_school = !empty($ad['no_previous_school']);
            $prev_school    = isset($ad['prev_school']) ? $ad['prev_school'] : array();

            // 2. Previous school record
            if (!$no_prev_school && !empty($prev_school['school_name'])) {
                $tc_document_id = NULL;

                // Move TC file from temp → permanent location
                if (!empty($prev_school['tc_temp_path'])) {
                    $temp_path  = FCPATH . $prev_school['tc_temp_path'];
                    $dest_dir   = FCPATH . 'uploads/documents/';
                    if (!is_dir($dest_dir)) {
                        mkdir($dest_dir, 0755, TRUE);
                    }
                    $dest_name = 'tc_' . $new_id . '_' . basename($prev_school['tc_temp_path']);
                    $dest_path = $dest_dir . $dest_name;

                    if (is_file($temp_path) && rename($temp_path, $dest_path)) {
                        $perm_path      = 'uploads/documents/' . $dest_name;
                        $tc_document_id = $this->Student_academic_model->insert_tc_document(
                            $new_id,
                            $perm_path,
                            isset($prev_school['tc_number']) ? $prev_school['tc_number'] : ''
                        );
                    }
                }

                $prev_school_data = array(
                    'student_id'             => $new_id,
                    'school_name'            => $prev_school['school_name'],
                    'school_address'         => isset($prev_school['school_address'])         ? $prev_school['school_address']         : NULL,
                    'school_board'           => isset($prev_school['school_board'])           ? $prev_school['school_board']           : NULL,
                    'previous_class'         => isset($prev_school['previous_class'])         ? $prev_school['previous_class']         : NULL,
                    'previous_academic_year' => isset($prev_school['previous_academic_year']) ? $prev_school['previous_academic_year'] : NULL,
                    'date_of_leaving'        => !empty($prev_school['date_of_leaving'])       ? $prev_school['date_of_leaving']        : NULL,
                    'reason_for_leaving'     => isset($prev_school['reason_for_leaving'])     ? $prev_school['reason_for_leaving']     : NULL,
                    'tc_number'              => isset($prev_school['tc_number'])               ? $prev_school['tc_number']               : NULL,
                    'tc_document_id'         => $tc_document_id,
                    'previous_percentage'    => isset($prev_school['previous_percentage'])    ? $prev_school['previous_percentage']    : NULL,
                    'status'                 => 1,
                    'created_at'             => date('Y-m-d H:i:s'),
                );
                $this->Student_academic_model->insert_previous_school($prev_school_data);
            }

            // 3. Academic activities
            $all_activities = array();
            $activities     = isset($ad['activities'])      ? $ad['activities']      : array();
            $extracurricular = isset($ad['extracurricular']) ? $ad['extracurricular'] : array();

            foreach (array_merge($activities, $extracurricular) as $act) {
                if (!empty($act['activity_name'])) {
                    $all_activities[] = array(
                        'student_id'      => $new_id,
                        'category'        => $act['category'],
                        'activity_type'   => $act['activity_type'],
                        'activity_name'   => $act['activity_name'],
                        'description'     => isset($act['description'])     ? $act['description']     : NULL,
                        'level'           => isset($act['level'])           ? $act['level']           : NULL,
                        'position_result' => isset($act['position_result']) ? $act['position_result'] : NULL,
                        'year'            => isset($act['year']) && $act['year'] > 0 ? (int)$act['year'] : NULL,
                        'status'          => 1,
                        'created_at'      => date('Y-m-d H:i:s'),
                    );
                }
            }

            if (!empty($all_activities)) {
                $this->Student_academic_model->insert_activities_batch($all_activities);
            }
        }

        $this->db->trans_complete();

        if (!$this->db->trans_status() || !$new_id) {
            // Roll back happened automatically; clear submitted flag so user can retry
            $wizard['submitted'] = FALSE;
            $this->session->set_userdata('student_registration_wizard', $wizard);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => FALSE,
                    'message' => 'Failed to save student. Please try again.',
                )));
            return;
        }

        // ── Success: clear wizard session data ────────────────────────────────
        $this->session->unset_userdata('student_registration_wizard');
        $this->session->set_flashdata('success', 'Student registered successfully.');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'  => TRUE,
                'redirect' => site_url('students/profile/' . $new_id),
            )));
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Wizard Cancel — clear session, redirect to student list
       GET/POST students/wizard_cancel
    ───────────────────────────────────────────────────────────────────────── */
    public function wizard_cancel()
    {
        $this->require_permission('students.create');

        // Clean up any temp TC file
        $wizard = $this->session->userdata('student_registration_wizard') ?: array();
        $ad     = isset($wizard['academic_details']) ? $wizard['academic_details'] : array();
        if (!empty($ad['prev_school']['tc_temp_path'])) {
            $temp = FCPATH . $ad['prev_school']['tc_temp_path'];
            if (is_file($temp)) {
                @unlink($temp);
            }
        }

        $this->session->unset_userdata('student_registration_wizard');
        redirect('students');
    }

    /* ─────────────────────────────────────────────────────────────────────────
       Internal: collect form_validation errors into a flat array
    ───────────────────────────────────────────────────────────────────────── */
    private function _collect_validation_errors()
    {
        $errors = array();
        foreach ($this->form_validation->error_array() as $field => $msg) {
            $errors[$field] = $msg;
        }
        return $errors;
    }


    /* =========================================================================
       3. Student Edit
       ========================================================================= */
    public function edit($student_id = NULL)
    {
        $this->require_permission('students.edit');

        if (!$student_id) {
            redirect('students');
            return;
        }

        $student = $this->Student_model->get_by_id($student_id);
        if (!$student) {
            $this->session->set_flashdata('error', 'Student not found.');
            redirect('students');
            return;
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
            $this->form_validation->set_rules('admission_number', 'Admission Number', 'required|trim');

            if ($this->form_validation->run() === TRUE) {
                $data = array(
                    'admission_number' => $this->input->post('admission_number', TRUE),
                    'first_name'       => $this->input->post('first_name', TRUE),
                    'last_name'        => $this->input->post('last_name', TRUE),
                    'gender'           => $this->input->post('gender', TRUE),
                    'date_of_birth'    => $this->input->post('date_of_birth', TRUE) ?: $student->date_of_birth,
                    'blood_group'      => $this->input->post('blood_group', TRUE),
                    'academic_year_id' => $this->input->post('academic_year_id') ?: $student->academic_year_id,
                    'class_id'         => $this->input->post('class_id') ?: $student->class_id,
                    'section_id'       => $this->input->post('section_id') ?: $student->section_id,
                    'roll_number'      => $this->input->post('roll_number', TRUE),
                    'guardian_name'    => $this->input->post('guardian_name', TRUE),
                    'guardian_relation'=> $this->input->post('guardian_relation', TRUE) ?: $student->guardian_relation,
                    'guardian_phone'   => $this->input->post('guardian_phone', TRUE),
                    'guardian_email'   => $this->input->post('guardian_email', TRUE),
                    'address'          => $this->input->post('address', TRUE),
                );
                $this->Student_model->update($student_id, $data);
                $this->session->set_flashdata('success', 'Student details updated successfully.');
                redirect('students/profile/' . $student_id);
                return;
            }
        }

        $classes  = $this->Class_model->get_all($student->academic_year_id);
        $sections = $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();

        $this->render('pages/students/edit', array(
            'title'      => 'Edit Student',
            'page_key'   => 'students',
            'breadcrumb' => array('Student Management', 'Edit Student'),
            'student'    => $student,
            'student_id' => $student_id,
            'classes'    => $classes,
            'sections'   => $sections,
            'years'      => $years,
        ));
    }

    /* =========================================================================
       4. Student Delete (Safe Deactivation)
       ========================================================================= */
    public function delete($student_id = NULL)
    {
        $this->require_permission('students.delete');

        if (!$student_id) {
            redirect('students');
            return;
        }

        $student = $this->Student_model->get_by_id($student_id);
        if ($student) {
            $this->Student_model->soft_delete($student_id);
            $this->session->set_flashdata('success', 'Student record has been deactivated safely.');
        } else {
            $this->session->set_flashdata('error', 'Student not found.');
        }

        redirect('students');
    }

    /* =========================================================================
       5. Student Profile
       ========================================================================= */
    public function profile($student_id = NULL)
    {
        $this->require_permission('students.view');
        if (!$student_id) {
            $first = $this->Student_model->get_all(array('academic_year_id' => $this->academic_year_id));
            $student_id = !empty($first) ? $first[0]->student_id : 1;
        }

        $student = $this->Student_model->get_profile($student_id);
        if (!$student) {
            $this->session->set_flashdata('error', 'Student profile not found.');
            redirect('students');
            return;
        }

        $classes  = $this->Class_model->get_all($student->academic_year_id);
        $sections = $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();

        $this->render('pages/students/profile', array(
            'title'      => 'Student Profile',
            'page_key'   => 'student-profile',
            'breadcrumb' => array('Student Management', 'Student Profile'),
            'student'    => $student,
            'student_id' => $student_id,
            'classes'    => $classes,
            'sections'   => $sections,
            'years'      => $years,
        ));
    }

    /* =========================================================================
       6. Admission Management
       ========================================================================= */
    public function admissions()
    {
        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');

            if ($action === 'new_admission') {
                $appNo = 'APP' . date('Y') . sprintf('%03d', rand(100, 999));
                $admData = array(
                    'application_number' => $appNo,
                    'first_name'        => $this->input->post('first_name', TRUE),
                    'last_name'         => $this->input->post('last_name', TRUE),
                    'gender'            => $this->input->post('gender', TRUE),
                    'date_of_birth'     => $this->input->post('date_of_birth', TRUE) ?: date('Y-m-d'),
                    'blood_group'       => $this->input->post('blood_group', TRUE),
                    'academic_year_id'  => $this->input->post('academic_year_id') ?: $this->academic_year_id,
                    'class_id'          => $this->input->post('class_id') ?: 1,
                    'guardian_name'     => $this->input->post('guardian_name', TRUE),
                    'guardian_relation' => $this->input->post('guardian_relation', TRUE) ?: 'Father',
                    'guardian_phone'    => $this->input->post('guardian_phone', TRUE),
                    'guardian_email'    => $this->input->post('guardian_email', TRUE),
                    'address'           => $this->input->post('address', TRUE),
                    'application_date'  => date('Y-m-d'),
                    'status'            => 'Pending',
                    'created_at'        => date('Y-m-d H:i:s')
                );
                $this->Student_model->add_admission($admData);
                $this->session->set_flashdata('success', 'New admission application submitted successfully (App No: ' . $appNo . ').');
                redirect('students/admissions');
                return;
            }

            if ($action === 'admit') {
                $admission_id = $this->input->post('admission_id');
                $section_id   = $this->input->post('section_id');
                $roll_number  = $this->input->post('roll_number');

                $new_student_id = $this->Student_model->convert_admission_to_student($admission_id, $section_id, $roll_number);
                if ($new_student_id) {
                    $this->session->set_flashdata('success', 'Student admitted and registered successfully.');
                    redirect('students/profile/' . $new_student_id);
                    return;
                }
            }

            if ($action === 'update_status') {
                $admission_id = $this->input->post('admission_id');
                $status       = $this->input->post('status');
                $this->Student_model->update_admission_status($admission_id, $status);
                $this->session->set_flashdata('success', 'Admission status updated to ' . $status . '.');
                redirect('students/admissions');
                return;
            }
        }

        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id') ?: $this->academic_year_id,
            'status'           => $this->input->get('status'),
            'class_id'         => $this->input->get('class_id'),
            'search'           => $this->input->get('search'),
        );

        $admissions = $this->Student_model->get_admissions($filters);
        $classes    = $this->Class_model->get_all($filters['academic_year_id']);
        $sections   = $this->Section_model->get_all();
        $years      = $this->Academic_year_model->get_all();

        $this->render('pages/students/admissions', array(
            'title'      => 'Admission Management',
            'page_key'   => 'admissions',
            'admissions' => $admissions,
            'classes'    => $classes,
            'sections'   => $sections,
            'years'      => $years,
            'filters'    => $filters,
        ));
    }

    /* =========================================================================
       7. Student Documents
       ========================================================================= */
    public function documents()
    {
        $this->require_permission('students.view');
        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id') ?: $this->academic_year_id,
            'student_id'       => $this->input->get('student_id'),
            'class_id'         => $this->input->get('class_id'),
            'document_type'    => $this->input->get('document_type'),
        );

        $documents = $this->Student_model->get_all_documents($filters);
        $students  = $this->Student_model->get_all(array('academic_year_id' => $filters['academic_year_id']));
        $classes   = $this->Class_model->get_all($filters['academic_year_id']);

        $this->render('pages/students/documents', array(
            'title'     => 'Student Documents',
            'page_key'  => 'student-documents',
            'documents' => $documents,
            'students'  => $students,
            'classes'   => $classes,
            'filters'   => $filters,
        ));
    }

    public function upload_document($student_id = NULL)
    {
        $this->require_permission('students.edit');
        if ($this->input->method() === 'post') {
            $student_id = $this->input->post('student_id') ?: $student_id;
            $docType    = $this->input->post('document_type', TRUE) ?: 'Other';
            $docName    = $this->input->post('document_name', TRUE) ?: 'Document';

            // Document file upload handling
            $filePath = 'uploads/documents/doc_' . time() . '.pdf';
            if (!empty($_FILES['document_file']['name'])) {
                $uploadDir = FCPATH . 'uploads/documents/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['document_file']['name']);
                if (move_uploaded_file($_FILES['document_file']['tmp_name'], $uploadDir . $fileName)) {
                    $filePath = 'uploads/documents/' . $fileName;
                    $docName = $docName ?: $_FILES['document_file']['name'];
                }
            }

            $data = array(
                'student_id'    => $student_id,
                'document_type' => $docType,
                'document_name' => $docName,
                'file_path'     => $filePath,
                'status'        => 1,
                'created_at'    => date('Y-m-d H:i:s'),
            );
            $this->Student_model->add_document($data);
            $this->session->set_flashdata('success', 'Document uploaded successfully.');

            $referrer = $this->input->post('redirect_to');
            redirect($referrer ?: 'students/profile/' . $student_id);
            return;
        }

        redirect('students');
    }

    public function delete_document($document_id = NULL)
    {
        $this->require_permission('students.edit');
        if ($document_id) {
            $this->Student_model->delete_document($document_id);
            $this->session->set_flashdata('success', 'Document removed successfully.');
        }
        $redirect = $this->input->get('redirect_to');
        redirect($redirect ?: 'students/documents');
    }

    /* =========================================================================
       8. Student ID Cards
       ========================================================================= */
    public function id_cards()
    {
        $this->require_permission('students.view');
        $class_id   = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');

        $students = $this->Student_model->get_all(array(
            'academic_year_id' => $this->academic_year_id,
            'class_id'         => $class_id,
            'section_id'       => $section_id,
            'status'           => 1
        ));
        $classes  = $this->Class_model->get_all($this->academic_year_id);
        $sections = $this->Section_model->get_all();

        $this->render('pages/students/id_cards', array(
            'title'    => 'Student ID Cards',
            'page_key' => 'student-id-cards',
            'students' => $students,
            'classes'  => $classes,
            'sections' => $sections,
            'selected_class'   => $class_id,
            'selected_section' => $section_id,
        ));
    }

    /* =========================================================================
       9. Student Promotion
       ========================================================================= */
    public function promotion()
    {
        $this->require_permission('students.promote');
        if ($this->input->method() === 'post') {
            $student_ids  = $this->input->post('student_ids');
            $from_year    = $this->input->post('from_academic_year_id');
            $from_class   = $this->input->post('from_class_id');
            $from_sec     = $this->input->post('from_section_id');
            $to_year      = $this->input->post('to_academic_year_id');
            $to_class     = $this->input->post('to_class_id');
            $to_sec       = $this->input->post('to_section_id');
            $promo_type   = $this->input->post('promotion_type') ?: 'Promoted';
            $remarks      = $this->input->post('remarks', TRUE);

            if (!empty($student_ids) && is_array($student_ids)) {
                $result = $this->Student_model->promote_students($student_ids, $from_year, $from_class, $from_sec, $to_year, $to_class, $to_sec, $promo_type, $remarks);
                if ($result) {
                    $this->session->set_flashdata('success', count($student_ids) . ' student(s) ' . strtolower($promo_type) . ' successfully.');
                    redirect('students/promotion?from_year=' . $to_year . '&from_class=' . $to_class . '&from_section=' . $to_sec);
                    return;
                }
            } else {
                $this->session->set_flashdata('error', 'Please select at least one student to promote.');
            }
        }

        $from_year  = $this->input->get('from_year') ?: $this->academic_year_id;
        $from_class = $this->input->get('from_class') ?: 8;
        $from_sec   = $this->input->get('from_section');

        $students = $this->Student_model->get_all(array(
            'academic_year_id' => $from_year,
            'class_id'         => $from_class,
            'section_id'       => $from_sec,
            'status'           => 1
        ));

        $promotions_history = $this->Student_model->get_promotions();
        $classes  = $this->Class_model->get_all($from_year);
        $sections = $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();

        $this->render('pages/students/promotion', array(
            'title'              => 'Student Promotion',
            'page_key'           => 'student-promotion',
            'students'           => $students,
            'promotions_history' => $promotions_history,
            'classes'            => $classes,
            'sections'           => $sections,
            'years'              => $years,
            'from_year'          => $from_year,
            'from_class'         => $from_class,
            'from_sec'           => $from_sec,
        ));
    }

    /* =========================================================================
       10. Transfer / TC Management
       ========================================================================= */
    public function transfers()
    {
        $this->require_permission('students.edit');
        if ($this->input->method() === 'post') {
            $student_id = $this->input->post('student_id');
            $student    = $this->Student_model->get_by_id($student_id);

            if ($student) {
                $tcNumber = 'TC/' . date('Y') . '/' . sprintf('%03d', rand(10, 999));
                $tcData = array(
                    'student_id'        => $student_id,
                    'tc_number'         => $tcNumber,
                    'transfer_date'     => $this->input->post('transfer_date') ?: date('Y-m-d'),
                    'reason'            => $this->input->post('reason', TRUE) ?: 'Parent Relocation',
                    'previous_class_id' => $student->class_id,
                    'academic_year_id'  => $student->academic_year_id,
                    'conduct'           => $this->input->post('conduct', TRUE) ?: 'Good',
                    'dues_cleared'      => 1,
                    'status'            => 'Issued',
                    'remarks'           => $this->input->post('remarks', TRUE),
                    'created_at'        => date('Y-m-d H:i:s'),
                );

                $transfer_id = $this->Student_model->issue_transfer($tcData);
                $this->session->set_flashdata('success', 'Transfer Certificate ' . $tcNumber . ' issued successfully.');
                redirect('students/tc/' . $transfer_id);
                return;
            }
        }

        $transfers = $this->Student_model->get_transfers();
        $students  = $this->Student_model->get_all(array('status' => 1, 'academic_year_id' => $this->academic_year_id));
        $classes   = $this->Class_model->get_all($this->academic_year_id);

        $this->render('pages/students/transfers', array(
            'title'     => 'Transfer / TC Management',
            'page_key'  => 'student-transfers',
            'transfers' => $transfers,
            'students'  => $students,
            'classes'   => $classes,
        ));
    }

    public function tc($transfer_id = NULL)
    {
        $this->require_permission('students.view');
        if (!$transfer_id) {
            redirect('students/transfers');
            return;
        }

        $transfer = $this->Student_model->get_transfer_by_id($transfer_id);
        if (!$transfer) {
            $this->session->set_flashdata('error', 'Transfer certificate record not found.');
            redirect('students/transfers');
            return;
        }

        $this->render('pages/students/tc_print', array(
            'title'      => 'Transfer Certificate - ' . $transfer->tc_number,
            'page_key'   => 'student-transfers',
            'transfer'   => $transfer,
        ));
    }

    /* =========================================================================
       11. Student Search & Filtering
       ========================================================================= */
    public function search()
    {
        $this->require_permission('students.view');
        $filters = array(
            'academic_year_id' => $this->input->get('academic_year_id') ?: $this->academic_year_id,
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'gender'           => $this->input->get('gender'),
            'status'           => $this->input->get('status'),
            'search'           => $this->input->get('search'),
        );

        $hasSearch = !empty($filters['search']) || !empty($filters['class_id']) || !empty($filters['section_id']) || !empty($filters['gender']) || !empty($filters['academic_year_id']) || ($filters['status'] !== '' && $filters['status'] !== NULL);

        $students = $hasSearch ? $this->Student_model->get_all($filters) : $this->Student_model->get_all(array('academic_year_id' => $this->academic_year_id), 20);
        $totalCount = $this->Student_model->count_filtered($filters);

        $classes  = $this->Class_model->get_all($filters['academic_year_id']);
        $sections = $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();

        $this->render('pages/students/search', array(
            'title'      => 'Student Search & Filtering',
            'page_key'   => 'student-search',
            'students'   => $students,
            'total_count'=> $totalCount,
            'classes'    => $classes,
            'sections'   => $sections,
            'years'      => $years,
            'filters'    => $filters,
        ));
    }
}

