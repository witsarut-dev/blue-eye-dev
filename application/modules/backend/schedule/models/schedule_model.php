<?php
class Schedule_model extends CI_Model 
{
    var $USER_ID = '';

    function __construct()
    {
        parent::__construct();
        $this->USER_ID = $this->session->userdata("USER_ID");
    }

    function get_client()
    {
    	$rows = $this->db
    				->select("client.client_id,client.company_name")
    				->where("client.sys_status","active")
    				->where("client.sys_action","publish")
                    ->order_by("client.company_name")
    				->get("client")
    				->result_array();
    	return $rows;
    }

    function get_schedule_by_client($client_id = 0)
    {
    	$rows = $this->db
    				->select("schedule.*")
    				->where("schedule.sys_users_id",$this->USER_ID)
    				->where("schedule.client_id",$client_id)
    				->where("client.sys_status","active")
    				->where("client.sys_action","publish")
    				->order_by("schedule.schedule_day,schedule.schedule_start")
    				->join("client","client.client_id = schedule.client_id")
    				->get("schedule")
    				->result_array();
    	return $rows;
    }

    function get_schedule()
    {
    	$rows = $this->db
    				->select("client.company_name,schedule.*")
    				->where("schedule.sys_users_id",$this->USER_ID)
    				->where("client.sys_status","active")
    				->where("client.sys_action","publish")
    				->join("client","client.client_id = schedule.client_id")
    				->order_by("schedule.schedule_day,schedule.schedule_start")
    				->get("schedule")
    				->result_array();
    	return $rows;
    }

    function get_client_by_color()
    {
    	$rows = $this->db
    				->select("client.company_name,client.client_id,schedule.schedule_color")
    				->group_by("client.company_name,client.client_id,schedule.schedule_color")
    				->where("schedule.sys_users_id",$this->USER_ID)
    				->where("client.sys_status","active")
    				->where("client.sys_action","publish")
    				->join("client","client.client_id = schedule.client_id")
    				->order_by("client.company_name")
    				->get("schedule")
    				->result_array();
    	return $rows;
    }

	function manage($post = array())
	{
		$days = $this->get_days();
		$save_batch = array();
		$color = $post['color'];
		$client_id = $post['client_id'];

		foreach($days as $day) {
			if(isset($post[$day])) {
				$schedule = array();
				$rows = 0;
				$start = "";
				for($i=0;$i<count($post[$day]);$i++) {
					if($start=="") $start = $post[$day][$i];
					$num1 = intval(@$post[$day][$i])+1;
					$num2 = intval(@$post[$day][$i+1]);
					if($num1!=$num2 || !isset($post[$day][$i+1])) {
						$end  = ($num1<10) ? "0{$num1}:00" : "{$num1}:00";
						$save = array();
						$save["schedule_day"]   = $day; 
						$save["schedule_start"] = $start;
						$save["schedule_end"]   = $end;
						$save["schedule_color"] = $color;
						$save["sys_users_id"]   = $this->USER_ID;
						$save["client_id"]      = $client_id;
						array_push($save_batch,$save);

						$start = @$post[$day][$i+1];
					}
				}
			}
		}

		$this->db->where("sys_users_id",$this->USER_ID);
		$this->db->where("client_id",$client_id);
		$this->db->delete("schedule");

		if(count($save_batch)>0) {
			$this->db->insert_batch('schedule',$save_batch);
		}

        $rec = $this->db->where("client_id",$client_id)->get("client")->first_row("array");
        return @$rec['username'];
	}

	function get_color($code_color = "")
	{
		switch ($code_color) {
			case 'c-default': $color = '#3a87ad'; break;
			case 'c-yellow': $color = '#fff105'; break;
			case 'c-light-green': $color = '#5cb85c'; break;
			default: $color = str_replace("c-", "", $code_color); break;
		}
		return $color;
	}

	function get_days()
	{
		$days = array("MON","TUE","WED","THU","FIR","SAT","SUN");
		return $days;
	}

}