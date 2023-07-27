<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel user-detail">
        <div class="x_title">
          <h2>User Detail</h2>
          <div class="clearfixMobile"></div>
          <div class="pull-right">
            <a href="<?php echo site_url($module."/block_user");?>" class="btn btn-dark btn-round fancybox">Block User</a>
            <a href="<?php echo site_url($module."/activity_log");?>" class="btn btn-dark btn-round fancybox">Activity Log</a>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">

          <form id="formAccount" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">Username : </label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                  <p class="form-control-static"><?php echo $client['username']; ?></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">Company name : </label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                  <p class="form-control-static"><?php echo $client['company_name']; ?></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">Email : </label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                  <p class="form-control-static"><?php echo $client['email']; ?></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">Telephone : </label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                  <p class="form-control-static"><?php echo $client['telephone']; ?></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">Start join : </label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                  <p class="form-control-static"><?php echo getDateformat($client['start_join']); ?></p>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12">End join : </label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                  <p class="form-control-static"><?php echo getDateformat($client['end_join']); ?></p>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>