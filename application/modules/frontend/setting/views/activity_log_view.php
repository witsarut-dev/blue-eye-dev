<div class="fancybox-body">
    <div class="panel panel-default card-view x_panel">
        <div class="panel-heading">
            <div class="pull-left"><h6 class="panel-title txt-dark">Activity Log</h6></div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
            <div class="panel-body">
                <div class="table-responsive">
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
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#tableActivityLog').DataTable({
        "order": [[ 2, "desc" ]],
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": "<?php echo site_url($module."/activity_list/");?>"
    });
});
</script>