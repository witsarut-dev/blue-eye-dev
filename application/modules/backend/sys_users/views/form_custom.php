<script type="text/javascript">
$(function(){

	if(app_mode.action=="add") {
		$("#username,#password").prop("disabled",true).css("background-color","#FFF");
		setTimeout(function(){
		 	$("#username,#password").val("");
		 	$("#username,#password").prop("disabled",false);
		},1000);
	} else {
		$("#password").prop("disabled",true).css("background-color","#FFF");
		setTimeout(function(){
		 	$("#password").val("");
		 	$("#password").prop("disabled",false);
		},1000);
		<?php if($ROLES_ID!='1') { ?>
			$("#password,#username").parent().parent().hide();
		<?php } ?>
	}

	<?php if($ROLES_ID=='1') { ?>
	$("#sys_roles_id").css({"margin-right":"5px","display":"inline-block"}).after('<button type="button" class="btn btn-dark btn-edit-role" disabled>Custom role</button>');
	$("input[type=hidden][id=id][value=<?php echo $USER_ID;?>]").parents("#myForm").find(".btn-delete-list,.btn-save").remove();
	<?php } ?>
	$("a[href='#tab-permission']").parent().hide();

	check_role(app_post.sys_roles_id);
	$(document).delegate("#sys_roles_id","change",function(){
		check_role($(this).val());
	});

	$("#tab-permission").hide();
	$("#tab-permission").append('<br /><div align="center"><button type="button" class="btn btn-dark btn-save-role" onclick="$.fancybox.close();">OK</button></div>')
	$(document).delegate(".btn-edit-role","click",function(){
		$("#tab-permission").addClass("form-horizontal").css({"opacity":1});
		$("#tab-permission h2:first,#tab-permission .form-group:first").hide();
		$.fancybox({
            'width': 500,
            'height': 400,
            'autoSize': false,
            'href': "#tab-permission",
            'padding': 20,
            'closeBtn': false
        });
	});

	$("label[for='config']").text("default config");
});

function check_role(sys_roles_id) {
	if(sys_roles_id=='0') {
		$(".btn-edit-role").attr("disabled",false);
		$("#assigned").prop("checked",true);
	} else {
		$(".btn-edit-role").attr("disabled",true);
		$("#assigned").prop("checked",false);
	}
}
</script>