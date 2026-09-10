<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homework_notification_model extends CI_Model {

    protected $table = 'tbl_homework_notifications';
    protected $primaryKey = 'notification_id';

    public function get_all($filters = array(), $limit = 50)
    {
        $this->db
            ->select('n.*, a.title as assignment_title, s.first_name, s.last_name, s.admission_number, c.class_name, div.division_name as division_name, div.division_name as section_name')
            ->from('tbl_homework_notifications n')
            ->join('tbl_assignments a', 'a.assignment_id = n.assignment_id', 'left')
            ->join('tbl_students s', 's.student_id = n.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = s.class_id', 'left')
            ->join('tbl_divisions div', 'div.division_id = s.division_id', 'left')
            ->order_by('n.created_at', 'DESC');

        if (!empty($filters['assignment_id'])) $this->db->where('n.assignment_id', $filters['assignment_id']);
        if (!empty($filters['notification_type'])) $this->db->where('n.notification_type', $filters['notification_type']);
        if (!empty($filters['status'])) $this->db->where('n.status', $filters['status']);

        if ($limit) $this->db->limit($limit);

        return $this->db->get()->result();
    }

    public function queue_notification($assignment_id, $student_id, $type, $message)
    {
        $student = $this->db->where('student_id', $student_id)->get('tbl_students')->row();
        
        $data = [
            'assignment_id'     => $assignment_id,
            'student_id'        => $student_id,
            'parent_name'       => $student ? ($student->guardian_name ?: ($student->father_name ?: 'Parent')) : 'Parent',
            'parent_phone'      => $student ? ($student->guardian_phone ?: ($student->father_phone ?: '')) : '',
            'parent_email'      => $student ? ($student->guardian_email ?: ($student->father_email ?: '')) : '',
            'notification_type' => $type,
            'message'           => $message,
            'status'            => 'Pending',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function queue_class_notifications($assignment_id, $type, $message)
    {
        $asgn = $this->db->where('assignment_id', $assignment_id)->get('tbl_assignments')->row();
        if (!$asgn) return 0;

        $this->db->where('class_id', $asgn->class_id);
        if (!empty($asgn->division_id)) $this->db->where('division_id', $asgn->division_id);
        $students = $this->db->where('status', 1)->get('tbl_students')->result();

        $count = 0;
        foreach ($students as $stu) {
            $this->queue_notification($assignment_id, $stu->student_id, $type, $message);
            $count++;
        }
        return $count;
    }
}
