<?php

/* load mx_controller class */
require APPPATH."third_party/MX/Controller.php";

class Backend extends MX_Controller {
	
	public static $view_data;

	public function __construct() {
		parent::__construct();

		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		$this->load->helper(array("db_pagination","do_upload"));
		$this->load->model("permission_model");

		self::$view_data["USER_ID"] = $this->session->userdata("USER_ID");
		self::$view_data["USERNAME"] = $this->session->userdata("USERNAME");
		self::$view_data["ROLES_ID"] = $this->session->userdata("ROLES_ID");
		self::$view_data["ASSIGNED"] = $this->session->userdata("ASSIGNED");

		// $module = $this->uri->segment(1);
		// $module = ($module=="authen") ? NULL : $module;
		// if(empty(self::$view_data["USER_ID"]) && !empty($module)):
		// 	show_404();
		// 	exit();
		// endif;
	}

	public function check_access($module = "" ,$action = "")
	{
		$this->permission_model->check_access($module,$action);
	}

    public function check_password($id = "" ,$password = "",&$message = "")
    {
        if(empty($id) && mb_strlen($password)<8) {
            $message = 'Please enter a "password" of 8 characters or more.';
            return false;
        } else if(trim($password)!="" && mb_strlen($password)<8) {
            $message = 'Please enter a "password" of 8 characters or more.';
            return false;
        } else {
            return true;
        }
    }

}