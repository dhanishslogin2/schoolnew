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
        $this->load->model('Id_card_model');
        $this->load->model('Setting_model');
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

    /**
     * All Students (Class & Academic Year Wise) main view.
     */
    public function all_students()
    {
        $this->require_permission('students.view');

        $years = $this->Academic_year_model->get_all();
        $selected_year = (int)($this->input->get('academic_year_id') ?: $this->academic_year_id);
        if (!$selected_year && !empty($years)) {
            $selected_year = (int)$years[0]->academic_year_id;
        }

        $classes_with_counts = $this->Student_model->get_classes_with_student_count($selected_year);
        $selected_class = $this->input->get('class_id') ? (int)$this->input->get('class_id') : NULL;

        // If no class is explicitly selected in URL, choose the first class with students or first available class
        if ($selected_class === NULL && !empty($classes_with_counts)) {
            $selected_class = (int)$classes_with_counts[0]->class_id;
        }

        $sections = $this->Section_model->get_all();

        $this->render('pages/students/all_students', array(
            'title'               => 'All Students',
            'page_key'            => 'all-students',
            'breadcrumb'          => array('Student Management', 'All Students'),
            'years'               => $years,
            'selected_year'       => $selected_year,
            'selected_class'      => $selected_class,
            'classes_with_counts' => $classes_with_counts,
            'sections'            => $sections,
        ));
    }

    /**
     * AJAX endpoint for loading class list and student counts when academic year changes.
     */
    public function class_counts_ajax()
    {
        $this->require_permission('students.view');
        $academic_year_id = (int)$this->input->get_post('academic_year_id');
        if (!$academic_year_id) {
            $academic_year_id = $this->academic_year_id;
        }

        $classes = $this->Student_model->get_classes_with_student_count($academic_year_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'          => true,
                'academic_year_id'=> $academic_year_id,
                'classes'         => $classes,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * AJAX DataTables endpoint for All Students class & academic year filtered list.
     */
    public function all_students_ajax()
    {
        $this->require_permission('students.view');

        $draw   = (int)$this->input->post('draw');
        $start  = (int)$this->input->post('start');
        $length = (int)$this->input->post('length') ?: 10;
        $order  = $this->input->post('order');
        $search = $this->input->post('search');

        $order_col_idx = isset($order[0]['column']) ? (int)$order[0]['column'] : 1;
        $order_dir     = (isset($order[0]['dir']) && strtolower($order[0]['dir']) === 'desc') ? 'DESC' : 'ASC';
        $search_val    = (isset($search['value']) && is_string($search['value'])) ? trim($search['value']) : '';

        $column_map = [
            0 => 'st.student_id',
            1 => 'st.admission_number',
            2 => 'st.roll_number',
            3 => 'c.class_name',
            4 => 'sec.section_name',
            5 => 'st.date_of_birth',
            6 => 'st.gender',
            7 => 'st.guardian_name',
            8 => 'st.guardian_phone',
            9 => 'st.status',
            10 => 'st.student_id'
        ];
        $order_col = isset($column_map[$order_col_idx]) ? $column_map[$order_col_idx] : 'st.student_id';

        $academic_year_id_raw = $this->input->post('academic_year_id') ?: $this->input->get('academic_year_id');
        $academic_year_id = (!empty($academic_year_id_raw) && is_numeric($academic_year_id_raw)) ? (int)$academic_year_id_raw : (int)$this->academic_year_id;

        $class_id_raw = $this->input->post('class_id');
        $class_id = (!empty($class_id_raw) && is_numeric($class_id_raw) && (int)$class_id_raw > 0) ? (int)$class_id_raw : NULL;

        $section_id_raw = $this->input->post('section_id');
        $section_id = (!empty($section_id_raw) && is_numeric($section_id_raw) && (int)$section_id_raw > 0) ? (int)$section_id_raw : NULL;

        $status_raw = $this->input->post('status');
        $status = ($status_raw !== NULL && $status_raw !== '' && $status_raw !== 'All' && is_numeric($status_raw)) ? (int)$status_raw : NULL;

        $custom_search = $this->input->post('custom_search');
        $effective_search = !empty($custom_search) ? trim($custom_search) : $search_val;

        $filters = array(
            'academic_year_id' => $academic_year_id,
            'class_id'         => $class_id,
            'section_id'       => $section_id,
            'status'           => $status,
            'search'           => $effective_search,
        );

        $records_total    = $this->Student_model->count_all_students(array('academic_year_id' => $academic_year_id, 'class_id' => $class_id));
        $records_filtered = $this->Student_model->count_all_students($filters);
        $students         = $this->Student_model->get_all_students_paginated($filters, $length, $start, $order_col, $order_dir);

        $data = array();
        foreach ($students as $st) {
            $fullName = trim($st->first_name . ' ' . ($st->middle_name ? $st->middle_name . ' ' : '') . $st->last_name);
            $nameParts = explode(' ', $fullName);
            $initials = '';
            foreach ($nameParts as $np) { if (!empty($np)) $initials .= strtoupper($np[0]); }
            $initials = substr($initials, 0, 2) ?: 'ST';

            // Photo Avatar
            $hasPhoto = !empty($st->photo) && file_exists(FCPATH . 'uploads/students/' . $st->photo);
            if ($hasPhoto) {
                $photoHtml = '<div class="w-9 h-9 rounded-xl border border-slate-200 overflow-hidden shrink-0 shadow-2xs">' .
                    '<img src="' . base_url('uploads/students/' . $st->photo) . '" alt="' . html_escape($fullName) . '" class="w-full h-full object-cover"/>' .
                '</div>';
            } else {
                $photoHtml = '<div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">' .
                    html_escape($initials) .
                '</div>';
            }

            // Admission Number & Roll
            $admHtml = '<span class="font-bold text-emerald-800 font-mono text-xs">' . html_escape($st->admission_number) . '</span>';

            // Student Name + Profile Link
            $nameHtml = '<div class="flex items-center gap-2.5">' .
                $photoHtml .
                '<div class="min-w-0">' .
                    '<a href="' . site_url('students/profile/' . $st->student_id) . '" class="font-bold text-slate-900 hover:text-emerald-700 transition-colors text-xs truncate block">' . html_escape($fullName) . '</a>' .
                    '<span class="text-[11px] text-slate-500 font-mono">Adm: ' . html_escape($st->admission_number) . '</span>' .
                '</div>' .
            '</div>';

            $rollHtml = !empty($st->roll_number) ? '<span class="font-mono text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">' . html_escape($st->roll_number) . '</span>' : '<span class="text-slate-400 font-mono text-xs">-</span>';

            $classHtml = '<span class="text-xs font-semibold text-slate-800">' . html_escape($st->class_name ?: 'Grade 10') . '</span>';
            $sectionHtml = !empty($st->section_name) ? '<span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">' . html_escape($st->section_name) . '</span>' : '<span class="text-slate-400 text-xs">-</span>';

            $dobFormatted = !empty($st->date_of_birth) ? date('d-m-Y', strtotime($st->date_of_birth)) : '<span class="text-slate-400">-</span>';
            $genderHtml = '<span class="text-xs text-slate-700">' . html_escape($st->gender ?: 'N/A') . '</span>';

            $guardianHtml = '<div class="text-xs">' .
                '<div class="font-bold text-slate-800 truncate">' . html_escape($st->guardian_name ?: 'test') . '</div>' .
                '<div class="text-[11px] text-slate-500 font-mono mt-0.5">' . html_escape($st->guardian_phone ?: ($st->mobile ?: 'N/A')) . '</div>' .
            '</div>';

            $contactHtml = '<span class="font-mono text-xs text-slate-700">' . html_escape($st->mobile ?: ($st->guardian_phone ?: 'N/A')) . '</span>';

            $statusBadge = ($st->status == 1)
                ? '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active</span>'
                : '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive</span>';

            // Actions Menu
            $actionsHtml = '<div class="flex items-center justify-end gap-1.5">' .
                '<a href="' . site_url('students/profile/' . $st->student_id) . '" title="View Profile" class="p-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-emerald-700 transition-colors shadow-2xs">' .
                    '<span class="material-symbols-outlined text-[17px]">visibility</span>' .
                '</a>' .
                '<a href="' . site_url('students/add?student_id=' . $st->student_id) . '" title="Edit Student" class="p-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-700 transition-colors shadow-2xs">' .
                    '<span class="material-symbols-outlined text-[17px]">edit</span>' .
                '</a>' .
                '<a href="' . site_url('students/id_cards?student_id=' . $st->student_id) . '" title="Generate ID Card" class="p-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 transition-colors shadow-2xs">' .
                    '<span class="material-symbols-outlined text-[17px]">badge</span>' .
                '</a>' .
            '</div>';

            $data[] = array(
                $nameHtml,
                $admHtml,
                $rollHtml,
                $classHtml,
                $sectionHtml,
                $dobFormatted,
                $genderHtml,
                $guardianHtml,
                $contactHtml,
                $statusBadge,
                $actionsHtml
            );
        }

        $output = array(
            'draw'            => $draw,
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $records_filtered,
            'data'            => $data,
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_hash'       => $this->security->get_csrf_hash()
        );

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
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
       Wizard Photo Upload — multipart file-only endpoint for Student Image
       POST students/wizard_photo_upload
       Allowed: JPG, JPEG, PNG (max 10MB)
       Returns JSON { success, temp_path, display_name, preview_url, error }
    ───────────────────────────────────────────────────────────────────────── */
    public function wizard_photo_upload()
    {
        try {
            $this->require_permission('students.create');

            if ($this->input->method() !== 'post') {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Invalid request.')));
                return;
            }

            if (empty($_FILES['student_image']['name'])) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Please select an image to upload.')));
                return;
            }

            $upload_err = isset($_FILES['student_image']['error']) ? (int)$_FILES['student_image']['error'] : UPLOAD_ERR_NO_FILE;
            if ($upload_err !== UPLOAD_ERR_OK) {
                $err_msg = 'Upload error. Please try again.';
                if ($upload_err === UPLOAD_ERR_INI_SIZE || $upload_err === UPLOAD_ERR_FORM_SIZE) {
                    $err_msg = 'Student image must not exceed 3 MB.';
                } elseif ($upload_err === UPLOAD_ERR_NO_FILE) {
                    $err_msg = 'Please select an image to upload.';
                }
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => $err_msg)));
                return;
            }

            // 1. Check extension whitelist (jpg, jpeg, png)
            $orig_name = $_FILES['student_image']['name'];
            $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
            $allowed_ext = array('jpg', 'jpeg', 'png');

            if (!in_array($ext, $allowed_ext, TRUE)) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Please upload a JPG, JPEG, or PNG image.')));
                return;
            }

            // 2. Binary Image Structure & MIME Validation (PHP core getimagesize — works without ext-fileinfo)
            $img_info = @getimagesize($_FILES['student_image']['tmp_name']);
            if ($img_info === FALSE) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Please upload a JPG, JPEG, or PNG image.')));
                return;
            }

            // Validate detected image type constant and MIME string
            $detected_type = isset($img_info[2]) ? $img_info[2] : 0;
            $detected_mime = isset($img_info['mime']) ? strtolower($img_info['mime']) : '';
            $allowed_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);
            $allowed_mimes = array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/x-png');

            if (!in_array($detected_type, $allowed_types, TRUE) || !in_array($detected_mime, $allowed_mimes, TRUE)) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Please upload a JPG, JPEG, or PNG image.')));
                return;
            }

            // Additional fileinfo/mime_content_type check if available on the server
            if (function_exists('finfo_open') && function_exists('finfo_file')) {
                $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                if (is_resource($finfo)) {
                    $fi_mime = @finfo_file($finfo, $_FILES['student_image']['tmp_name']);
                    @finfo_close($finfo);
                    if ($fi_mime && !in_array(strtolower($fi_mime), $allowed_mimes, TRUE)) {
                        $this->output->set_content_type('application/json')
                                     ->set_output(json_encode(array('success' => FALSE, 'error' => 'Please upload a JPG, JPEG, or PNG image.')));
                        return;
                    }
                }
            }

            // 3. Size check: 3 MB maximum
            $max_size = 3 * 1024 * 1024;
            if ($_FILES['student_image']['size'] > $max_size) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Student image must not exceed 3 MB.')));
                return;
            }

            // 4. Safe unique filename & temporary storage
            try {
                $rand_token = bin2hex(random_bytes(6));
            } catch (Exception $re) {
                $rand_token = substr(md5(uniqid(mt_rand(), true)), 0, 12);
            }
            $safe_name = 'photo_temp_' . time() . '_' . $rand_token . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
            $temp_dir  = FCPATH . 'uploads/photo_temp/';

            if (!is_dir($temp_dir)) {
                @mkdir($temp_dir, 0775, TRUE);
            }
            if (!is_dir($temp_dir)) {
                $temp_dir = './uploads/photo_temp/';
                if (!is_dir($temp_dir)) {
                    @mkdir($temp_dir, 0775, TRUE);
                }
            }

            if (!@move_uploaded_file($_FILES['student_image']['tmp_name'], $temp_dir . $safe_name)) {
                log_message('error', 'wizard_photo_upload: move_uploaded_file failed to ' . $temp_dir . $safe_name);
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Failed to store image. Please check upload folder permissions.')));
                return;
            }

            // Clean up any previously uploaded temp photo for this wizard session
            $wizard = $this->session->userdata('student_registration_wizard') ?: array();
            $old_sd = isset($wizard['student_details']) ? $wizard['student_details'] : array();
            if (!empty($old_sd['photo_temp_path'])) {
                $old_file = FCPATH . $old_sd['photo_temp_path'];
                if (is_file($old_file) && $old_file !== ($temp_dir . $safe_name)) {
                    @unlink($old_file);
                }
            }

            $temp_path = 'uploads/photo_temp/' . $safe_name;

            // Save in session
            if (!isset($wizard['student_details'])) {
                $wizard['student_details'] = array();
            }
            $wizard['student_details']['photo_temp_path']    = $temp_path;
            $wizard['student_details']['photo_display_name'] = $orig_name;
            $this->session->set_userdata('student_registration_wizard', $wizard);

            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array(
                             'success'      => TRUE,
                             'temp_path'    => $temp_path,
                             'display_name' => $orig_name,
                             'preview_url'  => base_url($temp_path),
                         )));
        } catch (Throwable $e) {
            log_message('error', 'wizard_photo_upload exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array(
                             'success' => FALSE,
                             'error'   => 'Image upload failed. Please ensure the file is a valid JPG/PNG image.'
                         )));
        }
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

        $photo_temp_path = $this->input->post('photo_temp_path', TRUE);
        $photo_display_name = $this->input->post('photo_display_name', TRUE);

        // If user cleared the photo, delete old temp file
        $old_photo = isset($wizard['student_details']['photo_temp_path']) ? $wizard['student_details']['photo_temp_path'] : '';
        if (empty($photo_temp_path) && !empty($old_photo)) {
            $old_f = FCPATH . $old_photo;
            if (is_file($old_f)) {
                @unlink($old_f);
            }
        }

        $wizard['student_details'] = array(
            'admission_number'   => $this->input->post('admission_number', TRUE),
            'first_name'         => $this->input->post('first_name',       TRUE),
            'last_name'          => $this->input->post('last_name',         TRUE),
            'gender'             => $this->input->post('gender',            TRUE) ?: 'Male',
            'date_of_birth'      => $this->input->post('date_of_birth',     TRUE) ?: date('Y-m-d'),
            'blood_group'        => $this->input->post('blood_group',       TRUE),
            'photo_temp_path'    => $photo_temp_path ?: '',
            'photo_display_name' => $photo_display_name ?: '',
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
        try {
            $this->require_permission('students.create');

            if ($this->input->method() !== 'post') {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Invalid request.')));
                return;
            }

            if (empty($_FILES['tc_document']['name'])) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'TC Document is required.')));
                return;
            }

            $upload_err = isset($_FILES['tc_document']['error']) ? (int)$_FILES['tc_document']['error'] : UPLOAD_ERR_NO_FILE;
            if ($upload_err !== UPLOAD_ERR_OK) {
                $err_msg = 'Upload error. Please try again.';
                if ($upload_err === UPLOAD_ERR_INI_SIZE || $upload_err === UPLOAD_ERR_FORM_SIZE) {
                    $err_msg = 'TC Document must not exceed 3 MB.';
                } elseif ($upload_err === UPLOAD_ERR_NO_FILE) {
                    $err_msg = 'TC Document is required.';
                }
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => $err_msg)));
                return;
            }

            // ── Security: whitelist extensions + binary header validation ──────────
            $allowed_ext  = array('pdf', 'jpg', 'jpeg', 'png');
            $orig_name    = $_FILES['tc_document']['name'];
            $ext          = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext, TRUE)) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'TC Document must be a PDF, JPG, JPEG, or PNG file.')));
                return;
            }

            // Binary header checks
            $tmp_path = $_FILES['tc_document']['tmp_name'];
            if ($ext === 'pdf') {
                $fh = @fopen($tmp_path, 'rb');
                $header = $fh ? @fread($fh, 5) : '';
                if ($fh) { @fclose($fh); }
                if (strpos($header, '%PDF-') !== 0) {
                    $this->output->set_content_type('application/json')
                                 ->set_output(json_encode(array('success' => FALSE, 'error' => 'TC Document must be a valid PDF file.')));
                    return;
                }
            } else {
                // Image verification via getimagesize
                $img_info = @getimagesize($tmp_path);
                if ($img_info === FALSE || !in_array($img_info[2], array(IMAGETYPE_JPEG, IMAGETYPE_PNG), TRUE)) {
                    $this->output->set_content_type('application/json')
                                 ->set_output(json_encode(array('success' => FALSE, 'error' => 'TC Document must be a valid PDF, JPG, JPEG, or PNG file.')));
                    return;
                }
            }

            // Size limit: 3MB
            $max_size = 3 * 1024 * 1024;
            if ($_FILES['tc_document']['size'] > $max_size) {
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'TC Document must not exceed 3 MB.')));
                return;
            }

            // ── Safe filename + temp storage ──────────────────────────────────────
            try {
                $rand_token = bin2hex(random_bytes(6));
            } catch (Exception $re) {
                $rand_token = substr(md5(uniqid(mt_rand(), true)), 0, 12);
            }
            $safe_name  = 'tc_' . time() . '_' . $rand_token . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
            $temp_dir   = FCPATH . 'uploads/tc_temp/';

            if (!is_dir($temp_dir)) {
                @mkdir($temp_dir, 0775, TRUE);
            }
            if (!is_dir($temp_dir)) {
                $temp_dir = './uploads/tc_temp/';
                if (!is_dir($temp_dir)) {
                    @mkdir($temp_dir, 0775, TRUE);
                }
            }

            if (!@move_uploaded_file($_FILES['tc_document']['tmp_name'], $temp_dir . $safe_name)) {
                log_message('error', 'wizard_tc_upload: move_uploaded_file failed to ' . $temp_dir . $safe_name);
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array('success' => FALSE, 'error' => 'Failed to store file. Please check upload folder permissions.')));
                return;
            }

            // Clean up any previously uploaded temp TC for this wizard session
            $wizard = $this->session->userdata('student_registration_wizard') ?: array();
            $old_ad = isset($wizard['academic_details']) ? $wizard['academic_details'] : array();
            if (!empty($old_ad['prev_school']['tc_temp_path'])) {
                $old_file = FCPATH . $old_ad['prev_school']['tc_temp_path'];
                if (is_file($old_file) && $old_file !== ($temp_dir . $safe_name)) {
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
        } catch (Throwable $e) {
            log_message('error', 'wizard_tc_upload exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array(
                             'success' => FALSE,
                             'error'   => 'TC Document upload failed. Please ensure the file is valid.'
                         )));
        }
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
            $this->form_validation->set_rules('tc_number',         'TC Number',               'trim|required',
                array('required' => 'TC Number is required.')
            );
            $this->form_validation->set_rules('prev_school_board',  'Previous School Board',   'trim');
            $this->form_validation->set_rules('prev_class',         'Previous Class',          'trim');
            $this->form_validation->set_rules('prev_academic_year', 'Previous Academic Year',  'trim');
            $this->form_validation->set_rules('prev_percentage',
                'Previous Percentage',
                'trim|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
            );
        }

        $errors = array();
        if ($this->form_validation->run() !== TRUE) {
            $errors = $this->_collect_validation_errors();
        }

        // Validate mandatory TC document when previous school is applicable
        if (!$no_prev_school) {
            $tc_temp_path = $this->input->post('tc_temp_path', TRUE);
            if (empty($tc_temp_path) || !is_file(FCPATH . $tc_temp_path)) {
                $errors['tc_document'] = 'TC Document is required.';
            }
        }

        if (!empty($errors)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => FALSE,
                    'errors'  => $errors,
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
            if (!empty($old_ad['prev_school']['tc_temp_path'])) {
                $old_file = FCPATH . $old_ad['prev_school']['tc_temp_path'];
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

        $class_id = !empty($ad['class_id']) ? (int)$ad['class_id'] : 1;
        $section_id = !empty($ad['section_id']) ? (int)$ad['section_id'] : NULL;
        if (!$section_id) {
            $sec_row = $this->db->get_where('tbl_sections', array('class_id' => $class_id, 'is_deleted' => 'n'))->row();
            if (!$sec_row) {
                $sec_row = $this->db->get_where('tbl_sections', array('is_deleted' => 'n'))->row();
            }
            if ($sec_row) {
                $section_id = (int)$sec_row->section_id;
            }
        }

        // ── Photo Handling: Move from temp → permanent uploads/students/ ──────
        $photo_filename   = NULL;
        $moved_photo_path = NULL;
        if (!empty($sd['photo_temp_path'])) {
            $temp_photo = FCPATH . $sd['photo_temp_path'];
            if (is_file($temp_photo)) {
                $photo_dest_dir = FCPATH . 'uploads/students/';
                if (!is_dir($photo_dest_dir)) {
                    mkdir($photo_dest_dir, 0755, TRUE);
                }
                $photo_ext = strtolower(pathinfo($sd['photo_temp_path'], PATHINFO_EXTENSION));
                if ($photo_ext === 'jpeg') $photo_ext = 'jpg';
                $photo_filename = 'photo_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $photo_ext;
                $photo_dest_path = $photo_dest_dir . $photo_filename;
                if (rename($temp_photo, $photo_dest_path)) {
                    $moved_photo_path = $photo_dest_path;
                } else {
                    $photo_filename = NULL;
                }
            }
        }

        $student_data = array(
            'admission_number'  => $sd['admission_number'],
            'first_name'        => $sd['first_name'],
            'last_name'         => $sd['last_name']         ?: '',
            'gender'            => $sd['gender']            ?: 'Male',
            'date_of_birth'     => $sd['date_of_birth']     ?: date('Y-m-d'),
            'blood_group'       => $sd['blood_group']       ?: '',
            'photo'             => $photo_filename,
            'academic_year_id'  => !empty($ad['academic_year_id']) ? (int)$ad['academic_year_id'] : $this->academic_year_id,
            'class_id'          => $class_id,
            'section_id'        => $section_id,
            'roll_number'       => !empty($ad['roll_number']) ? trim($ad['roll_number']) : '',
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

            if (!empty($ad['activities']) && is_array($ad['activities'])) {
                foreach ($ad['activities'] as $act) {
                    if (empty($act['activity_name'])) continue;
                    $all_activities[] = array(
                        'student_id'      => $new_id,
                        'category'        => 'Academic',
                        'activity_type'   => isset($act['activity_type'])   ? $act['activity_type']   : 'Competition',
                        'activity_name'   => $act['activity_name'],
                        'position_result' => isset($act['position_result']) ? $act['position_result'] : NULL,
                        'year'            => !empty($act['year'])           ? (int)$act['year']        : (int)date('Y'),
                        'description'     => isset($act['description'])     ? $act['description']     : NULL,
                        'status'          => 1,
                        'created_at'      => date('Y-m-d H:i:s'),
                    );
                }
            }

            if (!empty($ad['extracurricular']) && is_array($ad['extracurricular'])) {
                foreach ($ad['extracurricular'] as $extra) {
                    if (empty($extra['activity_name'])) continue;
                    $all_activities[] = array(
                        'student_id'      => $new_id,
                        'category'        => 'Extracurricular',
                        'activity_type'   => isset($extra['activity_type'])   ? $extra['activity_type']   : 'Sports',
                        'activity_name'   => $extra['activity_name'],
                        'level'           => isset($extra['level'])           ? $extra['level']           : NULL,
                        'position_result' => isset($extra['position_result']) ? $extra['position_result'] : NULL,
                        'year'            => !empty($extra['year'])           ? (int)$extra['year']        : (int)date('Y'),
                        'description'     => isset($extra['description'])     ? $extra['description']     : NULL,
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
       8. Student ID Cards & Services
       ========================================================================= */
    public function id_cards()
    {
        $this->require_permission('students.view');
        $class_id   = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');
        $student_id = $this->input->get('student_id');

        $students = $this->Student_model->get_all(array(
            'academic_year_id' => $this->academic_year_id,
            'class_id'         => $class_id,
            'section_id'       => $section_id,
            'status'           => 1
        ));

        $classes  = $this->Class_model->get_all($this->academic_year_id);
        $sections = $this->Section_model->get_all();
        $settings = $this->Id_card_model->get_settings();

        // Selected student for initial preview
        $selected_student = null;
        if (!empty($student_id)) {
            $selected_student = $this->Id_card_model->get_student_card_data($student_id);
        } elseif (!empty($students)) {
            $selected_student = $this->Id_card_model->get_student_card_data($students[0]->student_id);
        }

        $this->render('pages/students/id_cards', array(
            'title'            => 'Student ID Cards',
            'page_key'         => 'student-id-cards',
            'students'         => $students,
            'classes'          => $classes,
            'sections'         => $sections,
            'settings'         => $settings,
            'selected_class'   => $class_id,
            'selected_section' => $section_id,
            'selected_student' => $selected_student,
        ));
    }

    /**
     * AJAX endpoint to fetch fresh, complete data for a single student ID Card.
     */
    public function id_card_preview_ajax()
    {
        $this->require_permission('students.view');
        $student_id = (int)$this->input->get_post('student_id');

        if (!$student_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student ID is required.',
                    'csrf_token_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }

        $student = $this->Id_card_model->get_student_card_data($student_id);
        if (!$student) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student record not found.',
                    'csrf_token_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }

        $settings = $this->Id_card_model->get_settings();

        // Calculate initials for fallback photo
        $nameParts = explode(' ', trim($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name));
        $initials = '';
        foreach ($nameParts as $np) {
            if (!empty($np)) $initials .= strtoupper($np[0]);
        }
        $initials = substr($initials, 0, 2);

        // Photo URL resolution
        $photo_url = '';
        $has_photo = false;
        if (!empty($student->photo) && file_exists(FCPATH . 'uploads/students/' . $student->photo)) {
            $photo_url = base_url('uploads/students/' . $student->photo);
            $has_photo = true;
        }

        // Logo URL resolution
        $logo_url = '';
        if (!empty($settings->school_logo)) {
            if (file_exists(FCPATH . 'uploads/id_card/' . $settings->school_logo)) {
                $logo_url = base_url('uploads/id_card/' . $settings->school_logo);
            } elseif (file_exists(FCPATH . 'uploads/settings/' . $settings->school_logo)) {
                $logo_url = base_url('uploads/settings/' . $settings->school_logo);
            }
        }
        if (empty($logo_url) && file_exists(FCPATH . 'assets/logo.png')) {
            $logo_url = base_url('assets/logo.png');
        }

        // Signature URL resolution
        $signature_url = '';
        if (!empty($settings->principal_signature) && file_exists(FCPATH . 'uploads/id_card/' . $settings->principal_signature)) {
            $signature_url = base_url('uploads/id_card/' . $settings->principal_signature);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'        => true,
                'student'       => $student,
                'full_name'     => trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name),
                'initials'      => $initials ?: 'ST',
                'photo_url'     => $photo_url,
                'has_photo'     => $has_photo,
                'dob_formatted' => !empty($student->date_of_birth) ? date('d-m-Y', strtotime($student->date_of_birth)) : 'N/A',
                'class_display' => trim(($student->class_name ?: '') . ' ' . ($student->section_name ?: '')),
                'settings'      => $settings,
                'logo_url'      => $logo_url,
                'signature_url' => $signature_url,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * AJAX endpoint to fetch data for multiple students for bulk rendering.
     */
    public function id_card_bulk_data_ajax()
    {
        $this->require_permission('students.view');
        $student_ids = $this->input->post('student_ids');

        if (empty($student_ids) || !is_array($student_ids)) {
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $raw_students = $this->Student_model->get_all([
                'academic_year_id' => $this->academic_year_id,
                'class_id'         => $class_id,
                'section_id'       => $section_id,
                'status'           => 1
            ]);
            $student_ids = array_map(function($st) { return $st->student_id; }, $raw_students);
        }

        if (empty($student_ids)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'No students selected for bulk generation.',
                    'csrf_token_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }

        $students = $this->Id_card_model->get_students_bulk($student_ids);
        $settings = $this->Id_card_model->get_settings();

        // Logo URL resolution
        $logo_url = '';
        if (!empty($settings->school_logo)) {
            if (file_exists(FCPATH . 'uploads/id_card/' . $settings->school_logo)) {
                $logo_url = base_url('uploads/id_card/' . $settings->school_logo);
            } elseif (file_exists(FCPATH . 'uploads/settings/' . $settings->school_logo)) {
                $logo_url = base_url('uploads/settings/' . $settings->school_logo);
            }
        }

        // Signature URL resolution
        $signature_url = '';
        if (!empty($settings->principal_signature) && file_exists(FCPATH . 'uploads/id_card/' . $settings->principal_signature)) {
            $signature_url = base_url('uploads/id_card/' . $settings->principal_signature);
        }

        $formatted = [];
        foreach ($students as $st) {
            $nameParts = explode(' ', trim($st->first_name . ' ' . $st->middle_name . ' ' . $st->last_name));
            $initials = '';
            foreach ($nameParts as $np) {
                if (!empty($np)) $initials .= strtoupper($np[0]);
            }
            $initials = substr($initials, 0, 2);

            $photo_url = '';
            $has_photo = false;
            if (!empty($st->photo) && file_exists(FCPATH . 'uploads/students/' . $st->photo)) {
                $photo_url = base_url('uploads/students/' . $st->photo);
                $has_photo = true;
            }

            $formatted[] = [
                'student_id'       => $st->student_id,
                'admission_number' => $st->admission_number,
                'roll_number'      => $st->roll_number ?: 'N/A',
                'full_name'        => trim($st->first_name . ' ' . ($st->middle_name ? $st->middle_name . ' ' : '') . $st->last_name),
                'initials'         => $initials ?: 'ST',
                'gender'           => $st->gender ?: 'N/A',
                'blood_group'      => $st->blood_group ?: 'N/A',
                'dob_formatted'    => !empty($st->date_of_birth) ? date('d-m-Y', strtotime($st->date_of_birth)) : 'N/A',
                'class_display'    => trim(($st->class_name ?: '') . ' ' . ($st->section_name ?: '')),
                'year_name'        => $st->year_name ?: '2026-2027',
                'guardian_name'    => $st->guardian_name ?: 'N/A',
                'guardian_phone'   => $st->guardian_phone ?: 'N/A',
                'photo_url'        => $photo_url,
                'has_photo'        => $has_photo,
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'        => true,
                'count'         => count($formatted),
                'students'      => $formatted,
                'settings'      => $settings,
                'logo_url'      => $logo_url,
                'signature_url' => $signature_url,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * AJAX endpoint for ID Card Generation History DataTables.
     */
    public function id_card_history_ajax()
    {
        $this->require_permission('students.view');

        $draw        = (int)$this->input->post('draw');
        $start       = (int)$this->input->post('start');
        $length      = (int)$this->input->post('length') ?: 25;
        $order_arr   = $this->input->post('order');
        $order_col   = isset($order_arr[0]['column']) ? (int)$order_arr[0]['column'] : 0;
        $order_dir   = isset($order_arr[0]['dir']) ? $order_arr[0]['dir'] : 'DESC';
        $search_val  = $this->input->post('search')['value'] ?? '';

        $filters = [
            'academic_year_id' => $this->input->post('academic_year_id') ?: $this->academic_year_id,
            'class_id'         => $this->input->post('class_id'),
            'section_id'       => $this->input->post('section_id'),
            'status'           => $this->input->post('status'),
            'search'           => $search_val
        ];

        $total_records = $this->Id_card_model->get_history_count(['academic_year_id' => $filters['academic_year_id']]);
        $filtered_count = $this->Id_card_model->get_history_count($filters);
        $records = $this->Id_card_model->get_history($filters, $length, $start, $order_col, $order_dir);

        $data = [];
        foreach ($records as $r) {
            $fullName = trim($r->first_name . ' ' . $r->last_name);
            $classDisplay = trim(($r->class_name ?: '') . ' ' . ($r->section_name ?: ''));

            $statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-secondary-container text-on-secondary-container">' . html_escape($r->status) . '</span>';
            if ($r->status === 'Re-generated') {
                $statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-fixed text-on-primary-fixed">' . html_escape($r->status) . '</span>';
            }

            $actions = '
                <div class="flex items-center justify-end gap-1">
                    <button type="button" onclick="previewHistoryCard(' . $r->student_id . ')" title="Preview ID Card" class="p-1.5 rounded-lg text-primary hover:bg-primary-fixed/40 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                    </button>
                    <button type="button" onclick="printSingleCard(' . $r->student_id . ')" title="Print ID Card" class="p-1.5 rounded-lg text-secondary hover:bg-secondary-fixed/40 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                    </button>
                    <button type="button" onclick="downloadSinglePdf(' . $r->student_id . ')" title="Download PDF" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                    </button>
                    <button type="button" onclick="downloadSingleImage(' . $r->student_id . ', \'png\')" title="Download PNG" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-[18px]">image</span>
                    </button>
                    <button type="button" onclick="regenerateCard(' . $r->student_id . ')" title="Regenerate with Latest Info" class="p-1.5 rounded-lg text-tertiary hover:bg-tertiary-fixed-dim/30 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">sync</span>
                    </button>
                </div>
            ';

            $data[] = [
                '<span class="font-mono font-medium text-primary text-xs">' . html_escape($r->card_number) . '</span>',
                '<div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-xs font-bold text-primary shrink-0 overflow-hidden">
                        ' . (!empty($r->photo) && file_exists(FCPATH . 'uploads/students/' . $r->photo) ? '<img src="' . base_url('uploads/students/' . $r->photo) . '" class="w-full h-full object-cover"/>' : strtoupper(substr($r->first_name, 0, 1) . substr($r->last_name, 0, 1))) . '
                    </div>
                    <div>
                        <div class="font-semibold text-on-surface text-sm">' . html_escape($fullName) . '</div>
                        <div class="text-xs text-on-surface-variant font-mono">Adm: ' . html_escape($r->admission_number) . ($r->roll_number ? ' · Roll: ' . html_escape($r->roll_number) : '') . '</div>
                    </div>
                </div>',
                '<span class="text-sm font-medium text-on-surface">' . html_escape($classDisplay ?: 'N/A') . '</span>',
                '<span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-mono bg-surface-container text-on-surface-variant font-medium">v' . (int)$r->card_version . '</span>',
                '<div class="text-xs text-on-surface-variant">
                    <div>' . date('d M Y, h:i A', strtotime($r->generated_at)) . '</div>
                    <div class="text-[11px] text-on-surface-variant/70">By ' . html_escape($r->generated_by_name ?: 'System') . '</div>
                </div>',
                $statusBadge,
                $actions
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'draw'            => $draw,
                'recordsTotal'    => $total_records,
                'recordsFiltered' => $filtered_count,
                'data'            => $data,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * AJAX endpoint to record generation or print event.
     */
    public function id_card_record_ajax()
    {
        $this->require_permission('students.view');
        $student_id = (int)$this->input->post('student_id');
        $action     = trim($this->input->post('action')) ?: 'Generated';
        $user_id    = (int)($this->current_user->user_id ?? 1);

        if (!$student_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Student ID is required.',
                    'csrf_token_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }

        $record = $this->Id_card_model->record_generation($student_id, $this->academic_year_id, $user_id, $action);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'ID card activity recorded.',
                'record'  => $record,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * AJAX endpoint to regenerate an ID card with latest live student information.
     */
    public function id_card_regenerate_ajax()
    {
        $this->require_permission('students.view');
        $student_id = (int)$this->input->post('student_id');
        $user_id    = (int)($this->current_user->user_id ?? 1);

        if (!$student_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student ID is required.',
                    'csrf_token_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }

        // 1. Fetch fresh student data directly from DB
        $student = $this->Id_card_model->get_student_card_data($student_id);
        if (!$student) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Student record not found.',
                    'csrf_token_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }

        // 2. Increment version and update history
        $record = $this->Id_card_model->record_generation($student_id, $this->academic_year_id, $user_id, 'Regenerate');

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'ID card regenerated successfully with latest student information (v' . ($record->card_version ?? 1) . ').',
                'record'  => $record,
                'csrf_token_name' => $this->security->get_csrf_token_name(),
                'csrf_hash'       => $this->security->get_csrf_hash()
            ]));
    }

    /**
     * Save ID Card configuration settings (School name, address, signature, logo, etc.).
     */
    public function id_card_settings_save()
    {
        $this->require_permission('students.view');

        if ($this->input->method() !== 'post') {
            redirect('students/id_cards#settings');
            return;
        }

        $card_title        = trim($this->input->post('card_title', TRUE)) ?: 'STUDENT IDENTITY CARD';
        $school_name       = trim($this->input->post('school_name', TRUE));
        $school_code       = trim($this->input->post('school_code', TRUE));
        $school_address    = trim($this->input->post('school_address', TRUE));
        $phone             = trim($this->input->post('phone', TRUE));
        $email             = trim($this->input->post('email', TRUE));
        $website           = trim($this->input->post('website', TRUE));
        $emergency_contact = trim($this->input->post('emergency_contact', TRUE));
        $principal_name    = trim($this->input->post('principal_name', TRUE));
        $return_text       = trim($this->input->post('return_text', TRUE));
        $validity_text     = trim($this->input->post('validity_text', TRUE));
        $accent_color      = trim($this->input->post('accent_color', TRUE)) ?: '#091426';
        $secondary_color   = trim($this->input->post('secondary_color', TRUE)) ?: '#006c4a';

        $data = [
            'card_title'        => $card_title,
            'school_name'       => $school_name,
            'school_code'       => $school_code,
            'school_address'    => $school_address,
            'phone'             => $phone,
            'email'             => $email,
            'website'           => $website,
            'emergency_contact' => $emergency_contact,
            'principal_name'    => $principal_name,
            'return_text'       => $return_text,
            'validity_text'     => $validity_text,
            'accent_color'      => $accent_color,
            'secondary_color'   => $secondary_color,
        ];

        // Handle file uploads (Logo and Principal Signature)
        $upload_dir = FCPATH . 'uploads/id_card/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, TRUE);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|svg';
        $config['max_size']      = 5120; // 5MB
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if (!empty($_FILES['school_logo']['name'])) {
            $this->upload->initialize($config);
            if ($this->upload->do_upload('school_logo')) {
                $upload_res = $this->upload->data();
                $data['school_logo'] = $upload_res['file_name'];
            }
        }

        if (!empty($_FILES['principal_signature']['name'])) {
            $this->upload->initialize($config);
            if ($this->upload->do_upload('principal_signature')) {
                $upload_res = $this->upload->data();
                $data['principal_signature'] = $upload_res['file_name'];
            }
        }

        $this->Id_card_model->save_settings($data);

        // Also update tbl_school_settings if relevant to keep core info in sync
        $this->Setting_model->update_settings([
            'school_name'    => $school_name,
            'school_code'    => $school_code,
            'principal_name' => $principal_name,
            'phone'          => $phone,
            'email'          => $email,
            'website'        => $website,
            'address'        => $school_address
        ]);

        if ($this->input->is_ajax_request()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => 'ID Card settings saved successfully.',
                    'csrf_token_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash'       => $this->security->get_csrf_hash()
                ]));
        }

        $this->session->set_flashdata('success', 'ID Card settings saved successfully.');
        redirect('students/id_cards');
    }

    /**
     * Standalone clean print view for single or bulk ID cards.
     */
    public function id_card_print()
    {
        $this->require_permission('students.view');
        $student_id  = $this->input->get('student_id');
        $student_ids = $this->input->get('student_ids');
        $side        = $this->input->get('side') ?: 'both'; // 'both', 'front', 'back'

        $ids = [];
        if (!empty($student_id)) {
            $ids[] = (int)$student_id;
        } elseif (!empty($student_ids)) {
            $ids = array_map('intval', explode(',', $student_ids));
        } else {
            $class_id   = $this->input->get('class_id');
            $section_id = $this->input->get('section_id');
            $raw_students = $this->Student_model->get_all([
                'academic_year_id' => $this->academic_year_id,
                'class_id'         => $class_id,
                'section_id'       => $section_id,
                'status'           => 1
            ]);
            $ids = array_map(function($st) { return $st->student_id; }, $raw_students);
        }

        if (empty($ids)) {
            show_error('No students selected for printing.', 404);
            return;
        }

        $students = $this->Id_card_model->get_students_bulk($ids);
        $settings = $this->Id_card_model->get_settings();

        // Record print action for each student
        $uid = (int)($this->current_user->user_id ?? 1);
        foreach ($ids as $sid) {
            $this->Id_card_model->record_generation($sid, $this->academic_year_id, $uid, 'Printed');
        }

        $this->load->view('pages/students/id_card_print', [
            'students' => $students,
            'settings' => $settings,
            'side'     => $side
        ]);
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

