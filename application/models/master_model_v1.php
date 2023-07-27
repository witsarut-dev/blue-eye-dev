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

    function get_width_out($tb = "own_match")
    {
        $this->db->where("{$tb}.msg_status","1");
        $this->db->where("{$tb}.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='".$this->CLIENT_ID."')",null,false);
        //$this->db->where("{$tb}.{$tb}_sentiment IS NOT NULL",null,false);
    }

    function get_where_current($period = "Today",$tb = "own_match")
    {
        $date = date("Y-m-d");
        $date_1d = $date;
        $date_1w = date("Y-m-d",strtotime($date. ' -6 days'));
        $date_1m = date("Y-m-d",strtotime($date. ' -1 months'));
        $date_3m = date("Y-m-d",strtotime($date. ' -3 months'));

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

       // $this->get_width_out($tb);
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
        $date_1w = date("Y-m-d",strtotime($date. ' -7 days'));
        $date_2w = date("Y-m-d",strtotime($date. ' -13 days'));
        $date_1m = date("Y-m-d",strtotime($date. ' -1 months'));
        $date_2m = date("Y-m-d",strtotime($date. ' -2 months'));
        $date_3m = date("Y-m-d",strtotime($date. ' -3 months'));

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
            if($obj['period']=="W") {
                $start =  date("Y-m-d",strtotime($obj['end']. ' -13 days'));
                $end   =  date("Y-m-d",strtotime($obj['end']. ' -7 days'));
            } else if($obj['period']=="M") {
                $start =  date("Y-m-d",strtotime($obj['end']. ' -2 months'));
                $end   =  date("Y-m-d",strtotime($obj['end']. ' -1 months'));
            } else {
                $start =  date("Y-m-d",strtotime($obj['start']. ' -1 days'));
                $end   =  $obj['start'];
            }
            $this->db->where("DATE({$tb}.msg_time) BETWEEN '".$start."' AND '".$end."'",null,false);
        }

        //$this->get_width_out($tb);
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

        if(count($rowsdata)==0) {
            $rowsdata = $this->db->get("client")->result_array();
        }

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
        $period = $this->get_meta("period");
        if($period=="") {
            return "Today";
        } else {
            return $period;
        }
    }

    function get_custom_date()
    {
        $custom_date = $this->get_meta("custom_date");
        return $custom_date;
    }

    function insert_period($post = array())
    {
        if(isset($post['save_period'])) {
            $period = $this->get_meta("period");
            if($period=="") {
                $save = array();
                $save['client_id'] = $this->CLIENT_ID;
                $save['name']      = "period";
                $save['value']     = $post['period'];
                $this->db->insert("client_meta",$save);
            } else {
                $save = array();
                $save['value'] =  $post['period'];
                $this->db->where("name","period");
                $this->db->where("client_id",$this->CLIENT_ID);
                $this->db->update("client_meta",$save);
            }
            $num_rows = $this->db
                    ->where("name","custom_date")
                    ->where("client_id",$this->CLIENT_ID)
                    ->get("client_meta")
                    ->num_rows();

            if($num_rows==0) {
                $save = array();
                $save['client_id'] = $this->CLIENT_ID;
                $save['name']      = "custom_date";
                $save['value']     = $post['custom_date'];
                $this->db->insert("client_meta",$save);
            } else {
                $save = array();
                $save['value'] =  $post['custom_date'];
                $this->db->where("name","custom_date");
                $this->db->where("client_id",$this->CLIENT_ID);
                $this->db->update("client_meta",$save);
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
        $this->db->insert("activity_log",$save);
       
    }

    function get_display_keyword($data = array())
    {
        $result = array();
        $company_keyword_type = array("Company","Competitor");

        foreach($company_keyword_type as $type) {

            $tb_match      = ($type=="Company") ? "own_match" : "competitor_match";
            $tb_key_match  = ($type=="Company") ? "own_key_match" : "competitor_key_match";
            $tb_cate_match = ($type=="Company") ? "own_cate_match" : "competitor_cate_match";

            // $rowsdata = $this->db
            //     ->select("company_keyword.company_keyword_name")
            //     ->where("{$tb_match}.client_id",$this->CLIENT_ID)
            //     ->where("{$tb_match}.msg_id",$data["post_id"])
            //     ->join("company_keyword","company_keyword.company_keyword_id = {$tb_match}.company_keyword_id")
            //     ->get("{$tb_match}")
            //     ->result_array();

            // foreach($rowsdata as $k_row=>$v_row) {
            //     if(strpos($data["post_detail"],$v_row["company_keyword_name"])!==false) {
            //         $company_keyword_name = $v_row['company_keyword_name'];
            //         $result[$company_keyword_name] = $company_keyword_name;
            //     }
            // }

            $rowsdata = $this->db
                ->select("keyword.keyword_name")
                ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                ->where("{$tb_match}.msg_id",$data["post_id"])
                ->join("{$tb_key_match}","{$tb_key_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id")
                ->join("keyword","{$tb_key_match}.keyword_id = keyword.keyword_id")
                ->get("{$tb_match}")
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
                ->get("{$tb_match}")
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

}
?>