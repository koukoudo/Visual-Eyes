<?php defined('BASEPATH') OR exit('No direct script access allowed.');

class Users extends CI_Model {
    function __construct() {
        parent:: __construct();
        $this->table = 'users';
    }

    public function signupUser($user, $topics) {  
        $user['password'] = md5($user['password']);  
        $this->db->insert('users', $user);

        $id = $this->db->insert_id();

        foreach ($topics as $topic) {
            $entry = array(
                'userID' => $id,
                'topic' => $topic
            );

            $this->db->insert('userinterests', $entry);    
        }
    }

    public function signinUser($user) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $user['email']);
        $this->db->where('password', $user['password']);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        else { 
          return false;
        }
    }

    public function emailCheck($email) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email);
        $query = $this->db->get();
       
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function addAccessToken($user, $token) {
        $this->db->set('accessToken', $token);
        $this->db->where('userID', $user['user_id']);
        $this->db->update('users');
    }

    public function getEmailPasswordFromToken($token) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('accessToken', $token);
        $query = $this->db->get();
       
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }  
    }

    public function changePassword($user) {
        if (!$user['token']) {
            return false;
        }

        $this->db->set('password', $user['new-password']);       
        $this->db->where('resetToken', $user['token']);
        
        return $this->db->update('users');
    }

    public function updateFirstName($first_name) {
        $this->db->set('firstName', $first_name);
        $this->db->where('userID', $this->session->userdata('user_id'));
        $this->db->update('users'); 
    }

    public function updateLastName($last_name) {
        $this->db->set('lastName', $last_name);
        $this->db->where('userID', $this->session->userdata('user_id'));
        $this->db->update('users');  
    }

    public function updateEmail($email) {
        $this->db->set('email', $email);
        $this->db->set('verified', 0);
        $this->db->where('userID', $this->session->userdata('user_id'));
        $this->db->update('users');  
    }

    public function setHash($email) {
        $hash = md5(rand(0, 1000));
        $this->db->set('verificationHash', $hash);
        $this->db->where('email', $email);
        $this->db->update('users');  

        return $hash;
    }

    public function setResetToken($email) {
        $token = md5(rand(0, 1000));
        $this->db->set('resetToken', $token);
        $this->db->where('email', $email);
        $this->db->update('users');  

        return $token;        
    }

    public function verify($email) {
        $this->db->set('verified', 1);
        $this->db->where('email', $email);
        $this->db->update('users');
    }

    public function getHashFromEmail($email) {
        $this->db->select('verificationHash');
        $this->db->from('users');
        $this->db->where('email', $email);
        $query = $this->db->get();
       
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }  
    }

    public function getResetTokenFromEmail($email) {
        $this->db->select('resetToken');
        $this->db->from('users');
        $this->db->where('email', $email);
        $query = $this->db->get();
       
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }    
    }
}

?>