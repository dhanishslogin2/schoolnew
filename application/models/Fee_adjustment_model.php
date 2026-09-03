<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_adjustment_model extends CI_Model {

    public function get_adjustments($filters = array(), $limit = 50)
    {
        $this->db->select('fa.*, st.first_name, st.last_name, st.admission_number, c.class_name, div.division_name as division_name, div.division_name as section_name, sf.invoice_no, fh.head_name as category_name, u.name as adjusted_by_name')
                 ->from('tbl_fee_adjustments fa')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fa.student_fee_id', 'inner')
                 ->join('tbl_students st', 'st.student_id = fa.student_id', 'inner')
                 ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
                 ->join('tbl_fee_structures fs', 'fs.fee_structure_id = sf.fee_structure_id', 'left')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'left')
                 ->join('tbl_users u', 'u.user_id = fa.adjusted_by', 'left');

        if (!empty($filters['student_id'])) {
            $this->db->where('fa.student_id', $filters['student_id']);
        }
        if (!empty($filters['adjustment_type'])) {
            $this->db->where('fa.adjustment_type', $filters['adjustment_type']);
        }

        return $this->db->order_by('fa.adjustment_id', 'DESC')->limit($limit)->get()->result();
    }

    public function create_adjustment($data)
    {
        $this->db->insert('tbl_fee_adjustments', $data);
        return $this->db->insert_id();
    }

    public function get_refunds($filters = array(), $limit = 50)
    {
        $this->db->select('fr.*, fp.receipt_no, fp.amount_paid as original_paid, fp.payment_date, st.first_name, st.last_name, st.admission_number, c.class_name, div.division_name as division_name, div.division_name as section_name, u.name as approved_by_name')
                 ->from('tbl_fee_refunds fr')
                 ->join('tbl_fee_payments fp', 'fp.payment_id = fr.payment_id', 'inner')
                 ->join('tbl_students st', 'st.student_id = fr.student_id', 'inner')
                 ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
                 ->join('tbl_users u', 'u.user_id = fr.approved_by', 'left');

        if (!empty($filters['student_id'])) {
            $this->db->where('fr.student_id', $filters['student_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('fr.status', $filters['status']);
        }

        return $this->db->order_by('fr.refund_id', 'DESC')->limit($limit)->get()->result();
    }

    public function process_refund($payment_id, $refund_amount, $reason, $refund_mode, $approved_by)
    {
        $payment = $this->db->get_where('tbl_fee_payments', array('payment_id' => $payment_id))->row();
        if (!$payment) {
            return array('success' => false, 'message' => 'Payment record not found.');
        }

        if ($refund_amount <= 0 || $refund_amount > (float)$payment->amount_paid) {
            return array('success' => false, 'message' => 'Refund amount cannot exceed total payment amount of ' . $payment->amount_paid);
        }

        // Insert refund
        $this->db->insert('tbl_fee_refunds', array(
            'payment_id'     => $payment_id,
            'student_fee_id' => $payment->student_fee_id,
            'student_id'     => $payment->student_id,
            'refund_amount'  => $refund_amount,
            'refund_reason'  => $reason,
            'refund_mode'    => $refund_mode,
            'approved_by'    => $approved_by,
            'status'         => 'Approved',
            'created_at'     => date('Y-m-d H:i:s')
        ));
        $refund_id = $this->db->insert_id();

        // Update student fee record paid and due amounts
        $sfee = $this->db->get_where('tbl_student_fees', array('student_fee_id' => $payment->student_fee_id))->row();
        if ($sfee) {
            $new_paid = max(0.00, (float)$sfee->paid_amount - (float)$refund_amount);
            $new_due = max(0.00, (float)$sfee->final_amount - $new_paid);
            $new_status = ($new_paid == 0) ? 'Pending' : (($new_paid < (float)$sfee->final_amount) ? 'Partially Paid' : 'Paid');
            if ($new_paid < (float)$sfee->final_amount && strtotime($sfee->due_date) < time()) {
                $new_status = 'Overdue';
            }

            $this->db->where('student_fee_id', $sfee->student_fee_id)->update('tbl_student_fees', array(
                'paid_amount'    => $new_paid,
                'due_amount'     => $new_due,
                'payment_status' => $new_status
            ));
        }

        return array('success' => true, 'refund_id' => $refund_id);
    }
}
