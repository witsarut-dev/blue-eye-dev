<?php
class Link_analysis_model extends CI_Model {

    var $CLIENT_ID;
    var $FLAG_DELETE = 1;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
        $this->load->library("linkapi");
    }

    function get_link_id($link_id = 0)
    {
        $rowsdata = $this->db
                    ->select("client_link.*")
                    ->where("client_link.link_id",$link_id)
                    ->where("client_link.client_id",$this->CLIENT_ID)
                    ->get("client_link_analysis client_link")
                    ->first_row('array');

        return $rowsdata;
    }

    function get_list($link_id = 0)
    {
        $rowsdata = $this->db
                    ->select("client_list.*")
                    ->where("client_list.link_id",$link_id)
                    ->where("client_list.client_id",$this->CLIENT_ID)
                    ->order_by("client_list.link_no")
                    ->get("client_link_list client_list")
                    ->result_array();

        return $rowsdata;
    }

    function get_client_link($link_type = 'page')
    {
        $rowsdata = $this->db
                    ->select("client_link.*")
                    ->where("client_link.client_id",$this->CLIENT_ID)
                    ->where("client_link.link_type",$link_type)
                    ->order_by("client_link.link_id","ASC")
                    ->get("client_link_analysis client_link")
                    ->result_array();

        return $rowsdata;
    }


    function insert_link($post = array())
    {
        $save = array();
        $save["client_id"]   = $this->CLIENT_ID;
        $save["link_name"]   = $post['link_name'];
        $save["link_type"]   = $post['link_type'];
        $save["created_date"] = date("Y-m-d H:i:s");

        $this->db->insert("client_link_analysis",$save);
        $link_id = $this->db->insert_id("client_link_analysis");

        $this->insert_link_list($link_id,$post);
        return $link_id;
    }

    function insert_user($post = array())
    {
        $save = array();
        $save["client_id"]   = $this->CLIENT_ID;
        $save["link_name"]   = $post['link_name'];
        $save["link_type"]   = $post['link_type'];
        $save["created_date"] = date("Y-m-d H:i:s");

        $this->db->insert("client_link_analysis",$save);
        $link_id = $this->db->insert_id("client_link_analysis");

        $this->insert_user_list($link_id,$post);
        return $link_id;
    }


    function insert_fanpage($post = array())
    {
        $save = array();
        $save["client_id"]   = $this->CLIENT_ID;
        $save["link_name"]   = $post['link_name'];
        $save["link_type"]   = $post['link_type'];
        $save["created_date"] = date("Y-m-d H:i:s");

        $this->db->insert("client_link_analysis",$save);
        $link_id = $this->db->insert_id("client_link_analysis");

        $this->insert_fanpage_list($link_id,$post);
        return $link_id;
    }

    function update_link($post = array())
    {
        $link_id = $post['link_id'];

        $save = array();
        $save["link_name"]  = $post['link_name'];

        $this->db->where("link_id",$link_id);
        $this->db->update("client_link_analysis",$save);

        $this->insert_link_list($link_id,$post);
        return $link_id;
    }

    function update_user($post = array())
    {
        $link_id = $post['link_id'];

        $save = array();
        $save["link_name"]  = $post['link_name'];

        $this->db->where("link_id",$link_id);
        $this->db->update("client_link_analysis",$save);

        $this->insert_user_list($link_id,$post);
        return $link_id;
    }

    function update_fanpage($post = array())
    {
        $link_id = $post['link_id'];

        $save = array();
        $save["link_name"]  = $post['link_name'];

        $this->db->where("link_id",$link_id);
        $this->db->update("client_link_analysis",$save);

        $this->insert_fanpage_list($link_id,$post);
        return $link_id;
    }

    function insert_link_list($link_id,$post = array())
    {   
        if(isset($post['link_url'])) {
            foreach($post['link_url'] as $link_no=>$link_url) {
                if(trim($link_url)!="") {
                    $this->db->where("link_id",$link_id);
                    $this->db->where("link_no",$link_no);
                    $this->db->where("CLIENT_ID",$this->CLIENT_ID);
                    $rec = $this->db->get("client_link_list")->first_row('array');

                    if(!isset($rec['link_url'])) {
                        $source = $this->linkapi->get_link_source($link_url);
                        $msg_id = $this->linkapi->get_link_msg_id($link_url,true);
                        $type   = $this->linkapi->get_link_fb_type($link_url);
                        $save = array();
                        $save["link_id"]    = $link_id;
                        $save["client_id"]  = $this->CLIENT_ID;
                        $save["link_url"]   = $link_url;
                        $save["link_no"]    = $link_no;
                        $save['sourceid']   = get_media_type_id($source);
                        $save['msg_id']     = $msg_id;
                        $save['link_status'] = ($type=="photos") ? 2 : 0;
                        $save["link_type"]   = $post['link_type'];
                        $this->db->insert("client_link_list",$save);
                    }
                } else {
                    $this->db->where("link_id",$link_id);
                    $this->db->where("link_no",$link_no);
                    $this->db->where("CLIENT_ID",$this->CLIENT_ID);
                    $this->db->delete("client_link_list");
                }
            }
        }
    }

    function insert_user_list($link_id,$post = array())
    {   
        $link_url = $post['link_url'][1];
        $link_no = 1;
        $this->db->where("link_id",$link_id);
        $this->db->where("link_no",$link_no);
        $this->db->where("CLIENT_ID",$this->CLIENT_ID);
        $rec = $this->db->get("client_link_list")->first_row('array');

        if(!isset($rec['link_url'])) {
            $source = $this->linkapi->get_link_source($link_url);
            $user_id = $this->linkapi->get_link_user_id($link_url,$username);
            $save = array();
            $save["link_id"]    = $link_id;
            $save["client_id"]  = $this->CLIENT_ID;
            $save["link_url"]   = $link_url;
            $save["link_no"]    = $link_no;
            $save['sourceid']   = get_media_type_id($source);
            $save['msg_id']     = $user_id;
            $save['link_status'] = 0;
            $save["link_type"]   = $post['link_type'];
            $this->db->insert("client_link_list",$save);
        }
      
    }

    function insert_fanpage_list($link_id,$post = array())
    {   
        $this->insert_user_list($link_id,$post);
    }

    function delete_link($link_id = 0)
    {
        $rec = $this->db
            ->select("link_name")
            ->where("link_id",$link_id)
            ->get("client_link_analysis client_link")
            ->first_row("array");

        $where_delete = array("client_id"=>$this->CLIENT_ID,"link_id"=>$link_id);
        $this->db->delete("client_link_analysis",$where_delete);
        $this->db->delete("client_link_list",$where_delete);

        return $rec['link_name'];
    }

    function check_link_name($post = array())
    {
        $num_rows = 0;
        $check = false;
        if(@$post['link_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_link_id($post['link_id']);
            if($post['link_name']!=@$rec['link_name']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_link.link_type",$post['link_type'])
                ->where("client_link.link_name",$post['link_name'])
                ->where("client_link.client_id",$this->CLIENT_ID)
                ->get("client_link_analysis client_link")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_user_url($post = array())
    {
        $num_rows = 0;
        $check = false;

        $user_id = $this->linkapi->get_link_user_id($post['link_url'][1],$post['link_name']);

        if(@$post['link_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_link_id($post['link_id']);
            if($post['link_name']!=@$rec['link_name']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_link.link_type",$post['link_type'])
                ->where("client_link.link_name",$post['link_name'])
                ->where("client_link.client_id",$this->CLIENT_ID)
                ->get("client_link_analysis client_link")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_fanpage_url($post = array())
    {
        $num_rows = 0;
        $check = false;

        $page_id = $this->linkapi->get_link_fanpage_id($post['link_url'][1],$post['link_name']);

        if(@$post['link_id']=="") {
            $check = true;
        } else {
            $rec = $this->get_link_id($post['link_id']);
            if($post['link_name']!=@$rec['link_name']) {
                $check = true;
            }
        }
        if($check) {
            $num_rows = $this->db
                ->where("client_link.link_type",$post['link_type'])
                ->where("client_link.link_name",$post['link_name'])
                ->where("client_link.client_id",$this->CLIENT_ID)
                ->get("client_link_analysis client_link")
                ->num_rows();
        }
        return ($num_rows>0) ? true : false;
    }

    function check_link_max($link_type,&$add_link_analysis)
    {
        $config = $this->master_model->get_config();

        if($link_type=='page') {
            $add_link_analysis = isset($config['add_link_analysis']) ? $config['add_link_analysis'] : 10;
        } else {
            $add_link_analysis = isset($config['add_user_analysis']) ? $config['add_user_analysis'] : 10;
        }
        
        $this->db
            ->select("link_id")
            ->where("client_id",$this->CLIENT_ID)
            ->where("link_type",$link_type)
            ->where("MONTH(created_date)",date("n"))
            ->get("client_link_analysis")
            ->result_array();

        $sql = $this->db->last_query();

        $sql_log = str_replace("client_link_analysis","client_link_analysis_log",$sql);
        $num_rows = $this->db->query($sql.' UNION '.$sql_log)->num_rows();
        
        return ($num_rows>=$add_link_analysis) ? true : false;
    }

    function get_mongo_page($msg_id = 0) 
    {
        $filter = array('_id' => $msg_id);
        $result =  mongodb_query("Linkanalysis",$filter);
        return $result;
    }

    function get_mongo_user($msg_id = 0) 
    {
        $filter = array('_id' => $msg_id);
        $result = mongodb_query("Linkanalysisuser",$filter);
        return $result;
    }

    function get_mongo_fanpage($msg_id = 0) 
    {
        $filter = array('_id' => $msg_id);
        $result = mongodb_query("Linkanalysisfanpage",$filter);
        return $result;
    }

    //===================================================================link analysis news function

    function get_data_list($link_id = 0)         //ส่งค่า link-id เข้ามาเพื่อดึง msg_id ไปใช้ต่อ
    {
        $rowsdata = $this->db
                    ->select("client_list.*")
                    ->where("client_list.link_id",$link_id)
                    ->where("client_list.client_id",$this->CLIENT_ID)
                    ->order_by("client_list.link_no")
                    ->get("client_link_list client_list")
                    ->result_array();

        return $rowsdata;
    }

    function get_link_match($msg_id = 0){

        $rowsdata = $this->db
                    ->select("client_list.*")
                    ->where("client_list.msg_id",$msg_id)
                    ->where("client_list.client_id",$this->CLIENT_ID)
                    ->order_by("client_list._id")
                    ->get("link_analysis_match client_list")
                    ->result_array();
        return $rowsdata;

    }

    
    function get_mongo_linkanalysis_feed($msg_id=0)         //ดึงข้อมูลได้ปกติด้วย msg_id ชี้ไปที่ feeduserid
    {
        $filter = array('_id' => $msg_id);
        $result = mongodb_query("LinkanalysisFeed",$filter);
        return $result;
    }

    function get_mongo_linkanalysis_comment($id=0)      //ดึงข้อมูลได้ปกติด้วย id ชี้ไปที่ feedid
    {
        $filter = array('feedid' => $id);
        $result = mongodb_query("LinkanalysisComment",$filter);
        return $result;
    }

    function get_mongo_linkanalysis_share($id=0)        //ดึงข้อมูลได้ปกติด้วย id ชี้ไปที่ feedid
    {
        $filter = array('feedid' => $id);
        $result = mongodb_query("LinkanalysisShare",$filter);
        return $result;
    }
    

}
?>