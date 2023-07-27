
    <form id="formAddLink" class="form-horizontal form-label-left" novalidate="">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">List Page</h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div id="BoxMyLink" style="height: 115px">
                                <div class="scroll-pane">
                                    <?php
                                    foreach($client_link as $k_row=>$v_row) {
                                    $selected = ($v_row['link_id']==$this->input->get("link_id")) ? "active" : "";
                                    ?>
                                    <div class="link-list <?php echo $selected;?>" link-id="<?php echo $v_row['link_id'];?>">
                                        <div class="pull-left LinkName tooltip2" data-toggle="tooltip2" title="<?php echo $v_row['link_name'];?>"><?php echo $v_row['link_name'];?></div>
                                        <div class="pull-right">
                                            <a href="javascript:;" class="btnEditLink"><i class="fa fa-cog"></i></a>
                                            <a href="javascript:;" class="btnDeleteLink"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <br />
                            <button type="button" class="btn btn-primary btn-sm btn-round btnCancelLink" target="formAddLink">Add Page</button>
                            <input type="hidden" name="link_id" id="link_id" value="" />
                            <input type="hidden" name="link_type" id="link_type" value="fanpage" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">Add Page</h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Page URL</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12">
                                        <input id="link_url1" name="link_url[1]" type="text" class="form-control required" placeholder="Page URL" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Example</label>
                                    <div class="col-md-10 col-sm-10 col-xs-12 control-left">
                                        <input type="text" class="form-control input-readonly" value="https://www.facebook.com/pagename" disabled="disabled" style="border:none"/>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="line" />
                            <div class="pull-left">
                                <i class="fa fa-info-circle"></i> Right click choose activity.
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-default btn-sm btn-round btnCancelLink" target="formAddLink">Cancel</button>
                                <button type="button" class="btn btn-success btn-sm btn-round btnSaveLink" target="formAddLink">Submit</button>
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
                <div id="link-network" class="panel panel-primary card-view x_panel">
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="x_content">
                                <div class="col-lg-10 col-md-8 col-sm-12 col-xs-12">
                                    <i class="icon-share" style="margin-right: 10px"> Share</i>
                                    <i class="icon-bubble" style="margin-right: 10px"> Comment</i>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12">
                                    <div class="top_search pull-right">
                                        <div class="x_title">
                                            <ul class="nav navbar-right panel_toolbox">
                                                <li class="text-white" style="margin-right: 10px">Zoom</li>
                                                <li><span class="fa fa-search-plus nw-zoom" onclick="nw_zoom_out();"></span></li>
                                                <li><span class="fa fa-search-minus nw-zoom" onclick="nw_zoom_in();"></span></li>
                                                <li><span class="fa fa-search nw-zoom" onclick="nw_zoom_reset();"></span></li>
                                                <li class="text-white"><a href="javascript:;" class="btn btn-xs btn-success" id="cmdExport">Excel</a></li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="mynetwork"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="contextmenu">
            <div class="btn-close-context"><i class="fa fa-times"></i></div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6"><input type="checkbox" name="activity_all" value="all" checked="checked" /> All</div>
                <div class="col-md-6 col-sm-6 col-xs-6"><input type="checkbox" name="user_activity[]" value="shares" /> <i class="icon-share"></i> Share</div>
                <div class="col-md-6 col-sm-6 col-xs-6"><input type="checkbox" name="user_activity[]" value="comments" /> <i class="icon-bubble"></i> Comment</div>
            </div>
            <div class="btn-apply-context"><button type="button" class="btn btn-xs btn-success">Apply</button></div>
        </div>
    </form>
