$(function() {

    $("#listCtrl .btn-sort,#formCtrl .btn-sort").click(function() {

        var sorting = $(this).attr("sorting");

        if ($(this).hasClass("desc")) {
            $("#listCtrl .btn-sort,#formCtrl .btn-sort").removeClass("desc").removeClass("asc");
            $(this).addClass("asc");
            $("#orderby").val('asc');
            $("#sorting").val(sorting);
        } else {
            $("#listCtrl .btn-sort,#formCtrl .btn-sort").removeClass("desc").removeClass("asc");
            $(this).addClass("desc");
            $("#orderby").val('desc');
            $("#sorting").val(sorting);
        }

        $("#BoxPage .pagination li").removeClass("active");
        $("#BoxPage .pagination li.first").addClass("active");

        load_page_ajax();
    });

});

function load_page_ajax() {
    var url = "";

    if ($('#formSearch').size() > 0) {
        url = $('#formSearch').attr('ref');
    } else {
        url = $('#myForm').attr('ref');
    }

    var orderby = {
        name: "orderby",
        value: $("#orderby").val()
    };
    var sorting = {
        name: "sorting",
        value: $("#sorting").val()
    };
    var thispage = {
        name: "thispage",
        value: $("#thispage").val()
    };
    var pagesize = {
        name: "pagesize",
        value: $("#pagesize").val()
    };

    remove_form_post(form_post,'orderby');
    form_post.push(orderby);
    remove_form_post(form_post,'sorting');
    form_post.push(sorting);
    remove_form_post(form_post,'thispage');
    form_post.push(thispage);
    remove_form_post(form_post,'pagesize');
    form_post.push(pagesize);

    var $scope = null;

    if($('#listCtrl').size()>0) {
        $scope = angular.element('#listCtrl').scope();
    } else if ($('#formCtrl').size()>0) {
        $scope = angular.element('#formCtrl').scope();
    }

    $.ajax({
        dataType: "json",
        type: "POST",
        url: urlpath + url,
        data: form_post,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        success: function(res) {

            $.fancybox.hideLoading();

            $scope.$apply(function() {
                $scope.setValue(res);
                loadToolTip();
            });

            load_on_success(res);
            show_page();
            loadToolTip();
            load_fancybox();

        }
    });
}

function load_on_success(res)
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

function show_page() {
    var html = "";
    var totalpage = $('input[name=totalpage]').val();
    var thispage = $('input[name=thispage]').val();

    var size = Math.ceil(thispage / numsize) * numsize;
    var start = size - (numsize - 1);
    var limit = (size > totalpage) ? totalpage : size;


    html += '<li><a href="javascript:;" class="btn-first" onclick="move_page(-1,\'first\')">&laquo;</a></li>';
    html += '<li><a href="javascript:;" class="btn-prev" onclick="move_page(-1)">&lt;</a></li>';

    for (var i = start; i <= limit; i++) {
        var first = (i == 1) ? ' first' : '';
        if (thispage == i)
            html += '<li class="active' + first + '"><a href="javascript:;">' + i + '</a></li>';
        else
            html += '<li><a href="javascript:;" onclick="get_page(' + i + ')">' + i + '</a></li>';
    }

    html += '<li><a href="javascript:;" class="btn-next" onclick="move_page(1)">&gt;</a></li>';
    html += '<li><a href="javascript:;" class="btn-last" onclick="move_page(1,\'last\')">&raquo;</a></li>';

    $('#BoxPage .pagination').html(html);
}

function get_page(thispage) {
    if (thispage <= $('input[id=totalpage]').val()) {
        var endpage = $('select[name=pagesize]').val();
        set_page(thispage, endpage)
        load_page_ajax();
    }
}

function set_page(thispage, endpage) {
    var startpage = (thispage * endpage) - endpage;
    $('input[id=startpage]').val(startpage);
    $('input[id=endpage]').val(endpage);
    $('input[id=thispage]').val(thispage);
}

function move_page(page) {
    var params = move_page.arguments[1];
    var endpage = $('select[name=pagesize]').val();
    var totalpage = parseInt($('input[id=totalpage]').val(), 10);
    var currentpage = parseInt($('input[id=thispage]').val(), 10);
    var thispage = currentpage + eval(page);

    if (params == 'first' && currentpage != 1) {
        thispage = 1;
        set_page(thispage, endpage)
        load_page_ajax()
    } else if (params == 'last' && totalpage != currentpage) {
        thispage = totalpage;
        set_page(thispage, endpage)
        load_page_ajax()
    } else if (totalpage != currentpage && page == 1) {
        set_page(thispage, endpage)
        load_page_ajax()
    } else if (currentpage != 1 && page == -1) {
        set_page(thispage, endpage)
        load_page_ajax()
    }
}

