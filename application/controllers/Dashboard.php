<?php defined('BASEPATH') OR exit('No direct script access allowed.');

Class Dashboard extends CI_Controller {

    function __construct() {
        parent:: __construct();
        $this->load->model('Dashboards');
    } 

    public function index() {    
        $this->session->keep_flashdata('msg');
        
        if ($this->session->userdata('signed_in')) {
            Redirect('Dashboard/dashboardView');
        } else {
            Redirect('User/signInView');
        }     
    }

    public function dashboardView() {
     /*   $rec_datasets = $this->Dashboards->getRecommendations($this->session->userdata('user_id'));
        $data['datasets'] = array();
        foreach($rec_datasets as $dataset) {
            array_push($data['datasets'], $this->Dashboards->getDatasetFromDataID($dataset));
        } */

        $user_charts = $this->Dashboards->getChartsFromUserID($this->session->userdata('user_id'));
        $data['charts'] = array();
        foreach($user_charts as $chart) {
            array_push($data['charts'], $chart);       
        } 

        $data['title'] = 'Dashboard';  
        $this->template->load('default', 'dashboard', $data);         
    }
}

?>