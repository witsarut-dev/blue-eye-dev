<?php $this->load->view("template/list"); ?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Roles","link":"","active":"true"}];
	app_mode = {
		"checkbox_mode" : "ON",
		"publish_mode" : "ON",
		"control_mode" : "ON",
		"add_mode" : "ON",
		"edit_mode" : "ON",
		"delete_mode" : "ON",
		"display_mode" : "ON",
		"log_mode" : "ON"
	};
	app_searchForm = [];
	app_list = {
		"title" : "Roles",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "sys_roles_id",
		"orderby" : "desc",
		"module" : "sys_roles",
		"tbwidth" : "100%",
		"column" :[{"label":"Name","name":"roles_name","type":"text","sort":"T","width":"150","class":"left"},{"label":"Description","name":"roles_detail","type":"textarea","sort":"F","width":"250","class":"left"},{"label":"Last Upate","name":"lastupdate","type":"datetime","sort":"T","width":"150","class":"left"},{"label":"By","name":"update_name","type":"text","sort":"T","width":"80","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>