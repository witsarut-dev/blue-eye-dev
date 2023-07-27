<!DOCTYPE html>
<html ng-app="myApp" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="themes/default/assets/images/favicon.ico"/>

    <title>Web Admin</title>
    <?php $this->load->view("template/script.php"); ?>
  </head>
  <?php 
    if(@$this->session->userdata("CLOSE")=="true") {
      echo '<body class="nav-sm">';
    } else {
      echo '<body class="nav-md">';
    }
  ?>
    <div class="container body">
      <div class="main_container">
        <?php if($USER_ID!=""): ?>
        <div class="col-md-3 left_col">
          <?php $this->load->view("template/menu.php");?>
        </div>
        <div class="top_nav">
          <?php $this->load->view("template/top.php");?>
        </div>
        <?php endif; ?>