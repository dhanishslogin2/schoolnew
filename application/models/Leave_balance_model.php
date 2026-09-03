<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_balance_model extends CI_Model {

    protected $table = 'tbl_leave_balances';
    protected $primaryKey = 'balance_id';

    public function get_balance($academic_year_id, $entity_type, $entity_id, $leave_type_id)
    {
        $bal = $this->db
            ->where('academic_year_id', $academic_year_id)
            ->where('entity_type', $entity_type)
            ->where('entity_id', $entity_id)
            ->where('leave_type_id', $leave_type_id)
            ->get($this->table)
            ->row();

        if (!$bal) {
            // Auto-initialize balance from leave type default max_days
            $lt = $this->db->where('type_id', $leave_type_id)->get('tbl_leave_types')->row();
            $max_days = $lt ? (float)$lt->max_days : 12.0;

            $this->db->insert($this->table, [
                'academic_year_id' => $academic_year_id,
                'entity_type'      => $entity_type,
                'entity_id'        => $entity_id,
                'leave_type_id'    => $leave_type_id,
                'allocated_days'   => $max_days,
                'used_days'        => 0.0,
                'pending_days'     => 0.0,
                'carry_forward_days'=> 0.0,
                'created_at'       => date('Y-m-d H:i:s')
            ]);
            $bal = $this->get_balance($academic_year_id, $entity_type, $entity_id, $leave_type_id);
        }

        $bal->remaining_days = max(0, ($bal->allocated_days + $bal->carry_forward_days) - $bal->used_days);
        return $bal;
    }

    public function deduct_balance($academic_year_id, $entity_type, $entity_id, $leave_type_id, $days)
    {
        $bal = $this->get_balance($academic_year_id, $entity_type, $entity_id, $leave_type_id);
        $new_used = $bal->used_days + (float)$days;

        return $this->db->where('balance_id', $bal->balance_id)->update($this->table, [
            'used_days'  => $new_used,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function restore_balance($academic_year_id, $entity_type, $entity_id, $leave_type_id, $days)
    {
        $bal = $this->get_balance($academic_year_id, $entity_type, $entity_id, $leave_type_id);
        $new_used = max(0.0, $bal->used_days - (float)$days);

        return $this->db->where('balance_id', $bal->balance_id)->update($this->table, [
            'used_days'  => $new_used,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_all_balances($academic_year_id = 1, $entity_type = 'Staff', $limit = 50)
    {
        $this->db
            ->select('b.*, lt.type_name, lt.type_code, s.full_name as staff_name, s.employee_code, d.department_name, st.first_name, st.last_name, st.admission_number, st.admission_number as admission_no, c.class_name, div.division_name as division_name, div.division_name as section_name')
            ->from('tbl_leave_balances b')
            ->join('tbl_leave_types lt', 'lt.type_id = b.leave_type_id', 'left')
            ->join('tbl_staff s', "s.staff_id = b.entity_id AND b.entity_type = 'Staff'", 'left')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_students st', "st.student_id = b.entity_id AND b.entity_type = 'Student'", 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->where('b.academic_year_id', $academic_year_id)
            ->where('b.entity_type', $entity_type)
            ->limit($limit);

        $results = $this->db->get()->result();
        foreach ($results as &$r) {
            $r->remaining_days = max(0, ($r->allocated_days + $r->carry_forward_days) - $r->used_days);
        }
        return $results;
    }
}
