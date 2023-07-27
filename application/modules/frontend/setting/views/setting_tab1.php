<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
    <div class="row">
        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view x_panel">
                <div class="panel-heading">
                    <div class="pull-left"><h6 class="panel-title txt-dark">Add Company</h6></div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="x_content" style="border: none;">
                            <form id="formAddCom" class="form-horizontal form-label-left" novalidate="">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 small">Company name</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" name="company_keyword_name" required="required" class="form-control" placeholder="Company name" maxlength="255">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 small">FB official link</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" name="company_keyword_fb" class="form-control" placeholder="FB official link">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12 small">Company type</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control" name="company_keyword_type">
                                            <!--        <option value="Compnay">Compnay</option>
                                            <option value="Partner">Partner</option> -->
                                            <option value="Competitor">Competitor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-8 col-sm-12 col-xs-12 col-md-offset-4">
                                        <?php if($setting_allow) { ?>
                                        <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddCompany" targetForm="formAddCom" target="CompanyList"> Add</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default card-view x_panel">
                <div class="panel-heading">
                    <div class="pull-left"><h6 class="panel-title txt-dark">Keyword management</h6></div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div id="CompanyList" class="panel panel-default card-view x_panel">
                                    <div class="panel-heading">
                                        <div class="pull-left"><h6 class="panel-title txt-dark">Company Name</h6></div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-wrapper collapse in">
                                        <div class="panel-body">
                                            <div class="x_content" style="border: none;">
                                                <form id="formCompanyList" class="form-horizontal">
                                                    <ul class="to_do">
                                                        <?php foreach($company as $k_row=>$v_row) {  ?>
                                                        <li>
                                                            <p><input name="company_keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['company_keyword_id'];?>" target="list-group-keyword"> <?php echo $v_row['company_keyword_name'];?> (<?php echo $v_row['company_keyword_type'];?>)</p>
                                                        </li>
                                                        <?php } ?>
                                                    </ul>
                                                </form>
                                                <div class="ln_solid"></div>
                                                <?php if($setting_allow) { ?>
                                                <div class="btn-group" data-toggle="buttons">
                                                    <button type="button" class="btn btn-primary btn-round fa fa-trash btnDelCompany" disabled target="CompanyList"> Delete</button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if($categories_allow) { ?>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div id="GroupKeywordList_categories" class="panel panel-default card-view x_panel">
                                        <div class="panel-heading">
                                            <div class="pull-left"><h6 class="panel-title txt-dark">Group keyword</h6></div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <form id="formAddGroupKeyword_categories" class="form-horizontal form-label-left" novalidate="">
                                                    <div class="x_content" style="border: none;">
                                                        <ul class="to_do list-group-keyword">
                                                            <?php foreach($group_keyword as $k_row=>$v_row) {  ?>
                                                            <li>
                                                                <p><input name="group_keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['group_keyword_id'];?>" target="list-categories" parent="<?php echo $v_row['company_keyword_id'];?>"> <?php echo $v_row['group_keyword_name'];?></p>
                                                            </li>
                                                            <?php } ?>
                                                        </ul>
                                                        <br/>
                                                        <span id="CompanyListSelect" class="text-white"></span>
                                                        <input id="group_keyword_name" name="group_keyword_name" type="text" class="form-control required inputAddList" placeholder="Group keyword" maxlength="255">
                                                        <div class="ln_solid"></div>
                                                        <?php if($setting_allow) { ?>
                                                            <div class="btn-group" data-toggle="buttons">
                                                                <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddGroupKeyword_categories" target="GroupKeywordList_categories" targetForm="formAddGroupKeyword_categories"> Add</button>
                                                                <button type="button" class="btn btn-primary btn-round fa fa-trash btnDelGroupKeyword_categories" disabled target="GroupKeywordList_categories"> Delete</button>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div id="CategoriesList" class="panel panel-default card-view x_panel">
                                        <div class="panel-heading">
                                            <div class="pull-left"><h6 class="panel-title txt-dark" style="width: 30%;">Category</h6></div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <form id="formAddCategories" class="form-horizontal form-label-left" novalidate="">
                                                    <div class="x_content" style="border: none;">
                                                        <ul class="to_do list-categories" style="margin-bottom: 30px;">
                                                            <?php foreach($categories as $k_row=>$v_row) {  ?>
                                                            <li class="col-md-4 col-sm-6 col-xs-12" style="padding-left: 0px;">
                                                                <p><input name="categories_id[]" type="checkbox" class="flat" value="<?php echo $v_row['categories_id'];?>" target="list-keywrod" parent="<?php echo $v_row['group_keyword_id'];?>"> <?php echo $v_row['categories_name'];?></p>
                                                            </li>
                                                            <?php } ?>
                                                        </ul>
                                                        <div class="clearfix"></div>
                                                        <br />
                                                        <span id="GroupKeywordList_categories" class="text-white"></span>
                                                        <input id="categories_name" name="categories_name" type="text" class="form-control required inputAddList" placeholder="Category Name" maxlength="255">
                                                        <div class="ln_solid"></div>
                                                        <?php if($setting_allow) { ?>
                                                            <div class="btn-group" data-toggle="buttons">
                                                                <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddCategories" target="CategoriesList" targetForm="formAddCategories"> Add</button>
                                                                <button type="button" class="btn btn-primary btn-round fa fa-trash btnDelCategories" disabled target="CategoriesList"> Delete</button>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div id="KeywordList" class="panel panel-default card-view x_panel">
                                        <div class="panel-heading">
                                            <div class="pull-left"><h6 class="panel-title txt-dark" style="width: 30%;">Keyword</h6></div>
                                            <?php if($setting_allow) { ?>
                                            <div class="pull-right">ตัวอย่างไฟล์สำหรับ import "<a style="text-decoration: underline;" href="<?php echo base_url("upload/keyword/import_keyword.xls");?>">Click Here</a>" <button class="btn btn-sm btn-success btnImportKeyword">Import Keyword</button></div>
                                            <?php } ?>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <form id="formAddKeyword" class="form-horizontal form-label-left" novalidate="">
                                                    <div class="x_content" style="border: none;">
                                                        <div class="to_do list-keywrod">
                                                            <?php foreach($keyword as $k_row=>$v_row) {  ?>
                                                            <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left: 0px;">
                                                                <?php $thai_only = ($v_row['thai_only']=="1") ? ' <i class="flag flag-th"></i>' : ''; ?>
                                                                <?php $primary_keyword = ($v_row['primary_keyword']=="1") ? ' <i class="fa fa-star" style="color: #FFD91E;"></i>': ''; ?>
                                                                <p>
                                                                    <input name="keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['keyword_id'];?>" parent="<?php echo $v_row['categories_id'];?>"> 
                                                                    <a href="<?php echo site_url("setting/setting_filter/".$v_row['keyword_id']);?>" class="fancybox link-setting">
                                                                        <?php echo $v_row['keyword_name'].$thai_only.$primary_keyword;?>
                                                                    </a>
                                                                </p>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <br />
                                                        <span id="CategoriesListSelect" class="text-white" style="display: none;"></span>
                                                        <input id="keyword_name" name="keyword_name" type="text" class="form-control required inputAddList" placeholder="keyword">
                                                        <div class="checkbox  checkbox-primary">
                                                            <input id="thai_only_chk1" type="checkbox" name="thai_only" value="1">
                                                            <label for="thai_only_chk1">for thai language only </label>&nbsp;<i class="flag flag-th"></i>
                                                        </div>
                                                        <div class="checkbox checkbox-primary">
                                                            <input id="primary_keyword_chk1" type="checkbox" name="primary_keyword" value="1">
                                                            <label for="primary_keyword_chk1">for primary keyword </label>&nbsp;<i class="fa fa-star" style="color: #FFD91E;"></i>
                                                        </div>
                                                        <div class="ln_solid"></div>
                                                        <?php if($setting_allow) { ?>
                                                        <div class="btn-group" data-toggle="buttons">
                                                            <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddKeyword_categories" target="KeywordList" targetForm="formAddKeyword" maxlength="255"> Add</button>
                                                            <button type="button" class="btn btn-primary btn-round fa fa-trash btnDelKeyword_categories" disabled target="KeywordList"> Delete</button>
                                                        </div>
                                                        <?php }?>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div id="GroupKeywordList" class="panel panel-default card-view x_panel">
                                        <div class="panel-heading">
                                            <div class="pull-left"><h6 class="panel-title txt-dark">Group keyword</h6></div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <form id="formAddGroupKeyword" class="form-horizontal form-label-left" novalidate="">
                                                    <div class="x_content" style="border: none;">
                                                        <ul class="to_do list-group-keyword">
                                                            <?php foreach($group_keyword as $k_row=>$v_row) {  ?>
                                                            <li>
                                                                <p><input name="group_keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['group_keyword_id'];?>" target="list-keywrod" parent="<?php echo $v_row['company_keyword_id'];?>"> <?php echo $v_row['group_keyword_name'];?></p>
                                                            </li>
                                                            <?php } ?>
                                                        </ul>
                                                        <div class="clearfix"></div>
                                                        <br />
                                                        <span id="CompanyListSelect" class="text-white"></span>
                                                        <input id="group_keyword_name" name="group_keyword_name" type="text" class="form-control required inputAddList" placeholder="Group keyword" maxlength="255">
                                                        <div class="ln_solid"></div>
                                                        <?php if($setting_allow) { ?>
                                                        <div class="btn-group" data-toggle="buttons">
                                                            <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddGroupKeyword" target="GroupKeywordList" targetForm="formAddGroupKeyword"> Add</button>
                                                            <button type="button" class="btn btn-primary btn-round fa fa-trash btnDelGroupKeyword" disabled target="GroupKeywordList"> Delete</button>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div id="KeywordList" class="panel panel-default card-view x_panel">
                                        <div class="panel-heading">
                                            <div class="pull-left"><h6 class="panel-title txt-dark" style="width: 30%;">Keyword</h6></div>
                                            <?php if($setting_allow) { ?>
                                                <div class="pull-right">ตัวอย่างไฟล์สำหรับ import "<a style="text-decoration: underline;" href="<?php echo base_url("upload/keyword/import_keyword.xls");?>">Click Here</a>" <button class="btn btn-sm btn-success btnImportKeyword">Import Keyword</button></div>
                                            <?php } ?>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <form id="formAddKeyword" class="form-horizontal form-label-left" novalidate="">
                                                    <div class="x_content" style="border: none;">
                                                        <div class="to_do list-keywrod">
                                                            <?php foreach($keyword as $k_row=>$v_row) {  ?>
                                                            <div class="col-md-4 col-sm-6 col-xs-12" style="padding-left: 0px;">
                                                                <?php $thai_only = ($v_row['thai_only']=="1") ? ' <i class="flag flag-th"></i>' : ''; ?>
                                                                <?php $primary_keyword = ($v_row['primary_keyword']=="1") ? ' <i class="fa fa-star" style="color: #FFD91E;"></i>': ''; ?>
                                                                <p>
                                                                    <input name="keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['keyword_id'];?>" parent="<?php echo $v_row['group_keyword_id'];?>"> 
                                                                    <a href="<?php echo site_url("setting/setting_filter/".$v_row['keyword_id']);?>" class="fancybox link-setting">
                                                                        <?php echo $v_row['keyword_name'].$thai_only.$primary_keyword;?>
                                                                    </a>
                                                                </p>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <br />
                                                        <span id="GroupKeywordListSelect" class="text-white" style="display: none;"></span>
                                                        <input id="keyword_name" name="keyword_name" type="text" class="form-control required inputAddList" placeholder="keyword">
                                                        <div class="checkbox  checkbox-primary">
                                                            <input id="thai_only_chk1" type="checkbox" name="thai_only" value="1">
                                                            <label for="thai_only_chk1">for thai language only </label>&nbsp;<i class="flag flag-th"></i>
                                                        </div>
                                                        <div class="checkbox  checkbox-primary">
                                                            <input id="primary_keyword_chk1" type="checkbox" name="primary_keyword" value="1">
                                                            <label for="primary_keyword_chk1">for primary keyword </label>
                                                        </div>
                                                        <div class="ln_solid"></div>
                                                        <?php if($setting_allow) { ?>
                                                        <div class="btn-group" data-toggle="buttons">
                                                            <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddKeyword" target="KeywordList" targetForm="formAddKeyword" maxlength="255"> Add</button>
                                                            <button type="button" class="btn btn-primary btn-round fa fa-trash btnDelKeyword" disabled target="KeywordList"> Delete</button>
                                                        </div>
                                                        <?php }?>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if($setting_allow) { ?>
                                <div class="clearfix"></div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="panel panel-default card-view x_panel">                        
                                        <div class="panel-heading">
                                            <div class="pull-left"><h6 class="panel-title txt-dark" style="width: 100%;">Link url import</h6></div>
                                            <div class="pull-right">ตัวอย่างไฟล์สำหรับ import Link url   "<a style="text-decoration: underline;" href="<?php echo base_url("upload/link_url/import_link_url.xlsx");?>">Click Here</a> "  <button class="btn btn-sm btn-warning btnImportLinkurl">Import link url</button></div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="showPageImport" style="display:none">
    <div class="panel panel-default card-view x_panel">
        <form id="formImport" action="<?php echo site_url("setting/keyword_import/cmdSaveImport");?>" class="form-horizontal" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-sm-12">Choose File : <input type="file" name="file_import" id="file_import" filetype="xls|xlsx" style="padding: 6px 12px;display: inline-block;" /></label>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8"><button type="button" class="btn btn-success btn-import-file">Import File</button></div>
            </div>
            <input type="hidden" name="group_keyword_id" value="" />
        </form>
    </div>
</div>
<!-- Add it -->
<div id="showLinkImport" style="display:none"> 
    <div class="panel panel-default card-view x_panel">
        <form id="formImportUrl" action="<?php echo site_url("setting/link_import/cmdSaveImport");?>" class="form-horizontal" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-sm-12">Choose File of link url: <input type="file" name="file_import_url" id="file_import_url" value="" filetype="xls|xlsx" style="padding: 6px 12px;display: inline-block;" /></label>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8"><button type="button" class="btn btn-success  btn-import-file-url">Import File</button></div>
            </div>
        </form>
    </div>
</div>