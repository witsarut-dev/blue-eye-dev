<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends Frontend {

	var $module = "setting";

	function __construct()
	{
		parent::__construct();
		
		$this->authen->checkLogin();

		$this->load->model("master_model");
		$this->load->model("setting_model");
	}

	function index()
	{	
		$this->view();
	}

	function view()
	{	
		$config = $this->master_model->get_config();
		$viewdata = array();
		$viewdata['client'] = $this->setting_model->get_client();
		$viewdata['company'] = $this->setting_model->get_company_keyword();
		$viewdata['group_keyword'] = $this->setting_model->get_group_keyword();
		$viewdata['keyword'] = $this->setting_model->get_keyword();
		$viewdata['module']  = $this->module;
		$viewdata['setting_allow'] = $this->authen->getSettingAllow(); 
		
		$this->template->set('inline_style', $this->load->view("setting_style",null,true));
		$this->template->set('inline_script', $this->load->view("setting_script",null,true));
		$this->template->build("setting_view",$viewdata);
	}

	function cmdAddCompany()
	{
		$result = array();
		$post = $this->input->post();
		$config = $this->master_model->get_config();

		$row_competitor = count($this->setting_model->get_competitor_keyword());
		$add_competitor = isset($config['add_competitor']) ? $config['add_competitor'] : 2;

		if($row_competitor>=$add_competitor) {
			$result["message"] = "คุณสามารถเพิ่ม Competitor ได้สูงสุด ".$add_competitor." Company เท่านั้น";
			$result["status"]  = false;
		} else if(!isset($post['company_keyword_name']) || trim($post['company_keyword_name'])=="") {
			$result["message"] = "กรุณากรอก Company name";
			$result["status"]  = false;
		} else if($this->setting_model->check_company_keyword($post['company_keyword_name'])) {
			$result["message"] = "ขออภัยคุณมี Company name นี้แล้ว";
			$result["status"]  = false;
		} else if($post['company_keyword_type']!="Competitor") {
			$result["message"] = "คุณกรอก Company type ไม่ถูกต้อง";
			$result["status"]  = false;
		} else {
			$company_keyword_id = $this->setting_model->insert_company($post);
			$this->master_model->save_log("Add Company ".$post['company_keyword_name']);
			$result['company_keyword_id'] = $company_keyword_id;
			$result["status"]  = true;
		}
		echo json_encode($result);
	}

	function cmdDelCompany()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['company_keyword_id'])) {
			$result["status"]  = true;
			foreach($post['company_keyword_id'] as $val) {
				$company = $this->setting_model->get_company($val);
				if($company['company_keyword_type']=="") {
					$result["message"] = "คุณไม่สามารถลบ Company ของตัวคุณเองได้";
					$result["status"]  = false;
					break;
				} else {
					$name = $this->setting_model->delete_company($val);
					$this->setting_model->clean_keyword();
					$this->master_model->save_log("Delete Company ".$name);
				}
			}
		} else {
			$result["message"] = "กรุณาเลือก Company ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function cmdAddGroupKeyword()
	{
		$result = array();
		$post = $this->input->post();
		$config = $this->master_model->get_config();

		$row_group_keyword = count($this->setting_model->get_group_keyword());
		$add_group_keyword = isset($config['add_group_keyword']) ? $config['add_group_keyword'] : 10;

		if($row_group_keyword>=$add_group_keyword) {
			$result["message"] = "คุณสามารถเพิ่ม Group Keyword ได้สูงสุด ".$add_group_keyword." Group เท่านั้น";
			$result["status"]  = false;
		} else if(!isset($post['company_keyword_id']) || trim($post['company_keyword_id'])=="") {
			$result["message"] = "กรุณาเลือก Company";
			$result["status"]  = false;
		} else if(!isset($post['group_keyword_name']) || trim($post['group_keyword_name'])=="") {
			$result["message"] = "กรุณากรอก Group Keyword";
			$result["status"]  = false;
		} else if($this->setting_model->check_group_keyword($post['group_keyword_name'])) {
			$result["message"] = "ขออภัยคุณมี Group Keyword นี้แล้ว";
			$result["status"]  = false;
		} else {
			$group_keyword_id = $this->setting_model->insert_group_keyword($post);
			$this->master_model->save_log("Add Group Keyword ".$post['group_keyword_name']);
			$result['group_keyword_id'] = $group_keyword_id;
			$result['company_keyword_id'] = $post['company_keyword_id'];
			$result["status"]  = true;
		}
		echo json_encode($result);
	}

	function cmdDelGroupKeyword()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['group_keyword_id'])) {
			$result["status"]  = true;
			foreach($post['group_keyword_id'] as $val) {
				$name = $this->setting_model->delete_group_keyword($val);
				$this->setting_model->clean_keyword();
				$this->master_model->save_log("Delete Group Keyword ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Group Keyword ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}
	
	function cmdAddKeyword()
	{
		$result = array();
		$post = $this->input->post();
		$config = $this->master_model->get_config();

		$row_keyword = count($this->setting_model->get_keyword());
		$add_keyword = isset($config['add_keyword']) ? $config['add_keyword'] : 50;

		if($row_keyword>=$add_keyword) {
			$result["message"] = "คุณเพิ่ม Keyword ได้สูงสุด ".$add_keyword." Keyword เท่านั้น";
			$result["status"]  = false;
		} else if(!isset($post['group_keyword_id']) || trim($post['group_keyword_id'])=="") {
			$result["message"] = "กรุณาเลือก Group Keyword";
			$result["status"]  = false;
		} else if(!isset($post['keyword_name']) || trim($post['keyword_name'])=="") {
			$result["message"] = "กรุณากรอก Keyword";
			$result["status"]  = false;
		} else if(mb_strlen($post['keyword_name'])<3) {
			$result["message"] = "กรุณากรอก Keyword มากกว่า 3 ตัวอักษร";
			$result["status"]  = false;
		} else if($this->setting_model->check_keyword($post['keyword_name'])) {
			$result["message"] = "ขออภัยคุณมี Keyword นี้แล้ว";
			$result["status"]  = false;
		} else {
			$keyword_id = $this->setting_model->insert_keyword($post);
			$this->master_model->save_log("Add Keyword ".$post['keyword_name']);
			$result['keyword_id'] = $keyword_id;
			$result['group_keyword_id'] = $post['group_keyword_id'];
			$result["status"]  = true;
		}
		echo json_encode($result);
	}

	function cmdDelKeyword()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['keyword_id'])) {
			$result["status"]  = true;
			foreach($post['keyword_id'] as $val) {
				$name = $this->setting_model->delete_keyword($val);
				$this->setting_model->clean_keyword();
				$this->master_model->save_log("Delete Keyword ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Keyword ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function activity_log()
	{
		$viewdata = array();
		$viewdata['module'] = $this->module;
		$this->load->view("activity_log_view",$viewdata);
	}

	function activity_list()
	{
		$post = $this->input->get();
		$total_rows = 0;
		$rowsdata = $this->setting_model->get_activity_log($post,$total_rows);
		echo json_encode(array( "draw"=>$post['draw'],
			"recordsTotal"=>$total_rows,
			"recordsFiltered"=>$total_rows,
			"data"=>$rowsdata));
	}

	function block_user()
	{
		$viewdata = array();
		$viewdata['module'] = $this->module;
		$viewdata['rowsdata'] = $this->setting_model->get_block_user();
		$this->load->view("block_user_view",$viewdata);
	}

	function cmdUnblock()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['block_id'])) {
			$result["status"]  = true;
			foreach($post['block_id'] as $val) {
				$name = $this->setting_model->unblock($val);
				$this->master_model->save_log("Unblock User ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก User ที่ต้องการ Unblock";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}
}