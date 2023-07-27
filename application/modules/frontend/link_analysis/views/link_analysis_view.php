<div class="container-fluid">
    <ul id="myTab" class="nav nav-pills nav-pills-rounded" role="tablist">
        <?php
            if(isset($link_type) && in_array($link_type,array("user","fanpage"))) {
                $active[$link_type] = 'active';
            } else {
                $link_type = "page";
                $active["page"] = 'active';
            }
        ?>
        <!-- <li role="presentation" class="<?php echo @$active['page'];?>"><a href="<?php echo site_url("link_analysis?link_type=page");?>" id="page-tab" role="tab"><i class="fa fa-paper-plane"></i> Page Post</a></li>
        <li role="presentation" class="<?php echo @$active['fanpage'];?>"><a href="<?php echo site_url("link_analysis?link_type=fanpage");?>" id="user-tab" role="tab"><i class="fa fa-file"></i> Page</a></li> -->
        <li role="presentation" class="<?php echo @$active['user'];?>"><a href="<?php echo site_url("link_analysis?link_type=user");?>" id="user-tab" role="tab"><i class="fa fa-user"></i> User</a></li>
    </ul>
    <br />
    <?php echo $this->load->view("link_analysis/link_tab_".$link_type);  ?>
</div>
<form action="" id="formOpenLink" target="_blank"></form>