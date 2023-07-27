    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- No-cache for website -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title><?php echo $template['title']; ?></title>
    <base href="<?php echo base_url();?>" />
    <script type="text/javascript">
        var urlbase = "<?php echo base_url();?>";
        var urlpath = "<?php echo site_url();?>";
    </script>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo theme_assets_url(); ?>images/favicon.ico"/>
    <?php echo @$inline_style."\n"; ?>
    <link href="<?php echo theme_assets_url(); ?>css/fancybox.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/iCheck/skins/flat/flat.css" rel="stylesheet">


    <!-- Bootstrap Wysihtml5 css -->
    <link rel="stylesheet" href="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.css" />
        
    <!-- Bootstrap Daterangepicker CSS -->
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>

    <!-- select2 CSS -->
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css"/>

    <!-- multi-select CSS -->
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/multiselect/css/multi-select.css" rel="stylesheet" type="text/css"/>
        
    <!-- Custom CSS -->
    <link href="<?php echo theme_assets_url(); ?>css/style.css?v1" rel="stylesheet" type="text/css">
    <link href="<?php echo theme_assets_url(); ?>css/custom.css?v3" rel="stylesheet">