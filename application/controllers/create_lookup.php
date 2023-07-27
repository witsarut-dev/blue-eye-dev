<?php
class Create_lookup extends MX_Controller {

    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata("USER_ID")) {
            show_404();
        }
    }

    function index()
    {
        $lookup_token = (string)$this->input->post("token");
        $refid = (string)$this->input->post("refid");
        $rec = $this->db->where("lookup_token",$lookup_token)->get("sys_lookup")->first_row('array');
        $data = array();
        if(isset($rec['lookup_query'])) {
            $query = $rec['lookup_query'];
            if($refid!="") $query = str_replace("{refid}",$refid,$query);
            $result = $this->db->query($query)->result_array();
            foreach($result as $val) {
                $label = @$val["Label"];
                $value = @$val["Value"];
                $id    = $this->input->post("id");
                $post  = $this->input->post("value");
                array_push($data,array("label"=>$label,"value"=>$value,"id"=>$id,"post"=>$post));
            }
        } else {
            show_404();
        }
        echo json_encode($data);
    }

}
?>