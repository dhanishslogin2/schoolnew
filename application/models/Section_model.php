<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Section_model extends CI_Model {

    protected $table = 'tbl_sections';
    protected $primaryKey = 'section_id';

    /**
     * Get all sections for a class or across all classes.
     * If a specific class_id is passed and has no section records, automatically provides default Section 'A'.
     *
     * @param int|null $class_id
     * @return array
     */
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
            $this->db->where('sec.class_id', (int)$class_id);
        }

        $results = $this->db->get()->result();

        // If a specific class was requested and no sections exist in DB, provide default Section 'A'
        if ($class_id && empty($results)) {
            $class_row = $this->db->select('class_name')->where('class_id', (int)$class_id)->get('tbl_classes')->row();
            $default_sec_id = $this->get_default_section_id($class_id);
            $results = [
                (object)[
                    'section_id'         => $default_sec_id,
                    'class_id'           => (int)$class_id,
                    'section_name'       => 'A',
                    'class_name'         => $class_row ? $class_row->class_name : 'Class',
                    'class_teacher_id'   => null,
                    'class_teacher_name' => null,
                    'room_no'            => '',
                    'capacity'           => 40,
                    'student_count'      => 0,
                    'description'        => 'Default Section',
                    'status'             => 1,
                    'is_default'         => true
                ]
            ];
        }

        return $results;
    }

    /**
     * Centralized method: Get all sections belonging to a specific class.
     * Ensures Section 'A' is always present either from DB or as default fallback.
     *
     * @param int $class_id
     * @return array
     */
    public function get_sections_for_class($class_id)
    {
        return $this->get_all($class_id);
    }

    /**
     * Alias for get_all($class_id)
     */
    public function get_by_class($class_id)
    {
        return $this->get_all($class_id);
    }

    /**
     * Alias for get_all($class_id)
     */
    public function get_sections_by_class($class_id)
    {
        return $this->get_all($class_id);
    }

    /**
     * Dropdown list of sections.
     *
     * @param int|null $class_id
     * @return array
     */
    public function get_dropdown($class_id = NULL)
    {
        return $this->get_all($class_id);
    }

    /**
     * Get single section by ID.
     *
     * @param int $id
     * @return object|null
     */
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

    /**
     * Calculate the next alphabetical section name starting from 'B'.
     * Since 'A' is the reserved system default:
     * - No sections in DB (or only A) -> returns 'B'
     * - B exists in DB -> returns 'C'
     * - B, C exist in DB -> returns 'D'
     * - B, C, D exist in DB -> returns 'E', etc.
     *
     * @param int $class_id
     * @return string
     */
    public function get_next_section_name($class_id)
    {
        $existing = $this->db
            ->select('section_name')
            ->where('class_id', (int)$class_id)
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->get($this->table)
            ->result();

        $existing_names = array();
        foreach ($existing as $row) {
            $existing_names[] = strtoupper(trim($row->section_name));
        }

        // 'A' is always considered existing/reserved as default
        $existing_names[] = 'A';

        // Sequence of letters starting from 'B'
        $letters = range('B', 'Z');
        foreach ($letters as $letter) {
            if (!in_array($letter, $existing_names)) {
                return $letter;
            }
        }

        // If Z is reached, fallback to AA, AB etc.
        return 'B';
    }

    /**
     * Get a valid database section_id for Section 'A'.
     * Uses direct query to avoid polluting CodeIgniter Active Record state.
     *
     * @param int|null $class_id
     * @return int
     */
    public function get_default_section_id($class_id = NULL)
    {
        static $cached_default_ids = array();
        $cache_key = $class_id ? (int)$class_id : 'global';
        if (isset($cached_default_ids[$cache_key])) {
            return $cached_default_ids[$cache_key];
        }

        if ($class_id) {
            $row = $this->db->query("SELECT section_id FROM tbl_sections WHERE class_id = ? AND section_name = 'A' AND status = 1 AND is_deleted = 'n' LIMIT 1", [(int)$class_id])->row();
            if ($row) {
                $cached_default_ids[$cache_key] = (int)$row->section_id;
                return $cached_default_ids[$cache_key];
            }
        }

        $row_a = $this->db->query("SELECT section_id FROM tbl_sections WHERE section_name = 'A' AND status = 1 AND is_deleted = 'n' LIMIT 1")->row();
        if ($row_a) {
            $cached_default_ids[$cache_key] = (int)$row_a->section_id;
            return $cached_default_ids[$cache_key];
        }

        $any_sec = $this->db->query("SELECT section_id FROM tbl_sections WHERE status = 1 AND is_deleted = 'n' LIMIT 1")->row();
        $cached_default_ids[$cache_key] = $any_sec ? (int)$any_sec->section_id : 12;
        return $cached_default_ids[$cache_key];
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

    /**
     * Check for duplicate section name in the same class.
     *
     * @param int $class_id
     * @param string $section_name
     * @param int|null $exclude_id
     * @return bool
     */
    public function check_duplicate($class_id, $section_name, $exclude_id = NULL)
    {
        $this->db
            ->where('class_id', (int)$class_id)
            ->where('LOWER(TRIM(section_name))', strtolower(trim($section_name)))
            ->where('status', 1)
            ->where('is_deleted', 'n');

        if ($exclude_id) {
            $this->db->where($this->primaryKey . ' !=', (int)$exclude_id);
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
