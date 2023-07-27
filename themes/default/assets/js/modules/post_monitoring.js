/*Post Monitoring script*/
var network  = null;
var nw_scale = 1;
var PostItem = null;
var PostTarget = null;
var runTime = (1000*60*5);
var likeTimeout;
var shareTimeout;
var commentTimeout;
var allTimeout;
var lineColor = {'likes':"#5298da",'shares':"#e94362",'comments':"#90ee7d"};
var indexChart = {'likes':null,'shares':null,'comments':null};
var indexChartAll = null;
var loadSuccess = 3;
var isRenew = false;

var tableGraph = null; // Created by witsarut(view) , 09-03-2021

// create a network
$(function() {

    $('body').tooltip({selector: '.tooltip2'});
    $('[data-toggle="tooltip2"]').tooltip({container: 'body'});

    $("#BoxMyPost .scroll-pane").height(115).jScrollPane({autoReinitialise: true});
    $(".mark-box.scroll-pane").height(345).jScrollPane({autoReinitialise: true});

    if(ref_post_id!=0) {
      if($(".post-list[post-id="+ref_post_id+"]").size()>0) {
        loadSuccess = 0;
        $('#formAddPost').parsley().reset();
        PostItem = $(".post-list[post-id="+ref_post_id+"]");
        clear_graph();
        clearTimeoutAll();
        ajax_monitoring(ref_post_id);
      }
    }

    $(document).delegate('.btnDelPost', 'click', function() {
        $('#formAddPost').parsley().reset();
        PostTarget = this;
        dialog_delete("คุณยืนยันที่จะลบข้อมูลหรือไม่","deletePost()");
    });

    $(document).delegate('.btnDelMark', 'click', function() {
        var post_id = $("#post_id").val();
        var action = $(this).attr("action");
        var time = $(this).attr("time");
        var chart = indexChart[action];
        for(var i in chart.series[2].data) {
            var obj = chart.series[2].data[i];
            if(obj.x==time) {
                isAdd = false;
                chart.series[2].data[i].remove(false, false);
                ajax_update_mark(post_id, action, time, 'delete'); 
                break;
            }
        }
    });

    $(document).delegate('.post-list .PostName,.btnEditPost', 'click', function() {
        if (loadSuccess >= 3) {
            loadSuccess = 0;
            $('#formAddPost').parsley().reset();
            PostItem = $(this).parents(".post-list");
            var post_id = $(this).parents(".post-list").attr("post-id");
            clear_graph();
            clearTimeoutAll();
            ajax_monitoring(post_id);

            if ($(this).hasClass("PostName")) {
                var url = urlbase + "post_monitoring/view/" + post_id;
                history.pushState({}, "", url);
            }
        } else {
            dialog_error("ระบบกำลังทำงานกรุณารอสักครู่");
        }
    });

    $(".btnSavePost").click(function() {
        var target = $(this).attr("target");
        $("#"+target).parsley().validate();
        if(!$("#"+target).parsley().validate()) {
            $("#"+target).find('.parsley-errors-list').show();
        } else {
            
            var data  = $("#"+target).serialize();
            var url    = urlbase+"post_monitoring/cmdAddPost";
            var post_id   = "";
            var post_name = $("#txtPostName").val();

            if(PostItem!=null) {
                post_id = $(PostItem).attr("post-id");
            }

            $.ajax({
                type : 'post',
                dataType : 'json',
                data: data,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    clear_graph();
                    clearTimeoutAll();
                    $(".btnSavePost").prop("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    loadSuccess = 3;
                    dialog_error("No internet connection");
                    $(".btnSavePost").prop("disabled",false);
                },
                success : function(res) {

                    if(res.status) {
                        var post_id = res.post_id;
                        if(res.action=="Add") {
                            var html = '<div class="post-list" post-id="'+res.post_id+'">';
                            html += '<div class="pull-left PostName tooltip2" data-toggle="tooltip2" title="'+post_name+'">'+post_name+'</div>';
                            html += '<div class="pull-right">';
                            html += '<a href="javascript:;" class="btnEditPost"><i class="fa fa-cog"></i></a> ';
                            html += '<a href="javascript:;" class="btnDelPost"><i class="fa fa-times"></i></a>';
                            html += '</div>';
                            html += '<div class="clearfix"></div>';
                            html += '</div>';
                            $("#BoxMyPost .jspPane").append(html);
                            PostItem = $(".post-list[post-id="+res.post_id+"]");
                        } else {
                            PostItem = $(".post-list[post-id="+post_id+"]");
                            $(PostItem).find(".PostName").text(post_name).attr({"title":post_name,"data-original-title":post_name});
                            $(PostItem).find('.pull-right .fa-exclamation-triangle').remove();
                            if(res.post_expire) {
                                $(PostItem).find('.pull-right').prepend('<i class="fa fa-exclamation-triangle"></i>');
                            }
                        }
                        $("#post_id").val(post_id);
                        $("input[name^=post_url]").attr("readonly",true);
                        createChartAll(post_id);
                        show_renew(res.post_expire);
                    } else {
                        dialog_error(res.message);
                    }
                    $.fancybox.hideLoading();
                    $(".btnSavePost").prop("disabled",false);
                }
            });
        }
    });

    $(".btnCancelPost").click(function() {
      clear_post();
      show_renew(false);
    });

    $('#myTabs_7 a').click(function (e) {
        e.preventDefault();
        if($(this).attr("href")=="#all-chart") {
            $("#single-chart").css({"visibility":"hidden","height":"0"});
            $("#table-chart").css({"visibility":"hidden","height":"0"});
            $("#all-chart").css({"visibility":"visible","height":"auto"});

        } else if($(this).attr("href")=="#table-chart") {
            $("#single-chart").css({"visibility":"hidden","height":"0"});
            $("#all-chart").css({"visibility":"hidden","height":"0"});
            $("#table-chart").css({"visibility":"visible","height":"auto"});
        } else {
            $("#all-chart").css({"visibility":"hidden","height":"0"});
            $("#table-chart").css({"visibility":"hidden","height":"0"});
            $("#single-chart").css({"visibility":"visible","height":"auto"});
        }
    });


    // new js post-postmonitoring
    $(document).delegate('.btnPrint', 'click', function() {
        $('#formAddPost').parsley().reset();
        var post_id = $(this).parents(".post-list").attr("post-id");
        get_dataExport(post_id);
    });

});

// function send data to controllers 
function get_dataExport(post_id) {
    var post_id = post_id;
    var url  = urlbase+"post_monitoring/export_file_post";
    location.href = url+"/"+post_id;
    
}

function deletePost()
{
    var url  = urlbase+"post_monitoring/cmdDelPost";
    var post_id = $(PostTarget).parents(".post-list").attr("post-id");

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {post_id:post_id},
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
                $(PostTarget).parents(".post-list").fadeOut();
                if(PostItem!=null) {
                    PostItem = null;
                }
                if($("#post_id[value="+post_id+"]").size()>0) {
                  clear_post();
                  clear_graph();
                  clearTimeoutAll();
                  show_renew(false);
                }
                dialog_close();
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

function ajax_monitoring(post_id)
{
  $("input[name=post_id]").val(post_id);

  var url = urlbase+"post_monitoring/open_post";
  var data  = $("#formAddPost").serialize();
  $.ajax({
      type : 'post',
      dataType : 'json',
      data: data,
      url: url,
      beforeSend: function() {
          $.fancybox.showLoading();
          clearTimeoutAll();
      },
      error: function() {
          $.fancybox.hideLoading();
          loadSuccess = 3;
          dialog_error("No internet connection");
      },
      success : function(res) {
          if(res.status) {
              $.fancybox.hideLoading();
              clear_post();
              $("input[name=post_name]").val(res.post_name);
              $("input[name=post_id]").val(res.post_id);
              $("input[name=post_url]").val(res.post_url)
              $("input[name^=post_url]").attr("readonly",true);
              createChartAll(post_id);
              show_renew(res.post_renew);
          } else {
              dialog_error(res.message);
              $.fancybox.hideLoading();
          }
      }
  });
}

function createChartAll(post_id) {
  createChart(post_id,'likes');
  createChart(post_id,'shares');
  createChart(post_id,'comments');
  createChart(post_id,'all');
  $("#mark-likes,#mark-shares,#mark-comments,.line-post").show();
}

function createChart(post_id,action) {

    if(action=='all') {
        $.getJSON(urlbase+"post_monitoring/ajax_post_all/?post_id="+post_id, function (data) {
                //var tickIntervalY = get_max_Interval(data);
                var tickIntervalX = 60*1000*60;
                var selected = 4;

                Highcharts.setOptions({
                    global: {
                        useUTC: false
                    }
                });

                var series =  [{
                            id: 'series-likes',
                            name: 'likes',
                            color: lineColor['likes'],
                            data: data.series_likes,
                            yAxis: 0
                        } , {
                            id: 'series-shares',
                            name: 'shares',
                            color: lineColor['shares'],
                            data: data.series_shares,
                            yAxis: 1
                        }, {
                            id: 'series-comments',
                            name: 'comments',
                            color: lineColor['comments'],
                            data: data.series_comments,
                            yAxis: 2
                        }];

                indexChartAll = Highcharts.stockChart('chart-all', {
                    title : {
                      text : data.title,
                      useHTML : true
                    },
                    subtitle : {
                        text : data.subtitle
                    },
                    chart: {
                        style: {
                            fontFamily: '"Poppins", sans-serif'
                        },
                        height : 550,
                        events : {
                            load : function() {
                                indexChartAll = this;
                                $("#chart-all .highcharts-yaxis-grid:eq(0) .highcharts-grid-line:first").attr("stroke-width",2);
                                $("#chart-all .highcharts-yaxis-grid:eq(1) .highcharts-grid-line:first").attr("stroke-width",2);
                                $("#chart-all .highcharts-yaxis-grid:eq(2) .highcharts-grid-line:first").attr("stroke-width",2);
                                requestChartAll(post_id,data.series_likes.length);
                            }
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    xAxis: {
                        tickInterval : tickIntervalX,
                        dateTimeLabelFormats: {
                                millisecond: '%H:%M:%S.%L',
                                second: '%H:%M:%S',
                                minute: '%H:%M',
                                hour: '%H:%M',
                                day: '%e %b',
                                week: '%e. %b',
                                month: '%b %y',
                                year: '%Y'
                        }
                    },
                    yAxis: [{
                        allowDecimals: false,
                        labels: {
                            align: 'right',
                            x: -3
                        },
                        title: {
                            text: 'likes',
                        },
                        top: '0%',
                        height: '35%',
                        lineWidth: 2,
                        offset: 0,
                        min : 0,
                        gridLineWidth:0.25
                    },{
                        allowDecimals: false,
                        labels: {
                            align: 'right',
                            x: -3
                        },
                        title: {
                            text: 'shares',
                        },
                        top: '35%',
                        height: '35%',
                        lineWidth: 2,
                        offset: 0,
                        min : 0,
                        gridLineWidth:0.25
                    },{
                        allowDecimals: false,
                        labels: {
                            align: 'right',
                            x: -3
                        },
                        title: {
                            text: 'comments',
                        },
                        top: '70%',
                        height: '30%',
                        lineWidth: 2,
                        offset: 0,
                        min : 0,
                        gridLineWidth:0.25
                    }],
                    plotOptions: {
                        series: {
                            minPointLength: 0,
                            animation: {
                                duration: 2500
                            },
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function () {
                                       
                                    }
                                }
                            }
                        }
                    },
                    legend: {
                        enabled: false,
                        align: 'center',
                        verticalAlign: 'bottom',
                        x: 0,
                        y: 0
                    },
                    tooltip: {
                        formatter: function () {
                            var points = this.points;
                            if(points!=null) {
                                var dateLabel = Highcharts.dateFormat('%A %b, %e, %H:%M',new Date(this.x));
                                var pointsLength = points.length;
                                var tooltipMarkup = pointsLength ? '<span style="font-size: 10px">' +dateLabel + '</span><br/>' : '';
                                var index;
                                var rows = 0;

                                for(index = 0; index < pointsLength; index += 1) {
                                    var y_value = Math.ceil(points[index].y)
                                    tooltipMarkup += '<span style="color:' + points[index].series.color + '">\u25CF</span> ' + points[index].series.name + ': <b>' + y_value  + '</b><br/>';
                                    rows++;
                                }

                                if (rows>0) {
                                    return tooltipMarkup;
                                } else {
                                    return false;
                                }
                            } else {
                                return false;
                            }
                        }
                    },
                    exporting: {
                        buttons: {
                            contextButton: {
                                menuItems: [{
                                    text: 'Print chart',
                                    onclick: function() {
                                        var w = $("#chart-all").width();
                                        var h = $("#chart-all").height();
                                        $("html,body").css("min-height", h);
                                        this.setSize(680, h, false);
                                        this.print();
                                        $("html,body").css("min-height", $(".content").height());
                                        this.setSize(w, h, false);
                                    }
                                }, {
                                    text: 'Download image',
                                    onclick: function() {
                                        this.exportChartLocal();
                                    }
                                }]
                            }
                        }
                    },
                    series: series,
                    rangeSelector : { 
                        selected: selected,
                        enabled : true,
                        buttons: [{
                                    count: 24,
                                    type: 'hour',
                                    text: '1D'
                                },{
                                    count: 3,
                                    type: 'day',
                                    text: '3D'
                                },{
                                    type: 'day',
                                    count: 7,
                                    text: '1W'
                                }, {
                                    count: 14,
                                    type: 'day',
                                    text: '2W'
                                },{
                                    type: 'ytd',
                                    text: 'All'
                                }],
                        inputEnabled:false,
                        inputDateFormat:'%d/%m/%Y'
                        // ,inputEditDateFormat:'%d/%m/%Y'
                    }
                });

        });
    } else {
        $.getJSON(urlbase+"post_monitoring/ajax_post_data/?post_id="+post_id+"&action="+action, function (data) {
            loadSuccess++;
            //var tickIntervalY = get_max_Interval(data);
            var tickIntervalX = 60*1000*60;
            var selected = 4;

            Highcharts.setOptions({
                global: {
                    useUTC: false
                }
            });

            var icon = "";
            if(action=="likes") {
              icon = '<i class="icon-like"></i> ';
            } else if(action=="shares") {
              icon = '<i class="icon-share"></i> ';
            } else if(action=="comments") {
              icon = '<i class="icon-bubble"></i> ';
            }

            var series =  [{
                        id: 'series0',
                        name: action,
                        color: lineColor[action],
                        data: data.series,
                        marker: {
                        enabled: true,
                            radius: 3
                        },
                      shadow: true,
                    } , {
                        id: 'series1',
                        type: 'column',
                        name: 'change',
                        color: lineColor[action],
                        data: data.change,
                        yAxis: 1
                    }];

            if(data.flags.length>0) {
                var flags =  {
                    type: 'flags',
                    onSeries: 'series0',
                    shape: 'squarepin',
                    width: 32,
                    height: 16,
                    data: data.flags,
                    lineWidth : 1,
                    lineColor : lineColor[action],
                    fillColor: 'rgba(255,255,255,0.7)',
                    y:-40,
                    title : 'Mark'
                };
                series.push(flags);
                create_mark_list(action,data.flags);
            }

            indexChart[action] = Highcharts.stockChart('chart-'+action, {
                title : {
                  text : icon+action+' '+data.title,
                  useHTML : true
                },
                subtitle : {
                    text : data.subtitle
                },
                chart: {
                    style: {
                        fontFamily: '"Poppins", sans-serif'
                    },
                    height : 450,
                    events : {
                        load : function() {
                            if(action=="likes") {
                                indexChart.likes = this;
                            } else if(action=="shares") {
                                indexChart.shares = this;
                            } else if(action=="comments") {
                                indexChart.comments = this;
                            }
                            requestChart(post_id,action,data.series.length);
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    tickInterval : tickIntervalX,
                    dateTimeLabelFormats: {
                            millisecond: '%H:%M:%S.%L',
                            second: '%H:%M:%S',
                            minute: '%H:%M',
                            hour: '%H:%M',
                            day: '%e %b',
                            week: '%e. %b',
                            month: '%b %y',
                            year: '%Y'
                    }
                },
                yAxis: [{
                    allowDecimals: false,
                    labels: {
                        align: 'right',
                        x: -3
                    },
                    title: {
                        text: action,
                    },
                    height: '60%',
                    lineWidth: 2,
                    // lineWidth:1,
                    // tickInterval : tickIntervalY,
                    min : 0
                },{
                    allowDecimals: false,
                    labels: {
                        align: 'right',
                        x: -3
                    },
                    title: {
                        text: 'change'
                    },
                    top: '65%',
                    height: '35%',
                    offset: 0,
                    lineWidth: 2
                }],
                plotOptions: {
                    series: {
                        minPointLength: 0,
                        animation: {
                            duration: 2500
                        },
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function () {
                                    var chart = indexChart[action];
                                    var time = this.x;
                                    var flags =  {
                                        type: 'flags',
                                        onSeries: 'series0',
                                        shape: 'squarepin',
                                        data: [{x:time}],
                                        width: 32,
                                        height: 16,
                                        lineWidth : 1,
                                        lineColor : lineColor[action],
                                        fillColor: 'rgba(255,255,255,0.7)',
                                        y:-40,
                                        title : 'Mark'
                                    };
                                    if(chart.series.length<4) {
                                        chart.addSeries(flags);
                                        ajax_update_mark(post_id, action, time, 'add');
                                    } else {
                                        var isAdd = true;
                                        for(var i in chart.series[2].data) {
                                            var obj = chart.series[2].data[i];
                                            if(obj.x==time) {
                                                isAdd = false;
                                                chart.series[2].data[i].remove(false, false);
                                                ajax_update_mark(post_id, action, time, 'delete'); 
                                                break;
                                            }
                                        }
                                        if(isAdd) {
                                            chart.series[2].addPoint({x:time});
                                            ajax_update_mark(post_id, action, time, 'add'); 
                                        }
                                    }
                                }
                            }
                        }
                    },
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    enabled: false,
                    align: 'center',
                    verticalAlign: 'bottom',
                    x: 0,
                    y: 0
                },
                tooltip: {
                    formatter: function () {
                        var points = this.points;
                        if(points!=null) {
                            var dateLabel = Highcharts.dateFormat('%A %b, %e, %H:%M',new Date(this.x));
                            var pointsLength = points.length;
                            var tooltipMarkup = pointsLength ? '<span style="font-size: 10px">' +dateLabel + '</span><br/>' : '';
                            var index;
                            var rows = 0;

                            for(index = 0; index < pointsLength; index += 1) {
                                var y_value = Math.ceil(points[index].y)
                                if(points[index].series.name=='change') {
                                    if(y_value>0) y_value = '<span style="color:#90ee7d">+'+y_value+'</span>';
                                    if(y_value<0) y_value = '<span style="color:#f33923">'+y_value+'</span>';
                                }
                                tooltipMarkup += '<span style="color:' + points[index].series.color + '">\u25CF</span> ' + points[index].series.name + ': <b>' + y_value  + '</b><br/>';
                                rows++;
                            }

                            if (rows>0) {
                                return tooltipMarkup;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    }
                },
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: [{
                                text: 'Print chart',
                                onclick: function() {
                                    var w = $("#chart-likes").width();
                                    var h = $("#chart-likes").height();
                                    $("html,body").css("min-height", h);
                                    this.setSize(680, h, false);
                                    this.print();
                                    $("html,body").css("min-height", $(".content").height());
                                    this.setSize(w, h, false);
                                }
                            }, {
                                text: 'Download image',
                                onclick: function() {
                                    this.exportChartLocal();
                                }
                            }]
                        }
                    }
                },
                series: series,
                rangeSelector : { 
                    selected: selected,
                    enabled : true,
                    buttons: [{
                                count: 24,
                                type: 'hour',
                                text: '1D'
                            },{
                                count: 3,
                                type: 'day',
                                text: '3D'
                            },{
                                type: 'day',
                                count: 7,
                                text: '1W'
                            }, {
                                count: 14,
                                type: 'day',
                                text: '2W'
                            },{
                                type: 'ytd',
                                text: 'All'
                            }],
                    inputEnabled:false,
                    inputDateFormat:'%d/%m/%Y'
                    // ,inputEditDateFormat:'%d/%m/%Y'
                }
            });

        });
    }
}

function requestChart(post_id,action,length) {
    if(length==0) {
          setTimeout(function(){createChart(post_id,action);},runTime);
    } else {
        var series = null;
        var change = null;
        var max_time = 0;

        if(action=='likes') {
            series = indexChart.likes.series[0];
            change = indexChart.likes.series[1];
            if(series.xData.length>0) max_time = series.xData[series.xData.length-1];
        } else if(action=='shares') {
            series = indexChart.shares.series[0];
            change = indexChart.shares.series[1];
            if(series.xData.length>0) max_time = series.xData[series.xData.length-1];
        } else if(action=='comments') {
            series = indexChart.comments.series[0];
            change = indexChart.comments.series[1];
            if(series.xData.length>0) max_time = series.xData[series.xData.length-1];
        }

        var url  = urlbase+"post_monitoring/ajax_post_data/?post_id="+post_id+"&action="+action;
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{max_time:max_time},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                loadSuccess = 3;
                //dialog_error("Error");
                if(action=='likes') {
                  clearTimeout(likeTimeout);
                  likeTimeout = setTimeout("requestChart("+post_id+",'likes',"+length+")", runTime);
                } else if(action=='shares') {
                  clearTimeout(shareTimeout);
                  shareTimeout = setTimeout("requestChart("+post_id+",'shares',"+length+")", runTime);
                } else if(action=='comments') {
                  clearTimeout(commentTimeout);
                  commentTimeout = setTimeout("requestChart("+post_id+",'comments',"+length+")", runTime);
                }
            },
            success : function(res) {

                if(action=='likes' && !isRenew) {
                    clearTimeout(likeTimeout);
                    likeTimeout = setTimeout("requestChart("+post_id+",'likes',"+length+")", runTime);
                } else if(action=='shares' && !isRenew) {
                    clearTimeout(shareTimeout);
                    shareTimeout = setTimeout("requestChart("+post_id+",'shares',"+length+")", runTime);
                } else if(action=='comments' && !isRenew) {
                    clearTimeout(commentTimeout);
                    commentTimeout = setTimeout("requestChart("+post_id+",'comments',"+length+")", runTime);
                }
                if(res.series.length>0 && series!=null) {
                    for(var i in res.series) {
                        var point = res.series[i];
                        var change_point = res.change[i];
                        if(point[0]>max_time && post_id==res.post_id) {
                            series.addPoint(point);
                            change.addPoint(change_point);
                        } 
                        $("#chart-"+action+" .highcharts-range-selector-buttons .highcharts-button:last").trigger("click");
                    }
                }
                show_renew(res.post_renew);

            }
        });
    }
}

function requestChartAll(post_id,length) {
    if(length==0) {
          setTimeout(function(){createChart(post_id,'all');},runTime);
    } else {
        var series_likes = indexChartAll.series[0];
        var series_shares = indexChartAll.series[1];
        var series_comments = indexChartAll.series[2];
        var max_time = 0;
        if(series_likes.xData.length>0) max_time = series_likes.xData[series_likes.xData.length-1];

        var url  = urlbase+"post_monitoring/ajax_post_all/?post_id="+post_id;
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{max_time:max_time},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                clearTimeout(allTimeout);
                allTimeout = setTimeout("requestChartAll("+post_id+","+length+")", runTime);
            },
            success : function(res) {

                if(!isRenew) {
                    clearTimeout(allTimeout);
                    allTimeout = setTimeout("requestChartAll("+post_id+","+length+")", runTime);
                }

                if(res.series_likes.length>0) {
                    for(var i in res.series_likes) {
                        var point_likes = res.series_likes[i];
                        var point_shares = res.series_shares[i];
                        var point_comment = res.series_comments[i];
                        if(point_likes[0]>max_time && post_id==res.post_id) {
                            if(point_likes!=null) series_likes.addPoint(point_likes);
                            if(point_shares!=null) series_shares.addPoint(point_shares);
                            if(point_comment!=null) series_comments.addPoint(point_comment);
                        }
                        $("#chart-all .highcharts-range-selector-buttons .highcharts-button:last").trigger("click");
                    }
                }
            }
        });
    }

}

function clearTimeoutAll()
{
  clearTimeout(likeTimeout);
  clearTimeout(shareTimeout);
  clearTimeout(commentTimeout);
  clearTimeout(allTimeout);
}

function clear_post()
{
  $("#post_id").val("");
  $("input[name^=post_name]").val("");
  $("input[name^=post_url]").val("").attr("readonly",false);
  $('#formAddPost').parsley().reset();
}

function clear_graph()
{
    $("#chart-likes,#chart-shares,#chart-comments,#chart-all").html("");
    $("#mark-likes,#mark-shares,#mark-comments,.line-post").hide().find(".jspPane").html("");
    indexChart = {'likes':null,'shares':null,'comments':null};
}

function get_max_Interval(series)
{
    var max = 0;
    for(var i in series) {
      var data = series[i];
      if(data[1]>max) max = data[1];
    }
    return get_max_range(max);
}

function get_max_range(max)
{

    var range = [0,10,100,1000,10000,100000,1000000];
    for(var i in range) {
        var start = range[i];
        var end = (start==0) ? 1 : (start*10);
        if(max > start && max <= end) {
            var step = (start);
            if(step==0) {
                return 1;
            } else if(step==1) {
                return 10;
            } else {
                return step;
            }
        }
    }
    return 1;
}

function show_renew(status)
{
    if(status) {
        $("#post_renew").val("YES");
        $(".btnSavePost").removeClass("btn-success").addClass("btn-danger").text("Renew");
        isRenew = true;
    } else {
        $("#post_renew").val("");
        $(".btnSavePost").removeClass("btn-danger").addClass("btn-success").text("Submit");
        isRenew = false;
    }
}

function ajax_update_mark(post_id, action, time, status) {

    if(status=='add') {
        var url = urlbase + "post_monitoring/cmdAddMark";
    } else {
        var url = urlbase + "post_monitoring/cmdDelMark";
    }

    $.ajax({
        type: 'post',
        dataType: 'json',
        data: {post_id:post_id,action:action,time:time},
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            dialog_error("No internet connection");
        },
        success: function(res) {
            if (res.status) {
                $.fancybox.hideLoading();
                create_mark_list(res.action,res.flags);
            } else {
                $.fancybox.hideLoading();
            }
        }
    });
}

function create_mark_list(action,flags_mark)
{
    var html = "";
    for(var i in flags_mark) {
        var obj = flags_mark[i];
        html += '<div class="mark-list">';
        html += '<div class="pull-left MarkName">'+obj.datetime+'</div>';
        html += '<div class="pull-right">';
        html += '<a href="javascript:;" class="btnDelMark" time="'+obj.x+'" action="'+action+'"><i class="fa fa-times"></i></a>';
        html += '</div>';
        html += '<div class="clearfix"></div>';
        html += '</div>';
    }
    $("#mark-"+action).find(".scroll-pane .jspPane").html(html);
}
