<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group pull-left" style="margin-bottom: 0px; margin-left: -5px;">
				<div id="filterOther" class="input-group">
					<div class="bootstrap-tagsinput">
						<input name="And_filterOther" type="text" placeholder="Search" data-role="tagsinput" value="">
						<button type="button" class="btn btn-round btn-search" style="color:#000000;"><i class="fa fa-search"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default card-view" style="padding: 10px 5px 10px 0px; margin-top: 10px; margin-bottom: 15px;">
				<div class="panel-wrapper collapse in">
					<div class="panel-body" style="padding: 0px;">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="" role="tabpanel" data-example-id="togglable-tabs">
								<ul id="mentions-tab" class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active tab">
										<a href="#normal-mentions" id="normal-mentions-tab" role="tab" data-toggle="tab" aria-expanded="true">
											<label style="font-size: 11px;">Normal mentions</label>
										</a>
									</li>
									<li role="presentation" class="tab">
										<a href="#priority-mentions" id="priority-mentions-tab" role="tab" data-toggle="tab" aria-expanded="false">
											<label style="font-size: 11px;">Priority mentions</label>
											<div id="animation_main_div">
												<div class="circle"></div>
												<div class="circle2"></div>
												<div class="circle3"></div>
												<div class="circle4"></div>
											</div>
										</a>
									</li>
									<!-- <li role="presentation" class="tab">
										<a href="#post-monitor" id="post-monitor-tab" role="tab" data-toggle="tab" aria-expanded="false">
											<label style="font-size: 11px;">Post monitor</label>
										</a>
									</li> -->
								</ul>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding-right: 5px;">
							<div id="toolPeriod" style="float: right; margin-top: 5px;">
								<?php echo $this->load->view("include/period_view");?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
				<div id="myTabContent" class="tab-content">
					<?php echo $this->load->view("realtime/realtime_normal_list"); ?>
					<?php echo $this->load->view("realtime/realtime_priority_list"); ?>
					<?php// echo $this->load->view("realtime/realtime_post_monitor_list"); ?>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
				<div id="filter-mentions" class="panel panel-default card-view">
					<div class="panel-wrapper collapse in">
						<div class="panel-body" style="padding: 0px 0px 20px 0px">
							<div class="row" style="margin: 10px 0px;">
								<p class="header-text" style="margin: 0px 0px 10px 0px;" >Sentiment</p>
								<div class="form-group pull-left top_search">
									<div class="x_title">
										<div class="btn-group btn-group-justified panel_toolbox" data-toggle="buttons">
											<div class="btn-group" role="group">
												<button type="button" class="btn btnSearchPositive search-style" sentiment="Positive" style="color: #25D366; border: #1C6CB9; border-style: solid; border-width: 1px 0px 1px 1px; border-radius: 0px;"><label style="font-size: 11px;">Positive</label></button>
											</div>
											<div class="btn-group" role="group">
												<button type="button" class="btn btnSearchNormal search-style" sentiment="Normal" style="color: #000000; border: #1C6CB9; border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Neutral</label></button>
											</div>
											<div class="btn-group" role="group">
											<button type="button" class="btn btnSearchNegative search-style" sentiment="Negative" style="color: #FF3F3F; border: #1C6CB9; border-style: solid; border-width: 1px 1px 1px 0px; border-radius: 0px;"><label style="font-size: 11px;">Negative</label></button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row" style="margin: 10px 0px;">
								<p class="header-text" style="margin: 10px 0px;" >Company type</p>
								<div id="toolCompanyType">
									<div class="btn-group btn-group-justified" role="group">
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" company-type="All" style="border-style: solid; border-width: 1px 0px 1px 1px; border-radius: 0px;"><label style="font-size: 11px;">All</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" company-type="client" style="border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Client</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" company-type="competitor" style="border-style: solid; border-width: 1px 1px 1px 0px; border-radius: 0px;"><label style="font-size: 11px;">Competitor</label></button>
										</div>
									</div>
								</div>
							</div>
							<div class="row" style="margin: 10px 0px;">
								<p class="header-text" style="margin: 10px 0px;" >Sources</p>
								<div id="toolMediaType" class="pull-left">
									<div class="btn-group btn-group-justified" role="group">
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="All" style="border-width: 1px 0px 0px 1px;"><label style="font-size: 11px;">All</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="facebook" style="border-width: 1px 1px 0px 1px;"><i class="fa fa-facebook-square" style="color: #000000 !important;"></i>&nbsp;<label class="filter-label">Facebook</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="twitter" style="border-width: 1px 1px 0px 0px;"><i class="fa fa-twitter-square" style="color: #000000 !important;"></i>&nbsp;<label class="filter-label">Twitter</label></button>
										</div>
									</div>
									<div class="btn-group btn-group-justified" role="group">
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="youtube" style="border-width: 1px 0px 1px 1px;"><i class="fa fa-youtube-play" style="color: #000000 !important;"></i>&nbsp;<label class="filter-label">Youtube</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="instagram" style="border-width: 1px 1px 1px 1px;"><i class="fa fa-instagram" style="color: #000000 !important;"></i>&nbsp;<label class="filter-label">Instagram</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="tiktok" style="border-width: 1px 1px 1px 0px;"><i class="iconx ico-tiktokchat"></i>&nbsp;<label class="filter-label">TikTok</label></button>
										</div>
									</div>
									<div class="btn-group btn-group-justified" role="group">
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="line" style="border-width: 0px 0px 1px 1px;"><i class="iconx ico-linechat"></i>&nbsp;<label class="filter-label">Line</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="webboard" style="border-width: 0px 1px 1px 1px;"><i class="fa fa-comments" style="color: #000000 !important;"></i>&nbsp;<label class="filter-label">Forums</label></button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default" media-type="news" style="border-width: 0px 1px 1px 0px;"><i class="fa fa-newspaper-o" style="color: #000000 !important;"></i>&nbsp;<label class="filter-label">News</label></button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<?php $this->load->view("realtime/filter_realtime_view"); ?>

