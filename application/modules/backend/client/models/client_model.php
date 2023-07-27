<?php
class Client_model extends CI_Model 
{
    var $USER_ID = '';
     var $FIELDS = "client_id,username,password,client_group,company_name,email,telephone,start_join,end_join,setting_allow,access_token,sys_status,sys_action,createdate,createby,lastupdate,updateby";

    function __construct()
    {
        parent::__construct();
        $this->USER_ID = $this->session->userdata("USER_ID");
    }

    function get_rows($post = array())
    {
    	
    	$this->db->where("client_log.sys_status","active");
    	if(@$post["sys_action"]!="") {
    		if($post['sys_action'] == "unpublish") {
    			$this->db->where("(client_log.sys_action = 'created' OR client_log.sys_action = 'unpublish')",null,false);
    		} else {
    			$this->db->where("client_log.sys_action",$post['sys_action']);
    		}
    	}

    	if(@$post["keyword"]!="") $this->db->where("( client_log.username LIKE '%".$post["keyword"]."%' OR client_log.company_name LIKE '%".$post["keyword"]."%' )");
		
    	

    	$rows = $this->db
    			->count_all_results("client_log");
    			
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
		
		$this->db->where("client_log.sys_status","active");
		if(@$post["sys_action"]!="") {
    		if($post['sys_action'] == "unpublish") {
    			$this->db->where("(client_log.sys_action = 'created' OR client_log.sys_action = 'unpublish')",null,false);
    		} else {
    			$this->db->where("client_log.sys_action",$post['sys_action']);
    		}
    	}

		if(@$post["keyword"]!="") $this->db->where("( client_log.username LIKE '%".$post["keyword"]."%' OR client_log.company_name LIKE '%".$post["keyword"]."%' )");
		 
		

		$sorting = "";
		if(@$option["sorting"]=="update_name") {
			$sorting = "updated.username";
		} else if(@$option["sorting"]=="create_name") {
			$sorting = "created.username";
		} else if(strpos(@$option["sorting"],".")!==false) {
			$sorting = $option["sorting"];
		} else {
			$sorting = "client_log.".$option["sorting"];
		}

		$sql =  $this->db->select("client_log.*")
				->select("created.username AS create_name,updated.username AS update_name")
				->join("sys_users created","created.sys_users_id = client_log.createby","LEFT")
				->join("sys_users updated","updated.sys_users_id = client_log.updateby","LEFT")
				->order_by($sorting,@$option["orderby"])
				->from("client_log")
				->query_string();
		$newsql = get_page($sql,$this->db->dbdriver,$start,$end);
		return  $this->db->query($newsql)->result_array();
	}


	function count_log($id = 0,&$rows,&$totalpage)
	{
		$where = array("client_id"=>$id);
		$this->db->where($where);
		$rows = $this->db->count_all_results("client_log");
		$totalpage = ceil($rows/PAGESIZE);
	}

	function get_log($option = array(),$start,$end)
	{
		$sorting = "";
		if(@$option["sorting"]=="update_name") {
			$sorting = "updated.username";
		} else if(@$option["sorting"]=="create_name") {
			$sorting = "created.username";
		} else if(strpos(@$option["sorting"],".")!==false) {
			$sorting = $option["sorting"];
		} else {
			$sorting = "client_log.".$option["sorting"];
		}

		$this->db->where("client_log.client_id",$option['post']['id']);
		$sql =  $this->db->select("client_log.*")
				->select("created.username AS create_name,updated.username AS update_name")
				->join("sys_users created","created.sys_users_id = client_log.createby","LEFT")
				->join("sys_users updated","updated.sys_users_id = client_log.updateby","LEFT")
				->order_by($sorting,@$option["orderby"])
				->from("client_log")
				->query_string();
		$newsql = get_page($sql,$this->db->dbdriver,$start,$end);
		return  $this->db->query($newsql)->result_array();
	}

 	function get_id($id = 0)
 	{
 		$where = array("client_id"=>$id,"sys_status"=>"active");
		return $this->db->get_where("client_log",$where)->first_row("array");
 	}

	function insert($post = array())
	{
		$save = array();

		if(isset($post["username"])) $save["username"] = $post["username"];
		if(isset($post["password"])) $save["password"] = $post["password"];
		if(isset($post["client_group"])) $save["client_group"] = $post["client_group"];
		if(isset($post["company_name"])) $save["company_name"] = $post["company_name"];
		if(isset($post["email"])) $save["email"] = $post["email"];
		if(isset($post["telephone"])) $save["telephone"] = $post["telephone"];
		if(isset($post["start_join"])) $save["start_join"] = setDateformat($post["start_join"]);
		if(isset($post["end_join"])) $save["end_join"] = setDateformat($post["end_join"]);
		$save["setting_allow"] = @$post["setting_allow"];
		

		$save["createdate"] = date("Y-m-d H:i:s"); 
		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["createby"] = $this->USER_ID;
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "created";
		$save["sys_status"] = "active";

		$this->db->insert("client",$save);
		$val = $this->db->insert_id('client');

		$this->db->query("INSERT INTO client_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client WHERE client_id = '{$val}'");

		$where = array("client_id"=>$val);	
		$this->db->delete("client",$where);

		$this->update_child($val);

		return $val;
	}


	function update($post = array())
	{
		$val = @$post["id"];

		$this->db->query("INSERT INTO client_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_log WHERE client_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("client_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("client_log",$update,$where_update);

		$max = $this->db->select_max('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);

		$save = array();
		if(isset($post["username"])) $save["username"] = $post["username"];
		if(isset($post["password"])) $save["password"] = $post["password"];
		if(isset($post["client_group"])) $save["client_group"] = $post["client_group"];
		if(isset($post["company_name"])) $save["company_name"] = $post["company_name"];
		if(isset($post["email"])) $save["email"] = $post["email"];
		if(isset($post["telephone"])) $save["telephone"] = $post["telephone"];
		if(isset($post["start_join"])) $save["start_join"] = setDateformat($post["start_join"]);
		if(isset($post["end_join"])) $save["end_join"] = setDateformat($post["end_join"]);
		$save["setting_allow"] = @$post["setting_allow"];
		

		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "modified";
		$save["sys_status"] = "active";
		$this->db->update("client_log",$save,$where_update);
	}

	function savePublish($val = 0,$cmd = "")
	{	
		if($cmd=="Update") {
			$where = array("client_id"=>$val);
			$this->db->delete("client",$where);
		}

		$this->db->query("INSERT INTO client_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_log WHERE client_id = '{$val}' AND sys_status = 'active'");

		$where2 = array("client_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("client_log",$update,$where_update);

		$max = $this->db->select_max('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_log",$update,$where_update);

		$this->db->query("INSERT INTO client (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_log WHERE client_id = '{$val}' AND sys_status = 'active'");
	}

	function delete($val)
	{
		$where = array("client_id"=>$val);
		$this->db->delete("client",$where);

		$this->db->query("INSERT INTO client_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_log WHERE client_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("client_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("client_log",$update,$where_update);

		$max = $this->db->select_max('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"archived","sys_action"=>"delete","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_log",$update,$where_update);
	}

	
	function delete_file($post = array())
	{
		$val = $post["id"];
		$where = array("client_id"=>$val,"sys_status"=>"active");
		$update = array($post['filename']=>"");
		$this->db->update("client",$update,$where);
		$this->db->update("client_log",$update,$where);
	}

	function save_public($val)
	{
		$where = array("client_id"=>$val);
		$this->db->delete("client",$where);

		$this->db->query("INSERT INTO client_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_log WHERE client_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("client_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("client_log",$update,$where_update);

		$max = $this->db->select_max('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_log",$update,$where_update);

		$this->db->query("INSERT INTO client (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_log WHERE client_id = '{$val}' AND sys_status = 'active'");
	}

	function save_unpublic($val)
	{
		$where = array("client_id"=>$val);
		$this->db->delete("client",$where);

		$this->db->query("INSERT INTO client_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM client_log WHERE client_id = '{$val}' AND sys_status = 'active'");
			
		$where2 = array("client_id"=>$val,"sys_status"=>"active");

		$min = $this->db->select_min('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$min["log_id"]);
		$update = array("sys_status"=>"archived");
		$this->db->update("client_log",$update,$where_update);

		$max = $this->db->select_max('log_id','"log_id"')->get_where("client_log",$where2)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"active","sys_action"=>"unpublish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("client_log",$update,$where_update);
	}

	function update_child($sys_parent_id=0)
	{
		
		$update = array("sys_parent_id"=>$sys_parent_id);
		$where_update = array("sys_parent_id"=>0,"createby"=>$this->USER_ID);
		$this->db->update("client_config",$update,$where_update);
		$this->db->update("client_config_log",$update,$where_update);

	}

	function delete_child()
	{
		
		$where_update = array("sys_parent_id"=>0,"createby"=>$this->USER_ID);
		$this->db->delete("client_config",$where_update);
		$this->db->delete("client_config_log",$where_update);

	}

	function update_parent($json_array = array(),$parent_id = 0)
	{
		$parent_order = 1;
		foreach($json_array as $k_json=>$v_json) {

			$where_update = array("client_id"=>$v_json['id'],"sys_status"=>"active");
			$update = array("parent_id"=>$parent_id,"parent_order"=>($parent_order++));
			$this->db->update("client",$update,$where_update);
			$this->db->update("client_log",$update,$where_update);

			if(isset($v_json['children']) && count($v_json['children'])>0) {
				$this->update_parent($v_json['children'],$v_json['id']);
			}
		}
	}

	function update_status($post = array())
	{
		$status_col = $post['status_col'];
        $status_val = $post['status_val'];

        if(isset($post['id'])) {
            foreach($post['id'] as $val) {

                $save[$status_col] = $status_val;
				$save["lastupdate"] = date("Y-m-d H:i:s"); 
				$save["updateby"]   = $this->USER_ID;

                $this->db->where("client_id",$val);
                $this->db->where("sys_status","active");
                $this->db->update("client",$save);

                $this->db->where("client_id",$val);
                $this->db->where("sys_status","active");
                $this->db->update("client_log",$save);
            }
        }
	}
	
}