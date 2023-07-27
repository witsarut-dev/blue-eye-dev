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
		$viewdata['client_id'] = $this->setting_model->get_client_id();
		$viewdata['custom_date']  = $this->master_model->get_custom_date();
		$viewdata['client_graph'] = $this->analysis_model->get_client_graph();
		$viewdata['company_keyword'] = $this->setting_model->get_company_keyword();
		$viewdata['business_type'] = $this->analysis_model->get_business_type();

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
            $result['error']   = "graph_name";
		} else if($this->analysis_model->check_graph_name($post)) {
			$result["message"] = "ขออภัยคุณมี Graph Name นี้แล้ว";
			$result["status"]  = false;
            $result['error']   = "graph_name";
		} else if(!isset($post['graph_type']) || trim($post['graph_type'])=="") {
			$result["message"] = "กรุณาเลือก Graph Type";
			$result["status"]  = false;
            $result['error']   = "graph_type";
		} else if(!isset($post['graph_y']) || (trim($post['graph_y'])=="" && $post['graph_type']!="Table")) {
			$result["message"] = "กรุณาเลือก ตัวแปรเชิงปริมาณ Y";
			$result["status"]  = false;
            $result['error']   = "graph_y";
		} else if(!isset($post['graph_x']) || (trim($post['graph_x'])=="" && $post['graph_type']!="Table")) {
			$result["message"] = "กรุณาเลือก ตัวแปรเชิงคุณภาพ X";
			$result["status"]  = false;
            $result['error']   = "graph_x";
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

	function cmdExport($company_keyword_id = 0)
	{
		$this->load->library("PHPExcel");
		$period = $this->master_model->get_period();
		$post = $this->input->get();
		$post['period'] = $period;

		$time_name = "";
		if ($period == "Today") {
			$time_name = "daily report";
		} else if ($period == "1W"){
			$time_name = "weekly report";
		} else if ($period == "1M"){
			$time_name = "monthly report";
		} else if ($period == "3M"){
			$time_name = "3 months report";
		} else {
			$time_name = "custom report";
		}

		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="blueeye-'.$time_name.'.xlsx"');
		error_reporting(E_ALL);
		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		$objPHPExcel = new PHPExcel();

		if($company_keyword_id==0) {
			$company_keyword = $this->setting_model->get_company_keyword();
		} else {
			$com = $this->setting_model->get_company($company_keyword_id);
			$company_keyword[0] = $com;
		}

		$business_type = ucfirst($this->analysis_model->get_business_type());

		$client_id = $this->setting_model->get_client_id();

		// Condition check if category is allowed or not
		if ($this->authen->getCategoriesAllow()){
			foreach($company_keyword as $c_key=>$c_row) {
				// การตั้งค่าตารางเช่น ขนาดของ Column , หัวข้อของ Column  
	
				$objPHPExcel->createSheet($c_key);
				$objPHPExcel->setActiveSheetIndex($c_key);
				$objPHPExcel->getActiveSheet()->setTitle($c_row['company_keyword_name']);
	
				$objPHPExcel->getActiveSheet()->SetCellValue("A1","Channel");
				$objPHPExcel->getActiveSheet()->SetCellValue("B1","Type");
				$objPHPExcel->getActiveSheet()->SetCellValue("C1",$business_type);
				$objPHPExcel->getActiveSheet()->SetCellValue("D1","Category");
				$objPHPExcel->getActiveSheet()->SetCellValue("E1","Group Keyword");
				$objPHPExcel->getActiveSheet()->SetCellValue("F1","Account");
				$objPHPExcel->getActiveSheet()->SetCellValue("G1","Whom");
				$objPHPExcel->getActiveSheet()->SetCellValue("H1","Topic");
				$objPHPExcel->getActiveSheet()->SetCellValue("I1","Whom Tier");
				$objPHPExcel->getActiveSheet()->SetCellValue("J1","Topic Tier");
				$objPHPExcel->getActiveSheet()->SetCellValue("K1","Mention");
				$objPHPExcel->getActiveSheet()->SetCellValue("L1","Sentiment");
				$objPHPExcel->getActiveSheet()->SetCellValue("M1","Engagement");
				$objPHPExcel->getActiveSheet()->SetCellValue("N1","Like");
				$objPHPExcel->getActiveSheet()->SetCellValue("O1","Love");
				$objPHPExcel->getActiveSheet()->SetCellValue("P1","Wow");
				$objPHPExcel->getActiveSheet()->SetCellValue("Q1","Laugh");
				$objPHPExcel->getActiveSheet()->SetCellValue("R1","Sad");
				$objPHPExcel->getActiveSheet()->SetCellValue("S1","Angry");
				$objPHPExcel->getActiveSheet()->SetCellValue("T1","Care");
				$objPHPExcel->getActiveSheet()->SetCellValue("U1","Share");
				$objPHPExcel->getActiveSheet()->SetCellValue("V1","Comment");
				$objPHPExcel->getActiveSheet()->SetCellValue("W1","Date");
				$objPHPExcel->getActiveSheet()->SetCellValue("X1","Time");
				$objPHPExcel->getActiveSheet()->SetCellValue("Y1","URL");
				$objPHPExcel->getActiveSheet()->SetCellValue("Z1","Keyword");
	
				// Set the font color and style of the header row
				$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK));
				$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFont()->setName('Arial');
	
				// Set the fill color of the header row
				$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->getStartColor()->setARGB('DFE1E1');
				$objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->getStartColor()->setARGB('FEC89A');
				$objPHPExcel->getActiveSheet()->getStyle('D1:F1')->getFill()->getStartColor()->setARGB('DFE1E1');
				$objPHPExcel->getActiveSheet()->getStyle('G1:H1')->getFill()->getStartColor()->setARGB('FEC89A');
				$objPHPExcel->getActiveSheet()->getStyle('I1:Z1')->getFill()->getStartColor()->setARGB('DFE1E1');
	
				// Set the height of the header row
				$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
	
				// Set the text alignment and vertical alignment of the header row
				$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
				// Freeze the first row
				$objPHPExcel->getActiveSheet()->freezePane('A2');
	
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(70);
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
	
				// สิ้นสุดการตั้งค่าตาราง
				$post["company_keyword_id"] = $c_row['company_keyword_id'];
				
				// ไปดึงข้อมูลจากฐานข้อมูล มาเก็บไว้ในตัวแปร $rowsdata
				$rowsdata = $this->analysis_model->get_export($post);
	
				$rows = 2;
				// จัดการข้อมูล โดยนำข้อมูลไปใส่ในแต่ละ Column
				foreach($rowsdata as $k_row=>$v_row) {
	
					$objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					// $objPHPExcel->getActiveSheet()->getStyle('D:E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('G:J')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('L:X')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
					$objPHPExcel->getActiveSheet()->getStyle("A".$rows, $v_row[0])->getNumberFormat()->setFormatCode('0');
					$objPHPExcel->getActiveSheet()->SetCellValue("A".$rows, $v_row[0]);
					$objPHPExcel->getActiveSheet()->SetCellValue("B".$rows, $v_row[1]);
					$objPHPExcel->getActiveSheet()->SetCellValue("C".$rows, $v_row[2]);
					$objPHPExcel->getActiveSheet()->SetCellValue("D".$rows, $v_row[3]);
					$objPHPExcel->getActiveSheet()->SetCellValue("E".$rows, $v_row[4]);
					$objPHPExcel->getActiveSheet()->SetCellValue("F".$rows, $v_row[5]);
					$objPHPExcel->getActiveSheet()->SetCellValue("G".$rows, $v_row[6]);
					$objPHPExcel->getActiveSheet()->SetCellValue("H".$rows, $v_row[7]);
					$objPHPExcel->getActiveSheet()->SetCellValue("I".$rows, $v_row[8]);
					$objPHPExcel->getActiveSheet()->SetCellValue("J".$rows, $v_row[9]);
					$objPHPExcel->getActiveSheet()->SetCellValue("K".$rows, $v_row[10]);
					$objPHPExcel->getActiveSheet()->SetCellValue("L".$rows, $v_row[11]);
					$objPHPExcel->getActiveSheet()->SetCellValue("M".$rows, $v_row[12]);
					$objPHPExcel->getActiveSheet()->SetCellValue("N".$rows, $v_row[13]);
					$objPHPExcel->getActiveSheet()->SetCellValue("O".$rows, $v_row[14]);
					$objPHPExcel->getActiveSheet()->SetCellValue("P".$rows, $v_row[15]);
					$objPHPExcel->getActiveSheet()->SetCellValue("Q".$rows, $v_row[16]);
					$objPHPExcel->getActiveSheet()->SetCellValue("R".$rows, $v_row[17]);
					$objPHPExcel->getActiveSheet()->SetCellValue("S".$rows, $v_row[18]);
					$objPHPExcel->getActiveSheet()->SetCellValue("T".$rows, $v_row[19]);
					$objPHPExcel->getActiveSheet()->SetCellValue("U".$rows, $v_row[20]);
					$objPHPExcel->getActiveSheet()->SetCellValue("V".$rows, $v_row[21]);
					$objPHPExcel->getActiveSheet()->SetCellValue("W".$rows, $v_row[22]);
					$objPHPExcel->getActiveSheet()->SetCellValue("X".$rows, $v_row[23]);
					$objPHPExcel->getActiveSheet()->SetCellValue("Y".$rows, $v_row[24]);
					$objPHPExcel->getActiveSheet()->SetCellValue("Z".$rows, $v_row[25]);
	
					$rows++;
				}
	
				// สิ้นสุดการนำข้อมูลมาใส่ Column
	
			}
		} else {
			foreach($company_keyword as $c_key=>$c_row) {
				// การตั้งค่าตารางเช่น ขนาดของ Column , หัวข้อของ Column  
	
				$objPHPExcel->createSheet($c_key);
				$objPHPExcel->setActiveSheetIndex($c_key);
				$objPHPExcel->getActiveSheet()->setTitle($c_row['company_keyword_name']);
	
				$objPHPExcel->getActiveSheet()->SetCellValue("A1","Channel");
				$objPHPExcel->getActiveSheet()->SetCellValue("B1","Type");
				$objPHPExcel->getActiveSheet()->SetCellValue("C1",$business_type);
				$objPHPExcel->getActiveSheet()->SetCellValue("D1","Group Keyword");
				$objPHPExcel->getActiveSheet()->SetCellValue("E1","Account");
				$objPHPExcel->getActiveSheet()->SetCellValue("F1","Whom");
				$objPHPExcel->getActiveSheet()->SetCellValue("G1","Topic");
				$objPHPExcel->getActiveSheet()->SetCellValue("H1","Whom Tier");
				$objPHPExcel->getActiveSheet()->SetCellValue("I1","Topic Tier");
				$objPHPExcel->getActiveSheet()->SetCellValue("J1","Mention");
				$objPHPExcel->getActiveSheet()->SetCellValue("K1","Sentiment");
				$objPHPExcel->getActiveSheet()->SetCellValue("L1","Engagement");
				$objPHPExcel->getActiveSheet()->SetCellValue("M1","Like");
				$objPHPExcel->getActiveSheet()->SetCellValue("N1","Love");
				$objPHPExcel->getActiveSheet()->SetCellValue("O1","Wow");
				$objPHPExcel->getActiveSheet()->SetCellValue("P1","Laugh");
				$objPHPExcel->getActiveSheet()->SetCellValue("Q1","Sad");
				$objPHPExcel->getActiveSheet()->SetCellValue("R1","Angry");
				$objPHPExcel->getActiveSheet()->SetCellValue("S1","Care");
				$objPHPExcel->getActiveSheet()->SetCellValue("T1","Share");
				$objPHPExcel->getActiveSheet()->SetCellValue("U1","Comment");
				$objPHPExcel->getActiveSheet()->SetCellValue("V1","Date");
				$objPHPExcel->getActiveSheet()->SetCellValue("W1","Time");
				$objPHPExcel->getActiveSheet()->SetCellValue("X1","URL");
				$objPHPExcel->getActiveSheet()->SetCellValue("Y1","Keyword");
	
				// Set the font color and style of the header row
				$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK));
				$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFont()->setName('Arial');
	
				// Set the fill color of the header row
				$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->getStartColor()->setARGB('DFE1E1');
				$objPHPExcel->getActiveSheet()->getStyle('C1')->getFill()->getStartColor()->setARGB('FEC89A');
				$objPHPExcel->getActiveSheet()->getStyle('D1:E1')->getFill()->getStartColor()->setARGB('DFE1E1');
				$objPHPExcel->getActiveSheet()->getStyle('F1:G1')->getFill()->getStartColor()->setARGB('FEC89A');
				$objPHPExcel->getActiveSheet()->getStyle('H1:Y1')->getFill()->getStartColor()->setARGB('DFE1E1');
	
				// Set the height of the header row
				$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
	
				// Set the text alignment and vertical alignment of the header row
				$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
				// Freeze the first row
				$objPHPExcel->getActiveSheet()->freezePane('A2');
	
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(70);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
	
				// สิ้นสุดการตั้งค่าตาราง
				$post["company_keyword_id"] = $c_row['company_keyword_id'];
				
				// ไปดึงข้อมูลจากฐานข้อมูล มาเก็บไว้ในตัวแปร $rowsdata
				$rowsdata = $this->analysis_model->get_export($post);
	
				$rows = 2;
				// จัดการข้อมูล โดยนำข้อมูลไปใส่ในแต่ละ Column
				foreach($rowsdata as $k_row=>$v_row) {
	
					$objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					// $objPHPExcel->getActiveSheet()->getStyle('D:E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F:I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K:W')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
					$objPHPExcel->getActiveSheet()->getStyle("A".$rows, $v_row[0])->getNumberFormat()->setFormatCode('0');
					$objPHPExcel->getActiveSheet()->SetCellValue("A".$rows, $v_row[0]);
					$objPHPExcel->getActiveSheet()->SetCellValue("B".$rows, $v_row[1]);
					$objPHPExcel->getActiveSheet()->SetCellValue("C".$rows, $v_row[2]);
					$objPHPExcel->getActiveSheet()->SetCellValue("D".$rows, $v_row[3]);
					$objPHPExcel->getActiveSheet()->SetCellValue("E".$rows, $v_row[4]);
					$objPHPExcel->getActiveSheet()->SetCellValue("F".$rows, $v_row[5]);
					$objPHPExcel->getActiveSheet()->SetCellValue("G".$rows, $v_row[6]);
					$objPHPExcel->getActiveSheet()->SetCellValue("H".$rows, $v_row[7]);
					$objPHPExcel->getActiveSheet()->SetCellValue("I".$rows, $v_row[8]);
					$objPHPExcel->getActiveSheet()->SetCellValue("J".$rows, $v_row[9]);
					$objPHPExcel->getActiveSheet()->SetCellValue("K".$rows, $v_row[10]);
					$objPHPExcel->getActiveSheet()->SetCellValue("L".$rows, $v_row[11]);
					$objPHPExcel->getActiveSheet()->SetCellValue("M".$rows, $v_row[12]);
					$objPHPExcel->getActiveSheet()->SetCellValue("N".$rows, $v_row[13]);
					$objPHPExcel->getActiveSheet()->SetCellValue("O".$rows, $v_row[14]);
					$objPHPExcel->getActiveSheet()->SetCellValue("P".$rows, $v_row[15]);
					$objPHPExcel->getActiveSheet()->SetCellValue("Q".$rows, $v_row[16]);
					$objPHPExcel->getActiveSheet()->SetCellValue("R".$rows, $v_row[17]);
					$objPHPExcel->getActiveSheet()->SetCellValue("S".$rows, $v_row[18]);
					$objPHPExcel->getActiveSheet()->SetCellValue("T".$rows, $v_row[19]);
					$objPHPExcel->getActiveSheet()->SetCellValue("U".$rows, $v_row[20]);
					$objPHPExcel->getActiveSheet()->SetCellValue("V".$rows, $v_row[21]);
					$objPHPExcel->getActiveSheet()->SetCellValue("W".$rows, $v_row[22]);
					$objPHPExcel->getActiveSheet()->SetCellValue("X".$rows, $v_row[23]);
					$objPHPExcel->getActiveSheet()->SetCellValue("Y".$rows, $v_row[24]);
	
					$rows++;
				}
	
				// สิ้นสุดการนำข้อมูลมาใส่ Column
	
			}
		}

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
	}

	function cmdCSV($company_keyword_id = 0)
	{
		$this->load->helper("download");
		$com = $this->setting_model->get_company($company_keyword_id);
		$company_name = isset($com['company_keyword_name']) ? $com['company_keyword_name'] : null;

		$business_type = ucfirst($this->analysis_model->get_business_type());

		error_reporting(E_ALL);
		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		$period = $this->master_model->get_period();
		$post = $this->input->get();
		$post['period'] = $period;
		$eol  = "\n";
		$sep  = "\t";

		echo chr(255) . chr(254);

		$fields = array("Channel", "Type", $business_type, "Account", "Whom", "Topic", "Whom Tier", "Topic Tier", "Mention", "Sentiment", "Engagement", "Like", "Love", "Wow", "Laugh", "Sad", "Angry", "Care", "Share", "Comment", "Date", "Time", "URL", "Keyword");

		$csv = '"'.implode('"'.$sep.'"',$fields).'"'.$eol;
		$text_csv = mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');

		$post["company_keyword_id"] = $company_keyword_id;
		$rowsdata = $this->analysis_model->get_export($post);

		foreach($rowsdata as $k_row=>$v_row) {

			$post_link = $v_row[3];
			if($v_row[1]=="TW" && strpos($post_link,"https://")===false) {
				$post_link = "https://twitter.com/".$v_row[4]."/status/".$post_link;
			}
			
			$v_row[4] = str_replace('"',"'",$v_row[4]);
			$v_row[5] = str_replace('"',"'",$v_row[5]);
			$v_row[10] = str_replace('"',"'",$v_row[10]);
			$v_row[5] = str_replace(array('\r', '\n', '\t', '\v'), ' ', $v_row[5]);

			$csv = '"'.implode('"'.$sep.'"',$v_row).'"'.$eol;
			$text_csv .= mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
		}
		$file_name = 'BlueEyeExport'.$company_name.'-'.date("YmdHis").'.csv';
		force_download($file_name,$text_csv);
	}

	function cmdDeletePost()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['post_id'])) {
			foreach($post['post_id'] as $post_id) {
				$this->realtime_model->delete_post($post_id);
				$this->master_model->save_log("Delete Msg ID ".$post_id);
			}
			$result['status'] = true;
		} else {
			$result['message'] = "กรุณาเลือกรายการที่ต้องการลบอย่างน้อย 1 รายการ";
			$result['status'] = false;
		}
		echo json_encode($result);
	}

	function cmdBlockPost()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['post_id'])) {
			foreach($post['post_id'] as $post_id) {
				$post_user = $this->realtime_model->block_post($post_id);
				$this->master_model->save_log("Block User ".$post_user);
			}
			$result['status'] = true;
		} else {
			$result['message'] = "กรุณาเลือกรายการที่ต้องการลบอย่างน้อย 1 รายการ";
			$result['status'] = false;
		}
		echo json_encode($result);
	}

	function cmdHidePost()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['post_id'])) {
			foreach($post['post_id'] as $post_id) {
				$this->realtime_model->hide_post($post_id);
				// $this->master_model->save_log("Hide Msg ID ".$post_id);
			}
			$result['status'] = true;
		} else {
			$result['message'] = "กรุณาเลือกรายการที่ต้องการซ่อนอย่างน้อย 1 รายการ";
			$result['status'] = false;
		}
		echo json_encode($result);
	}
}