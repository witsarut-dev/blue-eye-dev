    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery-ui.min.js" type="text/javascript"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/html5shiv.js?3.7.0"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/respond.min.js?1.4.2"></script>
    <![endif]-->

    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script type="text/javascript" type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/iCheck/icheck.min.js"></script>

    
    <!-- wysuhtml5 Plugin JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/wysihtml5x/dist/wysihtml5x.min.js"></script>
    
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.js"></script>
    
    <!-- Fancy Dropdown JS -->
    <script src="<?php echo theme_assets_url(); ?>js/dropdown-bootstrap-extended.js"></script>
    
    <!-- Bootstrap Wysuhtml5 Init JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>js/bootstrap-wysuhtml5-data.js"></script>
    
    <!-- Slimscroll JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>js/jquery.slimscroll.js"></script>

        <!-- Progressbar Animation JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/jquery.counterup/jquery.counterup.min.js"></script>
    
    <!-- Owl JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>
    
    <!-- Switchery JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/switchery/dist/switchery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/select2/dist/js/select2.full.min.js"></script>

    <!-- Multiselect JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>vendors/bower_components/multiselect/js/jquery.multi-select.js"></script>
    
    <!-- Init JavaScript -->
    <script src="<?php echo theme_assets_url(); ?>js/init.js"></script>

    <?php echo @$inline_script."\n"; ?>
    <script type="text/javascript" type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/fancybox.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/moment/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>build/js/custom.min.js"></script>
    <script type="text/javascript">
        var _keywords = <?php echo json_encode($keywords); ?>;
        $(function(){
            $("#search_form input[name=keyword]").autocomplete({source: _keywords});
        });
    </script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/custom.js?v3"></script>
    <script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/chart.js"></script>