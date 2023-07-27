<?php 
	$this->load->view("template/list_child");
?>
<script type="text/javascript">
	app_mode = {"checkbox_mode":"ON","publish_mode":"OFF","control_mode":"ON","add_mode":"OFF","edit_mode":"ON","delete_mode":"OFF","display_mode":"ON","log_mode":"OFF"};
	app_searchForm = [];
	app_list = {
		"id" : parent.document.getElementById("id").value,
		"title" : "Config Package",
		"rows" : <?php echo $rows;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "config_name",
		"orderby" : "asc",
		"module" : "client",
		"child" : "client_config",
		"tbwidth" : "100%",
		"column" :[{"label":"Name","name":"config_name","type":"text","sort":"","width":"200","class":"left"},{"label":"Value","name":"config_val","type":"text","sort":"","width":"250","class":"left"},{"label":"Update","name":"lastupdate","type":"datetime","sort":"","width":"150","class":"left"},{"label":"By","name":"update_name","type":"","sort":"","width":"80","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>
<?php $this->load->view("client/list_child_custom"); ?>