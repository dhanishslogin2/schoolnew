<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth
 *
 * Handles user login and logout.
 * Credentials are verified against tbl_users using bcrypt (password_verify).
 * Accounts must be Active and not locked to succeed.
 */
class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper('app'); // school_initials()
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        } else {
            redirect('auth/login');
        }
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
            return;
        }

        if ($this->input->method() === 'post')
        {
            $this->form_validation->set_rules('email',    'Email or Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password',          'required');

            if ($this->form_validation->run() === TRUE)
            {
                $identifier = $this->input->post('email',    TRUE);
                $password   = $this->input->post('password');

                $user = $this->User_model->verify_credentials($identifier, $password);

                if ($user)
                {
                    $initials = school_initials($user->name);

                    // Store the canonical nested 'user' array plus essential
                    // top-level keys for backward compatibility with controllers
                    // and models that read userdata('user_id') / userdata('user_role')
                    $this->session->set_userdata([
                        'logged_in'  => TRUE,
                        // Legacy flat keys (still read by 30+ controller/model locations)
                        'user_id'    => (int)$user->user_id,
                        'user_name'  => $user->name,
                        'user_email' => $user->email,
                        'user_role'  => $user->role_name,
                        'role_id'    => (int)$user->role_id,
                        // Canonical nested object (used by MY_Controller + Rbac)
                        'user'       => [
                            'user_id'   => (int)$user->user_id,
                            'name'      => $user->name,
                            'username'  => $user->username,
                            'email'     => $user->email,
                            'role_id'   => (int)$user->role_id,
                            'role'      => $user->role_name,
                            'role_code' => $user->role_code ?: 'USER',
                            'user_type' => $user->user_type ?: 'Admin',
                            'initials'  => $initials,
                        ],
                    ]);


                    redirect('dashboard');
                    return;
                }
                else
                {
                    $this->session->set_flashdata('error', 'Invalid email address or password. Please try again.');
                }
            }
            else
            {
                $this->session->set_flashdata('error', validation_errors('', ''));
            }
        }

        $this->load->view('auth/login');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
