<?php
class menu_trigger extends Backend 
{
    function __construct()
    {
        parent::__construct();
    }

    function set_trigger($action = "",$id = 0,$data = array())
    {
    	$obj = array("status"=>true,"message"=>"");
		if($action=="save:after") {
			$CI =& get_instance();
            $CI->load->driver('cache');
            $CI->cache->file->delete('theme_title');
			$obj["status"] = true;
		} else {
			$obj["status"] = true;		
        }
		return $obj;
    }

}