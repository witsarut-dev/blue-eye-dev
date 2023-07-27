<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class task_timeline extends MX_Controller {

    var $item_run_time = 10;
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

        $this->mongo = new MongoClient(MONGO_CONNECTION);
        $this->mongodb = $this->mongo->blue_eye;
	}

    function run_timeline() 
    {
        $rowsdata = $this->get_client_timeline();
        foreach($rowsdata as $k_row=>$v_row) {
            echo $v_row['timeline_id'];
            $this->add_timeline_list($v_row['timeline_id'],$v_row['client_id']);
        }
    }

    private function get_timeline_id($timeline_id = 0)
    {
        $rowsdata = $this->db
                    ->select("client_timeline.*")
                    ->where("client_timeline.timeline_id",$timeline_id)
                    ->get("client_timeline")
                    ->first_row('array');

        return $rowsdata;
    }

    private function get_client_timeline()
    {
        $result = $this->db
                    ->select("client_timeline.*")
                    ->or_where("client_timeline.timeline_status",'0')
                    ->or_where("client_timeline.end_date >=",date("Y-m-d"))
                    ->order_by("client_timeline.timeline_id","ASC")
                    ->limit($this->item_run_time)
                    ->get("client_timeline")
                    ->result_array();

        return $result;
    }

    private function add_timeline_list($timeline_id = 0,$client_id = 0)
    {
        $rec = $this->get_timeline_id($timeline_id);
        $start_date = $rec['start_date'];
        $end_date = $rec['end_date'];

        $rows_msg_date = array();
        foreach(array("Feed","Comment") as $msg_type) {
            $rowsdata = $this->get_mongo_timeline($rec,$msg_type);
            foreach($rowsdata as $val) {
                $prefix = strtolower($msg_type);
                $date = date("Y-m-d",strtotime($val[$prefix.'timepost']));
                if(!isset($rows_msg_date[$date])) $rows_msg_date[$date] = array();
                $data = array();
                $data['msg_id'] = $val['_id'];
                $data['post_detail'] = mb_substr(@$val[$prefix.'content'],0,100);
                $data['post_link']   = @$val[$prefix.'link'];
                $data['post_user']   = @$val[$prefix.'user'];
                array_push($rows_msg_date[$date],$data);
            }
        }
        
        foreach($rows_msg_date as $msg_date=>$rowsdata) {

            foreach($rowsdata as $k_row=>$v_row) {

                $msg_id = $v_row['msg_id'];

                if($rec['timeline_status']==1 || $msg_date==date("Y-m-d")) {
                    $tb_own_match = "own_match_daily";
                } else {
                    $tb_own_match = "own_match";
                }

                $own_match = $this->db
                    ->where("own_match.msg_id",$msg_id)
                    ->where("own_match.client_id",$client_id)
                    ->get($tb_own_match." own_match")
                    ->first_row('array');

                if(isset($own_match['own_match_id'])) {
                    $num_rows = $this->db
                        ->select("timeline_list_id")
                        ->where("timeline_id",$timeline_id)
                        ->where("client_id",$client_id)
                        ->where("msg_id",$msg_id)
                        ->get("client_timeline_list")
                        ->num_rows();
                    
                    if($num_rows==0) {
                        $save = array();
                        $save['timeline_id'] = $timeline_id;
                        $save['msg_date']    = $msg_date;
                        $save['msg_id']      = $own_match['msg_id'];
                        $save['client_id']   = $own_match['client_id'];
                        $save['sourceid']    = $own_match['sourceid'];
                        $save['post_user']   = $v_row['post_user'];
                        $save['post_detail'] = $v_row['post_detail'];
                        $save['post_link']   = $v_row['post_link'];
                        $save['sentiment']   = $own_match['own_match_sentiment'];
                        $save['msg_time']    = $own_match['msg_time'];
                        $this->db->insert("client_timeline_list",$save);
                    }
                }
            }
        }

        $save = array();
        $save['timeline_status'] = 1;
        $this->db->where("timeline_id",$timeline_id);
        $this->db->update("client_timeline",$save);
    }

    private function get_mongo_timeline($rec,$msg_type = "Feed") 
    {
        $start_time = strtotime($rec['start_date']);
        $end_time = strtotime($rec['end_date']);
        $keyword_name = $rec['keyword_name'];

        if($rec['timeline_status']==1 || $start_time==$end_time) {
            $Dairy = "Dairy";
            $start_time = $end_time;
        } else {
            $Dairy = "";
        }

        $timepost = array();
        while($start_time<=$end_time) {
            $reg_date = new MongoRegex( "/". date("Y-m-d",$start_time) ."/" );
            array_push($timepost,$reg_date);
            $start_time = strtotime('+1 days',$start_time);
        }

        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
        $rec_replace = str_replace(".","\.",$keyword_name);
        if($msg_type=="Feed") {
            $collection = $mongodb->selectCollection($Dairy."Feed");
            $query = array('feedtimepost' => array('$in'=>$timepost),"feedcontent"=>new MongoRegex("/".$rec_replace."/i"));
        } else {
            $collection = $mongodb->selectCollection($Dairy."Comment");
            $query = array('commenttimepost' => array('$in'=>$timepost),"commentcontent"=>new MongoRegex("/".$rec_replace."/i"));
        }
        $cursor = $collection->find($query);
        $cursor->timeout(-1);
        return iterator_to_array($cursor,false);
    }

    private function update_percent($timeline_id,$start_date,$end_date,$msg_date)
    {
        // $dStart = new DateTime($start_date);
        // $dEnd  = new DateTime($end_date);
        // $dDiff = $dStart->diff($dEnd);
        // $days1 = ($dDiff->days+1);

        // $dStart = new DateTime($start_date);
        // $dEnd  = new DateTime($msg_date);
        // $dDiff = $dStart->diff($dEnd);
        // $days2 = ($dDiff->days+1);
        
        // $percent = intval(($days2*100) / $days1);
        // $percent = ($percent>=100) ? 99 : $percent;

        // $save = array();
        // $save['percent'] = $percent;
        // $this->db->where("timeline_id",$timeline_id);
        // $this->db->update("client_timeline",$save);
    }

}