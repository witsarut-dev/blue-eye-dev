<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_sentiment extends MX_Controller {

	var $item_run_time = 1000;
	var $tb_match = "own_match";

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

    function clear_keyword()
    {
        $this->db->query("DELETE FROM own_key_match  WHERE keyword_id NOT IN (SELECT keyword_id FROM keyword)");
        $this->db->query("DELETE FROM competitor_key_match WHERE keyword_id NOT IN (SELECT keyword_id FROM keyword)");
        $this->db->query("DELETE FROM own_match WHERE own_match_id NOT IN (SELECT own_match_id FROM own_key_match)");
        $this->db->query("DELETE FROM competitor_match WHERE competitor_match_id NOT IN (SELECT competitor_match_id FROM competitor_key_match )");
        $this->db->query("DELETE FROM own_match_daily WHERE own_match_id NOT IN (SELECT own_match_id FROM own_key_match)");
        $this->db->query("DELETE FROM competitor_match_daily WHERE competitor_match_id NOT IN (SELECT competitor_match_id FROM competitor_key_match )");
        $this->db->query("DELETE FROM own_match_3months WHERE own_match_id NOT IN (SELECT own_match_id FROM own_key_match)");
        $this->db->query("DELETE FROM competitor_match_3months WHERE competitor_match_id NOT IN (SELECT competitor_match_id FROM competitor_key_match )");
        $this->db->query("DELETE FROM sys_task WHERE task_type='Keyword' AND keyword_id NOT IN (SELECT keyword_id FROM keyword)");
        $this->db->query("DELETE FROM client_keyword WHERE keyword_id NOT IN (SELECT keyword_id FROM keyword)");
        $this->db->query("DELETE FROM own_cate_match WHERE own_match_id NOT IN (SELECT own_match_id FROM own_match)");
        $this->db->query("DELETE FROM competitor_cate_match WHERE competitor_match_id NOT IN (SELECT competitor_match_id FROM competitor_match)");
    }

	function run_time($type = "1")
	{	
        log_run_time("start=");
        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
		$tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";

        $rowsdata = $this->get_sentiment($tb_match);
        // print_r($rowsdata);
        // die();
        $rsFeed  = $this->get_feed_type($rowsdata);
        $arrFeed = $this->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"]);

		foreach($rowsdata as $k_row=>$v_row) {

            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }
        
            $text = @$feed['post_detail'];
            if(strlen($text) > 200) {
                $save = array();
                $save["{$tb_match}_sentiment"] = 0;
                $this->db->where("msg_id",$v_row["msg_id"]);
                $this->db->update("{$tb_match}",$save);

                $this->db->where("msg_id",$v_row["msg_id"]);
                $this->db->update("{$tb_match}_daily",$save);

                $this->db->where("msg_id",$v_row["msg_id"]);
                $this->db->update("{$tb_match}_3months",$save);

            } else {
                $sentiment = get_sentiment_ida_api($v_row['msg_id'],$text,600);

                if($sentiment!==null) {
                    $save = array();
                    $save["{$tb_match}_sentiment"] = $sentiment;
                    $this->db->where("msg_id",$v_row["msg_id"]);
                    $this->db->update("{$tb_match}",$save);

                    $this->db->where("msg_id",$v_row["msg_id"]);
                    $this->db->update("{$tb_match}_daily",$save);

                    $this->db->where("msg_id",$v_row["msg_id"]);
                    $this->db->update("{$tb_match}_3months",$save);
                } else {
                    $save = array();
                    $save["{$tb_match}_sentiment"] = 0;
                    $this->db->where("msg_id",$v_row["msg_id"]);
                    $this->db->update("{$tb_match}",$save);

                    $this->db->where("msg_id",$v_row["msg_id"]);
                    $this->db->update("{$tb_match}_daily",$save);

                    $this->db->where("msg_id",$v_row["msg_id"]);
                    $this->db->update("{$tb_match}_3months",$save);
                }
            }
        }

        log_run_time("end=");
	}

	function get_sentiment($tb_match = "own_match")
	{
		$rowsdata = $this->db
                    ->select("{$tb_match}_id AS match_id,match_type,msg_id")
                    ->where("{$tb_match}_sentiment IS NULL",null,false)
                    ->where("DATE(msg_time) >=","2021-01-01")
                    ->limit($this->item_run_time)
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
                $result[$v_row['_id']] = array("post_user"=>@$v_row['feeduser'],"post_link"=>@$v_row['feedlink'],"post_detail"=>@$v_row['feedcontent']);
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
                $result[$v_row['_id']] = array("post_user"=>@$v_row['commentuser'],"post_link"=>@$v_row['commentlink'],"post_detail"=>@$v_row['commentcontent']);
            }
            $mongo->close();
        }
        return $result;
    }

}