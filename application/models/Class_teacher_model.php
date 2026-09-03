<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_teacher_model extends CI_Model {

    protected $table = 'tbl_class_teachers';
    protected $primaryKey = 'class_teacher_id';

    public function get_all($filters = array())
    {
        $this->db
            ->select('ct.*, y.year_name, c.class_name, div.division_name as division_name, div.division_name as section_name, s.full_name as teacher_name, s.employee_code, s.phone')
            ->from('tbl_class_teachers ct')
            ->join('tbl_academic_years y', 'y.academic_year_id = ct.academic_year_id', 'left')
            ->join('tbl_classes c', 'c.class_id = ct.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = ct.division_id', 'left')
            ->join('tbl_staff s', 's.staff_id = ct.staff_id', 'left')
            ->where('ct.status', 1)
            ->order_by('ct.class_id', 'ASC')
            ->order_by('ct.division_id', 'ASC');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('ct.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('ct.class_id', $filters['class_id']);
        }
        if (!empty($filters['division_id'])) {
            $this->db->where('ct.division_id', $filters['division_id']);
        }
        if (!empty($filters['staff_id'])) {
            $this->db->where('ct.staff_id', $filters['staff_id']);
        }

        return $this->db->get()->result();
    }

    public function assign($academic_year_id, $class_id, $section_id, $staff_id)
    {
        // Enforce teacher only
        $staff = $this->db->where('staff_id', $staff_id)->where('staff_type', 'teacher')->get('tbl_staff')->row();
        if (!$staff) return FALSE;

        // Check existing for this class+section+year
        $existing = $this->db
            ->where('academic_year_id', $academic_year_id)
            ->where('class_id', $class_id)
            ->where('division_id', $section_id)
            ->get($this->table)
            ->row();

        if ($existing) {
            $this->db->where('class_teacher_id', $existing->class_teacher_id)->update($this->table, array(
                'staff_id'   => $staff_id,
                'status'     => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ));
            // Also sync tbl_sections.class_teacher_id
            $this->db->where('division_id', $section_id)->update('tbl_divisions', array('class_teacher_id' => $staff_id));
            return $existing->class_teacher_id;
        } else {
            $this->db->insert($this->table, array(
                'academic_year_id' => $academic_year_id,
                'class_id'         => $class_id,
                'division_id'       => $section_id,
                'staff_id'         => $staff_id,
                'status'           => 1,
                'created_at'       => date('Y-m-d H:i:s')
            ));
            $id = $this->db->insert_id();
            $this->db->where('division_id', $section_id)->update('tbl_divisions', array('class_teacher_id' => $staff_id));
            return $id;
        }
    }

    public function delete($id)
    {
        $ct = $this->db->where('class_teacher_id', $id)->get($this->table)->row();
        if ($ct) {
            $this->db->where('division_id', $ct->section_id)->update('tbl_divisions', array('class_teacher_id' => NULL));
            return $this->db->where('class_teacher_id', $id)->update($this->table, ['is_deleted' => 'y']);
        }
        return FALSE;
    }
}
