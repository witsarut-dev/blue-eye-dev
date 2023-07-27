        <nav class="navbar navbar-inverse navbar-fixed-top">
                <div class="mobile-only-brand pull-left">
                    <div class="nav-header pull-left">
                        <div class="logo-wrap">
                            <a href="./">
                                <img class="brand-img" src="<?php echo theme_assets_url(); ?>images/logo-3.png" width="40" height="auto" alt="brand"/>
                                <span class="brand-text">
                                    BlueEye
                                </span>
                            </a>
                        </div>
                    </div>  
                    <a id="toggle_nav_btn" class="toggle-left-nav-btn inline-block ml-20 pull-left" href="javascript:void(0);"><i class="zmdi zmdi-menu"></i></a>
                    <a id="toggle_mobile_search" data-toggle="collapse" data-target="#search_form" class="mobile-only-view" href="javascript:void(0);"><i class="zmdi zmdi-search"></i></a>
                    <a id="toggle_mobile_nav" class="mobile-only-view" href="javascript:void(0);"><i class="zmdi zmdi-more"></i></a>
                    <form id="search_form" action="<?php echo site_url("realtime");?>/" method="get" role="search" class="top-nav-search collapse pull-left">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control" placeholder="Enter Search">
                            <span class="input-group-btn">
                            <button type="button" class="btn  btn-default"  data-target="#search_form" data-toggle="collapse" aria-label="Close" aria-expanded="true" style="padding: 0 !important;"><i class="zmdi zmdi-search"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
                <div id="mobile_only_nav" class="mobile-only-nav pull-right">
                    <ul class="nav navbar-right top-nav pull-right">
                        <li class="dropdown auth-drp">
                            <a href="#" class="dropdown-toggle pr-0" data-toggle="dropdown">
                                <?php echo $this->authen->getName();?>&nbsp;&nbsp;<div class="pull-right"><span class=" fa fa-angle-down"></span></div>
                            </a>
                            <ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
                                <li>
                                    <a href="<?php  echo site_url("setting?tab=tab_content2");?>"><i class="zmdi zmdi-account"></i><span>Profile</span></a>
                                </li>
                                <li>
                                    <a href="<?php  echo site_url("setting?tab=tab_content1");?>"><i class="zmdi zmdi-settings"></i><span>Settings</span></a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php  echo site_url("login/cmdLogout");?>"><i class="zmdi zmdi-power"></i><span>Log Out</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>  
        </nav>

        <div class="fixed-sidebar-left">
            <ul class="nav navbar-nav side-nav nicescroll-bar">
                <li class="navigation-header">
                    <span>BE MAIN</span> 
                    <i class="zmdi zmdi-more"></i>
                </li>
                <?php
                    $html = "";
                    if(isset($menu[0])) {
                        foreach($menu[0] as $key=>$val) { 

                            $idx1 = $val['menu_id'];
                            $subid = "submenu_".$idx1;

                            $icon = ($val['menu_icon']!="") ? '<i class="'.$val['menu_icon'].'"></i> ' : '';
                            $link  = '<a href="'.$val['link'].'" class="'.theme_menu($val['menu_link']).'" target="'.$val['link_target'].'" data-toggle="collapse" data-target="#'.$subid.'">';
                            $link .= '<div class="pull-left">'.$icon.'<span class="right-nav-text">'.$val['menu_name'].'</span></div>';
                            if(isset($menu[$idx1])) $link .= '<div class="pull-right"><i class="zmdi zmdi-caret-down"></i></div>';
                            $link .= '<div class="clearfix"></div></a>';

                            $html .= '<li>'.$link;
                            $html1 = "";
                            if(isset($menu[$idx1])) {
                                $html1 .= '<ul id="'.$subid.'" class="collapse collapse-level-1 two-col-list submenu">';
                                foreach($menu[$idx1] as $key1=>$val1) { 
                                    $icon1 = ($val1['menu_icon']!="") ? '<i class="'.$val1['menu_icon'].'"></i> ' : '';
                                    $link1 = '<a href="'.$val1['link'].'" class="'.theme_menu($val1['menu_link'],'-page').'" target="'.$val1['link_target'].'">'.$icon1.$val1['menu_name'].'</a>';
                                    $html1 .= '<li>'.$link1;
                                }
                                $html1 .= '</ul></li>';
                            }
                            $html .= $html1."</li>";
     
                        } 
                    } 
                    echo $html;
                ?>
            </ul>
        </div>