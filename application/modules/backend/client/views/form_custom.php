<script type="text/javascript">
$(function(){
	if(app_mode.action=="add") {
		$("#username,#password").prop("disabled",true).css("background-color","#FFF");
		setTimeout(function(){
		  	$("#username,#password").val("");
		  	$("#username,#password").prop("disabled",false);
        },1000);
        $("#client_group").val("Demo");
	} else if(app_mode.action=="edit") {
        app_validate.password = {};
        $("#password").val("").parents(".form-group").find(".required").remove();
        var client_group = $("#client_group").val();
        if(client_group.search("Client")!=-1) {
            $("#client_group option:eq(1)").remove();
        }
    }

    $("#tab-close_menu").append('<h2>Menu Item</h2><div id="menu-list" class="form-group" style="margin-left:100px"></div><br />');

    var url = urlbase+"client/client_menu";
    var client_id  = $("#id").val();
    $.post(url,{client_id:client_id},function(res){
        var html = "";
        for(var i in res) {
            var obj = res[i];
            var menu_check = (obj.menu_check=='1') ? 'checked="checked"' : '';
            html += '<div class="checkbox" style="margin-left: '+(obj.menu_level*2)+'0px;"><label><input type="checkbox" name="menu_id[]" value="'+obj.menu_id+'" '+menu_check+'> '+obj.menu_name+'</label></div>';
        }
        $("#menu-list").append(html);
        $("#menu-list").append('<input type="hidden" name="menu_list" value="menu_list" />')
    },'json');

    $('#alert-box').on('hidden.bs.modal', function () {
        $("#password").prop("disabled",false);
    });

});

if(app_mode.action=="edit") {
    function send_action(action) {
        if($.trim($("#password").val())=="") {
            $("#password").prop("disabled",true);
        } 
        if (action != "") {
            _action_ = action;
            $('.form-data').submit();
        }
    }
}
</script>