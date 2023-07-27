<?php
class Map_model extends CI_Model {

	var $CLIENT_ID;
    var $custom_date;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID  = $this->authen->getId();
        $this->custom_date = $this->master_model->get_custom_date();
    }

    function get_map_match($post = array())
    {		
		$result = array();
		if(empty($post["keyword_id"])){
			$this->master_model->get_where_current_map(@$post["period"]);
		    $result = $this->db
					->select("*")
					->where("keyword.client_id",$this->CLIENT_ID)
					->join("map_match","map_match.map_match_keyword_id=keyword.keyword_id")
					->get("keyword")
					->result_array();
			return $result;
		}else {
			$this->master_model->get_where_current_map(@$post["period"]);
		    $result = $this->db
					->select("*")
					->where("keyword.client_id",$this->CLIENT_ID)
					->where("keyword.keyword_id",@$post["keyword_id"])
					->join("map_match","map_match.map_match_keyword_id=keyword.keyword_id")
					->get("keyword")
					->result_array();
			return $result;
		}

	}

	function get_keywordAndMention($post = array())
	{
		$rowsdata = array();
		$this->master_model->get_where_current_map(@$post["period"]);
		$rowsdata = $this->db
					->select("keyword.keyword_id,keyword.keyword_name,COUNT(map_match.map_match_id) AS mention")
					->where("keyword.client_id",$this->CLIENT_ID)
					->join("map_match","map_match.map_match_keyword_id=keyword.keyword_id")
					->group_by("keyword.keyword_id")
					->get("keyword")
					->result_array();

		return $rowsdata;
	}

	function get_keywordmap_feed($post = array())
    {
		if(!empty($post["keyword_id"]))
		{
			$this->db->where("keyword.keyword_id",$post["keyword_id"],null,false);
		}

		$this->master_model->get_where_current_map(@$post["period"]);
		$sql = $this->db
					->select("
							keyword.company_keyword_id com_id,
							map_match.map_match_msg_id post_id,
							map_match.map_match_username post_name,
							map_match.map_match_link post_link,
							map_match.map_match_msg_content post_detail,
							map_match.map_match_timepost post_time,
							map_match.map_match_sourceid sourceid,
							map_match.map_match_sentiment sentiment,
							")
					->where("keyword.client_id",$this->CLIENT_ID)
					->join("map_match","map_match.map_match_keyword_id=keyword.keyword_id")
					->order_by("map_match_timepost","desc")
					->from("keyword")
					->query_string();

		$post['post_rows'] = isset($post['post_rows']) ? $post['post_rows'] : 1;
        $newsql   = get_page($sql,$this->db->dbdriver,$post['post_rows'],PAGESIZE);
		$rowsdata = $this->db->query($newsql)->result_array();

		return $rowsdata;			
    }

}
