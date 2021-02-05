<?php defined('BASEPATH') OR exit('No direct script access allowed.');

Class About extends CI_Controller {

    function __construct() {
        parent:: __construct();
    }

    public function index() {
        Redirect('About/aboutView');
    }

    public function aboutView() {
        $data['title'] = 'About';
        $this->template->load('default', 'about', $data); 
    }

}

?>