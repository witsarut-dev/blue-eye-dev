<div id="SettingData" class="right_col_fix" role="main">
  <div class="">
    <div class="clearfix"></div>
    <br />
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="" role="tabpanel" data-example-id="togglable-tabs">
          <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true"><b>Keyword Setting</b></a></li>
            <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false"><b>View Profile</b></a></li>
          </ul>
          <div id="myTabContent" class="tab-content">
            <?php echo $this->load->view("setting/setting_tab1"); ?>
            <?php echo $this->load->view("setting/setting_tab2"); ?>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>