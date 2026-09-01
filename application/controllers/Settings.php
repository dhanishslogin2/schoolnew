<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Setting_model');
        $this->load->model('Staff_document_type_model');
    }

    public function index()
    {
        $this->require_permission('settings.view');

        if ($this->input->method() === 'post') {
            $this->require_permission('settings.edit');
            $data = array(
                'school_name'      => $this->input->post('school_name', TRUE),
                'school_code'      => $this->input->post('school_code', TRUE),
                'established_year' => $this->input->post('established_year', TRUE),
                'principal_name'   => $this->input->post('principal_name', TRUE),
                'phone'            => $this->input->post('phone', TRUE),
                'email'            => $this->input->post('email', TRUE),
                'website'          => $this->input->post('website', TRUE),
                'address'          => $this->input->post('address', TRUE),
                'description'      => $this->input->post('description', TRUE),
            );
            $this->Setting_model->update_settings($data);
            $this->session->set_flashdata('success', 'School settings updated successfully.');
            redirect('settings');
            return;
        }

        $settings = $this->Setting_model->get_settings();

        $this->render('pages/settings/index', array(
            'title'    => 'School Settings',
            'page_key' => 'settings',
            'settings' => $settings,
        ));
    }

    /* =========================================================================
       Staff Document Settings (Super Admin Only)
       ========================================================================= */
    public function staff_documents()
    {
        if (!$this->rbac->is_super_admin()) {
            show_error('Access restricted. Only Super Admin can manage Staff Document settings.', 403, '403 Forbidden');
            return;
        }

        $document_types = $this->Staff_document_type_model->get_all_for_settings();

        $this->render('pages/settings/staff_documents', array(
            'title'          => 'Staff Document Settings',
            'page_key'       => 'staff-document-settings',
            'breadcrumb'     => array('Login2 Settings', 'Staff Document'),
            'document_types' => $document_types,
        ));
    }

    public function staff_documents_add()
    {
        if (!$this->rbac->is_super_admin()) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_status_header(403)->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
                return;
            }
            show_error('Access restricted to Super Admin only.', 403, '403 Forbidden');
            return;
        }

        $doc_name = trim($this->input->post('document_name', TRUE));
        $desc     = trim($this->input->post('description', TRUE));
        $status   = $this->input->post('status') === 'Inactive' ? 'Inactive' : 'Active';

        if (empty($doc_name)) {
            $this->session->set_flashdata('error', 'Document name is required.');
            redirect('settings/staff_documents');
            return;
        }

        // Get max order
        $maxOrder = (int)$this->db->select_max('display_order')->where('is_deleted', 'n')->get('tbl_staff_document_types')->row()->display_order;

        $this->Staff_document_type_model->insert(array(
            'document_name' => $doc_name,
            'description'   => $desc ?: NULL,
            'status'        => $status,
            'display_order' => $maxOrder + 1,
            'created_by'    => $this->current_user->user_id ?? 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'is_deleted'    => 'n',
        ));

        $this->session->set_flashdata('success', 'Staff document requirement "' . html_escape($doc_name) . '" added successfully.');
        redirect('settings/staff_documents');
    }

    public function staff_documents_edit($id = NULL)
    {
        if (!$this->rbac->is_super_admin()) {
            show_error('Access restricted to Super Admin only.', 403, '403 Forbidden');
            return;
        }

        if (empty($id)) {
            redirect('settings/staff_documents');
            return;
        }

        $docType = $this->Staff_document_type_model->get_by_id($id);
        if (!$docType) {
            $this->session->set_flashdata('error', 'Document type not found.');
            redirect('settings/staff_documents');
            return;
        }

        $doc_name = trim($this->input->post('document_name', TRUE));
        $desc     = trim($this->input->post('description', TRUE));
        $status   = $this->input->post('status') === 'Inactive' ? 'Inactive' : 'Active';

        if (empty($doc_name)) {
            $this->session->set_flashdata('error', 'Document name cannot be empty.');
            redirect('settings/staff_documents');
            return;
        }

        $this->Staff_document_type_model->update($id, array(
            'document_name' => $doc_name,
            'description'   => $desc ?: NULL,
            'status'        => $status,
        ));

        $this->session->set_flashdata('success', 'Staff document "' . html_escape($doc_name) . '" updated successfully.');
        redirect('settings/staff_documents');
    }

    public function staff_documents_delete($id = NULL)
    {
        if (!$this->rbac->is_super_admin()) {
            show_error('Access restricted to Super Admin only.', 403, '403 Forbidden');
            return;
        }

        if (!empty($id)) {
            $docType = $this->Staff_document_type_model->get_by_id($id);
            if ($docType) {
                $this->Staff_document_type_model->soft_delete($id, $this->current_user->user_id ?? 1);
                $this->session->set_flashdata('success', 'Document requirement "' . html_escape($docType->document_name) . '" has been removed from future forms. Existing uploaded documents remain preserved.');
            }
        }

        redirect('settings/staff_documents');
    }

    public function staff_documents_toggle($id = NULL)
    {
        if (!$this->rbac->is_super_admin()) {
            show_error('Access restricted to Super Admin only.', 403, '403 Forbidden');
            return;
        }

        if (!empty($id)) {
            $this->Staff_document_type_model->toggle_status($id);
            $this->session->set_flashdata('success', 'Document requirement status updated.');
        }

        redirect('settings/staff_documents');
    }
}
