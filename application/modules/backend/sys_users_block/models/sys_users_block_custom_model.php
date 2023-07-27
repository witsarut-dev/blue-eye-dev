<?php
class Sys_users_block_custom_model extends CI_Model 
{
    var $USER_ID = '';

    function __construct()
    {
        parent::__construct();
        $this->USER_ID = $this->session->userdata("USER_ID");
    }

    function where_custom($post = array()) 
    {
        $this->db->where("block_time >=",date("Y-m-d H:i:s"));
    }

}
?>