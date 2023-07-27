<?php 
	$this->load->view("template/list_order");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Menu","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"ON","control_mode":"ON","add_mode":"ON","edit_mode":"ON","delete_mode":"ON","display_mode":"ON","log_mode":"ON","order_mode":"ON"};
	app_searchForm = [];
	app_statusForm = [];
	app_list = {
		"title" : "Menu",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "parent_order",
		"orderby" : "asc",
		"module" : "menu",
		"tbwidth" : "100%",
		"column" : [{"label":"Name","name":"menu_name","type":"text","sort":"","width":"400","class":"left"},{"label":"Update","name":"lastupdate","type":"datetime","sort":"","width":"150","class":"left"},{"label":"By","name":"update_name","type":"","sort":"","width":"80","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>
<?php $this->load->view("menu/list_custom"); ?>