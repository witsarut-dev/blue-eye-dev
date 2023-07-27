<div role="tabpanel" class="tab-pane fade" id="include-excludetab" aria-labelledby="keyword-tab">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view x_panel">
                <div class="panel-heading">
                    <div class="pull-left"><h6 class="panel-title txt-dark">Include & Exclude Keyword Setting</h6></div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <form name="myForm" id="formAddOperation" class="form-horizontal form-label-left" novalidate="">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Keyword</label>
                                        <div id="addIncludeExclude" class="input-group">
                                            <label class="control-label" id="lbkeyword" key-id=""></label>
                                            &nbsp;
                                            <div class="control-label pull-right">
                                                <button type="button" data-toggle="modal" data-target="#exampleModal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Tag Keyword</label>
                                        <div id="addIncludeExclude" class="input-group">
                                            <input type="text" id="tagkeyword" class="form-control" placeholder="Include - Exclude keyword" aria-label="Include - Exclude keyword" required>
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-success buttonInEx" id="buttonIn">Include</button>
                                                <button type="button" class="btn btn-danger buttonInEx" id="buttonEx">Exclude</button>
                                            </div>  
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div id="KeywordList" class="panel panel-default card-view x_panel">
                                    <div class="panel-heading">
                                        <div class="pull-left"><h6 class="panel-title txt-dark">Operation List</h6></div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-wrapper collapse in">
                                        <div class="panel-body">
                                            <div class="scroll-pane">
                                                <div class="post-list">
                                                    <div id="detal">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #f5f5f5;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="exampleModalLabel">Keyword</h3>
            </div>
            <div class="modal-body">
                <form id="formAddKeyword" class="form-horizontal form-label-left" novalidate="">
                    <div class="x_content" style="border: none;">
                        <div class="to_do list-keywrod" >
                            <?php foreach($keyword as $k_row=>$v_row) {  ?>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <?php $thai_only = ($v_row['thai_only']=="1") ? ' <i class="flag flag-th"></i>' : ''; ?>
                                <p>
                                    <!-- <input type="radio" name="myRadios" onclick="handleClick(this);" value="<?php echo $v_row['keyword_name'];?>"/> -->
                                    <input type="radio" name="myRadios" key-id="<?php echo $v_row['keyword_id'];?>" value="<?php echo $v_row['keyword_name'];?>"/>
                                    <?php echo $v_row['keyword_name'].$thai_only;?>
                                </p>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <br />
                    </div>
                    <?php if($setting_allow) { ?>
                    <div class="btn-group col-md-offset-11 col-sm-offset-10 col-xs-offset-2" data-toggle="buttons">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    <?php } ?>
                    
                </form>
                
            </div>
        </div>
    </div>
</div>

