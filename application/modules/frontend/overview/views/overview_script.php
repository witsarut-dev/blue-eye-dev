<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/highstock/highstock.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/highstock/modules/data.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/highstock/modules/drilldown.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/highstock/modules/exporting.js"></script>
<!-- <script type="text/javascript" src="<?php// echo theme_assets_url(); ?>js/highstock/themes/dark-unica.js"></script> -->
<!-- <script type="text/javascript" src="<?php// echo theme_assets_url(); ?>js/modules/overview.js?v1"></script> -->
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/modules/overview.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>

<script type="text/javascript">
var keywordData = <?php echo json_encode($keywordData); ?>;
var mediaCategories = <?php echo json_encode($mediaData["mediaCategories"]); ?>;
var mediaData = <?php echo json_encode($mediaData["mediaData"]); ?>;
var periodBefore = [];
var periodCurrent = [];
var marketPosData = <?php echo json_encode($marketPosData); ?>;
var mediaPosData = <?php echo json_encode($mediaPosData); ?>;
var PeriodType = "<?php echo $period; ?>";
var CustomTime = "<?php echo $custom_time; ?>";

var graphSentimentData = <?php echo json_encode($graphSentimentData); ?>;
//blue-eye-test
console.log(graphSentimentData);

</script>