<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_model');
        $this->load->model('Period_model');
        $this->load->model('Attendance_notification_model');
        $this->load->model('Attendance_setting_model');
        $this->load->model('Class_model');
        $this->load->model('Section_model');
        $this->load->model('Student_model');
        $this->load->model('Academic_year_model');
        $this->load->model('Class_teacher_model');
    }

    // Permission keys: attendance.view, attendance.mark

    /* =========================================================================
       1. Attendance Dashboard
       ========================================================================= */
    public function index()
    {
        $date       = $this->input->get('date') ?: date('Y-m-d');
        $class_id   = $this->input->get('class_id') ?: NULL;
        $section_id = $this->input->get('section_id') ?: NULL;
        $year_id    = $this->input->get('academic_year_id') ?: NULL;

        if (!$year_id) {
            $active_year = $this->Academic_year_model->get_active();
            $year_id = $active_year ? $active_year->academic_year_id : 1;
        }

        $stats           = $this->Attendance_model->get_dashboard_stats($date, $class_id, $section_id, $year_id);
        $class_overview  = $this->Attendance_model->get_class_overview($date, $year_id, $class_id, $section_id);
        $recent_activity = $this->Attendance_model->get_recent_activity(8);
        $classes         = $this->Class_model->get_all();
        $sections        = $class_id ? $this->Section_model->get_by_class($class_id) : $this->Section_model->get_all();
        $years           = $this->Academic_year_model->get_all();

        $this->render('pages/attendance/dashboard', array(
            'title'           => 'Attendance Dashboard',
            'page_key'        => 'attendance-dashboard',
            'breadcrumb'      => array('Attendance', 'Attendance Dashboard'),
            'stats'           => $stats,
            'class_overview'  => $class_overview,
            'recent_activity' => $recent_activity,
            'classes'         => $classes,
            'sections'        => $sections,
            'years'           => $years,
            'date'            => $date,
            'class_id'        => $class_id,
            'section_id'      => $section_id,
            'year_id'         => $year_id,
        ));
    }

    /* =========================================================================
       2. Daily Attendance
       ========================================================================= */
    public function daily()
    {
        $date       = $this->input->get('date') ?: date('Y-m-d');
        $class_id   = $this->input->get('class_id') ?: NULL;
        $section_id = $this->input->get('section_id') ?: NULL;
        $year_id    = $this->input->get('academic_year_id') ?: NULL;

        if (!$year_id) {
            $active_year = $this->Academic_year_model->get_active();
            $year_id = $active_year ? $active_year->academic_year_id : 1;
        }

        if ($this->input->method() === 'post') {
            $this->require_permission('attendance.mark');

            $post_attendance = $this->input->post('attendance'); // student_id => ['status' => ..., 'remarks' => ...]
            $post_date       = $this->input->post('date') ?: $date;
            $post_class_id   = $this->input->post('class_id') ?: $class_id;
            $post_section_id = $this->input->post('section_id') ?: $section_id;
            $post_year_id    = $this->input->post('academic_year_id') ?: $year_id;
            $user_id         = $this->session->userdata('user_id');

            if (is_array($post_attendance) && !empty($post_attendance)) {
                $saved = $this->Attendance_model->save_daily_attendance(
                    $post_attendance,
                    $post_date,
                    $post_year_id,
                    $post_class_id ?: 1,
                    $post_section_id ?: 1,
                    $user_id
                );
                $this->session->set_flashdata('success', "Daily attendance saved successfully for {$saved} student(s) on " . date('d M Y', strtotime($post_date)) . '.');
            } else {
                $this->session->set_flashdata('error', 'No student attendance records were submitted.');
            }

            $redirect_url = 'attendance/daily?date=' . $post_date;
            if ($post_class_id) $redirect_url .= '&class_id=' . $post_class_id;
            if ($post_section_id) $redirect_url .= '&section_id=' . $post_section_id;
            if ($post_year_id) $redirect_url .= '&academic_year_id=' . $post_year_id;

            redirect($redirect_url);
            return;
        }

        $students = array();
        $is_already_marked = FALSE;

        if ($class_id && $section_id) {
            $students = $this->Attendance_model->get_daily_sheet($date, $class_id, $section_id, $year_id);
            $is_already_marked = $this->Attendance_model->check_daily_marked($date, $class_id, $section_id, $year_id);
        }

        $classes  = $this->Class_model->get_all();
        $sections = $class_id ? $this->Section_model->get_by_class($class_id) : $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();
        $settings = $this->Attendance_setting_model->get_settings();

        $this->render('pages/attendance/daily', array(
            'title'              => 'Daily Attendance',
            'page_key'           => 'attendance-daily',
            'breadcrumb'         => array('Attendance', 'Daily Attendance'),
            'students'           => $students,
            'classes'            => $classes,
            'sections'           => $sections,
            'years'              => $years,
            'date'               => $date,
            'class_id'           => $class_id,
            'section_id'         => $section_id,
            'year_id'            => $year_id,
            'is_already_marked'  => $is_already_marked,
            'settings'           => $settings,
        ));
    }

    /* =========================================================================
       3. Period Management
       ========================================================================= */
    public function periods($action = NULL, $id = NULL)
    {
        if ($this->input->method() === 'post') {
            $this->require_permission('attendance.mark');
            $post_action = $this->input->post('action') ?: $action;

            if ($post_action === 'add') {
                $period_name   = trim($this->input->post('period_name', TRUE));
                $period_number = (int)$this->input->post('period_number');
                $start_time    = trim($this->input->post('start_time', TRUE));
                $end_time      = trim($this->input->post('end_time', TRUE));
                $status        = (int)$this->input->post('status');

                // Validation
                if (empty($period_name) || empty($period_number) || empty($start_time) || empty($end_time)) {
                    $this->session->set_flashdata('error', 'All fields (Period Name, Period Number, Start Time, End Time) are required.');
                    redirect('attendance/periods');
                    return;
                }

                if (strtotime($end_time) <= strtotime($start_time)) {
                    $this->session->set_flashdata('error', 'End Time must be later than Start Time.');
                    redirect('attendance/periods');
                    return;
                }

                if ($this->Period_model->check_number_exists($period_number)) {
                    $this->session->set_flashdata('error', "Period number {$period_number} already exists. Period numbers must be unique.");
                    redirect('attendance/periods');
                    return;
                }

                $overlap = $this->Period_model->check_overlap($start_time, $end_time);
                if ($overlap) {
                    $this->session->set_flashdata('error', "Time range {$start_time} - {$end_time} overlaps with {$overlap->period_name} ({$overlap->start_time} - {$overlap->end_time}).");
                    redirect('attendance/periods');
                    return;
                }

                $this->Period_model->insert(array(
                    'period_name'   => $period_name,
                    'period_number' => $period_number,
                    'start_time'    => $start_time,
                    'end_time'      => $end_time,
                    'period_order'  => $period_number,
                    'status'        => $status,
                ));

                $this->session->set_flashdata('success', "Period '{$period_name}' created successfully.");
                redirect('attendance/periods');
                return;
            }

            if ($post_action === 'edit') {
                $period_id     = (int)$this->input->post('period_id');
                $period_name   = trim($this->input->post('period_name', TRUE));
                $period_number = (int)$this->input->post('period_number');
                $start_time    = trim($this->input->post('start_time', TRUE));
                $end_time      = trim($this->input->post('end_time', TRUE));
                $status        = (int)$this->input->post('status');

                if (empty($period_name) || empty($period_number) || empty($start_time) || empty($end_time)) {
                    $this->session->set_flashdata('error', 'All fields are required.');
                    redirect('attendance/periods');
                    return;
                }

                if (strtotime($end_time) <= strtotime($start_time)) {
                    $this->session->set_flashdata('error', 'End Time must be later than Start Time.');
                    redirect('attendance/periods');
                    return;
                }

                if ($this->Period_model->check_number_exists($period_number, $period_id)) {
                    $this->session->set_flashdata('error', "Period number {$period_number} is already in use by another period.");
                    redirect('attendance/periods');
                    return;
                }

                $overlap = $this->Period_model->check_overlap($start_time, $end_time, $period_id);
                if ($overlap) {
                    $this->session->set_flashdata('error', "Time range {$start_time} - {$end_time} overlaps with {$overlap->period_name} ({$overlap->start_time} - {$overlap->end_time}).");
                    redirect('attendance/periods');
                    return;
                }

                $this->Period_model->update($period_id, array(
                    'period_name'   => $period_name,
                    'period_number' => $period_number,
                    'start_time'    => $start_time,
                    'end_time'      => $end_time,
                    'period_order'  => $period_number,
                    'status'        => $status,
                ));

                $this->session->set_flashdata('success', "Period '{$period_name}' updated successfully.");
                redirect('attendance/periods');
                return;
            }

            if ($post_action === 'toggle_status') {
                $period_id = (int)$this->input->post('period_id');
                $this->Period_model->toggle_status($period_id);
                $this->session->set_flashdata('success', 'Period status updated.');
                redirect('attendance/periods');
                return;
            }

            if ($post_action === 'delete') {
                $period_id = (int)$this->input->post('period_id');
                $this->Period_model->delete($period_id);
                $this->session->set_flashdata('success', 'Period deleted successfully.');
                redirect('attendance/periods');
                return;
            }
        }

        $periods = $this->Period_model->get_all(FALSE);

        $this->render('pages/attendance/periods', array(
            'title'      => 'Period Management',
            'page_key'   => 'attendance-periods',
            'breadcrumb' => array('Attendance', 'Period Management'),
            'periods'    => $periods,
        ));
    }

    /* =========================================================================
       4. Period-wise Attendance
       ========================================================================= */
    public function period_wise()
    {
        $date       = $this->input->get('date') ?: date('Y-m-d');
        $class_id   = $this->input->get('class_id') ?: NULL;
        $section_id = $this->input->get('section_id') ?: NULL;
        $period_id  = $this->input->get('period_id') ?: NULL;
        $year_id    = $this->input->get('academic_year_id') ?: NULL;

        if (!$year_id) {
            $active_year = $this->Academic_year_model->get_active();
            $year_id = $active_year ? $active_year->academic_year_id : 1;
        }

        if ($this->input->method() === 'post') {
            $this->require_permission('attendance.mark');

            $post_attendance = $this->input->post('attendance');
            $post_date       = $this->input->post('date') ?: $date;
            $post_period_id  = $this->input->post('period_id') ?: $period_id;
            $post_class_id   = $this->input->post('class_id') ?: $class_id;
            $post_section_id = $this->input->post('section_id') ?: $section_id;
            $post_year_id    = $this->input->post('academic_year_id') ?: $year_id;
            $user_id         = $this->session->userdata('user_id');

            if (is_array($post_attendance) && !empty($post_attendance) && $post_period_id) {
                $saved = $this->Attendance_model->save_period_attendance(
                    $post_attendance,
                    $post_date,
                    $post_period_id,
                    $post_year_id,
                    $post_class_id ?: 1,
                    $post_section_id ?: 1,
                    $user_id
                );
                $this->session->set_flashdata('success', "Period attendance saved for {$saved} student(s) on " . date('d M Y', strtotime($post_date)) . '.');
            } else {
                $this->session->set_flashdata('error', 'Please select a period and submit attendance records.');
            }

            $redirect_url = "attendance/period_wise?date={$post_date}&period_id={$post_period_id}";
            if ($post_class_id) $redirect_url .= "&class_id={$post_class_id}";
            if ($post_section_id) $redirect_url .= "&section_id={$post_section_id}";
            if ($post_year_id) $redirect_url .= "&academic_year_id={$post_year_id}";

            redirect($redirect_url);
            return;
        }

        $students = array();
        $is_already_marked = FALSE;

        if ($class_id && $section_id && $period_id) {
            $students = $this->Attendance_model->get_period_sheet($date, $period_id, $class_id, $section_id, $year_id);
            $is_already_marked = $this->Attendance_model->check_period_marked($date, $period_id, $class_id, $section_id, $year_id);
        }

        $classes  = $this->Class_model->get_all();
        $sections = $class_id ? $this->Section_model->get_by_class($class_id) : $this->Section_model->get_all();
        $periods  = $this->Period_model->get_all(TRUE);
        $years    = $this->Academic_year_model->get_all();
        $settings = $this->Attendance_setting_model->get_settings();

        $this->render('pages/attendance/period_wise', array(
            'title'             => 'Period-wise Attendance',
            'page_key'          => 'attendance-period-wise',
            'breadcrumb'        => array('Attendance', 'Period-wise Attendance'),
            'students'          => $students,
            'classes'           => $classes,
            'sections'          => $sections,
            'periods'           => $periods,
            'years'             => $years,
            'date'              => $date,
            'class_id'          => $class_id,
            'section_id'        => $section_id,
            'period_id'         => $period_id,
            'year_id'           => $year_id,
            'is_already_marked' => $is_already_marked,
            'settings'          => $settings,
        ));
    }

    /* =========================================================================
       5. Class Attendance
       ========================================================================= */
    public function class_attendance()
    {
        $date     = $this->input->get('date') ?: date('Y-m-d');
        $class_id = $this->input->get('class_id') ?: 1;
        $year_id  = $this->input->get('academic_year_id') ?: NULL;

        if (!$year_id) {
            $active_year = $this->Academic_year_model->get_active();
            $year_id = $active_year ? $active_year->academic_year_id : 1;
        }

        $classes   = $this->Class_model->get_all();
        $years     = $this->Academic_year_model->get_all();
        $sections_overview = $this->Attendance_model->get_class_overview($date, $year_id, $class_id);

        $selected_class = $this->Class_model->get_by_id($class_id);

        $this->render('pages/attendance/class_attendance', array(
            'title'             => 'Class Attendance',
            'page_key'          => 'attendance-class',
            'breadcrumb'        => array('Attendance', 'Class Attendance'),
            'classes'           => $classes,
            'years'             => $years,
            'sections_overview' => $sections_overview,
            'selected_class'    => $selected_class,
            'class_id'          => $class_id,
            'year_id'           => $year_id,
            'date'              => $date,
        ));
    }

    /* =========================================================================
       6. Section Attendance
       ========================================================================= */
    public function section_attendance()
    {
        $date       = $this->input->get('date') ?: date('Y-m-d');
        $class_id   = $this->input->get('class_id') ?: 1;
        $section_id = $this->input->get('section_id') ?: 1;
        $year_id    = $this->input->get('academic_year_id') ?: NULL;

        if (!$year_id) {
            $active_year = $this->Academic_year_model->get_active();
            $year_id = $active_year ? $active_year->academic_year_id : 1;
        }

        $students = $this->Attendance_model->get_daily_sheet($date, $class_id, $section_id, $year_id);
        $classes  = $this->Class_model->get_all();
        $sections = $class_id ? $this->Section_model->get_by_class($class_id) : $this->Section_model->get_all();
        $years    = $this->Academic_year_model->get_all();
        $stats    = $this->Attendance_model->get_dashboard_stats($date, $class_id, $section_id, $year_id);

        $this->render('pages/attendance/section_attendance', array(
            'title'      => 'Section Attendance',
            'page_key'   => 'attendance-section',
            'breadcrumb' => array('Attendance', 'Section Attendance'),
            'students'   => $students,
            'classes'    => $classes,
            'sections'   => $sections,
            'years'      => $years,
            'stats'      => $stats,
            'date'       => $date,
            'class_id'   => $class_id,
            'section_id' => $section_id,
            'year_id'    => $year_id,
        ));
    }

    /* =========================================================================
       7. Attendance History
       ========================================================================= */
    public function history()
    {
        if ($this->input->method() === 'post') {
            $this->require_permission('attendance.mark');
            $action = $this->input->post('action');
            if ($action === 'edit_record') {
                $att_id  = (int)$this->input->post('attendance_id');
                $status  = $this->input->post('attendance_status');
                $remarks = $this->input->post('remarks');
                $user_id = $this->session->userdata('user_id');

                $this->db->where('attendance_id', $att_id)->update('tbl_attendance', array(
                    'attendance_status' => $status,
                    'remarks'           => $remarks,
                    'marked_by'         => $user_id,
                    'updated_at'        => date('Y-m-d H:i:s')
                ));

                $this->session->set_flashdata('success', 'Attendance record updated successfully.');
                redirect('attendance/history?' . $_SERVER['QUERY_STRING']);
                return;
            }
        }

        $filters = array(
            'academic_year_id'  => $this->input->get('academic_year_id') ?: NULL,
            'class_id'          => $this->input->get('class_id') ?: NULL,
            'section_id'        => $this->input->get('section_id') ?: NULL,
            'student_id'        => $this->input->get('student_id') ?: NULL,
            'attendance_type'   => $this->input->get('attendance_type') ?: NULL,
            'attendance_status' => $this->input->get('attendance_status') ?: NULL,
            'from_date'         => $this->input->get('from_date') ?: date('Y-m-01'),
            'to_date'           => $this->input->get('to_date') ?: date('Y-m-d'),
            'search'            => $this->input->get('search') ?: NULL,
        );

        $limit  = 50;
        $page   = (int)($this->input->get('page') ?: 1);
        $offset = ($page - 1) * $limit;

        $records     = $this->Attendance_model->get_history($filters, $limit, $offset);
        $total_count = $this->Attendance_model->count_history($filters);
        $classes     = $this->Class_model->get_all();
        $sections    = $filters['class_id'] ? $this->Section_model->get_by_class($filters['class_id']) : $this->Section_model->get_all();
        $years       = $this->Academic_year_model->get_all();

        $this->render('pages/attendance/history', array(
            'title'       => 'Attendance History',
            'page_key'    => 'attendance-history',
            'breadcrumb'  => array('Attendance', 'Attendance History'),
            'records'     => $records,
            'total_count' => $total_count,
            'filters'     => $filters,
            'classes'     => $classes,
            'sections'    => $sections,
            'years'       => $years,
            'page'        => $page,
            'limit'       => $limit,
        ));
    }

    /* =========================================================================
       8. Absent / Late / Excused Tracking
       ========================================================================= */
    public function tracking()
    {
        $filters = array(
            'status_filter' => $this->input->get('status') ?: 'All',
            'class_id'      => $this->input->get('class_id') ?: NULL,
            'section_id'    => $this->input->get('section_id') ?: NULL,
            'student_id'    => $this->input->get('student_id') ?: NULL,
            'from_date'     => $this->input->get('from_date') ?: date('Y-m-01'),
            'to_date'       => $this->input->get('to_date') ?: date('Y-m-d'),
        );

        $records  = $this->Attendance_model->get_tracking_records($filters);
        $classes  = $this->Class_model->get_all();
        $sections = $filters['class_id'] ? $this->Section_model->get_by_class($filters['class_id']) : $this->Section_model->get_all();

        $this->render('pages/attendance/tracking', array(
            'title'      => 'Absent / Late / Excused Tracking',
            'page_key'   => 'attendance-tracking',
            'breadcrumb' => array('Attendance', 'Absent / Late Tracking'),
            'records'    => $records,
            'filters'    => $filters,
            'classes'    => $classes,
            'sections'   => $sections,
        ));
    }

    /* =========================================================================
       9. Attendance Calendar
       ========================================================================= */
    public function calendar()
    {
        $month      = (int)($this->input->get('month') ?: date('n'));
        $year       = (int)($this->input->get('year') ?: date('Y'));
        $class_id   = $this->input->get('class_id') ?: NULL;
        $section_id = $this->input->get('section_id') ?: NULL;
        $student_id = $this->input->get('student_id') ?: NULL;
        $type       = $this->input->get('attendance_type') ?: 'Daily';

        $matrix   = $this->Attendance_model->get_calendar_data($year, $month, $class_id, $section_id, $student_id, $type);
        $classes  = $this->Class_model->get_all();
        $sections = $class_id ? $this->Section_model->get_by_class($class_id) : $this->Section_model->get_all();
        $students = ($class_id && $section_id) ? $this->Student_model->get_by_section($section_id) : array();

        $selected_date = $this->input->get('date') ?: NULL;
        $day_details = array();
        if ($selected_date) {
            $day_details = $this->Attendance_model->get_date_attendance_details($selected_date, $class_id, $section_id, $student_id, $type);
        }

        $this->render('pages/attendance/calendar', array(
            'title'         => 'Attendance Calendar',
            'page_key'      => 'attendance-calendar',
            'breadcrumb'    => array('Attendance', 'Attendance Calendar'),
            'matrix'        => $matrix,
            'month'         => $month,
            'year'          => $year,
            'class_id'      => $class_id,
            'section_id'    => $section_id,
            'student_id'    => $student_id,
            'type'          => $type,
            'classes'       => $classes,
            'sections'      => $sections,
            'students'      => $students,
            'selected_date' => $selected_date,
            'day_details'   => $day_details,
        ));
    }

    /* =========================================================================
       10. Attendance Reports
       ========================================================================= */
    public function reports()
    {
        $report_type = $this->input->get('type') ?: 'class_summary'; // class_summary, daily, student, section, monthly, period
        $class_id    = $this->input->get('class_id') ?: NULL;
        $section_id  = $this->input->get('section_id') ?: NULL;
        $student_id  = $this->input->get('student_id') ?: NULL;
        $date        = $this->input->get('date') ?: date('Y-m-d');
        $from_date   = $this->input->get('from_date') ?: date('Y-m-01');
        $to_date     = $this->input->get('to_date') ?: date('Y-m-d');
        $month       = (int)($this->input->get('month') ?: date('n'));
        $year        = (int)($this->input->get('year') ?: date('Y'));

        $filters = array(
            'class_id'   => $class_id,
            'section_id' => $section_id,
            'student_id' => $student_id,
            'date'       => $date,
            'from_date'  => $from_date,
            'to_date'    => $to_date,
            'month'      => $month,
            'year'       => $year,
        );

        $data_results = array();

        if ($report_type === 'daily') {
            $data_results = $this->Attendance_model->get_daily_sheet($date, $class_id, $section_id);
        } elseif ($report_type === 'student') {
            $data_results = $this->Attendance_model->get_student_report($filters);
        } elseif ($report_type === 'section') {
            $data_results = $this->Attendance_model->get_reports_summary(NULL, $class_id);
        } elseif ($report_type === 'monthly') {
            $data_results = $this->Attendance_model->get_monthly_report($filters);
        } elseif ($report_type === 'period') {
            $data_results = $this->Attendance_model->get_period_wise_report($filters);
        } else {
            // Default class overview
            $data_results = $this->Attendance_model->get_reports_summary(NULL, $class_id);
        }

        // Export CSV if requested
        if ($this->input->get('export') === 'csv') {
            $this->_export_reports_csv($report_type, $data_results);
            return;
        }

        $classes  = $this->Class_model->get_all();
        $sections = $class_id ? $this->Section_model->get_by_class($class_id) : $this->Section_model->get_all();
        $students = ($class_id && $section_id) ? $this->Student_model->get_by_section($section_id) : array();

        $this->render('pages/attendance/reports', array(
            'title'        => 'Attendance Reports',
            'page_key'     => 'attendance-reports',
            'breadcrumb'   => array('Attendance', 'Attendance Reports'),
            'report_type'  => $report_type,
            'reports'      => $data_results,
            'classes'      => $classes,
            'sections'     => $sections,
            'students'     => $students,
            'filters'      => $filters,
        ));
    }

    private function _export_reports_csv($report_type, $data)
    {
        $filename = 'Attendance_Report_' . $report_type . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        $output = fopen('php://output', 'w');

        if ($report_type === 'student' || $report_type === 'monthly') {
            fputcsv($output, array('Admission No', 'Roll No', 'Student Name', 'Class', 'Section', 'Present Days', 'Absent Days', 'Late Days', 'Excused Days', 'Total Days', 'Attendance %'));
            foreach ($data as $r) {
                fputcsv($output, array(
                    $r->admission_number,
                    $r->roll_number,
                    $r->first_name . ' ' . $r->last_name,
                    $r->class_name,
                    $r->section_name,
                    $r->present_count ?: 0,
                    $r->absent_count ?: 0,
                    $r->late_count ?: 0,
                    $r->excused_count ?: 0,
                    $r->total_days ?: 0,
                    $r->percentage . '%'
                ));
            }
        } elseif ($report_type === 'period') {
            fputcsv($output, array('Period #', 'Period Name', 'Time', 'Present Count', 'Absent Count', 'Late Count', 'Excused Count', 'Total', 'Attendance %'));
            foreach ($data as $r) {
                fputcsv($output, array(
                    $r->period_number,
                    $r->period_name,
                    $r->start_time . ' - ' . $r->end_time,
                    $r->present_count ?: 0,
                    $r->absent_count ?: 0,
                    $r->late_count ?: 0,
                    $r->excused_count ?: 0,
                    $r->total_count ?: 0,
                    $r->percentage . '%'
                ));
            }
        } else {
            fputcsv($output, array('Class', 'Section', 'Present', 'Absent', 'Late', 'Excused', 'Attendance %'));
            foreach ($data as $r) {
                fputcsv($output, array(
                    isset($r->class_name) ? $r->class_name : '',
                    isset($r->section_name) ? $r->section_name : '',
                    isset($r->present_count) ? $r->present_count : 0,
                    isset($r->absent_count) ? $r->absent_count : 0,
                    isset($r->late_count) ? $r->late_count : 0,
                    isset($r->excused_count) ? $r->excused_count : 0,
                    (isset($r->percentage) ? $r->percentage : 0) . '%'
                ));
            }
        }
        fclose($output);
        exit;
    }

    /* =========================================================================
       11. Parent Notification Management
       ========================================================================= */
    public function notifications()
    {
        if ($this->input->method() === 'post') {
            $this->require_permission('attendance.mark');
            $action = $this->input->post('action');
            if ($action === 'update_status') {
                $id     = (int)$this->input->post('notification_id');
                $status = $this->input->post('status');
                $this->Attendance_notification_model->update_status($id, $status);
                $this->session->set_flashdata('success', 'Notification status updated.');
                redirect('attendance/notifications');
                return;
            }
            if ($action === 'delete') {
                $id = (int)$this->input->post('notification_id');
                $this->Attendance_notification_model->delete($id);
                $this->session->set_flashdata('success', 'Notification removed.');
                redirect('attendance/notifications');
                return;
            }
        }

        $filters = array(
            'status'            => $this->input->get('status') ?: 'Pending',
            'notification_type' => $this->input->get('type') ?: NULL,
            'class_id'          => $this->input->get('class_id') ?: NULL,
            'date'              => $this->input->get('date') ?: NULL,
            'search'            => $this->input->get('search') ?: NULL,
        );

        $notifications = $this->Attendance_notification_model->get_all($filters, 100);
        $classes       = $this->Class_model->get_all();

        $this->render('pages/attendance/notifications', array(
            'title'         => 'Parent Notifications',
            'page_key'      => 'attendance-notifications',
            'breadcrumb'    => array('Attendance', 'Parent Notifications'),
            'notifications' => $notifications,
            'classes'       => $classes,
            'filters'       => $filters,
        ));
    }

    /* =========================================================================
       12. Notification History
       ========================================================================= */
    public function notification_history()
    {
        $filters = array(
            'status'            => $this->input->get('status') ?: NULL,
            'notification_type' => $this->input->get('type') ?: NULL,
            'class_id'          => $this->input->get('class_id') ?: NULL,
            'from_date'         => $this->input->get('from_date') ?: date('Y-m-01'),
            'to_date'           => $this->input->get('to_date') ?: date('Y-m-d'),
            'search'            => $this->input->get('search') ?: NULL,
        );

        $notifications = $this->Attendance_notification_model->get_all($filters, 200);
        $classes       = $this->Class_model->get_all();

        $this->render('pages/attendance/notification_history', array(
            'title'         => 'Notification History',
            'page_key'      => 'attendance-notification-history',
            'breadcrumb'    => array('Attendance', 'Notification History'),
            'notifications' => $notifications,
            'classes'       => $classes,
            'filters'       => $filters,
        ));
    }

    /* =========================================================================
       13. Attendance Settings
       ========================================================================= */
    public function settings()
    {
        $this->require_permission('attendance.edit');

        if ($this->input->method() === 'post') {
            $data = array(
                'enable_present'              => $this->input->post('enable_present') ? 1 : 0,
                'enable_absent'               => $this->input->post('enable_absent') ? 1 : 0,
                'enable_late'                 => $this->input->post('enable_late') ? 1 : 0,
                'enable_excused'              => $this->input->post('enable_excused') ? 1 : 0,
                'enable_period_attendance'    => $this->input->post('enable_period_attendance') ? 1 : 0,
                'enable_absent_notification'  => $this->input->post('enable_absent_notification') ? 1 : 0,
                'enable_late_notification'    => $this->input->post('enable_late_notification') ? 1 : 0,
                'enable_summary_notification' => $this->input->post('enable_summary_notification') ? 1 : 0,
                'absent_template'             => trim($this->input->post('absent_template')),
                'late_template'               => trim($this->input->post('late_template')),
                'excused_template'            => trim($this->input->post('excused_template')),
                'summary_template'            => trim($this->input->post('summary_template')),
                'notification_timing'         => $this->input->post('notification_timing') ?: 'On Marking',
            );

            $this->Attendance_setting_model->update_settings($data);
            $this->session->set_flashdata('success', 'Attendance settings updated successfully.');
            redirect('attendance/settings');
            return;
        }

        $settings = $this->Attendance_setting_model->get_settings();
        $periods  = $this->Period_model->get_all(FALSE);

        $this->render('pages/attendance/settings', array(
            'title'      => 'Attendance Settings',
            'page_key'   => 'attendance-settings',
            'breadcrumb' => array('Attendance', 'Attendance Settings'),
            'settings'   => $settings,
            'periods'    => $periods,
        ));
    }
}
