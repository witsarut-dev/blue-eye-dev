<!-- Datatables -->
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net/fixedcolumns/fixedColumns.dataTables.min.css" rel="stylesheet">
<link href="<?php echo theme_assets_url(); ?>css/jquery.jscrollpane.css" rel="stylesheet">
<style>
	.graph-list.active {font-weight: bold;}
	.graph-list .GraphName:hover {cursor: pointer;text-decoration: underline;}
	.choose-remark {text-align: center;display: none}
	.drag-drop {
		text-align: right;
		color: #ccc;
		padding: 3px 10px 3px 10px;
		border-top: 1px solid rgba(255,255,255,0.1);
        font-size: 11px;
        margin-left: -15px;
        margin-right: -15px;
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
	#ChartTable {
		visibility: hidden;
		height:0px;
	}

	#tableGraph td:nth-child(2),
	#tableGraph th:nth-child(2),
	#tableGraph td:nth-child(3),
	#tableGraph th:nth-child(3),
	#tableGraph td:nth-child(6),
	#tableGraph th:nth-child(6),
	#tableGraph td:nth-child(7),
	#tableGraph th:nth-child(7),
	#tableGraph td:nth-child(8),
	#tableGraph th:nth-child(8)
	{
		text-align : center;
	}

	#tableGraph td:nth-child(4),
	#tableGraph th:nth-child(4)
	{
		text-transform: capitalize;
	}

	#tableGraph tr:nth-child(even) {background-color: #eeeeee;}
	#tableGraph tr:nth-child(odd) {background-color: #ffffff;}

	#tableGraph {
		white-space: nowrap;
		overflow-x: auto;
		border-collapse: collapse;
	}

	#tableGraph th,
	#tableGraph td {
		/* padding: 10px; */
		text-overflow: ellipsis;
		overflow: hidden;
	}

	#tableGraph td:nth-child(5),
	#tableGraph th:nth-child(5)
	{
		max-width: 300px;
	}

	#tableGraph td:nth-child(4),
	#tableGraph th:nth-child(4),
	#tableGraph td:nth-child(9),
	#tableGraph th:nth-child(9)
	{
		max-width: 150px;
	}

	#tableGraph td:nth-child(10),
	#tableGraph th:nth-child(10)
	{
		max-width: 90px;
	}

	.btn-export {
		position: relative;
        right: 0px;
        float: right;
	}
	#tableGraph_wrapper table.dataTable {
		margin-top: 0px !important;
    	margin-bottom: 0px !important;
	}
	#tableGraph_wrapper table.dataTable tr th {
		border: 1px solid black;
	}
	#tableGraph_wrapper table.dataTable tr td {
		border-bottom: none !important;
	}
	.DTFC_LeftBodyWrapper {
		margin-top: -1px;
	}
	.DTFC_LeftBodyWrapper tr.odd {
		background-color: rgb(44, 44, 44) !important;
	}
	.DTFC_LeftBodyWrapper tr.even {
		background-color: rgb(33, 33, 33) !important;
	}
	.DTFC_LeftHeadWrapper tr th  {
		border-right: 1px solid #2f79c0 !important;
		text-align: center !important;
	}
	.DTFC_LeftBodyWrapper tr td {
		border-right: 1px solid #383838 !important;
		text-align: center !important;
	}
	.dataTables_scrollHead,.dataTables_scrollBody {
		margin-left: 1px;
	}
	.uncheck-all {
		display: none;
		cursor: pointer;
		font-size:13px;
	}
	.uncheck-all:hover {
		text-decoration: underline;
	}
	#ChartTable .table-responsive {
		overflow-x: hidden;
	}
	.btn-full-screen {
		display: none;
	}
	.select-checkbox {
		position: absolute;
		width: 100%;
		height: 100%;
		display: block;
		top: 0px;
		left: 0px;
	}
	table td,table th {
		position: relative;
	}
	.dropdown-menu-2 {
		left:-69px !important;
	}
 </style>