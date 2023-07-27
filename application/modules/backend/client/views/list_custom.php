<script>
$(function(){
	var remark_label = 'Default Config เป็น Config พื้นฐานสำหรับทุก Company';
	remark_label += 'ท่านสามารถปรับ Config เฉพาะ Company ได้ที่ " <span class="glyphicon glyphicon-pencil"></span> &rarr; Config "';
	$("#formSearch").after('<div class="alert alert-danger alert-dismissable">'+remark_label+'</div>');
});

function load_on_success(res)
{
	$("#formList table tbody tr").each(function(index){
		var start_join = strtotime(res[index].start_join);
		var end_join = strtotime(res[index].end_join);
		var createdate = strtotime(res[index].createdate);
		var client_group = res[index].client_group;
		$(this).find("td:eq(9) div").html('<span class="text-primary">Active</span>');
		if(client_group.search("Demo")!=-1) {
			var current = new Date('<?php echo date("m/d/Y", strtotime(date("Y-m-d")." -1 month"));?>').getTime();
			if(createdate<current) {
				$(this).find("td:eq(9) div").html('<span class="text-danger">Expire</span>');
			}
		}
		if(client_group.search("Client")!=-1) {
			var current = new Date('<?php echo date("m/d/Y");?>').getTime();
			if(current<start_join || current>end_join) {
				$(this).find("td:eq(9) div").html('<span class="text-danger">Expire</span>');
			}
		}
	});
}


function strtotime(date)
{
	date = date.substring(0,10);
	var obj = date.split("-");
	date = obj[1]+"/"+obj[2]+"/"+obj[0];
	return new Date(date).getTime();
}
</script>