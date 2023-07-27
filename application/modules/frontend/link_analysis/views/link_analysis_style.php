	<link href="<?php echo theme_assets_url(); ?>css/jquery.jscrollpane.css" rel="stylesheet">
    <link href="<?php echo theme_assets_url(); ?>vendors/bower_components/vis/dist/vis-network.min.css" rel="stylesheet">
    <style>
        #BoxMyLink .LinkName {
            width: 63%;
            word-break: break-word;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #BoxAddLink {
          padding: 0px 10px;
        }
        #BoxAddLink input {
            height: 34px;
            font-size:12px;
        }
        #BoxAddLink .btnSaveLink {
            position: absolute;
            right: -5px;
            z-index: 9;
        }
        #BoxAddLink  .parsley-required {
            font-size: 12px;
        }
        #BoxMyLink .jspHorizontalBar {
            display: none;
        }
        .vis-network {
            background: #212121;
        }
        div.vis-tooltip i {
            color:rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }
        div.vis-tooltip {
            padding:5px 5px;
            background:#212121;
            border:2px solid #333333;
        }
        .users-list .nav-tabs > li {
            float:left !important;
        }
        .nw-user-box {
            margin-bottom: 10px;
        }
        .nw-img-user {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .nw-ico-user {
            float: left;
            position: relative;
        }
        .nw-name-user {
            float: left;
            position: relative;
            top: 5px;
            margin-left: 10px;
            
        }
        .nw-name-user i {
            font-size: 13px;
            
        }
        .nw-name-user p {
            overflow: hidden;
            margin: 0px;
            width: 200px;
            white-space:nowrap;
            text-overflow: ellipsis;
            
        }
        .nw-zoom {
            padding: 0px;
            margin-right: 10px;
            cursor: pointer;
            display: block;
        }
        .nw-zoom:hover {
            color:#FFFFFF;
        }
        #link-network {
            overflow: auto;
            min-height: 400px;
        }
        .nw-user-list {
            visibility: hidden;
        }
        .link-list .LinkName {
            cursor: pointer;
        }
        .link-list .LinkName:hover {
            text-decoration: underline;
        }
        .input-readonly[readonly],.input-readonly[disabled] {
            background: none !important;
            cursor: not-allowed !important;
            opacity: 0.5 !important;
        }
        .link-filter {
            display: none;
            position: absolute;
            width: 130px;
            left: -65px;
            top: 30px;
            z-index: 999;
            padding: 3px;
            border: 1px solid rgba(255,255,255,.09);
            background: rgba(33, 33, 33,0.9);
        }
        .link-filter li {
            margin-bottom: 3px;
            border-bottom: 1px solid rgba(0,0,0,1);
        }
        .link-filter li:last-child {
            border-bottom: none;
        }
        .vis-network {
            max-width: 100% !important;
            height: 100 !important;
            outline: none;
        }
        #icon-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            -ms-transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            z-index: 999;
            display: none;
        }
        #icon-loading i {
            font-size: 30px;
        }
        .contextmenu  {
            display: none;
            position: absolute;
            z-index: 999;
            padding: 3px;
            border: 1px solid rgba(255,255,255,.09);
            background: rgba(33, 33, 33,0.9);
            padding: 10px 20px;
            width: 300px;
        }
        .contextmenu .row {
            margin-top: 5px;
        }
        .btn-close-context {
            position: absolute;
            right: 5px;
            top: 2px;
            cursor: pointer;
        }
        .btn-apply-context {
            text-align: center;
            cursor: pointer;
            display: block;
            margin-top: 10px;
        }
        .scroll-pane{
            height: 500px;
            overflow: hidden ;  
            padding:0 ;
            margin:0 ;
            outline: none;
        }
        
        .head-post{
            background-color:#e6e6e6;
        }
        .head-post:hover{
            background-color:rgb(200,200,200);
        }
        .link-open:hover{
            color:rgb(101, 174, 255);
        }
        .highlight {
            border: 1px solid black;
            color: rgb(13, 12, 12);
        }
        #mark-key {
            background-color:yellow;
        }
        /* Standard syntax */
        @keyframes loading {
            from {background-color: #212121;border:1px solid #212121;}
            to {background-color: #4aa23c;border:1px solid #4aa23c;}
        }
    </style>
