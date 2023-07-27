<?php
class Login_model extends CI_Model 
{
	public static $client_id;

    function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        self::$client_id = $this->session->userdata("CLIENT_ID");
    }

    function get_id($client_id)
    {
        $result = $this->db
                ->select("*")
                ->where("client_id",$client_id)
                ->where("sys_action","publish")
                ->get("client")
                ->first_row('array');
        return $result;
    }

    function get_authen($username = "" ,$password = "")
    {
        $this->form_validation->set_rules('username', 'username', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() == false) {
            $result['message'] = $this->form_validation->error_string();
            $result['status']  = false;
        } else {
            $result = $this->db
                    ->select("*")
                    ->where("username",$username)
                    ->where("password",md5($password))
                    ->where("sys_action","publish")
                    ->get("client")
                    ->first_row('array');

            $result['message'] = "success";
            $result['status']  = true;
        }
        return $result;
    }


    function get_client()
    {
        $rows = $this->db
                    ->select("client.client_id,client.company_name")
                    ->where("client.sys_status","active")
                    ->where("client.sys_action","publish")
                    ->order_by("client.company_name")
                    ->get("client")
                    ->result_array();
        return $rows;
    }

    function set_login($username = "",$client_id = "",$remember = false)
    {
        $result = $this->db
                ->select("*")
                ->where("username",$username)
                ->where("client_id",$client_id)
                ->where("sys_action","publish")
                ->get("client")
                ->first_row('array');

        $access_token = md5($username).md5(sha1($username.$client_id)).md5(sha1($client_id));

        $save = array("access_token"=>$access_token);
        $this->db->where("client_id",$client_id);
        $this->db->where("username",$username);
        $this->db->update("client",$save);

        if($remember) {
            $this->input->set_cookie("BE_ACCESS_TOKEN", $access_token, 31536000);
            $this->input->set_cookie("BE_CLIENT_ID", $client_id, 31536000);
            $this->input->set_cookie("BE_USERNAME", $username, 31536000);
            $this->input->set_cookie("BE_COMPANY", @$result['company_name'], 31536000);
        } else {
            $this->input->set_cookie("BE_ACCESS_TOKEN", $access_token, 0);
            $this->input->set_cookie("BE_CLIENT_ID", $client_id, 0);
            $this->input->set_cookie("BE_USERNAME", $username, 0);
            $this->input->set_cookie("BE_COMPANY", @$result['company_name'], 0);
        }
    }

}