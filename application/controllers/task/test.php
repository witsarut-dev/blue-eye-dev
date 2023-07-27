<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MX_Controller {


    var $item_run_time = 10000;
    var $tb_match = "own_match";

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);

        $this->load->helper("common");
	}

    function index($type = "1") 
    {
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
 
        $collection = $mongodb->selectCollection("DairyFeed");

        $query =  array(
                    '$and' => array(
                        array("feedcontent"=>new MongoRegex("/สุวรรณภูมิ/"))
                    ),
                    '$or' => array(
                        array("feedtimestamp"=>new MongoRegex("/2017-05-19 10/")),
                        array("feedtimestamp"=>new MongoRegex("/2017-05-10 11/"))
                    )
                );

        $cursor = $collection->find($query);

        foreach($cursor as $val) {
            echo($val['feedcontent']." ".$val['feedtimestamp'])."<br />";
        }
    }


    function display() 
    {
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
 
        $collection = $mongodb->selectCollection("Comment");

        $query =  array("commentcontent"=>new MongoRegex("/คนดี/"));

        $cursor = $collection->find($query);

        //var_dump($cursor);

        foreach($cursor as $val) {
            echo($val['commentcontent']." ".$val['commenttimestamp'])."<br />";
        }
    }

    function sentiment()
    {
        $text =  "ที่เมืองไทยถ้าเปิดร้านขายยาบ้า คนจะมาอุดหนุนเยอะกว่าร้านขายหนังสือครับ.";
        $sentiment = get_sentiment_api($text);
        echo $sentiment;

        //echo  file_get_contents("http://128.199.201.187/SSenseV2/Analyze?q=".$text);
    }


    function run_time($type = "1")
    {   
        log_run_time("start=");
        $company_keyword_type = ($type=="1") ? "Company" : "Competitor";
        $tb_match  = ($company_keyword_type=="Company") ? "own_match" : "competitor_match";

        $rowsdata = $this->get_sentiment($tb_match);

        $rsFeed  = $this->get_feed_type($rowsdata);
        $arrFeed = $this->get_mongo_feed($rsFeed["arrFeed"]);
        $arrComment = $this->get_mongo_comment($rsFeed["arrComment"]);


            echo var_dump($arrFeed);
            die;


        foreach($rowsdata as $k_row=>$v_row) {

            if($v_row['match_type']=="Feed") {
                $feed = @$arrFeed[$v_row['msg_id']];
            } else {
                $feed = @$arrComment[$v_row['msg_id']];
            }

            $ch = curl_init();

            $text = str_replace('%','',@$feed['post_detail']);

            $key  = S_SENSE_KEY;

            // curl_setopt($ch, CURLOPT_URL,"http://sansarn.com/api/ssense-v2.php");
            // curl_setopt($ch, CURLOPT_POST, 1);
            // curl_setopt($ch, CURLOPT_POSTFIELDS,"text={$text}&key={$key}");
            curl_setopt($ch, CURLOPT_URL,"http://128.199.201.187/SSenseV2/Analyze");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,"q={$text}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close ($ch);

            $obj = json_decode($output,true);

            if(isset($obj['sentiment']['polarity'])) {
                if(@$obj['sentiment']['polarity']=="positive") {
                    $sentiment = floatval($obj['sentiment']['score']);
                } else if(@$obj['sentiment']['polarity']=="negative") {
                    $sentiment = floatval("-".$obj['sentiment']['score']);
                } else {
                    $sentiment = 0;
                }

                $save = array();
                $save["{$tb_match}_sentiment"] = $sentiment;
                //$this->db->where("{$tb_match}_id",$v_row["match_id"]);
                $this->db->where("msg_id",$v_row["msg_id"]);
                $this->db->update("{$tb_match}",$save);
            }
        }

        log_run_time("end=");
    }

    function get_sentiment($tb_match = "own_match")
    {
        $rowsdata = $this->db
                    ->select("{$tb_match}_id AS match_id,match_type,msg_id")
                    ->where("{$tb_match}_sentiment IS NULL",null,false)
                    ->limit($this->item_run_time)
                    //->order_by("msg_time","DESC")
                    ->get("{$tb_match}")
                    ->result_array();

        return $rowsdata;
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

            $fields = array("_id"=>1,"sourceid"=>1,"feeduser"=>1,"feedctimepost"=>1);
            $cursor = $collection->find($query,$fields);
           
            foreach($cursor as $k_row=>$v_row) {
                $result[$v_row['_id']] = array("post_user"=>@$v_row['feeduser'],"post_link"=>@$v_row['feedlink'],"post_detail"=>@$v_row['feedcontent']);
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
            
            $fields = array("_id"=>1,"sourceid"=>1,"commentuser"=>1,"commenttimepost"=>1);
            $cursor = $collection->find($query,$fields);
           
            foreach($cursor as $k_row=>$v_row) {
                $result[$v_row['_id']] = array("post_user"=>@$v_row['commentuser'],"post_link"=>@$v_row['commentlink'],"post_detail"=>@$v_row['commentcontent']);
            }
            $mongo->close();
        }
        return $result;
    }

    function get_sentiment_ida_api()
    {
        $_id = "0231";
        print_r($_id);
        $text = "เบียร์รสชาติห่วยแตกมาก";
        $timeout = '';

        $key  = 'ACC9R9jwCvjAVYMgKFqv2nnMkJ2TVrJNdNLdeVx63caxx6o3XuSeMzmZUqvFcpf6FCG3q2aVGhx9xSAivcDMTrds5EXPeB8AbUCMkLjIIs';
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://167.99.65.95/api/v1/sentiment/Overview?token='.$key.'&_id='.$_id.'&content='.$text,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_CUSTOMREQUEST => 'POST',
          ));
        $output = curl_exec($curl);
        curl_close($curl);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL,"http://178.128.115.233/api/v1/sentiment/Overview");
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS,"token={$key}&_id={$_id}&content={$text}");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // if($timeout!="") curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
        // $output = curl_exec($ch);
        // curl_close ($ch);

        $obj = json_decode($output,true);
        print_r($obj);
        // $sentiment = null;
        // if(isset($obj['data']['status']) && $text!="") {
        //     if(@$obj['data']['status']=="neg") {
        //         $sentiment = floatval("-".$obj['data']['tensor'][0]);
        //     }else if(@$obj['data']['status']=="pos") {
        //         $sentiment = floatval($obj['data']['tensor'][2]);
        //     }else if(@$obj['data']['status']=="neu" && $obj['data']['tensor'][2] > $obj['data']['tensor'][0]) {
        //         $sentiment = floatval($obj['data']['tensor'][2]);
        //     }else {
        //         $sentiment = 0;
        //     }
        // }

        // if(isset($obj['data']['status']) && $text!="") {
        // 	if(@$obj['data']['status']=="neg") {
        //         $sentiment = floatval("-".$obj['data']['tensor'][0]);
        //     }else if(@$obj['data']['status']=="pos") {
        //         $sentiment = floatval($obj['data']['tensor'][2]);
        //     }else {
        //         $sentiment = 0;
        //     }
        // }
        // return $sentiment;

        // print_r($sentiment);
    }

}