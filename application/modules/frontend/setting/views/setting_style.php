<!-- Datatables -->
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/flags/flags.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" type="text/css">
<style type="text/css">
    #alert-error {
        z-index: 9999;
    }
    .link-setting:hover {
        opacity: 0.5;
    }
    #select-InEx {
        width: -webkit-fill-available;
        overflow: auto;
        padding: 0;
        background: none;
        border: none;
        border-radius: 0;
        outline: none; 
        -moz-appearance: none;
        appearance: none;
    }
    #detal ul {
        list-style: inside;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        }

        .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
        }

        .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
        }

        .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        }

        input:checked + .slider {
        background-color: #2196F3;
        }

        input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
        border-radius: 34px;
        }

        .slider.round:before {
        border-radius: 50%;
        }
</style>