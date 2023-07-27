<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/parsleyjs/dist/parsley.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net/js/jquery.dataTables.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net/js/jszip.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net/fixedcolumns/dataTables.fixedColumns.min.js"></script>

<script src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>

<script src="<?php echo theme_assets_url(); ?>js/highcharts/highcharts.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/highcharts/modules/exporting.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/highcharts/highcharts-more.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/highcharts/export-th.js"></script>
<!-- <script src="<?php// echo theme_assets_url(); ?>js/highcharts/themes/dark-unica.js" type="text/javascript"></script> -->
<script src="<?php echo theme_assets_url(); ?>js/modules/analysis.js?v=3"></script>

<script>
    function edit_sentiment_realtime(event) {
        var value_new_sentiment = document.getElementById(event.target.id).value;
        var id_post = event.target.id ;
        var r = confirm("Confirm to Edit Sentiment? ");
        if (r == true) {
            $.ajax({
                url:urlbase+"master/realtime_update_edit_sentiment", 
                type: "post", 
                dataType: 'json',
                data: {new_sentiment_edit: value_new_sentiment , post_id: id_post },
                success:function(result){
                    alert(data);
                }
            });  
            location.reload();
        }
    }
</script>