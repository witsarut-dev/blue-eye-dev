<div class="right_col_fix" role="main">
	<div class="">
		<div class="page-title">
			<div class="title_left" id="toolMediaType">
				<h3>Media Type</h3>
				<button type="button" class="btn btn-default btn-xs" media-type="facebook"><i class="ico ico-fb"></i> FB</button>
				<button type="button" class="btn btn-default btn-xs" media-type="twitter"><i class="ico ico-tw"></i> TW</button>
				<button type="button" class="btn btn-default btn-xs" media-type="youtube"><i class="ico ico-yt"></i> YT</button>
				<button type="button" class="btn btn-default btn-xs" media-type="instagram"><i class="ico ico-ig"></i> IG</button>
				<button type="button" class="btn btn-default btn-xs" media-type="webboard"><i class="ico ico-wb"></i> Web</button>
				<button type="button" class="btn btn-default btn-xs" media-type="news"><i class="ico ico-nw"></i> News</button>
				<button type="button" class="btn btn-default btn-xs" media-type="All">All</button>
			</div>
			<div class="title_right">
				<div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
					<div class="x_title">
						<ul class="nav navbar-right panel_toolbox">
							<li><a class="none-link">Filter</a></li>
							<li><a href="#filterRealtime" class="fancybox"><i class="fa fa-filter"></i></a></li>
							<li><button type="button" class="btnSearchPositive" sentiment="Positive"><i class="fa fa-plus"></i></button></li>
							<li><button type="button" class="btnSearchNormal" sentiment="Normal"><i class="fa fa-circle-o"></i></button></li>
							<li><button type="button" class="btnSearchNegative" sentiment="Negative"><i class="fa fa-minus"></i></button></li>
						</ul>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<br />
		<div class="">
			<div id="MediaBox" class="col-lg-4 col-md-6 col-sm-6 col-xs-12 CategoryBox">
				<div class="x_panel">
					<div class="x_title">
						<h2>Social Network</h2>
						<div class="clearfix"></div>
					</div>
					<div class="scroll-pane">
						
					</div>
				</div>
			</div>
			<div id="WebBox" class="col-lg-4 col-md-6 col-sm-6 col-xs-12 CategoryBox">
				<div class="x_panel">
					<div class="x_title">
						<h2>Webboard & Blog</h2>
						<div class="clearfix"></div>
					</div>
					<div class="scroll-pane">
						
					</div>
				</div>
			</div>
			<div id="NewsBox" class="col-lg-4 col-md-6 col-sm-6 col-xs-12 CategoryBox">
				<div class="x_panel">
					<div class="x_title">
						<h2>News</h2>
						<div class="clearfix"></div>
					</div>
					<div class="scroll-pane">
						
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<?php $this->load->view("realtime/filter_realtime_view"); ?>