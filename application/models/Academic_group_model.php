<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academic_group_model extends CI_Model {

    protected $table = 'tbl_academic_groups';
    protected $classesTable = 'tbl_academic_group_classes';
    protected $primaryKey = 'academic_group_id';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all academic groups ordered by display_order.
     *
     * @param bool $include_inactive
     * @return array
     */
    public function get_all($include_inactive = false)
    {
        $this->db
            ->select('ag.*, (SELECT COUNT(class_id) FROM tbl_classes WHERE academic_group_id = ag.academic_group_id AND status = 1 AND is_deleted = "n") as class_count')
            ->from($this->table . ' ag')
            ->where('ag.is_deleted', 'n')
            ->order_by('ag.display_order', 'ASC')
            ->order_by('ag.academic_group_id', 'ASC');

        if (!$include_inactive) {
            $this->db->where('ag.status', 1);
        }

        return $this->db->get()->result();
    }

    /**
     * Get single academic group by ID.
     *
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db
            ->where($this->primaryKey, (int)$id)
            ->where('is_deleted', 'n')
            ->get($this->table)
            ->row();
    }

    /**
     * Check if group name already exists (case-insensitive).
     *
     * @param string $group_name
     * @param int|null $exclude_id
     * @return bool
     */
    public function check_duplicate($group_name, $exclude_id = null)
    {
        $this->db
            ->where('LOWER(group_name)', strtolower(trim($group_name)))
            ->where('is_deleted', 'n');

        if ($exclude_id) {
            $this->db->where($this->primaryKey . ' !=', (int)$exclude_id);
        }

        return ($this->db->count_all_results($this->table) > 0);
    }

    /**
     * Insert new academic group.
     *
     * @param array $data
     * @return int
     */
    public function insert($data)
    {
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }
        if (!isset($data['is_deleted'])) {
            $data['is_deleted'] = 'n';
        }
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update academic group.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db
            ->where($this->primaryKey, (int)$id)
            ->update($this->table, $data);
    }

    /**
     * Soft delete an academic group.
     *
     * @param int $id
     * @return bool
     */
    public function soft_delete($id)
    {
        return $this->update($id, array('is_deleted' => 'y', 'status' => 0));
    }

    /**
     * Enable/Disable status.
     *
     * @param int $id
     * @param int $status
     * @return bool
     */
    public function set_status($id, $status)
    {
        return $this->update($id, array('status' => (int)$status));
    }

    /**
     * Fetch the database-configured allowed class names for an Academic Group.
     * Comes from tbl_academic_group_classes so it is never hardcoded.
     *
     * @param int $academic_group_id
     * @return array
     */
    public function get_allowed_classes($academic_group_id)
    {
        return $this->db
            ->where('academic_group_id', (int)$academic_group_id)
            ->order_by('display_order', 'ASC')
            ->get($this->classesTable)
            ->result();
    }

    /**
     * Get complete hierarchy of Academic Group -> Class -> Division.
     *
     * @param int|null $academic_year_id
     * @return array
     */
    public function get_hierarchy($academic_year_id = NULL)
    {
        $this->load->model('Division_model');
        $groups = $this->get_all(false);
        $hierarchy = [];

        foreach ($groups as $grp) {
            $this->db
                ->select('c.*, y.year_name')
                ->from('tbl_classes c')
                ->join('tbl_academic_years y', 'y.academic_year_id = c.academic_year_id', 'left')
                ->where('c.academic_group_id', (int)$grp->academic_group_id)
                ->where('c.status', 1)
                ->where('c.is_deleted', 'n')
                ->order_by('c.class_id', 'ASC');

            if ($academic_year_id) {
                $this->db->where('c.academic_year_id', (int)$academic_year_id);
            }

            $classes = $this->db->get()->result();

            foreach ($classes as &$cls) {
                $cls->divisions = $this->Division_model->get_all($cls->class_id);
            }

            $grp->classes = $classes;
            $hierarchy[] = $grp;
        }

        return $hierarchy;
    }
}
