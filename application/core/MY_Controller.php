<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller — Base controller for every EduCore page.
 *
 * Provides:
 *  - Authentication guard (redirects to login if no session)
 *  - RBAC permission enforcement via require_permission()
 *  - render() helper that loads header → page view → footer
 *  - Effective permissions are fetched once per render() call from the
 *    Rbac library's per-request cache (no extra DB queries)
 */
class MY_Controller extends CI_Controller {

    /** @var object  The authenticated user from session */
    public $current_user;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Rbac');
        $this->load->model('User_model');

        // Enforce authentication — redirect to login if no valid session
        $user_data = $this->session->userdata('user');
        if (!$this->session->userdata('logged_in') || empty($user_data)) {
            redirect('auth/login');
            exit;
        }

        // Build a consistent current_user object from the session 'user' array
        $this->current_user = (object)$user_data;
    }

    // -------------------------------------------------------------------------
    // Auth guard
    // -------------------------------------------------------------------------

    /**
     * Explicit auth check — available for legacy call sites but now redundant
     * since __construct() already enforces it.  Kept for backwards compatibility.
     */
    public function require_auth()
    {
        $user_data = $this->session->userdata('user');
        if (!$this->session->userdata('logged_in') || empty($user_data)) {
            redirect('auth/login');
            exit;
        }
    }

    // -------------------------------------------------------------------------
    // Permission guard
    // -------------------------------------------------------------------------

    /**
     * Enforce backend authorisation for a protected controller action.
     *
     * Super Admins always pass.
     * Other users need the exact permission key assigned to their role.
     *
     * For AJAX requests a JSON 403 response is returned instead of a redirect.
     *
     * @param  string $permission_key  e.g. 'students.view', 'fees.collect'
     */
    public function require_permission($permission_key)
    {
        if ($this->rbac->is_super_admin()) {
            return;
        }

        if (!$this->rbac->has_permission($permission_key)) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'Access denied. Required permission: ' . $permission_key,
                    ]))
                    ->_display();
                exit;
            }

            $this->session->set_flashdata(
                'error',
                'Access Denied: You do not have permission to perform this action.'
            );
            redirect('unauthorized');
        }
    }

    // -------------------------------------------------------------------------
    // View renderer
    // -------------------------------------------------------------------------

    /**
     * Loads templates/header → $view → templates/footer.
     *
     * Effective permissions are read from Rbac's per-request cache so no
     * extra DB queries are added here after the first permission check.
     *
     * @param  string $view   View path relative to application/views/
     * @param  array  $data   Data array passed to all three view files
     */
    public function render($view, array $data = array())
    {
        $uid = (int)($this->current_user->user_id ?? 0);

        $data['title']               = $data['title'] ?? 'EduCore';
        $data['page_key']            = $data['page_key'] ?? '';
        $data['breadcrumb']          = isset($data['breadcrumb'])
                                         ? json_encode($data['breadcrumb'])
                                         : NULL;
        $data['current_user']        = $this->current_user;
        $data['is_super_admin']      = $this->rbac->is_super_admin();

        // Permissions come from Rbac's per-request cache — no extra DB query
        // if has_permission() or is_super_admin() was already called this request
        $data['effective_permissions'] = $this->rbac->is_super_admin()
            ? ['*']
            : $this->User_model->get_effective_permissions($uid);

        $this->load->view('templates/header', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer', $data);
    }
}
