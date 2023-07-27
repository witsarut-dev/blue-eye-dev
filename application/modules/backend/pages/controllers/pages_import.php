<?php
class pages_import extends Backend 
{

    public static $message = "";
    var $module = "pages";
    var $title = "Pages";

    function __construct()
    {
        parent::__construct();
        $this->load->model("pages_model");
        $this->load->model("pages_mongo_model","pages_mongo");
        $this->load->model("sys_logs_model");
    }

    function cmdSaveImport()
    {
        $pages = array();
        set_time_limit(0);

        $result = array(
            "total_rows"=>0,
            "success_rows"=>0,
            "error_rows"=>0,
            "duplicate_rows"=>0,
            "action"=>"import"
        );

        $this->sys_logs_model->save_log($this->module,"import");
        $imp_id = $this->pages_mongo->insert($result);

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

        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) { 
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
            $rowCell = $rowData[0];
            $page_id = $rowCell[0];
            $page_type = $rowCell[1];

            $save = array("page_id"=>$page_id,"page_type"=>$page_type);
            array_push($pages,$save);
        }

        echo json_encode(array("imp_id"=>$imp_id,"pages"=>$pages));
    }

    function cmdSavePage()
    {
        $post = $this->input->post();
        $page_id = $post['page_id'];
        $page_type = $post['page_type'];
        $total_rows = 1;
        $success_rows = 0;
        $error_rows = 0;
        $duplicate_rows = 0;

        if($page_id!="") {

            $num_rows = $this->db
                    ->where("page_id",$page_id)
                    ->where("page_type",$page_type)
                    ->where("sys_status","active")
                    ->get("pages")
                    ->num_rows();

            if($num_rows==0) {
                $data = array("page_id"=>$page_id,"page_type"=>$page_type);
                $data = $this->pages_mongo->get_page_info($data);
                if($data['page_id']!="") {
                    $this->pages_mongo->insert_mongodb($data);
                    $this->pages_model->insert($data);
                    $success_rows = 1;
                } else {
                    $error_rows = 1;
                }
            } else {
                $duplicate_rows = 1;
            }
        } else {
            $error_rows = 1;
        }

        $result = array(
            "total_rows"=>$total_rows,
            "success_rows"=>$success_rows,
            "error_rows"=>$error_rows,
            "duplicate_rows"=>$duplicate_rows
        );

        echo json_encode($result);
    }

    function cmdUpdateImport()
    {
        $post = $this->input->post();

        $result = array(
            "imp_id"=>$post['imp_id'],
            "total_rows"=>$post['total_rows'],
            "success_rows"=>$post['success_rows'],
            "error_rows"=>$post['error_rows'],
            "duplicate_rows"=>$post['duplicate_rows'],
        );

        $imp_id = $this->pages_mongo->update($result);
    }

}