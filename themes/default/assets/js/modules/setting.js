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
                    $("#SettingData .btnAddCompany").attr("disabled",true);
		        },
		        error: function() {
		            $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                    $("#SettingData .btnAddCompany").attr("disabled",false);
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
                    $("#SettingData .btnAddCompany").attr("disabled",false);
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
        if($("#CompanyList input[type=checkbox]").is(":checked") == false) {
            dialog_error("กรุณาเลือก Company");
            return false;
        } else if($("#CompanyList input[type=checkbox]:checked").size() > 1) {
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
                data: data + "&company_keyword_id=" + company_keyword_id,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    $("#SettingData .btnAddGroupKeyword").attr("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                    $("#SettingData .btnAddGroupKeyword").attr("disabled",false);
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
                    $("#SettingData .btnAddGroupKeyword").attr("disabled",false);
                }
            });
        }
    });

    $("#SettingData button.btnDelGroupKeyword").click(function(){
        dialog_confirm("เมื่อคุณลบ Group keyword ข้อมูล Keyword จะโดยลบไปด้วยคุณยืนยันที่จะลบหรือไม่","cmd_del_group_keyword();");
    });

    //
    $("#SettingData .btnAddGroupKeyword_categories").click(function(){
        if($("#CompanyList input[type=checkbox]").is(":checked") == false) {
            dialog_error("กรุณาเลือก Company");
            return false;
        } else if($("#CompanyList input[type=checkbox]:checked").size() > 1) {
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
                data: data + "&company_keyword_id=" + company_keyword_id,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    $("#SettingData .btnAddGroupKeyword_categories").attr("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                    $("#SettingData .btnAddGroupKeyword_categories").attr("disabled",false);
                },
                success : function(res) {
                    if(res.status) {
                        $("#"+target).find('.inputAddList').val("");

                        var _value_ = res.group_keyword_id;
                        var _parent_ = res.company_keyword_id;
                        var params = ' target="list-categories" value="'+_value_+'" parent="'+_parent_+'" ';

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
                    $("#SettingData .btnAddGroupKeyword_categories").attr("disabled",false);
                }
            });
        }
    });

    $("#SettingData button.btnDelGroupKeyword_categories").click(function(){
        dialog_confirm("เมื่อคุณลบ Group keyword ข้อมูล Keyword จะโดยลบไปด้วยคุณยืนยันที่จะลบหรือไม่", "cmd_del_group_categories();");
    });

    // Action for "add" category name to database
    $("#SettingData .btnAddCategories").click(function(){
        if($("#GroupKeywordList_categories input[type=checkbox]").is(":checked") == false) {
            dialog_error("กรุณาเลือก Group Keyword");
            return false;
        } else if($("#GroupKeywordList_categories input[type=checkbox]:checked").size() > 1) {
            dialog_error("คุณสามารถเลือกได้แค่ 1 Group Keyword เท่านั้น");
            return false;
        }

        var targetForm = $(this).attr("targetForm");

        $("#"+targetForm).parsley().validate();

        if(!$("#"+targetForm).parsley().validate()) {
            $("#"+targetForm).find('.parsley-errors-list').show();
        } else {
            var url  = urlbase + "setting/cmdAddCategories";
            var data = $("#" + targetForm).serialize();
            var group_keyword_id = $("#GroupKeywordList_categories input[type=checkbox]:checked").val();

            var target = $(this).attr('target');
            var inputAddList = $("#"+target).find('.inputAddList').val();

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data + "&group_keyword_id=" + group_keyword_id,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    $("#SettingData .btnAddCategories").attr("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                    $("#SettingData .btnAddCategories").attr("disabled",false);
                },
                success : function(res) {
                    if(res.status) {
                        $("#"+target).find('.inputAddList').val("");

                        var _value_ = res.categories_id;
                        var _parent_ = res.group_keyword_id;
                        var params = ' target="list-keywrod" value="'+_value_+'" parent="'+_parent_+'" ';
                        $("#"+target+" .to_do").append('<li class="col-md-4 col-sm-6 col-xs-6" style="padding-left: 0px;"><p><input name="categories_id[]" type="checkbox" class="flat" "'+params+'"> '+inputAddList+'</p></li>');
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
                    $("#SettingData .btnAddCategories").attr("disabled",false);
                }
            });
        }
    });
    // end Action for "add" category name to database

    // Action for "delete" category name from database
    $("#SettingData button.btnDelCategories").click(function(){
        dialog_confirm("เมื่อคุณลบ Category ข้อมูล Keyword จะถูกลบไปด้วยคุณยืนยันที่จะลบหรือไม่", "cmd_del_categories();");
    });
    // end Action for "delete" category name from database

    // Action for "add" keyword name of category that selected to database
    $("#SettingData .btnAddKeyword_categories").click(function() {
        if($("#CategoriesList input[type=checkbox]").is(":checked")==false) {
            dialog_error("กรุณาเลือก Category");
            return false;
        } else if($("#CategoriesList input[type=checkbox]:checked").size()>1) {
            dialog_error("คุณสามารถเลือกได้แค่ 1 หมวดหมู่เท่านั้น");
            return false;
        }

        var targetForm = $(this).attr("targetForm");
        $("#"+targetForm).parsley().validate();
        if(!$("#"+targetForm).parsley().validate()) {
            $("#"+targetForm).find('.parsley-errors-list').show();
        } else {
            var url  = urlbase+"setting/cmdAddKeyword";
            var data = $("#"+targetForm).serialize();
            var categories_id = $("#CategoriesList input[type=checkbox]:checked").val();

            var target = $(this).attr('target');
            var inputAddList = $("#"+target).find('.inputAddList').val();

            $.ajax({
                type : 'post',
                dataType : 'json',
                data: data+"&categories_id="+categories_id,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    $("#SettingData .btnAddKeyword_categories").attr("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                    $("#SettingData .btnAddKeyword_categories").attr("disabled",false);
                },
                success : function(res) {
                    if(res.status) {
                        $("#"+target).find('.inputAddList').val("");

                        var _value_ = res.keyword_id;
                        var _parent_ = res.categories_id;

                        var params = ' value="'+_value_+'" parent="'+_parent_+'" ';
                        var keylink = '<a href="'+urlbase+'setting/setting_filter/'+res.keyword_id+'" class="fancybox link-setting">'+inputAddList+'</a>';
                        $("#"+target+" .to_do").append('<div class="col-md-4 col-sm-6 col-xs-12" style="padding-left: 0px;"><p><input name="keyword_id[]" type="checkbox" class="flat" "'+params+'"> '+keylink+'</p></div>');
                        $("#"+target+" .to_do div:last").hide().fadeIn("slow");
                        $("#"+target+" input.flat:last").iCheck({
                            checkboxClass: "icheckbox_flat",
                            radioClass: "iradio_flat"
                        });
                        $("#"+target+" input.flat:last").iCheck("check");

                        var key_parent = $("#formAddKeyword input[name^=keyword_id][value="+res.keyword_id+"]").parents("p");

                        $(key_parent).find(".flag").remove();
                        if($("#formAddKeyword input[name=thai_only]").is(":checked")) {
                            $(key_parent).find('a').append(' <i class="flag flag-th"></i>');
                        }
                        
                        $(key_parent).find(".fa").remove();
                        if($("#formAddKeyword input[name=primary_keyword]").is(":checked")) {
                            $(key_parent).find('a').append(' <i class="fa fa-star" style="color: #FFD91E;"></i>');
                        }

                        $("#formAddKeyword input[name=thai_only]").attr("checked",false);
                        $("#formAddKeyword input[name=primary_keyword]").attr("checked",false);

                        load_fancybox();
                    } else {
                        dialog_error(res.message);
                    }
                    $.fancybox.hideLoading();
                    set_color_company_keyword();
                    $("#SettingData .btnAddKeyword_categories").attr("disabled",false);
                }
            });
        }
    });
    // end Action for "add" keyword name of category that selected to database

    // Action for "delete" keyword name of category that selected to database
    $("#SettingData button.btnDelKeyword_categories").click(function(){
        dialog_confirm("คุณต้องการลบ Keyword คุณยืนยันที่จะลบหรือไม่","cmd_del_keyword_categories();");
    });
    // end Action for "delete" keyword name of category that selected to database

    $("#SettingData .btnAddKeyword").click(function() {
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
                data: data + "&group_keyword_id=" + group_keyword_id,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    $("#SettingData .btnAddKeyword").attr("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                    $("#SettingData .btnAddKeyword").attr("disabled",false);
                },
                success : function(res) {
                    if(res.status) {
                        $("#"+target).find('.inputAddList').val("");

                        var _value_ = res.keyword_id;
                        var _parent_ = res.group_keyword_id;

                        var params = ' value="'+_value_+'" parent="'+_parent_+'" ';
                        var keylink = '<a href="'+urlbase+'setting/setting_filter/'+res.keyword_id+'" class="fancybox link-setting">'+inputAddList+'</a>';
                        $("#"+target+" .to_do").append('<div class="col-md-4 col-sm-6 col-xs-12" style="padding-left: 0px;"><p><input name="keyword_id[]" type="checkbox" class="flat" "'+params+'"> '+keylink+'</p></div>');
                        $("#"+target+" .to_do div:last").hide().fadeIn("slow");
                        $("#"+target+" input.flat:last").iCheck({
                            checkboxClass: "icheckbox_flat",
                            radioClass: "iradio_flat"
                        });
                        $("#"+target+" input.flat:last").iCheck("check");

                        var key_parent = $("#formAddKeyword input[name^=keyword_id][value="+res.keyword_id+"]").parents("p");
                        
                        $(key_parent).find(".flag").remove();
                        if($("#formAddKeyword input[name=thai_only]").is(":checked")) {
                            $(key_parent).find('a').append(' <i class="flag flag-th"></i>');
                        }

                        $(key_parent).find(".fa").remove();
                        if($("#formAddKeyword input[name=primary_keyword]").is(":checked")) {
                            $(key_parent).find('a').append(' <i class="fa fa-star" style="color: #FFD91E;"></i>');
                        }

                        $("#formAddKeyword input[name=thai_only]").attr("checked",false);
                        $("#formAddKeyword input[name=primary_keyword]").attr("checked",false);
                        load_fancybox();
                    } else {
                        dialog_error(res.message);
                    }
                    $.fancybox.hideLoading();
                    set_color_company_keyword();
                    $("#SettingData .btnAddKeyword").attr("disabled",false);
                }
            });
        }
    });

    $("#SettingData button.btnDelKeyword").click(function(){
        dialog_confirm("คุณต้องการลบ Keyword คุณยืนยันที่จะลบหรือไม่","cmd_del_keyword();");
    });

    $("#SettingData button.btnSaveKeyword").click(function(){
        cmd_save_keyword();
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

     // Add It
     $("#SettingData .btnImportLinkurl").click(function(){
        $('#formImportUrl');
        $.fancybox({
            'width': 400,
            'height': 150,
            'autoSize': false,
            'href': "#showLinkImport",
            'padding': 20,
            'closeBtn': true
        });
    });

    $(document).delegate(".btn-import-file-url","click",function(){
        if($("#file_import_url").val()=="") {
            alert("Please choose your file.");
        } else {
            $("#formImportUrl").submit();
        }
    });

    $('#formImportUrl').submit(function(event) {

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

    $('#fake_news .input-group .form-control').keypress(function(event){
        if(event.keyCode === 13){//Enter key pressed
            $('#fake_news .btn-default').click();//Trigger search button click event
        }
     });

    $('#fake_news .btn-default').click(function() {
        var url_news = $('#fake_news input[name=url_news]').val();
        var data = $('#fake_news :input').serialize();
        var url = urlbase+"setting/check_fake_news";
        // alert(url_news);
        $('#fake_news input[name=url_news]').val("")
        $('#fake_news #url_news').attr("placeholder", url_news);

        $.ajax({
            type : 'post',
            dataType : 'json',
            data: data,
            url: url,
            timeout: 100000,
            beforeSend: function() {
                // setting a timeout
                $('#loading_fake').show();
                $('#fake_massage').hide();
            },
            error: function() {
                $("#massage_return").text("ERROR! Make sure you have entered a valid URL.");
            },
            success : function(res) {
                // alert(res.fake);
                $('#loading_fake').hide();
                $('#fake_massage').show();
                if(res.status == false){
                    $("#massage_return1").text("Error!").css('color', '#f8b32d');
                    $("#massage_return2").text(res.message).css('color', '#f8b32d');
                    $("#head_Fake_news").removeClass('panel-default').addClass('panel-warning');
                    $("#head_Fake_news").removeClass('panel-danger').addClass('panel-warning');
                    $("#head_Fake_news").removeClass('panel-success').addClass('panel-warning');
                }else {
                    if(res.fake == false){
                        $("#massage_return1").text("REAL").css('color', '#4aa23c');
                        $("#massage_return2").text(res.message).css('color', '#4aa23c');
                        $("#head_Fake_news").removeClass('panel-default').addClass('panel-success');
                        $("#head_Fake_news").removeClass('panel-warning').addClass('panel-success');
                        $("#head_Fake_news").removeClass('panel-danger').addClass('panel-success');
                        // alert(res.fake);
                    }else{
                        $("#massage_return1").text("FAKE!").css('color', '#f33923');
                        $("#massage_return2").text(res.message).css('color', '#f33923');
                        $("#head_Fake_news").removeClass('panel-default').addClass('panel-danger');
                        $("#head_Fake_news").removeClass('panel-warning').addClass('panel-danger');
                        $("#head_Fake_news").removeClass('panel-success').addClass('panel-danger');
                    }
                }
            }
        });
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
    // ลบ group keyword , keyword และข้อมูลทั้งหมดที่เกี่ยวข้องออกไป
    // var url  = urlbase + "setting/cmdDelGroupKeyword";
    // var data = $("#formAddGroupKeyword").serialize();

    // $.ajax({
    //     type : 'post',
    //     dataType : 'json',
    //     data: data,
    //     url: url,
    //     beforeSend: function() {
    //         $.fancybox.showLoading();
    //     },
    //     error: function() {
    //         $.fancybox.hideLoading();
    //         dialog_error("No internet connection");
    //     },
    //     success : function(res) {
    //         if(res.status) {
    //             window.location.reload(0);
    //         } else {
    //             dialog_error(res.message);
    //         }
    //         $.fancybox.hideLoading();
    //     }
    // });

    // อัปเดต status ให้เป็น inactive แต่ข้อมูลที่เกี่ยวข้องทั้งหมดยังอยู่
    var url  = urlbase+"setting/cmdUpdateGroupKeyword_status";
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
    // ลบ keyword และข้อมูลทั้งหมดที่เกี่ยวข้องออกไป
    // var url  = urlbase+"setting/cmdDelKeyword";
    // var data = $("#formAddKeyword").serialize();

    // $.ajax({
    //     type : 'post',
    //     dataType : 'json',
    //     data: data,
    //     url: url,
    //     beforeSend: function() {
    //         $.fancybox.showLoading();
    //     },
    //     error: function() {
    //         $.fancybox.hideLoading();
    //         dialog_error("No internet connection");
    //     },
    //     success : function(res) {
    //         if(res.status) {
    //             window.location.reload(0);
    //         } else {
    //             dialog_error(res.message);
    //         }
    //         $.fancybox.hideLoading();
    //     }
    // });

    // อัปเดต status ให้เป็น inactive แต่ข้อมูลที่เกี่ยวข้องทั้งหมดยังอยู่
    var url  = urlbase+"setting/cmdUpdateKeyword_status";
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

function cmd_del_group_categories() {
    // อัปเดต status ให้เป็น inactive แต่ข้อมูลที่เกี่ยวข้องทั้งหมดยังอยู่
    var url  = urlbase+"setting/cmdUpdateGroupKeyword_status";
    var data = $("#formAddGroupKeyword_categories").serialize();

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

function cmd_del_categories() {
    // อัปเดต status ให้เป็น inactive แต่ข้อมูลที่เกี่ยวข้องทั้งหมดยังอยู่
    var url  = urlbase+"setting/cmdUpdateCategories_status";
    var data = $("#formAddCategories").serialize();

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

function cmd_del_keyword_categories() {
    // ลบ keyword และข้อมูลทั้งหมดที่เกี่ยวข้องออกไป
    // var url  = urlbase+"setting/cmdDelKeyword";
    // var data = $("#formAddKeyword").serialize();

    // $.ajax({
    //     type : 'post',
    //     dataType : 'json',
    //     data: data,
    //     url: url,
    //     beforeSend: function() {
    //         $.fancybox.showLoading();
    //     },
    //     error: function() {
    //         $.fancybox.hideLoading();
    //         dialog_error("No internet connection");
    //     },
    //     success : function(res) {
    //         if(res.status) {
    //             window.location.reload(0);
    //         } else {
    //             dialog_error(res.message);
    //         }
    //         $.fancybox.hideLoading();
    //     }
    // });
    
    // อัปเดต status ให้เป็น inactive แต่ข้อมูลที่เกี่ยวข้องทั้งหมดยังอยู่
    var url  = urlbase+"setting/cmdUpdateKeyword_status";
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

function cmd_save_keyword() {
    var url  = urlbase+"setting/cmdUpdateKeyword_status";
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

var txtColor = ["#2D6A4F", "#308274", "#2C7078", "#2E6C8F", "#2A4C85", "#2A369C", "#392791", "#5F25A8", "#7B239E", "#B31FB5", "#AB1D67", "#C2171A", "#B82D16", "#CF470E", "#C45E0E", "#DB8104", "#D19004", "#E8B707", "#DEC507", "#F5F105", "#A7EB05", "#4ED406", "#08F607", "#04DE42", "#05FA90", "#07E3C2"];
var txtComColor = [];
var txtGroupColor = [];

function set_color_company_keyword()
{
    $("#formCompanyList .to_do li").each(function(index){
        var color = txtColor[index];
        var parent = $(this).find("input[name^='company_keyword_id']").val();
        $(this).find("p").css("color", color);
        txtComColor[parent] = color;
    });
    set_color_group_keyword();
    set_color_group_keyword_categories();
}

function set_color_group_keyword()
{
    $("#formAddGroupKeyword .to_do li").each(function(){
        var index = $(this).find("input[name^='group_keyword_id']").attr("parent");
        var color = txtComColor[index];
        var parent = $(this).find("input[name^='group_keyword_id']").val();
        $(this).find("p").css("color", color);
        txtGroupColor[parent] = color;
    });
    set_color_keyword_keyword();
}

function set_color_group_keyword_categories() {
    $("#formAddGroupKeyword_categories .to_do li").each(function(){
        var index = $(this).find("input[name^='group_keyword_id']").attr("parent");
        var color = txtComColor[index];
        var parent = $(this).find("input[name^='group_keyword_id']").val();
        $(this).find("p").css("color", color);
        txtGroupColor[parent] = color;
    });

    set_color_categories();
}

// function set color category name
function set_color_categories()
{
    $("#formAddCategories .to_do li").each(function(index){
        var index = $(this).find("input[name^='categories_id']").attr("parent");
        var color = txtGroupColor[index];
        var parent = $(this).find("input[name^='categories_id']").val();
        $(this).find("p").css("color", color);
        txtGroupColor[parent] = color;
    });

    set_color_keyword_keyword();
    set_color_choose_keyword();
}
// end function set_color_categories

function set_color_keyword_keyword()
{
    $("#formAddKeyword .to_do div").each(function(index){
        var index = $(this).find("input[name^='keyword_id']").attr("parent");
        var color = txtGroupColor[index];
        $(this).find("a").css("color", color);
    });
}

function set_color_choose_keyword() {
    $("#formChooseKeyword .to_do div").each(function(index){
        var index = $(this).find("input[name^='Radios']").attr("parent");
        var color = txtGroupColor[index];
        $(this).find("p").css("color", color);
    });
}