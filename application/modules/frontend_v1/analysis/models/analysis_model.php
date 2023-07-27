<?php
class Analysis_model extends CI_Model {

	var $CLIENT_ID;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
    }

    function get_graph_id($graph_id = 0)
    {
        $rowsdata = $this->db
                    ->select("client_graph.*")
                    ->where("client_graph.graph_id",$graph_id)
                    ->where("client_graph.client_id",$this->CLIENT_ID)
                    ->get("client_graph")
                    ->first_row('array');

        return $rowsdata;
    }

    function get_client_graph()
    {
        $rowsdata = $this->db
                    ->select("client_graph.*")
                    ->where("client_graph.client_id",$this->CLIENT_ID)
                    ->order_by("client_graph.graph_id","ASC")
                    ->get("client_graph")
                    ->result_array();

        return $rowsdata;
    }

    function insert_graph($post = array())
    {
        $save = array();
        $save["client_id"]    = $this->CLIENT_ID;
        $save["graph_name"]   = $post['graph_name'];
        $save["graph_x"]      = $post['graph_x'];
        $save["graph_y"]      = $post['graph_y'];
        $save["graph_type"]   = $post['graph_type'];
        $save["created_date"] = date("Y-m-d H:i:s");

        $this->db->insert("client_graph",$save);
        $graph_id = $this->db->insert_id("client_graph");

        return $graph_id;
    }

    function update_graph($post = array())
    {
        $graph_id = $post['graph_id'];

        $save = array();
        $save["graph_name"]   = $post['graph_name'];
        $save["graph_x"]      = $post['graph_x'];
        $save["graph_y"]      = $post['graph_y'];
        $save["graph_type"]   = $post['graph_type'];

        $this->db->where("graph_id",$graph_id);
        $this->db->update("client_graph",$save);

        return $graph_id;
    }

    function delete_graph($graph_id = 0)
    {
        $rec = $this->db
            ->select("graph_name")
            ->where("graph_id",$graph_id)
            ->get("client_graph")
            ->first_row("array");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("graph_id",$graph_id);
        $this->db->delete("client_graph");

        return $rec['graph_name'];
    }

    function check_graph_name($post = array())
    {
        $num_rows = 0;
        $check = false;
        if(@$post['graph_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_graph_id($post['graph_id']);
            if($post['graph_name']!=@$rec['graph_name']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_graph.graph_name",$post['graph_name'])
                ->where("client_graph.client_id",$this->CLIENT_ID)
                ->get("client_graph")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_graph_type($post = array())
    {
        $num_rows = 0;
        $check = false;
        if(@$post['graph_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_graph_id($post['graph_id']);
            if($post['graph_y']!=@$rec['graph_y']
                || $post['graph_x']!=@$rec['graph_x']
                || $post['graph_type']!=@$rec['graph_type']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_graph.graph_y",$post['graph_y'])
                ->where("client_graph.graph_x",$post['graph_x'])
                ->where("client_graph.graph_type",$post['graph_type'])
                ->where("client_graph.client_id",$this->CLIENT_ID)
                ->get("client_graph")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function getChartPie($post = array())
    {
        $result = array("data"=>array());
        @$post['graph_y'] = "Mention";
				$obj_graph_x = @$post['graph_x'];
					$obj_gx = array("MediaType", "Company", "GroupKeyword");
					if(!in_array($obj_graph_x,$obj_gx) ){
						$gtop = "";
						$graph_x = "";
						$str = explode("KeywordTop",$obj_graph_x);
						$graph_x = str_replace($str,'',$obj_graph_x);
						@$post['graph_x'] = $graph_x;
						$gTop = $str[1];
						// print_r (@$post['graph_x']);
						// print_r ($str[1]);
						// die();
					}

        switch (@$post['graph_x']) {
            case 'MediaType':
                $rowsx = $this->get_media_type_x($post);
                $rowsy = $this->get_match_data_y($post);
                break;
            case 'Company':
                $rowsx = $this->get_company_x($post);
                $rowsy = $this->get_match_data_y($post);
                break;
            case 'GroupKeyword':
                $rowsx = $this->get_group_keyword_x($post);
                $rowsy = $this->get_match_data_y($post);
                break;
            case 'KeywordTop':
                $rowsx = $this->get_top_keyword_x($post,$gTop);
                $rowsy = $this->get_match_data_y($post);
                break;

            default:
                $rowsx = array();
                break;
        }


        foreach($rowsx as $k_row=>$v_row) {

            switch (@$post['graph_x']) {
                case 'MediaType':
                    $name = get_soruce_short($v_row['sourceid']);
                    $post['sourceid'] = $v_row['sourceid'];
                    break;
                case 'Company':
                    $name = $v_row['company_keyword_name'];
                    $post['company_keyword_id'] = $v_row['company_keyword_id'];
                    break;
                case 'GroupKeyword':
                    $name = $v_row['group_keyword_name'];
                    $post['group_keyword_id']   = $v_row['group_keyword_id'];
                    break;
                case 'KeywordTop':
                    $name = $v_row['keyword_name'];
                    $post['keyword_id']   = $v_row['keyword_id'];
                    break;
            }

            $tb_match = (@$v_row['company_keyword_type']!="Competitor") ? "own_match" : "competitor_match";

            $value = 0;
            foreach($rowsy as $k2_row=>$v2_row) {
                $rec = $this->get_match_data_y($post,$tb_match);
                if(isset($rec[0]['countTotal'])) {
                    switch (@$post['graph_y']) {
                        case 'Sentiment':
                            $value = ($rec[0]["countTotal"]>0) ? round($rec[0]["sumSentiment"]/$rec[0]["countTotal"],2) : 0;
                            break;
                        case 'Mention':
                            $value = floatval($rec[0]['countTotal']);
                            break;
                    }
                }
            }
            array_push($result["data"],array("name"=>$name,"y"=>$value));
        }

        $result['name'] = @$post['graph_x'];
        $result['colorByPoint'] = true;

        return  array($result);
    }

    function getChartBar($post = array())
    {
        $result = array();
        $series = array();
        $categories = array();
				$obj_graph_x = @$post['graph_x'];
					$obj_gx = array("MediaType", "Company", "GroupKeyword");
					if(!in_array($obj_graph_x,$obj_gx) ){
						$gtop = "";
						$graph_x = "";
						$str = explode("KeywordTop",$obj_graph_x);
						$graph_x = str_replace($str,'',$obj_graph_x);
						@$post['graph_x'] = $graph_x;
						$gTop = $str[1];
						// print_r (@$post['graph_x']);
						// print_r ($str[1]);
						// die();
					}
        switch (@$post['graph_x']) {
            case 'MediaType':
                $rowsx = $this->get_media_type_x($post);
                $rowsy = $this->get_match_data_y($post);
                break;
            case 'Company':
                $rowsx = $this->get_company_x($post);
                $rowsy = $this->get_match_data_y($post);
                break;
            case 'GroupKeyword':
                $rowsx = $this->get_group_keyword_x($post);
                $rowsy = $this->get_match_data_y($post);
                break;
						case 'KeywordTop':
		            $rowsx = $this->get_top_keyword_x($post,$gTop);
		            $rowsy = $this->get_match_data_y($post);
		            break;
            default:
                $rowsx = array();
                break;
        }

        foreach($rowsy as $k_row=>$v_row) {
            if($v_row['time_type']=="HOUR") {
                array_push($categories,$v_row['new_time']." Hrs.");
            } else if($v_row['time_type']=="DATE") {
                array_push($categories,getDateformat($v_row['new_time']));
            } else if($v_row['time_type']=="WEEK") {
                array_push($categories,"W".$v_row['new_time']);
            } else if($v_row['time_type']=="MONTH") {
                array_push($categories,get_graph_month($v_row['new_time']));
            } else {
                array_push($categories,$v_row['new_time']);
            }
        }

        foreach($rowsx as $k_row=>$v_row) {

            switch (@$post['graph_x']) {
                case 'MediaType':
                    $name = get_soruce_short($v_row['sourceid']);
                    $post['sourceid'] = $v_row['sourceid'];
                    break;
                case 'Company':
                    $name = $v_row['company_keyword_name'];
                    $post['company_keyword_id'] = $v_row['company_keyword_id'];
                    break;
                case 'GroupKeyword':
                    $name = $v_row['group_keyword_name'];
                    $post['group_keyword_id']   = $v_row['group_keyword_id'];
                    break;
								case 'KeywordTop':
		                $name = $v_row['keyword_name'];
		                $post['keyword_id']   = $v_row['keyword_id'];
		                break;

            }

            $data = array();
            $tb_match = (@$v_row['company_keyword_type']!="Competitor") ? "own_match" : "competitor_match";

            foreach($rowsy as $k2_row=>$v2_row) {
                $value = 0;

                $post['new_time'] = $v2_row['new_time'];
                $rec = $this->get_match_data_y($post,$tb_match);

                if(isset($rec[0]['countTotal'])) {
                    switch (@$post['graph_y']) {
                        case 'Sentiment':
                            $value = ($rec[0]["countTotal"]>0) ? round($rec[0]["sumSentiment"]/$rec[0]["countTotal"],2) : 0;
                            break;
                        case 'Mention':
                            $value = floatval($rec[0]['countTotal']);
                            break;
                    }
                }

                array_push($data,$value);
            }
            array_push($series,array("name"=>$name,"data"=>$data));
        }

        $result['series'] = $series;
        $result['categories'] = $categories;
        return $result;
    }

    function getChartLine($post = array())
    {
        return $this->getChartBar($post);
    }

    function get_match_data_y($post = array(),$tb_match = "own_match")
    {
			// print_r($post);
			// die();
        $period = $post['period'];

        if(in_array(@$post['graph_type'],array("Bar","Line"))) {
            if($period=="Today") {
                $this->db->select("HOUR({$tb_match}.msg_time) AS new_time,'HOUR' AS time_type",false);
                if(@$post['new_time']!="") $this->db->where("HOUR({$tb_match}.msg_time)",$post['new_time']);
            } else if($period=="1W") {
                $this->db->select("DATE({$tb_match}.msg_time) AS new_time,'DATE' AS time_type",false);
                if(@$post['new_time']!="") $this->db->where("DATE({$tb_match}.msg_time)",$post['new_time']);
            } else if($period=="1M") {
                $this->db->select("WEEK({$tb_match}.msg_time) AS new_time,'WEEK' AS time_type",false);
                if(@$post['new_time']!="") $this->db->where("WEEK({$tb_match}.msg_time)",$post['new_time']);
            } else if($period=="3M") {
                $this->db->select("MONTH({$tb_match}.msg_time) AS new_time,'MONTH' AS time_type",false);
                if(@$post['new_time']!="") $this->db->where("MONTH({$tb_match}.msg_time)",$post['new_time']);
            } else {/////
                $this->db->select("DATE({$tb_match}.msg_time) AS new_time,'DATE' AS time_type",false);
                if(@$post['new_time']!="") $this->db->where("DATE({$tb_match}.msg_time)",$post['new_time']);
            }
            $this->db->group_by("new_time,time_type");
        }// input rebel

        if(@$post['sourceid']!="")           $this->db->where("{$tb_match}.sourceid",$post['sourceid']);
        if(@$post['company_keyword_id']!="") $this->db->where("{$tb_match}.company_keyword_id",$post['company_keyword_id']);

        if(@$post['group_keyword_id']!="")   {
            $tb_key_match  = ($tb_match=="own_match") ? "own_key_match" : "competitor_key_match";
            $this->db->join("{$tb_key_match}","{$tb_key_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id");
            $this->db->join("keyword","{$tb_key_match}.keyword_id = keyword.keyword_id");
            $this->db->where("keyword.group_keyword_id",$post['group_keyword_id']);
        }
				if(@$post['keyword_id']!="")  {
						$tb_key_match = "own_key_match";
		        $this->db->join("{$tb_key_match}","{$tb_key_match}.{$tb_match}_id = {$tb_match}.{$tb_match}_id");
						$this->db->join("keyword","{$tb_key_match}.keyword_id = keyword.keyword_id");
						$this->db->where("{$tb_key_match}.keyword_id",$post['keyword_id']);
				}
				// print_r($this->db);
				// die();
        $this->master_model->get_where_current(@$post["period"],$tb_match);
        $rowsdata = $this->db
                    ->select("SUM(CASE WHEN {$tb_match}.{$tb_match}_sentiment > 0 THEN 1 ELSE 0 END) AS countPositive")
                    ->select("SUM(CASE WHEN {$tb_match}.{$tb_match}_sentiment < 0 THEN 1 ELSE 0 END) AS countNegative")
                    ->select("SUM(CASE WHEN {$tb_match}.{$tb_match}_sentiment = 0 THEN 1 ELSE 0 END) AS countNormal")
                    ->select("SUM({$tb_match}.{$tb_match}_sentiment) AS sumSentiment")
                    ->select("count(*) AS countTotal")
                    ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                    ->get("{$tb_match}")
                    ->result_array();
        return $rowsdata;
    }

    function get_media_type_x($post = array())
    {
        $this->master_model->get_where_current(@$post["period"],"own_match");
        $rowsdata = $this->db
                    ->select("own_match.sourceid")
                    ->group_by("own_match.sourceid")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    ->get("own_match")
                    ->result_array();
        return $rowsdata;
    }

    function get_company_x($post = array())
    {
        $result = array();
        $rowsdata = $this->db
                    ->select("company_keyword.company_keyword_id,company_keyword.company_keyword_name,company_keyword.company_keyword_type")
                    ->where("company_keyword.client_id",$this->CLIENT_ID)
                    ->get("company_keyword")
                    ->result_array();
        return $rowsdata;
    }

    function get_group_keyword_x($post = array())
    {
        $result = array();
        $rowsdata = $this->db
                    ->select("company_keyword.company_keyword_id,company_keyword.company_keyword_name,company_keyword.company_keyword_type")
                    ->select("group_keyword.group_keyword_id,group_keyword.group_keyword_name")
                    ->where("company_keyword.client_id",$this->CLIENT_ID)
                    ->join("group_keyword","group_keyword.company_keyword_id = company_keyword.company_keyword_id")
                    ->get("company_keyword")
                    ->result_array();
        return $rowsdata;
    }

		function get_top_keyword_x($post = array(),$toplimit)
    {
        $result = array();
        $rowsdata = $this->db
                    ->select("keyword.keyword_id,keyword.keyword_name")
                    ->select("COUNT(own_key_match.own_key_match_id) AS TotalMen")
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->join("own_key_match","own_key_match.keyword_id = keyword.keyword_id")
										->order_by("TotalMen","DESC")
										->group_by("keyword.keyword_id")
										->limit($toplimit)
										->get("keyword")
                    ->result_array();
				// print_r($rowsdata);
				// print_r($this->db->last_query());
				// die();
        return $rowsdata;
    }

    function get_table_list($post = array(),&$total_rows)
    {
        $result = array();

        $lenght = $post["length"];
        $page   = ($post['start'] / $lenght) + 1;
        $sort   = $post['order']['0']['column'];
        $order  = $post['order']['0']['dir'];
        $column = array("msg_id");

        $this->master_model->get_where_current(@$post["period"],"own_match");
        $sql = $this->db
                    ->select("own_match.*")
                    ->where("own_match.client_id",$this->CLIENT_ID)
                    //->order_by($column[$sort],$order)
                    ->order_by("msg_time","DESC")
                    ->from("own_match")
                    ->query_string();

        $total_rows = $this->db->query($sql)->num_rows();
        $newsql = get_page($sql,$this->db->dbdriver,$page,$lenght);
        $rowsdata = $this->db->query($newsql)->result_array();

        $rsFeed     = $this->realtime_model->get_feed_type($rowsdata);
        $arrFeed    = $this->realtime_model->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->realtime_model->get_mongo_comment($rsFeed["arrComment"]);

        foreach($rowsdata as $k_row=>$v_row) {

            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }

            $media_name = get_soruce_short($v_row['sourceid']);
            $post_detail = mb_substr(@$feed['post_detail'],0,200);
            $post_detail .= (mb_strlen(@$feed['post_detail'])>200) ? "..." : "";
            $msg_time   = getDatetimeformat($v_row['msg_time']);
            array_push($result,array(
                $v_row['msg_id'],
                $media_name,
                $v_row['match_type'],
                @$feed['post_link'],
                $v_row['post_user'],
                $post_detail,
                intval(@$v_row['post_share']),
                intval(@$feed['post_like']),
                $msg_time,
                $v_row['own_match_sentiment']
            ));
        }

        return $result;
    }

    function get_export($post = array())
    {
        $result = array();
        $company_keyword_id = $post['company_keyword_id'];
        $com = $this->setting_model->get_company($company_keyword_id);

        $company_keyword_type = @$com['company_keyword_type'];

        $tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";

        $this->master_model->get_where_current(@$post["period"],$tb_match);
        $sql = $this->db
                    ->select("{$tb_match}.*")
                    ->where("{$tb_match}.client_id",$this->CLIENT_ID)
                    ->where("{$tb_match}.company_keyword_id",$company_keyword_id)
                    ->order_by("msg_time","DESC")
                    ->from("{$tb_match}")
                    ->query_string();

        $rowsdata = $this->db->query($sql)->result_array();

        $rsFeed     = $this->realtime_model->get_feed_type($rowsdata);
        $arrFeed    = $this->realtime_model->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->realtime_model->get_mongo_comment($rsFeed["arrComment"]);


        foreach($rowsdata as $k_row=>$v_row) {

            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }

            $keyword = $this->get_keyword(@$v_row['own_match_id']);

            $media_name = get_soruce_short($v_row['sourceid']);
            $msg_time   = getDatetimeformat($v_row['msg_time']);
            array_push($result,array(
                $v_row['msg_id'],
                $media_name,
                $v_row['match_type'],
                @$feed['post_link'],
                $v_row['post_user'],
                @$feed['post_detail'],
                intval(@$v_row['post_share']),
                intval(@$feed['post_like']),
                $msg_time,
                $v_row["{$tb_match}_sentiment"],
                $keyword
            ));
        }

        return $result;
    }

    function get_keyword($own_match_id = 0)
    {
        $result = "";
        $rowsdata = $this->db
            ->select("keyword.keyword_name")
            ->group_by("keyword.keyword_name")
            ->where("own_key_match.client_id",$this->CLIENT_ID)
            ->where("own_key_match.own_match_id",$own_match_id)
            ->join("keyword","own_key_match.keyword_id = keyword.keyword_id")
            ->get("own_key_match")
            ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            $result .= $v_row['keyword_name'].",";
        }

        return trim($result,",");
    }


}
?>
