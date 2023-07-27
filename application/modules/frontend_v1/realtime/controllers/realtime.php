<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Realtime extends Frontend {

	var $module = "realtime";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();

		$this->load->model("master_model");
		$this->load->model("realtime_model");
		$this->load->model("setting/setting_model");
		$this->load->model("overview/overview_model");
	}

	function index()
	{	
		$this->view();
	}

	function view()
	{	
		$viewdata = array();
		$viewdata['module']  = $this->module;
		$viewdata['realtime_group_keyword'] = $this->master_model->get_meta("realtime_group_keyword");
		$viewdata['realtime_keyword'] = $this->master_model->get_meta("realtime_keyword");
		$viewdata['group_keyword']    = $this->overview_model->get_group_keyword();
		$viewdata['keyword']          = $this->overview_model->get_keyword();
		$this->template->set('inline_style', $this->load->view("realtime_style",null,true));
		$this->template->set('inline_script', $this->load->view("realtime_script",null,true));
		$this->template->build("realtime_view",$viewdata);
	}

	function get_feed()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;

		$viewdata = array();
		$viewdata['rowsdata'] = $this->realtime_model->get_feed($post);
		$this->load->view("realtime_list",$viewdata);
	}

	function add_feed()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;

		if(isset($post["last_time"])) {
			$viewdata = array();
			$viewdata['rowsdata'] = $this->realtime_model->add_feed($post);
			$this->load->view("realtime_list",$viewdata);
		}
	}

	function filter_realtime()
	{
		$viewdata = array();
		$viewdata['realtime_group_keyword'] = $this->master_model->get_meta("realtime_group_keyword");
		$viewdata['realtime_keyword'] = $this->master_model->get_meta("realtime_keyword");
		$viewdata['group_keyword']    = $this->setting_model->get_group_keyword();
		$viewdata['keyword'] = $this->setting_model->get_keyword();
		$viewdata['module']  = $this->module;
		$this->load->view("filter_realtime_view",$viewdata);
	}
	
	function post_detail($post_id = 0,$com_id = 0)
	{
		$viewdata = array();
		$viewdata['post_detail']    = $this->realtime_model->get_post_detail($post_id,$com_id);
		$viewdata['module']   = $this->module;
		$this->load->view("post_detail_view",$viewdata);
	}

	function get_comment()
	{
		$post = $this->input->post();
		$viewdata['post']     = $post;
		$viewdata['comments'] = $this->realtime_model->get_comments($post["post_id"],$post['post_rows']);
		$this->load->view("comment_view",$viewdata);
	}

	function cmdFilterKeyword()
	{
		$post = $this->input->post();
		$this->realtime_model->insert_filter_keyword($post);
		redirect(site_url($this->module));
	}

	function cmdDeletePost()
	{
		$result['status'] = true;
		$post = $this->input->post();
		$post_id = $post['post_id'];
		$this->realtime_model->delete_post($post_id);
		$this->master_model->save_log("Delete Msg ID ".$post_id);

		echo json_encode($result);
	}

	function cmdBlockPost()
	{
		$result['status'] = true;
		$post = $this->input->post();
		$post_id = $post['post_id'];
		$post_user = $this->realtime_model->block_post($post_id);
		$this->master_model->save_log("Block User ".$post_user);
		
		echo json_encode($result);
	}
	
}