<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject_model extends CI_Model {

    protected $table = 'tbl_subjects';
    protected $primaryKey = 'subject_id';

    /**
     * Get all subjects, optionally filtered by class_id.
     * @param int|null $class_id  If provided, only return subjects for that class.
     *                            Pass NULL (or omit) for all subjects.
     */
    public function get_all($class_id = NULL)
    {
        $this->db
            ->select('sub.*, c.class_name, s.full_name as teacher_name')
            ->from('tbl_subjects sub')
            ->join('tbl_classes c', 'c.class_id = sub.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sub.teacher_id', 'left')
            ->where('sub.status', 1)
            ->order_by('sub.class_id', 'ASC')
            ->order_by('sub.subject_name', 'ASC');

        if ($class_id && $class_id !== TRUE) {
            $this->db->where('sub.class_id', (int)$class_id);
        }

        return $this->db->get()->result();
    }

    public function get_dropdown($class_id = NULL)
    {
        $this->db
            ->select('subject_id, subject_name, subject_code, class_id')
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->order_by('subject_name', 'ASC');

        if ($class_id) {
            $this->db->where('class_id', (int)$class_id);
        }

        return $this->db->get($this->table)->result();
    }

    /**
     * Get all active subjects (no class filter). 
     * Use this when you need every subject in the system.
     */
    public function get_all_active()
    {
        return $this->db
            ->select('sub.*, c.class_name, s.full_name as teacher_name')
            ->from('tbl_subjects sub')
            ->join('tbl_classes c', 'c.class_id = sub.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sub.teacher_id', 'left')
            ->where('sub.status', 1)
            ->order_by('sub.class_name', 'ASC')
            ->order_by('sub.subject_name', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Get subjects assigned to a class/section/year via tbl_subject_allocations.
     * This is the CORRECT method for the timetable builder — it only returns
     * subjects that have been explicitly allocated to the selected class.
     *
     * Falls back to tbl_subjects.class_id if no allocations exist yet.
     *
     * @param int $year_id
     * @param int $class_id
     * @param int|null $section_id  Optional — if provided, also filter by section.
     */
    public function get_for_class($year_id, $class_id, $section_id = NULL)
    {
        // Primary: subjects from subject_allocations table
        $this->db
            ->select('sub.subject_id, sub.subject_name, sub.subject_code, sub.subject_type,
                      sub.class_id, sub.teacher_id, sub.status,
                      c.class_name, s.full_name as teacher_name,
                      sa.allocation_id, sa.weekly_periods_target')
            ->from('tbl_subject_allocations sa')
            ->join('tbl_subjects sub', 'sub.subject_id = sa.subject_id', 'left')
            ->join('tbl_classes c', 'c.class_id = sa.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sa.teacher_id', 'left')
            ->where('sa.academic_year_id', (int)$year_id)
            ->where('sa.class_id', (int)$class_id)
            ->where('sa.status', 1)
            ->where('sub.status', 1)
            ->order_by('sub.subject_name', 'ASC');

        if ($section_id) {
            $this->db->where('sa.section_id', (int)$section_id);
        }

        $via_allocations = $this->db->get()->result();

        if (!empty($via_allocations)) {
            return $via_allocations;
        }

        // Fallback: subjects linked directly to class in tbl_subjects
        return $this->db
            ->select('sub.*, c.class_name, s.full_name as teacher_name')
            ->from('tbl_subjects sub')
            ->join('tbl_classes c', 'c.class_id = sub.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sub.teacher_id', 'left')
            ->where('sub.class_id', (int)$class_id)
            ->where('sub.status', 1)
            ->order_by('sub.subject_name', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('sub.*, c.class_name, s.full_name as teacher_name')
            ->from('tbl_subjects sub')
            ->join('tbl_classes c', 'c.class_id = sub.class_id', 'left')
            ->join('tbl_staff s', 's.staff_id = sub.teacher_id', 'left')
            ->where('sub.subject_id', $id)
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

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, array('status' => 0));
    }
}
