<?php
class Overview_model extends CI_Model {

	var $CLIENT_ID;
    var $custom_date;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID  = $this->authen->getId();
        $this->custom_date = $this->master_model->get_custom_date();
    }

    function get_group_keyword()
    {
        $rowsdata = $this->db
                    ->select("group_keyword.*")
                    ->where("group_keyword.client_id",$this->CLIENT_ID)
                    ->where("company.company_keyword_type","company")
                    ->where("group_keyword.status","active")
                    ->order_by("group_keyword.group_keyword_id","ASC")
                    ->join("company_keyword company","company.company_keyword_id=group_keyword.company_keyword_id")
                    ->get("group_keyword")
                    ->result_array();

        return $rowsdata;
    }

    function get_keyword()
    {
        $result = array();
        $config = $this->master_model->get_config();
        $add_keyword = isset($config['add_keyword']) ? $config['add_keyword'] : 50;

        $rowsdata = $this->db
                    ->select("keyword.*,company.company_keyword_type")
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->where("keyword.status","active")
                    //->where("company.company_keyword_type","company")
                    ->order_by("keyword.keyword_id","ASC")
                    ->join("company_keyword company","company.company_keyword_id=keyword.company_keyword_id")
                    ->limit($add_keyword)
                    ->get("keyword")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            if($v_row['company_keyword_type']=="Company") {
                array_push($result,$v_row);
            }
        }

        return $result;
    }

    function get_keyword_data($post = array())
    {
        // $table_match = get_match_table("own_match",@$post["period"]);

        // 3months check
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match own_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
        }

        $key_arr = array();
        $key_row = $this->setting_model->get_keyword();
        foreach($key_row as $k_row=>$v_row) {
            array_push($key_arr,$v_row['keyword_id']);
        }

        $custom_time = false;
        $period = @$post["period"];
    	$result = array();
        $data = array();
        $time = array();
        $temp = array();
        $client_keyword_id = array();

        if(count($key_arr)>0) {
            $this->db->where_in("keyword.keyword_id",$key_arr);
        }

        $rowskey = $this->db
            ->select("keyword.keyword_id,keyword.keyword_name")
            ->group_by("keyword.keyword_id,keyword.keyword_name")
            ->where("client_keyword.client_id",$this->CLIENT_ID)
            ->where("keyword.status","active")
            ->join("keyword","keyword.keyword_id = client_keyword.keyword_id")
            ->get("client_keyword")
            ->result_array();
    
        foreach($rowskey as $k_row=>$v_row) {
            $keyword_id = $v_row['keyword_id'];
            if(!isset($data[$keyword_id])) {
                $data[$keyword_id] = array();
                $data[$keyword_id]["name"] = $v_row["keyword_name"];
                $data[$keyword_id]["data"] = array();
                array_push($client_keyword_id,$keyword_id);
            }
        }

        $this->db->where("own_match.client_id",$this->CLIENT_ID);
        $this->master_model->get_where_current(@$post["period"]);

        if(@$post["period"]=="Today") {
            $this->db->select("own_key.keyword_id,DATE_FORMAT(own_match.msg_time,'%Y-%m-%d %H:%i') AS match_time,COUNT(*) AS match_count",false);
        } else if(@$post["period"]=="Custom") {
            $obj = get_custom_date($this->custom_date);
            if($obj['start']==$obj['end']) {
                if($obj['start']==date("Y-m-d")) {
                    $period = "Today";
                    $custom_time = false;
                } else {
                    $custom_time = true;
                }
                $this->db->select("own_key.keyword_id,DATE_FORMAT(own_match.msg_time,'%Y-%m-%d %H:%i') AS match_time,COUNT(*) AS match_count",false);
            } else {
                $this->db->select("own_key.keyword_id,DATE(own_match.msg_time) AS match_time,COUNT(*) AS match_count");
            }
        } else {
            $this->db->select("own_key.keyword_id,DATE(own_match.msg_time) AS match_time,COUNT(*) AS match_count");
        }
        $this->db->group_by("own_key.keyword_id,match_time");

        if(count($client_keyword_id)>0) {
            $this->db->where_in("own_key.keyword_id",$client_keyword_id);
        } else {
            $this->db->where("own_key.keyword_id","0");
        }
    	
    	$rowsdata = $this->db
                    ->join("own_key_match own_key IGNORE INDEX (idx_keyword_id)","own_match.own_match_id = own_key.own_match_id")
                    ->order_by("match_time","ASC")
		            ->get("{$table_match}")
		            ->result_array();

    	foreach($rowsdata as $k_row=>$v_row) {
            $keyword_id = $v_row['keyword_id'];
            $match_count = intval($v_row['match_count']);
            $match_time  = strtotime($v_row['match_time']) * 1000;
            $index = $match_time."_".$keyword_id;
            $temp[$index] = $match_count;
    	}
        
        if(@$period=="Today") {
            $rowsdata = array();
            $start = strtotime(date("Y-m-d 00:00"));
            $end   = strtotime(date("Y-m-d H:i"));
            while($start<=$end) {
                $match_time = date("Y-m-d H:i",$start);
                $add_min    = date("Y-m-d H:i",strtotime($match_time." +1 minutes"));
                array_push($rowsdata,array("match_time"=>$match_time));
                $start = strtotime($add_min);
            }
        } else if($custom_time) {
            $obj = get_custom_date($this->custom_date);
            $rowsdata = array();
            $start = strtotime(date($obj['start']." 00:00"));
            $end   = strtotime(date($obj['start']." 23:58"));
            while($start<=$end) {
                $match_time = date("Y-m-d H:i",$start);
                $add_min    = date("Y-m-d H:i",strtotime($match_time." +1 minutes"));
                array_push($rowsdata,array("match_time"=>$match_time));
                $start = strtotime($add_min);
            }
        } else {
            $rowsdata = array();

            $date  = date("Y-m-d");
            $start = strtotime($date);

            if(@$post["period"]=="1W") {
                $start = strtotime($date. ' -6 days');
            } else if(@$post["period"]=="1M") {
                $start = strtotime($date. ' -30 days');
            } else if(@$post["period"]=="3M") {
                $start = strtotime($date. ' -90 days');
            } else if(@$post["period"]=="Custom") {
                $obj   = get_custom_date($this->custom_date);
                $start = strtotime($obj['start']);
                $date  = $obj['end'];
            }

            $end = strtotime($date);

            while($start<=$end) {
                $match_time = date("Y-m-d",$start);
                $add_min    = date("Y-m-d",strtotime($match_time." +1 days"));
                array_push($rowsdata,array("match_time"=>$match_time));
                $start = strtotime($add_min);
            }
        }
        foreach($rowsdata as $k_row=>$v_row) {
            $match_time  = strtotime($v_row['match_time']) * 1000;
            foreach($rowskey as $k2_row=>$v2_row) {
                $keyword_id = $v2_row['keyword_id'];
                $index = $match_time."_".$keyword_id;
                $match_count = isset($temp[$index]) ? $temp[$index] : 0;
                array_push($data[$keyword_id]["data"],array($match_time,$match_count));
            }
        }

        foreach($data as $k_row=>$v_row) {
            array_push($result,array("name"=>$v_row['name'],"data"=>$v_row['data']));
        }

    	return  $result;
    }

    function get_client_keyword($client_id = 0)
    {
        $result = array();
        $rowsdata = $this->db
                    ->select("client_keyword.keyword_id")
                    ->where("client_keyword.client_id",$this->CLIENT_ID)
                    ->get("client_keyword")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            array_push($result,$v_row['keyword_id']);
        }

        return $result;
    }

    function insert_client_keyword($post = array())
    {
        $save_batch = array();
  
        if(isset($post['keyword_id'])) {
            foreach($post['keyword_id'] as $k_row=>$v_row) {
                $save = array();
                $save["client_id"] = $this->CLIENT_ID;
                $save["keyword_id"] = $v_row;
                array_push($save_batch,$save);
            }
        }

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->delete("client_keyword");

        if(count($save_batch)>0) $this->db->insert_batch("client_keyword",$save_batch);
    }

    function get_media_data($post = array())
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

        $result = array("mediaCategories"=>array(),"mediaData"=>array());

        $data = array();
        $client_keyword_id = array();
        $rowskey = $this->db
            ->select("keyword.keyword_id,keyword.keyword_name")
            ->group_by("keyword.keyword_id,keyword.keyword_name")
            ->where("keyword.client_id",$this->CLIENT_ID)
            ->where("keyword.status","active")
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
                    ->select("own_key.keyword_id,own_match.sourceid")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
                    ->select("COUNT(own_key.keyword_id) AS countTotal")
                    ->join("own_key_match own_key IGNORE INDEX (idx_keyword_id)","own_match.own_match_id = own_key.own_match_id")
                    ->group_by("own_key.keyword_id,own_match.sourceid")
                    // ->where("own_match.own_match_sentiment <> ",0)
                    ->get("{$table_match}")
                    ->result_array();

        $top_key = array();
        foreach($rowsdata as $k_row=>$v_row) {
            $sourceid = $v_row['sourceid'];
            if(!isset($top_key[$sourceid])) {
                $top_key[$sourceid] = array();
            }
            array_push($top_key[$sourceid],$v_row);
        }

        /*media current*/
        $this->master_model->get_where_current(@$post["period"]);
        $rowsdata = $this->db
                    ->select("own_match.sourceid")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
                    ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
                    ->select("count(*) AS countData")
                    ->group_by("own_match.sourceid")
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
            $sourceid = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            $media_color = get_media_color($sourceid);
            $media_icon  = get_icon_post_type($sourceid);
            $countCurrent = intval($v_row['countData']);
            $countBefore  = intval(@$arrBefore[$sourceid]);

            if(!isset($top_key[$sourceid])) $top_key[$sourceid] = array();
            $topPositive  = $this->get_top_keyword($top_key[$sourceid],5,'countPositive',$data);
            $topNegative  = $this->get_top_keyword($top_key[$sourceid],5,'countNegative',$data);
            $topNormal    = $this->get_top_keyword($top_key[$sourceid],5,'countNormal',$data);

            $countTotal = ($v_row['countPositive'] + $v_row['countNegative'] + $v_row['countNormal']);
            $countPositive = ($countTotal>0) ? round(($v_row["countPositive"]*100)/$countTotal) : 0;
            $countNegative = ($countTotal>0) ? round(($v_row["countNegative"]*100)/$countTotal) : 0;
            $countNormal   = ($countTotal>0) ? round(($v_row["countNormal"]*100)/$countTotal) : 0;
            $countPercent  = ($totalData>0) ? round(($countCurrent*100)/$totalData,2) : 0;

            $url = site_url("realtime/?media_type=".$media_full);
            array_push($result["mediaCategories"],$media_short);
            $drilldown = array("categories"=>array($media_short),"data"=>array($countBefore));
            array_push($result["mediaData"],
                array(
                    "y"=>$countCurrent,
                    "color"=>$media_color,
                    "url"=>$url,
                    "drilldown"=>$drilldown,
                    "mediaChannel"=>$media_icon.' '.$media_full,
                    "countPositive"=>$countPositive,
                    "countNegative"=>$countNegative,
                    "countNormal"=>$countNormal,
                    "countPercent"=>number_format($countPercent,2),
                    "topPositive"=>$topPositive,
                    "topNegative"=>$topNegative,
                    "topNormal"=>$topNormal,
                ));
        }
        return  $result;
    }

    function get_marketpos_data($post = array())
    {   
    	$result = array();
        $color = array("#90ed7d","#d9534f","#f7a35c","#434348","#169f85");

        $rowsdata = $this->setting_model->get_company_keyword();
        foreach($rowsdata as $k_row=>$v_row) {

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

            $this->master_model->get_where_before(@$post["period"],$tb_match);
            $rec1 = $this->db
                ->select("SUM({$tb_match}.{$tb_match}_sentiment/100) AS sumSentiment")
                ->select("COUNT(*) AS countTotal")
                ->where("{$tb_match}.company_keyword_id",$v_row["company_keyword_id"])
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
                ->where("{$tb_match}.company_keyword_id",$v_row["company_keyword_id"])
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
                    $beforeMention  = ($countTotal>0) ? round(($rec1["countTotal"]/$countTotal),2) : 0;
                    $currentMention = ($countTotal>0) ? round(($rec2["countTotal"]/$countTotal),2) : 0;
                }

                $data1  = array($beforeMention,$beforeSentiment);
                $data2  = array($currentMention,$currentSentiment);

                array_push($result,array("name"=>$v_row["company_keyword_name"],"total"=>$countTotal,"color"=>@$color[$k_row],"data"=>array($data1,$data2)));
            } else {
                array_push($result,array("name"=>$v_row["company_keyword_name"],"total"=>0,"color"=>@$color[$k_row],"data"=>array()));
            }
        }

        if(count($result)==0) array_push($result,array());

    	return  $result;
    }

    function get_mediapos_data($post = array(),$company_keyword_id)
    {
    	$result = array();

        $v_row = $this->setting_model->get_company($company_keyword_id);
        $tb_match = ($v_row['company_keyword_type']!="Competitor") ? "own_match" : "competitor_match";
        $tb_cate_match  = ($v_row['company_keyword_type']!="Competitor") ? "own_cate_match" : "competitor_cate_match";

        // $table_match = get_match_table($tb_match,@$post["period"]);

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

        $this->master_model->get_where_current(@$post["period"],$tb_match);
        
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

        if(count($result)==0) array_push($result,array());

    	return  $result;
    }

    function get_sentiment_data($post = array())
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

        $this->master_model->get_where_current(@$post["period"]);

        $rec = $this->db
            ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
            ->select("COUNT(*) AS countTotal")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.own_match_sentiment IS NOT NULL",null,false)
            ->get("{$table_match}")
            ->first_row('array');

        $result = array();
        $result["Positive"] = ($rec["countTotal"]>0) ? round(($rec["countPositive"]*100)/$rec["countTotal"]) : 0;
        $result["Negative"] = ($rec["countTotal"]>0) ? round(($rec["countNegative"]*100)/$rec["countTotal"]) : 0;
        //$result["Normal"]   = ($rec["countTotal"]>0) ? round(($rec["countNormal"]*100)/$rec["countTotal"]) : 0;
        $result["Normal"]   = 100 - ($result["Positive"]+$result["Negative"]);

        $result["Positive_row"] =$rec["countPositive"];
        $result["Negative_row"] = $rec["countNegative"];
        $result["Normal_row"]   = $rec["countNormal"];

        return  $result;
    }

    function get_total_data($post = array())
    {
        // $table_match = get_match_table("own_match",@$post["period"]);

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

        /*mention count*/
        $this->master_model->get_where_current(@$post["period"]);
        $countMention = $this->db
            // ->select("own_match.own_match_id,own_match.msg_time")
            ->select("own_match.own_match_id")
            ->group_by("own_match.own_match_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->get("{$table_match}")
            ->num_rows();
        $result["mentionCurrent"] = number_format($countMention);

        $this->master_model->get_where_before(@$post["period"]);
        $countMention = $this->db
            ->select("own_match.own_match_id")
            ->group_by("own_match.own_match_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->get("{$table_match}")
            ->num_rows();
        $result["mentionBefore"]  = number_format($countMention);

        /*user count*/
        $this->master_model->get_where_current(@$post["period"]);
        $countUser1 = $this->db
            ->select("own_match.post_user_id")
            ->group_by("own_match.post_user_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.sourceid <>",4)
            ->get("{$table_match}")
            ->num_rows();
        
        $this->master_model->get_where_current(@$post["period"]);
        $countUser2 = $this->db
            ->select("own_match.post_user_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.post_user_id =",0)
            ->where("own_match.sourceid <>",4)
            ->get("{$table_match}")
            ->num_rows();

        if (($countUser1 + $countUser2) <> 0) {
            $countUser = ($countUser1 + $countUser2) - 1;
        } else {
            $countUser = ($countUser1 + $countUser2);
        }
        $result["userCurrent"] = number_format($countUser);

        $this->master_model->get_where_before(@$post["period"]);
        $countUser1 = $this->db
            ->select("own_match.post_user_id")
            ->group_by("own_match.post_user_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.sourceid <>",4)
            ->get("{$table_match}")
            ->num_rows();

        $this->master_model->get_where_before(@$post["period"]);
        $countUser2 = $this->db
            ->select("own_match.post_user_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.post_user_id =",0)
            ->where("own_match.sourceid <>",4)
            ->get("{$table_match}")
            ->num_rows();

        if (($countUser1 + $countUser2) <> 0) {
            $countUser = ($countUser1 + $countUser2) - 1;
        } else {
            $countUser = ($countUser1 + $countUser2);
        }
        $result["userBefore"] = number_format($countUser);

        return  $result;
    }

    function get_top_keyword($top_key = array(),$top = 0,$type = "",$keyword = array())
    {   
        $data = array();
        $sort = array();
        $total = 0;

        foreach ($top_key as $key => $val) {
            $total += $val[$type];
        }

        foreach ($top_key as $key => $val) {
            $count = $val[$type];
            if($count>0) {
                $percent = ($total>0) ? round(($count*100)/$total,2) : 0;
                $sort[$val['keyword_id']]  = number_format($percent,2);
            }
        }

        arsort($sort);

        $row = 1;
        foreach($sort as $key=>$val) {
            if($row<=$top) {
                $keyword_name = isset($keyword[$key]['name']) ? $keyword[$key]['name'] : null;
                array_push($data,array("key"=>$keyword_name,"sen"=>$val));
            }
            $row++;
        }
        return $data;
    }

    function get_graph_sentiment_data($post = array())
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

        $custom_time = false;
        $period = @$post["period"];
        $result = array();
        $data = array();
        $time = array();
        $temp = array();

        $data_graph_sentiment = array();
        // $data["name"]  = array("Positive", "Normal", "Negative");
        $data_graph_sentiment[1]["name_sentiment"]  = "Positive";
        $data_graph_sentiment[2]["name_sentiment"]  = "Neutral";
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

    // function get_group_keyword_list($post = array())
	// {
	// 	// $table_match = get_match_table("own_match",@$post["period"]);
    //     // $table_match_comp = get_match_table("competitor_match",@$post["period"]);

    //     // 3months check
    //     if(@$post["period"] == "Custom") {
    //         $date = date("Y-m-d");
    //         $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
    //         $obj = get_custom_date($this->custom_date);
    //         if($obj['start'] >= $date_3m) {
    //             $table_match = "own_match_3months own_match";
    //             // $table_match_comp = "competitor_match_3months competitor_match";
    //         } else {
    //             $table_match = get_match_table("own_match", @$post["period"]);
    //             // $table_match_comp = get_match_table("competitor_match", @$post["period"]);
    //         }
    //     } else {
    //         $table_match = get_match_table("own_match", @$post["period"]);
    //         // $table_match_comp = get_match_table("competitor_match", @$post["period"]);
    //     }

    //     $result = array();

    //     $this->master_model->get_where_current(@$post["period"], "own_match");

    //     // $rowsdata = $this->db->select("group_keyword.group_keyword_name AS group_keyword_name , own_match.sourceid , count(DISTINCT(own_key_match.own_match_id)) AS count_num")
    //     //                      ->get("{$table_match}")
    //     //                      ->join("own_key_match", "own_match.own_match_id = own_key_match.own_match_id", "left")
    //     //                      ->join("keyword", "own_key_match.keyword_id = keyword.keyword_id", "left")
    //     //                      ->join("group_keyword", "keyword.group_keyword_id = group_keyword.group_keyword_id", "left")
    //     //                      ->where("own_match.client_id", $this->CLIENT_ID)
    //     //                      ->where("group_keyword.status", "active")
    //     //                      ->group_by("keyword.group_keyword_id, own_match.sourceid")
    //     //                      ->result_array();
    //     $rowsdata = $this->db->query("select group_keyword.group_keyword_name AS group_keyword_name , own_match.sourceid , count(DISTINCT(own_key_match.own_match_id)) AS count_num
    //                                     from ".$table_match."
    //                                     left join own_key_match ON own_match.own_match_id = own_key_match.own_match_id
    //                                     left join keyword ON own_key_match.keyword_id = keyword.keyword_id
    //                                     left join group_keyword ON keyword.group_keyword_id = group_keyword.group_keyword_id
    //                                     where own_match.client_id = ".$this->CLIENT_ID."
    //                                     and group_keyword.status = 'active'
    //                                     and ".$this->master_model->get_where_current_manual(@$post["period"], "own_match")."
    //                                     group by keyword.group_keyword_id , own_match.sourceid
    //                                     order by group_keyword_name ASC")->result_array();

    //     $array_keep = array_fill(1, 8, 0);

    //     for ($x = 0; $x < count($rowsdata); $x++) {
    //         $array_keep_gname = $rowsdata[$x]['group_keyword_name'];
    //         if ($array_keep_gname == $rowsdata[$x-1]['group_keyword_name'] && $x > 0 ) {
    //             if ($rowsdata[$x]['sourceid'] == 1) {$array_keep[1] = $rowsdata[$x]['count_num'];}
    //             if ($rowsdata[$x]['sourceid'] == 2) {$array_keep[2] = $rowsdata[$x]['count_num'];}
    //             if ($rowsdata[$x]['sourceid'] == 3) {$array_keep[3] = $rowsdata[$x]['count_num'];}
    //             if ($rowsdata[$x]['sourceid'] == 4) {$array_keep[4] = $rowsdata[$x]['count_num'];}
    //             if ($rowsdata[$x]['sourceid'] == 5) {$array_keep[5] = $rowsdata[$x]['count_num'];}
    //             if ($rowsdata[$x]['sourceid'] == 6) {$array_keep[6] = $rowsdata[$x]['count_num'];}
    //             if ($rowsdata[$x]['sourceid'] == 7) {$array_keep[7] = $rowsdata[$x]['count_num'];}
    //             if ($rowsdata[$x]['sourceid'] == 9) {$array_keep[8] = $rowsdata[$x]['count_num'];}
    //             if ($x == count($rowsdata)-1) {
    //                 array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
    //                 $array_keep = array_fill(1, 8, 0);
    //             }
    //         } else {
    //             if ($x == 0) {
    //                 if ($rowsdata[$x]['sourceid'] == 1) {$array_keep[1] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 2) {$array_keep[2] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 3) {$array_keep[3] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 4) {$array_keep[4] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 5) {$array_keep[5] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 6) {$array_keep[6] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 7) {$array_keep[7] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 9) {$array_keep[8] = $rowsdata[$x]['count_num'];}
    //                 if ($x == count($rowsdata)-1) {
    //                     array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
    //                     $array_keep = array_fill(1, 8, 0);
    //                 }
    //             } else {
    //                 array_push($result, array("name"=>$rowsdata[$x-1]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
    //                 $array_keep = array_fill(1, 8, 0);
    //                 if ($rowsdata[$x]['sourceid'] == 1) {$array_keep[1] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 2) {$array_keep[2] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 3) {$array_keep[3] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 4) {$array_keep[4] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 5) {$array_keep[5] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 6) {$array_keep[6] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 7) {$array_keep[7] = $rowsdata[$x]['count_num'];}
    //                 if ($rowsdata[$x]['sourceid'] == 9) {$array_keep[8] = $rowsdata[$x]['count_num'];}
    //                 if ($x == count($rowsdata)-1) {
    //                     array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name'],"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
    //                     $array_keep = array_fill(1, 8, 0);
    //                 }
    //             }
    //         }
    //     }
    //     $myJSON1 = json_encode($result);
    //     return $myJSON1;
	// }
    
    function get_group_keyword_list($post = array())
	{
        if(@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
                $table_match_comp = "competitor_match_3months competitor_match";
            } else {
                $table_match = get_match_table("own_match", @$post["period"]);
                $table_match_comp = get_match_table("competitor_match", @$post["period"]);
            }
        } else {
            $table_match = get_match_table("own_match", @$post["period"]);
            $table_match_comp = get_match_table("competitor_match", @$post["period"]);
        }

        $result = array();

        $rowsdata = $this->db->query("select group_keyword.group_keyword_name AS group_keyword_name,tag, own_match.sourceid, count(DISTINCT(own_key_match.own_match_id)) AS count_num
                                        from ".$table_match."
                                        left join own_key_match ON own_match.own_match_id = own_key_match.own_match_id
                                        left join keyword ON own_key_match.keyword_id = keyword.keyword_id
                                        left join group_keyword ON keyword.group_keyword_id = group_keyword.group_keyword_id
                                        where own_match.client_id = ".$this->CLIENT_ID."
                                        and group_keyword.status = 'active'
                                        and ".$this->master_model->get_where_current_manual(@$post["period"], "own_match")."
                                        group by keyword.group_keyword_id , own_match.sourceid
                                        ")->result_array();

        $group_keyword_name = $this->db->query("
                                    SELECT DISTINCT(group_keyword.group_keyword_name) AS group_keyword_name
                                    FROM ".$table_match." 
                                    JOIN keyword ON keyword.company_keyword_id = own_match.company_keyword_id 
                                    JOIN group_keyword ON group_keyword.group_keyword_id = keyword.group_keyword_id 
                                    WHERE own_match.client_id = ".$this->CLIENT_ID."
                                    AND group_keyword.status = 'active' 
                                    AND ".$this->master_model->get_where_current_manual(@$post["period"], "own_match")."")->result_array();      
        
        // Loop through each element in array 2
        foreach ($group_keyword_name as $element2) {
            $duplicate = false;
            // Check if the element's group_keyword_name already exists in array 1
            foreach ($rowsdata as $element1) {
                if ($element1['group_keyword_name'] === $element2['group_keyword_name'] && $element1['tag'] == $element2['tag'] ) {
                    $duplicate = true;
                    break;
                }
            }
            // If the element's group_keyword_name is not a duplicate, add it to array 1
            if (!$duplicate) {
                $rowsdata[] = $element2;
            }
        }

        $array_keep = array_fill(1, 8, 0);

        for ($x = 0; $x < count($rowsdata); $x++) {
            $array_keep_gname = $rowsdata[$x]['group_keyword_name'];
            $tag = $rowsdata[$x]['tag'];       
            $product_name = $rowsdata[$x]['product_name'];       
            if ($array_keep_gname == $rowsdata[$x-1]['group_keyword_name'] && $tag == $rowsdata[$x-1]['tag'] && $product_name == $rowsdata[$x-1]['product_name'] && $x > 0 ) {
                if ($rowsdata[$x]['sourceid'] == 1) {$array_keep[1] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 2) {$array_keep[2] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 3) {$array_keep[3] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 4) {$array_keep[4] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 5) {$array_keep[5] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 6) {$array_keep[6] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 7) {$array_keep[7] = $rowsdata[$x]['count_num'];}
                if ($rowsdata[$x]['sourceid'] == 9) {$array_keep[8] = $rowsdata[$x]['count_num'];}
                if ($x == count($rowsdata)-1) {
                    array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name']. (!empty($rowsdata[$x]['product_name']) ? " [".$rowsdata[$x]['product_name']."]" : " ") . (!empty($rowsdata[$x]['tag']) ? " [".$rowsdata[$x]['tag']."]" : " "),"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
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
                        array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name']. (!empty($rowsdata[$x]['product_name']) ? " [".$rowsdata[$x]['product_name']."]" : " ") . (!empty($rowsdata[$x]['tag']) ? " [".$rowsdata[$x]['tag']."]" : " "),"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
                        $array_keep = array_fill(1, 8, 0);
                    }
                } else {
                    array_push($result, array("name"=>$rowsdata[$x-1]['group_keyword_name']. (!empty($rowsdata[$x-1]['product_name']) ? " [".$rowsdata[$x-1]['product_name']."]" : " ") . (!empty($rowsdata[$x-1]['tag']) ? " [".$rowsdata[$x-1]['tag']."]" : " "),"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
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
                        array_push($result, array("name"=>$rowsdata[$x]['group_keyword_name']. (!empty($rowsdata[$x]['product_name']) ? " [".$rowsdata[$x]['product_name']."]" : " ") . (!empty($rowsdata[$x]['tag']) ? " [".$rowsdata[$x]['tag']."]" : " "),"Facebook"=>$array_keep[1],"Twitter"=>$array_keep[2],"Youtube"=>$array_keep[3],"Instagram"=>$array_keep[6],"Tiktok"=>$array_keep[7],"Line"=>$array_keep[8],"News"=>$array_keep[4],"Webboard"=>$array_keep[5],"Total"=> array_sum($array_keep)));
                        $array_keep = array_fill(1, 8, 0);
                    }
                }
            }
        }
        $myJSON1 = json_encode($result);
        return $myJSON1;
	}
}
?>