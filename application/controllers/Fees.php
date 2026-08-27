<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fees extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            'Fee_model',
            'Fee_category_model',
            'Fee_structure_model',
            'Fee_discount_model',
            'Fee_adjustment_model',
            'Fee_reminder_model',
            'Finance_setting_model',
            'Finance_audit_model',
            'Student_model',
            'Class_model',
            'Section_model',
            'Academic_year_model'
        ));
    }

    // 1. Fee Dashboard
    public function index()
    {
        $this->require_permission('fees.view');
        $metrics = $this->Fee_model->get_dashboard_metrics();
        $collection_summary = $this->Fee_model->get_collection_summary();
        $outstanding_summary = $this->Fee_model->get_outstanding_summary();
        $recent_payments = $this->Fee_model->get_recent_payments(10);
        $settings = $this->Finance_setting_model->get_settings();

        $this->render('pages/fees/dashboard', array(
            'title'               => 'Fees & Finance Dashboard',
            'page_key'            => 'fee-dashboard',
            'metrics'             => $metrics,
            'collection_summary'  => $collection_summary,
            'outstanding_summary' => $outstanding_summary,
            'recent_payments'     => $recent_payments,
            'settings'            => $settings,
        ));
    }

    // 2. Fee Categories
    public function categories()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $action = $this->input->post('action');
            if ($action === 'delete') {
                $cat_id = (int)$this->input->post('fee_head_id');
                if ($this->Fee_category_model->delete($cat_id)) {
                    $this->Finance_audit_model->log('FEE_CATEGORY_DELETED', 'tbl_fee_heads', $cat_id, 'Deleted fee category ID: ' . $cat_id);
                    $this->session->set_flashdata('success', 'Fee category deleted successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Cannot delete category linked to existing fee structures.');
                }
                redirect('fees/categories');
            }

            $cat_id = (int)$this->input->post('fee_head_id');
            $head_name = trim($this->input->post('head_name'));
            $category_code = trim($this->input->post('category_code'));
            $description = trim($this->input->post('description'));
            $applicable_to = trim($this->input->post('applicable_to') ?: 'All Students');
            $frequency = trim($this->input->post('frequency') ?: 'Yearly');
            $status = $this->input->post('status') ? 1 : 0;

            if (empty($head_name)) {
                $this->session->set_flashdata('error', 'Category name is required.');
                redirect('fees/categories');
            }

            if (!$this->Fee_category_model->is_name_unique($head_name, $cat_id)) {
                $this->session->set_flashdata('error', 'Category with this name already exists.');
                redirect('fees/categories');
            }

            $data = array(
                'head_name'     => $head_name,
                'category_code' => $category_code,
                'description'   => $description,
                'applicable_to' => $applicable_to,
                'frequency'     => $frequency,
                'status'        => $status,
            );

            $id = $this->Fee_category_model->save($data, $cat_id);
            $this->Finance_audit_model->log(
                ($cat_id > 0) ? 'FEE_CATEGORY_UPDATED' : 'FEE_CATEGORY_CREATED',
                'tbl_fee_heads',
                $id,
                "Saved fee category: {$head_name} ({$frequency})"
            );

            $this->session->set_flashdata('success', 'Fee category saved successfully.');
            redirect('fees/categories');
        }

        $categories = $this->Fee_category_model->get_all();
        $this->render('pages/fees/categories', array(
            'title'      => 'Fee Categories',
            'page_key'   => 'fee-categories',
            'categories' => $categories,
        ));
    }

    // 3. Fee Structures
    public function structures()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $action = $this->input->post('action');
            if ($action === 'delete') {
                $struct_id = (int)$this->input->post('fee_structure_id');
                if ($this->Fee_structure_model->delete($struct_id)) {
                    $this->Finance_audit_model->log('FEE_STRUCTURE_DELETED', 'tbl_fee_structures', $struct_id, 'Deleted fee structure ID: ' . $struct_id);
                    $this->session->set_flashdata('success', 'Fee structure deleted successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Cannot delete structure linked to assigned student fees.');
                }
                redirect('fees/structures');
            }

            $struct_id = (int)$this->input->post('fee_structure_id');
            $fee_head_id = (int)$this->input->post('fee_head_id');
            $academic_year_id = (int)$this->input->post('academic_year_id');
            $class_id = (int)$this->input->post('class_id');
            $amount = (float)$this->input->post('amount');
            $frequency = trim($this->input->post('frequency') ?: 'Yearly');
            $due_date = trim($this->input->post('due_date'));
            $applicable_from = $this->input->post('applicable_from') ?: null;
            $applicable_to = $this->input->post('applicable_to') ?: null;
            $is_optional = $this->input->post('is_optional') ? 1 : 0;
            $status = $this->input->post('status') ? 1 : 0;

            if ($amount <= 0) {
                $this->session->set_flashdata('error', 'Fee amount must be greater than zero.');
                redirect('fees/structures');
            }

            if (empty($due_date)) {
                $this->session->set_flashdata('error', 'Due date is required.');
                redirect('fees/structures');
            }

            if ($this->Fee_structure_model->is_duplicate($fee_head_id, $academic_year_id, $class_id, $struct_id)) {
                $this->session->set_flashdata('error', 'A fee structure for this Category, Class, and Academic Year already exists.');
                redirect('fees/structures');
            }

            $data = array(
                'fee_head_id'      => $fee_head_id,
                'academic_year_id' => $academic_year_id,
                'class_id'         => $class_id,
                'amount'           => $amount,
                'frequency'        => $frequency,
                'due_date'         => $due_date,
                'applicable_from'  => $applicable_from,
                'applicable_to'    => $applicable_to,
                'is_optional'      => $is_optional,
                'status'           => $status,
            );

            $id = $this->Fee_structure_model->save($data, $struct_id);
            $this->Finance_audit_model->log(
                ($struct_id > 0) ? 'FEE_STRUCTURE_UPDATED' : 'FEE_STRUCTURE_CREATED',
                'tbl_fee_structures',
                $id,
                "Saved fee structure for Class ID {$class_id}, Amount ₹{$amount}"
            );

            $this->session->set_flashdata('success', 'Fee structure saved successfully.');
            redirect('fees/structures');
        }

        $filters = array(
            'class_id'         => $this->input->get('class_id'),
            'fee_head_id'      => $this->input->get('fee_head_id'),
            'academic_year_id' => $this->input->get('academic_year_id'),
        );

        $structures = $this->Fee_structure_model->get_all($filters);
        $categories = $this->Fee_category_model->get_all(true);
        $classes = $this->Class_model->get_all(true);
        $academic_years = $this->Academic_year_model->get_all();

        $this->render('pages/fees/structures', array(
            'title'          => 'Fee Structure Management',
            'page_key'       => 'fee-structures',
            'structures'     => $structures,
            'categories'     => $categories,
            'classes'        => $classes,
            'academic_years' => $academic_years,
            'filters'        => $filters,
        ));
    }

    // 4. Student Fee Assignment
    public function assignments()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $assignment_type = $this->input->post('assignment_type');

            if ($assignment_type === 'individual') {
                $student_id = (int)$this->input->post('student_id');
                $fee_structure_id = (int)$this->input->post('fee_structure_id');
                $amount = (float)$this->input->post('amount');
                $due_date = trim($this->input->post('due_date'));
                $discount_amount = (float)$this->input->post('discount_amount');
                $concession_amount = (float)$this->input->post('concession_amount');
                $remarks = trim($this->input->post('remarks'));

                if ($student_id <= 0 || $fee_structure_id <= 0 || $amount <= 0 || empty($due_date)) {
                    $this->session->set_flashdata('error', 'All required fields must be valid.');
                    redirect('fees/assignments');
                }

                $id = $this->Fee_model->assign_student_fee($student_id, $fee_structure_id, $amount, $due_date, $discount_amount, $concession_amount, $remarks);
                $this->Finance_audit_model->log('FEE_ASSIGNED_INDIVIDUAL', 'tbl_student_fees', $id, "Assigned fee structure ID {$fee_structure_id} to student ID {$student_id}");

                $this->session->set_flashdata('success', 'Fee successfully assigned to student.');
                redirect('fees/student_fees');
            } elseif ($assignment_type === 'bulk') {
                $class_id = (int)$this->input->post('class_id');
                $section_id = (int)$this->input->post('section_id');
                $fee_structure_id = (int)$this->input->post('fee_structure_id');
                $academic_year_id = (int)$this->input->post('academic_year_id') ?: 1;

                if ($class_id <= 0 || $fee_structure_id <= 0) {
                    $this->session->set_flashdata('error', 'Class and Fee Structure are required for bulk assignment.');
                    redirect('fees/assignments');
                }

                $assigned_count = $this->Fee_model->bulk_assign_fee_structure($class_id, $section_id, $fee_structure_id, $academic_year_id);
                $this->Finance_audit_model->log('FEE_ASSIGNED_BULK', 'tbl_student_fees', $fee_structure_id, "Bulk assigned fee structure {$fee_structure_id} to {$assigned_count} students in Class {$class_id}");

                $this->session->set_flashdata('success', "Fee structure assigned to {$assigned_count} students successfully.");
                redirect('fees/student_fees');
            }
        }

        $classes = $this->Class_model->get_all(true);
        $sections = $this->Section_model->get_all(true);
        $structures = $this->Fee_structure_model->get_all();
        $academic_years = $this->Academic_year_model->get_all();
        $students = $this->Student_model->get_all(array('status' => 1), 500);

        $this->render('pages/fees/assignments', array(
            'title'          => 'Student Fee Assignment',
            'page_key'       => 'fee-assignments',
            'classes'        => $classes,
            'sections'       => $sections,
            'structures'     => $structures,
            'academic_years' => $academic_years,
            'students'       => $students,
        ));
    }

    // 5. Student Fee Details
    public function student_fees($student_id = 0)
    {
        $this->require_permission('fees.view');
        $filters = array(
            'student_id'     => $student_id ?: $this->input->get('student_id'),
            'class_id'       => $this->input->get('class_id'),
            'section_id'     => $this->input->get('section_id'),
            'fee_head_id'    => $this->input->get('fee_head_id'),
            'payment_status' => $this->input->get('payment_status'),
            'search'         => $this->input->get('search'),
        );

        $fees = $this->Fee_model->get_student_fees($filters);
        $classes = $this->Class_model->get_all(true);
        $sections = $this->Section_model->get_all(true);
        $categories = $this->Fee_category_model->get_all(true);

        $this->render('pages/fees/student_fees', array(
            'title'      => 'Student Fee Directory',
            'page_key'   => 'student-fees',
            'fees'       => $fees,
            'classes'    => $classes,
            'sections'   => $sections,
            'categories' => $categories,
            'filters'    => $filters,
        ));
    }

    // 6. Fee Collection & Payment Processing
    public function collection()
    {
        $this->require_permission('fees.collect');
        if ($this->input->method() === 'post') {
            $student_fee_id = (int)$this->input->post('student_fee_id');
            $amount_to_pay = (float)$this->input->post('amount_to_pay');
            $payment_mode = trim($this->input->post('payment_mode') ?: 'Cash');
            $transaction_ref = trim($this->input->post('transaction_reference'));
            $payment_date = trim($this->input->post('payment_date') ?: date('Y-m-d'));
            $remarks = trim($this->input->post('remarks'));
            $collected_by = $this->session->userdata('user_id');

            $result = $this->Fee_model->collect_payment(
                $student_fee_id,
                $amount_to_pay,
                $payment_mode,
                $transaction_ref,
                $payment_date,
                $remarks,
                $collected_by
            );

            if ($result['success']) {
                $this->session->set_flashdata('success', "Payment of ₹" . number_format($amount_to_pay, 2) . " processed successfully! Receipt: " . $result['receipt_no']);
                redirect('fees/receipt/' . $result['payment_id']);
            } else {
                $this->session->set_flashdata('error', $result['message']);
                redirect('fees/collection');
            }
        }

        $search = trim($this->input->get('search') ?? '');
        $selected_student_id = (int)$this->input->get('student_id');
        $student_info = null;
        $student_fees = array();

        if ($selected_student_id > 0) {
            $student_info = $this->Student_model->get_by_id($selected_student_id);
            if ($student_info) {
                $student_fees = $this->Fee_model->get_student_fees(array('student_id' => $selected_student_id));
            }
        } elseif (!empty($search)) {
            $matched_students = $this->Student_model->get_all(array('search' => $search), 1);
            if (!empty($matched_students)) {
                $student_info = $matched_students[0];
                $student_fees = $this->Fee_model->get_student_fees(array('student_id' => $student_info->student_id));
            }
        }

        $this->render('pages/fees/collection', array(
            'title'        => 'Fee Collection & Payment',
            'page_key'     => 'fee-collection',
            'search'       => $search,
            'student_info' => $student_info,
            'student_fees' => $student_fees,
        ));
    }

    // 7. Payment History
    public function payments()
    {
        $this->require_permission('fees.view');
        $filters = array(
            'class_id'     => $this->input->get('class_id'),
            'payment_mode' => $this->input->get('payment_mode'),
            'date_from'    => $this->input->get('date_from'),
            'date_to'      => $this->input->get('date_to'),
            'search'       => $this->input->get('search'),
        );

        $payments = $this->Fee_model->get_payments($filters);
        $classes = $this->Class_model->get_all(true);

        $this->render('pages/fees/payments', array(
            'title'    => 'Payment Transaction History',
            'page_key' => 'payment-history',
            'payments' => $payments,
            'classes'  => $classes,
            'filters'  => $filters,
        ));
    }

    public function ajax_payments_list()
    {
        $this->require_permission('fees.view');

        $draw   = (int)$this->input->post('draw');
        $start  = (int)$this->input->post('start');
        $length = (int)$this->input->post('length');
        $order  = $this->input->post('order');
        $search = $this->input->post('search');

        $order_col_idx = isset($order[0]['column']) ? (int)$order[0]['column'] : 0;
        $order_dir     = isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';
        $search_val    = isset($search['value']) ? trim($search['value']) : '';

        $filters = array(
            'class_id'     => $this->input->post('class_id') ?: $this->input->get('class_id'),
            'payment_mode' => $this->input->post('payment_mode') ?: $this->input->get('payment_mode'),
            'date_from'    => $this->input->post('date_from') ?: $this->input->get('date_from'),
            'date_to'      => $this->input->post('date_to') ?: $this->input->get('date_to'),
            'search'       => $search_val,
        );

        $records_total    = $this->Fee_model->get_payments_count_all();
        $records_filtered = $this->Fee_model->count_filtered_payments($filters);
        $payments_list    = $this->Fee_model->get_payments_datatables($filters, $length, $start, $order_col_idx, $order_dir);

        $data = array();
        foreach ($payments_list as $p) {
            $receiptCol = '<a href="' . site_url('fees/receipt/' . $p->payment_id) . '" class="hover:underline font-mono font-bold text-primary">' . html_escape($p->receipt_no) . '</a>';
            $studentCol = '<a href="' . site_url('students/profile/' . $p->student_id) . '" class="text-on-surface font-medium hover:underline">' . html_escape($p->first_name . ' ' . $p->last_name) . '</a>' .
                '<div class="text-[12px] text-on-surface-variant font-mono">' . html_escape($p->admission_number) . '</div>';
            $classCol = html_escape(trim(($p->class_name ?: '') . ' ' . ($p->section_name ?: '')));
            $amountCol = school_currency($p->amount_paid);
            $modeCol = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-surface-container-high text-on-surface-variant">' . html_escape($p->payment_mode) . '</span>';
            $dateCol = school_date($p->payment_date);
            $actionCol = '<a href="' . site_url('fees/receipt/' . $p->payment_id) . '" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors inline-flex items-center gap-1 text-[12px] font-medium" title="View Receipt">' .
                '<span class="material-symbols-outlined text-[16px]">receipt</span> Print' .
            '</a>';

            $data[] = array(
                $receiptCol,
                $studentCol,
                $classCol,
                html_escape($p->category_name ?: 'General Fee'),
                $amountCol,
                $modeCol,
                html_escape($p->transaction_reference ?: '—'),
                $dateCol,
                $actionCol
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

    // 8. Receipts List
    public function receipts()
    {
        $this->require_permission('fees.view');
        $filters = array(
            'class_id'     => $this->input->get('class_id'),
            'payment_mode' => $this->input->get('payment_mode'),
            'date_from'    => $this->input->get('date_from'),
            'date_to'      => $this->input->get('date_to'),
            'search'       => $this->input->get('search'),
        );

        $payments = $this->Fee_model->get_payments($filters);
        $classes = $this->Class_model->get_all(true);

        $this->render('pages/fees/receipts', array(
            'title'    => 'Receipt History',
            'page_key' => 'fee-receipts',
            'payments' => $payments,
            'classes'  => $classes,
            'filters'  => $filters,
        ));
    }

    // 9. Single Printable Receipt
    public function receipt($payment_id = 0)
    {
        $this->require_permission('fees.view');
        $receipt = $this->Fee_model->get_receipt_by_id((int)$payment_id);
        if (!$receipt) {
            $this->session->set_flashdata('error', 'Receipt record not found.');
            redirect('fees/receipts');
        }

        $settings = $this->Finance_setting_model->get_settings();

        $this->render('pages/fees/receipt_view', array(
            'title'    => 'Official Fee Receipt - ' . $receipt->receipt_no,
            'page_key' => 'fee-receipts',
            'receipt'  => $receipt,
            'settings' => $settings,
        ));
    }

    // 10. Discounts & Concessions
    public function discounts()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $action = $this->input->post('action');
            if ($action === 'delete') {
                $discount_id = (int)$this->input->post('discount_id');
                $this->Fee_discount_model->delete($discount_id);
                $this->Finance_audit_model->log('DISCOUNT_DELETED', 'tbl_fee_discounts', $discount_id, 'Deleted discount ID: ' . $discount_id);
                $this->session->set_flashdata('success', 'Discount scheme deleted.');
                redirect('fees/discounts');
            }

            $discount_id = (int)$this->input->post('discount_id');
            $name = trim($this->input->post('name'));
            $discount_type = trim($this->input->post('discount_type') ?: 'Fixed Amount');
            $discount_value = (float)$this->input->post('discount_value');
            $max_discount = $this->input->post('max_discount') ? (float)$this->input->post('max_discount') : null;
            $is_concession = $this->input->post('is_concession') ? 1 : 0;
            $status = $this->input->post('status') ? 1 : 0;

            if (empty($name) || $discount_value <= 0) {
                $this->session->set_flashdata('error', 'Scheme name and valid discount value are required.');
                redirect('fees/discounts');
            }

            $data = array(
                'name'           => $name,
                'discount_type'  => $discount_type,
                'discount_value' => $discount_value,
                'max_discount'   => $max_discount,
                'is_concession'  => $is_concession,
                'status'         => $status,
            );

            $id = $this->Fee_discount_model->save($data, $discount_id);
            $this->Finance_audit_model->log(
                ($discount_id > 0) ? 'DISCOUNT_UPDATED' : 'DISCOUNT_CREATED',
                'tbl_fee_discounts',
                $id,
                "Saved discount scheme: {$name}"
            );

            $this->session->set_flashdata('success', 'Discount / Concession scheme saved successfully.');
            redirect('fees/discounts');
        }

        $discounts = $this->Fee_discount_model->get_all();
        $this->render('pages/fees/discounts', array(
            'title'     => 'Discounts & Concessions',
            'page_key'  => 'fee-discounts',
            'discounts' => $discounts,
        ));
    }

    // 11. Due Fees
    public function due_fees()
    {
        $this->require_permission('fees.view');
        $filters = array(
            'class_id'    => $this->input->get('class_id'),
            'section_id'  => $this->input->get('section_id'),
            'fee_head_id' => $this->input->get('fee_head_id'),
            'status'      => $this->input->get('status'),
            'search'      => $this->input->get('search'),
        );

        $due_fees = $this->Fee_model->get_due_fees($filters);
        $classes = $this->Class_model->get_all(true);
        $sections = $this->Section_model->get_all(true);
        $categories = $this->Fee_category_model->get_all(true);

        $this->render('pages/fees/due_fees', array(
            'title'      => 'Outstanding & Due Fees',
            'page_key'   => 'due-fees',
            'due_fees'   => $due_fees,
            'classes'    => $classes,
            'sections'   => $sections,
            'categories' => $categories,
            'filters'    => $filters,
        ));
    }

    // 12. Fee Reminders
    public function reminders()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $student_id = (int)$this->input->post('student_id');
            $student_fee_id = (int)$this->input->post('student_fee_id');
            $reminder_type = trim($this->input->post('reminder_type') ?: 'Upcoming Due');
            $message = trim($this->input->post('message'));
            $scheduled_date = trim($this->input->post('scheduled_date') ?: date('Y-m-d'));

            $student = $this->Student_model->get_by_id($student_id);
            if (!$student) {
                $this->session->set_flashdata('error', 'Student record not found.');
                redirect('fees/reminders');
            }

            $rem_id = $this->Fee_reminder_model->create_reminder(array(
                'student_id'     => $student_id,
                'parent_name'    => $student->guardian_name ?: 'Parent',
                'parent_phone'   => $student->guardian_phone ?: '',
                'parent_email'   => $student->email ?: '',
                'student_fee_id' => $student_fee_id,
                'reminder_type'  => $reminder_type,
                'message'        => $message,
                'scheduled_date' => $scheduled_date,
                'status'         => 'Pending',
                'created_by'     => $this->session->userdata('user_id'),
            ));

            $this->Finance_audit_model->log('REMINDER_CREATED', 'tbl_fee_reminders', $rem_id, "Created {$reminder_type} reminder for student {$student->first_name}");

            $this->session->set_flashdata('success', 'Fee reminder queued successfully.');
            redirect('fees/reminder_history');
        }

        $due_fees = $this->Fee_model->get_due_fees(array(), 100);
        $settings = $this->Finance_setting_model->get_settings();

        $this->render('pages/fees/reminders', array(
            'title'    => 'Fee Reminders',
            'page_key' => 'fee-reminders',
            'due_fees' => $due_fees,
            'settings' => $settings,
        ));
    }

    // 13. Fee Reminder History
    public function reminder_history()
    {
        $this->require_permission('fees.view');
        $reminders = $this->Fee_reminder_model->get_all();
        $this->render('pages/fees/reminder_history', array(
            'title'     => 'Fee Reminder History',
            'page_key'  => 'fee-reminders',
            'reminders' => $reminders,
        ));
    }

    // 14. Fee Adjustments
    public function adjustments()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $student_fee_id = (int)$this->input->post('student_fee_id');
            $adjustment_type = trim($this->input->post('adjustment_type') ?: 'Waiver');
            $adjustment_amount = (float)$this->input->post('adjustment_amount');
            $reason = trim($this->input->post('reason'));

            $sfee = $this->Fee_model->get_student_fee_by_id($student_fee_id);
            if (!$sfee) {
                $this->session->set_flashdata('error', 'Student fee record not found.');
                redirect('fees/adjustments');
            }

            if ($adjustment_amount <= 0) {
                $this->session->set_flashdata('error', 'Adjustment amount must be greater than zero.');
                redirect('fees/adjustments');
            }

            if (empty($reason)) {
                $this->session->set_flashdata('error', 'Reason is required for fee adjustments.');
                redirect('fees/adjustments');
            }

            $prev_final = (float)$sfee->final_amount;
            $new_final = max(0.00, $prev_final - $adjustment_amount);
            $new_due = max(0.00, $new_final - (float)$sfee->paid_amount);
            $new_status = ($new_due == 0.00) ? 'Paid' : (($sfee->paid_amount > 0) ? 'Partially Paid' : 'Pending');

            // Update student fee
            $this->db->where('student_fee_id', $student_fee_id)->update('tbl_student_fees', array(
                'final_amount'      => $new_final,
                'concession_amount' => (float)$sfee->concession_amount + $adjustment_amount,
                'due_amount'        => $new_due,
                'payment_status'    => $new_status,
            ));

            // Log adjustment
            $adj_id = $this->Fee_adjustment_model->create_adjustment(array(
                'student_fee_id'    => $student_fee_id,
                'student_id'        => $sfee->student_id,
                'adjustment_type'   => $adjustment_type,
                'previous_amount'   => $prev_final,
                'new_amount'        => $new_final,
                'adjustment_amount' => $adjustment_amount,
                'reason'            => $reason,
                'adjusted_by'       => $this->session->userdata('user_id'),
            ));

            $this->Finance_audit_model->log('FEE_ADJUSTED', 'tbl_fee_adjustments', $adj_id, "Applied {$adjustment_type} of ₹{$adjustment_amount} to invoice {$sfee->invoice_no}. Reason: {$reason}");

            $this->session->set_flashdata('success', 'Fee adjustment successfully applied.');
            redirect('fees/adjustments');
        }

        $adjustments = $this->Fee_adjustment_model->get_adjustments();
        $student_fees = $this->Fee_model->get_student_fees(array('payment_status' => 'Pending'), 100);

        $this->render('pages/fees/adjustments', array(
            'title'        => 'Fee Adjustments & Waivers',
            'page_key'     => 'fee-adjustments',
            'adjustments'  => $adjustments,
            'student_fees' => $student_fees,
        ));
    }

    // 15. Refunds
    public function refunds()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $payment_id = (int)$this->input->post('payment_id');
            $refund_amount = (float)$this->input->post('refund_amount');
            $refund_reason = trim($this->input->post('refund_reason'));
            $refund_mode = trim($this->input->post('refund_mode') ?: 'Bank Transfer');
            $approved_by = $this->session->userdata('user_id');

            if (empty($refund_reason)) {
                $this->session->set_flashdata('error', 'Refund reason is mandatory.');
                redirect('fees/refunds');
            }

            $res = $this->Fee_adjustment_model->process_refund($payment_id, $refund_amount, $refund_reason, $refund_mode, $approved_by);
            if ($res['success']) {
                $this->Finance_audit_model->log('REFUND_PROCESSED', 'tbl_fee_refunds', $res['refund_id'], "Refunded ₹{$refund_amount} for Payment ID {$payment_id}. Reason: {$refund_reason}");
                $this->session->set_flashdata('success', 'Refund of ₹' . number_format($refund_amount, 2) . ' processed successfully.');
            } else {
                $this->session->set_flashdata('error', $res['message']);
            }
            redirect('fees/refunds');
        }

        $refunds = $this->Fee_adjustment_model->get_refunds();
        $recent_payments = $this->Fee_model->get_payments(array(), 100);

        $this->render('pages/fees/refunds', array(
            'title'           => 'Payment Refunds',
            'page_key'        => 'fee-refunds',
            'refunds'         => $refunds,
            'recent_payments' => $recent_payments,
        ));
    }

    // 16. Financial Reports
    public function reports()
    {
        $this->require_permission('fees.view');
        $report_type = $this->input->get('type') ?: 'collection';
        $export = $this->input->get('export');

        $filters = array(
            'class_id'  => $this->input->get('class_id'),
            'date_from' => $this->input->get('date_from'),
            'date_to'   => $this->input->get('date_to'),
        );

        $classes = $this->Class_model->get_all(true);
        $report_data = array();

        if ($report_type === 'collection') {
            $report_data = $this->Fee_model->get_payments($filters, 200);
        } elseif ($report_type === 'due') {
            $report_data = $this->Fee_model->get_due_fees($filters, 200);
        } elseif ($report_type === 'student') {
            $report_data = $this->Fee_model->get_student_fees($filters, 200);
        }

        if ($export === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=fee_report_' . $report_type . '_' . date('Ymd_His') . '.csv');
            $out = fopen('php://output', 'w');

            if ($report_type === 'collection') {
                fputcsv($out, array('Receipt No', 'Student Name', 'Admission No', 'Class', 'Category', 'Amount Paid', 'Payment Mode', 'Date'));
                foreach ($report_data as $r) {
                    fputcsv($out, array($r->receipt_no, $r->first_name . ' ' . $r->last_name, $r->admission_number, $r->class_name, $r->category_name, $r->amount_paid, $r->payment_mode, $r->payment_date));
                }
            } elseif ($report_type === 'due') {
                fputcsv($out, array('Admission No', 'Student Name', 'Class', 'Category', 'Original Amount', 'Paid Amount', 'Due Amount', 'Due Date', 'Days Overdue'));
                foreach ($report_data as $r) {
                    fputcsv($out, array($r->admission_number, $r->first_name . ' ' . $r->last_name, $r->class_name . ' ' . $r->section_name, $r->category_name, $r->original_amount, $r->paid_amount, $r->due_amount, $r->due_date, $r->days_overdue));
                }
            } elseif ($report_type === 'student') {
                fputcsv($out, array('Invoice No', 'Student Name', 'Admission No', 'Class', 'Category', 'Original Amount', 'Discount', 'Final Amount', 'Paid Amount', 'Due Amount', 'Status'));
                foreach ($report_data as $r) {
                    fputcsv($out, array($r->invoice_no, $r->first_name . ' ' . $r->last_name, $r->admission_number, $r->class_name . ' ' . $r->section_name, $r->category_name, $r->original_amount, $r->discount_amount, $r->final_amount, $r->paid_amount, $r->due_amount, $r->payment_status));
                }
            }
            fclose($out);
            exit;
        }

        $this->render('pages/fees/reports', array(
            'title'       => 'Financial & Collection Reports',
            'page_key'    => ($report_type === 'due') ? 'due-reports' : (($report_type === 'student') ? 'student-fee-reports' : 'collection-reports'),
            'report_type' => $report_type,
            'report_data' => $report_data,
            'classes'     => $classes,
            'filters'     => $filters,
        ));
    }

    // 17. Finance Settings
    public function settings()
    {
        $this->require_permission('fees.view');
        if ($this->input->method() === 'post') {
            $this->require_permission('fees.edit');
            $data = array(
                'currency_symbol'            => trim($this->input->post('currency_symbol') ?: '₹'),
                'currency_code'              => trim($this->input->post('currency_code') ?: 'INR'),
                'receipt_prefix'             => trim($this->input->post('receipt_prefix') ?: 'REC-2026-'),
                'receipt_footer'             => trim($this->input->post('receipt_footer')),
                'authorized_signature_title' => trim($this->input->post('authorized_signature_title') ?: 'Accounts Officer'),
                'allow_partial_payments'     => $this->input->post('allow_partial_payments') ? 1 : 0,
                'allow_overpayment'          => $this->input->post('allow_overpayment') ? 1 : 0,
                'require_transaction_ref'    => $this->input->post('require_transaction_ref') ? 1 : 0,
                'grace_period_days'          => (int)$this->input->post('grace_period_days') ?: 7,
                'discount_approval_required' => $this->input->post('discount_approval_required') ? 1 : 0,
                'reminder_template_upcoming' => trim($this->input->post('reminder_template_upcoming')),
                'reminder_template_overdue'  => trim($this->input->post('reminder_template_overdue')),
                'reminder_template_payment'  => trim($this->input->post('reminder_template_payment')),
            );

            $this->Finance_setting_model->update_settings($data);
            $this->Finance_audit_model->log('FINANCE_SETTINGS_UPDATED', 'tbl_finance_settings', 1, 'Updated finance settings and receipt config');

            $this->session->set_flashdata('success', 'Finance settings updated successfully.');
            redirect('fees/settings');
        }

        $settings = $this->Finance_setting_model->get_settings();
        $audit_logs = $this->Finance_audit_model->get_logs(20);

        $this->render('pages/fees/settings', array(
            'title'      => 'Finance Settings',
            'page_key'   => 'finance-settings',
            'settings'   => $settings,
            'audit_logs' => $audit_logs,
        ));
    }
}
