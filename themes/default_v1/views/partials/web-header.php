    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $template['title']; ?></title>
    <base href="<?php echo base_url();?>" />
    <script type="text/javascript">
        var urlbase = "<?php echo base_url();?>";
        var urlpath = "<?php echo site_url();?>";
    </script>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo theme_assets_url(); ?>images/favicon.ico"/>
    <link href="<?php echo theme_assets_url(); ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/iCheck/skins/flat/flat.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/mmenu/jquery.mmenu.all.min.css" rel="stylesheet">
    <?php echo @$inline_style."\n"; ?>
    <link href="<?php echo theme_assets_url(); ?>css/fancybox.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>build/css/custom.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>css/custom.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>css/responsive.css" rel="stylesheet">