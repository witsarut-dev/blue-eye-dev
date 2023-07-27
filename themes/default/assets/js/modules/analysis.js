/*Analysis script*/
var GraphItem = null;
var GraphTarget = null;
var GraphColors = ["#6a040f","#9d0208","#d00000","#dc2f02","#e85d04","#f48c06","#faa307","#ffc01e","#ffd466","#ffc169","#feb16c","#fc9172","#fa5b7c","#f72585","#b5179e","#7209b7","#560bad","#480ca8","#3a0ca3","#3f37c9","#4361ee","#4895ef","#4cc9f0","#95d5b2","#74c69d","#52b788","#40916c","#2d6a4f"];
var tableGraph = null;
var tableGraph_Special = null;
var postData = null;

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
            $("#btnAddGraph").text("Close").removeClass("btn-primary").addClass("btn-danger");
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
                        $("#btnAddGraph").text("Add Graph").removeClass("btn-danger").addClass("btn-primary");
                        $("#BoxAddGraph").slideUp();
                    } else {
                        dialog_error(res.message);
                        if(res.error=="graph_y") {
                            $("html, body").animate({ scrollTop: $('#container_y1').offset().top - 100 }, 500);
                        } else if(res.error=="graph_x") {
                            $("html, body").animate({ scrollTop: $('#container_x1').offset().top - 100 }, 500);
                        } else if(res.error=="graph_type") {
                            $("html, body").animate({ scrollTop: $('.graph-type').offset().top - 100 }, 500);
                        }
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
            $(this).text("Close").removeClass("btn-primary").addClass("btn-danger");
            GraphItem = null;
        } else {
            $("#BoxAddGraph").slideUp();
            $("#txtGraphName").val("");
            $(this).text("Add Graph").removeClass("btn-danger").addClass("btn-primary");
            GraphItem = null;
        }
    });

    $(document).delegate(".DTFC_LeftHeadWrapper .chk-all","click",function(){

        if($(this).is(":checked")) {
            $(".DTFC_LeftBodyWrapper input[name^=post_id]").prop("checked",true);    
        } else {
            $(".DTFC_LeftBodyWrapper input[name^=post_id]").prop("checked",false);   
        }

        $(".DTFC_LeftBodyWrapper input[name^=post_id]").each(function(){
            var post_id = $(this).val();
            if($(this).is(":checked")) {
                postData.add(post_id);
            } else {
                postData.remove(post_id);
            }
        });
        postData.show_count();

    });

    $(document).delegate(".DTFC_LeftBodyWrapper input[name^=post_id]","click",function(){
        var post_id = $(this).val();
        if($(this).is(":checked")) {
            postData.add(post_id);
        } else {
            postData.remove(post_id);
        }
        postData.show_count();
    });

    $(document).delegate(".select-checkbox","click",function(){
        $(this).parent().find("input[type=checkbox]").trigger("click");
    });

    $(document).delegate(".btnDeletePostTB","click",function(){
        if(postData.count()==0) {
            dialog_error("กรุณาเลือกรายการที่ต้องการลบอย่างน้อย 1 รายการ");
        } else {
            if(window.confirm("คุณยืนยันที่จะลบข้อมูลหรือไม่")) {
                deletePost();
            }
        }
    });

    $(document).delegate(".btnBlockPostTB","click",function(){
        if(postData.count()==0) {
            dialog_error("กรุณาเลือกรายการที่ต้องการ Block อย่างน้อย 1 รายการ");
        } else {
            // if(window.confirm("คุณยืนยันที่จะ Block User หรือไม่")) {
            //     blockPost();
            // }
            var word = prompt("คุณยืนยันที่จะ Block User หรือไม่ \nกรุณากรอกคำว่า Block เพื่อยืนยัน: Block", "");
            if (word.toLocaleLowerCase() == 'block') {
                blockPost();
            } else  if(word != null) {
                dialog_error("คุณยืนยันการ block ไม่ถูกต้อง");
            }
        }
    });


    $(document).delegate(".btnHidePostTB","click",function(){
        if(postData.count()==0) {
            dialog_error("กรุณาเลือกรายการที่ต้องการซ่อนอย่างน้อย 1 รายการ");
        } else {
            if(window.confirm("คุณยืนยันที่จะซ่อนข้อมูลหรือไม่")) {
                hidePost();
            }
        }
    });

    $(document).delegate(".uncheck-all","click",function(){
        $(".DTFC_LeftHeadWrapper .chk-all").prop("checked",false);
        postData.remove_all();
    });

    $(".btn-full-screen").click(function(){
        if($(".list-box").is(":hidden")) {
            $(".list-box").show();
            $(".result-box").removeClass("col-lg-12").addClass("col-lg-7");
        } else {
            $(".list-box").hide();
            $(".result-box").removeClass("col-lg-7").addClass("col-lg-12");
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
                    $("#btnAddGraph").text("Add Graph").removeClass("btn-danger").addClass("btn-primary");
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
    obj.graph_y    = ($("#container_y2 .btn-success.selected").size()>0) ? $("#container_y2 .btn-success.selected").attr("itemid") : "";
    obj.graph_x    = ($("#container_x2 .btn-primary.selected").size()>0) ? $("#container_x2 .btn-primary.selected").attr("itemid") : "";
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
    $(".btn-full-screen").hide();
    if(graph.graph_type=="Pie") {
        getChartPie();
    } else if(graph.graph_type=="Bar") {
        getChartBar();
    } else if(graph.graph_type=="Line") {
        getChartLine();
    } else if(graph.graph_type=="Table") {
        getChartTable();
        $(".btn-full-screen").show();
    } else {
        $("#ChartContainer").html("");
        $(".choose-remark").show();
    }
}

$(function() {

    $("#GraphType #container_y2 .btn").click(function(){
        $("#ChartTable,#ChartTable table").css({"visibility":"hidden","height":"0px"});
        $("#GraphType #container_y2 .btn").removeClass("selected");
        $(this).addClass("selected");
        load_graph();
    });

    $("#GraphType #container_x2 .btn").click(function(){
        $("#ChartTable,#ChartTable table").css({"visibility":"hidden","height":"0px"});
        $("#GraphType #container_x2 .btn").removeClass("selected");
        $(this).addClass("selected");
        load_graph();
    });

    $("#GraphType .graph-type .btn").click(function(){
        $("#ChartTable,#ChartTable table").css({"visibility":"hidden","height":"0px"});
        $("#GraphType .graph-type .btn").removeClass("selected");
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
                    // this.exportChartLocal();
                    this.exportChart({type:"image/jpeg"});
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
                        style: {
                            fontFamily: '"Poppins", sans-serif'
                        },
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        marginRight: 0,
                        marginLeft: 0,
                        marginTop: 20,
                        marginBottom: 0
                    },
                    colors: GraphColors,
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
                        style: {
                            fontFamily: '"Poppins", sans-serif'
                        },
                        type: 'column'
                    },
                    credits: {
                        enabled: false
                    },
                    colors: GraphColors,
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
                        style: {
                            fontFamily: '"Poppins", sans-serif'
                        },
                        type: 'line'
                    },
                    colors: GraphColors,
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
    $("#ChartTable,#ChartTable table").css({"visibility":"visible","height":"auto"});
}

$(document).ready(function() {
    tableGraph = $('#tableGraph').DataTable({
        "columnDefs": [
            { "width": "40px","targets": 0,},
            { "width": "100px","targets": 1 },
            { "width": "150px","targets": 2 },
            { "width": "200px","targets": 3 },
            { "width": "300px","targets": 4 },
            { "width": "100px","targets": 5 },
            { "width": "100px","targets": 6 },
            { "width": "100px","targets": 7 },
            { "width": "150px","targets": 8 },
            { "width": "100px","targets": 9 },
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
        "order": [[ 6, "desc" ],[ 7, 'desc']],
        "processing": true,
        "serverSide": true,
        "searching": false,
        // "dom": 'Bfrtip',
        // "buttons": ['excel'],
        "ajax": urlpath + "analysis/get_table_list",
        scrollY:        500,
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns:   {
            leftColumns: 1
        },
        initComplete: function(settings, json) {
           //$("#tableGraph").parents(".col-sm-12").css("overflow","auto");
           $("#tableGraph").css("width","1500px");
           $("#tableGraph").css("height","50px");
           $("#tableGraph_wrapper .col-sm-6:eq(1)").html('<br /><div class="pull-right"><span class="uncheck-all">Uncheck All</span> <span class="label label-primary countChecked"><i class="fa fa-check-square"></i> <b>0</b></span></div>');
        },
        "fnDrawCallback": function() {
            $('.DTFC_LeftBodyWrapper input[name^=post_id]').css("opacity","0.5");
            setTimeout(function(){
                postData.check_all();
            },500);
            load_fancybox();
        }
    })
    .on( 'length.dt', function () { chagne_table_page(); } )
    .on( 'page.dt',   function () { chagne_table_page(); } )
});

function chagne_table_page()
{
    $(".DTFC_LeftHeadWrapper .chk-all").prop("checked",false);
}

function deletePost()
{
    var url  = urlbase+"analysis/cmdDeletePost";
    cmdActionTable(url);
}

function blockPost()
{
    var url  = urlbase+"analysis/cmdBlockPost";
    cmdActionTable(url);
}
function hidePost()
{
    var url  = urlbase+"analysis/cmdHidePost";
    cmdActionTable(url);
}

function cmdActionTable(url)
{
    var post_id = $("#input-post-id").find('input[name^="post_id"]').serialize();

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: post_id,
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
                $(".DTFC_LeftHeadWrapper .chk-all").prop("checked",false);
                tableGraph.ajax.reload();
                postData.remove_all();
                postData.show_count();
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

var PostData = function() {};

PostData.prototype.get = function(post_id) {
    if($("#input-post-id").find('input[value="'+post_id+'"]').size()>0) {
        return true;
    } else {
        return false;
    }
};

PostData.prototype.check_all = function() {
    $("#input-post-id").find('input[name^="post_id"]').each(function(){
        var post_id = $(this).val();
        $('.DTFC_LeftBodyWrapper input[name^=post_id][value="'+post_id+'"]').prop("checked",true);
    });
    $('.DTFC_LeftBodyWrapper input[name^=post_id]').css("opcity","1");
};

PostData.prototype.check = function(post_id) {
    if(this.get(post_id)>0) {
        $('.DTFC_LeftBodyWrapper input[name^=post_id][value="'+post_id+'"]').prop("checked",true);
    } else {
        $('.DTFC_LeftBodyWrapper input[name^=post_id][value="'+post_id+'"]').prop("checked",false);
    }
};

PostData.prototype.add = function(post_id) {
    $("#input-post-id").find('input[value="'+post_id+'"]').remove();
    $("#input-post-id").append('<input type="hidden" name="post_id[]" value="'+post_id+'" />');
};

PostData.prototype.remove = function(post_id) {
    $("#input-post-id").find('input[value="'+post_id+'"]').remove();
};

PostData.prototype.remove_all = function() {
    $(".DTFC_LeftBodyWrapper").find('input[name^="post_id"]').prop("checked",false);
    $("#input-post-id").find('input[name^="post_id"]').remove();
    $(".uncheck-all").hide();
    $(".countChecked b").html(number_format(0,0));
};

PostData.prototype.count = function() {
    var count = $("#input-post-id").find('input[name^="post_id"]').size();
    return count;
};

PostData.prototype.show_count = function() {
    var count = this.count();
    if(count>0) {
        $(".uncheck-all").show();
    } else {
        $(".uncheck-all").hide();
    }
    $(".countChecked b").html(number_format(count,0));
};

postData = new PostData();