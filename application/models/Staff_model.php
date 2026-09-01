<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Staff_model extends CI_Model {

    protected $table = 'tbl_staff';
    protected $primaryKey = 'staff_id';

    public function get_dashboard_stats()
    {
        $total_staff = $this->db->where('is_deleted', 'n')->count_all_results('tbl_staff');
        $active_staff = $this->db->where('status', 1)->where('is_deleted', 'n')->count_all_results('tbl_staff');
        $inactive_staff = $this->db->where('status', 0)->where('is_deleted', 'n')->count_all_results('tbl_staff');

        $total_teachers = $this->db->where('status', 1)->where('is_deleted', 'n')
            ->group_start()
                ->where('staff_type', 'Teacher')
                ->or_where('staff_type', 'teaching')
                ->or_where('category', 'Teaching')
            ->group_end()
            ->count_all_results('tbl_staff');

        $non_teaching = $this->db->where('status', 1)->where('is_deleted', 'n')
            ->group_start()
                ->where('staff_type', 'non_teaching')
                ->or_where('category', 'Non-Teaching')
            ->group_end()
            ->count_all_results('tbl_staff');

        // Department breakdown
        $departments = $this->db->query("
            SELECT d.department_id, d.department_name, 
                   COUNT(s.staff_id) as staff_count
            FROM tbl_departments d
            LEFT JOIN tbl_staff s ON s.department_id = d.department_id AND s.status = 1 AND s.is_deleted = 'n'
            WHERE d.status = 1 AND d.is_deleted = 'n'
            GROUP BY d.department_id
            ORDER BY d.department_name ASC
        ")->result();

        // Recent staff
        $recent_staff = $this->db
            ->select('s.*, d.department_name, dg.designation_name')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.status', 1)
            ->where('s.is_deleted', 'n')
            ->order_by('s.staff_id', 'DESC')
            ->limit(8)
            ->get()
            ->result();

        return (object)[
            'total_staff'        => $total_staff,
            'active_staff'       => $active_staff,
            'inactive_staff'     => $inactive_staff,
            'total_teachers'     => $total_teachers,
            'non_teaching_staff' => $non_teaching,
            'departments'        => $departments,
            'recent_staff'       => $recent_staff,
        ];
    }

    public function get_all($filters = array())
    {
        $this->db
            ->select('s.*, 
                      s.full_name,
                      SUBSTRING_INDEX(s.full_name, " ", 1) AS first_name,
                      TRIM(SUBSTRING(s.full_name, LENGTH(SUBSTRING_INDEX(s.full_name, " ", 1)) + 1)) AS last_name,
                      s.category AS designation_category,
                      d.department_name, 
                      dg.designation_name')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.status >=', 0)
            ->where('s.is_deleted', 'n')
            ->order_by('s.staff_id', 'ASC');

        if (!empty($filters['staff_type'])) {
            $this->db->where('s.staff_type', $filters['staff_type']);
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', $filters['department_id']);
        }
        if (!empty($filters['designation_id'])) {
            $this->db->where('s.designation_id', $filters['designation_id']);
        }
        if (!empty($filters['employment_status'])) {
            $this->db->where('s.employment_status', $filters['employment_status']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('s.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('s.full_name', $s)
                ->or_like('s.employee_code', $s)
                ->or_like('s.email', $s)
                ->or_like('s.phone', $s)
                ->group_end();
        }

        return $this->db->get()->result();
    }

    public function count_filtered($filters = array())
    {
        $this->db
            ->from('tbl_staff s')
            ->where('s.is_deleted', 'n');

        if (!empty($filters['staff_type'])) {
            $this->db->where('s.staff_type', $filters['staff_type']);
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', (int)$filters['department_id']);
        }
        if (!empty($filters['designation_id'])) {
            $this->db->where('s.designation_id', (int)$filters['designation_id']);
        }
        if (!empty($filters['employment_status'])) {
            $this->db->where('s.employment_status', $filters['employment_status']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('s.status', (int)$filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('s.full_name', $s)
                ->or_like('s.employee_code', $s)
                ->or_like('s.email', $s)
                ->or_like('s.phone', $s)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_datatables_data($filters = array(), $limit = 25, $start = 0, $order_col = 's.staff_id', $order_dir = 'ASC')
    {
        $allowed_cols = array(
            0 => 's.employee_code',
            1 => 's.full_name',
            2 => 'd.department_name',
            3 => 'dg.designation_name',
            4 => 's.email',
            5 => 's.status',
            6 => 's.staff_id'
        );

        $col = isset($allowed_cols[$order_col]) ? $allowed_cols[$order_col] : 's.staff_id';
        $dir = (strtoupper($order_dir) === 'DESC') ? 'DESC' : 'ASC';

        $this->db
            ->select('s.staff_id, s.employee_code, s.full_name, s.gender, s.email, s.phone, s.photo, s.status, s.staff_type,
                      d.department_name, dg.designation_name')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.is_deleted', 'n');

        if (!empty($filters['staff_type'])) {
            $this->db->where('s.staff_type', $filters['staff_type']);
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', (int)$filters['department_id']);
        }
        if (!empty($filters['designation_id'])) {
            $this->db->where('s.designation_id', (int)$filters['designation_id']);
        }
        if (!empty($filters['employment_status'])) {
            $this->db->where('s.employment_status', $filters['employment_status']);
        }
        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'All') {
            $this->db->where('s.status', (int)$filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('s.full_name', $s)
                ->or_like('s.employee_code', $s)
                ->or_like('s.email', $s)
                ->or_like('s.phone', $s)
                ->or_like('d.department_name', $s)
                ->or_like('dg.designation_name', $s)
                ->group_end();
        }

        $this->db->order_by($col, $dir);

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    public function get_datatables_count_all()
    {
        return $this->db->where('is_deleted', 'n')->count_all_results('tbl_staff');
    }

    public function get_teachers($filters = array())
    {
        $this->db
            ->select('s.*, 
                      s.full_name,
                      SUBSTRING_INDEX(s.full_name, " ", 1) AS first_name,
                      TRIM(SUBSTRING(s.full_name, LENGTH(SUBSTRING_INDEX(s.full_name, " ", 1)) + 1)) AS last_name,
                      s.category AS designation_category,
                      d.department_name, 
                      dg.designation_name,
                      (SELECT GROUP_CONCAT(DISTINCT sub.subject_name SEPARATOR ", ") FROM tbl_subjects sub WHERE sub.teacher_id = s.staff_id AND sub.status = 1) as subjects_handled,
                      (SELECT GROUP_CONCAT(DISTINCT sub.subject_name SEPARATOR ", ") FROM tbl_subjects sub WHERE sub.teacher_id = s.staff_id AND sub.status = 1) as subject_specialization,
                      (SELECT GROUP_CONCAT(DISTINCT c.class_name SEPARATOR ", ") FROM tbl_subjects sub JOIN tbl_classes c ON c.class_id = sub.class_id WHERE sub.teacher_id = s.staff_id AND sub.status = 1) as classes_handled,
                      (SELECT GROUP_CONCAT(DISTINCT CONCAT(c.class_name, " ", sec.section_name) SEPARATOR ", ") FROM tbl_sections sec JOIN tbl_classes c ON c.class_id = sec.class_id WHERE sec.class_teacher_id = s.staff_id AND sec.status = 1) as sections_handled')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.status >=', 0)
            ->where('s.is_deleted', 'n')
            ->group_start()
                ->where('s.staff_type', 'Teacher')
                ->or_where('s.staff_type', 'teacher')
                ->or_where('s.category', 'Teaching')
            ->group_end()
            ->order_by('s.staff_id', 'ASC');

        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', $filters['department_id']);
        }
        if (!empty($filters['designation_id'])) {
            $this->db->where('s.designation_id', $filters['designation_id']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('s.full_name', $s)
                ->or_like('s.employee_code', $s)
                ->or_like('s.email', $s)
                ->group_end();
        }
        if (!empty($filters['subject_name'])) {
            $this->db->having('subjects_handled LIKE', '%' . $filters['subject_name'] . '%');
        }

        return $this->db->get()->result();
    }

    public function get_teaching_staff($filters = array())
    {
        return $this->get_teachers($filters);
    }

    public function get_non_teaching($filters = array())
    {
        $this->db
            ->select('s.*, 
                      s.full_name,
                      SUBSTRING_INDEX(s.full_name, " ", 1) AS first_name,
                      TRIM(SUBSTRING(s.full_name, LENGTH(SUBSTRING_INDEX(s.full_name, " ", 1)) + 1)) AS last_name,
                      s.category AS designation_category,
                      d.department_name, 
                      dg.designation_name')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.status >=', 0)
            ->where('s.is_deleted', 'n')
            ->where('s.staff_type', 'non_teaching')
            ->order_by('s.staff_id', 'ASC');

        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', $filters['department_id']);
        }
        if (!empty($filters['designation_id'])) {
            $this->db->where('s.designation_id', $filters['designation_id']);
        }
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('s.full_name', $s)
                ->or_like('s.employee_code', $s)
                ->or_like('s.email', $s)
                ->group_end();
        }

        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('s.*, 
                      s.full_name,
                      SUBSTRING_INDEX(s.full_name, " ", 1) AS first_name,
                      TRIM(SUBSTRING(s.full_name, LENGTH(SUBSTRING_INDEX(s.full_name, " ", 1)) + 1)) AS last_name,
                      s.category AS designation_category,
                      d.department_name, 
                      dg.designation_name')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->where('s.staff_id', $id)
            ->get()
            ->row();
    }

    public function get_profile($id)
    {
        $staff = $this->get_by_id($id);
        if (!$staff) return NULL;

        // 1. Documents
        $staff->documents = $this->db
            ->where('staff_id', $id)
            ->where('status', 1)
            ->order_by('document_id', 'DESC')
            ->get('tbl_staff_documents')
            ->result();

        // 2. Workload (Only for teachers)
        if ($staff->staff_type === 'teacher') {
            $staff->workload = $this->db
                ->select('w.*, sub.subject_name, c.class_name, sec.section_name, y.year_name')
                ->from('tbl_teacher_workload w')
                ->join('tbl_subjects sub', 'sub.subject_id = w.subject_id', 'left')
                ->join('tbl_classes c', 'c.class_id = w.class_id', 'left')
                ->join('tbl_sections sec', 'sec.section_id = w.section_id', 'left')
                ->join('tbl_academic_years y', 'y.academic_year_id = w.academic_year_id', 'left')
                ->where('w.staff_id', $id)
                ->where('w.status', 1)
                ->order_by('w.workload_id', 'DESC')
                ->get()
                ->result();
        } else {
            $staff->workload = array();
        }

        // 3. Attendance Summary
        $att_total = $this->db->where('staff_id', $id)->count_all_results('tbl_staff_attendance');
        $att_present = $this->db->where('staff_id', $id)->where('attendance_status', 'Present')->count_all_results('tbl_staff_attendance');
        $att_leave = $this->db->where('staff_id', $id)->where('attendance_status', 'Leave')->count_all_results('tbl_staff_attendance');
        $att_absent = $this->db->where('staff_id', $id)->where('attendance_status', 'Absent')->count_all_results('tbl_staff_attendance');
        $staff->attendance = (object) array(
            'total_days' => $att_total,
            'present'    => $att_present,
            'leave'      => $att_leave,
            'absent'     => $att_absent,
            'percentage' => ($att_total > 0) ? round(($att_present / $att_total) * 100, 1) : 100
        );

        // 4. Leave History
        $staff->leaves = $this->db
            ->where('staff_id', $id)
            ->order_by('leave_id', 'DESC')
            ->get('tbl_staff_leave')
            ->result();

        return $staff;
    }

    /* =========================================================================
       Staff Documents
       ========================================================================= */
    public function get_all_documents($filters = array())
    {
        $this->db
            ->select('d.*, s.employee_code, s.full_name, s.staff_type, dept.department_name, desig.designation_name, dt.document_name as type_name')
            ->from('tbl_staff_documents d')
            ->join('tbl_staff s', 's.staff_id = d.staff_id', 'left')
            ->join('tbl_departments dept', 'dept.department_id = s.department_id', 'left')
            ->join('tbl_designations desig', 'desig.designation_id = s.designation_id', 'left')
            ->join('tbl_staff_document_types dt', 'dt.id = d.document_type_id', 'left')
            ->where('d.status', 1)
            ->where('d.is_deleted', 'n')
            ->order_by('d.document_id', 'DESC');

        if (!empty($filters['staff_id'])) {
            $this->db->where('d.staff_id', (int)$filters['staff_id']);
        }
        if (!empty($filters['document_type_id'])) {
            $this->db->where('d.document_type_id', (int)$filters['document_type_id']);
        }
        if (!empty($filters['document_type'])) {
            $this->db->group_start()
                ->where('d.document_type', $filters['document_type'])
                ->or_where('d.document_name', $filters['document_type'])
                ->or_where('dt.document_name', $filters['document_type'])
                ->group_end();
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', (int)$filters['department_id']);
        }

        return $this->db->get()->result();
    }

    public function get_staff_documents_map($staff_id)
    {
        $docs = $this->db
            ->where('staff_id', (int)$staff_id)
            ->where('is_deleted', 'n')
            ->where('status', 1)
            ->order_by('document_id', 'DESC')
            ->get('tbl_staff_documents')
            ->result();

        $map = array();
        foreach ($docs as $doc) {
            if (!empty($doc->document_type_id) && !isset($map[$doc->document_type_id])) {
                $map[$doc->document_type_id] = $doc;
            }
            // Also map by lowercase document name as fallback
            $nameKey = strtolower(trim($doc->document_type ?: $doc->document_name));
            if (!isset($map[$nameKey])) {
                $map[$nameKey] = $doc;
            }
        }
        return $map;
    }

    public function get_document_by_id($document_id)
    {
        return $this->db
            ->select('d.*, s.full_name, s.employee_code')
            ->from('tbl_staff_documents d')
            ->join('tbl_staff s', 's.staff_id = d.staff_id', 'left')
            ->where('d.document_id', (int)$document_id)
            ->where('d.is_deleted', 'n')
            ->get()
            ->row();
    }

    public function add_document($data)
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['is_deleted'])) {
            $data['is_deleted'] = 'n';
        }
        $this->db->insert('tbl_staff_documents', $data);
        return $this->db->insert_id();
    }

    public function update_document($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('document_id', (int)$id)->update('tbl_staff_documents', $data);
    }

    public function delete_document($id)
    {
        return $this->db->where('document_id', (int)$id)->update('tbl_staff_documents', [
            'is_deleted' => 'y',
            'status'     => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /* =========================================================================
       Teacher Workload
       ========================================================================= */
    public function get_workloads($filters = array())
    {
        $this->db
            ->select('w.*, s.full_name, s.employee_code, sub.subject_name, c.class_name, sec.section_name, y.year_name')
            ->from('tbl_teacher_workload w')
            ->join('tbl_staff s', 's.staff_id = w.staff_id', 'left')
            ->join('tbl_subjects sub', 'sub.subject_id = w.subject_id', 'left')
            ->join('tbl_classes c', 'c.class_id = w.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = w.section_id', 'left')
            ->join('tbl_academic_years y', 'y.academic_year_id = w.academic_year_id', 'left')
            ->where('w.status', 1)
            ->where('s.staff_type', 'teacher')
            ->order_by('w.workload_id', 'DESC');

        if (!empty($filters['staff_id'])) {
            $this->db->where('w.staff_id', $filters['staff_id']);
        }
        if (!empty($filters['academic_year_id'])) {
            $this->db->where('w.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('w.class_id', $filters['class_id']);
        }
        if (!empty($filters['subject_id'])) {
            $this->db->where('w.subject_id', $filters['subject_id']);
        }

        return $this->db->get()->result();
    }

    public function add_workload($data)
    {
        $this->db->insert('tbl_teacher_workload', $data);
        return $this->db->insert_id();
    }

    public function delete_workload($id)
    {
        return $this->db->where('workload_id', $id)->update('tbl_teacher_workload', ['is_deleted' => 'y', 'status' => 0]);
    }

    /* =========================================================================
       Staff Attendance
       ========================================================================= */
    public function get_attendance_for_date($date, $department_id = NULL)
    {
        $this->db
            ->select('s.staff_id, s.employee_code, s.full_name, s.staff_type, d.department_name, dg.designation_name, att.attendance_status, att.remarks, att.attendance_id')
            ->from('tbl_staff s')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->join('tbl_staff_attendance att', 'att.staff_id = s.staff_id AND att.attendance_date = ' . $this->db->escape($date), 'left')
            ->where('s.status', 1)
            ->order_by('s.staff_id', 'ASC');

        if (!empty($department_id)) {
            $this->db->where('s.department_id', $department_id);
        }

        return $this->db->get()->result();
    }

    public function save_attendance_batch($date, $records)
    {
        if (empty($records) || !is_array($records)) return FALSE;

        $this->db->trans_start();

        foreach ($records as $staff_id => $data) {
            $status = isset($data['status']) ? $data['status'] : 'Present';
            $remarks = isset($data['remarks']) ? $data['remarks'] : '';

            $existing = $this->db
                ->where('staff_id', $staff_id)
                ->where('attendance_date', $date)
                ->get('tbl_staff_attendance')
                ->row();

            if ($existing) {
                $this->db->where('attendance_id', $existing->attendance_id)->update('tbl_staff_attendance', array(
                    'attendance_status' => $status,
                    'remarks'           => $remarks,
                    'updated_at'        => date('Y-m-d H:i:s')
                ));
            } else {
                $this->db->insert('tbl_staff_attendance', array(
                    'staff_id'          => $staff_id,
                    'attendance_date'   => $date,
                    'attendance_status' => $status,
                    'remarks'           => $remarks,
                    'created_at'        => date('Y-m-d H:i:s')
                ));
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /* =========================================================================
       Staff Leave Management
       ========================================================================= */
    public function get_leaves($filters = array())
    {
        $this->db
            ->select('l.*, s.full_name, s.employee_code, s.staff_type, d.department_name, dg.designation_name')
            ->from('tbl_staff_leave l')
            ->join('tbl_staff s', 's.staff_id = l.staff_id', 'left')
            ->join('tbl_departments d', 'd.department_id = s.department_id', 'left')
            ->join('tbl_designations dg', 'dg.designation_id = s.designation_id', 'left')
            ->order_by('l.leave_id', 'DESC');

        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $this->db->where('l.status', $filters['status']);
        }
        if (!empty($filters['staff_id'])) {
            $this->db->where('l.staff_id', $filters['staff_id']);
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('s.department_id', $filters['department_id']);
        }

        return $this->db->get()->result();
    }

    public function apply_leave($data)
    {
        $this->db->insert('tbl_staff_leave', $data);
        return $this->db->insert_id();
    }

    public function update_leave_status($leave_id, $status, $approved_by = NULL, $remarks = '')
    {
        $update = array(
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        if ($approved_by) $update['approved_by'] = $approved_by;
        if ($remarks) $update['remarks'] = $remarks;

        return $this->db->where('leave_id', $leave_id)->update('tbl_staff_leave', $update);
    }

    /* =========================================================================
       CRUD Helpers
       ========================================================================= */
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
            ->update($this->table, array('status' => 0, 'is_deleted' => 'y', 'employment_status' => 'Resigned'));
    }

    public function count_staff()
    {
        return $this->db->where('status', 1)->where('is_deleted', 'n')->count_all_results($this->table);
    }

    public function count_teachers()
    {
        return $this->db->where('status', 1)->where('is_deleted', 'n')->where('staff_type', 'teacher')->count_all_results($this->table);
    }

    public function update_photo($staff_id, $photo_filename)
    {
        return $this->db
            ->where($this->primaryKey, (int)$staff_id)
            ->update($this->table, array('photo' => $photo_filename, 'updated_at' => date('Y-m-d H:i:s')));
    }

    public function delete_photo($staff_id)
    {
        $staff = $this->get_by_id($staff_id);
        if ($staff && !empty($staff->photo)) {
            $filePath = FCPATH . 'uploads/staff/' . $staff->photo;
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }
        return $this->db
            ->where($this->primaryKey, (int)$staff_id)
            ->update($this->table, array('photo' => NULL, 'updated_at' => date('Y-m-d H:i:s')));
    }
}
