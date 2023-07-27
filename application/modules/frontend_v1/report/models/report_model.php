<?php
class Report_model extends CI_Model {

	var $CLIENT_ID;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
    }

    function get_feed($post = array())
    {
        return $this->realtime_model->get_feed($post);
    }

    function get_top_share($post = array())
    {
        $config = $this->master_model->get_config();
        $top_share  = isset($config['top_share ']) ? $config['top_share '] : 10;

        $result = array();

        $this->master_model->get_where_current(@$post["period"]);

        $rowsdata = $this->db
                    ->select("own_match.*")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->where("sourceid",1)
                    ->order_by("own_match.post_share DESC ,own_match.msg_time DESC")
                    ->limit($top_share)
                    ->get("own_match")
                    ->result_array();

        $rsFeed = $this->realtime_model->get_feed_type($rowsdata);

        $arrFeed = $this->realtime_model->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->realtime_model->get_mongo_comment($rsFeed["arrComment"]);

        foreach($rowsdata as $k_row=>$v_row)
        {
            $sourceid = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);

            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }
            $post_detail = mb_substr($feed["post_detail"],0,50)."...";
            array_push($result,array("post_id"=>$v_row["msg_id"],
                "post_link"=>$feed["post_link"],
                "post_detail"=>$post_detail,
                "post_time"=>$v_row["msg_time"],
                "sourceid"=>$sourceid,
                "post_type"=>$media_full,
                "count_share"=>$v_row["post_share"]));
        }
        return $result;
    }

    function get_top_user($post = array())
    {
        $config = $this->master_model->get_config();
        $top_user  = isset($config['top_user']) ? $config['top_user'] : 10;

        $result = array();

        $this->master_model->get_where_current(@$post["period"]);

        $rowsdata = $this->db
                    ->select("own_match.post_user_id")
                    ->select("SUM(own_match.own_match_sentiment) AS sumSentiment")
                    ->select("COUNT(*) As count_post")
                    ->group_by("own_match.post_user_id")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->where("own_match.sourceid <>",4)
                    ->order_by("count_post DESC")
                    ->limit($top_user)
                    ->get("own_match")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $user = $this->master_model->get_post_user($v_row['post_user_id']);
            $sentiment = ($v_row["count_post"]>0) ? round($v_row["sumSentiment"]/$v_row["count_post"],2) : 0;
            array_push($result,array("post_user_id"=>@$v_row["post_user_id"],"post_name"=>@$user["post_user"],"sourceid"=>@$user["sourceid"],"sentiment"=>$sentiment,"count_post"=>$v_row["count_post"]));
        }
        return $result;
    }


    function get_word_cloud($post = array())
    {
        $result = array();

        $this->master_model->get_where_current(@$post["period"]);
        
        $rowsdata = $this->db
                    ->select("keyword.keyword_name")
                    ->select("COUNT(*) As share_count")
                    ->group_by("keyword.keyword_name")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->join("own_key_match own_key","own_key.own_match_id = own_match.own_match_id")
                    ->join("keyword","keyword.keyword_id = own_key.keyword_id")
                    ->get("own_match")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            array_push($result,array("key" => $v_row["keyword_name"],"share_count" => $v_row["share_count"]));
        }
        return $result;
    }

}
?>