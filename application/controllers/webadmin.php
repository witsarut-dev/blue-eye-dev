<?php
class Webadmin extends MX_Controller {

	function __construct()
    {
        parent::__construct();
    }

	function index()
	{
		redirect(site_url("authen"));
	}

}
?>