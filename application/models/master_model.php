<?php
class Master_model extends CI_Model {

    var $CLIENT_ID;
    var $custom_date;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
        $this->custom_date = $this->get_custom_date();
    }

    function get_with_out($tb = "own_match")
    {
        $this->db->where("{$tb}.msg_status","1");
        $this->db->where("{$tb}.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->where("{$tb}.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='".$this->CLIENT_ID."')",null,false);
        //$this->db->where("{$tb}.{$tb}_sentiment IS NOT NULL",null,false);
    }

    function get_where_current($period = "Today",$tb = "own_match")
    {
        $date = date("Y-m-d");
        $date_1d = $date;
        $date_1w = date("Y-m-d",strtotime($date. ' -6 days'));
        $date_1m = date("Y-m-d",strtotime($date. ' -30 days'));
        $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));

        if($period=="Today") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_1d."' AND '".$date."'",null,false);
        } else if($period=="1W") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_1w."' AND '".$date."'",null,false);
        } else if($period=="1M") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_1m."' AND '".$date."'",null,false);
        } else if($period=="3M") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_3m."' AND '".$date."'",null,false);
        } else if($period=="Custom") {
            $obj = get_custom_date($this->custom_date);
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$obj['start']."' AND '".$obj['end']."'",null,false);
        }

        $this->get_with_out($tb);
    }

    function get_groupby_current($period = "Today",$tb = "own_match")
    {
        if($period=="Today") {
            $this->db->select("CONCAT(DATE({$tb}.msg_time),' ',HOUR({$tb}.msg_time)) AS new_time",false);
        } else if($period=="1W") {
            $this->db->select("DATE({$tb}.msg_time) AS new_time");
        } else if($period=="1M") {
            $this->db->select("WEEK({$tb}.msg_time) AS new_time");
        } else if($period=="3M") {
            $this->db->select("WEEK({$tb}.msg_time) AS new_time");
        } else if($period=="Custom") {
            $obj = get_custom_date($this->custom_date);
            if($obj['period']=="W") {
                $this->db->select("DATE({$tb}.msg_time) AS new_time");
            } else if($obj['period']=="M") {
                $this->db->select("WEEK({$tb}.msg_time) AS new_time");
            } else {
                $this->db->select("CONCAT(DATE({$tb}.msg_time),' ',HOUR({$tb}.msg_time)) AS new_time",false);
            }
        }
        $this->db->group_by("new_time");
    }

    function get_where_before($period = "Today",$tb = "own_match")
    { 
        $date = date("Y-m-d");
        $date_1d = date("Y-m-d",strtotime($date. ' -1 days'));
        $date_1w = date("Y-m-d",strtotime($date. ' -6 days'));
        $date_2w = date("Y-m-d",strtotime($date. ' -13 days'));
        $date_1m = date("Y-m-d",strtotime($date. ' -30 days'));
        $date_2m = date("Y-m-d",strtotime($date. ' -60 days'));
        $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));

        if($period=="Today") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_1d."' AND '".$date_1d."'",null,false);
        } else if($period=="1W") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_2w."' AND '".$date_1w."'",null,false);
        } else if($period=="1M") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_2m."' AND '".$date_1m."'",null,false);
        } else if($period=="3M") {
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$date_3m."' AND '".$date_2m."'",null,false);
        } else if($period=="Custom") {
            $obj = get_custom_date($this->custom_date);
            $dStart = new DateTime(date("Y-m-d",strtotime($obj['start'])));
            $dEnd  = new DateTime(date("Y-m-d",strtotime($obj['end'])));
            $dDiff = $dStart->diff($dEnd);
            $end_days = $dDiff->days;

            $end   =  date("Y-m-d",strtotime($obj['start']. ' -1 days'));
            $start =  date("Y-m-d",strtotime($obj['start']. ' -'.($end_days+1).' days'));
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$start."' AND '".$end."'",null,false);
        }
        $this->get_with_out($tb);
    }

    function get_where_current_map($period = "Today",$tb = "map_match") //for map
    {
        $date = date("Y-m-d");
        $date_1d = $date;
        $date_1w = date("Y-m-d",strtotime($date. ' -6 days'));
        $date_1m = date("Y-m-d",strtotime($date. ' -30 days'));
        $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));

        if($period=="Today") {
            $this->db->where("DATE({$tb}.map_match_timepost) BETWEEN '".$date_1d."' AND '".$date."'",null,false);
        } else if($period=="1W") {
            $this->db->where("DATE({$tb}.map_match_timepost) BETWEEN '".$date_1w."' AND '".$date."'",null,false);
        } else if($period=="1M") {
            $this->db->where("DATE({$tb}.map_match_timepost) BETWEEN '".$date_1m."' AND '".$date."'",null,false);
        } else if($period=="3M") {
            $this->db->where("DATE({$tb}.map_match_timepost) BETWEEN '".$date_3m."' AND '".$date."'",null,false);
        } else if($period=="Custom") {
            $obj = get_custom_date($this->custom_date);
            $this->db->where("DATE({$tb}.map_match_timepost) BETWEEN '".$obj['start']."' AND '".$obj['end']."'",null,false);
        }
	}

    function get_where_sentiment($post = array(),$tb = "own_match")
    {
        if(isset($post['sentiment']) && $post['sentiment']!="") {
            if($post['sentiment']=="Positive") {
                $this->db->where("{$tb}.{$tb}_sentiment >",0);
            } else if($post['sentiment']=="Normal") {
                $this->db->where("{$tb}.{$tb}_sentiment",0);
            } else if($post['sentiment']=="Negative") {
                $this->db->where("{$tb}.{$tb}_sentiment <",0);
            } 
        }
    }
    
    function get_config()
    {
        $result = array();
        $rowsdata = $this->db
                    ->where("sys_parent_id",$this->CLIENT_ID)
                    ->get("client_config")
                    ->result_array();

        // if(count($rowsdata)==0) {
        //     $rowsdata = $this->db->get("client")->result_array();
        // }

        foreach($rowsdata as $k_row=>$v_row) {
            $result[$v_row['config_name']] = $v_row['config_val'];
        }
        return $result;
    }

    function get_meta($name = "")
    {
        $result = $this->db
                    ->where("name",$name)
                    ->where("client_id",$this->CLIENT_ID)
                    ->get("client_meta")
                    ->first_row('array');

        return isset($result['value']) ? $result['value'] : null;
    }

    function get_period()
    {
        $period = get_cookie("META_PERIOD");
        if($period=="") {
            return "Today";
        } else {
            return $period;
        }
    }

    function get_custom_date()
    {
        $custom_date =get_cookie("META_CUSTOM_DATE");
        return $custom_date;
    }

    function insert_period($post = array())
    {
        if(isset($post['save_period'])) {
            $this->input->set_cookie("META_PERIOD", $post['period'], 0);
            if($post['period']=="Custom") {
                $this->input->set_cookie("META_CUSTOM_DATE", $post['custom_date'], 0);
            } else {
                $this->input->set_cookie("META_CUSTOM_DATE", "", 0);
            }
        }
    }

    function get_media_com()
    {
        $media_com = $this->get_meta("media_com");
        if($media_com=="") {
            $rec = $this->db
                ->select("company_keyword.company_keyword_id")
                ->where("company_keyword.company_keyword_type","Company")
                ->where("company_keyword.client_id",$this->CLIENT_ID)
                ->get("company_keyword")
                ->first_row('array');

            return $rec['company_keyword_id'];
        } else {
            return $media_com;
        }
    }

    function insert_media_com($post = array())
    {
        if(isset($post['save_media_com'])) {
            $period = $this->get_meta("media_com");
            if($period=="") {
                $save = array();
                $save['client_id'] = $this->CLIENT_ID;
                $save['name']      = "media_com";
                $save['value']     = $post['media_com'];
                $this->db->insert("client_meta",$save);
            } else {
                $save = array();
                $save['value'] =  $post['media_com'];
                $this->db->where("name","media_com");
                $this->db->where("client_id",$this->CLIENT_ID);
                $this->db->update("client_meta",$save);
            }
        }
    }

    function save_log($log_activity = "")
    {
        $save = array();
        $save['client_id']    = $this->CLIENT_ID;
        $save['log_activity'] = $log_activity;
        $save['log_user']     = $this->authen->getUsername();
        $save['log_time']     = date("Y-m-d H:i:s");
        $save['ip_address']   = $this->getUserIP();
        $save['user_agent']   = $this->session->userdata('user_agent');
        $save['log_admin']    = $this->authen->getUseradmin();
        $this->db->insert("activity_log",$save);
    }

    function getUserIP()
	{
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];

	    if(filter_var($client, FILTER_VALIDATE_IP))
	    {
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP))
	    {
	        $ip = $forward;
	    }
	    else
	    {
	        $ip = $remote;
	    }
	    return $ip;
	}

    function get_display_keyword($data = array())
    {
        $result = array();
        $company_keyword_type = array("Company", "Competitor");

        foreach($company_keyword_type as $type) {

            $tb_match      = ($type=="Company") ? "own_match" : "competitor_match";
            $period        = $this->master_model->get_period();
            // $table_match   = get_match_table($tb_match, $period);
            // 3months check
            if($period == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if($obj['start'] >= $date_3m) {
                    $table_match = $tb_match."_3months ".$tb_match;
                } else {
                    $table_match = get_match_table($tb_match, $period);
                }
            } else {
                $table_match = get_match_table($tb_match, $period);
            }
            $tb_key_match  = ($type=="Company") ? "own_key_match" : "competitor_key_match";
            $tb_cate_match = ($type=="Company") ? "own_cate_match" : "competitor_cate_match";

            $rowsdata = $this->db
                ->select("keyword.keyword_name")
                ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                ->where("{$tb_match}.msg_id",$data["post_id"])
                ->join("{$tb_key_match}","{$tb_key_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
                ->join("keyword","{$tb_key_match}.keyword_id = keyword.keyword_id")
                ->get("{$table_match}")
                ->result_array();
            foreach($rowsdata as $k_row=>$v_row) {
                $keyword_name = $v_row['keyword_name'];
                $result[$keyword_name] = $keyword_name;
            }

            $rowsdata = $this->db
                ->select("category.category_name")
                ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                ->where("{$tb_match}.msg_id",$data["post_id"])
                ->join("{$tb_cate_match}","{$tb_cate_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
                ->join("category","{$tb_cate_match}.category_id = category.category_id")
                ->get("{$table_match}")
                ->result_array();
            foreach($rowsdata as $k_row=>$v_row) {
                $keyword_name = $v_row['category_name'];
                $result[$keyword_name] = $keyword_name;
            }
        }
        arsort($result);
        return $result;
    }

    function get_post_user($post_user_id = 0)
    {
        $rec = $this->db
                ->where("post_user_id",$post_user_id)
                ->get("post_user_match")
                ->first_row("array");

        return $rec;
    }

    function get_keyword_list()
    {
        $result = array();
        $config = $this->get_config();
        $add_keyword = isset($config['add_keyword']) ? $config['add_keyword'] : 50;

        $rowsdata = $this->db
                    ->select("keyword.*")
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->order_by("keyword.company_keyword_id,keyword.keyword_id","ASC")
                    ->limit($add_keyword)
                    ->get("keyword")
                    ->result_array();

        foreach($rowsdata as $key=>$val) {
            array_push($result,$val['keyword_name']);
        }
        return $result;
    }

    function get_menu()
    {
        $client_menu = array();
        $rows = $this->db
                    ->where_in("client_id",$this->CLIENT_ID)
                    ->get("client_menu")
                    ->result_array();
        foreach($rows as $val) {
            array_push($client_menu,$val['menu_id']);
        }

        $menu = array();
        $size = 3;

        for($i=0;$i<$size;$i++) {

            if($i==0) $parent_id = array(0);

            if(count($parent_id)>0) {
                $rows = $this->db
                        ->where_in("parent_id",$parent_id)
                        ->select("menu.*")
                        ->order_by("parent_order","asc")
                        ->get("menu")
                        ->result_array();

                $parent_id = array();

                foreach($rows as $key=>$val) {
                    if(!in_array($val['menu_id'],$client_menu)) {
                        $idx = $val['parent_id'];
                        if($val['menu_link']=="") {
                            $val['link'] = "javascript:;";
                        } else if(strpos($val['menu_link'],'http')==false) {
                            $val['link'] = site_url($val['menu_link']);
                        } else {
                            $val['link'] = $val['menu_link'];
                        }
                        array_push($parent_id,$val['menu_id']);
                        if(!isset($menu[$idx])) $menu[$idx] = array();
                        array_push($menu[$idx],$val);
                    }
                }
            }
        }
        return $menu;
    }

    function update_edit_new_sentiment($new_sentiment_edit , $post_id)
    {
        // $new_sentiment_edit = $new_sentiment_edit1;
        // $post_id = $post_id1;
        
        if($new_sentiment_edit == "1"){
            $new_sentiment_edit = 100;
        }else if($new_sentiment_edit == "2"){
            $new_sentiment_edit = -100;
        }else{
            $new_sentiment_edit = 0;
        }

        $data = array('own_match_sentiment' => $new_sentiment_edit);
        $this->db->where("own_match_id", $post_id);
        $this->db->update('own_match', $data);

        $data = array('own_match_sentiment' => $new_sentiment_edit);
        $this->db->where("own_match_id", $post_id);
        $this->db->update('own_match_daily', $data);

        $data = array('own_match_sentiment' => $new_sentiment_edit);
        $this->db->where("own_match_id", $post_id);
        $this->db->update('own_match_3months', $data);

        $data = array('competitor_match_sentiment' => $new_sentiment_edit);
        $this->db->where("competitor_match_id", $post_id);
        $this->db->update('competitor_match', $data);

        $data = array('competitor_match_sentiment' => $new_sentiment_edit);
        $this->db->where("competitor_match_id", $post_id);
        $this->db->update('competitor_match_daily', $data);

        $data = array('competitor_match_sentiment' => $new_sentiment_edit);
        $this->db->where("competitor_match_id", $post_id);
        $this->db->update('competitor_match_3months', $data);

    }

    function update_edit_new_sentiment_daily($new_sentiment_edit , $post_id)
    {
        // $new_sentiment_edit = $new_sentiment_edit1;
        // $post_id = $post_id1;
        
        if($new_sentiment_edit == "1"){
            $new_sentiment_edit = 100;
        }else if($new_sentiment_edit == "2"){
            $new_sentiment_edit = -100;
        }else{
            $new_sentiment_edit = 0;
        }

        $data = array('own_match_sentiment' => $new_sentiment_edit);
        $this->db->where("own_match_id",$post_id);
        $this->db->update('own_match_daily',$data);

        $data = array('competitor_match_sentiment' => $new_sentiment_edit);
        $this->db->where("competitor_match_id",$post_id);
        $this->db->update('competitor_match_daily',$data);

    }

    function get_where_current_manual($period = "Today",$tb = "own_match")
    {
        $result = "";
        $date = date("Y-m-d");
        $date_1d = $date;
        $date_1w = date("Y-m-d",strtotime($date. ' -6 days'));
        $date_1m = date("Y-m-d",strtotime($date. ' -30 days'));
        $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));

        if($period=="Today") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_1d."' AND '".$date."'";
        } else if($period=="1W") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_1w."' AND '".$date."'";
        } else if($period=="1M") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_1m."' AND '".$date."'";
        } else if($period=="3M") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_3m."' AND '".$date."'";
        } else if($period=="Custom") {
            $obj = get_custom_date($this->custom_date);
            $result = "DATE({$tb}.msg_time) BETWEEN '".$obj['start']."' AND '".$obj['end']."'";
        }
        return $result;
    }

    function get_where_before_manual($period = "Today",$tb = "own_match")
    { 
        $result = "";
        $date = date("Y-m-d");
        $date_1d = date("Y-m-d",strtotime($date. ' -1 days'));
        $date_1w = date("Y-m-d",strtotime($date. ' -6 days'));
        $date_2w = date("Y-m-d",strtotime($date. ' -13 days'));
        $date_1m = date("Y-m-d",strtotime($date. ' -30 days'));
        $date_2m = date("Y-m-d",strtotime($date. ' -60 days'));
        $date_3m = date("Y-m-d",strtotime($date. ' -90 days'));

        if($period=="Today") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_1d."' AND '".$date_1d."'";
        } else if($period=="1W") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_2w."' AND '".$date_1w."'";
        } else if($period=="1M") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_2m."' AND '".$date_1m."'";
        } else if($period=="3M") {
            $result = "DATE({$tb}.msg_time) BETWEEN '".$date_3m."' AND '".$date_2m."'";
        } else if($period=="Custom") {
            $obj = get_custom_date($this->custom_date);
            $dStart = new DateTime(date("Y-m-d",strtotime($obj['start'])));
            $dEnd  = new DateTime(date("Y-m-d",strtotime($obj['end'])));
            $dDiff = $dStart->diff($dEnd);
            $end_days = $dDiff->days;

            $end   =  date("Y-m-d",strtotime($obj['start']. ' -1 days'));
            $start =  date("Y-m-d",strtotime($obj['start']. ' -'.($end_days+1).' days'));
            $result = "DATE({$tb}.msg_time) BETWEEN '".$start."' AND '".$end."'";
        }
        return $result;
    }

    function get_where_sentiment_manual($post = array(),$tb = "own_match")
    {
        $result = "";
        if(isset($post['sentiment']) && $post['sentiment']!="") {
            if($post['sentiment']=="Positive") {
                $result = "and {$tb}.{$tb}_sentiment > 0";
            } else if($post['sentiment']=="Normal") {
                $result = "and {$tb}.{$tb}_sentiment = 0";
            } else if($post['sentiment']=="Negative") {
                $result = "and {$tb}.{$tb}_sentiment < 0";
            } 
        } 
        return $result;
    }
}
?>