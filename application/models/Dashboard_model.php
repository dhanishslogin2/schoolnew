<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard_model
 *
 * Consolidated dashboard queries for the main School dashboard.
 * Each method uses a single optimised SQL query rather than multiple
 * separate calls to keep the dashboard page load fast.
 */
class Dashboard_model extends CI_Model {

    // -------------------------------------------------------------------------
    // Summary Stats Card
    // -------------------------------------------------------------------------

    /**
     * Returns a single object with school-wide counts for the top stat cards.
     * One query covers students, staff, classes, and today's admissions.
     *
     * @param  int|null $academic_year_id  Filter to a specific year (optional)
     * @return object
     */
    public function get_summary_stats($academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        // -- Student counts (active, male, female, new this month) --
        $student_row = $this->db->query("
            SELECT
                COUNT(*)                                                      AS total_students,
                SUM(CASE WHEN s.status = 1 THEN 1 ELSE 0 END)                AS active_students,
                SUM(CASE WHEN s.gender = 'Male'   AND s.status = 1 THEN 1 ELSE 0 END) AS male_students,
                SUM(CASE WHEN s.gender = 'Female' AND s.status = 1 THEN 1 ELSE 0 END) AS female_students,
                SUM(CASE WHEN s.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 ELSE 0 END) AS new_admissions
            FROM tbl_students s
            WHERE s.is_deleted = 'n'
              AND s.academic_year_id = ?
        ", [$academic_year_id])->row();

        // -- Staff count (global master data) --
        $staff_row = $this->db->query("
            SELECT
                COUNT(*)                                                          AS total_staff,
                SUM(CASE WHEN s.staff_type = 'Teacher' THEN 1 ELSE 0 END)        AS total_teachers
            FROM tbl_staff s
            WHERE s.is_deleted = 'n'
              AND s.status = 1
        ")->row();

        // -- Class count for current academic year --
        $class_count = (int)$this->db->query("
            SELECT COUNT(*) AS cnt FROM tbl_classes WHERE is_deleted = 'n' AND status = 1 AND academic_year_id = ?
        ", [$academic_year_id])->row()->cnt;

        return (object)[
            'total_students'   => (int)($student_row->total_students   ?? 0),
            'active_students'  => (int)($student_row->active_students  ?? 0),
            'male_students'    => (int)($student_row->male_students    ?? 0),
            'female_students'  => (int)($student_row->female_students  ?? 0),
            'new_admissions'   => (int)($student_row->new_admissions   ?? 0),
            'total_staff'      => (int)($staff_row->total_staff        ?? 0),
            'total_teachers'   => (int)($staff_row->total_teachers     ?? 0),
            'total_classes'    => $class_count,
        ];
    }

    // -------------------------------------------------------------------------
    // Today's Attendance Summary
    // -------------------------------------------------------------------------

    /**
     * Returns present / absent / late counts for today (or a given date).
     *
     * @param  string   $date  Y-m-d  (defaults to today)
     * @param  int|null $academic_year_id
     * @return object
     */
    public function get_today_attendance($date = NULL, $academic_year_id = NULL)
    {
        $date = $date ?: date('Y-m-d');
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        $row = $this->db->query("
            SELECT
                SUM(CASE WHEN attendance_status = 'Present'  AND is_deleted = 'n' THEN 1 ELSE 0 END) AS present,
                SUM(CASE WHEN attendance_status = 'Absent'   AND is_deleted = 'n' THEN 1 ELSE 0 END) AS absent,
                SUM(CASE WHEN attendance_status = 'Late'     AND is_deleted = 'n' THEN 1 ELSE 0 END) AS late,
                SUM(CASE WHEN attendance_status = 'Excused'  AND is_deleted = 'n' THEN 1 ELSE 0 END) AS excused,
                COUNT(CASE WHEN is_deleted = 'n' THEN 1 END)                                         AS total_marked
            FROM tbl_attendance
            WHERE attendance_date = ?
              AND attendance_type = 'Daily'
              AND academic_year_id = ?
        ", [$date, $academic_year_id])->row();

        $present = (int)($row->present ?? 0);
        $absent  = (int)($row->absent  ?? 0);
        $late    = (int)($row->late    ?? 0);
        $total   = (int)($row->total_marked ?? 0);

        $pct = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        $absent_pct  = $total > 0 ? round(($absent  / $total) * 100, 1) : 0;
        $late_pct    = $total > 0 ? round(($late    / $total) * 100, 1) : 0;

        return (object)[
            'present'      => $present,
            'absent'       => $absent,
            'late'         => $late,
            'total_marked' => $total,
            'pct'          => $pct,
            'absent_pct'   => $absent_pct,
            'late_pct'     => $late_pct,
            'date'         => $date,
        ];
    }

    // -------------------------------------------------------------------------
    // Fees Summary
    // -------------------------------------------------------------------------

    /**
     * Returns today's collection, monthly collection, total pending, and overdue fees.
     * Filtered by active academic year.
     *
     * @param  int|null $academic_year_id
     * @return object
     */
    public function get_fees_summary($academic_year_id = NULL)
    {
        $today            = date('Y-m-d');
        $month_start      = date('Y-m-01');
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        $row = $this->db->query("
            SELECT
                COALESCE(SUM(CASE WHEN p.payment_date = ? AND p.is_deleted='n' THEN p.amount_paid ELSE 0 END), 0)  AS today_collection,
                COALESCE(SUM(CASE WHEN p.payment_date >= ? AND p.is_deleted='n' THEN p.amount_paid ELSE 0 END), 0) AS monthly_collection
            FROM tbl_fee_payments p
            JOIN tbl_student_fees sf ON sf.student_fee_id = p.student_fee_id AND sf.is_deleted = 'n'
            WHERE sf.academic_year_id = ?
        ", [$today, $month_start, $academic_year_id])->row();

        // Pending = student_fees where status != 'paid' and is_deleted='n' for this academic year
        $pending_row = $this->db->query("
            SELECT
                COALESCE(SUM(CASE WHEN payment_status IN ('Pending','Partially Paid','Overdue') THEN (final_amount - paid_amount) ELSE 0 END), 0) AS total_pending,
                COALESCE(SUM(CASE WHEN payment_status = 'Overdue' THEN (final_amount - paid_amount) ELSE 0 END), 0) AS overdue_amount
            FROM tbl_student_fees
            WHERE is_deleted = 'n'
              AND academic_year_id = ?
        ", [$academic_year_id])->row();

        return (object)[
            'today_collection'   => (float)($row->today_collection          ?? 0),
            'monthly_collection' => (float)($row->monthly_collection        ?? 0),
            'total_pending'      => (float)($pending_row->total_pending     ?? 0),
            'overdue_amount'     => (float)($pending_row->overdue_amount    ?? 0),
        ];
    }

    // -------------------------------------------------------------------------
    // Upcoming Events
    // -------------------------------------------------------------------------

    /**
     * Returns upcoming events on or after today, ordered by date ASC.
     *
     * @param  int $limit
     * @return array
     */
    public function get_upcoming_events($limit = 5)
    {
        return $this->db->query("
            SELECT event_id, title, event_date, audience, venue
            FROM tbl_events
            WHERE event_date >= CURDATE()
              AND is_deleted = 'n'
              AND status = 1
            ORDER BY event_date ASC
            LIMIT ?
        ", [(int)$limit])->result();
    }

    // -------------------------------------------------------------------------
    // Recent Notices
    // -------------------------------------------------------------------------

    /**
     * Returns recently published notices, ordered by publish_date DESC.
     *
     * @param  int $limit
     * @param  int|null $academic_year_id
     * @return array
     */
    public function get_recent_notices($limit = 5, $academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        return $this->db->query("
            SELECT notice_id, title, posted_by, publish_date, category, priority
            FROM tbl_notices
            WHERE status = 'Published'
              AND is_deleted = 'n'
              AND (academic_year_id = ? OR academic_year_id IS NULL)
            ORDER BY publish_date DESC, created_at DESC
            LIMIT ?
        ", [$academic_year_id, (int)$limit])->result();
    }

    // -------------------------------------------------------------------------
    // Students by Class (for the mini class breakdown)
    // -------------------------------------------------------------------------

    /**
     * Returns student count per class for the dashboard overview widget.
     *
     * @param  int|null $academic_year_id
     * @return array  Each row: class_name, student_count
     */
    public function get_students_by_class($academic_year_id = NULL)
    {
        $academic_year_id = $academic_year_id ? (int)$academic_year_id : get_current_academic_year_id();

        return $this->db->query("
            SELECT c.class_name,
                   COUNT(s.student_id) AS student_count
            FROM tbl_classes c
            LEFT JOIN tbl_students s
                   ON s.class_id = c.class_id
                  AND s.is_deleted = 'n'
                  AND s.status = 1
                  AND s.academic_year_id = ?
            WHERE c.is_deleted = 'n'
              AND c.status = 1
              AND c.academic_year_id = ?
            GROUP BY c.class_id, c.class_name
            ORDER BY c.class_name ASC
        ", [$academic_year_id, $academic_year_id])->result();
    }
}
