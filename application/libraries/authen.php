<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Authen {

	var $CI;
	var $username;
	var $client_id;
	var $access_token;

	function __construct()
    {
       	$this->CI =& get_instance();
    	$this->CI->load->library('session');
    	$this->CI->load->helper('cookie');


    	$this->username     = get_cookie("BE_USERNAME");
    	$this->client_id    = get_cookie("BE_CLIENT_ID");
    	$this->access_token = get_cookie("BE_ACCESS_TOKEN");
    }

	function checkLogin()
	{
		$result = $this->getClient();
		if(@$result['client_id']=="") {
	       	redirect(site_url("login/cmdLogout"));
	        exit(0);
	    }
	}

    function checkPermission()
    {
        $this->db = $this->CI->load->database("default",true);
        $client_id = $this->getId();
        $menu_link = strtolower($this->CI->uri->segment(1));

        $client_menu = array();
        $rows = $this->db
                    ->where_in("client_id",$client_id)
                    ->get("client_menu")
                    ->result_array();
        foreach($rows as $val) {
            array_push($client_menu,$val['menu_id']);
        }

        if(count($client_menu)>0) {
            $this->db->where_not_in("menu_id",$client_menu);
        }

        $num_rows = $this->db
                ->where("menu_link",$menu_link)
                ->get("menu")
                ->num_rows();

        if($num_rows==0) {
            show_404();
            exit(0);
        }
    }

	function getLogin()
	{
		$result = $this->getClient();
		if(@$result['client_id']=="") {
	       	return false;
	    } else {
	    	return true;
	    }
	}

	function getId()
	{
		return get_cookie("BE_CLIENT_ID");
	}

	function getUsername()
	{
		return get_cookie("BE_USERNAME");
	}

	function getName()
	{
	    return get_cookie("BE_COMPANY");
	}

	function getUseradmin()
	{
	    return get_cookie("BE_USERADMIN");
	}

	function getClient()
	{
		$result = $this->CI->db
					->where("client_id",$this->client_id)
					->where("username",$this->username)
					->where("access_token",$this->access_token)
					->where(where_client_expire(),null,false)
					->get("client")
					->first_row("array");
		return $result;
	}

	function getSettingAllow()
	{
		$this->db = $this->CI->load->database("default",true);
		$client_id = $this->getId();

		$rec = $this->db
				->select("setting_allow")
				->where("client_id",$client_id)
				->get("client")
				->first_row("array");

		if(@$rec['setting_allow']=="Yes" || get_cookie("BE_ADMIN")=="Yes") {
			return true;
		} else {
			return false;
		}
	}

	function getCategoriesAllow()
	{
		$this->db = $this->CI->load->database("default", true);
		$client_id = $this->getId();

		$rec = $this->db
				->select("category_allow")
				->where("client_id",$client_id)
				->get("client")
				->first_row("array");

		if(@$rec['category_allow']=="Yes") {
			return true;
		} else {
			return false;
		}
	}

	function check_first_page()
    {
        $this->db = $this->CI->load->database("default",true);
        $client_id = $this->getId();
		$menu_link  = "overview";
        $client_menu = array();
        $rows = $this->db
                    ->where_in("client_id",$client_id)
                    ->get("client_menu")
                    ->result_array();
        foreach($rows as $val) {
            array_push($client_menu,$val['menu_id']);
        }

        if(count($client_menu)>0) {
            $this->db->where_not_in("menu_id",$client_menu);
        }

        $num_rows = $this->db
                ->where("menu_link",$menu_link)
                ->get("menu")
                ->num_rows();

		return $num_rows;
    }
}