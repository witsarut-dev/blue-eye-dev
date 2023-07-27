<div id="filterRealtime" class="container body fancybox-body">
  <form name="formFilterKeyword" action="<?php echo site_url($module."/cmdFilterKeyword");?>" method="post">
    <div class="main_container">
      <div class="right_col" role="main">
        <div class="">
          <div class="page-title">
            <div class="title_left">
              <h3>Filter Real-time Monitoring</h3>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="">
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Group Keyword</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <ul class="to_do">
                    <?php 
                    foreach($group_keyword as $k_row=>$v_row) {  
                      $group_keyword_id = explode(",",$realtime_group_keyword);
                      $checked = in_array($v_row['group_keyword_id'],$group_keyword_id) ? ' checked="checked" ' : '';
                    ?>
                    <li>
                      <p><input name="group_keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['group_keyword_id'];?>" target="list-keywrod" parent="<?php echo $v_row['company_keyword_id'];?>" <?php echo $checked;?>> <?php echo $v_row['group_keyword_name'];?></p>
                    </li>
                    <?php } ?>
                  </ul>
                </div>
                <div class="clearfix"></div>
              </div>
            </div>
            <div class="col-md-8 col-sm-8 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Keyword</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="">
                    <div class="to_do list-keywrod">
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <p><input type="checkbox" class="flat check-all"> Select All</p>
                      </div>
                      <?php 
                      foreach($keyword as $k_row=>$v_row) {  
                        $keyword_id = explode(",",$realtime_keyword);
                        $checked = in_array($v_row['keyword_id'],$keyword_id) ? ' checked="checked" ' : '';
                      ?>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <p><input name="keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['keyword_id'];?>" parent="<?php echo $v_row['group_keyword_id'];?>" <?php echo $checked;?>> <?php echo $v_row['keyword_name'];?></p>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="navbar-right"><button type="submit" class="btn btn-success btn-round"><i class="fa fa-save"></i> Submit</button></div>
        </div>
        
      </div>
    </div>
  </form>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $("input.flat")[0] && $(document).ready(function() {
        $("input.flat").iCheck({
            checkboxClass: "icheckbox_flat",
            radioClass: "iradio_flat"
        })
    });
});
</script>