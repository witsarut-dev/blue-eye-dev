<?php
class Sys_roles extends Backend 
{
	public static $message = "";
	var $module = "sys_roles";
	var $title = "Roles";

    function __construct()
    {
        parent::__construct();
        parent::$view_data["module"] = $this->module;
        parent::$view_data["title"] = $this->title;
        $this->load->model("sys_roles_model");
        $this->load->model("sys_logs_model");
        $this->load->model("authen_model");
    }

    function index()
    {
    	$this->formList();
    }

	function formList()
	{
		$post = $this->input->post();
		parent::check_access($this->module,"view");

		$rows = $this->sys_roles_model->get_rows($post);
		parent::$view_data["pagesize"] = 15;
		parent::$view_data["rows"] = $rows;
		parent::$view_data["rows_publish"] = $this->sys_roles_model->get_rows_publish($post);
		parent::$view_data["rows_modified"] = $this->sys_roles_model->get_rows_modified($post);
		parent::$view_data["rows_unpublish"] = $this->sys_roles_model->get_rows_unpublish($post);
		parent::$view_data["totalpage"] = ceil($rows/parent::$view_data["pagesize"]);
		parent::$view_data["post"] = $post;
		parent::$view_data["control"] = true;

		$this->load->view("sys_roles_list_view",parent::$view_data);
	}

	function ajaxList()
	{
		$post = $this->input->post();

		$option = array("post"=>$post
				,"orderby"=>$post["orderby"]
				,"sorting"=>$post["sorting"]);

		$start = $post["thispage"];
		$end = $post["pagesize"];
		$item = $this->sys_roles_model->get_page($option,$start,$end);
		echo  json_encode($item);
	}

	function formAdd()
	{
		$post = $this->input->post();
		parent::check_access($this->module,"created");
		parent::$view_data["action"] = "add";
		parent::$view_data["post"] =  $post;

		parent::$view_data["_modules_"] = $this->authen_model->get_module();
		$this->load->view("sys_roles_form_view",parent::$view_data);
	}

	function formDisplay($id = 0)
	{
		$post = $this->sys_roles_model->get_id($id);
		parent::check_access($this->module,"view");
		parent::$view_data["action"] = "display";
		parent::$view_data["post"] = $this->get_permission($id,$post);
		parent::$view_data["id"] = $id;
		parent::$view_data["control"] = false;

		$this->sys_roles_model->count_log($id,$rows,$totalpage);
		parent::$view_data["rows"] = $rows;
		parent::$view_data["totalpage"] = $totalpage;

		parent::$view_data["_modules_"] = $this->authen_model->get_module();
		$this->load->view("sys_roles_form_view",parent::$view_data);
	}

	function formEdit($id = 0)
	{
		$post = $this->sys_roles_model->get_id($id);
		parent::check_access($this->module,"modified");
		parent::$view_data["action"] = "edit";
		parent::$view_data["post"] =  $this->get_permission($id,$post);
		parent::$view_data["id"] = $id;
		parent::$view_data["control"] = false;

		$this->sys_roles_model->count_log($id,$rows,$totalpage);
		parent::$view_data["rows"] = $rows;
		parent::$view_data["totalpage"] = $totalpage;

		parent::$view_data["_modules_"] = $this->authen_model->get_module();
		$this->load->view("sys_roles_form_view",parent::$view_data);
	}

	function get_permission($id,$post = array())
	{
		$permission = $this->authen_model->get_permission($id);
		$v_per = "";

		foreach($permission as $v_per) :
			$k_per = $v_per["module"]."_".$v_per["action"];
			$post[$k_per] = '1';
		endforeach;
		return $post;
	}

	function cmdSave(&$id)
	{
		$post = $this->input->post();

		$val = @$post["id"];
		$status = self::trigger("save:before",$val,$post);
		if($status) {
			if(empty($val)) {
				parent::check_access($this->module,"created");
				$this->sys_logs_model->save_log($this->module,"created");
				$val = $this->sys_roles_model->insert($post);
			} else {
				parent::check_access($this->module,"modified");
				$this->sys_logs_model->save_log($this->module,"modified");
				$this->sys_roles_model->update($post);
			}
			$status = self::trigger("save:after",$val,$post);
		}

		$id = $val;
		return $status;
	}

	function cmdSavePublish(&$id)
	{
		$post = $this->input->post();
		$get = $this->input->get();
		parent::check_access($this->module,"modified");
		$cmd = "";

		$val = @$post["id"];
		$status = self::trigger("save:before",$val,$post);
		if($status) {
			if(empty($val)) {
				$this->sys_logs_model->save_log($this->module,"created");
				$val = $this->sys_roles_model->insert($post);
				$cmd = "Insert";
			} else {
				$this->sys_logs_model->save_log($this->module,"modified");
				$this->sys_roles_model->update($post);
				$cmd = "Update";
			}
			$status = self::trigger("save:after",$val,$post);
			if($status) {
				parent::check_access($this->module,"publish");
				$status = self::trigger("public:before",$val,$post);
				if($status) {
					$this->sys_logs_model->save_log($this->module,"publish");
					$this->sys_roles_model->savePublish($val,$cmd);
					$status = self::trigger("public:after",$val,$post);
				}
			}
		}
		$id = $val;
		return $status;
	}


	function cmdDelete()
	{
		$post = $this->input->post();
		parent::check_access($this->module,"deleted");

		if(gettype($post["id"])=="array") {
			foreach($post["id"] as $key=>$val) {
				$status = self::trigger("delete:before",$val,array());
				if($status) {
					$this->sys_logs_model->save_log($this->module,"delete");
					$this->sys_roles_model->delete($val);
					$status = self::trigger("delete:after",$val,array());
				} else {
					break;
				}
			}
		} else {
			$val = $post["id"];
			$status = self::trigger("delete:before",$val,array());
            if($status) {
			    $this->sys_logs_model->save_log($this->module,"delete");
			    $this->sys_roles_model->delete($val);
			    $status = self::trigger("delete:after",$val,array());
            }
		}
		return $status;
	}

	function cmdPublish(&$id)
	{
		$post = $this->input->post();
		$val = 0;
		parent::check_access($this->module,"publish");

		if(gettype($post["id"])=="array") {
			foreach($post["id"] as $key=>$val) {
				$status = self::trigger("public:before",$val,array());
				if($status) {
					$this->sys_logs_model->save_log($this->module,"publish");
					$this->sys_roles_model->save_public($val);
					$status = self::trigger("public:after",$val,array());
				} else {
					break;
				}
			}
		} else {
			$val = $post["id"];
			$status = self::trigger("public:before",$val,array());
            if($status) {
			    $this->sys_logs_model->save_log($this->module,"publish");
			    $this->sys_roles_model->save_public($val);
			    $status = self::trigger("public:after",$val,array());
            }
		}

		$id = $val;
		return $status;
	}

	function cmdUnPublish()
	{
		$post = $this->input->post();
		parent::check_access($this->module,"publish");

		foreach($post["id"] as $key=>$val) {
			$status = self::trigger("unpublic:before",$val,array());
			if($status) {
				$this->sys_logs_model->save_log($this->module,"unpublish");
				$this->sys_roles_model->save_unpublic($val);
				$status = self::trigger("unpublic:after",$val,array());
			} else {
				break;
			}
		}
		return $status;
	}

	#cmd to redirection
	function cmdSaveToList()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdSave($id);
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles");
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdSaveToForm()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdSave($id);
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles/formEdit/".$id);
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdSavePublishToList()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdSavePublish($id);
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save and publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles");
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdSavePublishToForm()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdSavePublish($id);
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save and publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles/formEdit/".$id);
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdPublishToList()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdPublish($id);
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles");
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdPublishToForm()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdPublish($id);
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles/formEdit/".$id);
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdUnPublishToList()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdUnPublish();
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You unpublish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles");
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdDeleteToList()
	{
        $post = $this->input->post();
        if($post) {
    		$status = self::cmdDelete();
            if($status) {
                $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You delete '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_roles");
    		$result = array("status"=>$status
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function cmdDeleteFile()
	{
		$post = $this->input->post();
        if($post) {

            $alert_message = isset($post['roles_name']) ? '"'.$post['roles_name'].'"' : "";
            $this->session->set_flashdata('ALERT_MESSAGE','You publish '.$alert_message.' completed.');

    		parent::check_access($this->module,"deleted");
    		$BASEPATH = str_replace("system/","",BASEPATH);
    		@unlink($BASEPATH."upload/sys_roles/thumb_edit/".@$post['filepath']);
    		@unlink($BASEPATH."upload/sys_roles/thumb_list/".@$post['filepath']);
    		@unlink($BASEPATH."upload/sys_roles/".@$post['filepath']);

    		$this->sys_roles_model->delete_file($post);

    		$site_url = site_url("sys_roles/formEdit/".@$post["id"]);
    		$result = array("status"=>true
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
        } else {
            show_404();
        }
	}

	function changeLog()
	{
		$post = $this->input->post();

		$option = array("post"=>$post
				,"orderby"=>$post["orderby"]
				,"sorting"=>$post["sorting"]);

		$start = $post["thispage"];
		$end = $post["pagesize"];
		$item = $this->sys_roles_model->get_log($option,$start,$end);
		echo  json_encode($item);
	}

    public static function trigger($action = "",$id = 0,$data = array())
    {
        $obj = modules::run('sys_roles/sys_roles_trigger/set_trigger', $action, $id, $data);
        self::$message = $obj["message"];
        return $obj["status"];
    }

}