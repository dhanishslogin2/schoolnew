<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Student_academic_model
 *
 * Handles CRUD for:
 *   - tbl_student_previous_school   (previous school + TC + prior performance)
 *   - tbl_student_activities        (academic achievements + extracurricular)
 *
 * Loaded by Students controller during wizard_save.
 * All writes are wrapped inside the controller's transaction.
 */
class Student_academic_model extends CI_Model
{
    /* =========================================================================
       Previous School
    ========================================================================= */

    /**
     * Insert a previous school record.
     *
     * @param  array $data  Columns matching tbl_student_previous_school
     * @return int          Inserted ID
     */
    public function insert_previous_school(array $data)
    {
        $this->db->insert('tbl_student_previous_school', $data);
        return (int)$this->db->insert_id();
    }

    /**
     * Get previous school record for a student.
     *
     * @param  int $student_id
     * @return object|null
     */
    public function get_previous_school($student_id)
    {
        return $this->db
            ->where('student_id', (int)$student_id)
            ->where('status', 1)
            ->order_by('prev_school_id', 'DESC')
            ->get('tbl_student_previous_school')
            ->row();
    }

    /* =========================================================================
       Activities (Academic & Extracurricular)
    ========================================================================= */

    /**
     * Insert a single activity record.
     *
     * @param  array $data  Columns matching tbl_student_activities
     * @return int          Inserted ID
     */
    public function insert_activity(array $data)
    {
        $this->db->insert('tbl_student_activities', $data);
        return (int)$this->db->insert_id();
    }

    /**
     * Bulk-insert multiple activity records.
     * Uses batch insert for efficiency.
     *
     * @param  array $rows  Array of data arrays
     * @return bool
     */
    public function insert_activities_batch(array $rows)
    {
        if (empty($rows)) {
            return TRUE;
        }
        return (bool)$this->db->insert_batch('tbl_student_activities', $rows);
    }

    /**
     * Get all activities for a student, optionally filtered by category.
     *
     * @param  int         $student_id
     * @param  string|null $category  'Academic' | 'Extracurricular' | null (all)
     * @return array
     */
    public function get_activities($student_id, $category = NULL)
    {
        $this->db->where('student_id', (int)$student_id)
                 ->where('status', 1);

        if ($category !== NULL) {
            $this->db->where('category', $category);
        }

        return $this->db
            ->order_by('category', 'ASC')
            ->order_by('activity_type', 'ASC')
            ->order_by('year', 'DESC')
            ->get('tbl_student_activities')
            ->result();
    }

    /* =========================================================================
       TC Document (via tbl_student_documents — reused existing table)
    ========================================================================= */

    /**
     * Insert a TC document into tbl_student_documents.
     * Returns the document_id so it can be stored in tbl_student_previous_school.
     *
     * @param  int    $student_id
     * @param  string $file_path    Relative path e.g. 'uploads/documents/tc_xxx.pdf'
     * @param  string $tc_number    Original TC number for naming
     * @return int                  document_id
     */
    public function insert_tc_document($student_id, $file_path, $tc_number = '')
    {
        $data = array(
            'student_id'    => (int)$student_id,
            'document_type' => 'Transfer Certificate',
            'document_name' => 'TC - ' . ($tc_number ?: 'Transfer Certificate'),
            'file_path'     => $file_path,
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
        );
        $this->db->insert('tbl_student_documents', $data);
        return (int)$this->db->insert_id();
    }
}
