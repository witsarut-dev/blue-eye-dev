<?php
class Setting_model extends CI_Model {

    var $CLIENT_ID;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
        $this->USER_ID = $this->session->userdata("USER_ID");
    }

    function get_client_id()
    {
        return $this->CLIENT_ID;
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

    function check_group_keyword($group_keyword_name = "", $company_keyword_id = 0) 
    {
        $num_rows = $this->db
                    ->where("group_keyword.group_keyword_name",$group_keyword_name)
                    ->where("group_keyword.client_id",$this->CLIENT_ID)
                    ->where("group_keyword.company_keyword_id",$company_keyword_id)
                    ->where("group_keyword.status","active")
                    ->get("group_keyword")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function check_inactive_group_keyword($group_keyword_name = "", $company_keyword_id = 0){
        $num_rows = $this->db
                    ->where("group_keyword.group_keyword_name",$group_keyword_name)
                    ->where("group_keyword.client_id",$this->CLIENT_ID)
                    ->where("group_keyword.company_keyword_id",$company_keyword_id)
                    ->where("group_keyword.status","inactive")
                    ->get("group_keyword")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function check_categories($categories_name = "", $group_keyword_id = 0) {
        $num_rows = $this->db->where("categories.categories_name", $categories_name)
                             ->where("categories.client_id", $this->CLIENT_ID)
                             ->where("categories.group_keyword_id", $group_keyword_id)
                             ->where("categories.status", "active")
                             ->get("categories")
                             ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function check_keyword($keyword_name = "", $group_keyword_id = 0) {
        $num_rows = $this->db
                    ->where("keyword.keyword_name", $keyword_name)
                    ->where("keyword.client_id", $this->CLIENT_ID)
                    ->where("keyword.group_keyword_id",$group_keyword_id)
                    ->where("keyword.status","active")
                    ->get("keyword")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    // In case that user want to use keyword that has been delete before.
    function check_keyword_inactive($keyword_name = "", $group_keyword_id = 0, $categories_id = 0) {
        $num_rows = $this->db->where("keyword.keyword_name", $keyword_name)
                             ->where("keyword.client_id", $this->CLIENT_ID)
                             ->where("keyword.categories_id", $categories_id)
                             ->where("keyword.group_keyword_id", $group_keyword_id)
                             ->where("keyword.status", "inactive")
                             ->get("keyword")
                             ->num_rows();

        return ($num_rows>0) ? true : false;
    }
    //

    function check_tag_keyword($tag_keyword , $type_keyword , $keyword_id) {
        $num_rows = $this->db
                    ->where("include_exclude_keyword.includeexclude_name",$tag_keyword)
                    ->where("include_exclude_keyword.type",$type_keyword)
                    ->where("include_exclude_keyword.keyword_id",$keyword_id)
                    ->get("include_exclude_keyword")
                    ->num_rows();

        return ($num_rows>0) ? true : false;
    }

    function get_competitor_keyword($company_keyword_type = "") {
        $rowsdata = $this->db
                    ->select("company.*")
                    ->where("company.client_id",$this->CLIENT_ID)
                    ->where("company.company_keyword_type","Competitor")
                    ->order_by("company.company_keyword_id","ASC")
                    ->get("company_keyword company")
                    ->result_array();

        return $rowsdata;
    }

    function get_group_keyword() {
        $rowsdata = $this->db
                    ->select("group_keyword.*")
                    ->where("group_keyword.client_id",$this->CLIENT_ID)
                    ->where("group_keyword.status","active")
                    ->order_by("group_keyword.company_keyword_id,group_keyword.group_keyword_id","ASC")
                    ->get("group_keyword")
                    ->result_array();

        return $rowsdata;
    }

    function get_keyword_name($keyword_name = "") {
        $rowsdata = $this->db
                    ->select("keyword.keyword_id")
                    ->where("keyword.keyword_name",$keyword_name)
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->get("keyword")
                    ->first_row('array');

        return $rowsdata['keyword_id'];
    }

    function get_keyword() {
        $config = $this->master_model->get_config();
        $add_keyword = isset($config['add_keyword']) ? $config['add_keyword'] : 50;

        $rowsdata = $this->db
                    ->select("keyword.*")
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->where("keyword.status","active")
                    ->order_by("keyword.company_keyword_id,keyword.keyword_id","ASC")
                    ->limit($add_keyword)
                    ->get("keyword")
                    ->result_array();

        return $rowsdata;
    }

    function get_tagkeyword($id) {
        $rowsdata = $this->db
                    ->select("include_exclude_keyword.*")
                    ->where("include_exclude_keyword.keyword_id",$id)
                    ->get("include_exclude_keyword")
                    ->result_array();

        return $rowsdata;
    }

    function get_categories() {
        $rowsdata = $this->db->select("categories.*")
                             ->where("categories.client_id", $this->CLIENT_ID)
                             ->where("categories.status", "active")
                             ->order_by("categories.company_keyword_id, categories.categories_id", "ASC")
                             ->get("categories")
                             ->result_array();

        return $rowsdata;
    }

    function del_key_includeexclude($id) {
        $rec = $this->db
            ->select("includeexclude_name")
            ->where("includeexclude_id",$id)
            ->get("include_exclude_keyword")
            ->first_row("array");

        $where_delete = array("includeexclude_id" => $id);

        $this->db->delete("include_exclude_keyword", $where_delete);

        return $rec['includeexclude_name'];
    }

    function insert_key_includeexclude($key_tag, $type, $key_id) {
        echo $key_tag;
    	$save = array();
    	$save['includeexclude_name'] = $key_tag;
    	$save['keyword_id']          = $key_id;
    	$save['type']                = $type;
        $save['created_date']        = date("Y-m-d H:i:s");
        $this->db->insert("include_exclude_keyword", $save);

        // $include_exclude_keyword_id = $this->db->insert_id("include_exclude_keyword");
        $include_exclude_keyword_name = $save['includeexclude_name'];
        return $include_exclude_keyword_name;
    }

    function insert_company($post = array()) {
    	$save = array();
    	$save['client_id']               = $this->CLIENT_ID;
    	$save['company_keyword_name']    = $post['company_keyword_name'];
    	$save['company_keyword_type']    = $post['company_keyword_type'];
    	$save['company_keyword_fb']      = $post['company_keyword_fb'];
    	$save['created_date']            = date("Y-m-d H:i:s");
        $this->db->insert("company_keyword", $save);

        $company_keyword_id = $this->db->insert_id("company_keyword");

        return $company_keyword_id;
    }

    function insert_group_keyword($post = array()) {
        $save = array();
        $save['client_id']          = $this->CLIENT_ID;
        $save['group_keyword_name'] = $post['group_keyword_name'];
        $save['company_keyword_id'] = $post['company_keyword_id'];
        $save['created_date']       = date("Y-m-d H:i:s");
        $save['status'] = 'active';

        $this->db->insert("group_keyword", $save);

        $group_keyword_id = $this->db->insert_id("group_keyword");

        return $group_keyword_id;
    }

    function insert_keyword($post = array()) {
        $query_result = $this->db->where("group_keyword_id", $post['group_keyword_id'])
                                 ->get("group_keyword")
                                 ->first_row("array");

        $save = array();
        $save['client_id']          = $this->CLIENT_ID;
        $save['keyword_name']       = $post['keyword_name'];
        $save['company_keyword_id'] = $query_result['company_keyword_id'];
        $save['group_keyword_id']   = $post['group_keyword_id'];

        $save['thai_only']          = (isset($post['thai_only']) && $post['thai_only']=='1') ? 1 : 0;
        $save['primary_keyword']    = (isset($post['primary_keyword']) && $post['primary_keyword']=='1') ? 1 : 0;

        $save['created_date']       = date("Y-m-d H:i:s");
        $save['status'] = 'active';

        $this->db->insert("keyword",$save);

        $keyword_id = $this->db->insert_id("keyword");

        $save_task = array();
        $save_task['task_type']          = "Keyword";
        $save_task['client_id']          = $this->CLIENT_ID;
        $save_task['company_keyword_id'] = $query_result['company_keyword_id'];
        $save_task['category_id']        = 0;
        $save_task['keyword_id']         = $keyword_id;

        $this->db->insert("sys_task", $save_task);

        $this->insert_keyword_mongodb($post, $keyword_id);

        return $keyword_id;
    }

    // function for "insert" keyword name that category name selected to database
    function insert_keyword_categories($post = array()) {
        $query_result = $this->db->where("categories_id", $post['categories_id'])
                                 ->get("categories")
                                 ->first_row("array");

        $save = array();
        $save['client_id']          = $this->CLIENT_ID;
        $save['keyword_name']       = $post['keyword_name'];
        $save['company_keyword_id'] = $query_result['company_keyword_id'];
        $save['group_keyword_id']   = $query_result['group_keyword_id'];

        $save['categories_id']      = $post['categories_id'];

        $save['thai_only']          = (isset($post['thai_only']) && $post['thai_only']=='1') ? 1 : 0;
        $save['primary_keyword']    = (isset($post['primary_keyword']) && $post['primary_keyword']=='1') ? 1 : 0;

        $save['created_date']       = date("Y-m-d H:i:s");
        $save['status'] = 'active';

        $this->db->insert("keyword", $save);

        $keyword_id = $this->db->insert_id("keyword");

        $save_task = array();
        $save_task['task_type']          = "Keyword";
        $save_task['client_id']          = $this->CLIENT_ID;
        $save_task['company_keyword_id'] = $query_result['company_keyword_id'];
        $save_task['category_id']        = 0;
        $save_task['keyword_id']         = $keyword_id;

        $this->db->insert("sys_task", $save_task);

        return $keyword_id;
    }
    // end of function insert_keyword_categories

    // function for "insert" category name to database
    function insert_categories($post = array()) {
        $query_result = $this->db->where("group_keyword_id", $post['group_keyword_id'])
                                 ->get("group_keyword")
                                 ->first_row("array");
        $save = array();
        $save['client_id']          = $this->CLIENT_ID;
        $save['categories_name']    = $post['categories_name'];
        $save['company_keyword_id'] = $query_result['company_keyword_id'];
        $save['group_keyword_id']   = $post['group_keyword_id'];
        $save['created_date']       = date("Y-m-d H:i:s");
        $save['status'] = 'active';

        $this->db->insert("categories", $save);

        $categories_id = $this->db->insert_id("categories");

        return $categories_id;
    }
    // end of function insert_categories

    // function for "insert" include & exlude keyword to database
    function insert_include_exclude($post = array()) {
        $save = array();
        $save['includeexclude_name'] = $post['includeexclude_name'];
        $save['keyword_id']          = $post['keyword_id'];
        $save['type']                = "include";
        $save['created_date']        = date("Y-m-d H:i:s");

        $this->db->insert("include_exclude_keyword",$save);

        $includeexclude_id = $this->db->insert_id("include_exclude_keyword");

        return $includeexclude_id;
    }
    // end of function insert_include_exclude

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
        $this->db->delete("competitor_match_daily");

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

    function delete_keyword($keyword_id = 0) {
        $rec = $this->db->select("keyword_name")
                        ->where("keyword_id",$keyword_id)
                        ->get("keyword")
                        ->first_row("array");

    	$this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("keyword_id",$keyword_id);
        $this->db->delete("keyword");

        // $this->delete_keyword_mongodb($keyword_id);

        return $rec['keyword_name'];
    }

    // function for "delete" category name from database
    function delete_categories($categories_id = 0) {
        $rec = $this->db->select("categories_name")
                        ->where("categories_id", $categories_id)
                        ->get("categories")
                        ->first_row("array");

    	$this->db->where("client_id", $this->CLIENT_ID);
    	$this->db->where("categories_id", $categories_id);
        $this->db->delete("categories");

        return $rec['categories_name'];
    }
    // end of function delete_categories

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
        $this->db->where("own_match_id NOT IN (SELECT own_match_id FROM own_key_match WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("own_match_daily");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("competitor_match_id NOT IN (SELECT competitor_match_id FROM competitor_key_match WHERE client_id='".$this->CLIENT_ID."')",null,false);
        $this->db->delete("competitor_match_daily");

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
        // $mongo      = new MongoClient(MONGO_CONNECTION);
        // $mongodb    = $mongo->blue_eye;
        // $collection = $mongodb->createCollection("Keyword");
        // $save       = array("keyword_id"=>new MongoInt32($keyword_id),"keyword_name"=>$post['keyword_name'],"client_id"=>new MongoInt32($this->CLIENT_ID),"3mcollect"=>new MongoInt32(0)); 
        // $collection->insert($save);
        // $mongo->close();
    }

    function delete_keyword_mongodb($keyword_id = 0)
    {
        // $mongo        = new MongoClient(MONGO_CONNECTION);
        // $mongodb      = $mongo->blue_eye;
        // $collection   = $mongodb->createCollection("Keyword");
        // $where_delete = array("keyword_id"=>new MongoInt32($keyword_id),"client_id"=>new MongoInt32($this->CLIENT_ID)); 
        // $collection->remove($where_delete);
        // $mongo->close();
    }

    function get_keyword_id($keyword_id = 0)
    {
        $rowsdata = $this->db
                    ->select("keyword.*")
                    ->where("keyword.client_id",$this->CLIENT_ID)
                    ->where("keyword.keyword_id",$keyword_id)
                    ->get("keyword")
                    ->first_row('array');

        return $rowsdata;
    }

    function save_keyword_setting($post = array())
    {
        $keyword_id = $post['keyword_id'];
        $rec = $this->db->where("keyword_id",$keyword_id)
                        ->get("keyword")
                        ->first_row("array");

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("keyword_id",$keyword_id);

        $save = array();
        $save['thai_only'] = (isset($post['thai_only']) && $post['thai_only']=='1') ? 1 : 0;
        $save['primary_keyword'] = (isset($post['primary_keyword']) && $post['primary_keyword']=='1') ? 1 : 0;
        $this->db->update("keyword",$save);

        return $rec['keyword_name'];
    }

    //add it
    function insert_link_url($post = array())
    {
        $save["url"]            = $post["url"];
        $save["msg_id"]         = $post["msg_id"];
        $save["createby"]       = $this->USER_ID;
        $save["status"]         = $post["status"];
        $save['created_date']   = date("Y-m-d H:i:s");
        $this->db->insert("link_url",$save);
        
    }

    function get_link_url($msg_id)
    {   $rowsdata = array();
        $rowsdata = $this->db
                    ->select("link_url.id")
                    ->where("link_url.msg_id",$msg_id)
                    ->get("link_url")
                    ->first_row('array');

        return $rowsdata;
    }

    function insert_fix_post_url($row_link_url)
    {
        $save["url"]            = $row_link_url;
        $save["client_id"]      = $this->CLIENT_ID;
        $save["createby"]       = $this->USER_ID;
        $save["status"]         = null;
        $save['created_date']   = date("Y-m-d H:i:s");
        $save['updated_date']   = null;
        $this->db->insert("fix_post_url",$save);    
    }

    function update_company_keyword_status()
    {
        // เก็บค่าที่เราจะลบไว้ก่อน เพื่อที่จะนำไปเก็บไว้ในส่วนของ log
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
            $this->update_keyword_status($keyword_id);
        }

        // update คอลัมน์ status ใน ตาราง group_keyword และตำแหน่งของ group_keyword ที่จะลบให้เป็น inactive 
        $this->db->set("status","inactive");
    	$this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->update("group_keyword");

        $this->db->set("status","inactive");
        $this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->update("company_keyword");

        return $rec['company_keyword_name'];
    }

    function update_group_keyword_status($group_keyword_id = 0)
    {
        // เก็บค่าที่เราจะลบไว้ก่อน เพื่อที่จะนำไปเก็บไว้ในส่วนของ log 
        $rec = $this->db->select("group_keyword_name")
                        ->where("group_keyword_id", $group_keyword_id)
                        ->get("group_keyword")
                        ->first_row("array");

        
        // เปลี่ยนสถานะของ categories ที่เกี่ยวข้อง
        $rowsdata = $this->db->select("categories_id")
                        ->where("group_keyword_id", $group_keyword_id)
                        ->where("client_id", $this->CLIENT_ID)
                        ->get("categories")
                        ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $categories_id = $v_row['categories_id'];
            $this->update_categories_status($categories_id);
        }

        // เปลี่ยนสถานะของ keyword ที่เกี่ยวข้อง
        $rowsdata = $this->db->select("keyword_id")
                             ->where("group_keyword_id", $group_keyword_id)
                             ->where("client_id", $this->CLIENT_ID)
                             ->get("keyword")
                             ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $keyword_id = $v_row['keyword_id'];
            $this->update_keyword_status($keyword_id);
        }

        // update คอลัมน์ status ใน ตาราง group_keyword และตำแหน่งของ group_keyword ที่จะลบให้เป็น inactive 
        $this->db->set("status","inactive");
    	$this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("group_keyword_id",$group_keyword_id);
        $this->db->update("group_keyword");

        return $rec['group_keyword_name'];
    }

    function update_categories_status($categories_id = 0)
    {
        // เก็บค่าที่เราจะลบไว้ก่อน เพื่อที่จะนำไปเก็บไว้ในส่วนของ log 
        $rec = $this->db->select("categories_name")
                        ->where("categories_id", $categories_id)
                        ->get("categories")
                        ->first_row("array");

        $rowsdata = $this->db->select("keyword_id")
                             ->where("categories_id", $categories_id)
                             ->where("client_id", $this->CLIENT_ID)
                             ->get("keyword")
                             ->result_array();

        foreach($rowsdata as $k_row=>$v_row)
        {
            $keyword_id = $v_row['keyword_id'];
            $this->update_keyword_status($keyword_id);
        }

        // update คอลัมน์ status ใน ตาราง group_keyword และตำแหน่งของ group_keyword ที่จะลบให้เป็น inactive 
        $this->db->set("status","inactive");
    	$this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("categories_id",$categories_id);
        $this->db->update("categories");

        return $rec['categories_name'];
    }

    function update_keyword_status($keyword_id = 0)
    {
        // เก็บค่าที่เราจะลบไว้ก่อน เพื่อที่จะนำไปเก็บไว้ในส่วนของ log 
        $rec = $this->db->select("keyword_name")
                        ->where("keyword_id",$keyword_id)
                        ->get("keyword")
                        ->first_row("array");

        // update คอลัมน์ status ใน ตาราง keyword และตำแหน่งของ keyword ที่จะลบให้เป็น inactive 
        $this->db->set("status","inactive");
    	$this->db->where("client_id",$this->CLIENT_ID);
    	$this->db->where("keyword_id",$keyword_id);
        $this->db->update("keyword");

        // $this->delete_keyword_mongodb($keyword_id);

        return $rec['keyword_name'];
    }

    function update_group_keyword_status_active($post = array())
    {
        $save = array();
        $save['status']          = 'active';

        // update คอลัมน์ status ใน ตาราง group_keyword
    	$this->db->where("client_id", $this->CLIENT_ID);
        $this->db->where("company_keyword_id", $post['company_keyword_id']);
    	$this->db->where("group_keyword_name", $post['group_keyword_name']);
        $this->db->update("group_keyword", $save);

        return $rec['group_keyword_name'];
    }

    // In case that user want to use keyword that has been delete before.
    function update_keyword_status_active($post = array())
    {
        // เก็บค่าที่เราจะลบไว้ก่อน เพื่อที่จะนำไปเก็บไว้ในส่วนของ log 
        // $rec = $this->db
        //     ->select("keyword_name")
        //     ->where("keyword_id",$keyword_id)
        //     ->get("keyword")
        //     ->first_row("array");

        $save = array();
        $save['thai_only']       = (isset($post['thai_only']) && $post['thai_only']=='1') ? 1 : 0;
        $save['primary_keyword'] = (isset($post['primary_keyword']) && $post['primary_keyword']=='1') ? 1 : 0;
        $save['status']          = 'active';

        // update คอลัมน์ status ใน ตาราง keyword และตำแหน่งของ keyword ที่จะลบให้เป็น inactive 
    	$this->db->where("client_id", $this->CLIENT_ID);
    	$this->db->where("keyword_name", $post['keyword_name']);
        $this->db->update("keyword", $save);

        return $rec['keyword_name'];
    }
    //
}
?>