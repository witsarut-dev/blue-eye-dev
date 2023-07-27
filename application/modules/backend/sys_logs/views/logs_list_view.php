<?php $this->load->view("template/list"); ?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Logs","link":"","active":"true"}];
	app_mode = {
		"checkbox_mode" : "OFF",
		"publish_mode" : "OFF",
		"control_mode" : "OFF",
		"add_mode" : "OFF",
		"edit_mode" : "OFF",
		"delete_mode" : "OFF",
		"display_mode" : "OFF",
		"log_mode" : "OFF"
	};
	app_searchForm = [];
	app_list = {
		"title" : "Logs",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : 'lastupdate',
		"orderby" : 'desc',
		"module" : "sys_logs",
		"tbwidth" : "100%",
		"column" : [{"label":"Action","name":"action","type":"text","sort":"T","width":"100","class":"left"},{"label":"Date&Time","name":"lastupdate","type":"datetime","sort":"T","width":"150","class":"center"},{"label":"Module","name":"module","type":"text","sort":"T","width":"150","class":"left"},{"label":"Message","name":"message","type":"text","sort":"F","width":"350","class":"left"},{"label":"By","name":"update_name","type":"text","sort":"T","width":"100","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>