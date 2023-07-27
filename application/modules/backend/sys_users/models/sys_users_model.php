<?php
class Sys_users_model extends CI_Model 
{
    var $USER_ID = '';
    var $FIELDS = 'sys_users_id,firstname,lastname,username,password,email,sys_roles_id,assigned,sys_status,sys_action,createdate,createby,lastupdate,updateby';

    function __construct()
    {
        parent::__construct();
        $this->load->model("authen_model");
        $this->USER_ID = $this->session->userdata("USER_ID");
        $this->ROLES_ID = $this->session->userdata("ROLES_ID");
    }

    function get_rows($post = array())
    {
		$this->db->where("sys_users_log.sys_status","active");
		if(@$post["sys_action"]!="") {
    		if($post['sys_action'] == "unpublish") {
    			$this->db->where("(sys_users_log.sys_action = 'created' OR sys_users_log.sys_action = 'unpublish')",null,false);
    		} else {
    			$this->db->where("sys_users_log.sys_action",$post['sys_action']);
    		}
    	}

    	if(@$post["keyword"]!="") $this->db->where("(sys_users_log.username LIKE '%".$post["keyword"]."%' OR sys_users_log.firstname LIKE '%".$post["keyword"]."%' OR sys_users_log.lastname LIKE '%".$post["keyword"]."%')");
		if($this->ROLES_ID!='1') $this->db->where('sys_roles.sys_roles_id <>',1);
    	
    	$rows = $this->db
				->join("sys_roles","sys_roles.sys_roles_id = sys_users_log.sys_roles_id","LEFT")
    			->count_all_results("sys_users_log");
    			
		return $rows;
    }

    function get_rows_publish($post = array())
    {
    	$post['sys_action'] = "publish";
    	return $this->get_rows($post);
	}

	function get_rows_modified($post = array())
    {
    	$post['sys_action'] = "modified";
    	return $this->get_rows($post);
	}

	function get_rows_unpublish($post = array())
    {
    	$post['sys_action'] = "unpublish";
    	return $this->get_rows($post);
	}

	function get_page($option = array(),$start,$end)
	{
		$post = $option["post"];
		$this->db->where("sys_users_log.sys_status","active");
		if(@$post["sys_action"]!="") {
    		if($post['sys_action'] == "unpublish") {
    			$this->db->where("(sys_users_log.sys_action = 'created' OR sys_users_log.sys_action = 'unpublish')",null,false);
    		} else {
    			$this->db->where("sys_users_log.sys_action",$post['sys_action']);
    		}
    	}

		if(@$post["keyword"]!="") $this->db->where("(sys_users_log.username LIKE '%".$post["keyword"]."%' OR sys_users_log.firstname LIKE '%".$post["keyword"]."%' OR sys_users_log.lastname LIKE '%".$post["keyword"]."%')");
		if($this->ROLES_ID!='1') $this->db->where('sys_roles.sys_roles_id <>',1);
		 
		$sorting = "";
		if(@$option["sorting"]=="update_name") {
			$sorting = "updated.username";
		} else if(@$option["sorting"]=="create_name") {
			$sorting = "created.username";
		} else if(strpos(@$option["sorting"],".")!==false) {
			$sorting = $option["sorting"];
		} else {
			$sorting = "sys_users_log.".$option["sorting"];
		}

		$sql =  $this->db->select("sys_users_log.*")
				->select("created.username AS create_name,updated.username AS update_name")
				->select("sys_roles.roles_name")
				->join("sys_users created","created.sys_users_id = sys_users_log.createby","LEFT")
				->join("sys_users updated","updated.sys_users_id = sys_users_log.updateby","LEFT")
				->join("sys_roles","sys_roles.sys_roles_id = sys_users_log.sys_roles_id","LEFT")
				->order_by($sorting,@$option["orderby"])
				->from("sys_users_log")
				->query_string();
		$newsql = get_page($sql,$this->db->dbdriver,$start,$end);
		return  $this->db->query($newsql)->result_array();
	}


	function count_log($id = 0,&$rows,&$totalpage)
	{
		$where = array("sys_users_id"=>$id);
		$this->db->where($where);
		$rows = $this->db->count_all_results("sys_users_log");
		$totalpage = ceil($rows/PAGESIZE);
	}

	function get_log($option = array(),$start,$end)
	{
		$this->db->where("sys_users_log.sys_users_id",$option['post']['id']);
		$sql =  $this->db->select("sys_users_log.*")
				->select("created.username AS create_name,updated.username AS update_name")
				->join("sys_users created","created.sys_users_id = sys_users_log.createby","LEFT")
				->join("sys_users updated","updated.sys_users_id = sys_users_log.updateby","LEFT")
				->order_by(@$option["sorting"],@$option["orderby"])
				->from("sys_users_log")
				->query_string();
		$newsql = get_page($sql,$this->db->dbdriver,$start,$end);
		return  $this->db->query($newsql)->result_array();
	}

 	function get_id($id = 0)
 	{
 		$where = array("sys_users_id"=>$id,"sys_status"=>"active");
		return $this->db->get_where("sys_users_log",$where)->first_row("array");
 	}

	function insert($post = array())
	{
		$save = array();

		if(isset($post["firstname"])) $save["firstname"] = $post["firstname"];
		if(isset($post["lastname"])) $save["lastname"] = $post["lastname"];
		if(isset($post["username"])) $save["username"] = $post["username"];
		if(isset($post["password"])) $save["password"] = md5($post["password"]);
		if(isset($post["email"])) $save["email"] = $post["email"];
		if(isset($post["sys_roles_id"])) $save["sys_roles_id"] = $post["sys_roles_id"];
		if($this->ROLES_ID=='1') $save["assigned"] = @$post["assigned"];
		
		$save["createdate"] = date("Y-m-d H:i:s"); 
		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["createby"] = $this->USER_ID;
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "created";
		$save["sys_status"] = "active";

		$this->db->insert("sys_users",$save);
		$val = $this->db->insert_id();

		$this->db->query("INSERT INTO sys_users_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users WHERE sys_users_id = '{$val}'");

		$where = array("sys_users_id"=>$val);	
		$this->db->delete("sys_users",$where);

		$this->update_child($val);
		if($this->ROLES_ID=='1') $this->authen_model->update_permission_u($val,$post);

		return $val;
	}


	function update($post = array())
	{
		$val = @$post["id"];

		$this->db->query("INSERT INTO sys_users_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users_log WHERE sys_users_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("sys_users_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("sys_users_log",$update,$where_update);

		$max = $this->db->select_max('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);

		$save = array();
		if(isset($post["firstname"])) $save["firstname"] = $post["firstname"];
		if(isset($post["lastname"])) $save["lastname"] = $post["lastname"];
		if(isset($post["username"])) $save["username"] = $post["username"];
		if(isset($post["password"]) && $post["password"]!="") $save["password"] = md5($post["password"]);
		if(isset($post["email"])) $save["email"] = $post["email"];
		if(isset($post["sys_roles_id"])) $save["sys_roles_id"] = $post["sys_roles_id"];
		if($this->ROLES_ID=='1') $save["assigned"] = @$post["assigned"];
		

		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "modified";
		$save["sys_status"] = "active";
		$this->db->update("sys_users_log",$save,$where_update);

		if($this->ROLES_ID=='1') $this->authen_model->update_permission_u($val,$post);
	}

	function update_users($post)
	{	
		$where_update = array("sys_users_id"=>$this->USER_ID,"sys_status"=>"active");

		$save = array();

		if(isset($post["firstname"])) $save["firstname"] = $post["firstname"];
		if(isset($post["lastname"])) $save["lastname"] = $post["lastname"];
		if(isset($post["password"]) && $post["password"]!="") $save["password"] = md5($post["password"]);
		if(isset($post["email"])) $save["email"] = $post["email"];

		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["updateby"] = $this->USER_ID;

		$this->db->update("sys_users",$save,$where_update);
		$this->db->update("sys_users_log",$save,$where_update);
	}

	function savePublish($val = 0,$cmd = "")
	{	
		if($cmd=="Update") {
			$where = array("sys_users_id"=>$val);
			$this->db->delete("sys_users",$where);
		}

		$this->db->query("INSERT INTO sys_users_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users_log WHERE sys_users_id = '{$val}' AND sys_status = 'active'");

		$where2 = array("sys_users_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("sys_users_log",$update,$where_update);

		$max = $this->db->select_max('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("sys_users_log",$update,$where_update);

		$this->db->query("INSERT INTO sys_users (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users_log WHERE sys_users_id = '{$val}' AND sys_status = 'active'");
	}

	function delete($val)
	{
		$where = array("sys_users_id"=>$val);
		$this->db->delete("sys_users",$where);

		$this->db->query("INSERT INTO sys_users_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users_log WHERE sys_users_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("sys_users_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("sys_users_log",$update,$where_update);

		$max = $this->db->select_max('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"archived","sys_action"=>"delete","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("sys_users_log",$update,$where_update);
	}

	
	function delete_file($post = array())
	{
		$val = $post["id"];
		$where = array("sys_users_id"=>$val);
		$update = array($post['filename']=>"");
		$this->db = DB::table("sys_users");
		$this->db->where($where);
		$this->db->update($update);
		$this->db = DB::table("sys_users_log");
		$this->db->where($where);
		$this->db->update($update);
	}

	function save_public($val)
	{
		$where = array("sys_users_id"=>$val);
		$this->db->delete("sys_users",$where);

		$this->db->query("INSERT INTO sys_users_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users_log WHERE sys_users_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("sys_users_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("sys_users_log",$update,$where_update);

		$max = $this->db->select_max('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("sys_users_log",$update,$where_update);

		$this->db->query("INSERT INTO sys_users (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users_log WHERE sys_users_id = '{$val}' AND sys_status = 'active'");
	}

	function save_unpublic($val)
	{
		$where = array("sys_users_id"=>$val);
		$this->db->delete("sys_users",$where);

		$this->db->query("INSERT INTO sys_users_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM sys_users_log WHERE sys_users_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("sys_users_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("sys_users_log",$update,$where_update);

		$max = $this->db->select_max('log_id','log_id')->get_where("sys_users_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"active","sys_action"=>"unpublish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("sys_users_log",$update,$where_update);
	}

	function update_child($sys_parent_id=0)
	{
		
	}

}