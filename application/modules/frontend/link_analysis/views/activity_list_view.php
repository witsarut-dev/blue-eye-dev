<div class="users-list fancybox-body">
    <div class="panel panel-default card-view">
        <div class="panel-heading">
            <div class="title_left">
                <h3><?php echo ucfirst(str_replace("post","",$msg_type));?> List</h3>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
            <div class="panel-body">
                <div class="x_panel">
                   <div class="to_do">
                        <div class="scroll-pane">
                            <div class="nw-user-list">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php echo $this->load->view("activity_list_script"); ?>