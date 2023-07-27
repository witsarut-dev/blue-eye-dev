<?php $visibility = ($period=="3M" || $period=="Custom") ? "hidden" :  "visible";?>
<div class="right_col" role="main">
	<div class="">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="dashboard_graph">
				<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
					<div class="pull-left" id="toolPeriod">
						<?php echo $this->load->view("include/period_view");?>
					</div>
					<div class="pull-right">
						<a id="chooseKeyword" href="<?php echo site_url($module."/filter_keyword");?>" class="btn btn-success btn-round fancybox">Choose Keyword</a>
					</div>
					<div id="ChartKeyword"></div>
				</div>
				<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
					<div class="x_panel tile">
						<div class="x_content">
							<div id="ChartMedia"></div>
							<span style="visibility:<?php echo $visibility;?>"><input type="checkbox" id="btnMediaBefore" /> Show the same period before</span>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<br />
	<div class="">
		<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile">
						<div class="x_title">
							<h2 class="title">Marketing Position Monitoring</h2>
							<div class="clearfix"></div>
						</div>
						<?php if(count($category)>0) { ?>
						<div class="x_content">
							<div id="ChartMarketPos" style="height:370px"></div>
						</div>
						<?php } else { ?>
						<div class="x_content">
							<div align="center" style="height:370px;position: relative;">
								<div class="screen-center">ไม่พบข้อมูล Category คุณสามารถเพิ่มข้อมูลได้โดยคลิกที่ปุ่มนี้ <br /><a href="<?php echo site_url("marketing");?>" class="btn btn-sm btn-primary btn-round">Add Category</a></div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile">
						<div class="x_title">
							<h2 class="title">Media Position Monitoring</h2>
							<div class="clearfix"></div>
						</div>
						<?php if(count($category)>0) { ?>
						<form id="formMediaCom" action="<?php echo site_url($module);?>" method="post" style="position: absolute;">
							<div class="btn-group" role="group" style="z-index: 100;">
							<?php
							$dropdown_menu = "";
							$dropdown_select = "";
							foreach($company as $k_row=>$v_row) {
								if($v_row['company_keyword_id']!=$media_com) {
									$dropdown_menu .= '<li><a href="javascript:;" media-com="'.$v_row['company_keyword_id'].'">'.$v_row['company_keyword_name'].'</a></li>';
								} else {
									$dropdown_select = '<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> '.$v_row['company_keyword_name'].' <span class="caret"></span></button>';
								}
							}
							if($dropdown_menu!="") {
								$dropdown_menu = '<ul class="dropdown-menu">'.$dropdown_menu.'</ul>';
							}
							echo $dropdown_select;
							echo $dropdown_menu;
							?>
							<input type="hidden" name="save_media_com" value="save_media_com" />
							<input type="hidden" name="media_com" value="<?php echo $media_com;?>" />
							<input type="hidden" name="module" value="<?php echo $module;?>" />
							</div>
						</form>
						<div class="x_content">
							<div id="ChartMediaPos" style="height:370px"></div>
						</div>
						<?php } else { ?>
						<div class="x_content">
							<div align="center" style="height:370px;position: relative;">
								<div class="screen-center">ไม่พบข้อมูล Category คุณสามารถเพิ่มข้อมูลได้โดยคลิกที่ปุ่มนี้ <br /><a href="<?php echo site_url("marketing");?>" class="btn btn-sm btn-primary btn-round">Add Category</a></div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="x_panel tile">
						<div class="x_title">
							<h2 class="title" style="text-align: left !important">Sentiment Monitoring Analysis</h2>
							<div class="clearfix"></div>
						</div>
						<div id="SentimentProgress" class="x_content">
							<div class="progress right">
								<a href="<?php echo site_url("realtime/?Sentiment=Positive");?>"><div class="progress-bar progress-bar-success" style="width: <?php echo $sentimentData['Positive'];?>%;"><?php echo $sentimentData['Positive'];?>%</div></a>
								<a href="<?php echo site_url("realtime/?Sentiment=Normal");?>"><div class="progress-bar progress-bar-white" style="width: <?php echo $sentimentData['Normal'];?>%;"><?php echo $sentimentData['Normal'];?>%</div></a>
								<a href="<?php echo site_url("realtime/?Sentiment=Negative");?>"><div class="progress-bar progress-bar-danger" style="width: <?php echo $sentimentData['Negative'];?>%;"><?php echo $sentimentData['Negative'];?>%</div></a>
							</div>
							<span><span class="badge badge-success">&nbsp;</span> Positive</span>
							<span><span class="badge badge-white">&nbsp;</span> Normal</span>
							<span><span class="badge badge-danger">&nbsp;</span> Negative</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
			<div id="TotalData" class="x_panel tile">
				<div class="x_title">
					<h2 class="title">Total Mention</h2>
					<div class="clearfix"></div>
				</div>
				<br />
				<div class="x_content">
					<h1 id="mentionCurrent"><?php echo ($totalData["mentionCurrent"]);?></h1>
					<h1 id="mentionBefore" class="before"><?php echo ($totalData["mentionBefore"]);?></h1>
				</div>
				<div class="clearfix"></div>
				<br />
				<br />
				<div class="x_title">
					<h2 class="title">Total User</h2>
					<div class="clearfix"></div>
				</div>
				<br />
				<div class="x_content">
					<h1 id="userCurrent"><?php echo ($totalData["userCurrent"]);?></h1>
					<h1 id="userBefore" class="before"><?php echo ($totalData["userBefore"]);?></h1>
				</div>
				<div class="clearfix"></div>
				<br />
				<br />
				<br />
				<br />
				<span style="visibility:<?php echo $visibility;?>"><input type="checkbox" id="btnTotalBefore" /> Show the same period before</span>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
