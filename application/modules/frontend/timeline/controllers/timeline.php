<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Timeline extends Frontend {

	var $module = "timeline";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("timeline_model");
		$this->load->model("setting/setting_model");
        $this->load->model("overview/overview_model");
        set_time_limit(0);
	}

	function index()
	{	
		$this->view();
	}

	function view($timeline_id = 0)
	{	
        $post   = $this->input->post();
        
		$viewdata = array();
		$viewdata['module']  = $this->module;
        $viewdata['client_timeline'] = $this->timeline_model->get_client_timeline();
        $viewdata['timeline_id'] = $timeline_id;
		$this->template->set('inline_style', $this->load->view("timeline_style",null,true));
		$this->template->set('inline_script', $this->load->view("timeline_script",$viewdata,true));
		$this->template->build("timeline_view",$viewdata);
	}

    function open_timeline()
    {
        $post = $this->input->post();
        if($post) {
            $timeline_id = $post['timeline_id'];
            $rec = $this->timeline_model->get_timeline_id($timeline_id);
            if(!isset($rec['timeline_id'])) {
                $result["message"] = "ไม่พบข้อมูล Timeline ที่ต้องการ";
                $result["status"]  = false;
            } else {
                $obj = $this->timeline_model->get_timeline_date($rec);
                $start_date = $obj['start_date'];
                $end_date = $obj['end_date'];
                
                $timeline_id = $rec['timeline_id'];
                $result['timeline_id'] = $rec['timeline_id'];
                $result['timeline_name'] = $rec['timeline_name'];
                $result['keyword_name'] = $rec['keyword_name'];
                $result['timeline_date'] = $start_date." - ".$end_date;
                $result["status"] = true;
            }
            echo json_encode($result);
        }
    }

    function get_timeline()
    {
        $post = $this->input->post();
        $result = array();
        $timeline_list = array();
        $start_date = "";
        $end_id = "";
        if($post) {
            $timeline_id = $post['timeline_id'];
            $rec = $this->timeline_model->get_timeline_id($timeline_id);
            $result["status"]  = ($rec['timeline_status']==0) ? false : true;
            if($result['status']) {
                $timeline_list = $this->timeline_model->get_timeline_list($timeline_id,$start_date,$end_id);
            }
            $result["start_date"] = $start_date;
            $result["end_id"]   = $end_id;
            $result["timeline_list"] = $timeline_list;
        }
        echo json_encode($result);
    }

    function get_feed($timeline_id =0)
    {
        $post = $this->input->get();
        if($post) {
            $viewdata = array();
            $viewdata['timeline_id'] = $timeline_id;
            $viewdata['msg_date']    = urldecode($post['msg_date']);
            $viewdata['start_date']  = urldecode($post['start_date']);
            $viewdata['end_id']      = $post['end_id'];
            $this->load->view("timeline_list_view",$viewdata);
        }
    }

    function get_feed_list()
    {
        $result = array();
        $post = $this->input->post();
        if($post) {
            $result = $this->timeline_model->get_feed_list($post);
        }
        echo json_encode($result);
    }

    function cmdAddTimeline()
    {
        $result = array();
        $post = $this->input->post();
        $message = "";

        if(!isset($post['timeline_name']) || trim($post['timeline_name'])=="") {
            $result["message"] = "กรุณากรอก Topic";
            $result["status"]  = false;
            $result['error']   = "timeline_name";
        } else if($this->timeline_model->check_timeline_name($post)) {
            $result["message"] = "ขออภัยคุณมี Topic นี้แล้ว ";
            $result["status"]  = false;
            $result['error']   = "timeline_name";
        } else if(!isset($post['keyword_name']) || trim($post['keyword_name'])=="") {
            $result["message"] = "กรุณากรอก Keyword";
            $result["status"]  = false;
            $result['error']   = "keyword_name";
        } else if(mb_strlen($post['keyword_name'])<3) {
            $result["message"] = "กรุณากรอก Keyword มากกว่า 3 ตัวอักษร";
            $result["status"]  = false;
            $result['error']   = "keyword_name";
        } else if(!isset($post['timeline_date']) || trim($post['timeline_date'])=="") {
            $result["message"] = "กรุณากรอก Period";
            $result["status"]  = false;
            $result['error']   = "timeline_date";
        } else if($this->timeline_model->check_keyword_name($post)) {
            $result["message"] = "ขออภัยคุณมี Keyword และ Period นี้แล้ว ";
            $result["status"]  = false;
            $result['error']   = "timeline_date";
        } else if($this->timeline_model->check_timeline_max($add_timeline_post) && @$post['timeline_id']=="") {
            $result["message"] = "ขออภัยคุณเพิ่มข้อมูลเกิน ".$add_timeline_post." ครั้งแล้ว<br />คุณจะสามารถเพิ่มข้อมูลได้อีกครั้งในเดือนถัดไป";
            $result["status"]  = false;
            $result['error']   = "timeline_max";
        } else {
            if(!isset($post['timeline_id']) || $post['timeline_id']=="") {
                $timeline_id = $this->timeline_model->insert_timeline($post);
                $this->master_model->save_log("Add Timeline ".$post['timeline_name']);
                $result['action'] = "Add";
            } else {
                $timeline_id = $this->timeline_model->update_timeline($post);
                $this->master_model->save_log("Edit Timeline ".$post['timeline_name']);
                $result['action'] = "Edit";
            }
            $rec = $this->timeline_model->get_timeline_id($timeline_id);
            $result['timeline_id'] = $timeline_id;
            $result["status"]  = true;
        }
        echo json_encode($result);
    }

    function cmdDelTimeline()
    {
        $result = array();
        $post = $this->input->post();
        if(isset($post['timeline_id'])) {
            $result["status"]  = true;
            $name = $this->timeline_model->delete_timeline($post['timeline_id']);
            $this->master_model->save_log("Delete Timeline ".$name);
        } else {
            $result["message"] = "กรุณาเลือก Timeline ที่ต้องการลบ";
            $result["status"]  = false;
        }
        echo json_encode($result);
    }
	
}