<?php
function get_custom_date($custom_date = "")
{
	$period="Today";

	@list($start,$end) = explode("-",$custom_date);
    $start = trim($start);
    $end   = trim($end);
    @list($d1,$m1,$y1) = explode("/",$start);
    @list($d2,$m2,$y2) = explode("/",$end);
    $start = $y1."-".$m1."-".$d1;
    $end   = $y2."-".$m2."-".$d2;

    $time_diff = abs(strtotime($end)-strtotime($start));

	$seconds    = $time_diff;
    $days       = round($time_diff / 86400 );
    $months     = round($time_diff / 2600640 );

    if($months>=1) {
    	$period = "M";
    } else if($days>=1) {
    	$period = "W";
    }

    return array("start"=>$start,"end"=>$end,"period"=>$period);
}

function get_graph_month($m = 1)
{	$month = array('','Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	return @$month[$m];
}

function get_soruce_full($sourceid = 1)
{
	$source = array("1"=>"facebook","2"=>"twitter","3"=>"youtube","4"=>"news","5"=>"webboard","6"=>"instagram","7"=>"tiktok","8"=>"blockdit","9"=>"line");
	return @$source[$sourceid];
}

function get_soruce_short($sourceid = 1)
{
	$source = array("1"=>"FB","2"=>"TW","3"=>"YT","4"=>"NW","5"=>"WB","6"=>"IG","7"=>"TT","8"=>"BD","9"=>"LN");
	return @$source[$sourceid];
}

function get_media_type_id($media_type = "")
{
	$source = array("facebook"=>"1","twitter"=>"2","youtube"=>"3","news"=>"4","webboard"=>"5","instagram"=>"6","tiktok"=>"7","blockdit"=>"8","line"=>"9");
	return @$source[$media_type];
}

function get_media_type($media_type = "")
{
	$media_type = strtolower($media_type);
	$source = array("fb"=>"facebook","tw"=>"twitter","yt"=>"youtube","nw"=>"news","wb"=>"webboard","ig"=>"instagram","tt"=>"tiktok","bd"=>"blockdit","ln"=>"line");
	return @$source[$media_type];
}

function get_post_type($type = "")
{
	$type = strtolower($type);
	switch ($type) {
		case 'facebook':
			$icon = '<li><i class="ico ico-facebook"></i></li>';
			break;
		case 'twitter':
			$icon = '<li><i class="ico ico-twitter"></i></li>';
			break;
		case 'youtube':
			$icon = '<li><i class="ico ico-youtube"></i></li>';
			break;
		case 'news':
			$icon = '<li><i class="ico ico-news"></i></li>';
			break;
		case 'webboard':
			$icon = '<li><i class="ico ico-webboard"></i></li>';
			break;
		case 'instagram':
			$icon = '<li><i class="ico ico-instagram"></i></li>';
			break;
		case 'tiktok':
			$icon = '<li><i class="ico ico-tiktok"></i></li>';
			break;
		case 'blockdit':
			$icon = '<li><i class="ico ico-blockdit"></i></li>';
			break;
		case 'line':
			$icon = '<li><i class="ico ico-line"></i></li>';
			break;	
		default:
			$icon = '<li class="web-name"><strong class="name" data-toggle="tooltip" title="'.$type.'">'.$type.'</strong></li>';
			break;
	}
	return $icon;
}

function get_post_detail_type($type = "")
{
	$type = strtolower($type);
	$sourceid = get_media_type_id($type);
	$icon = get_icon_post_type($sourceid).' '.ucfirst($type);
	return $icon;
}

function get_icon_post_type($sourceid = 1)
{
	switch ($sourceid) {
		case '1':
			$icon = '<i class="ico ico-fb"></i>';
			break;
		case '2':
			$icon = '<i class="ico ico-tw"></i>';
			break;
		case '3':
			$icon = '<i class="ico ico-yt"></i>';
			break;
		case '4':
			$icon = '<i class="ico ico-nw"></i>';
			break;
		case '5':
			$icon = '<i class="ico ico-wb"></i>';
			break;
		case '6':
			$icon = '<i class="ico ico-ig"></i>';
			break;
		case '7':
			$icon = '<i class="ico ico-tt"></i>';
			break;
		case '8':
			$icon = '<i class="ico ico-bd"></i>';
			break;
		case '9':
			$icon = '<i class="ico ico-ln"></i>';
			break;
		default:
			$icon = '';
			break;
	}
	return $icon;
}

function get_media_color($sourceid = 1)
{
	$source = array("1"=>"#1877F2","2"=>"#55ACEE","3"=>"#FF0000","4"=>"#25D366","5"=>"#5A189A","6"=>"#FF4D6D","7"=>"#010101","8"=>"#4A69FF","9"=>"#99D98C");
	return @$source[$sourceid];
}

function get_post_time($time = "")
{
	date_default_timezone_set("Asia/Bangkok"); //Apilarb change for grap time error
	$time_diff = abs(time()-strtotime($time));
	$seconds    = $time_diff;
	$minutes    = round($time_diff / 60 );
    $hours      = round($time_diff / 3600);
    $days       = round($time_diff / 86400 );
    $weeks      = round($time_diff / 604800);
    $months     = round($time_diff / 2600640 );
    $years      = round($time_diff / 31207680 );

	if($seconds <= 60) {
		return "Just Now";
	} else if($minutes <=60){
		return $minutes." Mins.";
	} else if($hours <=24){
		return $hours." hrs.";
	} else if($days <= 30){
		return $days." Days";
	} else if($months <=12){
		return $months." Month";
	} else {
		return $years." Year";
	}
}

function get_post_link($post_link,$post = array())
{
	if($post['sourceid']=="2" && strpos($post_link,"https://")===false) {
		return "https://twitter.com/".$post['post_name']."/status/".$post_link;
	} else {
		return $post_link;
	}
}

function get_sentiment_detail($sentiment,$class = "",$id_post_sentiment) {
	if($sentiment>0) {
		$icon = '<span style="color:#2ad600;">Positive</span>';
	} else if($sentiment<0) {
		$icon = '<span style="color:#FF3F3F;">Negative</span>';
	} else {
		$icon = '<span style="color:#E2E2E2;">Neutral</span>';
	}
	return $icon;
}

function get_sentiment($sentiment, $class = "", $id_post_sentiment) {
    $icon = '<select id="'. abs($id_post_sentiment) .'" onchange="edit_sentiment_realtime(event)" style="-webkit-appearance: none; -moz-appearance: none; -o-appearance: none; appearance: none; text-align: center; float: right; width: 100px; color: #ffffff; border: 0px; border-radius: 8px; background-color:';
    
    if($sentiment > 0) {
        $icon .= '#25D366;" name="edit_sentiment1"">';
        $icon .= '<option selected style="background-color:#25D366;">Positive</option>';
        $icon .= '<option value="2" style="background-color:#FF3F3F;">Negative</option>';
        $icon .= '<option value="3" style="background-color:#E2E2E2;">Neutral</option>';
    } else if($sentiment < 0) {
        $icon .= '#FF3F3F;" name="edit_sentiment2"">';
        $icon .= '<option value="1" style="background-color:#25D366;">Positive</option>';
        $icon .= '<option selected style="background-color:#FF3F3F;">Negative</option>';
        $icon .= '<option value="3" style="background-color:#E2E2E2;">Neutral</option>';
    } else {
        $icon .= '#CACACA;" name="edit_sentiment3"">';
        $icon .= '<option value="1" style="background-color:#25D366;">Positive</option>';
        $icon .= '<option value="2" style="background-color:#FF3F3F;">Negative</option>';
        $icon .= '<option selected style="background-color:#E2E2E2;">Neutral</option>';
    }
    $icon .= '</select>';

	$icon .= '<script>
                    document.getElementById('. abs($id_post_sentiment) .').addEventListener("change", function() {
                        var select = this;
                        var value = select.options[select.selectedIndex].value;
                        if(value == 1) {
                            select.style.backgroundColor = "#25D366";
                        } else if(value == 2) {
                            select.style.backgroundColor = "#FF3F3F";
                        } else {
                            select.style.backgroundColor = "#E2E2E2";
                        }
                    });
              </script>';

    return $icon;
}

function get_sentiment_analysis($sentiment,$class = "",$id_post_sentiment) {
	if($sentiment>0) {
		$icon = '<select id = '.abs($id_post_sentiment).' name="edit_sentiment1" onchange="edit_sentiment_realtime(event)" style="   -webkit-appearance: none;
		-moz-appearance: none;
		-o-appearance: none;
		appearance: none;  border:0px; color:#25D366; background-color: transparent;">
		<option selected="" value="1" style="color:#25D366;" disabled>Positive</option>
		<option value="2" style="color:#FF3F3F;">Negative</option>
		<option value="3" style="color:#000000;">Neutral</option>
	</select>';
	} else if($sentiment<0) {
		$icon = '<select id = '.abs($id_post_sentiment).' name="edit_sentiment2" onchange="edit_sentiment_realtime(event)" style="   -webkit-appearance: none;
		-moz-appearance: none;
		-o-appearance: none;
		appearance: none;  border:0px; color:#FF3F3F; background-color: transparent;">
		<option value="1" style="color:#25D366;">Positive</option>
		<option selected="" value="2" style="color:#FF3F3F;" disabled>Negative</option>
		<option value="3" style="color:#000000;">Neutral</option>
	</select>';
	} else {
		$icon = '<select id = '.abs($id_post_sentiment).' name="edit_sentiment3" onchange="edit_sentiment_realtime(event)" style="   -webkit-appearance: none;
		-moz-appearance: none;
		-o-appearance: none;
		appearance: none;  border:0px; color:#000000; background-color: transparent;">
		<option value="1" style="color:#25D366;"">Positive</option>
		<option value="2" style="color:#FF3F3F;">Negative</option>
		<option selected="" value="3" style="color:#000000;" disabled>Neutral</option>
	</select>';
	}
	return $icon;
}

// Convert from sentiment int value to string value -> 05-03-2021 15:53
function get_text_sentimentLastDOM($sentiment,$class = "",$id_post_sentiment){ 
	if($sentiment>0) {
		$icon_text = "Positive";
	} else if($sentiment<0) {
		$icon_text = "Negative";
	} else {
		$icon_text = "Neutral";
	}
	return $icon_text;
}

function get_text_Doc($sentimentAnalysis){ 
	if($sentimentAnalysis > 0) {
		$sentiment = "Positive";
	} else if($sentimentAnalysis < 0) {
		$sentiment = "Negative";
	} else {
		$sentiment = "Neutral";
	}
	return $sentiment;
}

function display_post_name($post_name = "",$sourceid = "",$key = 1)
{
	if($sourceid==4) {
		$post_arr = explode(":", $post_name);
		return @$post_arr[$key];
	} else {
		return ($post_name==null) ? '<i style="color:#999">Unknown</i>' : $post_name;
	}
}

function tag_keyword($post_detail = "",$display_keyword = array())
{
	foreach($display_keyword as $val)
	{
		$post_detail = preg_replace('/('.$val.')/i', '<span class="tags-keyword">$1</span>', $post_detail);
	}
	return $post_detail;
}

function get_sentiment_api($text = "",$timeout = "")
{
    $ch = curl_init();
    $key  = S_SENSE_KEY;

    // curl_setopt($ch, CURLOPT_URL,"http://sansarn.com/api/ssense-v2.php");
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS,"text={$text}&key={$key}");
    $q = urlencode($text);
    $q = curl_escape($ch,$q);
    curl_setopt($ch, CURLOPT_URL,"http://10.130.90.103/SSenseV2/Analyze");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"q={$q}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($timeout!="") curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
    $output = curl_exec($ch);
    curl_close ($ch);


    $obj = json_decode($output,true);

    $sentiment = null;

    if(isset($obj['sentiment']['polarity']) && $text!="") {
        if(@$obj['sentiment']['polarity']=="positive") {
            $sentiment = floatval($obj['sentiment']['score']);
        } else if(@$obj['sentiment']['polarity']=="negative") {
            $sentiment = floatval("-".$obj['sentiment']['score']);
        } else {
            $sentiment = 0;
        }
    }
    return $sentiment;
}

function log_run_time($text_log = "")
{
    echo $text_log.date("Y-m-d H:i:s")."\n";
}

function get_access_token()
{
    if(!isset($_SESSION)) session_start();
    $CI =& get_instance();
    $encryption_key = $CI->config->item('encryption_key');
    $session_id = session_id();
    return md5($session_id).md5($encryption_key);
}

function get_match_table($tb = "",$period = "Today")
{
	// $tb = "own_match" , $period = "Today"

    if($period=="Today" || $period=="") {
        return $tb."_daily ".$tb;
		// own_match_daily own_match
    } else if($period=="1W" || $period=="1M" || $period=="3M") {
		return $tb."_3months ".$tb;
	} else {
		return $tb;
    }
}

function encodeURIComponent($str) 
{
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            array_push($new_array, $array[$k]);
        }
    }

    return $new_array;
}

function get_fb_access_token()
{
	$CI =& get_instance();
	// $rec = $CI->db
	// 		->select("access_token")
	// 		->order_by("token_id","desc")
	// 		->get("sys_access_token")
	// 		->first_row('array');
	$rec = $CI->db
			->select("access_token")
			->where("token_type", "serverToken")
			->where("status", "active")
			->get("sys_access_token")
			->first_row('array');

	return isset($rec['access_token']) ? $rec['access_token'] : '';
}

function where_client_expire()
{
	$sql = "(
		( DATE(DATE_ADD(client.createdate,INTERVAL 1 MONTH)) >= '".date("Y-m-d")."' AND client.client_group LIKE '%Demo%' )
		OR
		( '".date("Y-m-d")."' BETWEEN client.start_join AND client.end_join AND client.client_group LIKE '%Client%' ))";
	return $sql;
}

function mongodb_query($collect = "", $filter = array())
{
	$result = array();
	$version = (float)phpversion();
	if($version >= 7) {
		$mongo = new MongoDB\Driver\Manager(MONGO_CONNECTION);
        $query = new MongoDB\Driver\Query($filter); 
        $cursor = $mongo->executeQuery("blue_eye.".$collect, $query);
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
        $result = $cursor->toArray();
	} else {
		$mongo = new MongoClient(MONGO_CONNECTION);
        $mongodb = $mongo->blue_eye;
        $collection = $mongodb->selectCollection($collect);
        $cursor = $collection->find($filter);
        $result = iterator_to_array($cursor,false);	
	}
	return $result;
}

function sentiment_multilang($text,$lang_code)
{
	// $text = urlencode($text);
	$lang_code = urlencode($lang_code);
	$url = 'https://apis.paralleldots.com/v4/sentiment';
	$data = array();
	$data['api_key'] = "Jp2mqnooTi9gKnm8wdmYTzVGA6pJwymzjvtMt2JYYeE";
	$data['text'] = $text;
	$data['lang_code'] = $lang_code;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type: multipart/form-data"));
	$response = curl_exec($ch);
	return $response;
}

function get_sentiment_ida_api($_id = "",$text = "",$timeout = "")
{
	$key  = "ACC9R9jwCvjAVYMgKFqv2nnMkJ2TVrJNdNLdeVx63caxx6o3XuSeMzmZUqvFcpf6FCG3q2aVGhx9xSAivcDMTrds5EXPeB8AbUCMkLjIIs";
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "http://178.128.115.233/sentiment/api/v2?token=".$key,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 600,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => "id={$_id}&content={$text}",
		CURLOPT_HTTPHEADER => array(
		  'Content-Type: application/x-www-form-urlencoded'
		),
	));
	$output = curl_exec($curl);
	curl_close($curl);

	$obj = json_decode($output,true);
	$sentiment = null;
	if(isset($obj['data']['status']) && $text!="") {
		if(@$obj['data']['status']=="neg") {
            $sentiment = floatval("-".$obj['data']['tensor'][0]);
        }else if(@$obj['data']['status']=="pos") {
            $sentiment = floatval($obj['data']['tensor'][2]);
		}else if(@$obj['data']['status']=="neu" && $obj['data']['tensor'][2] > $obj['data']['tensor'][0]) {
            $sentiment = floatval($obj['data']['tensor'][2]);
        }else {
            $sentiment = 0;
        }
	}
	return $sentiment;
}
?>