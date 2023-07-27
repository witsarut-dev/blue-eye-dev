<?php
class sys_users_trigger extends Backend 
{
    function __construct()
    {
        parent::__construct();
    }

    function set_trigger($action = "",$id = 0,$data = array())
    {
    	$obj = array("status"=>true,"message"=>"");
		if($action=="") {
            //code trigger
            $obj["status"] = false;
        } else if($action=="save:before") {

            $CI =& get_instance();
            $num_rows = $CI->db
                    ->where("username",$data['username'])
                    ->where("sys_status","active")
                    ->get("sys_users_log")
                    ->num_rows();

            if($num_rows>0 && $id=="") {
                $obj["message"] = 'Username can not use this. Because a user already exists.';
                $obj["status"] = false;
            } else {
                if($id!="") {
                    $result = $CI->db
                        ->select("username")
                        ->where("sys_users_id",$id)
                        ->where("sys_status","active")
                        ->get("sys_users_log")
                        ->first_row('array');

                    if(@$result['username']!=$data['username'] && $num_rows>0) {
                        $obj["message"] = 'Username can not use this. Because a user already exists.';
                        $obj["status"] = false;
                    }
                }
            }

            if(!parent::check_password($id,$data['password'],$message)) {
                $obj["message"] = $message;
                $obj["status"] = false;
            }
            
        } else if($action=="update:before") {
            $CI =& get_instance();
            $result = $CI->db
                    ->select("password")
                    ->where("sys_users_id",$id)
                    ->where("sys_status","active")
                    ->get("sys_users_log")
                    ->first_row('array');
            if(@$result['password']!=md5($data['old_password'])) {
                $obj["message"] = 'Your old password is invalid.';
                $obj["status"] = false;
            }

            if(!parent::check_password($id,$data['password'],$message)) {
                $obj["message"] = $message;
                $obj["status"] = false;
            }

		} else {
			$obj["status"] = true;		
        }
		return $obj;
    }

}