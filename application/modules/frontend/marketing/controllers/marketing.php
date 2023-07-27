<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Marketing extends Frontend {

	var $module = "marketing";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("marketing_model");
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

		$category = $this->marketing_model->get_category();
		$company  = $this->setting_model->get_company_keyword();
		$sentiment = $this->marketing_model->get_sentiment($post);

		$viewdata = array();
		$viewdata['period']    = $period;
		$viewdata['module']    = $this->module;
		$viewdata['category']  = $category;
		$viewdata['company']   = $company;
		$viewdata['sentiment'] = $sentiment;
		$viewdata['custom_date']  = $this->master_model->get_custom_date();
		$viewdata['categoryData'] = $this->marketing_model->get_category_data($category);
		$viewdata['positiveData'] = $this->marketing_model->get_positive_data($category,$company,$sentiment);
		$viewdata['negativeData'] = $this->marketing_model->get_negative_data($category,$company,$sentiment);
		$viewdata['setting_allow'] = $this->authen->getSettingAllow(); 

		$this->template->set('inline_style', $this->load->view("marketing_style",null,true));
		$this->template->set('inline_script', $this->load->view("marketing_script",$viewdata,true));
		$this->template->build("marketing_view",$viewdata);
	}

	function get_feed()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;

		$viewdata = array();
		$viewdata['rowsdata'] = $this->marketing_model->get_feed($post);
		$this->load->view("marketing_list",$viewdata);
	}

	function add_category()
	{
		$config = $this->master_model->get_config();
		$viewdata = array();
		$viewdata['add_category'] = isset($config['add_category']) ? $config['add_category'] : 4;
		$viewdata['category'] = $this->marketing_model->get_category();
		$viewdata['module'] = $this->module;
		$this->load->view("add_category_view",$viewdata);
	}

	function cmdAddCategory()
	{
		$result = array();
		$post = $this->input->post();
		$config = $this->master_model->get_config();

		$row_category = count($this->marketing_model->get_category());
		$add_category = isset($config['add_category']) ? $config['add_category'] : 4;

		if($row_category>=$add_category) {
			$result["message"] = "คุณเพิ่ม Category ได้สูงสุด ".$add_category." Category เท่านั้น";
			$result["status"]  = false;
		} else if(!isset($post['category_name']) || trim($post['category_name'])=="") {
			$result["message"] = "กรุณากรอก Category";
			$result["status"]  = false;
		} else if($this->marketing_model->check_category($post['category_name'])) {
			$result["message"] = "ขออภัยคุณมี Category นี้แล้ว";
			$result["status"]  = false;
		} else {
			$category_id = $this->marketing_model->insert_category($post);
			$this->master_model->save_log("Add Category ".$post['category_name']);
			$result['category_id'] = $category_id;
			$result["status"]  = true;
		}
		echo json_encode($result);
	}

	function cmdDelCategory()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['category_id'])) {
			$result["status"]  = true;
			foreach($post['category_id'] as $val) {
				$name = $this->marketing_model->delete_category($val);
				$this->master_model->save_log("Delete Category ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Category ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}
	
}