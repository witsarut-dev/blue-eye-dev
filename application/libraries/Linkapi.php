<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LinkAPI {

	function __construct()
    {
		$CI =& get_instance();
		$CI->load->helper("common");
    }

    function get_link_source($link_url) 
	{
	    if(strpos($link_url,'https://www.facebook.com')!==false || strpos($link_url,'https://web.facebook.com')!==false) {
	        return 'facebook';
	    } else if(strpos($link_url,'https://twitter.com')!==false) {
	        return 'twitter';
	    } else if(strpos($link_url,'https://youtube.com')!==false) {
	        return 'youtube';
	    } else {
	        return 'nothing';
	    }
	}

	function get_link_msg_id($link_url, $addPage = false)
	{
	    $post_id  = 0;
	    $page_arr = explode('/',$link_url);
		rsort($page_arr);
		$page_id = $this->get_link_user_id($link_url,$page_name);
	    foreach ($page_arr as $value) {
	        if(is_numeric($value) && $value!=$page_id) {
	            $post_id = $value;
	        }
		}
		if($addPage) $this->add_page($page_id,$page_name);

	    return $page_id."_".$post_id;
	}

	function check_link_msg($link_url) 
	{
	    $source = $this->get_link_source($link_url);
	    if($source=="facebook" && $this->check_link_fb_type($link_url)) {
			$page_ = explode('/',$link_url);
			$page_name = $page_[3];
	        $msg_id  = $this->get_link_msg_id($link_url);
			$fb_access_token = get_fb_access_token();
			// แก้ไขโดย วิว 22-03-2021 $msg_id -> $page_name
			$url = "https://graph.facebook.com/".$page_name."?fields=&access_token=".$fb_access_token;
			//

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	        $output = json_decode(curl_exec($ch),true);
	        curl_close($ch);
	        if(!isset($output['id'])) {
				return false;
	        } else {
	        	return true;
	        }
			return true;
	    } else {
	    	return false;
	    }
	}

	function check_error_user($link_url,$link_type = 'user') 
	{
	    $source = $this->get_link_source($link_url);
	    if($source=="facebook") {
			$check_link = $this->check_link_fb_type($link_url);
			if(!$check_link) {
				$user_id = $this->get_link_user_id($link_url,$username);
				$fb_access_token = get_fb_access_token();
				$url = "https://graph.facebook.com/".$user_id."?fields=&access_token=".$fb_access_token;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				$output = json_decode(curl_exec($ch),true);
				curl_close($ch);
				// $code = ($link_type=='user') ? '110' : '100';
				// if(isset($output['error']['code']) && $output['error']['code']=='110') {
				if(isset($output['id'])) {
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
	    } else {
	    	return true;
	    }
	}

	function check_error_fanpage($link_url)
	{
		return $this->check_error_user($link_url,'page'); 
	}

    function check_link_fb_type($link_url) 
    {
        $types = explode('/',$link_url);
        $type = isset($types[4]) ? $types[4] : 'null';
        if(in_array($type,array("videos","posts","photos"))) {
            return true;
        } else {
            return false;
        }
    }

	function get_link_user_id($link_url,&$page_name) 
	{
		$fb_url = "https://www.facebook.com/profile.php?id=";
		if(strpos($link_url,$fb_url)!==false) {
			$user_id = str_replace($fb_url,"",$link_url);
		} else {
			$users = explode('/',$link_url);
			$user_id = isset($users[3]) ? $users[3] : 0;
		}
		$fb_access_token = get_fb_access_token();
		// แก้ไขโดย วิว 27-04-2021 fields=name,username -> fields=name
	    $url = "https://graph.facebook.com/".$user_id."?fields=name&access_token=".$fb_access_token;
		//
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    $output = json_decode(curl_exec($ch),true);
	    curl_close($ch);
		
		$id = isset($output['id']) ? $output['id'] : "";
		// แก้ไขโดย วิว 10-05-2021 ['username'] -> ['name']
		$page_name = isset($output['name']) ? $output['name'] : "";
		//
		$page_name = ($page_name=="") ? @$output['name'] : $page_name;
	    return $id;
	}

	function get_link_fanpage_id($link_url,&$page_name)
	{
		return $this->get_link_user_id($link_url,$page_name); 
	}

    function get_link_fb_type($link_url) 
    {
        $types = explode('/',$link_url);
        $type = isset($types[4]) ? $types[4] : 0;
        return $type;
    }

	function get_link_icon_tag($icon)
	{
	    $result = "";
	    if(gettype($icon)=='array') {
	        $icons = $icon;
	    } else {
	        $icons[0] = $icon;
	    }
	    foreach ($icons as $key => $val) {
	        $icon_name = $this->get_link_icon_name($val);
	        $result .= '<i class="'.$icon_name.'"></i> ';
	    }
	    return trim($result);
	}

	function get_link_icon_name($icon)
	{
		$icon_name = "";
	    switch ($icon) {
	        case 'like': $icon_name = "icon-like";break;
	        case 'share': $icon_name = "icon-share";break;
			case 'comment': $icon_name = "icon-bubble";break;
			case 'likeposts': $icon_name = "icon-like";break;
	        case 'shareposts': $icon_name = "icon-share";break;
			case 'commentposts': $icon_name = "icon-bubble";break;
			case 'groups': $icon_name = "icon-grid";break;
	        case 'friends': $icon_name = "icon-people";break;
			case 'pages': $icon_name = "icon-doc";break;
			case 'games': $icon_name = "icon-game-controller";break;
			case 'musics': $icon_name = "icon-music-tone-alt";break;
			case 'movies': $icon_name = "icon-film";break;
	        case 'televisions': $icon_name = "icon-screen-desktop";break;
		}
		return $icon_name;
	}

	function add_page($page_id,$page_name) {
		$fb_access_token = get_fb_access_token();
		$url = "https://graph.facebook.com/".$page_id."?fields=is_shared_login&access_token=".$fb_access_token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$output = json_decode(curl_exec($ch),true);
		if(isset($output['error']['code']) && $output['error']['code']=='100') {

			$CI =& get_instance();
			$num_rows = $CI->db
					->where("page_id",$page_id)
					->where("sys_status","active")
					->get("pages")
					->num_rows();

			if($num_rows==0) {
				$save["page_id"]    = $page_id;
				$save["page_name"]  = $page_name;
				$save["page_type"]  = 'facebook';
				$save["createdate"] = date("Y-m-d H:i:s"); 
				$save["lastupdate"] = date("Y-m-d H:i:s"); 
				$save["createby"]   = 0;
				$save["updateby"]   = 0;
				$save["sys_action"] = "created";
				$save["sys_status"] = "active";
				$CI->db->insert("pages",$save);
			}

		}
	}

}