<?php
class Sys_logs_model extends CI_Model {

    var $module   = '';
    var $action = '';
    var $message    = '';
	var $session_id = '';
	var $ip_address = '';
	var $user_agent = '';
	var $lastupdate = '';
	var $updateby = '';

    function __construct()
    {
        parent::__construct();
    }
    
	function save_log($module = "",$action = "")
	{	
		$USERNAME = $this->session->userdata("USERNAME");
		if(in_array($action,array("login","logout"))) :
			if($USERNAME=="") {
				$USERNAME = $this->input->post("username");
				$message = '"'.$action.'" unsuccessful by "'.$USERNAME.'"' ;
			} else {
				$message = '"'.$action.'" successful by "'.$USERNAME.'"' ;
			}
		else:
			$message = '"'.$module.'" item was '.$action.' by "'.$USERNAME.'"' ;
		endif;

		$this->module   = $module;
		$this->action = $action;
		$this->message    = $message." time ".date("d/m/Y H:i");
		$this->session_id = $this->session->userdata('session_id');
		$this->ip_address =  $this->getUserIP();
		$this->user_agent =  $this->session->userdata('user_agent');
		$this->lastupdate = date("Y-m-d H:i:s");
		$this->updateby = $this->session->userdata("USER_ID");
		$this->db->insert("sys_logs",$this);
	}

	function getUserIP()
	{
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];

	    if(filter_var($client, FILTER_VALIDATE_IP))
	    {
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP))
	    {
	        $ip = $forward;
	    }
	    else
	    {
	        $ip = $remote;
	    }

	    return $ip;
	}

	function get_rows($post = array())
    {
		if(@$post["keyword"]!="") $this->db->where("(sys_logs.action LIKE '%".$post["keyword"]."%' OR sys_logs.module LIKE '%".$post["keyword"]."%')");
    	$rows = $this->db->count_all_results("sys_logs");
		return $rows;
    }

    function get_rows_publish($post = array())
    {
    	return 0;
	}

	function get_rows_modified($post = array())
    {
    	return 0;
	}

	function get_rows_unpublish($post = array())
    {
    	return 0;
	}

	function get_page($option = array(),$start,$end)
	{
		$post = $option["post"];

		if(@$post["keyword"]!="") $this->db->where("(sys_logs.action LIKE '%".$post["keyword"]."%' OR sys_logs.module LIKE '%".$post["keyword"]."%')");
		$sorting = "";
		if(@$option["sorting"]=="update_name") {
			$sorting = "updated.username";
		} else if(@$option["sorting"]=="create_name") {
			$sorting = "created.username";
		} else if(strpos(@$option["sorting"],".")!==false) {
			$sorting = $option["sorting"];
		} else {
			$sorting = "sys_logs.".$option["sorting"];
		}

		$sql =  $this->db->select("sys_logs.*")
				->select("updated.username AS update_name")
				->join("sys_users updated","updated.sys_users_id = sys_logs.updateby","LEFT")
				->order_by($sorting,@$option["orderby"])
				->from("sys_logs")
				->query_string();
		$newsql = get_page($sql,$this->db->dbdriver,$start,$end);
		return  $this->db->query($newsql)->result_array();
	}

}
?>