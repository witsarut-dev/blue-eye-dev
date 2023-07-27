$(function() {

    get_media_data();
    requestKeywordData();
    requestSentimentData();
    requestGraphSentimentData();
    requestMarketposData();
    requestMediaposData();
    requestTotalData();
    requestMediaData();

    /*chart overview*/
    if($('#ChartMarketPos').size()>0) createArrowChart();
    if($('#ChartKeyword').size()>0) createChartKeyword(keywordData,null);
    if($('#ChartMedia').size()>0) createChartMedia(periodCurrent);
  
    if($('#ChartMarketPos').size()>0) createChartMarketPos(marketPosData);
    if($('#ChartMediaPos').size()>0) createChartMediaPos(mediaPosData);

    if($('#ChartSentiment').size()>0) createChartSentiment(graphSentimentData,null);

    $(window).resize(function(){
        if($('#ChartMarketPos').size()>0) createArrowChart();
        if($('#ChartKeyword').size()>0) createChartKeyword(keywordData,null);
        if($('#ChartMedia').size()>0) createChartMedia(periodCurrent);
        if($('#ChartMarketPos').size()>0) createChartMarketPos(marketPosData);
        if($('#ChartMediaPos').size()>0) createChartMediaPos(mediaPosData);
        if($('#ChartSentiment').size()>0) createChartSentiment(graphSentimentData,null);
    });

    $(".btnTotalBefore").each(function(){
        if($(this).is(":checked")) {
            $(this).parents(".panel-body").find(".before").fadeTo( "slow" , 0.5, function() {});
        }
    });

    $(".btnTotalBefore").click(function(){
        if($(this).is(":checked")) {
            $(this).parents(".panel-body").find(".before").fadeTo( "slow" , 0.5, function() {});
        } else {
            $(this).parents(".panel-body").find(".before").fadeTo( "slow" , 0, function() {});
        }
    });

    $("#formMediaCom a").click(function(){
        showOnLoading();
        var media_com = $(this).attr("media-com");
        $("#formMediaCom input[name=media_com]").val(media_com);
        $("#formMediaCom").submit();
    });

    $(".btn-media-back").click(function(){
        close_keyword_crosstab();
    });
});



$(document).ready(function() {
    $.ajax({
        url: urlbase+"overview/ajax_group_keyword_list", 
        type: "post", 
        dataType: 'json',
        data:{period:PeriodType},
        success: function(result){
            for (var i = 0; i < result.length; i++) {
                $('#tableGraph_data').append('<tr><td>' + result[i].name + '</td><td>' + result[i].Facebook + "</td><td> " + result[i].Twitter+ '</td><td>' + result[i].Youtube + '</td><td>' + result[i].Instagram + '</td><td>' + result[i].Tiktok + '</td><td>' + result[i].Line + '</td><td>' + result[i].News +'</td><td>' + result[i].Webboard + '</td><<td>' + result[i].Total +'</td></tr>');
                
            }
        }
    });
});
// $(document).ready(function() {
//     $.ajax({
//         url: urlbase+"overview/ajax_group_keyword_list", 
//         type: "post", 
//         dataType: 'json',
//         data:{period:PeriodType},
//         success: function(result){
//             var tableRows = '';
//             for (var i = 0; i < result.length; i++) {
//                 var backgroundColor = i % 2 == 0 ? '#fff' : '#f2f2f2';
//                 tableRows += '<tr style="background: ' + backgroundColor + ';"><td>' + result[i].name + '</td><td>' + result[i].Facebook + "</td><td> " + result[i].Twitter+ '</td><td>' + result[i].Youtube + '</td><td>' + result[i].Instagram + '</td><td>' + result[i].Tiktok + '</td><td>' + result[i].Line + '</td><td>' + result[i].News +'</td><td>' + result[i].Webboard + '</td><<td>' + result[i].Total +'</td></tr>';
//             }
//             $('#tableGraph_data').append(tableRows);
//         }
//     });
// });


function get_media_data()
{
    for (var i = 0; i < mediaData.length; i += 1) {

        periodCurrent.push({
            name: mediaCategories[i],
            y: mediaData[i].y,
            url : mediaData[i].url,
            color : mediaData[i].color,
            mediaChannel : mediaData[i].mediaChannel,
            countPercent : mediaData[i].countPercent,
            countPositive : mediaData[i].countPositive,
            countNegative : mediaData[i].countNegative,
            countNormal : mediaData[i].countNormal,
            topPositive : mediaData[i].topPositive,
            topNegative : mediaData[i].topNegative,
            topNormal : mediaData[i].topNormal
        });

        // var drillDataLen = mediaData[i].drilldown.data.length;
        // for (var j = 0; j < drillDataLen; j += 1) {
        //     periodBefore.push({
        //         name: mediaData[i].drilldown.categories[j],
        //         y: mediaData[i].drilldown.data[j],
        //         url : mediaData[i].url,
        //         color : mediaData[i].color
        //     });
        // }
    }
}

function requestMediaData()
{
    if(PeriodType=="Today") {
        var url  = urlbase+"overview/ajax_media_data";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestMediaData, 1000*60); 
            },
            success : function(res) {
                periodBefore    = [];
                periodCurrent   = [];
                mediaData       = res.mediaData;
                mediaCategories = res.mediaCategories;
                get_media_data();
                createChartMedia(periodCurrent);
                countMediaData(res.mediaData);
                setTimeout(requestMediaData, 1000*60);  
            }
        });
    }
}

function countMediaData(data)
{
    var result = {SW:0,WB:0,NW:0};
    for(var i in data) {
        var obj = data[i];
        if(obj.drilldown.categories=='WB') {
            result.WB = obj.y;
        } else if(obj.drilldown.categories=='NW') {
            result.NW = obj.y;
        } else {
            result.SW += obj.y;
        }
    }
    $(".count-sm").text(number_format(result.SW,0));
    $(".count-wb").text(number_format(result.WB,0));
    $(".count-nw").text(number_format(result.NW,0));
}

function requestMarketposData()
{
    if(PeriodType=="Today") {
        var url  = urlbase+"overview/ajax_marketpos_data";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestMarketposData, 1000*60);
            },
            success : function(res) {
                marketPosData = res;
                createChartMarketPos(marketPosData);
                setTimeout(requestMarketposData, 1000*60);  
            }
        });
    }
}

function requestMediaposData()
{
    if(PeriodType=="Today") {
        var url  = urlbase+"overview/ajax_mediapos_data";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType,media_com:$("input[name=media_com]").val()},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestMediaposData, 1000*60);
            },
            success : function(res) {
                mediaPosData = res;
                createChartMediaPos(mediaPosData);
                setTimeout(requestMediaposData, 1000*60);  
            }
        });
    }
}

function requestSentimentData()
{
    if(PeriodType=="Today") {
        var url  = urlbase+"overview/ajax_sentiment_data";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestSentimentData, 1000*60);
            },
            success : function(res) {
                var Positive = res.Positive;
                var Normal = res.Normal;
                var Negative = res.Negative;
                $(".progress-bar-success").css("width",Positive+"%").find('span').text(Positive);
                $(".progress-bar-white").css("width",Normal+"%").find('span').text(Normal);
                $(".progress-bar-danger").css("width",Negative+"%").find('span').text(Negative);
                setTimeout(requestSentimentData, 1000*60);  
            }
        });
    }
}

function requestTotalData(type)
{
    if(PeriodType=="Today") {
        var url  = urlbase+"overview/ajax_total_data";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestTotalData, 1000*60);
            },
            success : function(res) {
                if(type==null || type=="mention") $("#mentionCurrent span").text(res.mentionCurrent);
                if(type==null || type=="mention") $("#mentionBefore span").text(res.mentionBefore);
                if(type==null || type=="user") $("#userCurrent span").text(res.userCurrent);
                if(type==null || type=="user") $("#userBefore span").text(res.userBefore);
                setTimeout(requestTotalData, 1000*60);  
            }
        });
    }
}

/*chart overview*/
function requestKeywordData() {
    if(PeriodType=="Today") {
        var url  = urlbase+"overview/ajax_keyword_data";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestKeywordData, 1000*60);
            },
            success : function(res) {
                var series = ChartKeyword.series;
                var length = series.length - 1;
                for(var i=0; i<length;i++) {
                    if(res[i].length>0) {
                        for(var j in  res[i]) {
                            var point = res[i][j];
                            series[i].addPoint(point,true,true);
                        }
                    }
                }
                setTimeout(requestKeywordData, 1000*60);  
            }
        });
    }
}

function get_max_Interval(series)
{
    var max = 0;
    for(var i in series) {
        var key = series[i];
        if(key.data.length>0) {
            for(var j in key.data) {
                var data = key.data[j];
                if(data[1]>max) max = data[1];
            }
        }
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
            var step = (start/10);
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

var ChartKeyword;

function createChartKeyword(series,col) {

    if(series.length>0) {

        var tickIntervalY = get_max_Interval(series);
        var tickIntervalX = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 60*1000*60 : null;
        var selected = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 0 : 4;
        

        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

        ChartKeyword = Highcharts.stockChart('ChartKeyword', {
            chart: {
                style: {
                    fontFamily: '"Poppins", sans-serif'
                },
                marginRight: 0,
                marginLeft: 0,
                events : {
                    load : requestKeywordData
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
            yAxis: {
                title: {
                    enabled: false,
                    text: 'Keyword',
                    align: "high",
                    rotation: 0,
                    y : -20,
                    x : -40,
                    offset : -20
                },
                lineWidth:1,
                tickInterval : tickIntervalY,
                // minRange : 1
            },
            plotOptions: {
                series: {
                    dataGrouping: {
                        enabled: false
                    },
                    marker: {
                        states: {
                            hover: {
                                enabled: true,
                                animation: {
                                    duration: 100
                                },
                                enableMouseTracking: true,
                                stickyTracking: true
                            }
                        }
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                if(this.y >= 1) {
                                    showOnLoading();
                                    window.location.href = urlpath+"realtime/?keyword="+this.series.name+"&time="+this.x;
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
                enabled: true,
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'middle',
                floating: true,
                y : -100
            },
            tooltip: {
                // pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} ครั้ง</b><br/>',
                // //valueDecimals: 0
                shared: true,
                formatter: function () {
                    var points = this.points;
                    if (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) {
                        var dateLabel = Highcharts.dateFormat('%A %b, %e, %H:%M',new Date(this.x));
                    } else {
                        var dateLabel = Highcharts.dateFormat('%A %b, %e, %Y',new Date(this.x));
                    }
                    var pointsLength = points.length;
                    var tooltipMarkup = pointsLength ? '<span style="font-size: 10px">' +dateLabel + '</span><br/>' : '';
                    var index;
                    var rows = 0;

                    for(index = 0; index < pointsLength; index += 1) {
                        var y_value = points[index].y
                        if(y_value>0) {
                            tooltipMarkup += '<span style="color:' + points[index].series.color + '">\u25CF</span> ' + points[index].series.name + ': <b>' + y_value  + ' ครั้ง</b><br/>';
                            rows++;
                        }
                    }

                    if (rows>0) {
                        return tooltipMarkup;
                    } else {
                        return false;
                    }
                }
            },
            series: series,
            rangeSelector : { 
                selected: selected,
                enabled : true,
                buttons: [{
                            count: 6,
                            type: 'hour',
                            text: '12H'
                        },{
                            type: 'day',
                            count: 1,
                            text: 'Now'
                        },{
                            type: 'day',
                            count: 7,
                            text: 'Week'
                        }, {
                            type: 'year',
                            count: 1,
                            text: 'Month'
                        }, {
                            type: 'all',
                            text: 'Year'
                        }, {
                            type: 'ytd',
                            text: 'Initiaion'
                        }],
                inputEnabled:true,
                inputDateFormat:'%d/%m/%Y'
                // ,inputEditDateFormat:'%d/%m/%Y'
            }
        });
		
    }
	

}
// $(document).delegate('.buttonExport', 'click', function () {
	// 	var svgString = ChartKeyword.getSVG();
	// 	console.log(svgString);

    // 		// Use DOMParser to parse new svg element from svgString
    // 		let parser = new DOMParser(); 
    // 		let svgElem = parser.parseFromString(svgString, "image/svg+xml").documentElement;

    // 		// Use toDataURL extension to generate Base64 string
    // 		let b64 = svgElem.toDataURL();

    // 		// Log string in console
    // 		console.log(b64);

	// 	var url  = urlbase+"overview/ajax_graph_sentiment_data"; themes/default/assets/js/modules
    //     	console.log(url);
    // });

Math.easeOutBounce = function (pos) {
    if ((pos) < (1 / 2.75)) {
        return (7.5625 * pos * pos);
    }
    if (pos < (2 / 2.75)) {
        return (7.5625 * (pos -= (1.5 / 2.75)) * pos + 0.75);
    }
    if (pos < (2.5 / 2.75)) {
        return (7.5625 * (pos -= (2.25 / 2.75)) * pos + 0.9375);
    }
    return (7.5625 * (pos -= (2.625 / 2.75)) * pos + 0.984375);
};


function createChartMedia(periodCurrent) {

    var series = [{
            name: 'Media Channel',
            data: periodCurrent,
            size: '80%',
            innerSize: '60%',
            dataLabels: {
                distance : 5
            }
        }];

    $('#ChartMedia').highcharts({
        credits: {
            enabled: false
        },
        chart: {
            style: {
                fontFamily: '"Poppins", sans-serif'
            },
            type: 'pie',
            spacingRight: 0,
            spacingLeft: 0,
            spacingTop: 0,
            spacingBottom: 0,
            marginRight: 25,
            marginLeft: 25,
            marginTop: 0,
            marginBottom: 0
        },
        title: {
            text: '',
            useHTML: true,
            margin : 0
        },
        plotOptions: {
            pie: {
                shadow: true,
                center: ['50%', '50%'],
                borderWidth:0,
            },
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            // if(this.series.name=="Media period current") {
                            //     var url = this.options.url+"&period_type=current";
                            // } else {
                            //     var url = this.options.url+"&period_type=before";
                            // }
                            // if(this.percentage>0) location.href = url;
                            open_keyword_crosstab(this.name);
                        }
                    }
                },
                dataLabels: {
                    useHTML: true,
                    enabled: true,
                    formatter: function () {
                        if(this.point.percentage>0) {
                            var img = '<i class="ico ico-'+this.point.name.toLowerCase()+'" style="float: right;position: relative;right: 9px;top:-3px"></i>';
                            //return img+' <span style="font-size:10px;font-family: tahoma;color:#000">'+this.point.name+ " "+Highcharts.numberFormat(this.point.percentage,1)+"%</span>";
                            var html = img+'<br /> <span style="font-size:9px;font-family: tahoma;color:#888888">'+" "+Highcharts.numberFormat(this.point.percentage,2)+"%</span>";
                            html = '<a href="javascript:;" onclick="open_keyword_crosstab(\''+this.key+'\')">'+html+'</a>';
                            return html;
                        } else {
                            return null;
                        }
                    }
                },
                animation: {
                    duration: 2500,
                    easing: 'easeOutBounce'
                }
            }
        },
        tooltip: {
            padding: 0,
            useHTML: true,
            backgroundColor: null,
            shadow: false,
            style : {
                color:"#ffffff",
            },
            formatter: function () {
                if(this.point.percentage>0) {
                    var img = '<i class="ico ico-'+this.point.name.toLowerCase()+'"></i>';
                    return '<div style="background-color:#000000;padding:5px;margin:-10px">'+this.series.name+"<br />"+img+' '+this.point.name+ ": "+Highcharts.numberFormat(this.point.percentage,2)+"%</div>";
                } else {
                    return null;
                }
            }
        },
        series: series
    });
}

function open_keyword_crosstab(media_channel)
{
    $("#ChartMedia").animate({opacity: 0}, 500,function(){
        $("#KeywordCrossTab").fadeIn();
        $(".btn-media-back").css('visibility','visible');
        $("#KeywordCrossTab #MediaKeywordProgress").find('.progress2-bar-success').html('').css("width",'0');
        $("#KeywordCrossTab #MediaKeywordProgress").find('.progress2-bar-danger').html('').css("width",'0');
        $("#KeywordCrossTab #MediaKeywordProgress").find('.progress2-bar-grey').html('').css("width",'0');
        $("#KeywordCrossTab #media-percent").html('');
        $("#KeywordCrossTab #media-channel").html('');
        $("#KeywordCrossTab #TableTopPositive tbody").find('tr').remove();
        $("#KeywordCrossTab #TableTopNegative tbody").find('tr').remove();
        $("#KeywordCrossTab #TableTopNormal tbody").find('tr').remove();
        if(periodCurrent.length>1) {
            for(var i in periodCurrent) {
                var obj = periodCurrent[i];
                if(obj.name==media_channel) {
                    var html_count = '<span class="counterAnim label label-default" style="font-size: 14px;">'+number_format(obj.y,0)+'</span>';
                    $("#KeywordCrossTab #MediaKeywordProgress").find('.progress2-bar-success').html('<span class="counterAnim">'+obj.countPositive+'</span>%').css("width",obj.countPositive+'%');
                    $("#KeywordCrossTab #MediaKeywordProgress").find('.progress2-bar-danger').html('<span class="counterAnim">'+obj.countNegative+'</span>%').css("width",obj.countNegative+'%');
                    $("#KeywordCrossTab #MediaKeywordProgress").find('.progress2-bar-grey').html('<span class="counterAnim">'+obj.countNormal+'</span>%').css("width",obj.countNormal+'%');
                    $("#KeywordCrossTab #media-percent").html('<span class="counterAnim">'+obj.countPercent+'</span>%');
                    $("#KeywordCrossTab #media-channel").html('<a href="'+obj.url+'">'+obj.mediaChannel+'</a> '+html_count);
                    for(var j in obj.topPositive) {
                        var obj2 = obj.topPositive[j];
                        var html = "";
                        html += '<tr>';
                        html += '<td class="col-key">'+obj2.key+'</td>';
                        html += '<td class="col-sen"><span class="counterAnim">'+obj2.sen+'</span>%</td>';
                        html += '</tr>';
                        $("#KeywordCrossTab #TableTopPositive tbody").append(html);
                    }
                    for(var j in obj.topNegative) {
                        var obj2 = obj.topNegative[j];
                        var html = "";
                        html += '<tr>';
                        html += '<td class="col-key">'+obj2.key+'</td>';
                        html += '<td class="col-sen"><span class="counterAnim">'+obj2.sen+'</span>%</td>';
                        html += '</tr>';
                        $("#KeywordCrossTab #TableTopNegative tbody").append(html);
                    }
                    for(var j in obj.topNormal) {
                        var obj2 = obj.topNormal[j];
                        var html = "";
                        html += '<tr>';
                        html += '<td class="col-key">'+obj2.key+'</td>';
                        html += '<td class="col-sen"><span class="counterAnim">'+obj2.sen+'</span>%</td>';
                        html += '</tr>';
                        $("#KeywordCrossTab #TableTopNormal tbody").append(html);
                    }
                }
            }
        }
        $(".counterAnim").counterUp({ delay: 10,time: 1000});
    });
}

function close_keyword_crosstab()
{
    $("#KeywordCrossTab").fadeOut();
    $(".btn-media-back").css('visibility','hidden');
    $("#ChartMedia").animate({opacity: 1}, 500,function(){
            
    });
}

function createChartMarketPos(series) {

  var chart = new Highcharts.Chart({
        chart : {
            style: {
                fontFamily: '"Poppins", sans-serif'
            },
            marginTop: "70",
            marginLeft : "25",
            zoomType: 'xy',
            renderTo: 'ChartMarketPos',
            className: 'line-arrow',
            events : {
                load : function() {
                    if(series[0].name==null) {
                        $("#ChartMarketPos .highcharts-legend-item").hide();
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: '',
            useHTML: true
        },
        xAxis: {
            title: {
                text: 'Mention',
                offset: -150,
                align: "high"
            },
            plotLines: [{
                color: '#000000',
                width: 2,
                value: 0.5
            }],
            max: 1,
            min: 0,
            tickInterval : 1
        },
        yAxis: {
            title: {
                align: "high",
                rotation: 0,
                text: 'Sentiment',
                y : 15,
                offset : -170
            },
            plotLines: [{
                color: '#000000',
                width: 2,
                value: 0
            }],
            max: 1,
            min: -1,
            tickInterval : 1
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            borderWidth: 0,
            floating: true,
            y : -15,
            symbolWidth: 10,
            symbolRadius: 10,
        },
        series: series,
        plotOptions: {
            series: {
                marker: {
                    enabled: false
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            showOnLoading();
                            window.location.href = urlpath+"marketing";
                        }
                    }
                }
            }
        },
        tooltip: {
            enabled: true,
            useHTML : true,
            formatter: function () {
                var total = this.series.userOptions.total;
                var name = this.series.userOptions.name;
                var setiment = this.y;
                var mention = this.x;
                var color = this.color;
                var total = parseInt(Math.round(total * mention));

                var tooltip = '<div style="background-color:#000000;padding:5px;margin:-10px">';
                tooltip += '<b style="color:' + color + '">\u25CF</b> <b style="font-size:14px">' +name + '</b>';
                tooltip += '<br /><b>Mention : '+total+' ('+mention+')</b>';
                tooltip += '<br /><b>Sentiment : '+setiment+'</b>';
                tooltip += '</div>'
        
                return tooltip;
            }
        }
    });
}

function createChartMediaPos(series) {

  var chart = new Highcharts.Chart({
        chart : {
            style: {
                fontFamily: '"Poppins", sans-serif'
            },
            marginTop: "70",
            marginLeft : "25",
            zoomType: 'xy',
            renderTo: 'ChartMediaPos',
            className: 'line-arrow',
            events : {
                load : function() {
                    if(series[0].name==null) {
                        $("#ChartMediaPos .highcharts-legend-item").hide();
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: '',
            useHTML: true
        },
        xAxis: {
            title: {
                text: 'Mention',
                offset: -150,
                align: "high"
            },
            plotLines: [{
                color: '#000000',
                width: 2,
                value: 0.5
            }],
            max: 1,
            min: 0,
            tickInterval : 1
        },
        yAxis: {
            title: {
                align: "high",
                rotation: 0,
                text: 'Sentiment',
                y : 15,
                offset : -170
            },
            plotLines: [{
                color: '#000000',
                width: 2,
                value: 0,
            }],
            max: 1,
            min: -1,
            tickInterval : 1
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            borderWidth: 0,
            floating: true,
            y : -15,
            useHTML: true,
            symbolPadding: 0,
            symbolWidth: 0,
            symbolRadius: 0,
            labelFormatter: function () {
                var img = '<i class="ico ico-'+this.name.toLowerCase()+'" style=""></i>';
                return img+' <span style="font-size:10px;font-family: tahoma;">'+this.name+"</span>";
            }
        },
        series: series,
        plotOptions: {
            series: {
                marker: {
                    enabled: false
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                           location.href = urlpath+"marketing/?media_type="+this.series.name;
                        }
                    }
                }
            },
        },
        tooltip: {
            enabled: true,
            useHTML : true,
            formatter: function () {
                var total = this.series.userOptions.total;
                var name = this.series.userOptions.name;
                var setiment = this.y;
                var mention = this.x;
                var color = this.color;
                var total = parseInt(Math.round(total * mention));

                var tooltip = '<div style="background-color:#000000;padding:5px;margin:-10px">';
                tooltip += '<b style="color:' + color + '">\u25CF</b> <b style="font-size:14px">' +name + '</b>';
                tooltip += '<br /><b>Mention : '+total+' ('+mention+')</b>';
                tooltip += '<br /><b>Sentiment : '+setiment+'</b>';
                tooltip += '</div>'
        
                return tooltip;
            }
        }
    });
}

function createArrowChart()
{
    (function(H) {

    var arrowCheck = false,
        pathTag;

    H.wrap(H.Series.prototype, 'drawGraph', function(proceed) {

        // Now apply the original function with the original arguments, 
        // which are sliced off this function's arguments
        proceed.apply(this, Array.prototype.slice.call(arguments, 1));

        // used a bit of regex to clean up the series name enough to be used as an id
        var arrowName = "arrow"+this.name.replace(/\W/g,"_").toLowerCase();
        // console.log("----------->arrowName:"+arrowName)

        var arrowLength = 15,
        arrowWidth = 7,
        series = this,
        data = series.data,
        len = data.length,
        segments = data,
        lastSeg = segments[segments.length - 1],
        path = [],
        lastPoint = null,
        nextLastPoint = null;

        var className = this.chart.userOptions.chart.className;

        if (typeof lastSeg !== 'undefined' && lastSeg!=null && className=="line-arrow") {
           
              if (lastSeg.y == 0) {
                lastPoint = segments[segments.length - 2];
                nextLastPoint = segments[segments.length - 1];
              } else {
                lastPoint = segments[segments.length - 1];
                nextLastPoint = segments[segments.length - 2];
              }

              var angle = Math.atan((lastPoint.plotX - nextLastPoint.plotX) /
                (lastPoint.plotY - nextLastPoint.plotY));

              if (angle < 0) angle = Math.PI + angle;

              path.push('M', lastPoint.plotX, lastPoint.plotY);

              if (lastPoint.plotX > nextLastPoint.plotX || (lastPoint.plotX==nextLastPoint.plotX && lastPoint.plotY > nextLastPoint.plotY)) {

                if (arrowCheck === true) {
                  //replaced 'arrow' with arrowName
                  pathTag = document.getElementById(arrowName);
                  if (pathTag != null) {
                    pathTag.remove(pathTag);
                  }
                }

                path.push(
                  'L',
                  lastPoint.plotX + arrowWidth * Math.cos(angle),
                  lastPoint.plotY - arrowWidth * Math.sin(angle)
                );
                path.push(
                  lastPoint.plotX + arrowLength * Math.sin(angle),
                  lastPoint.plotY + arrowLength * Math.cos(angle)
                );
                path.push(
                  lastPoint.plotX - arrowWidth * Math.cos(angle),
                  lastPoint.plotY + arrowWidth * Math.sin(angle),
                  'Z'
                );
              } else {


                if (arrowCheck === true) {
                  //replaced 'arrow' with arrowName
                  pathTag = document.getElementById(arrowName);
                  if (pathTag != null) {
                    pathTag.remove(pathTag);
                  }
                }

                path.push(
                  'L',
                  lastPoint.plotX - arrowWidth * Math.cos(angle),
                  lastPoint.plotY + arrowWidth * Math.sin(angle)
                );
                path.push(
                  lastPoint.plotX - arrowLength * Math.sin(angle),
                  lastPoint.plotY - arrowLength * Math.cos(angle)
                );
                path.push(
                  lastPoint.plotX + arrowWidth * Math.cos(angle),
                  lastPoint.plotY - arrowWidth * Math.sin(angle),
                  'Z'
                );
              }

              series.chart.renderer.path(path)
                .attr({
                  fill: series.color,
                  id: arrowName //changed from 'arrow' to arrowName to enable more than one series with an arrow
                })
                .add(series.group);

               arrowCheck = true;

        }

    });
  }(Highcharts));
}

/*chart sentiment*/
function requestGraphSentimentData() {
    if(PeriodType=="Today") {
        var url  = urlbase+"overview/ajax_graph_sentiment_data";
        $.ajax({
            type : 'post',
            dataType : 'json',
            data:{period:PeriodType},
            url: url,
            cache: false,
            beforeSend: function() {
            },
            error: function() {
                //dialog_error("Error");
                setTimeout(requestGraphSentimentData, 1000*60);
            },
            success : function(res) {
                var series = ChartSentiment.series;
                var length = series.length - 1;
                for(var i=0; i<length;i++) {
                    if(res[i].length>0) {
                        for(var j in  res[i]) {
                            var point = res[i][j];
                            series[i].addPoint(point,true,true);
                        }
                    }
                }
                setTimeout(requestGraphSentimentData, 1000*60);  
            }
        });
    }
}

var ChartSentiment;

function createChartSentiment(series,col) 
{
    const SEVEN_HOURS_IN_MS = 7 * 60 * 60 * 1000; // 7 hours in milliseconds

    const arrWithAddedHours = series.map(obj => ({
    ...obj,
    data: obj.data.map(([timestamp, value]) => [timestamp + SEVEN_HOURS_IN_MS, value])
    }));
    console.log(arrWithAddedHours);
    if(arrWithAddedHours.length>0) {

        if(PeriodType=="Today") {
            // ชื่อที่จะถูกระบุในแนวตั้งของกราฟ เช่น จำนวน
            var tickIntervalY = get_max_Interval(arrWithAddedHours);
            // ชื่อที่จะถูกระบุในแนวนอนของกราฟ เช่น เวลา 
            var tickIntervalX = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 60*1000*60 : null;
            var selected = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 0 : 4;
            // start เริ่มต้นตั้งแต่ 00.00 น. ของวัน และ end จบ 23.59 น.
            var start = new Date();
            start.setUTCHours(-7, 0, 0, 0);
            var end = new Date();
            end.setUTCHours(16, 59, 59, 999);

            const today = new Date();
            const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate()); // set time to 00:00:00
            const endOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59); // set time to 23:59:59
            const min = Date.parse(startOfToday) + SEVEN_HOURS_IN_MS;
            const max = Date.parse(endOfToday) + SEVEN_HOURS_IN_MS;
            console.log(min);
            console.log(max);
            console.log("max");

            Highcharts.setOptions({
                global: {
                    useUTC: false
                }
            });
            ChartSentiment = Highcharts.stockChart('ChartSentiment', {
                chart: {
                    type: 'column',
                    style: {
                        fontFamily: '"Poppins", sans-serif'
                    },
                    marginRight: 0,
                    marginLeft: 0,
                    events : {
                        load : requestGraphSentimentData
                    }
                },
                colors: ['#25D366', '#E2E2E2', '#FF3F3F']
                ,
                navigator: {
                    enabled: false      
                },
                scrollbar: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    type: 'datetime',
                    ordinal: false,
                    min: min,
                    max: max,
                    labels: {
                        formatter: function () {
                            return Highcharts.dateFormat('%H:%M', this.value - (7 * 3600 * 1000));
                        }
                    }
                },
                yAxis: {
                    lineWidth: 1,
                    tickInterval : tickIntervalY
                },
                legend: {
                    enabled: true,
                    align: 'right',
                    x: -30,
                    verticalAlign: 'top',
                    y: 5,
                    floating: true,
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                    shadow: false
                },
                tooltip: {
                    enabled: false,
        
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{point.series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
                series: arrWithAddedHours
                
            });
        } 
        else {
            // ชื่อที่จะถูกระบุในแนวตั้งของกราฟ เช่น จำนวน
            var tickIntervalY = get_max_Interval(arrWithAddedHours);
            // ชื่อที่จะถูกระบุในแนวนอนของกราฟ เช่น เวลา 
            var tickIntervalX = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 60*1000*60 : null;
            var selected = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 0 : 4;
            Highcharts.setOptions({
                global: {
                    useUTC: false
                }
            });

            ChartSentiment = Highcharts.stockChart('ChartSentiment', {
                chart: {
                    type: 'column',
                    style: {
                        fontFamily: '"Poppins", sans-serif'
                    },
                    marginRight: 0,
                    marginLeft: 0,
                    events : {
                        load : requestGraphSentimentData
                    }
                },
                colors: ['#25D366', '#E2E2E2', '#FF3F3F']
                ,
                navigator: {
                    enabled: false      
                },
                scrollbar: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    tickInterval: tickIntervalX,
                    ordinal: false,
                    dateTimeLabelFormats: {
                            millisecond: '%H:00',
                            second: '%H:00',
                            minute: '%H:00',
                            hour: '%H:00',
                            day: '%e %b',
                            week: '%e. %b',
                            month: '%b %y',
                            year: '%Y'
                    }
                },
                yAxis: {
                    lineWidth: 1,
                    tickInterval : tickIntervalY
                },
                legend: {
                    enabled: true,
                    align: 'right',
                    x: -30,
                    verticalAlign: 'top',
                    y: 5,
                    floating: true,
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                    shadow: false
                },
                tooltip: {
                    enabled: false,
        
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
                series: arrWithAddedHours
                
            });
        }
    }
}