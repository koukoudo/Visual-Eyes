<?php defined('BASEPATH') OR exit('No direct script access allowed.');

Class Visualize extends CI_Controller {

    function __construct() {
        parent:: __construct();
        $this->load->model('Visualizes');
    }

    public function index() {
        //$this->Visualizes->addDatasetsToDB();
        //$this->Visualizes->addCSVDataToDB();
        Redirect('Visualize/visualizeView');
    }

    public function visualizeView() {
        $data['title'] = 'Visualize';
        $this->template->load('default', 'visualize', $data); 
    }

    public function getDataSets() {
        $data['keywords'] = $this->input->post('keywords');

        $get_data = $this->Visualizes->search($data);

        if ($get_data != false ) {
            echo json_encode($get_data);
        } 
    }

    public function getDataFiles() {
        $id = $this->input->post('id');

        $get_data = $this->Visualizes->select($id);

        $this->session->set_userdata('data_id', $get_data['id']);

        echo json_encode($get_data['files']);
    }

    public function getDataTypes() {
        $file_name = $this->input->post('file');
        $this->session->set_userdata('file_name', $file_name);
        
        $columns = $this->Visualizes->getAxisTitles($file_name);
        $this->session->set_userdata('axis_titles', $columns);

        $types = array();

        if (!empty($columns['dates']) && !empty($columns['numbers'])) {
            array_push($types, 'Line Graph');
        }

        if (count($columns['numbers']) >= 2) {
            array_push($types, 'Scatter Plot');
        }

        if (!empty($columns['strings']) && !empty($columns['numbers'])) {
            array_push($types, 'Bar Chart');
        }

        echo json_encode($types);
    }

    public function getAxes() {
        $this->session->set_userdata('chart_type', $this->input->post('type'));

        echo json_encode($this->session->userdata('axis_titles'));
    }

    // public function uploadData() {
    //     $tmp = $this->input->post();

    //     $config = array(
    //         'upload_path' => "./uploads/",
    //         'allowed_types' => "csv",
    //         'overwrite' => true,
    //         'max_size' => "2048000", 
    //     );

    //     $this->load->library('upload', $config);

    //     if($this->upload->do_upload('file-to-upload')) {
    //         $file_name = array();
    //         array_push($file_name, $this->upload->data('file_name'));
    //         $file_path = 'uploads/';
    //         $this->session->set_userdata('type', 'upload');
    //         $this->session->set_userdata('file_path', 'uploads/');
    //         $this->session->set_userdata('files', $file_name);
    //         echo json_encode($file_path);
    //     }
    // } 
 
    public function create() {
        $x_axis = $this->input->post('x_axis_title');
        $y_axis = $this->input->post('y_axis_title');

        $values = $this->Visualizes->getDataPoints($x_axis, $y_axis);

        if ($values !== false) {
            echo json_encode($values);
        } 
    }

    public function addChartToDB() {
        $chart = array(
            'dataID' => $this->session->userdata('data_id'),
            'userID' => $this->session->userdata('user_id'),
            'title' => $this->input->post('title')
        );

        $config = array(
            'file_name' => $chart['userID']." ".$chart['dataID']." ".$chart['title'],
            'upload_path' => './charts/',
            'allowed_types' => 'gif|jpg|png',
            'overwrite' => true,
            'max_size' => '2048000', 
        );

        $this->load->library('upload', $config);

        if($this->upload->do_upload('chart')) {
            $chart['fileName'] = $this->upload->data('file_name');
            $this->Visualizes->addChart($chart);
        }
    }
}

?>