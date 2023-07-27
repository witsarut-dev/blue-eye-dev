<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overview extends Frontend {

	var $module = "overview";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("overview_model");
		$this->load->model("setting/setting_model");
		$this->load->model("marketing/marketing_model");
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
		$custom_date = $this->master_model->get_custom_date();
		$obj = get_custom_date($custom_date);

		if(isset($post['media_com'])) {
			$media_com = $post['media_com'];
		} else {
			$media_com = $this->master_model->get_media_com();
		}

		$viewdata = array();
		$viewdata['keywordData']    = $this->overview_model->get_keyword_data($post);
		$viewdata['mediaData']      = $this->overview_model->get_media_data($post);
        $viewdata['mediaCount']     = $this->get_count_media($viewdata['mediaData']);
		$viewdata['totalData']      = $this->overview_model->get_total_data($post);
		$viewdata['sentimentData']  = $this->overview_model->get_sentiment_data($post);
		$viewdata['marketPosData']  = $this->overview_model->get_marketpos_data($post);
		$viewdata['mediaPosData']   = $this->overview_model->get_mediapos_data($post,$media_com);
		$viewdata['company']        = $this->setting_model->get_company_keyword();
		$viewdata['category']       = $this->marketing_model->get_category();
		$viewdata['client_id']      = $this->setting_model->get_client_id();
		$viewdata['period']         = $period;
		$viewdata['custom_date']    = $custom_date;
		$viewdata['custom_time']    = (@$obj['start']==@$obj['end']) ? true : false;
		$viewdata['media_com']      = $media_com;
		$viewdata['module']         = $this->module;

		$viewdata['graphSentimentData'] = $this->overview_model->get_graph_sentiment_data($post);

		$this->template->set('inline_script', $this->load->view("overview_script",$viewdata,true));
		$this->template->build("overview_view",$viewdata);
	}

	function filter_keyword()
	{
		$config = $this->master_model->get_config();
		$viewdata = array();
		$viewdata['choose_keyword'] = isset($config['choose_keyword']) ? $config['choose_keyword'] : 5;
		$viewdata['client_keyword'] = $this->overview_model->get_client_keyword();
		$viewdata['keyword']        = $this->overview_model->get_keyword();
		$viewdata['module']         = $this->module;
		$this->load->view("filter_keyword_view",$viewdata);
	}

	function filter_keyword_include_exclude()
	{
		$config = $this->master_model->get_config();
		$viewdata = array();
		$viewdata['client_keyword'] = $this->overview_model->get_client_keyword();
		$viewdata['keyword']        = $this->overview_model->get_keyword();
		$viewdata['module']         = $this->module;
		$this->load->view("filter_keyword_include_exclude_view", $viewdata);
	}

	function cmdChooseKeyword()
	{
		$post = $this->input->post();
		$this->overview_model->insert_client_keyword($post);
		redirect(site_url($this->module));
	}

	function ajax_keyword_data()
	{
		$result = array();
		$post   = $this->input->post();
		$keywordData = $this->overview_model->get_keyword_data($post);

		foreach($keywordData as $k_row=>$v_row) {
			$i = 0; 
			$max = count($v_row['data']);
			$data = array();
			foreach($v_row['data'] as $k2_row=>$v2_row) {
				if(($k2_row+3)>$max) array_push($data,$v2_row);
			}
			array_push($result,$data);
		}
		echo json_encode($result);
	}

	function ajax_media_data()
	{
		$result = array();
		$post   = $this->input->post();
		$result = $this->overview_model->get_media_data($post);
		echo json_encode($result);
	}

	function ajax_sentiment_data()
	{
		$result = array();
		$post   = $this->input->post();
		$result = $this->overview_model->get_sentiment_data($post);
		echo json_encode($result);
	}

	function ajax_total_data()
	{
		$result = array();
		$post   = $this->input->post();
		$result = $this->overview_model->get_total_data($post);
		echo json_encode($result);
	}

	function ajax_marketpos_data()
	{
		$result = array();
		$post   = $this->input->post();
		$result = $this->overview_model->get_marketpos_data($post);
		echo json_encode($result);
	}

	function ajax_mediapos_data()
	{
        $post   = $this->input->post();
		if(isset($post['media_com'])) {
            $media_com = $post['media_com'];
        } else {
            $media_com = $this->master_model->get_media_com();
        }

		$result = array();
		$result = $this->overview_model->get_mediapos_data($post,$media_com);
		echo json_encode($result);
	}

	function ajax_graph_sentiment_data()
	{
		$result = array();
		$post   = $this->input->post();
		$graphSentimentData = $this->overview_model->get_graph_sentiment_data($post);
		print_r($graphSentimentData);
		// die();
		foreach($graphSentimentData as $k_row=>$v_row) {
			$i = 0; 
			$max = count($v_row['data']);
			$data = array();
			// print_r($v_row['data']);
			foreach($v_row['data'] as $k2_row=>$v2_row) {
				if(($k2_row+3)>$max) array_push($data,$v2_row);
			}
			array_push($result,$data);
		}
		echo json_encode($result);
	}
	
    private function get_count_media($data)
    {
        $result = array("SM"=>0,"WB"=>0,"NW"=>0);
        if(isset($data['mediaData'])) {
            foreach($data['mediaData'] as $key=>$val) {
                if($val['drilldown']['categories'][0]=='WB') {
                    $result['WB'] = $val['y'];
                } else if($val['drilldown']['categories'][0]=='NW') {
                    $result['NW'] = $val['y'];
                } else {
                    $result['SM'] += $val['y'];
                }
            }
        }
        $result['SM'] = number_format($result['SM']);
        $result['WB'] = number_format($result['WB']);
        $result['NW'] = number_format($result['NW']);
        return $result;
    }

	function ajax_group_keyword_list()
	{
		$post   = $this->input->post();
		$data_test = $this->overview_model->get_group_keyword_list($post);
		print $data_test;
	}
}