<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class migrate_client_config extends MX_Controller {

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);
	}

    function run() 
    {
        $client_rows = $this->db
                    ->select("client_id,client_group")
                    ->where("sys_status","active")
                    ->where("sys_action <>","delete")
                    ->get("client_log")
                    ->result_array();
        
        foreach($client_rows as $client) {
            $config_rows = $this->db
                        ->where("config_group",$client['client_group'])
                        ->get("config")
                        ->result_array();
            foreach($config_rows as $config) {
                $num_rows = $this->db
                            ->where("sys_parent_id",$client['client_id'])
                            ->where("config_name",$config['config_name'])
                            ->get("client_config")
                            ->num_rows();
                if($num_rows==0) {
                    $save = array();
                    $save['config_id']      = $config['config_id'];
                    $save['config_name']    = $config['config_name'];
                    $save['config_val']     = $config['config_val'];
                    $save['config_detail']  = $config['config_detail'];
                    $save['sys_parent_id']  = $client['client_id'];
                    $save['sys_status']     = $config['sys_status'];
                    $save['sys_action']     = $config['sys_action'];
                    $save["createdate"]     = date("Y-m-d H:i:s"); 
                    $save["lastupdate"]     = date("Y-m-d H:i:s");
                    $save["createby"]       = 1;
                    $save["updateby"]       = 1;
                    $this->db->insert("client_config",$save);
                }
            }

        }

    }

}