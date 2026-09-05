<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_model extends CI_Model {

    public function get_dashboard_metrics($academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();
        $today = date('Y-m-d');
        $cur_month = date('m');
        $cur_year = date('Y');

        // 1. Total Fee Expected (Total final_amount of active assigned fees)
        $this->db->select('SUM(final_amount) as total')->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $exp_res = $this->db->get('tbl_student_fees')->row();
        $total_expected = ($exp_res && $exp_res->total) ? (float)$exp_res->total : 0.00;

        // 2. Total Fee Collected (Total amount_paid in tbl_fee_payments)
        $this->db->select('SUM(fp.amount_paid) as total')
                 ->from('tbl_fee_payments fp')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'inner')
                 ->where('fp.status', 1);
        if ($academic_year_id) $this->db->where('sf.academic_year_id', $academic_year_id);
        $col_res = $this->db->get()->row();
        $total_collected = ($col_res && $col_res->total) ? (float)$col_res->total : 0.00;

        // 3. Total Pending & Overdue
        $this->db->select('SUM(due_amount) as total')
                 ->where_in('payment_status', array('Pending', 'Partially Paid', 'Overdue'))
                 ->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $pend_res = $this->db->get('tbl_student_fees')->row();
        $total_pending = ($pend_res && $pend_res->total) ? (float)$pend_res->total : max(0.00, $total_expected - $total_collected);

        $this->db->select('SUM(due_amount) as total')
                 ->where('due_date <', $today)
                 ->where('due_amount >', 0)
                 ->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $over_res = $this->db->get('tbl_student_fees')->row();
        $total_overdue = ($over_res && $over_res->total) ? (float)$over_res->total : 0.00;

        // 4. Today's Collection
        $this->db->select('SUM(fp.amount_paid) as total')
                 ->from('tbl_fee_payments fp')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'inner')
                 ->where('fp.payment_date', $today)
                 ->where('fp.status', 1);
        if ($academic_year_id) $this->db->where('sf.academic_year_id', $academic_year_id);
        $today_res = $this->db->get()->row();
        $today_collection = ($today_res && $today_res->total) ? (float)$today_res->total : 0.00;

        // 5. This Month's Collection
        $this->db->select('SUM(fp.amount_paid) as total')
                 ->from('tbl_fee_payments fp')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'inner')
                 ->where('MONTH(fp.payment_date)', $cur_month)
                 ->where('YEAR(fp.payment_date)', $cur_year)
                 ->where('fp.status', 1);
        if ($academic_year_id) $this->db->where('sf.academic_year_id', $academic_year_id);
        $month_res = $this->db->get()->row();
        $monthly_collection = ($month_res && $month_res->total) ? (float)$month_res->total : 0.00;

        // 6. Number of Students with Due Fees
        $this->db->select('COUNT(DISTINCT student_id) as cnt')->where('due_amount >', 0)->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $due_stu_res = $this->db->get('tbl_student_fees')->row();
        $students_with_dues = ($due_stu_res) ? (int)$due_stu_res->cnt : 0;

        // 7. Number of Fully Paid Students
        $this->db->select('COUNT(DISTINCT student_id) as cnt')->where('payment_status', 'Paid')->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $paid_stu_res = $this->db->get('tbl_student_fees')->row();
        $fully_paid_students = ($paid_stu_res) ? (int)$paid_stu_res->cnt : 0;

        return array(
            'total_expected'       => $total_expected,
            'total_collected'      => $total_collected,
            'total_pending'        => $total_pending,
            'total_overdue'        => $total_overdue,
            'today_collection'     => $today_collection,
            'monthly_collection'   => $monthly_collection,
            'students_with_dues'   => $students_with_dues,
            'fully_paid_students'  => $fully_paid_students,
        );
    }

    public function get_collection_summary($academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();
        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('-7 days'));
        $month_start = date('Y-m-01');
        $year_start = date('Y-01-01');

        $calc_sum = function($where_date) use ($academic_year_id) {
            $this->db->select('SUM(fp.amount_paid) as total')
                     ->from('tbl_fee_payments fp')
                     ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'inner')
                     ->where('fp.status', 1);
            if ($academic_year_id) $this->db->where('sf.academic_year_id', $academic_year_id);
            if (!empty($where_date)) {
                foreach ($where_date as $k => $v) {
                    $this->db->where($k, $v);
                }
            }
            $row = $this->db->get()->row();
            return ($row && $row->total) ? (float)$row->total : 0.00;
        };

        $daily = $calc_sum(array('fp.payment_date' => $today));
        $weekly = $calc_sum(array('fp.payment_date >=' => $week_start));
        $monthly = $calc_sum(array('fp.payment_date >=' => $month_start));
        $yearly = $calc_sum(array('fp.payment_date >=' => $year_start));

        return array(
            'daily'   => (float)$daily,
            'weekly'  => (float)$weekly,
            'monthly' => (float)$monthly,
            'yearly'  => (float)$yearly,
        );
    }

    public function get_outstanding_summary($academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();
        $today = date('Y-m-d');

        $this->db->select('SUM(due_amount) as total')->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $total_res = $this->db->get('tbl_student_fees')->row();
        $total_outstanding = ($total_res && $total_res->total) ? (float)$total_res->total : 0.00;

        $this->db->select('SUM(due_amount) as total')->where('due_date <', $today)->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $over_res = $this->db->get('tbl_student_fees')->row();
        $overdue_amount = ($over_res && $over_res->total) ? (float)$over_res->total : 0.00;

        $this->db->select('SUM(due_amount) as total')->where('due_date >=', $today)->where('status', 1);
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $up_res = $this->db->get('tbl_student_fees')->row();
        $upcoming_dues = ($up_res && $up_res->total) ? (float)$up_res->total : 0.00;

        return array(
            'total_outstanding' => $total_outstanding,
            'overdue_amount'    => $overdue_amount,
            'upcoming_dues'     => $upcoming_dues,
        );
    }

    public function get_recent_payments($limit = 10, $academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        $this->db->select('fp.*, st.admission_number, st.first_name, st.last_name, c.class_name, div.division_name as division_name, div.division_name as section_name, fh.head_name as category_name, sf.invoice_no')
                 ->from('tbl_fee_payments fp')
                 ->join('tbl_students st', 'st.student_id = fp.student_id', 'left')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'left')
                 ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
                 ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
                 ->where('fp.status', 1);

        if ($academic_year_id) {
            $this->db->where('sf.academic_year_id', $academic_year_id);
        }

        return $this->db->order_by('fp.payment_id', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->result();
    }

    public function get_student_fees($filters = array(), $limit = 100)
    {
        $this->db->select('sf.*, st.admission_number, st.first_name, st.last_name, st.roll_number, st.guardian_name, st.guardian_phone, c.class_name, div.division_name as division_name, div.division_name as section_name, fh.head_name as category_name, fh.category_code, fs.frequency')
                 ->from('tbl_student_fees sf')
                 ->join('tbl_students st', 'st.student_id = sf.student_id', 'inner')
                 ->join('tbl_classes c', 'c.class_id = sf.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = sf.division_id', 'left')
                 ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('sf.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['student_id'])) {
            $this->db->where('sf.student_id', $filters['student_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('sf.class_id', $filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $this->db->where('sf.division_id', $filters['division_id']);
        }
        if (!empty($filters['fee_head_id'])) {
            $this->db->where('fs.fee_head_id', $filters['fee_head_id']);
        }
        if (!empty($filters['payment_status'])) {
            $this->db->where('sf.payment_status', $filters['payment_status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                     ->like('st.first_name', $s)
                     ->or_like('st.last_name', $s)
                     ->or_like('st.admission_number', $s)
                     ->or_like('sf.invoice_no', $s)
                     ->group_end();
        }

        return $this->db->order_by('sf.student_fee_id', 'DESC')->limit($limit)->get()->result();
    }

    public function get_student_fee_by_id($id)
    {
        return $this->db->select('sf.*, st.admission_number, st.first_name, st.last_name, st.roll_number, st.guardian_name, st.guardian_phone, c.class_name, div.division_name as division_name, div.division_name as section_name, fh.head_name as category_name, fh.category_code, fs.amount as structure_amount, fs.frequency')
                        ->from('tbl_student_fees sf')
                        ->join('tbl_students st', 'st.student_id = sf.student_id', 'inner')
                        ->join('tbl_classes c', 'c.class_id = sf.class_id', 'left')
                        ->join('tbl_divisions div', 'div.division_id = sf.division_id', 'left')
                        ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                        ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
                        ->where('sf.student_fee_id', $id)
                        ->get()
                        ->row();
    }

    public function get_student_fee_profile($student_id)
    {
        $today = date('Y-m-d');
        $fees = $this->get_student_fees(array('student_id' => $student_id));
        $payments = $this->get_payments(array('student_id' => $student_id));

        $total_assigned = 0.00;
        $total_paid = 0.00;
        $total_discount = 0.00;
        $total_concession = 0.00;
        $total_due = 0.00;
        $total_overdue = 0.00;

        foreach ($fees as $f) {
            $total_assigned += (float)$f->original_amount;
            $total_paid += (float)$f->paid_amount;
            $total_discount += (float)$f->discount_amount;
            $total_concession += (float)$f->concession_amount;
            $total_due += (float)$f->due_amount;
            if ($f->due_date < $today && (float)$f->due_amount > 0) {
                $total_overdue += (float)$f->due_amount;
            }
        }

        return (object) array(
            'summary' => (object) array(
                'total_assigned'   => $total_assigned,
                'total_paid'       => $total_paid,
                'total_discount'   => $total_discount,
                'total_concession' => $total_concession,
                'total_due'        => $total_due,
                'total_overdue'    => $total_overdue,
            ),
            'fees'     => $fees,
            'payments' => $payments
        );
    }

    public function assign_student_fee($student_id, $fee_structure_id, $amount, $due_date, $discount_amount = 0.00, $concession_amount = 0.00, $remarks = '')
    {
        $student = $this->db->get_where('tbl_students', array('student_id' => $student_id))->row();
        if (!$student) return false;

        $fs = $this->db->get_where('tbl_fee_structures', array('fee_structure_id' => $fee_structure_id))->row();
        $academic_year_id = ($fs) ? $fs->academic_year_id : ($student->academic_year_id ?: 1);

        $orig = (float)$amount;
        $disc = (float)$discount_amount;
        $conc = (float)$concession_amount;
        $final = max(0.00, $orig - $disc - $conc);
        $due = $final;

        $data = array(
            'student_id'        => $student_id,
            'academic_year_id'  => $academic_year_id,
            'class_id'          => $student->class_id,
            'division_id'       => $student->division_id ?? ($student->section_id ?? null),
            'fee_structure_id'  => $fee_structure_id,
            'original_amount'   => $orig,
            'discount_amount'   => $disc,
            'concession_amount' => $conc,
            'final_amount'      => $final,
            'paid_amount'       => 0.00,
            'due_amount'        => $due,
            'due_date'          => $due_date,
            'payment_status'    => ($due == 0) ? 'Paid' : 'Pending',
            'remarks'           => $remarks,
            'status'            => 1,
            'created_at'        => date('Y-m-d H:i:s')
        );

        $this->db->insert('tbl_student_fees', $data);
        $id = $this->db->insert_id();

        // Update invoice number
        $invoice_no = 'INV-2026-' . str_pad($id, 4, '0', STR_PAD_LEFT);
        $this->db->where('student_fee_id', $id)->update('tbl_student_fees', array('invoice_no' => $invoice_no));

        return $id;
    }

    /**
     * Get active students for a class and division in an academic year,
     * along with their fee assignment status for a given fee structure.
     *
     * @param int $academic_year_id
     * @param int $class_id
     * @param int $division_id
     * @param int|null $fee_structure_id
     * @return array
     */
    public function get_students_for_bulk_assignment($academic_year_id, $class_id, $division_id, $fee_structure_id = null)
    {
        $academic_year_id = (int)$academic_year_id;
        $class_id = (int)$class_id;
        $division_id = (int)$division_id;
        $fee_structure_id = $fee_structure_id ? (int)$fee_structure_id : null;

        if ($class_id <= 0 || $division_id <= 0) {
            return array();
        }

        $this->db
            ->select('st.student_id, st.admission_number, st.roll_number, st.first_name, st.last_name, st.class_id, st.division_id, st.academic_year_id')
            ->from('tbl_students st')
            ->where('st.class_id', $class_id)
            ->where('st.division_id', $division_id)
            ->where('st.status', 1)
            ->where('st.is_deleted', 'n');

        if ($academic_year_id > 0) {
            $this->db->where('st.academic_year_id', $academic_year_id);
        }

        $students = $this->db
            ->order_by('CAST(st.roll_number AS UNSIGNED)', 'ASC')
            ->order_by('st.student_id', 'ASC')
            ->get()
            ->result();

        if (empty($students)) {
            return array();
        }

        // Check already assigned students if fee_structure_id is provided
        $assigned_map = array();
        if ($fee_structure_id && $fee_structure_id > 0) {
            $student_ids = array_map(function($s) { return (int)$s->student_id; }, $students);
            if (!empty($student_ids)) {
                $assigned_rows = $this->db
                    ->select('student_id')
                    ->from('tbl_student_fees')
                    ->where('fee_structure_id', $fee_structure_id)
                    ->where_in('student_id', $student_ids)
                    ->where('status', 1)
                    ->where('is_deleted', 'n')
                    ->get()
                    ->result();

                foreach ($assigned_rows as $row) {
                    $assigned_map[(int)$row->student_id] = true;
                }
            }
        }

        foreach ($students as &$s) {
            $s->student_name = trim($s->first_name . ' ' . $s->last_name);
            $s->already_assigned = isset($assigned_map[(int)$s->student_id]);
        }

        return $students;
    }

    /**
     * Assign fee structure to specific user-selected students.
     * Verifies that each student belongs to the specified academic year, class, and division,
     * is active, and does not already have this fee structure assigned.
     *
     * @param array $student_ids
     * @param int $fee_structure_id
     * @param int $academic_year_id
     * @param int $class_id
     * @param int $division_id
     * @return array ['assigned_count' => int, 'skipped_count' => int]
     */
    public function bulk_assign_selected_students($student_ids, $fee_structure_id, $academic_year_id, $class_id, $division_id)
    {
        $fs = $this->db->get_where('tbl_fee_structures', array('fee_structure_id' => (int)$fee_structure_id))->row();
        if (!$fs) {
            return array('assigned_count' => 0, 'skipped_count' => 0);
        }

        if (!is_array($student_ids) || empty($student_ids)) {
            return array('assigned_count' => 0, 'skipped_count' => 0);
        }

        // Sanitize and filter student IDs
        $clean_ids = array_values(array_filter(array_map('intval', $student_ids), function($id) { return $id > 0; }));
        if (empty($clean_ids)) {
            return array('assigned_count' => 0, 'skipped_count' => 0);
        }

        // Verify valid students belonging to this academic year, class, and division
        $this->db
            ->select('student_id')
            ->from('tbl_students')
            ->where_in('student_id', $clean_ids)
            ->where('class_id', (int)$class_id)
            ->where('division_id', (int)$division_id)
            ->where('status', 1)
            ->where('is_deleted', 'n');

        if ($academic_year_id > 0) {
            $this->db->where('academic_year_id', (int)$academic_year_id);
        }

        $valid_students = $this->db->get()->result();
        if (empty($valid_students)) {
            return array('assigned_count' => 0, 'skipped_count' => count($clean_ids));
        }

        $valid_student_ids = array_map(function($s) { return (int)$s->student_id; }, $valid_students);

        // Check which ones are already assigned
        $already_assigned_rows = $this->db
            ->select('student_id')
            ->from('tbl_student_fees')
            ->where('fee_structure_id', (int)$fee_structure_id)
            ->where_in('student_id', $valid_student_ids)
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->get()
            ->result();

        $already_assigned_map = array();
        foreach ($already_assigned_rows as $row) {
            $already_assigned_map[(int)$row->student_id] = true;
        }

        $assigned_count = 0;
        $skipped_count = 0;

        foreach ($clean_ids as $sid) {
            // If student is not in valid class/division/academic_year list
            if (!in_array($sid, $valid_student_ids, true)) {
                $skipped_count++;
                continue;
            }

            // If student already has this fee structure
            if (isset($already_assigned_map[$sid])) {
                $skipped_count++;
                continue;
            }

            // Assign fee
            $assigned_id = $this->assign_student_fee($sid, (int)$fee_structure_id, $fs->amount, $fs->due_date);
            if ($assigned_id) {
                $assigned_count++;
                $already_assigned_map[$sid] = true; // prevent duplicate within same run
            } else {
                $skipped_count++;
            }
        }

        return array('assigned_count' => $assigned_count, 'skipped_count' => $skipped_count);
    }

    public function bulk_assign_fee_structure($class_id, $section_id, $fee_structure_id, $academic_year_id)
    {
        $fs = $this->db->get_where('tbl_fee_structures', array('fee_structure_id' => $fee_structure_id))->row();
        if (!$fs) return 0;

        $this->db->select('student_id, class_id, division_id')->from('tbl_students')->where('status', 1);
        if ($class_id > 0) {
            $this->db->where('class_id', $class_id);
        }
        if ($section_id > 0) {
            $this->db->where('division_id', $section_id);
        }
        $students = $this->db->get()->result();

        $count = 0;
        foreach ($students as $s) {
            // Check if already assigned for this structure
            $exists = $this->db->where('student_id', $s->student_id)
                               ->where('fee_structure_id', $fee_structure_id)
                               ->where('status', 1)
                               ->count_all_results('tbl_student_fees');
            if ($exists === 0) {
                $this->assign_student_fee($s->student_id, $fee_structure_id, $fs->amount, $fs->due_date);
                $count++;
            }
        }
        return $count;
    }

    public function collect_payment($student_fee_id, $amount_to_pay, $payment_mode, $transaction_ref, $payment_date, $remarks, $collected_by)
    {
        $this->load->model('Finance_setting_model');
        $sfee = $this->get_student_fee_by_id($student_fee_id);
        if (!$sfee) {
            return array('success' => false, 'message' => 'Fee record not found.');
        }

        $pay_amount = (float)$amount_to_pay;
        if ($pay_amount <= 0) {
            return array('success' => false, 'message' => 'Payment amount must be greater than zero.');
        }

        if ($pay_amount > (float)$sfee->due_amount) {
            $settings = $this->Finance_setting_model->get_settings();
            if (!$settings->allow_overpayment) {
                return array('success' => false, 'message' => 'Payment amount (₹' . number_format($pay_amount, 2) . ') exceeds outstanding due amount of ₹' . number_format($sfee->due_amount, 2));
            }
        }

        // Generate receipt number
        $receipt_no = $this->Finance_setting_model->generate_next_receipt_number();

        // 1. Insert into tbl_fee_payments
        $this->db->insert('tbl_fee_payments', array(
            'student_fee_id'        => $student_fee_id,
            'student_id'            => $sfee->student_id,
            'amount_paid'           => $pay_amount,
            'payment_mode'          => $payment_mode,
            'transaction_reference' => $transaction_ref,
            'payment_date'          => $payment_date,
            'receipt_no'            => $receipt_no,
            'collected_by'          => $collected_by,
            'remarks'               => $remarks,
            'status'                => 1,
            'created_at'            => date('Y-m-d H:i:s')
        ));
        $payment_id = $this->db->insert_id();

        // 2. Update tbl_student_fees balance & status
        $new_paid = (float)$sfee->paid_amount + $pay_amount;
        $new_due = max(0.00, (float)$sfee->final_amount - $new_paid);
        $new_status = ($new_due == 0.00) ? 'Paid' : 'Partially Paid';

        $this->db->where('student_fee_id', $student_fee_id)->update('tbl_student_fees', array(
            'paid_amount'    => $new_paid,
            'due_amount'     => $new_due,
            'payment_status' => $new_status
        ));

        // 3. Log Audit
        $this->load->model('Finance_audit_model');
        $this->Finance_audit_model->log(
            'PAYMENT_COLLECTED',
            'tbl_fee_payments',
            $payment_id,
            "Collected ₹{$pay_amount} for {$sfee->first_name} {$sfee->last_name} ({$sfee->category_name}). Receipt: {$receipt_no}",
            array('due_before' => $sfee->due_amount),
            array('due_after' => $new_due, 'receipt_no' => $receipt_no)
        );

        return array(
            'success'    => true,
            'payment_id' => $payment_id,
            'receipt_no' => $receipt_no
        );
    }

    public function get_receipt_by_id($payment_id)
    {
        return $this->db->select('fp.*, st.admission_number, st.first_name, st.last_name, st.roll_number, st.guardian_name, st.guardian_phone, c.class_name, div.division_name as division_name, div.division_name as section_name, fh.head_name as category_name, sf.invoice_no, sf.original_amount, sf.discount_amount, sf.concession_amount, sf.final_amount, sf.paid_amount as total_paid_to_date, sf.due_amount as remaining_due, sf.due_date, u.name as collected_by_name')
                        ->from('tbl_fee_payments fp')
                        ->join('tbl_students st', 'st.student_id = fp.student_id', 'inner')
                        ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'inner')
                        ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                        ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
                        ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
                        ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
                        ->join('tbl_users u', 'u.user_id = fp.collected_by', 'left')
                        ->where('fp.payment_id', $payment_id)
                        ->get()
                        ->row();
    }

    public function get_payments($filters = array(), $limit = 100)
    {
        $this->db->select('fp.*, st.admission_number, st.first_name, st.last_name, c.class_name, div.division_name as division_name, div.division_name as section_name, fh.head_name as category_name, sf.invoice_no, u.name as collected_by_name')
                 ->from('tbl_fee_payments fp')
                 ->join('tbl_students st', 'st.student_id = fp.student_id', 'inner')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'left')
                 ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
                 ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
                 ->join('tbl_users u', 'u.user_id = fp.collected_by', 'left')
                 ->where('fp.status', 1);

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('sf.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['student_id'])) {
            $this->db->where('fp.student_id', $filters['student_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', $filters['class_id']);
        }
        if (!empty($filters['payment_mode'])) {
            $this->db->where('fp.payment_mode', $filters['payment_mode']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('fp.payment_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('fp.payment_date <=', $filters['date_to']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                     ->like('st.first_name', $s)
                     ->or_like('st.last_name', $s)
                     ->or_like('fp.receipt_no', $s)
                     ->or_like('st.admission_number', $s)
                     ->group_end();
        }

        return $this->db->order_by('fp.payment_id', 'DESC')->limit($limit)->get()->result();
    }

    public function count_filtered_payments($filters = array())
    {
        $this->db
            ->from('tbl_fee_payments fp')
            ->join('tbl_students st', 'st.student_id = fp.student_id', 'inner')
            ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'left')
            ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
            ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->where('fp.is_deleted', 'n')
            ->where('fp.status', 1);

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('sf.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['student_id'])) {
            $this->db->where('fp.student_id', (int)$filters['student_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['payment_mode'])) {
            $this->db->where('fp.payment_mode', $filters['payment_mode']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('fp.payment_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('fp.payment_date <=', $filters['date_to']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                     ->like('st.first_name', $s)
                     ->or_like('st.last_name', $s)
                     ->or_like('fp.receipt_no', $s)
                     ->or_like('st.admission_number', $s)
                     ->or_like('fp.transaction_reference', $s)
                     ->or_like('fh.head_name', $s)
                     ->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_payments_datatables($filters = array(), $limit = 25, $start = 0, $order_col = 'fp.payment_id', $order_dir = 'DESC')
    {
        $allowed_cols = array(
            0 => 'fp.receipt_no',
            1 => 'st.first_name',
            2 => 'c.class_name',
            3 => 'fh.head_name',
            4 => 'fp.amount_paid',
            5 => 'fp.payment_mode',
            6 => 'fp.transaction_reference',
            7 => 'fp.payment_date',
            8 => 'fp.payment_id'
        );

        $col = isset($allowed_cols[$order_col]) ? $allowed_cols[$order_col] : 'fp.payment_id';
        $dir = (strtoupper($order_dir) === 'ASC') ? 'ASC' : 'DESC';

        $this->db->select('fp.payment_id, fp.receipt_no, fp.amount_paid, fp.payment_mode, fp.transaction_reference, fp.payment_date,
                           st.student_id, st.admission_number, st.first_name, st.last_name, 
                           c.class_name, div.division_name as division_name, div.division_name as section_name, fh.head_name as category_name, sf.invoice_no, u.name as collected_by_name')
                 ->from('tbl_fee_payments fp')
                 ->join('tbl_students st', 'st.student_id = fp.student_id', 'inner')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'left')
                 ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
                 ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
                 ->join('tbl_users u', 'u.user_id = fp.collected_by', 'left')
                 ->where('fp.is_deleted', 'n')
                 ->where('fp.status', 1);

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('sf.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['student_id'])) {
            $this->db->where('fp.student_id', (int)$filters['student_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['payment_mode'])) {
            $this->db->where('fp.payment_mode', $filters['payment_mode']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('fp.payment_date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('fp.payment_date <=', $filters['date_to']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                     ->like('st.first_name', $s)
                     ->or_like('st.last_name', $s)
                     ->or_like('fp.receipt_no', $s)
                     ->or_like('st.admission_number', $s)
                     ->or_like('fp.transaction_reference', $s)
                     ->or_like('fh.head_name', $s)
                     ->group_end();
        }

        $this->db->order_by($col, $dir);

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    public function get_payments_count_all($academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        $this->db->from('tbl_fee_payments fp')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fp.student_fee_id', 'left')
                 ->where('fp.is_deleted', 'n')
                 ->where('fp.status', 1);

        if ($academic_year_id) {
            $this->db->where('sf.academic_year_id', $academic_year_id);
        }

        return $this->db->count_all_results();
    }

    public function get_due_fees($filters = array(), $limit = 100)
    {
        $today = date('Y-m-d');
        $this->db->select("sf.*, st.admission_number, st.first_name, st.last_name, st.roll_number, st.guardian_name, st.guardian_phone, c.class_name, div.division_name as division_name, div.division_name as section_name, fh.head_name as category_name, DATEDIFF('{$today}', sf.due_date) as days_overdue")
                 ->from('tbl_student_fees sf')
                 ->join('tbl_students st', 'st.student_id = sf.student_id', 'inner')
                 ->join('tbl_classes c', 'c.class_id = sf.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = sf.division_id', 'left')
                 ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
                 ->where('sf.due_amount >', 0)
                 ->where('sf.status', 1);

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('sf.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('sf.class_id', $filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $this->db->where('sf.division_id', $filters['division_id']);
        }
        if (!empty($filters['fee_head_id'])) {
            $this->db->where('fs.fee_head_id', $filters['fee_head_id']);
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'Due Today') {
                $this->db->where('sf.due_date', $today);
            } elseif ($filters['status'] === 'Upcoming') {
                $this->db->where('sf.due_date >', $today);
            } elseif ($filters['status'] === 'Overdue') {
                $this->db->where('sf.due_date <', $today);
            } elseif ($filters['status'] === 'Partially Paid') {
                $this->db->where('sf.payment_status', 'Partially Paid');
            }
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                     ->like('st.first_name', $s)
                     ->or_like('st.last_name', $s)
                     ->or_like('st.admission_number', $s)
                     ->group_end();
        }

        return $this->db->order_by('sf.due_date', 'ASC')->limit($limit)->get()->result();
    }
}
