<div class="timeline-feed-list fancybox-body">
    <div class="panel panel-default card-view">
        <div class="panel-heading">
            <div class="title_left">
                <h3>Timeline <?php echo $msg_date; ?></h3>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
            <div class="panel-body">
                <div class="x_panel">
                   <div class="to_do">
                        <div class="scroll-pane">
                            <ul class="timeline"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php echo $this->load->view("timeline_list_script"); ?>