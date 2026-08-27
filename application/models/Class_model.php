<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_model extends CI_Model {

    protected $table = 'tbl_classes';
    protected $primaryKey = 'class_id';

    public function get_all($academic_year_id = NULL)
    {
        $this->db
            ->select('c.*, y.year_name, s.full_name as class_teacher_name, (SELECT COUNT(student_id) FROM tbl_students WHERE class_id = c.class_id AND status = 1 AND is_deleted = \'n\') as student_count')
            ->from('tbl_classes c')
            ->join('tbl_academic_years y', 'y.academic_year_id = c.academic_year_id', 'left')
            ->join('tbl_staff s', 's.staff_id = c.class_teacher_id', 'left')
            ->where('c.status', 1)
            ->where('c.is_deleted', 'n')
            ->order_by('c.class_id', 'ASC');

        if ($academic_year_id) {
            $this->db->where('c.academic_year_id', $academic_year_id);
        }

        return $this->db->get()->result();
    }

    public function get_dropdown($academic_year_id = NULL)
    {
        $this->db
            ->select('class_id, class_name')
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->order_by('class_id', 'ASC');

        if ($academic_year_id) {
            $this->db->where('academic_year_id', (int)$academic_year_id);
        }

        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('c.*, y.year_name, s.full_name as class_teacher_name')
            ->from('tbl_classes c')
            ->join('tbl_academic_years y', 'y.academic_year_id = c.academic_year_id', 'left')
            ->join('tbl_staff s', 's.staff_id = c.class_teacher_id', 'left')
            ->where('c.class_id', $id)
            ->get()
            ->row();
    }

    public function count_classes($academic_year_id = NULL)
    {
        if ($academic_year_id) {
            $this->db->where('academic_year_id', $academic_year_id);
        }
        return $this->db
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->count_all_results($this->table);
    }

    public function insert($data)
    {
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }
        if (!isset($data['is_deleted'])) {
            $data['is_deleted'] = 'n';
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, ['status' => 0, 'is_deleted' => 'y']);
    }
}
