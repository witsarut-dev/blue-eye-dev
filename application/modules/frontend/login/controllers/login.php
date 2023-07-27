<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Frontend {

    var $block_time = 10;
    var $block_max = 5;
    var $block_type = 'frontend';

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
            if($this->authen->check_first_page() == 0){
                redirect(site_url("link_analysis"));
            }else{
                redirect(site_url("overview"));
            }
		} else {
			$viewdata = array();
            $viewdata['access_token'] = get_access_token();
			$this->load->view("login_view",$viewdata);
		}
	}

	function cmdLogin()
	{
		$result = array();
		$post = $this->input->post();
        $access_token = get_access_token();

        $username = @$post['username'];

        if(!isset($post['access_token']) || $access_token!=$post['access_token'])  {
            $result['message'] = "access_token not found.";
            $result['status'] = false;
        } else {
            $authen = $this->login_model->get_authen(@$post['username'],@$post['password']);
            $checkBlock  = $this->get_users_block($username);
            $block_count = $this->get_block_count($username);
            if($checkBlock) {
                $result['message'] = "Your username is banned ".$this->block_time." minute.";
                $result['status']  = false;
            } else if($block_count>=$this->block_max) {
                $result['message'] = "Your username is banned ".$this->block_time." minute.";
                $result['status']  = false;
            } else if(@$authen['client_id'] !="") {
                if($authen['status']) {
                    if(isset($post['remember'])) {
                        $this->login_model->set_login($authen['username'],$authen['client_id'],true);
                    } else {
                        $this->login_model->set_login($authen['username'],$authen['client_id'],false);
                    }
                    $result['status'] = true;
                } else {
                    $result['status'] = false;
                    $result['message'] = "Your username expired.";
                }
                $this->remove_users_block($username);
            } else {

                $block_count = ($block_count + 1);
                $this->add_users_block($username,$block_count);
                if($block_count>=($this->block_max-2)) {
                    $result['message'] = "You've entered the wrong password ".$block_count." times. (Max ".$this->block_max.")";
                } else {
                    if($authen['status']) {
                        $result['message'] = "Username or Password is incorrect.";
                    } 
                }

                $this->clean_login();
                $result['status'] = false;
            }
        }
        delete_cookie("META_PERIOD");
        delete_cookie("META_CUSTOM_DATE");
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
            $this->input->set_cookie("BE_ADMIN", "Yes", 0);
            $this->input->set_cookie("BE_USERADMIN", $this->session->userdata("USERNAME"), 0);
		}
        delete_cookie("META_PERIOD");
        delete_cookie("META_CUSTOM_DATE");
		echo json_encode($result);
	}

	function cmdLogout()
	{
		$this->clean_login();
		redirect(site_url());
	}

	function clean_login()
	{
        delete_cookie("META_PERIOD");
        delete_cookie("META_CUSTOM_DATE");
		delete_cookie("BE_ACCESS_TOKEN");
		delete_cookie("BE_CLIENT_ID");
		delete_cookie("BE_USERNAME");
        delete_cookie("BE_COMPANY");
        delete_cookie("BE_ADMIN");
        delete_cookie("BE_USERADMIN");
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

    private function add_users_block($username = "",$block_count = 0)
    {
        $rec = $this->db
                ->where("username",$username)
                ->where("block_type",$this->block_type)
                ->get("sys_users_block")
                ->first_row('array');

        $save = array();

        if($block_count>=5) $save['block_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +".$this->block_time." minutes"));

        $save['username'] = $username;
        $save['block_type'] = $this->block_type;
        $save['block_count'] = $block_count;
        $save['sys_status'] = 'active';
        if(isset($rec['sys_users_block_id'])) {
            $this->db->where("sys_users_block_id",$rec['sys_users_block_id']);
            $this->db->update("sys_users_block",$save);
        } else {
            $this->db->insert("sys_users_block",$save);
        }
    }

    private function get_users_block($username = "")
    {
        $where = array(
            "username" => $username,
            "block_type" => $this->block_type
        );

        $rec = $this->db
                    ->where($where)
                    ->get("sys_users_block")
                    ->first_row('array');

        if(isset($rec['block_time'])) {
             $block_time = strtotime($rec['block_time']);
             if(strtotime("now")>$block_time) {
                $this->db->where($where);
                $this->db->delete("sys_users_block");
                return false;
             } else {
                return true;
             }
        } else {
            return false;
        }
    }

    private function get_block_count($username = "")
    {
        $rec = $this->db
                    ->where("username",$username)
                    ->where("block_type",$this->block_type)
                    ->get("sys_users_block")
                    ->first_row('array');

        return intval(@$rec['block_count']);
    }

    private function remove_users_block($username = "")
    {
        $where = array(
            "username" => $username,
            "block_type" => $this->block_type
        );
        $this->db->where($where);
        $this->db->delete("sys_users_block");
    }
}
