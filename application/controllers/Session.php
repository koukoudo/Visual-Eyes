<?php

Class Session extends CI_Controller {
    public function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function create($data) {
        $this->session->set_userdata('username', $data['userinfo'][0]['username']);
        $this->session->set_userdata('email', $data['userinfo'][0]['email']);   
        $this->session->set_userdata('signed_in', true);    
        $this->session->set_flashdata('user_signedin', 'You are now signed in.');
    }

    public function unset() {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('email');        
        $this->session->unset_userdata('signed_in');        
    }

    public function destroy() {
        $this->session->session_destroy();
    }
}
    
?>