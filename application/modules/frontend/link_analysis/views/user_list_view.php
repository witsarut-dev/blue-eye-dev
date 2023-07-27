<div class="users-list fancybox-body">
    <div class="panel panel-default card-view">
        <div class="panel-heading">
            <div class="title_left">
                <h3>User List</h3>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
            <div class="panel-body">
                <div class="x_panel">
                    <div class="tab-struct custom-tab-1">
                        <ul role="tablist" class="nav nav-tabs">
                            <?php 
                            $rows = 0;
                            foreach($users as $key=>$val) { 
                                $icon =   $this->linkapi->get_link_icon_name($key);
                                $active = ($key==$msg_type) ? 'active' : '';
                                $count = number_format(count($val));
                                $rows++;
                            ?>
                            <li class="<?php echo $active;?>"><a data-toggle="tab" role="tab"  href="#tab-<?php echo $key;?>"><i class="<?php echo $icon;?>"> <?php echo $count;?></i></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <br />
                    <div class="tab-content">
                        <?php 
                        $rows = 0;
                        foreach($users as $key=>$val) { 
                            $active = ($key==$msg_type) ? 'active' : '';
                            $icon =   $this->linkapi->get_link_icon_name($key);
                            $rows++;
                        ?>
                        <div id="tab-<?php echo $key?>" class="tab-pane fade <?php echo $active;?> in" role="tabpanel">
                            <div class="to_do">
                                <div class="scroll-pane">
                                    <div class="nw-user-list">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php echo $this->load->view("user_list_script"); ?>