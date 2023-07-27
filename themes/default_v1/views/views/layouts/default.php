<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $template['partials']['header']; ?>
    <?php echo @$template['partials']['inline-style']; ?>
</head>
<body class="nav-md">
    <a id="hamburger" href="#topmenu"><span></span></a>
    <div id="topmenu"></div>

    <div class="container body">
      	<div class="main_container">
		<?php echo $template['partials']['menu']; ?>
	    <?php echo $template['body']; ?>
		<?php echo $template['partials']['footer']; ?>
	    </div>
    </div>
	<?php echo $template['partials']['script']; ?>
	<?php echo @$template['partials']['inline-script']; ?>
	 <div id="w_screen"></div>
</body>
</html>
