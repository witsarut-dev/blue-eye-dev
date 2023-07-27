<?php 
$jsonPermission = '{"id":"permission","label":"Assigned permission","form":[';

if($ROLES_ID=='1') {
	$jsonPermission .= '{"groups":"Users assigned","id":"assigned","label":"Assigned","name":"assigned","type":"checkbox","class":"","data": [{"id":"assigned","name":"assigned","value":"Y","label":"with out roles"}] },';
}

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

$where_role = "WHERE sys_roles_id<>'1' && sys_roles_id<>'0'";
if($ROLES_ID=='1') {
	$jsonPermission = ",".rtrim($jsonPermission,",")."]}";
	$where_role = "";
} else {
	$jsonPermission = "";
}
$this->load->view("template/form"); 
$this->load->view("sys_users/form_custom");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Users","link":"sys_users","active":"false"},{"label":"Setting","link":"","active":"true"}];
	app_mode = {
		"checkbox_mode" : "ON",
		"publish_mode" : "ON",
		"control_mode" : "ON",
		"add_mode" : "ON",
		"edit_mode" : "ON",
		"delete_mode" : "ON",
		"display_mode" : "ON",
		"log_mode" : "ON",
		"action" : '<?php echo @$action;?>'
	};
	app_tabForm = [{
		    "id": "setting",
		    "label": "Setting",
		    "form": [{
		        "groups": "Users data",
		        "id": "firstname",
		        "label": "Name",
		        "name": "firstname",
		        "type": "text",
		        "maxlength": "255",
		        "validate": "notEmpty",
		        "class": ""
		    }, {
		        "groups": "Users data",
		        "id": "lastname",
		        "label": "Surname",
		        "name": "lastname",
		        "type": "text",
		        "maxlength": "255",
		        "validate": "notEmpty",
		        "class": ""
		    }, {
		        "groups": "Users data",
		        "id": "username",
		        "label": "Username",
		        "name": "username",
		        "type": "text",
		        "maxlength": "100",
		        "validate": "notEmpty",
		        "class": ""
		    }, {
		        "groups": "Users data",
		        "id": "password",
		        "label": "Password",
		        "name": "password",
		        "type": "password",
		        "maxlength": "100",
		        "class": ""
		    }, {
		        "groups": "Users data",
		        "id": "email",
		        "label": "Email",
		        "name": "email",
		        "type": "text",
		        "maxlength": "255",
		        "validate": "notEmpty",
		        "class": ""
		    }, {
		        "groups": "Users data",
		        "id": "sys_roles_id",
		        "label": "Role",
		        "name": "sys_roles_id",
		        "type": "lookup",
		        "query": "SELECT roles_name AS Label,sys_roles_id AS Value FROM sys_roles <?php echo $where_role; ?> ORDER BY sys_roles_id ASC",
		        "validate": "notEmpty",
		        "class": ""
		    }]
		} <?php echo $jsonPermission;?>
	];
	app_list = {
		"id" : "<?php echo @$id;?>",
		"title" : "Users",
		"rows" : "<?php echo @$rows;?>",
		"totalpage" : "<?php echo @$totalpage;?>",
		"pagesize" : <?php echo PAGESIZE; ?>,
		"module" : "sys_users",
		"tbwidth" : "100%",
		"column" :[],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {
		"firstname": {
	        "validators": {
	            "notEmpty": {
	                "message": "The value is required and cannot be empty"
	            }
	        }
	    },
	    "lastname": {
	        "validators": {
	            "notEmpty": {
	                "message": "The value is required and cannot be empty"
	            }
	        }
	    },
	    "username": {
	        "validators": {
	            "notEmpty": {
	                "message": "The value is required and cannot be empty"
	            }
	        }
	    },
	    <?php if(@$action=="add") { ?>
	    "password": {
	        "validators": {
	            "notEmpty": {
	                "message": "The value is required and cannot be empty"
	            }
	        }
	    },
	    <?php } ?>
	    "email": {
	        "validators": {
	            "notEmpty": {
	                "message": "The value is required and cannot be empty"
	            },
	            "emailAddress": {
	                "message": "The input is not a valid email address"
	            }
	        }
	    },
	    "sys_roles_id": {
	        "validators": {
	            "notEmpty": {
	                "message": "The value is required and cannot be empty"
	            }
	        }
	    }
	};
</script>