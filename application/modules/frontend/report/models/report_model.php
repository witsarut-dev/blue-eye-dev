<?php
class Report_model extends CI_Model {

	var $CLIENT_ID;
    var $custom_date;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
        $this->custom_date = $this->master_model->get_custom_date();
    }

    function get_feed($post = array())
    {
        return $this->realtime_model->get_feed($post,"own");
    }

    function get_top_share($post = array())
    {
        // 3months check
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $config = $this->master_model->get_config();
        $top_share  = isset($config['top_share']) ? $config['top_share'] : 10;

        $result = array();

        $this->master_model->get_where_current(@$post["period"]);

        if(isset($post['keyword']) && $post['keyword']!="") {
            $this->db->where("own_match.own_match_id IN (SELECT own_match_id FROM own_key_match JOIN keyword ON own_key_match.keyword_id = keyword.keyword_id WHERE keyword_name = '".$post['keyword']."')",null,false);
        }

        $rowsdata = $this->db->select("own_match.*")
                             ->where("own_match.client_id",$this->CLIENT_ID)
                             ->where("sourceid", 1)
                             ->order_by('own_match.post_share DESC, own_match.msg_time DESC')
                             ->limit($top_share)
                             ->get("{$table_match}")
                             ->result_array();

        $rsFeed = $this->realtime_model->get_feed_type($rowsdata);

        $arrFeed = $this->realtime_model->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->realtime_model->get_mongo_comment($rsFeed["arrComment"]);

        foreach($rowsdata as $k_row=>$v_row)
        {
            $sourceid = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);

            if($v_row['match_type'] == 'Feed') {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }
            $post_detail = mb_substr($feed['post_detail'], 0, 70).'...';
            array_push($result,
                       array('post_id'     => $v_row['msg_id'],
                             'post_link'   => $feed['post_link'],
                             'post_detail' => $post_detail,
                             'post_time'   => $v_row['msg_time'],
                             'sourceid'    => $sourceid,
                             'post_type'   => $media_full,
                             'icon'        => get_icon_post_type($v_row['sourceid']),
                             'text_time'   => get_post_time($v_row['msg_time']),
                             'count_share' => $v_row['post_share']
                        )
            );
        }
        return $result;
    }

    function get_top_user($post = array())
    {
        // $table_match = get_match_table("own_match", @$post["period"]);

        // 3months check
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $config = $this->master_model->get_config();
        $top_user  = isset($config['top_user']) ? $config['top_user'] : 10;

        $result = array();

        $this->master_model->get_where_current(@$post["period"]);

        if(isset($post['keyword']) && $post['keyword']!="") {
            $this->db->where("own_match.own_match_id IN (SELECT own_match_id FROM own_key_match JOIN keyword ON own_key_match.keyword_id = keyword.keyword_id WHERE keyword_name = '".$post['keyword']."')",null,false);
        }

        $rowsdata = $this->db
                    ->select("own_match.post_user_id")
                    ->select("SUM(own_match.own_match_sentiment) AS sumSentiment")
                    ->select("COUNT(*) As count_post")
                    ->group_by("own_match.post_user_id")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->where("own_match.sourceid <>",4)
                    ->where("own_match.post_user_id <>",'0')
                    ->where("own_match.post_user <>",'')
                    ->order_by("count_post DESC")
                    ->limit($top_user)
                    ->get("{$table_match}")
                    ->result_array();
        foreach($rowsdata as $k_row=>$v_row)
        {
            $user = $this->master_model->get_post_user($v_row['post_user_id']);
            $sentiment = ($v_row["count_post"]>0) ? round($v_row["sumSentiment"]/$v_row["count_post"],2) : 0;
            array_push($result,array("post_user_id"=>@$v_row["post_user_id"],
                "post_name"=>@$user["post_user"],
                "sourceid"=>@$user["sourceid"],
                "sentiment"=>get_sentiment($sentiment,'display-full'),
                'icon'=> get_icon_post_type(@$user['sourceid']),
                "count_post"=>$v_row["count_post"]));
        }
        return $result;
    }


    function get_word_cloud($post = array())
    {
        // $table_match = get_match_table("own_match", @$post["period"]);

        // 3months check
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $result = array();

        $this->master_model->get_where_current(@$post["period"]);
        
        $rowsdata = $this->db
                    ->select("keyword.keyword_name")
                    ->select("COUNT(*) As share_count")
                    ->group_by("keyword.keyword_name")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->join("own_key_match own_key","own_key.own_match_id = own_match.own_match_id")
                    ->join("keyword","keyword.keyword_id = own_key.keyword_id")
                    ->get("{$table_match}")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            array_push($result,array("key" => $v_row["keyword_name"],"share_count" => $v_row["share_count"]));
        }
        return $result;
    }

    function get_top5sentiment($post = array())
    {
        // $table_match = get_match_table("own_match", @$post["period"]);

        // 3months check
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $arrBefore = array();
        $data = array();
        $client_keyword_id = array();
        $result = array("mediaData"=>array());
        $rowskey = $this->db
            ->select("keyword.keyword_id,keyword.keyword_name")
            ->group_by("keyword.keyword_id,keyword.keyword_name")
            ->where("keyword.client_id",$this->CLIENT_ID)
            ->get("keyword")
            ->result_array();
      
        foreach($rowskey as $k_row=>$v_row) {
            $keyword_id = $v_row['keyword_id'];
            if(!isset($data[$keyword_id])) {
                $data[$keyword_id] = array();
                $data[$keyword_id]["name"] = $v_row["keyword_name"];
                array_push($client_keyword_id,$keyword_id);
            }
        }

        $this->db->where("own_match.client_id",$this->CLIENT_ID);
        $this->master_model->get_where_current(@$post["period"]);
        if(count($client_keyword_id)>0) {
            $this->db->where_in("own_key.keyword_id",$client_keyword_id);
        } else {
            $this->db->where("own_key.keyword_id","0");
        }

        $rowsdata = $this->db
                    ->select("own_key.keyword_id,own_match.client_id")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
                    ->select("COUNT(own_key.keyword_id) AS countTotal")
                    ->join("own_key_match own_key IGNORE INDEX (idx_keyword_id)","own_match.own_match_id = own_key.own_match_id")
                    ->group_by("own_key.keyword_id,own_match.client_id")
                    ->order_by("countTotal","desc")
                    ->where("own_match.own_match_sentiment <> ",0)
                    ->get("{$table_match}")
                    ->result_array();

        $top_key = array();
        foreach($rowsdata as $k_row=>$v_row) {
            $client_id = $v_row['client_id'];
            if(!isset($top_key[$client_id])) $top_key[$client_id] = array();
            array_push($top_key[$client_id],$v_row);
        }

        /*media current*/
        $this->master_model->get_where_current(@$post["period"]);
        $rowsdata = $this->db
                    ->select("own_match.client_id")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
                    ->select("count(*) AS countData")
                    ->group_by("own_match.client_id")
                    ->order_by("countData","desc")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    //->where("own_match.own_match_sentiment <> ",0)
                    ->get("{$table_match}")
                    ->result_array();
                    
        $totalData = 0;
        foreach($rowsdata as $k_row=>$v_row) {
            $totalData += $v_row['countData'];
        }
       
        foreach($rowsdata as $k_row=>$v_row) {
            $client_id = $v_row['client_id'];
            $countCurrent = intval($v_row['countData']);
            $countBefore  = intval(@$arrBefore[$client_id]);

            if(!isset($top_key[$client_id])) $top_key[$client_id] = array();
            $topPositive  = $this->overview_model->get_top_keyword($top_key[$client_id],5,'countPositive',$data);
            $topNegative  = $this->overview_model->get_top_keyword($top_key[$client_id],5,'countNegative',$data);

            $countTotal = ($v_row['countPositive'] + $v_row['countNegative']);
            $countPositive = ($countTotal>0) ? round(($v_row["countPositive"]*100)/$countTotal) : 0;
            $countNegative = ($countTotal>0) ? round(($v_row["countNegative"]*100)/$countTotal) : 0;
            $countPercent  = ($totalData>0) ? round(($countCurrent*100)/$totalData,2) : 0;

            array_push($result["mediaData"],
                array(
                    "y"=>$countCurrent,
                    "countPositive"=>$countPositive,
                    "countNegative"=>$countNegative,
                    "countPercent"=>number_format($countPercent,2),
                    "topPositive"=>$topPositive,
                    "topNegative"=>$topNegative,
                ));
        }
        
        return  $result;
    }

    function get_post_monitoring($post = array())
    {
        $client_post = $this->post_monitoring_model->get_client_post();
       
        $result = array();

        if(!empty($client_post)){
            //active reactive expire
			foreach($client_post as $k_row => $v_row){
                $likes = array();
                $shares = array();
                $comments = array();
                $data = $this->post_monitoring_model->get_mongo_post($v_row['msg_id']);

                $start_date = strtotime($v_row['start_date']); 
                $created_date = strtotime($v_row['created_date']); 
                $endDate = strtotime($v_row['end_date']);
                $now_date = strtotime(date('Y-m-d H:i'));
               
                $expireEnd = strtotime($v_row['end_date'].' +1 months');
                $status = "";

              if(!empty($data)){
                if($now_date >= $start_date && $now_date <= $endDate && $created_date == $start_date){
                    $end_date = $endDate;
                    $status = "Active";
                }else if($now_date >= $start_date && $now_date <= $endDate && $created_date > $start_date){
                    $end_date = $endDate;
                    $status = "Reactive";
                }else if($now_date >= $start_date && ($created_date == $start_date || $created_date > $start_date)){
                    $end_date = $expireEnd;
                    $status = "Expire";
                }
                if(isset($data[0]['likes'])){
                    $l_max = 0;
                    $l_max_count = 0;
                    foreach($data[0]['likes'] as $k_likes => $v_likes){
                        $time = strtotime(date("Y-m-d H:i",strtotime($v_likes['timestamp'])));
                        if($time>=$start_date && $time<=$end_date){
                            if ($time > $l_max){
                                $l_max = $time;
                                $l_max_count = $v_likes['likes_count'];
                            }
                        }
                    }
                    array_push($likes,array("timestamp"=>$l_max,"likes_count"=>$l_max_count));
                }
                if(isset($data[0]['shares'])){
                    $s_max = 0;
                    $s_max_count = "";
                    foreach($data[0]['shares'] as $k_shares => $v_shares){
                        $time = strtotime(date("Y-m-d H:i",strtotime($v_shares['timestamp'])));
                        if($time>=$start_date && $time<=$end_date){
                            if ($time > $s_max){
                                $s_max = $time;
                                $s_max_count = $v_shares['shares_count'];
                            }
                        }
                    }
                    array_push($shares,array("timestamp"=>$s_max,"shares_count"=>$s_max_count));
                }
                if(isset($data[0]['comments'])){
                    $c_max = 0;
                    $c_max_count = "";
                    foreach($data[0]['comments'] as $k_comments => $v_comments){
                        $time = strtotime(date("Y-m-d H:i",strtotime($v_comments['timestamp'])));
                        if($time>=$start_date && $time<=$end_date){
                            if ($time > $c_max){
                                $c_max = $time;
                                $c_max_count = $v_comments['comments_count'];
                            }
                        }
                    }
                    array_push($comments,array("timestamp"=>$c_max,"comments_count"=>$c_max_count));
                }
                array_push($result,
                    array(
                        "msg_id"=>$v_row['msg_id'],
                        "topic"=>$v_row['post_name'],
                        "url"=>$v_row['post_url'],
                        "likes"=>$likes[0]['likes_count'],
                        "shares"=>$shares[0]['shares_count'],
                        "comments"=>$comments[0]['comments_count'],
                        "qrcode"=>$this->qrcode($v_row['post_url'],$v_row['msg_id']),
                        "status"=>$status
                    )
                );
              }
            }  
        return  $result;
        }
    }

    function qrcode($url, $msg_id){
        require_once(BASEPATH."/libraries/phpqrcode/qrlib.php");  
        $dir_name = $this->session->userdata('session_id');
		$path = "upload/genPDF/".$dir_name."/";
		$name =  $msg_id.date('ymdhis').'.png';
		$fileName = $path.$name;
		QRcode::png($url,$fileName);
		return $fileName;
    }

    function check_intime_keyword($post = array())
	{   
        // $table_match = get_match_table("own_match", $post["period"]);

        // 3months check
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $this->master_model->get_where_current($post["period"]);
        $result = $this->db
                    ->select("COUNT(*) AS Intime")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->get("{$table_match}")
                    ->first_row('array');
        return $result;
    }

    function get_mediapos_data_withImg($post = array(), $company_keyword_id)
    {
    	$result = array();

        $v_row = $this->setting_model->get_company($company_keyword_id);
        $tb_match = ($v_row['company_keyword_type']!="Competitor") ? "own_match" : "competitor_match";
        $tb_cate_match  = ($v_row['company_keyword_type']!="Competitor") ? "own_cate_match" : "competitor_cate_match";

        // $table_match = get_match_table($tb_match, @$post["period"]);

        // 3months check
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = $tb_match."_3months ".$tb_match;
            } else {
                $table_match = get_match_table($tb_match, @$post["period"]);
            }
        } else {
            $table_match = get_match_table($tb_match, @$post["period"]);
        }

        $this->master_model->get_where_current(@$post["period"], $tb_match);
        
        $rowsdata = $this->db
            ->select("{$tb_match}.sourceid")
            ->group_by("{$tb_match}.sourceid")
            ->where("{$tb_match}.company_keyword_id",$company_keyword_id)
            ->where("{$tb_match}.client_id",$this->CLIENT_ID)
            ->where("{$tb_match}.sourceid IS NOT NULL",null,false)
            ->join("{$tb_cate_match}","{$tb_cate_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
            ->get("{$table_match}")
            ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {

            $sourceid = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            $media_color = get_media_color($sourceid);

            $this->master_model->get_where_before(@$post["period"],$tb_match);
            $rec1 = $this->db
                ->select("SUM({$tb_match}.{$tb_match}_sentiment/100) AS sumSentiment")
                ->select("COUNT(*) AS countTotal")
                ->where("{$tb_match}.sourceid",$sourceid)
                ->where("{$tb_match}.company_keyword_id",$company_keyword_id)
                ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                ->where("{$tb_match}.{$tb_match}_sentiment IS NOT NULL",null,false)
                ->join("{$tb_cate_match}","{$tb_cate_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
                ->get("{$table_match}")
                ->first_row('array');
            $beforeSentiment = ($rec1["countTotal"]>0) ? round(($rec1["sumSentiment"]/$rec1["countTotal"]),2) : 0;

            $this->master_model->get_where_current(@$post["period"],$tb_match);
            $rec2 = $this->db
                ->select("SUM({$tb_match}.{$tb_match}_sentiment/100) AS sumSentiment")
                ->select("COUNT(*) AS countTotal")
                ->where("{$tb_match}.sourceid",$sourceid)
                ->where("{$tb_match}.company_keyword_id",$company_keyword_id)
                ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                ->where("{$tb_match}.{$tb_match}_sentiment IS NOT NULL",null,false)
                ->join("{$tb_cate_match}","{$tb_cate_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
                ->get("{$table_match}")
                ->first_row('array');
            $currentSentiment = ($rec2["countTotal"]>0) ? round(($rec2["sumSentiment"]/$rec2["countTotal"]),2) : 0;

            if($rec1["sumSentiment"]!="" && $rec2["sumSentiment"]!="") {
                $countTotal  = ($rec1["countTotal"]+$rec2["countTotal"]);
                $beforeMention   = 0;
                $currentMention  = 0;
                if($countTotal>0) {
                    $beforeMention = ($countTotal>0) ? round(($rec1["countTotal"]/$countTotal),2) : 0;
                    $currentMention = ($countTotal>0) ? round(($rec2["countTotal"]/$countTotal),2) : 0;
                }
                $data1  = array($beforeMention,$beforeSentiment);
                $data2  = array($currentMention,$currentSentiment);
                array_push($result,array("name"=>$media_short,"total"=>$countTotal,"color"=>$media_color,"data"=>array($data1,$data2)));
            }
        }
        if(count($result)==0) {
            array_push($result,array());
        }
    	return  $result;
    }

    function get_daterange($post = array())
    {
        // var_dump($post);
        $datetime1 = new DateTime();
        $date = date("jS F Y");
        $date_1d = $date;
        $date_1w = date("jS F Y", strtotime($date . '-7 days'));
        $date_1m = date("jS F Y", strtotime($date . '-30 days'));
        $date_3m = date("jS F Y", strtotime($date . '-90 days'));
        $datetimeW = new DateTime($date_1w);
        $datetimeM = new DateTime($date_1m);
        $datetime3M = new DateTime($date_3m);
        $dateRange = '';
        $period = @$post["period"];

        if ($period == "Today") {
            $dateRange = $date_1d . ' - ' . $date . ' (Today)';
        } else if ($period == "1W") {
            $interval = $datetime1->diff($datetimeW);
            $dateRange = $interval->format( $date_1w . ' - ' . $date . ' (' . '%a days)');
        } else if ($period == "1M") {
            $interval = $datetime1->diff($datetimeM);
            $dateRange = $interval->format( $date_1m . ' - ' . $date . ' (' . '%a days)');
        } else if ($period == "3M") {
            $interval = $datetime1->diff($datetime3M);
            $dateRange = $interval->format( $date_3m . ' - ' . $date . ' (' . '%a days)');
        } else if ($period == "Custom") {
            $obj = get_custom_date($this->custom_date);
            $start = date("jS F Y", strtotime($obj['start']. '0 days'));
            $end = date("jS F Y", strtotime($obj['end']. '0 days'));
            $datetimeStart = new DateTime($start);
            $datetimeEnd = new DateTime($end);
            $interval = $datetimeStart->diff($datetimeEnd);
            $dateRange = $interval->format( $start . ' - ' . $end . ' (' . '%a days)');
        }
        return $dateRange;
    }

    function daterange($post = array()){
        $date_format ="";
        if (@$post["period"] == "Custom") {
            $where_time_own = $this->master_model->get_where_current_manual(@$post["period"]);
            $a = str_replace("'","",explode(" ",$where_time_own));
            $date1 = new DateTime($a[2]);
            $date2 = new DateTime($a[4]);
            $interval = $date1->diff($date2);
            $interval->days;
            if ($interval->days > 90) {
                $date_format = '%Y-%m';
            } elseif($interval->days >= 7) {
                $date_format = '%Y-%m-%d';
            } else {
                $date_format = '%Y-%m-%d %H:00:00';
            }
        }elseif (@$post["period"] == "Today") {
            $date_format = '%Y-%m-%d %H:00:00';
        }else {
            $date_format = '%Y-%m-%d';
        }
        return $date_format;
    }

    function get_mention_data($post = array())
    {
        $date_format = $this->daterange($post);
        // $table_match = get_match_table("own_match",@$post["period"]);
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }
        $result = array();
        /*mention*/
        $this->master_model->get_where_current(@$post["period"]);
        $countMention = $this->db
            ->select("own_match.own_match_id as own_match_id")
            ->select("COUNT(own_match.own_match_id) as count")
            ->select("DATE_FORMAT(own_match.msg_time,'".$date_format."') AS match_time",false)
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->group_by("match_time")
            ->get("{$table_match}")
            ->result_array();
        foreach($countMention as $k_row=>$v_row) {
            array_push($result,$v_row);
        }
        return  $result;
    }

    function get_social_media_data($post = array())
    {
        $date_format = $this->daterange($post);
        // $table_match = get_match_table("own_match",@$post["period"]);
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $sourceid = array(1,2,3,6,7,8);

        $result = array();
        $this->master_model->get_where_current(@$post["period"]);
        $countMention = $this->db
            ->select("own_match.own_match_id as own_match_id")
            ->select("COUNT(own_match.own_match_id) as count")
            ->select("DATE_FORMAT(own_match.msg_time,'".$date_format."') AS match_time",false)
            ->where("own_match.client_id", $this->CLIENT_ID)
            ->where_in("own_match.sourceid", $sourceid)
            ->group_by("match_time")
            ->get("{$table_match}")
            ->result_array();
        foreach($countMention as $k_row=>$v_row) {
            array_push($result, $v_row);
        }
        return  $result;
    }
    function get_non_social_media_data($post = array())
    {
        $date_format = $this->daterange($post);
        // $table_match = get_match_table("own_match",@$post["period"]);
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $sourceid = array(4,5);

        $result = array();
        $this->master_model->get_where_current(@$post["period"]);
        $countMention = $this->db
            ->select("own_match.own_match_id as own_match_id")
            ->select("COUNT(own_match.own_match_id) as count")
            ->select("DATE_FORMAT(own_match.msg_time,'".$date_format."') AS match_time",false)
            ->where("own_match.client_id", $this->CLIENT_ID)
            ->where_in("own_match.sourceid", $sourceid)
            ->group_by("match_time")
            ->get("{$table_match}")
            ->result_array();
        foreach($countMention as $k_row=>$v_row) {
            array_push($result, $v_row);
        }
        return  $result;
    }

    function get_mentions_per_category ($post = array())
    {
        $date_format = $this->daterange($post);
        $result = array('countFB' => array(),'countTW' => array(),'countYT' => array(),'countNW' => array(),'countPT' => array(),'countIG' => array(),'countTT' => array(),'countLN' => array());
        for ($x = 1; $x <= count($result); $x++) {
            // $table_match = get_match_table("own_match",@$post["period"]);
            if(@$post["period"] == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if($obj['start'] >= $date_3m) {
                    $table_match = "own_match_3months own_match";
                } else {
                    $table_match = get_match_table("own_match", @$post["period"]);
                }
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
            $this->master_model->get_where_current(@$post["period"]);
            $countFB = $this->db
                ->select("own_match.own_match_id as own_match_id")
                ->select("COUNT(own_match.own_match_id) as count")
                ->select("DATE_FORMAT(own_match.msg_time,'".$date_format."') AS match_time",false)
                ->where("own_match.client_id", $this->CLIENT_ID)
                ->where("own_match.sourceid", $x)
                ->group_by("match_time")
                ->get("{$table_match}")
                ->result_array();
                foreach($countFB as $k_row=>$v_row) {
                    if ($x == 1) {
                        array_push($result["countFB"],$v_row);
                    }elseif($x == 2){
                        array_push($result["countTW"],$v_row);
                    }elseif($x == 3){
                        array_push($result["countYT"],$v_row);
                    }elseif($x == 4){
                        array_push($result["countNW"],$v_row);
                    }elseif($x == 5){
                        array_push($result["countPT"],$v_row);
                    }elseif($x == 6){
                        array_push($result["countIG"],$v_row);
                    }elseif($x == 7){
                        array_push($result["countTT"],$v_row);
                    }elseif($x == 8){
                        array_push($result["countBD"],$v_row);
                    }elseif($x == 9){
                        array_push($result["countLN"],$v_row);
                    }
                }
        }
        return  $result;
    }

    function get_group_keyword_list($post = array())
	{
		// $table_match = get_match_table("own_match", @$post["period"]);
        // $table_match_comp = get_match_table("competitor_match", @$post["period"]);
        $result = array();

        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
                // $table_match_comp = "competitor_match_3months competitor_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
                // $table_match_comp = get_match_table("competitor_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
            // $table_match_comp = get_match_table("competitor_match", @$post["period"]);
        }

        $rowsdata = $this->db->query("select group_keyword.group_keyword_name AS group_keyword_name , own_match.sourceid , count(DISTINCT(own_key_match.own_match_id)) AS count_num
                                        from ".$table_match."
                                        left join own_key_match ON own_match.own_match_id = own_key_match.own_match_id
                                        left join keyword ON own_key_match.keyword_id = keyword.keyword_id
                                        left join group_keyword ON keyword.group_keyword_id = group_keyword.group_keyword_id
                                        where own_match.client_id = ".$this->CLIENT_ID."
                                        and group_keyword.status = 'active'
                                        and ".$this->master_model->get_where_current_manual(@$post["period"],"own_match")."
                                        group by keyword.group_keyword_id , own_match.sourceid
                                        order by group_keyword_name ASC")->result_array();

        $array_keep = array_fill(1, 8, 0);

        for ($x = 0; $x < count($rowsdata); $x++) {
            $array_keep_gname = $rowsdata[$x]['group_keyword_name'];
            if ($array_keep_gname == $rowsdata[$x-1]['group_keyword_name'] && $x > 0 ) {
                if ($rowsdata[$x]['sourceid'] == 1) {$array_keep[1] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 2) {$array_keep[2] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 3) {$array_keep[3] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 4) {$array_keep[4] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 5) {$array_keep[5] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 6) {$array_keep[6] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 7) {$array_keep[7] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 9) {$array_keep[8] = $rowsdata[$x]['count_num'];}
                if ($x == count($rowsdata)-1) {
                    array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
                    $array_keep = array_fill(1, 8, 0);
                }
            } else {
                if ($x == 0) {
                    if ($rowsdata[$x]['sourceid'] == 1) {$array_keep[1] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 2) {$array_keep[2] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 3) {$array_keep[3] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 4) {$array_keep[4] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 5) {$array_keep[5] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 6) {$array_keep[6] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 7) {$array_keep[7] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 9) {$array_keep[8] = $rowsdata[$x]['count_num'];}
                    if ($x == count($rowsdata)-1) {
                        array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
                        $array_keep = array_fill(1, 8, 0);
                    }
                } else {
                    array_push($result, array("name"=>$rowsdata[$x-1]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
                    $array_keep = array_fill(1, 8, 0);
                    if ($rowsdata[$x]['sourceid'] == 1) {$array_keep[1] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 2) {$array_keep[2] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 3) {$array_keep[3] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 4) {$array_keep[4] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 5) {$array_keep[5] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 6) {$array_keep[6] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 7) {$array_keep[7] = $rowsdata[$x]['count_num'];}
                    if ($rowsdata[$x]['sourceid'] == 9) {$array_keep[8] = $rowsdata[$x]['count_num'];}
                    if ($x == count($rowsdata)-1) {
                        array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
                        $array_keep = array_fill(1, 8, 0);
                    }
                }
            }
        }
        // $myJSON1 = json_encode($result);
        return $result;
	}

    function get_graph_sentiment_data($post = array())
    {
        // $table_match = get_match_table("own_match",@$post["period"]);
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }
        $custom_time = false;
        $period = @$post["period"];
        $result = array();
        $data = array();
        $time = array();
        $temp = array();

        $data_graph_sentiment = array();
        // $data["name"]  = array("Positive", "Normal", "Negative");
        $data_graph_sentiment[1]["name_sentiment"]  = "Positive";
        $data_graph_sentiment[2]["name_sentiment"]  = "Normal";
        $data_graph_sentiment[3]["name_sentiment"]  = "Negative";

        $data_graph_sentiment[1]["data"] = array();
        $data_graph_sentiment[2]["data"] = array();
        $data_graph_sentiment[3]["data"] = array();
    
        $this->db->where("own_match.client_id",$this->CLIENT_ID);
        $this->master_model->get_where_current(@$post["period"]);

        if(@$post["period"]=="Today") {
            $this->db
            ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
            ->select("DATE_FORMAT(msg_time,'%Y-%m-%d %H:00') AS match_time",false);

        } else if(@$post["period"]=="Custom") {
            $obj = get_custom_date($this->custom_date);
            if($obj['start']==$obj['end']) {
                if($obj['start']==date("Y-m-d")) {
                    $period = "Today";
                    $custom_time = false;
                } else {
                    $custom_time = true;
                }

                $this->db
                ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
                ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
                ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
                ->select("DATE_FORMAT(msg_time,'%Y-%m-%d %H:00') AS match_time",false);


            } else {
                $this->db
                ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
                ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
                ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
                ->select("DATE(own_match.msg_time) AS match_time");

            }
        } else {
            $this->db
            ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
            ->select("DATE(own_match.msg_time) AS match_time");

        }
        $this->db->where("own_match.own_match_sentiment IS NOT NULL",null,false);
        // $this->db->group_by("own_key.keyword_id,match_time");
        $this->db->group_by("match_time");

        $rowsdata = $this->db
                    ->order_by("match_time","ASC")
                    ->get("{$table_match}")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {

            $match_time  = strtotime($v_row['match_time'])*1000;

            $countPositive  = intval($v_row['countPositive']);
            $countNegative  = intval($v_row['countNegative']);
            $countNormal  = intval($v_row['countNormal']);

            array_push($data_graph_sentiment[1]["data"],array($match_time,$countPositive));
            array_push($data_graph_sentiment[2]["data"],array($match_time,$countNormal));
            array_push($data_graph_sentiment[3]["data"],array($match_time,$countNegative));
        }
        foreach($data_graph_sentiment as $k_row=>$v_row) {
            array_push($result,array("name"=>$v_row['name_sentiment'],"data"=>$v_row['data']));
        }
        return  $result;
    }

    function get_group_keyword()
    {
        $rowsdata = $this->db
                    ->select("group_keyword.group_keyword_name,company_keyword.company_keyword_type") // <-- There is never any reason to write this line!
                    ->join("company_keyword", "company_keyword.company_keyword_id = group_keyword.company_keyword_id AND group_keyword.client_id = $this->CLIENT_ID")
                    ->order_by("group_keyword.company_keyword_id,group_keyword.group_keyword_id","ASC")
                    ->get("group_keyword")
                    ->result_array();
        return $rowsdata;
    }

    function get_sources_count($post = array()) {
        // $table_match = get_match_table("own_match",@$post["period"]);
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $this->db->where("own_match.client_id",$this->CLIENT_ID);
        $this->master_model->get_where_current(@$post["period"]);

        $rec = $this->db->select("SUM(CASE WHEN sourceid = '1' THEN 1 ELSE 0 END) AS Facebook")
                             ->select("SUM(CASE WHEN sourceid = '2' THEN 1 ELSE 0 END) AS Twitter")
                             ->select("SUM(CASE WHEN sourceid = '3' THEN 1 ELSE 0 END) AS Youtube")
                             ->select("SUM(CASE WHEN sourceid = '4' THEN 1 ELSE 0 END) AS News")
                             ->select("SUM(CASE WHEN sourceid = '5' THEN 1 ELSE 0 END) AS Pantip")
                             ->select("SUM(CASE WHEN sourceid = '6' THEN 1 ELSE 0 END) AS Instagram")
                             ->select("SUM(CASE WHEN sourceid = '7' THEN 1 ELSE 0 END) AS Tiktok")
                             ->select("SUM(CASE WHEN sourceid = '9' THEN 1 ELSE 0 END) AS Line")
                             ->get("{$table_match}")
                             ->first_row('array');

        $result = array();

        $result["facebook"] = number_format($rec["Facebook"]);
        $result["twitter"] = number_format($rec["Twitter"]);
        $result["youtube"] = number_format($rec["Youtube"]);
        $result["news"] = number_format($rec["News"]);
        $result["pantip"] = number_format($rec["Pantip"]);
        $result["instagram"] = number_format($rec["Instagram"]);
        $result["tiktok"] = number_format($rec["Tiktok"]);
        $result["line"] = number_format($rec["Line"]);

        return  $result;
    }
}
?>