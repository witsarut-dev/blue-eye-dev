<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/tagcanvas.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/modules/report.js"></script>
<script type="text/javascript">
    var wordData = <?php echo json_encode($wordData); ?>;
    var Sentiment = '<?php echo ($this->input->get("Sentiment")!="") ? $this->input->get("Sentiment") : "";?>';
</script>