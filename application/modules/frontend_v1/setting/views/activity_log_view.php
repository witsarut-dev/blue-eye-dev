<div class="container body fancybox-body">
  <div class="main_container">
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>Activity Log</h3>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_content">
                <div class="">
                  <table id="tableActivityLog" class="table table-striped jambo_table datatable-fixed-header">
                    <thead>
                      <tr>
                        <th style="vertical-align: middle;width:50%">Activity</th>
                        <th style="vertical-align: middle;width:20%">User</th>
                        <th style="vertical-align: middle;width:30%">Time</th>
                      </tr>
                    </thead>
                  
                  </table>
                </div>
              </div>
<!--               <div class="clearfix"></div>
              <div class="navbar-right"><button class="btn btn-danger btn-round" onclick="$.fancybox.close();"><i class="fa fa-remove"></i> Close</button></div> -->
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#tableActivityLog').DataTable({
        "order": [[ 2, "desc" ]],
        "processing": true,
        "serverSide": true,
        "ajax": "<?php echo site_url($module."/activity_list/");?>"
    });
});
</script>