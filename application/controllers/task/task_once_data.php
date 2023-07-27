<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_once_data extends MX_Controller {

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);
        ini_set('memory_limit', '8192M');
	}

    function index() 
    {
        //$this->run_once($type);
    }

	function run_once()
	{	
        $this->clear_data();
        $this->update_share();
	}

    function clear_data()
    {
        $last_date = date("Y-m-d",strtotime(date("Y-m-d")." -36 months"));
        // $last_date = '2019-01-01';
        $rowsdata  = $this->db
                    ->select("own_match_id")
                    ->where("DATE(own_match.msg_time) <",$last_date)
                    ->get("own_match")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $where_delete = array("own_match_id"=>$v_row['own_match_id']);

            $this->db->delete("own_cate_match",$where_delete);
            $this->db->delete("own_key_match",$where_delete);
            $this->db->delete("own_match",$where_delete);
        }

        $rowsdata  = $this->db
                    ->select("competitor_match_id")
                    ->where("DATE(competitor_match.msg_time) <",$last_date)
                    ->get("competitor_match")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $where_delete = array("competitor_match_id"=>$v_row['competitor_match_id']);

            $this->db->delete("competitor_cate_match",$where_delete);
            $this->db->delete("competitor_key_match",$where_delete);
            $this->db->delete("competitor_match",$where_delete);
        }

        // last date -1 day
        $last_date = date("Y-m-d",strtotime(date("Y-m-d")." -1 days"));
        $rowsdata  = $this->db
                    ->select("DATE(msg_time) AS msg_time")
                    ->where("DATE(msg_time) <",$last_date)
                    ->group_by("DATE(msg_time)")
                    ->get("own_match_daily")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $where_delete = array("DATE(msg_time)"=>$v_row['msg_time']);
            $this->db->delete("own_match_daily",$where_delete);
        }

        $last_date = date("Y-m-d",strtotime(date("Y-m-d")." -1 days"));
        $rowsdata  = $this->db
                    ->select("DATE(msg_time) AS msg_time")
                    ->where("DATE(msg_time) <",$last_date)
                    ->group_by("DATE(msg_time)")
                    ->get("competitor_match_daily")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $where_delete = array("DATE(msg_time)"=>$v_row['msg_time']);
            $this->db->delete("competitor_match_daily",$where_delete);
        }

        // last date -100 days
        $last_date = date("Y-m-d",strtotime(date("Y-m-d")." -100 days"));
        $rowsdata  = $this->db
                    ->select("DATE(msg_time) AS msg_time")
                    ->where("DATE(msg_time) <",$last_date)
                    ->group_by("DATE(msg_time)")
                    ->get("own_match_3months")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $where_delete = array("DATE(msg_time)"=>$v_row['msg_time']);
            $this->db->delete("own_match_3months",$where_delete);
        }

        $last_date = date("Y-m-d",strtotime(date("Y-m-d")." -100 days"));
        $rowsdata  = $this->db
                    ->select("DATE(msg_time) AS msg_time")
                    ->where("DATE(msg_time) <",$last_date)
                    ->group_by("DATE(msg_time)")
                    ->get("competitor_match_3months")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $where_delete = array("DATE(msg_time)"=>$v_row['msg_time']);
            $this->db->delete("competitor_match_3months",$where_delete);
        }
    }

    function update_share()
    {
        $rowsdata  = $this->db
                    ->select("own_match_id,own_match.msg_id,own_match.match_type")
                    ->where("own_match.sourceid",1)
                    ->where("own_match.match_type","Feed")
                    ->order_by("own_match.post_share","DESC")
                    ->get("own_match")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $result = $this->get_mongo_feed($v_row['msg_id']);
            $save = array();
            $save["post_share"]  = intval(@$result["feedshares"]);
            $this->db->where("own_match_id",$v_row["own_match_id"]);
            $this->db->update("own_match",$save);

            $this->db->where("own_match_id",$v_row["own_match_id"]);
            $this->db->update("own_match_daily",$save);

            $this->db->where("own_match_id",$v_row["own_match_id"]);
            $this->db->update("own_match_3months",$save);
        }
    }

    function get_mongo_feed($msg_id = 0)
    {
        $result = array();
        if(count($msg_id)>0) {
            $mongo = new MongoClient(MONGO_CONNECTION);
            $mongodb = $mongo->blue_eye;
            $collection = $mongodb->selectCollection("Feed");
            $query = array('_id' => $msg_id);
            $cursor = $collection->find($query);
           
            foreach($cursor as $k_row=>$v_row) {
                $result = $v_row;
            }
            $mongo->close();
        }
        return $result;
    }
}