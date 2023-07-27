<?php
class Post_monitoring_model extends CI_Model {

    var $CLIENT_ID;
    var $FLAG_DELETE = 1;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
        $this->load->library("linkapi");
    }

    function get_post_id($post_id = 0)
    {
        $rowsdata = $this->db
                    ->select("client_post.*")
                    ->where("client_post.post_id",$post_id)
                    ->where("client_post.client_id",$this->CLIENT_ID)
                    ->get("client_post_monitoring client_post")
                    ->first_row('array');

        return $rowsdata;
    }

    function get_client_post()
    {
        $result = array();
        $rowsdata = $this->db
                    ->select("client_post.*")
                    ->where("client_post.client_id",$this->CLIENT_ID)
                    ->order_by("client_post.post_id","ASC")
                    ->get("client_post_monitoring client_post")
                    ->result_array();

        foreach($rowsdata as $key=>$val) { 
            $end_date = strtotime($val['end_date']);
            if($end_date<strtotime("now")) {
                $val['post_expire'] = true;
            } else {
                $val['post_expire'] = false;
            }
            array_push($result,$val);
        }

        return $result;
    }


    function insert_post($post = array())
    {
        $date_now = date("Y-m-d H:i:s");
        $post_url = $post['post_url'];
        $source = $this->linkapi->get_link_source($post_url);
        $msg_id = $this->linkapi->get_link_msg_id($post_url,true);

        $save = array();
        $save["client_id"]    = $this->CLIENT_ID;
        $save["post_name"]    = $post['post_name'];
        $save['post_url']     = $post_url;
        $save['sourceid']     = get_media_type_id($source);
        $save['msg_id']       = $msg_id;
        $save["created_date"] = $date_now;
        $save["start_date"]   = $date_now;
        $save["end_date"]     = date("Y-m-d H:i:s",strtotime($date_now. ' +2 WEEK'));
        $save["status"]       = "0";

        $this->db->insert("client_post_monitoring",$save);
        $post_id = $this->db->insert_id("client_post_monitoring");

        return $post_id;
    }

    function update_post($post = array())
    {
        $date_now = date("Y-m-d H:i:s");
        $post_url = $post['post_url'];
        $post_id = $post['post_id'];

        $rec = $this->get_post_id($post_id);

        $save = array();
        $save["post_name"]  = $post['post_name'];

        if($post['post_renew']=="YES") {
            $save["created_date"] = date("Y-m-d H:i:s");
            $save["end_date"]     = date("Y-m-d H:i:s",strtotime($date_now. ' +2 WEEK'));
        }

        $this->db->where("post_id",$post_id);
        $this->db->update("client_post_monitoring",$save);

        return $post_id;
    }

    function delete_post($post_id = 0)
    {
        $rec = $this->db
            ->select("post_name")
            ->where("post_id",$post_id)
            ->get("client_post_monitoring client_post")
            ->first_row("array");

        $where_delete = array("client_id"=>$this->CLIENT_ID,"post_id"=>$post_id);
        $this->db->delete("client_post_monitoring",$where_delete);

        return $rec['post_name'];
    }

    function check_post_name($post = array())
    {
        $num_rows = 0;
        $check = false;
        if(@$post['post_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_post_id($post['post_id']);
            if($post['post_name']!=@$rec['post_name']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_post.post_name",$post['post_name'])
                ->where("client_post.client_id",$this->CLIENT_ID)
                ->get("client_post_monitoring client_post")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_post_url($post = array())
    {
        $num_rows = 0;
        $check = false;
        $msg_id = $this->linkapi->get_link_msg_id($post['post_url']);
        if(@$post['post_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_post_id($post['post_id']);
            if($msg_id!=@$rec['msg_id']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_post.msg_id",$msg_id)
                ->where("client_post.client_id",$this->CLIENT_ID)
                ->get("client_post_monitoring client_post")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_post_max(&$add_post_monitoring)
    {
        $config = $this->master_model->get_config();
        $add_post_monitoring = isset($config['add_post_monitoring']) ? $config['add_post_monitoring'] : 10;
        
        $this->db
            ->select("post_id")
            ->where("client_id",$this->CLIENT_ID)
            ->where("MONTH(start_date)",date("n"))
            ->get("client_post_monitoring")
            ->result_array();

        $sql = $this->db->last_query();
        
        $sql_log = str_replace("client_post_monitoring","client_post_monitoring_log",$sql);
        
        $num_rows = $this->db->query($sql.' UNION '.$sql_log)->num_rows();
        
        return ($num_rows>=$add_post_monitoring) ? true : false;
    }

    function get_mongo_post($msg_id = 0) 
    {
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
        $collection = $mongodb->selectCollection("Postmonitoring");
        $query = array('_id' => $msg_id);
        $cursor = $collection->find($query);
        return iterator_to_array($cursor,false);
    }

    function update_mark($post = array())
    {
        $post_id = $post['post_id'];
        $flags_mark = $post['flags_mark'];

        $save = array();
        $save["flags_mark"]  = json_encode($flags_mark);

        $this->db->where("post_id",$post_id);
        $this->db->update("client_post_monitoring",$save);

        return $post_id;
    }

    function get_post_comment($post_id,&$total_rows)
    {
        $result = array();

        $lenght = $post["length"];
        // $page   = ($post['start'] / $lenght) + 1;
        // $sort   = $post['order']['0']['column'];
        // $order  = $post['order']['0']['dir'];
        // $column = array("msg_id");

        $connection = new MongoClient(MONGO_CONNECTION);
        $mongodb = $connection->blue_eye;
        $collection = $mongodb->selectCollection("Postmonitoring_Comment");

        $query = array('post_id' => $post_id);
        $total_rows = $collection->find($query).count();
        $cursor = $collection->find($query);
        $cursor->timeout(-1);
        $post_link = 'this is postlink';

        foreach($cursor as $k_row=>$v_row) {
            array_push($result,array(
                $post_link,
                $v_row['commentuser'],
                $v_row['commentlink'],
                $v_row['commentcontent'],
                $v_row['commentlikes']['like'],
                $v_row['commentlikes']['sad']
            ));
        }

        return $result;
    }

    //create postmonitoring===================

    // funtion connect mysql=> client_post_monitoring
    function get_postmonitoring_id($post_id = 0)
    {
        $rowsdata = $this->db
                    ->select("client_post.*")
                    ->where("client_post.post_id",$post_id)
                    ->where("client_post.client_id",$this->CLIENT_ID)
                    ->get("client_post_monitoring client_post")
                    ->first_row('array');
        return $rowsdata;
    }

    //funtion connect mongo=>  Postmonitoring_Comment
    function get_mongo_commentUser($post_id = 0) 
    {
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
        $collection = $mongodb->selectCollection("Postmonitoring_Comment");
        $query = array('post_id' => $post_id);
        $cursor = $collection->find($query);
        return iterator_to_array($cursor,false);
    }


    // function export post_monitoring
    function get_data_export($post_id = 0){
        $result_post = array();
        $data = $this->post_monitoring_model->get_postmonitoring_id($post_id);
        $data_commentuser = $this->post_monitoring_model->get_mongo_commentUser($data['post_id']);
        if (count($data_commentuser)>0){
            for ($x = 0;$x < count($data_commentuser);$x++){
                $url_post = $data['post_url'];
                $user_post = $data_commentuser[$x]['commentuser'];
                $content_post = $data_commentuser[$x]['commentcontent'];
                $total_post = $data_commentuser[$x]['commentlikes']["total"];
                $like_post = $data_commentuser[$x]['commentlikes']["like"];
                $love_post = $data_commentuser[$x]['commentlikes']["love"];
                $wow_post = $data_commentuser[$x]['commentlikes']["wow"];
                $laugh_post = $data_commentuser[$x]['commentlikes']["laugh"];
                $sad_post = $data_commentuser[$x]['commentlikes']["sad"];
                $angry_post = $data_commentuser[$x]['commentlikes']["angry"];
                $care_post = $data_commentuser[$x]['commentlikes']["care"];
                $source_post = $data_commentuser[$x]['commentlink'];
                $time_post = $data_commentuser[$x]['commenttimepost'];

                array_push($result_post,array(
                    'Url_post' =>$url_post,
                    'Comment_post' =>$user_post,
                    'Content_post' =>$content_post,
                    'Total_post' => $total_post,
                    'Like_post' =>$like_post,
                    'Love_post' => $love_post,
                    'Wow_post' => $wow_post,
                    'Laugh_post' => $laugh_post,
                    'Sad_post' =>$sad_post,
                    'Angry_post' => $angry_post,
                    'Care_post' => $care_post,
                    'Source_post' =>$source_post,
                    'Time_post' =>$time_post
                ));
            }
         return $result_post;
        }
    }
    //----------------------------------------------test
    function test_get_data_export($post_id = 0){
        $result_post = array();
        $data = $this->post_monitoring_model->get_postmonitoring_id($post_id);
        $data_commentuser = $this->post_monitoring_model->get_mongo_commentUser($data['post_id']);

        return $data_commentuser;

    }

}
?>