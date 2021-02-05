<?php defined('BASEPATH') OR exit('No direct script access allowed.');

Class User extends CI_Controller {
    public $data = array('msg' => '');

    function __construct() {
        parent:: __construct();
        $this->load->model('Users');
    } 

    public function index() {   
        $this->session->keep_flashdata('msg');
        
        if ($this->session->userdata('signed_in')) {
            Redirect('Dashboard');
        } else {
            Redirect('User/signInView');
        }     
    }

    public function signUpView() {
        $data['title'] = 'Sign Up';
        $this->template->load('default', 'signup', $data);        
    }

    public function signInView() {
        if($this->input->cookie(['user']) == true) {
            $get_user = $this->Users->getEmailPasswordFromToken($this->input->cookie('user'));

            if ($get_user) {
                $data['email'] = $get_user[0]['email'];
                $data['pass'] = $get_user[0]['password'];
            }
        }
        $data['title'] = 'Sign In';
        $this->template->load('default', 'signin', $data);        
    }

    public function passwordView() {
        $this->session->keep_flashdata('token');
        $data['title'] = 'Reset Password';
        $this->template->load('default', 'password', $data);         
    }

    public function signUp() {
        //validate input 
        $this->form_validation->set_rules('first-name', 'first name', 'required'); 
        $this->form_validation->set_rules('last-name', 'last name', 'required'); 
        $this->form_validation->set_rules('email', 'email', 'required|valid_email'); 
        $this->form_validation->set_rules('password', 'password', 'required'); 
        $this->form_validation->set_rules('confirm-password', 'password confirmation', 'required|matches[password]');

        $topics = $this->input->post('topics');

        //store user data in array
        $user_data = array(
            'firstName' => $this->input->post('first-name', TRUE),
            'lastName' => $this->input->post('last-name', TRUE),
            'country' => $this->input->post('country', TRUE),
            'email' => $this->input->post('email', TRUE),
            'verificationHash' => md5(rand(0, 1000)),
            'password' => $this->input->post('password', TRUE)
        );

        //check for invalid input
        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('msg', validation_errors());
            Redirect('User/signUpView'); 
        } 

        //validate password strength
        $uppercase = preg_match('@[A-Z]@', $user_data['password']);
        $lowercase = preg_match('@[a-z]@', $user_data['password']);
        $number    = preg_match('@[0-9]@', $user_data['password']);
        $specialChars = preg_match('@[^\w]@', $user_data['password']);

        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($user_data['password']) < 8) {     //if password strong enough
            $this->session->set_flashdata('msg', '<p>Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</p>');
            Redirect('User/signUpView');   
        }

        //check if email already exists
        if ($this->Users->emailCheck($user_data['email'])) {   
            $this->session->set_flashdata('msg', '<p>An account with that email address already exists. Please choose another email address or sign in to your existing account.</p>');
            Redirect('User/signUpView');  
        } 

        //all tests passed
        $this->Users->signupUser($user_data, $topics);  
        
        //send confirmation email
        $subject = 'Email Verification';         
        $message = "
        <html>
        <head>
            <title>Email Verification</title>
        </head>
        <body>
            <h2>Thank you for signing up.</h2>
            <h3>Your account has been created.</h3>
            <h3>Here are your sign in details:</h3>
            <p>Email: ".$user_data['email']."</p>
            <p>Password: ".$user_data['password']."</p>
            <p>Please click the link below to verify your account.</p>
            <h4><a href='".base_url()."User/verifyEmail?email=".$user_data['email']."&hash=".$user_data['verificationHash']."'>Verify My Account</a></h4>
        </body>
        </html>
        ";
        $this->sendVerificationEmail($user_data['email'], $subject, $message);

        $this->session->set_flashdata('msg', '<p>Your account has been successful created.</p> <p>Please check your inbox for an email with a verification link.</p>');
        Redirect('User/signInView');
    }

    public function signIn() {
        //validate input
        $this->form_validation->set_rules('email', 'email', 'required|valid_email'); 
        $this->form_validation->set_rules('password', 'password', 'required'); 

        //store user data in array
        $user_data = array(
            'email' => $this->input->post('email', TRUE),
            'password' => md5($this->input->post('password', TRUE))
        );

        //check for invalid input
        if (!$this->form_validation->run()) {    
            $this->session->set_flashdata('msg', validation_errors());
            Redirect('User/signInView');
        }

        //check if email already exists
        if (!$this->Users->emailCheck($user_data['email'])) {  
            $this->session->set_flashdata('msg', '<p>No account exists with that email address. Please try again or sign up for an account if you do not have one.</p>');
            Redirect('User/signInView');  
        }

        //validate password
        $get_user = $this->Users->signinUser($user_data);     
        
        if (!$get_user) {   
            $this->session->set_flashdata('msg', '<p>Invalid password. Please try again.</p>');
            Redirect('User/signInView'); 
        }

        //all tests passed
        $user = array(
            'user_id' => $get_user[0]['userID'],
            'firstname' => $get_user[0]['firstName'],
            'lastname' => $get_user[0]['lastName'],
            'email' => $get_user[0]['email'],
            'verified' => $get_user[0]['verified'],
            'signed_in' => true
        );

        //create cookie if remember me checkbox ticked
        if ($this->input->post('remember') == "yes") {  
            $access_token = md5(rand(0, 1000));
            $this->Users->addAccessToken($user, $access_token);
            $this->input->set_cookie('user', $access_token, 86400);
        } else {
            //delete existing cookie if checkbox not ticked
            if($this->input->cookie('user')) {   
                $this->input->set_cookie('user', $access_token, time()-3600);
            }
        }

        $this->session->set_userdata($user);
        $data['user'] = $user; 
        $this->session->set_flashdata('msg', '<p>Welcome '.$user['firstname'].' . You are now signed in.</p>');
        Redirect('Dashboard');
    }

    public function signOut() {
        $this->session->unset_userdata('firstname');
        $this->session->unset_userdata('lastname');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('verified');
        $this->session->unset_userdata('password');
        $this->session->unset_userdata('signed_in');

        $this->session->set_flashdata('msg', '<p>You have successfuly signed out.</p>');
        Redirect('User/signInView');
    }

    public function captcha() {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array('response' => $this->input->post('response', TRUE), 'secret' => getenv('CAPTCHA_SECRET_KEY'));

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded\r\n',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        echo json_encode($result);
    }

    public function forgotPassword() {
        $email = $this->input->post('email', TRUE);

        if ($this->Users->emailCheck($email) == false) {
            echo 'error';
        }

        $token = $this->Users->setResetToken($email);

        $subject = 'Password Reset';
        $message = "
        <html>
        <head>
            <title>Password Reset</title>
        </head>
        <body>
            <h2>You have requested a password reset.</h2>
            <p>Please click the link below to create a new password.</p>
            <h4><a href='".base_url()."User/resetPassword?email=".$email."&token=".$token."'>Reset Password</a></h4>
        </body>
        </html>
        ";
        $this->sendVerificationEmail($email, $subject, $message);

        echo json_encode($email);
    }

    public function resetPassword() {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $token_db = $this->Users->getResetTokenFromEmail($email)[0]['resetToken'];

        if ($token_db != false) {
            if ($token == $token_db) {
                $this->session->set_flashdata('token', $token);
                Redirect('User/passwordView');
            }
        }

        $this->session->set_flashdata('msg', '<p>We were unable to reset your password.</p>');
        Redirect('User/signInView');
    }

    public function changePassword() {
        $this->form_validation->set_rules('new-password', 'new password', 'required'); 
        $this->form_validation->set_rules('new-password-confirm', 'new password confirmation', 'required|matches[new-password]');
    
        //check for invalid input
        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('msg', validation_errors());
            Redirect('User/passwordView'); 
        } 

        $user_data = array(
            'token' => $this->session->flashdata('token'),
            'new-password' => $this->input->post('new-password', TRUE)
        );

        //validate password strength
        $uppercase = preg_match('@[A-Z]@', $user_data['new-password']);
        $lowercase = preg_match('@[a-z]@', $user_data['new-password']);
        $number    = preg_match('@[0-9]@', $user_data['new-password']);
        $specialChars = preg_match('@[^\w]@', $user_data['new-password']);

        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($user_data['new-password']) < 8) {     //if password strong enough
            $this->session->set_flashdata('msg', '<p>Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.</p>');
            Redirect('User/passwordView');   
        }

        $user_data['new-password'] = md5($user_data['new-password']);

        $result = $this->Users->changePassword($user_data);  

        if ($result == false) {
            $this->session->set_flashdata('msg', '<p>Your password could not be updated. Please try again.</p>');
            Redirect('User/passwordView');   
        }

        if ($this->input->cookie('user')) {   
            $this->input->set_cookie('user', '', time()-3600);
        }

        $this->session->set_flashdata('msg', '<p>Your password has been successfully updated. Please sign in.</p>');
        Redirect('User/signInView');
    }

    public function updateUserInfo() {
        $user_data = array(
            'firstname' => $this->input->post('firstName', TRUE),
            'lastname' => $this->input->post('lastName', TRUE),
            'email' => $this->input->post('email', TRUE),
        );

        $get_user = $this->Users->getEmailPasswordFromToken($this->input->cookie('user'));

        if ($get_user) {
            $email = $get_user[0]['email'];
            $pass = $get_user[0]['password'];
        }

        //update email
        if ($user_data['email'] != $email) {
            if ($this->Users->emailCheck($user_data['email'])) {   
                echo 'error';
            } else {
                $this->Users->updateEmail($user_data['email']);
                $this->session->unset_userdata('email');
                $this->session->set_userdata('email', $user_data['email']);
                $this->session->unset_userdata('verified');
                $this->session->set_userdata('verified', 0);
                if ($this->input->cookie('user')) {   
                    $this->input->set_cookie('user', '', time()-3600);
                }
                $hash = $this->Users->setHash($user_data['email']);
                $subject = 'Email Verification';
                $message = "
                <html>
                <head>
                    <title>Email Verification</title>
                </head>
                <body>
                    <h2>Your email has been updated.</h2>
                    <p>New email: ".$user_data['email']."</p>
                    <p>Please click the link below to verify your account.</p>
                    <h4><a href='".base_url()."User/verifyEmail?email=".$user_data['email']."&hash=".$hash."'>Verify My Account</a></h4>
                </body>
                </html>
                ";
                $this->sendVerificationEmail($user_data['email'], $subject, $message);
                $user_data['emailUpdated'] = true; 
            }
        }

        //update first name
        if ($user_data['firstname'] != $this->session->userdata('firstname')) {
            $this->Users->updateFirstName($user_data['firstname']);
            $this->session->unset_userdata('firstname');
            $this->session->set_userdata('firstname', $user_data['firstname']);
        }

        //update last name 
        if ($user_data['lastname'] != $this->session->userdata('lastname')) {
            $this->Users->updateLastName($user_data['lastname']);
            $this->session->unset_userdata('lastname');
            $this->session->set_userdata('lastname', $user_data['lastname']);
        }

        echo json_encode($user_data);
    }

    public function sendVerificationEmail($email, $subject, $message) {
     /*   $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'mailhub.eait.uq.edu.au',
            'smtp_port' => 25,
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
        ); */
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => getenv('SMTP_HOST'),
            'smtp_port' => getenv('SMTP_PORT'),
            'smtp_user' => getenv('SMTP_USER'), 
            'smtp_pass' => getenv('SMTP_PASS'),
            'smtp_crypto' => getenv('SMTP_CRYPTO'),
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
      );
        $this->load->library('email', $config);  	
        $this->email->set_newline("\r\n");
        $this->email->from(getenv('ADMIN_EMAIL'), 'visual-eyes');
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message); 
        $this->email->send();
    }

    public function verifyEmail() {
        $email = $this->input->get('email');
        $hash = $this->input->get('hash');

        $hash_db = $this->Users->getHashFromEmail($email)[0]['verificationHash'];

        if ($hash_db != false) {
            if ($hash == $hash_db) {
                $this->Users->verify($email);
                $this->session->unset_userdata('verified');
                $this->session->set_userdata('verified', 1);
                $this->session->set_flashdata('msg', '<p>Thank you. Your email has been verified.</p>');
                Redirect('User/index');
            }
        }

        $this->session->set_flashdata('msg', '<p>We were unable to verify your email.</p>');
        Redirect('User/index');
    }
}

?>