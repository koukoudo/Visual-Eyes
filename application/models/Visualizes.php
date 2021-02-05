<?php defined('BASEPATH') OR exit('No direct script access allowed.');

use Symfony\Component\DomCrawler\Crawler;

class Visualizes extends CI_Model {
    function __construct() {
        parent:: __construct();
    }

    public function addCSVDataToDB() {
        $this->load->dbforge();
        $this->db->query('use visualeyes');

        $this->db->select('dataTitle');
        $this->db->from('datasets');
        $query = $this->db->get();

        $result = $query->result_array();
        foreach ($result as $dataset) {
            $folder_path = 'data_files/'.$dataset['dataTitle'].'/';

            $files = @scandir($folder_path);
            if ($files !== false) {
                $files = array_slice($files, 2);

                foreach ($files as $file) {  
                    $table_name = str_replace('.csv', '', $file);
                    $table_name = str_replace(' ', '', $table_name);
                    $table_name = str_replace(str_split('()\/:*?!@#%^&-"<>|.,'), '', $table_name);

                    if (is_numeric($table_name)) {
                        $table_name = '_'.$table_name;
                    }

                    if (!$this->db->table_exists($table_name)) {

                        $file_path = $folder_path.$file;

                        if (($h = fopen($file_path, "r")) !== false) {     
                            $titles = fgetcsv($h);
                            $values = fgetcsv($h);
                            $fields = array();
                            $entry = array();
                            $date_fields = array();
                            $ignore_fields = array();

                            for ($i = 0; $i < count($values); $i++) {
                                $titles[$i] = str_replace(' ', '_', $titles[$i]);
                                $titles[$i] = str_replace(str_split('()\/:*?!@#%^&-"<>|.,'), '_', $titles[$i]);

                                if (!empty($titles[$i])) {
                                    if (strtolower($titles[$i]) == 'year') {
                                        $fields[$titles[$i]] = array(
                                            'type' => 'YEAR',
                                            'constraint' => 4,
                                            'null' => TRUE
                                        );
        
                                        $entry[$titles[$i]] = $values[$i];
                                    } else if (($date = strtotime($values[$i])) !== false) {
                                        $fields[$titles[$i]] = array(
                                            'type' => 'DATE',
                                            'null' => TRUE
                                        );
        
                                        $date = date('Y-m-d', $date);
                                        $entry[$titles[$i]] = $date;
        
                                        array_push($date_fields, $i);
                                    } else if (is_numeric($values[$i])) {
                                        $fields[$titles[$i]] = array(
                                            'type' => 'INT',
                                            'constraint' => 10,
                                            'null' => TRUE
                                        );
        
                                        $entry[$titles[$i]] = $values[$i];
                                    } else {
                                        $fields[$titles[$i]] = array(
                                            'type' => 'VARCHAR',
                                            'constraint' => 50,
                                            'null' => TRUE
                                        );
        
                                        $entry[$titles[$i]] = trim($values[$i]);
                                    }
                                } else {
                                    array_push($ignore_fields, $i);
                                }
                            } 

                            $this->dbforge->add_field($fields);

                            $this->dbforge->create_table($table_name, TRUE);

                            $this->db->insert($table_name, $entry);

                            while (($values = fgetcsv($h)) !== FALSE) {
                                $entry = array();

                                for ($i = 0; $i < count($values); $i++) {
                                    if (!in_array($i, $ignore_fields)) {
                                        if (in_array($i, $date_fields)) {
                                            $entry[$titles[$i]] = date('Y-m-d', strtotime($values[$i]));
                                        } else if (is_numeric($values[$i])) {
                                            $entry[$titles[$i]] = $values[$i];
                                        } else {
                                            $entry[$titles[$i]] = trim($values[$i]);
                                        }
                                    }
                                }

                                $this->db->insert($table_name, $entry);
                            }
                        }
                    }
                }
            }                    
        }
    }

    public function addDatasetsToDB() {
        $fh = fopen(base_url('data_files/structure.txt'), 'r');

        while ($line = fgets($fh)) {
            $files = array();
            $pieces = explode(': ', $line);
            $title = $pieces[0];
            $title = str_replace('.', '', $title);
            $title = str_replace(':', '', $title);

            $description = $pieces[1];
            if (count($pieces) > 3) {
                for ($i = 2; $i < count($pieces) - 1; $i++) {
                    $description = $description . $pieces[$i];
                }
            }

            $files = @scandir('data_files/'.$title);
            if ($files !== false) {
                $files = array_slice($files, 2);
                if (count($files) == 0) {
                    rmdir('data_files/'.$title);
                } else {
                    $dataset = array(
                        'dataTitle' => $title,
                        'description' => $description
                    );
    
                    $this->db->insert('datasets', $dataset);
        
                    $id = $this->db->insert_id();
    
                    foreach ($files as $file) {
                        $entry = array(
                            'dataID' => $id,
                            'fileName' => $file
                        );
    
                        $this->db->insert('datafilenames', $entry);    
                    }
                }
            } 
        }

        fclose($fh);
    }

    public function search($criteria) {
        $this->db->select('*');
        $this->db->from('datasets');
        $this->db->like('dataTitle', $criteria['keywords']) OR $this->db->like('description', $criteria['keywords']);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        else { 
          return false;
        }
    }
 
    public function select($id) {
        $this->db->select('*');
        $this->db->from('datasets');
        $this->db->where('dataID', $id);   

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $tmp = $query->result_array();
            $dataset = array(
                'title' => $tmp[0]['dataTitle'],
                'id' => $tmp[0]['dataID'],
                'files' => array()
            );

            $this->db->select('*');
            $this->db->from('datafilenames');
            $this->db->where('dataID', $id);  
            
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $tmp = $query->result_array();
                foreach ($tmp as $file) {
                    $file_name = strtolower($file['fileName']);
                    $file_name = str_replace('.csv', '', $file_name);
                    $file_name = str_replace(' ', '', $file_name);
                    $file_name = str_replace(str_split('()\/:*?!@#%^&-"<>|.,'), '', $file_name);

                    if (is_numeric($file_name)) {
                        $file_name = '_'.$file_name;
                    }

                    if ($this->db->table_exists($file_name)) {
                        array_push($dataset['files'], $file_name);
                    } else {
                        $this->db->where('fileName', $file);
                        $this->db->delete('datafilenames');
                    }
                }

                return $dataset;
            }

            return false;
        }
        else { 
            return false;
        }       
    }

    public function getAxisTitles($file) {
        $fields = $this->db->field_data($file);
        $columns = array(
            'dates' => array(),
            'numbers' => array(),
            'strings' => array()
        );

        foreach ($fields as $field) {
            if ($field->type == 'date' || $field->type == 'year') {
                array_push($columns['dates'], $field->name);
            } else if ($field->type == 'int') {
                array_push($columns['numbers'], $field->name);
            } else if ($field->type == 'varchar') {
                array_push($columns['strings'], $field->name);
            }
        }

        return $columns;
    }

    public function getDataPoints($x_axis, $y_axis) {
        $table_name = $this->session->userdata('file_name');
        $fields = array(
            $x_axis,
            $y_axis
        );

        $this->db->select($fields);
        $this->db->from($table_name);

        if ($this->session->userdata('chart_type') == 'Scatter Plot' || $this->session->userdata('chart_type') == 'Line Graph') {
            $this->db->order_by($x_axis, 'asc');
        } else if ($this->session->userdata('chart_type') == 'Bar Graph') {
            $this->db->order_by($y_axis, 'asc');
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $points = $query->result_array();

            $i = 0;

            while ($i < count($points)) {
                if (empty($points[$i][$x_axis]) || empty($points[$i][$y_axis])) {
                    array_splice($points, $i, 1);
                } else {
                    $i++;
                }
            }

            return $points;
        }

        return false;
    }

    public function addChart($chart) {
        $this->db->insert('visualizations', $chart);
    }
}

?>