<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends Frontend {

	var $module = "setting";

	function __construct()
	{
		parent::__construct();
		
		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("setting_model");
	}

	function index()
	{	$get = $this->input->get();

        if(isset($get['tab'])) {
            redirect(site_url("setting#".$get['tab']));
            die;
        }
		$this->view();
	}

	function view()
	{	
		$config = $this->master_model->get_config();
		$viewdata = array();
		$viewdata['client_id'] = $this->setting_model->get_client_id();
		$viewdata['client'] = $this->setting_model->get_client();

		$viewdata['company'] = $this->setting_model->get_company_keyword();
		$viewdata['group_keyword'] = $this->setting_model->get_group_keyword();
		$viewdata['categories'] = $this->setting_model->get_categories();
		$viewdata['keyword'] = $this->setting_model->get_keyword();

		$viewdata['module']  = $this->module;
		$viewdata['setting_allow'] = $this->authen->getSettingAllow(); 
		// $viewdata['tag_keyword'] = $this->setting_model->get_tagkeyword();

		$viewdata['categories_allow'] = $this->authen->getCategoriesAllow(); 
		
		$this->template->set('inline_style', $this->load->view("setting_style",null,true));
		$this->template->set('inline_script', $this->load->view("setting_script",null,true));
		$this->template->build("setting_view",$viewdata);
	}

	function cmdGetKeywordSetting()
	{
		$id = $_POST['id'];
		if(isset($_POST['id'])) {
			$viewdata['tag_keyword'] = $this->setting_model->get_tagkeyword($id);
		} else {
			$result["message"] = "ไม่พบข้อมูล Keyword";
		}
		echo json_encode($viewdata);
	}
	function cmdDelKeyInEx()
	{
		$id = $_POST['id'];
		// echo $id;
		if(isset($_POST['id'])) {
			$viewdata['includeexclude_name'] = $this->setting_model->del_key_includeexclude($id);
			$this->master_model->save_log("Delete Tag Keyword ".$viewdata['includeexclude_name']);
		} else {
			// $result["message"] = "ไม่พบข้อมูล Keyword";
			// $result["status"]  = false;
		}
		echo json_encode($viewdata);
	}

	function cmdInsertKeyInEx()
	{
		$tag_keyword  = $_POST['key_tag'];
		$type_keyword = $_POST['type'];
		$keyword_id   = $_POST['key_id'];
		echo $tag_keyword;
		if(empty($_POST['key_id'])) {
			// $result["message"] = "กรุณากรอก Company name";
			echo "กรุณากรอก keyword";
		} else if(empty($_POST['key_tag'])) {
			// $result["message"] = "กรุณากรอก Company name";
			echo "กรุณากรอก tag keyword";
		} else if($this->setting_model->check_tag_keyword($tag_keyword , $type_keyword , $keyword_id)) {
			// $result["message"] = "ขออภัยคุณมี Company name นี้แล้ว";
			echo "ขออภัยคุณมี tag keyword นี้แล้ว";
		} 
		else {
			$include_exclude_keyword_name = $this->setting_model->insert_key_includeexclude($tag_keyword , $type_keyword , $keyword_id);
			$this->master_model->save_log("Add Tag Keyword ".$include_exclude_keyword_name);
			$result['include_exclude_keyword_name'] = $include_exclude_keyword_name;
			// $result["status"]  = true;
			echo $include_exclude_keyword_name;
		}
		// echo json_encode($result);
	}

	function cmdAddCompany()
	{
		$result = array();
		$post = $this->input->post();
		$config = $this->master_model->get_config();

		$row_competitor = count($this->setting_model->get_competitor_keyword());
		$add_competitor = isset($config['add_competitor']) ? $config['add_competitor'] : 2;

		if($row_competitor>=$add_competitor) {
			$result["message"] = "คุณสามารถเพิ่ม Competitor ได้สูงสุด ".$add_competitor." Company เท่านั้น";
			$result["status"]  = false;
		} else if(!isset($post['company_keyword_name']) || trim($post['company_keyword_name'])=="") {
			$result["message"] = "กรุณากรอก Company name";
			$result["status"]  = false;
		} else if($this->setting_model->check_company_keyword($post['company_keyword_name'])) {
			$result["message"] = "ขออภัยคุณมี Company name นี้แล้ว";
			$result["status"]  = false;
		} else if($post['company_keyword_type']!="Competitor") {
			$result["message"] = "คุณกรอก Company type ไม่ถูกต้อง";
			$result["status"]  = false;
		} else {
			$company_keyword_id = $this->setting_model->insert_company($post);
			$this->master_model->save_log("Add Company ".$post['company_keyword_name']);
			$result['company_keyword_id'] = $company_keyword_id;
			$result["status"]  = true;
		}
		echo json_encode($result);
	}

	function cmdDelCompany()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['company_keyword_id'])) {
			$result["status"]  = true;
			foreach($post['company_keyword_id'] as $val) {
				$company = $this->setting_model->get_company($val);
				if($company['company_keyword_type']=="") {
					$result["message"] = "คุณไม่สามารถลบ Company ของตัวคุณเองได้";
					$result["status"]  = false;
					break;
				} else {
					$name = $this->setting_model->delete_company($val);
					$this->setting_model->clean_keyword();
					$this->master_model->save_log("Delete Company ".$name);
				}
			}
		} else {
			$result["message"] = "กรุณาเลือก Company ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function cmdAddGroupKeyword()
	{
		$result = array();
		$post = $this->input->post();
		$config = $this->master_model->get_config();

		$row_group_keyword = count($this->setting_model->get_group_keyword());
		$add_group_keyword = isset($config['add_group_keyword']) ? $config['add_group_keyword'] : 10;

		if($row_group_keyword>=$add_group_keyword) {
			$result["message"] = "คุณสามารถเพิ่ม Group Keyword ได้สูงสุด ".$add_group_keyword." Group เท่านั้น";
			$result["status"]  = false;
		} else if(!isset($post['company_keyword_id']) || trim($post['company_keyword_id'])=="") {
			$result["message"] = "กรุณาเลือก Company";
			$result["status"]  = false;
		} else if(!isset($post['group_keyword_name']) || trim($post['group_keyword_name'])=="") {
			$result["message"] = "กรุณากรอก Group Keyword";
			$result["status"]  = false;
		} else if($this->setting_model->check_group_keyword($post['group_keyword_name'], $post['company_keyword_id'])) {
			$result["message"] = "ขออภัยคุณมี Group Keyword นี้แล้ว";
			$result["status"]  = false;
		} else if($this->setting_model->check_inactive_group_keyword($post['group_keyword_name'], $post['company_keyword_id'])) {
			$group_keyword_id = $this->setting_model->update_group_keyword_status_active($post);
			$this->master_model->save_log("Update Group Keyword status".$post['group_keyword_name']);
			$result["status"] = true;
		} else {
			$group_keyword_id = $this->setting_model->insert_group_keyword($post);
			$this->master_model->save_log("Add Group Keyword ".$post['group_keyword_name']);
			$result['group_keyword_id'] = $group_keyword_id;
			$result['company_keyword_id'] = $post['company_keyword_id'];
			$result["status"] = true;
		}
		echo json_encode($result);
	}

	function cmdDelGroupKeyword()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['group_keyword_id'])) {
			$result["status"]  = true;
			foreach($post['group_keyword_id'] as $val) {
				$name = $this->setting_model->delete_group_keyword($val);
				$this->setting_model->clean_keyword();
				$this->master_model->save_log("Delete Group Keyword ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Group Keyword ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}
	
	// function for check conditions before "insert" keyword name to database
	function cmdAddKeyword()
	{
		$result = array();
		$post = $this->input->post();
		$config = $this->master_model->get_config();

		$row_keyword = count($this->setting_model->get_keyword());
		$add_keyword = isset($config['add_keyword']) ? $config['add_keyword'] : 50;

		// condition check if c
		$category_allow = $this->authen->getCategoriesAllow();
		if($category_allow) {
			if($row_keyword >= $add_keyword) {
				$result["message"] = "คุณเพิ่ม Keyword ได้สูงสุด ".$add_keyword." Keyword เท่านั้น";
				$result["status"]  = false;
			} else if(!isset($post['categories_id']) || trim($post['categories_id'])=="") {
				$result["message"] = "กรุณาเลือก Category";
				$result["status"]  = false;
			} else if(!isset($post['keyword_name']) || trim($post['keyword_name'])=="") {
				$result["message"] = "กรุณากรอก Keyword";
				$result["status"]  = false;
			} else if(mb_strlen($post['keyword_name'])<3) {
				$result["message"] = "กรุณากรอก Keyword มากกว่า 3 ตัวอักษร";
				$result["status"]  = false;
			} else if($this->setting_model->check_keyword($post['keyword_name'], $post['categories_id'])) {
				$result["message"] = "ขออภัยคุณมี Keyword นี้แล้ว";
				$result["status"]  = false;
			} else if($this->setting_model->check_keyword_inactive($post['keyword_name'], $post['group_keyword_id'], $post['categories_id'])) {
				$this->setting_model->update_keyword_status_active($post);
				$result["message"] = "บันทึก Keyword เรียบร้อยแล้ว";
				$result["status"]  = true;
			} else {
				$keyword_id = $this->setting_model->insert_keyword_categories($post);
				$this->master_model->save_log("Add Keyword ".$post['keyword_name']);
				$result['keyword_id'] = $keyword_id;
				$result['categories_id'] = $post['categories_id'];
				$result["message"] = "บันทึก Keyword เรียบร้อยแล้ว";
				$result["status"]  = true;
			}
		} else {
			if($row_keyword>=$add_keyword) {
				$result["message"] = "คุณเพิ่ม Keyword ได้สูงสุด ".$add_keyword." Keyword เท่านั้น";
				$result["status"]  = false;
			} else if(!isset($post['group_keyword_id']) || trim($post['group_keyword_id'])=="") {
				$result["message"] = "กรุณาเลือก Group Keyword";
				$result["status"]  = false;
			} else if(!isset($post['keyword_name']) || trim($post['keyword_name'])=="") {
				$result["message"] = "กรุณากรอก Keyword";
				$result["status"]  = false;
			} else if(mb_strlen($post['keyword_name'])<3) {
				$result["message"] = "กรุณากรอก Keyword มากกว่า 3 ตัวอักษร";
				$result["status"]  = false;
			} else if($this->setting_model->check_keyword($post['keyword_name'], $post['group_keyword_id'])) {
				$result["message"] = "ขออภัยคุณมี Keyword นี้แล้ว";
				$result["status"]  = false;
			} else if($this->setting_model->check_keyword_inactive($post['keyword_name'], $post['group_keyword_id'])) {
				$this->setting_model->update_keyword_status_active($post);
				$result["message"] = "บันทึก Keyword เรียบร้อยแล้ว";
				$result["status"]  = true;
			} else {
				$keyword_id = $this->setting_model->insert_keyword($post);
				$this->master_model->save_log("Add Keyword ".$post['keyword_name']);
				$result['keyword_id'] = $keyword_id;
				$result['group_keyword_id'] = $post['group_keyword_id'];
				$result["message"] = "บันทึก Keyword เรียบร้อยแล้ว";
				$result["status"]  = true;
			}
		}
		echo json_encode($result);
	}
	// end of function cmdAddKeyword()

	// function for check conditions before "delete" keyword name from database
	function cmdDelKeyword() {
		$result = array();
		$post = $this->input->post();
		if(isset($post['keyword_id'])) {
			$result["status"]  = true;
			foreach($post['keyword_id'] as $val) {
				$name = $this->setting_model->delete_keyword($val);
				$this->setting_model->clean_keyword();
				$this->master_model->save_log("Delete Keyword ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Keyword ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}
	// end of function cmdDelKeyword()

	// function for check conditions before "insert" category name to database
	function cmdAddCategories() {
		$result = array();
		$post = $this->input->post();
		// $config = $this->master_model->get_config();

		$row_categories = count($this->setting_model->get_categories());
		$add_categories = 50;

		if($row_categories >= $add_categories) {
			$result["message"] = "คุณสามารถเพิ่ม Category ได้สูงสุด " . $add_categories . " คำเท่านั้น";
			$result["status"]  = false;
		} else if(!isset($post['group_keyword_id']) || trim($post['group_keyword_id'])=="") {
			$result["message"] = "กรุณาเลือก Group Keyword";
			$result["status"]  = false;
		} else if(!isset($post['categories_name']) || trim($post['categories_name'])=="") {
			$result["message"] = "กรุณากรอก Category Name";
			$result["status"]  = false;
		} else if($this->setting_model->check_categories($post['categories_name'], $post['group_keyword_id'])) {
			$result["message"] = "ขออภัยคุณมี Category Name นี้แล้ว";
			$result["status"]  = false;
		} else {
			$categories_id = $this->setting_model->insert_categories($post);
			$this->master_model->save_log("Add Category ".$post['categories_name']);
			$result['categories_id'] = $categories_id;
			$result['group_keyword_id'] = $post['group_keyword_id'];
			$result["status"] = true;
		}

		echo json_encode($result);
	}
	// end of function cmdAddCategories()

	// function for check conditions before "delete" category name from database
	function cmdDelCategories() {
		$result = array();
		$post = $this->input->post();
		if(isset($post['categories_id'])) {
			$result["status"]  = true;
			foreach($post['categories_id'] as $val) {
				$name = $this->setting_model->delete_categories($val);
				$this->setting_model->clean_keyword();
				$this->master_model->save_log("Delete Category ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Category ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}
	// end of function cmdDelCategories()

	function activity_log()
	{
		$viewdata = array();
		$viewdata['module'] = $this->module;
		$this->load->view("activity_log_view",$viewdata);
	}

	function activity_list()
	{
		$post = $this->input->get();
		$total_rows = 0;
		$rowsdata = $this->setting_model->get_activity_log($post,$total_rows);
		echo json_encode(array( "draw"=>$post['draw'],
			"recordsTotal"=>$total_rows,
			"recordsFiltered"=>$total_rows,
			"data"=>$rowsdata));
	}

	function block_user()
	{
		$viewdata = array();
		$viewdata['module'] = $this->module;
		$viewdata['rowsdata'] = $this->setting_model->get_block_user();
		$this->load->view("block_user_view",$viewdata);
	}

	function cmdUnblock()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['block_id'])) {
			$result["status"]  = true;
			foreach($post['block_id'] as $val) {
				$name = $this->setting_model->unblock($val);
				$this->master_model->save_log("Unblock User ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก User ที่ต้องการ Unblock";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function setting_filter($keyword_id = 0)
	{
		$keyword = $this->setting_model->get_keyword_id($keyword_id);
		if(isset($keyword['keyword_id'])) {
			$viewdata = array();
			$viewdata['setting_allow'] = $this->authen->getSettingAllow(); 
			$viewdata['post'] = $this->setting_model->get_keyword_id($keyword_id);
			$this->load->view("setting_filter_view",$viewdata);
		}
	}

	function cmdSaveKeywordSetting()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['keyword_id'])) {
			$name = $this->setting_model->save_keyword_setting($post);
			$this->master_model->save_log("Keyword Setting ".$name);
			$result["status"]  = true;
		} else {
			$result["message"] = "ไม่พบข้อมูล Keyword";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function check_fake_news()
	{
		set_time_limit(0);
		$result = array();
		$post = $this->input->post();

		if(isset($post["url_news"]) && $post["url_news"] != null){
			if(filter_var($post["url_news"], FILTER_VALIDATE_URL)){
				$scheme = parse_url($post["url_news"],PHP_URL_SCHEME);
				$url_news = parse_url($post["url_news"],PHP_URL_HOST);
				$url = "http://www.fakenewsai.com/detect?url=".$scheme."://".$url_news;
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"Accept: */*",
					"Cache-Control: no-cache",
					"Connection: keep-alive",
					"Host: www.fakenewsai.com",
					"Postman-Token: 13ffb318-18f5-4028-9612-0e13db898cbd,1dc10904-30d6-4e33-b339-6d974fb0b657",
					"User-Agent: PostmanRuntime/7.13.0",
					"accept-encoding: gzip, deflate",
					"cache-control: no-cache"
				  ),
				));
				$response = json_decode(curl_exec($curl),true);
				
				$err = curl_error($curl);
				curl_close($curl);
				
				if($err) {
					$result["message"] = "please check your internet connection.";
					$result["status"]  = false;

				}elseif($response["error"]	== true){
					$result["message"] = "Make sure you have entered a valid URL.";
					$result["status"]  = false;	

				}elseif($response["fake"] == true){
					$result["message"] = "This site is probably not a reliable news source.";
					$result["status"]  = true;
					$result["fake"]  = true;

				}else{
					$result["message"] = "This site is probably not a fake news site.";
					$result["status"]  = true;
					$result["fake"]  = false;					
				}
			}else{
				$result["message"] = "Make sure you have entered a valid URL.";
				$result["status"]  = false;
			}
		}else{
			$result["message"] = "Please input a valid URL.";
			$result["status"]  = false;
		}
		
		echo json_encode($result);
	}

	function cmdUpdateCompany_status()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['company_keyword_id'])) {
			$result["status"]  = true;
			foreach($post['company_keyword_id'] as $val) {
				$name = $this->setting_model->update_company_keyword_status($val);
				// $this->setting_model->clean_keyword();
				$this->master_model->save_log("Update Company Keyword Status ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Company ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function cmdUpdateGroupKeyword_status()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['group_keyword_id'])) {
			$result["status"]  = true;
			foreach($post['group_keyword_id'] as $val) {
				$name = $this->setting_model->update_group_keyword_status($val);
				// $this->setting_model->clean_keyword();
				$this->master_model->save_log("Update Group Keyword Status ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Group Keyword ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function cmdUpdateCategories_status() {
		$result = array();
		$post = $this->input->post();
		if(isset($post['categories_id'])) {
			$result["status"]  = true;
			foreach($post['categories_id'] as $val) {
				$name = $this->setting_model->update_categories_status($val);
				// $this->setting_model->clean_keyword();
				$this->master_model->save_log("Update Categories Status ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Category ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function cmdUpdateKeyword_status()
	{
		$result = array();
		$post = $this->input->post();
		if(isset($post['keyword_id'])) {
			$result["status"]  = true;
			foreach($post['keyword_id'] as $val) {
				$name = $this->setting_model->update_keyword_status($val);
				// $this->setting_model->clean_keyword();
				$this->master_model->save_log("Update Keyword Status ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Keyword ที่ต้องการลบ";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}

	function cmdUpdateKeyword() {
		$result = array();
		$post = $this->input->post();
		if(isset($post['keyword_id'])) {
			$result["status"]  = true;
			foreach($post['keyword_id'] as $val) {
				$name = $this->setting_model->update_keyword($val);
				// $this->setting_model->clean_keyword();
				$this->master_model->save_log("Update Keyword ".$name);
			}
		} else {
			$result["message"] = "กรุณาเลือก Keyword ที่ต้องการอัปเดต";
			$result["status"]  = false;
		}
		echo json_encode($result);
	}
}
?>