<?php
class Keyword extends MX_Controller 
{
	public static $message = "";
	var $module = "config";
	var $title = "config";

    function __construct()
    {
        parent::__construct();
    }

    function cmdReset()
    {
    	$rowsdata = $this->db->get("keyword")->result_array();

        $mongo      = new MongoClient(MONGO_CONNECTION);
        $mongodb    = $mongo->blue_eye;
        $collection = $mongodb->createCollection("Keyword");
        $collection->drop();
        foreach($rowsdata as $k_row=>$v_row) {
            $save       = array(
                "keyword_id"=>new MongoInt32($v_row['keyword_id']),
                "keyword_name"=>$v_row['keyword_name'],
                "client_id"=>new MongoInt32($v_row['client_id']),
                "3mcollect"=>new MongoInt32(1)); 
            $collection->insert($save);
        }

        $mongo->close();
        
        echo '<h4 class="text-success">บันทึกข้อมูลเรียบร้อยแล้ว</h4>';
    }
}
?>