var numsize = 10;
var app_breadcrumb = [];
var app_mode = {};
var app_list = {};
var app_searchForm = [];
var app_post = [];
var app_validate = {};
var app_tabForm = [];

var myApp = angular.module('myApp',[]);
myApp.controller('breadcrumbCtrl', function($scope) {
	$scope.breadcrumb = app_breadcrumb;
});
myApp.controller('listCtrl', function($scope) {
	$scope.refid = app_list.refid;
	$scope.ref_name = app_list.ref_name;
	$scope.ref_back = app_list.ref_back;
	$scope.title = app_list.title;
	$scope.totalpage = app_list.totalpage;
	$scope.pagesize = app_list.pagesize;
	$scope.orderby = app_list.orderby;
	$scope.sorting = app_list.sorting;
	$scope.rows = app_list.rows;
	$scope.rows_all = app_list.rows;
	$scope.rows_publish = app_list.rows_publish;
	$scope.rows_modified = app_list.rows_modified;
	$scope.rows_unpublish = app_list.rows_unpublish;
	$scope.tbwidth = app_list.tbwidth;
	$scope.module = app_list.module;
	$scope.column = app_list.column;
	$scope.items = app_list.items;
	$scope.searchForm = app_searchForm;
	$scope.post = app_post;

	$scope.control = true;
	$scope.checkbox_mode = app_mode.checkbox_mode;
	$scope.publish_mode = app_mode.publish_mode;
	$scope.control_mode = app_mode.control_mode;
	$scope.add_mode = app_mode.add_mode;
	$scope.edit_mode = app_mode.edit_mode;
	$scope.delete_mode = app_mode.delete_mode;
	$scope.display_mode = app_mode.display_mode;
	$scope.log_mode = app_mode.log_mode;

	$scope.setValue = function(value) {
		$scope.items = value;
	}
	$scope.formatValue = function(value,type)
	{
		return formatValue(type,value);
	}
	$scope.getObj = function(obj,name)
	{
		return getObj(obj,name);
	}
	$scope.setRefresh = function(url)
	{
		window.location.href = url;
	}
	$scope.createForm = function(searchForm,post,module,child)
	{
		var advanceForm = createForm(searchForm,post,module,child);
		var myEl = angular.element( document.querySelector('#advanceForm' ));
     	myEl.append(advanceForm);  
	}
});
myApp.controller('formCtrl', function($scope) {
	$scope.id = app_list.id;
	$scope.refid = app_list.refid;
	$scope.ref_name = app_list.ref_name;
	$scope.ref_back = app_list.ref_back;
	$scope.title = app_list.title;
	$scope.totalpage = app_list.totalpage;
	$scope.pagesize = app_list.pagesize;
	$scope.orderby = app_list.orderby;
	$scope.sorting = app_list.sorting;
	$scope.rows = app_list.rows;
	$scope.tbwidth = app_list.tbwidth;
	$scope.module = app_list.module;
	$scope.column = app_list.column;
	$scope.items = app_list.items;
	$scope.tabForm = app_tabForm;
	$scope.post = app_post;

	$scope.control = false;
	$scope.checkbox_mode = app_mode.checkbox_mode;
	$scope.publish_mode = app_mode.publish_mode;
	$scope.control_mode = app_mode.control_mode;
	$scope.add_mode = app_mode.add_mode;
	$scope.edit_mode = app_mode.edit_mode;
	$scope.delete_mode = app_mode.delete_mode;
	$scope.display_mode = app_mode.display_mode;
	$scope.log_mode = app_mode.log_mode;
	$scope.action = app_mode.action;

	$scope.setValue = function(value) {
		$scope.items = value;
	}
	$scope.formatValue = function(value,type)
	{
		return formatValue(type,value);
	}
	$scope.getObj = function(obj,name)
	{
		return getObj(obj,name);
	}
	$scope.setRefresh = function(url)
	{
		window.location.href = url;
	}
	$scope.createForm = function(tabid,myForm,post,module,child)
	{
		var myForm = createForm(myForm,post,module,child);
		var myEl = angular.element( document.querySelector('#'+tabid));
     	myEl.append(myForm);  
	}
});
// angular.element(document).ready(function() { 
//     angular.bootstrap(document.getElementById('myApp'), ['myApp']);
// });

function getObj(obj,name)
{
	for(var o in obj) {
		if(o==name) {
			return obj[o];
		}
	}
	return  "";
}

function formatValue(type,value)
{
	if(type=="date" || type=="datetime") {
		if(value=="" || value==null) {
			value = "";
		} else {
			var datetime = value.split(' ');
			var date = datetime[0].split('-');
			if(datetime.length>1) var time = datetime[1].split(':');
			if(type=="date") {
				value = date[2]+"/"+date[1]+"/"+date[0];
			} else {
				value = date[2]+"/"+date[1]+"/"+date[0]+ " "+time[0]+":"+time[1];
			}
		}
	}
	return value;
}

function check_all(obj)
{
	var check = $(obj).attr("id");
	if($(obj).is(":checked")) {
		$("."+check).prop("checked",true);
	} else {
		$("."+check).prop("checked",false);
	}
}

$(function() {

	$(".menu_section li").each(function() {
		var module = $("#module").val();
		if($(this).hasClass(module)) {
			$(this).parents(".parent_menu").addClass("active");
			$(this).parents(".child_menu").show();
			$(this).addClass("current-page");
		}	
	});

	$(".menu_section .parent_menu").each(function() {
		var obj = this;
		var i = false;
		$(obj).find("li").each(function() {
			if($(this).css("display")=="block") {
				i = true;
				return false;
			}
		});
		if(i) $(obj).attr("style","display:block !important");
	});

	$('#myTab a.active').tab('show');
	$('#myTab a').click(function (e) {
  		e.preventDefault()
  		$(this).tab('show');
	});

	$('#myTabSearch a.active').tab('show');
	$('#myTabSearch a').click(function (e) {

  		e.preventDefault()
  		$(this).tab('show');
  		$("#TabSelected").val($(this).attr("href"));
  		
	});

	// tinymce.init({
	//     selector: "textarea.editor"
	// });

	$(document).delegate("input[type=file]",'change',function(){
		var start = ($(this).val().length-3);
		var end = $(this).val().length;
		var type = $(this).val().substring(start,end);
		var start2 = ($(this).val().length-4);
		var end2 = $(this).val().length;
		var type2 = $(this).val().substring(start2,end2);
		var filetype = $(this).attr("filetype");
		var isError = true;
		if(filetype!="") {
			$.each(filetype.split("|"),function(index,value){
				if(value==type || value==type2) {
					isError = false;
					return false;
				}
			});

			if(isError) {
				alert("Please upload file extensions "+filetype);
				$(this).val('');
			}
		}
	});

	if($("#listCtrl").size()>0) {
		var scope = angular.element('#listCtrl').scope();
	    scope.$apply(function() {
	        scope.createForm(app_searchForm,app_post,app_list.module,false);
	    });
	    create_lookup();
	}

	if($("#formCtrl").size()>0) {
		var scope = angular.element('#formCtrl').scope();
	    scope.$apply(function() {
	    	for(i in app_tabForm) {
	    		var item = app_tabForm[i];
	    		var tabid = "tab-"+item.id;
	        	scope.createForm(tabid,item.form,app_post,app_list.module,false);
	        }
	    });
	    create_lookup();
	    load_validate('myForm',app_validate);
	    $("#formCtrl .child").each(function(index, item) {
        	load_list_child(this);
   		});
   		$(".form_display select,.form_display textarea").prop('disabled', true);
		$(".form_display input").prop('disabled', true);
		$(".form_display input[type=hidden]").prop('disabled', false);
		$(".form_display .glyphicon-remove").hide();
	}

    load_date_picker();
	load_fancybox();

	if($(window).width()  < 991) {
		if($("#listCtrl").size()>0) {
			$("#listCtrl .table-responsive").width($(window).width() - 80);
		}
	} 
	$(window).resize(function(){
		if($("#listCtrl").size()>0) {
			if($(window).width()  < 991) {
				$("#listCtrl .table-responsive").width($(window).width() - 80);
			} else {
				$("#listCtrl .table-responsive").width("100%");
			}
		}
	});


	$("#menu_toggle").click(function(){
		if( $("body").hasClass("nav-md") ) {
			$(".parent_menu.current-page .child_menu").show();
			close_menu(false);
		} else {
			$(".parent_menu.current-page .child_menu").hide();
			close_menu(true);
		}
	});

	if( $("body").hasClass("nav-sm") ) {
		$(".child_menu").removeAttr("style");
		$(".parent_menu.active").addClass("current-page");
		$(".parent_menu").removeClass("active");
	}

	$("#item-sys_action a").click(function(){
		$("#item-sys_action li").removeClass("active");
		$(this).parents("li").addClass("active");
		$("#formSearch #sys_action").val($(this).attr("rel"));
		$("#thispage").val(1);
		var sys_action = {
        	name: "sys_action",
        	value: $("#sys_action").val()
    	};
    	remove_form_post(form_post,'sys_action');
    	form_post.push(sys_action);

    	var new_rows = $(this).find(".badge").text();
    	var scope = angular.element('#listCtrl').scope();
	    scope.$apply(function() {
	    	scope.rows = new_rows;
	        scope.totalpage = Math.ceil(new_rows/scope.pagesize);
	    });

   		load_page_ajax();
	});

	disable_value();
	$('#myTabSearch a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		disable_value();
	});
	
});

function disable_value()
{
	$("#myTabSearchContent .tab-pane input").attr("disabled",false);
	$("#myTabSearchContent .tab-pane select").attr("disabled",false);
	$("#myTabSearchContent .tab-pane textarea").attr("disabled",false);
	$("#myTabSearchContent .tab-pane:hidden input").attr("disabled",true);
	$("#myTabSearchContent .tab-pane:hidden select").attr("disabled",true);
	$("#myTabSearchContent .tab-pane:hidden textarea").attr("disabled",true);
}

function close_menu(close)
{
	var url = urlpath+"authen/close_menu";
	var data = {"close":close};

	$.post(url,data,function(res) {
	},'json');
}



function load_date_picker()
{
    $("input.date").datetimepicker({
        format: 'd/m/Y',
        timepicker:false,
        closeOnDateSelect:true,
        scrollMonth : false,
        scrollInput : false,
        onSelectDate : function(ct,obj){
        	$(obj).parents('.form-group').removeClass('has-error has-feedback').addClass('has-success');
        	$(obj).parents('.form-group').find('.help-block').hide();
        }
    });

    $("input.datetime").datetimepicker({
        format: 'd/m/Y H:i',
        scrollMonth : false,
        scrollInput : false,
        onSelectDate : function(ct,obj){
        	$(obj).parents('.form-group').removeClass('has-error has-feedback').addClass('has-success');
        	$(obj).parents('.form-group').find('.help-block').hide();
        }
    });
}

function default_date_picker()
{
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	var hh = today.getHours();
	var ii = today.getMinutes();
	var year = 10;

	if(dd<10) dd='0'+dd;
	if(mm<10)  mm='0'+mm;
	if(hh<10)  hh='0'+hh;
	if(ii<10)  ii='0'+ii;

	var startdate = dd+'/'+mm+'/'+yyyy;
	var enddate = dd+'/'+mm+'/'+(yyyy+year);
	var starttime = dd+'/'+mm+'/'+yyyy+" "+hh+":"+ii;
	var endtime = dd+'/'+mm+'/'+(yyyy+year)+" "+hh+":"+ii;

	$("input.date.startdate").each(function() {
        if( $(this).val() == "" ) {
            $(this).val(startdate);
        }
    });
    $("input.date.enddate").each(function() {
        if( $(this).val() == "" ) {
            $(this).val(enddate);
        }
    });
    $("input.datetime.startdate").each(function() {
        if( $(this).val() == "" ) {
            $(this).val(starttime);
        }
    });
    $("input.datetime.enddate").each(function() {
        if( $(this).val() == "" ) {
            $(this).val(endtime);
        }
    });
}

function load_validate(FormID,validate)
{
	$("#"+FormID).bootstrapValidator({
		fields: validate
	});
}

$(function () {
	$('.editor').each(function(){
		var rows = $(this).attr("rows");
		var height = (rows!="") ? (rows*100) : 250;
	    CKEDITOR.replace($(this).attr('id'), {
	    	height: height,
	        toolbarGroups: [
	            { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
	            { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
	            { name: 'links' },
	            { name: 'tools' },
	            { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
	            { name: 'others' },
	            '/',
	            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	            { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	            { name: 'styles' },
	            { name: 'insert', groups: [ 'Image']},
	            { name: 'colors' }
	        ]
	    });
	});
	update_ckeditor();
});

function update_ckeditor()
{
    for ( instance in CKEDITOR.instances )
    {
        CKEDITOR.instances[instance].on('change', function() { 
				CKEDITOR.instances[instance].updateElement();
		});
    }
}

function number_format(value,dec)
{
    return value.toFixed(dec).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}