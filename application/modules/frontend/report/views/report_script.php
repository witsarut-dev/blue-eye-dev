<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/tagcanvas.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/modules/report.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/highstock/highstock.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/highstock/modules/data.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/highstock/modules/exporting.js"></script>
<script type="text/javascript" src="<?php echo theme_assets_url(); ?>js/modules/pdf_report.js?v1"></script>
<script type="text/javascript">
    var wordData = {};
    var Sentiment = '<?php echo ($this->input->get("Sentiment")!="") ? $this->input->get("Sentiment") : "";?>';
    var PeriodType = "<?php echo $period; ?>";
</script>
<script>
    const keywordData = <?php echo json_encode($keywordData); ?>;
    const mention = <?php echo json_encode($mention); ?>;
    const sm = <?php echo json_encode($sm); ?>;
    const nonsm = <?php echo json_encode($nonsm); ?>;
    const test = <?php echo json_encode($test); ?>;
    const sentimentData = <?php echo json_encode($sentimentData); ?>;
    const getGroupKeyword = <?php echo json_encode($getGroupKeyword); ?>;
    var PeriodType = "<?php echo $period; ?>";
    let objMention = [{name: "Mention" ,data:[]}];
    let objSM = [{name: "SM" ,data:[]}];
    let objNonSM = [{name: "NONSM" ,data:[]}];
    let objTest = [];
    let ChartKeyword4 = [];
    let objMention2 = [{name: "Mention" ,data:[]}];
    function edit_sentiment_realtime(event) {
        var value_new_sentiment = document.getElementById(event.target.id).value;
        var id_post = event.target.id ;
        var r = confirm("Confirm to Edit Sentiment? ");
        if (r == true) {
            $.ajax({
                url:urlbase+"master/realtime_update_edit_sentiment", 
                type: "post", 
                dataType: 'json',
                data: {new_sentiment_edit: value_new_sentiment , post_id: id_post },
                success:function(result){
                    alert(data);
                }
            });  
            location.reload();
        }
    }

    Object.keys(sentimentData).forEach(function(key) {
        for (let index = 0; index < sentimentData[key].data.length; index++) {
            var datetime = new Date(sentimentData[key].data[index][0]);
            datetime.setHours(datetime.getHours()+7); 
            sentimentData[key].data[index][0] = Date.parse(datetime);
        }
    });
    Object.keys(test).forEach(function(key) {
        let arr = [];
        var NewIssue = {};
        NewIssue.name = key;        
        for (let index = 0; index < test[key].length; index++) {
            var datetime = new Date(test[key][index]["match_time"]);
                datetime.setHours(datetime.getHours()+7); 
                arr.push([
                    Date.parse(datetime),
                    parseInt(test[key][index]["count"])
                ]);
        }
        NewIssue.data = arr;
        objTest.push(NewIssue);
    });
    for (let index = 0; index < mention.length; index++) {
        var datetime = new Date(mention[index]["match_time"]);
        datetime.setHours(datetime.getHours()+7); 
        objMention[0].data.push({
        x: Date.parse(datetime),
        y: parseInt(mention[index]["count"])
      });
    }
    for (let index = 0; index < sm.length; index++) {
        var datetime = new Date(sm[index]["match_time"]);
        datetime.setHours(datetime.getHours()+7); 
        objSM[0].data.push({
        x: Date.parse(datetime),
        y: parseInt(sm[index]["count"])
      });
    }
    for (let index = 0; index < nonsm.length; index++) {
        var datetime = new Date(nonsm[index]["match_time"]);
        datetime.setHours(datetime.getHours()+7); 
        objNonSM[0].data.push({
        x: Date.parse(datetime),
        y: parseInt(nonsm[index]["count"])
      });
    }
    for (let index = 0; index < mention.length; index++) {
        var datetime = new Date(mention[index]["match_time"]);
        datetime.setHours(datetime.getHours()+7); 
        objMention2[0].data.push({
        x: Date.parse(datetime),
        y: parseInt(mention[index]["count"])
      });
    }
    $('.checkbox').click(function() {
        var ss = $('.checkbox:checked').map(function() { return this.value; }).get();
        // console.log(ss.length);
        if(ss.length >= 9){
            $('#all').prop('checked',true);
        }else{
            $('#all').prop('checked',false);
        }     
    })
    $("#volumeOfMentions").on('change', function () {
        if ($(this).is(':checked')) {
            var svgString = ChartKeyword.getSVG();
            $('#volumeOfMentionsB64').val(createB64(svgString))
        } else {
            $('#volumeOfMentionsB64').val('')
        }
    });
    $("#socialMediaReachGraph").on('change', function () {
        if ($(this).is(':checked')) {
            var svgString = ChartKeyword2.getSVG();
            $('#socialMediaReachGraphB64').val(createB64(svgString))
        } else {
            $('#socialMediaReachGraphB64').val('')
        }
    });
    $("#nonSocialMediaReachGraph").on('change', function () {
        if ($(this).is(':checked')) {
            var svgString = ChartKeyword3.getSVG();
            $('#nonSocialMediaReachGraphB64').val(createB64(svgString))
        } else {
            $('#nonSocialMediaReachGraphB64').val('')
        }
    });
    $("#mentionsPerCategory").on('change', function () {
        var myString = '';
        if ($(this).is(':checked')) {
            Object.keys(ChartKeyword4).forEach(function(key) {
                var svgString = ChartKeyword4[key].getSVG();
                let parser = new DOMParser();
                let svgElem = parser.parseFromString(svgString, "image/svg+xml").documentElement;
                let b64 = svgElem.toDataURL();
                myString += `${b64}@`;
            });
            $('#mentionsPerCategoryB64').val(myString)
        } else {
            $('#mentionsPerCategoryB64').val('')
        }
    });
    $("#graphSentimentMonitoringAnalysis").on('change', function () {
        if ($(this).is(':checked')) {
            var svgString = ChartKeyword5.getSVG();
            $('#graphSentimentMonitoringAnalysisB64').val(createB64(svgString))
        } else {
            $('#graphSentimentMonitoringAnalysisB64').val('')
        }
    });
    $("#all").on('change', function () {
        if ($(this).is(':checked')) {
            $('input[type=checkbox]').prop('checked',true);
            var svgString = ChartKeyword.getSVG();
            $('#volumeOfMentionsB64').val(createB64(svgString))
            var svgString2 = ChartKeyword2.getSVG();
            $('#socialMediaReachGraphB64').val(createB64(svgString2))
            var svgString3 = ChartKeyword3.getSVG();
            $('#nonSocialMediaReachGraphB64').val(createB64(svgString3))
            var myString = '';
            Object.keys(ChartKeyword4).forEach(function(key) {
                var svgString4 = ChartKeyword4[key].getSVG();
                let parser = new DOMParser();
                let svgElem = parser.parseFromString(svgString4, "image/svg+xml").documentElement;
                let b64 = svgElem.toDataURL();
                myString += `${b64}@`;
            });
            $('#mentionsPerCategoryB64').val(myString)
            var svgString5 = ChartKeyword5.getSVG();
            $('#graphSentimentMonitoringAnalysisB64').val(createB64(svgString5))
        } else {
            $('input[type=checkbox]').prop('checked',false);
            $('#volumeOfMentionsB64').val('')
            $('#socialMediaReachGraphB64').val('')
            $('#nonSocialMediaReachGraphB64').val('')
            $('#mentionsPerCategoryB64').val('')
            $('#graphSentimentMonitoringAnalysisB64').val('')
        }
    });

    function createB64(svgString) {
        let parser = new DOMParser();
        let svgElem = parser.parseFromString(svgString, "image/svg+xml").documentElement;
        let b64 = svgElem.toDataURL();
        return b64;
    }

    var ChartKeyword = Highcharts.stockChart('container1', {
        chart: {
            style: {
                fontFamily: '"Poppins", sans-serif',
            },
            marginRight: 0,
            marginLeft: 0,
        },
        rangeSelector: {
            enabled: false
        },
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
            ordinal: false,
            labels: {
                style: {
                    fontSize: '16px'
                }
            },
        },
        yAxis: {
            lineWidth: 0,
            labels: {
                style: {
                    fontSize: '16px'
                }
            },
        },
        legend: {
            enabled: false,
        },
        tooltip: {
            enabled: false,
        },
      series: objMention
    });
    var ChartKeyword2 = Highcharts.stockChart('container2', {
        chart: {
            style: {
                fontFamily: '"Poppins", sans-serif',
            },
            marginRight: 0,
            marginLeft: 0,
        },
        rangeSelector: {
            enabled: false
        },
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
            ordinal: false,
            labels: {
                style: {
                    fontSize: '16px'
                }
            }
        },
        yAxis: {
            lineWidth: 0,
            labels: {
                style: {
                    fontSize: '16px'
                }
            },
        },
        legend: {
            enabled: false,
        },
        tooltip: {
            enabled: false,
        },
      series: objSM
    });
    var ChartKeyword3 = Highcharts.stockChart('container3', {
        chart: {
            style: {
                fontFamily: '"Poppins", sans-serif',
            },
            marginRight: 0,
            marginLeft: 0,
        },
        rangeSelector: {
            enabled: false
        },
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
            ordinal: false,
            labels: {
                style: {
                    fontSize: '16px'
                }
            },
        },
        yAxis: {
            lineWidth: 0,
            labels: {
                style: {
                    fontSize: '16px'
                }
            },
        },
        legend: {
            enabled: false,
        },
        tooltip: {
            enabled: false,
        },
      series: objNonSM
    });

    

    var ChartKeyword5 = Highcharts.stockChart('container5', {
            chart: {
                type: 'column',
                style: {
                    fontFamily: '"Poppins", sans-serif',
                },
                marginRight: 0,
                marginLeft: 0,
            },
            colors: ['#62c462', '#cfcfcf', '#e62117']
            ,
            rangeSelector: {
                enabled: false
            },
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
                },
                labels: {
                    style: {
                        fontSize: '16px'
                    }
                },
            },
            yAxis: {
                lineWidth: 1,
                labels: {
                    style: {
                        fontSize: '16px'
                    }
                },
            },
            legend: {
                enabled: false,
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
            series: sentimentData
        });



    var start = new Date();
    start.setUTCHours(-1, 0, 0, 0);
    var end = new Date();
    end.setUTCHours(23, 59, 59, 999);
    var container
    for (var i = 0; i <= (objTest.length-1); i++) {
        var interval = 1 * 60 * 60 * 1000,
        j = 1,
        a = Date.parse(start) + interval;
        for (var x = 0; x <= (objTest[i].data.length-1); x++) {
            for (a; a < objTest[i].data[x][0]; a += interval) {
                objTest[i].data.push([a, 0])
            }
            a += interval;
            
        }
        container = document.createElement('div');
        container.className = 'container';
        document.getElementById('container4').appendChild(container);
        ChartKeyword4.push(Highcharts.chart(container, {
                title: {
                    text: ''
                },
                chart: {
                    type: 'area',
                }, // type of the chart
                credits: {
                    enabled: false
                },
                xAxis: {
                    type: 'datetime',
                    ordinal: false,
                    tickInterval: interval,
                    labels: {
                        enabled: false
                    },
                    title: {
                        text: null
                    },
                    startOnTick: false,
                    endOnTick: false,
                    tickPositions: []
                },
                yAxis: {
                    min: 0,
                    endOnTick: false,
                    startOnTick: false,
                    labels: {
                        enabled: false
                    },
                    title: {
                        text: null
                    },
                    tickPositions: [0]
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    enabled: false,
                },
                plotOptions: {
                    series: {
                        connectNulls: true
                    }
                },
                plotOptions: {
                    series: {
                        connectNulls: true,
                        animation: false,
                        lineWidth: 1,
                        shadow: false,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        marker: {
                            radius: 1,
                            states: {
                                hover: {
                                    radius: 2
                                }
                            }
                        },
                        fillOpacity: 0.25
                    },
                    column: {
                        negativeColor: '#910000',
                        borderColor: 'silver'
                    }
                },
                series: [{
                    name:objTest[i].name,
                    data:objTest[i].data.sort(function(a, b) {
                        return a[0] - b[0]
                    })
                }],
            })
        );
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



    SVGElement.prototype.toDataURL = function (type, options) {
        var _svg = this;

        function debug(s) {
            // console.log("SVG.toDataURL:", s);
        }

        function exportSVG() {
            var svg_xml = XMLSerialize(_svg);
            var svg_dataurl = base64dataURLencode(svg_xml);
            debug(type + " length: " + svg_dataurl.length);

            // NOTE double data carrier
            if (options.callback) options.callback(svg_dataurl);
            return svg_dataurl;
        }

        function XMLSerialize(svg) {

            // quick-n-serialize an SVG dom, needed for IE9 where there's no XMLSerializer nor SVG.xml
            // s: SVG dom, which is the <svg> elemennt
            function XMLSerializerForIE(s) {
                var out = "";

                out += "<" + s.nodeName;
                for (var n = 0; n < s.attributes.length; n++) {
                    out += " " + s.attributes[n].name + "=" + "'" + s.attributes[n].value + "'";
                }

                if (s.hasChildNodes()) {
                    out += ">\n";

                    for (var n = 0; n < s.childNodes.length; n++) {
                        out += XMLSerializerForIE(s.childNodes[n]);
                    }

                    out += "</" + s.nodeName + ">" + "\n";

                } else out += " />\n";

                return out;
            }


            if (window.XMLSerializer) {
                debug("using standard XMLSerializer.serializeToString")
                return (new XMLSerializer()).serializeToString(svg);
            } else {
                debug("using custom XMLSerializerForIE")
                return XMLSerializerForIE(svg);
            }

        }

        function base64dataURLencode(s) {
            var b64 = "data:image/svg+xml;base64,";

            // https://developer.mozilla.org/en/DOM/window.btoa
            if (window.btoa) {
                debug("using window.btoa for base64 encoding");
                // b64 += btoa(s);
                b64 += btoa(unescape(encodeURIComponent(s)))
            } else {
                debug("using custom base64 encoder");
                b64 += Base64.encode(s);
            }

            return b64;
        }

        function exportImage(type) {
            var canvas = document.createElement("canvas");
            var ctx = canvas.getContext('2d');

            // TODO: if (options.keepOutsideViewport), do some translation magic?

            var svg_img = new Image();
            var svg_xml = XMLSerialize(_svg);
            svg_img.src = base64dataURLencode(svg_xml);

            svg_img.onload = function () {
                debug("exported image size: " + [svg_img.width, svg_img.height])
                canvas.width = svg_img.width;
                canvas.height = svg_img.height;
                ctx.drawImage(svg_img, 0, 0);

                // SECURITY_ERR WILL HAPPEN NOW
                var png_dataurl = canvas.toDataURL(type);
                debug(type + " length: " + png_dataurl.length);

                if (options.callback) options.callback(png_dataurl);
                else debug("WARNING: no callback set, so nothing happens.");
            }

            svg_img.onerror = function () {
                // console.log(
                //     "Can't export! Maybe your browser doesn't support " +
                //     "SVG in img element or SVG input for Canvas drawImage?\n" +
                //     "http://en.wikipedia.org/wiki/SVG#Native_support"
                // );
            }

            // NOTE: will not return anything
        }

        function exportImageCanvg(type) {
            var canvas = document.createElement("canvas");
            var ctx = canvas.getContext('2d');
            var svg_xml = XMLSerialize(_svg);

            // NOTE: canvg gets the SVG element dimensions incorrectly if not specified as attributes
            //debug("detected svg dimensions " + [_svg.clientWidth, _svg.clientHeight])
            //debug("canvas dimensions " + [canvas.width, canvas.height])

            var keepBB = options.keepOutsideViewport;
            if (keepBB) var bb = _svg.getBBox();

            // NOTE: this canvg call is synchronous and blocks
            canvg(canvas, svg_xml, {
                ignoreMouse: true, ignoreAnimation: true,
                offsetX: keepBB ? -bb.x : undefined,
                offsetY: keepBB ? -bb.y : undefined,
                scaleWidth: keepBB ? bb.width + bb.x : undefined,
                scaleHeight: keepBB ? bb.height + bb.y : undefined,
                renderCallback: function () {
                    debug("exported image dimensions " + [canvas.width, canvas.height]);
                    var png_dataurl = canvas.toDataURL(type);
                    debug(type + " length: " + png_dataurl.length);

                    if (options.callback) options.callback(png_dataurl);
                }
            });

            // NOTE: return in addition to callback
            return canvas.toDataURL(type);
        }

        // BEGIN MAIN

        if (!type) type = "image/svg+xml";
        if (!options) options = {};

        if (options.keepNonSafe) debug("NOTE: keepNonSafe is NOT supported and will be ignored!");
        if (options.keepOutsideViewport) debug("NOTE: keepOutsideViewport is only supported with canvg exporter.");

        switch (type) {
            case "image/svg+xml":
                return exportSVG();
                break;

            case "image/png":
            case "image/jpeg":

                if (!options.renderer) {
                    if (window.canvg) options.renderer = "canvg";
                    else options.renderer = "native";
                }

                switch (options.renderer) {
                    case "canvg":
                        debug("using canvg renderer for png export");
                        return exportImageCanvg(type);
                        break;

                    case "native":
                        debug("using native renderer for png export. THIS MIGHT FAIL.");
                        return exportImage(type);
                        break;

                    default:
                        debug("unknown png renderer given, doing noting (" + options.renderer + ")");
                }

                break;

            default:
                debug("Sorry! Exporting as '" + type + "' is not supported!")
        }
    }

</script>