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