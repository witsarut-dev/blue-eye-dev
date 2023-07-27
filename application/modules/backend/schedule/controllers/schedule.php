<?php
class Schedule extends Backend 
{
	public static $message = "";
	var $module = "schedule";
	var $title = "Monitor Schedule";

    function __construct()
    {
        parent::__construct();
        parent::$view_data["module"] = $this->module;
        parent::$view_data["title"] = $this->title;
        parent::$view_data["log_mode"] = "OFF";
		parent::$view_data["publish_mode"] = "OFF";
		
        $this->load->model("schedule_model");
        $this->load->model("sys_logs_model");
    }

    function index()
    {
    	$this->formList();
    }

	function formList()
	{
		parent::check_access($this->module,"view");
		parent::$view_data["schedule"] = $this->schedule_model->get_schedule();
		parent::$view_data['sch_clients'] = $this->schedule_model->get_client_by_color();
		$this->load->view("schedule_list_view",parent::$view_data);
	}

	function formManage()
	{
		parent::$view_data["clients"] = $this->schedule_model->get_client();
		parent::$view_data['sch_clients'] = $this->schedule_model->get_client_by_color();
		$this->load->view("schedule_form_view",parent::$view_data);
	}

	function cmdManage()
	{
		$post = $this->input->post();
		$this->sys_logs_model->save_log($this->module,"manage");

		$alert_message = $this->schedule_model->manage($post);
        $this->session->set_flashdata('ALERT_MESSAGE','You save "'.$alert_message.'" completed.');

		$status = true;
		$site_url = site_url("schedule");
		$result = array("status"=>$status
			,"message"=>self::$message
			,"url"=>$site_url);

		echo json_encode($result);
	}

	function getScheduleByClient()
	{
		$post = $this->input->post();
		$result = $this->schedule_model->get_schedule_by_client($post['client_id']);
		echo json_encode($result);
	}

}