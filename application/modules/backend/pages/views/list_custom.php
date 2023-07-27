<style>
.border_remrak {
	border: 1px solid red;
    padding: 1px 2px;
    border-radius: 5px;
    color:#aaa;
}
.ex-header {
	font-size: 18px;
	position: relative;
    top: 5px;
}
.ex-detail {
	 position: relative;
    top: -16px;
}
</style>
<script>
$(function(){
	var remark_label = 'ตัวอย่างไฟล์สำหรับ import "<a style="color:#ff0000;text-decoration: underline;" href="<?php echo base_url("upload/pages/import_pages.xls");?>">Click Here</a></span>" ';
	var remark_page = '<b>Page ID</b> = https://www.facebook.com/<span class="border_remrak">thairathtv</span>';
	remark_page += ' / <b>TW USER ID</b> = https://twitter.com/<span class="border_remrak">Thairath_News</span></span>';
	$("#BoxPage:first").after('<span class="ex-header">ตัวอย่าง</span><div class="alert alert-danger alert-dismissable pull-right">'+remark_label+'</div><div class="clear"></div><div class="pull-left ex-detail">'+remark_page+'</div>');
	$("#BoxPage .btn-group").prepend('<button type="button" class="btn btn-xs btn-dark btn-import-page"><span class="fa fa-file-excel-o"></span> Import</button>');
	
	$(document).delegate(".btn-import-page","click",function(){
		$.fancybox({
            'width': 400,
            'height': 150,
            'autoSize': false,
            'href': "#showPageImport",
            'padding': 20,
            'closeBtn': true
        });
	});

	$(document).delegate(".btn-import-file","click",function(){
		if($("#file_import").val()=="") {
			alert("Please choose your file.");
		} else {
			$.fancybox.close();
			$.fancybox({
				'width': 800,
	            'height': 150,
	            'autoSize': false,
	            'href': "#showPageWait",
	            'padding': 20,
	            'closeBtn': false,
				'helpers'   : { 
					'overlay' : {closeClick: false}
				},
				beforeShow: function(){
				  	$(".fancybox-skin").css({"backgroundColor":"transparent","-webkit-box-shadow":"none","box-shadow":"none","color":"#FFF"});
				}
			});
			$("#formImport").submit();
		}
	});

	$('#formImport').submit(function(event) {

        var options = {
            url: $(this).attr("action"),
            type: 'post',
            dataType: 'json',
            clearForm: false,
            resetForm: false,
            timeout: 10000,
            beforeSend: function() {
                $(".btn").prop("disabled",true);
            },
            success: function(res) {
            	$(".btn").prop("disabled",false);
            	pages_total = res.pages.length;
            	imp_id = res.imp_id;
            	page_rows = 0;
            	save_page(res.pages);
            }
        };

        $(this).ajaxSubmit(options);
        return false;
    });
});

var pages_total = 0;
var total_rows = 0;
var success_rows = 0;
var error_rows = 0;
var duplicate_rows = 0;
var imp_id = 0;
var page_rows = 0;

function save_page(pages)
{
	if(page_rows<pages_total) {
		var page_id = pages[page_rows].page_id;
	    var page_type = pages[page_rows].page_type;
    	var url = '<?php echo site_url("pages/pages_import/cmdSavePage");?>';
		$.post(url,{page_id:page_id,page_type:page_type},function(page){
			save_status = true;
			success_rows += page.success_rows;
			error_rows += page.error_rows;
			duplicate_rows += page.duplicate_rows;
			total_rows += page.total_rows;
			page_rows = page_rows + 1;
			save_page(pages);
		},'json').fail(function() {
    		setTimeout(function(){ save_page(pages); }, 3000);
  		});
	} else {
		$.fancybox.close();
		$("#showResultImport .total_label").text(total_rows);
        $("#showResultImport .success_label").text(success_rows);
        $("#showResultImport .error_label").text(error_rows);
        $("#showResultImport .duplicate_label").text(duplicate_rows);
        $.fancybox({
            'width': 400,
            'height': 200,
            'autoSize': false,
            'href': "#showResultImport",
            'padding': 20,
            'closeBtn': true,
            afterClose: function() {
            	window.location.href = '<?php echo site_url("pages");?>';
            }
        });
        var url = '<?php echo site_url("pages/pages_import/cmdUpdateImport");?>';
        $.post(url,{
        	imp_id:imp_id,
        	total_rows:total_rows,
        	success_rows:success_rows,
        	error_rows:error_rows,
        	duplicate_rows:duplicate_rows},function(){
        });
	}
}
</script>
<div id="showPageImport" style="display:none">
	<form id="formImport" action="<?php echo site_url("pages/pages_import/cmdSaveImport");?>" class="form-horizontal" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-sm-12">Choose File : <input type="file" name="file_import" id="file_import" filetype="xls|xlsx" style="padding: 6px 12px;display: inline-block;" /></label>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-8"><button type="button" class="btn btn-dark btn-import-file">Import File</button></div>
		</div>
	</form>
</div>

<div id="showPageWait" style="display:none">
	<form id="formResultImport" class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-12"><h3 align="center">ระบบกำลังทำงานอาจใช้เวลานาน... ถ้าปิดหน้าต่างนี้ระบบจะหยุดทำงานทันที</h3></label>
		</div>
	</form>
</div>

<div id="showResultImport" style="display:none">
	<form id="formResultImport" class="form-horizontal">
		<h3>Import information</h3>
		<div class="form-group">
			<label class="col-sm-4">Total :</label> 
			<label class="col-sm-8"><span class="total_label">0</span></label> 
		</div>
		<div class="form-group">
			<label class="col-sm-4">Success :</label> 
			<label class="col-sm-8"><span class="success_label text-success">0</span></label> 
		</div>
		<div class="form-group">
			<label class="col-sm-4">Error :</label> 
			<label class="col-sm-8"><span class="error_label text-danger">0</span></label> 
		</div>
		<div class="form-group">
			<label class="col-sm-4">Duplicate :</label> 
			<label class="col-sm-8"><span class="duplicate_label text-info">0</span></label> 
		</div>
	</form>
</div>