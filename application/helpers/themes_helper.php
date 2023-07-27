<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

if (!function_exists('theme_url')) {

    function theme_url($theme='default')
    {
        return base_url().'themes/'.$theme;
    }

}

if (!function_exists('theme_assets_url')) {

	function theme_assets_url($theme='default')
	{
	    return base_url().'themes/'.$theme.'/assets/';
	}

}

if (!function_exists('theme_menu')) {

	function theme_menu($page='',$class = '')
	{
		$CI =& get_instance();
		return (strtolower($CI->uri->segment(1))==strtolower($page)) ? 'active'.$class : '';
	}
}


if (!function_exists('theme_title')) {

	function theme_title($module='')
	{
        $CI =& get_instance();
        $CI->load->driver('cache');
        if (!$cache = $CI->cache->file->get('theme_title'))
        {
            $cache = array();
            $rows = $CI->db
                    ->select("menu_title,menu_link")
                    ->get("menu")
                    ->result_array();
            foreach ($rows as $key => $val) {
                $cache[$val['menu_link']] = $val['menu_title'];
            }

            $CI->cache->file->save('theme_title', $cache, 30000);
        }
        return isset($cache[$module]) ? $cache[$module] : '';
	}

}