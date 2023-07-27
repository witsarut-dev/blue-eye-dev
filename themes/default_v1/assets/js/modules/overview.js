$(function() {
    get_media_data();
    requestSentimentData();
    requestMarketposData();
    requestMediaposData();
    requestTotalData();
    requestMediaData();

    /*chart overview*/
    if($('#ChartMarketPos').size()>0) createArrowChart();
    if($('#ChartKeyword').size()>0) createChartKeyword(keywordData,null);
    if($('#ChartMedia').size()>0) createChartMedia(periodBefore,periodCurrent,false);
    if($('#ChartMarketPos').size()>0) createChartMarketPos(marketPosData);
    if($('#ChartMediaPos').size()>0) createChartMediaPos(mediaPosData);

    $(window).resize(function(){
        if($('#ChartMarketPos').size()>0) createArrowChart();
        if($('#ChartKeyword').size()>0) createChartKeyword(keywordData,null);
        if($('#ChartMedia').size()>0) createChartMedia(periodBefore,periodCurrent,false);
        if($('#ChartMarketPos').size()>0) createChartMarketPos(marketPosData);
        if($('#ChartMediaPos').size()>0) createChartMediaPos(mediaPosData);
    });


    if($("#btnTotalBefore").is(":checked")) {
        $("#TotalData h1.before").fadeIn("slow");
    }

    $("#btnTotalBefore").click(function(){
        if($(this).is(":checked")) {
            $("#TotalData h1.before").fadeIn("slow");
        } else {
            $("#TotalData h1.before").fadeOut("slow");
        }
    });

    if($("#btnMediaBefore").is(":checked")) {
        createChartMedia(periodBefore,periodCurrent,true);
    }

    $("#btnMediaBefore").click(function(){
        if($(this).is(":checked")) {
            createChartMedia(periodBefore,periodCurrent,true);
        } else {
            createChartMedia(periodBefore,periodCurrent,false);
        }
    });

    $("#formMediaCom a").click(function(){
        var media_com = $(this).attr("media-com");
        $("#formMediaCom input[name=media_com]").val(media_com);
        $("#formMediaCom").submit();
    });
});

function get_media_data()
{
    for (var i = 0; i < mediaData.length; i += 1) {

        periodCurrent.push({
            name: mediaCategories[i],
            y: mediaData[i].y,
            url : mediaData[i].url,
            color : mediaData[i].color
        });

        var drillDataLen = mediaData[i].drilldown.data.length;
        for (var j = 0; j < drillDataLen; j += 1) {
            periodBefore.push({
                name: mediaData[i].drilldown.categories[j],
                y: mediaData[i].drilldown.data[j],
                url : mediaData[i].url,
                color : mediaData[i].color
            });
        }
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
                if($("#btnMediaBefore").is(":checked")) {
                    createChartMedia(periodBefore,periodCurrent,true);
                } else {
                    createChartMedia(periodBefore,periodCurrent,false);
                }
                setTimeout(requestMediaData, 1000*60);  
            }
        });
    }
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
            data:{period:PeriodType},
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
                var Positive = res.Positive+"%";
                var Normal = res.Normal+"%";
                var Negative = res.Negative+"%";
                $(".progress-bar-success").css("width",Positive).text(Positive);
                $(".progress-bar-white").css("width",Normal).text(Normal);
                $(".progress-bar-danger").css("width",Negative).text(Negative);
                setTimeout(requestSentimentData, 1000*60);  
            }
        });
    }
}

function requestTotalData()
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
                $("#mentionCurrent").text(res.mentionCurrent);
                $("#mentionBefore").text(res.mentionBefore);
                $("#userCurrent").text(res.userCurrent);
                $("#userBefore").text(res.userBefore);
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

var ChartKeyword;

function createChartKeyword(series,col) {

    if(series.length>0) {

        var tickInterval = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 60*1000*60 : null;
        var selected = (PeriodType=="Today" || (PeriodType=="Custom" && CustomTime)) ? 0 : 4;

        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

        ChartKeyword = Highcharts.stockChart('ChartKeyword', {
            chart: {
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
                tickInterval : tickInterval,
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
                tickInterval : 1,
                minRange : 1
            },
            plotOptions: {
                series: {
                    minPointLength: 0,
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                if(this.y >= 1) {
                                    window.location.href = urlpath+"realtime/?keyword="+this.series.name+"&time="+this.x;
                                }
                            }
                        }
                    },
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

function createChartMedia(periodBefore,periodCurrent,isBefore) {

    if(isBefore) {
        var series = [{
            name: 'Media period before',
            data: periodBefore,
            size: '70%',
            innerSize: '50%',
            dataLabels: {
                distance : -50
            }
        }, {
            name: 'Media period current',
            data: periodCurrent,
            size: '80%',
            innerSize: '70%',
            dataLabels: {
                distance : 5
            }
        }];
    } else {
        var series = [{
            name: 'Media period current',
            data: periodCurrent,
            size: '80%',
            innerSize: '70%',
            dataLabels: {
                distance : 5
            }
        }];
    }

    $('#ChartMedia').highcharts({
        credits: {
            enabled: false
        },
        chart: {
            type: 'pie',
            spacingRight: 0,
            spacingLeft: 0,
            spacingTop: 0,
            spacingBottom: 0,
            marginRight: 10,
            marginLeft: 10,
            marginTop: 0,
            marginBottom: 0
        },
        title: {
            text: '<div class="x_title"><h2 class="title">Media Monitoring</h2></div>',
            useHTML: true
        },
        plotOptions: {
            pie: {
                shadow: false,
                center: ['50%', '50%']
            },
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            if(this.series.name=="Media period current") {
                                var url = this.options.url+"&period_type=current";
                            } else {
                                var url = this.options.url+"&period_type=before";
                            }
                            if(this.percentage>0) location.href = url;
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
                            return img+'<br /> <span style="font-size:10px;font-family: tahoma;color:#000">'+" "+Highcharts.numberFormat(this.point.percentage,1)+"%</span>";
                        } else {
                            return null;
                        }
                    }
                }
            }
        },
        tooltip: {
            padding: 1,
            useHTML: true,
            backgroundColor: "rgba(255,255,255,1)",
            formatter: function () {
                if(this.point.percentage>0) {
                    var img = '<i class="ico ico-'+this.point.name.toLowerCase()+'"></i>';
                    return this.series.name+"<br />"+img+' '+this.point.name+ ": "+Highcharts.numberFormat(this.point.percentage,1)+"%";
                } else {
                    return null;
                }
            }
        },
        series: series
    });
}

function createChartMarketPos(series) {

  var chart = new Highcharts.Chart({
        chart : {
            marginTop: "70",
            marginLeft : "25",
            zoomType: 'xy',
            renderTo: 'ChartMarketPos',
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
                value: 0
            }],
            max: 1.1,
            min: -1.1,
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
                           location.href = urlpath+"marketing";
                        }
                    }
                }
            }
        },
        tooltip: {
            enabled: false
        }
    });
}

function createChartMediaPos(series) {

  var chart = new Highcharts.Chart({
        chart : {
            marginTop: "70",
            marginLeft : "25",
            zoomType: 'xy',
            renderTo: 'ChartMediaPos',
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
                value: 0
            }],
            max: 1.1,
            min: -1.1,
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
            }
        },
        tooltip: {
            enabled: false
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

        if (typeof lastSeg !== 'undefined' && this.tooltipOptions.enabled==false) {

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

              if (lastPoint.plotX > nextLastPoint.plotX) {

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