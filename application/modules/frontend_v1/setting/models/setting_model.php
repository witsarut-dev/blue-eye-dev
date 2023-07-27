<?php
class Setting_model extends CI_Model {

    var $CLIENT_ID;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
    }

    function get_client()
    {
        $result = $this->db
                    ->select("client.*")
                    ->where("client.client_id",$this->CLIENT_ID)
                    ->get("client")
                    ->first_row('array');

        return $result;
    }

    function get_company($company_keyword_id = 0)
    {
        $result = $this->db
                    ->select("company.*")
                    ->where("company.company_keyword_id",$company_keyword_id)
                    ->where("company.client_id",$this->CLIENT_ID)
                    ->get("company_keyword company")
                    ->first_row('array');

        return $result;
    }

    function get_company_keyword()
    {
        $rowsdata = $this->db
                    ->select("company.*")
                    ->where("company.client_id",$this->CLIENT_ID)
                    ->order_by("company.company_keyword_id","ASC")
                    ->get("company_keyword company")
                    ->result_array();

        return $rowsdata;
    }

    function check_company_keyword($company_keyword_name = "") 
    {
        $num_rows = $this->db
                    ->where("company.company_keyword_name",$company_keyword_name)
                    ->where("company.client_id",$this->CLIENT_ID)
                    ->get("company_keyword company")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function check_group_keyword($group_keyword_name = "") 
    {
        $num_rows = $this->db
                    ->where("group_keyword.group_keyword_name",$group_keyword_name)
                    ->where("group_keyword.client_id",$this->CLIENT_ID)
                    ->get("group_keyword")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function check_keyword($keyword_name = "") 
    {
        $num_rows = $this->db
                    ->where("keyword.keyword_name",$keyword_name)
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->get("keyword")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function get_competitor_keyword($company_keyword_type = "")
    {
        $rowsdata = $this->db
                    ->select("company.*")
                    ->where("company.client_id",$this->CLIENT_ID)
                    ->where("company.company_keyword_type","Competitor")
                    ->order_by("company.company_keyword_id","ASC")
                    ->get("company_keyword company")
                    ->result_array();

        return $rowsdata;
    }

    function get_group_keyword()
    {
        $rowsdata = $this->db
                    ->select("group_keyword.*")
                    ->where("group_keyword.client_id",$this->CLIENT_ID)
                    ->order_by("group_keyword.company_keyword_id,group_keyword.group_keyword_id","ASC")
                    ->get("group_keyword")
                    ->result_array();

        return $rowsdata;
    }

    function get_keyword()
    {
        $config = $this->master_model->get_config();
        $add_keyword = isset($config['add_keyword']) ? $config['add_keyword'] : 50;

        $rowsdata = $this->db
                    ->select("keyword.*")
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->order_by("keyword.company_keyword_id,keyword.keyword_id","ASC")
                    ->limit($add_keyword)
                    ->get("keyword")
                    ->result_array();

        return $rowsdata;
    }

    function insert_company($post = array())
    {

    	$save = array();
    	$save['client_id']               = $this->CLIENT_ID;
    	$save['company_keyword_name']    = $post['company_keyword_name'];
    	$save['company_keyword_type']    = $post['company_keyword_type'];
    	$save['company_keyword_fb']      = $post['company_keyword_fb'];
    	$save['created_date']            = date("Y-m-d H:i:s");
        $this->db->insert("company_keyword",$save);

        $company_keyword_id = $this->db->insert_id("company_keyword");

        return $company_keyword_id;
    }

    function insert_group_keyword($post = array())
    {
        $save = array();
        $save['client_id']             = $this->CLIENT_ID;
        $save['group_keyword_name']    = $post['group_keyword_name'];
        $save['company_keyword_id']    = $post['company_keyword_id'];
        $save['created_date']          = date("Y-m-d H:i:s");
        $this->db->insert("group_keyword",$save);

        $group_keyword_id = $this->db->insert_id("group_keyword");

        return $group_keyword_id;
    }

    function insert_keyword($post = array())
    {
        $company = $this->db
                ->where("group_keyword_id",$post['group_keyword_id'])
                ->get("group_keyword")
                ->first_row("array");

        $save = array();
        $save['client_id']             = $this->CLIENT_ID;
        $save['keyword_name']          = $post['keyword_name'];
        $save['company_keyword_id']    = $company['company_keyword_id'];
        $save['group_keyword_id']      = $post['group_keyword_id'];
        $save['created_date']          = date("Y-m-d H:i:s");
        $this->db->insert("keyword",$save);

        $keyword_id = $this->db->insert_id("keyword");

        $this->insert_keyword_mongodb($post,$keyword_id);

        return $keyword_id;
    }

    function delete_company($company_keyword_id = 0)
    {
        $rec = $this->db
            ->select("company_keyword_name")
            ->where("company_keyword_id",$company_keyword_id)
            ->get("company_keyword")
            ->first_row("array");

        $rowsdata = $this->db
            ->select("keyword_id")
            ->where("company_keyword_id",$company_keyword_id)
            ->where("client_id",$this->CLIENT_ID)
            ->get("keyword")
            ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $keyword_id = $v_row['keyword_id'];
            $this->delete_keyword($keyword_id);
        }

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->delete("group_keyword");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->delete("competitor_key_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->delete("competitor_cate_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->delete("competitor_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->delete("sys_task");

    	$this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->delete("company_keyword");

        return $rec['company_keyword_name'];
    }

    function delete_group_keyword($group_keyword_id = 0)
    {
        $rec = $this->db
            ->select("group_keyword_name")
            ->where("group_keyword_id",$group_keyword_id)
            ->get("group_keyword")
            ->first_row("array");

        $rowsdata = $this->db
            ->select("keyword_id")
            ->where("group_keyword_id",$group_keyword_id)
            ->where("client_id",$this->CLIENT_ID)
            ->get("keyword")
            ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $keyword_id = $v_row['keyword_id'];
            $this->delete_keyword($keyword_id);
        }

    	$this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("group_keyword_id",$group_keyword_id);
        $this->db->delete("group_keyword");

        return $rec['group_keyword_name'];
    }

    function delete_keyword($keyword_id = 0)
    {
        $rec = $this->db
            ->select("keyword_name")
            ->where("keyword_id",$keyword_id)
            ->get("keyword")
            ->first_row("array");

    	$this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("keyword_id",$keyword_id);
        $this->db->delete("keyword");

        $this->delete_keyword_mongodb($keyword_id);

        return $rec['keyword_name'];
    }

    function clean_keyword()
    {
        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("keyword_id NOT IN (SELECT keyword_id FROM keyword WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("sys_task");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("keyword_id NOT IN (SELECT keyword_id FROM keyword WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("client_keyword");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("keyword_id NOT IN (SELECT keyword_id FROM keyword WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("own_key_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("keyword_id NOT IN (SELECT keyword_id FROM keyword WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("competitor_key_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("own_match_id NOT IN (SELECT own_match_id FROM own_key_match WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("own_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("competitor_match_id NOT IN (SELECT competitor_match_id FROM competitor_key_match WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("competitor_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("own_match_id NOT IN (SELECT own_match_id FROM own_match WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("own_cate_match");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("competitor_match_id NOT IN (SELECT competitor_match_id FROM competitor_match WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("competitor_cate_match");
    }

    function get_activity_log($post = array(),&$total_rows)
    {
        $result = array();

        $lenght = $post["length"];
        $page   = ($post['start'] / $lenght) + 1;
        $sort   = $post['order']['0']['column'];
        $order  = $post['order']['0']['dir'];
        $column = array("log_activity","log_user","log_time");

        $sql = $this->db
                    ->where("client_id",$this->CLIENT_ID)
                    ->order_by($column[$sort],$order)
                    ->from("activity_log")
                    ->query_string();

        $total_rows = $this->db->query($sql)->num_rows();
        $newsql = get_page($sql,$this->db->dbdriver,$page,$lenght);
        $rowsdata = $this->db->query($newsql)->result_array();
        foreach($rowsdata as $k_row=>$v_row) {
            array_push($result,array(
                $v_row["log_activity"],
                $v_row["log_user"],
                getDatetimeformat($v_row["log_time"])
            ));
        }

        return $result;
    }

    function get_block_user()
    {
        $sql = $this->db
                    ->where("client_id",$this->CLIENT_ID)
                    ->order_by("block_time","DESC")
                    ->from("block_user")
                    ->query_string();

        $newsql = get_page($sql,$this->db->dbdriver,1,1000);
        $rowsdata = $this->db->query($newsql)->result_array();

        return $rowsdata;
    }

    function unblock($block_id = 0)
    {
        $rec = $this->db
            ->where("block_id",$block_id)
            ->get("block_user")
            ->first_row("array");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("block_id",$block_id);
        $this->db->delete("block_user");

        return $rec['block_user'];
    }


    function insert_keyword_mongodb($post = array(),$keyword_id = 0)
    {
        $mongo      = new MongoClient(MONGO_CONNECTION);
        $mongodb    = $mongo->blue_eye;
        $collection = $mongodb->createCollection("Keyword");
        $save       = array("keyword_id"=>new MongoInt32($keyword_id),"keyword_name"=>$post['keyword_name'],"client_id"=>new MongoInt32($this->CLIENT_ID),"3mcollect"=>new MongoInt32(0)); 
        $collection->insert($save);
        $mongo->close();
    }

    function delete_keyword_mongodb($keyword_id = 0)
    {
        $mongo        = new MongoClient(MONGO_CONNECTION);
        $mongodb      = $mongo->blue_eye;
        $collection   = $mongodb->createCollection("Keyword");
        $where_delete = array("keyword_id"=>new MongoInt32($keyword_id),"client_id"=>new MongoInt32($this->CLIENT_ID)); 
        $collection->remove($where_delete);
        $mongo->close();
    }

}
?>