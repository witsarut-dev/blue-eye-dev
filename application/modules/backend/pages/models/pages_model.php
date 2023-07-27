<?php
class Pages_model extends CI_Model 
{
    var $USER_ID = '';
     var $FIELDS = "pages_id,page_id,page_name,page_type,sys_status,sys_action,createdate,createby,lastupdate,updateby";

    function __construct()
    {
        parent::__construct();
        $this->USER_ID = $this->session->userdata("USER_ID");
    }

    function get_rows($post = array())
    {
    	
    	$this->db->where("pages.sys_status","active");
    	if(@$post["sys_action"]!="") {
    		if($post['sys_action'] == "unpublish") {
    			$this->db->where("(pages.sys_action = 'created' OR pages.sys_action = 'unpublish')",null,false);
    		} else {
    			$this->db->where("pages.sys_action",$post['sys_action']);
    		}
    	}

    	if(@$post["keyword"]!="") $this->db->where("( pages.page_id LIKE '%".$post["keyword"]."%' OR pages.page_name LIKE '%".$post["keyword"]."%' )");
		
    	if(@$post["page_type"]!="") $this->db->where('pages.page_type',$post["page_type"]);
		

    	$rows = $this->db
    			->count_all_results("pages");

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
		
		$this->db->where("pages.sys_status","active");
		if(@$post["sys_action"]!="") {
    		if($post['sys_action'] == "unpublish") {
    			$this->db->where("(pages.sys_action = 'created' OR pages.sys_action = 'unpublish')",null,false);
    		} else {
    			$this->db->where("pages.sys_action",$post['sys_action']);
    		}
    	}

		if(@$post["keyword"]!="") $this->db->where("( pages.page_id LIKE '%".$post["keyword"]."%' OR pages.page_name LIKE '%".$post["keyword"]."%' )");
		 
		if(@$post["page_type"]!="") $this->db->where('pages.page_type',$post["page_type"]);
		

		$sorting = "";
		if(@$option["sorting"]=="update_name") {
			$sorting = "updated.username";
		} else if(@$option["sorting"]=="create_name") {
			$sorting = "created.username";
		} else if(strpos(@$option["sorting"],".")!==false) {
			$sorting = $option["sorting"];
		} else {
			$sorting = "pages.".$option["sorting"];
		}

		$sql =  $this->db->select("pages.*")
				->select("created.username AS create_name,updated.username AS update_name")
				->join("sys_users created","created.sys_users_id = pages.createby","LEFT")
				->join("sys_users updated","updated.sys_users_id = pages.updateby","LEFT")
				->order_by($sorting,@$option["orderby"])
				->from("pages")
				->query_string();
		$newsql = get_page($sql,$this->db->dbdriver,$start,$end);
		return  $this->db->query($newsql)->result_array();
	}


	function count_log($id = 0,&$rows,&$totalpage)
	{
		return 0;
	}

	function get_log($option = array(),$start,$end)
	{
		return array();
	}

 	function get_id($id = 0)
 	{
 		$where = array("pages_id"=>$id,"sys_status"=>"active");
		return $this->db->get_where("pages",$where)->first_row("array");
 	}

	function insert($post = array())
	{
		$save = array();

		if(isset($post["page_id"])) $save["page_id"] = $post["page_id"];
		if(isset($post["page_name"])) $save["page_name"] = $post["page_name"];
		if(isset($post["page_type"])) $save["page_type"] = $post["page_type"];
		

		$save["createdate"] = date("Y-m-d H:i:s"); 
		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["createby"] = $this->USER_ID;
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "created";
		$save["sys_status"] = "active";

		$this->db->insert("pages",$save);
		$val = $this->db->insert_id('pages');

		$this->update_child($val);

		return $val;
	}


	function update($post = array())
	{
		$val = @$post["id"];
		$where_update = array("pages_id"=>$val,"sys_status"=>"active");

		$save = array();

		if(isset($post["page_id"])) $save["page_id"] = $post["page_id"];
		if(isset($post["page_name"])) $save["page_name"] = $post["page_name"];
		if(isset($post["page_type"])) $save["page_type"] = $post["page_type"];
		

		$save["lastupdate"] = date("Y-m-d H:i:s"); 
		$save["updateby"] = $this->USER_ID;
		$save["sys_action"] = "publish";
		$save["sys_status"] = "active";
		$this->db->update("pages",$save,$where_update);
	}

	function savePublish($val = 0,$cmd = "")
	{	
		$where_update = array("pages_id"=>$val,"sys_status"=>"active");
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("pages",$update,$where_update);
	}

	function delete($val)
	{
		$this->db->query("INSERT INTO pages_log (".$this->FIELDS.") SELECT ".$this->FIELDS." FROM pages WHERE pages_id = '{$val}' AND sys_status = 'active'");
		$where_update = array("pages_id"=>$val,"sys_status"=>"active");
		$this->db->delete("pages",$where_update);

		$max = $this->db->select_max('log_id','"log_id"')->get_where("pages_log",$where_update)->first_row('array');
		$where_update = array("log_id"=>$max["log_id"]);
		$update = array("sys_status"=>"archived","sys_action"=>"delete","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("pages_log",$update,$where_update);
	}

	function delete_file($post = array())
	{
		$val = $post["id"];
		$where = array("pages_id"=>$val,"sys_status"=>"active");
		$update = array($post['filename']=>"");
		$this->db->update("pages",$update,$where);
	}

	function save_public($val)
	{	
		$where_update = array("pages_id"=>$val,"sys_status"=>"active");
		$update = array("sys_status"=>"active","sys_action"=>"publish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("pages",$update,$where_update);
	}

	function save_unpublic($val)
	{
		$where_update = array("pages_id"=>$val,"sys_status"=>"active");
		$update = array("sys_status"=>"active","sys_action"=>"unpublish","lastupdate"=>date("Y-m-d H:i:s"),"updateby"=>$this->USER_ID);
		$this->db->update("pages",$update,$where_update);
	}

	function update_child($sys_parent_id=0)
	{
		
	}

	function delete_child()
	{
		
	}

	function update_parent($json_array = array(),$parent_id = 0)
	{
		$parent_order = 1;
		foreach($json_array as $k_json=>$v_json) {

			$where_update = array("pages_id"=>$v_json['id'],"sys_status"=>"active");
			$update = array("parent_id"=>$parent_id,"parent_order"=>($parent_order++));
			$this->db->update("pages",$update,$where_update);

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

                $this->db->where("pages_id",$val);
                $this->db->where("sys_status","active");
                $this->db->update("pages",$save);
            }
        }
	}

}