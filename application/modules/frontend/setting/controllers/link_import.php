<?php
class Link_import extends Frontend 
{
    var $module = "setting";

    function __construct()
    {
        parent::__construct();

        $this->authen->checkLogin();

        $this->load->model("master_model");
        $this->load->model("setting_model");
    }

    function cmdSaveImport()
    {
        if(!isset($_FILES['file_import_url']['tmp_name'])) {
            show_404();
            die;
        }

        $result = array();
        $keyword = array();
        $rowsdata = array();
        $post = $this->input->post();
        $config = $this->master_model->get_config();
        set_time_limit(0);

        $this->load->library("PHPExcel");
        $inputFileName = $_FILES['file_import_url']['tmp_name'];

        //  Read your Excel workbook
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);

        //  Get worksheet dimensions
        
        $sheet = $objPHPExcel->getSheet(0); 
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();

        if ($highestRow < 2){
            $result["message"] = "file excel ไม่มีข้อมูล";
            $result["status"]  = false;

        }else{
            $check_same = 0;
            for ($row = 2; $row <= $highestRow; $row++) { 
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
                $rowCell = $rowData[0];
                $row_link_url = trim($rowCell[0]);
                if(isset($row_link_url)){
                    $link_url = strstr($row_link_url,'https');
                    $msg_id = $this->find_msg_id($link_url);
                    if(isset($msg_id)){
                        $check_msg_id = $this->setting_model->get_link_url($msg_id);
                        if(empty($check_msg_id)){
                            $save = array();
                            $save["url"]    = $link_url;
                            $save["msg_id"] = $msg_id;
                            $save["status"] = 0;
                            $check_insert = $this->setting_model->insert_link_url($save);
                            $check_same--;
                        
                        }else{
                            $check_same++;
                        }
                    }
                }
            }

            if($check_same > 0){
                $result["message"] = " success but same ".$check_same." url.";
                $result["status"]  = false;
            }else{
                $result["message"] = " success ";
                $result["status"]  = true;
            }
        }
        
        echo json_encode($result);
    }

    function get_string_between($string, $start, $end=NULL){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0){
            return null;
        } 
            $ini += strlen($start);
            if(isset($end)){
                $len = strpos($string, $end, $ini) - $ini;
            }else{
                $len = strpos($string, $start);
            }            
        return substr($string, $ini, $len);
    }

    function find_msg_id($row_link_url = null){
        if(isset($row_link_url)){
            if (strpos($row_link_url,'facebook') == true){
                $post_id = null;
                if (strpos($row_link_url,'photo.php') == true){
                    $post_id = $this->get_string_between($row_link_url,"fbid=","&");
                    return $post_id;

                } elseif(strpos($row_link_url,'sale_post_id=') == true){
                    $post_id = $this->get_string_between($row_link_url,"sale_post_id=","&");
                    return $post_id;

                } elseif(strpos($row_link_url,'story_fbid=') == true){
                    $post_id = $this->get_string_between($row_link_url,"story_fbid=","&");
                    return $post_id;

                } elseif(strpos($row_link_url,'groups') == true){
                    $post_id = $this->get_string_between($row_link_url,"permalink/","/");
                    return $post_id;
                    
                } elseif(strpos($row_link_url,'videos') == true){
                    if(strpos($row_link_url,'comment_id') == true){
                        $raw_result = $this->get_string_between($row_link_url,"videos/");
                        $post_id = strstr($raw_result, '/?comment_id', true);
                        return $post_id;
                    }elseif(strpos($row_link_url,'type') == true){
                        $raw_result = $this->get_string_between($row_link_url,"videos/","/?type");
                        $post_id = $this->get_string_between($raw_result,'/');
                        return $post_id;
                    
                    }else{
                        $raw_result = $this->get_string_between($row_link_url,"videos/");
                        $post_id = str_replace('/', '', $raw_result);
                        return $post_id;
                    }

                } elseif(strpos($row_link_url,'posts') == true){
                    if(strpos($row_link_url,'?__tn') == true || strpos($row_link_url,'comment_id') == true ){
                        $post_id = $this->get_string_between($row_link_url,"posts/","?");
                        return $post_id;
                    }else{
                        $post_id = $this->get_string_between($row_link_url,"posts/");
                        return $post_id;
                    }
                    
                } elseif(strpos($row_link_url,'photos') == true){
                    if(strpos($row_link_url,'type') == true){
                        $raw_result = $this->get_string_between($row_link_url,"photos/","/?type");
                        $post_id = $this->get_string_between($raw_result,"/");
                        return $post_id;
                    }else{
                        $raw_result = $this->get_string_between($row_link_url,"photos/");
                        $post_id = $this->get_string_between($raw_result,"/");
                        return $post_id;
                    } 

                } else{
                    if(strpos($row_link_url,'_') == true){
                        $post_id = $this->get_string_between($raw_result,"_");
                        return $post_id;
                    }else{
                        $this->setting_model->insert_fix_post_url($row_link_url);
                        return null;
                    }

                }
                return null;  
            } else if(strpos($row_link_url, 'tiktok') == true) {
                // https://www.tiktok.com/@sornkornyongnok/video/7140627983936621850
                if(strpos($row_link_url, '?') == true){
                    $expl_full_link = explode("?", $row_link_url);
                    $link_url = $expl_full_link[0];
                    $expl = explode("/", $link_url);
                    $post_id = end($expl);
                    return $post_id;
                } else {
                    $expl = explode("/", $row_link_url);
                    $post_id = end($expl);
                    return $post_id;
                }
            }             
            return null;
        }
        return null;
    }
}