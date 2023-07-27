<?php
class pages_trigger extends Backend 
{
    var $mongodb;

    function __construct()
    {
        parent::__construct();
        //$mongo = new MongoClient(MONGO_CONNECTION); // connect
        //$mongo->close();

        $CI =& get_instance();
        $CI->load->model("pages_mongo_model","pages_mongo");
    }

    function set_trigger($action = "",$id = 0,$data = array())
    {
        $CI =& get_instance();
        $obj = array("status"=>true,"message"=>"");
        if($action=="") {
            //code trigger
            $obj["status"] = false;

        } else if($action=="save:before") {
            $data = $this->pages_mongo->get_page_info($data);
            if($data['page_id'] != "") {
                $num_rows = $CI->db
                        ->where("page_id",$data['page_id'])
                        ->where("page_type",$data['page_type'])
                        ->where("sys_status","active")
                        ->get("pages")
                        ->num_rows();

                if($num_rows>0 && $id=="") {
                    $obj["message"] = 'ID or Username already exists.';
                    $obj["status"] = false;

                } else {
                    if($id!="") {
                        $result = $CI->db
                            ->select("page_id")
                            ->where("pages_id",$id)
                            ->where("page_type",$data['page_type'])
                            ->where("sys_status","active")
                            ->get("pages")
                            ->first_row('array');

                        if(@$result['page_id'] != $data['page_id'] && $num_rows > 0) {
                            $obj["message"] = 'ID or Username already exists.';
                            $obj["status"] = false;

                        } else {
                            $this->pages_mongo->delete_mongodb($id);
                            $this->pages_mongo->insert_mongodb($data);
                        }
                    }
                }

                if($id=="" && $obj["status"]==true) {
                    $this->pages_mongo->insert_mongodb($data);
                }

            } else {
                $obj["message"] = 'Something wrong ! / Invalid ID or Username, Please contact support.';
                $obj["status"] = false;
            }

        } else if($action=="delete:before") {
            $this->pages_mongo->delete_mongodb($id);

        } else if($action=="save:after") {

            $data = $this->pages_mongo->get_page_info($data);
            $save = array("page_id"=>$data['page_id'],"page_name"=>$data['page_name']);
            $CI->db->where("pages_id",$id);
            $CI->db->update("pages",$save);

        } else {
            $obj["status"] = true;      
        }
        return $obj;
    }
}