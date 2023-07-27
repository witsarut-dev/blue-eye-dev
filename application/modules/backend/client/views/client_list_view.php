<?php 
	$this->load->view("template/list");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Customer","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"ON","control_mode":"ON","add_mode":"ON","edit_mode":"ON","delete_mode":"ON","display_mode":"ON","log_mode":"ON"};
	app_searchForm = [];
	app_statusForm = [];
	app_list = {
		"title" : "Customer",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "client_id",
		"orderby" : "desc",
		"module" : "client",
		"tbwidth" : "100%",
		"column" : [{"label":"Company Name","name":"company_name","type":"text","sort":"","width":"140","class":"left"},{"label":"Username","name":"username","type":"text","sort":"","width":"80","class":"left"},{"label":"Cus. Group","name":"client_group","type":"select","sort":"","width":"80","class":"left"},{"label":"Start Join","name":"start_join","type":"date","sort":"","width":"50","class":"center"},{"label":"End Join","name":"end_join","type":"date","sort":"","width":"50","class":"center"},{"label":"Set Allow","name":"setting_allow","type":"checkbox","sort":"","width":"60","class":"center"},{"label":"Status","name":"","type":"","sort":"","width":"30","class":"center"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>
<?php $this->load->view("client/list_custom"); ?>