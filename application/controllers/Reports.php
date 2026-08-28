<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            'Student_model',
            'Staff_model',
            'Attendance_model',
            'Fee_model',
            'Exam_model',
            'Transport_assignment_model'
        ));
    }

    public function index()
    {
        $this->require_permission('reports.view');
        $today = date('Y-m-d');
        $year_id = $this->academic_year_id;

        $total_students = $this->db->where('academic_year_id', $year_id)->where('status >=', 0)->where('is_deleted', 'n')->count_all_results('tbl_students');
        $total_staff = $this->db->where('status >=', 0)->where('is_deleted', 'n')->count_all_results('tbl_staff');
        
        $att_stats = $this->Attendance_model->get_dashboard_stats($today, $year_id);
        $attendance_pct = is_object($att_stats) ? ($att_stats->percentage ?? 0) : (!empty($att_stats['percentage']) ? $att_stats['percentage'] : 0);
        
        $fee_metrics = $this->Fee_model->get_dashboard_metrics($year_id);
        $fee_collected = is_object($fee_metrics) ? ($fee_metrics->total_collected ?? 0) : (!empty($fee_metrics['total_collected']) ? $fee_metrics['total_collected'] : 0);
        $fee_pending = is_object($fee_metrics) ? ($fee_metrics->total_pending ?? 0) : (!empty($fee_metrics['total_pending']) ? $fee_metrics['total_pending'] : 0);
        
        $total_exams = $this->db->where('academic_year_id', $year_id)->where('is_deleted', 'n')->count_all_results('tbl_exams');
        $transport_users = $this->db->where('academic_year_id', $year_id)->where('status', 'Active')->count_all_results('tbl_student_transport_assignments');

        $stats = array(
            'total_students'  => $total_students,
            'total_staff'     => $total_staff,
            'attendance_pct'  => $attendance_pct,
            'fee_collected'   => $fee_collected,
            'fee_pending'     => $fee_pending,
            'total_exams'     => $total_exams,
            'transport_users' => $transport_users,
        );

        $this->render('pages/reports/index', array(
            'title'    => 'Reports Dashboard',
            'page_key' => 'reports',
            'stats'    => $stats,
        ));
    }
}
