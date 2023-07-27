<?php 
	$this->load->view("template/form"); 
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Customer","link":"client","active":"false"},{"label":"Update data","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"ON","control_mode":"ON","add_mode":"ON","edit_mode":"ON","delete_mode":"ON","display_mode":"ON","log_mode":"ON","action":"<?php echo @$action;?>"};
	app_tabForm = [{"id":"general","label":"General","form":[{"groups":"User Detail","id":"username","label":"Username","name":"username","type":"text","validate":"notEmpty"},{"groups":"User Detail","id":"password","label":"Password","name":"password","type":"password","validate":"notEmpty"},{"groups":"User Detail","id":"client_group","label":"Customer Group","name":"client_group","type":"select","alert":"\u0e40\u0e21\u0e37\u0e48\u0e2d\u0e01\u0e33\u0e2b\u0e19\u0e14 Customer Group \u0e40\u0e1b\u0e47\u0e19 Client \u0e41\u0e25\u0e49\u0e27\u0e08\u0e30\u0e44\u0e21\u0e48\u0e2a\u0e32\u0e21\u0e32\u0e23\u0e16\u0e40\u0e1b\u0e25\u0e35\u0e48\u0e22\u0e19\u0e40\u0e1b\u0e47\u0e19 Demo \u0e44\u0e14\u0e49","validate":"notEmpty","data":[{"id":"","name":"","value":"Demo","label":"Demo"},{"id":"","name":"","value":"Client Social","label":"Client Social"},{"id":"","name":"","value":"Client Innovation","label":"Client Innovation"}]},{"groups":"Company Detail","id":"company_name","label":"Company Name","name":"company_name","type":"text","validate":"notEmpty"},{"groups":"Company Detail","id":"email","label":"Email","name":"email","type":"text","validate":"notEmpty"},{"groups":"Company Detail","id":"telephone","label":"Telephone","name":"telephone","type":"text","validate":"notEmpty"},{"groups":"Company Detail","id":"start_join","label":"Start Join","name":"start_join","type":"date","validate":"notEmpty"},{"groups":"Company Detail","id":"end_join","label":"End Join","name":"end_join","type":"date","validate":"notEmpty"},{"groups":"Company Detail","id":"setting_allow","label":"Keyword Setting Allow","name":"setting_allow","type":"checkbox","alert":"\u0e15\u0e34\u0e4a\u0e01\u0e40\u0e25\u0e37\u0e2d\u0e01\u0e40\u0e1e\u0e37\u0e48\u0e2d\u0e01\u0e33\u0e2b\u0e19\u0e14\u0e43\u0e2b\u0e49 Customer \u0e2a\u0e32\u0e21\u0e32\u0e23\u0e16\u0e08\u0e31\u0e14\u0e01\u0e32\u0e23\u0e01\u0e31\u0e1a Keyword \u0e2b\u0e23\u0e37\u0e2d Category \u0e44\u0e14\u0e49","data":[{"id":"setting_allow","name":"setting_allow","value":"Yes","label":"Yes"}]}]},{"id":"close_menu","label":"Close Menu","form":[]},{"id":"config","label":"Config","form":[{"groups":"Config Data","id":"client_config","name":"child_client_config","type":"child"}]}];
	app_list = {
		"id" : "<?php echo @$id;?>",
		"title" : "Customer",
		"rows" : "<?php echo @$rows;?>",
		"totalpage" : "<?php echo @$totalpage;?>",
		"pagesize" : <?php echo PAGESIZE; ?>,
		"module" : "client",
		"tbwidth" : "100%",
		"column" :[],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {username : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,password : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,client_group : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,company_name : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,email : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,telephone : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,start_join : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,end_join : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		
	};
</script>
<?php $this->load->view("client/form_custom"); ?>