<?php 
	$this->load->view("template/form"); 
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Users Block","link":"sys_users_block","active":"false"},{"label":"Display Users","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"OFF","control_mode":"ON","add_mode":"OFF","edit_mode":"OFF","delete_mode":"ON","display_mode":"ON","log_mode":"OFF","action":"<?php echo @$action;?>"};
	app_tabForm = [{"id":"general","label":"General","form":[{"groups":"Users Data","id":"username","label":"Username","name":"username","type":"text","validate":"notEmpty"},{"groups":"Users Data","id":"block_type","label":"Block Type","name":"block_type","type":"select","validate":"notEmpty","data":[{"id":"","name":"","value":"backend","label":"backend"},{"id":"","name":"","value":"frontend","label":"frontend"}]},{"groups":"Users Data","id":"block_time","label":"Block Time","name":"block_time","type":"datetime","validate":"notEmpty"}]}];
	app_list = {
		"id" : "<?php echo @$id;?>",
		"title" : "Users Block",
		"rows" : "<?php echo @$rows;?>",
		"totalpage" : "<?php echo @$totalpage;?>",
		"pagesize" : <?php echo PAGESIZE; ?>,
		"module" : "sys_users_block",
		"tbwidth" : "100%",
		"column" :[],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {username : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,block_type : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,block_time : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		
	};
</script>
<?php  ?>