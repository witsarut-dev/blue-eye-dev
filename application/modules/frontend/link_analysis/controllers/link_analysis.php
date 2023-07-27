<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Link_analysis extends Frontend {

	var $module = "link_analysis";
    var $max_link = 3;
    var $color_link = array("#46629e","#4aa23c","#f33923");
    var $fb_link = "https://www.facebook.com/";
    var $user_activity = array('likeposts','shareposts','commentposts','groups','games','pages','friends','movies','televisions','musics');
    var $count_activity = array("likeposts","shareposts","commentposts");
    var $fanpage_activity = array('shares','comments');

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("link_analysis_model");
		$this->load->model("setting/setting_model");
		$this->load->model("overview/overview_model");
        $this->load->library("linkapi");
        ini_set('memory_limit', '2048M');
	}

	function index()
	{	
		$this->view();
	}

	function view($link_id = 0)
	{	
        $post   = $this->input->post();
        $rec = $this->link_analysis_model->get_link_id($link_id);

		$viewdata = array();
		$viewdata['module']  = $this->module;
        $viewdata['client_link'] = $this->link_analysis_model->get_client_link();
        $viewdata['max_link'] = $this->max_link;
        $viewdata['color_link'] = $this->color_link;
        $viewdata['link_id'] = $link_id;
        if(isset($rec['link_type'])) {
            $link_type = $rec['link_type'];
        } else {
            $link_type = ($this->input->get("link_type")) ? $this->input->get("link_type") : 'user';
        }
        $viewdata['client_link'] = $this->link_analysis_model->get_client_link($link_type);
        $viewdata['link_type'] = $link_type;
		$this->template->set('inline_style', $this->load->view("link_analysis_style",null,true));
		$this->template->set('inline_script', $this->load->view("link_analysis_script",$viewdata,true));
		$this->template->build("link_analysis_view",$viewdata);
	}

    function open_link()
    {
        $post = $this->input->post();
        if($post) {
            $link_id = $post['link_id'];
            $rec = $this->link_analysis_model->get_link_id($link_id);
            if(!isset($rec['link_id'])) {
                $result["message"] = "ไม่พบข้อมูล Link ที่ต้องการ";
                $result["status"]  = false;
            } else {
                $link_id = $rec['link_id'];
                $link_list = $this->link_analysis_model->get_list($link_id);
                $reload = $this->check_reload_status($link_list);
                if($rec['link_type']=="user") {
                    $result = $this->get_node_user($link_id,$post);
                } else if($rec['link_type']=="fanpage") {
                    $result = $this->get_node_fanpage($link_id,$post);
                } else {
                    $result = $this->get_node_page($link_id,$post);
                }
                $result['link_id'] = $rec['link_id'];
                $result['link_name'] = $rec['link_name'];
                $result["link_list"] = $link_list;
                $result['reload'] = $reload;
                $result["status"] = true;
            }
            echo json_encode($result);
        }
    }

    function get_users()
    {
        $post = $this->input->get();
        if($post) {
            $msg_id = $post['msg_id'];
            $msg_type = $post['msg_type'];

            $viewdata = array();
            $users = array();
            $like = array();
            $share = array();
            $comment = array();

            $rec = $this->link_analysis_model->get_mongo_page($msg_id);
            if(isset($rec[0]['likes']) && count($rec[0]['likes'])>0) $users['like'] = $rec[0]['likes'];
            if(isset($rec[0]['shares']) && count($rec[0]['shares'])>0) $users['share'] = $rec[0]['shares'];
            if(isset($rec[0]['comments']) && count($rec[0]['comments'])>0) $users['comment'] = $rec[0]['comments'];

            $viewdata['users'] = $users;
            $viewdata['msg_id'] = $msg_id;
            $viewdata['msg_type'] = $msg_type;
            $this->load->view("user_list_view",$viewdata);
        }
    }

    function get_user_list()
    {
        $post = $this->input->post();
        $users = array();
        if($post) {
            $msg_id = $post['msg_id'];
            $user_type = $post['user_type'];
            $post_rows = $post['post_rows'];
            $end_rows  = $post_rows * 21;
            $start_rows = ($end_rows - 21);
            $rec = $this->link_analysis_model->get_mongo_page($msg_id);
            switch ($user_type) {
                case 'like': $type = "likes"; break;
                case 'share': $type = "shares"; break;
                case 'comment': $type = "comments"; break;
                default: $type = ""; break;
            }
            if(isset($rec[0][$type])) {
                $result = $rec[0][$type];
                foreach($result as $key=>$val) {
                    if($key >= $start_rows && $key < $end_rows) {
                        $data = array();
                        $data['id'] = $val[$type.'id'];
                        $data['name'] = $val[$type.'name'];
                        $data['pic'] = $val[$type.'pic'];
                        $data['url'] = $this->fb_link.$data['id'];
                        $data['icon'] = $this->linkapi->get_link_icon_tag($user_type);
                        array_push($users,$data);
                    }
                }
            }
        }
        echo json_encode($users);
    }

    function get_relates($link_id = 0)
    {
        $post = $this->input->get();
        if($post) {
            $viewdata = array();
            $viewdata['k_nodes'] = $post['k_nodes'];
            $viewdata['link_id'] = $link_id;
            $this->load->view("relate_list_view",$viewdata);
        }
    }

    function get_relate_list()
    {
        $post = $this->input->post();
        $users = array();
        if($post) {
            $post_rows = $post['post_rows'];
            $end_rows  = $post_rows * 21;
            $start_rows = ($end_rows - 21);
            $user_id = array();
            $link_id = $post['link_id'];
            $k_nodes = $post['k_nodes'];

            $result = $this->link_analysis_model->get_list($link_id);
            $relate = $this->get_users_relate($result);
            $result = $relate['result'];
            $user_relate = $relate['user_relate'];
            $user_profile = $relate['user_profile'];
            $msg_arr = $relate['msg_arr'];
            $icons = $relate['icons'];

            $user_all = array();
          
            foreach ($user_profile as $u_id=>$u_val) {
                $icon = $this->linkapi->get_link_icon_tag($icons[$u_id]);
                $nodes_from = "";
                $count_relate = 0;
                $users_tmp = array();

                foreach ($user_relate[$u_id] as $msg_id=>$activitys) {
                    foreach($activitys as $u_activity) {
                        $nodes_from .= $msg_id."_".$u_activity."_";
                        $users_tmp[$u_id] = $u_val;
                        $users_tmp[$u_id]['url'] = $this->fb_link.$u_id;
                        $users_tmp[$u_id]['icon'] = $icon;
                        $count_relate++;
                    }
                }

                if($count_relate>1) {
                    $nodes_from = rtrim($nodes_from,"_");
                    if($nodes_from==$k_nodes) {
                        foreach ($users_tmp as $u_id => $val) {
                            if(!in_array($u_id,$user_id)) {
                                array_push($user_all,$val);
                                array_push($user_id,$u_id);
                            }
                        }
                    }
                }
            }

            foreach($user_all as $key=>$val) {
                if($key >= $start_rows && $key < $end_rows) {
                    array_push($users,$val);
                }
            }

        }
        echo json_encode($users);
    }

    function get_activitys()
    {
        $post = $this->input->get();
        if($post) {
            $msg_id = $post['msg_id'];
            $msg_type = $post['msg_type'];

            $viewdata = array();
            $users = array();

            $viewdata['msg_id'] = $msg_id;
            $viewdata['msg_type'] = $msg_type;
            $this->load->view("activity_list_view",$viewdata);
        }
    }

    function get_activity_list()
    {
        $post = $this->input->post();
        $users = array();
        if($post) {
            $msg_id = $post['msg_id'];
            $type = $post['msg_type'];
            $post_rows = $post['post_rows'];
            $end_rows  = $post_rows * 21;
            $start_rows = ($end_rows - 21);
            $rec = $this->link_analysis_model->get_mongo_user($msg_id);
            if(isset($rec[0][$type])) {
                $result = $rec[0][$type];
                if(in_array($type,$this->count_activity)) {
                    $result = array_sort($result, $type.'count', SORT_DESC);
                }
                foreach($result as $key=>$val) {
                    if($key >= $start_rows && $key < $end_rows) {
                        $data = array();
                        $data['id'] = $val[$type.'id'];
                        $data['name'] = $val[$type.'name'];
                        $data['pic'] = $val[$type.'pic'];
                        $data['url'] = $this->fb_link.$data['id'];
                        $data['icon'] = $this->linkapi->get_link_icon_tag($type);
                        if(isset($val[$type.'count'])) {
                            $data['icon'] .= " ".number_format($val[$type.'count']);
                        }
                        array_push($users,$data);
                    }
                }
            }
        }
        echo json_encode($users);
    }

    function cmdAddLink()
    {
        $result = array();
        $post = $this->input->post();

        $message = "";

        if(!isset($post['link_name']) || trim($post['link_name'])=="") {
            $result["message"] = "กรุณากรอก Link Name";
            $result["status"]  = false;
            $result['error']   = "link_name";
        } else if($this->link_analysis_model->check_link_name($post)) {
            $result["message"] = "ขออภัยคุณมี Link Name นี้แล้ว";
            $result["status"]  = false;
            $result['error']   = "link_name";
        } else if(!isset($post['link_url'][1]) || trim($post['link_url'][1])=="") {
            $result["message"] = "กรุณากรอก Link 1";
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->check_link_unique($post,$message)) {
            $result["message"] = "คุณกรอก Link ซ้ำกัน ".$message;
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->check_link_post($post,$message)) {
            $result["message"] = "ไม่พบข้อมูล Link หรือ Link ไม่ถูกต้อง ".$message;
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->link_analysis_model->check_link_max($post['link_type'],$add_link_analysis) && @$post['link_id']=="") {
            $result["message"] = "ขออภัยคุณเพิ่มข้อมูลเกิน ".$add_link_analysis." ครั้งแล้ว<br />คุณจะสามารถเพิ่มข้อมูลได้อีกครั้งในเดือนถัดไป";
            $result["status"]  = false;
            $result['error']   = "link_max";
        } else {
            if(!isset($post['link_id']) || $post['link_id']=="") {
                $link_id = $this->link_analysis_model->insert_link($post);
                $this->master_model->save_log("Add Link Analysis ".$post['link_name']);
                $action = "Add";
            } else {
                $link_id = $this->link_analysis_model->update_link($post);
                $this->master_model->save_log("Edit Link Analysis ".$post['link_name']);
                $action = "Edit";
            }
            
            $link_list = $this->link_analysis_model->get_list($link_id);
            $reload = $this->check_reload_status($link_list);
            $result = $this->get_node_page($link_id,$post);

            $result['action'] = $action;
            $result['reload'] = $reload;
            $result['link_id'] = $link_id;
            $result['link_name'] = $post['link_name'];
            $result["status"]  = true;
        }
        echo json_encode($result);
    }

    function cmdAddUser()
    {
        $result = array();
        $post = $this->input->post();
        $pos = strpos($post['link_url'][1],"%3A");
        if($pos !== false){
            preg_match('/%3A(.*?)%3A/', $post['link_url'][1] , $match);
            $post['link_url'][1] = 'https://www.facebook.com/'.$match[1];
        }
        $message = "";

        if(!isset($post['link_url'][1]) || trim($post['link_url'][1])=="") {
            $result["message"] = "กรุณากรอก User Url";
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->linkapi->check_error_user($post['link_url'][1])) {
            $result["message"] = "ไม่พบข้อมูล User หรือ User ไม่ถูกต้อง";
            // $result["message"] = $this->linkapi->print_check($post['link_url'][1]);
            // $result["message"] = json_encode($post);
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->link_analysis_model->check_user_url($post)) {
            $result["message"] = "ขออภัยคุณมี User Url นี้แล้ว";
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->link_analysis_model->check_link_max($post['link_type'],$add_link_analysis) && @$post['link_id']=="") {
            $result["message"] = "ขออภัยคุณเพิ่มข้อมูลเกิน ".$add_link_analysis." ครั้งแล้ว<br />คุณจะสามารถเพิ่มข้อมูลได้อีกครั้งในเดือนถัดไป";
            $result["status"]  = false;
            $result['error']   = "link_max";
        } else {
            $user_id = $this->linkapi->get_link_user_id($post['link_url'][1],$post['link_name']);
            if(!isset($post['link_id']) || $post['link_id']=="") {
                $link_id = $this->link_analysis_model->insert_user($post);
                $this->master_model->save_log("Add Link Analysis ".$post['link_name']);
                $action = "Add";
            } else {
                $link_id = $this->link_analysis_model->update_user($post);
                $this->master_model->save_log("Edit Link Analysis ".$post['link_name']);
                $action = "Edit";
            }

            $link_list = $this->link_analysis_model->get_list($link_id);
            $reload = $this->check_reload_status($link_list);
            $result = $this->get_node_user($link_id,$post);

            $result['action'] = $action;
            $result['reload'] = $reload;
            $result['link_id'] = $link_id;
            $result['link_name'] = $post['link_name'];
            $result["status"]  = true;
        }
        echo json_encode($result);
    }

    function cmdAddFanPage()
    {
        $result = array();
        $post = $this->input->post();
        $message = "";

        if(!isset($post['link_url'][1]) || trim($post['link_url'][1])=="") {
            $result["message"] = "กรุณากรอก Page Url";
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->linkapi->check_error_fanpage($post['link_url'][1])) {
            $result["message"] = "ไม่พบข้อมูล Page หรือ Page ไม่ถูกต้อง";
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->link_analysis_model->check_user_url($post)) {
            $result["message"] = "ขออภัยคุณมี Page Url นี้แล้ว";
            $result["status"]  = false;
            $result['error']   = "link_url";
        } else if($this->link_analysis_model->check_link_max($post['link_type'],$add_link_analysis) && @$post['link_id']=="") {
            $result["message"] = "ขออภัยคุณเพิ่มข้อมูลเกิน ".$add_link_analysis." ครั้งแล้ว<br />คุณจะสามารถเพิ่มข้อมูลได้อีกครั้งในเดือนถัดไป";
            $result["status"]  = false;
            $result['error']   = "link_max";
        } else {
            $page_id = $this->linkapi->get_link_fanpage_id($post['link_url'][1],$post['link_name']);
            if(!isset($post['link_id']) || $post['link_id']=="") {
                $link_id = $this->link_analysis_model->insert_fanpage($post);
                $this->master_model->save_log("Add Link Analysis ".$post['link_name']);
                $action = "Add";
            } else {
                $link_id = $this->link_analysis_model->update_fanpage($post);
                $this->master_model->save_log("Edit Link Analysis ".$post['link_name']);
                $action = "Edit";
            }

            $link_list = $this->link_analysis_model->get_list($link_id);
            $reload = $this->check_reload_status($link_list);
            $result = $this->get_node_user($link_id,$post);

            $result['action'] = $action;
            $result['reload'] = $reload;
            $result['link_id'] = $link_id;
            $result['link_name'] = $post['link_name'];
            $result["status"]  = true;
        }
        echo json_encode($result);
    }

    function cmdDelLink()
    {
        $result = array();
        $post = $this->input->post();
        if(isset($post['link_id'])) {
            $result["status"]  = true;
            $name = $this->link_analysis_model->delete_link($post['link_id']);
            $this->master_model->save_log("Delete Link Analysis ".$name);
        } else {
            $result["message"] = "กรุณาเลือก Link ที่ต้องการลบ";
            $result["status"]  = false;
        }
        echo json_encode($result);
    }

    private function check_link_post($post = array(),&$message = "")
    {
        $error = false;
        if(isset($post['link_url'])) {
            foreach($post['link_url'] as $link_url) {
                if(trim($link_url)!="") {
                    $result = $this->linkapi->check_link_msg($link_url);
                    if(!$result) {
                        $error = true;
                        $message = $link_url;
                        break;
                    }
                }
            }
        } else {
            $error = true;
        }
        return $error;
    }

    private function check_link_unique($post = array(),&$message = "")
    {
        $error = false;
        if(isset($post['link_url'])) {
            $link_arr = array();
            foreach($post['link_url'] as $link_url) {
                if(trim($link_url)!="") {
                    if(in_array($link_url,$link_arr)) {
                        $error = true;
                        $message = $link_url;
                        break;
                    }
                    array_push($link_arr,$link_url);
                }
            }
        } else {
            $error = true;
        }
        return $error;
    }
    

    private function check_reload_status($link_list = array()) {
        $isReload = false;
        foreach ($link_list as $key => $val) {
            if($val['link_status']==='0') {
                $isReload = true;
                break;
            }
        }
        return $isReload;
    }

    private function get_node_page($link_id = 0,$post = array())
    {
        $result = $this->link_analysis_model->get_list($link_id);
        $nodes = array();
        $edges = array();

        $relate = $this->get_users_relate($result);
        $result = $relate['result'];
        $user_relate = $relate['user_relate'];
        $user_profile = $relate['user_profile'];
        $msg_arr = $relate['msg_arr'];
        $icons = $relate['icons'];

        $nodes_relate = array();
        if(isset($post['relate_all']) || isset($post['link_relate'])) {
            foreach ($user_profile as $u_id=>$u_val) {
                $icon = $this->linkapi->get_link_icon_tag($icons[$u_id]);
                $nodes_from = "";
                $count_relate = 0;
                $edges_tmp = array();
                $users_tmp = array();

                #Node Users relate
                foreach ($user_relate[$u_id] as $msg_id=>$activitys) {
                    foreach($activitys as $u_activity) {
                        $nodes_from .= $msg_id."_".$u_activity."_";
                        if(!isset($edges_tmp[$msg_id])) $edges_tmp[$msg_id] = array();
                        array_push($edges_tmp[$msg_id],$u_activity);
                        $users_tmp[$u_id] = $u_val;
                        $count_relate++;
                    }
                }

                if($count_relate>1) {
                    if((isset($post['link_relate']) && in_array($count_relate,$post['link_relate'])) || isset($post['relate_all'])) {
                        $nodes_from = rtrim($nodes_from,"_");
                        if(!isset($nodes_relate[$count_relate][$nodes_from]['edges'])) $nodes_relate[$count_relate][$nodes_from]['edges'] = $edges_tmp;
                        if(!isset($nodes_relate[$count_relate][$nodes_from]['users'])) $nodes_relate[$count_relate][$nodes_from]['users'] = array();
                        $nodes_relate[$count_relate][$nodes_from]['users'] += $users_tmp;
                    }
                }
            }
        }

        foreach ($nodes_relate as $count => $nodes_from) {
            foreach ($nodes_from as $k_nodes=>$v_nodes) {

                $label = "<b>".$count." Relationship</b>\n".count($v_nodes['users']);
                $url = site_url("link_analysis/get_relates/{$link_id}/?k_nodes=".$k_nodes);
                array_push($nodes,array("id" => $k_nodes, "label" => $label, "margin"=>10, "shape" => 'ellipse', "size" => 20, "font"=>array("size"=>14,"multi"=>true), "url" => $url, "type" => "PopUp"));
                foreach ($v_nodes['edges'] as $msg_id=>$v_edges) {
                    foreach ($v_edges as $u_activity) {
                        $to = $k_nodes;
                        $from = $msg_id."_".$u_activity;
                        array_push($edges,array("from"=> $from,"to"=> $to));
                    }
                }
                $rows = 1;
                foreach ($v_nodes['users'] as $u_id=>$u_val) {
                    if($rows<=10) {
                        $to = $u_id;
                        $from = $k_nodes;
                        $icon = $this->linkapi->get_link_icon_tag($icons[$u_id]);
                        $url = $this->fb_link.$u_id;
                        array_push($nodes,array("id" => $u_id, "title" => $icon, "label" => $u_val['name'], "shape" => 'circularImage', "size" => 20, "image" => $u_val['pic'], "url" => $url,"type" => "LinkUrl"));
                        array_push($edges,array("from"=> $from,"to"=> $to));
                    } else {
                        break;
                    }
                    $rows++;
                }
            }
        }
        
        foreach($result as $key=>$val) {
            $msg_id = $val['msg_id'];
            $from = $msg_id;

            #Node Link facebook
            $url = $val['link_url'];
            array_push($nodes,array("id" => $from, "label" => "<b>f</b> ".$val['link_no'], "widthConstraint" => 100,"heightConstraint"=>100, "title" => '<span style="color:#ccc;font-size:12px"">'.$val['link_url'].'</span>', "shape" => 'circle', "font"=>array("size"=>35,"multi"=>true) , "color"=>array("background"=>$this->color_link[$key],"border"=>"#FFFFFF") , "borderWidth" => 3, "url" => $url, "type" => "LinkUrl"));
            //if($key>0) array_push($edges,array("from" => $result[$key]['msg_id'],"to" => $result[$key-1]['msg_id']));

            if(count($val['count'])>0) {
                foreach($val['count'] as $key2=>$val2) {
                    #Node likes shares comments
                    $label = "<b>".strtoupper($key2)." </b>\n".number_format($val2);
                    $url = site_url("link_analysis/get_users?msg_id=".$msg_id."&msg_type=".$key2);
                    $to = $msg_id."_".$key2;
                    $group = "G".$key;
                    array_push($nodes,array("group"=>$group, "id" => $to, "label" => $label, "widthConstraint" => 80,"heightConstraint"=>80, "shape" => 'circle', "font"=>array("size"=>12,"multi"=>true),"borderWidth" => 1, "url" => $url, "type" => "PopUp"));
                    array_push($edges,array("from"=> $from,"to"=> $to));
                }
            }
        }

        return array("nodes"=>$nodes,"edges"=>$edges);
    }

    private function get_node_user($link_id = 0,$post = array())
    {
        $result = $this->link_analysis_model->get_list($link_id);
        $nodes = array();
        $edges = array();
        $activity = array();

        if(isset($post['user_activity'])) {
            $activity = $post['user_activity'];
        } else if(isset($post['activity_all'])) {
            $activity = $this->user_activity;
        }

        $relate = $this->get_users_activity($result,$activity);
        $result = $relate['result'];
        $user_profile = $relate['user_profile'];

        foreach($result as $key=>$val) {
            $msg_id = $val['msg_id'];
            $from = $msg_id;
            #Node User facebook
            $url = $val['link_url'];
            $fb_access_token = get_fb_access_token();
            $image = "https://graph.facebook.com/".$msg_id."/picture?width=200&access_token=".$fb_access_token;
            array_push($nodes,array("id" => $from, "label" => "", "shape" => 'circularImage', "size" => 50, "image" => $image,"color"=>array("border"=>"#FFFFFF") , "borderWidth" => 3, "url" => "", "type" => ""));

            if(count($user_profile)>0) {
                foreach($user_profile as $key2=>$val2) {
                    #Node likes shares comments

                    $count_total = "";
                    if(in_array($key2,array("likeposts","shareposts","commentposts"))) {
                        $count_total = 0;
                        foreach ($val2 as $key3=>$val3) {
                            $count_total += intval(@$val3['count']);
                        }
                        $count_total = " / ".number_format($count_total);
                    }

                    $name2  = str_replace("post","",$key2);
                    $count2 = number_format(count($val2));
                    $label  = "<b>".strtoupper($name2)."</b>\n".$count2.$count_total;
                    $url = site_url("link_analysis/get_activitys?msg_id=".$msg_id."&msg_type=".$key2);
                    $to2 = $msg_id."_".$key2;
                    $icon = $this->linkapi->get_link_icon_tag($key2);
                    array_push($nodes,array("id" => $to2, "label" => $label, "margin"=>20, "font"=>array("size"=>14,"multi"=>true),"borderWidth" => 1, "url" => $url, "type" => "PopUp"));
                    array_push($edges,array("from"=> $from,"to"=> $to2));
                    $rows = 1;
                    foreach ($val2 as $key3=>$val3) {
                        if($rows<=10) {
                            $name3  = mb_substr($val3['name'],0,15);
                            $count3 = ($val3['count']>0) ? number_format($val3['count']) : null;
                            $label  = $name3."\n".$count3;
                            $to3    = $key2."_".$val3['id'];
                            $from2  = $to2;
                            $url    = $this->fb_link.$to3;
                            array_push($nodes,array("id" => $to3, "title" => $icon.'<span style="color:#ccc;font-size:12px"> '.$count3.'</span>', "label" => $label, "shape" => 'circularImage', "size" => 20, "image" => $val3['pic'], "url" => $url,"type" => "LinkUrl"));
                            array_push($edges,array("from"=> $from2,"to"=> $to3));
                        } else {
                            break;
                        }
                        $rows++;
                    }
                }
            }
        }

        return array("nodes"=>$nodes,"edges"=>$edges);
    }

    private function get_node_fanpage($link_id = 0,$post = array())
    {
        $result = $this->link_analysis_model->get_list($link_id);
        $nodes = array();
        $edges = array();
        $activity = array();

        if(isset($post['user_activity'])) {
            $activity = $post['user_activity'];
        } else if(isset($post['activity_all'])) {
            $activity = $this->fanpage_activity;
        }

        $relate = $this->get_fanpage_activity($result,$activity);
        $result = $relate['result'];
        $user_profile = $relate['user_profile'];

        foreach($result as $key=>$val) {
            $msg_id = $val['msg_id'];
            $from = $msg_id;

            #Node User facebook
            $url = $val['link_url'];
            $image = "https://graph.facebook.com/".$msg_id."/picture?width=200";
            array_push($nodes,array("id" => $from, "label" => "", "shape" => 'circularImage', "size" => 50, "image" => $image,"color"=>array("border"=>"#FFFFFF") , "borderWidth" => 3, "url" => "", "type" => ""));

            if(count($user_profile)>0) {
                foreach($user_profile as $key2=>$val2) {
                    #Node likes shares comments

                    $count_total = "";
                    if(in_array($key2,array("likeposts","shareposts","commentposts"))) {
                        $count_total = 0;
                        foreach ($val2 as $key3=>$val3) {
                            $count_total += intval(@$val3['count']);
                        }
                        $count_total = " / ".number_format($count_total);
                    }

                    $name2  = str_replace("post","",$key2);
                    $count2 = number_format(count($val2));
                    $label  = "<b>".strtoupper($name2)."</b>\n".$count2.$count_total;
                    $url = site_url("link_analysis/get_activitys?msg_id=".$msg_id."&msg_type=".$key2."&link_type=fanpage");
                    $to2 = $msg_id."_".$key2;
                    $icon = $this->linkapi->get_link_icon_tag($key2);
                    array_push($nodes,array("id" => $to2, "label" => $label, "margin"=>20, "font"=>array("size"=>14,"multi"=>true),"borderWidth" => 1, "url" => $url, "type" => "PopUp"));
                    array_push($edges,array("from"=> $from,"to"=> $to2));
                    $rows = 1;
                    foreach ($val2 as $key3=>$val3) {
                        if($rows<=10) {
                            $name3  = mb_substr($val3['name'],0,15);
                            $count3 = ($val3['count']>0) ? number_format($val3['count']) : null;
                            $label  = $name3."\n".$count3;
                            $to3    = $key2."_".$val3['id'];
                            $from2  = $to2;
                            $url    = $this->fb_link.$to3;
                            array_push($nodes,array("id" => $to3, "title" => $icon.'<span style="color:#ccc;font-size:12px"> '.$count3.'</span>', "label" => $label, "shape" => 'circularImage', "size" => 20, "image" => $val3['pic'], "url" => $url,"type" => "LinkUrl"));
                            array_push($edges,array("from"=> $from2,"to"=> $to3));
                        } else {
                            break;
                        }
                        $rows++;
                    }
                }
            }
        }

        return array("nodes"=>$nodes,"edges"=>$edges);
    }

    function cmdExport($link_type = 'page', $link_id = 0)
    {
        if($link_type=='user') {
            $this->cmdExportUser($link_id);
        } else  if($link_type=='fanpage') {
            $this->cmdExportFanPage($link_id);
        } else {
            $this->cmdExportPage($link_id);
        }
    }

    private function cmdExportPage($link_id = 0)
    {
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="LinkAnalysisPageExport-'.date("YmdHis").'.xlsx"');
        error_reporting(E_ALL);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $this->load->library("PHPExcel");

        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $result = $this->link_analysis_model->get_list($link_id);


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->createSheet(0);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle("Link Analysis");

        $objPHPExcel->getActiveSheet()->SetCellValue("A1","No.");
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
        $objPHPExcel->getActiveSheet()->SetCellValue("B1","User ID");
        $objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
        $objPHPExcel->getActiveSheet()->SetCellValue("C1","Name");
        $objPHPExcel->getActiveSheet()->mergeCells('C1:C2');
        $objPHPExcel->getActiveSheet()->SetCellValue("D1","Url");
        $objPHPExcel->getActiveSheet()->mergeCells('D1:D2');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);

        $start_col = 'E';
        $last_col = '';

        $next_col = $start_col;
        foreach($result as $key=>$val) {
            $col = $next_col;
            $objPHPExcel->getActiveSheet()->SetCellValue($col."1","Link ".$val['link_no']);

            $objPHPExcel->getActiveSheet()->SetCellValue($next_col."2","Likes");
            $objPHPExcel->getActiveSheet()->getColumnDimension($next_col)->setWidth(10);
            ++$next_col;

            $objPHPExcel->getActiveSheet()->SetCellValue($next_col."2","Shares");
            $objPHPExcel->getActiveSheet()->getColumnDimension($next_col)->setWidth(10);
            $merge_col = ++$next_col;

            $objPHPExcel->getActiveSheet()->SetCellValue($next_col."2","Comments");
            $objPHPExcel->getActiveSheet()->getColumnDimension($next_col)->setWidth(10);
            $last_col = ++$next_col;

            $objPHPExcel->getActiveSheet()->mergeCells($col.'1:'.$merge_col.'1');
        }

        $objPHPExcel->getActiveSheet()->getStyle("A1:".$last_col."1")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("A2:".$last_col."2")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->SetCellValue($last_col."1","Relationship");
        $objPHPExcel->getActiveSheet()->mergeCells($last_col.'1:'.$last_col.'2');
        $objPHPExcel->getActiveSheet()->getColumnDimension($last_col)->setWidth(15);

        $relate = $this->get_users_relate($result);
        $user_relate = $relate['user_relate'];
        $user_profile = $relate['user_profile'];
        $msg_arr = $relate['msg_arr'];
        $icons = $relate['icons'];

        $users = array();
        foreach($user_profile as $k_row=>$v_row) {

            $url = $this->fb_link.$k_row;
            $index = $k_row;

            $data = array();
            $data['id'] = $k_row;
            $data['name'] = $v_row['name'];
            $data['url'] = $url;
            $data['link'] = array(); 
            $count = 0;
            foreach($result as $key=>$val) {
                $msg_id = $val['msg_id'];
                $activity = array("like"=>0,"share"=>0,"comment"=>0);
                if(isset($user_relate[$index][$msg_id])) {
                    foreach($user_relate[$index][$msg_id] as $u_activity) {
                        $activity[$u_activity] = 1;
                        $count++;
                    }
                }
                $data['link'][$key]['like'] = $activity["like"];
                $data['link'][$key]['share'] = $activity["share"];
                $data['link'][$key]['comment'] = $activity["comment"];
            }
            $data['count'] = $count;
            array_push($users,$data);
        }

        $users = array_sort($users,'count',SORT_DESC);

        $rows = 3;
        foreach($users as $k_row=>$v_row) {

            $objPHPExcel->getActiveSheet()->SetCellValue("A".$rows,($k_row+1));
            $objPHPExcel->getActiveSheet()->getStyle("B".$rows,$v_row['id'])->getNumberFormat()->setFormatCode('0');
            $objPHPExcel->getActiveSheet()->SetCellValue("B".$rows,$v_row['id']);
            $objPHPExcel->getActiveSheet()->SetCellValue("C".$rows,$v_row['name']);
            $objPHPExcel->getActiveSheet()->SetCellValue("D".$rows,$v_row['url']);

            $next_col = $start_col;
            foreach($v_row['link'] as $key=>$val) {
                $objPHPExcel->getActiveSheet()->SetCellValue(($next_col++).$rows,$val["like"]);
                $objPHPExcel->getActiveSheet()->SetCellValue(($next_col++).$rows,$val["share"]);
                $objPHPExcel->getActiveSheet()->SetCellValue(($next_col++).$rows,$val["comment"]);
            }

            $objPHPExcel->getActiveSheet()->SetCellValue(($last_col).$rows,$v_row['count']);
            $rows++;
        }

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

    private function cmdExportUser($link_id = 0)
    {
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="LinkAnalysisUserExport-'.date("YmdHis").'.xlsx"');
        error_reporting(E_ALL);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $this->load->library("PHPExcel");

        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $result = $this->link_analysis_model->get_list($link_id);
        $relate = $this->get_users_activity($result,$this->user_activity);
        $result = $relate['result'];
        $user_profile = $relate['user_profile'];
        
        $objPHPExcel = new PHPExcel();

        if(count($user_profile)>0) {
            $sheet = 0;
            foreach($user_profile as $key=>$val) {

                $showCount = (in_array($key,$this->count_activity)) ? true : false;

                $objPHPExcel->createSheet($sheet);
                $objPHPExcel->setActiveSheetIndex($sheet);
                $objPHPExcel->getActiveSheet()->setTitle($key);
        
                $objPHPExcel->getActiveSheet()->SetCellValue("A1","No.");
                $objPHPExcel->getActiveSheet()->SetCellValue("B1","User ID");
                $objPHPExcel->getActiveSheet()->SetCellValue("C1","Name");
                $objPHPExcel->getActiveSheet()->SetCellValue("D1","Url");
                if($showCount) $objPHPExcel->getActiveSheet()->SetCellValue("E1","Count");
        
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
                if($showCount )$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->applyFromArray($style);

                $rows = 2;
                foreach ($val as $k_row=>$v_row) {
                    $v_row['url'] =  $this->fb_link.$v_row['id'];
                    $v_row['count'] = ($v_row['count']>0) ? $v_row['count'] : "";
                    $objPHPExcel->getActiveSheet()->SetCellValue("A".$rows,($k_row+1));
                    $objPHPExcel->getActiveSheet()->getStyle("B".$rows,$v_row['id'])->getNumberFormat()->setFormatCode('0');
                    $objPHPExcel->getActiveSheet()->SetCellValue("B".$rows,$v_row['id']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("C".$rows,$v_row['name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("D".$rows,$v_row['url']);
                    if($showCount) $objPHPExcel->getActiveSheet()->SetCellValue("E".$rows,$v_row['count']);
                    $rows++;
                }
                $sheet++;
            }
        }

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

    private function cmdExportFanPage($link_id = 0)
    {
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="LinkAnalysisFanPageExport-'.date("YmdHis").'.xlsx"');
        error_reporting(E_ALL);
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $this->load->library("PHPExcel");

        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $result = $this->link_analysis_model->get_list($link_id);
        $relate = $this->get_fanpage_activity($result,$this->fanpage_activity);
        $result = $relate['result'];
        $user_profile = $relate['user_profile'];
        
        $objPHPExcel = new PHPExcel();

        if(count($user_profile)>0) {
            $sheet = 0;
            foreach($user_profile as $key=>$val) {

                $showCount = (in_array($key,$this->count_activity)) ? true : false;

                $objPHPExcel->createSheet($sheet);
                $objPHPExcel->setActiveSheetIndex($sheet);
                $objPHPExcel->getActiveSheet()->setTitle($key);
        
                $objPHPExcel->getActiveSheet()->SetCellValue("A1","No.");
                $objPHPExcel->getActiveSheet()->SetCellValue("B1","User ID");
                $objPHPExcel->getActiveSheet()->SetCellValue("C1","Name");
                $objPHPExcel->getActiveSheet()->SetCellValue("D1","Url");
                if($showCount) $objPHPExcel->getActiveSheet()->SetCellValue("E1","Count");
        
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
                if($showCount )$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->applyFromArray($style);

                $rows = 2;
                foreach ($val as $k_row=>$v_row) {
                    $v_row['url'] =  $this->fb_link.$v_row['id'];
                    $v_row['count'] = ($v_row['count']>0) ? $v_row['count'] : "";
                    $objPHPExcel->getActiveSheet()->SetCellValue("A".$rows,($k_row+1));
                    $objPHPExcel->getActiveSheet()->getStyle("B".$rows,$v_row['id'])->getNumberFormat()->setFormatCode('0');
                    $objPHPExcel->getActiveSheet()->SetCellValue("B".$rows,$v_row['id']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("C".$rows,$v_row['name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("D".$rows,$v_row['url']);
                    if($showCount) $objPHPExcel->getActiveSheet()->SetCellValue("E".$rows,$v_row['count']);
                    $rows++;
                }
                $sheet++;
            }
        }

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }


    private function get_users_relate($link_list = array())
    {
        $user_profile = array();
        $user_relate = array();
        $icons = array();
        $msg_arr = array();

        foreach($link_list as $key=>$val) {
            $msg_id = $val['msg_id'];
            $rec = $this->link_analysis_model->get_mongo_page($msg_id);
            $link_list[$key]['count'] = array();
            if(isset($rec[0]['likes']) || isset($rec[0]['shares']) || isset($rec[0]['comments'])) {

                if(isset($rec[0]['likes']) && count($rec[0]['likes'])>0) {
                    $link_list[$key]['count']['like'] = count($rec[0]['likes']);
                    foreach($rec[0]['likes'] as $user) {
                        $index = $user['likesid'];
                        if(!isset($user_relate[$index][$msg_id])) $user_relate[$index][$msg_id] = array();
                        $user_profile[$index]['id'] = $index;
                        $user_profile[$index]['name'] = $user['likesname'];
                        $user_profile[$index]['pic'] = $user['likespic'];
                        array_push($user_relate[$index][$msg_id],"like");
                        $icons[$index][0] = 'like';
                    }
                }

                if(isset($rec[0]['shares']) && count($rec[0]['shares'])>0) {
                    $link_list[$key]['count']['share'] = count($rec[0]['shares']);
                    foreach($rec[0]['shares'] as $user) {
                        $index = $user['sharesid'];
                        if(!isset($user_relate[$index][$msg_id])) $user_relate[$index][$msg_id] = array();
                        $user_profile[$index]['id'] = $index;
                        $user_profile[$index]['name'] = $user['sharesname'];
                        $user_profile[$index]['pic'] = $user['sharespic'];
                        array_push($user_relate[$index][$msg_id],"share");
                        $icons[$index][1] = 'share';
                    }
                }

                if(isset($rec[0]['comments']) && count($rec[0]['comments'])>0) {
                    $link_list[$key]['count']['comment'] = count($rec[0]['comments']);
                    foreach($rec[0]['comments'] as $user) {
                        $index = $user['commentsid'];
                        if(!isset($user_relate[$index][$msg_id])) $user_relate[$index][$msg_id] = array();
                        $user_profile[$index]['id'] = $index;
                        $user_profile[$index]['name'] = $user['commentsname'];
                        $user_profile[$index]['pic'] = $user['commentspic'];
                        array_push($user_relate[$index][$msg_id],"comment");
                        $icons[$index][2] = 'comment';
                    }
                }
                array_push($msg_arr,$msg_id);
            }
        }

        $result = array(
            "result"=>$link_list,
            "user_profile"=>$user_profile,
            "user_relate"=>$user_relate,
            "icons"=>$icons,
            "msg_arr"=>$msg_arr,
        );

        return $result;
    }

    private function get_users_activity($link_list = array(),$activity = array())
    {
        $user_profile = array();
  
        foreach($link_list as $key=>$val) {
            $msg_id = $val['msg_id'];
            $rec = $this->link_analysis_model->get_mongo_user($msg_id);

            foreach($activity as $prefix) {
                if(isset($rec[0][$prefix]) && count($rec[0][$prefix])>0) {
                    $is_count = false;
                    $user_profile[$prefix] = array();
                    foreach($rec[0][$prefix] as $key=>$val) {
                        $data = array();
                        $data['id'] = $val[$prefix.'id'];
                        $data['name'] = $val[$prefix.'name'];
                        $data['pic'] = $val[$prefix.'pic'];
                        if(isset($val[$prefix.'count'])) {
                            $data['count'] = intval($val[$prefix.'count']);
                            $is_count = true;
                        } else {
                            $data['count'] = 0;
                        }
                        array_push($user_profile[$prefix],$data);
                    }
                    if($is_count) {
                        $user_profile[$prefix] = array_sort($user_profile[$prefix], 'count', SORT_DESC);
                    }
                }
            }
        }

        $result = array(
            "result"=>$link_list,
            "user_profile"=>$user_profile,
        );

        return $result;
    }

    private function get_fanpage_activity($link_list = array(),$activity = array())
    {
        $user_profile = array();
  
        foreach($link_list as $key=>$val) {
            $msg_id = $val['msg_id'];
            $rec = $this->link_analysis_model->get_mongo_fanpage($msg_id);

            foreach($activity as $prefix) {
                if(isset($rec[0][$prefix]) && count($rec[0][$prefix])>0) {
                    $is_count = false;
                    $user_profile[$prefix] = array();
                    foreach($rec[0][$prefix] as $key=>$val) {
                        $data = array();
                        $data['id'] = $val[$prefix.'id'];
                        $data['name'] = $val[$prefix.'name'];
                        $data['pic'] = $val[$prefix.'pic'];
                        if(isset($val[$prefix.'count'])) {
                            $data['count'] = intval($val[$prefix.'count']);
                            $is_count = true;
                        } else {
                            $data['count'] = 0;
                        }
                        array_push($user_profile[$prefix],$data);
                    }
                    if($is_count) {
                        $user_profile[$prefix] = array_sort($user_profile[$prefix], 'count', SORT_DESC);
                    }
                }
            }
        }

        $result = array(
            "result"=>$link_list,
            "user_profile"=>$user_profile,
        );

        return $result;
    }

    //====================================================    Eget News function

    function get_Post_User(){
        $post = $this->input->post(); 
        // $link_id = 648;                                // open test data
        $link_id = $post['id'];    
        $arr_check = array();
        $arr_data = array();
        $arr_keyword = array();
        $result_post = array();                                                       

        $data = $this->link_analysis_model->get_data_list($link_id);          //link id จาก javascript
        $Msg_id = $data[0]["msg_id"];
        print_r($data);

        $rowdata_match = $this->link_analysis_model->get_link_match($Msg_id);

        for($i=0;$i < count($rowdata_match);$i++){
            $id_feed = $rowdata_match[$i]['_id'];
            $keyword_name = $rowdata_match[$i]['keyword_name'];
            if (!in_array($id_feed, $arr_check)){
                array_push($arr_check,$id_feed);
            }
        }

        for($x=0;$x < count($arr_check);$x++){
            $id_check = $arr_check[$x];
            $keyword_check = "";
            for($y=0;$y < count($rowdata_match);$y++){
                $id_y = $rowdata_match[$y]['_id'];
                if($id_check == $id_y){
                    $id_last = $id_check;
                    $keyword = $rowdata_match[$y]['keyword_name'];
                    $keyword_check .= "$keyword ";  
                }
            }
            array_push($arr_data,array(
                '_id' =>$id_last,
                'keyword_name'=>$keyword_check
            ));   
        }

        for($z=0;$z < count($arr_data);$z++){
            $id_get_mongo = $arr_data[$z]['_id'];
            $keyword_last = $arr_data[$z]['keyword_name'];

            $result = $this->link_analysis_model->get_mongo_linkanalysis_feed($id_get_mongo);

            for($a=0;$a<count($result);$a++){

            
                $id_post = $result[$a]['_id'];
                $time_post = $result[$a]['feedtimepost'];
                $content_post = $result[$a]['feedcontent'];
                $location_post = $result[$a]['feedlocation'];
                $likes_post = $result[$a]['feedlikes'];
                $shares_post = $result[$a]['feedshares'];
                $comment_post = $result[$a]['feedcomment'];
                $link_post = $result[$a]['feedlink'];
                $user_post = $result[$a]['feeduser'];
    
                array_push($result_post,array(
                    'id_post' =>$id_post,
                    'user_post' => $user_post,
                    'time_post' => $time_post,
                    'content_post'=>$content_post,
                    'location_post'=>$location_post,
                    'likes_post'=>$likes_post,
                    'shares_post'=>$shares_post,
                    'comment_post'=>$comment_post,
                    'link_post'=>$link_post,
                    'keyword'=>$keyword_last
                ));
            }
        }
        echo json_encode($result_post);
        // echo json_encode($result_post,JSON_UNESCAPED_UNICODE);
    }

    function get_comment_user(){    //ต้องส่งเข้ามาเป็น string

        $result_comment = array();
        $post = $this->input->post();
        $comment_id = $post['id_comment'];     

        $data_result = $this->link_analysis_model->get_mongo_linkanalysis_comment($comment_id);
        
        for($x=0;$x<count($data_result);$x++)
        {
            $id_comment = $data_result[$x]['feedid'];
            $time_comment = $data_result[$x]['commenttimepost'];
            $content_comment = $data_result[$x]['commentcontent'];
            $like_comment = $data_result[$x]['commentlikes'];
            $link_comment = $data_result[$x]['commentlink'];
            $user_comment = $data_result[$x]['commentuser'];

            array_push($result_comment,array(
                'id_comment' =>$id_comment,
                'time_comment'=>$time_comment,
                'content_comment'=>$content_comment,
                'like_comment'=>$like_comment,
                'link_comment'=>$link_comment,
                'user_comment'=>$user_comment
            ));
        }
        echo json_encode($result_comment);
    }

    function get_share_user(){    //input string

        $result_share = array();
        $post = $this->input->post();
        $share_id = $post['id_comment'];
        
        $data_result = $this->link_analysis_model->get_mongo_linkanalysis_share($share_id);
        

        for($x=0;$x<count($data_result);$x++)
        {
            $id_share = $data_result[$x]['feedid'];
            $time_share = $data_result[$x]['sharetimepost'];
            $content_share = $data_result[$x]['sharecontent'];
            $like_share = $data_result[$x]['sharelikes'];
            $share_share = $data_result[$x]['shareshares'];
            $comment_share = $data_result[$x]['sharecomment'];
            $link_share = $data_result[$x]['sharelink'];
            $user_share = $data_result[$x]['shareuser'];

            array_push($result_share,array(
                'id_share' =>$id_share,
                'time_share'=>$time_share,
                'content_share'=>$content_share,
                'like_share'=>$like_share,
                'share_share'=>$share_share,
                'comment_share'=>$comment_share,
                'link_share'=>$link_share,
                'user_share'=>$user_share
            ));

        }
        echo json_encode($result_share);
    }

    //==================================================================================================test get_Post_User_test add keyword
    // function get_Post_User_test(){
                   
    //     $post = $this->input->post(); 
    //     $link_id = 648;                  
    //     // $link_id = $post['id'];    
    //     $arr_check = array();
    //     $arr_data = array();
    //     $arr_keyword = array();
    //     $result_post = array();                                                       

    //     $data = $this->link_analysis_model->get_data_list($link_id);          //link id จาก javascript
    //     $Msg_id = $data[0]["msg_id"];

    //     $rowdata_match = $this->link_analysis_model->get_link_match($Msg_id);

    //     for($i=0;$i < count($rowdata_match);$i++){

    //         $id_feed = $rowdata_match[$i]['_id'];
    //         $keyword_name = $rowdata_match[$i]['keyword_name'];

    //         if (!in_array($id_feed, $arr_check)){
    //             array_push($arr_check,$id_feed);
    //         }
    //     }

    //     for($x=0;$x < count($arr_check);$x++){

    //         $id_check = $arr_check[$x];
    //         $keyword_check = "";
    //         for($y=0;$y < count($rowdata_match);$y++){

    //             $id_y = $rowdata_match[$y]['_id'];

    //             if($id_check == $id_y){

    //                 $id_last = $id_check;
    //                 $keyword = $rowdata_match[$y]['keyword_name'];
    //                 $keyword_check .= "$keyword ";
                    
                    
    //             }

    //         }
    //         array_push($arr_data,array(
    //             '_id' =>$id_last,
    //             'keyword_name'=>$keyword_check
    //         ));
            
    //     }

    //     for($z=0;$z < count($arr_data);$z++){

    //         $id_get_mongo = $arr_data[$z]['_id'];
    //         $keyword_last = $arr_data[$z]['keyword_name'];

    //         $result = $this->link_analysis_model->get_mongo_linkanalysis_feed($id_get_mongo);

    //         for($a=0;$a<count($result);$a++){

            
    //             $id_post = $result[$a]['_id'];
    //             $time_post = $result[$a]['feedtimepost'];
    //             $content_post = $result[$a]['feedcontent'];
    //             $location_post = $result[$a]['feedlocation'];
    //             $likes_post = $result[$a]['feedlikes'];
    //             $shares_post = $result[$a]['feedshares'];
    //             $comment_post = $result[$a]['feedcomment'];
    //             $link_post = $result[$a]['feedlink'];
    //             $user_post = $result[$a]['feeduser'];
    
    //             array_push($result_post,array(
    //                 'id_post' =>$id_post,
    //                 'user_post' => $user_post,
    //                 'time_post' => $time_post,
    //                 'content_post'=>$content_post,
    //                 'location_post'=>$location_post,
    //                 'likes_post'=>$likes_post,
    //                 'shares_post'=>$shares_post,
    //                 'comment_post'=>$comment_post,
    //                 'link_post'=>$link_post,
    //                 'keyword'=>$keyword_last
    //             ));
    //         }

    //     }
    //     // echo json_encode($result_post);
    //     echo json_encode($result_post,JSON_UNESCAPED_UNICODE);
    // }

    //==============================================================================================get_Post_User old
    // function get_Post_User(){

    //     $arr_check = array();
    //     $result_post = array();
    //     $post = $this->input->post();                   
    //     $link_id = $post['id'];                                                         

    //     $data = $this->link_analysis_model->get_data_list($link_id);          //link id จาก javascript
    //     $Msg_id = $data[0]["msg_id"];

    //     $rowdata_match = $this->link_analysis_model->get_link_match($Msg_id);
        
    //     for($i=0;$i < count($rowdata_match);$i++){

    //         $id_feed = $rowdata_match[$i]['_id'];
    //         $keyword_name = $rowdata_match[$i]['keyword_name'];

    //         if (!in_array($id_feed, $arr_check)){
    //             array_push($arr_check,$id_feed);

    //             $result = $this->link_analysis_model->get_mongo_linkanalysis_feed($id_feed);

    //             for($x=0;$x<count($result);$x++){

            
    //                 $id_post = $result[$x]['_id'];
    //                 $time_post = $result[$x]['feedtimepost'];
    //                 $content_post = $result[$x]['feedcontent'];
    //                 $location_post = $result[$x]['feedlocation'];
    //                 $likes_post = $result[$x]['feedlikes'];
    //                 $shares_post = $result[$x]['feedshares'];
    //                 $comment_post = $result[$x]['feedcomment'];
    //                 $link_post = $result[$x]['feedlink'];
    //                 $user_post = $result[$x]['feeduser'];
        
    //                 array_push($result_post,array(
    //                     'id_post' =>$id_post,
    //                     'user_post' => $user_post,
    //                     'time_post' => $time_post,
    //                     'content_post'=>$content_post,
    //                     'location_post'=>$location_post,
    //                     'likes_post'=>$likes_post,
    //                     'shares_post'=>$shares_post,
    //                     'comment_post'=>$comment_post,
    //                     'link_post'=>$link_post
    //                 ));
    //             }
    //         }   
    //     }
    //     echo json_encode($result_post);  
    //     // echo json_encode($result_post,JSON_UNESCAPED_UNICODE);  
    // }


}