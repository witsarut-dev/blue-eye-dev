	<link href="<?php echo theme_assets_url(); ?>css/jquery.jscrollpane.css" rel="stylesheet">
    <style>
        #BoxMyTimeline .TimelineName {
            width: 50%;
            word-break: break-word;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #BoxAddTimeline {
          padding: 0px 10px;
        }
        #BoxAddTimeline input {
            height: 34px;
            font-size:12px;
        }
        #BoxAddTimeline .btnSaveTimeline {
            position: absolute;
            right: -5px;
            z-index: 9;
        }
        #BoxAddTimeline .parsley-required {
            font-size: 12px;
        }
        #BoxMyTimeline .jspHorizontalBar {
            display: none;
        }
        .timeline-list .TimelineName {
            cursor: pointer;
        }
        .timeline-list .TimelineName:hover {
            text-decoration: underline;
        }
        .input-readonly[readonly],.input-readonly[disabled] {
            background: none !important;
            cursor: not-allowed !important;
            opacity: 0.5 !important;
        }
        #timeline_date {
            display: inline-block;
            width: 80%
        }
        #timeline-box {
            min-height: 400px;
        }
        .timeline:before {
            top:15px !important;
            /* bottom:80px !important; */
        }
        .timeline-link {
            position: absolute;
            right: 20px;
            top: 20px;
        }
        .timeline-name {
            position: relative;
            top: -5px;
            margin-left: 10px;
        }
        .timeline {
            padding: 0px !important;
        }
        .timeline-feed-list .timeline {
            visibility: hidden;
        }
        .timeline-end {
            /* border-right: 5px solid #212121; */
            border-right: 5px rgb(0 0 0 / 46%);
            position: relative;
            right: -3px;
            margin-bottom: 0px !important;
        }
        .timeline-inverted.timeline-end {
            border-left: 5px solid #212121;
            position: relative;
            left: -4px;
        }
        .timeline > li.timeline-inverted {
            margin-top: 80px !important;
        }
        .icon-timeline-date {
            width: 50px;
            height: 42px;
            display: inline-block;
            border: 1px solid #FFF;
            border-color: rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
            top: 1px;
            border-right: 0px;
            margin-right: -3px;
            padding: 10px 12px;
        }
        .timeline > li {
            /* margin-bottom: 80px !important; */
        }
        .timeline li .timeline-content {
            min-height:42px;
            word-break: break-all;
        }
        #icon-loading,#msg-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            -ms-transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            z-index: 999;
            display: none;
        }
        #icon-loading {
            top: 40%;
        }
        #msg-loading {
            top: 60%;
            text-align: center;
        }
        #icon-loading i {
            font-size: 30px;
        }
        .show-all {
            padding: 0px;
            margin-right: 10px;
            cursor: pointer;
            display: block;
        }
        .show-all:hover {
            color:#FFFFFF;
        }
        .top_search .tooltip-inner {
            white-space:nowrap;
            max-width:none;
        }
    </style>
