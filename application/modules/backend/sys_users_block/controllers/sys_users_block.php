<?php
class Sys_users_block extends Backend 
{
	public static $message = "";
	var $module = "sys_users_block";
	var $title = "Users Block";

    function __construct()
    {
        parent::__construct();
        parent::$view_data["module"] = $this->module;
        parent::$view_data["title"] = $this->title;
        parent::$view_data["log_mode"] = "Off";
		parent::$view_data["control_mode"] = "On";
		parent::$view_data["add_mode"] = "Off";
		parent::$view_data["edit_mode"] = "Off";
		parent::$view_data["publish_mode"] = "Off";
		parent::$view_data["delete_mode"] = "On";
		
        $this->load->model("sys_users_block_model");
        $this->load->model("sys_logs_model");
        $this->load->model("sys_users_block_custom_model");

    }

    function index()
    {
    	$this->formList();
    }

	function formList()
	{
		$post   = $this->input->post();
		$status = self::trigger("view:list",null,$post);
		parent::check_access($this->module,"view");

		$rows = $this->sys_users_block_model->get_rows($post);
		parent::$view_data["pagesize"] = 15;
		parent::$view_data["rows"] = $rows;
		parent::$view_data["rows_publish"] = $this->sys_users_block_model->get_rows_publish($post);
		parent::$view_data["rows_modified"] = $this->sys_users_block_model->get_rows_modified($post);
		parent::$view_data["rows_unpublish"] = $this->sys_users_block_model->get_rows_unpublish($post);
		parent::$view_data["totalpage"] = ceil($rows/parent::$view_data["pagesize"]);
		parent::$view_data["post"] = $post;
		parent::$view_data["control"] = true;

		$this->load->view("sys_users_block_list_view",parent::$view_data);
	}

	function ajaxList()
	{
		$post = $this->input->post();

        if($post) {
            parent::check_access($this->module,"view");
    		$option = array("post"=>$post
    				,"orderby"=>$post["orderby"]
    				,"sorting"=>$post["sorting"]);

    		$start = $post["thispage"];
    		$end = $post["pagesize"];
    		$item = $this->sys_users_block_model->get_page($option,$start,$end);
    		echo  json_encode($item);
        }
	}

	function formAdd()
	{
		$post   = $this->input->post();
		$this->sys_users_block_model->delete_child();
		$status = self::trigger("view:add",null,$post);
		parent::check_access($this->module,"created");
		parent::$view_data["action"] = "add";
		parent::$view_data["post"] = $post;
		$this->load->view("sys_users_block_form_view",parent::$view_data);
	}

	function formDisplay($id = 0)
	{
		$post   = $this->sys_users_block_model->get_id($id);
		$status = self::trigger("view:display",$id,$post);
		parent::check_access($this->module,"view");
		parent::$view_data["action"] = "display";
		parent::$view_data["post"] = $post;
		parent::$view_data["id"] = $id;
		parent::$view_data["control"] = false;

		$this->sys_users_block_model->count_log($id,$rows,$totalpage);
		parent::$view_data["rows"] = $rows;
		parent::$view_data["totalpage"] = $totalpage;

		$this->load->view("sys_users_block_form_view",parent::$view_data);
	}

	function formEdit($id = 0)
	{
		$post   = $this->sys_users_block_model->get_id($id);
		$status = self::trigger("view:edit",$id,$post);
		parent::check_access($this->module,"modified");
		parent::$view_data["action"] = "edit";
		parent::$view_data["post"] = $post;
		parent::$view_data["id"] = $id;
		parent::$view_data["control"] = false;

		$this->sys_users_block_model->count_log($id,$rows,$totalpage);
		parent::$view_data["rows"] = $rows;
		parent::$view_data["totalpage"] = $totalpage;

		$this->load->view("sys_users_block_form_view",parent::$view_data);
	}

	private function cmdSave(&$id)
	{
		$post = $this->input->post();

		$val = @$post["id"];
		$status = self::trigger("save:before",$val,$post);
		if($status) {
			if(empty($val)) {
				parent::check_access($this->module,"created");
				$this->sys_logs_model->save_log($this->module,"created");
				$val = $this->sys_users_block_model->insert($post);
			} else {
				parent::check_access($this->module,"modified");
				$this->sys_logs_model->save_log($this->module,"modified");
				$this->sys_users_block_model->update($post);
			}
			$status = self::trigger("save:after",$val,$post);
		}

		$id = $val;
		return $status;
	}

	private function cmdSavePublish(&$id)
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
				$val = $this->sys_users_block_model->insert($post);
				$cmd = "Insert";
			} else {
				$this->sys_logs_model->save_log($this->module,"modified");
				$this->sys_users_block_model->update($post);
				$cmd = "Update";
			}
			$status = self::trigger("save:after",$val,$post);
			if($status) {
				parent::check_access($this->module,"publish");
				$status = self::trigger("public:before",$val,$post);
				if($status) {
					$this->sys_logs_model->save_log($this->module,"publish");
					$this->sys_users_block_model->savePublish($val,$cmd);
					$status = self::trigger("public:after",$val,$post);
				}
			}
		}
		$id = $val;
		return $status;
	}


	private function cmdDelete()
	{
		$post = $this->input->post();
		parent::check_access($this->module,"deleted");

		if(gettype($post["id"])=="array") {
			foreach($post["id"] as $key=>$val) {
				$status = self::trigger("delete:before",$val,array());
				if($status) {
					$this->sys_logs_model->save_log($this->module,"delete");
					$this->sys_users_block_model->delete($val);
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
			    $this->sys_users_block_model->delete($val);
			    $status = self::trigger("delete:after",$val,array());
            }
		}
		return $status;
	}

	private function cmdPublish(&$id)
	{
		$post = $this->input->post();
		$val = 0;
		parent::check_access($this->module,"publish");

		if(gettype($post["id"])=="array") {
			foreach($post["id"] as $key=>$val) {
				$status = self::trigger("public:before",$val,array());
				if($status) {
					$this->sys_logs_model->save_log($this->module,"publish");
					$this->sys_users_block_model->save_public($val);
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
			    $this->sys_users_block_model->save_public($val);
			    $status = self::trigger("public:after",$val,array());
            }
		}

		$id = $val;
		return $status;
	}

	private function cmdUnPublish()
	{
		$post = $this->input->post();
		parent::check_access($this->module,"publish");

		foreach($post["id"] as $key=>$val) {
			$status = self::trigger("unpublic:before",$val,array());
			if($status) {
				$this->sys_logs_model->save_log($this->module,"unpublish");
				$this->sys_users_block_model->save_unpublic($val);
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block");
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block/formEdit/".$id);
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save and publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block");
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You save and publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block/formEdit/".$id);
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block");
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You publish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block/formEdit/".$id);
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You unpublish '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block");
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
                $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
                $this->session->set_flashdata('ALERT_MESSAGE','You delete '.$alert_message.' completed.');
            }
    		$site_url = site_url("sys_users_block");
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

            $alert_message = isset($post['ALERT']) ? '"'.$post['ALERT'].'"' : "";
            $this->session->set_flashdata('ALERT_MESSAGE','You delete file '.$alert_message.' completed.');

    		parent::check_access($this->module,"deleted");
    		$BASEPATH = str_replace("system/","",BASEPATH);
    		@unlink($BASEPATH."upload/sys_users_block/thumb_edit/".@$post['filepath']);
    		@unlink($BASEPATH."upload/sys_users_block/thumb_list/".@$post['filepath']);
    		@unlink($BASEPATH."upload/sys_users_block/".@$post['filepath']);

    		$this->sys_users_block_model->delete_file($post);

    		$site_url = site_url("sys_users_block/formEdit/".@$post["id"]);
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
        if($post) {
            parent::check_access($this->module,"view");
    		$option = array("post"=>$post
    				,"orderby"=>$post["orderby"]
    				,"sorting"=>$post["sorting"]);

    		$start = $post["thispage"];
    		$end = $post["pagesize"];
    		$item = $this->sys_users_block_model->get_log($option,$start,$end);
    		echo  json_encode($item);
        } else {
            show_404();
        }
	}

	function cmdAction()
	{
		$get = $this->input->get();
		$id  = @$get['id'];
		$trigger  = @$get['trigger'];
		$obj = self::trigger($trigger,$id,$get);
		$status = $obj["status"];
		self::$message = $obj["message"];
		$result = array("status"=>$status
			,"message"=>self::$message);

		echo json_encode($result);
	}

	function cmdUpdateParent()
	{
		$post = $this->input->post();
        if($post) {
            parent::check_access($this->module,"modified");
            $json_array = json_decode($post['json'], true);
            $this->sys_users_block_model->update_parent($json_array,0);
        } else {
            show_404();
        }
	}

	function cmdUpdateStatus()
    {
        $post = $this->input->post();
        if($post) {

            $alert_message = isset($post['status_col']) ? '"'.$post['status_col'].'"' : "";
            $this->session->set_flashdata('ALERT_MESSAGE','You update status '.$alert_message.' completed.');

            parent::check_access($this->module,"modified");
            $this->sys_users_block_model->update_status($post);
        } else {
            show_404();
        }
    }

	public static function trigger($action = "",$id = 0,$data = array())
	{
		$obj = modules::run('sys_users_block/sys_users_block_trigger/set_trigger', $action, $id, $data);
		self::$message = $obj["message"];
		return $obj["status"];
	}

}