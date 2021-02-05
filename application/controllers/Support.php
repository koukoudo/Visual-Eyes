<?php defined('BASEPATH') OR exit('No direct script access allowed.');

Class Support extends CI_Controller {

    function __construct() {
        parent:: __construct();
    }

    public function index() {
        Redirect('Support/supportView');
    }

    public function supportView() {
        $data['title'] = 'Support';
        $this->template->load('default', 'donate', $data); 
    }

}

?>