<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Frontend {

	function __construct()
	{
		parent::__construct();

		$this->load->model("login_model");
		$this->load->library("authen");
	}

	function index()
	{	
		$this->view();
	}

	function login()
	{
		$this->view("login");
	}

	function view()
	{	
		if($this->authen->getLogin()) {
			redirect(site_url("overview"));
		} else {
			$viewdata = array();
			$this->load->view("login_view",$viewdata);
		}
	}

	function cmdLogin()
	{
		$result = array();
		$post = $this->input->post();

		$result = $this->login_model->get_authen(@$post['username'],@$post['password']);

		if(@$result['client_id'] !="") {
			if(isset($post['remember'])) {
        		$this->login_model->set_login($result['username'],$result['client_id'],true);
			} else {
				$this->login_model->set_login($result['username'],$result['client_id'],false);
			}
			$result['status'] = true;
		} else {
			if($result['status']) {
				$result['message'] = "Username or Password is incorrect.";
			} 
			$this->clean_login();
			$result['status'] = false;
		}
		echo json_encode($result);
	}

	function cmdAdmin()
	{
		$result = array();
		$post = $this->input->post();

		if($this->session->userdata("USER_ID")!="") {
			$result = $this->login_model->get_id(@$post['client_id']);
			$this->login_model->set_login(@$result['username'],@$result['client_id'],false);
			$result['status'] = true;
		}
		echo json_encode($result);
	}

	function cmdLogout()
	{
		$this->clean_login();
		redirect(site_url());
	}

	function clean_login()
	{
		delete_cookie("BE_ACCESS_TOKEN");
		delete_cookie("BE_CLIENT_ID");
		delete_cookie("BE_USERNAME");
		delete_cookie("BE_COMPANY");
	}

	function check_login_timeout()
	{
		$result = array();
		if($this->authen->getLogin()) {
			$result['status'] = true;
		} else {
			$result['status'] = false;
		}
		echo json_encode($result);
	}
}