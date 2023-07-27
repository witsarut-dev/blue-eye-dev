<div class="container-fluid">
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default card-view" style="padding: 10px 5px 10px 0px; margin-top: 10px; margin-bottom: 15px;">
				<div class="panel-wrapper collapse in">
					<div class="panel-body" style="padding: 0px;">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-right: 5px;">
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
        <div class="clearfix"></div>
        <div class="">
            <div class="col-lg-2 col-md-5 col-sm-5 col-xs-12 list-box">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">List Graph</h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="">
                                <div id="BoxMyGarph">
                                    <?php
                                    foreach($client_graph as $k_row=>$v_row) {
                                    $selected = ($v_row['graph_id']==$this->input->get("graph_id")) ? "active" : "";
                                    ?>
                                    <div class="graph-list <?php echo $selected;?>" graph-id="<?php echo $v_row['graph_id'];?>">
                                        <div class="pull-left GraphName"><?php echo $v_row['graph_name'];?></div>
                                        <div class="pull-right">
                                            <a href="javascript:;" class="btnEditGraph"><i class="fa fa-cog"></i></a>
                                            <a href="javascript:;" class="btnDeleteGraph"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <br />
                                <div id="BoxAddGraph">
                                    <form id="formAddGraph" class="form-horizontal form-label-left" novalidate="">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-primary btnSaveGraph" target="formAddGraph"><i class="fa fa-save"></i></button>
                                                <input id="txtGraphName" type="text" class="form-control required" placeholder="Graph Name" maxlength="255">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm btn-round" id="btnAddGraph">Add Graph</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-7 col-sm-7 col-xs-12 list-box">
                <div class="panel panel card-view">
                    <div class="" id="GraphType">
                    <div class="panel panel panel-success card-view">
                            <div class="panel-heading">
                                <h1 class="panel-title">ตัวแปรเชิงปริมาณ Y</h1>
                                <?php $graph_y = array("Sentiment"=>"Sentiment","Mention"=>"Mention"); ?>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body" style="padding-bottom: 5px;">
                                    <div id="container_y2" class="panel-body ">
                                        <?php
                                        foreach($graph_y as $k_row=>$v_row) {
                                        ?>
                                        <div itemid="<?php echo $k_row;?>" class="btn btn-success btn-outline fancy-button btn-0 btn-sm <?php if($k_row==$this->input->get("graph_y")) echo 'selected';?>"><?php echo $v_row;?></div>
                                        <?php 
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-primary card-view">
                            <div class="panel-heading">
                                <h1 class="panel-title">ตัวแปรเชิงคุณภาพ X</h1>
                                <?php $graph_x = array("MediaType"=>"Media Type","Company"=>"Company","GroupKeyword"=>"Group Keyword","KeywordTop5"=>"Keyword Top 5"); ?>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body" style="padding-bottom: 5px;">
                                    <div id="container_x2" class="panel-body ">
                                        <?php
                                        foreach($graph_x as $k_row=>$v_row) {
                                        ?>
                                        <div itemid="<?php echo $k_row;?>" class="btn btn-primary btn-outline fancy-button btn-0 btn-sm <?php if($k_row==$this->input->get("graph_x")) echo 'selected';?>"><?php echo $v_row;?></div>
                                        <?php 
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default card-view">
                            <div class="panel-heading">
                                <h1 class="panel-title">Graph Type</h1>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body graph-type">
                                    <a class="btn btn-app <?php if($this->input->get('graph_type')=='Pie') echo 'selected';?>" graph-type="Pie"><i class="fa fa-pie-chart"></i> Pie</a>
                                    <a class="btn btn-app <?php if($this->input->get('graph_type')=='Bar') echo 'selected';?>" graph-type="Bar"><i class="fa fa-bar-chart"></i> Bar</a>
                                    <a class="btn btn-app <?php if($this->input->get('graph_type')=='Line') echo 'selected';?>" graph-type="Line"><i class="fa fa-line-chart"></i> Line</a>
                                    <a class="btn btn-app <?php if($this->input->get('graph_type')=='Table') echo 'selected';?>" graph-type="Table"><i class="fa fa-th"></i> Table</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 result-box">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">Display Graph</h6></div>
                        <div class="pull-right"><a href="javascript:;" class="btn-full-screen"><i class="fa fa-arrows-alt" data-toggle="tooltip" title="Full Screen"></i></a></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="">
                                <h7 class="choose-remark text-danger"><i class="fa fa-check-square-o"></i> Please select axis Y,X and Graph Type.</h7>
                                <div id="ChartContainer" class="ChartContainer">
                                </div>
                                <div id="ChartTable">
                                    <div class="pull-left">
                                        <button type="button" class="btn btn-primary btn-round fa fa-trash btnDeletePostTB"> Delete</button>
                                        <!-- <button type="button" class="btn btn-primary btn-round fa fa-user btnBlockPostTB"> Block</button> -->
                                        <button type="button" class="btn btn-primary btn-round fa fa-eye-slash btnHidePostTB"> Hide</button>
                                    </div>
                                    <div class="btn-export">
                                        <div class="dropdown" style="display: inline-block;">
												<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">CSV <span class="caret"></span></button>
												<ul class="dropdown-menu" role="menu">
                                                    <?php foreach($company_keyword as $k_row=>$v_row) { ?>
                                                    <li><a href="<?php echo site_url($module."/cmdCSV/".$v_row['company_keyword_id']);?>"><?php echo $v_row['company_keyword_name'];?></a></li>
                                                    <?php } ?>
												</ul>
                                        </div>
                                        <div class="dropdown" style="display: inline-block;">
												<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Excel <span class="caret"></span></button>
												<ul class="dropdown-menu dropdown-menu-2" role="menu">
                                                    <li><a href="<?php echo site_url($module."/cmdExport");?>" class="text-success">All data</a></li>
                                                    <?php foreach($company_keyword as $k_row=>$v_row) { ?>
                                                    <li><a href="<?php echo site_url($module."/cmdExport/".$v_row['company_keyword_id']);?>"><?php echo $v_row['company_keyword_name'];?></a></li>
                                                    <?php } ?>
												</ul>
										</div>
                                        <!-- <button class="btn btn-success" onclick="window.location.href='<?php echo site_url($module."/cmdExport");?>'">Excel</button> -->
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="table-responsive">
                                        <table id="tableGraph" style="width:2500px;" class="table table-bordered jambo_table table-striped datatable-fixed-header">
                                            <thead>
                                                <tr>
                                                    <th style="vertical-align: middle;"><span class="select-checkbox"></span><input type="checkbox" class="chk-all" value="" /></th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Channel</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Type</th>
                                                    <!-- <th style="text-align: center; vertical-align: middle; text-transform: capitalize; background-color: #FEC89A; color: #000000;"><?php// echo $business_type; ?></th> -->
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Account</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Mention</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Sentiment</th>
                                                    <!-- <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Engagement</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Like</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Love</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Wow</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Laugh</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Sad</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Angry</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none; background-color: #f4f4f4; color: #000000;">Care</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Share</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Comment</th> -->
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Date</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Time</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">URL</th>
                                                    <th style="text-align: center; vertical-align: middle; text-transform: none;">Keyword</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div id="input-post-id">

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