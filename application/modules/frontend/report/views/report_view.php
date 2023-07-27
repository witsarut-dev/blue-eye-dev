<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default card-view" style="padding: 10px 5px 10px 0px; margin-top: 10px; margin-bottom: 15px;">
				<div class="panel-wrapper collapse in">
					<div class="panel-body" style="padding: 0px;">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
							<div class="form-group pull-left top_search">
								<div class="x_title">
									<div class="btn-group panel_toolbox" data-toggle="buttons">
										<button type="button" class="btn btnSearchPositive search-style" sentiment="Positive" style="color: #25D366; width: 100px; border: #1C6CB9; border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Positive</label></button>
										<button type="button" class="btn btnSearchNormal search-style" sentiment="Normal" style="color: #000000; width: 100px; border: #1C6CB9; border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Neutral</label></button>
										<button type="button" class="btn btnSearchNegative search-style" sentiment="Negative" style="color: #FF3F3F; width: 100px; border: #1C6CB9; border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Negative</label></button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="padding-right: 5px;">
							<div id="toolPeriod" class="pull-right" style="margin-top: 5px;">
								<?php echo $this->load->view("include/period_view");?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="panel panel-primary card-view x_panel containment" id="box-tc">
				<div class="panel-heading">
					<div class="pull-left"><h6 class="panel-title" style="color: white;">Word Cloud</h6></div>
					<div class="clearfix"></div>
				</div>
				<div class="panel-wrapper collapse in">
					<div class="panel-body">
						<div class="icon-container">
							<i class="fa fa-spin fa-spinner"></i>
						</div>
						<section class="stage" style="border: 5px solid #FAFAFA;">
							<figure class="ball">
								<canvas id="tc-view"></canvas>
								<ul class="weighted" id="tc-data"></ul>
								<span class="shadow"></span>
							</figure>
						</section>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div id="ResultBox" class="col-lg-8 col-md-6 col-sm-6 col-xs-12 CategoryBox">
			<div class="panel panel-success card-view x_panel">
                <div class="panel-heading">
					<div class="pull-left"><h6 class="panel-title" style="color: white;">Result</h6></div>
					<div class="pull-right"><span class="counterAnim label label-default countResult"></span></div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
						<h7 class="choose-remark text-danger"><i class="fa fa-check-square-o"></i> Please select keyword in word cloud or user</h7>
        				<div class="scroll-pane">
        				</div>
                    </div>
                </div>
			</div>
		</div>
	
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
			<div class="panel panel-default card-view" style="padding: 10px 5px 10px 0px; margin-bottom: 15px;">
				<div class="panel-wrapper collapse in">
					<div class="panel-body" style="padding: 0px;">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="" role="tabpanel" data-example-id="togglable-tabs">
								<ul id="mentions-tab" class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active tab">
										<a href="#top-user" id="top-user-tab" role="tab" data-toggle="tab" aria-expanded="true">
											<label style="font-size: 11px;">Top user</label>
										</a>
									</li>
									<li role="presentation" class="tab">
										<a href="#top-share" id="top-share-tab" role="tab" data-toggle="tab" aria-expanded="false">
											<label style="font-size: 11px;">Top share</label>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="myTabContent" class="tab-content">
				<?php echo $this->load->view("report/report_top_user_list"); ?>
				<?php echo $this->load->view("report/report_top_share_list"); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
			<div id="filter-mentions" class="panel panel-default card-view" style="height: 390px;">
				<div class="panel-wrapper collapse in">
					<div class="panel-body" style="padding: 0px 0px 20px 0px">
						<div class="row" style="margin: 10px 0px 30px 0px; border-bottom: 1px solid #C7C7C7; padding-bottom: 30px;">
							<section>
								<header style="margin-bottom: 1.2rem;">
									<i class="fa fa-file-text blue_icostyle"></i>
									<label class="report-heading-style">Summary</label>
								</header>
								<table class="summary-content-table">
									<tbody>
										<td style="width: 20%;">
											<div class="summary-detail">
												<div style="padding-right: 10px;">
													<i class="fa fa-bar-chart" aria-hidden="true"></i>
												</div>
												<div>
													<div class="summary-detail__title">
														Mentions
													</div>
													<div class="summary-detail__value">
														<span class="counter-anim count-sm">
															<?php echo $summary_of_mentions["mentions"]["mentionCurrent"]; ?>
														</span>
													</div>
												</div>
											</div>
										</td>
										<td style="width: 20%;">
											<div class="summary-detail">
												<div style="padding-right: 10px;">
													<i class="fa fa-wifi" aria-hidden="true"></i>
												</div>
												<div>
													<div class="summary-detail__title">
														SM Reach
													</div>
													<div class="summary-detail__value">
														<span class="counter-anim count-sm">
															<?php echo $summary_of_mentions["media_reach"]["SM"]; ?>
														</span>
													</div>
												</div>
											</div>
										</td>
										<td style="width: 25%;">
											<div class="summary-detail">
												<div style="padding-right: 10px;">
													<i class="fa fa-spinner" aria-hidden="true"></i>
												</div>
												<div>
													<div class="summary-detail__title">
														Non SM Reach
													</div>
													<div class="summary-detail__value">
														<span class="counter-anim count-sm">
															<?php echo $summary_of_mentions["media_reach"]["WB"] + $summary_of_mentions["media_reach"]["NW"]; ?>
														</span>
													</div>
												</div>
											</div>
										</td>
										<td style="width: 15%;">
											<div class="summary-detail">
												<div style="padding-right: 10px;">
													<i class="fa fa-smile-o" aria-hidden="true"></i>
												</div>
												<div>
													<div class="summary-detail__title">
														Positive
													</div>
													<div class="summary-detail__value">
														<span class="counter-anim count-sm">
															<?php echo $summary_of_mentions["sentiment"]["Positive_row"]; ?>
														</span>
													</div>
												</div>
											</div>
										</td>
										<td style="width: 20%;">
											<div class="summary-detail">
												<div style="padding-right: 10px;">
													<i class="fa fa-frown-o" aria-hidden="true"></i>
												</div>
												<div>
													<div class="summary-detail__title">
														Negative
													</div>
													<div class="summary-detail__value">
														<span class="counter-anim count-sm">
															<?php echo $summary_of_mentions["sentiment"]["Negative_row"]; ?>
														</span>
													</div>
												</div>
											</div>
										</td>
									</tbody>
								</table>
							</section>
						</div>
						<div class="row" style="margin: 10px 0px 30px 0px; border-bottom: 1px solid #C7C7C7; padding-bottom: 30px;">
							<section>
								<header style="margin-bottom: 1.2rem;">
									<i class="fa fa-share-alt blue_icostyle"></i>
									<label class="report-heading-style">Sources</label>
								</header>
								<table class="summary-content-table">
									<tbody>
										<tr class="row-space">
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="fa fa-facebook-square" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															Facebook
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["facebook"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="fa fa-twitter-square" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															Twitter
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["twitter"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="fa fa-youtube-play" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															Youtube
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["youtube"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="fa fa-instagram" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															Instagram
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["instagram"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="iconx ico-tiktokchat" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															TikTok
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["tiktok"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="iconx ico-linechat" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															Line
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["line"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="fa fa-comments" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															Forums
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["pantip"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
											<td style="width: 20%;">
												<div class="summary-detail">
													<div style="padding-right: 10px;">
														<i class="fa fa-newspaper-o" aria-hidden="true"></i>
													</div>
													<div>
														<div class="summary-detail__title">
															News
														</div>
														<div class="summary-detail__value">
															<span class="counter-anim count-sm">
																<?php echo $sources_count["news"]; ?>
															</span>
														</div>
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</section>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<form method="post" name="form" action="report/generate_report" target="_blank">
				<div class="panel panel-default card-view">
					<div class="panel-heading">
						<div class="pull-left">
							<h6 class="panel-title txt-dark">Choose report content</h6>
						</div>
						<div class="pull-right">
							<label class="toggle">
								<input id="all" class="toggle-checkbox" type="checkbox">
								<div class="toggle-switch"></div>
								<span class="toggle-label">Select All</span>
							</label>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="module__list">
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="summaryOfMentions" class="checkbox" type="checkbox"
										value="summaryOfMentions" name="summaryOfMentions">
									<label for="summaryOfMentions">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Summary of
												mentions</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="volumeOfMentions" class="checkbox" type="checkbox"
										value="volumeOfMentions" name="volumeOfMentions">
									<label for="volumeOfMentions">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Volume of mentions</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="socialMediaReachGraph" class="checkbox" type="checkbox"
										value="socialMediaReachGraph" name="socialMediaReachGraph">
									<label for="socialMediaReachGraph">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Social media reach graph</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="nonSocialMediaReachGraph" class="checkbox" type="checkbox"
										value="nonSocialMediaReachGraph" name="nonSocialMediaReachGraph">
									<label for="nonSocialMediaReachGraph">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Non social media reach graph</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="mentionsPerCategory" class="checkbox" type="checkbox"
										value="mentionsPerCategory" name="mentionsPerCategory">
									<label for="mentionsPerCategory">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Mentions per category</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="tableGroupKeywordList" class="checkbox" type="checkbox"
										value="tableGroupKeywordList" name="tableGroupKeywordList">
									<label for="tableGroupKeywordList">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Table group keyword list</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="graphSentimentMonitoringAnalysis" class="checkbox" type="checkbox"
										value="graphSentimentMonitoringAnalysis" name="graphSentimentMonitoringAnalysis">
									<label for="graphSentimentMonitoringAnalysis">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Graph Sentiment Monitoring Analysis</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="topUser" class="checkbox" type="checkbox"
										value="topUser" name="topUser">
									<label for="topUser">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Top User</span>
										</div>
									</label>
								</span>
							</div>
							<div class="module__list-item">
								<span class="switcher switcher__boxed">
									<input id="topShare" class="checkbox" type="checkbox"
										value="topShare" name="topShare">
									<label for="topShare">
										<span class="switcher__toggle switcher__toggle--icon"></span>
										<div>
											<span class="switcher__label">Top Share</span>
										</div>
									</label>
								</span>
							</div>
							<div id="container1" style="display: none;"></div>
							<div id="container2" style="display: none;"></div>
							<div id="container3" style="display: none;"></div>
							<div id="container4" style="display: none;"></div>
							<div id="container5" style="display: none;"></div>
						</div>
						<div style="display: flex; justify-content: flex-end;">
							<input type="submit" value="Export PDF" class="buttonExport">
						</div>
					</div>
				</div>
				<input name="volumeOfMentionsB64" id="volumeOfMentionsB64"style="display: none;">
				<input name="socialMediaReachGraphB64" id="socialMediaReachGraphB64"style="display: none;">
				<input name="nonSocialMediaReachGraphB64" id="nonSocialMediaReachGraphB64"style="display: none;">
				<input name="mentionsPerCategoryB64" id="mentionsPerCategoryB64"style="display: none;">
				<input name="graphSentimentMonitoringAnalysisB64" id="graphSentimentMonitoringAnalysisB64"style="display: none;">
			</form>
		</div>
	</div>
</div>