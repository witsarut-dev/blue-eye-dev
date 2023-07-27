<?php $this->load->view("template/header.php");?>
<?php $this->load->view("schedule/schedule_script");?>
<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="page-title">
      <div class="title_left">
        <?php $this->load->view("template/breadcrumb.php"); ?>
      </div>
    </div>

    <div class="clearfix"></div>

    <div id="listCtrl" ng-controller="listCtrl" class="row">
      <div class="col-md-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>{{title}}</h2>
            <div class="clearfix"></div>
          </div>
          <?php $alert_massage = $this->session->flashdata('ALERT_MESSAGE'); ?>
          <?php if($alert_massage!="") { ?>
          <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo $alert_massage; ?>
          </div>
          <?php } ?>
          <div class="x_content">
			<div id="BoxPage" class="row">
				<div class="col-md-8">
				</div>
				<div class="col-md-4 control">
					<div class="btn-group">
						<a href="<?php echo site_url("schedule/formManage");?>" class="btn btn-xs btn-dark fancybox">
							<span class="glyphicon glyphicon-plus-sign"></span> Manage Schedule
						</a>
					</div>
				</div>
			</div>
			<span id="selectCompnay" class="color-control">
		  		<?php foreach($sch_clients as $client) { ?>
		  		<i class="fa fa-circle active <?php echo $client['schedule_color'];?>" item-data="client-id-<?php echo $client['client_id'];?>"></i> <?php echo $client['company_name'];?>&nbsp;&nbsp;&nbsp;
		  		<?php } ?>
		  	</span>
		  	<div id="schedule"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->
<?php $this->load->view("template/footer.php");?>
<?php 
$events = array();
foreach($schedule as $sch) {
	$color = $this->schedule_model->get_color($sch['schedule_color']);
	$data = array(
		"id"=> $sch['schedule_id'], 
		"resourceId"=> $sch['schedule_day'], 
		"backgroundColor"=> $color, 
		"borderColor"=> $color, 
		"start"=> '2001-01-01T'.$sch['schedule_start'], 
		"end"=> '2001-01-01T'.$sch['schedule_end'], 
		"title"=> $sch['company_name'],
		"className"=>"client-id-".$sch['client_id']
	);
	array_push($events,$data);
}
?>
<link rel="stylesheet" type="text/css" href="themes/admin/vendors/fullcalendar-scheduler-1.5.1/lib/fullcalendar.min.css">
<link rel="stylesheet" type="text/css" href="themes/admin/vendors/fullcalendar-scheduler-1.5.1/lib/cupertino/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="themes/admin/vendors/fullcalendar-scheduler-1.5.1/scheduler.min.css">
<script type="text/javascript" src="themes/admin/vendors/fullcalendar-scheduler-1.5.1/lib/moment.min.js"></script>
<script type="text/javascript" src="themes/admin/vendors/fullcalendar-scheduler-1.5.1/lib/fullcalendar.min.js"></script>
<script type="text/javascript" src="themes/admin/vendors/fullcalendar-scheduler-1.5.1/lib/locale-all.js"></script>
<script type="text/javascript" src="themes/admin/vendors/fullcalendar-scheduler-1.5.1/scheduler.min.js"></script>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Monitor Schedule","link":"","active":"true"}];
	app_mode = {};
	app_searchForm = [];
	app_list = {
		"title" : "Monitor Schedule",
		"rows" : 0,
		"rows_publish" : 0,
		"rows_modified" : 0,
		"rows_unpublish" : 0,
		"totalpage" : 0,
		"pagesize" : 0,
		"sorting" : "schedule_id",
		"orderby" : "asc",
		"module" : "schedule",
		"tbwidth" : "100",
		"column" : [],
		"items" : []
	};
	app_post = [];
	app_validate = {};

	var events = <?php echo json_encode($events); ?>;
	$(function() { // document ready

		$('#schedule').fullCalendar({
			locale: 'th',
			theme: true,
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			resourceAreaWidth: 80,
			now: '2001-01-01',
			editable: false, // enable draggable events
			aspectRatio: 1.8,
			minTime : '00:00:00',
			maxTime : '24:00:00',
			scrollTime: '00:00', // undo default 6am scrollTime
			header: {
				left: false,
				center: false ,
				right: false 
			},
			defaultView: 'timelineDay',
			views: {
				timelineThreeDays: {
					type: 'timeline'
				}
			},
			resourceLabelText: 'Days',
			resources: [
				{ id: 'MON', title: 'MON' },
				{ id: 'TUE', title: 'TUE' },
				{ id: 'WED', title: 'WED' },
				{ id: 'THU', title: 'THU' },
				{ id: 'FIR', title: 'FIR' },
				{ id: 'SAT', title: 'SAT' },
				{ id: 'SUN', title: 'SUN' }
			],
			events: events
		});
		
		$("#schedule span.fc-cell-text").each(function(){
			var num = $(this).text();
			$(this).parents(".fc-cell-content").css("text-align","center");
			if(!isNaN(parseInt(num))) {
				if(num<10) {
					var time = "0"+num+".00";
				} else {
					var time = num+".00";
				}
				$(this).text(time);
			}
		});

		$("#selectCompnay .fa").click(function(){
			$.fancybox.showLoading();
			$('#schedule').fullCalendar('removeEventSources');
			$('#schedule').fullCalendar('addEventSource',events); 
			if($(this).hasClass("active")) {
				$(this).removeClass("active");
			} else {
				$(this).addClass("active");
			}
			$("#selectCompnay .fa:not(.active)").each(function(){
				var  className = $(this).attr("item-data");
				$(events).each(function(i,val){
					if(val.className==className) {
						$('#schedule').fullCalendar('removeEvents',val.id);
					}
				});
			});        
			$.fancybox.hideLoading();
		});
	});
</script>