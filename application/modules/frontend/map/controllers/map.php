<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends Frontend {

	var $module = "map";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("map_model");
		$this->load->model("setting/setting_model");
	}

	function index()
	{
		$this->view();
	}

	function test_func()
	{
		$this->map_model->get_keywordmap_feed();
	}

	function view()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;

		$viewdata = array();
		$viewdata['period']         = $period;
		$viewdata['custom_date']  = $this->master_model->get_custom_date();
		$viewdata['module']         = $this->module;
		$this->template->set('inline_style', $this->load->view("map_style",null,true));
		$this->template->set('inline_script', $this->load->view("map_script",$viewdata,true));
		$this->template->build("map_view",$viewdata);
	}

	function ajax_map_data()
	{
		// $viewdata['mapkeyword']     = $this->overview_model->get_keyword();
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$custom_date = $this->master_model->get_custom_date();
		$obj = get_custom_date($custom_date);
		$post['period'] = $period;
		$result = array();
		$result = $this->map_model->get_map_match($post);
		echo json_encode($result);

	}

	function get_keywordAndMention() //get keyword and mention
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$custom_date = $this->master_model->get_custom_date();
		$obj = get_custom_date($custom_date);
		$post['period'] = $period;
		$viewdata = array();
		$viewdata['rowsdata'] = $this->map_model->get_keywordAndMention($post);
			$sum_mention = 0;
			foreach($viewdata['rowsdata'] as $key => $value)
			{	
				$sum_mention += $value['mention'];
			}
		$viewdata['total_mention'] = $sum_mention;
		$this->load->view("map_keyword_list",$viewdata);

	}

	function get_keywordmap_feed()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;
		$viewdata = array();
		$viewdata['rowsdata'] = $this->map_model->get_keywordmap_feed($post);
		$this->load->view("map_keywordfeed_list",$viewdata);
	}

	function get_map()
	{
		$post = $this->input->get();
		$viewdata = array();
		$viewdata['mapdata'] = $this->map_model->get_map_match($post);
		$this->load->view("map_gen",$viewdata);

	}

}
