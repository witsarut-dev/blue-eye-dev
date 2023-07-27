<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_keyword_match extends MX_Controller {

	var $item_run_time = 100;
    var $item_run_now = 10000;
    var $item_process = 1000;
	var $tb_match = "own_match";
	var $tb_key_match = "own_key_match";

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
		$this->tb_key_match  = ($company_keyword_type=="Company") ? "own_key_match" : "competitor_key_match";

		$rowsdata = $this->get_keyword($company_keyword_type,"time");
		foreach($rowsdata as $k_row=>$v_row) {

			$this->search_mongo_feed($v_row,"time");
			$this->search_mongo_comment($v_row,"time");

            if(@$v_row["task_id"]=="") {
                $save = array();
                $save["task_type"]  = "Keyword";
                $save["client_id"]  = $v_row['client_id'];
                $save["company_keyword_id"]  = $v_row['company_keyword_id'];
                $save["keyword_id"] = $v_row['keyword_id'];
                $save["run_time"]   = date("Y-m-d H:i:s");
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
            $params    = "task_keyword_match run_now {$type} {$client_id}";
            $cmd_check = 'ps -ef | grep "'.ROOT_PATH.' '.$params.'" | grep -v grep | wc -l';
            $pid_count = trim(shell_exec($cmd_check));
            if($pid_count!=="0") {
                echo "Process task_keyword_match is running.";
            } else {
                $cmd_run  = PHP_PATH." ".ROOT_PATH." {$params}";
                shell_exec($cmd_run);
            }
        }

    }

    function run_now($type = "1",$client_id = 0)
    {
        ini_set('memory_limit', '2048M');
        log_run_time("start now=");
        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
        $this->tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";
        $this->tb_key_match  = ($company_keyword_type=="Company") ? "own_key_match" : "competitor_key_match";

        $rowsdata = $this->get_keyword($company_keyword_type,"now",$client_id);
        

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
                $save["task_type"]  = "Keyword";
                $save["client_id"]  = $v_row['client_id'];
                $save["company_keyword_id"]  = $v_row['company_keyword_id'];
                $save["keyword_id"] = $v_row['keyword_id'];
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

	function get_keyword($company_keyword_type = "Company",$run = "time",$client_id = 0)
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
					->select("keyword.client_id,keyword.company_keyword_id,keyword.keyword_id,keyword.keyword_name,keyword.thai_only")
                    ->select("(SELECT COUNT(*) FROM include_exclude_keyword ie WHERE type = 'include' and ie.keyword_id = keyword.keyword_id) AS include")
                    ->select("(SELECT COUNT(*) FROM include_exclude_keyword ie WHERE type = 'exclude' and ie.keyword_id = keyword.keyword_id) AS exclude")
                    ->group_by("keyword.client_id,keyword.company_keyword_id,keyword.keyword_id,keyword.keyword_name,keyword.thai_only")
                    ->where("company_keyword.company_keyword_type",$company_keyword_type)
                    ->where("keyword.status","active")
                    ->where(where_client_expire(),null,false)
					->join("company_keyword","company_keyword.client_id = client.client_id")
					->join("keyword","company_keyword.company_keyword_id = keyword.company_keyword_id")
                    ->join("sys_task","sys_task.company_keyword_id = keyword.company_keyword_id AND sys_task.keyword_id = keyword.keyword_id AND sys_task.task_type = 'Keyword'","left")
                    ->join("include_exclude_keyword","include_exclude_keyword.keyword_id = keyword.keyword_id","left")
					->get("client")
                    ->result_array();

		return $rowsdata;
	}

	function search_mongo_feed($rec = array(),$run = "time")
    {
    	$tb_match = $this->tb_match;
    	$tb_key_match = $this->tb_key_match;
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;

        $rec_replace = str_replace(".", "\.", $rec['keyword_name']); //replace . to \.
        // $query = array('feedcontent' => new MongoRegex("/".$rec_replace."/i"));

        if($run=="time") {
            $collection = $mongodb->selectCollection("Feed");
        } else {
            $collection = $mongodb->selectCollection("DairyFeed");
        }

        $rec_include = "";
        $rec_exclude = "";

        if (strpos($rec_replace, '_') !== false) {
            $rec_replace = str_replace('_', ' ', $rec_replace);
        }

        if($rec['include'] > 0 || $rec['exclude'] > 0) {
            // $rec_replace = str_replace(".","\.",$rec['keyword_name']); //replace . to \.

            if($rec['include'] > 0) {

                $rowsdata = $this->db->select("includeexclude_name")
                                     ->where("keyword_id",$rec["keyword_id"])
                                     ->where("type","include")
                                     ->get("include_exclude_keyword")
                                     ->result_array();
                foreach($rowsdata as $k_row=>$v_row) {
                    // $rec_include .= ".*" . $v_row['includeexclude_name'];
                    $rec_include .= $v_row['includeexclude_name'] . "|";
                }
                $rec_include = rtrim($rec_include, "|");

                if($rec['exclude'] > 0) {
                    $rowsdata = $this->db->select("includeexclude_name")
                                         ->where("keyword_id",$rec["keyword_id"])
                                         ->where("type","exclude")
                                         ->get("include_exclude_keyword")
                                         ->result_array();
                    foreach($rowsdata as $k_row=>$v_row) {
                        $rec_exclude .= $v_row['includeexclude_name'] . "|";
                    }
                    $rec_exclude = rtrim($rec_exclude, "|");
                    $query = array(
                                    '$and' => array(
                                                     array( 'feedcontent' => new MongoRegex("/" . preg_quote($rec_replace) . ".*(". $rec_include.")/i")),
                                                     array( 'feedcontent' => array( '$not' => new MongoRegex("/".$rec_exclude."/i")))
                                                   )
                                  );
                } else {
                    $query = array('feedcontent' => new MongoRegex("/" . preg_quote($rec_replace) . ".*(" . $rec_include . ")/i"));
                }
                
            } else {
                $rowsdata = $this->db->select("includeexclude_name")
                                     ->where("keyword_id",$rec["keyword_id"])
                                     ->where("type","exclude")
                                     ->get("include_exclude_keyword")
                                     ->result_array();
                foreach($rowsdata as $k_row=>$v_row) {
                    $rec_exclude .= $v_row['includeexclude_name'] . "|";
                }
                $rec_exclude = rtrim($rec_exclude, "|");
                $query = array(
                                '$and' => array(
                                                 array( 'feedcontent' => new MongoRegex("/" . preg_quote($rec_replace) . "/i")),
                                                 array( 'feedcontent' => array( '$not' => new MongoRegex("/".$rec_exclude."/i")))
                                               )
                              );
            }
        } else {
            // $rec_replace = str_replace(".","\.",$rec['keyword_name']); //replace . to \.
            $query = array('feedcontent' => new MongoRegex("/" . preg_quote($rec_replace) . "/i"));
        }

        $cursor = $collection->find($query);
        $cursor->timeout(-1);

        print_r($query);

        foreach($cursor as $k_row=>$v_row) {

        	$rec['sourceid'] = $v_row['sourceid'];
        	$rec['msg_id']   = $v_row['_id'];
        	$result          = $this->check_data_macth($rec);
        	$match_id        = $this->get_match_id($rec);
            $post_user_id    = $this->add_post_user($v_row["feeduser"],$v_row["sourceid"]);
            $check_reg  = true;

            print_r($v_row);

        	if(!isset($result["{$tb_key_match}_id"])) {

                if($match_id == 0) {
                    //$sentiment = get_sentiment_api(@$v_row['feedcontent'],1);
                    if($rec['thai_only']=='1') {
                        $check_reg = $this->check_thai_only(@$v_row['feedcontent']);
                    }
                    
                    if($check_reg) {
                        $sentiment = null;
                        $save = array();
                        $save["company_keyword_id"]    = $rec["company_keyword_id"];
                        $save["{$tb_match}_sentiment"] = $sentiment;
                        $save["client_id"]             = $rec["client_id"];
                        $save["sourceid"]              = $rec["sourceid"];
                        $save["msg_id"]                = $rec["msg_id"];
                        $save["match_type"]            = "Feed";
                        $save["post_share"]            = $v_row["feedshares"];
                        $save["post_user_id"]          = ($rec["sourceid"]!=4) ? $post_user_id : 0;
                        $save["post_user"]             = ($rec["sourceid"]!=4) ? $v_row["feeduser"] : null;
                        $save["msg_time"]              = date("Y-m-d H:i:s",strtotime($v_row["feedtimepost"]));
                        $save["msg_status"]            = "1";
                        $this->db->insert("{$tb_match}",$save);
                        $match_id = $this->db->insert_id("{$tb_match}");

                        $save["{$tb_match}_id"] = $match_id;
                        $this->db->insert("{$tb_match}_daily",$save);

                        $this->db->insert("{$tb_match}_3months",$save);
                    }
                }

                if($check_reg) {
                    $save = array();
                    $save["company_keyword_id"] = $rec["company_keyword_id"];
                    $save["client_id"]     = $rec["client_id"];
                    $save["keyword_id"]    = $rec["keyword_id"];
                    $save["{$tb_match}_id"] = $match_id;
                    $this->db->insert("{$tb_key_match}",$save);
                }
        	}
        }
        $mongo->close();
    }

    function search_mongo_comment($rec = array(),$run = "time")
    {
    	$tb_match = $this->tb_match;
    	$tb_key_match = $this->tb_key_match;
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;

        $rec_replace = str_replace(".","\.",$rec['keyword_name']); //replace . to \.
        // $query = array('commentcontent' => new MongoRegex("/".$rec_replace."/i"));
    
        if($run=="time") {
            $collection = $mongodb->selectCollection("Comment");
        } else {
            $collection = $mongodb->selectCollection("DairyComment");
        }

        $rec_include = "";
        $rec_exclude = "";

        if (strpos($rec_replace, '_') !== false) {
            $rec_replace = str_replace('_', ' ', $rec_replace);
        }

        if($rec['include'] > 0 || $rec['exclude'] > 0) {
            // $rec_replace = str_replace(".","\.",$rec['keyword_name']); //replace . to \.

            if($rec['include'] > 0) {
                $rowsdata = $this->db->select("includeexclude_name")
                                     ->where("keyword_id",$rec["keyword_id"])
                                     ->where("type","include")
                                     ->get("include_exclude_keyword")
                                     ->result_array();
                foreach($rowsdata as $k_row=>$v_row) {
                    // $rec_include .= ".*" . $v_row['includeexclude_name'];
                    $rec_include .= $v_row['includeexclude_name'] . "|";
                }
                $rec_include = rtrim($rec_include, "|");

                if($rec['exclude'] > 0) {
                    $rowsdata = $this->db->select("includeexclude_name")
                                         ->where("keyword_id",$rec["keyword_id"])
                                         ->where("type","exclude")
                                         ->get("include_exclude_keyword")
                                         ->result_array();
                    foreach($rowsdata as $k_row=>$v_row) {
                        $rec_exclude .= $v_row['includeexclude_name'] . "|";
                    }
                    $rec_exclude = rtrim($rec_exclude, "|");

                    $query = array(
                                    '$and' => array(
                                                    array( 'commentcontent' => new MongoRegex("/" . preg_quote($rec_replace) .".*(". $rec_include .")/i")),
                                                    array( 'commentcontent' => array( '$not' => new MongoRegex("/".$rec_exclude."/i")))
                                                )
                                );

                } else {
                    $query = array('commentcontent' => new MongoRegex("/" . preg_quote($rec_replace) . ".*(". $rec_include.")/i"));
                }
                
            } else {
                $rowsdata = $this->db->select("includeexclude_name")
                                     ->where("keyword_id",$rec["keyword_id"])
                                     ->where("type","exclude")
                                     ->get("include_exclude_keyword")
                                     ->result_array();
                foreach($rowsdata as $k_row=>$v_row) {
                    $rec_exclude .= $v_row['includeexclude_name'] . "|";
                }

                $rec_exclude = rtrim($rec_exclude, "|");
                $query = array(
                                '$and' => array(
                                                 array( 'commentcontent' => new MongoRegex("/" . preg_quote($rec_replace) . "/i")),
                                                 array( 'commentcontent' => array( '$not' => new MongoRegex("/".$rec_exclude."/i")))
                                               )
                              );
            }
        } else {
            $query = array('commentcontent' => new MongoRegex("/" . preg_quote($rec_replace) . "/i"));
        }

        $cursor = $collection->find($query);
        $cursor->timeout(-1);

        foreach($cursor as $k_row=>$v_row) {
        	$rec['sourceid'] = $v_row['sourceid'];
        	$rec['msg_id']   = $v_row['_id'];
        	$result          = $this->check_data_macth($rec);
        	$match_id        = $this->get_match_id($rec);
            $post_user_id    = $this->add_post_user($v_row["commentuser"],$v_row["sourceid"]);
            $check_reg       = true;

        	if(!isset($result["{$tb_key_match}_id"])) {

                if($match_id == 0) {
                    //$sentiment = get_sentiment_api(@$v_row['commentcontent'],1);
                    if($rec['thai_only']=='1') {
                        $check_reg = $this->check_thai_only(@$v_row['commentcontent']);
                    }

                    if($check_reg) {
                        $sentiment = null;
                        $save = array();
                        $save["company_keyword_id"]     = $rec["company_keyword_id"];
                        $save["{$tb_match}_sentiment"]  = $sentiment;
                        $save["client_id"]              = $rec["client_id"];
                        $save["sourceid"]               = $rec["sourceid"];
                        $save["msg_id"]                 = $rec["msg_id"];
                        $save["match_type"]             = "Comment";
                        $save["post_share"]             = 0;
                        $save["post_user_id"]           = ($rec["sourceid"]!=4) ? $post_user_id : 0;
                        $save["post_user"]              = ($rec["sourceid"]!=4) ? $v_row["commentuser"] : null;
                        $save["msg_time"]               = date("Y-m-d H:i:s",strtotime($v_row["commenttimepost"]));
                        $save["msg_status"]             = "1";
                        $this->db->insert("{$tb_match}",$save);
                        $match_id = $this->db->insert_id("{$tb_match}");

                        $save["{$tb_match}_id"] = $match_id;
                        $this->db->insert("{$tb_match}_daily",$save);

                        $this->db->insert("{$tb_match}_3months",$save);
                    }
                }

                if($check_reg) {
                    $save = array();
                    $save["company_keyword_id"] = $rec["company_keyword_id"];
                    $save["client_id"]     = $rec["client_id"];
                    $save["keyword_id"]    = $rec["keyword_id"];
                    $save["{$tb_match}_id"] = $match_id;
                    $this->db->insert("{$tb_key_match}",$save);
                }
        	}
        }
        $mongo->close();
    }

    function get_match_id($rec = array()) {
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

    function check_data_macth($rec = array()) {
    	$tb_match = $this->tb_match;
    	$tb_key_match = $this->tb_key_match;
    	$result = $this->db
    				->select("{$tb_key_match}_id")
    				->where("{$tb_match}.company_keyword_id",$rec['company_keyword_id'])
					->where("{$tb_match}.client_id",$rec['client_id'])
					->where("{$tb_match}.msg_id",$rec['msg_id'])
					->where("{$tb_match}.sourceid",$rec['sourceid'])
					->where("keyword.keyword_name",$rec["keyword_name"])
					->join("{$tb_key_match}","{$tb_key_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
					->join("keyword","{$tb_key_match}.keyword_id = keyword.keyword_id")
					->get("{$tb_match}")
					->first_row("array");

		return $result;
    }

    function add_post_user($post_user = "",$sourceid = 0) {
        $post_user_id = 0;
        if($post_user!="" && $sourceid <> 4) {
            $insert_query = sprintf("INSERT IGNORE INTO post_user_match (post_user,sourceid) VALUES ('%s','%s');",
                mysql_real_escape_string($post_user),
                mysql_real_escape_string($sourceid));
            $this->db->query($insert_query);

            $rec = $this->db
                ->select("post_user_id")
                ->where("post_user",$post_user)
                ->where("sourceid",$sourceid)
                ->get("post_user_match")
                ->first_row("array");

            $post_user_id = intval(@$rec['post_user_id']);
        }

        return $post_user_id;
    }

    function check_thai_only($text) {
        $check = false;
        if (preg_match('/\p{Thai}+/u', $text) === 1) {
            $check = true;
        }else{
            $check = false;
        }
        return $check;
    }
}