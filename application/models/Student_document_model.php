<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_document_model extends CI_Model {

    protected $table = 'tbl_student_documents';
    protected $primaryKey = 'document_id';

    public function get_all($filters = array())
    {
        $this->db->select('sd.*, st.first_name, st.last_name, st.admission_number, c.class_name, div.division_name as division_name, div.division_name as section_name, dc.category_name, dc.code as category_code, u.username as verifier_name')
            ->from('tbl_student_documents sd')
            ->join('tbl_students st', 'st.student_id = sd.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->join('tbl_document_categories dc', 'dc.category_id = sd.category_id', 'left')
            ->join('tbl_users u', 'u.user_id = sd.verified_by', 'left')
            ->where('sd.status', 1);

        if (!empty($filters['student_id'])) {
            $this->db->where('sd.student_id', $filters['student_id']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->where('sd.category_id', $filters['category_id']);
        }
        if (!empty($filters['verification_status'])) {
            $this->db->where('sd.verification_status', $filters['verification_status']);
        }
        if (!empty($filters['search'])) {
            $q = $filters['search'];
            $this->db->group_start()
                ->like('st.first_name', $q)
                ->or_like('st.last_name', $q)
                ->or_like('st.admission_number', $q)
                ->or_like('sd.document_name', $q)
                ->or_like('sd.document_number', $q)
                ->group_end();
        }

        $this->db->order_by('sd.document_id', 'DESC');
        $docs = $this->db->get()->result();

        // Calculate dynamic expiry status
        $today = date('Y-m-d');
        foreach ($docs as &$doc) {
            if ($doc->expiry_date) {
                $days_to_expiry = (strtotime($doc->expiry_date) - strtotime($today)) / 86400;
                if ($days_to_expiry < 0) {
                    $doc->expiry_status = 'Expired';
                } elseif ($days_to_expiry <= 30) {
                    $doc->expiry_status = 'Expiring Soon';
                } else {
                    $doc->expiry_status = 'Active';
                }
            } else {
                $doc->expiry_status = 'Permanent / N/A';
            }
        }

        return $docs;
    }

    public function get_by_id($id)
    {
        return $this->db->select('sd.*, st.first_name, st.last_name, st.admission_number, c.class_name, div.division_name as division_name, div.division_name as section_name, dc.category_name')
            ->from('tbl_student_documents sd')
            ->join('tbl_students st', 'st.student_id = sd.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = st.division_id', 'left')
            ->join('tbl_document_categories dc', 'dc.category_id = sd.category_id', 'left')
            ->where('sd.document_id', $id)
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
        return $this->db->where($this->primaryKey, $id)->update($this->table, $data);
    }

    public function verify_document($id, $status, $reason = null, $user_id = null)
    {
        $data = array(
            'verification_status' => $status,
            'rejection_reason'   => ($status === 'Rejected') ? $reason : null,
            'verified_by'        => $user_id,
            'verified_at'        => date('Y-m-d H:i:s')
        );
        return $this->update($id, $data);
    }
}
