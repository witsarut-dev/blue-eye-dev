<div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="keyword-tab">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view x_panel">
                <div class="panel-heading">
                    <div class="pull-left"><h6 class="panel-title txt-dark">Keyword Operation</h6></div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-5 col-sm-12 col-xs-12">
                                <form id="formAddOperation" class="form-horizontal form-label-left" novalidate="">
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12 small"><span class="label label-primary">1</span> Keyword</label>
                                        <div class="col-md-8 col-sm-9 col-xs-12">
                                            <input type="text" name="keyword_name_1" required="required" class="form-control" placeholder="Keyword" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12 small">Operation</label>
                                        <div class="col-md-8 col-sm-9 col-xs-12">
                                            <select class="form-control" name="keyword_op" required="required">
                                                <option value="" selected="selected">Choose Operation</option>
                                                <option value="AND">AND</option>
                                                <option value="NOT">NOT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4 col-sm-3 col-xs-12 small"><span class="label label-primary">2</span> Keyword</label>
                                        <div class="col-md-8 col-sm-9 col-xs-12">
                                            <input type="text" name="keyword_name_2" required="required" class="form-control" placeholder="Keyword" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-8 col-sm-12 col-xs-12 col-md-offset-4">
                                            <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddOperation" targetForm="formAddOperation" target="formOperationList"> Add Operation</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-7 col-sm-12 col-xs-12">
                                <div id="KeywordList" class="panel panel-default card-view x_panel">
                                    <div class="panel-heading">
                                        <div class="pull-left"><h6 class="panel-title txt-dark">Operation List</h6></div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-wrapper collapse in">
                                        <div class="panel-body">
                                            <form id="formOperationList" class="form-horizontal form-label-left" novalidate="">
                                                <div class="x_content">
                                                    <div class="to_do list-keywrod-operation">
                                                        <?php 
                                                        foreach($operation as $k_row=>$v_row) {  
                                                            $label_op = ($v_row['keyword_op']=='NOT') ? 'label label-danger' : 'label label-primary';
                                                            $checked  = ($v_row['keyword_status']=='1') ? 'checked="checked"' : '';
                                                            $keyword_op_id = $v_row['keyword_op_id'];
                                                        ?>
                                                        <div class="col-md-12 col-sm-12 col-xs-12 key-op-list">
                                                            <div class="checkbox  checkbox-primary">
                                                                <input type="hidden" name="keyword_op_id" value="<?php echo $keyword_op_id;?>" />
                                                                <input type="checkbox" id="op_chk<?php echo $keyword_op_id;?>" class="btn-check-op" value="1" <?php echo $checked; ?> />
                                                                <label for="op_chk<?php echo $keyword_op_id;?>"></label>
                                                                <span class=""><?php echo $v_row['keyword_name_1'];?></span>
                                                                <span class="<?php echo $label_op; ?>"><?php echo $v_row['keyword_op'];?></span>
                                                                <span class=""><?php echo $v_row['keyword_name_2'];?></span>
                                                                <a href="javascript:;" class="btn-delete-op"><i class="fa fa-trash text-danger"></i></a>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="ln_solid"></div>
                                                    <i class="fa fa-info-circle"></i> Click the checkbox to enable or disable.
                                                </div>
                                            </form>
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