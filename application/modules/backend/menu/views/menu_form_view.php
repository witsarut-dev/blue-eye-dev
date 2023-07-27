<?php 
	$this->load->view("template/form"); 
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Menu","link":"menu","active":"false"},{"label":"Update Menu","link":"","active":"true"}];
	app_mode = {"checkbox_mode":"ON","publish_mode":"ON","control_mode":"ON","add_mode":"ON","edit_mode":"ON","delete_mode":"ON","display_mode":"ON","log_mode":"ON","action":"<?php echo @$action;?>","order_mode":"ON"};
	app_tabForm = [{"id":"general","label":"General","form":[{"groups":"Menu Item","id":"menu_name","label":"Name","name":"menu_name","type":"text","validate":"notEmpty"},{"groups":"Menu Item","id":"menu_title","label":"Title","name":"menu_title","type":"text","validate":"notEmpty"},{"groups":"Menu Item","id":"menu_icon","label":"Icon","name":"menu_icon","type":"text"},{"groups":"Menu Item","id":"menu_link","label":"Link","name":"menu_link","type":"text","width":"700px","alert":"IN = page\/example , OUT = http:\/\/example.com"},{"groups":"Menu Item","id":"link_target","label":"Link Target","name":"link_target","type":"select","data":[{"id":"","name":"","value":"_self","label":"Self"},{"id":"","name":"","value":"_blank","label":"Blank"},{"id":"","name":"","value":"_parent","label":"Parent"}]}]}];
	app_list = {
		"id" : "<?php echo @$id;?>",
		"title" : "Menu",
		"rows" : "<?php echo @$rows;?>",
		"totalpage" : "<?php echo @$totalpage;?>",
		"pagesize" : <?php echo PAGESIZE; ?>,
		"module" : "menu",
		"tbwidth" : "100%",
		"column" :[],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {menu_name : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		,menu_title : { validators: {
		notEmpty: {message: 'The value is required and cannot be empty'}
		}}
		
	};
</script>
<?php $this->load->view("menu/form_custom"); ?>