<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $template['partials']['header']; ?>
    <?php echo @$template['partials']['inline-style']; ?>
</head>
<body>
    <div class="preloader-it">
        <div class="la-anim-1"></div>
    </div>
    <div  class="loading-main">
        <div class="loading-overlay">
            <p class="loading-spinner">
                <span class="loading-icon"></span>
                <span class="loading-text">loading</span>
            </p>
        </div>
    </div>
    <div class="wrapper theme-1-active pimary-color-blue">
		<?php echo $template['partials']['menu']; ?>
        <div class="page-wrapper">
            <div class="row heading-bg">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                  <h3 class="txt-dark"><?php echo theme_title(@$module); ?></h3>
                </div>
                <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                  <ol class="breadcrumb">
                    <li><a href="./">Home</a></li>
                    <li class="active"><span><?php echo theme_title(@$module); ?></span></li>
                  </ol>
                </div>
            </div>
    	    <?php echo $template['body']; ?>
    		<?php echo $template['partials']['footer']; ?>
        </div>
    </div>
	<?php echo $template['partials']['script']; ?>
	<?php echo @$template['partials']['inline-script']; ?>
    <div id="w_screen"></div>
</body>
</html>
