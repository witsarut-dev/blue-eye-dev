<?php
class client_trigger extends Backend 
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("client_menu_model");
    }

    function set_trigger($action = "",$id = 0,$data = array())
    {
        $CI =& get_instance();
        $obj = array("status"=>true,"message"=>"");
        if($action=="") {
            //code trigger
            $obj["status"] = false;
        } else if($action=="save:before") {

            $start_join = strtotime(setDateformat($data['start_join']));
            $end_join = strtotime(setDateformat($data['end_join']));
            if($end_join<$start_join) {
                $obj["message"] = 'Start Join must lower than End Join.';
                $obj["status"] = false;
                return $obj;
            }

            $num_rows = $CI->db
                    ->where("username",$data['username'])
                    ->where("sys_status","active")
                    ->get("client_log")
                    ->num_rows();

            if($num_rows>0 && $id=="") {
                $obj["message"] = 'Username can not use this. Because a user already exists.';
                $obj["status"] = false;
            } else {
                if($id!="") {
                    $result = $CI->db
                        ->select("username")
                        ->where("client_id",$id)
                        ->where("sys_status","active")
                        ->get("client_log")
                        ->first_row('array');

                    if(@$result['username']!=$data['username'] && $num_rows>0) {
                        $obj["message"] = 'Username can not use this. Because a user already exists.';
                        $obj["status"] = false;
                    }
                }
            }

            if(!parent::check_password($id,@$data['password'],$message)) {
                $obj["message"] = $message;
                $obj["status"] = false;
            }

            if($obj['status']==true) {
                $this->update_client_config($id,$data['client_group']);
            }

        } else if($action=="save:after") {

            $result = $CI->db
                    ->select("company.*")
                    ->where("company.client_id",$id)
                    ->where("company.company_keyword_type","Company")
                    ->get("company_keyword company")
                    ->first_row('array');

            if(@$result['company_keyword_id']=="") {
                $save = array();
                $save['client_id']               = $id;
                $save['company_keyword_name']    = $data['company_name'];
                $save['company_keyword_type']    = "Company";
                $save['company_keyword_fb']      = "";
                $save['created_date']            = date("Y-m-d H:i:s");
                $this->db->insert("company_keyword",$save);
            } else {
                $save = array();
                $save['company_keyword_name']    = $data['company_name'];
                $this->db->where("company_keyword_id",$result['company_keyword_id']);
                $this->db->update("company_keyword",$save);
            }

            $this->update_password($id,$data);
            $this->client_menu_model->close_menu($id);
       
        } else {
            $obj["status"] = true;      
        }
        return $obj;
    }

    private function update_password($client_id = 0,$data = array())
    {
        $CI =& get_instance();

        $result = $CI->db
            ->select("password")
            ->where("client_id",$client_id)
            ->where("sys_status","active")
            ->get("client_log")
            ->first_row('array');

        $password = md5(@$result['password']);

        if (isset($data['password']) && $data['password']!="") {
            $save = array();
            $save['password'] = $password;
            $this->db->where("client_id",$client_id);
            $this->db->where("sys_status","active");
            $this->db->update("client_log",$save);
        }

        $result = $CI->db
            ->select("password")
            ->where("client_id",$client_id)
            ->where("sys_status","active")
            ->get("client")
            ->first_row('array');

        if (isset($data['password']) && $data['password']!="") {
            $save = array();
            $save['password'] = $password;
            $this->db->where("client_id",$client_id);
            $this->db->where("sys_status","active");
            $this->db->update("client",$save);
        }
    }

    private function update_client_config($sys_parent_id,$client_group)
    {
        $CI =& get_instance();
        $client = $CI->db
            ->select("client.client_group")
            ->where("client.client_id",$sys_parent_id)
            ->where("client.sys_status","active")
            ->get("client_log client")
            ->first_row('array');

        $old_group = isset($client['client_group']) ? $client['client_group'] : null;
        $new_group = $client_group;

        if ($old_group=="" || ($old_group=="Demo" && $new_group!="Demo")) {

            $CI->db->where("sys_parent_id",$sys_parent_id);
            $CI->db->delete("client_config");

            $rowsdata = $CI->db
                ->select("*")
                ->where("config_group",$client_group)
                ->where("sys_status","active")
                ->get("config")
                ->result_array();

            foreach($rowsdata as $key=>$val) {
                $save = array();
                $save['config_id']      = $val['config_id'];
                $save['config_name']    = $val['config_name'];
                $save['config_val']     = $val['config_val'];
                $save['config_detail']  = $val['config_detail'];
                $save['sys_parent_id']  = $sys_parent_id;
                $save['sys_status']     = $val['sys_status'];
                $save['sys_action']     = $val['sys_action'];
                $save["createdate"]     = date("Y-m-d H:i:s"); 
                $save["lastupdate"]     = date("Y-m-d H:i:s");
                $save["createby"]       = $CI->session->userdata("USER_ID");
                $save["updateby"]       = $CI->session->userdata("USER_ID");
                $CI->db->insert("client_config",$save);
            }
        }
    }

}