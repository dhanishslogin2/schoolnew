<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academic_year_model extends CI_Model {

    protected $table = 'tbl_academic_years';
    protected $primaryKey = 'academic_year_id';

    public function get_all()
    {
        return $this->db
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->order_by('start_date', 'DESC')
            ->get($this->table)
            ->result();
    }

    public function get_dropdown()
    {
        return $this->db
            ->select('academic_year_id, year_name, is_active, start_date, end_date')
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->order_by('start_date', 'DESC')
            ->get($this->table)
            ->result();
    }

    public function get_available_years()
    {
        return $this->get_dropdown();
    }

    public function get_by_id($id)
    {
        static $cached_ids = [];
        $id = (int)$id;
        if (isset($cached_ids[$id])) {
            return $cached_ids[$id];
        }

        $res = $this->db
            ->where($this->primaryKey, $id)
            ->where('is_deleted', 'n')
            ->get($this->table)
            ->row();

        $cached_ids[$id] = $res;
        return $res;
    }

    public function get_active_year()
    {
        static $cached_active = NULL;
        if ($cached_active !== NULL) {
            return $cached_active;
        }

        $active = $this->db
            ->where('is_active', 1)
            ->where('status', 1)
            ->where('is_deleted', 'n')
            ->get($this->table)
            ->row();

        if (!$active) {
            // Fallback to latest valid academic year
            $active = $this->db
                ->where('status', 1)
                ->where('is_deleted', 'n')
                ->order_by('start_date', 'DESC')
                ->limit(1)
                ->get($this->table)
                ->row();
        }

        $cached_active = $active;
        return $active;
    }

    public function get_active()
    {
        return $this->get_active_year();
    }

    public function set_active($id)
    {
        // Enforce single active academic year rule
        $this->db->update($this->table, array('is_active' => 0));
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, array('is_active' => 1));
    }

    public function insert($data)
    {
        if (!empty($data['is_active'])) {
            $this->db->update($this->table, array('is_active' => 0));
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!empty($data['is_active'])) {
            $this->db->update($this->table, array('is_active' => 0));
        }
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
