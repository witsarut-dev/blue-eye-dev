
var result_feed = {};
var result_rows = {};

$(function() {

    set_gap_time();

   /*marketing*/

    $(document).ready(function() {
        $(".CategoryBox .CategoryList .panel_toolbox a").on("click", function() {
        	var cate_id = $(this).parents(".CategoryList").attr("cate-id");
        	var parents = $(this).parents(".CategoryBox");

        	$(parents).find(".CategoryList[cate-id!="+cate_id+"] .panel_toolbox a i").removeClass("fa-chevron-up");
        	$(parents).find(".CategoryList[cate-id!="+cate_id+"] .panel_toolbox a i").addClass("fa-chevron-down");
        	$(parents).find(".CategoryList[cate-id!="+cate_id+"] .x_content.item-show").hide();

            var e = $(this).closest(".CategoryList"),
                t = $(this).find("i"),
                n = e.find(".x_content.item-show");
            e.attr("style") ? n.slideToggle(200, function() {
                e.removeAttr("style")
            }) : (n.slideToggle(200), e.css("height", "auto")), t.toggleClass("fa-chevron-up fa-chevron-down")
        }), $(".close-link").click(function() {
            var e = $(this).closest(".CategoryList");
            e.remove()
        })
    });

    if($(".CategoryBox .scroll-pane").size()>0) {
    	$(".CategoryBox .scroll-pane").height(400).jScrollPane({autoReinitialise: true})
		.bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
			var com_id = $(this).attr("com-id");
			var active = $(".scroll-pane[com-id="+com_id+"]").find(".fa-chevron-up");
			var cate_id = $(active).parents(".CategoryList").attr("cate-id");
			if(isAtBottom) get_result_feed(com_id,cate_id);
		});
    }

    $(".CategoryBox .CategoryList").each(function(){
    	var com_id  = $(this).parents(".scroll-pane").attr("com-id");
    	var cate_id = $(this).attr("cate-id");
    	if(!result_feed[com_id]) result_feed[com_id] = {};
    	if(!result_rows[com_id]) result_rows[com_id] = {};
    	result_feed[com_id][cate_id] = true;
        result_rows[com_id][cate_id] = 1;
    	get_result_feed(com_id,cate_id);
    });


    $(document).delegate('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive', 'click', function() {
        if($(this).hasClass("search-selected")) {
            $(".panel_toolbox button").removeClass("search-selected");
            Sentiment = "";
        } else {
            $(".panel_toolbox button").removeClass("search-selected");
            $(this).addClass("search-selected");
            Sentiment = $(this).attr("sentiment");
        }
        for(var com_id in result_feed) {
            result_rows2 = result_feed[com_id];
            for(var cate_id in result_rows2) {
                result_feed[com_id][cate_id] = true;
                result_rows[com_id][cate_id] = 1;
                get_result_feed(com_id,cate_id);
            }
        }
    });

    /*chart marketing*/
    if($('#ChartPositive').size()>0) createChartPositive(positiveData);
    if($('#ChartNegative').size()>0) createChartNegative(negativeData);

});

function get_result_feed(com_id,cate_id)
{
	if(result_feed[com_id][cate_id]) {
		result_feed[com_id][cate_id] = false;
		get_feed(com_id,cate_id);
	}
}

function get_feed(com_id,cate_id)
{
	var post_rows = 1;
	var url  = urlbase+"marketing/get_feed";

	post_rows = result_rows[com_id][cate_id];

    $.ajax({
        type : 'post',
        dataType : 'html',
        data: {
            post_type:"CategoryBox",
            com_id:com_id,
            cate_id:cate_id,
            post_rows:post_rows,
            media_type:MediaType,
            sentiment:Sentiment},
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            //dialog_error("Error");
            result_feed[com_id][cate_id] = true;
        },
        success : function(html) {
            if(post_rows==1) {
                $(".scroll-pane[com-id="+com_id+"] .CategoryList[cate-id="+cate_id+"]").find(".item-show").remove()
                $(".scroll-pane[com-id="+com_id+"] .CategoryList[cate-id="+cate_id+"]").append(html);
                $(".CategoryBox").each(function(){
                    $(this).find(".CategoryList:not(:first) .x_content").hide();
                });
            } else {
                $(".scroll-pane[com-id="+com_id+"] .CategoryList[cate-id="+cate_id+"]").append(html);
            }
        	if(html!="") {
            	result_feed[com_id][cate_id] = true; 
            	result_rows[com_id][cate_id]++;
        		load_fancybox();
            } else {
            	result_feed[com_id][cate_id] = false;
            }
            $.fancybox.hideLoading();
        }
    });
}

/*chart marketing*/
function createChartPositive(series)
{
    $('#ChartPositive').highcharts({
        credits: {
            enabled: false
        },
        chart: {
            polar: true,
            type: 'line',
            spacingRight: 0,
            spacingLeft: 0,
            spacingTop: 0,
            spacingBottom: 0,
            marginRight: 0,
            marginLeft: 0,
            marginTop: 20,
            marginBottom: 0
        },

        title: {
            text: '<h2 class="title"><span class="text-success">Positive</span></h2>',
            useHTML: true
        },

        pane: {
            size: '60%'
        },

        xAxis: {
            categories: categoryData,
            tickmarkPlacement: 'on',
            lineWidth: 0,
            labels: {
                formatter: function() {
                    return (this.value.toString().length < 2 ) ? '' : this.value.toString();
                }
            }
        },

        yAxis: {
            gridLineInterpolation: 'polygon',
            lineWidth: 0,
            min: 0,
            max: 1,
        },
        tooltip: {
            shared: true,
            pointFormat:'<span style="color:{series.color}">{series.name}: <b>Sen. {point.y:,.2f}</b><br/>',
            // formatter: function() {
            //     var value = (this.y>0) ? Highcharts.numberFormat(this.y, 2) : '0.00';
            //     return this.x+'<br /><span style="color:'+this.series.color+'">'+this.series.name+'</span>: <b>Sen. '+ value +'</b><br/>';
            // }
       },
        legend: {
            enabled: true,
            align: 'right',
            verticalAlign: 'top',
            y: 20,
            layout: 'vertical',
            floating: true,
        },

        series: series ,
        plotOptions: {
            series: {
                marker: {
                    enabled: false
                }
            }
        }

    });
}

function createChartNegative(series)
{
    $('#ChartNegative').highcharts({
        credits: {
            enabled: false
        },
        chart: {
            polar: true,
            type: 'line',
            spacingRight: 0,
            spacingLeft: 0,
            spacingTop: 0,
            spacingBottom: 0,
            marginRight: 0,
            marginLeft: 0,
            marginTop: 20,
            marginBottom: 0
        },
        title: {
            text: '<h2 class="title"><span class="text-danger">Negative</span></h2>',
            useHTML: true
        },

        pane: {
            size: '60%'
        },

        xAxis: {
            categories: categoryData,
            tickmarkPlacement: 'on',
            lineWidth: 0,
            labels: {
                formatter: function() {
                    return (this.value.toString().length < 2 ) ? '' : this.value.toString();
                }
            }
        },

        yAxis: {
            gridLineInterpolation: 'polygon',
            lineWidth: 0,
            min: 0,
            max: 1,
            labels: {
                formatter: function() {
                    return (this.value.toString() == 0 ) ? 0 : "-"+this.value.toString();
                }
            }
        },

        tooltip: {
            shared: true,
            pointFormat:'<span style="color:{series.color}">{series.name}: <b>Sen. {point.y:,.2f}</b><br/>',
            // formatter: function() {
            //     var value = (this.y>0) ? "-"+Highcharts.numberFormat(this.y, 2) : '0.00';
            //     return this.x+'<br /><span style="color:'+this.series.color+'">'+this.series.name+'</span>: <b>Sen. '+ value +'</b><br/>';
            // }
       },
        legend: {
            enabled: true,
            align: 'right',
            verticalAlign: 'top',
            y: 20,
            layout: 'vertical',
            floating: true,
        },

        series: series ,
        plotOptions: {
            series: {
                marker: {
                    enabled: false
                }
            }
        }

    });
}
