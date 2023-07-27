<?php 
	$this->load->view("template/form"); 
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Config","link":"config","active":"false"},{"label":"Update data","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"OFF","control_mode":"ON","add_mode":"OFF","edit_mode":"ON","delete_mode":"OFF","display_mode":"ON","log_mode":"OFF","action":"<?php echo @$action;?>"};
	app_tabForm = [{"id":"general","label":"General","form":[{"groups":"Config Data","id":"config_group","label":"Group","name":"config_group","type":"text"},{"groups":"Config Data","id":"config_name","label":"Name","name":"config_name","type":"text","validate":"notEmpty"},{"groups":"Config Data","id":"config_val","label":"Value","name":"config_val","type":"text","validate":"notEmpty"},{"groups":"Config Data","id":"config_detail","label":"Detail","name":"config_detail","type":"textarea"}]}];
	app_list = {
		"id" : "<?php echo @$id;?>",
		"title" : "Default Config",
		"rows" : "<?php echo @$rows;?>",
		"totalpage" : "<?php echo @$totalpage;?>",
		"pagesize" : <?php echo PAGESIZE; ?>,
		"module" : "config",
		"tbwidth" : "100%",
		"column" :[],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {config_name : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,config_val : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		
	};
</script>
<?php $this->load->view("config/form_custom"); ?>