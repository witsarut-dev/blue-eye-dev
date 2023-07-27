        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                Overview Company
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li><button onclick="window.location.href='<?php echo site_url("overview");?>'" type="button" class="btn btn-round <?php echo theme_menu('Overview');?>">Overview</button></li>
                <li><button onclick="window.location.href='<?php echo site_url("realtime");?>'" type="button" class="btn btn-round <?php echo theme_menu('Realtime');?>">Realtime</button></li>
                <li><button onclick="window.location.href='<?php echo site_url("report");?>'" type="button" class="btn btn-round <?php echo theme_menu('Report');?>">Report</button></li>
                <li><button onclick="window.location.href='<?php echo site_url("marketing");?>'" type="button" class="btn btn-round <?php echo theme_menu('Marketing');?>">Marketing</button></li>
                <li><button onclick="window.location.href='<?php echo site_url("analysis");?>'" type="button" class="btn btn-round <?php echo theme_menu('Analysis');?>">Analysis</button></li>
                <li><button onclick="window.location.href='<?php echo site_url("setting");?>'" ype="button" class="btn btn-round <?php echo theme_menu('Setting');?>">Setting</button></li>
              </ul>
            </nav>
          </div>
          <ul class="nav navbar-nav navbar-profile">
            <li class="">
              <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user"></i> <?php echo $this->authen->getUsername();?> <span class=" fa fa-angle-down"></span>
              </a>
              <ul class="dropdown-menu dropdown-usermenu pull-right">
                <li><a href="<?php  echo site_url("setting/#tab_content2");?>">View Profile</a></li>
                <li><a href="<?php  echo site_url("login/cmdLogout");?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
              </ul>
            </li>
          </ul>
        </div>