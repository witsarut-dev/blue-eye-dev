<?php $this->load->view("schedule/schedule_script");?>
<div id="scheduleBox" class="container Popup">
	<div class="back-to-close pull-right">
        <a href="javascript:;" onclick="$.fancybox.close();" class="glyphicon glyphicon-remove"></a>
     </div>
	<div class="x_content">
		<div id="BoxPage">
			<div class="col-md-3"><h3>Manage Schedule</h3></div>
			<div class="col-md-9 control pull-right">
				<span class="color-control">
					<i class="fa fa-circle c-default active" color="c-default"></i>
					<i class="fa fa-circle c-red" color="c-red"></i>
					<i class="fa fa-circle c-yellow" color="c-yellow"></i>
					<i class="fa fa-circle c-light-green" color="c-light-green"></i>
					<i class="fa fa-circle c-green" color="c-green"></i>
					<i class="fa fa-circle c-black" color="c-black"></i>
					<i class="fa fa-circle c-blue" color="c-blue"></i>
					<i class="fa fa-circle c-orange" color="c-orange"></i>
					<i class="fa fa-circle c-violet" color="c-violet"></i>
					<i class="fa fa-circle c-gray" color="c-gray"></i>
					&nbsp;&nbsp;&nbsp;
				</span>
				<div class="btn-group">
					<select id="client_id" name="client_id" class="form-control required">
						<option value="">Choose Company</option>
						<?php foreach($clients as $client) { ?>
						<option value="<?php echo $client['client_id'];?>"><?php echo $client['company_name'];?></option>
						<?php } ?>
					</select>
					<button type="button" class="btn btn-dark btn-submit">
						<span class="glyphicon glyphicon-floppy-save"></span> Save
					</button>
				</div>
			</div>
		</div>
		<div class="rows">
			<div id="selectManageClient" class="col-md-12">
		  		<?php foreach($sch_clients as $client) { ?>
		  		<i class="fa fa-circle active <?php echo $client['schedule_color'];?>" item-data="client-id-<?php echo $client['client_id'];?>"></i> <?php echo $client['company_name'];?>&nbsp;&nbsp;&nbsp;
		  		<?php } ?>
		  	</div>
		</div>
	</div>
	<div class="x_content">
		<div class="col-md-12">
			<div class="table-responsive">
				<table align="center" class="table table-striped table-bordered">
		            <thead>
		            	<tr>
		            		<th class="center">Days</th>
		            		<?php 
			            		for($i=0;$i<=23;$i++) {
			            			$time = ($i<10) ? "0$i.00" : "$i.00";
			            			echo '<th class="center">'.$time.'</th>';
			            		} 
		            		?>
		            	</tr>
		            </thead>
		            <tbody>
		            	<?php
		            		$days = $this->schedule_model->get_days(); 
		            		foreach($days as $key=>$day) { 
		            	?>
		            	<tr class="item-days" item-data="<?php echo $key;?>">
		            		<th class="center"><?php echo $day;?></th>
		            		<?php 
			            		for($i=0;$i<=23;$i++) {
			            			$time = ($i<10) ? "0$i:00" : "$i:00";
			            			echo '<td class="center td-schedule c-default" item-day="'.$day.'" item-data="'.$time.'"></td>';
			            		}
		            		?>
		            	</tr>
		            	<?php } ?>
		            </tbody>
		        </table>
		    </div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function() {

	var isMouseDown = false;
	var isHighlighted;

	$("#selectManageClient .fa").click(function(){
		var client_id = $(this).attr("item-data").replace("client-id-","");
		$("#client_id").val(client_id);
		load_client_schedule();
	});

	$("#scheduleBox .table .td-schedule").mousedown(function () {

		if($("#scheduleBox #client_id").val()=="") {
			alert_message("Please choose your company.");
			return false;
		}

	 	isMouseDown = true;
	 	$(this).toggleClass("highlighted");
	 	isHighlighted = $(this).hasClass("highlighted");
	 	return false; // prevent text selection
	}).mouseover(function () {

		if($("#scheduleBox #client_id").val()=="") {
			return false;
		}

	 	if (isMouseDown) {
	  		$(this).toggleClass("highlighted", isHighlighted);
	  	}
	}).bind("selectstart", function () {
	 	return false;
	})

	$(document).mouseup(function () {
	 	isMouseDown = false;
	});

	$("#scheduleBox .color-control .fa").click(function(){

		var color = $(this).attr("color");

		$("#scheduleBox .color-control .fa").removeClass("active");
		$(this).addClass("active");
		clear_control_color();
		$("#scheduleBox .td-schedule").addClass(color);

	});

	$("#scheduleBox .btn-submit").click(function(){
		if($("#scheduleBox #client_id").val()=="") {
			alert_message("Please choose your company.");
		} else {
			var days = [];
			var color = $("#scheduleBox .color-control .fa.active").attr("color");
			$("#scheduleBox .item-days").each(function(){
				var day = $(this).attr("item-data");
				days[day] = [];
				$(this).find(".highlighted").each(function(){
					var time = $(this).attr("item-data");
					days[day].push(time);
				});
			});
			
			$.ajax({
				dataType: "json",
				type: "post",
			  	url: "<?php echo site_url('schedule/cmdManage');?>",
			  	data : {
			  		'MON' : days[0],
			  		'TUE' : days[1],
			  		'WED' : days[2],
			  		'THU' : days[3],
			  		'FIR' : days[4],
			  		'SAT' : days[5],
			  		'SUN' : days[6],
			  		'color' : color,
			  		'client_id' : $("#scheduleBox #client_id").val()
			  	},
			  	beforeSend : function () {
			  		$.fancybox.showLoading();
			  	},
			  	error : function () {
			  		$.fancybox.hideLoading();
			  		alert("Error");
			  	},
			  	success : function (res) {
			  		if(res.status) {
			  			window.location.href = res.url;
			  		}
			  		$.fancybox.hideLoading();
			  	}
			});
		}
	});

	$("#scheduleBox #client_id").change(function(){
		load_client_schedule();
	});

});


function load_client_schedule()
{
	$.fancybox.showLoading();

	$("#scheduleBox .color-control .fa").removeClass("active");
	clear_control_color();
	$("#scheduleBox .td-schedule").removeClass("highlighted");

	if($("#scheduleBox #client_id").val()!="") {
		$.ajax({
			dataType: "json",
			type: "post",
		  	url: "<?php echo site_url('schedule/getScheduleByClient');?>",
		  	data : {
		  		'client_id' : $("#scheduleBox #client_id").val()
		  	},
		  	beforeSend : function () {
		  		$.fancybox.showLoading();
		  	},
		  	error : function () {
		  		$.fancybox.hideLoading();
		  		alert("Error");
		  	},
		  	success : function (res) {

		  		if(res.length==0) {
		  			$("#scheduleBox .td-schedule").addClass("c-default");
		  			$("#scheduleBox .color-control .c-default").addClass("active");
		  		} else {
			  		$(res).each(function(index,val){
			  			$("#scheduleBox .td-schedule").addClass(val.schedule_color);
			  			$("#scheduleBox .color-control ."+val.schedule_color).addClass("active");
			  			var start = parseInt(val.schedule_start);
			  			var end = parseInt(val.schedule_end);
			  			for(var i = start;i<end;i++) {
			  				var time = (i<10) ? "0"+i+":00" : i+":00";
			  				$("#scheduleBox td[item-day='"+val.schedule_day+"'][item-data='"+time+"']").addClass("highlighted");
			  			}

			  		});
			  	}
		  		$.fancybox.hideLoading();
		  	}
		});
	} else {
		$("#scheduleBox .td-schedule").addClass("c-default");
		$("#scheduleBox .color-control .c-default").addClass("active");
		$.fancybox.hideLoading();
	}
}

function clear_control_color()
{
	$("#scheduleBox .td-schedule")
		.removeClass("c-default")
		.removeClass("c-yellow")
		.removeClass("c-blue")
		.removeClass("c-gray")
		.removeClass("c-violet")
		.removeClass("c-orange")
		.removeClass("c-red")
		.removeClass("c-green")
		.removeClass("c-black")
		.removeClass("c-light-green");
}

function alert_message(msg)
{
	$('#alert-box .modal-title').html(msg);
    $('#alert-box').modal('show');
}
</script>