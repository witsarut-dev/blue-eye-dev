<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends Frontend {

	var $module = "report";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();

		$this->load->model("master_model");
		$this->load->model("report_model");
		$this->load->model("realtime/realtime_model");
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
		$viewdata['period']   = $period;
		$viewdata['module']   = $this->module;
		$viewdata['topShare'] = $this->report_model->get_top_share($post);
		$viewdata['topUser']  = $this->report_model->get_top_user($post);
		$viewdata['wordData'] = $this->report_model->get_word_cloud($post);
		$viewdata['custom_date']  = $this->master_model->get_custom_date();

		$this->template->set('inline_style', $this->load->view("report_style",null,true));
		$this->template->set('inline_script', $this->load->view("report_script",$viewdata,true));
		$this->template->build("report_view",$viewdata);
	}
	
	function get_feed()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;

		$viewdata = array();
		$viewdata['rowsdata'] = $this->report_model->get_feed($post);
		$this->load->view("report_list",$viewdata);
	}

}