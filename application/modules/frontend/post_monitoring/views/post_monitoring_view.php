<div class="container-fluid">
    <form id="formAddPost" class="form-horizontal form-label-left" novalidate="">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">List Post</h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div id="BoxMyPost" style="height: 115px">
                                <div class="scroll-pane">
                                    <?php
                                    foreach($client_post as $k_row=>$v_row) {
                                        $selected = ($v_row['post_id']==$this->input->get("post_id")) ? "active" : "";
                                    ?>
                                    <div class="post-list <?php echo $selected;?>" post-id="<?php echo $v_row['post_id'];?>">
                                        <div class="pull-left PostName tooltip2" data-toggle="tooltip2" title="<?php echo $v_row['post_name'];?>"><?php echo $v_row['post_name'];?></div>
                                        <div class="pull-right">
                                            <?php if($v_row['post_expire']) { ?><i class="fa fa-exclamation-triangle"></i><?php } ?>
                                            <a href="javascript:;" class="btnPrint" ><i class="fa fa-print"></i></a>
                                            <a href="javascript:;" class="btnEditPost"><i class="fa fa-cog"></i></a>
                                            <a href="javascript:;" class="btnDelPost"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <br />
                            <button type="button" class="btn btn-primary btn-sm btn-round btnCancelPost" target="formAddPost">Add Post</button>
                            <input type="hidden" name="post_id" id="post_id" value="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">Add Post</h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Topic</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input id="txtPostName" name="post_name" type="text" class="form-control required" placeholder="Post Name" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Post Link</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <input type="text" class="form-control required" id="post_url" name="post_url" />
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="line" />
                            <div class="pull-left">
                                <i class="fa fa-exclamation-triangle"></i> Post Expire
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-default btn-sm btn-round btnCancelPost" target="formAddPost">Cancel</button>
                                <button type="button" class="btn btn-success btn-sm btn-round btnSavePost" target="formAddPost">Submit</button>
                                <input type="hidden" id="post_renew" name="post_renew" value="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">       
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="post-network" class="panel panel-primary card-view x_panel">
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="tab-struct custom-tab-1">
                                <ul role="tablist" class="nav nav-tabs" id="myTabs_7">
                                    <li class="active" role="presentation"><a data-toggle="tab" role="tab"  href="#single-chart" style="color: #000000;">Single Chart</a></li>
                                    <li role="presentation"><a data-toggle="tab" role="tab" href="#all-chart">All Chart</a></li>
                                    <!-- <li role="presentation"><a data-toggle="tab" role="tab" href="#table-chart">Table Chart</a></li> -->
                                </ul>
                            </div>
                            <br />
                            <div class="tab2-content">
                                <div id="single-chart" class="x_content tab2-pane">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <i class="icon-direction"> Click series on chart for "Mark"</i>
                                        </div>
                                    </div>
                                    <br />
                                    <?php 
                                    foreach(array("likes","shares","comments") as $val) { 
                                        $panel = "";
                                        switch ($val) {
                                            case 'likes': $panel = "panel-primary";break;
                                            case 'shares': $panel = "panel-danger";break;
                                            case 'comments': $panel = "panel-success";break;
                                        }
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-10 col-md-9 col-sm-9 col-xs-12">
                                            <div id="chart-<?php echo $val; ?>" class="chart-layout"></div>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                            <div id="mark-<?php echo $val; ?>" class="panel <?php echo $panel; ?> card-view x_panel">
                                                <div class="panel-heading">
                                                    <div class="pull-left"><h6 class="panel-title txt-dark">Mark <?php echo $val; ?></h6></div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body">
                                                        <div class="mark-box scroll-pane">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br /><hr class="line line-post"/><br />
                                    <?php } ?>
                                </div>
                                <div id="all-chart" class="x_content tab2-pane">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div id="chart-all" class="chart-layout"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>