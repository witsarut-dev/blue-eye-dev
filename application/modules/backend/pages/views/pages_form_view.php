<?php 
	$this->load->view("template/form"); 
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Pages","link":"pages","active":"false"},{"label":"Update data","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"OFF","control_mode":"ON","add_mode":"ON","edit_mode":"ON","delete_mode":"ON","display_mode":"ON","log_mode":"OFF","action":"<?php echo @$action;?>"};
	app_tabForm = [{"id":"general",
		            "label":"General",
					"form":[{"groups":"Pages Detail",
						     "id":"page_id",
							 "label":"ID \/ Username",
							 "name":"page_id",
							 "type":"text",
							 "validate":"notEmpty"},
							// {"groups":"Pages Detail",
							//  "id":"page_name",
							//  "label":"Username \/ TW USER ID",
							//  "name":"page_name",
							//  "type":"text"},
							{"groups":"Pages Detail",
							 "id":"page_type",
							 "label":"Type",
							 "name":"page_type",
							 "type":"radio",
							 "validate":"notEmpty",
							 "data":[{"id":"page_type","name":"page_type","value":"Facebook","label":"Facebook"},
									 {"id":"page_type","name":"page_type","value":"Twitter","label":"Twitter"},
									 {"id":"page_type","name":"page_type","value":"Tiktok","label":"Tiktok"},
									 {"id":"page_type","name":"page_type","value":"Blockdit","label":"Blockdit"}
									]}]}];
	app_list = {
		"id" : "<?php echo @$id;?>",
		"title" : "Pages",
		"rows" : "<?php echo @$rows;?>",
		"totalpage" : "<?php echo @$totalpage;?>",
		"pagesize" : <?php echo PAGESIZE; ?>,
		"module" : "pages",
		"tbwidth" : "100%",
		"column" :[],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {page_id : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,page_type : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		
	};
</script>
<?php $this->load->view("pages/form_custom"); ?>