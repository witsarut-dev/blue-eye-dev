<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Blue Eye</title>
    <link rel="shortcut icon" type="<?php echo theme_assets_url(); ?>image/x-icon" href="images/favicon.ico"/>

    <!-- Bootstrap -->
    <link href="<?php echo theme_assets_url(); ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo theme_assets_url(); ?>build/css/custom.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>css/custom.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>css/responsive.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="form login_form">
          <section class="login_content">
            <p><img src="<?php echo theme_assets_url(); ?>images/logo.jpg" class="logo" /></p>
    
            <form id="formLogin" action="<?php echo site_url("login"); ?>" method="post">
              <h1>Blue Eye Login</h1>
              <?php 
              if($this->session->userdata("USER_ID")!="") { 
                $clients = $this->login_model->get_client();
              ?>
              <div>
                <select id="client_id" name="client_id" class="form-control" required="required">
                  <option value="">Choose Company</option>
                  <?php foreach($clients as $client) { ?>
                  <option value="<?php echo $client['client_id'];?>"><?php echo $client['company_name'];?></option>
                  <?php } ?>
                </select>
              </div>
              <br />
              <?php } else { ?>
              <div>
                <input name="username" type="text" class="form-control" required="required" placeholder="Username" />
              </div>
              <div>
                <input name="password" type="password" class="form-control" required="required" placeholder="Password"  />
              </div>
              <?php } ?>
              <div>
                <?php if($this->session->userdata("USER_ID")!="") { ?>
                <i class="fa fa-user" style="float: left;margin-top: 13px !important;"></i>
                <a class="menu_admin" href="<?php echo site_url("webadmin");?>">Web Admin</a>
                <span class="menu_admin"> | </span>
                <a class="menu_admin" href="<?php echo site_url("authen/logout");?>">Logout</a>
                <a class="btn btn-default btn-round btn-admin" href="javascript:;">Log in</a>
                <?php } else { ?>
                <input name="remember" value="Y" type="checkbox" style="float: left;margin-top: 13px !important;"/>
                <a class="reset_pass" href="#"> Remember me</a>
                <a class="btn btn-default btn-round btn-login" href="javascript:;">Log in</a>
                <?php } ?>
              </div>

              <div class="clearfix"></div>

            </form>
          </section>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/parsleyjs/dist/parsley.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/html5shiv.js?3.7.0"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/respond.min.js?1.4.2"></script>
    <![endif]-->

    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/custom.js"></script>
  </body>
</html>
