<div class="left_col scroll-view">
  <div class="navbar nav_title" style="border: 0;">
     <a href="webadmin" class="site_title"><img src="themes/default/assets/images/logo-3.png" style="width: 50px;margin-top: -6px;" /><span>BlueEye Admin</span></a>
  </div>

  <div class="clearfix"></div>

  <!-- sidebar menu -->
  <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
      <ul class="nav side-menu">
        <li class="authen"><a href="<?php echo site_url("webadmin");?>"><i class="fa fa-home"></i> Home</a></li>
        <li class="module schedule"><a href="<?php echo site_url("schedule");?>"><i class="fa fa-th"></i> Monitor Schedule</a></li>
        <li class="module client"><a href="<?php echo site_url("client");?>"><i class="fa fa-id-card-o"></i> Customer</a></li>
        <li class="module menu"><a href="<?php echo site_url("menu");?>"><i class="fa fa-list"></i> Menu</a></li>
        <li class="module pages"><a href="<?php echo site_url("pages");?>"><i class="fa fa fa-clipboard"></i> Pages</a></li>
        <li class="module config"><a href="<?php echo site_url("config");?>"><i class="fa fa-cog"></i> Default Config</a></li>
<!--         <li class="module fb_users"><a href="<?php echo site_url("fb_users");?>"><i class="fa fa-facebook"></i> FB Users</a></li> -->
        <li class="parent_menu"><a><i class="fa fa-user"></i> Administrator <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
              <li class="module sys_users"><a href="<?php echo site_url("sys_users");?>">Users</a></li>
              <li class="module sys_roles"><a href="<?php echo site_url("sys_roles");?>">Roles</a></li>
              <li class="module sys_logs"><a href="<?php echo site_url("sys_logs");?>">Logs</a></li>
              <li class="module sys_users_block"><a href="<?php echo site_url("sys_users_block");?>">Users Block</a></li>
            </ul>
        </li>
      </ul>
    </div>
  </div>
  <!-- /sidebar menu -->

</div>