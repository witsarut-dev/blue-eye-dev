<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_category_match extends MX_Controller {

	var $item_run_time = 10;
    var $item_run_now = 10000;
    var $item_process = 1000;
	var $tb_match = "own_match";
	var $tb_cate_match = "own_cate_match";

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        $this->load->model("login/login_model");
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
        ini_set('memory_limit', '8192M');
        log_run_time("start time=");
        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
		$this->tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";
		$this->tb_cate_match  = ($company_keyword_type=="Company") ? "own_cate_match" : "competitor_cate_match";
		$rowsdata = $this->get_category($company_keyword_type,"time");
		foreach($rowsdata as $k_row=>$v_row) {

			$this->search_mongo_feed($v_row,"time");
			$this->search_mongo_comment($v_row,"time");

            if(@$v_row["task_id"]=="") {
                $save = array();
                $save["task_type"]  = "Category";
                $save["client_id"]  = $v_row['client_id'];
                $save["company_keyword_id"]  = $v_row['company_keyword_id'];
                $save["category_id"] = $v_row['category_id'];
                $save["run_time"]    = date("Y-m-d H:i:s");
                $this->db->insert("sys_task",$save);
            } else {
                $save = array();
                $save["run_time"]   = date("Y-m-d H:i:s");
                $this->db->where("task_id",$v_row["task_id"]);
                $this->db->update("sys_task",$save);
            }
		}
        log_run_time("end time=");
	}

    function shell_exec_now($type = "1")
    {
        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
        $rowsdata = $this->login_model->get_client();

        foreach($rowsdata as $k_row=>$v_row) {
            $client_id = $v_row['client_id'];
            $params    = "task_category_match run_now {$type} {$client_id}";
            $cmd_check = 'ps -ef | grep "'.ROOT_PATH.' '.$params.'" | grep -v grep | wc -l';
            $pid_count = trim(shell_exec($cmd_check));
            if($pid_count!=="0") {
                echo "Process task_category_match is running.";
            } else {
                $cmd_run  = PHP_PATH." ".ROOT_PATH." {$params}";
                shell_exec($cmd_run);
            }
        }
    }

    function run_now($type = "1",$client_id = 0)
    {   
        log_run_time("start now=");
        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
        $this->tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";
        $this->tb_cate_match  = ($company_keyword_type=="Company") ? "own_cate_match" : "competitor_cate_match";
        
        $rowsdata = $this->get_category($company_keyword_type,"now",$client_id);
        
        // if($client_id>0) {
        //     foreach($rowsdata as $k_row=>$v_row) {
        //         $save = array();
        //         $save["run_now"]   = date("Y-m-d H:i:s");
        //         $this->db->where("task_id",$v_row["task_id"]);
        //         $this->db->update("sys_task",$save);
        //     }
        // }

        foreach($rowsdata as $k_row=>$v_row) {

            $this->search_mongo_feed($v_row,"now");
            $this->search_mongo_comment($v_row,"now");

            if(@$v_row["task_id"]=="") {
                $save = array();
                $save["task_type"]  = "Category";
                $save["client_id"]  = $v_row['client_id'];
                $save["company_keyword_id"]  = $v_row['company_keyword_id'];
                $save["category_id"] = $v_row['category_id'];
                $save["run_now"]    = date("Y-m-d H:i:s");
                $this->db->insert("sys_task",$save);
            } else {
                $save = array();
                $save["run_now"]   = date("Y-m-d H:i:s");
                $this->db->where("task_id",$v_row["task_id"]);
                $this->db->update("sys_task",$save);
            }
        }
        log_run_time("end now=");
    }

	function get_category($company_keyword_type = "Company",$run = "time",$client_id = 0)
	{
        if($run=="time") {
            //$this->db->where("(DATE(sys_task.run_time) <> '".date("Y-m-d")."' OR sys_task.run_time IS NULL)",null,false);
            $this->db->where("sys_task.run_time IS NULL",null,false);
            $this->db->limit($this->item_run_time);
            $this->db->order_by("MIN(sys_task.run_time)","ASC");
        } else {
            if($client_id>0) $this->db->where("sys_task.client_id",$client_id);
            $this->db->limit($this->item_run_now);
            $this->db->order_by("MIN(sys_task.run_now)","ASC");
        }

		$rowsdata = $this->db
                    ->select("MAX(sys_task.task_id) AS task_id")
                    ->select("category.client_id,company_keyword.company_keyword_id,category.category_id,category.category_name")
                    ->group_by("category.client_id,company_keyword.company_keyword_id,category.category_id,category.category_name")
                    ->where("company_keyword.company_keyword_type",$company_keyword_type)
                    ->where(where_client_expire(),null,false)
					->join("company_keyword","company_keyword.client_id = client.client_id")
					->join("category","category.client_id = client.client_id")
                    ->join("sys_task","sys_task.company_keyword_id = company_keyword.company_keyword_id AND sys_task.category_id = category.category_id AND sys_task.task_type = 'Category'","left")
					->get("client")
					->result_array();

		return $rowsdata;
	}

	function search_mongo_feed($rec = array(),$run = "time")
    {
    	$tb_match = $this->tb_match;
    	$tb_cate_match = $this->tb_cate_match;
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;

        if($run=="time") {
            $collection = $mongodb->selectCollection("Feed");
            $query = array('feedcontent' => new MongoRegex("/".$rec['category_name']."/i"));
        } else {
            $collection = $mongodb->selectCollection("DairyFeed");
            $start_time = date("Y-m-d H",strtotime('-24 hours'));
            $end_time   = date("Y-m-d H");
            $query =  array(
                '$and' => array(
                    array("feedcontent"=>new MongoRegex("/".$rec['category_name']."/i"))
                ),
                '$or' => array(
                    array("feedtimestamp"=>new MongoRegex("/{$start_time}/")),
                    array("feedtimestamp"=>new MongoRegex("/{$end_time}/"))
                )
            );
        }

        $cursor = $collection->find($query);
        $cursor->timeout(-1);
       
        foreach($cursor as $k_row=>$v_row) {
        	$rec['sourceid'] = $v_row['sourceid'];
        	$rec['msg_id']   = $v_row['_id'];
        	$result = $this->check_data_macth($rec);
        	$match_id = $this->get_match_id($rec);

        	if(!isset($result["{$tb_cate_match}_id"]) && $match_id!=0) {
        		$save = array();
        		$save["company_keyword_id"] = $rec["company_keyword_id"];
        		$save["client_id"]      = $rec["client_id"];
        		$save["category_id"]    = $rec["category_id"];
        		$save["{$tb_match}_id"] = $match_id;
        		$this->db->insert("{$tb_cate_match}",$save);
        	} 
        }
        $mongo->close();
    }

    function search_mongo_comment($rec = array(),$run = "time")
    {
    	$tb_match = $this->tb_match;
    	$tb_cate_match = $this->tb_cate_match;
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;

        if($run=="time") {
            $collection = $mongodb->selectCollection("Comment");
            $query = array('commentcontent' => new MongoRegex("/".$rec['category_name']."/i"));
        } else {
            $collection = $mongodb->selectCollection("DairyComment");
            $start_time = date("Y-m-d H",strtotime('-24 hours'));
            $end_time   = date("Y-m-d H");
            $query =  array(
                '$and' => array(
                    array("commentcontent"=>new MongoRegex("/".$rec['category_name']."/i"))
                ),
                '$or' => array(
                    array("commenttimestamp"=>new MongoRegex("/{$start_time}/")),
                    array("commenttimestamp"=>new MongoRegex("/{$end_time}/"))
                )
            );
        }

        $cursor = $collection->find($query);
        $cursor->timeout(-1);
       
        foreach($cursor as $k_row=>$v_row) {
        	$rec['sourceid'] = $v_row['sourceid'];
        	$rec['msg_id']   = $v_row['_id'];
        	$result = $this->check_data_macth($rec);
        	$match_id = $this->get_match_id($rec);

        	if(!isset($result["{$tb_cate_match}_id"]) && $match_id!=0) {
        		$save = array();
        		$save["company_keyword_id"] = $rec["company_keyword_id"];
        		$save["client_id"]      = $rec["client_id"];
        		$save["category_id"]    = $rec["category_id"];
        		$save["{$tb_match}_id"] = $match_id;
        		$this->db->insert("{$tb_cate_match}",$save);
        	} 
        }
        $mongo->close();
    }

    function get_match_id($rec = array())
    {
    	$tb_match = $this->tb_match;
    	$result = $this->db
    				->select("{$tb_match}_id AS match_id")
    				->where("company_keyword_id",$rec['company_keyword_id'])
					->where("client_id",$rec['client_id'])
					->where("msg_id",$rec['msg_id'])
					->where("sourceid",$rec['sourceid'])
					->get("{$tb_match}")
					->first_row("array");

		return isset($result['match_id']) ? $result['match_id'] : 0;
    }

    function check_data_macth($rec = array())
    {
    	$tb_match = $this->tb_match;
    	$tb_cate_match = $this->tb_cate_match;
    	$result = $this->db
    				->select("{$tb_cate_match}_id")
    				->where("{$tb_match}.company_keyword_id",$rec['company_keyword_id'])
					->where("{$tb_match}.client_id",$rec['client_id'])
					->where("{$tb_match}.msg_id",$rec['msg_id'])
					->where("{$tb_match}.sourceid",$rec['sourceid'])
					->where("category.category_name",$rec["category_name"])
					->join("{$tb_cate_match}","{$tb_cate_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
					->join("category","{$tb_cate_match}.category_id = category.category_id")
					->get("{$tb_match}")
					->first_row("array");

		return $result;
    }

}