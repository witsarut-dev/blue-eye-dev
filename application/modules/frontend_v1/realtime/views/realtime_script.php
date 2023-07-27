<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/modules/realtime.js"></script>
<script type="text/javascript">
var MediaType = '<?php echo ($this->input->get("media_type")!="") ? $this->input->get("media_type") : "All";?>';
var Sentiment = '<?php echo ($this->input->get("Sentiment")!="") ? $this->input->get("Sentiment") : "";?>';
var GetKeyword = '<?php echo ($this->input->get("keyword")!="") ? $this->input->get("keyword") : "";?>';
var GetTime = '<?php echo ($this->input->get("time")!="") ? $this->input->get("time") : "";?>';
var PeriodType = '<?php echo ($this->input->get("period_type")!="") ? $this->input->get("period_type") : "";?>';
</script>

<script type="text/javascript">
$(document).ready(function() {
    $("input.flat")[0] && $(document).ready(function() {
        $("input.flat").iCheck({
            checkboxClass: "icheckbox_flat",
            radioClass: "iradio_flat"
        })
    });

    <?php if($this->input->get()===false) { ?>
	    setInterval(function(){ 
	        add_feed("MediaBox");
	        add_feed("WebBox");
	        add_feed("NewsBox");
	    },1000*60);
    <?php } ?>

});
</script>
 