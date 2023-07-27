<?php 
$jsonPermission = '{"id":"permission","label":"Assigned permission","form":[';

foreach($_modules_ as $k_mod=>$v_mod) :
	$data = "[";
	if($v_mod["view"]=='1') $data .= '{"id":"'.$v_mod["module"].'_view","label":"View","name":"'.$v_mod["module"].'_view","value":"1"},';
	if($v_mod["created"]=='1') $data .= '{"id":"'.$v_mod["module"].'_created","label":"Created","name":"'.$v_mod["module"].'_created","value":"1"},';
	if($v_mod["modified"]=='1') $data .= '{"id":"'.$v_mod["module"].'_modified","label":"Modified","name":"'.$v_mod["module"].'_modified","value":"1"},';
	if($v_mod["publish"]=='1') $data .= '{"id":"'.$v_mod["module"].'_publish","label":"Publish","name":"'.$v_mod["module"].'_publish","value":"1"},';
	if($v_mod["deleted"]=='1') $data .= '{"id":"'.$v_mod["module"].'_deleted","label":"Delete","name":"'.$v_mod["module"].'_deleted","value":"1"},';
	$data = rtrim($data,",")."]";
	$jsonPermission .= '{"groups":"Permission", "id":"'.$v_mod["module"].'","label":"'.$v_mod["module"].'","name":"'.$v_mod["module"].'","type":"checkbox","data":'.$data.'},';
endforeach;

if($ROLES_ID=='1') {
	$jsonPermission = ",".rtrim($jsonPermission,",")."]}";
} else {
	$jsonPermission = "";
}
$this->load->view("template/form"); 
$this->load->view("sys_roles/form_custom");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Roles","link":"sys_roles","active":"false"},{"label":"Setting","link":"","active":"true"}];
	app_mode = {
		"checkbox_mode" : "ON",
		"publish_mode" : "ON",
		"control_mode" : "ON",
		"add_mode" : "ON",
		"edit_mode" : "ON",
		"delete_mode" : "ON",
		"display_mode" : "ON",
		"log_mode" : "ON",
		"action" : "<?php echo @$action;?>"
	};
	app_tabForm = [
	    {id:"setting",label:"Setting","form": [{
	        "groups": "Roles data",
	        "id": "roles_name",
	        "label": "Name",
	        "name": "roles_name",
	        "type": "text",
	        "maxlength": "100",
	        "validate": "notEmpty",
	        "class": ""
	    }, {
	        "groups": "Roles data",
	        "id": "roles_detail",
	        "label": "Detail",
	        "name": "roles_detail",
	        "type": "textarea",
	        "class": "",
	        "rows" : "5"
	    }]
	} <?php echo $jsonPermission;?>
];
	app_list = {
		"id" : "<?php echo @$id;?>",
		"title" : "Roles",
		"rows" : "<?php echo @$rows;?>",
		"totalpage" : "<?php echo @$totalpage;?>",
		"pagesize" : <?php echo PAGESIZE; ?>,
		"module" : "sys_roles",
		"tbwidth" : "100%",
		"column" :[],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {
		"roles_name" : { 
			"validators": {
				"notEmpty": {"message": "The value is required and cannot be empty"}
			}
		}
	};
</script>