<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_reminder_model extends CI_Model {

    public function get_all($filters = array(), $limit = 50)
    {
        $this->db->select('fr.*, st.first_name, st.last_name, st.admission_number, c.class_name, div.division_name as division_name, div.division_name as section_name, sf.invoice_no, sf.due_amount, u.name as created_by_name')
                 ->from('tbl_fee_reminders fr')
                 ->join('tbl_students st', 'st.student_id = fr.student_id', 'inner')
                 ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
                 ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
                 ->join('tbl_student_fees sf', 'sf.student_fee_id = fr.student_fee_id', 'left')
                 ->join('tbl_users u', 'u.user_id = fr.created_by', 'left');

        if (!empty($filters['student_id'])) {
            $this->db->where('fr.student_id', $filters['student_id']);
        }
        if (!empty($filters['reminder_type'])) {
            $this->db->where('fr.reminder_type', $filters['reminder_type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('fr.status', $filters['status']);
        }

        return $this->db->order_by('fr.reminder_id', 'DESC')->limit($limit)->get()->result();
    }

    public function create_reminder($data)
    {
        $this->db->insert('tbl_fee_reminders', $data);
        return $this->db->insert_id();
    }

    public function build_message($template, $variables = array())
    {
        $message = $template;
        foreach ($variables as $key => $val) {
            $message = str_replace('{' . $key . '}', $val, $message);
        }
        return $message;
    }
}
