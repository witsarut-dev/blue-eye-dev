<?php
class config_trigger extends Backend 
{
    function __construct()
    {
        parent::__construct();
    }

    function set_trigger($action = "",$id = 0,$data = array())
    {
    	$obj = array("status"=>true,"message"=>"");
		if($action=="") {
			//code trigger
			$obj["status"] = true;
		} 
		return $obj;
    }

}