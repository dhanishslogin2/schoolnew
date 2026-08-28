<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard
 *
 * Loads real-time school statistics from the database and renders
 * the main School dashboard.  All stat cards, attendance summary,
 * fees overview, events and notices are fully dynamic.
 */
class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
    }

    public function index()
    {
        $year_id = $this->academic_year_id;
        $today   = normalize_date_to_academic_year(date('Y-m-d'), $year_id);

        // All data is fetched with minimal DB round-trips via Dashboard_model
        $stats           = $this->Dashboard_model->get_summary_stats($year_id);
        $attendance      = $this->Dashboard_model->get_today_attendance($today, $year_id);
        $fees            = $this->Dashboard_model->get_fees_summary($year_id);
        $upcoming_events = $this->Dashboard_model->get_upcoming_events(5);
        $recent_notices  = $this->Dashboard_model->get_recent_notices(5, $year_id);
        $students_by_class = $this->Dashboard_model->get_students_by_class($year_id);

        $this->render('pages/dashboard', [
            'title'             => 'Dashboard',
            'page_key'          => 'dashboard',
            'stats'             => $stats,
            'attendance'        => $attendance,
            'fees'              => $fees,
            'upcoming_events'   => $upcoming_events,
            'recent_notices'    => $recent_notices,
            'students_by_class' => $students_by_class,
            'today'             => $today,
        ]);
    }
}
