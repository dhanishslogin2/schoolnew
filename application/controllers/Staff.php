<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Staff_model');
        $this->load->model('Staff_document_type_model');
        $this->load->model('Department_model');
        $this->load->model('Designation_model');
        $this->load->model('Subject_model');
        $this->load->model('Class_model');
        $this->load->model('Division_model');
        $this->load->model('Section_model');
        $this->load->model('Academic_year_model');
        $this->load->library('form_validation');
    }

    /* =========================================================================
       1. Staff Management Overview
       ========================================================================= */
    public function index()
    {
        $this->overview();
    }

    public function overview()
    {
        $this->require_permission('staff.view');
        $stats = $this->Staff_model->get_dashboard_stats();

        $this->render('pages/staff/overview', array(
            'title'        => 'Staff Management Overview',
            'page_key'     => 'staff',
            'breadcrumb'   => array('Staff Management', 'Overview'),
            'stats'        => $stats,
        ));
    }

    public function directory()
    {
        $this->require_permission('staff.view');
        $dept_id    = $this->input->get('department_id');
        $desig_id   = $this->input->get('designation_id');
        $staff_type = $this->input->get('staff_type');
        $status     = $this->input->get('status');
        $search     = $this->input->get('search');

        $staff = $this->Staff_model->get_all(array(
            'department_id'  => $dept_id,
            'designation_id' => $desig_id,
            'staff_type'     => $staff_type,
            'status'         => ($status !== NULL && $status !== '') ? $status : '1',
            'search'         => $search,
        ));

        $departments  = $this->Department_model->get_all();
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/index', array(
            'title'        => 'All Staff',
            'page_key'     => 'staff-directory',
            'breadcrumb'   => array('Staff Management', 'All Staff'),
            'staff'        => $staff,
            'departments'  => $departments,
            'designations' => $designations,
        ));
    }

    public function ajax_list()
    {
        $this->require_permission('staff.view');

        $draw   = (int)$this->input->post('draw');
        $start  = (int)$this->input->post('start');
        $length = (int)$this->input->post('length');
        $order  = $this->input->post('order');
        $search = $this->input->post('search');

        $order_col_idx = isset($order[0]['column']) ? (int)$order[0]['column'] : 0;
        $order_dir     = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $search_val    = isset($search['value']) ? trim($search['value']) : '';

        $filters = array(
            'department_id'  => $this->input->post('department_id') ?: $this->input->get('department_id'),
            'designation_id' => $this->input->post('designation_id') ?: $this->input->get('designation_id'),
            'staff_type'     => $this->input->post('staff_type') ?: $this->input->get('staff_type'),
            'status'         => $this->input->post('status') !== NULL ? $this->input->post('status') : $this->input->get('status'),
            'search'         => $search_val,
        );

        $records_total    = $this->Staff_model->get_datatables_count_all();
        $records_filtered = $this->Staff_model->count_filtered($filters);
        $staff_list       = $this->Staff_model->get_datatables_data($filters, $length, $start, $order_col_idx, $order_dir);

        $data = array();
        foreach ($staff_list as $s) {
            $initials = school_initials($s->full_name);

            $statusBadge = ($s->status == 1)
                ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-secondary-container text-on-secondary-container">Active</span>'
                : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">Inactive</span>';

            $codeCol = '<a href="' . site_url('staff/profile/' . $s->staff_id) . '" class="text-primary font-medium hover:underline font-mono">' . html_escape($s->employee_code ?: '—') . '</a>';

            $avatarImg = (!empty($s->photo) && file_exists(FCPATH . 'uploads/staff/' . $s->photo))
                ? '<img src="' . base_url('uploads/staff/' . $s->photo) . '" alt="' . html_escape($s->full_name) . '" class="w-8 h-8 rounded-full object-cover shrink-0 border border-outline-variant/60 shadow-sm"/>'
                : '<div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center text-[11px] font-semibold shrink-0">' . html_escape($initials) . '</div>';

            $nameCol = '<div class="flex items-center gap-2.5">' .
                $avatarImg .
                '<div>' .
                    '<div class="font-medium text-on-surface">' . html_escape($s->full_name) . '</div>' .
                    '<div class="text-[12px] text-on-surface-variant">' . html_escape($s->staff_type ?: 'Staff') . '</div>' .
                '</div>' .
            '</div>';

            $contactCol = '<div>' .
                '<div class="text-on-surface">' . html_escape($s->email ?: '—') . '</div>' .
                '<div class="text-[12px] text-on-surface-variant">' . html_escape($s->phone ?: '—') . '</div>' .
            '</div>';

            $actionsCol = '<div class="flex items-center justify-end gap-1.5">' .
                '<a href="' . site_url('staff/profile/' . $s->staff_id) . '" title="View Profile" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span></a>' .
                '<a href="' . site_url('staff/edit/' . $s->staff_id) . '" title="Edit Staff" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">edit</span></a>' .
            '</div>';

            $data[] = array(
                $codeCol,
                $nameCol,
                html_escape($s->department_name ?: '—'),
                html_escape($s->designation_name ?: '—'),
                $contactCol,
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
       2. Teachers Directory
       ========================================================================= */
    public function teachers()
    {
        $this->require_permission('staff.view');
        $dept_id  = $this->input->get('department_id');
        $desig_id = $this->input->get('designation_id');
        $subject  = $this->input->get('subject');
        $search   = $this->input->get('search');

        $teachers = $this->Staff_model->get_teachers(array(
            'department_id'  => $dept_id,
            'designation_id' => $desig_id,
            'subject_name'   => $subject,
            'search'         => $search,
        ));

        $departments  = $this->Department_model->get_all();
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/teachers', array(
            'title'        => 'Teachers',
            'page_key'     => 'teachers',
            'breadcrumb'   => array('Staff Management', 'All Staff', 'Teachers'),
            'teachers'     => $teachers,
            'departments'  => $departments,
            'designations' => $designations,
        ));
    }

    /* =========================================================================
       3. Non-Teaching Staff Directory
       ========================================================================= */
    public function non_teaching()
    {
        $this->require_permission('staff.view');
        $dept_id  = $this->input->get('department_id');
        $desig_id = $this->input->get('designation_id');
        $search   = $this->input->get('search');

        $staff = $this->Staff_model->get_non_teaching(array(
            'department_id'  => $dept_id,
            'designation_id' => $desig_id,
            'search'         => $search,
        ));

        $departments  = $this->Department_model->get_all();
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/non_teaching', array(
            'title'        => 'Non-Teaching Staff',
            'page_key'     => 'non-teaching-staff',
            'breadcrumb'   => array('Staff Management', 'All Staff', 'Non-Teaching Staff'),
            'staff'        => $staff,
            'departments'  => $departments,
            'designations' => $designations,
        ));
    }

    /* =========================================================================
       4. Staff Registration / Add
       ========================================================================= */
    public function add()
    {
        return $this->register();
    }

    public function register()
    {
        $this->require_permission('staff.create');

        $document_types = $this->Staff_document_type_model->get_active_types();
        $doc_errors = array();
        $photo_error = NULL;

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('full_name', 'Staff Name', 'required|trim');
            $this->form_validation->set_rules('employee_code', 'Employee ID', 'required|trim');
            $this->form_validation->set_rules('phone', 'Phone Number', 'required|trim');
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|trim');
            $this->form_validation->set_rules('staff_type', 'Staff Type', 'required');
            $this->form_validation->set_rules('department_id', 'Department', 'required');
            $this->form_validation->set_rules('designation_id', 'Designation', 'required');
            $this->form_validation->set_rules('joining_date', 'Joining Date', 'required');

            // 1. Process Staff Profile Photo (Crop / Upload)
            $photo_result = $this->process_staff_photo();
            $photo_filename = NULL;
            if ($photo_result['success'] === FALSE) {
                $photo_error = $photo_result['error'];
            } else {
                $photo_filename = $photo_result['file_name'];
            }

            // 2. Validate Dynamic Required Staff Documents
            $allowedExtensions = array('pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx');
            $maxFileSize = 10 * 1024 * 1024; // 10MB

            foreach ($document_types as $dt) {
                $typeId = $dt->id;
                $hasFile = isset($_FILES['staff_doc_file']['name'][$typeId]) && 
                           !empty($_FILES['staff_doc_file']['name'][$typeId]) &&
                           isset($_FILES['staff_doc_file']['error'][$typeId]) && 
                           $_FILES['staff_doc_file']['error'][$typeId] === UPLOAD_ERR_OK;

                if (!$hasFile) {
                    $doc_errors[] = 'Please upload the required ' . $dt->document_name . ' document.';
                } else {
                    $fileName = $_FILES['staff_doc_file']['name'][$typeId];
                    $fileSize = $_FILES['staff_doc_file']['size'][$typeId];
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowedExtensions)) {
                        $doc_errors[] = $dt->document_name . ': Invalid file format (.' . $ext . '). Allowed: ' . implode(', ', $allowedExtensions) . '.';
                    }
                    if ($fileSize > $maxFileSize || $fileSize <= 0) {
                        $doc_errors[] = $dt->document_name . ': File size exceeds the 10MB limit.';
                    }
                }
            }

            $formValid = $this->form_validation->run();

            if ($formValid === TRUE && empty($doc_errors) && empty($photo_error)) {
                $staffType = $this->input->post('staff_type');
                $data = array(
                    'employee_code'     => $this->input->post('employee_code'),
                    'full_name'         => $this->input->post('full_name'),
                    'gender'            => $this->input->post('gender') ?: 'Male',
                    'date_of_birth'     => $this->input->post('date_of_birth') ?: NULL,
                    'blood_group'       => $this->input->post('blood_group') ?: NULL,
                    'phone'             => $this->input->post('phone'),
                    'alternate_phone'   => $this->input->post('alternate_phone') ?: NULL,
                    'email'             => $this->input->post('email'),
                    'address'           => $this->input->post('address') ?: NULL,
                    'staff_type'        => $staffType,
                    'category'          => ($staffType === 'teacher') ? 'Teacher' : 'Non-Teaching',
                    'department_id'     => $this->input->post('department_id'),
                    'designation_id'    => $this->input->post('designation_id'),
                    'joining_date'      => $this->input->post('joining_date'),
                    'salary'            => $this->input->post('salary') ? floatval($this->input->post('salary')) : 0.00,
                    'qualification'     => $this->input->post('qualification') ?: NULL,
                    'experience'        => $this->input->post('experience') ?: NULL,
                    'specialization'    => ($staffType === 'teacher') ? ($this->input->post('specialization') ?: NULL) : NULL,
                    'employment_status' => $this->input->post('employment_status') ?: 'Active',
                    'photo'             => $photo_filename,
                    'status'            => 1,
                    'created_at'        => date('Y-m-d H:i:s'),
                );

                $staff_id = $this->Staff_model->insert($data);

                // Process Document Uploads
                $uploadDir = FCPATH . 'uploads/staff_docs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                foreach ($document_types as $dt) {
                    $typeId = $dt->id;
                    if (isset($_FILES['staff_doc_file']['name'][$typeId]) && !empty($_FILES['staff_doc_file']['name'][$typeId])) {
                        $origName = $_FILES['staff_doc_file']['name'][$typeId];
                        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                        $safeName = 'doc_' . $staff_id . '_' . $typeId . '_' . time() . '_' . substr(md5(uniqid()), 0, 8) . '.' . $ext;
                        $destPath = $uploadDir . $safeName;

                        if (move_uploaded_file($_FILES['staff_doc_file']['tmp_name'][$typeId], $destPath)) {
                            $mimeType = $_FILES['staff_doc_file']['type'][$typeId] ?: 'application/octet-stream';
                            $fileSize = $_FILES['staff_doc_file']['size'][$typeId];

                            $this->Staff_model->add_document(array(
                                'staff_id'         => $staff_id,
                                'document_type_id' => $typeId,
                                'document_type'    => $dt->document_name,
                                'document_name'    => $dt->document_name,
                                'file_name'        => $origName,
                                'file_path'        => 'uploads/staff_docs/' . $safeName,
                                'file_type'        => $mimeType,
                                'file_size'        => $fileSize,
                                'mime_type'        => $mimeType,
                                'uploaded_by'      => $this->current_user->user_id ?? 1,
                                'status'           => 1,
                                'is_deleted'       => 'n',
                                'created_at'       => date('Y-m-d H:i:s'),
                            ));
                        }
                    }
                }

                $this->session->set_flashdata('success', 'Staff member registered successfully!');
                redirect('staff/profile/' . $staff_id);
                return;
            }
        }

        $departments  = $this->Department_model->get_all();
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/add', array(
            'title'          => 'Staff Registration',
            'page_key'       => 'staff_add',
            'breadcrumb'     => array('Staff Management', 'Add Staff'),
            'departments'    => $departments,
            'designations'   => $designations,
            'document_types' => $document_types,
            'doc_errors'     => $doc_errors,
            'photo_error'    => $photo_error,
        ));
    }

    /* =========================================================================
       5. Edit Staff
       ========================================================================= */
    public function edit($staff_id = NULL)
    {
        $this->require_permission('staff.edit');

        if (empty($staff_id)) {
            redirect('staff');
        }

        $staff = $this->Staff_model->get_by_id($staff_id);
        if (!$staff) {
            show_404();
        }

        $document_types    = $this->Staff_document_type_model->get_active_types();
        $existing_docs_map = $this->Staff_model->get_staff_documents_map($staff_id);
        $doc_errors        = array();
        $photo_error       = NULL;

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('full_name', 'Staff Name', 'required|trim');
            $this->form_validation->set_rules('employee_code', 'Employee ID', 'required|trim');
            $this->form_validation->set_rules('phone', 'Phone Number', 'required|trim');
            $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|trim');

            // Handle Photo Removal if requested
            if ($this->input->post('remove_photo') === '1') {
                $this->Staff_model->delete_photo($staff_id);
                $staff->photo = NULL;
            }

            // Handle New/Replaced Photo Upload
            $new_photo = NULL;
            $photo_result = $this->process_staff_photo($staff_id);
            if ($photo_result['success'] === FALSE) {
                $photo_error = $photo_result['error'];
            } elseif (!empty($photo_result['file_name'])) {
                $new_photo = $photo_result['file_name'];
            }

            $allowedExtensions = array('pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx');
            $maxFileSize = 10 * 1024 * 1024; // 10MB

            // Validate any replacement documents uploaded
            if (!empty($_FILES['staff_doc_file']['name'])) {
                foreach ($_FILES['staff_doc_file']['name'] as $typeId => $name) {
                    if (!empty($name) && isset($_FILES['staff_doc_file']['error'][$typeId]) && $_FILES['staff_doc_file']['error'][$typeId] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $size = $_FILES['staff_doc_file']['size'][$typeId];
                        if (!in_array($ext, $allowedExtensions)) {
                            $doc_errors[] = 'Invalid file format for replacement document (' . $name . ').';
                        }
                        if ($size > $maxFileSize || $size <= 0) {
                            $doc_errors[] = 'File size exceeds 10MB limit (' . $name . ').';
                        }
                    }
                }
            }

            if ($this->form_validation->run() === TRUE && empty($doc_errors) && empty($photo_error)) {
                $staffType = $this->input->post('staff_type') ?: $staff->staff_type;
                $data = array(
                    'employee_code'     => $this->input->post('employee_code'),
                    'full_name'         => $this->input->post('full_name'),
                    'gender'            => $this->input->post('gender') ?: $staff->gender,
                    'date_of_birth'     => $this->input->post('date_of_birth') ?: $staff->date_of_birth,
                    'blood_group'       => $this->input->post('blood_group') ?: $staff->blood_group,
                    'phone'             => $this->input->post('phone'),
                    'alternate_phone'   => $this->input->post('alternate_phone'),
                    'email'             => $this->input->post('email'),
                    'address'           => $this->input->post('address'),
                    'staff_type'        => $staffType,
                    'category'          => ($staffType === 'teacher') ? 'Teacher' : 'Non-Teaching',
                    'department_id'     => $this->input->post('department_id'),
                    'designation_id'    => $this->input->post('designation_id'),
                    'joining_date'      => $this->input->post('joining_date'),
                    'salary'            => $this->input->post('salary') ? floatval($this->input->post('salary')) : $staff->salary,
                    'qualification'     => $this->input->post('qualification'),
                    'experience'        => $this->input->post('experience'),
                    'specialization'    => ($staffType === 'teacher') ? $this->input->post('specialization') : NULL,
                    'employment_status' => $this->input->post('employment_status') ?: $staff->employment_status,
                    'updated_at'        => date('Y-m-d H:i:s'),
                );

                if (!empty($new_photo)) {
                    $oldPhoto = $staff->photo;
                    $data['photo'] = $new_photo;
                }

                $this->Staff_model->update($staff_id, $data);

                // If photo was successfully updated and an old photo file existed, unlink the old file
                if (!empty($new_photo) && !empty($oldPhoto) && $oldPhoto !== $new_photo) {
                    $oldFilePath = FCPATH . 'uploads/staff/' . $oldPhoto;
                    if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }

                // Process replacement or newly uploaded documents
                $uploadDir = FCPATH . 'uploads/staff_docs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (!empty($_FILES['staff_doc_file']['name'])) {
                    foreach ($_FILES['staff_doc_file']['name'] as $typeId => $origName) {
                        if (!empty($origName) && isset($_FILES['staff_doc_file']['error'][$typeId]) && $_FILES['staff_doc_file']['error'][$typeId] === UPLOAD_ERR_OK) {
                            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                            $safeName = 'doc_' . $staff_id . '_' . $typeId . '_' . time() . '_' . substr(md5(uniqid()), 0, 8) . '.' . $ext;
                            $destPath = $uploadDir . $safeName;

                            if (move_uploaded_file($_FILES['staff_doc_file']['tmp_name'][$typeId], $destPath)) {
                                $mimeType = $_FILES['staff_doc_file']['type'][$typeId] ?: 'application/octet-stream';
                                $fileSize = $_FILES['staff_doc_file']['size'][$typeId];

                                // Find doc type name
                                $dtObj = $this->Staff_document_type_model->get_by_id($typeId);
                                $docName = $dtObj ? $dtObj->document_name : 'Staff Document';

                                // Deactivate old document if replacing
                                if (isset($existing_docs_map[$typeId])) {
                                    $this->Staff_model->delete_document($existing_docs_map[$typeId]->document_id);
                                }

                                $this->Staff_model->add_document(array(
                                    'staff_id'         => $staff_id,
                                    'document_type_id' => $typeId,
                                    'document_type'    => $docName,
                                    'document_name'    => $docName,
                                    'file_name'        => $origName,
                                    'file_path'        => 'uploads/staff_docs/' . $safeName,
                                    'file_type'        => $mimeType,
                                    'file_size'        => $fileSize,
                                    'mime_type'        => $mimeType,
                                    'uploaded_by'      => $this->current_user->user_id ?? 1,
                                    'status'           => 1,
                                    'is_deleted'       => 'n',
                                    'created_at'       => date('Y-m-d H:i:s'),
                                ));
                            }
                        }
                    }
                }

                $this->session->set_flashdata('success', 'Staff details updated successfully!');
                redirect('staff/profile/' . $staff_id);
                return;
            }
        }

        $departments  = $this->Department_model->get_all();
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/edit', array(
            'title'             => 'Edit Staff: ' . $staff->full_name,
            'page_key'          => 'staff_edit',
            'breadcrumb'        => array('Staff Management', 'Edit Staff'),
            'staff'             => $staff,
            'staff_id'          => $staff_id,
            'departments'       => $departments,
            'designations'      => $designations,
            'document_types'    => $document_types,
            'existing_docs_map' => $existing_docs_map,
            'doc_errors'        => $doc_errors,
            'photo_error'       => $photo_error,
        ));
    }

    public function remove_photo($staff_id = NULL)
    {
        $this->require_permission('staff.edit');

        if (empty($staff_id)) {
            show_404();
            return;
        }

        $this->Staff_model->delete_photo($staff_id);

        if ($this->input->is_ajax_request()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => TRUE, 'message' => 'Staff photo removed successfully.')));
            return;
        }

        $this->session->set_flashdata('success', 'Staff photo removed.');
        $redirect = $this->input->get('redirect_to') ?: ('staff/edit/' . $staff_id);
        redirect($redirect);
    }

    /* =========================================================================
       Private: Process and Validate Staff Photo Upload / Base64 Cropped Data
       ========================================================================= */
    private function process_staff_photo($staff_id = NULL)
    {
        $uploadDir = FCPATH . 'uploads/staff/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $maxSize = 3 * 1024 * 1024; // 3 MB
        $allowedExts = array('jpg', 'jpeg', 'png', 'webp');
        $allowedMimes = array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp');
        $allowedTypes = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);
        if (defined('IMAGETYPE_WEBP')) {
            $allowedTypes[] = IMAGETYPE_WEBP;
        }

        // 1. Check if cropped image payload is submitted (Base64 data URL from Cropper)
        $croppedData = $this->input->post('cropped_image_data');
        if (!empty($croppedData)) {
            if (preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,([A-Za-z0-9+\/=\r\n]+)$/i', $croppedData, $matches)) {
                $ext = strtolower($matches[1]);
                if ($ext === 'jpeg') $ext = 'jpg';
                $decoded = base64_decode($matches[2]);

                if ($decoded === FALSE || strlen($decoded) < 50) {
                    return array('success' => FALSE, 'error' => 'The selected file is not a valid image.');
                }

                if (strlen($decoded) > $maxSize) {
                    return array('success' => FALSE, 'error' => 'Staff image must not exceed 3 MB.');
                }

                $imgInfo = @getimagesizefromstring($decoded);
                if ($imgInfo === FALSE) {
                    return array('success' => FALSE, 'error' => 'The selected file is not a valid image.');
                }

                $detectedType = isset($imgInfo[2]) ? $imgInfo[2] : 0;
                $detectedMime = isset($imgInfo['mime']) ? strtolower($imgInfo['mime']) : '';
                if (!in_array($detectedType, $allowedTypes, TRUE) && !in_array($detectedMime, $allowedMimes, TRUE)) {
                    return array('success' => FALSE, 'error' => 'Only JPG, JPEG, PNG, and WEBP images are allowed.');
                }

                $safeName = 'staff_' . ($staff_id ?: 'new') . '_' . date('YmdHis') . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 6) . '.' . $ext;
                $destPath = $uploadDir . $safeName;

                if (file_put_contents($destPath, $decoded) !== FALSE) {
                    return array('success' => TRUE, 'file_name' => $safeName);
                } else {
                    return array('success' => FALSE, 'error' => 'Unable to upload staff image. Please try again.');
                }
            } else {
                return array('success' => FALSE, 'error' => 'Invalid cropped image format.');
            }
        }

        // 2. Fallback check: Direct standard file upload $_FILES['staff_image']
        if (isset($_FILES['staff_image']['name']) && !empty($_FILES['staff_image']['name'])) {
            $fileError = isset($_FILES['staff_image']['error']) ? (int)$_FILES['staff_image']['error'] : UPLOAD_ERR_NO_FILE;
            if ($fileError !== UPLOAD_ERR_OK) {
                if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                    return array('success' => FALSE, 'error' => 'Staff image must not exceed 3 MB.');
                }
                return array('success' => FALSE, 'error' => 'Unable to upload staff image. Please try again.');
            }

            $origName = $_FILES['staff_image']['name'];
            $fileSize = $_FILES['staff_image']['size'];
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExts, TRUE)) {
                return array('success' => FALSE, 'error' => 'Only JPG, JPEG, PNG, and WEBP images are allowed.');
            }

            if ($fileSize > $maxSize || $fileSize <= 0) {
                return array('success' => FALSE, 'error' => 'Staff image must not exceed 3 MB.');
            }

            $imgInfo = @getimagesize($_FILES['staff_image']['tmp_name']);
            if ($imgInfo === FALSE) {
                return array('success' => FALSE, 'error' => 'The selected file is not a valid image.');
            }

            $detectedType = isset($imgInfo[2]) ? $imgInfo[2] : 0;
            $detectedMime = isset($imgInfo['mime']) ? strtolower($imgInfo['mime']) : '';
            if (!in_array($detectedType, $allowedTypes, TRUE) && !in_array($detectedMime, $allowedMimes, TRUE)) {
                return array('success' => FALSE, 'error' => 'Only JPG, JPEG, PNG, and WEBP images are allowed.');
            }

            if ($ext === 'jpeg') $ext = 'jpg';
            $safeName = 'staff_' . ($staff_id ?: 'new') . '_' . date('YmdHis') . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 6) . '.' . $ext;
            $destPath = $uploadDir . $safeName;

            if (move_uploaded_file($_FILES['staff_image']['tmp_name'], $destPath)) {
                return array('success' => TRUE, 'file_name' => $safeName);
            } else {
                return array('success' => FALSE, 'error' => 'Unable to upload staff image. Please try again.');
            }
        }

        // No image provided
        return array('success' => TRUE, 'file_name' => NULL);
    }

    /* =========================================================================
       6. Delete Staff (Safe Deactivation)
       ========================================================================= */
    public function delete($staff_id = NULL)
    {
        $this->require_permission('staff.delete');

        if (!empty($staff_id)) {
            $this->Staff_model->soft_delete($staff_id);
            $this->session->set_flashdata('success', 'Staff member deactivated safely.');
        }
        redirect('staff');
    }

    /* =========================================================================
       7. Staff Profile View
       ========================================================================= */
    public function profile($staff_id = NULL)
    {
        $this->require_permission('staff.view');

        if (empty($staff_id)) {
            redirect('staff');
        }

        $staff = $this->Staff_model->get_profile($staff_id);
        if (!$staff) {
            show_404();
        }

        $departments       = $this->Department_model->get_all();
        $designations      = $this->Designation_model->get_all();
        $years             = $this->Academic_year_model->get_all();
        $classes           = $this->Class_model->get_all();
        $sections          = $this->Division_model->get_all();
        $subjects          = $this->Subject_model->get_all();
        $document_types    = $this->Staff_document_type_model->get_active_types();
        $existing_docs_map = $this->Staff_model->get_staff_documents_map($staff_id);

        $this->render('pages/staff/profile', array(
            'title'             => 'Staff Profile: ' . $staff->full_name,
            'page_key'          => ($staff->staff_type === 'teacher') ? 'teachers' : 'non-teaching-staff',
            'breadcrumb'        => array('Staff Management', 'Staff Profile'),
            'staff'             => $staff,
            'staff_id'          => $staff_id,
            'departments'       => $departments,
            'designations'      => $designations,
            'years'             => $years,
            'classes'           => $classes,
            'sections'          => $sections,
            'subjects'          => $subjects,
            'document_types'    => $document_types,
            'existing_docs_map' => $existing_docs_map,
        ));
    }

    /* =========================================================================
       8. Departments & Designations Management
       ========================================================================= */
    public function departments_designations()
    {
        $this->require_permission('staff.edit');

        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'add_department') {
                $this->Department_model->insert(array(
                    'department_name' => $this->input->post('department_name'),
                    'description'     => $this->input->post('description'),
                    'status'          => 1,
                    'created_at'      => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Department added successfully!');
            } elseif ($action === 'add_designation') {
                $this->Designation_model->insert(array(
                    'designation_name' => $this->input->post('designation_name'),
                    'category'         => $this->input->post('category') ?: 'Teaching',
                    'description'      => $this->input->post('description'),
                    'status'           => 1,
                    'created_at'       => date('Y-m-d H:i:s')
                ));
                $this->session->set_flashdata('success', 'Designation added successfully!');
            }
            redirect('staff/departments_designations');
        }

        $departments  = $this->Department_model->get_all();
        $designations = $this->Designation_model->get_all();

        $this->render('pages/staff/departments_designations', array(
            'title'        => 'Departments & Designations',
            'page_key'     => 'designations',
            'breadcrumb'   => array('Staff Management', 'Departments & Designations'),
            'departments'  => $departments,
            'designations' => $designations,
        ));
    }

    public function departments()
    {
        redirect('staff/departments_designations');
    }

    public function designations()
    {
        redirect('staff/departments_designations');
    }

    /* =========================================================================
       9. Staff Documents
       ========================================================================= */
    public function documents()
    {
        $this->require_permission('staff.view');

        $staff_id  = $this->input->get('staff_id');
        $doc_type  = $this->input->get('document_type');
        $dept_id   = $this->input->get('department_id');

        $documents = $this->Staff_model->get_all_documents(array(
            'staff_id'      => $staff_id,
            'document_type' => $doc_type,
            'department_id' => $dept_id,
        ));

        $staff_list     = $this->Staff_model->get_all(array('status' => 1));
        $departments    = $this->Department_model->get_all();
        $document_types = $this->Staff_document_type_model->get_active_types();

        $this->render('pages/staff/documents', array(
            'title'          => 'Staff Documents',
            'page_key'       => 'staff_documents',
            'breadcrumb'     => array('Staff Management', 'Staff Documents'),
            'documents'      => $documents,
            'staff_list'     => $staff_list,
            'departments'    => $departments,
            'document_types' => $document_types,
        ));
    }

    public function upload_document()
    {
        $this->require_permission('staff.edit');

        $staff_id = (int)$this->input->post('staff_id');
        $type_id  = (int)$this->input->post('document_type_id');
        $doc_name = trim($this->input->post('document_name'));
        $redirect = $this->input->post('redirect_to') ?: ('staff/profile/' . $staff_id);

        $dtObj = $this->Staff_document_type_model->get_by_id($type_id);
        $typeName = $dtObj ? $dtObj->document_name : ($this->input->post('document_type') ?: 'Other');
        if (empty($doc_name)) {
            $doc_name = $typeName;
        }

        if (!empty($staff_id) && !empty($_FILES['document_file']['name']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
            $allowedExtensions = array('pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx');
            $origName = $_FILES['document_file']['name'];
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (in_array($ext, $allowedExtensions)) {
                $uploadDir = FCPATH . 'uploads/staff_docs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $safeName = 'doc_' . $staff_id . '_' . ($type_id ?: 'other') . '_' . time() . '_' . substr(md5(uniqid()), 0, 8) . '.' . $ext;
                if (move_uploaded_file($_FILES['document_file']['tmp_name'], $uploadDir . $safeName)) {
                    $mimeType = $_FILES['document_file']['type'] ?: 'application/octet-stream';
                    $fileSize = $_FILES['document_file']['size'];

                    $this->Staff_model->add_document(array(
                        'staff_id'         => $staff_id,
                        'document_type_id' => $type_id ?: NULL,
                        'document_type'    => $typeName,
                        'document_name'    => $doc_name,
                        'file_name'        => $origName,
                        'file_path'        => 'uploads/staff_docs/' . $safeName,
                        'file_type'        => $mimeType,
                        'file_size'        => $fileSize,
                        'mime_type'        => $mimeType,
                        'uploaded_by'      => $this->current_user->user_id ?? 1,
                        'status'           => 1,
                        'is_deleted'       => 'n',
                        'created_at'       => date('Y-m-d H:i:s'),
                    ));
                    $this->session->set_flashdata('success', 'Staff document uploaded successfully!');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid file type. Allowed formats: PDF, JPG, PNG, DOC, DOCX.');
            }
        }

        redirect($redirect);
    }

    public function delete_document($id = NULL)
    {
        $this->require_permission('staff.edit');

        $redirect = $this->input->get('redirect_to') ?: 'staff/documents';
        if (!empty($id)) {
            $this->Staff_model->delete_document($id);
            $this->session->set_flashdata('success', 'Staff document removed.');
        }
        redirect($redirect);
    }

    /* =========================================================================
       Secure Document View & Download Handlers
       ========================================================================= */
    public function view_document($document_id = NULL)
    {
        $this->require_permission('staff.view');

        if (empty($document_id)) {
            show_404();
            return;
        }

        $doc = $this->Staff_model->get_document_by_id($document_id);
        if (!$doc || empty($doc->file_path)) {
            show_404();
            return;
        }

        $filePath = FCPATH . $doc->file_path;
        if (!file_exists($filePath)) {
            show_error('Document file not found on server.', 404, '404 File Not Found');
            return;
        }

        $mime = $doc->mime_type ?: mime_content_type($filePath) ?: 'application/octet-stream';
        $fileName = $doc->file_name ?: basename($filePath);

        // Security headers
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . addslashes($fileName) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        
        readfile($filePath);
        exit;
    }

    public function download_document($document_id = NULL)
    {
        $this->require_permission('staff.view');

        if (empty($document_id)) {
            show_404();
            return;
        }

        $doc = $this->Staff_model->get_document_by_id($document_id);
        if (!$doc || empty($doc->file_path)) {
            show_404();
            return;
        }

        $filePath = FCPATH . $doc->file_path;
        if (!file_exists($filePath)) {
            show_error('Document file not found on server.', 404, '404 File Not Found');
            return;
        }

        $fileName = $doc->file_name ?: basename($filePath);

        // Download headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        header('X-Content-Type-Options: nosniff');
        
        readfile($filePath);
        exit;
    }

    /* =========================================================================
       10. Teacher Workload Management
       ========================================================================= */
    public function workload()
    {
        $this->require_permission('staff.view');

        if ($this->input->method() === 'post') {
            $staff_id = $this->input->post('staff_id');
            // Ensure selected staff is a teacher
            $staffMember = $this->Staff_model->get_by_id($staff_id);
            if ($staffMember && $staffMember->staff_type === 'teacher') {
                $workload_id = $this->input->post('workload_id');
                $workload_data = array(
                    'staff_id'         => $staff_id,
                    'academic_year_id' => $this->input->post('academic_year_id') ?: 1,
                    'subject_id'       => $this->input->post('subject_id'),
                    'class_id'         => $this->input->post('class_id'),
                    'division_id'      => $this->input->post('division_id') ?: ($this->input->post('section_id') ?: NULL),
                    'periods'          => $this->input->post('periods') ? intval($this->input->post('periods')) : 5,
                    'working_days'     => $this->input->post('working_days') ?: 'Mon,Tue,Wed,Thu,Fri',
                    'remarks'          => $this->input->post('remarks'),
                    'status'           => 1,
                );

                if (!empty($workload_id)) {
                    $this->Staff_model->update_workload($workload_id, $workload_data);
                    $this->session->set_flashdata('success', 'Teacher workload updated successfully!');
                } else {
                    $workload_data['created_at'] = date('Y-m-d H:i:s');
                    $this->Staff_model->add_workload($workload_data);
                    $this->session->set_flashdata('success', 'Teacher workload assigned successfully!');
                }
            } else {
                $this->session->set_flashdata('error', 'Workload can only be assigned to teaching staff.');
            }
            redirect('staff/workload');
        }

        $filters = array(
            'staff_id'         => $this->input->get('staff_id'),
            'academic_year_id' => $this->input->get('academic_year_id'),
            'class_id'         => $this->input->get('class_id'),
            'division_id'      => $this->input->get('division_id') ?: $this->input->get('section_id'),
            'subject_id'       => $this->input->get('subject_id'),
        );

        $workloads = $this->Staff_model->get_workloads($filters);
        $teachers  = $this->Staff_model->get_teachers();
        $years     = $this->Academic_year_model->get_all();
        $classes   = $this->Class_model->get_all();
        $divisions = $this->Division_model->get_all();
        $subjects  = $this->Subject_model->get_all();

        $this->render('pages/staff/workload', array(
            'title'       => 'Teacher Workload',
            'page_key'    => 'teacher_workload',
            'breadcrumb'  => array('Staff Management', 'Teacher Workload'),
            'workloads'   => $workloads,
            'teachers'    => $teachers,
            'years'       => $years,
            'classes'     => $classes,
            'divisions'   => $divisions,
            'sections'    => $divisions,
            'subjects'    => $subjects,
        ));
    }

    public function delete_workload($id = NULL)
    {
        $this->require_permission('staff.edit');

        $redirect = $this->input->get('redirect_to') ?: 'staff/workload';
        if (!empty($id)) {
            $this->Staff_model->delete_workload($id);
            $this->session->set_flashdata('success', 'Workload record removed.');
        }
        redirect($redirect);
    }

    /* =========================================================================
       11. Staff Daily Attendance
       ========================================================================= */
    public function attendance()
    {
        $this->require_permission('staff.view');

        $date    = $this->input->get('date') ?: date('Y-m-d');
        $dept_id = $this->input->get('department_id');

        if ($this->input->method() === 'post') {
            $postDate = $this->input->post('attendance_date') ?: $date;
            $records  = $this->input->post('attendance');
            $this->Staff_model->save_attendance_batch($postDate, $records);
            $this->session->set_flashdata('success', 'Staff attendance saved successfully for ' . date('d M Y', strtotime($postDate)));
            redirect('staff/attendance?date=' . $postDate . ($dept_id ? '&department_id=' . $dept_id : ''));
        }

        $attendance_list = $this->Staff_model->get_attendance_for_date($date, $dept_id);
        $departments     = $this->Department_model->get_all();

        $this->render('pages/staff/attendance', array(
            'title'           => 'Staff Attendance',
            'page_key'        => 'staff_attendance',
            'breadcrumb'      => array('Staff Management', 'Staff Attendance'),
            'attendance_list' => $attendance_list,
            'departments'     => $departments,
            'date'            => $date,
            'selected_dept'   => $dept_id,
        ));
    }

    /* =========================================================================
       12. Staff Leave Management
       ========================================================================= */
    public function leave()
    {
        $this->require_permission('staff.view');

        if ($this->input->method() === 'post') {
            $action = $this->input->post('action');
            if ($action === 'apply') {
                $staff_id = $this->input->post('staff_id');
                if (empty($staff_id)) {
                    $this->session->set_flashdata('error', 'Please select a staff member.');
                    redirect('staff/leave');
                    return;
                }
                $from = $this->input->post('from_date');
                $to   = $this->input->post('to_date');
                if (strtotime($from) > strtotime($to)) {
                    $this->session->set_flashdata('error', 'From Date cannot be after To Date.');
                } else {
                    $days = max(1, round((strtotime($to) - strtotime($from)) / (60 * 60 * 24)) + 1);
                    $this->Staff_model->apply_leave(array(
                        'staff_id'     => $staff_id,
                        'leave_type'   => $this->input->post('leave_type'),
                        'from_date'    => $from,
                        'to_date'      => $to,
                        'total_days'   => $days,
                        'reason'       => $this->input->post('reason'),
                        'status'       => 'Pending',
                        'applied_date' => date('Y-m-d'),
                        'created_at'   => date('Y-m-d H:i:s')
                    ));
                    $this->session->set_flashdata('success', 'Leave request submitted successfully!');
                }
            } elseif ($action === 'update_status') {
                $leave_id = $this->input->post('leave_id');
                $status   = $this->input->post('status');
                $remarks  = $this->input->post('remarks') ?: '';
                $this->Staff_model->update_leave_status($leave_id, $status, $this->session->userdata('user_id'), $remarks);
                $this->session->set_flashdata('success', 'Leave request ' . strtolower($status) . ' successfully.');
            }
            redirect('staff/leave');
        }

        $filters = array(
            'status'        => $this->input->get('status'),
            'staff_id'      => $this->input->get('staff_id'),
            'department_id' => $this->input->get('department_id'),
        );

        $leaves      = $this->Staff_model->get_leaves($filters);
        $staff_list  = $this->Staff_model->get_all(array('status' => 1));
        $departments = $this->Department_model->get_all();

        $this->render('pages/staff/leave', array(
            'title'        => 'Staff Leave Management',
            'page_key'     => 'staff_leave',
            'breadcrumb'   => array('Staff Management', 'Leave Management'),
            'leaves'       => $leaves,
            'staff_list'   => $staff_list,
            'departments'  => $departments,
        ));
    }
}
