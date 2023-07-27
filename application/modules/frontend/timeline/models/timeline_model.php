<?php
class Timeline_model extends CI_Model {

    var $CLIENT_ID;
    var $FLAG_DELETE = 1;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
    }

    function get_timeline_id($timeline_id = 0)
    {
        $rowsdata = $this->db
                    ->select("client_timeline.*")
                    ->where("client_timeline.timeline_id",$timeline_id)
                    ->where("client_timeline.client_id",$this->CLIENT_ID)
                    ->get("client_timeline")
                    ->first_row('array');

        return $rowsdata;
    }

    function get_client_timeline()
    {
        $result = $this->db
                    ->select("client_timeline.*")
                    ->where("client_timeline.client_id",$this->CLIENT_ID)
                    ->order_by("client_timeline.timeline_id","ASC")
                    ->get("client_timeline")
                    ->result_array();

        return $result;
    }


    function insert_timeline($post = array())
    {
        $obj = $this->set_timeline_date($post);
        $start_date = $obj['start_date'];
        $end_date = $obj['end_date'];

        $save = array();
        $save["client_id"]     = $this->CLIENT_ID;
        $save["timeline_name"] = $post['timeline_name'];
        $save["keyword_name"]  = $post['keyword_name'];
        $save["created_date"]  = date("Y-m-d H:i:s");
        $save["start_date"]    = $start_date;
        $save["end_date"]      = $end_date;
        $save['timeline_status'] = 0;

        $this->db->insert("client_timeline",$save);
        $timeline_id = $this->db->insert_id("client_timeline");

        return $timeline_id;
    }

    function update_timeline($post = array())
    {
        $timeline_id = $post['timeline_id'];

        $rec = $this->get_timeline_id($timeline_id);

        $save = array();
        $save["timeline_name"]  = $post['timeline_name'];

        $this->db->where("timeline_id",$timeline_id);
        $this->db->update("client_timeline",$save);

        return $timeline_id;
    }

    function delete_timeline($timeline_id = 0)
    {
        $rec = $this->db
            ->select("timeline_name")
            ->where("timeline_id",$timeline_id)
            ->get("client_timeline client_timeline")
            ->first_row("array");

        $where_delete = array("client_id"=>$this->CLIENT_ID,"timeline_id"=>$timeline_id);

        $this->db->delete("client_timeline_list",$where_delete);
        $this->db->delete("client_timeline",$where_delete);
        return $rec['timeline_name'];
    }

    function check_timeline_name($post = array())
    {
        $num_rows = 0;
        $check = false;
        if(@$post['timeline_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_timeline_id($post['timeline_id']);
            if($post['timeline_name']!=@$rec['timeline_name']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_timeline.timeline_name",$post['timeline_name'])
                ->where("client_timeline.client_id",$this->CLIENT_ID)
                ->get("client_timeline")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_keyword_name($post = array())
    {
        $num_rows = 0;
        $check = false;
        $obj = $this->set_timeline_date($post);
        $start_date = $obj['start_date'];
        $end_date = $obj['end_date'];
        if(@$post['timeline_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_timeline_id($post['timeline_id']);
            if($post['keyword_name']!=@$rec['keyword_name'] && $start_date!=@$rec['start_date'] && $end_date!=@$rec['end_date']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_timeline.keyword_name",$post['keyword_name'])
                ->where("client_timeline.start_date",$start_date)
                ->where("client_timeline.end_date",$end_date)
                ->where("client_timeline.client_id",$this->CLIENT_ID)
                ->get("client_timeline")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_timeline_max(&$add_timeline_post)
    {
        $config = $this->master_model->get_config();
        $add_timeline_post = isset($config['add_timeline_post']) ? $config['add_timeline_post'] : 10;
        
        $this->db
            ->select("timeline_id")
            ->where("client_id",$this->CLIENT_ID)
            ->where("MONTH(created_date)",date("n"))
            ->get("client_timeline")
            ->result_array();

        $sql = $this->db->last_query();

        $sql_log = str_replace("client_timeline","client_timeline_log",$sql);
        $num_rows = $this->db->query($sql.' UNION '.$sql_log)->num_rows();
        
        return ($num_rows>=$add_timeline_post) ? true : false;
    }

    function get_timeline_list($timeline_id = 0,&$start_date, &$end_id)
    {
        $result = array();
        $date_arr = array();
        $rows_arr = array();
        $rowsdata = $this->db
                    ->where("client_id",$this->CLIENT_ID)
                    ->where("timeline_id",$timeline_id)
                    ->order_by("msg_time","ASC")
                    ->get("client_timeline_list")
                    ->result_array();
        $rows = 0;
        $start = 0;
        $end = count($rowsdata)-1; 
        $start_date = '';
        $end_id = '';
        $param_date = '';
        foreach($rowsdata as $k_row=>$v_row) {
            $msg_time = getDatetimeformat($v_row['msg_time']);
            if($k_row==$start) {
                $param_date = date("Y-m-d H:i",strtotime($v_row['msg_time']));
                $start_date = $v_row['msg_date'] = $msg_time;
                $v_row['post_order'] = 'start';
            } else if($msg_time==$start_date) {
                $param_date =  date("Y-m-d H:i",strtotime($v_row['msg_time']));
                $v_row['msg_date'] = $msg_time;
                $v_row['post_order'] = '';
            } else if($k_row==$end) {
                $param_date =  date("Y-m-d H:i",strtotime($v_row['msg_time']));
                $end_id = $v_row['timeline_list_id'];
                $v_row['msg_date'] = getDatetimeformat($v_row['msg_time']);
                $v_row['post_order'] = 'end';
            } else {
                $param_date =  $v_row['msg_date'];
                $v_row['msg_date'] = getDateformat($v_row['msg_date']);
                $v_row['post_order'] = '';
            }

            if(!in_array($v_row['msg_date'],$date_arr)) {
                $result[$rows] = $v_row;
                $post_type = get_soruce_full($v_row['sourceid']);
                $result[$rows]['post_link']  = get_post_link($v_row['post_link'],$v_row);
                $result[$rows]['msg_time']   = getDatetimeformat($v_row['msg_time']);
                $result[$rows]['post_icon']  = '<i class="ico ico-'.$post_type.'"></i>';
                $result[$rows]['post_count'] = 0;
                $result[$rows]['param_date'] = $param_date;
                array_push($date_arr,$v_row['msg_date']);
                $rows_arr[$v_row['msg_date']] = $rows;
                $rows++;
            } else {
                $rows2 = $rows_arr[$v_row['msg_date']];
                $result[$rows2]['post_count']++;
            }
        }

        if($start_date!="") $start_date = date("Y-m-d H:i",strtotime(setDatetimeformat($start_date)));

        return $result;
    }

    function get_feed_list($post = array())
    {
        $result = array();
        $timeline_id = $post['timeline_id'];
        $msg_date = date("Y-m-d",strtotime($post['msg_date']));

        if(strpos($post['msg_date'],":")!==false) {
            $this->db->where("DATE_FORMAT(msg_time,\"%Y-%m-%d %H:%i\")='".$post['msg_date']."'",null,false);
        } else {
            if($post['start_date']!="") $this->db->where("DATE_FORMAT(msg_time,\"%Y-%m-%d %H:%i\")<>'".$post['start_date']."'",null,false);
            if($post['end_id']!="") $this->db->where("timeline_list_id <>",$post['end_id']);
        }

        $sql = $this->db
                    ->where("client_id",$this->CLIENT_ID)
                    ->where("timeline_id",$timeline_id)
                    ->where("msg_date",$msg_date)
                    ->order_by("msg_time","ASC")
                    ->from("client_timeline_list")
                    ->query_string();
        $num_rows = $this->db->query($sql)->num_rows();
        $end_date = $num_rows -1; 

        $post['post_rows'] = isset($post['post_rows']) ? $post['post_rows'] : 1;
        $newsql   = get_page($sql,$this->db->dbdriver,$post['post_rows'],PAGESIZE);
        $rowsdata = $this->db->query($newsql)->result_array();
        $start_page = (($post['post_rows']-1)*PAGESIZE);

        foreach($rowsdata as $k_row=>$v_row) {
            $result[$k_row] = $v_row;
            $post_type = get_soruce_full($v_row['sourceid']);
            $result[$k_row]['post_link'] = get_post_link($v_row['post_link'],$v_row);
            $result[$k_row]['msg_time']  = getDatetimeformat($v_row['msg_time']);
            $result[$k_row]['post_icon'] = '<i class="ico ico-'.$post_type.'"></i>';
            if($k_row==0 && $post['post_rows']==1) {
                $result[$k_row]['post_order'] = 'start';
            } else if(($k_row+$start_page)==$end_date) {
                $result[$k_row]['post_order'] = 'end';
            } else {
                $result[$k_row]['post_order'] = '';
            }
        }
        return $result;
    }

    function set_timeline_date($post = array())
    {
        list($start_date,$end_date) = explode("-",$post['timeline_date']);
        $start_date = setDateformat(trim($start_date));
        $end_date   = setDateformat(trim($end_date));
        return array("start_date"=>$start_date,"end_date"=>$end_date);
    }

    function get_timeline_date($post = array())
    {
        $start_date = getDateformat(trim($post['start_date']));
        $end_date   = getDateformat(trim($post['end_date']));
        return array("start_date"=>$start_date,"end_date"=>$end_date);
    }

}
?>