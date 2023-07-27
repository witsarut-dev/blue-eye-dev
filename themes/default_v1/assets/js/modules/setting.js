$(function(){

    set_color_company_keyword();

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = "setting"+$(e.target).attr("href") // activated tab
        history.pushState({}, "", target);
    });

    if(window.location.hash!="") {
        $("#SettingData a[href='"+window.location.hash+"']").trigger("click");
    }

    $("#SettingData .btnAddCompany").click(function(){
        var targetForm = $(this).attr("targetForm");
        $("#"+targetForm).parsley().validate();
        if(!$("#"+targetForm).parsley().validate()) {
            $("#"+targetForm).find('.parsley-errors-list').show();
        } else {
        	var url  = urlbase+"setting/cmdAddCompany";
        	var data = $("#formAddCom").serialize();

        	var target = $(this).attr('target');
			var company_keyword_name = $('input[name=company_keyword_name]').val();
			var company_keyword_type = $('select[name=company_keyword_type]').val();
			var inputAddList = company_keyword_name+" ("+company_keyword_type+")";

		    $.ajax({
		        type : 'post',
		        dataType : 'json',
		        data: data,
		        url: url,
		        beforeSend: function() {
		        	$.fancybox.showLoading();
		        },
		        error: function() {
		            $.fancybox.hideLoading();
		            dialog_error("No internet connection");
		        },
		        success : function(res) {
		            if(res.status) {
				        $('input[name=company_keyword_name]').val("");
				        $('input[name=company_keyword_fb]').val("");
                        var _value_ = res.company_keyword_id;
   						var params = ' target="list-group-keyword" value="'+_value_+'" ';
			            $("#"+target+" .to_do").append('<li><p><input name="company_keyword_id[]" type="checkbox" class="flat" "'+params+'"> '+inputAddList+'</p></li>');
			            $("#"+target+" .to_do li:last").hide().fadeIn("slow");
			            $("#"+target+" input.flat:last").iCheck({
			                checkboxClass: "icheckbox_flat",
			                radioClass: "iradio_flat"
			            });
		            } else {
		           		dialog_error(res.message);
		            }
		            $.fancybox.hideLoading();
                    set_color_company_keyword();
		        }
		    });

        }
    });

    $("#SettingData button.btnDelCompany").click(function(){
        if($(this).parents(".x_content").find("input[type=checkbox]:first").is(":checked")) {
        	dialog_error("คุณไม่สามารถลบ Company ของตัวคุณเองได้");
        } else {
        	dialog_confirm("เมื่อคุณลบ Company ข้อมูล Group keyword และ keyword จะโดยลบไปด้วยคุณยืนยันที่จะลบหรือไม่","cmd_del_company();");
        }
    });

    $("#SettingData .btnAddGroupKeyword").click(function(){

        if($("#CompanyList input[type=checkbox]").is(":checked")==false) {
            dialog_error("กรุณาเลือก Company");
            return false;
        } else if($("#CompanyList input[type=checkbox]:checked").size()>1) {
            dialog_error("คุณสามารถเลือกได้แค่ 1 Company เท่านั้น");
            return false;
        }

        var targetForm = $(this).attr("targetForm");
        $("#"+targetForm).parsley().validate();
        if(!$("#"+targetForm).parsley().validate()) {
            $("#"+targetForm).find('.parsley-errors-list').show();
        } else {
            var url  = urlbase+"setting/cmdAddGroupKeyword";
            var data = $("#"+targetForm).serialize();
            var company_keyword_id = $("#CompanyList input[type=checkbox]:checked").val();

            var target = $(this).attr('target');
            var inputAddList = $("#"+target).find('.inputAddList').val();

            $.ajax({
                type : 'post',
                dataType : 'json',
                data: data+"&company_keyword_id="+company_keyword_id,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                },
                success : function(res) {
                    if(res.status) {
                        $("#"+target).find('.inputAddList').val("");

                        var _value_ = res.group_keyword_id;
                        var _parent_ = res.company_keyword_id;
                        var params = ' target="list-keywrod" value="'+_value_+'" parent="'+_parent_+'" ';
                        $("#"+target+" .to_do").append('<li><p><input name="group_keyword_id[]" type="checkbox" class="flat" "'+params+'"> '+inputAddList+'</p></li>');
                        $("#"+target+" .to_do li:last").hide().fadeIn("slow");
                        $("#"+target+" input.flat:last").iCheck({
                            checkboxClass: "icheckbox_flat",
                            radioClass: "iradio_flat"
                        });
                        $("#"+target+" input.flat:last").iCheck("check");
                    } else {
                        dialog_error(res.message);
                    }
                    $.fancybox.hideLoading();
                    set_color_company_keyword();
                }
            });
        }
    });

    $("#SettingData button.btnDelGroupKeyword").click(function(){
        dialog_confirm("เมื่อคุณลบ Group keyword ข้อมูล Keyword จะโดยลบไปด้วยคุณยืนยันที่จะลบหรือไม่","cmd_del_group_keyword();");
    });


    $("#SettingData .btnAddKeyword").click(function(){

        if($("#GroupKeywordList input[type=checkbox]").is(":checked")==false) {
            dialog_error("กรุณาเลือก Group Keyword");
            return false;
        } else if($("#GroupKeywordList input[type=checkbox]:checked").size()>1) {
            dialog_error("คุณสามารถเลือกได้แค่ 1 Group Keyword เท่านั้น");
            return false;
        }

        var targetForm = $(this).attr("targetForm");
        $("#"+targetForm).parsley().validate();
        if(!$("#"+targetForm).parsley().validate()) {
            $("#"+targetForm).find('.parsley-errors-list').show();
        } else {
            var url  = urlbase+"setting/cmdAddKeyword";
            var data = $("#"+targetForm).serialize();
            var group_keyword_id = $("#GroupKeywordList input[type=checkbox]:checked").val();

            var target = $(this).attr('target');
            var inputAddList = $("#"+target).find('.inputAddList').val();

            $.ajax({
                type : 'post',
                dataType : 'json',
                data: data+"&group_keyword_id="+group_keyword_id,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                },
                success : function(res) {
                    if(res.status) {
                        $("#"+target).find('.inputAddList').val("");

                        var _value_ = res.keyword_id;
                        var _parent_ = res.group_keyword_id;

                        var params = ' value="'+_value_+'" parent="'+_parent_+'" ';
                        $("#"+target+" .to_do").append('<div class="col-md-4 col-sm-6 col-xs-12"><p><input name="keyword_id[]" type="checkbox" class="flat" "'+params+'"> '+inputAddList+'</p></div>');
                        $("#"+target+" .to_do div:last").hide().fadeIn("slow");
                        $("#"+target+" input.flat:last").iCheck({
                            checkboxClass: "icheckbox_flat",
                            radioClass: "iradio_flat"
                        });
                        $("#"+target+" input.flat:last").iCheck("check");
                    } else {
                        dialog_error(res.message);
                    }
                    $.fancybox.hideLoading();
                    set_color_company_keyword();
                }
            });
        }
    });

    $("#SettingData button.btnDelKeyword").click(function(){
        dialog_confirm("คุณต้องการลบ Keyword คุณยืนยันที่จะลบหรือไม่","cmd_del_keyword();");
    });

    $("#SettingData .btnImportKeyword").click(function(){
        if($("#GroupKeywordList input[type=checkbox]").is(":checked")==false) {
            dialog_error("กรุณาเลือก Group Keyword");
            return false;
        } else if($("#GroupKeywordList input[type=checkbox]:checked").size()>1) {
            dialog_error("คุณสามารถเลือกได้แค่ 1 Group Keyword เท่านั้น");
            return false;
        } else {
            var group_keyword_id = $("#GroupKeywordList input[type=checkbox]:checked").val();
            $('#formImport input[name=group_keyword_id]').val(group_keyword_id);
            $.fancybox({
                'width': 400,
                'height': 150,
                'autoSize': false,
                'href': "#showPageImport",
                'padding': 20,
                'closeBtn': true
            });
        }
    });

    $(document).delegate(".btn-import-file","click",function(){
        if($("#file_import").val()=="") {
            alert("Please choose your file.");
        } else {
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
                $.fancybox.showLoading();
            },
            error: function() {
                $(".btn").prop("disabled",false);
                $.fancybox.hideLoading();
                dialog_error("No internet connection");
            },
            success: function(res) {
                $(".btn").prop("disabled",false);
                $.fancybox.hideLoading();
                if(res.status) {
                    window.location.reload(0);
                } else {
                    dialog_error(res.message);
                }
            }
        };

        $(this).ajaxSubmit(options);
        return false;
    });

});

function cmd_del_company()
{
   	var url  = urlbase+"setting/cmdDelCompany";
	var data = $("#formCompanyList").serialize();

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: data,
        url: url,
        beforeSend: function() {
        	$.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            dialog_error("No internet connection");
        },
        success : function(res) {
            if(res.status) {
		        window.location.reload(0);
            } else {
           		dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

function cmd_del_group_keyword()
{
    var url  = urlbase+"setting/cmdDelGroupKeyword";
    var data = $("#formAddGroupKeyword").serialize();

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: data,
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            dialog_error("No internet connection");
        },
        success : function(res) {
            if(res.status) {
                window.location.reload(0);
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

function cmd_del_keyword()
{
    var url  = urlbase+"setting/cmdDelKeyword";
    var data = $("#formAddKeyword").serialize();

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: data,
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            dialog_error("No internet connection");
        },
        success : function(res) {
            if(res.status) {
                window.location.reload(0);
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

var txtColor = ["#449d44","#337ab7","#c43431","#ca24c9","#1cce00","#245269","#843534","#5bc0de","#f0ad4e","#0003cc","#555555","#008000","#a94442"];
var txtComColor = [];
var txtGroupColor = [];

function set_color_company_keyword()
{
    $("#formCompanyList .to_do li").each(function(index){
        var color = txtColor[index];
        var parent = $(this).find("input[name^='company_keyword_id']").val();
        $(this).find("p").css("color",color);
        txtComColor[parent] = color;
    });
    set_color_group_keyword();
}

function set_color_group_keyword()
{
    $("#formAddGroupKeyword .to_do li").each(function(){
        var index = $(this).find("input[name^='group_keyword_id']").attr("parent");
        var color = txtComColor[index];
        var parent = $(this).find("input[name^='group_keyword_id']").val();
        $(this).find("p").css("color",color);
        txtGroupColor[parent] = color;
    });
    set_color_keyword_keyword();
}

function set_color_keyword_keyword()
{
    $("#formAddKeyword .to_do div").each(function(index){
        var index = $(this).find("input[name^='keyword_id']").attr("parent");
        var color = txtGroupColor[index];
        $(this).find("p").css("color",color);
    });
}

