<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/parsleyjs/dist/parsley.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/highcharts/highcharts.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/highcharts/highcharts-more.js"></script>
<!-- <script type="text/javascript" src="<?php// echo theme_assets_url(); ?>js/highcharts/themes/dark-unica.js"></script> -->
<script src="<?php echo theme_assets_url(); ?>js/modules/marketing.js"></script>
<script>
var categoryData = <?php echo json_encode($categoryData);?>;
var positiveData = <?php echo json_encode($positiveData);?>;
var negativeData = <?php echo json_encode($negativeData);?>;
var MediaType = '<?php echo ($this->input->get("media_type")!="") ? $this->input->get("media_type") : "";?>';
var Sentiment = '<?php echo ($this->input->get("Sentiment")!="") ? $this->input->get("Sentiment") : "";?>';
</script>