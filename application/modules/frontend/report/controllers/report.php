<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends Frontend {

	var $module = "report";

	function __construct()
	{
		parent::__construct();

		$this->authen->checkLogin();
        $this->authen->checkPermission();

		$this->load->model("master_model");
		$this->load->model("report_model");
		$this->load->model("overview/overview_model");
		$this->load->model("realtime/realtime_model");
		$this->load->model("analysis/analysis_model");
		$this->load->model("post_monitoring/post_monitoring_model");
		$this->load->model("marketing/marketing_model");
		$this->load->model("setting/setting_model");
	}

	function index()
	{	
		$this->view();
	}

	function view()
	{	
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;
		
		$category = $this->marketing_model->get_category();
		$company  = $this->setting_model->get_company_keyword();
		$sentiment = $this->marketing_model->get_sentiment($post);

		$viewdata = array();
		$viewdata['period'] = $period;
		$viewdata['module'] = $this->module;
		$viewdata['custom_date'] = $this->master_model->get_custom_date();
		$viewdata['totalData'] = $this->overview_model->get_total_data($post);
		$viewdata['keywordData'] = $this->overview_model->get_keyword_data($post);
		$viewdata['mention'] = $this->report_model->get_mention_data($post);
		$viewdata['sm'] = $this->report_model->get_social_media_data($post);
		$viewdata['nonsm'] = $this->report_model->get_non_social_media_data($post);
		$viewdata['test'] = $this->report_model->get_mentions_per_category($post);
		$viewdata['sentimentData'] = $this->report_model->get_graph_sentiment_data($post);
		$viewdata['getGroupKeyword'] = $this->report_model->get_group_keyword();

		# view data content
		$viewdata['summary_of_mentions'] = $this->get_summary_of_mentions();
		$viewdata['top_user'] = $this->report_model->get_top_user($post);
		$viewdata['top_share'] = $this->report_model->get_top_share($post);
		$viewdata['sources_count'] = $this->report_model->get_sources_count($post);

		$this->template->set('inline_style', $this->load->view("report_style", null, true));
		$this->template->set('inline_script', $this->load->view("report_script", $viewdata, true));
		$this->template->build("report_view", $viewdata);
	}
	
	function get_feed()
	{
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		$post['period'] = $period;

		$viewdata = array();
		$viewdata['rowsdata'] = $this->report_model->get_feed($post);
		$this->load->view("report_list",$viewdata);
	}		
	
	function generate_report()
	{
		$summaryOfMentions                   = $_POST['summaryOfMentions'];
		$volumeOfMentions                    = $_POST['volumeOfMentions'];
		$socialMediaReachGraph               = $_POST['socialMediaReachGraph'];
		$nonSocialMediaReachGraph            = $_POST['nonSocialMediaReachGraph'];
		$mentionsPerCategory                 = $_POST['mentionsPerCategory'];
		$tableGroupKeywordList               = $_POST['tableGroupKeywordList'];
		$graphSentimentMonitoringAnalysis    = $_POST['graphSentimentMonitoringAnalysis'];
		$topUser                             = $_POST['topUser'];
		$topShare                            = $_POST['topShare'];
		$volumeOfMentionsB64                 = $_POST['volumeOfMentionsB64'];
		$socialMediaReachGraphB64            = $_POST['socialMediaReachGraphB64'];
		$nonSocialMediaReachGraphB64         = $_POST['nonSocialMediaReachGraphB64'];
		$mentionsPerCategoryB64              = $_POST['mentionsPerCategoryB64'];
		$graphSentimentMonitoringAnalysisB64 = $_POST['graphSentimentMonitoringAnalysisB64'];

		$this->load->library('pdf');
		$period = $this->master_model->get_period();
		$post = $this->input->post();
		$post['period'] = $period;

		$intime_key = $this->report_model->check_intime_keyword($post);
		if (!empty($intime_key['Intime'])) {
			$check_pathdir = $this->mkdir_sesname(); //create dir for session report
			$viewdata = array();
			$viewdata['mediaData'] = $this->overview_model->get_media_data($post);
			$graph = array();
			$graph['client'] = $this->setting_model->get_client(); //project name
			$graph['dateRange'] = $this->report_model->get_daterange($post); //date range (from select period)
			if ($summaryOfMentions == "summaryOfMentions") {
				$graph['summaryOfMentions']['volum'] = $this->overview_model->get_total_data($post);
				$graph['summaryOfMentions']['social'] = $this->get_count_media($viewdata['mediaData']);
				$graph['summaryOfMentions']['nonsocial'] = $this->get_count_media($viewdata['mediaData']);
				$graph['summaryOfMentions']['positive'] = $this->overview_model->get_sentiment_data($post);
				$graph['summaryOfMentions']['negative'] = $this->overview_model->get_sentiment_data($post);
			}
			if ($volumeOfMentions == "volumeOfMentions") {
				$graph['volumeOfMentions']['img'] = $volumeOfMentionsB64;
				for ($i = 0; $i < count($this->report_model->get_mention_data($post)) ; $i++) {
					$graph['volumeOfMentions']['maxValue'] = max( array_column( $this->report_model->get_mention_data($post), 'count'));
					$graph['volumeOfMentions']['minValue'] = min( array_column( $this->report_model->get_mention_data($post), 'count'));
				}
			}
			if ($socialMediaReachGraph == "socialMediaReachGraph") {
				$graph['socialMediaReachGraph']['img'] = $socialMediaReachGraphB64;
				for ($i = 0; $i < count($this->report_model->get_social_media_data($post)) ; $i++) {
					$graph['socialMediaReachGraph']['maxValue'] = max( array_column( $this->report_model->get_social_media_data($post), 'count'));
					$graph['socialMediaReachGraph']['minValue'] = min( array_column( $this->report_model->get_social_media_data($post), 'count'));
				}
			}
			if ($nonSocialMediaReachGraph == "nonSocialMediaReachGraph") {
				$graph['nonSocialMediaReachGraph']['img'] = $nonSocialMediaReachGraphB64;
				for ($i = 0; $i < count($this->report_model->get_non_social_media_data($post)) ; $i++) {
					$graph['nonSocialMediaReachGraph']['maxValue'] = max( array_column( $this->report_model->get_non_social_media_data($post), 'count'));
					$graph['nonSocialMediaReachGraph']['minValue'] = min( array_column( $this->report_model->get_non_social_media_data($post), 'count'));
				}
			}
			if ($mentionsPerCategory == "mentionsPerCategory") {
				$substr = explode("@",$mentionsPerCategoryB64);
				$test = $this->report_model->get_mentions_per_category($post);
				for ($i = 0; $i < count($this->report_model->get_mentions_per_category($post)) ; $i++) {
					$graph['mentionsPerCategory']['count']['fb'] = array_sum(array_column( $test['countFB'], 'count'));
					$graph['mentionsPerCategory']['count']['tw'] = array_sum(array_column( $test['countTW'], 'count'));
					$graph['mentionsPerCategory']['count']['yt'] = array_sum(array_column( $test['countYT'], 'count'));
					$graph['mentionsPerCategory']['count']['nw'] = array_sum(array_column( $test['countNW'], 'count'));
					$graph['mentionsPerCategory']['count']['pt'] = array_sum(array_column( $test['countPT'], 'count'));
					$graph['mentionsPerCategory']['count']['ig'] = array_sum(array_column( $test['countIG'], 'count'));
					$graph['mentionsPerCategory']['count']['tt'] = array_sum(array_column( $test['countTT'], 'count'));
					$graph['mentionsPerCategory']['count']['ln'] = array_sum(array_column( $test['countLN'], 'count'));
				}
				$graph['mentionsPerCategory']['img'] = $substr;
			}
			if ($tableGroupKeywordList == "tableGroupKeywordList") {
				$graph['tableGroupKeywordList']['sum'] = $this->report_model->get_group_keyword_list($post);
			}
			if ($graphSentimentMonitoringAnalysis == "graphSentimentMonitoringAnalysis") {
				$graph['graphSentimentMonitoringAnalysis']['img'] = $graphSentimentMonitoringAnalysisB64;
			}
			if ($topUser == "topUser") {
				$graph['topUser'] = $this->report_model->get_top_user($post);
			}
			if ($topShare == "topShare") {
				$graph['topShare'] = $this->report_model->get_top_share($post);
			}

			$stream_name = $graph['client']['company_name'] . '.pdf';

			// comment below for testing new design of pdf -- 2022-12-29 (witsarut - view)
			// $html = $this->load->view('report_genPDF', $graph, true);
			$html = $this->load->view("report_gen_pdf", $graph, true);

			$dompdf = $this->pdf->loadPDF();
			$dompdf->set_paper('A4', 'portrait');
			$dompdf->load_html($html);
			$dompdf->render();
			$dompdf->stream($stream_name, array("Attachment" => false));

			if (!empty($graph) && !empty($check_pathdir)) {
				$files = glob($check_pathdir . '/*'); // get all file names
				foreach ($files as $file) { // iterate files
					if (is_file($file))
						unlink($file); // delete file
				}
				rmdir($check_pathdir);
			}
			echo json_encode($graph);
		}
	}
	function get_graph_overview_sentiment($post=array())
	{	
		$g_name = "overview_sentiment";
		$result = array();
		$data = array();
		$data = $this->overview_model->get_sentiment_data($post);
		if(!empty($data)){
			$Positive = $data['Positive'];
			$Negative = $data['Negative'];
			$Normal = $data['Normal'];
		
		$options = "{	
						credits: {
							enabled: false
						},
						exporting:{
							enabled: false
						},
						chart: {
							backgroundColor: null ,
							type: 'pie'
						},
						title: {
							text: null,
							style: {
								fontSize:'28px',
								fontWeight: 'bold'
							}
						},
						series: [{
							minPointSize: 10,
							innerSize: '20%',
							zMin: 0,
							data: [
								{
									name: 'Positive ".$Positive."%',
									dataLabels: {
										enabled: true,
										color: '#4aa23c',
										style: {
											textOutline: false 
										}
									},
									y: ".$Positive.",
									color: '#4aa23c'
								},
								{
									name: 'Normal ".$Normal."%', 
									dataLabels: {
										enabled: true,
										color: '#000000',
										style: {
											textOutline: false 
										}
									},
									y: ".$Normal.",
									color: '#cccccc',
								},
								{
									name: 'Negative ".$Negative."%',
									dataLabels: {
										enabled: true,
										color: '#f33923',
										style: {
											textOutline: false 
										}
									},
									y: ".$Negative.",
									color: '#f33923'
								}
							],
							size: '80%',
							innerSize: '50%',
							dataLabels: {
								distance : 15,
								style: { 
									fontSize:'18px'
								}
							}
			

						}]
					};";

			$result = $this->curl_filename($options,$g_name);
			}
		return $result;
	}

	function get_graph_top10keyword($post=array())
	{
		$g_name = "top10keyword";
		$result = array();
		$data = array();
		$data = $this->analysis_model->get_top_keyword_x($post,10,1);
		// print_r($data);
		// die;
		if(!empty($data)){
			$sub_data = "";
			$sum_mention ="";
			foreach($data as $k_row => $v_row){
				$sum_mention += $v_row['TotalMen'];	
			}
			foreach($data as $k_row => $v_row){
				$percent = ($v_row['TotalMen']/$sum_mention)*100;
				$sub_data .= "{
								name:'".$v_row['keyword_name']."',
								y: ".$v_row['TotalMen'].",
								per: ".$percent."
							},";
			}
		
			$options = "{
				credits: {
					enabled: false
				},
				exporting:{
					enabled: false
				},
				chart: {
					backgroundColor: null ,
					type: 'column'
				},
				plotOptions: {
					series: {
						borderWidth: 0,
						dataLabels: {
							enabled: true,
							format: '{point.y}<br>{point.per:.1f}%',
							style: {
								textOutline: false 
							}
						}
					}
				},
				title: {
					text: null,
					style: {
						fontSize:'28px',
						fontWeight: 'bold'
					}

				},
				xAxis: {
					type: 'category',
					labels: {
						style: {
							fontSize:'16px'
						}
					}
				},
				yAxis: {
					title: {
						text: 'Total mention'
					}
			
				},
				legend: {
					enabled: false
				},
				series: [
					{
						colorByPoint: true,
						data: [
							".$sub_data."
						],
						size: '80%',
						innerSize: '60%',
						dataLabels: {
							distance : 10,
							style: { 
								fontSize:'12px'
							}
						}
					}
				]
			}";

			$result = $this->curl_filename($options,$g_name);
		}
		return $result;
	}

	function get_graph_top5sentiment($post=array())
	{
		$g_name = "top5sentiment";
		$result = array();
		$result = $this->report_model->get_top5sentiment($post);
		// $result = $this->overview_model->get_media_data($post);
		
		return $result;

	}

	function get_graph_media_monitoring($post=array()){
		$g_name = "media_monitoring";
        $result = array("mediaCategories"=>array(),"mediaData"=>array());
		$data = array();
		$data = $this->overview_model->get_media_data($post);

		$data_media = null;
		$data_channel = null;
		if(!empty($data)){
			foreach($data['mediaData'] as $k_row => $v_row){
				$data_media .= "{
					name:'".substr_replace($v_row['mediaChannel'],'', 0, 26)." ".$v_row['countPercent']."%',
					y: ".$v_row['y'].",
					color : '".$v_row['color']."'
				},";
				// <img src='".theme_assets_url()."images/".substr($v_row['mediaChannel'],18,2).".png'>
				$sentiment_bar = null;
				if($v_row["countPositive"] > 0){
					$positive_bar = '<th style="background: #4aa23c; text-align: center; width: '.$v_row['countPositive'].'%;"><div style="height:20px; color: white;position:relative;top:-10px;" class="strong">'.$v_row["countPositive"].'%</div></th>';
					$sentiment_bar .= $positive_bar;
				}
				if($v_row["countNegative"] > 0){
					$negative_bar = '<th style="background: #f33923; text-align: center; width: '.$v_row['countNegative'].'%;"><div style="height:20px; color: white;position:relative;top:-10px;" class="strong">'.$v_row["countNegative"].'%</div></th>';
					$sentiment_bar .= $negative_bar;
				}
				if($v_row["countNegative"] == 0 && $v_row["countPositive"]==0){
					$negative_bar = '<th style="background: #cccccc;text-align: center; width: 100%;" colspan="2"><div style="height:20px; color: #000000;position:relative;top:-10px;" class="strong">0%</div></th>';
					$sentiment_bar .= $negative_bar;
				}
				$data_channel .= '<table style="width: 100%; border-spacing: 0;margin-top:10px">
									<thead>
										<tr>
											<th style="text-align:left; border: 0px;"><div class="strong" style="font-size: 24px;">&nbsp;<img src="themes/default/assets/images/'.substr($v_row["mediaChannel"],18,2).'.png">' .substr_replace($v_row["mediaChannel"],"", 0, 26).'</div></th>
											<th class="alignRight"><div class="strong" style="font-size: 22px;">'.$v_row["countPercent"].'%&nbsp;&nbsp;</div></th>
										</tr>
									</thead>
								</table>
								<table style="width: 100%; border-spacing: 0;">
								<thead>
									<tr>
										'.$sentiment_bar.'
									</tr>
								</thead>
							</table>';
			}

			$options = "
			{	
				credits: {
					enabled: false
				},
				exporting:{
					enabled: false
				},
				chart: {
					backgroundColor: null ,
					type: 'pie',
					spacingRight: 0,
					spacingLeft: 0,
					spacingTop: 0,
					spacingBottom: 0,
					marginRight: 10,
					marginLeft: 10,
					marginTop: 0,
					marginBottom: 0
				},
				title: {
					text: null			
				},
				series: [{
					name: 'Media Channel',
					data: [
						".$data_media."
					],
					size: '60%',
					innerSize: '50%',
					dataLabels: {
						distance : 15,
						style: { 
							fontSize:'14px',
							textOutline: false 
						},
					}
				}]
			}";
		
			$mediaCategories = $this->curl_filename($options,$g_name);
			array_push($result["mediaCategories"],$mediaCategories);
			array_push($result["mediaData"],$data_channel);	
		}
		
		return $result;
	}

	function get_graph_post_monitoring($post = array()){
		$result = array();
		$all_result = array();
		// $result = $this->report_model->data_test();
		$result = $this->report_model->get_post_monitoring($post);
		// $perpage = ceil(count($result)/6) ;
		// print_r($perpage);
		$result = array_slice($result,0,12);
		// die;
		return $result;
	}

	function get_graph_marketing($post = array()){
		$result = array();
		$category = $this->marketing_model->get_category();
		$company  = $this->setting_model->get_company_keyword();
		$sentiment = $this->marketing_model->get_sentiment($post);
		$categoryData = $this->marketing_model->get_category_data($category);

		$positiveData = $this->marketing_model->get_positive_data($category,$company,$sentiment);
		// print_r(json_encode($positiveData));

		if(!empty($positiveData)){
			$g_name = "positiveMarketing";
			$series = json_encode($positiveData);
			$categories = json_encode($categoryData);
			$options = '{
				credits: {
					enabled: false
				},
				exporting:{
					enabled: false
				},
				chart: {
					backgroundColor: null,
					polar: true,
					type: "line",
					spacingRight: 0,
					spacingLeft: 0,
					spacingTop: 0,
					spacingBottom: 0,
					marginRight: 0,
					marginLeft: 0,
					marginTop: "-40",
					marginBottom: 0
				},
				title: {
					text: null
				},
				pane: {
					size: "60%"
				},
				legend: {
					itemStyle: {
						fontWeight: "bold",
						fontSize: "18px"
					}
				},
				xAxis: {
					categories: '.$categories.',
					tickmarkPlacement: "on",
					lineWidth: 0,
					labels: {
						style: {
							fontSize:"28px"
						}
					}
				},
				yAxis: {
					gridLineInterpolation: "polygon",
					lineWidth: 0,
					min: 0,
					max: 1
				},
				series: '.$series.' ,
				plotOptions: {
					series: {
						marker: {
							enabled: false
						}
					}
				}
			}';

			$positiveMarketing = $this->curl_filename($options,$g_name);
		}
		
		$negativeData = $this->marketing_model->get_negative_data($category,$company,$sentiment);
		$mediaCategories = $this->curl_filename($options,$g_name);
		if(!empty($negativeData)){
			$g_name = "negativeMarketing";
			$series = json_encode($negativeData);
			$categories = json_encode($categoryData);
			
			$options = '{
				
				credits: {
					enabled: false
				},
				exporting:{
					enabled: false
				},
				chart: {
					backgroundColor: null,
					polar: true,
					type: "line",
					spacingRight: 0,
					spacingLeft: 0,
					spacingTop: 0,
					spacingBottom: 0,
					marginRight: 0,
					marginLeft: 0,
					marginTop: "-40",
					marginBottom: 0
				},
				title: {
					text: null
				},
				pane: {
					size: "60%"
				},
				legend: {
					itemStyle: {
						fontWeight: "bold",
						fontSize: "18px"
					}
				},
				xAxis: {
					categories: '.$categories.',
					tickmarkPlacement: "on",
					lineWidth: 0,
					labels: {
						style: {
							fontSize:"28px"
						}
					}
				},
				yAxis: {
					gridLineInterpolation: "polygon",
					lineWidth: 0,
					min: 0,
					max: 1,
				},
				series: '.$series.' ,
				plotOptions: {
					series: {
						marker: {
							enabled: false
						},
						dataLabels: {
							style: { 
								fontSize:"24px"
							}
						}
					}
				}
			}';

			$negativeMarketing = $this->curl_filename($options,$g_name);
		}
		$result['positiveMarketing'] = $positiveMarketing;
		$result['negativeMarketing'] = $negativeMarketing;
		return $result;
	}

	function get_graph_Position($post=array()){
		$result = array();
		$media_com = $this->master_model->get_media_com();

		$marketPosData  = $this->overview_model->get_marketpos_data($post);
		$mediaPosData   = $this->report_model->get_mediapos_data_withImg($post,$media_com);
		
		if(!empty($marketPosData)){
			$g_name = 'marketPosition';
			$series = json_encode($marketPosData);
			$options = '{
				exporting:{
					enabled: false
				},
				chart : {
					backgroundColor: null,
					style: {
						fontFamily: "Poppins, sans-serif"
					},
					marginTop: "70",
					marginLeft : "25",
					zoomType: "xy",					
					className: "line-arrow"			

				}
				,
				credits: {
					enabled: false
				},
				title: {
					text: null,
					useHTML: true
				},
				xAxis: {
					title: {
						text: "Mention",
						offset: -150,
						align: "high"
					},
					plotLines: [{
						color: "#000000",
						width: 2,
						value: 0.5
					}],
					max: 1,
					min: 0,
					tickInterval : 1
				},
				yAxis: {
					title: {
						align: "high",
						rotation: 0,
						text: "Sentiment",
						y : 15,
						offset : -170
					},
					plotLines: [{
						color: "#000000",
						width: 2,
						value: 0
					}],
					max: 1,
					min: -1,
					tickInterval : 1
				},
				legend: {
					layout: "vertical",
					align: "right",
					verticalAlign: "top",
					borderWidth: 0,
					floating: true,
					y : -15,
					symbolPadding: 5,
					itemStyle: {
						fontWeight: "bold",
						fontSize: "18px"
					}
					
				},
				series: '.$series.',
				plotOptions: {
					series: {
						marker: {
							enabled: false
						}
					}
				}
			}';
			
			$marketPosition = $this->curl_filename($options,$g_name);
		}
		
		if(!empty($mediaPosData)){
			$g_name = 'mediaPosition';
			$series = json_encode($mediaPosData);
			$options = '{
				exporting:{
					enabled: false
				},
				chart : {
					backgroundColor: null,
					style: {
						fontFamily: "Poppins, sans-serif"
					},
					marginTop: "70",
					marginLeft : "25",
					zoomType: "xy",
					className: "line-arrow"			
				},
				credits: {
					enabled: false
				},
				title: {
					text: null,
					useHTML: true
				},
				xAxis: {
					title: {
						text: "Mention",
						offset: -150,
						align: "high"
					},
					plotLines: [{
						color: "#000000",
						width: 2,
						value: 0.5
					}],
					max: 1,
					min: 0,
					tickInterval : 1
				},
				yAxis: {
					title: {
						align: "high",
						rotation: 0,
						text: "Sentiment",
						y : 15,
						offset : -170
					},
					plotLines: [{
						color: "#000000",
						width: 2,
						value: 0
					}],
					max: 1,
					min: -1,
					tickInterval : 1
				},
				legend: {
					layout: "vertical",
					align: "right",
					verticalAlign: "top",
					borderWidth: 0,
					floating: true,
					y : -15,
					useHTML: true,
					symbolWidth: 15,
					itemStyle: {
						fontWeight: "bold",
						fontSize: "18px"
					}
				},
				series: '.$series.',
				plotOptions: {
					series: {
						marker: {
							enabled: false
						}
					}
				}
			}';
			
			$mediaPosition = $this->curl_filename($options,$g_name);
		}
		$result['marketPosition'] = $marketPosition;
		$result['mediaPosition'] = $mediaPosition;
		return $result;
	}

	function imagecreatefromfile($filename) {

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $filename); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$result = curl_exec($ch); 
		curl_close($ch);

		return imagecreatefromstring($result);
	}

	function curl_filename($options=null,$g_name=null){
		
		// sleep(6);
		$result = array();

		$url = HIGHCHARTS_SERVER;
		$async = true;
		$type = 'image/png';
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"url={$url}&options={$options}&type={$type}&async={$async}"); 

		$result = curl_exec($ch); 
		curl_close($ch);
		$dir_name = $this->session->userdata('session_id');
		$path = "upload/genPDF/".$dir_name."/";
		$name = $g_name."_".date('ymdhis').'.png';
		$fileName = $path.$name;
		$im = $this->imagecreatefromfile($url.$result);
		imagesavealpha($im, true);
	
		if ($im !== false) {
			imagepng($im, $fileName);
			imagedestroy($im);
			$result = $fileName;
		} 
		return $result;
	}

	function get_word_data()
	{
		$result = array();
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		if($post) {
			$post['period'] = $period;
			$result = $this->report_model->get_word_cloud($post);
		}
		echo json_encode($result);
	}

	function get_top_share()
	{
		$result = array();
		$period = $this->master_model->get_period();
		$post   = $this->input->post();
		if($post) {
			$post['period'] = $period;
			$result = $this->report_model->get_top_share($post);
		}
		echo json_encode($result);
	}

	function get_top_user()
	{
		$result = array();
		$period = $this->master_model->get_period();
		$post = $this->input->post();
		if ($post) {
			$post['period'] = $period;
			$result = $this->report_model->get_top_user($post);
		}
		echo json_encode($result);
	}

	private function get_count_media($data)
	{
		$result = array("SM" => 0, "WB" => 0, "NW" => 0);
		if (isset($data['mediaData'])) {
			foreach ($data['mediaData'] as $key => $val) {
				if ($val['drilldown']['categories'][0] == 'WB') {
					$result['WB'] = $val['y'];
				} else if ($val['drilldown']['categories'][0] == 'NW') {
					$result['NW'] = $val['y'];
				} else {
					$result['SM'] += $val['y'];
				}
			}
		}
		$result['SM'] = number_format($result['SM']);
		$result['WB'] = number_format($result['WB']);
		$result['NW'] = number_format($result['NW']);

		return $result;
		echo json_encode($result);
	}
	function mkdir_sesname()
	{
		$dir_name = $this->session->userdata('session_id');
		$path_name = "upload/genPDF/".$dir_name;
		if(!is_dir($path_name)) {
			mkdir($path_name, 0777,true);
		}
		
		return $path_name;
	}

	function get_summary_of_mentions() {
		$result = array();

		$period = $this->master_model->get_period();

		$post = $this->input->post();
		$post['period'] = $period;
		
		$media_data = $this->overview_model->get_media_data($post);
		
		// volume of mentions array ()
		$result["mentions"] = $this->overview_model->get_total_data($post);

		// media_reach array ("SM", "WB", "NW")
		$result["media_reach"] = $this->get_count_media($media_data);

		// sentiment array ("Positive_row", "Negative_row", "Normal_row")
		$result["sentiment"] = $this->overview_model->get_sentiment_data($post);

		return $result;
	}
}