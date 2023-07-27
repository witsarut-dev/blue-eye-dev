<?php
class Pages_mongo_model extends CI_Model
{

    var $mongodb;
    var $USER_ID;

    function __construct()
    {
        parent::__construct();
        //$mongo = new MongoClient(MONGO_CONNECTION); // connect
        //$this->mongodb = $mongo->blue_eye;
        $this->USER_ID = $this->session->userdata("USER_ID");
    }

    function insert($post = array())
    {
        $save = array();

        $save["total_rows"] = $post['total_rows'];
        $save["success_rows"] = $post['success_rows'];
        $save["error_rows"] = $post['error_rows'];
        $save["duplicate_rows"] = $post['duplicate_rows'];
        $save["importdate"] = date("Y-m-d H:i:s");
        $save["importby"] = $this->USER_ID;

        $this->db->insert("pages_import",$save);
        $val = $this->db->insert_id('pages_import');

        return $val;
    }

    function update($post = array())
    {
        $save = array();

        $save["total_rows"] = $post['total_rows'];
        $save["success_rows"] = $post['success_rows'];
        $save["error_rows"] = $post['error_rows'];
        $save["duplicate_rows"] = $post['duplicate_rows'];

        $this->db->where("imp_id",$post['imp_id']);
        $this->db->update("pages_import",$save);
    }

    function get_rows($post = array())
    {
        // if($post['page_type']=="Facebook") {
        //     $collection = $this->mongodb->createCollection("Page");
        // } else {
        //     $collection = $this->mongodb->createCollection("Twuser");
        // }

        // $pages = array( "page_id" => $post["page_id"]);

        // $rows = $collection->count($pages);

        // return $rows;
    }

    function insert_mongodb($post = array())
    {
        // $rows = $this->get_rows($post);
        // if($rows==0) {
        //     if($post['page_type']=="Facebook") {
        //         $collection = $this->mongodb->createCollection("Page");
        //     } else {
        //         $collection = $this->mongodb->createCollection("Twuser");
        //     }
        //     $pages = array(
        //     "page_id" => $post["page_id"],
        //     "page_name" => $post["page_name"],
        //     "page_type" => $post["page_type"]);

        //     $collection->insert($pages);
        // }
    }

    function delete_mongodb($id = 0)
    {
        // $rec = $this->db
        //             ->where("pages_id",$id)
        //             ->get("pages")
        //             ->first_row("array");

        // $page_id = $rec["page_id"];
        // $where_delete = array("page_id" => $page_id);

        // if($rec['page_type']=="Facebook") {
        //     $collection = $this->mongodb->createCollection("Page");
        // } else {
        //     $collection = $this->mongodb->createCollection("Twuser");
        // }

        // $collection->remove($where_delete);
    }

    function get_page_info($data = array())
    {
        $this->load->helper("common");
        if($data['page_type']=="Facebook"){
            $fb_access_token = get_fb_access_token();
            $url = "https://graph.facebook.com/".$data['page_id']."?fields=id,username&access_token=".$fb_access_token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $output = json_decode(curl_exec($ch),true);
            curl_close($ch);

            $data['page_id']   = @$output['id'];
            $data['page_name'] = @$output['username'];

        } else if($data['page_type']=="Twitter"){

            // require_once(APPPATH.'/libraries/TwitterAPIExchange.php');

            // $settings = array(
            //     'oauth_access_token' => TB_OAUTH_ACCESS_TOKEN,
            //     'oauth_access_token_secret' => TB_OAUTH_ACCESS_TOKEN_SECRET,
            //     'consumer_key' => TB_CONSUMER_KEY,
            //     'consumer_secret' => TB_CONSUMER_SECRET
            // );

            // $url = 'https://api.twitter.com/2/users/b';
            // $getfield = '?usernames='.$data['page_id'];
            // $requestMethod = 'GET';

            // $twitter = new TwitterAPIExchange($settings);
            // $result = $twitter->setGetfield($getfield)
            //     ->buildOauth($url, $requestMethod)
            //     ->performRequest();

            // $output = json_decode($result,true);

            $data['page_id']   = $data['page_id'];
            $data['page_name'] = $data['page_id'];

        } else if($data['page_type']=="Tiktok") {
            $tiktok_username = $data['page_id'];
            $data['page_id']   = str_replace("@", "", $data['page_id']);
            $data['page_name'] = $tiktok_username;

        } else if($data['page_type']=="Blockdit") {
            // Initialize curl
            $ch = curl_init();
            
            // URL for Scraping
            $url = "https://www.blockdit.com/".$data['page_id'];
            curl_setopt($ch, CURLOPT_URL, $url);
            
            // Return Transfer True
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($ch);
            $blockdit_doc = new DOMDocument;
            libxml_use_internal_errors(true);
            $blockdit_doc->loadHTML($output);
            libxml_clear_errors();

            $blockdit_xpath = new DOMXPath($blockdit_doc);

            $blockdit_id = $blockdit_xpath->evaluate('string(//link[@rel="canonical"]/@href)');
            $blockdit_username = $data['page_id'];
            
            $data['page_id']   = str_replace("https://www.blockdit.com/pages/", "", $blockdit_id);
            $data['page_name'] = str_replace("pages/", "", $blockdit_username);
        }

        return $data;
    }
}