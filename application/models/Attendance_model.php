<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_model extends CI_Model {

    protected $table = 'tbl_attendance';
    protected $primaryKey = 'attendance_id';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_setting_model');
        $this->load->model('Attendance_notification_model');
    }

    /* =========================================================================
       1. Dashboard & Statistics
       ========================================================================= */
    public function get_dashboard_stats($date = NULL, $class_id = NULL, $section_id = NULL, $year_id = NULL)
    {
        if (!$date) $date = date('Y-m-d');

        // Total active students matching filter
        $this->db->where('status', 1);
        if ($class_id) $this->db->where('class_id', $class_id);
        if ($section_id) $this->db->where('section_id', $section_id);
        if ($year_id) $this->db->where('academic_year_id', $year_id);
        $total_students = $this->db->count_all_results('tbl_students');

        // Query daily attendance for this date
        $this->db
            ->select('attendance_status, COUNT(*) as count')
            ->from($this->table)
            ->where('attendance_date', $date)
            ->where('attendance_type', 'Daily');

        if ($class_id) $this->db->where('class_id', $class_id);
        if ($section_id) $this->db->where('section_id', $section_id);
        if ($year_id) $this->db->where('academic_year_id', $year_id);

        $counts = $this->db->group_by('attendance_status')->get()->result();

        $present = 0;
        $absent = 0;
        $late = 0;
        $excused = 0;

        foreach ($counts as $c) {
            if ($c->attendance_status === 'Present') $present = (int)$c->count;
            elseif ($c->attendance_status === 'Absent') $absent = (int)$c->count;
            elseif ($c->attendance_status === 'Late') $late = (int)$c->count;
            elseif (in_array($c->attendance_status, array('Excused', 'Leave'))) $excused += (int)$c->count;
        }

        $total_marked = $present + $absent + $late + $excused;
        $not_marked = max(0, $total_students - $total_marked);

        $percentage = ($total_marked > 0) ? round(($present / $total_marked) * 100, 1) : 0;
        $present_pct = ($total_marked > 0) ? round(($present / $total_marked) * 100, 1) : 0;
        $absent_pct = ($total_marked > 0) ? round(($absent / $total_marked) * 100, 1) : 0;
        $late_pct = ($total_marked > 0) ? round(($late / $total_marked) * 100, 1) : 0;
        $excused_pct = ($total_marked > 0) ? round(($excused / $total_marked) * 100, 1) : 0;

        return (object) array(
            'date'           => $date,
            'total_students' => $total_students,
            'total_marked'   => $total_marked,
            'not_marked'     => $not_marked,
            'present'        => $present,
            'absent'         => $absent,
            'late'           => $late,
            'excused'        => $excused,
            'percentage'     => $percentage,
            'present_pct'    => $present_pct,
            'absent_pct'     => $absent_pct,
            'late_pct'       => $late_pct,
            'excused_pct'    => $excused_pct,
        );
    }

    public function get_class_overview($date = NULL, $year_id = NULL, $class_id = NULL, $section_id = NULL)
    {
        if (!$date) $date = normalize_date_to_academic_year(NULL, $year_id);
        $year_id = $year_id ? (int)$year_id : get_current_academic_year_id();

        $this->db
            ->select('c.class_id, c.class_name, sec.section_id, sec.section_name,
                COUNT(DISTINCT st.student_id) as total_students,
                SUM(CASE WHEN a.attendance_status = "Present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.attendance_status = "Absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.attendance_status = "Late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN a.attendance_status IN ("Excused", "Leave") THEN 1 ELSE 0 END) as excused_count,
                COUNT(a.attendance_id) as marked_count')
            ->from('tbl_classes c')
            ->join('tbl_sections sec', 'sec.class_id = c.class_id AND sec.status = 1', 'inner')
            ->join('tbl_students st', 'st.class_id = c.class_id AND st.section_id = sec.section_id AND st.status = 1 AND (st.academic_year_id = ' . (int)$year_id . ' OR st.academic_year_id IS NULL)', 'left')
            ->join($this->table . ' a', 'a.student_id = st.student_id AND a.attendance_date = ' . $this->db->escape($date) . ' AND a.attendance_type = "Daily" AND (a.academic_year_id = ' . (int)$year_id . ' OR a.academic_year_id IS NULL)', 'left')
            ->where('c.status', 1)
            ->group_by(array('c.class_id', 'sec.section_id'))
            ->order_by('c.class_id', 'ASC')
            ->order_by('sec.section_name', 'ASC');

        if ($year_id) $this->db->where('c.academic_year_id', $year_id);
        if ($class_id) $this->db->where('c.class_id', $class_id);
        if ($section_id) $this->db->where('sec.section_id', $section_id);

        $results = $this->db->get()->result();

        foreach ($results as $row) {
            $marked = (int)$row->marked_count;
            $row->is_marked = ($marked > 0);
            $row->percentage = ($marked > 0) ? round(($row->present_count / $marked) * 100, 1) : 0;
        }

        return $results;
    }

    public function get_recent_activity($limit = 10, $year_id = NULL)
    {
        $year_id = $year_id ? (int)$year_id : get_current_academic_year_id();

        $this->db
            ->select('a.*, st.first_name, st.last_name, st.admission_number, st.roll_number, c.class_name, sec.section_name, p.period_name, u.name as marked_by_name')
            ->from($this->table . ' a')
            ->join('tbl_students st', 'st.student_id = a.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = a.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = a.section_id', 'left')
            ->join('tbl_periods p', 'p.period_id = a.period_id', 'left')
            ->join('tbl_users u', 'u.user_id = a.marked_by', 'left');

        if ($year_id) {
            $this->db->where('a.academic_year_id', $year_id);
        }

        return $this->db
            ->order_by('a.updated_at', 'DESC')
            ->order_by('a.attendance_id', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }

    /* =========================================================================
       2. Daily Attendance Sheet & Marking
       ========================================================================= */
    public function get_daily_sheet($date, $class_id = NULL, $section_id = NULL, $year_id = NULL)
    {
        $this->db
            ->select('st.student_id, st.admission_number, st.roll_number, st.first_name, st.middle_name, st.last_name, st.photo, st.guardian_name, st.guardian_phone, st.guardian_email, c.class_name, sec.section_name, a.attendance_id, a.attendance_status, a.remarks, a.attendance_type, a.updated_at')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->join($this->table . ' a', 'a.student_id = st.student_id AND a.attendance_date = ' . $this->db->escape($date) . ' AND a.attendance_type = "Daily"', 'left')
            ->where('st.status', 1)
            ->order_by('CAST(st.roll_number AS UNSIGNED)', 'ASC')
            ->order_by('st.first_name', 'ASC');

        if ($class_id) $this->db->where('st.class_id', $class_id);
        if ($section_id) $this->db->where('st.section_id', $section_id);
        if ($year_id) $this->db->where('st.academic_year_id', $year_id);

        return $this->db->get()->result();
    }

    public function check_daily_marked($date, $class_id, $section_id, $year_id = NULL)
    {
        $this->db
            ->where('attendance_date', $date)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('attendance_type', 'Daily');
        if ($year_id) {
            $this->db->where('academic_year_id', $year_id);
        }
        return ($this->db->count_all_results($this->table) > 0);
    }

    public function save_daily_attendance($attendance_records, $date, $academic_year_id, $class_id, $section_id, $user_id = NULL)
    {
        $settings = $this->Attendance_setting_model->get_settings();
        $saved_count = 0;

        foreach ($attendance_records as $student_id => $rec) {
            $status = is_array($rec) ? (isset($rec['status']) ? $rec['status'] : 'Present') : $rec;
            $remarks = is_array($rec) ? (isset($rec['remarks']) ? $rec['remarks'] : '') : '';

            // Check existing daily attendance for this student and date
            $existing = $this->db
                ->where('student_id', $student_id)
                ->where('attendance_date', $date)
                ->where('attendance_type', 'Daily')
                ->get($this->table)
                ->row();

            $att_id = NULL;

            if ($existing) {
                $this->db
                    ->where('attendance_id', $existing->attendance_id)
                    ->update($this->table, array(
                        'attendance_status' => $status,
                        'remarks'           => $remarks,
                        'marked_by'         => $user_id,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ));
                $att_id = $existing->attendance_id;
            } else {
                $this->db->insert($this->table, array(
                    'student_id'        => $student_id,
                    'academic_year_id'  => $academic_year_id,
                    'class_id'          => $class_id,
                    'section_id'        => $section_id,
                    'attendance_date'   => $date,
                    'attendance_type'   => 'Daily',
                    'period_id'         => NULL,
                    'attendance_status' => $status,
                    'remarks'           => $remarks,
                    'marked_by'         => $user_id,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ));
                $att_id = $this->db->insert_id();
            }

            $saved_count++;

            // Handle parent notification generation foundation for Absent / Late / Excused
            if ($att_id && in_array($status, array('Absent', 'Late', 'Excused'))) {
                $student = $this->db->where('student_id', $student_id)->get('tbl_students')->row();
                if ($student) {
                    $this->Attendance_notification_model->create_for_attendance($student, $att_id, $date, $status, $settings);
                }
            }
        }

        return $saved_count;
    }

    /* =========================================================================
       3. Period-wise Attendance Sheet & Marking
       ========================================================================= */
    public function get_period_sheet($date, $period_id, $class_id = NULL, $section_id = NULL, $year_id = NULL)
    {
        $this->db
            ->select('st.student_id, st.admission_number, st.roll_number, st.first_name, st.last_name, st.photo, c.class_name, sec.section_name, p.period_name, p.period_number, p.start_time, p.end_time, a.attendance_id, a.attendance_status, a.remarks, a.updated_at')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->join('tbl_periods p', 'p.period_id = ' . $this->db->escape($period_id), 'left')
            ->join($this->table . ' a', 'a.student_id = st.student_id AND a.attendance_date = ' . $this->db->escape($date) . ' AND a.attendance_type = "Period-wise" AND a.period_id = ' . $this->db->escape($period_id), 'left')
            ->where('st.status', 1)
            ->order_by('CAST(st.roll_number AS UNSIGNED)', 'ASC')
            ->order_by('st.first_name', 'ASC');

        if ($class_id) $this->db->where('st.class_id', $class_id);
        if ($section_id) $this->db->where('st.section_id', $section_id);
        if ($year_id) $this->db->where('st.academic_year_id', $year_id);

        return $this->db->get()->result();
    }

    public function check_period_marked($date, $period_id, $class_id, $section_id, $year_id = NULL)
    {
        $this->db
            ->where('attendance_date', $date)
            ->where('period_id', $period_id)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('attendance_type', 'Period-wise');
        if ($year_id) {
            $this->db->where('academic_year_id', $year_id);
        }
        return ($this->db->count_all_results($this->table) > 0);
    }

    public function save_period_attendance($attendance_records, $date, $period_id, $academic_year_id, $class_id, $section_id, $user_id = NULL)
    {
        $settings = $this->Attendance_setting_model->get_settings();
        $saved_count = 0;

        foreach ($attendance_records as $student_id => $rec) {
            $status = is_array($rec) ? (isset($rec['status']) ? $rec['status'] : 'Present') : $rec;
            $remarks = is_array($rec) ? (isset($rec['remarks']) ? $rec['remarks'] : '') : '';

            $existing = $this->db
                ->where('student_id', $student_id)
                ->where('attendance_date', $date)
                ->where('attendance_type', 'Period-wise')
                ->where('period_id', $period_id)
                ->get($this->table)
                ->row();

            $att_id = NULL;

            if ($existing) {
                $this->db
                    ->where('attendance_id', $existing->attendance_id)
                    ->update($this->table, array(
                        'attendance_status' => $status,
                        'remarks'           => $remarks,
                        'marked_by'         => $user_id,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ));
                $att_id = $existing->attendance_id;
            } else {
                $this->db->insert($this->table, array(
                    'student_id'        => $student_id,
                    'academic_year_id'  => $academic_year_id,
                    'class_id'          => $class_id,
                    'section_id'        => $section_id,
                    'attendance_date'   => $date,
                    'attendance_type'   => 'Period-wise',
                    'period_id'         => $period_id,
                    'attendance_status' => $status,
                    'remarks'           => $remarks,
                    'marked_by'         => $user_id,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ));
                $att_id = $this->db->insert_id();
            }

            $saved_count++;
        }

        return $saved_count;
    }

    /* =========================================================================
       4. History & Tracking Queries
       ========================================================================= */
    public function get_history($filters = array(), $limit = NULL, $offset = NULL)
    {
        $this->db
            ->select('a.*, st.admission_number, st.roll_number, st.first_name, st.last_name, c.class_name, sec.section_name, p.period_name, p.period_number, u.name as marked_by_name')
            ->from($this->table . ' a')
            ->join('tbl_students st', 'st.student_id = a.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = a.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = a.section_id', 'left')
            ->join('tbl_periods p', 'p.period_id = a.period_id', 'left')
            ->join('tbl_users u', 'u.user_id = a.marked_by', 'left')
            ->order_by('a.attendance_date', 'DESC')
            ->order_by('a.updated_at', 'DESC');

        $this->_apply_history_filters($filters);

        if ($limit) {
            $this->db->limit($limit, $offset ?: 0);
        }

        return $this->db->get()->result();
    }

    public function count_history($filters = array())
    {
        $this->db
            ->from($this->table . ' a')
            ->join('tbl_students st', 'st.student_id = a.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = a.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = a.section_id', 'left');

        $this->_apply_history_filters($filters);

        return $this->db->count_all_results();
    }

    private function _apply_history_filters($filters)
    {
        if (!empty($filters['attendance_type'])) {
            $this->db->where('a.attendance_type', $filters['attendance_type']);
        }
        if (!empty($filters['attendance_status'])) {
            $this->db->where('a.attendance_status', $filters['attendance_status']);
        }
        if (!empty($filters['academic_year_id'])) {
            $this->db->where('a.academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['class_id'])) {
            $this->db->where('a.class_id', $filters['class_id']);
        }
        if (!empty($filters['section_id'])) {
            $this->db->where('a.section_id', $filters['section_id']);
        }
        if (!empty($filters['student_id'])) {
            $this->db->where('a.student_id', $filters['student_id']);
        }
        if (!empty($filters['period_id'])) {
            $this->db->where('a.period_id', $filters['period_id']);
        }
        if (!empty($filters['date'])) {
            $this->db->where('a.attendance_date', $filters['date']);
        }
        if (!empty($filters['from_date'])) {
            $this->db->where('a.attendance_date >=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $this->db->where('a.attendance_date <=', $filters['to_date']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $this->db->group_start()
                ->like('st.first_name', $s)
                ->or_like('st.last_name', $s)
                ->or_like('st.admission_number', $s)
                ->or_like('a.remarks', $s)
                ->group_end();
        }
    }

    public function get_tracking_records($filters = array(), $limit = 100)
    {
        // Tracking is dedicated for Absent, Late, Excused
        $this->db
            ->select('a.*, st.admission_number, st.roll_number, st.first_name, st.last_name, st.guardian_name, st.guardian_phone, c.class_name, sec.section_name, p.period_name')
            ->from($this->table . ' a')
            ->join('tbl_students st', 'st.student_id = a.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = a.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = a.section_id', 'left')
            ->join('tbl_periods p', 'p.period_id = a.period_id', 'left')
            ->where_in('a.attendance_status', array('Absent', 'Late', 'Excused', 'Leave'))
            ->order_by('a.attendance_date', 'DESC')
            ->order_by('a.updated_at', 'DESC');

        if (!empty($filters['status_filter']) && $filters['status_filter'] !== 'All') {
            if ($filters['status_filter'] === 'Excused') {
                $this->db->where_in('a.attendance_status', array('Excused', 'Leave'));
            } else {
                $this->db->where('a.attendance_status', $filters['status_filter']);
            }
        }
        if (!empty($filters['class_id'])) $this->db->where('a.class_id', $filters['class_id']);
        if (!empty($filters['section_id'])) $this->db->where('a.section_id', $filters['section_id']);
        if (!empty($filters['student_id'])) $this->db->where('a.student_id', $filters['student_id']);
        if (!empty($filters['academic_year_id'])) $this->db->where('a.academic_year_id', $filters['academic_year_id']);
        if (!empty($filters['from_date'])) $this->db->where('a.attendance_date >=', $filters['from_date']);
        if (!empty($filters['to_date'])) $this->db->where('a.attendance_date <=', $filters['to_date']);

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    /* =========================================================================
       5. Calendar Matrix & Aggregation
       ========================================================================= */
    public function get_calendar_data($year, $month, $class_id = NULL, $section_id = NULL, $student_id = NULL, $type = 'Daily', $academic_year_id = NULL)
    {
        $start_date = sprintf('%04d-%02d-01', $year, $month);
        $end_date   = date('Y-m-t', strtotime($start_date));

        $this->db
            ->select('a.attendance_date, a.attendance_status, COUNT(*) as count')
            ->from($this->table . ' a')
            ->where('a.attendance_date >=', $start_date)
            ->where('a.attendance_date <=', $end_date)
            ->where('a.attendance_type', $type);

        if ($student_id) $this->db->where('a.student_id', $student_id);
        if ($class_id) $this->db->where('a.class_id', $class_id);
        if ($section_id) $this->db->where('a.section_id', $section_id);
        if ($academic_year_id) $this->db->where('a.academic_year_id', $academic_year_id);

        $records = $this->db
            ->group_by(array('a.attendance_date', 'a.attendance_status'))
            ->get()
            ->result();

        $matrix = array();
        foreach ($records as $r) {
            $date = $r->attendance_date;
            if (!isset($matrix[$date])) {
                $matrix[$date] = array('Present' => 0, 'Absent' => 0, 'Late' => 0, 'Excused' => 0, 'total' => 0);
            }
            $st = ($r->attendance_status === 'Leave') ? 'Excused' : $r->attendance_status;
            if (isset($matrix[$date][$st])) {
                $matrix[$date][$st] += (int)$r->count;
            }
            $matrix[$date]['total'] += (int)$r->count;
        }

        return $matrix;
    }

    public function get_date_attendance_details($date, $class_id = NULL, $section_id = NULL, $student_id = NULL, $type = 'Daily', $academic_year_id = NULL)
    {
        $this->db
            ->select('a.*, st.first_name, st.last_name, st.admission_number, st.roll_number, c.class_name, sec.section_name, p.period_name')
            ->from($this->table . ' a')
            ->join('tbl_students st', 'st.student_id = a.student_id', 'left')
            ->join('tbl_classes c', 'c.class_id = a.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = a.section_id', 'left')
            ->join('tbl_periods p', 'p.period_id = a.period_id', 'left')
            ->where('a.attendance_date', $date)
            ->where('a.attendance_type', $type)
            ->order_by('c.class_id', 'ASC')
            ->order_by('sec.section_id', 'ASC')
            ->order_by('CAST(st.roll_number AS UNSIGNED)', 'ASC');

        if ($student_id) $this->db->where('a.student_id', $student_id);
        if ($class_id) $this->db->where('a.class_id', $class_id);
        if ($section_id) $this->db->where('a.section_id', $section_id);
        if ($academic_year_id) $this->db->where('a.academic_year_id', $academic_year_id);

        return $this->db->get()->result();
    }

    /* =========================================================================
       6. Advanced Reports Hub
       ========================================================================= */
    public function get_reports_summary($academic_year_id = NULL, $class_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        $this->db
            ->select('c.class_name, sec.section_name,
                SUM(CASE WHEN a.attendance_status = "Present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.attendance_status = "Absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.attendance_status = "Late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN a.attendance_status IN ("Excused", "Leave") THEN 1 ELSE 0 END) as excused_count,
                COUNT(a.attendance_id) as total_count')
            ->from('tbl_sections sec')
            ->join('tbl_classes c', 'c.class_id = sec.class_id', 'inner')
            ->join('tbl_attendance a', 'a.section_id = sec.section_id AND a.attendance_type = "Daily" AND a.academic_year_id = ' . (int)$academic_year_id, 'left')
            ->where('sec.status', 1)
            ->where('c.academic_year_id', $academic_year_id)
            ->group_by('sec.section_id')
            ->order_by('c.class_id', 'ASC')
            ->order_by('sec.section_name', 'ASC');

        if ($class_id) $this->db->where('c.class_id', $class_id);

        $results = $this->db->get()->result();

        foreach ($results as $row) {
            $effective_total = $row->present_count + $row->absent_count + $row->late_count + $row->excused_count;
            $row->percentage = $effective_total > 0 ? round(($row->present_count / $effective_total) * 100, 1) : 0;
        }

        return $results;
    }

    public function get_student_report($filters = array())
    {
        $year_id = !empty($filters['academic_year_id']) ? (int)$filters['academic_year_id'] : get_current_academic_year_id();

        $this->db
            ->select('st.student_id, st.admission_number, st.roll_number, st.first_name, st.last_name, c.class_name, sec.section_name,
                SUM(CASE WHEN a.attendance_status = "Present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.attendance_status = "Absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.attendance_status = "Late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN a.attendance_status IN ("Excused", "Leave") THEN 1 ELSE 0 END) as excused_count,
                COUNT(a.attendance_id) as total_days')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->join($this->table . ' a', 'a.student_id = st.student_id AND a.attendance_type = "Daily" AND a.academic_year_id = ' . (int)$year_id, 'left')
            ->where('st.status', 1)
            ->where('st.academic_year_id', $year_id)
            ->group_by('st.student_id')
            ->order_by('c.class_id', 'ASC')
            ->order_by('sec.section_id', 'ASC')
            ->order_by('CAST(st.roll_number AS UNSIGNED)', 'ASC');

        if (!empty($filters['class_id'])) $this->db->where('st.class_id', $filters['class_id']);
        if (!empty($filters['section_id'])) $this->db->where('st.section_id', $filters['section_id']);
        if (!empty($filters['student_id'])) $this->db->where('st.student_id', $filters['student_id']);
        if (!empty($filters['from_date'])) $this->db->where('a.attendance_date >=', $filters['from_date']);
        if (!empty($filters['to_date'])) $this->db->where('a.attendance_date <=', $filters['to_date']);

        $results = $this->db->get()->result();

        foreach ($results as $row) {
            $effective = (int)$row->total_days;
            $row->percentage = $effective > 0 ? round(((int)$row->present_count / $effective) * 100, 1) : 0;
        }

        return $results;
    }

    public function get_monthly_report($filters = array())
    {
        $year_id = !empty($filters['academic_year_id']) ? (int)$filters['academic_year_id'] : get_current_academic_year_id();
        $month = !empty($filters['month']) ? (int)$filters['month'] : (int)date('m');
        $year  = !empty($filters['year']) ? (int)$filters['year'] : (int)date('Y');
        $start_date = sprintf('%04d-%02d-01', $year, $month);
        $end_date   = date('Y-m-t', strtotime($start_date));

        $this->db
            ->select('st.student_id, st.admission_number, st.roll_number, st.first_name, st.last_name, c.class_name, sec.section_name,
                SUM(CASE WHEN a.attendance_status = "Present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.attendance_status = "Absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.attendance_status = "Late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN a.attendance_status IN ("Excused", "Leave") THEN 1 ELSE 0 END) as excused_count,
                COUNT(a.attendance_id) as total_days')
            ->from('tbl_students st')
            ->join('tbl_classes c', 'c.class_id = st.class_id', 'left')
            ->join('tbl_sections sec', 'sec.section_id = st.section_id', 'left')
            ->join($this->table . ' a', 'a.student_id = st.student_id AND a.attendance_type = "Daily" AND a.academic_year_id = ' . (int)$year_id . ' AND a.attendance_date >= ' . $this->db->escape($start_date) . ' AND a.attendance_date <= ' . $this->db->escape($end_date), 'left')
            ->where('st.status', 1)
            ->where('st.academic_year_id', $year_id)
            ->group_by('st.student_id')
            ->order_by('c.class_id', 'ASC')
            ->order_by('sec.section_id', 'ASC')
            ->order_by('CAST(st.roll_number AS UNSIGNED)', 'ASC');

        if (!empty($filters['class_id'])) $this->db->where('st.class_id', $filters['class_id']);
        if (!empty($filters['section_id'])) $this->db->where('st.section_id', $filters['section_id']);

        $results = $this->db->get()->result();

        foreach ($results as $row) {
            $effective = (int)$row->total_days;
            $row->percentage = $effective > 0 ? round(((int)$row->present_count / $effective) * 100, 1) : 0;
        }

        return $results;
    }

    public function get_period_wise_report($filters = array())
    {
        $year_id = !empty($filters['academic_year_id']) ? (int)$filters['academic_year_id'] : get_current_academic_year_id();

        $this->db
            ->select('p.period_id, p.period_number, p.period_name, p.start_time, p.end_time,
                SUM(CASE WHEN a.attendance_status = "Present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.attendance_status = "Absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.attendance_status = "Late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN a.attendance_status IN ("Excused", "Leave") THEN 1 ELSE 0 END) as excused_count,
                COUNT(a.attendance_id) as total_count')
            ->from('tbl_periods p')
            ->join($this->table . ' a', 'a.period_id = p.period_id AND a.attendance_type = "Period-wise" AND a.academic_year_id = ' . (int)$year_id, 'left')
            ->where('p.status', 1)
            ->group_by('p.period_id')
            ->order_by('p.period_number', 'ASC')
            ->order_by('p.start_time', 'ASC');

        if (!empty($filters['date'])) $this->db->where('a.attendance_date', $filters['date']);
        if (!empty($filters['from_date'])) $this->db->where('a.attendance_date >=', $filters['from_date']);
        if (!empty($filters['to_date'])) $this->db->where('a.attendance_date <=', $filters['to_date']);
        if (!empty($filters['class_id'])) $this->db->where('a.class_id', $filters['class_id']);
        if (!empty($filters['section_id'])) $this->db->where('a.section_id', $filters['section_id']);

        $results = $this->db->get()->result();

        foreach ($results as $row) {
            $effective = (int)$row->total_count;
            $row->percentage = $effective > 0 ? round(((int)$row->present_count / $effective) * 100, 1) : 0;
        }

        return $results;
    }

    /* =========================================================================
       7. Student Profile Attendance Tab Enhancement
       ========================================================================= */
    public function get_student_profile_attendance($student_id, $academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ?: get_current_academic_year_id();

        // 1. Overall Summary
        $this->db->where('student_id', $student_id)->where('attendance_type', 'Daily');
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $total = $this->db->count_all_results($this->table);

        $this->db->where('student_id', $student_id)->where('attendance_type', 'Daily')->where('attendance_status', 'Present');
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $present = $this->db->count_all_results($this->table);

        $this->db->where('student_id', $student_id)->where('attendance_type', 'Daily')->where('attendance_status', 'Absent');
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $absent = $this->db->count_all_results($this->table);

        $this->db->where('student_id', $student_id)->where('attendance_type', 'Daily')->where('attendance_status', 'Late');
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $late = $this->db->count_all_results($this->table);

        $this->db->where('student_id', $student_id)->where('attendance_type', 'Daily')->where_in('attendance_status', array('Excused', 'Leave'));
        if ($academic_year_id) $this->db->where('academic_year_id', $academic_year_id);
        $excused = $this->db->count_all_results($this->table);

        $pct = ($total > 0) ? round(($present / $total) * 100, 1) : 100;

        // 2. Month-wise Breakdown
        $months = $this->db
            ->select('DATE_FORMAT(attendance_date, "%Y-%m") as ym, DATE_FORMAT(attendance_date, "%M %Y") as month_name,
                SUM(CASE WHEN attendance_status = "Present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendance_status = "Absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN attendance_status = "Late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN attendance_status IN ("Excused", "Leave") THEN 1 ELSE 0 END) as excused_count,
                COUNT(attendance_id) as total_days')
            ->from($this->table)
            ->where('student_id', $student_id)
            ->where('attendance_type', 'Daily')
            ->group_by('ym')
            ->order_by('ym', 'DESC')
            ->get()
            ->result();

        foreach ($months as $m) {
            $tot = (int)$m->total_days;
            $m->percentage = ($tot > 0) ? round(((int)$m->present_count / $tot) * 100, 1) : 0;
        }

        // 3. Recent 30 Days Records
        $recent_records = $this->db
            ->where('student_id', $student_id)
            ->where('attendance_type', 'Daily')
            ->order_by('attendance_date', 'DESC')
            ->limit(30)
            ->get($this->table)
            ->result();

        return (object) array(
            'total_days'     => $total,
            'present'        => $present,
            'absent'         => $absent,
            'late'           => $late,
            'excused'        => $excused,
            'percentage'     => $pct,
            'monthly'        => $months,
            'recent_records' => $recent_records
        );
    }
}
