	<link href="<?php echo theme_assets_url(); ?>css/jquery.jscrollpane.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/vis/dist/vis-network.min.css" rel="stylesheet">
    <style>
        #BoxMyPost .PostName {
            width: 63%;
            word-break: break-word;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #BoxAddPost {
          padding: 0px 10px;
        }
        #BoxAddPost input {
            height: 34px;
            font-size:12px;
        }
        #BoxAddPost .btnSavePost {
            position: absolute;
            right: -5px;
            z-index: 9;
        }
        #BoxAddPost .parsley-required {
            font-size: 12px;
        }
        #BoxMyPost .jspHorizontalBar {
            display: none;
        }
        .post-list .PostName {
            cursor: pointer;
        }
        .post-list .PostName:hover {
            text-decoration: underline;
        }
        #post_url[readonly],#post_url[disabled] {
            background: none;
            cursor: not-allowed;
            opacity: 0.5;
        }
        .line-post {
            display: none;
        }
        .highcharts-range-selector-buttons {
            display: block !important;
        }
        .highcharts-legend-item .highcharts-point,
        .highcharts-legend-item .highcharts-graph {
          display: none 
        }
        #mark-likes,#mark-shares,#mark-comments {
            display: none;
        }
        .MarkName {
            font-size: 13px;
        }
        .highcharts-subtitle {
            text-transform: unset !important;
        }
        #all-chart {
            visibility: hidden;
            height: 0px;
        }
    </style>
