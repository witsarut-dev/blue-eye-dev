<?php
class Keyword_import extends Frontend 
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
        if(!isset($_FILES['file_import']['tmp_name'])) {
            show_404();
            die;
        }

        $result = array();
        $keyword = array();
        $post = $this->input->post();
        $config = $this->master_model->get_config();
        set_time_limit(0);

        $this->load->library("PHPExcel");
        $inputFileName = $_FILES['file_import']['tmp_name'];

        //  Read your Excel workbook
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0); 
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();

        $row_keyword = count($this->setting_model->get_keyword());
        $row_keyword = ($highestRow-1)+$row_keyword;
        $add_keyword = isset($config['add_keyword']) ? $config['add_keyword'] : 50;
        $keyword_file = array();

        if($row_keyword==0) {
            $result["message"] = "ไม่พบ Keyword ที่คุณต้องการเพิ่ม";
            $result["status"]  = false;
        } else if($row_keyword>$add_keyword) {
            $result["message"] = "คุณเพิ่ม Keyword ได้สูงสุด ".$add_keyword." Keyword เท่านั้น";
            $result["status"]  = false;
        } else if(!isset($post['group_keyword_id']) || trim($post['group_keyword_id'])=="") {
            $result["message"] = "กรุณาเลือก Group Keyword";
            $result["status"]  = false;
        } else {

            //  Loop through each row of the worksheet in turn
            for ($row = 2; $row <= $highestRow; $row++) { 
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
                $rowCell = $rowData[0];
                $keyword_name = trim($rowCell[0]);

                if($keyword_name=="") {
                    $result["message"] = "พบ Keyword ที่เป็นค่าว่าง กรุณาใส่ Keyword ให้ถูกต้อง";
                    $result["status"]  = false;
                    break;
                } else if(mb_strlen($keyword_name)<3) {
                    $result["message"] = "กรุณากรอก Keyword มากกว่า 3 ตัวอักษร";
                    $result["status"]  = false;
                    break;
                } else if($this->setting_model->check_keyword($keyword_name)) {
                    $result["message"] = "ขออภัยคุณมี Keyword \"{$keyword_name}\" แล้ว";
                    $result["status"]  = false;
                    break;
                } else if(in_array($keyword_name,$keyword)) {
                    $result["message"] = "ขออภัยคุณมี Keyword \"{$keyword_name}\" ซ้ำในไฟล์ import";
                    $result["status"]  = false;
                    break;
                } else {
                    array_push($keyword,$keyword_name);
                }

            }

            if(@$result["status"]!==false) {

                foreach($keyword as $keyword_name) {
                    $post['keyword_name'] = $keyword_name;
                    $this->setting_model->insert_keyword($post);
                    $this->master_model->save_log("Add Keyword ".$post['keyword_name']);
                }

                $result["message"] = "success";
                $result["keyword"] = $keyword;
                $result["status"]  = true;
            }

        }

        echo json_encode($result);
    }

}