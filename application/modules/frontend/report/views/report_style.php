    <!-- Datatables -->
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>css/jquery.jscrollpane.css" rel="stylesheet">
    <style>
        .choose-remark {
            text-align: center;
        }

        #TableTopShare td {
            word-break: break-word;
        }

        .box-keyword {
            display: none;
        }

        .box-keyword .label {
            font-size: 13px !important;
            top: 5px;
            position: relative;
            text-transform: none;
        }

        .box-keyword .delete-keyword {
            cursor: pointer;
        }

        .icon-container .fa-spinner {
            position: absolute;
            top: 50%;
            left: 48%;
            -ms-transform: translate(-48%, -50%);
            -webkit-transform: translate(-48%, -50%);
            transform: translate(-48%, -48%);
            z-index: 999;
        }

        #TopUser .panel-body {
            min-height: 319px;
            max-height: 319px;
            overflow-y: auto;
        }

        #TopShare .panel-body {
            min-height: 319px;
            max-height: 319px;
            overflow-y: auto;
        }

        .module__list-item {
            width: 49%;
            margin-bottom: 1rem;
        }

        .switcher__boxed {
            width: 100%;
            height: 37px;
            padding: 0.5rem 1.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .switcher {
            display: flex;
            position: relative;
            flex-flow: row;
            width: 100%;
        }

        .switcher input[type="checkbox"] {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            display: none;
        }

        .switcher input+label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .switcher input+label .switcher__toggle--icon {
            order: 1;
        }

        .switcher input+label .switcher__toggle {
            position: relative;
            display: inline-block;
            height: 15px;
            width: 35px;
            border-radius: 15px;
            background-color: #DEDEDE;
            border: 1px solid #AAB4C0;
            cursor: pointer;
            transition: background-color 250ms ease-in-out;
        }

        .switcher input+label .switcher__label {
            font-size: 0.8rem;
            line-height: normal;
        }

        .switcher input+label .switcher__toggle:before {
            content: '';
            position: absolute;
            left: -1px;
            top: -1px;
            height: 15px;
            width: 15px;
            border-radius: 15px;
            border: 1px solid #AAB4C0;
            background-color: #fff;
            cursor: pointer;
            transition: left 250ms ease-in-out;
        }

        .switcher input:checked+label .switcher__toggle:before {
            left: 20px;
        }

        .switcher input:checked+label .switcher__toggle {
            background-color: #0FB36C;
        }

        .module__list {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            width: 100%;
        }

        .toggle {
            cursor: pointer;
            display: inline-block;
        }

        .toggle-switch {
            display: inline-block;
            background: #ccc;
            border-radius: 16px;
            width: 50px;
            height: 24px;
            position: relative;
            vertical-align: middle;
            transition: background 0.25s;
        }

        .toggle-switch:before,
        .toggle-switch:after {
            content: "";
        }

        .toggle-switch:before {
            display: block;
            background: linear-gradient(to bottom, #fff 0%, #eee 100%);
            border-radius: 50%;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.25);
            width: 16px;
            height: 16px;
            position: absolute;
            top: 4px;
            left: 4px;
            transition: left 0.25s;
        }

        .toggle:hover .toggle-switch:before {
            background: linear-gradient(to bottom, #fff 0%, #fff 100%);
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.5);
        }

        .toggle-checkbox:checked+.toggle-switch {
            background: #56c080;
        }

        .toggle-checkbox:checked+.toggle-switch:before {
            left: 30px;
        }

        .toggle-checkbox {
            position: absolute;
            visibility: hidden;
        }

        .toggle-label {
            margin-left: 5px;
            position: relative;
            top: 2px;
        }

        .content-icon-sizing {
            width: 15px;
            height: 15px;
        }

        .td-text-bold {
            color: #8e8e8e;
            font-size: 11px;
        }

        .topic-icon-sizing {
            width: 17px;
            height: 17px;
            padding-bottom: -3px;
        }

        .content-icon-sizing {
            width: 15px;
            height: 15px;
        }

        .summary-text {
            text-transform: none;
            font-size: 11px;
        }

        .summary-detail {
            display: flex;
            padding-left: 10px;
            align-items: center;
        }

        .summary-detail__title {
            font-size: 0.7em;
            font-weight: 500;
            text-transform: uppercase;
            color: #9f8484;
        }

        .summary-detail__value {
            font-size: 1em;
            font-weight: 500;
            color: #000000;
        }

        .blue_icostyle {
            color: #2B83D4;
            font-size: 1.3em;
        }

        .report-heading-style {
            margin: 0 auto 0 0.4rem;
            font-size: 1rem;
            color: #2D3438;
            vertical-align: middle;
            text-transform: none;
        }

        tr.row-space>td {
            padding-bottom: 2em;
        }

        .summary-content-table {
            width: 100%;
        }

        .border {
            /* border: 1px solid #000000; */
            border-collapse: collapse;
        }
        .border-solid thead > tr > th {
            border-bottom: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }
        .border-solid tbody > tr > td {
            /* border-left: 1px solid #000000;
            border-right: 1px solid #000000; */
            text-align: center;
            vertical-align: middle;
        }
        .border-solid tbody:last-child {
            /* border-bottom: 1px solid #000000; */
        }
        .rows tr:nth-child(even) {background-color: #eeeeee}
        .rows tr:nth-child(odd) {background-color: #ffffff}

    </style>