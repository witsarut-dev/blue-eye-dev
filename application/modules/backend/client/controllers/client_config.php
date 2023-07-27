<?php
class Client_config extends Backend
{
	public static $message = "";
	var $module = "client";
	var $child = "client_config";
	var $title = "Config Package";

    function __construct()
    {
        parent::__construct();
        parent::$view_data["module"] = $this->module;
		parent::$view_data["child"] = $this->child;
		parent::$view_data["title"] = $this->title;
		parent::$view_data["log_mode"] = "OFF";
		parent::$view_data["publish_mode"] = "OFF";
		parent::$view_data["delete_mode"] = "OFF";
		parent::$view_data["add_mode"] = "OFF";
		
        $this->load->model("client_config_model");
        $this->load->model("sys_logs_model");
        
    }

    function index()
    {
    	$this->formList();
    }

	function formList()
	{
		if($this->input->get()) {
			$post   = $this->input->get();
		} else {
			$post   = $this->input->post();
		}
		$status = self::trigger("view:list",null,$post);
		parent::check_access($this->module,"view");

		$rows = $this->client_config_model->get_rows($post);
		parent::$view_data["pagesize"] = 15;
		parent::$view_data["rows"] = $rows;
		parent::$view_data["totalpage"] = ceil($rows/parent::$view_data["pagesize"]);
		parent::$view_data["post"] = $post;
		parent::$view_data["control"] = true;

		$this->load->view("client_config_list_view",parent::$view_data);
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
    		$item = $this->client_config_model->get_page($option,$start,$end);
    		echo  json_encode($item);
        }
	}

	function formAdd()
	{
		$post   = $this->input->post();
		$status = self::trigger("view:add",null,$post);
		parent::check_access($this->module,"created");
		parent::$view_data["action"] = "add";
		parent::$view_data["post"] = $post;
		$this->load->view("client_config_form_view",parent::$view_data);
	}

	function formDisplay($id = 0)
	{
		$post   = $this->client_config_model->get_id($id);
		$status = self::trigger("view:display",$id,$post);
		parent::check_access($this->module,"view");
		parent::$view_data["action"] = "display";
		parent::$view_data["post"] = $post;
		parent::$view_data["id"] = $id;
		parent::$view_data["control"] = false;

		$this->load->view("client_config_form_view",parent::$view_data);
	}

	function formEdit($id = 0)
	{
		$post   = $this->client_config_model->get_id($id);
		$status = self::trigger("view:edit",$id,$post);
		parent::check_access($this->module,"modified");
		parent::$view_data["action"] = "edit";
		parent::$view_data["post"] = $post;
		parent::$view_data["id"] = $id;
		parent::$view_data["control"] = false;

		$this->load->view("client_config_form_view",parent::$view_data);
	}

	private function cmdSave(&$id)
	{
		$post = $this->input->post();

		$val = @$post["child_id"];
		$status = self::trigger("save:before",$val,$post);
		if($status) {
			if(empty($val)) {
				parent::check_access($this->module,"created");
				//$this->sys_logs_model->save_log($this->module,"created");
				$val = $this->client_config_model->insert($post);
			} else {
				parent::check_access($this->module,"modified");
				//$this->sys_logs_model->save_log($this->module,"modified");
				$this->client_config_model->update($post);
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

		$val = @$post["child_id"];
		$status = self::trigger("save:before",$val,$post);
		if($status) {
			if(empty($val)) {
				//$this->sys_logs_model->save_log($this->module,"created");
				$val = $this->client_config_model->insert($post);
				$cmd = "Insert";
			} else {
				//$this->sys_logs_model->save_log($this->module,"modified");
				$this->client_config_model->update($post);
				$cmd = "Update";
			}
			$status = self::trigger("save:after",$val,$post);
			if($status) {
				parent::check_access($this->module,"publish");
				$status = self::trigger("public:before",$val,$post);
				if($status) {
					//$this->sys_logs_model->save_log($this->module,"publish");
					$this->client_config_model->savePublish($val,$cmd);
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

		if(gettype($post["child_id"])=="array") {
			foreach($post["child_id"] as $key=>$val) {
				$status = self::trigger("delete:before",$val,array());
				if($status) {
					//$this->sys_logs_model->save_log($this->module,"delete");
					$this->client_config_model->delete($val);
					$status = self::trigger("delete:after",$val,array());
				} else {
					break;
				}
			}
		} else {
			$val = $post["child_id"];
			$status = self::trigger("delete:before",$val,array());
            if($status) {
			    //$this->sys_logs_model->save_log($this->module,"delete");
			    $this->client_config_model->delete($val);
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

		if(gettype($post["child_id"])=="array") {
			foreach($post["child_id"] as $key=>$val) {
				$status = self::trigger("public:before",$val,array());
				if($status) {
					//$this->sys_logs_model->save_log($this->module,"publish");
					$this->client_config_model->save_public($val);
					$status = self::trigger("public:after",$val,array());
				} else {
					break;
				}
			}
		} else {
			$val = $post["child_id"];
			$status = self::trigger("public:before",$val,array());
            if($status) {
			    //$this->sys_logs_model->save_log($this->module,"publish");
			    $this->client_config_model->save_public($val);
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

		foreach($post["child_id"] as $key=>$val) {
			$status = self::trigger("unpublic:before",$val,array());
			if($status) {
				//$this->sys_logs_model->save_log($this->module,"unpublish");
				$this->client_config_model->save_unpublic($val);
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
    		$site_url = site_url("client/client_config");
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
    		$site_url = site_url("client/client_config/formEdit/".$id);
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
    		$site_url = site_url("client/client_config");
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
    		$site_url = site_url("client/client_config/formEdit/".$id);
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
    		$site_url = site_url("client/client_config");
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
    		$site_url = site_url("client/client_config/formEdit/".$id);
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
    		$site_url = site_url("client/client_config");
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
    		$site_url = site_url("client/client_config");
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
    		parent::check_access($this->module,"deleted");
    		$BASEPATH = str_replace("system/","",BASEPATH);
    		@unlink($BASEPATH."upload/client_config/thumb_edit/".@$post['filepath']);
    		@unlink($BASEPATH."upload/client_config/thumb_list/".@$post['filepath']);
    		@unlink($BASEPATH."upload/client_config/".@$post['filepath']);

    		$this->client_config_model->delete_file($post);

    		$site_url = site_url("client/client_config/formEdit/".@$post["child_id"]);
    		$result = array("status"=>true
    			,"message"=>self::$message
    			,"url"=>$site_url);

    		echo json_encode($result);
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
            $this->client_config_model->update_parent($json_array,0);
        } else {
            show_404();
        }
	}

	public static function trigger($action = "",$id = 0,$data = array())
	{
		$obj = modules::run('client/client_config_trigger/set_trigger', $action, $id, $data);
		self::$message = $obj["message"];
		return $obj["status"];
	}

}