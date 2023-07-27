<?php 
	$this->load->view("template/list");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Config","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"OFF","control_mode":"ON","add_mode":"OFF","edit_mode":"ON","delete_mode":"OFF","display_mode":"ON","log_mode":"OFF"};
	app_searchForm = [];
	app_statusForm = [];
	app_list = {
		"title" : "Default Config",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "config_group,config_name",
		"orderby" : "asc",
		"module" : "config",
		"tbwidth" : "100%",
		"column" : [{"label":"Name","name":"config_name","type":"text","sort":"","width":"200","class":"left"},{"label":"Value","name":"config_val","type":"text","sort":"","width":"250","class":"left"},{"label":"Update","name":"lastupdate","type":"datetime","sort":"","width":"150","class":"left"},{"label":"By","name":"update_name","type":"","sort":"","width":"80","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>
<?php $this->load->view("config/list_custom"); ?>