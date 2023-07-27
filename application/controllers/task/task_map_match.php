<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_map_match extends MX_Controller {

	var $item_run_time = 10;
  	var $item_run_now = 10000;
  	var $item_process = 100;
	var $tb_match = "own_match";
	var $tb_key_match = "own_key_match";

	function __construct()
	{
		parent::__construct();
        $this->load->helper("common");
        date_default_timezone_set('Asia/Bangkok');
        ignore_user_abort(1);
        set_time_limit(0);
		ini_set('memory_limit', '2048M');
		//global $ii;
		//$ii = 1;
	}

	function run_map_match($frequency="") // ""=runnow DairyFeed  1=runone Feed 23.00
  	{
        // echo MONGO_CONNECTION;
        $mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
		if(empty($frequency)){
			$collection = $mongodb->selectCollection("DairyFeed");
		}else{
			$collection = $mongodb->selectCollection("Feed");
		}
		$query = array("feedlocation" => new MongoRegex("/ /"));
        $cursor = $collection->find($query);
				// $cursor->limit(100);
		$cursor->timeout(-1);
        $cursorArray = iterator_to_array($cursor, false);
		$mongo->close();
		 // var_dump($cursorArray);
		 
		if(!empty($cursorArray)){
			$id_api = 1;
        	$arrlength = count($cursorArray);
        	$j = 0;
			   
			while ($j < $arrlength){
				$v_row = $cursorArray[$j];
				$ad = json_decode($v_row['feedlocation'], JSON_UNESCAPED_UNICODE);
				if(isset($ad['name'])){
					$raw_address = $ad['name'];
					$address= preg_replace("/[\r\n]+/", "\n", $raw_address);
					// echo $address;
					// die;
					$location_same_dict = $this->check_location_samedict($address);

					if(empty($location_same_dict)){
						$ch = curl_init();
						$locate = urlencode($address);
						$key_api = 'AIzaSyCba9rsdojemmiPfGo4CI5nmR-HZdU5gYQ';
						// $key = $this->get_keyapi($id_api);
						$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$locate."&key=".$key_api."&sensor=false";
						curl_setopt($ch, CURLOPT_URL,$url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$output = curl_exec($ch);
						curl_close ($ch);
						$geo = json_decode($output,true);

						if($geo['status'] == 'OVER_QUERY_LIMIT'){
							$id_api++; // get new api and do geocode again
						}elseif($geo['status'] == 'OK'){
							$geo_results = $geo['results'][0];
							$geo_components = $geo_results['address_components'];
							$geo_geometry_location = $geo_results['geometry']['location'];
							$latitude = $geo_geometry_location['lat'];
							$longitude = $geo_geometry_location['lng'];
							$geo_components_lenght = count($geo_components);

							for($k = 0;$k <= $geo_components_lenght;$k++)
							{
								$check_type_components =$geo_components[$k]['types'];
								if(in_array('political',$check_type_components)){
									$geo_location = $geo_components[$k]['long_name'];
									$location_dict = $this->check_location_dict($geo_location);
									if(!empty($location_dict)){
										$this->insert_location_same_dict($address,$location_dict['loc_id']);
									}else{
										$this->insert_location_dict($geo_location,$latitude,$longitude,$address);
									}
								break;
								}elseif (in_array('natural_feature',$check_type_components)){
									$geo_location = $geo_components[$k]['long_name'];
									$location_dict = $this->check_location_dict($geo_location);
									if(!empty($location_dict)){
										$this->insert_location_same_dict($address,$location_dict['loc_id']);
									}else{
										$this->insert_location_dict($geo_location,$latitude,$longitude,$address);
									}
								break;
								}
							}
							$j++;
						}else{
							$j++; // skip to next if not empty
						}
						usleep(500000);
					}else{
						$j++;
					}

					$location_match = $this->check_location_dict_joinsame($address);
					if(!empty($location_match)){
						$latlong = array('latitude' => $location_match['loc_latitude'], 'longitude' => $location_match['loc_longitude']);
						$this->match_map_data($v_row,$latlong);
					}

				}else if(isset($ad['latitude']) && isset($ad['longitude'])){
					$latlong = array('latitude' => $ad['latitude'], 'longitude' => $ad['longitude']);
					$this->match_map_data($v_row,$latlong);
					$j++;
				}else{
					$j++;
				}
			}
		}
	}

	function match_map_data($v_row=array(),$latlong=array())
	{	
		$client_idbymsg_id = $this->get_client_bymsg_id($v_row['_id']);
		if(!empty($client_idbymsg_id)){
					
			foreach ($client_idbymsg_id as $key => $value){

				$in_map_match = $this->check_map_match($value['client_id'],$value['msg_id']);

				if(empty($in_map_match)){
				
					$sub_content = mb_substr($v_row['feedcontent'],0,50);
					$msg_content = $sub_content."... ";
					$keyword = $this->get_keyword($value['own_match_id'],$value['client_id'],$value['company_keyword_id']);

					$save=array();
					$save["map_match_client"]  			= $value['client_id'];
					$save["map_match_timepost"]  		= $v_row['feedtimepost'];
					$save["map_match_keyword_id"]  		= $keyword['keyword_id'];
					$save["map_match_keyword_name"] 	= $keyword['keyword_name'];
					$save["map_match_msg_id"]  			= $v_row['_id'];
					$save["map_match_username"]  		= $value['post_user'];
					$save["map_match_msg_content"]  	= $msg_content;
					$save["map_match_sourceid"]  		= $v_row['sourceid'];
					$save["map_match_sentiment"]  		= $value['own_match_sentiment'];
					$save["map_match_latitude"]  		= $latlong['latitude'];
					$save["map_match_longitude"]  		= $latlong['longitude'];
					$save["map_match_link"]  			= $v_row['feedlink'];
					$this->db->insert("map_match",$save);

				}else if(empty($in_map_match["map_match_sentiment"])){
					$update=array();
					$update["map_match_sentiment"]  	= $value['own_match_sentiment'];
					$this->db->where("map_match_id",$in_map_match["map_match_id"]);
					$this->db->update("map_match",$update);
				}
			}
		}
	}

	function insert_location_same_dict($address="",$loc_id="")
	{
		$save = array();
		$save["loc_same_name"]  = $address;
		$save["loc_id"]         = $loc_id;
		$this->db->insert("location_same_dict",$save);
		// echo $address." ".$loc_id." Same locationdict##<br />";

	}

	function insert_location_dict($geo_location="",$latitude="",$longitude="",$address="")
	{
		$save = array();
		$save["loc_name"]  = $geo_location;
		$save["loc_latitude"]  = $latitude;
		$save["loc_longitude"] = $longitude;
		$this->db->insert("location_dict",$save);

		$loc_id = $this->db->insert_id();

		$save2 = array();
		$save2["loc_same_name"] = $address;
		$save2["loc_id"]        = $loc_id;
		$this->db->insert("location_same_dict",$save2);
		// echo $geo_location." ".$latitude." ".$longitude." ".$address." ".$loc_id." NOTSAME<br />";

	}

    function check_location_samedict($locate="")
		{
        $result = $this->db
                ->select("*")
                ->like("loc_same_name",$locate)
                ->get("location_same_dict")
                ->first_row('array');
        return $result;
    }

    function check_location_dict($locate="")
	{
        $result = $this->db
                ->select("*")
                ->where("loc_name",$locate)
                ->get("location_dict")
                ->first_row('array');
        return $result;
    }

    function get_keyapi($numid="")
		{
        $result = $this->db
                ->select("api_key")
                ->where("api_id",$numid)
                ->get("key_api")
                ->first_row('array');
        return $result;
  	}

		function get_client_bymsg_id($msg_id="")
		{
		$result = $this->db
						->select("*")
						->where("msg_id",$msg_id)
						->where("msg_status","1")
						->where("own_match_sentiment IS NOT NULL",null,false)
						->get("own_match")
						->result_array();
		return $result;
		}

		function get_keyword($own_match_id="",$client_id="",$company_keyword_id="")
		{
			$result = $this->db
							->select("*")
							->join("keyword","keyword.keyword_id=own_key_match.keyword_id","inner")
							->where("own_key_match.own_match_id",$own_match_id)
							->where("own_key_match.client_id",$client_id)
							->where("own_key_match.company_keyword_id",$company_keyword_id)
							->get("own_key_match")
							->first_row('array');
			return $result;
		}

		function check_location_dict_joinsame($locate="")
		{
			$result = $this->db
							->select("*")
							->where("location_same_dict.loc_same_name",$locate)
							->join("location_same_dict","location_same_dict.loc_id=location_dict.loc_id","inner")
							->get("location_dict")
							->first_row('array');
			return $result;
		}

		function check_map_match($client_id,$msg_id)
		{
			$result = $this->db
							->select("*")
							->where("map_match_client",$client_id)
							->where("map_match_msg_id",$msg_id)
							->get("map_match")
							->first_row('array');				
			return $result;
		}


}
?>
