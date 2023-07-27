<style>
	.sub-group {display: none}
	.sub-group.sub-active {display: table-row}
	.head-group td{cursor: pointer;display: }
	.head-group.head-active td{cursor: default;background: #ededed}
</style>
<script>
$(function(){
    //$("#formSearch").after('<div class="pull-right"><button  class="btn btn-dark" onclick="reset_keyword();">Reset keyword at MongoDB</a></button>');
	$(document).delegate(".head-group","click",function(){
		$(".head-group").removeClass("head-active").find(".fa").removeClass("fa-folder-open").addClass("fa-folder");
		$(".sub-group").removeClass("sub-active");

		var parent_id = $(this).attr("parent-id");
		$(this).addClass("head-active").find(".fa").removeClass("fa-folder").addClass("fa-folder-open");
		$(".sub-group[parent-id="+parent_id+"]").addClass("sub-active");
	});

});

function reset_keyword(id)
{
    $.fancybox({
        'width': 200,
        'autoScale': false,
        'href': urlbase+"config/keyword/cmdReset",
        'type': 'ajax',
        'closeBtn': true
    });
}

function load_on_success(res)
{
	var config_group = "";
	var td_id = 0;
	$("#formList table tbody tr").each(function(index){
		var group = res[index].config_group;
		if(res[index].config_group!=config_group) {
			var head_active = '';
			var head_arrow = 'fa-folder';
			if(config_group=="") {
				head_active = 'head-active';
				head_arrow = 'fa-folder-open';
			}
			config_group = res[index].config_group;
			td_id++;

			$(this).before('<tr class="head-group '+head_active+'" parent-id="'+td_id+'"><td colspan="6"><b>Group '+config_group+'</b><span class="pull-right"><i class="fa '+head_arrow+'"></i></span></td></tr>');
		}
		var sub_active = (td_id==1) ? 'sub-active' : '';
		$(this).attr("parent-id",td_id).addClass("sub-group "+sub_active);
	});
}
</script>
