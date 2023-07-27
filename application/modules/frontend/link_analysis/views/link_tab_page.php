
<form id="formAddLink" class="form-horizontal form-label-left" novalidate="">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view x_panel">
                <div class="panel-heading">
                    <div class="pull-left"><h6 class="panel-title txt-dark">List Link</h6></div>
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
                        <button type="button" class="btn btn-primary btn-sm btn-round btnCancelLink" target="formAddLink">Add Link Page Post</button>
                        <input type="hidden" name="link_id" id="link_id" value="" />
                        <input type="hidden" name="link_type" id="link_type" value="page" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view x_panel">
                <div class="panel-heading">
                    <div class="pull-left"><h6 class="panel-title txt-dark">Add Link</h6></div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Name</label>
                                <div class="col-md-10 col-sm-10 col-xs-12">
                                    <input id="txtLinkName" name="link_name" type="text" class="form-control required" placeholder="Link Name" maxlength="255">
                                </div>
                            </div>
                        </div>
                        <?php for($i=1;$i<=$max_link;$i++) { ?>
                        <?php $required = ($i<3) ? 'required' : null; ?> 
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12 small">Link <?php echo $i;?></label>
                                <div class="col-md-10 col-sm-10 col-xs-12">
                                    <input type="text" class="form-control <?php echo $required;?>" id="link_url<?php echo $i;?>" name="link_url[<?php echo $i;?>]" />
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <hr class="line" />
                        <div class="pull-left">
                                <i class="fa fa-info-circle"></i> Double click node view more.
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
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <i class="icon-share" style="margin-right: 10px"> Share</i>
                                <i class="icon-bubble"> Comment</i>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="top_search pull-right">
                                    <div class="x_title">
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li class="text-white" style="margin-right: 10px">Zoom</li>
                                            <li><span class="fa fa-search-plus nw-zoom" onclick="nw_zoom_out();"></span></li>
                                            <li><span class="fa fa-search-minus nw-zoom" onclick="nw_zoom_in();"></span></li>
                                            <li><span class="fa fa-search nw-zoom" onclick="nw_zoom_reset();"></span></li>
                                            <li class="text-white" style="margin-left: 10px;margin-right: 10px">Filter</li>
                                            <li><span class="fa fa-filter nw-zoom" style="margin-right: 10px" onclick="show_filter();"></span>
                                                <ul class="link-filter">
                                                    <li><input type="checkbox" name="relate_all" value="all" checked="checked" /> All</li>
                                                    <?php for($i=2;$i<=9;$i++) { ?>
                                                    <li><input type="checkbox" name="link_relate[]" value="<?php echo $i;?>" /> <?php echo $i;?> Relationship</li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
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
</form>
