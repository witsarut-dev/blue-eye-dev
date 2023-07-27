<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analysis extends Frontend {

	var $module = "analysis";

	function __construct()
	{
		parent::__construct();

		$this->load->model("master_model");
		$this->load->model("analysis_model");
		$this->load->model("realtime/realtime_model");
		$this->load->model("setting/setting_model");
	}

	function index()
	{	
		$this->view();
	}

	function view()
	{	
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;

		$viewdata = array();
		$viewdata['period']    = $period;
		$viewdata['module']    = $this->module;
		$viewdata['custom_date']  = $this->master_model->get_custom_date();
		$viewdata['client_graph'] = $this->analysis_model->get_client_graph();

		$this->template->set('inline_style', $this->load->view("analysis_style",null,true));
		$this->template->set('inline_script', $this->load->view("analysis_script",$viewdata,true));
		$this->template->build("analysis_view",$viewdata);
	}

	function open_graph($graph_id = 0)
	{
		$rec = $this->analysis_model->get_graph_id($graph_id);
		$params  = "?graph_id=".@$rec['graph_id'];
		$params .= "&graph_y=".@$rec['graph_y'];
		$params .= "&graph_x=".@$rec['graph_x'];
		$params .= "&graph_type=".@$rec['graph_type'];
		$site_url = site_url($this->module."/".$params);
		redirect($site_url);
	}

	function cmdAddGraph()
	{
		$result = array();
		$post = $this->input->post();

		if(!isset($post['graph_name']) || trim($post['graph_name'])=="") {
			$result["message"] = "กรุณากรอก Graph Name";
			$result["status"]  = false;
		} else if($this->analysis_model->check_graph_name($post)) {
			$result["message"] = "ขออภัยคุณมี Graph Name นี้แล้ว";
			$result["status"]  = false;
		} else if(!isset($post['graph_type']) || trim($post['graph_type'])=="") {
			$result["message"] = "กรุณาเลือก Graph Type";
			$result["status"]  = false;
		} else if(!isset($post['graph_y']) || (trim($post['graph_y'])=="" && $post['graph_type']!="Table")) {
			$result["message"] = "กรุณาเลือก ตัวแปรเชิงปริมาณ Y";
			$result["status"]  = false;
		} else if(!isset($post['graph_x']) || (trim($post['graph_x'])=="" && $post['graph_type']!="Table")) {
			$result["message"] = "กรุณาเลือก ตัวแปรเชิงคุณภาพ X";
			$result["status"]  = false;
		} else {
			if(!isset($post['graph_id']) || $post['graph_id']=="") {
				$result['action'] = "Add";
				$graph_id = $this->analysis_model->insert_graph($post);
				$this->master_model->save_log("Add Graph ".$post['graph_name']);
			} else {
				$result['action'] = "Edit";
				$graph_id = $this->analysis_model->update_graph($post);
				$this->master_model->save_log("Edit Graph ".$post['graph_name']);
			}
			$result['graph_id'] = $graph_id;
			$result["status"]  = true;
		}
		echo json_encode($result);
	}

	function cmdDelGraph()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['graph_id'])) {
			$result["status"]  = true;
			$name = $this->analysis_model->delete_graph($post['graph_id']);
			$this->master_model->save_log("Delete Graph ".$name);
		} else {
			$result["message"] = "กรุณาเลือก Graph ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function getChartPie()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;
		$rec    = $this->analysis_model->get_graph_id(@$post['graph_id']);
		$result = $this->analysis_model->getChartPie($post);
		echo json_encode(array("title"=>@$rec['graph_name'],"series"=>$result));
	}

	function getChartBar()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;
		$rec    = $this->analysis_model->get_graph_id(@$post['graph_id']);
		$result = $this->analysis_model->getChartBar($post);
		echo json_encode(array(
			"title"=>@$rec['graph_name'],
			"ytitle"=>@$post['graph_y'],
			"categories"=>$result['categories'],
			"series"=>$result['series']));
	}

	function getChartLine()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;
		$rec    = $this->analysis_model->get_graph_id(@$post['graph_id']);
		$result = $this->analysis_model->getChartLine($post);
		echo json_encode(array(
			"title"=>@$rec['graph_name'],
			"ytitle"=>@$post['graph_y'],
			"categories"=>$result['categories'],
			"series"=>$result['series']));
	}

	function get_table_list()
	{
		$period = $this->master_model->get_period();
		$post = $this->input->get();
		$post['period'] = $period;
		$total_rows = 0;
		$rowsdata = $this->analysis_model->get_table_list($post,$total_rows);

		echo json_encode(array( "draw"=>$post['draw'],
			"recordsTotal"=>$total_rows,
			"recordsFiltered"=>$total_rows,
			"data"=>$rowsdata));
	}

	function cmdExport()
	{
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="BlueEyeExport-'.date("YmdHis").'.xlsx"');
		error_reporting(E_ALL);
		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		$this->load->library("PHPExcel");
		$period = $this->master_model->get_period();
		$post = $this->input->get();
		$post['period'] = $period;

		$objPHPExcel = new PHPExcel();
		$company_keyword = $this->setting_model->get_company_keyword();

		foreach($company_keyword as $c_key=>$c_row) {

			$objPHPExcel->createSheet($c_key);
			$objPHPExcel->setActiveSheetIndex($c_key);
			$objPHPExcel->getActiveSheet()->setTitle($c_row['company_keyword_name']);

			$objPHPExcel->getActiveSheet()->SetCellValue("A1","Msg ID");
			$objPHPExcel->getActiveSheet()->SetCellValue("B1","Media Type");
			$objPHPExcel->getActiveSheet()->SetCellValue("C1","Feed Type");
			$objPHPExcel->getActiveSheet()->SetCellValue("D1","Url");
			$objPHPExcel->getActiveSheet()->SetCellValue("E1","Author");
			$objPHPExcel->getActiveSheet()->SetCellValue("F1","Body");
			$objPHPExcel->getActiveSheet()->SetCellValue("G1","Share");
			$objPHPExcel->getActiveSheet()->SetCellValue("H1","Like");
			$objPHPExcel->getActiveSheet()->SetCellValue("I1","Time");
			$objPHPExcel->getActiveSheet()->SetCellValue("J1","Sentiment");
			$objPHPExcel->getActiveSheet()->SetCellValue("K1","Keyword");

			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);

			$post["company_keyword_id"] = $c_row['company_keyword_id'];
			$rowsdata = $this->analysis_model->get_export($post);

			$rows = 2;
			foreach($rowsdata as $k_row=>$v_row) {

				$post_link = $v_row[3];
				if($v_row[1]=="TW" && strpos($post_link,"https://")===false) {
					$post_link = "https://twitter.com/".$v_row[4]."/status/".$post_link;
				}

				$objPHPExcel->getActiveSheet()->getStyle("A".$rows,$v_row[0])->getNumberFormat()->setFormatCode('0');
				$objPHPExcel->getActiveSheet()->SetCellValue("A".$rows,$v_row[0]);
				$objPHPExcel->getActiveSheet()->SetCellValue("B".$rows,$v_row[1]);
				$objPHPExcel->getActiveSheet()->SetCellValue("C".$rows,$v_row[2]);
				$objPHPExcel->getActiveSheet()->SetCellValue("D".$rows,$post_link);
				$objPHPExcel->getActiveSheet()->SetCellValue("E".$rows,$v_row[4]);
				$objPHPExcel->getActiveSheet()->SetCellValue("F".$rows,$v_row[5]);
				$objPHPExcel->getActiveSheet()->SetCellValue("G".$rows,$v_row[6]);
				$objPHPExcel->getActiveSheet()->SetCellValue("H".$rows,$v_row[7]);
				$objPHPExcel->getActiveSheet()->SetCellValue("I".$rows,$v_row[8]);
				$objPHPExcel->getActiveSheet()->SetCellValue("J".$rows,$v_row[9]);
				$objPHPExcel->getActiveSheet()->SetCellValue("K".$rows,$v_row[10]);
			    $rows++;
			}

		}

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
	}
	
}