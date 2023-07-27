var myApp_child = angular.module('myApp-child',[]);
myApp_child.controller('listCtrl-child', function($scope) {
    $scope.id = app_list.id;
    $scope.title = app_list.title;
    $scope.totalpage = app_list.totalpage;
    $scope.pagesize = app_list.pagesize;
    $scope.orderby = app_list.orderby;
    $scope.sorting = app_list.sorting;
    $scope.rows = app_list.rows;
    $scope.tbwidth = app_list.tbwidth;
    $scope.module = app_list.module;
    $scope.child = app_list.child;
    $scope.column = app_list.column;
    $scope.items = app_list.items;

    $scope.control = true;
    $scope.checkbox_mode = app_mode.checkbox_mode;
    $scope.publish_mode = app_mode.publish_mode;
    $scope.control_mode = app_mode.control_mode;
    $scope.add_mode = app_mode.add_mode;
    $scope.edit_mode = app_mode.edit_mode;
    $scope.delete_mode = app_mode.delete_mode;
    $scope.display_mode = app_mode.display_mode;

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
myApp_child.controller('formCtrl-child', function($scope) {
    $scope.id = app_list.id;
    $scope.title = app_list.title;
    $scope.totalpage = app_list.totalpage;
    $scope.pagesize = app_list.pagesize;
    $scope.orderby = app_list.orderby;
    $scope.sorting = app_list.sorting;
    $scope.rows = app_list.rows;
    $scope.tbwidth = app_list.tbwidth;
    $scope.module = app_list.module;
    $scope.child = app_list.child;
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
    $scope.createForm = function(myForm,post,module,child)
    {
        var myForm = createForm(myForm,post,module,child);
        var myEl = angular.element( document.querySelector('#tabForm-child'));
        myEl.append(myForm);  
    }
});

$(function(){
    if($("#formCtrl-child").size()>0) {
        var scope = angular.element('#formCtrl-child').scope();
        scope.$apply(function() {
            for(i in app_tabForm) {
                var item = app_tabForm[i];
                scope.createForm(item.form,app_post,app_list.child,true);
            }
        });
        create_lookup();
        load_validate('childForm-'+app_child,app_validate);
    }
})

function load_list_child(obj) {
    var child = $(obj).attr("child");
    var url = $("#module").val() + "/" + child;
    if(child==null) {
        child = $("#child").val();
        var url = $("#module", window.parent.document).val() + "/" + child;
        var form_child = $("#myForm", window.parent.document).serialize();
        url = url + "?"+form_child;
        $("#child_"+child, window.parent.document).html('<iframe id="iframe-'+child+'" src="'+url+'" width="100%" scrolling="auto" frameborder="0"></iframe>');

    } else {
        var url = $("#module").val() + "/" + child;
        var form_child = $("#myForm").serialize();
        url = url + "?"+form_child;
        $(obj).html('<iframe id="iframe-'+child+'" src="'+url+'" width="100%" scrolling="auto" frameborder="0"></iframe>');
    }

    // $.ajax({
    //     dataType: "html",
    //     type: "POST",
    //     url: urlpath + url,
    //     data: form_child,
    //     beforeSend: function() {

    //     },
    //     success: function(res) {
    //         $(obj).html(res);
    //         // angular.element(document).ready(function() { 
    //         //     angular.bootstrap(document, ['myApp']);
    //         // });
    //     }
    // });
}

$(function() {

    $("#myApp-child .btn-sort").click(function() {

        var sorting = $(this).attr("sorting");
        var child = app_list.child;

        if ($(this).hasClass("desc")) {
            $("#myApp-child .btn-sort").removeClass("desc").removeClass("asc");
            $(this).addClass("asc");
            $('#myApp-child input[name=orderby_'+child+']').val('asc');
            $('#myApp-child input[name=sorting_'+child+']').val(sorting);
        } else {
            $("#myApp-child .btn-sort").removeClass("desc").removeClass("asc");
            $(this).addClass("desc");
            $('#myApp-child input[name=orderby_'+child+']').val('desc');
            $('#myApp-child input[name=sorting_'+child+']').val(sorting);
        }

        $("#myApp-child .pagination li").removeClass("active");
        $("#myApp-child .pagination li.first").addClass("active");

        load_child_page(child);

    });

});

function load_child_page(child) {

    var url = $("#module").val() + "/" + child + "/ajaxList";
    var child_post = [];

    var orderby = {
        name: "orderby",
        value: $("input[name=orderby_" + child + "]").val()
    };
    var sorting = {
        name: "sorting",
        value: $("input[name=sorting_" + child + "]").val()
    };
    var thispage = {
        name: "thispage",
        value: $("input[name=thispage_" + child + "]").val()
    };
    var pagesize = {
        name: "pagesize",
        value: $("input[name=pagesize_" + child + "]").val()
    };
    var id = {
        name: "id",
        value: $("#id").val()
    };

    remove_form_post(form_post,'orderby');
    child_post.push(orderby);
    remove_form_post(form_post,'sorting');
    child_post.push(sorting);
    remove_form_post(form_post,'thispage');
    child_post.push(thispage);
    remove_form_post(form_post,'pagesize');
    child_post.push(pagesize);
    remove_form_post(form_post,'id');
    child_post.push(id);

    $.ajax({
        dataType: "json",
        type: "POST",
        url: urlpath + url,
        data: child_post,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        success: function(res) {

            $.fancybox.hideLoading();

            var scope = angular.element('#listCtrl-child').scope();
            scope.$apply(function() {
                scope.setValue(res);
            });

            load_on_child_success(res);
            show_child_page(child);
            dialog_child();
            load_fancybox();

        }
    });
}

function load_on_child_success(res)
{
    /*custom*/
}

function remove_form_post(obj,name)
{
    for (var key in obj) {
    if (!obj.hasOwnProperty(key)) continue;
        var obj2 = obj[key];
        for (var prop in obj2) {
            if(!obj2.hasOwnProperty(prop)) continue;

            if(prop=='name' && obj2[prop]==name) {
                delete obj[key];
            }
        }
    }
}

function show_child_page(child) {
    var html = "";
    var totalpage = $('input[name=totalpage_' + child + ']').val();
    var thispage = $('input[name=thispage_' + child + ']').val();

    var size = Math.ceil(thispage / numsize) * numsize;
    var start = size - (numsize - 1);
    var limit = (size > totalpage) ? totalpage : size;


    html += '<li><a href="javascript:;" class="btn-first" onclick="move_child_page(-1,\'first\',\'' + child + '\')">&laquo;</a></li>';
    html += '<li><a href="javascript:;" class="btn-prev" onclick="move_child_page(-1,\'\',\'' + child + '\')">&lt;</a></li>';

    for (var i = start; i <= limit; i++) {
        var first = (i == 1) ? ' first' : '';
        if (thispage == i)
            html += '<li class="active' + first + '"><a href="javascript:;">' + i + '</a></li>';
        else
            html += '<li><a href="javascript:;" onclick="get_child_page(' + i + ',\'' + child + '\')">' + i + '</a></li>';
    }

    html += '<li><a href="javascript:;" class="btn-next" onclick="move_child_page(1,\'\',\'' + child + '\')">&gt;</a></li>';
    html += '<li><a href="javascript:;" class="btn-last" onclick="move_child_page(1,\'last\',\'' + child + '\')">&raquo;</a></li>';

    $('#child_' + child + ' .pagination').html(html);
}

function get_child_page(thispage, child) {
    if (thispage <= $('input[name=totalpage_' + child + ']').val()) {
        var endpage = $('select[name=pagesize_' + child + ']').val();
        set_child_page(thispage, endpage, child);
        load_child_page(child);
    }
}

function set_child_page(thispage, endpage, child) {
    var startpage = (thispage * endpage) - endpage;
    $('input[name=startpage_' + child + ']').val(startpage);
    $('input[name=endpage_' + child + ']').val(endpage);
    $('input[name=thispage_' + child + ']').val(thispage);
}

function move_child_page(page, move, child) {
    var params = move;
    var endpage = $('select[name=pagesize_' + child + ']').val();
    var totalpage = parseInt($('input[name=totalpage_' + child + ']').val(), 10);
    var currentpage = parseInt($('input[name=thispage_' + child + ']').val(), 10);
    var thispage = currentpage + eval(page);

    if (params == 'first' && currentpage != 1) {
        thispage = 1;
        set_child_page(thispage, endpage, child)
        load_child_page(child);
    } else if (params == 'last' && totalpage != currentpage) {
        thispage = totalpage;
        set_child_page(thispage, endpage, child)
        load_child_page(child);
    } else if (totalpage != currentpage && page == 1) {
        set_child_page(thispage, endpage, child)
        load_child_page(child);
    } else if (currentpage != 1 && page == -1) {
        set_child_page(thispage, endpage, child)
        load_child_page(child);
    }
}

function dialog_child() {
    dialog_delete_child();
    dialog_publish_child();
    alert_save_publish_child();
    alert_save_child();
    alert_delete_child();
    alert_publish_child();
    alert_unpublish_child();
    alert_publish_all_child();
    alert_publish_list_child();
    alert_delete_list_child();
    alert_delete_file_child();
    save_child();
    loadToolTip_child();
}

function dialog_delete_child() {
    $(".btn-delete-all-child").click(function() {
        _child_ = $(this).attr("child");
        if ($(".check-all-" + _child_).is(":checked")) {
            $('#alert-delete-child').modal('show');
        } else {
            $('#alert-box .modal-title').html("Please select at least one list.");
            $('#alert-box').modal('show');
        }
    });
}

function dialog_publish_child() {
    $(".btn-publish-all-child").click(function() {
        _child_ = $(this).attr("child");
        if ($(".check-all-" + _child_).is(":checked")) {
            $('#alert-publish-all-child').modal('show');
        } else {
            $('#alert-box .modal-title').html("Please select at least one list.");
            $('#alert-box').modal('show');
        }
    });
}

function loadToolTip_child() {
    $('.btn-delete-child,.btn-publish-child,.btn-created-child,.btn-modified-child,.btn-display-child,.btn-edit-child,.btn-delete-file-child').tooltip({container: 'body'});
    $('.btn-delete-child,.btn-publish-child,.btn-created-child,.btn-modified-child,.btn-display-child,.btn-edit-child,.btn-delete-file-child').tooltip('show');
    $('.btn-delete-child,.btn-publish-child,.btn-created-child,.btn-modified-child,.btn-display-child,.btn-edit-child,.btn-delete-file-child').tooltip('hide');
}

function alert_save_child() {
    var obj = {};

    obj.title = "Confirm save your data ?";
    obj.id = "alert-save-child";
    //obj.body = "What do you want to do next ?";
    obj.button = [];
    obj.button.push({
        "label": "Save to list",
        "func": "send_action_child('cmdSaveToList');",
        "Class": "btn-default"
    });
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdSaveToForm');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-save-child', 'click', function() {
        _child_ = $(this).attr("child");
        if($("#childForm-" + _child_).attr('novalidate')=="novalidate") {
            $("#childForm-" + _child_).bootstrapValidator('validate');
        }
        var rel = $(this).attr("rel");
        var isError = $("#childForm-" + _child_ + " .form-group").hasClass("has-error");
        if (!isError) {
            $('#alert-save-child').modal('show');
        }
    });
}

function alert_save_publish_child() {
    var obj = {};

    obj.title = "Confirm save your data ?";
    obj.id = "alert-save-publish-child";
    //obj.body = "What do you want to do next ?";
    obj.button = [];
    obj.button.push({
        "label": "Publish to list",
        "func": "send_action_child('cmdSavePublishToList');",
        "Class": "btn-default"
    });
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdSavePublishToForm');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-save-publish-child', 'click', function() {
        _child_ = $(this).attr("child");
        if($("#childForm-" + _child_).attr('novalidate')=="novalidate") {
            $("#childForm-" + _child_).bootstrapValidator('validate');
        }
        var rel = $(this).attr("rel");
        var isError = $("#childForm-" + _child_ + " .form-group").hasClass("has-error");
        if (!isError) {
            $('#alert-save-publish-child').modal('show');
        }
    });
}

function alert_delete_child() {
    var obj = {};

    obj.title = "Confirm delete your data ?";
    obj.id = "alert-delete-child";
    obj.button = [];
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdDeleteToList');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-delete-child', 'click', function() {
        _child_ = $(this).attr("child");
        $("input[name$='id[]']").prop("checked", false);
        var rel = $(this).attr("rel");
        $('#alert-delete-child').modal('show');
        $("input[name$='id[]'][value=" + rel + "]").prop("checked", true);
    });
}

function alert_publish_child() {
    var obj = {};

    obj.title = "Confirm publish your data ?";
    obj.id = "alert-publish-child";
    obj.button = [];
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdPublishToList');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-publish-child', 'click', function() {
        _child_ = $(this).attr("child");
        $("input[name$='id[]']").prop("checked", false);
        var rel = $(this).attr("rel");
        $('#alert-unpublish-child').modal('show');
        $("input[name$='id[]'][value=" + rel + "]").prop("checked", true);
    });
}

function alert_unpublish_child() {
    var obj = {};

    obj.title = "Confirm unpublish your data ?";
    obj.id = "alert-unpublish-child";
    obj.button = [];
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdUnPublishToList');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-unpublish-child,.btn-created-child,.btn-modified-child', 'click', function() {
        _child_ = $(this).attr("child");
        $("input[name$='id[]']").prop("checked", false);
        var rel = $(this).attr("rel");
        $('#alert-publish-child').modal('show');
        $("input[name$='id[]'][value=" + rel + "]").prop("checked", true);
    });
}

function alert_publish_all_child() {
    var obj = {};

    obj.title = "Confirm publish or unpublish your data ?";
    obj.id = "alert-publish-all-child";
    obj.button = [];
    obj.button.push({
        "label": "Publish",
        "func": "send_action_child('cmdPublishToList');",
        "Class": "btn-dark"
    });
    obj.button.push({
        "label": "UnPublish",
        "func": "send_action_child('cmdUnPublishToList');",
        "Class": "btn-default"
    });
    dialogCreate(obj);
}

function alert_delete_list_child() {
    var obj = {};

    obj.title = "Confirm delete your data ?";
    obj.id = "alert-delete-list-child";
    obj.button = [];
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdDeleteToList');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-delete-list-child', 'click', function() {
        _child_ = $(this).attr("child");
        var rel = $(this).attr("rel");
        $('#alert-delete-list-child').modal('show');
    });
}

function alert_publish_list_child() {
    var obj = {};

    obj.title = "Confirm publish your data ?";
    obj.id = "alert-publish-list-child";
    obj.button = [];
    obj.button.push({
        "label": "Publish to list",
        "func": "send_action_child('cmdPublishToList');",
        "Class": "btn-default"
    });
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdPublishToForm');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-publish-list-child', 'click', function() {
        _child_ = $(this).attr("child");
        var rel = $(this).attr("rel");
        $('#alert-publish-list-child').modal('show');
    });
}

function alert_delete_file_child() {
    var obj = {};

    obj.title = "Confirm delete your file ?";
    obj.id = "alert-delete-file-child";
    obj.button = [];
    obj.button.push({
        "label": "OK",
        "func": "send_action_child('cmdDeleteFile');",
        "Class": "btn-dark"
    });
    dialogCreate(obj);

    $(document).delegate('.btn-delete-file-child', 'click', function() {
        _child_ = $(this).attr("child");
        var rel = $(this).attr("rel");
        var ref = $(this).attr("ref");
        $(".form-child #filepath").val(rel);
        $(".form-child #filename").val(ref);
        $('#alert-delete-file-child').modal('show');
    });
}

var _action_child_;
var _child_;

function send_action_child(action) {

    if (action != "") {
        _action_child_ = action;
        if ($('#childForm-' + _child_).size() > 0) {
            $('#childForm-' + _child_).submit();
        } else {
            $('#childList-' + _child_).submit();
        }
    }
}

var submitting_child = true;
function save_child() {

    $('.form-child').submit(function(event) {
      
        var url = $("#module").val() + "/" + _child_ + "/";
        var posturl = urlpath + url + _action_child_;

        var child_id = $(this).attr("ref");

        var options = {
            url: posturl,
            type: 'post',
            dataType: 'json',
            clearForm: false,
            resetForm: false,
            timeout: 10000,
            beforeSend: function() {
                $.fancybox.showLoading();
                $(".modal").click();
                $(".modal-backdrop").remove();
                $(".btn").prop("disabled",true);
            },
            success: function(res) {
                submitting_child = true;
                $(".btn").prop("disabled",false);
                var obj = $("#child_" + child_id);
                if (res.status == false) {
                    $('#alert-box .modal-title').html(res.message);
                    $('#alert-box .modal-title').prepend('<span class="glyphicon glyphicon-exclamation-sign" style="color:#FF0000;"></span> ');
                    $('#alert-box').modal('show');
                } else {
                    //parent.$.fancybox.close();
                    setTimeout(function() {
                        load_list_child(obj);
                        if (_action_child_ == "cmdDeleteFile" || _action_child_ == "cmdSaveToForm" || _action_child_ == "cmdSavePublishToForm" || _action_child_ == "cmdPublishToForm") {
                            // $.fancybox({
                            //     'width': 800,
                            //     'autoScale': false,
                            //     'href': res.url,
                            //     'type': 'ajax',
                            //     'padding': 0,
                            //     'closeBtn': false,
                            // });
                            parent.$.fancybox({
                                'width': 800,
                                'autoScale': false,
                                'href': res.url,
                                'type': 'iframe',
                                'padding': 0,
                                'closeBtn': false
                            }).click();
                        } else {
                            parent.$.fancybox.close();
                        }
                    }, 500);
                }
                $.fancybox.hideLoading();
            }
        };

        if(submitting_child) {
            submitting_child = false;
            $(this).ajaxSubmit(options);
        }

        return false;
    });

    load_fancybox();
}


function load_fancybox() {
    $(".btn-add-child,.btn-edit-child").addClass("disabled");

    $('.fancybox').each(function() {
        $(this).fancybox({
            'width': 800,
            'autoScale': false,
            'href': $(this).attr('id'),
            'type': 'ajax',
            'padding': 0,
            'closeBtn': false
        });
    });

    $('.fancybox_iframe').each(function() {
        $(this).on('click',function(e){
            e.preventDefault();
            parent.$.fancybox({
                'width': 800,
                'autoScale': false,
                'href': $(this).attr('href'),
                'type': 'iframe',
                'padding': 0,
                'closeBtn': false
            });
        });
    });


    $(".btn-add-child,.btn-edit-child").removeClass("disabled");
}