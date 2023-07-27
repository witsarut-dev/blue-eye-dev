<?php
class Keyword_model extends CI_Model {

    var $CLIENT_ID;

    function __construct()
    {
        parent::__construct();
        $this->CLIENT_ID = $this->authen->getId();
    }

    function get_company_keyword($company_keyword_type = "")
    {
        if($company_keyword_type!="") $this->db->where("company.company_keyword_type",$company_keyword_type);
        $rowsdata = $this->db
                    ->select("company.*")
                    ->where("company.client_id",$this->CLIENT_ID)
                    ->order_by("company.company_keyword_id","ASC")
                    ->get("company_keyword company")
                    ->result_array();

        return $rowsdata;
    }

    function get_company_keyword_id($client_id = 0)
    {
        if($client_id!="" && $client_id!=0) {
            $this->db->where("company.client_id",$client_id);
        } else {
            $this->db->where("company.client_id",$this->CLIENT_ID);
        }

        $result = array();
        $rec = $this->db
                    ->select("company.company_keyword_id")
                    ->where("company.company_keyword_type","Company")
                    ->join("company_keyword company","company.company_keyword_id = keyword.company_keyword_id")
                    ->get("keyword")
                    ->first_row('array');

        return isset($rec['company_keyword_id']) ? $rec['company_keyword_id'] : 0;
    }
    
    function get_keyword($client_id = 0)
    {
        $company_keyword_id = $this->get_company_keyword_id($client_id);
        $result = array();
        $rowsdata = $this->db
                    ->select("keyword.keyword_id,keyword.keyword_name")
                    ->where("keyword.company_keyword_id",$company_keyword_id)
                    ->get("keyword")
                    ->result_array();

        return $rowsdata;
    }

    function get_client_keyword($client_id = 0)
    {
        $result = array();
        $company_keyword_id = $this->get_company_keyword_id($client_id);
        $result = array();
        $rowsdata = $this->db
                    ->select("c_keyword.keyword_id")
                    ->where("c_keyword.company_keyword_id",$company_keyword_id)
                    ->get("client_keyword c_keyword")
                    ->result_array();

        foreach($rowsdata as $k_row=>$v_row) {
            array_push($result,$v_row['keyword_id']);
        }

        return $result;
    }

    function insert_client_keyword($post = array())
    {
        $save_batch = array();
        $company_keyword_id = $this->get_company_keyword_id();
  
        if(isset($post['keyword_id'])) {
            foreach($post['keyword_id'] as $k_row=>$v_row) {
                $save = array();
                $save["client_id"] = $this->CLIENT_ID;
                $save["keyword_id"] = $v_row;
                $save["company_keyword_id"] = $company_keyword_id;
                array_push($save_batch,$save);
            }
        }

        $this->db->where("client_id",$this->CLIENT_ID);
        $this->db->where("company_keyword_id",$company_keyword_id);
        $this->db->delete("client_keyword");

        if(count($save_batch)>0) $this->db->insert_batch("client_keyword",$save_batch);
    }
}
?>