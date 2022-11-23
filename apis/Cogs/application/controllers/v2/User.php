<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// User Controller to deal with User related operations
class User extends CI_Controller {

    // default controller constuction
    function __construct() {
        parent::__construct();
        validate_access_key();
        $this->load->library('form_validation');
        $this->load->model('v2/users');
    }

    // authentication for user to log in API
    public function login() {
        $config = [
            [
                'field' => 'email',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => $this->lang->line('req_email'),
                ]
            ], [
                'field' => 'password',
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => $this->lang->line('req_password'),
                    'min_length' => $this->lang->line('len_password')
                ]
            ], [
                'field' => 'id',
                'rules' => 'required',
                'errors' => [
                    'required' => $this->lang->line('req_account_id')
                ]
            ]
        ];

        $this->form_validation->set_data($this->input->post());
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() == FALSE) {
            write_response($this->lang->line('MSG_998'), $this->form_validation->error_array());
        } else {
            // encrypting password
            $data = array(
                'email' => $this->input->post('email'),
                'password' => md5($this->input->post('password')),
                'id' => $this->input->post('id')
            );
            $user_data = $this->users->authenticate($data);
            if (count($user_data)>0) {
                $response = array(
                    'api_key' => generate_api_key(),
                    'profile' => $user_data
                );
                $this->session->set_userdata('user_data', $user_data);
                write_response($this->lang->line('suc_login'), $response);
            } else {
                write_response($this->lang->line('MSG_997'));
            }
        }
    }

    // kicking out user
    public function logout() {
        
        // clearing session and logging out
        $this->session->unset_userdata('user_data');
    }

    public function profile() {
        echo "Profile";
    }

    public function change_password() {
        echo "Change Password";
    }

}
