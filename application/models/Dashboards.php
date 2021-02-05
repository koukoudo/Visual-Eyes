<?php defined('BASEPATH') OR exit('No direct script access allowed.');

class Dashboards extends CI_Model {
    function __construct() {
        parent:: __construct();
    }

    public function getRecommendations($user_id) {
        //get user keywords
        $this->db->select('keyword');
        $this->db->from('user-keywords');
        $this->db->where('userID', $user_id);
        $this->db->order_by('keyword', 'DESC');
        $query = $this->db->get();
        $keywords = $query->result_array();

        $data_matches = array();

        //for each user keyword, get datasets with same keyword
        foreach ($keywords as $keyword) {
            $this->db->select('dataID');
            $this->db->from('data-keywords');
            $this->db->where('keyword', $keyword['keyword']);
            $query = $this->db->get();
            $result = $query->result_array();

            //for each dataset
            foreach($result as $dataset) {
                //if not present in array
                if (!in_array($dataset['dataID'], $data_matches)) {
                    $this->db->select('*');
                    $this->db->from('visualizations');
                    $this->db->where('dataID', $dataset['dataID']);
                    $this->db->where('userID', $user_id);
                    $query = $this->db->get();

                    //if no charts created with dataset by user
                    if ($query->num_rows() == 0) {
                        array_push($data_matches, $dataset['dataID']);
                    }
                }
            }
        }

        return array_slice($data_matches, 0, 3);
    }

    public function getDatasetFromDataID($id) {
        $this->db->select('*');
        $this->db->from('datasets');
        $this->db->where('dataID', $id);
        $query = $this->db->get();

        return $query->result_array();
    }

    
    public function getChartsFromUserID($id) {
        $this->db->select('*');
        $this->db->from('visualizations');
        $this->db->where('userID', $id);
        $this->db->join('datasets', 'datasets.dataID = visualizations.dataID');
        $query = $this->db->get();

        return $query->result_array();
    }
}