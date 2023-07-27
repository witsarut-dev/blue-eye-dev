<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
  <div class="row">
    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Add Company</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <form id="formAddCom" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">Company name</label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                <input type="text" name="company_keyword_name" required="required" class="form-control" placeholder="Company name" maxlength="255">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">FB official link</label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                <input type="text" name="company_keyword_fb" class="form-control" placeholder="FB official link">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">Compnay type</label>
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
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <h2>Keyword management</h2>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-12 col-xs-12">
            <div id="CompanyList" class="x_panel">
              <div class="x_title">
                <h2>Company Name</h2>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
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
          <div class="col-md-6 col-sm-12 col-xs-12">
            <div id="GroupKeywordList" class="x_panel">
              <div class="x_title">
                <h2>Group keyword</h2>
                <div class="clearfix"></div>
              </div>
              <form id="formAddGroupKeyword" class="form-horizontal form-label-left" novalidate="">
                <div class="x_content">
                  <ul class="to_do list-group-keyword">
                    <?php foreach($group_keyword as $k_row=>$v_row) {  ?>
                    <li>
                      <p><input name="group_keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['group_keyword_id'];?>" target="list-keywrod" parent="<?php echo $v_row['company_keyword_id'];?>"> <?php echo $v_row['group_keyword_name'];?></p>
                    </li>
                    <?php } ?>
                  </ul>
                  <br />
                  <strong id="CompanyListSelect" class="text-success"></strong>
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
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div id="KeywordList" class="x_panel">
              <div class="x_title">
                <h2 style="width: 30%;">Keyword</h2>
                <?php if($setting_allow) { ?>
                <div class="pull-right">ตัวอย่างไฟล์สำหรับ import "<a style="text-decoration: underline;" href="<?php echo base_url("upload/keyword/import_keyword.xls");?>">Click Here</a>" <button class="btn btn-sm btn-success btnImportKeyword">Import Keyword</button></div>
                <?php } ?>
                <div class="clearfix"></div>
              </div>
              <form id="formAddKeyword" class="form-horizontal form-label-left" novalidate="">
                <div class="x_content">
                  <div class="to_do list-keywrod">
                    <?php foreach($keyword as $k_row=>$v_row) {  ?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <p><input name="keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['keyword_id'];?>" parent="<?php echo $v_row['group_keyword_id'];?>"> <?php echo $v_row['keyword_name'];?></p>
                    </div>
                    <?php } ?>
                  </div>
                  <div class="clear"></div>
                  <br />
                  <strong id="GroupKeywordListSelect" class="text-success"></strong>
                  <input id="keyword_name" name="keyword_name" type="text" class="form-control required inputAddList" placeholder="keyword">
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
    </div>
  </div>
</div>
<div id="showPageImport" style="display:none">
  <form id="formImport" action="<?php echo site_url("setting/keyword_import/cmdSaveImport");?>" class="form-horizontal" method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label class="col-sm-12">Choose File : <input type="file" name="file_import" id="file_import" filetype="xls|xlsx" style="padding: 6px 12px;display: inline-block;" /></label>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-4 col-sm-8"><button type="button" class="btn btn-dark btn-import-file">Import File</button></div>
    </div>
    <input type="hidden" name="group_keyword_id" value="" />
  </form>
</div>