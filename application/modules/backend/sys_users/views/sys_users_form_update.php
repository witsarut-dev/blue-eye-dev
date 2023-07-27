<?php $this->load->view("template/form"); ?>
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
		"log_mode" : "OFF",
		"action" : "<?php echo @$action;?>"
	};
	app_tabForm = [{
	    id: "setting",
	    label: "Setting",
	    "form": [{
	        "groups": "Edit profile",
	        "id": "firstname",
	        "label": "Name",
	        "name": "firstname",
	        "type": "text",
	        "maxlength": "255",
	        "validate": "notEmpty",
	        "class": ""
		}, {
	        "groups": "Edit profile",
	        "id": "lastname",
	        "label": "Surname",
	        "name": "lastname",
	        "type": "text",
	        "maxlength": "255",
	        "validate": "notEmpty",
	        "class": ""
        }, {
            "groups": "Edit profile",
            "id": "old_password",
            "label": "Old Password",
            "name": "old_password",
            "type": "password",
            "maxlength": "100",
            "class": ""
		}, {
	        "groups": "Edit profile",
	        "id": "password",
	        "label": "New Password",
	        "name": "password",
	        "type": "password",
	        "maxlength": "100",
	        "class": ""
	    }, {
	        "groups": "Edit profile",
	        "id": "re_password",
	        "label": "Re-password",
	        "name": "re_password",
	        "type": "password",
	        "maxlength": "100",
	        "class": ""
	    }, {
	        "groups": "Edit profile",
	        "id": "email",
	        "label": "Email",
	        "name": "email",
	        "type": "text",
	        "maxlength": "255",
	        "validate": "notEmpty",
	        "class": "required"
	    }]
	}];
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
             "old_password": {
                "validators": {
                    "notEmpty": {
                        "message": "The value is required and cannot be empty"
                    }
                }
            },
			"password": {
	            "validators": {
	                "identical": {
	                    "field": "re_password",
	                    "message": "The password and its confirm are not the same"
	                }
	            }
	        },
		    "re_password": {
		        "validators": {
		            "identical": {
		                "field": "password",
		                "message": "The password are not the same"
		            }
		        }
		    },
		    "email": {
		        "validators": {
		            "notEmpty": {
		                "message": "The value is required and cannot be empty"
		            },
		            "emailAddress": {
		                "message": "The input is not a valid email address"
		            }
		        }
		    }
		};
</script>