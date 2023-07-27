<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_week_data extends MX_Controller {

    var $mongo;
    var $mongodb;

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);
        ini_set('memory_limit', '8192M');

        $this->mongo = new MongoClient(MONGO_CONNECTION); // connect
        $this->mongodb = $this->mongo->blue_eye;
	}

    function index() 
    {
        //$this->run_once($type);
    }

	function run_week()
	{	
        //$this->clear_page();
	}

    function clear_page()
    {
        $rowsdata  = $this->db
                    ->select("pages.*")
                    ->where("page_type","Facebook")
                    ->get("pages")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $data = $this->get_page_info($v_row);
            if(@$data['page_id']=="" && @$data['error']==true) {
                //$this->delete_mongodb($v_row['pages_id']);

                $this->db->where("pages_id",$v_row['pages_id']);
                $this->db->delete("pages");
            }
        }
        $this->mongo->close();
    }

    function delete_mongodb($id = 0)
    {
        $rec = $this->db
                    ->where("pages_id",$id)
                    ->get("pages")
                    ->first_row("array");

        $page_id = $rec['page_id'];
        $where_delete = array("_id"=>$page_id);

        if($rec['page_type']=="Facebook") {
            $collection = $this->mongodb->createCollection("Page");
        } else {
            $collection = $this->mongodb->createCollection("Twuser");
        }

        $collection->remove($where_delete);
    }

    function get_page_info($data = array())
    {
        if($data['page_type']=="Facebook") {

                $fb_access_token = get_fb_access_token();
                $url = "https://graph.facebook.com/v2.8/".$data['page_id']."?access_token=".$fb_access_token."&fields=id,username";
                
                $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_URL, $url); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                $output = json_decode(curl_exec($ch),true);
                curl_close($ch);
                $data['error']     = isset($output['error']) ? true : false;
                $data['page_id']   = @$output['id'];
                $data['page_name'] = @$output['username'];
        } else {

            require_once(APPPATH.'/libraries/TwitterAPIExchange.php');

            $settings = array(
                'oauth_access_token' => TB_OAUTH_ACCESS_TOKEN,
                'oauth_access_token_secret' => TB_OAUTH_ACCESS_TOKEN_SECRET,
                'consumer_key' => TB_CONSUMER_KEY,
                'consumer_secret' => TB_CONSUMER_SECRET
            );

            $url = 'https://api.twitter.com/1.1/users/show.json';
            $getfield = '?screen_name='.$data['page_id'];
            $requestMethod = 'GET';

            $twitter = new TwitterAPIExchange($settings);
            $result = $twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest();

            $output = json_decode($result,true);

            $data['error']     = isset($output['errors']) ? true : false;
            $data['page_id']   = @$output['screen_name'];
            $data['page_name'] = @$output['screen_name'];
        }

        return $data;
    }

}