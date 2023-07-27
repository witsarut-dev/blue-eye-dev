<?php
class Marketing_model extends CI_Model {

	var $CLIENT_ID;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
    }

    function get_where($post,$tb = "own_match")
    {
        if(isset($post['media_type']) && $post['media_type']!="") {
            $media_type = get_media_type_id(get_media_type($post['media_type']));
            $this->db->where("{$tb }.sourceid",$media_type);
        }

        if($this->input->get('media_type') && $this->input->get('media_type')!="") {
            $media_type = get_media_type_id(get_media_type($this->input->get('media_type')));
            $this->db->where("{$tb }.sourceid",$media_type);
        }
    }

    function get_feed($post = array())
    {
        $result = array();
        $com = $this->setting_model->get_company($post['com_id']);

        if(@$com["company_keyword_type"]=="Competitor") {
            $this->get_where($post,"competitor_match");
            $this->master_model->get_where_sentiment($post,"competitor_match");
            $this->master_model->get_where_current(@$post["period"],"competitor_match");
            $sql = $this->db
                ->select("competitor_match.*")
                ->select("competitor_match.competitor_match_sentiment AS match_sentiment")
                ->where("competitor_match.client_id",$this->CLIENT_ID)
                ->where("competitor_cate.category_id",$post['cate_id'])
                ->where("competitor_match.company_keyword_id",$post['com_id'])
                ->join("competitor_cate_match competitor_cate","competitor_cate.competitor_match_id = competitor_match.competitor_match_id","left")
                ->order_by("competitor_match.msg_time","DESC")
                ->from("competitor_match")
                ->query_string();
        } else {
            $this->get_where($post,"own_match");
            $this->master_model->get_where_sentiment($post,"own_match");
            $this->master_model->get_where_current(@$post["period"],"own_match");
            $sql = $this->db
                ->select("own_match.*")
                ->select("own_match.own_match_sentiment AS match_sentiment")
                ->where("own_match.client_id",$this->CLIENT_ID)
                ->where("own_cate.category_id",$post['cate_id'])
                ->where("own_match.company_keyword_id",$post['com_id'])
                ->join("own_cate_match own_cate","own_cate.own_match_id = own_match.own_match_id","left")
                ->order_by("own_match.msg_time","DESC")
                ->from("own_match")
                ->query_string();
        }

        $newsql   = get_page($sql,$this->db->dbdriver,$post['post_rows'],PAGESIZE);
        $rowsdata = $this->db->query($newsql)->result_array();
        $rsFeed   = $this->realtime_model->get_feed_type($rowsdata);

        $arrFeed    = $this->realtime_model->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->realtime_model->get_mongo_comment($rsFeed["arrComment"]);

        foreach($rowsdata as $k_row=>$v_row) {
            $sourceid    = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }
            $sentiment = ($v_row['match_sentiment']);
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

    function get_category($post = array())
    {
        $rowsdata = array();
        $rowsdata =  $this->db
                ->where("client_id",$this->CLIENT_ID)
                ->order_by("category_id","ASC")
                ->get("category")
                ->result_array();
        return $rowsdata;
    }

    function get_sentiment($post = array())
    {
        $result = array();

        $this->get_where($post,"own_match");
        $this->master_model->get_where_current(@$post["period"]);
        $rowsdata = $this->db
            ->select("own_match.company_keyword_id,own_cate.category_id")
            ->select("SUM(own_match.own_match_sentiment/100) AS sumSentiment")
            ->select("COUNT(*) AS countTotal")
            ->group_by("own_match.company_keyword_id,own_cate.category_id")
            ->where("own_match.client_id",$this->CLIENT_ID)
            ->join("own_cate_match own_cate","own_cate.own_match_id = own_match.own_match_id","left")
            ->get("own_match")
            ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            $company_keyword_id = $v_row['company_keyword_id'];
            $category_id        = $v_row['category_id'];
            $result[$company_keyword_id][$category_id] = ($v_row["countTotal"]>0) ? round(($v_row["sumSentiment"]/$v_row["countTotal"]),2) : 0;
        }

        $this->get_where($post,"competitor_match");
        $this->master_model->get_where_current(@$post["period"],"competitor_match");
        $rowsdata = $this->db
            ->select("competitor_match.company_keyword_id,competitor_cate.category_id")
            ->select("SUM(competitor_match.competitor_match_sentiment/100) AS sumSentiment")
            ->select("COUNT(*) AS countTotal")
            ->group_by("competitor_match.company_keyword_id,competitor_cate.category_id")
            ->where("competitor_match.client_id",$this->CLIENT_ID)
            ->join("competitor_cate_match competitor_cate","competitor_cate.competitor_match_id = competitor_match.competitor_match_id","left")
            ->get("competitor_match")
            ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            $company_keyword_id = $v_row['company_keyword_id'];
            $category_id        = $v_row['category_id'];
            $result[$company_keyword_id][$category_id] = ($v_row["countTotal"]>0) ? round(($v_row["sumSentiment"]/$v_row["countTotal"]),2) : 0;
        }
        return $result;
    }

    function get_category_data($category = array())
    {
        $rowsdata = array();
        foreach($category as $k_row=>$v_row) {
            array_push($rowsdata,$v_row['category_name']);
        }
        return $rowsdata;
    }

    function get_positive_data($category = array(),$company= array(),$sentiment= array())
    {
        $color = array("#449d44","#74f32a","#169F85","#007711");
        $rowsdata = array();
        foreach($company as $k_com=>$v_com) {
            $name = $v_com['company_keyword_name'];
            $data = array();
            foreach($category as $k_row=>$v_row) {
                $company_keyword_id = $v_com["company_keyword_id"];
                $category_id = $v_row["category_id"];
                $value = floatval(@$sentiment[$company_keyword_id][$category_id]);
                if($value<0) $value = 0;
                array_push($data,abs($value));
            }
            array_push($rowsdata,array("name"=>$name,"color"=>@$color[$k_com],"data"=>$data,"pointPlacement"=>"on"));
        }
        return $rowsdata;
    }

    function get_negative_data($category = array(),$company= array(),$sentiment= array())
    {
        $color = array("#fb0500","#b71111","#ffa500","#bd0e0e");
        $rowsdata = array();
        foreach($company as $k_com=>$v_com) {
            $name = $v_com['company_keyword_name'];
            $data = array();
            foreach($category as $k_row=>$v_row) {
                $company_keyword_id = $v_com["company_keyword_id"];
                $category_id = $v_row["category_id"];
                $value = floatval(@$sentiment[$company_keyword_id][$category_id]);
                if($value>0) $value = 0;
                array_push($data,abs($value));
            }
            array_push($rowsdata,array("name"=>$name,"color"=>@$color[$k_com],"data"=>$data,"pointPlacement"=>"on"));
        }
        return $rowsdata;
    }


    function check_category($category_name = "") 
    {
        $num_rows = $this->db
                    ->where("category.category_name",$category_name)
                    ->where("category.client_id",$this->CLIENT_ID)
                    ->get("category")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function insert_category($post = array())
    {
        $save = array();
        $save['client_id']             = $this->CLIENT_ID;
        $save['category_name']         = $post['category_name'];
        $save['created_date']          = date("Y-m-d H:i:s");
        $this->db->insert("category",$save);

        $category_id = $this->db->insert_id("category");

        return $category_id;
    }

    function delete_category($category_id = 0)
    {
        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("category_id",$category_id);
        $this->db->delete("own_cate_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("category_id",$category_id);
        $this->db->delete("competitor_cate_match");

        $rec = $this->db
            ->select("category_name")
            ->where("category_id",$category_id)
            ->get("category")
            ->first_row("array");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("category_id",$category_id);
        $this->db->delete("category");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("category_id",$category_id);
        $this->db->delete("sys_task");

        return $rec['category_name'];
    }

}
?>