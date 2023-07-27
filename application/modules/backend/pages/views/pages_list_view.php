<?php 
	$this->load->view("template/list");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Pages","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"OFF","control_mode":"ON","add_mode":"ON","edit_mode":"ON","delete_mode":"ON","display_mode":"ON","log_mode":"OFF"};
	app_searchForm = [{"id":"page_type",
					   "label":"Type",
					   "name":"page_type",
					   "type":"select",
					   "data":[{"id":"page_type","name":"page_type","value":"Facebook","label":"Facebook"},
					   	       {"id":"page_type","name":"page_type","value":"Twitter","label":"Twitter"},
							   {"id":"page_type","name":"page_type","value":"Tiktok","label":"Tiktok"},
							   {"id":"page_type","name":"page_type","value":"Blockdit","label":"Blockdit"}
							  ],
					   "value":""}];
	app_statusForm = [];
	app_list = {
		"title" : "Pages",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "pages_id",
		"orderby" : "desc",
		"module" : "pages",
		"tbwidth" : "100%",
		"column" : [{"label":"ID or Username","name":"page_id","type":"text","sort":"","width":"250","class":"left"},{"label":"Page Username \/ TW USER ID","name":"page_name","type":"text","sort":"","width":"200","class":"left"},{"label":"Type","name":"page_type","type":"radio","sort":"","width":"150","class":"left"},{"label":"Update","name":"lastupdate","type":"datetime","sort":"","width":"150","class":"left"},{"label":"By","name":"update_name","type":"","sort":"","width":"80","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>
<?php $this->load->view("pages/list_custom"); ?>