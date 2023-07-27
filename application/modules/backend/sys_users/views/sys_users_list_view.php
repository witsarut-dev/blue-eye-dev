<?php 
$this->load->view("template/list"); 
$this->load->view("sys_users/list_custom");
?>
<script type="text/javascript">
	app_breadcrumb = [{"label":"Users","link":"","active":"true"}];
	app_mode = {
		"checkbox_mode" : "ON",
		"publish_mode" : "ON",
		"control_mode" : "ON",
		"add_mode" : "ON",
		"edit_mode" : "ON",
		"delete_mode" : "ON",
		"display_mode" : "ON",
		"log_mode" : "ON"
	};
	app_searchForm = [];
	app_list = {
		"title" : "Users",
		"rows" : <?php echo $rows;?>,
		"rows_publish" : <?php echo $rows_publish;?>,
		"rows_modified" : <?php echo $rows_modified;?>,
		"rows_unpublish" : <?php echo $rows_unpublish;?>,
		"totalpage" : <?php echo $totalpage;?>,
		"pagesize" : <?php echo $pagesize;?>,
		"sorting" : "sys_users_id",
		"orderby" : "desc",
		"module" : "sys_users",
		"tbwidth" : "100%",
		"column" :[{"label":"Name","name":"firstname","type":"text","sort":"T","width":"80","class":"left"},{"label":"Surname","name":"lastname","type":"text","sort":"T","width":"120","class":"left"},{"label":"Username","name":"username","type":"text","sort":"T","width":"120","class":"left"},{"label":"Roles","name":"roles_name","type":"lookup","sort":"sys_roles","width":"120","class":"left"},{"label":"Update","name":"lastupdate","type":"datetime","sort":"T","width":"100","class":"left"}],
		"items" : []
	};
	app_post = <?php echo json_encode($post);?>;
	app_validate = {};
</script>
<script type="text/javascript">
<?php if($ROLES_ID=='1') { ?>
function load_on_success(res)
{
	var _chk = $("input[type=checkbox][value=<?php echo $USER_ID;?>]");
	var _parent = $(_chk).parents('tr');
	$(_parent).find("input[type=checkbox]").attr("disabled",true);
	$(_parent).find(".glyphicon-globe").removeClass("btn-publish").css({"color":"#ccc","cursor":"default"});
	$(_parent).find(".glyphicon-trash").removeClass("btn-delete").css({"color":"#ccc","cursor":"default"});
}
<?php } ?>
</script>