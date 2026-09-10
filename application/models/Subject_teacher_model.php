<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject_teacher_model extends CI_Model {

    protected $table = 'tbl_subject_teachers';
    protected $primaryKey = 'subject_teacher_id';

    public function get_all($filters = array())
    {
        $this->db
            ->select('st.*, y.year_name, c.class_name, div.division_name as division_name, div.division_name as section_name, sub.subject_name, sub.subject_code, s.full_name as teacher_name, s.employee_code')
            ->from('tbl_subject_teachers st')
            ->join('tbl_academic_years y', 'y.academic_year_id = st.academic_year_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->join('tbl_subjects sub', 'sub.subject_id = st.subject_id', 'left')
            ->join('tbl_staff s', 's.staff_id = st.staff_id', 'left')
            ->where('st.status', 1)
            ->where('st.is_deleted', 'n')
            ->where('c.is_deleted', 'n')
            ->order_by('st.class_id', 'ASC')
            ->order_by('st.division_id', 'ASC')
            ->order_by('sub.subject_name', 'ASC');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('st.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('st.class_id', $filters['class_id']);
        }
        $div_id = $filters['division_id'] ?? ($filters['section_id'] ?? null);
        if (!empty($div_id)) {
            $this->db->where('st.division_id', $div_id);
        }
        if (!empty($filters['subject_id'])) {
            $this->db->where('st.subject_id', $filters['subject_id']);
        }
        if (!empty($filters['staff_id'])) {
            $this->db->where('st.staff_id', $filters['staff_id']);
        }

        return $this->db->get()->result();
    }

    public function assign($academic_year_id, $class_id, $division_id, $subject_id, $staff_id)
    {
        // Enforce teacher only
        $staff = $this->db->where('staff_id', $staff_id)->where('is_deleted', 'n')->get('tbl_staff')->row();
        if (!$staff || $staff->status != 1 || (strtolower($staff->staff_type) !== 'teacher' && strtolower($staff->category) !== 'teaching' && strtolower($staff->category) !== 'teacher')) return FALSE;

        $existing = $this->db
            ->where('academic_year_id', $academic_year_id)
            ->where('class_id', $class_id)
            ->where('division_id', $division_id)
            ->where('subject_id', $subject_id)
            ->get($this->table)
            ->row();

        if ($existing) {
            $this->db->where('subject_teacher_id', $existing->subject_teacher_id)->update($this->table, array(
                'staff_id'   => $staff_id,
                'status'     => 1,
                'is_deleted' => 'n',
                'updated_at' => date('Y-m-d H:i:s')
            ));
            return $existing->subject_teacher_id;
        } else {
            $this->db->insert($this->table, array(
                'academic_year_id' => $academic_year_id,
                'class_id'         => $class_id,
                'division_id'      => $division_id,
                'subject_id'       => $subject_id,
                'staff_id'         => $staff_id,
                'status'           => 1,
                'is_deleted'       => 'n',
                'created_at'       => date('Y-m-d H:i:s')
            ));
            return $this->db->insert_id();
        }
    }

    public function get_teachers_by_subject($academic_year_id, $class_id, $division_id, $subject_id)
    {
        $this->db
            ->select('s.staff_id, s.full_name, s.employee_code')
            ->from('tbl_subject_teachers st')
            ->join('tbl_staff s', 's.staff_id = st.staff_id', 'inner')
            ->where('st.academic_year_id', $academic_year_id)
            ->where('st.class_id', $class_id)
            ->where('st.division_id', $division_id)
            ->where('st.subject_id', $subject_id)
            ->where('st.status', 1)
            ->where('st.is_deleted', 'n')
            ->where('s.status', 1)
            ->where('s.is_deleted', 'n');

        return $this->db->get()->result();
    }

    public function is_assigned($academic_year_id, $class_id, $division_id, $subject_id, $staff_id)
    {
        $this->db
            ->where('academic_year_id', (int)$academic_year_id)
            ->where('class_id', (int)$class_id)
            ->where('subject_id', (int)$subject_id)
            ->where('staff_id', (int)$staff_id)
            ->where('status', 1)
            ->where('is_deleted', 'n');

        if (!empty($division_id)) {
            $this->db->where('division_id', (int)$division_id);
        }

        return ($this->db->count_all_results($this->table) > 0);
    }

    public function delete($id)
    {
        return $this->db->where('subject_teacher_id', $id)->delete($this->table);
    }
}
