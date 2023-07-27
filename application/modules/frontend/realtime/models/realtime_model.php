<?php
class Realtime_model extends CI_Model
{
    var $CLIENT_ID;
    var $custom_date;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
        $this->custom_date = $this->master_model->get_custom_date();
    }

    function get_own_match($post_id = 0)
    {
        $period = $this->master_model->get_period();
        // $table_match = get_match_table("own_match", $period);

        // 3months check
        if ($period == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if ($obj['start'] >= $date_3m) {
                $table_match = "own_match_3months own_match";
            } else {
                $table_match = get_match_table("own_match", $period);
            }
        } else {
            $table_match = get_match_table("own_match", $period);
        }

        $rowsdata = $this->db
            ->select("own_match.*")
            ->where("own_match.client_id", $this->CLIENT_ID)
            ->where("own_match.msg_id", $post_id)
            ->get("{$table_match}")
            ->first_row('array');

        return $rowsdata;
    }

    function get_where($post, $tb = "own")
    {
        if (isset($post['post_type']) && $post['post_type'] != "") {
            $this->db->where_in("{$tb}_match.sourceid", array(1, 2, 3, 4, 5, 6, 7, 8, 9));
        }

        if (isset($post['media_type']) && $post['media_type'] != "") {
            if ($post['media_type'] != "All") {
                $media_type = get_media_type_id($post['media_type']);
                $this->db->where("{$tb}_match.sourceid", $media_type);
            }
        }

        if (isset($post['keyword']) && $post['keyword'] != "") {
            //$this->db->where("keyword.keyword_name",$post['keyword']);
            $this->db->where("{$tb}_match.{$tb}_match_id IN (SELECT {$tb}_match_id FROM {$tb}_key_match JOIN keyword ON {$tb}_key_match.keyword_id = keyword.keyword_id WHERE keyword_name = '" . $post['keyword'] . "')", null, false);
        }

        if (isset($post['keyword_in']) && count($post['keyword_in']) > 0) {
            //$this->db->where_in("keyword.keyword_id",$post['keyword_in']);
            $this->db->where("{$tb}_match.{$tb}_match_id IN (SELECT {$tb}_match_id FROM {$tb}_key_match WHERE keyword_id IN (" . implode(",", $post['keyword_in']) . "))", null, false);
        }

        if (isset($post['post_user']) && $post['post_user'] != "") {
            $this->db->where("{$tb}_match.post_user", stripcslashes($post['post_user']));
        }

        if (isset($post['post_user_id']) && $post['post_user_id'] != "") {
            $this->db->where("{$tb}_match.post_user_id", $post['post_user_id']);
        }

        if (isset($post['time']) && $post['time'] != "") {
            $time = substr($post['time'], 0, (strlen($post['time']) - 3));
            if (date("Hi", $time) == "0000") {
                $this->db->where("DATE({$tb}_match.msg_time)", date("Y-m-d", $time));
            } else {
                $this->db->where("DATE_FORMAT({$tb}_match.msg_time,'%Y-%m-%d %H:%i') = '" . date("Y-m-d H:i", $time) . "'", null, false);
            }
        }
        $this->master_model->get_with_out("{$tb}_match");
    }

    // // _manual สร้างขึ้นเพื่อ return ค่ากลับไปเป็น รูปแบบ String แทนการสั่งให้ระบบทำการต่อ SQL ผ่านการใช้ $this->db->where หรือ $this->db->select เป็นต้น 
    function get_where_manual($post, $tb = "own")
    {
        $result = "";
        if (isset($post['post_type']) && $post['post_type'] != "") {
            $result .= "{$tb}_match.sourceid in (1, 2, 3, 4, 5, 6, 7, 8, 9)";

        }

        if (isset($post['media_type']) && $post['media_type'] != "") {
            if ($post['media_type'] != "All") {
                $media_type = get_media_type_id($post['media_type']);
                $result .= " and {$tb}_match.sourceid = {$media_type}";
            }
        }

        if (isset($post['keyword']) && $post['keyword'] != "") {
            $result .= " and {$tb}_match.{$tb}_match_id IN (SELECT {$tb}_match_id FROM {$tb}_key_match JOIN keyword ON {$tb}_key_match.keyword_id = keyword.keyword_id WHERE keyword_name = '" . $post['keyword'] . "')";
        }

        if (isset($post['keyword_in']) && count($post['keyword_in']) > 0) {
            $result .= " and {$tb}_match.{$tb}_match_id IN (SELECT {$tb}_match_id FROM {$tb}_key_match WHERE keyword_id IN (" . implode(",", $post['keyword_in']) . "))";
        }

        if (isset($post['post_user']) && $post['post_user'] != "") {
            $result .= " and {$tb}_match.post_user = " . stripcslashes($post['post_user']);
        }

        if (isset($post['post_user_id']) && $post['post_user_id'] != "") {
            $result .= " and {$tb}_match.post_user_id = " . $post['post_user_id'];
        }

        if (isset($post['time']) && $post['time'] != "") {
            $time = substr($post['time'], 0, (strlen($post['time']) - 3));
            if (date("Hi", $time) == "0000") {
                $result .= " and DATE({$tb}_match.msg_time) = " . date("Y-m-d", $time);
            } else {
                $result .= " and DATE_FORMAT({$tb}_match.msg_time,'%Y-%m-%d %H:%i') = '" . date("Y-m-d H:i", $time) . "'";
            }
        }

        // $this->master_model->get_with_out();
        return $result;
    }

    function get_is_read($post = array()) {
        // 3months check
        if (@$post["period"] == "Custom") {
            $date = date("Y-m-d");
            $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
            $obj = get_custom_date($this->custom_date);
            if ($obj['start'] >= $date_3m) {
                $table_match_own = "own_match_3months own_match";
                $table_match_competitor = "competitor_match_3months competitor_match";
            } else {
                $table_match_own = get_match_table("own_match", @$post["period"]);
                $table_match_competitor = get_match_table("competitor_match", @$post["period"]);
            }
        } else {
            $table_match_own = get_match_table("own_match", @$post["period"]);
            $table_match_competitor = get_match_table("competitor_match", @$post["period"]);
        }

        $where_own = $this->get_where_manual($post, "own");
        if (isset($post['period_type']) && $post['period_type'] == "before") {
            $where_time_own = $this->master_model->get_where_before_manual(@$post["period"]);
        } else {
            $where_time_own = $this->master_model->get_where_current_manual(@$post["period"]);
        }
        $where_sentiment_own = $this->master_model->get_where_sentiment_manual($post, "own_match");

        $where_competitor = $this->get_where_manual($post, "competitor");
        if (isset($post['period_type']) && $post['period_type'] == "before") {
            $where_time_competitor = $this->master_model->get_where_before_manual(@$post["period"], "competitor_match");
        } else {
            $where_time_competitor = $this->master_model->get_where_current_manual(@$post["period"], "competitor_match");
        }
        $where_sentiment_competitor = $this->master_model->get_where_sentiment_manual($post, "competitor_match");


        $rowsdata = $this->db->query("select A.company_keyword_id , A.msg_id , A.msg_time , A.match_type , A.sourceid , A.post_user_id , A.match_id , A.match_sentiment
                                    from ( select own_match.company_keyword_id company_keyword_id , own_match.msg_id msg_id , own_match.msg_time msg_time , own_match.match_type match_type , own_match.sourceid sourceid , own_match.post_user_id post_user_id , own_match.own_match_id match_id , own_match.own_match_sentiment match_sentiment
                                            from " . $table_match_own . "
                                            join own_key_match on own_match.own_match_id = own_key_match.own_match_id
                                            join keyword on own_key_match.keyword_id = keyword.keyword_id
                                            where own_match.client_id = '" . $this->CLIENT_ID . "'
                                            and keyword.primary_keyword = '1'
                                            and " . $where_time_own . "
                                            " . $where_sentiment_own . "
                                            and own_match.msg_status = 1
                                            and own_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and own_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and own_match.is_read = 0
                                            UNION
                                            select competitor_match.company_keyword_id company_keyword_id , competitor_match.msg_id msg_id , competitor_match.msg_time msg_time , competitor_match.match_type match_type , competitor_match.sourceid sourceid , competitor_match.post_user_id post_user_id , competitor_match.competitor_match_id match_id , competitor_match.competitor_match_sentiment match_sentiment
                                            from " . $table_match_competitor . "
                                            join competitor_key_match on competitor_match.competitor_match_id = competitor_key_match.competitor_match_id
                                            join keyword on competitor_key_match.keyword_id = keyword.keyword_id
                                            where competitor_match.client_id = '" . $this->CLIENT_ID . "'
                                            and keyword.primary_keyword = '1'
                                            and " . $where_time_competitor . "
                                            " . $where_sentiment_competitor . "
                                            and competitor_match.msg_status = 1
                                            and competitor_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and competitor_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and competitor_match.is_read = 0
                                        ) as A
                                    group by A.msg_id
                                    order by A.msg_time DESC")
                                    ->num_rows();
            $result = array();
            $result["row"] = number_format($rowsdata);
    
            return $result;
    }
    
    function update_is_read($post = array()){
        $this->db->set('is_read','1');
        $this->db->update('own_match_daily');

        $this->db->set('is_read','1');
        $this->db->update('competitor_match_daily');
        // return "";
    }

    function get_feed($post = array(), $tb = "All")
    {
        if ($tb == "All") {
            // $table_match_own = get_match_table("own_match", @$post["period"]);

            // 3months check
            if (@$post["period"] == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if ($obj['start'] >= $date_3m) {
                    $table_match_own = "own_match_3months own_match";
                    $table_match_competitor = "competitor_match_3months competitor_match";
                } else {
                    $table_match_own = get_match_table("own_match", @$post["period"]);
                    $table_match_competitor = get_match_table("competitor_match", @$post["period"]);
                }
            } else {
                $table_match_own = get_match_table("own_match", @$post["period"]);
                $table_match_competitor = get_match_table("competitor_match", @$post["period"]);
            }

            $where_own = $this->get_where_manual($post, "own");
            if (isset($post['period_type']) && $post['period_type'] == "before") {
                $where_time_own = $this->master_model->get_where_before_manual(@$post["period"]);
            } else {
                $where_time_own = $this->master_model->get_where_current_manual(@$post["period"]);
            }
            $where_sentiment_own = $this->master_model->get_where_sentiment_manual($post, "own_match");

            $where_competitor = $this->get_where_manual($post, "competitor");
            if (isset($post['period_type']) && $post['period_type'] == "before") {
                $where_time_competitor = $this->master_model->get_where_before_manual(@$post["period"], "competitor_match");
            } else {
                $where_time_competitor = $this->master_model->get_where_current_manual(@$post["period"], "competitor_match");
            }
            $where_sentiment_competitor = $this->master_model->get_where_sentiment_manual($post, "competitor_match");

            $post['post_rows'] = isset($post['post_rows']) ? $post['post_rows'] : 1;

            // start condition checking for Box tab
            if ($post['post_type'] == 'PriorityBox') {

                // start condition checking for keyword from filter
                if (isset($post['other_keyword'])) {
                    $rowsdata = $this->db->query("select A.company_keyword_id , A.msg_id , A.msg_time , A.match_type , A.sourceid , A.post_user_id , A.match_id , A.match_sentiment
                                                from ( select own_match.company_keyword_id company_keyword_id , own_match.msg_id msg_id , own_match.msg_time msg_time , own_match.match_type match_type , own_match.sourceid sourceid , own_match.post_user_id post_user_id , own_match.own_match_id match_id , own_match.own_match_sentiment match_sentiment
                                                        from " . $table_match_own . "
                                                        join own_key_match on own_match.own_match_id = own_key_match.own_match_id
                                                        join keyword on own_key_match.keyword_id = keyword.keyword_id
                                                        where own_match.client_id = '" . $this->CLIENT_ID . "'
                                                        and keyword.primary_keyword = '1'
                                                        and " . $where_own . "
                                                        and " . $where_time_own . "
                                                        " . $where_sentiment_own . "
                                                        " . $where_with_out_own . "
                                                        and own_match.msg_status = 1
                                                        and own_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                        and own_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                        UNION
                                                        select competitor_match.company_keyword_id company_keyword_id , competitor_match.msg_id msg_id , competitor_match.msg_time msg_time , competitor_match.match_type match_type , competitor_match.sourceid sourceid , competitor_match.post_user_id post_user_id , competitor_match.competitor_match_id match_id , competitor_match.competitor_match_sentiment match_sentiment
                                                        from " . $table_match_competitor . "
                                                        join competitor_key_match on competitor_match.competitor_match_id = competitor_key_match.competitor_match_id
                                                        join keyword on competitor_key_match.keyword_id = keyword.keyword_id
                                                        where competitor_match.client_id = '" . $this->CLIENT_ID . "'
                                                        and keyword.primary_keyword = '1'
                                                        and " . $where_competitor . "
                                                        and " . $where_time_competitor . "
                                                        " . $where_sentiment_competitor . "
                                                        " . $where_with_out_competitor . "
                                                        and competitor_match.msg_status = 1
                                                        and competitor_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                        and competitor_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                    ) as A
                                                group by A.msg_id
                                                order by A.msg_time DESC")->result_array();
                } else {
                    $newsql = get_page("select A.company_keyword_id , A.msg_id , A.msg_time , A.match_type , A.sourceid , A.post_user_id , A.match_id , A.match_sentiment
                                        from ( select own_match.company_keyword_id company_keyword_id , own_match.msg_id msg_id , own_match.msg_time msg_time , own_match.match_type match_type , own_match.sourceid sourceid , own_match.post_user_id post_user_id , own_match.own_match_id match_id , own_match.own_match_sentiment match_sentiment
                                            from " . $table_match_own . "
                                            join own_key_match on own_match.own_match_id = own_key_match.own_match_id
                                            join keyword on own_key_match.keyword_id = keyword.keyword_id
                                            where own_match.client_id = '" . $this->CLIENT_ID . "'
                                            and keyword.primary_keyword = '1'
                                            and " . $where_own . "
                                            and " . $where_time_own . "
                                            " . $where_sentiment_own . "
                                            and own_match.msg_status = 1
                                            and own_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and own_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            UNION
                                            select competitor_match.company_keyword_id company_keyword_id , competitor_match.msg_id msg_id , competitor_match.msg_time msg_time , competitor_match.match_type match_type , competitor_match.sourceid sourceid , competitor_match.post_user_id post_user_id , competitor_match.competitor_match_id match_id , competitor_match.competitor_match_sentiment match_sentiment
                                            from " . $table_match_competitor . "
                                            join competitor_key_match on competitor_match.competitor_match_id = competitor_key_match.competitor_match_id
                                            join keyword on competitor_key_match.keyword_id = keyword.keyword_id
                                            where competitor_match.client_id = '" . $this->CLIENT_ID . "'
                                            and keyword.primary_keyword = '1'
                                            and " . $where_competitor . "
                                            and " . $where_time_competitor . "
                                            " . $where_sentiment_competitor . "
                                            and competitor_match.msg_status = 1
                                            and competitor_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and competitor_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            ) as A
                                        group by A.msg_id
                                        order by A.msg_time DESC", $this->db->dbdriver, $post['post_rows'], PAGESIZE);
                    $rowsdata = $this->db->query($newsql)->result_array();
                } // end condition checking for keyword from filter

            } else {
                // start condition checking for keyword from filter
                if (isset($post['other_keyword'])) {
                    $rowsdata = $this->db->query("select A.company_keyword_id , A.msg_id , A.msg_time , A.match_type , A.sourceid , A.post_user_id , A.match_id , A.match_sentiment
                                                 from ( select own_match.company_keyword_id company_keyword_id , own_match.msg_id msg_id , own_match.msg_time msg_time , own_match.match_type match_type , own_match.sourceid sourceid , own_match.post_user_id post_user_id , own_match.own_match_id match_id , own_match.own_match_sentiment match_sentiment
                                                        from " . $table_match_own . "
                                                        where own_match.client_id = '" . $this->CLIENT_ID . "'
                                                        and " . $where_own . "
                                                        and " . $where_time_own . "
                                                        " . $where_sentiment_own . "
                                                        and own_match.msg_status = 1
                                                        and own_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                        and own_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                        UNION
                                                        select competitor_match.company_keyword_id company_keyword_id , competitor_match.msg_id msg_id , competitor_match.msg_time msg_time , competitor_match.match_type match_type , competitor_match.sourceid sourceid , competitor_match.post_user_id post_user_id , competitor_match.competitor_match_id match_id , competitor_match.competitor_match_sentiment match_sentiment
                                                        from " . $table_match_competitor . "
                                                        where competitor_match.client_id = '" . $this->CLIENT_ID . "'
                                                        and " . $where_competitor . "
                                                        and " . $where_time_competitor . "
                                                        " . $where_sentiment_competitor . "
                                                        and competitor_match.msg_status = 1
                                                        and competitor_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                        and competitor_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                                    ) as A
                                                group by A.msg_id
                                                order by A.msg_time DESC")->result_array();
                } else {
                    $newsql = get_page("select A.company_keyword_id , A.msg_id , A.msg_time , A.match_type , A.sourceid , A.post_user_id , A.match_id , A.match_sentiment
                                        from ( select own_match.company_keyword_id company_keyword_id , own_match.msg_id msg_id , own_match.msg_time msg_time , own_match.match_type match_type , own_match.sourceid sourceid , own_match.post_user_id post_user_id , own_match.own_match_id match_id , own_match.own_match_sentiment match_sentiment
                                            from " . $table_match_own . "
                                            where own_match.client_id = '" . $this->CLIENT_ID . "'
                                            and " . $where_own . "
                                            and " . $where_time_own . "
                                            " . $where_sentiment_own . "
                                            and own_match.msg_status = 1
                                            and own_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and own_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            UNION
                                            select competitor_match.company_keyword_id company_keyword_id , competitor_match.msg_id msg_id , competitor_match.msg_time msg_time , competitor_match.match_type match_type , competitor_match.sourceid sourceid , competitor_match.post_user_id post_user_id , competitor_match.competitor_match_id match_id , competitor_match.competitor_match_sentiment match_sentiment
                                            from " . $table_match_competitor . "
                                            where competitor_match.client_id = '" . $this->CLIENT_ID . "'
                                            and " . $where_competitor . "
                                            and " . $where_time_competitor . "
                                            " . $where_sentiment_competitor . "
                                            and competitor_match.msg_status = 1
                                            and competitor_match.post_user_id NOT IN (SELECT post_user_id FROM block_user WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            and competitor_match.msg_id NOT IN (SELECT msg_id FROM hide_post WHERE client_id='" . $this->CLIENT_ID . "')" . "
                                            ) as A
                                        group by A.msg_id
                                        order by A.msg_time DESC", $this->db->dbdriver, $post['post_rows'], PAGESIZE);
                    $rowsdata = $this->db->query($newsql)->result_array();
                } // end condition checking for keyword from filter

            } // end condition checking for Box tab

        } else {
            // $table_match = get_match_table("{$tb}_match", @$post["period"]);

            // 3months check
            if (@$post["period"] == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if ($obj['start'] >= $date_3m) {
                    $table_match = "{$tb}_match_3months {$tb}_match";
                } else {
                    $table_match = get_match_table("{$tb}_match", @$post["period"]);
                }
            } else {
                $table_match = get_match_table("{$tb}_match", @$post["period"]);
            }

            if (isset($post['period_type']) && $post['period_type'] == "before") {
                $this->master_model->get_where_before(@$post["period"], "{$tb}_match");
            } else {
                $this->master_model->get_where_current(@$post["period"], "{$tb}_match");
            }
            $this->get_where($post, "{$tb}");
            $this->master_model->get_where_sentiment($post, "{$tb}_match");

            $select = "{$tb}_match.company_keyword_id, {$tb}_match.msg_id, {$tb}_match.msg_time, {$tb}_match.match_type, {$tb}_match.sourceid, {$tb}_match.post_user_id, {$tb}_match.{$tb}_match_id AS match_id";
            if ($post['post_type'] == 'PriorityBox') {
                $sql = $this->db->select($select)
                    ->select("{$tb}_match.{$tb}_match_sentiment AS match_sentiment")
                    ->from("{$table_match}")
                    ->join("{$tb}_key_match", "{$tb}_match.{$tb}_match_id = {$tb}_key_match.{$tb}_match_id")
                    ->join("keyword", "{$tb}_key_match.keyword_id = keyword.keyword_id")
                    ->where("{$tb}_match.client_id", $this->CLIENT_ID)
                    ->where("keyword.primary_keyword", "1")
                    ->group_by("{$tb}_match.msg_id")
                    ->order_by("{$tb}_match.msg_time", "DESC")
                    ->query_string();
            } else {
                $sql = $this->db->select($select)
                    ->select("{$tb}_match.{$tb}_match_sentiment AS match_sentiment")
                    ->from("{$table_match}")
                    ->where("{$tb}_match.client_id", $this->CLIENT_ID)
                    ->group_by("{$tb}_match.msg_id")
                    ->order_by("{$tb}_match.msg_time", "DESC")
                    ->query_string();
            }

            $post['post_rows'] = isset($post['post_rows']) ? $post['post_rows'] : 1;
            if (isset($post['other_keyword'])) {
                $rowsdata = $this->db->query($sql)->result_array();
            } else {
                $newsql   = get_page($sql, $this->db->dbdriver, $post['post_rows'], PAGESIZE);
                $rowsdata = $this->db->query($newsql)->result_array();
            }
        }
        $result = array();

        $rsFeed   = $this->get_feed_type($rowsdata);

        $arrFeed    = $this->get_mongo_feed($rsFeed["arrFeed"], $post['other_keyword'], @$post["period"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"], $post['other_keyword'], @$post["period"]);

        foreach ($rowsdata as $k_row => $v_row) {
            $sourceid    = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            if ($v_row['match_type'] == "Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }

            $post_like = "";
            if (@$feed['post_total']) {
                $post_like = intval(@$feed['post_total']);
            } else {
                $post_like = intval(@$feed['post_like']);
            }

            array_push(
                $result,
                array(
                    "com_id"       => $v_row['company_keyword_id'],
                    "match_id"     => $v_row['match_id'],
                    "post_id"      => $v_row['msg_id'],
                    "post_user_id" => $v_row['post_user_id'],
                    "post_like"    => $post_like,
                    "post_share"   => @$feed['post_share'],
                    "post_comment" => @$feed['post_comment'],
                    "post_view"    => @$feed['post_view'],
                    "post_name"    => $feed['post_user'],
                    "post_link"    => @$feed['post_link'],
                    "post_detail"  => @$feed['post_detail'],
                    "post_time"    => $v_row['msg_time'],
                    "post_type"    => $media_full,
                    "sourceid"     => $v_row['sourceid'],
                    "sentiment"    => $v_row['match_sentiment']
                )
            );
        }

        return $result;
    }

    function add_feed($post = array(), $tb = "All")
    {
        if ($tb == "All") {
            $result = array();
            // $table_match_own = get_match_table("own_match", @$post["period"]);

            // 3months check
            if (@$post["period"] == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if ($obj['start'] >= $date_3m) {
                    $table_match_own = "own_match_3months own_match";
                } else {
                    $table_match_own = get_match_table("own_match", @$post["period"]);
                }
            } else {
                $table_match_own = get_match_table("own_match", @$post["period"]);
            }

            $where_own = $this->get_where_manual($post, "own");
            if (isset($post['period_type']) && $post['period_type'] == "before") {
                $where_time_own = $this->master_model->get_where_before_manual(@$post["period"]);
            } else {
                $where_time_own = $this->master_model->get_where_current_manual(@$post["period"]);
            }
            $where_sentiment_own = $this->master_model->get_where_sentiment_manual($post, "own_match");

            // $table_match_competitor = get_match_table("competitor_match", @$post["period"]);

            // 3months check
            if (@$post["period"] == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if ($obj['start'] >= $date_3m) {
                    $table_match_competitor = "competitor_match_3months competitor_match";
                } else {
                    $table_match_competitor = get_match_table("competitor_match", @$post["period"]);
                }
            } else {
                $table_match_competitor = get_match_table("competitor_match", @$post["period"]);
            }

            $where_competitor = $this->get_where_manual($post, "competitor");
            if (isset($post['period_type']) && $post['period_type'] == "before") {
                $where_time_competitor = $this->master_model->get_where_before_manual(@$post["period"], "competitor_match");
            } else {
                $where_time_competitor = $this->master_model->get_where_current_manual(@$post["period"], "competitor_match");
            }
            $where_sentiment_competitor = $this->master_model->get_where_sentiment_manual($post, "competitor_match");

            // start condition checking for Box tab
            if ($post['post_type'] == 'PriorityBox') {
                $rowsdata = $this->db->query("select A.company_keyword_id , A.msg_id , A.msg_time , A.match_type , A.sourceid , A.post_user_id , A.match_id , A.match_sentiment
                                              from ( select own_match.company_keyword_id company_keyword_id , own_match.msg_id msg_id , own_match.msg_time msg_time , own_match.match_type match_type , own_match.sourceid sourceid , own_match.post_user_id post_user_id , own_match.own_match_id match_id , own_match.own_match_sentiment match_sentiment
                                                    from " . $table_match_own . "
                                                    join own_key_match on own_match.own_match_id = own_key_match.own_match_id
                                                    join keyword on own_key_match.keyword_id = keyword.keyword_id
                                                    where own_match.client_id = '" . $this->CLIENT_ID . "'
                                                    and keyword.primary_keyword = '1'
                                                    and own_match.msg_time > '" . $post['last_time'] . "'
                                                    and " . $where_own . "
                                                    and " . $where_time_own . "
                                                    " . $where_sentiment_own . "
                                                    UNION
                                                    select competitor_match.company_keyword_id company_keyword_id , competitor_match.msg_id msg_id , competitor_match.msg_time msg_time , competitor_match.match_type match_type , competitor_match.sourceid sourceid , competitor_match.post_user_id post_user_id , competitor_match.competitor_match_id match_id , competitor_match.competitor_match_sentiment match_sentiment
                                                    from " . $table_match_competitor . "
                                                    join competitor_key_match on competitor_match.competitor_match_id = competitor_key_match.competitor_match_id
                                                    join keyword on competitor_key_match.keyword_id = keyword.keyword_id
                                                    where competitor_match.client_id = '" . $this->CLIENT_ID . "'
                                                    and keyword.primary_keyword = '1'
                                                    and competitor_match.msg_time > '" . $post['last_time'] . "'
                                                    and " . $where_competitor . "
                                                    and " . $where_time_competitor . "
                                                    " . $where_sentiment_competitor . "
                                                    ) as A
                                            group by A.msg_id
                                            order by A.msg_time DESC")->result_array();
            } else {
                $rowsdata = $this->db->query("select A.company_keyword_id , A.msg_id , A.msg_time , A.match_type , A.sourceid , A.post_user_id , A.match_id , A.match_sentiment
                                            from ( select own_match.company_keyword_id company_keyword_id , own_match.msg_id msg_id , own_match.msg_time msg_time , own_match.match_type match_type , own_match.sourceid sourceid , own_match.post_user_id post_user_id , own_match.own_match_id match_id , own_match.own_match_sentiment match_sentiment
                                                    from " . $table_match_own . "
                                                    where own_match.client_id = '" . $this->CLIENT_ID . "'
                                                    and own_match.msg_time > '" . $post['last_time'] . "'
                                                    and " . $where_own . "
                                                    and " . $where_time_own . "
                                                    " . $where_sentiment_own . "
                                                    UNION
                                                    select competitor_match.company_keyword_id company_keyword_id , competitor_match.msg_id msg_id , competitor_match.msg_time msg_time , competitor_match.match_type match_type , competitor_match.sourceid sourceid , competitor_match.post_user_id post_user_id , competitor_match.competitor_match_id match_id , competitor_match.competitor_match_sentiment match_sentiment
                                                    from " . $table_match_competitor . "
                                                    where competitor_match.client_id = '" . $this->CLIENT_ID . "'
                                                    and competitor_match.msg_time > '" . $post['last_time'] . "'
                                                    and " . $where_competitor . "
                                                    and " . $where_time_competitor . "
                                                    " . $where_sentiment_competitor . "
                                                    ) as A
                                            group by A.msg_id
                                            order by A.msg_time DESC")->result_array();
            }
        } else {
            // $table_match = get_match_table("{$tb}_match", @$post["period"]);

            // 3months check
            if (@$post["period"] == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if ($obj['start'] >= $date_3m) {
                    $table_match = "{$tb}_match_3months {$tb}_match";
                } else {
                    $table_match = get_match_table("{$tb}_match", @$post["period"]);
                }
            } else {
                $table_match = get_match_table("{$tb}_match", @$post["period"]);
            }

            $this->get_where($post, $tb);

            if (isset($post['period_type']) && $post['period_type'] == "before") {
                $this->master_model->get_where_before(@$post["period"], "{$tb}_match");
            } else {
                $this->master_model->get_where_current(@$post["period"], "{$tb}_match");
            }

            $this->master_model->get_where_sentiment($post, "{$tb}_match");

            $select = "{$tb}_match.company_keyword_id,{$tb}_match.msg_id,{$tb}_match.msg_time,{$tb}_match.match_type,{$tb}_match.sourceid,{$tb}_match.post_user_id";
            if ($post['post_type'] == 'PriorityBox') {
                $sql = $this->db->select($select)
                    ->select("{$tb}_match.{$tb}_match_sentiment AS match_sentiment")
                    ->from("{$table_match}")
                    ->join("{$tb}_key_match", "{$tb}_match.{$tb}_match_id = {$tb}_key_match.{$tb}_match_id")
                    ->join("keyword", "{$tb}_key_match.keyword_id = keyword.keyword_id")
                    ->where("{$tb}_match.client_id", $this->CLIENT_ID)
                    ->where("{$tb}_match.msg_time >", $post['last_time'])
                    ->where("keyword.primary_keyword", "1")
                    ->group_by("{$tb}_match.msg_id")
                    ->order_by("{$tb}_match.msg_time", "DESC")
                    ->query_string();
            } else {
                $sql = $this->db->select($select)
                    ->select("{$tb}_match.{$tb}_match_sentiment AS match_sentiment")
                    ->where("{$tb}_match.client_id", $this->CLIENT_ID)
                    ->where("{$tb}_match.msg_time >", $post['last_time'])
                    ->group_by("{$tb}_match.msg_id")
                    ->order_by("{$tb}_match.msg_time", "DESC")
                    ->from("{$table_match}")
                    ->query_string();
            }

            $rowsdata = $this->db->query($sql)->result_array();
        }

        $rsFeed   = $this->get_feed_type($rowsdata);

        $arrFeed    = $this->get_mongo_feed($rsFeed["arrFeed"], $post['other_keyword'], @$post["period"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"], $post['other_keyword'], @$post["period"]);
        $other_keyword = array();

        foreach ($rowsdata as $k_row => $v_row) {
            $sourceid    = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);

            if ($v_row['match_type'] == "Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }

            $post_like = "";
            if (@$feed['post_total']) {
                $post_like = intval(@$feed['post_total']);
            } else {
                $post_like = intval(@$feed['post_like']);
            }

            array_push(
                $result,
                array(
                    "com_id"       => $v_row['company_keyword_id'],
                    "post_id"      => $v_row['msg_id'],
                    "post_user_id" => $v_row['post_user_id'],
                    "post_like"    => $post_like,
                    "post_share"   => @$feed['post_share'],
                    "post_comment" => @$feed['post_comment'],
                    "post_view"    => @$feed['post_view'],
                    "post_name"    => $feed['post_user'],
                    "post_link"    => @$feed['post_link'],
                    "post_detail"  => @$feed['post_detail'],
                    "post_time"    => $v_row['msg_time'],
                    "post_type"    => $media_full,
                    "sourceid"     => $v_row['sourceid'],
                    "sentiment"    => $v_row['match_sentiment']
                )
            );
        }
        return $result;
    }

    function get_feed_type($rowsdata = array())
    {
        $arrFeed = array();
        $arrComment = array();
        foreach ($rowsdata as $k_row => $v_row) {
            if ($v_row['match_type'] == "Feed") {
                array_push($arrFeed, $v_row['msg_id']);
            } else {
                array_push($arrComment, $v_row['msg_id']);
            }
        }
        return array("arrFeed" => $arrFeed, "arrComment" => $arrComment);
    }

    function get_mongo_feed($msg_id = array(), $other_keyword = array(), $period = "")
    {
        $result = array();
        if (count($msg_id) > 0) {
            $mongo = new MongoClient(MONGO_CONNECTION);
            $mongodb = $mongo->blue_eye;

            if ($period == "Today") {
                $collection = $mongodb->selectCollection("DairyFeed");
            } else {
                $collection = $mongodb->selectCollection("Feed");
            }

            if (isset($other_keyword) && ($other_keyword != null)) {
                $and_other_keyword = array();
                foreach ($other_keyword as $key => $val) {
                    array_push($and_other_keyword, array('feedcontent' => new MongoRegex("/" . $val . "/i")));
                }
                $query = array('_id' => array('$in' => $msg_id), '$and' => $and_other_keyword);
            } else {
                $query = array('_id' => array('$in' => $msg_id));
            }
            $cursor = $collection->find($query);
            $cursor->timeout(-1);

            # {'total':0, 'like':0, 'heart':0, 'wow':0, 'laugh':0, 'sad':0, 'angry':0, 'love':0}

            foreach ($cursor as $k_row => $v_row) {
                $result[$v_row['_id']] = array(
                    "post_user" => @$v_row['feeduser'],
                    "post_link" => @$v_row['feedlink'],
                    "post_detail" => @$v_row['feedcontent'],
                    "post_share" => @$v_row['feedshares'],
                    "post_comment" => @$v_row['feedcomments'],
                    "post_like" => @$v_row['feedlikes'],
                    "post_total" => @$v_row['feedlikes']['total'],
                    "post_like_array" => @$v_row['feedlikes']['like'],
                    "post_love_array" => @$v_row['feedlikes']['heart'],
                    "post_wow_array" => @$v_row['feedlikes']['wow'],
                    "post_laugh_array" => @$v_row['feedlikes']['laugh'],
                    "post_sad_array" => @$v_row['feedlikes']['sad'],
                    "post_angry_array" => @$v_row['feedlikes']['angry'],
                    "post_care_array" => @$v_row['feedlikes']['love'],
                    "post_view" => @$v_row['feedviews']
                );
            }
            $mongo->close();
        }
        return $result;
    }

    function get_mongo_comment($msg_id = array(), $other_keyword = array(), $period = "")
    {
        $result = array();
        if (count($msg_id) > 0) {
            $mongo = new MongoClient(MONGO_CONNECTION);
            $mongodb = $mongo->blue_eye;

            if ($period == "Today") {
                $collection = $mongodb->selectCollection("DairyComment");
            } else {
                $collection = $mongodb->selectCollection("Comment");
            }

            if (isset($other_keyword) && ($other_keyword != null)) {
                $and_other_keyword = array();
                foreach ($other_keyword as $key => $val) {
                    array_push($and_other_keyword, array('feedcontent' => new MongoRegex("/" . $val . "/i")));
                }
                $query = array('_id' => array('$in' => $msg_id), '$and' => $and_other_keyword);
            } else {
                $query = array('_id' => array('$in' => $msg_id));
            }
            $cursor = $collection->find($query);
            $cursor->timeout(-1);

            foreach ($cursor as $k_row => $v_row) {
                $result[$v_row['_id']] = array(
                    "post_user" => @$v_row['commentuser'],
                    "post_link" => @$v_row['commentlink'],
                    "post_detail" => @$v_row['commentcontent']
                );
            }
            $mongo->close();
        }
        return $result;
    }

    function get_post_detail($post_id = 0, $com_id = 0)
    {
        $arrFeed = array();
        $result = array();

        $rowsdata = $this->get_rows_match($post_id, $com_id);
        $rsFeed = $this->get_feed_type($rowsdata);
        $arrFeed = $this->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"]);

        foreach ($rowsdata as $k_row => $v_row) {
            $sourceid = $v_row['sourceid'];
            $media_full  = get_soruce_full($sourceid);
            $media_short = get_soruce_short($sourceid);
            if ($v_row['match_type'] == "Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }

            $post_like = "";
            if (@$feed['post_total']) {
                $post_like = intval(@$feed['post_total']);
            } else {
                $post_like = intval(@$feed['post_like']);
            }

            $sentiment = $v_row['match_sentiment'];

            $result = array(
                "com_id"       => $v_row['company_keyword_id'],
                "post_id"      => $v_row['msg_id'],
                "post_user_id" => $v_row['post_user_id'],
                "post_name"    => $feed['post_user'],
                "post_link"    => @$feed['post_link'],
                "post_detail"  => @$feed['post_detail'],
                "post_time"    => $v_row['msg_time'],
                "post_like"    => $post_like,
                "post_share"   => @$feed['post_share'],
                "post_comment" => @$feed['post_comment'],
                "post_type"    => $media_full,
                "match_type"   => $v_row['match_type'],
                "sourceid"     => $v_row['sourceid'],
                "sentiment"    => $sentiment
            );
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
        $skip  = ($post_rows - 1) * PAGESIZE;

        $rec = $this->get_rows_match($post_id, $com_id);

        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;

        if (isset($rec[0]['match_type']) && $rec[0]['match_type'] == "Feed") {

            $collection = $mongodb->selectCollection("Comment");
            $query = array('feedid' => $post_id);
            $field = array("_id");
            $cursor = $collection->find($query, $field);
            $cursor->timeout(-1);

            foreach ($cursor as $k_row => $v_row) {
                $rec = $this->get_rows_match($v_row['_id'], $com_id);
                if (isset($rec[0]['msg_id'])) array_push($msg_id, $v_row['_id']);
            }
            if (count($msg_id) > 0) {
                $collection = $mongodb->selectCollection("Comment");
                $query2 = array('feedid' => $post_id, '_id' => array('$in' => $msg_id));
                $cursor2 = $collection
                    ->find($query2)
                    ->sort(array("feedtimepost" => -1))
                    ->limit(PAGESIZE)
                    ->skip($skip);
                $cursor2->timeout(-1);

                foreach ($cursor2 as $k2_row => $v2_row) {
                    array_push($rowsdata, array(
                        "post_id" => $v2_row["_id"],
                        "post_name" => $v2_row["commentuser"],
                        "post_detail" => $v2_row["commentcontent"],
                        "post_time" => $v2_row["commenttimepost"],
                        "post_type" => "Comment"
                    ));
                }
            }
        } else {

            $collection = $mongodb->selectCollection("Comment");
            $query = array('_id' => $post_id);
            $cursor = $collection->find($query);
            $cursor->timeout(-1);

            foreach ($cursor as $k_row => $v_row) {
                $collection = $mongodb->selectCollection("Feed");
                $query2 = array('_id' => $v_row['feedid']);
                $cursor2 = $collection
                    ->find($query2)
                    ->limit(1)
                    ->skip($skip);
                $cursor2->timeout(-1);

                foreach ($cursor2 as $k2_row => $v2_row) {
                    array_push($rowsdata, array(
                        "post_id" => $v2_row["_id"],
                        "post_name" => $v2_row["feeduser"],
                        "post_detail" => $v2_row["feedcontent"],
                        "post_time" => $v2_row["feedtimepost"],
                        "post_type" => "Feed"
                    ));
                }
            }
        }

        $mongo->close();

        return $rowsdata;
    }

    function insert_filter_keyword($post = array())
    {
        $this->db->where("client_id", $this->CLIENT_ID);
        $this->db->where("name", "realtime_keyword");
        $this->db->delete("client_meta");

        $this->db->where("client_id", $this->CLIENT_ID);
        $this->db->where("name", "realtime_group_keyword");
        $this->db->delete("client_meta");

        if (isset($post['keyword_id'])) {
            $save = array();
            $save["client_id"] = $this->CLIENT_ID;
            $save["name"]      = "realtime_keyword";
            $save["value"]     = implode(",", $post['keyword_id']);
            $this->db->insert("client_meta", $save);
        }

        if (isset($post['group_keyword_id'])) {
            $save = array();
            $save["client_id"] = $this->CLIENT_ID;
            $save["name"]      = "realtime_group_keyword";
            $save["value"]     = implode(",", $post['group_keyword_id']);
            $this->db->insert("client_meta", $save);
        }
    }

    function delete_post($post_id = 0)
    {
        $where = array("client_id" => $this->CLIENT_ID, "msg_id" => $post_id);
        $save  = array("msg_status" => '0');

        $this->db->update("own_match", $save, $where);
        $this->db->update("competitor_match", $save, $where);

        $this->db->update("own_match_daily", $save, $where);
        $this->db->update("competitor_match_daily", $save, $where);

        $this->db->update("own_match_3months", $save, $where);
        $this->db->update("competitor_match_3months", $save, $where);
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
        $this->db->insert("block_user", $save);

        return $own_match["post_user"];
    }

    function hide_post($post_id = 0)
    {
        $query = $this->db
            ->select("*")
            ->where("client_id", $this->CLIENT_ID)->where("msg_id", $post_id)
            ->get("hide_post")
            ->first_row('array');

        if ($query == null) {
            $save = array();
            $save['client_id'] = $this->CLIENT_ID;
            $save['msg_id']      = $post_id;
            $this->db->insert("hide_post", $save);
        }
    }

    function get_rows_match($post_id = 0, $com_id = 0)
    {
        $rowsdata = array();
        $com = $this->setting_model->get_company($com_id);
        $period = get_cookie("META_PERIOD");
        if (@$com["company_keyword_type"] == "Competitor") {
            // $table_match = get_match_table("competitor_match", $period);

            // 3months check
            if ($period == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if ($obj['start'] >= $date_3m) {
                    $table_match = "competitor_match_3months competitor_match";
                } else {
                    $table_match = get_match_table("competitor_match", $period);
                }
            } else {
                $table_match = get_match_table("competitor_match", $period);
            }

            $rowsdata = $this->db
                ->select("competitor_match.*")
                ->select("competitor_match.competitor_match_sentiment AS match_sentiment")
                ->where("competitor_match.client_id", $this->CLIENT_ID)
                ->where("competitor_match.msg_id", $post_id)
                ->where("competitor_match.company_keyword_id", $com_id)
                ->get("{$table_match}")
                ->result_array();
        } else {
            // $table_match = get_match_table("own_match", $period);

            // 3months check
            if ($period == "Custom") {
                $date = date("Y-m-d");
                $date_3m = date("Y-m-d", strtotime($date . ' -90 days'));
                $obj = get_custom_date($this->custom_date);
                if ($obj['start'] >= $date_3m) {
                    $table_match = "own_match_3months own_match";
                } else {
                    $table_match = get_match_table("own_match", $period);
                }
            } else {
                $table_match = get_match_table("own_match", $period);
            }

            $rowsdata = $this->db
                ->select("own_match.*")
                ->select("own_match.own_match_sentiment AS match_sentiment")
                ->where("own_match.client_id", $this->CLIENT_ID)
                ->where("own_match.msg_id", $post_id)
                ->where("own_match.company_keyword_id", $com_id)
                ->get("{$table_match}")
                ->result_array();
        }
        return $rowsdata;
    }

    function get_competitor($post = array())
    {
        $rowsdata = array();
        $rowsdata = $this->db
            ->where("client_id", $this->CLIENT_ID)
            ->get("company_keyword")
            ->result_array();
        return $rowsdata;
    }
}
?>