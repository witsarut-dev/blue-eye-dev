<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_clear_match extends MX_Controller {

	var $tb_match = "own_match";
    var $tb_key_match  = "own_key_match";
    var $tb_cate_match = "own_cate_match";

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);
        ini_set('memory_limit', '8192M');
	}

    function index($type = "1") 
    {
        //$this->run_time($type);
    }

	function run_time($type = "1")
	{	
        log_run_time("start=");
        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
		$tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";
        $tb_key_match  = ($company_keyword_type=="Company") ? "own_key_match" : "competitor_key_match";
        $tb_cate_match = ($company_keyword_type=="Company") ? "own_cate_match" : "competitor_cate_match";

		$rowsdata = $this->get_match_data($tb_match);

        $rsFeed  = $this->get_feed_type($rowsdata);
        $arrFeed = $this->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"]);

		foreach($rowsdata as $k_row=>$v_row) {

            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }

            if(!isset($feed['msg_id'])) {
                $where_delete = array("{$tb_match}_id"=>$v_row['match_id']);

                $this->db->delete("{$tb_cate_match}",$where_delete);
                $this->db->delete("{$tb_key_match}",$where_delete);
                $this->db->delete("{$tb_match}",$where_delete);
            }
		}

        log_run_time("end=");
	}

	function get_match_data($tb_match = "own_match")
	{
		$rowsdata = $this->db
					->select("{$tb_match}_id AS match_id,match_type,msg_id")
                    ->where("DATE({$tb_match}.msg_time) <",date("Y-m-d"))
					->get("{$tb_match}")
					->result_array();

		return $rowsdata;
	}

    function get_feed_type($rowsdata = array())
    {
        $arrFeed = array();
        $arrComment = array();
        foreach($rowsdata as $k_row=>$v_row) {
            if($v_row['match_type']=="Feed") {
                array_push($arrFeed,$v_row['msg_id']);
            } else {
                array_push($arrComment,$v_row['msg_id']);
            }
        }
        return array("arrFeed"=>$arrFeed,"arrComment"=>$arrComment);
    }

    function get_mongo_feed($msg_id = array())
    {
        $result = array();
        if(count($msg_id)>0) {
            $mongo = new MongoClient(MONGO_CONNECTION);
            $mongodb = $mongo->blue_eye;
            $collection = $mongodb->selectCollection("Feed");
            $query = array('_id' => array('$in'=>$msg_id));
            $cursor = $collection->find($query);
           
            foreach($cursor as $k_row=>$v_row) {
                $result[$v_row['_id']] = array("msg_id"=>@$v_row['_id']);
            }
            $mongo->close();
        }
        return $result;
    }

    function get_mongo_comment($msg_id = array())
    {
        $result = array();
        if(count($msg_id)>0) {
            $mongo = new MongoClient(MONGO_CONNECTION);
            $mongodb = $mongo->blue_eye;
            $collection = $mongodb->selectCollection("Comment");
            $query = array('_id' => array('$in'=>$msg_id));
            $cursor = $collection->find($query);
           
            foreach($cursor as $k_row=>$v_row) {
                $result[$v_row['_id']] = array("msg_id"=>@$v_row['_id']);
            }
            $mongo->close();
        }
        return $result;
    }

}