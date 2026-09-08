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

    /**
     * Check whether an academic year name already exists in tbl_academic_years.
     * Normalizes input whitespace and checks both exact and formatted variants.
     *
     * @param string $year_name
     * @param int|null $exclude_id  Optional record ID to exclude during edits
     * @return bool
     */
    public function is_year_name_exists($year_name, $exclude_id = NULL)
    {
        $year_name = trim(preg_replace('/\s+/', ' ', (string)$year_name));
        if ($year_name === '') {
            return false;
        }

        $compact_name = preg_replace('/\s*-\s*/', '-', $year_name);
        $spaced_name  = preg_replace('/\s*-\s*/', ' - ', $year_name);

        $this->db->group_start()
            ->where('year_name', $year_name)
            ->or_where('year_name', $compact_name)
            ->or_where('year_name', $spaced_name)
            ->group_end();

        if ($exclude_id !== NULL && (int)$exclude_id > 0) {
            $this->db->where($this->primaryKey . ' !=', (int)$exclude_id);
        }

        return ($this->db->count_all_results($this->table) > 0);
    }

    public function insert($data)
    {
        if (!empty($data['is_active'])) {
            $this->db->update($this->table, array('is_active' => 0));
        }

        // Defensive handling for race condition on uk_academic_year_name unique key
        $saved_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $res = $this->db->insert($this->table, $data);
        $err = $this->db->error();

        $this->db->db_debug = $saved_debug;

        if (!$res) {
            if (isset($err['code']) && (int)$err['code'] === 1062) {
                log_message('error', 'Duplicate academic year entry caught on insert: ' . ($err['message'] ?? ''));
                return false;
            }
            if ($saved_debug) {
                $this->db->display_error($err['message'] ?? 'Database error');
            }
            return false;
        }

        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        if (!empty($data['is_active'])) {
            $this->db->update($this->table, array('is_active' => 0));
        }

        // Defensive handling for race condition on uk_academic_year_name unique key
        $saved_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $res = $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, $data);
        $err = $this->db->error();

        $this->db->db_debug = $saved_debug;

        if (!$res) {
            if (isset($err['code']) && (int)$err['code'] === 1062) {
                log_message('error', 'Duplicate academic year entry caught on update: ' . ($err['message'] ?? ''));
                return false;
            }
            if ($saved_debug) {
                $this->db->display_error($err['message'] ?? 'Database error');
            }
            return false;
        }

        return $res;
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where($this->primaryKey, $id)
            ->update($this->table, ['status' => 0, 'is_deleted' => 'y']);
    }
}
