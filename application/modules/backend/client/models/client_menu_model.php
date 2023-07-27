<?php
class Client_menu_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }

    function get_menu($client_id = 0)
    {
        $menu = array();
        $result = array();
        $size = 3;

        for($i=0;$i<$size;$i++) {

            if($i==0) $parent_id = array(0);

            if(count($parent_id)>0) {
                $rows = $this->db
                        ->where_in("parent_id",$parent_id)
                        ->select("menu.*")
                        ->order_by("parent_order","asc")
                        ->get("menu")
                        ->result_array();

                $parent_id = array();

                foreach($rows as $key=>$val) {
                    $idx = $val['parent_id'];
                    array_push($parent_id,$val['menu_id']);
                    if(!isset($menu[$idx])) $menu[$idx] = array();
                    array_push($menu[$idx],$val);
                }
            }

        }

        $client_menu = $this->get_client_menu($client_id);
        if(isset($menu[0])) {
            foreach($menu[0] as $key=>$val) { 
                $idx1 = $val['menu_id'];
                $menu_check = (in_array($val['menu_id'],$client_menu)) ? 1 : 0;
                $data = array("menu_id"=>$val['menu_id'],"menu_name"=>$val['menu_name'],"menu_check"=>$menu_check,"menu_level"=>"1");
                array_push($result,$data);
                if(isset($menu[$idx1])) {
                    foreach($menu[$idx1] as $key1=>$val1) { 
                        $menu_check = (in_array($val1['menu_id'],$client_menu)) ? 1 : 0;
                        $data = array("menu_id"=>$val1['menu_id'],"menu_name"=>$val1['menu_name'],"menu_check"=>$menu_check,"menu_level"=>"2");
                        array_push($result,$data);
                    }
                }
            } 
        } 

        return $result;
    }


    function close_menu($client_id)
    {
        $post = $this->input->post();
        if(isset($post['menu_list'])) {
            $this->db->where("client_id",$client_id);
            $this->db->delete("client_menu");
            if(isset($post['menu_id'])) {
                foreach ($post['menu_id'] as $menu_id) {
                    $save = array();
                    $save["client_id"] = $client_id;
                    $save["menu_id"]   = $menu_id;
                    $this->db->insert("client_menu",$save);
                }
            }
        }
    }

    function get_client_menu($client_id) {
        $result = array();
        $rows = $this->db
                    ->where_in("client_id",$client_id)
                    ->get("client_menu")
                    ->result_array();
        foreach($rows as $val) {
            array_push($result,$val['menu_id']);
        }
        return  $result;
    }

}