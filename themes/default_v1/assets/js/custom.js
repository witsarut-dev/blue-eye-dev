var API = null;

/*Share script*/
$(function(){

    load_fancybox();
    check_login_timeout();
    setInterval(function(){ 
        check_login_timeout();
    },1000*60);

    $("#toolPeriod button").click(function(){
        var period = $(this).attr("range");
        if(period!="Custom") {
            $("#toolPeriod input[name=period]").val(period);
            $("#formPeriod").submit();
        }
    });

    $("#btnCustomPeriod").click(function(){
        $("#custom_date").trigger("click");
    });

    if($('#custom_date').size()>0) {

        var minDate = new Date();
        var maxDate = new Date();
        minDate.setMonth(minDate.getMonth( ) - 3);

        $('#custom_date').daterangepicker({
            minDate: minDate,
            maxDate: maxDate,
            locale: {
                format: 'DD/MM/YYYY'
            },
            showDropdowns: true}, function(start, end, label) {
            $("#toolPeriod input[name=period]").val("Custom");
            $("#formPeriod").submit();
        });
    }

    $(document).delegate(".applyBtn","click",function(){
        var start = $("input[name=daterangepicker_start]").val();
        var end = $("input[name=daterangepicker_end]").val();
        $('#custom-period').html(start+ ' - ' + end);
        $("#toolPeriod input[name=period]").val("Custom");
        $("#formPeriod").submit();
    });

    if($('.inputDate').size()>0) {
        $('.inputDate').daterangepicker({
          singleDatePicker: true,
          format: 'DD/MM/YYYY',
          showDropdowns: true,
          calender_style: "picker_4"
        }, function(start, end, label) {
          //console.log(start.toISOString(), end.toISOString(), label);
        });
    }

    $('#clickUpload').bind('click', function(e) {
      $('#Upload').click();
    });

    $(document).delegate('.btnDeletePost,.btnDeletePostDetail', 'click', function() {
        if(window.confirm("คุณยืนยันที่จะลบข้อมูลหรือไม่")) {
            var obj = this;
            if($(obj).hasClass("btnDeletePost")) {
                var post_id = $(obj).parents(".x_content").attr("post-id");
            } else {
                var post_id = $(obj).parents(".user-detail").attr("post-id");
            }
            var url  = urlbase+"realtime/cmdDeletePost";
            $.ajax({
                type : 'post',
                dataType : 'json',
                data: {post_id,post_id},
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("Error");
                },
                success : function(res) {
                    if(res.status) {
                        if($(obj).hasClass("btnDeletePost")) {
                            $(obj).parents(".x_content").slideUp(500,function(){
                                $(obj).remove();
                            });
                        } else {
                            $.fancybox.close();
                            $(".x_content[post-id="+post_id+"]").hide();
                        }
                    }
                    $.fancybox.hideLoading();
                }
            });
        }
    });

    $(document).delegate('.btnBlockPost,.btnBlockPostDetail', 'click', function() {
        if(window.confirm("คุณยืนยันที่จะ Block User หรือไม่")) {
            var obj = this;
            if($(obj).hasClass("btnBlockPost")) {
                var post_id = $(obj).parents(".x_content").attr("post-id");
                var post_block= $(obj).parents(".x_content").attr("post-block");
            } else {
                var post_id = $(obj).parents(".user-detail").attr("post-id");
                var post_block = $(obj).parents(".user-detail").attr("post-block");
            }
            var url  = urlbase+"realtime/cmdBlockPost";
            $.ajax({
                type : 'post',
                dataType : 'json',
                data: {post_id,post_id},
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("Error");
                },
                success : function(res) {
                    if(res.status) {
                        if($(obj).hasClass("btnBlockPost")) {
                            $(".x_content[post-block='"+post_block+"']").slideUp(500,function(){
                                $(obj).remove();
                            });
                        } else {
                            $.fancybox.close();
                            $(".x_content[post-block='"+post_block+"']").hide();
                        }
                    }
                    $.fancybox.hideLoading();
                }
            });
        }
    });

    $(document).delegate('input.flat', 'ifChanged',function() {
        var target = $(this).parents('.x_panel').attr("id");
        if($("#"+target).find("input[type=checkbox]").is(":checked")==false) {
            $("#"+target).find('.fa-trash:first').prop("disabled",true);
        } else {
            $("#"+target).find('.fa-trash:first').prop("disabled",false);
        }

        if(target=="GroupKeywordList" || target=="CompanyList") {
            var objChecked = $("#"+target).find("input[type=checkbox]:checked");
            if($(objChecked).size()=="1") {
                $("#"+target+"Select").html($(objChecked).parents("li").find("p").text());
            } else {
                $("#"+target+"Select").html("");
            }
        }

        var target = $(this).attr("target");
        if(target!="" && target!=null) {
            var parent = $(this).val();
            if($(this).is(":checked")) {
                $("."+target).find("input[parent='"+parent+"']").iCheck('check');
                _parent_ = parent;
            } else {
                $("."+target).find("input[parent='"+parent+"']").iCheck('uncheck');
            }
        }
    });

    if($(".datatable-not-page").size()>0) {
        $(".datatable-not-page").DataTable({
            fixedHeader: true,
            responsive: true,
            bPaginate: false
        });
    }

    // $('.dataTableList').each(function(){

    //     if($("#tab_content2").size()>0) $("#tab_content2").addClass("active");
    //     var datatable = $(this).find('.datatable-fixed-header').DataTable({
    //         fixedHeader: true,
    //         responsive: true
    //     });

    //     $(this).find('.inputSearch').on('keyup', function () {
    //         datatable.search( this.value ).draw();
    //     });

    //     $(this).find('.btnSearch').on('click', function () {
    //         datatable.search( $(this).parents(".input-group").find(".inputSearch").val() ).draw();
    //     });
    //     if($("#tab_content2").size()>0) $("#tab_content2").removeClass("active");
    // });

    $(document).delegate('input.check-all', 'ifClicked',function() {
        var parent = $(this).parents(".x_content");
        if(!$(this).is(":checked")) {
            $(parent).find('input[type=checkbox]').iCheck('check');
        } else {
            $(parent).find('input[type=checkbox]').iCheck('uncheck');
        }
    });

    $(document).delegate('input.check-one', 'ifClicked',function() {
        var parent = $(this).parents(".x_content");
        var checkbox = $(parent).find("input.check-one").size();
        var checked = $(parent).find("input.check-one:checked").size()
        checked = (!$(this).is(":checked")) ? checked+1 : checked-1;
        if(checkbox==checked) {
            $(parent).find('input.check-all').iCheck('check');
        } else {
            $(parent).find('input.check-all').iCheck('uncheck');
        }
    });

});

function requestConfirm()
{
    $('#modalConfirm').modal('show');
    dialog_close();
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

/*Admin scirpt*/
$(function(){

    $('.btn-submit').on('click', function() {
        var target = $(this).attr("target");
        $("#"+target).parsley().validate();
        if(!$("#"+target).parsley().validate()) {
            $("#"+target).find('.parsley-errors-list').show();
        }
    });

    $(".btn-login").on("click", function() {
        $("#formLogin").parsley().validate();
        if(!$("#formLogin").parsley().validate()) {
            $("#formLogin").find('.parsley-errors-list').show();
        } else {
            var url  = $("#formLogin").attr("action");
            var post = $("#formLogin").serialize();
            $.post(url+"/cmdLogin",post,function(res){
                if(res.status) {
                    window.location.reload(0);
                } else {
                    dialog_error(res.message);
                }
            },'json');
        }
    });

    $(".btn-admin").on("click", function() {
        $("#formLogin").parsley().validate();
        if(!$("#formLogin").parsley().validate()) {
            $("#formLogin").find('.parsley-errors-list').show();
        } else {
            var url  = $("#formLogin").attr("action");
            var post = $("#formLogin").serialize();
            $.post(url+"/cmdAdmin",post,function(res){
                if(res.status) {
                    window.location.reload(0);
                } else {
                    dialog_error(res.message);
                }
            },'json');

        }
    });

    $('.btn-cancel').on('click', function() {
        var target = $(this).attr("target");
        $("#"+target).find('.parsley-errors-list').hide();
        $("#"+target).find("input").val("").removeClass('parsley-error');
        $("#"+target).find("input[type=password]").val("").removeClass('parsley-error');
        $("#"+target).find("input[type=text]").val("").removeClass('parsley-error');
        $("#"+target).find("textarea").val("").removeClass('parsley-error');
        $("#"+target).find("select option:first").prop("selected",true).removeClass('parsley-error');
    });

    $(".table-onclick tbody tr").on('click',function(){
        $(this).parents("tbody").find("tr").removeClass("tr-selected");
        $(this).addClass("tr-selected");
    });

});

/*Mobile script*/
function isMobile()
{
    // if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    //  return true;
    // }
    if($('#w_screen').width()=='100') {
        return true;
    }
    return false;
}

function loadOnPage()
{
    API.close();
}

function loadOnMobile()
{
    setTimeout(function(){
        API.close();
    },100);

    $("#hamburger").click(function(){
        if($("#topmenu").hasClass("mm-opened")) {
            API.close();
        }
    });
}

$(function(){

    if($("#topmenu").size()>0) {
        $("#topmenu").html('<ul>'+$(".nav_menu ul").html()+'</ul>');
        $("#topmenu ul li").each(function(){
            var name_menu = $(this).find('button').text();
            var onclick = $(this).find('button').attr("onclick");
            name_menu = '<a href="javascript:;" onclick="'+onclick+'">'+name_menu+'</a>';
            $(this).html(name_menu);
        });

        $("#topmenu").mmenu();
        API = $("#topmenu").data("mmenu");

        if(!isMobile()) {
            loadOnPage();
        } else {
            loadOnMobile();
        }

        $(window).on('resize', function (){
            if(!isMobile()) {
                loadOnPage();
            } else {
                loadOnMobile();
            }
        });
    }

});

/*dialog script*/
function dialogCreate(obj) {
    var html = "";
    var border = "";

    if (obj.body == null) {
        border = 'style="border:none"';
    }

    $("body #" + obj.id).remove();

    html += '<div id="' + obj.id + '" class="modal fade">';
    html += '<div class="modal-dialog">';
    html += '<div class="modal-content">';
    html += '<div class="modal-header" ' + border + '">';
    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
    html += '<h4 class="modal-title">' + obj.title + '</h4>';
    html += '</div>';

    if (obj.body != null) {
        html += '<div class="modal-body">';
        html += '<p>' + obj.body + '</p>';
        html += '</div>';
    }

    html += '<div class="modal-footer" ' + border + '">';
    html += '<div class="btn-group">';
    html += '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
    $.each(obj.button, function(index, item) {
        html += '<button type="button" class="btn ' + item.Class + '" onclick="' + item.func + '">' + item.label + '</button>';
    });
    html += '</div>';
    html += ' </div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    $("body").append(html);
}

function dialog_error(title,body) {
    var obj = {};

    obj.title = '<span class="text-danger"><i class="fa fa-exclamation-circle"></i> '+title+'</span>';
    obj.id = "alert-error";
    if(body!=null) obj.body = body;
    obj.button = [];
    dialogCreate(obj);
    $('#alert-error').modal('show');
}

function dialog_show(title,body) {
    var obj = {};

    obj.title = '<span class="text-success"><i class="fa fa-info-circle"></i> '+title+'</span>';
    obj.id = "alert-show";
    if(body!=null) obj.body = body;
    obj.button = [];
    dialogCreate(obj);
    $('#alert-show').modal('show');
}

function dialog_confirm(title,func) {
    var obj = {};

    obj.title = '<span class="text-success"><i class="fa fa-question-circle"></i> '+title+'</span>';
    obj.id = "alert-confirm";
    //obj.body = "What do you want to do next ?";
    obj.button = [];
    obj.button.push({
        "label": "OK",
        "func": func,
        "Class": "btn-primary"
    });
    dialogCreate(obj);

    $('#alert-confirm').modal('show');
}

function dialog_delete(title,func) {
    var obj = {};

    obj.title = '<i class="fa fa-trash"></i> '+title+'';
    obj.id = "alert-delete";
    //obj.body = "What do you want to do next ?";
    obj.button = [];
    obj.button.push({
        "label": "OK",
        "func": func,
        "Class": "btn-primary"
    });
    dialogCreate(obj);

   $('#alert-delete').modal('show');
}

function dialog_close()
{
    if($('#alert-delete:visible').size()>0)  $('#alert-delete').modal('toggle');
    if($('#alert-confirm:visible').size()>0)  $('#alert-confirm').modal('toggle');
    if($('#alert-show:visible').size()>0)  $('#alert-show').modal('toggle');
    if($('#alert-error:visible').size()>0)  $('#alert-error').modal('toggle');
}

function getTagcloud() {
    $('#tc-data').hide();
    var data = '';
//    alert(Object.keys(wordData).length);
    if (Object.keys(wordData).length > 0) {
        var data = [];
        $.each(wordData, function (i, v) {
            data += '<li><a href="javascript:;" onclick="get_result_list(\''+v.key+'\')" data-weight="' + v.share_count + '">' + v.key + '</a></li>';
        });
        $('#tc-data').html(data);
        $('#tc-view').attr('width', $('.stage').width());
        $('#tc-view').attr('height', $('.stage').height());
        var options = {
            textColour: '#1269af',
            weightMode: 'size', weight: true, weightFrom: 'data-weight',
//                    shape:'vcylinder',
            initial: [0.06, 0.06], minSpeed: 0.005, outlineOffset: 0,
            weightSizeMin: 10, weightSizeMax: 50,
            freezeActive: true, zoom: 1.1,wheelZoom:false,
            reverse: true, shadowBlur: 1, shuffleTags: true,
            outlineMethod: 'colour', outlineColour: '#336290'
        };
        TagCanvas.Start('tc-view', 'tc-data', options);
        $('#tc-data').show();
        wordData = {};
    }
}

function load_fancybox()
{
    $('.fancybox').each(function(){

        var newWidth = 1000;
        var href = $(this).attr('href');
        
        if($(this).attr("newWidth")!=null) {
            newWidth = $(this).attr("newWidth");
        }

        if(href.substr(0,1)=="#") {
            $(this).fancybox({
                autoScale : false,
                href : href,
                padding : 5,
                width : newWidth,
                height : 500,
                closeClick  : false,
                autoSize: false,
                autoDimensions: false,
                onUpdate: function() {
                    //$(".fancybox-inner").css('height','100%');
                }
            });
        } else {
            $(this).fancybox({
                autoScale : false,
                href : href,
                type:'ajax',
                padding : 5,
                width : newWidth,
                height : 500,
                closeClick  : false,
                autoSize: false,
                autoDimensions: false,
                onUpdate: function() {
                    //$(".fancybox-inner").css('height','100%');
                }
            });
        }

    });
}

function set_gap_time()
{
    setInterval(function()
    { 
        $("gap.post-time").each(function(){
            var time = $(this).attr("time");
            var gap  = get_post_time(time);
            $(this).text(gap);
        })
    },1000*30);
}

function get_post_time(time)
{
    var a_dt = time.split(" ");
    var a_date = a_dt[0].split("-");
    var a_time = a_dt[1].split(":");

    var date1 = new Date(a_date[0], parseInt(a_date[1]-1), parseInt(a_date[2]), parseInt(a_time[0]), parseInt(a_time[1]), parseInt(a_time[2]));
    
    var d = new Date();
    var date2 = new Date(d.getFullYear(),d.getMonth(),d.getDate(),d.getHours(),d.getMinutes(),d.getSeconds());

    var time_diff = Math.abs(date1.getTime() - date2.getTime());

    var seconds    = time_diff;
    var minutes    = Math.round(time_diff / (1000*60));
    var hours      = Math.round(time_diff / (1000*3600));
    var days       = Math.round(time_diff / (1000*86400));
    var weeks      = Math.round(time_diff / (1000*604800));
    var months     = Math.round(time_diff / (1000*2600640));
    var years      = Math.round(time_diff / (1000*31207680));

    if(seconds <= 60) {
        return "Just Now";
    } else if(minutes <=60){
        return minutes+" Mins.";
    } else if(hours <=24){
        return hours+" hrs.";
    } else if(days <= 30){
        return days+" Days";
    } else if(months <=12){
        return months+" Month";
    } else {
        return years+" Year";
    }
}

function check_login_timeout()
{   
    if (typeof urlbase !== 'undefined') {
        var url  = urlbase+"login/check_login_timeout";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data: null,
            url: url,
            beforeSend: function() {
            },
            error: function() {
                //window.location.reload(0);
            },
            success : function(res) {
                if(!res.status) {
                    window.location.href = urlbase+"login/cmdLogout";
                }
            }
        });
    }
}
