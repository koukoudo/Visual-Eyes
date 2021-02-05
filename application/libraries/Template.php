<?php defined('BASEPATH') OR exit('No direct script access allowed.');

    class Template {
        var $ci;
         
        function __construct() 
        {
            $this->ci =& get_instance();
            $this->ci->load->library('session');
        }

        function load($tpl_view, $body_view = null, $data = null) {
            if (!is_null( $body_view )) {
                if (file_exists( APPPATH.'views/'.$tpl_view.'/'.$body_view.'.php')) {
                    $body_view_path = $tpl_view.'/'.$body_view.'.php';
                }

                $data['body'] = $this->ci->load->view($body_view_path, $data, TRUE);
            }
            
            $data['msg'] = $this->ci->session->flashdata('msg');

            $this->ci->load->view('templates/'.$tpl_view, $data);
        }
    }
    
?>