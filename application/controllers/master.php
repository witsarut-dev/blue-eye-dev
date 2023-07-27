<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master extends Frontend {

	function __construct()
	{
		parent::__construct();
		$this->load->library("authen");
		$this->authen->checkLogin();
		
		$this->load->model("master_model");
	}
	
	public function cmdSavePeriod()
	{
		$post = $this->input->post();
		$this->master_model->insert_period($post);

		$params = "";
		if(@$post['graph_id']!="")   $params .= "&graph_id=".$post['graph_id'];
		if(@$post['graph_y']!="")    $params .= "&graph_y=".$post['graph_y'];
		if(@$post['graph_x']!="")    $params .= "&graph_x=".$post['graph_x'];
		if(@$post['graph_type']!="") $params .= "&graph_type=".$post['graph_type'];
		if(@$post['mediaType']!="") $params .= "&mediaType=".$post['mediaType'];
		if(@$post['other_keyword']!="") $params .= "&other_keyword=".$post['other_keyword'];
		if($params!="") $params = "/?".ltrim($params,"&");

		redirect(site_url($post['module'].$params));
	}

	public function cmdSaveMediaCom()
	{
		$post = $this->input->post();
		$this->master_model->insert_media_com($post);
		redirect(site_url($post['module']));
	}

	public function realtime_update_edit_sentiment()
	{
		$this->master_model->update_edit_new_sentiment($_POST['new_sentiment_edit'],$_POST['post_id']);
		// $this->master_model->update_edit_new_sentiment_daily($_POST['new_sentiment_edit'],$_POST['post_id']);
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */