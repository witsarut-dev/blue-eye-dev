<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_monitoring extends Frontend {

	var $module = "post_monitoring";
    var $fb_link = "https://www.facebook.com/";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("post_monitoring_model");
		$this->load->model("setting/setting_model");
		$this->load->model("overview/overview_model");
        $this->load->library("linkapi");
	}

	function index()
	{	
		$this->view();
	}

	function view($post_id = 0)
	{	
        $post   = $this->input->post();
        
		$viewdata = array();
		$viewdata['module']  = $this->module;
        $viewdata['client_post'] = $this->post_monitoring_model->get_client_post();
        $viewdata['post_id'] = $post_id;
		$this->template->set('inline_style', $this->load->view("post_monitoring_style",null,true));
		$this->template->set('inline_script', $this->load->view("post_monitoring_script",$viewdata,true));
		$this->template->build("post_monitoring_view",$viewdata);
	}

    function open_post()
    {
        $post = $this->input->post();
        if($post) {
            $post_id = $post['post_id'];
            $rec = $this->post_monitoring_model->get_post_id($post_id);
            if(!isset($rec['post_id'])) {
                $result["message"] = "ไม่พบข้อมูล Post ที่ต้องการ";
                $result["status"]  = false;
            } else {
                $post_id = $rec['post_id'];
                $result['post_id'] = $rec['post_id'];
                $result['post_name'] = $rec['post_name'];
                $result['post_url'] = $rec['post_url'];
                $result['post_renew'] = (strtotime($rec['end_date']) <= strtotime("now")) ? true : false;
                $result["status"] = true;
            }
            echo json_encode($result);
        }
    }

    function cmdAddPost()
    {
        $result = array();
        $post = $this->input->post();
        $message = "";

        if(!isset($post['post_name']) || trim($post['post_name'])=="") {
            $result["message"] = "กรุณากรอก Post Name";
            $result["status"]  = false;
            $result['error']   = "post_name";
        } else if($this->post_monitoring_model->check_post_name($post)) {
            $result["message"] = "ขออภัยคุณมี Post Name นี้แล้ว";
            $result["status"]  = false;
            $result['error']   = "post_name";
        } else if(!isset($post['post_url']) || trim($post['post_url'])=="") {
            $result["message"] = "กรุณากรอก Post Link";
            $result["status"]  = false;
            $result['error']   = "post_url";
        } else if($this->post_monitoring_model->check_post_url($post)) {
            $result["message"] = "ขออภัยคุณมี Post Link นี้แล้ว ";
            $result["status"]  = false;
            $result['error']   = "post_url";
        } else if($this->check_post_link($post,$message)) {
            $result["message"] = "ไม่พบข้อมูล Post Link หรือ Post Link ไม่ถูกต้อง ".$message;
            $result["status"]  = false;
            $result['error']   = "post_url";
        } else if($this->post_monitoring_model->check_post_max($add_post_monitoring) && @$post['post_id']=="") {
            $result["message"] = "ขออภัยคุณเพิ่มข้อมูลเกิน ".$add_post_monitoring." ครั้งแล้ว<br />คุณจะสามารถเพิ่มข้อมูลได้อีกครั้งในเดือนถัดไป";
            $result["status"]  = false;
            $result['error']   = "post_max";
        } else {
            if(!isset($post['post_id']) || $post['post_id']=="") {
                $post_id = $this->post_monitoring_model->insert_post($post);
                $this->master_model->save_log("Add Post Monitoring ".$post['post_name']);
                $result['action'] = "Add";
            } else {
                $post_id = $this->post_monitoring_model->update_post($post);
                $this->master_model->save_log("Edit Post Monitoring ".$post['post_name']);
                $result['action'] = "Edit";
            }
            $rec = $this->post_monitoring_model->get_post_id($post_id);
            $result['post_expire'] = (strtotime($rec['end_date']) <= strtotime("now")) ? true : false;
            $result['post_id'] = $post_id;
            $result["status"]  = true;
        }
        echo json_encode($result);
    }

    function cmdDelPost()
    {
        $result = array();
        $post = $this->input->post();
        if(isset($post['post_id'])) {
            $result["status"]  = true;
            $name = $this->post_monitoring_model->delete_post($post['post_id']);
            $this->master_model->save_log("Delete Post Monitoring ".$name);
        } else {
            $result["message"] = "กรุณาเลือก Post ที่ต้องการลบ";
            $result["status"]  = false;
        }
        echo json_encode($result);
    }

    private function check_post_link($post = array(),&$message = "")
    {
        $error = false;
        $post_url = $post['post_url'];
        $result = $this->linkapi->check_link_msg($post_url);
        
        if(!$result) {
            $error = true;
            $message = $post_url;
        }

        return $error;
    }

    function ajax_post_data()
    {
        $result = array();
        $result['post_id'] = 0;
        $result['series'] = array();
        $result['change'] = array();
        $result['flags'] = array();
        $result['post_renew'] = false;
        $result['subtitle'] = "";
        $result['title'] = "";
        $get = $this->input->get();
        $post = $this->input->post();
        $max_time = isset($post['max_time']) ? $post['max_time'] : 0;
        if($get) {
            if(isset($get['post_id']) && $get['post_id']!="" && isset($get['action'])) {
                $post_id = $result['post_id'] = $get['post_id'];
                $action  = $get['action'];
                $data  = $this->get_post_data($post_id,$action,$max_time);
                $rec = $this->post_monitoring_model->get_post_id($post_id);

                $result['post_renew'] = (strtotime($rec['end_date']) <= strtotime("now")) ? true : false;
                $result['series'] = $data['series'];
                $result['change'] = $data['change'];
                $result['flags']  = $data['flags'];

                if(!isset($post['max_time'])) {
                    $start_date = date("d/m/Y H:i",strtotime($rec['start_date']));
                    $end_date = date("d/m/Y H:i",strtotime($rec['end_date']));
                    $result['title']  = $rec['post_name'];
                    $result['subtitle'] = 'Start time '.$start_date.' to '.$end_date;
                }
            }
            echo json_encode($result);
        }
    }

    function ajax_post_all()
    {
        $result = array();
        $result['post_id'] = 0;
        $result['series_likes'] = array();
        $result['series_shares'] = array();
        $result['series_comments'] = array();
        $result['subtitle'] = "";
        $get = $this->input->get();
        $post = $this->input->post();
        $max_time = isset($post['max_time']) ? $post['max_time'] : 0;
        if($get) {
            if(isset($get['post_id']) && $get['post_id']!="") {
                $post_id = $result['post_id'] = $get['post_id'];
                $data['series_likes']  = $this->get_post_data($post_id,'likes',$max_time);
                $data['series_shares']  = $this->get_post_data($post_id,'shares',$max_time);
                $data['series_comments']  = $this->get_post_data($post_id,'comments',$max_time);
                $max = count($data['series_likes']['series']);
                $rec = $this->post_monitoring_model->get_post_id($post_id);
           
                $result['series_likes'] = $data['series_likes']['series'];
                $result['series_shares'] = $data['series_shares']['series'];
                $result['series_comments']  = $data['series_comments']['series'];

                if(!isset($post['max_time'])) {
                    $start_date = date("d/m/Y H:i",strtotime($rec['start_date']));
                    $end_date = date("d/m/Y H:i",strtotime($rec['end_date']));
                    $result['title']  = $rec['post_name'];
                    $result['subtitle'] = 'Start time '.$start_date.' to '.$end_date;
                }
            }
            echo json_encode($result);
        }
    }

    function ajax_post_comment()
	{
		// $period = $this->master_model->get_period();
        $result = array();
        $result['post_id'] = 0;
        $get = $this->input->get();
		$post = $this->input->post();

		$total_rows = 0;

        if($get) {
            if(isset($get['post_id']) && $get['post_id']!="") {
                $post_id = $result['post_id'] = $get['post_id'];
                $rowsdata = $this->post_monitoring_model->get_post_comment($post_id,$total_rows);
            }
            
            echo json_encode(array( "draw"=>$post['draw'],
                    "recordsTotal"=>$total_rows,
                    "recordsFiltered"=>$total_rows,
                    "data"=>$rowsdata));
        }
    }

    private function get_post_data($post_id = 0,$action = "likes",$max_time = 0)
    {
        $result['series'] = array();
        $result['change'] = array();
        $result['flags'] = array();
        
        $rec = $this->post_monitoring_model->get_post_id($post_id);
        if($rec['msg_id']) {
            $data = $this->post_monitoring_model->get_mongo_post($rec['msg_id']);
            $start_date = strtotime($rec['start_date']);
            $end_date = strtotime($rec['end_date']);
            $flags_mark = json_decode($rec['flags_mark'],true);

            $data = isset($data[0][$action]) ? $data[0][$action] : array();
            foreach($data as $key=>$val) {
                $time = strtotime(date("Y-m-d H:i",strtotime($val['timestamp'])));
                if(($time*1000)>$max_time) {
                    $count = intval($val[$action.'_count']);
                    if($key==0) {
                        $change = 0;
                    } else {
                        $change = intval($data[$key-1][$action.'_count']);
                        $change = ($count - $change);
                    }
                    if($time>=$start_date && $time<=$end_date) {
                        $timestamp = ($time*1000);
                        array_push($result['series'],array($timestamp,$count));
                        array_push($result['change'],array($timestamp,$change));
                        if(isset($flags_mark[$action][$timestamp])) {
                            $datetime = date("d/m/Y H:i",$time);
                            array_push($result['flags'],array("x"=>$timestamp,"datetime"=>$datetime));
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function cmdAddMark()
    {
        $result = array();
        $post = $this->input->post();
        $post_id = $post['post_id'];
        $action = $post['action'];
        $time = $post['time'];
        $rec = $this->post_monitoring_model->get_post_id($post_id);
        if($rec['post_id']) {
            $data = $this->post_monitoring_model->get_mongo_post($rec['msg_id']);
            $data = isset($data[0][$action]) ? $data[0][$action] : array();
            if($this->get_mark_time($time,$data)) {
                $flags_mark = json_decode($rec['flags_mark'],true);
                if(!isset($flags_mark['likes'])) $flags_mark['likes'] = array();
                if(!isset($flags_mark['shares'])) $flags_mark['shares'] = array();
                if(!isset($flags_mark['comments'])) $flags_mark['comments'] = array();
                $flags_mark[$action][$time] = date("Y-m-d H:i",($time/1000));
                $post['flags_mark'] =  $flags_mark;
                $this->post_monitoring_model->update_mark($post);
                $result['flags'] = $this->get_flags($flags_mark[$action]);
                $result['status'] = true;
            } else {
                $result['status'] = false;
            }
        } else {
            $result['status'] = false;
        }
        $result['action'] = $action;
        echo json_encode($result);
    }

    public function cmdDelMark()
    {
        $result = array();
        $post = $this->input->post();
        $post_id = $post['post_id'];
        $action = $post['action'];
        $time = $post['time'];
        $rec = $this->post_monitoring_model->get_post_id($post_id);
        if($rec['post_id']) {
            $flags_mark = json_decode($rec['flags_mark'],true);
            if(!isset($flags_mark['likes'])) $flags_mark['likes'] = array();
            if(!isset($flags_mark['shares'])) $flags_mark['shares'] = array();
            if(!isset($flags_mark['comments'])) $flags_mark['comments'] = array();
            unset($flags_mark[$action][$time]);
            $post['flags_mark'] = $flags_mark;
            $this->post_monitoring_model->update_mark($post);
            $result['flags'] = $this->get_flags($flags_mark[$action]);
            $result['status'] = true;
        } else {
            $result['status'] = false;
        }
        $result['action'] = $action;
        echo json_encode($result);
    }

    private function get_flags($flags_mark = array())
    {
        $flags = array();
        foreach ($flags_mark as $timestamp => $datetime) {
            $time = ($timestamp/1000);
            $datetime = date("d/m/Y H:i",$time);
            array_push($flags,array("x"=>$timestamp,"datetime"=>$datetime));
        }
        return array_sort($flags,'x',SORT_ASC);
    }

    private function get_mark_time($time,$time_list = array()) 
    {
        foreach ($time_list as $key => $val) {
            $datetime = date("Y-m-d H:i",strtotime($val['timestamp']));
            $timestamp = strtotime($datetime) * 1000;
            if($time==$timestamp) {
                return true;
            }
        }
        return false;
    }

  
    // function export_file controllers
    function export_file_post($post_id=0){
        $data_export = $this->post_monitoring_model->get_data_export($post_id);
        $title_client = $this->post_monitoring_model->get_client_post();
        $Post_id = $post_id;

        for($a=0;$a<count($title_client);$a++){
            
            if ($Post_id == $title_client[$a]['post_id'])
            {
                $Name_title = $title_client[$a]['post_name'];
            }
        }
        if($data_export){

            header('Content-type: application/vnd.ms-excel');
		    header('Content-Disposition: attachment; filename="BlueEyePostmonitoringID- '.$Name_title.'.xlsx"');
		    error_reporting(E_ALL);
		    ini_set('memory_limit', '2048M');
		    set_time_limit(0);

            $this->load->library("PHPExcel");

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle($post_id);

            $objPHPExcel->getActiveSheet()->SetCellValue("A1","Link URL");
            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Comment User");
            $objPHPExcel->getActiveSheet()->SetCellValue("C1","Content");
            $objPHPExcel->getActiveSheet()->SetCellValue("D1","Total");
            $objPHPExcel->getActiveSheet()->SetCellValue("E1","Like");
            $objPHPExcel->getActiveSheet()->SetCellValue("F1","Love");
            $objPHPExcel->getActiveSheet()->SetCellValue("G1","WoW");
            $objPHPExcel->getActiveSheet()->SetCellValue("H1","Laugh");
            $objPHPExcel->getActiveSheet()->SetCellValue("I1","Sad");
            $objPHPExcel->getActiveSheet()->SetCellValue("J1","Angry");
            $objPHPExcel->getActiveSheet()->SetCellValue("K1","Care");
            $objPHPExcel->getActiveSheet()->SetCellValue("L1","Create Time");
            $objPHPExcel->getActiveSheet()->SetCellValue("M1","Source URL");

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(40);

            $rows = 2;
            $objPHPExcel->getActiveSheet()->SetCellValue("A".$rows,$data_export[0]['Url_post']);

                foreach($data_export as $k_row=>$v_row) {    
                    $objPHPExcel->getActiveSheet()->SetCellValue("B".$rows,$v_row['Comment_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("C".$rows,$v_row['Content_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("D".$rows,$v_row['Total_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("E".$rows,$v_row['Like_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("F".$rows,$v_row['Love_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("G".$rows,$v_row['Wow_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("H".$rows,$v_row['Laugh_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("I".$rows,$v_row['Sad_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("J".$rows,$v_row['Angry_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("K".$rows,$v_row['Care_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("L".$rows,$v_row['Time_post']);
                    $objPHPExcel->getActiveSheet()->SetCellValue("M".$rows,$v_row['Source_post']);
                    $rows++;

                }
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save('php://output');
        }
        else{
            header('Content-type: application/vnd.ms-excel');
		    header('Content-Disposition: attachment; filename="BlueEyePostmonitoringID-'.$Name_title.'.xlsx"');
		    error_reporting(E_ALL);
		    ini_set('memory_limit', '2048M');
		    set_time_limit(0);

            $this->load->library("PHPExcel");

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle($post_id);

            $objPHPExcel->getActiveSheet()->SetCellValue("A1","Link URL");
            $objPHPExcel->getActiveSheet()->SetCellValue("B1","Comment User");
            $objPHPExcel->getActiveSheet()->SetCellValue("C1","Content");
            $objPHPExcel->getActiveSheet()->SetCellValue("D1","Total");
            $objPHPExcel->getActiveSheet()->SetCellValue("E1","Like");
            $objPHPExcel->getActiveSheet()->SetCellValue("F1","Love");
            $objPHPExcel->getActiveSheet()->SetCellValue("G1","WoW");
            $objPHPExcel->getActiveSheet()->SetCellValue("H1","Laugh");
            $objPHPExcel->getActiveSheet()->SetCellValue("I1","Sad");
            $objPHPExcel->getActiveSheet()->SetCellValue("J1","Angry");
            $objPHPExcel->getActiveSheet()->SetCellValue("K1","Care");
            $objPHPExcel->getActiveSheet()->SetCellValue("L1","Create Time");
            $objPHPExcel->getActiveSheet()->SetCellValue("M1","Source URL");

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(40);


            $rows = 2;              
            $objPHPExcel->getActiveSheet()->SetCellValue("A".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("B".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("C".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("D".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("E".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("F".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("G".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("H".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("I".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("J".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("K".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("L".$rows,"No data");
            $objPHPExcel->getActiveSheet()->SetCellValue("M".$rows,"No data");

            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save('php://output');
        }
        
    }
    //------------------------------------------------------------------------test
    function test_data_postmonitoring(){

        $data_export = $this->post_monitoring_model->test_get_data_export(75);    //fig post id
        var_dump($data_export);


    }
	
}