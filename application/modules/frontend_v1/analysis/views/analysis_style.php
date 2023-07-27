<!-- Datatables -->
<link href="<?php echo theme_assets_url(); ?>vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
<style>
	.graph-list.active {font-weight: bold;}
	.graph-list .GraphName:hover {cursor: pointer;text-decoration: underline;}
	.choose-remark {text-align: center;display: none}
	.drag-drop {
		text-align: right;
		color: #ccc;
		padding: 3px 10px 3px 10px;
		border-top: 1px solid #eee;
		font-size: 11px;
	}
	.btn.btn-app {
		min-width: 30% !important;
	}
	#container_y2 .btn-default {
		background-color: #dff0d8 !important;
    	border-color: #d6e9c6 !important;
	}
	#container_x2 .btn-default {
		background-color: #d9edf7 !important;
    	border-color: #bce8f1 !important;
	}
	#container_y1 .btn-default,
	#container_y2 .btn-default,
	#container_x1 .btn-default,
	#container_x2 .btn-default {
		font-size: 12px;
		padding: 6px 8px;
	}
	#tableGraph td:nth-child(2),
	#tableGraph th:nth-child(2),
	#tableGraph td:nth-child(3),
	#tableGraph th:nth-child(3),
	#tableGraph td:nth-child(7),
	#tableGraph th:nth-child(7),
	#tableGraph td:nth-child(8),
	#tableGraph th:nth-child(8)
	{
	    text-align : center;
	    white-space: nowrap;
	}
	#tableGraph td {
		word-break: break-all;
	}
	.btn-export {
		position: absolute;
    	right: 0px;
    	z-index: 9999;
	}
	#
 </style>