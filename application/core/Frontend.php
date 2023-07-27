<?php

class Frontend extends MX_Controller{

    function __construct()
    {
        parent::__construct();

        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $this->load->helper(array("themes","db_pagination","common","cookie"));
        $this->load->library(array("template","authen"));
        $this->load->model("master_model");
        $keywords = $this->master_model->get_keyword_list();
        $menu = $this->master_model->get_menu();

        $data = array('theme' => 'default','keywords'=>$keywords,"menu"=>$menu);
        $this->template->set_partial('header','partials/web-header',$data);
        $this->template->set_partial('menu','partials/web-menu',$data);
        $this->template->set_partial('footer','partials/web-footer',$data);
        $this->template->set_partial('script','partials/web-script',$data);
        $this->template->title('Blue Eye');
    }

} 