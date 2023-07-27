<?php
class Authen extends Backend {

    var $block_time = 10;
    var $block_max = 5;
    var $block_type = 'backend';

    function __construct()
    {
        parent::__construct();
        parent::$view_data["module"] = "authen";
        $this->load->model('authen_model',"authen");
        $this->load->model('sys_logs_model',"sys_logs");
        $this->load->helper(array("common","cookie"));
    }

    function index()
    {
        parent::$view_data['access_token'] = get_access_token();
        $this->load->view("authen_view", parent::$view_data);
    }

    function login()
    {
        $post = $this->input->post();
        $access_token = get_access_token();

        $username = @$post['username'];

        if(!isset($post['access_token']) || $access_token!=$post['access_token'])  {
            $this->session->set_flashdata('error_login', 'access_token not found.');
        } else {
            $item =  $this->authen->get_users(@$post['username'],md5(@$post['password']));
            $checkBlock  = $this->get_users_block($username);
            $block_count = $this->get_block_count($username);
            if($checkBlock) {
                $this->session->set_flashdata('error_login', "Your username is banned ".$this->block_time." minute.");
            } else if($block_count>=$this->block_max) {
                $this->session->set_flashdata('error_login', "Your username is banned ".$this->block_time." minute.");
            } else if(@$item['sys_users_id'] !="") {
                $this->session->set_userdata("USER_ID",$item['sys_users_id']);
                $this->session->set_userdata("USERNAME",$item['username']);
                $this->session->set_userdata("ROLES_ID",$item['sys_roles_id']);
                $this->session->set_userdata("ASSIGNED",$item['assigned']);
                $this->remove_users_block($username);
            } else {
                $block_count = ($block_count + 1);
                $this->add_users_block($username,$block_count);
                if($block_count>=($this->block_max-2)) {
                    $error_login = "You've entered the wrong password ".$block_count." times. (Max ".$this->block_max.")";
                } else {
                    $error_login = 'Username or Password is incorrect.';
                }

                $this->session->unset_userdata("USER_ID");
                $this->session->unset_userdata("USERNAME");
                $this->session->unset_userdata("ROLES_ID");
                $this->session->unset_userdata("ASSIGNED");
                $this->session->set_flashdata('error_login', $error_login);
            }
        }
        $this->sys_logs->save_log("Authenticate","login");
        header("Location:".site_url('authen'));
    }

    function logout()
    {
        $this->sys_logs->save_log("Authenticate","logout");
        $this->session->unset_userdata("USER_ID");
        $this->session->unset_userdata("USERNAME");
        $this->session->unset_userdata("ROLES_ID");

        delete_cookie("META_PERIOD");
        delete_cookie("META_CUSTOM_DATE");
        delete_cookie("BE_ACCESS_TOKEN");
        delete_cookie("BE_CLIENT_ID");
        delete_cookie("BE_USERNAME");
        delete_cookie("BE_COMPANY");
        delete_cookie("BE_ADMIN");
        delete_cookie("BE_USERADMIN");

        header("Location:".site_url('authen'));
    }

    function close_menu()
    {
        $post = $this->input->post();
        $this->session->set_userdata("CLOSE",@$post['close']);
        echo json_encode(array("close"=>@$post["close"]));
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
?>
