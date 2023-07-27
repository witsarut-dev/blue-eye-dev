<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_company_match extends MX_Controller {

	var $item_run_time = 10;
    var $item_run_now = 1000;
	var $tb_match = "own_match";

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);
	}

    function index($type = "1") 
    {
        //$this->run_time($type);
        //$this->run_now($type);
    }

	function run_time($type = "1")
	{	
  //       log_run_time("start=");
  //       $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
		// $this->tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";

		// $rowsdata = $this->get_company($company_keyword_type,"time");
		// foreach($rowsdata as $k_row=>$v_row) {

		// 	$this->search_mongo_feed($v_row,"time");
		// 	$this->search_mongo_comment($v_row,"time");

  //           if(@$v_row["task_id"]=="") {
  //               $save = array();
  //               $save["task_type"]  = "Company";
  //               $save["client_id"]  = $v_row['client_id'];
  //               $save["company_keyword_id"]  = $v_row['company_keyword_id'];
  //               $save["run_time"]   = date("Y-m-d H:i:s");
  //               $this->db->insert("sys_task",$save);
  //           } else {
  //               $save = array();
  //               $save["run_time"]   = date("Y-m-d H:i:s");
  //               $this->db->where("task_id",$v_row["task_id"]);
  //               $this->db->update("sys_task",$save);
  //           }
		// }
  //       log_run_time("end=");
	}

    function run_now($type = "1")
    {   
        // log_run_time("start now=");
        // $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
        // $this->tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";

        // $rowsdata = $this->get_company($company_keyword_type,"now");
        // foreach($rowsdata as $k_row=>$v_row) {

        //     $this->search_mongo_feed($v_row,"now");
        //     $this->search_mongo_comment($v_row,"now");

        //     if(@$v_row["task_id"]=="") {
        //         $save = array();
        //         $save["task_type"]  = "Company";
        //         $save["client_id"]  = $v_row['client_id'];
        //         $save["company_keyword_id"]  = $v_row['company_keyword_id'];
        //         $save["run_now"]    = date("Y-m-d H:i:s");
        //         $this->db->insert("sys_task",$save);
        //     } else {
        //         $save = array();
        //         $save["run_now"]   = date("Y-m-d H:i:s");
        //         $this->db->where("task_id",$v_row["task_id"]);
        //         $this->db->update("sys_task",$save);
        //     }
        // }
        // log_run_time("end now=");
    }

	function get_company($company_keyword_type = "Company",$run = "time")
	{
        if($run=="time") {
            $this->db->where("(DATE(sys_task.run_time) <> '".date("Y-m-d")."' OR sys_task.run_time IS NULL)",null,false);
            $this->db->limit($this->item_run_time);
            $this->db->order_by("sys_task.run_time","ASC");
        } else {
            $this->db->limit($this->item_run_now);
            $this->db->order_by("sys_task.run_now","ASC");
        }

		$rowsdata = $this->db
					->select("sys_task.task_id,company_keyword.*")
					->where("company_keyword.company_keyword_type",$company_keyword_type)
					->join("company_keyword","company_keyword.client_id = client.client_id")
                    ->join("sys_task","sys_task.company_keyword_id = company_keyword.company_keyword_id AND sys_task.task_type = 'Company'","left")
					->get("client")
					->result_array();

		return $rowsdata;
	}

	function search_mongo_feed($rec = array(),$run = "time")
    {
    	$tb_match = $this->tb_match;
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;

        if($run=="time") {
            $collection = $mongodb->selectCollection("Feed");
        } else {
            $collection = $mongodb->selectCollection("DairyFeed");
        }

        $query = array('feedcontent' => 
            array(
                '$regex' => new MongoRegex("/".$rec['company_keyword_name']."/")
            )
        );

        $cursor = $collection->find($query);
       
        foreach($cursor as $k_row=>$v_row) {

        	$rec['sourceid'] = $v_row['sourceid'];
        	$rec['msg_id']   = $v_row['_id'];

        	$save = array();
        	$save["post_share"] = isset($v_row["feedshares"]) ? $v_row["feedshares"] : 0;
        	$result = $this->check_data_macth($rec);

        	if(!isset($result["{$tb_match}_id"])) {
        		$save["company_keyword_id"] = $rec["company_keyword_id"];
        		$save["{$tb_match}_sentiment"]  = null;
        		$save["client_id"]  = $rec["client_id"];
        		$save["sourceid"]   = $rec["sourceid"];
        		$save["msg_id"]     = $rec["msg_id"];
        		$save["match_type"] = "Feed";
        		$save["post_user"]  = ($rec["sourceid"]!=4) ? $v_row["feeduser"] : null;
        		$save["msg_time"]   = date("Y-m-d H:i:s",strtotime($v_row["feedtimepost"]));
        		$save["msg_status"] = "1";
        		$this->db->insert("{$tb_match}",$save);
        	} else {
        		$this->db->where("{$tb_match}_id",$result["{$tb_match}_id"]);
        		$this->db->update("{$tb_match}",$save);
        	}
        }
        $mongo->close();
    }

    function search_mongo_comment($rec = array(),$run = "time")
    {
    	$tb_match = $this->tb_match;
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
        $collection = $mongodb->selectCollection("Comment");

        if($run=="time") {
            $collection = $mongodb->selectCollection("Comment");
        } else {
            $collection = $mongodb->selectCollection("DairyComment");
        }

        $query = array('commentcontent' => 
            array(
                '$regex' => new MongoRegex("/".$rec['company_keyword_name']."/")
            )
        );

        $cursor = $collection->find($query);
       
        foreach($cursor as $k_row=>$v_row) {
        	$rec['sourceid'] = $v_row['sourceid'];
        	$rec['msg_id']   = $v_row['_id'];
        	$result = $this->check_data_macth($rec);

        	$save = array();
        	$save["post_share"] = isset($v_row["commentshares"]) ? $v_row["commentshares"] : 0;

        	if(!isset($result["{$tb_match}_id"])) {
        		$save["company_keyword_id"] = $rec["company_keyword_id"];
        		$save["{$tb_match}_sentiment"]  = null;
        		$save["client_id"]  = $rec["client_id"];
        		$save["sourceid"]   = $rec["sourceid"];
        		$save["msg_id"]     = $rec["msg_id"];
        		$save["match_type"] = "Comment";
                $save["post_user"]  = ($rec["sourceid"]!=4) ? $v_row["commentuser"] : null;
        		$save["msg_time"]   = date("Y-m-d H:i:s",strtotime($v_row["commenttimepost"]));
        		$save["msg_status"] = "1";
        		$this->db->insert("{$tb_match}",$save);
        	} else {
        		$this->db->where("{$tb_match}_id",$result["{$tb_match}_id"]);
        		$this->db->update("{$tb_match}",$save);
        	}
        }
        $mongo->close();
    }

    function check_data_macth($rec = array())
    {
    	$tb_match = $this->tb_match;
    	$result = $this->db
    				->select("{$tb_match}_id")
    				->where("company_keyword_id",$rec['company_keyword_id'])
					->where("client_id",$rec['client_id'])
					->where("msg_id",$rec['msg_id'])
					->where("sourceid",$rec['sourceid'])
					->get("{$tb_match}")
					->first_row("array");

		return $result;
    }

}