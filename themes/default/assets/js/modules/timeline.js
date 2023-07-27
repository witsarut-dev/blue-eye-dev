/*Tinelime script*/
var TimelineItem = null;
var TimelineTarget = null;
var loadSuccess = 1;
var myTimeout;
var runTime = (1000*15);
var callAjax = true;

$(function() {

    var minDate = new Date();
    var maxDate = new Date();
    minDate.setMonth(minDate.getMonth( ) - 3);

    $('#timeline_date').daterangepicker({
        minDate: minDate,
        maxDate: maxDate,
        autoUpdateInput: false,
        locale: {
            format: 'DD/MM/YYYY'
        },
        showDropdowns: true
    });

    $('#timeline_date').daterangepicker({
        minDate: minDate,
        maxDate: maxDate,
        autoUpdateInput: false,
        locale: {
            format: 'DD/MM/YYYY'
        },
        showDropdowns: true}, 
        function(start, end, label) {
            // var start_date = $("input[name=daterangepicker_start]").val();
            // var end_date = $("input[name=daterangepicker_end]").val();
            // $('#timeline_date').val(start_date+ ' - ' + end_date);
    });

    $(document).delegate(".applyBtn","click",function(){
        var start_date = $("input[name=daterangepicker_start]").val();
        var end_date = $("input[name=daterangepicker_end]").val();
        $('#timeline_date').val(start_date+ ' - ' + end_date);
    });

    $('body').tooltip({selector: '.tooltip2'});
    $('[data-toggle="tooltip2"]').tooltip({container: 'body'});

    $("#BoxMyTimeline .scroll-pane").height(115).jScrollPane({autoReinitialise: true});

    if(ref_timeline_id!=0) {
      if($(".timeline-list[timeline-id="+ref_timeline_id+"]").size()>0) {
        loadSuccess = 0;
        $('#formAddTimeline').parsley().reset();
        TimelineItem = $(".timeline-list[timeline-id="+ref_timeline_id+"]");

        callAjax = true;
        ajax_timeline(ref_timeline_id);
      }
    }

    $(document).delegate('.btnDelTimeline', 'click', function() {
        $('#formAddTimeline').parsley().reset();
        TimelineTarget = this;
        dialog_delete("คุณยืนยันที่จะลบข้อมูลหรือไม่","deleteTimeline()");
    });

    $(document).delegate('.timeline-list .TimelineName,.btnEditTimeline', 'click', function() {
        if (loadSuccess >= 1) {
            loadSuccess = 0;
            $('#formAddTimeline').parsley().reset();
            TimelineItem = $(this).parents(".timeline-list");
            var timeline_id = $(this).parents(".timeline-list").attr("timeline-id");

            callAjax = true;
            ajax_timeline(timeline_id);

            if ($(this).hasClass("TimelineName")) {
                var url = urlbase + "timeline/view/" + timeline_id;
                history.pushState({}, "", url);
            }
        } else {
            dialog_error("ระบบกำลังทำงานกรุณารอสักครู่");
        }
    });

    $(".btnSaveTimeline").click(function() {
        var target = $(this).attr("target");
        $("#"+target).parsley().validate();
        if(!$("#"+target).parsley().validate()) {
            $("#"+target).find('.parsley-errors-list').show();
        } else {
            
            $("input[name=timeline_date]").attr("disabled",false);
            $("input[name=keyword_name]").attr("disabled",false);
            var data  = $("#"+target).serialize();
            var url    = urlbase+"timeline/cmdAddTimeline";
            var timeline_id   = "";
            var timeline_name = $("#txtTimelineName").val();

            if(TimelineItem!=null) {
                timeline_id = $(TimelineItem).attr("timeline-id");
            }
            
            $.ajax({
                type : 'post',
                dataType : 'json',
                data: data,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    $(".btnSaveTimeline").prop("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    loadSuccess = 1;
                    dialog_error("No internet connection");
                    $(".btnSaveTimeline").prop("disabled",false);
                },
                success : function(res) {

                    if(res.status) {
                        var timeline_id = res.timeline_id;
                        if(res.action=="Add") {
                            var html = '<div class="timeline-list" timeline-id="'+res.timeline_id+'">';
                            html += '<div class="pull-left TimelineName tooltip2" data-toggle="tooltip2" title="'+timeline_name+'">'+timeline_name+'</div>';
                            html += '<div class="pull-right">';
                            html += '<a href="javascript:;" class="btnEditTimeline"><i class="fa fa-cog"></i></a> ';
                            html += '<a href="javascript:;" class="btnDelTimeline"><i class="fa fa-times"></i></a>';
                            html += '</div>';
                            html += '<div class="clearfix"></div>';
                            html += '</div>';
                            $("#BoxMyTimeline .jspPane").append(html);
                            TimelineItem = $(".timeline-list[timeline-id="+res.timeline_id+"]");
                        } else {
                            TimelineItem = $(".timeline-list[timeline-id="+timeline_id+"]");
                            $(TimelineItem).find(".TimelineName").text(timeline_name).attr({"title":timeline_name,"data-original-title":timeline_name});
                            $(TimelineItem).find('.pull-right .fa-exclamation-triangle').remove();
                        }
                        $("#timeline_id").val(timeline_id);
                        $("input[name=timeline_date]").attr("disabled",true);
                        $("input[name=keyword_name]").attr("disabled",true);
                        $.fancybox.hideLoading();

                        callAjax = true;
                        create_timeline(timeline_id);

                    } else {
                        dialog_error(res.message);
                        $.fancybox.hideLoading();
                    }
                    $(".btnSaveTimeline").prop("disabled",false);
                }
            });
        }
    });

    $(".btnCancelTimeline").click(function() {
      clear_timeline();
      stop_loading();
    });

});

function deleteTimeline()
{
    var url  = urlbase+"timeline/cmdDelTimeline";
    var timeline_id = $(TimelineTarget).parents(".timeline-list").attr("timeline-id");

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {timeline_id:timeline_id},
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
                $(TimelineTarget).parents(".timeline-list").fadeOut();
                if(TimelineItem!=null) {
                    TimelineItem = null;
                }
                if($("#timeline_id[value="+timeline_id+"]").size()>0) {
                  clear_timeline();
                  stop_loading();
                }
                dialog_close();
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

function ajax_timeline(timeline_id)
{
  $("input[name=timeline_id]").val(timeline_id);

  var url = urlbase+"timeline/open_timeline";
  var data  = $("#formAddTimeline").serialize();
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
          loadSuccess = 1;
          dialog_error("No internet connection");
      },
      success : function(res) {
            loadSuccess = 1;
            if(res.status) {
                $.fancybox.hideLoading();
                clear_timeline();
                $("input[name=timeline_name]").val(res.timeline_name);
                $("input[name=keyword_name]").val(res.keyword_name).attr("disabled",true);
                $("input[name=timeline_id]").val(res.timeline_id);
                $("input[name=timeline_date]").val(res.timeline_date).attr("disabled",true);
                
                create_timeline(timeline_id);

            } else {
                dialog_error(res.message);
                $.fancybox.hideLoading();
            }
      }
  });
}


function clear_timeline()
{
  $("#timeline_id").val("");
  $("input[name=timeline_name]").val("");
  $("input[name=keyword_name]").val("").attr("disabled",false);
  $("input[name=timeline_date]").val("").attr("disabled",false);
  $('#formAddTimeline').parsley().reset();
  $("#mytimeline").html("");
}


function create_timeline(timeline_id)
{
    if(callAjax) {
        var url = urlbase+"timeline/get_timeline";
        var data  = {timeline_id:timeline_id};
        $.ajax({
            type : 'post',
            dataType : 'json',
            data: data,
            url: url,
            beforeSend: function() {
                start_loading();
            },
            error: function() {
                clearTimeout(myTimeout);
                myTimeout = setTimeout("create_timeline("+timeline_id+")", runTime);
                callAjax = true;
            },
            success : function(res) {
                if(res.status) {
                    clearTimeout(myTimeout);
                    callAjax = false;
                    var html = "";
                    html += '<ul class="timeline">';
                    var start = 0;
                    var end  = (res.timeline_list.length-1);
                    for(var i in res.timeline_list) {
                        var obj = res.timeline_list[i];
                        var class_invert = (i%2!=0) ? 'timeline-inverted' : '';
                        var circle_start = (obj.post_order=='start') ? '<div class="timeline-badge bg-green"><span style="position: relative;top: -2px;">Start</span></div>' : '';
                        var circle_end   = (obj.post_order=='end') ? '<div class="timeline-badge bg-red"><span style="position: relative;top: -2px;">End</span></div>' : '';
                        var text_sen = "";

                        if(obj.sentiment > 0) {
                            text_sen = '<spn class="text-success">Sen. '+obj.sentiment+'%</span>';
                        } else if(obj.sentiment < 0) {
                            text_sen = '<spn class="text-danger">Sen. '+obj.sentiment+'%</span>';
                        } else {
                            text_sen = '<spn class="">Sen. 0%</span>';
                        }

                        html += '<li class="'+class_invert+'">'+circle_start+circle_end+'<div class="timeline-panel pa-30">';
                        html += '<a href="'+obj.post_link+'" class="timeline-link" target="_blank"><i class="fa fa-arrow-circle-right"></i></a>';
                        html += '<div class="timeline-heading"><h6 class="mb-15">'+obj.msg_date+'</h6></div>';
                        html += '<div class="timeline-body">';
                        html += '<h4 class="mb-5">'+obj.post_icon+' <span class="timeline-name">'+obj.post_user+'</span></h4>';
                        html += '<br /><p class="timeline-content">'+obj.post_detail.replace(/\?/g,'')+' ...</p><hr class="line"/>';
                        html += '<div class="pull-left"><i class="fa fa-clock-o"></i> '+obj.msg_time+'</div>';
                        html += '<div class="pull-right">'+text_sen+'</div>';
                        html += '</div>';
                        if(obj.post_count>0) {
                            var params = encodeURI('/?'+'msg_date='+obj.param_date+'&start_date='+res.start_date+'&end_id='+res.end_id);
                            html += '<br /><br /><div align="center"><small><a href="'+urlbase+'timeline/get_feed/'+obj.timeline_id+params+'" class="fancybox">ดูทั้งหมด...</a></small></div>';
                        }
                        html += '</div></li>';
                    }
                    html += '</ul>';
                    $("#mytimeline").html(html);
                    $("#mytimeline .timeline li").removeClass("timeline-end");
                    $("#mytimeline .timeline li:last").addClass("timeline-end");
                    $("#mytimeline .timeline").append('<li class="clearfix no-float"></li>');

                    load_fancybox();
                    stop_loading();

                    if(res.timeline_list.length==0) $("#mytimeline").html('<div align="center">ไม่พบข้อมูล</div>');

                } else {
                    clearTimeout(myTimeout);
                    myTimeout = setTimeout("create_timeline("+timeline_id+")", runTime);
                    callAjax = true;
                }
            }
        });

    }
}

function click_show_all(all) 
{
    if($(".timeline li").size()>3) {
        $(".timeline li").hide();
        if(all=='0') {
            $(".timeline li:first,.timeline li.timeline-end").fadeIn();
            if($(".timeline li.timeline-end.timeline-inverted").size()==0) {
                $(".timeline li.timeline-end").addClass("timeline-inverted timeline-end-inverted");
            }
        } else {
            $(".timeline li.timeline-end.timeline-end-inverted").removeClass("timeline-inverted");
            $(".timeline li").fadeIn();
        }
    }
}

function start_loading()
{
    $("#icon-loading,#msg-loading").show();
}

function stop_loading()
{
    
    $("#icon-loading,#msg-loading").hide();
}