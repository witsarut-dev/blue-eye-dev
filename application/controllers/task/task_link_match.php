<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_link_match extends MX_Controller {

	
    var $tb_link = "link_analysis_match";

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        $this->load->model("login/login_model");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);
	}

    function index($type = "1")
    {
        //$this->run_time($type);
        //$this->run_now($type);
    }
	
    //========================================================================================================================champ write start
    

    function run_match_link($type = "1"){

        $rowsdata_client = $this->login_model->get_client();
        

        foreach($rowsdata_client as $k_row=>$v_row){
            $client_id = $v_row['client_id'];

            $array = $this->get_keyword_link($client_id);

            foreach($array as $k_row=>$v_row) {
                $this->search_mongo_linkFeed($v_row);
            }

        }        
    }
    
    function get_keyword_link($client_num){

        $rowsdata = $this->db
                    ->select("keyword_match.*")
                    ->where("client_id",$client_num)       
                    ->where("keyword_match.status = 'active'")
                    ->order_by("keyword_match.created_date","ASC")
                    ->get("keyword keyword_match")
                    ->result_array(); 
		return $rowsdata;
    }

    function search_mongo_linkFeed($rec = array()){

        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
        $tb_link = $this->tb_link;

        $rec_replace = str_replace(".","\.",$rec['keyword_name']); //replace . to \.
        $query = array('feedcontent' => new MongoRegex("/".$rec_replace."/i"));
        $collection = $mongodb->selectCollection("LinkanalysisFeed");
        $cursor = $collection->find($query);
        $cursor->timeout(-1);



        foreach($cursor as $k_row=>$v_row) {

            $client_id = $rec["client_id"];
            $_id = $v_row['_id'];
            $keyword_id = $rec["keyword_id"];
            // $keyword_name = $rec["keyword_name"];
            // $msg_id = $v_row['feeduserid'];

            $value_number = $this->check_max();
            $value_check = $this->check_data_match_link($client_id,$_id,$keyword_id);

            
            if($value_check){

                $save = array();

                $link_analysis_match_id = ((int)$value_number) + 1;
                $client_id = $rec["client_id"];
                $keyword_id = $rec["keyword_id"];
                $keyword_name = $rec["keyword_name"];
                $msg_id = $v_row['feeduserid'];
                $_id = $v_row['_id'];

                $save["link_analysis_match_id"] = $link_analysis_match_id;
                $save["client_id"] = $client_id;
                $save["keyword_id"] = $keyword_id;
                $save["keyword_name"] = $keyword_name;
                $save["msg_id"] = $msg_id;
                $save["_id"] = $_id;

                $this->db->insert("{$tb_link}",$save);
            }
        }
        $mongo->close();
    }

    function check_data_match_link($client_id,$_id,$keyword_id){

        $check_match = 0;
        $rowsdata = $this->db
                    ->select("data_match.*")
                    //->select("data_match._id")
                    ->where("client_id",$client_id)     
                    ->where("keyword_id",$keyword_id)  
                    ->where("_id",$_id)
                    ->order_by("data_match.link_analysis_match_id","ASC")
                    ->get("link_analysis_match data_match")
                    ->first_row("array"); 

        if(($rowsdata["client_id"] == $client_id) && ($rowsdata["_id"] == $_id) && ($rowsdata["keyword_id"] == $keyword_id)){
            $check_match = 0;     
        }
        else{
            $check_match = 1;   
        }
        return $check_match;
        
    }

    function check_max(){
        $maxdata_id = $this->db
                    ->select("MAX(data_match.link_analysis_match_id) AS maxdata")
                    ->get("link_analysis_match data_match")
                    ->first_row("array"); 

        if($maxdata_id["maxdata"] == NULL){
            $max_val = 0;
        }
        else{
            $max_val = $maxdata_id["maxdata"];
        }
        return $max_val;
    }





    // function check_data_test(){

    //     $check_match = 0;
    //     $rowsdata = $this->db
    //                 ->select("data_match.*")
    //                 ->where("data_match.client_id = '6' ")       
    //                 ->where("data_match.keyword_id = '2660' ")       
    //                 ->where("data_match._id = '575635818_10158466745900819'")
    //                 ->order_by("data_match.link_analysis_match_id","ASC")
    //                 ->get("link_analysis_match data_match")
    //                 // ->result_array();
    //                 ->first_row("array"); 

	// 	var_dump($rowsdata);
    //     // echo $rowsdata["client_id"];

    //     if(($rowsdata["client_id"] == '6') && ($rowsdata["_id"] == '575635818_10158466745900819') && ($rowsdata["keyword_id"] == '2660')){

    //         $check_match = 0;
           
    //     }
    //     else{

    //         $check_match = 1;
            
    //     }
    //     echo $check_match;
        
    // }

    



//========================================================================================================================
	

}
