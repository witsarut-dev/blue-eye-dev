<?php $this->load->view("template/header.php"); ?>
<?php if($USER_ID!=""): ?>
<!-- page content -->
<div class="right_col" role="main">
  <div class="">
      <div class="page-title">
        <div class="title_left">
          <?php $this->load->view("template/breadcrumb.php"); ?>
        </div>
      </div>

      <div class="clearfix"></div>

      <div class="row">
          <div class="col-md-12">
        <div class="jumbotron">
          <h1><span class="glyphicon glyphicon-phone"></span> Welcome to</h1>
          <p>BlueEye Admin Management System.</p>
          <p>Please select the menu in list on the left side for operation in each module. </p>
          <p><a href="authen/logout" class="btn btn-dark btn-lg" role="button">Log Out</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="module" id="module" value="authen" />
<?php else: ?>
<div class="login_wrapper">
  <div class="animate form login_form x_panel">
    <section class="login_content">
      <form id="formLogin" action="authen/login" method="post" class="form-horizontal">
        <h5 class="heading">RESTRICTED AREA - BlueEye</h5>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">
              <span class="sr-only">40% Complete</span>
            </div>
          </div>
          <h2><img src="themes/admin/assets/images/lock.png"> Please sign in</h2>
        <div class="form-group"><div class="col-md-12 text-danger"><?php echo $this->session->flashdata('error_login'); ?></div></div>
        <div class="form-group">
          <div class="col-md-3 control-label">Username</div>
          <div class="col-md-9"><input id="username" name="username" type="text" class="form-control" placeholder="Username" required="" /></div>
        </div>
        <div class="form-group">
          <div class="col-md-3 control-label">Password</div>
          <div class="col-md-9"><input id="password" name="password" type="password" class="form-control" placeholder="Password" required="" /></div>
        </div>
        <div class="form-group">
          <div class="col-md-3"></div>
          <div class="col-md-9" style="text-align:left"><button class="btn btn-dark" type="submit">Log in</button></div>
        </div>
        <input type="hidden" name="access_token" value="<?php echo $access_token;?>" />
      </form>
    </section>
  </div>
</div>
<?php endif; ?>
<?php $this->load->view("template/footer.php"); ?>
<script type="text/javascript">

$(function(){
  $("#username,#password").prop("disabled",true).css("background-color","#FFF");
  setTimeout(function(){
    $("#username,#password").val("");
    $("#username,#password").prop("disabled",false);
  },1000);
});

$(function() {
    $('#formLogin').bootstrapValidator({
        fields: {
            username: {
                validators: {
                    notEmpty: {
                        message: 'The value is required and cannot be empty'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'The value is required and cannot be empty'
                    }
                }
            }
        }
    });
});
</script>