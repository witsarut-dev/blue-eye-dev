<?php $visibility = ($period == "3M" || $period == "Custom") ? "hidden" : "visible"; ?>
<div class="container-fluid">
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default card-view" style="padding: 10px 5px 10px 0px; margin-top: 10px; margin-bottom: 15px;">
				<div class="panel-wrapper collapse in">
					<div class="panel-body" style="padding: 0px;">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-right: 5px;">
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
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view">
                    <div class="panel-heading">
                        <div class="pull-right">
                            <a id="chooseKeyword" href="<?php echo site_url($module . "/filter_keyword"); ?>" class="btn btn-success btn-round fancybox">Choose Keyword</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div id="ChartKeyword" class="chart-layout" style="min-height: 400px;"></div>
                    </div>
                    <!-- <button type="button" class="buttonExport">Export</button> -->
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view panel-refresh">
                    <div class="refresh-container">
                        <div class="la-anim-1"></div>
                    </div>
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h6 class="panel-title txt-dark">Media Monitoring</h6>
                        </div>
                        <div class="pull-right">
                            <button class="btn  btn-success btn-outline btn-media-back"><span class="fa fa-chevron-left"></span> Back</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body sm-data-box-1" style="position: relative;">
                            <div id="ChartMedia" class="chart-layout" style="position: relative;z-index: 8"></div>

                            <div id="KeywordCrossTab">

                                <div id="MediaKeywordProgress">
                                    <span class="txt-dark block counter">Sentiment</span>
                                    <div class="progress">
                                        <div class="progress-bar progress2-bar-success" style="width: 0%;"></div>
                                        <div class="progress-bar progress2-bar-grey" style="width: 0%;"></div>
                                        <div class="progress-bar progress2-bar-danger" style="width: 0%;"></div>
                                    </div>
                                </div>
                                <span class="uppercase-font weight-500 font-14 block text-left txt-dark"><span id="media-channel"></span> <span class="pull-right" style="font-size: 18px;" id="media-percent"></span></span>
                                <br />

                                <div style="display: flex;">
                                    <div style="flex: 1; border: 1px solid;">
                                        <table id="TableTopPositive" style="width: 100%;" class="table display jambo_table mb-0">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Top 5 (Positive)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div style="flex: 1; border: 1px solid;">
                                        <table id="TableTopNormal" style="width: 100%;" class="table display jambo_table mb-0">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Top 5 (Neutral)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div style="flex:1; border:1px solid;">
                                        <table id="TableTopNegative" style="width: 100%;" class="table display jambo_table mb-0">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Top 5 (Negative)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
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
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <div class="panel panel-default card-view pa-0">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body pa-0">
                        <div class="sm-data-box">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                        <span class="txt-dark block counter"><span class="counter-anim count-sm">
                                                <?php echo $mediaCount['SM']; ?>
                                            </span></span>
                                        <span class="weight-500 uppercase-font block font-13 txt-primary">Social
                                            Media</span>
                                    </div>
                                    <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                        <i class="icon-people data-right-rep-icon txt-light-grey"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <div class="panel panel-default card-view pa-0">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body pa-0">
                        <div class="sm-data-box">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                        <span class="txt-dark block counter"><span class="counter-anim count-wb">
                                                <?php echo $mediaCount['WB']; ?>
                                            </span></span>
                                        <span class="weight-500 uppercase-font block txt-primary">Webboard & Blog</span>
                                    </div>
                                    <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                        <i class="icon-speech data-right-rep-icon txt-light-grey"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <div class="panel panel-default card-view pa-0">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body pa-0">
                        <div class="sm-data-box">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
                                        <span class="txt-dark block counter"><span class="counter-anim count-nw">
                                                <?php echo $mediaCount['NW']; ?>
                                            </span></span>
                                        <span class="weight-500 uppercase-font block txt-primary">News</span>
                                    </div>
                                    <div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
                                        <i class="icon-book-open data-right-rep-icon txt-light-grey"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-default card-view panel-refresh">
                        <div class="refresh-container">
                            <div class="la-anim-1"></div>
                        </div>
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h6 class="panel-title txt-dark">Marketing Position Monitoring</h6>
                            </div>
                            <div class="pull-right"><a href="#" class="pull-left inline-block refresh mr-15"><i class="zmdi zmdi-replay" onclick="requestMarketposData()"></i></a></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <?php if (count($category) > 0) { ?>
                                    <div id="ChartMarketPos" class="chart-layout" style="height:370px"></div>
                                <?php } else { ?>
                                    <div align="center" style="height:370px;position: relative;">
                                        <div class="screen-center">ไม่พบข้อมูล Category
                                            คุณสามารถเพิ่มข้อมูลได้โดยคลิกที่ปุ่มนี้ <br /><a href="<?php echo site_url("marketing"); ?>" class="btn btn-sm btn-primary btn-round">Add Category</a></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-default card-view panel-refresh">
                        <div class="refresh-container">
                            <div class="la-anim-1"></div>
                        </div>
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h6 class="panel-title txt-dark">Media Position Monitoring</h6>
                            </div>
                            <div class="pull-right"><a href="#" class="pull-left inline-block refresh mr-15"><i class="zmdi zmdi-replay" onclick="requestMediaposData()"></i></a></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body" style="position: relative;">
                                <?php if (count($category) > 0) { ?>
                                    <form id="formMediaCom" action="<?php echo site_url($module); ?>" method="post" style="position: absolute;top:0px;">
                                        <div class="btn-group" role="group" style="z-index: 100;">
                                            <?php
                                            $dropdown_menu = "";
                                            $dropdown_select = "";
                                            foreach ($company as $k_row => $v_row) {
                                                if ($v_row['company_keyword_id'] != $media_com) {
                                                    $dropdown_menu .= '<li><a href="javascript:;" media-com="' . $v_row['company_keyword_id'] . '">' . $v_row['company_keyword_name'] . '</a></li>';
                                                } else {
                                                    $dropdown_select = '<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ' . $v_row['company_keyword_name'] . ' <span class="caret"></span></button>';
                                                }
                                            }
                                            if ($dropdown_menu != "") {
                                                $dropdown_menu = '<ul class="dropdown-menu">' . $dropdown_menu . '</ul>';
                                            }
                                            echo $dropdown_select;
                                            echo $dropdown_menu;
                                            ?>
                                            <input type="hidden" name="save_media_com" value="save_media_com" />
                                            <input type="hidden" name="media_com" value="<?php echo $media_com; ?>" />
                                            <input type="hidden" name="module" value="<?php echo $module; ?>" />
                                        </div>
                                    </form>
                                    <div id="ChartMediaPos" class="chart-layout" style="height:370px"></div>
                                <?php } else { ?>
                                    <div align="center" style="height:370px;position: relative;">
                                        <div class="screen-center">ไม่พบข้อมูล Category
                                            คุณสามารถเพิ่มข้อมูลได้โดยคลิกที่ปุ่มนี้ <br /><a href="<?php echo site_url("marketing"); ?>" class="btn btn-sm btn-primary btn-round">Add Category</a></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="panel panel-default card-view panel-refresh">
                        <!--<div class="refresh-container">
                            <div class="la-anim-1"></div>
                        </div>-->
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h6 class="panel-title txt-dark">Sentiment Monitoring Analysis</h6>
                            </div>
                            <!--<div class="pull-right"><a href="#" class="pull-left inline-block refresh mr-15"><i class="zmdi zmdi-replay"></i></a></div>-->
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div id="SentimentProgress">
                                    <!-- Show Percentage of feed on each status (BAR) -->
                                    <div class="progress right">
                                        <a href="<?php echo site_url("realtime/?Sentiment=Positive"); ?>">
                                            <div class="progress-bar progress-bar-success" style="width: <?php echo $sentimentData['Positive']; ?>%;"><span class="counter-anim">
                                                    <?php echo $sentimentData['Positive']; ?>
                                                </span>%</div>
                                        </a>
                                        <a href="<?php echo site_url("realtime/?Sentiment=Normal"); ?>">
                                            <div class="progress-bar progress-bar-grey" style="width: <?php echo $sentimentData['Normal']; ?>%;"><span class="counter-anim">
                                                    <?php echo $sentimentData['Normal']; ?>
                                                </span>%</div>
                                        </a>
                                        <a href="<?php echo site_url("realtime/?Sentiment=Negative"); ?>">
                                            <div class="progress-bar progress-bar-danger" style="width: <?php echo $sentimentData['Negative']; ?>%;"><span class="counter-anim">
                                                    <?php echo $sentimentData['Negative']; ?>
                                                </span>%</div>
                                        </a>
                                    </div>
                                    <!-- End Show Percentage -->
                                    <!-- Show Count of feed on each status -->
                                    <div><span class="badge badge-success">&nbsp;</span> Positive &nbsp;[total-count :
                                        <span class="counter-anim">
                                            <?php echo $sentimentData['Positive_row']; ?>
                                        </span> feed] &nbsp;&nbsp;&nbsp;&nbsp;<span class="badge badge-white">&nbsp;</span> Neutral &nbsp;[total-count : <span class="counter-anim">
                                            <?php echo $sentimentData['Normal_row']; ?>
                                        </span> feed] &nbsp;&nbsp;&nbsp;&nbsp;<span class="badge badge-danger">&nbsp;</span> Negative &nbsp;[total-count : <span class="counter-anim">
                                            <?php echo $sentimentData['Negative_row']; ?>
                                        </span> feed]
                                    </div>
                                    <!-- End Show Count -->
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div id="TotalData" class="x_panel tile">
                <div class="panel panel-default card-view panel-refresh">
                    <div class="refresh-container">
                        <div class="la-anim-1"></div>
                    </div>
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h6 class="panel-title txt-dark">Total Mention</h6>
                        </div>
                        <div class="pull-right"><a href="#" class="pull-left inline-block refresh mr-15"><i class="zmdi zmdi-replay" onclick="requestTotalData('mention')"></i></a></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body sm-data-box-1">
                            <div id="mentionCurrent" class="cus-sat-stat weight-500 txt-primary text-center mt-5"><span class="counter-anim">
                                    <?php echo ($totalData["mentionCurrent"]); ?>
                                </span></div>
                            <div id="mentionBefore" class="cus-sat-stat weight-500 txt-primary text-center mt-5"><span class="counter-anim before">
                                    <?php echo ($totalData["mentionBefore"]); ?>
                                </span></div>
                            <div class="mt-60">
                                <span style="visibility:<?php echo $visibility; ?>"><input type="checkbox" class="btnTotalBefore" /> Show the same period before</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default card-view panel-refresh">
                    <div class="refresh-container">
                        <div class="la-anim-1"></div>
                    </div>
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h6 class="panel-title txt-dark">Total User</h6>
                        </div>
                        <div class="pull-right"><a href="#" class="pull-left inline-block refresh mr-15"><i class="zmdi zmdi-replay" onclick="requestTotalData('user')"></i></a></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body sm-data-box-1">
                            <div id="userCurrent" class="cus-sat-stat weight-500 txt-primary text-center mt-5"><span class="counter-anim">
                                    <?php echo ($totalData["userCurrent"]); ?>
                                </span></div>
                            <div id="userBefore" class="cus-sat-stat weight-500 txt-primary text-center mt-5"><span class="counter-anim before">
                                    <?php echo ($totalData["userBefore"]); ?>
                                </span></div>
                            <div class="mt-60">
                                <span style="visibility:<?php echo $visibility; ?>"><input type="checkbox" class="btnTotalBefore" /> Show the same period before</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Graph Sentiment Monitoring Analysis</h6>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div id="ChartSentiment" class="chart-layout" style="min-height: 300px;"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Table Group Keyword List</h6>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div style="overflow: auto; height: 400px;">
                    <style>
                        .table-striped>tbody>tr:nth-of-type(odd) {
                            background-color: #f9f9f9;
                        }
                    </style>
                        <table id="tableGraph" class="table table-bordered jambo_table table-striped datatable-fixed-header col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-collapse: collapse; width: 100%;">
                            <thead class="datatable-fixed-header">
                                <tr>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;">Group Keyword Name</th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/facebook-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/twitter-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/youtube-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/instagram-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/tik-tok-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/line-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/news-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;"><img src="themes/default/assets/images/interface/webboard-color.png" width="26" height="26"></th>
                                    <th style="position: sticky; top: 0;background:#1b6cb9;text-align: center;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tableGraph_data" style="vertical-align: middle; text-align: center;">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>