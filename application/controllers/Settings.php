<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Setting_model');
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
}
