<div id="SettingData" class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                    <ul id="myTab" class="nav nav-pills nav-pills-rounded" role="tablist">
                        <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true"><b>Keyword Setting</b></a></li>
                        <li role="presentation" class=""><a href="#include-excludetab" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false"><b>Include & Exclude Keyword Setting</b></a></li>
                        <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false"><b>View Profile</b></a></li>
                        <!-- <li role="presentation" class=""><a href="#tab_content4" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false"><b>Fake News</b></a></li> -->
                    </ul>
                    <br />
                    <div id="myTabContent" class="tab-content">
                        <?php echo $this->load->view("setting/setting_tab1"); ?>
                        <?php echo $this->load->view("setting/setting_tab5"); ?>
                        <?php echo $this->load->view("setting/setting_tab2"); ?>
                        <?php //echo $this->load->view("setting/setting_tab4"); ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>