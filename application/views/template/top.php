<div class="nav_menu">
  <nav>
    <div class="nav toggle">
      <a id="menu_toggle"><i class="fa fa-bars"></i></a>
    </div>
    <ul class="nav navbar-nav navbar-right">
      <li class="">
        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <span class="fa fa-user"></span> Welcome <span><?php echo $USERNAME; ?></span>
          <span class=" fa fa-angle-down"></span>
        </a>
        <ul class="dropdown-menu dropdown-usermenu pull-right">
          <li><a href="<?php echo site_url("sys_users/formUpdate");?>">Edit Profile</a></li>
          <li><a href="<?php echo site_url();?>"><i class="fa fa-user-circle-o pull-right"></i> Change to client</a></li>
          <li><a href="<?php echo site_url("authen/logout");?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
        </ul>
      </li>
    </ul>
  </nav>
</div>

