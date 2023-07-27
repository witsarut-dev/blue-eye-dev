<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/modules/realtime.js"></script>
<!-- filterOther -->
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

<script type="text/javascript">
    var MediaType = '<?php echo ($this->input->get("mediaType") != "") ? $this->input->get("mediaType") : "All"; ?>';
    var OtherKeyword = '<?php echo ($this->input->get("other_keyword") != "") ? $this->input->get("other_keyword") : ""; ?>';
    var Sentiment = '<?php echo ($this->input->get("Sentiment") != "") ? $this->input->get("Sentiment") : ""; ?>';
    var GetKeyword = '<?php echo ($this->input->get("keyword") != "") ? $this->input->get("keyword") : ""; ?>';
    var GetTime = '<?php echo ($this->input->get("time") != "") ? $this->input->get("time") : ""; ?>';
    var PeriodType = '<?php echo ($this->input->get("period_type") != "") ? $this->input->get("period_type") : ""; ?>';
    var CompanyType = '<?php echo ($this->input->get("companyType") != "") ? $this->input->get("companyType") : "All"; ?>';
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $("input.flat")[0] && $(document).ready(function() {
            $("input.flat").iCheck({
                checkboxClass: "icheckbox_flat",
                radioClass: "iradio_flat"
            })
        });

        <?php if ($this->input->get() === false) { ?>
            setInterval(function() {
                if (CompanyType == "All") {
                    add_feed("NormalBox", "All");
                    add_feed("PriorityBox", "All");
                } else {
                    if (CompanyType == "client") {
                        add_feed("NormalBox", "own");
                        add_feed("PriorityBox", "own");
                    } else {
                        add_feed("NormalBox", "competitor");
                        add_feed("PriorityBox", "competitor");
                    }
                }

                // get all the tab "li" elements
                const tabs = document.querySelectorAll('#mentions-tab li');

                // loop through the tabs to find the active one
                let activeTabId = null;
                tabs.forEach(tab => {
                if (tab.classList.contains('active')) {
                    // found the active tab
                    activeTabId = tab.firstElementChild.getAttribute('href').slice(1);
                }
                });
                get_is_read();
            }, 1000 * 60);
        <?php } ?>

    });
</script>
<script>
    function edit_sentiment_realtime(event) {
        var value_new_sentiment = document.getElementById(event.target.id).value;
        var id_post = event.target.id ;

        $.ajax({
            url:urlbase+"master/realtime_update_edit_sentiment", 
            type: "post", 
            dataType: 'json',
            data: {new_sentiment_edit: value_new_sentiment , post_id: id_post },
            success:function(result){
                alert(data);
            }
        });  
    }
</script>