<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fee_structure_model extends CI_Model {

    public function get_all($filters = array())
    {
        $this->db->select('fs.*, fh.head_name as category_name, fh.category_code, ay.year_name, c.class_name, ag.academic_group_id, ag.group_name')
                 ->from('tbl_fee_structures fs')
                 ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'inner')
                 ->join('tbl_academic_years ay', 'ay.academic_year_id = fs.academic_year_id', 'left')
                 ->join('tbl_classes c', 'c.class_id = fs.class_id', 'left')
                 ->join('tbl_academic_groups ag', 'ag.academic_group_id = c.academic_group_id', 'left')
                 ->where('fs.is_deleted', 'n');

        if (!empty($filters['academic_year_id'])) {
            $this->db->where('fs.academic_year_id', (int)$filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('fs.class_id', (int)$filters['class_id']);
        }
        if (!empty($filters['academic_group_id'])) {
            $this->db->where('c.academic_group_id', (int)$filters['academic_group_id']);
        }
        if (!empty($filters['fee_head_id'])) {
            $this->db->where('fs.fee_head_id', (int)$filters['fee_head_id']);
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('fs.status', (int)$filters['status']);
        }

        return $this->db->order_by('ag.display_order', 'ASC')
                        ->order_by('c.class_id', 'ASC')
                        ->order_by('fh.head_name', 'ASC')
                        ->get()
                        ->result();
    }

    public function get_by_id($id)
    {
        return $this->db->select('fs.*, fh.head_name as category_name, fh.category_code, ay.year_name, c.class_name, ag.academic_group_id, ag.group_name')
                        ->from('tbl_fee_structures fs')
                        ->join('tbl_fee_heads fh', 'fh.fee_head_id = fs.fee_head_id', 'inner')
                        ->join('tbl_academic_years ay', 'ay.academic_year_id = fs.academic_year_id', 'left')
                        ->join('tbl_classes c', 'c.class_id = fs.class_id', 'left')
                        ->join('tbl_academic_groups ag', 'ag.academic_group_id = c.academic_group_id', 'left')
                        ->where('fs.fee_structure_id', (int)$id)
                        ->where('fs.is_deleted', 'n')
                        ->get()
                        ->row();
    }

    public function is_duplicate($fee_head_id, $academic_year_id, $class_id, $exclude_id = 0)
    {
        $this->db->where('fee_head_id', (int)$fee_head_id)
                 ->where('academic_year_id', (int)$academic_year_id)
                 ->where('class_id', (int)$class_id)
                 ->where('is_deleted', 'n');

        if ($exclude_id > 0) {
            $this->db->where('fee_structure_id !=', (int)$exclude_id);
        }

        return ($this->db->count_all_results('tbl_fee_structures') > 0);
    }

    /**
     * Bulk create fee structures for target classes.
     * Skips existing duplicates and creates missing records within a transaction.
     *
     * @param array $base_data Base template fields (fee_head_id, academic_year_id, amount, frequency, due_date, etc.)
     * @param array $target_classes Array of class objects or class IDs
     * @return array ['created' => int, 'skipped' => int, 'created_ids' => array]
     */
    public function bulk_create_structures($base_data, $target_classes)
    {
        $this->db->trans_start();

        $created_count = 0;
        $skipped_count = 0;
        $created_ids = array();

        foreach ($target_classes as $target) {
            $class_id = is_object($target) ? (int)$target->class_id : (is_array($target) ? (int)$target['class_id'] : (int)$target);

            if ($class_id <= 0) {
                continue;
            }

            if ($this->is_duplicate($base_data['fee_head_id'], $base_data['academic_year_id'], $class_id)) {
                $skipped_count++;
                continue;
            }

            $record = $base_data;
            $record['class_id'] = $class_id;
            $record['created_at'] = date('Y-m-d H:i:s');

            $this->db->insert('tbl_fee_structures', $record);
            $new_id = $this->db->insert_id();
            if ($new_id) {
                $created_ids[] = $new_id;
                $created_count++;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array('created' => 0, 'skipped' => 0, 'error' => true);
        }

        return array(
            'created'     => $created_count,
            'skipped'     => $skipped_count,
            'created_ids' => $created_ids
        );
    }

    public function save($data, $id = 0)
    {
        if ($id > 0) {
            $this->db->where('fee_structure_id', (int)$id)->update('tbl_fee_structures', $data);
            return $id;
        } else {
            $this->db->insert('tbl_fee_structures', $data);
            return $this->db->insert_id();
        }
    }

    public function delete($id)
    {
        // Prevent deletion if assigned to any student fee
        $assigned = $this->db->where('fee_structure_id', (int)$id)->count_all_results('tbl_student_fees');
        if ($assigned > 0) {
            return false;
        }
        return $this->db->where('fee_structure_id', (int)$id)->update('tbl_fee_structures', ['is_deleted' => 'y']);
    }
}
