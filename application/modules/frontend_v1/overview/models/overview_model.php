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
		            ->get("own_match")
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
                $start = strtotime($date. ' -1 months');
            } else if(@$post["period"]=="3M") {
                $start = strtotime($date. ' -3 months');
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

        $arrBefore = array();

        $result = array("mediaCategories"=>array(),"mediaData"=>array());

        /*media before*/
        $this->master_model->get_where_before(@$post["period"]);
        $rowsdata = $this->db
                    ->select("own_match.sourceid")
                    ->select("count(*) AS countData")
                    ->group_by("own_match.sourceid")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->get("own_match")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            $arrBefore[$v_row['sourceid']] = intval($v_row['countData']);
        }

         /*media current*/
        $this->master_model->get_where_current(@$post["period"]);
        $rowsdata = $this->db
                    ->select("own_match.sourceid")
                    ->select("count(*) AS countData")
                    ->group_by("own_match.sourceid")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->get("own_match")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            $sourceid = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            $media_color = get_media_color($sourceid);
            $countCurrent = intval($v_row['countData']);
            $countBefore  = intval(@$arrBefore[$sourceid]);

            $url = site_url("realtime/?media_type=".$media_full);
            array_push($result["mediaCategories"],$media_short);
            $drilldown = array("categories"=>array($media_short),"data"=>array($countBefore));
            array_push($result["mediaData"],array("y"=>$countCurrent,"color"=>$media_color,"url"=>$url,"drilldown"=>$drilldown));
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

            $this->master_model->get_where_before(@$post["period"],$tb_match);
            $rec1 = $this->db
                ->select("SUM({$tb_match}.{$tb_match}_sentiment/100) AS sumSentiment")
                ->select("COUNT(*) AS countTotal")
                ->where("{$tb_match}.company_keyword_id",$v_row["company_keyword_id"])
                ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                ->where("{$tb_match}.{$tb_match}_sentiment IS NOT NULL",null,false)
                ->join("{$tb_cate_match}","{$tb_cate_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
                ->get("{$tb_match}")
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
                ->get("{$tb_match}")
                ->first_row('array');
            $currentSentiment = ($rec2["countTotal"]>0) ? round(($rec2["sumSentiment"]/$rec2["countTotal"]),2) : 0;

            if($rec1["sumSentiment"]!="" && $rec2["sumSentiment"]!="") {
                $countTotal  = ($rec1["countTotal"]+$rec2["countTotal"]);
                $beforeMention   = 0;
                $currentMention  = 0;
                if($countTotal>0) {
                    $beforeMention  = ($countTotal>0) ? round(($rec1["countTotal"]/$countTotal),2) : 0;
                    $currentMention = ($countTotal>0) ? round(($rec2["countTotal"]/$countTotal),2) : 0;
                    if($rec1["countTotal"]<$rec2["countTotal"]) $beforeMention  = floatval("-".abs($beforeMention));
                    if($rec2["countTotal"]<$rec1["countTotal"]) $currentMention = floatval("-".abs($currentMention));
                }

                if($beforeMention==0) $beforeMention = 0.01;
                if($beforeSentiment==0) $beforeSentiment = 0.01;
                if($currentMention==0) $currentMention = 0.01;
                if($currentSentiment==0) $currentSentiment = 0.01;

                if($beforeMention==1) $beforeMention = 0.99;
                if($beforeSentiment==1) $beforeSentiment = 0.99;
                if($currentMention==1) $currentMention = 0.99;
                if($currentSentiment==1) $currentSentiment = 0.99;

                $data1  = array($beforeMention,$beforeSentiment);
                $data2  = array($currentMention,$currentSentiment);

                array_push($result,array("name"=>$v_row["company_keyword_name"],"color"=>@$color[$k_row],"data"=>array($data1,$data2)));
            } else {
                array_push($result,array("name"=>$v_row["company_keyword_name"],"color"=>@$color[$k_row],"data"=>array()));
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

        $this->master_model->get_where_current(@$post["period"],$tb_match);
        
        $rowsdata = $this->db
            ->select("{$tb_match}.sourceid")
            ->group_by("{$tb_match}.sourceid")
            ->where("{$tb_match}.company_keyword_id",$company_keyword_id)
            ->where("{$tb_match}.client_id",$this->CLIENT_ID)
            ->where("{$tb_match}.sourceid IS NOT NULL",null,false)
            ->join("{$tb_cate_match}","{$tb_cate_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
            ->get("{$tb_match}")
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
                ->get("{$tb_match}")
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
                ->get("{$tb_match}")
                ->first_row('array');
            $currentSentiment = ($rec2["countTotal"]>0) ? round(($rec2["sumSentiment"]/$rec2["countTotal"]),2) : 0;

            if($rec1["sumSentiment"]!="" && $rec2["sumSentiment"]!="") {
                $countTotal  = ($rec1["countTotal"]+$rec2["countTotal"]);
                $beforeMention   = 0;
                $currentMention  = 0;
                if($countTotal>0) {
                    $beforeMention = ($countTotal>0) ? round(($rec1["countTotal"]/$countTotal),2) : 0;
                    $currentMention = ($countTotal>0) ? round(($rec2["countTotal"]/$countTotal),2) : 0;
                    if($rec1["countTotal"]<$rec2["countTotal"]) $beforeMention  = floatval("-".abs($beforeMention));
                    if($rec2["countTotal"]<$rec1["countTotal"]) $currentMention = floatval("-".abs($currentMention));
                }

                if($beforeMention==0) $beforeMention = 0.01;
                if($beforeSentiment==0) $beforeSentiment = 0.01;
                if($currentMention==0) $currentMention = 0.01;
                if($currentSentiment==0) $currentSentiment = 0.01;

                if($beforeMention==1) $beforeMention = 0.99;
                if($beforeSentiment==1) $beforeSentiment = 0.99;
                if($currentMention==1) $currentMention = 0.99;
                if($currentSentiment==1) $currentSentiment = 0.99;

                $data1  = array($beforeMention,$beforeSentiment);
                $data2  = array($currentMention,$currentSentiment);
                array_push($result,array("name"=>$media_short,"color"=>$media_color,"data"=>array($data2,$data1)));
            }
        }

        if(count($result)==0) array_push($result,array());

    	return  $result;
    }

    function get_sentiment_data($post = array())
    {
        $this->master_model->get_where_current(@$post["period"]);

        $rec = $this->db
            ->select("SUM(CASE WHEN own_match.own_match_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
            ->select("SUM(CASE WHEN own_match.own_match_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
            ->select("COUNT(*) AS countTotal")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.own_match_sentiment IS NOT NULL",null,false)
            ->get("own_match")
            ->first_row('array');

        $result = array();
        $result["Positive"] = ($rec["countTotal"]>0) ? round(($rec["countPositive"]*100)/$rec["countTotal"]) : 0;
        $result["Negative"] = ($rec["countTotal"]>0) ? round(($rec["countNegative"]*100)/$rec["countTotal"]) : 0;
        //$result["Normal"]   = ($rec["countTotal"]>0) ? round(($rec["countNormal"]*100)/$rec["countTotal"]) : 0;
        $result["Normal"]   = 100 - ($result["Positive"]+$result["Negative"]);

        return  $result;
    }

    function get_total_data($post = array())
    {
        $result = array();

        /*mention count*/
        $this->master_model->get_where_current(@$post["period"]);
        $countMention = $this->db
            ->select("own_match.own_match_id")
            ->group_by("own_match.own_match_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->get("own_match")
            ->num_rows();
        $result["mentionCurrent"] = number_format($countMention);

        $this->master_model->get_where_before(@$post["period"]);
        $countMention = $this->db
            ->select("own_match.own_match_id")
            ->group_by("own_match.own_match_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->get("own_match")
            ->num_rows();
        $result["mentionBefore"]  = number_format($countMention);

        /*user count*/
        $this->master_model->get_where_current(@$post["period"]);
        $countUser = $this->db
            ->select("own_match.post_user_id")
            ->group_by("own_match.post_user_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.sourceid <>",4)
            ->get("own_match")
            ->num_rows();
        $result["userCurrent"] = number_format($countUser);

        $this->master_model->get_where_before(@$post["period"]);
        $countUser = $this->db
            ->select("own_match.post_user_id")
            ->group_by("own_match.post_user_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.sourceid <>",4)
            ->get("own_match")
            ->num_rows();
        $result["userBefore"] = number_format($countUser);

        return  $result;
    }

}
?>