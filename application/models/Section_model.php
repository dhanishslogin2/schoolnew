<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Section_model extends CI_Model {

    protected $table = 'tbl_sections';
    protected $primaryKey = 'section_id';

    public function get_all($class_id = NULL)
    {
        $this->db
            ->select('sec.*, c.class_name, s.full_name as class_teacher_name, (SELECT COUNT(student_id) FROM tbl_students WHERE section_id = sec.section_id AND status = 1 AND is_deleted = \'n\') as student_count')
            ->from('tbl_sections sec')
            ->join('tbl_classes c', 'c.class_id = sec.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sec.class_teacher_id', 'left')
            ->where('sec.status', 1)
            ->where('sec.is_deleted', 'n')
            ->order_by('sec.class_id', 'ASC')
            ->order_by('sec.section_name', 'ASC');

        if ($class_id) {
            $this->db->where('sec.class_id', $class_id);
        }

        return $this->db->get()->result();
    }

    public function get_dropdown($class_id = NULL)
    {
        $this->db
            ->select('sec.section_id, sec.section_name, sec.class_id, c.class_name')
            ->from('tbl_sections sec')
            ->join('tbl_classes c', 'c.class_id = sec.class_id', 'left')
            ->where('sec.status', 1)
            ->where('sec.is_deleted', 'n')
            ->order_by('sec.class_id', 'ASC')
            ->order_by('sec.section_name', 'ASC');

        if ($class_id) {
            $this->db->where('sec.class_id', (int)$class_id);
        }

        return $this->db->get()->result();
    }

    public function get_by_class($class_id)
    {
        return $this->get_all($class_id);
    }

    public function get_sections_by_class($class_id)
    {
        return $this->get_all($class_id);
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('sec.*, c.class_name, s.full_name as class_teacher_name')
            ->from('tbl_sections sec')
            ->join('tbl_classes c', 'c.class_id = sec.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sec.class_teacher_id', 'left')
            ->where('sec.section_id', $id)
            ->get()
            ->row();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
    }

    public function check_duplicate($class_id, $section_name, $exclude_id = NULL)
    {
        $this->db
            ->where('class_id', $class_id)
            ->where('section_name', $section_name)
            ->where('status', 1)
            ->where('is_deleted', 'n');

        if ($exclude_id) {
            $this->db->where($this->primaryKey . ' !=', $exclude_id);
        }

        return $this->db->count_all_results($this->table) > 0;
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, ['status' => 0, 'is_deleted' => 'y']);
    }
}
