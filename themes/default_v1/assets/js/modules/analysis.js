/*Analysis script*/
var GraphItem = null;
var GraphTarget = null;

$(function() {

    $(document).delegate('.btnDeleteGraph', 'click', function() {
        GraphTarget = this;
        dialog_delete("คุณยืนยันที่จะลบข้อมูลหรือไม่","deleteGraph()");
    });

    $(document).delegate('.btnEditGraph', 'click', function() {
        GraphItem = $(this).parents(".graph-list");
        $("#txtGraphName").val($(this).parent().parent().find(".GraphName").text());
        if($("#BoxAddGraph").is(":hidden")) {
            $("#BoxAddGraph").slideDown();
            $("#btnAddGraph").text("Close Graph");
        }
    });

    $(document).delegate('.graph-list .GraphName', 'click', function() {
        var graph_id = $(this).parents(".graph-list").attr("graph-id");
        window.location.href = urlbase+"analysis/open_graph/"+graph_id;
    });

    $(".btnSaveGraph").click(function() {
        var target = $(this).attr("target");
        $("#"+target).parsley().validate();
        if(!$("#"+target).parsley().validate()) {
            $("#"+target).find('.parsley-errors-list').show();
        } else {
            
            var graph  = get_graph_data();
            var url    = urlbase+"analysis/cmdAddGraph";
            var graph_id   = "";
            var graph_name = $("#txtGraphName").val();

            if(GraphItem!=null) {
                graph_id = $(GraphItem).attr("graph-id");
            }

            $.ajax({
                type : 'post',
                dataType : 'json',
                data: {
                    graph_id:graph_id,
                    graph_name:graph_name,
                    graph_x:graph.graph_x,
                    graph_y:graph.graph_y,
                    graph_type:graph.graph_type},
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

                        if(res.action=="Add") {
                            var html = '<div class="graph-list" graph-id="'+res.graph_id+'">';
                            html += '<div class="pull-left GraphName">'+graph_name+'</div>';
                            html += '<div class="pull-right">';
                            html += '<a href="javascript:;" class="btnEditGraph"><i class="fa fa-cog"></i></a> ';
                            html += '<a href="javascript:;" class="btnDeleteGraph"><i class="fa fa-times"></i></a>';
                            html += '</div>';
                            html += '<div class="clearfix"></div>';
                            html += '</div>';
                            $("#BoxMyGarph").append(html);
                        } else {
                            $(GraphItem).find(".GraphName").text(graph_name);
                            GraphItem = null;
                        }

                        $("#txtGraphName").val("");
                        $("#btnAddGraph").text("Add Graph");
                        $("#BoxAddGraph").slideUp();
                    } else {
                        dialog_error(res.message);
                    }
                    $.fancybox.hideLoading();
                }
            });
        }
    });

    $("#btnAddGraph").click(function(){
        if($("#BoxAddGraph").is(":hidden")) {
            $("#BoxAddGraph").slideDown();
            $("#txtGraphName").val("");
            $(this).text("Close Graph");
            GraphItem = null;
        } else {
            $("#BoxAddGraph").slideUp();
            $("#txtGraphName").val("");
            $(this).text("Add Graph");
            GraphItem = null;
        }
    });

});

function deleteGraph()
{
    var url  = urlbase+"analysis/cmdDelGraph";
    var graph_id = $(GraphTarget).parents(".graph-list").attr("graph-id");

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {graph_id:graph_id},
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
                $(GraphTarget).parents(".graph-list").fadeOut();
                if(GraphItem!=null) {
                    $("#BoxAddGraph").slideUp();
                    $("#txtGraphName").val("");
                    $("#btnAddGraph").text("Add Graph");
                    GraphItem = null;
                }
                dialog_close();
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

/*analysis*/
var obj_item = null;

$(function(){

    if($('.box-item').size()>0) {

        $('.box-item').draggable({
            cursor: 'move',
            helper: "clone",
            drag: function( event, ui ) {
                obj_item = this;
            }
        });

        $("#container_y1").droppable({
            drop: function(event, ui) {

                var thisid = $(this).attr("id");
                var parentid = $(obj_item).parent().attr("id");
                var itemid = $(obj_item).attr("itemid");

                if(parentid=="container_y1" || parentid=="container_y2") {
                    $('#'+parentid+' .box-item').each(function() {
                        if ($(this).attr("itemid") === itemid) {
                            $(this).appendTo("#"+thisid);
                        }
                    });
                    load_graph();
                }
            }
        });

        $("#container_y2").droppable({
            drop: function(event, ui) {

                var thisid = $(this).attr("id");
                var parentid = $(obj_item).parent().attr("id");
                var itemid = $(obj_item).attr("itemid");

                if(parentid=="container_y1" || parentid=="container_y2") {
                    $("#container_y2 .box-item").each(function() {
                        $(this).appendTo("#container_y1");
                    });
                    $("#container_y2").html("");

                    $('#'+parentid+' .box-item').each(function() {
                        if ($(this).attr("itemid") === itemid) {
                            $(this).appendTo("#"+thisid);
                        }
                    });
                    load_graph();
                }
            }
        });

        $("#container_x1").droppable({
            drop: function(event, ui) {

                var thisid = $(this).attr("id");
                var parentid = $(obj_item).parent().attr("id");
                var itemid = $(obj_item).attr("itemid");

                if(parentid=="container_x1" || parentid=="container_x2") {
                    $('#'+parentid+' .box-item').each(function() {
                        if ($(this).attr("itemid") === itemid) {
                            $(this).appendTo("#"+thisid);
                        }
                    });
                    load_graph();
                }
            }
        });

        $("#container_x2").droppable({
            drop: function(event, ui) {

                var thisid = $(this).attr("id");
                var parentid = $(obj_item).parent().attr("id");
                var itemid = $(obj_item).attr("itemid");

                if(parentid=="container_x1" || parentid=="container_x2") {
                    $("#container_x2 .box-item").each(function() {
                        $(this).appendTo("#container_x1");
                    });
                    $("#container_x2").html("");
                    $('#'+parentid+' .box-item').each(function() {
                        if ($(this).attr("itemid") === itemid) {
                            $(this).appendTo("#"+thisid);
                        }
                    });
                    load_graph();
                }
            }
        });
        
    }
});


/*chart analysis*/
function get_graph_data()
{
    var obj = {graph_y:"",graph_x:"",graph_type:""};
    obj.graph_y    = ($("#container_y2 .box-item").size()>0) ? $("#container_y2 .box-item:first").attr("itemid") : "";
    obj.graph_x    = ($("#container_x2 .box-item").size()>0) ? $("#container_x2 .box-item:first").attr("itemid") : "";
    obj.graph_type = ($(".btn-app.selected").size()>0) ? $(".btn-app.selected").attr("graph-type") : "";
    obj.graph_id   = ($(".graph-list.active").size()>0) ? $(".graph-list.active").attr("graph-id") : "";

    $("#formPeriod input[name^=graph_y]").val(obj.graph_y);
    $("#formPeriod input[name^=graph_x]").val(obj.graph_x);
    $("#formPeriod input[name^=graph_type]").val(obj.graph_type);

    return obj;
}

function load_graph()
{
    var graph = get_graph_data();
    if(graph.graph_type=="Pie") {
        getChartPie();
    } else if(graph.graph_type=="Bar") {
        getChartBar();
    } else if(graph.graph_type=="Line") {
        getChartLine();
    } else if(graph.graph_type=="Table") {
        getChartTable();
    } else {
        $("#ChartContainer").html("");
        $(".choose-remark").show();
    }
}

$(function() {

    $("#GraphType .btn").click(function(){
        $("#ChartTable,#ChartTable table").hide();
        $("#GraphType .btn").removeClass("selected");
        $(this).addClass("selected");
        load_graph();
    });

    if($('#ChartContainer').size()>0) {
        if($(".btn-app.selected").size()==0) {
            $(".choose-remark").show();
        } else {
            $(".btn-app.selected").trigger("click");
        }
    }

});

var exporting = {
    buttons: {
        contextButton: {
            menuItems: [{
                text: 'Print chart',
                onclick: function() {
                    var w = $("#ChartContainer").width();
                    var h = $("#ChartContainer").height();
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
};

function getChartPie()
{
    var graph = get_graph_data();
    var url   = urlbase+"analysis/getChartPie";

    if(graph.graph_x=="" || graph.graph_y=="" || graph.graph_type=="") {
        $("#ChartContainer").html("");
        $(".choose-remark").show();
    } else {
        $(".choose-remark").hide();
        $.ajax({
            type : 'post',
            dataType : 'json',
            data: {
                graph_id:graph.graph_id,
                graph_x:graph.graph_x,
                graph_y:graph.graph_y,
                graph_type:graph.graph_type},
            url: url,
            beforeSend: function() {
                $.fancybox.showLoading();
            },
            error: function() {
                $.fancybox.hideLoading();
                dialog_error("No internet connection");
            },
            success : function(res) {
                $('#ChartContainer').highcharts({
                    credits: {
                        enabled: false
                    },
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        spacingRight: 0,
                        spacingLeft: 0,
                        spacingTop: 0,
                        spacingBottom: 0,
                        marginRight: 0,
                        marginLeft: 0,
                        marginTop: 20,
                        marginBottom: 0
                    },
                    exporting: exporting,
                    title: {
                        text: res.title
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                        },
                        series: {
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                formatter: function () {
                                    if(this.point.percentage>0) {
                                        return this.point.name+ " "+Highcharts.numberFormat(this.point.percentage,1)+"%";
                                    } else {
                                        return null;
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        formatter: function () {
                            if(this.point.percentage>0) {
                                return this.series.name+"<br />"+this.point.name+ ": "+Highcharts.numberFormat(this.point.percentage,1)+"%";
                            } else {
                                return null;
                            }
                        }
                    },
                    series: res.series
                });
                $.fancybox.hideLoading();
            }
        });
    }
}

function getChartBar()
{
    var graph = get_graph_data();
    var url   = urlbase+"analysis/getChartBar";

    if(graph.graph_x=="" || graph.graph_y=="" || graph.graph_type=="") {
        $("#ChartContainer").html("");
        $(".choose-remark").show();
    } else {
        $(".choose-remark").hide();
        $.ajax({
            type : 'post',
            dataType : 'json',
            data: {
                graph_id:graph.graph_id,
                graph_x:graph.graph_x,
                graph_y:graph.graph_y,
                graph_type:graph.graph_type},
            url: url,
            beforeSend: function() {
                $.fancybox.showLoading();
            },
            error: function() {
                $.fancybox.hideLoading();
                dialog_error("No internet connection");
            },
            success : function(res) {
                $('#ChartContainer').highcharts({
                    chart: {
                        type: 'column'
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: exporting,
                    title: {
                        text: res.title
                    },
                    xAxis: {
                        categories:res.categories,
                        crosshair: true
                    },
                    yAxis: {
                        title: {
                            text: res.ytitle
                        }
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: res.series
                });
                $.fancybox.hideLoading();
            }
        });
    }
}

function getChartLine()
{
    var graph = get_graph_data();
    var url   = urlbase+"analysis/getChartLine";

    if(graph.graph_x=="" || graph.graph_y=="" || graph.graph_type=="") {
        $("#ChartContainer").html("");
        $(".choose-remark").show();
    } else {
        $(".choose-remark").hide();
        $.ajax({
            type : 'post',
            dataType : 'json',
            data: {
                graph_id:graph.graph_id,
                graph_x:graph.graph_x,
                graph_y:graph.graph_y,
                graph_type:graph.graph_type},
            url: url,
            beforeSend: function() {
                $.fancybox.showLoading();
            },
            error: function() {
                $.fancybox.hideLoading();
                dialog_error("No internet connection");
            },
            success : function(res) {
                $('#ChartContainer').highcharts({
                    chart: {
                        type: 'line'
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: exporting,
                    title: {
                        text: res.title
                    },
                    xAxis: {
                        categories:res.categories,
                        crosshair: true
                    },
                    yAxis: {
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }],
                        title: {
                            text: res.ytitle
                        }
                    },
                    tooltip: {
                        shared: true
                    },
                    series: res.series
                });
                $.fancybox.hideLoading();
            }
        });
    }
}

function getChartTable()
{
    $('#ChartContainer').html("");
    $(".choose-remark").hide();
    $("#ChartTable,#ChartTable table").show();
}

$(document).ready(function() {
    $('#tableGraph').DataTable({
        "columnDefs": [
            { "width": "100px","targets": 0 },
            { "width": "80px","targets": 1 },
            { "width": "80px","targets": 2 },
            { "width": "200px","targets": 3 },
            { "width": "100px","targets": 4 },
            { "width": "400px","targets": 5 },
            { "width": "80px","targets": 6 },
            { "width": "80px","targets": 7 },
            { "width": "100px","targets": 8 },
            { "width": "80px","targets": 9 }
          ],
        "autoWidth": false,
        "pageLength": 10,
        "aoColumns": [ 
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false },
            { "bSortable": false }],
        "order": [[ 8, "desc" ]],
        "processing": true,
        "serverSide": true,
        // "dom": 'Bfrtip',
        // "buttons": ['excel'],
        "ajax": urlpath+"analysis/get_table_list",
        initComplete: function(settings, json) {
           $("#tableGraph").parents(".col-sm-12").css("overflow","auto");
           $("#tableGraph").css("width","1300px");
        }
    });

});