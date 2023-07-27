<div class="container-fluid">
    <form id="formAddTimeline" class="form-horizontal form-label-left" novalidate="">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">List Timeline</h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div id="BoxMyTimeline" style="height: 115px">
                                <div class="scroll-pane">
                                    <?php
                                    foreach($client_timeline as $k_row=>$v_row) {
                                        $selected = ($v_row['timeline_id']==$this->input->get("timeline_id")) ? "active" : "";
                                    ?>
                                    <div class="timeline-list <?php echo $selected;?>" timeline-id="<?php echo $v_row['timeline_id'];?>">
                                        <div class="pull-left TimelineName tooltip2" data-toggle="tooltip2" title="<?php echo $v_row['timeline_name'];?>"><?php echo $v_row['timeline_name'];?></div>
                                        <div class="pull-right">
                                            <a href="javascript:;" class="btnEditTimeline"><i class="fa fa-cog"></i></a>
                                            <a href="javascript:;" class="btnDelTimeline"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <br />
                            <button type="button" class="btn btn-primary btn-sm btn-round btnCancelTimeline" target="formAddTimeline">Add Timeline</button>
                            <input type="hidden" name="timeline_id" id="timeline_id" value="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">Add Timeline</h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Topic</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <input id="txtTimelineName" name="timeline_name" type="text" class="form-control required" placeholder="Topic" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Period</label>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <div class="icon-timeline-date">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                        <input type="text" class="form-control required form-control input-daterange-datepicke input-readonly" id="timeline_date" name="timeline_date" value="" />
                                    </div>
                                    <label class="control-label col-md-1 col-sm-1 col-xs-12 small">Keyword</label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="txtTimelineName" name="keyword_name" type="text" class="form-control required input-readonly" placeholder="Keyword Name" maxlength="255" minlength="3" />
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="line" />
                            <div class="pull-right">
                                <button type="button" class="btn btn-default btn-sm btn-round btnCancelTimeline" target="formAddTimeline">Cancel</button>
                                <button type="button" class="btn btn-success btn-sm btn-round btnSaveTimeline" target="formAddTimeline">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">       
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="icon-loading" class="icon-container">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <br />
                <div id="msg-loading">ระบบกำลังดำเนินการ อาจใช้เวลานานขึ้นอยู่กับ Period และ keyword ที่คุณเลือก</div>
                <div id="timeline-box" class="panel panel-primary card-view x_panel">
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="pull-right">
                                <div class="top_search">
                                    <div class="x_title">
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li class="text-black" style="margin-right: 10px">Show</li>
                                            <li><span class="fa fa-minus-square show-all" onclick="click_show_all(0);" title="Start End" data-toggle="tooltip"></span></li>
                                            <li><span class="fa fa-bars show-all" onclick="click_show_all(1);" title="Show All" data-toggle="tooltip"></span></li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br />
                            <div id="mytimeline"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>