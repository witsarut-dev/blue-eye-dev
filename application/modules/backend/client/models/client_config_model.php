<?php
class Client_config_model extends CI_Model 
{
    var $USER_ID = '';
    var $FIELDS = "client_config_id,config_id,config_name,config_val,config_detail,sys_parent_id,sys_status,sys_action,createdate,createby,lastupdate,updateby";

    function __construct()
    {
        parent::__construct();
        $this->USER_ID = $this->session->userdata("USER_ID");
    }

    function get_rows($post = array())
    {
    	
    	if(empty($post["id"])) :
			$this->db->where("client_config.sys_parent_id",0);
			$this->db->where("client_config.createby",$this->USER_ID);
		else :
			$this->db->where("client_config.sys_parent_id",$post["id"]);
		endif;

    	$this->db->where("client_config.sys_status","active");

    	$rows = $this->db
    			->count_all_results("client_config");
		return $rows;
    }

	function get_page($option = array(),$start,$end)
	{
		$post = $option["post"];
		
		if(empty($post["id"])) :
			$this->db->where("client_config.sys_parent_id",0);
			$this->db->where("client_config.createby",$this->USER_ID);
		else :
			$this->db->where("client_config.sys_parent_id",$post["id"]);
		endif;

		$this->db->where("client_config.sys_status","active");

		$sorting = "";
		if(@$option["sorting"]=="update_name") {
			$sorting = "updated.username";
		} else if(@$option["sorting"]=="create_name") {
			$sorting = "created.username";
		} else if(strpos(@$option["sorting"],".")!==false) {
			$sorting = $option["sorting"];
		} else {
			$sorting = "client_config.".$option["sorting"];
		}

		$sql =  $this->db->select("client_config.*")
				->select("created.username AS create_name,updated.username AS update_name")
				->join("sys_users created","created.sys_users_id = client_config.createby","LEFT")
				->join("sys_users updated","updated.sys_users_id = client_config.updateby","LEFT")
				->order_by($sorting,@$option["orderby"])
				->from("client_config")
				->query_string();
		$newsql = get_page($sql,$this->db->dbdriver,$start,$end);
		return  $this->db->query($newsql)->result_array();
	}

 	function get_id($id = 0)
 	{
 		$where = array("client_config_id"=>$id,"sys_status"=>"active");
		return $this->db->get_where("client_config",$where)->first_row("array");
 	}

	function insert($post = array())
	{
		$save = array();

		if(@$post["config_name"]!="") $save["config_name"] = $post["config_name"];
		if(@$post["config_val"]!="") $save["config_val"] = $post["config_val"];
		if(@$post["config_detail"]!="") $save["config_detail"] = $post["config_detail"];
		

		$save["createdate"] = date("Y-m-d H:i:s"); 
		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["createby"] = $this->USER_ID;
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "created";
		$save["sys_status"] = "active";
		$save["sys_parent_id"] = $post["id"];

		$this->db->insert("client_config",$save);
		$val = $this->db->insert_id('client_config');

		return $val;
	}


	function update($post = array())
	{
		$val = @$post["child_id"];
		$where_update = array("client_config_id"=>$val,"sys_status"=>"active");

		$save = array();

		if(@$post["config_name"]!="") $save["config_name"] = $post["config_name"];
		if(@$post["config_val"]!="") $save["config_val"] = $post["config_val"];
		if(@$post["config_detail"]!="") $save["config_detail"] = $post["config_detail"];
		

		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "publish";
		$save["sys_status"] = "active";
		$this->db->update("client_config",$save,$where_update);
	}

	function savePublish($val = 0,$cmd = "")
	{	
		$where_update = array("client_config_id"=>$val,"sys_status"=>"active");
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_config",$update,$where_update);
	}

	function delete($val)
	{
		$this->db->query("INSERT INTO client_config_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_config WHERE client_config_id = '{$val}' AND sys_status = 'active'");
		$where_update = array("client_config_id"=>$val,"sys_status"=>"active");
		$this->db->delete("client_config",$where_update);

		$max = $this->db->select_max('log_id','"log_id"')->get_where("client_config_log",$where_update)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"archived","sys_action"=>"delete","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_config_log",$update,$where_update);
	}

	function delete_file($post = array())
	{
		$val = $post["child_id"];
		$where = array("client_config_id"=>$val,"sys_status"=>"active");
		$update = array($post['filename']=>"");
		$this->db->update("client_config",$update,$where);
	}

	function save_public($val)
	{	
		$where_update = array("client_config_id"=>$val,"sys_status"=>"active");
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_config",$update,$where_update);

	}

	function save_unpublic($val)
	{
		$where_update = array("client_config_id"=>$val,"sys_status"=>"active");
		$update = array("sys_status"=>"active","sys_action"=>"unpublish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_config",$update,$where_update);
	}

	function update_parent($json_array = array(),$parent_id = 0)
	{
		$parent_order = 1;
		foreach($json_array as $k_json=>$v_json) {

			$where_update = array("client_config_id"=>$v_json['id'],"sys_status"=>"active");
			$update = array("parent_id"=>$parent_id,"parent_order"=>($parent_order++));
			$this->db->update("client_config",$update,$where_update);

			if(isset($v_json['children']) && count($v_json['children'])>0) {
				$this->update_parent($v_json['children'],$v_json['id']);
			}
		}
	}

}