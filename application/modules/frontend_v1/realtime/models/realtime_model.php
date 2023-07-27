<?php
class Realtime_model extends CI_Model {

    var $CLIENT_ID;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
    }

    function get_own_match($post_id = 0)
    {   
        $rowsdata = $this->db
            ->select("own_match.*")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.msg_id",$post_id)
            ->get("own_match")
            ->first_row('array');

        return $rowsdata;
    }

    function get_where($post)
    {
        if(isset($post['post_type']) && $post['post_type']!="") {
            if($post['post_type']=="MediaBox") {
                $this->db->where_in("own_match.sourceid",array(1,2,3,6));
            } else if($post['post_type']=="NewsBox") {
                $this->db->where_in("own_match.sourceid",array(4));
            } else if($post['post_type']=="WebBox") {
                $this->db->where_in("own_match.sourceid",array(5));
            } 
        }

        if(isset($post['media_type']) && $post['media_type']!="") {
            if($post['media_type']!="All") {
                $media_type = get_media_type_id($post['media_type']);
                $this->db->where("own_match.sourceid",$media_type);
            }
        }

        if(isset($post['keyword']) && $post['keyword']!="") {
            //$this->db->where("keyword.keyword_name",$post['keyword']);
            $this->db->where("own_match.own_match_id IN (SELECT own_match_id FROM own_key_match JOIN keyword ON own_key_match.keyword_id = keyword.keyword_id WHERE keyword_name = '".$post['keyword']."')",null,false);
        }

        if(isset($post['keyword_in']) && count($post['keyword_in'])>0) {
            //$this->db->where_in("keyword.keyword_id",$post['keyword_in']);
            $this->db->where("own_match.own_match_id IN (SELECT own_match_id FROM own_key_match WHERE keyword_id IN (".implode(",",$post['keyword_in'])."))",null,false);
        }

        if(isset($post['post_user']) && $post['post_user']!="") {
            $this->db->where("own_match.post_user",stripcslashes($post['post_user']));
        }

        if(isset($post['post_user_id']) && $post['post_user_id']!="") {
            $this->db->where("own_match.post_user_id",$post['post_user_id']);
        }

        if(isset($post['time']) && $post['time']!="")  {
            $time = substr($post['time'],0,(strlen($post['time'])-3));
            if(date("Hi",$time)=="0000"){
                $this->db->where("DATE(own_match.msg_time)",date("Y-m-d",$time));
            } else {
                $this->db->where("DATE_FORMAT(own_match.msg_time,'%Y-%m-%d %H:%i') = '".date("Y-m-d H:i",$time)."'",null,false);
            }
        }

        //$this->master_model->get_width_out();
    }

    function get_feed($post = array())
    {
        $result = array();

        $this->get_where($post);

        if(isset($post['period_type']) && $post['period_type']=="before") {
            $this->master_model->get_where_before(@$post["period"]);
        } else {
            $this->master_model->get_where_current(@$post["period"]);
        }

        $this->master_model->get_where_sentiment($post,"own_match");

        $select = "own_match.company_keyword_id,own_match.msg_id,own_match.msg_time,own_match.match_type,own_match.sourceid,own_match.post_user_id";
        $sql = $this->db
            ->select($select)
            ->select("own_match.own_match_sentiment AS match_sentiment")
            //->group_by($select)
            //->group_by("own_match.own_match_sentiment")
            ->where("own_match.client_id",$this->CLIENT_ID)
            //->join("own_key_match own_key","own_key.own_match_id = own_match.own_match_id","left")
            //->join("keyword","keyword.keyword_id = own_key.keyword_id","left")
            ->order_by("own_match.msg_time","DESC")
            ->from("own_match")
            ->query_string();

        $post['post_rows'] = isset($post['post_rows']) ? $post['post_rows'] : 1;
        $newsql   = get_page($sql,$this->db->dbdriver,$post['post_rows'],PAGESIZE);
        $rowsdata = $this->db->query($newsql)->result_array();
        $rsFeed   = $this->get_feed_type($rowsdata);

        $arrFeed    = $this->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"]);

        foreach($rowsdata as $k_row=>$v_row) {
            $sourceid    = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }
            $sentiment = $v_row['match_sentiment'];
            array_push($result,
                array("com_id"=>$v_row['company_keyword_id'],
                    "post_id"=>$v_row['msg_id'],
                    "post_user_id"=>$v_row['post_user_id'],
                    "post_name"=>$feed['post_user'],
                    "post_link"=>@$feed['post_link'],
                    "post_detail"=>@$feed['post_detail'],
                    "post_time"=>$v_row['msg_time'],
                    "post_type"=>$media_full,
                    "sourceid"=>$v_row['sourceid'],
                    "sentiment"=>$sentiment));
        }

        return $result;
    }

    function add_feed($post = array())
    {
        $result = array();

        $this->get_where($post);

        if(isset($post['period_type']) && $post['period_type']=="before") {
            $this->master_model->get_where_before(@$post["period"]);
        } else {
            $this->master_model->get_where_current(@$post["period"]);
        }

        $this->master_model->get_where_sentiment($post,"own_match");

        $select = "own_match.company_keyword_id,own_match.msg_id,own_match.msg_time,own_match.match_type,own_match.sourceid,own_match.post_user_id";
        $sql = $this->db
            ->select($select)
            ->select("own_match.own_match_sentiment AS match_sentiment")
            //->group_by($select)
            //->group_by("own_match.own_match_sentiment")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->where("own_match.msg_time >",$post['last_time'])
            //->join("own_key_match own_key","own_key.own_match_id = own_match.own_match_id","left")
            //->join("keyword","keyword.keyword_id = own_key.keyword_id","left")
            ->order_by("own_match.msg_time","DESC")
            ->from("own_match")
            ->query_string();

        $rowsdata = $this->db->query($sql)->result_array();
        $rsFeed   = $this->get_feed_type($rowsdata);

        $arrFeed    = $this->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"]);

        foreach($rowsdata as $k_row=>$v_row) {
            $sourceid    = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }
            $sentiment = $v_row['match_sentiment'];
            array_push($result,
                array("com_id"=>$v_row['company_keyword_id'],
                    "post_id"=>$v_row['msg_id'],
                    "post_user_id"=>$v_row['post_user_id'],
                    "post_like"=>@$feed['post_like'],
                    "post_name"=>$feed['post_user'],
                    "post_link"=>@$feed['post_link'],
                    "post_detail"=>@$feed['post_detail'],
                    "post_time"=>$v_row['msg_time'],
                    "post_type"=>$media_full,
                    "sourceid"=>$v_row['sourceid'],
                    "sentiment"=>$sentiment));
        }

        return $result;
    }

    function get_feed_type($rowsdata = array())
    {
        $arrFeed = array();
        $arrComment = array();
        foreach($rowsdata as $k_row=>$v_row) {
            if($v_row['match_type']=="Feed") {
                array_push($arrFeed,$v_row['msg_id']);
            } else {
                array_push($arrComment,$v_row['msg_id']);
            }
        }
        return array("arrFeed"=>$arrFeed,"arrComment"=>$arrComment);
    }

    function get_mongo_feed($msg_id = array())
    {
        $result = array();
        if(count($msg_id)>0) {
            $mongo = new MongoClient(MONGO_CONNECTION);
            $mongodb = $mongo->blue_eye;
            $collection = $mongodb->selectCollection("Feed");
            $query = array('_id' => array('$in'=>$msg_id));
            $cursor = $collection->find($query);
           
            foreach($cursor as $k_row=>$v_row) {
                $result[$v_row['_id']] = array("post_user"=>@$v_row['feeduser'],"post_link"=>@$v_row['feedlink'],"post_detail"=>@$v_row['feedcontent'],"post_like"=>@$v_row['feedlikes']);
            }
            $mongo->close();
        }
        return $result;
    }

    function get_mongo_comment($msg_id = array())
    {
        $result = array();
        if(count($msg_id)>0) {
            $mongo = new MongoClient(MONGO_CONNECTION);
            $mongodb = $mongo->blue_eye;
            $collection = $mongodb->selectCollection("Comment");
            $query = array('_id' => array('$in'=>$msg_id));
            $cursor = $collection->find($query);
           
            foreach($cursor as $k_row=>$v_row) {
                $result[$v_row['_id']] = array("post_user"=>@$v_row['commentuser'],"post_link"=>@$v_row['commentlink'],"post_detail"=>@$v_row['commentcontent']);
            }
            $mongo->close();
        }
        return $result;
    }

    function get_post_detail($post_id = 0,$com_id = 0)
    {
        $arrFeed = array();
        $result = array();

        $rowsdata = $this->get_rows_match($post_id,$com_id);
        $rsFeed = $this->get_feed_type($rowsdata);
        $arrFeed = $this->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"]);

        foreach($rowsdata as $k_row=>$v_row) {
            $sourceid = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }
            $sentiment = $v_row['match_sentiment'];

            $result = array("com_id"=>$v_row['company_keyword_id'],
                            "post_id"=>$v_row['msg_id'],
                            "post_user_id"=>$v_row['post_user_id'],
                            "post_name"=>$feed['post_user'],
                            "post_link"=>@$feed['post_link'],
                            "post_detail"=>@$feed['post_detail'],
                            "post_time"=>$v_row['msg_time'],
                            "post_type"=>$media_full,
                            "match_type"=>$v_row['match_type'],
                            "sourceid"=>$v_row['sourceid'],
                            "sentiment"=>$sentiment);
        }

        return $result;
    }

    function get_comments()
    {   
        $rowsdata = array();
        $msg_id   = array();
        $post = $this->input->post();
        $post_id   = $post['post_id'];
        $com_id    = $post['com_id'];
        $post_rows = $post['post_rows'];
        $skip  = ($post_rows-1) * PAGESIZE;

        $rec = $this->get_rows_match($post_id,$com_id); 

        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;

        if(isset($rec[0]['match_type']) && $rec[0]['match_type']=="Feed") {

            $collection = $mongodb->selectCollection("Comment");
            $query = array('feedid' => $post_id);
            $field = array("_id");
            $cursor = $collection->find($query,$field);
  
            foreach($cursor as $k_row=>$v_row) {
                $rec = $this->get_rows_match($v_row['_id'],$com_id);
                if(isset($rec[0]['msg_id'])) array_push($msg_id,$v_row['_id']); 
            }
            if(count($msg_id)>0) {
                $collection = $mongodb->selectCollection("Comment");
                $query2 = array('feedid' => $post_id,'_id' => array('$in'=>$msg_id));
                $cursor2 = $collection
                    ->find($query2)
                    ->sort(array("feedtimepost"=>-1))
                    ->limit(PAGESIZE)
                    ->skip($skip);
               
                foreach($cursor2 as $k2_row=>$v2_row) {
                    array_push($rowsdata,array("post_id"=>$v2_row["_id"],
                        "post_name"=>$v2_row["commentuser"],
                        "post_detail"=>$v2_row["commentcontent"],
                        "post_time"=>$v2_row["commenttimepost"],
                        "post_type"=>"Comment"));
                }
            }
            
        } else {

            $collection = $mongodb->selectCollection("Comment");
            $query = array('_id' => $post_id);
            $cursor = $collection->find($query);

            foreach($cursor as $k_row=>$v_row) {
                $collection = $mongodb->selectCollection("Feed");
                $query2 = array('_id' => $v_row['feedid']);
                $cursor2 = $collection
                    ->find($query2)
                    ->limit(1)
                    ->skip($skip);
                foreach($cursor2 as $k2_row=>$v2_row) {
                    array_push($rowsdata,array("post_id"=>$v2_row["_id"],
                        "post_name"=>$v2_row["feeduser"],
                        "post_detail"=>$v2_row["feedcontent"],
                        "post_time"=>$v2_row["feedtimepost"],
                        "post_type"=>"Feed"));
                }
            }
        }

        $mongo->close();
        
        return $rowsdata;
    }

    function insert_filter_keyword($post = array())
    {
        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("name","realtime_keyword");
        $this->db->delete("client_meta");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("name","realtime_group_keyword");
        $this->db->delete("client_meta");

        if(isset($post['keyword_id'])) {
            $save = array();
            $save["client_id"] = $this->CLIENT_ID;
            $save["name"]      = "realtime_keyword";
            $save["value"]     = implode(",",$post['keyword_id']);
            $this->db->insert("client_meta",$save);
        }

        if(isset($post['group_keyword_id'])) {
            $save = array();
            $save["client_id"] = $this->CLIENT_ID;
            $save["name"]      = "realtime_group_keyword";
            $save["value"]     = implode(",",$post['group_keyword_id']);
            $this->db->insert("client_meta",$save);
        }
    }

    function delete_post($post_id = 0)
    {
        $where = array("client_id"=>$this->CLIENT_ID,"msg_id"=>$post_id);
        $save  = array("msg_status"=>'0');

        $this->db->update("own_match",$save,$where);
        $this->db->update("competitor_match",$save,$where);
    }

    function block_post($post_id = 0)
    {
        $own_match = $this->get_own_match($post_id);

        $save = array();
        $save["client_id"]     = $this->CLIENT_ID;
        $save["post_user_id"]  = $own_match["post_user_id"];
        $save["block_user"]    = $own_match["post_user"];
        $save["sourceid"]      = $own_match["sourceid"];
        $save["block_time"]    = date("Y-m-d H:i:s");
        $this->db->insert("block_user",$save);

        return $own_match["post_user"];
    }

    function get_rows_match($post_id = 0,$com_id = 0)
    {
        $rowsdata = array();
        $com = $this->setting_model->get_company($com_id);
        if(@$com["company_keyword_type"]=="Competitor") {
            $rowsdata = $this->db
                ->select("competitor_match.*")
                ->select("competitor_match.competitor_match_sentiment AS match_sentiment")
                ->where("competitor_match.client_id",$this->CLIENT_ID)
                ->where("competitor_match.msg_id",$post_id)
                ->where("competitor_match.company_keyword_id",$com_id)
                ->get("competitor_match")
                ->result_array();
        } else {
            $rowsdata = $this->db
                ->select("own_match.*")
                ->select("own_match.own_match_sentiment AS match_sentiment")
                ->where("own_match.client_id",$this->CLIENT_ID)
                ->where("own_match.msg_id",$post_id)
                ->where("own_match.company_keyword_id",$com_id)
                ->get("own_match")
                ->result_array();
        }
        return $rowsdata;
    }

}
?>