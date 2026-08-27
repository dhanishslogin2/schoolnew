<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Student_model');
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
        $year_id = $this->input->get('academic_year_id') ?: NULL;
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
            'academic_year_id' => $this->input->get('academic_year_id'),
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'gender'           => $this->input->get('gender'),
            'status'           => $this->input->get('status'),
            'search'           => $this->input->get('search'),
        );

        $students = $this->Student_model->get_all($filters);
        $classes  = $this->Class_model->get_all();
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
            'academic_year_id' => $this->input->post('academic_year_id') ?: $this->input->get('academic_year_id'),
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
       2. Student Registration / Add Student
       ========================================================================= */
    public function register()
    {
        $this->add();
    }

    public function add()
    {
        $this->require_permission('students.create');

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
            $this->form_validation->set_rules('admission_number', 'Admission Number', 'required|trim');

            if ($this->form_validation->run() === TRUE) {
                $data = array(
                    'admission_number' => $this->input->post('admission_number', TRUE),
                    'first_name'       => $this->input->post('first_name', TRUE),
                    'last_name'        => $this->input->post('last_name', TRUE),
                    'gender'           => $this->input->post('gender', TRUE),
                    'date_of_birth'    => $this->input->post('date_of_birth', TRUE) ?: date('Y-m-d'),
                    'blood_group'      => $this->input->post('blood_group', TRUE),
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'class_id'         => $this->input->post('class_id') ?: 1,
                    'section_id'       => $this->input->post('section_id') ?: 1,
                    'roll_number'      => $this->input->post('roll_number', TRUE),
                    'guardian_name'    => $this->input->post('guardian_name', TRUE),
                    'guardian_relation'=> $this->input->post('guardian_relation', TRUE) ?: 'Father',
                    'guardian_phone'   => $this->input->post('guardian_phone', TRUE),
                    'guardian_email'   => $this->input->post('guardian_email', TRUE),
                    'address'          => $this->input->post('address', TRUE),
                    'status'           => 1,
                    'created_at'       => date('Y-m-d H:i:s'),
                );
                $new_id = $this->Student_model->insert($data);
                $this->session->set_flashdata('success', 'Student registered successfully.');
                redirect('students/profile/' . $new_id);
                return;
            }
        }

        $classes  = $this->Class_model->get_all();
        $sections = $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();

        $this->render('pages/students/add', array(
            'title'    => 'Student Registration',
            'page_key' => 'student-registration',
            'classes'  => $classes,
            'sections' => $sections,
            'years'    => $years,
        ));
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

        $classes  = $this->Class_model->get_all();
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
            $first = $this->Student_model->get_all();
            $student_id = !empty($first) ? $first[0]->student_id : 1;
        }

        $student = $this->Student_model->get_profile($student_id);
        if (!$student) {
            $this->session->set_flashdata('error', 'Student profile not found.');
            redirect('students');
            return;
        }

        $classes  = $this->Class_model->get_all();
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
                    'academic_year_id'  => $this->input->post('academic_year_id') ?: 1,
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
            'status'   => $this->input->get('status'),
            'class_id' => $this->input->get('class_id'),
            'search'   => $this->input->get('search'),
        );

        $admissions = $this->Student_model->get_admissions($filters);
        $classes    = $this->Class_model->get_all();
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
            'student_id'    => $this->input->get('student_id'),
            'class_id'      => $this->input->get('class_id'),
            'document_type' => $this->input->get('document_type'),
        );

        $documents = $this->Student_model->get_all_documents($filters);
        $students  = $this->Student_model->get_all();
        $classes   = $this->Class_model->get_all();

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
            'class_id'   => $class_id,
            'section_id' => $section_id,
            'status'     => 1
        ));
        $classes  = $this->Class_model->get_all();
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

        $from_year  = $this->input->get('from_year') ?: 1;
        $from_class = $this->input->get('from_class') ?: 8;
        $from_sec   = $this->input->get('from_section');

        $students = $this->Student_model->get_all(array(
            'academic_year_id' => $from_year,
            'class_id'         => $from_class,
            'section_id'       => $from_sec,
            'status'           => 1
        ));

        $promotions_history = $this->Student_model->get_promotions();
        $classes  = $this->Class_model->get_all();
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
        $students  = $this->Student_model->get_all(array('status' => 1));
        $classes   = $this->Class_model->get_all();

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
            'academic_year_id' => $this->input->get('academic_year_id'),
            'class_id'         => $this->input->get('class_id'),
            'section_id'       => $this->input->get('section_id'),
            'gender'           => $this->input->get('gender'),
            'status'           => $this->input->get('status'),
            'search'           => $this->input->get('search'),
        );

        $hasSearch = !empty($filters['search']) || !empty($filters['class_id']) || !empty($filters['section_id']) || !empty($filters['gender']) || !empty($filters['academic_year_id']) || ($filters['status'] !== '' && $filters['status'] !== NULL);

        $students = $hasSearch ? $this->Student_model->get_all($filters) : $this->Student_model->get_all(array(), 20);
        $totalCount = $this->Student_model->count_filtered($filters);

        $classes  = $this->Class_model->get_all();
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

