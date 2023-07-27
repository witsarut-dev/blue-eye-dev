<?php 
	$this->load->view("template/list");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Users Block","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"OFF","control_mode":"ON","add_mode":"OFF","edit_mode":"OFF","delete_mode":"ON","display_mode":"ON","log_mode":"OFF"};
	app_searchForm = [];
	app_statusForm = [];
	app_list = {
		"title" : "Users Block",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "sys_users_block_id",
		"orderby" : "desc",
		"module" : "sys_users_block",
		"tbwidth" : "100%",
		"column" : [{"label":"Username","name":"username","type":"text","sort":"","width":"400","class":"left"},{"label":"Block Type","name":"block_type","type":"select","sort":"","width":"80","class":"left"},{"label":"Block Time","name":"block_time","type":"datetime","sort":"","width":"150","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>
<?php  ?>