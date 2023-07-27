<div class="fancybox-body">
    <div class="panel panel-default card-view x_panel">
        <div class="panel-heading">
            <div class="pull-left"><h6 class="panel-title txt-dark">Block User</h6></div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
            <div class="panel-body">
                <div class="pull-right">
                    <button class="btn btn-success btn-round btn-unblock"><i class="fa fa-lock"></i> Unblock</button>
                </div>
                <div class="clearfix"></div>
                <form id="formUnblock">
                    <div class="table-responsive">
                        <table id="tableBlock" class="table table-striped jambo_table datatable-fixed-header">
                            <thead>
                                <tr>
                                    <th style="vertical-align: middle;width:30%">User</th>
                                    <th style="vertical-align: middle;width:20%">Web</th>
                                    <th style="vertical-align: middle;width:50%">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($rowsdata as $k_row=>$v_row) {
                                $sourceid = $v_row['sourceid'];
                                $media_full  = get_soruce_full($sourceid);
                                $media_short = get_soruce_short($sourceid);
                                ?>
                                <tr>
                                    <td><input name="block_id[]" value="<?php echo $v_row["block_id"];?>" type="checkbox" class="flat"> <?php echo $v_row["block_user"];?></td>
                                    <td><?php echo $media_full;?></td>
                                    <td>Block on <?php echo getDatetimeformat($v_row["block_time"]);?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
.fancybox-overlay-fixed {
  z-index: 8000;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    // $("input.flat")[0] && $(document).ready(function() {
    //     $("input.flat").iCheck({
    //         checkboxClass: "icheckbox_flat",
    //         radioClass: "iradio_flat"
    //     })
    // })
    $('#tableBlock').DataTable({"order": [[ 1, "desc" ]],"autoWidth": false})
    
    $(".btn-unblock").click(function(){
        if($("input[name^=block_id]").is(":checked")==false) {
          dialog_error("กรุณาเลือก User ที่ต้องการ Unblock");
          return false;
        } else  {
          var url  = urlbase+"setting/cmdUnblock";
          var data = $("#formUnblock").serialize();

          $.ajax({
              type : 'post',
              dataType : 'json',
              data: data,
              url: url,
              beforeSend: function() {
                  $.fancybox.showLoading();
              },
              error: function() {
                  $.fancybox.hideLoading();
                  dialog_error("Error");
              },
              success : function(res) {
                  if(res.status) {
                      window.location.reload(0);
                  } else {
                      dialog_error(res.message);
                  }
                  $.fancybox.hideLoading();
              }
          });
          return true;
        }
    });
});
</script>