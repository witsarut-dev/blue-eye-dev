<div id="filterKeyword" class="container body fancybox-body">
  <form name="formChooseKeyword" action="<?php echo site_url($module."/cmdChooseKeyword");?>" method="post">
    <div class="main_container">
      <div class="right_col" role="main">
        <div class="">
          <div class="page-title">
            <div class="title_left">
              <h3>Edit Keyword Monitoring</h3>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Keyword List (เลือกได้ไม่เกิน <?php echo $choose_keyword;?> Keyword)</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="to_do">
                      <?php 
                      foreach($keyword as $k_row=>$v_row) { 
                        $checked = in_array($v_row['keyword_id'],$client_keyword) ? ' checked="checked" ' : '';
                      ?>
                      <div class="col-md-3 col-sm-4 col-xs-12">
                        <p><input name="keyword_id[]" type="checkbox" class="flat" value="<?php echo $v_row['keyword_id'];?>" <?php echo $checked;?>> <?php echo $v_row['keyword_name'];?></p>
                      </div>
                      <?php } ?>
                      <?php if(count($keyword)==0) { ?>
                      <div align="center">ไม่พบข้อมูล Keyword คุณสามารถเพิ่มข้อมูลได้โดยคลิกที่ปุ่มนี้ <a href="<?php echo site_url("setting");?>" class="btn btn-sm btn-primary btn-round">Add Keyword</a></div>
                      <?php } ?>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <?php if(count($keyword)>0) { ?>
          <div class="navbar-right"><button type="submit" class="btn btn-success btn-round"><i class="fa fa-save"></i> Submit</button></div>
          <?php } ?>
        </div>
      </div>
    </div>
  </form>
</div>
<input type="hidden" id="choose_keyword" name="choose_keyword" value="<?php echo $choose_keyword;?>" />
<script type="text/javascript">
$(document).ready(function() {
    $("input.flat")[0] && $(document).ready(function() {
        $("input.flat").iCheck({
            checkboxClass: "icheckbox_flat",
            radioClass: "iradio_flat"
        })
        check_choose_keyword();
    });

    $(document).delegate('#filterKeyword input.flat', 'ifChanged',function() {
      check_choose_keyword();
    });
});

function check_choose_keyword()
{
      var check_size = $("#filterKeyword input.flat").parents(".to_do").find("input[type='checkbox']:checked").size();
      var choose_keyword = parseInt($("#choose_keyword").val());
      if(check_size>=choose_keyword) {
        $("#filterKeyword input.flat").parents(".to_do").find("input[type='checkbox']:not(:checked)").attr("disabled",true);
        $("#filterKeyword input.flat").parents(".to_do").find("input[type='checkbox']:not(:checked)").parents('.icheckbox_flat').css("cursor","not-allowed");
      } else {
        $("#filterKeyword input.flat").parents(".to_do").find("input[type='checkbox']:not(:checked)").attr("disabled",false);
        $("#filterKeyword input.flat").parents(".to_do").find("input[type='checkbox']:not(:checked)").parents('.icheckbox_flat').css("cursor","pointer");
      }
}
</script>