<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff_document_type_model extends CI_Model {

    protected $table = 'tbl_staff_document_types';
    protected $primaryKey = 'id';

    /**
     * Get all active document types (for Add Staff and Edit Staff forms)
     */
    public function get_active_types()
    {
        return $this->db
            ->where('is_deleted', 'n')
            ->where('status', 'Active')
            ->order_by('display_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->table)
            ->result();
    }

    /**
     * Get all document types for Settings list (including active and inactive, but not soft-deleted)
     */
    public function get_all_for_settings()
    {
        return $this->db
            ->select('dt.*, u.name as creator_name, r.role_name as creator_role')
            ->from($this->table . ' dt')
            ->join('tbl_users u', 'u.user_id = dt.created_by', 'left')
            ->join('tbl_roles r', 'r.role_id = u.role_id', 'left')
            ->where('dt.is_deleted', 'n')
            ->order_by('dt.display_order', 'ASC')
            ->order_by('dt.id', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Get single document type by ID
     */
    public function get_by_id($id)
    {
        return $this->db
            ->where('id', (int)$id)
            ->where('is_deleted', 'n')
            ->get($this->table)
            ->row();
    }

    /**
     * Insert new document type
     */
    public function insert($data)
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['is_deleted'])) {
            $data['is_deleted'] = 'n';
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update existing document type
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', (int)$id)->update($this->table, $data);
    }

    /**
     * Soft delete document type (preserves all historical staff uploaded documents)
     */
    public function soft_delete($id, $user_id = NULL)
    {
        return $this->db->where('id', (int)$id)->update($this->table, array(
            'is_deleted' => 'y',
            'status'     => 'Inactive',
            'deleted_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Toggle document type active/inactive status
     */
    public function toggle_status($id)
    {
        $type = $this->get_by_id($id);
        if (!$type) return FALSE;

        $newStatus = ($type->status === 'Active') ? 'Inactive' : 'Active';
        return $this->update($id, array('status' => $newStatus));
    }

    /**
     * Count active document types
     */
    public function count_active()
    {
        return $this->db
            ->where('is_deleted', 'n')
            ->where('status', 'Active')
            ->count_all_results($this->table);
    }
}
