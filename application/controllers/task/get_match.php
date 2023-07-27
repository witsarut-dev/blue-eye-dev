<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class get_match extends MX_Controller {

	var $tb_match = "own_match";
    var $tb_key_match  = "own_key_match";
    var $tb_cate_match = "own_cate_match";

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);

        $this->mongo = new MongoClient(MONGO_CONNECTION);
        $this->mongodb = $this->mongo->blue_eye;
	}

    function index($type = "1") 
    {
        //$this->select_type($type);
    }

	function select_type()
	{	
        $get = $this->input->get();
        $type = (@$get['type']=="") ? 1 : $get['type'];
        $page = (@$get['page']=="") ? 1 : $get['page'];

        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
		$tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";
        $tb_key_match  = ($company_keyword_type=="Company") ? "own_key_match" : "competitor_key_match";
        $tb_cate_match = ($company_keyword_type=="Company") ? "own_cate_match" : "competitor_cate_match";

		$rowsdata = $this->get_match_data($tb_match,$page);

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=".$tb_match.".xls");
        error_reporting(E_ALL);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $this->load->library("PHPExcel");

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getActiveSheet()->SetCellValue("A1","#");
        $objPHPExcel->getActiveSheet()->SetCellValue("B1","SOURCE ID");
        $objPHPExcel->getActiveSheet()->SetCellValue("C1","Msg ID");

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);

        $rows = 2;
		foreach($rowsdata as $k_row=>$v_row) {

            if($v_row['match_type']=="Feed") {
                $feed = $this->get_mongo_feed($v_row['msg_id']);
            } else {
                $feed = $this->get_mongo_comment($v_row['msg_id']);
            }

            if(!isset($feed['msg_id'])) {

                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    )
                );

                $objPHPExcel->getActiveSheet()->getStyle("B".$rows)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle("B".$rows,$v_row['msg_id'])->getNumberFormat()->setFormatCode('0');
                $objPHPExcel->getActiveSheet()->SetCellValue("A".$rows,($k_row+1));
                $objPHPExcel->getActiveSheet()->SetCellValue("B".$rows,$v_row['msg_id']);
                $objPHPExcel->getActiveSheet()->SetCellValue("C".$rows,$v_row['sourceid']);
                $rows++;
            }
		}

        $this->mongo->close();

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
	}

	function get_match_data($tb_match = "own_match",$page = 1)
	{
        $end_page = 100000;
        $start_page = ($page-1) * $end_page; 
        $this->db->limit($end_page,$start_page);

		$rowsdata = $this->db
					->select("{$tb_match}_id AS match_id,match_type,msg_id,sourceid")
                    ->order_by("{$tb_match}_id","ASC")
					->get("{$tb_match}")
					->result_array();

		return $rowsdata;
	}

    function get_feed_type($rowsdata = array())
    {
        $arrFeed = array();
        $arrComment = array();
        foreach($rowsdata as $k_row=>$v_row) {
            if($v_row['match_type']=="Feed") {
                array_push($arrFeed,$v_row['msg_id']);
            } else {
                array_push($arrComment,$v_row['msg_id']);
            }
        }
        return array("arrFeed"=>$arrFeed,"arrComment"=>$arrComment);
    }

    function get_mongo_feed($msg_id = array())
    {
        $result = array();
        if(count($msg_id)>0) {
            $collection = $this->mongodb->selectCollection("Feed");
            $query = array('_id' => $msg_id);
            $cursor = $collection->find($query);
           
            foreach($cursor as $k_row=>$v_row) {
                $result = array("msg_id"=>@$v_row['_id']);
            }
        }
        return $result;
    }

    function get_mongo_comment($msg_id = array())
    {
        $result = array();
        if(count($msg_id)>0) {
            $collection = $this->mongodb->selectCollection("Comment");
            $query = array('_id' => $msg_id);
            $cursor = $collection->find($query);
           
            foreach($cursor as $k_row=>$v_row) {
                $result = array("msg_id"=>@$v_row['_id']);
            }
        }
        return $result;
    }

    function get_test_sentiment()
    {
        $text = $this->input->get("text");
        $sentiment = get_sentiment_api($text);
        var_dump($sentiment);
    }

}