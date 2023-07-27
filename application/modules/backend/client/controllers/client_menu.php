<?php
class Client_menu extends Backend
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("client_menu_model");
    }

    function index()
    {
        $post = $this->input->post();
        $menu = $this->client_menu_model->get_menu($post['client_id']);
        echo json_encode($menu);
    }

}