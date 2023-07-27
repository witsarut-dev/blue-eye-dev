/*Link Analysis script*/
var network = null;
var nw_scale = 1;
var LinkItem = null;
var LinkTarget = null;
var network_data = {};
var myTimeout;

// create a network
$(function () {

    $('body').tooltip({ selector: '.tooltip2' });
    $('[data-toggle="tooltip2"]').tooltip({ container: 'body' });

    $("#BoxMyLink .scroll-pane").height(115).jScrollPane({ autoReinitialise: true });
    $("#box-textdata-user").hide();
    $("#box-textdata-comment").hide();
    $("#box-textdata-share").hide();

    if (ref_link_id != 0) {
        if ($(".link-list[link-id=" + ref_link_id + "]").size() > 0) {
            $('#formAddLink').parsley().reset();
            LinkItem = $(".link-list[link-id=" + ref_link_id + "]");
            $("#mynetwork").attr("link-id", ref_link_id);
            clearTimeout(myTimeout);
            ajax_network(ref_link_id, true);     
        }
    }

    $(document).delegate('.btnDeleteLink', 'click', function () {
        $('#formAddLink').parsley().reset();
        LinkTarget = this;
        dialog_delete("คุณยืนยันที่จะลบข้อมูลหรือไม่", "deleteLink()");
    });

    $(document).delegate('.link-list .LinkName,.btnEditLink', 'click', function () {
        clean_context();
        $('#formAddLink').parsley().reset();
        $("#box-textdata-user").slideDown("slow");
        LinkItem = $(this).parents(".link-list");
        var link_id = $(this).parents(".link-list").attr("link-id");
        var data = {id: link_id}; 
        var url_link = urlbase + "link_analysis/get_Post_User";
        $("#mynetwork").attr("link-id", link_id);       
        clearTimeout(myTimeout);
        ajax_network(link_id, true);   

        if ($(this).hasClass("LinkName")) {
            var url = urlbase + "link_analysis/view/" + link_id;
            history.pushState({}, "", url);
        }
        
        //=============================================================================================================================Champ open
        create_post_user(url_link,data)
    });

    $(document).delegate('.post-userid', 'click', function () {
        var id_post = $(this).attr("post-id");    
        var data = {id_comment: id_post};
        var url_comment = urlbase + "link_analysis/get_comment_user";
        var url_share = urlbase + "link_analysis/get_share_user";
        $Divs = $(".post-userid");
        $Divs.removeClass("highlight");
        $(this).addClass("highlight");
        $("#box-textdata-comment").slideDown("slow");
        $("#box-textdata-share").slideDown("slow");
        create_comment(url_comment,data);
        create_share(url_share,data);
        
    });

//==================================================================================================================== End กำลังเขียนอยู่ใจเย็นๆ


    $(".btnSaveLink").click(function () {
        var target = $(this).attr("target");
        $("#" + target).parsley().validate();
        if (!$("#" + target).parsley().validate()) {
            $("#" + target).find('.parsley-errors-list').show();
        } else {
            clean_context();
            var data = $("#" + target).serialize();
            if (ref_link_type == "user") {
                var url = urlbase + "link_analysis/cmdAddUser";
            } else if (ref_link_type == "fanpage") {
                var url = urlbase + "link_analysis/cmdAddFanPage";
            } else {
                var url = urlbase + "link_analysis/cmdAddLink";
            }
            var link_id = "";

            if (LinkItem != null) {
                link_id = $(LinkItem).attr("link-id");
            }

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: url,
                beforeSend: function () {
                    $.fancybox.showLoading();
                    clearTimeout(myTimeout);
                    start_loading();
                    $(".btnSaveLink").prop("disabled",true);
                },
                error: function () {
                    $.fancybox.hideLoading();
                    dialog_error("No internet connection");
                    $(".btnSaveLink").prop("disabled",false);
                },
                success: function (res) {
                    
                    if (res.status) {
                        var link_id = res.link_id;
                        var link_name = res.link_name;
                        if (res.action == "Add") {
                            var html = '<div class="link-list" link-id="' + res.link_id + '">';
                            html += '<div class="pull-left LinkName tooltip2" data-toggle="tooltip2" title="' + link_name + '">' + link_name + '</div>';
                            html += '<div class="pull-right">';
                            html += '<a href="javascript:;" class="btnEditLink"><i class="fa fa-cog"></i></a> ';
                            html += '<a href="javascript:;" class="btnDeleteLink"><i class="fa fa-times"></i></a>';
                            html += '</div>';
                            html += '<div class="clearfix"></div>';
                            html += '</div>';
                            $("#BoxMyLink .jspPane").append(html);
                            LinkItem = $(".link-list[link-id=" + res.link_id + "]");
                            $("#mynetwork").attr("link-id", res.link_id);
                        } else {
                            LinkItem = $(".link-list[link-id=" + link_id + "]");
                            $(LinkItem).find(".LinkName").text(link_name).attr({ "title": link_name, "data-original-title": link_name });
                            $(LinkItem).find(".LinkName").text(link_name);
                        }
                        $("input[id^=link_url]").filter(function() {return this.value;}).addClass("input-readonly").attr("readonly",true);

                        if (network != null) network.destroy();
                        var nodes = new vis.DataSet(res.nodes);
                        var edges = new vis.DataSet(res.edges);
                        network_data = { nodes: nodes, edges: edges }
                        load_network(network_data, true);
                        $("html,body").animate({ scrollTop: $("#mynetwork").offset().top - 150 }, 500);
                        $("#cmdExport").attr("href", urlbase + "link_analysis/cmdExport/" + ref_link_type +'/' + link_id);
                        $("#link_id").val(link_id);

                        if (res.reload) {
                            myTimeout = setTimeout('ajax_network(' + link_id + ',false)', 1000 * 60);
                        } else {
                            stop_loading();
                        }
                    } else {
                        dialog_error(res.message);
                        stop_loading();
                    }
                    $.fancybox.hideLoading();
                    $(".btnSaveLink").prop("disabled",false);
                }
            });
        }
    });

    $(".btnCancelLink").click(function () {
        clean_link();
        reset_loading();
    });

    $(".link-filter input[name^=relate_all],.link-filter input[name^=link_relate]").click(function () {
        if ($(this).val() == "all") {
            $('.link-filter input[name^=link_relate]').attr("checked", false);
        } else {
            $('.link-filter input[name^=relate_all]').attr("checked", false);
        }
        if (network != null) {
            network.destroy();
            var link_id = $("#mynetwork").attr("link-id");
            if (link_id != "" && link_id != null) ajax_network(link_id, true);
        }
    });

    $(".btn-close-context i").click(function(){
        $(".contextmenu").hide();
    });

    $(".btn-apply-context button").click(function(){
        $(".contextmenu").hide();
        if (network != null) {
            network.destroy();
            var link_id = $("#mynetwork").attr("link-id");
            if (link_id != "" && link_id != null) ajax_network(link_id, true);
        }
    });

    $(".contextmenu input[name^=activity_all],.contextmenu input[name^=user_activity]").click(function () {
        if ($(this).val() == "all") {
            $('.contextmenu input[name^=user_activity]').attr("checked", false);
        } else {
            $('.contextmenu input[name^=activity_all]').attr("checked", false);
        }
    });

    $(window).resize(function () {
        if (network != null) {
            network.destroy();
            load_network(network_data, false);
        }
        $(".contextmenu").hide();
    });

});

function deleteLink() {
    var url = urlbase + "link_analysis/cmdDelLink";
    var link_id = $(LinkTarget).parents(".link-list").attr("link-id");

    $.ajax({
        type: 'post',
        dataType: 'json',
        data: { link_id: link_id },
        url: url,
        beforeSend: function () {
            $.fancybox.showLoading();
        },
        error: function () {
            $.fancybox.hideLoading();
            dialog_error("No internet connection");
        },
        success: function (res) {
            if (res.status) {
                $(LinkTarget).parents(".link-list").fadeOut();
                if (LinkItem != null) {
                    LinkItem = null;
                }
                if ($("#mynetwork[link-id=" + link_id + "]").size() > 0) {
                    $("#mynetwork[link-id=" + link_id + "]").html("");
                    clean_link();
                    reset_loading();
                    var url = urlbase + "link_analysis?link_type=" + ref_link_type;
                    history.pushState({}, "", url);
                }
                clearTimeout(myTimeout);
                dialog_close();
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}

function clean_link() {
    $("#link_id").val("");
    $("input[name^=link_name]").val("");
    $("input[name^=link_url]").val("").removeClass("input-readonly").attr("readonly",false);
    $("#cmdExport").attr("href", "javascript:;");
    $('#formAddLink').parsley().reset();
}

function clean_context() {
    $('.contextmenu input[name^=user_activity]').attr("checked", false);
    $('.contextmenu input[name^=activity_all]').attr("checked", true);
}

function ajax_network(link_id, isLoad) {
    $("input[name=link_id]").val(link_id);

    var url = urlbase + "link_analysis/open_link";
    var data = $("#formAddLink").serialize();
    $.ajax({
        type: 'post',
        dataType: 'json',
        data: data,
        url: url,
        beforeSend: function () {
            clearTimeout(myTimeout);
            if (isLoad) $.fancybox.showLoading();
            start_loading();
        },
        error: function () {
            $.fancybox.hideLoading();
            dialog_error("No internet connection");
        },
        success: function (res) {
            if (res.status) {
                clean_link();
                $("input[name=link_name]").val(res.link_name);
                $("input[name=link_id]").val(res.link_id);
                for (var i in res.link_list) {
                    var obj = res.link_list[i];
                    $("input[id=link_url" + obj.link_no + "]").val(obj.link_url);
                }

                $("input[id^=link_url]").filter(function() {return this.value;}).addClass("input-readonly").attr("readonly",true);
                
                if (network != null) network.destroy();
                if (isLoad) $.fancybox.hideLoading();
                var nodes = new vis.DataSet(res.nodes);
                var edges = new vis.DataSet(res.edges);
                network_data = { nodes: nodes, edges: edges }
                load_network(network_data, true);
                if (isLoad) $("html,body").animate({ scrollTop: $("#mynetwork").offset().top - 150 }, 500);
                $("#cmdExport").attr("href", urlbase + "link_analysis/cmdExport/" + ref_link_type +'/' + res.link_id);

                if (res.reload) {
                    myTimeout = setTimeout('ajax_network(' + link_id + ',false)', 1000 * 60);
                } else {
                    stop_loading();
                }

            } else {
                dialog_error(res.message);
                if (isLoad) $.fancybox.hideLoading();
                stop_loading();
            }
        }
    });
}

function load_network(data, isLoad) {

    if(ref_link_type=='page') {
        nodes_color = {
            border: '#666666',
            background: '#666666',
            highlight: {
                background: '#1b6cb9'
            }
        };
    } else {
        nodes_color = {
            border: '#1b6cb9',
            background: '#1b6cb9',
            highlight: {
                background: '#318be0'
            }
        };
    }

    if (isLoad) $.fancybox.showLoading();
    var height = data.nodes.length * 5;
    var nw_width = parseInt($("#mynetwork").width());
    var nw_height = parseInt($(".page-wrapper").height()) + height;
    if ($(window).width() > 1024) {
        nw_height = nw_height;
    } else {
        nw_height = nw_width;
    }
    var container = document.getElementById('mynetwork');
    var options = {
        autoResize: false,
        width: nw_width + 'px',
        height: nw_height + 'px',
        layout: {
            randomSeed: 1,
            hierarchical: {
                enabled: false,
                levelSeparation: 150,
                nodeSpacing: 100,
                treeSpacing: 200,
                blockShifting: true,
                edgeMinimization: true,
                parentCentralization: true,
                direction: 'UD',
                sortMethod: 'hubsize'
            }
        },
        interaction: {
            zoomView: false
        },
        nodes:  {
            borderWidth: 2,
            size: 20,
            color: nodes_color,
            font: { color: '#000000', size: 12 },         //================================================2
            shapeProperties: {
                useBorderWithImage: true
            }
        },
        physics: {
            stabilization: true
        },
        groups: {
            G0: { color: { background: color_link[0], border: color_link[0] } },
            G1: { color: { background: color_link[1], border: color_link[1] } },
            G2: { color: { background: color_link[2], border: color_link[2] } }
        }
    };
    network = new vis.Network(container, data, options);

    // setTimeout(function() {
    //     network.fit({
    //         animation: {
    //             duration: 1000
    //         }
    //     });
    //     network.stopSimulation();
    // }, 1000);

    var networkCanvas = document.getElementById("mynetwork").getElementsByTagName("canvas")[0];
    networkCanvas.style.cursor = 'pointer';

    network.on('doubleClick', function (properties) {
        var ids = properties.nodes;
        var clickedNodes = network_data.nodes.get(ids);
        var obj = clickedNodes[0];
        if (obj != null && obj.url != "" && obj.type == "PopUp") {
            var url = obj.url;
            var newWidth = 1000;
            if ($(this).attr("newWidth") != null) {
                newWidth = $(this).attr("newWidth");
            }
            $.fancybox({
                autoScale: false,
                href: url,
                type: 'ajax',
                padding: 5,
                width: newWidth,
                height: 500,
                closeClick: false,
                autoSize: false,
                autoDimensions: false,
                onUpdate: function () {
                    $(".fancybox-inner .nw-user-list").css("width", $(".users-list .panel-body").width());
                }
            });
        } else if (obj != null && obj.url != "" && obj.type == "LinkUrl") {
            $("#formOpenLink").html("");
            var params = obj.url.split("?");
            if (params.length > 1) {
                var val = params[1].split("&");
                for (var i in val) {
                    var data = val[i].split("=");
                    var p1 = data[0];
                    var p2 = (data[1] != null) ? data[1] : "";
                    $("#formOpenLink").append('<input type="hidden" name="' + p1 + '" value="' + p2 + '"/>');
                }
            }
            $("#formOpenLink").attr("action", obj.url);
            $("#formOpenLink").submit();
        }
    });
    /*network.on("oncontext", function (params) {
        params.event.preventDefault();
        var pageY = params.event.pageY;
        var pageX = params.event.pageX - 200;
        if($(".contextmenu").size()>0) {
            $(".contextmenu").css({"top":pageY,"left":pageX});
            $(".contextmenu").show();
        }
    });*/
    network.on("beforeDrawing", function (ctx) {
        ctx.save();
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.fillStyle = '#e8e8e8';                                     //==============================================1
        ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.restore();
    });
    network.on("afterDrawing", function () {
        if (isLoad) $.fancybox.hideLoading();
    });
}

function nw_zoom_in() {
    if (network != null) {
        nw_scale--;
        if (nw_scale > 1) {
            network.moveTo({
                scale: nw_scale,
                animation: {
                    duration: 1000
                }
            });
            network.stopSimulation();
        } else {
            nw_zoom_reset();
        }
    }
}

function nw_zoom_out() {
    if (network != null) {
        network.moveTo({
            scale: nw_scale,
            animation: {
                duration: 1000
            }
        });
        if (nw_scale < 4) nw_scale++;
        network.stopSimulation();
    }
}

function nw_zoom_reset() {
    if (network != null) {
        nw_scale = 1;
        network.fit({
            animation: {
                duration: 1000
            }
        });
        network.stopSimulation();
    }
}

function show_filter() {
    if ($(".link-filter").is(":hidden")) {
        $(".link-filter").slideDown();
    } else {
        $(".link-filter").slideUp();
    }
}

function start_loading() {
    $("#icon-loading").show();
}

function reset_loading() {
    $("#icon-loading").hide();
}

function stop_loading() {
    $("#icon-loading").hide();
}

function create_post_user(url_link,data){

    $.ajax({  
        type: 'post',  
        url: url_link,
        data: data,
        beforeSend: function () {
            $('#PostUserData').html("");
            $('#CommentUserData').html("");
            $('#ShareUserData').html("");
            $('.alert_Text').html("");
        },
        error: function () {
            var alert_text = "Error Send Data";
            $('#PostUserData').append(alert_text);
        },
        success: function(response) {
            if (response)
            {
                var dic_data = JSON.parse(response);
                var index;
                var html_show;
                var text = '<h7 class="choose-remark text-danger">No Data!!!</h7>';
                var str_content_last;
 
                if(dic_data.length < 1){
                    $("#box-textdata-user").hide();
                    $("#box-textdata-comment").hide();
                    $("#box-textdata-share").hide();
                    $('.alert_Text').append(text);

                }
                else{
                    var text = '<h7 class="choose-remark text-danger" id="shares-usericon"><i class="fa fa-check-square-o"></i>Please select post user</h7>';
                    $("#box-textdata-comment").show();
                    $("#box-textdata-share").show();
                    $('#CommentUserData').append(text);
                    $('#ShareUserData').append(text);

                    var arr = new Array ();

                    for (index = 0; index < dic_data.length; index++) {

                        var keyword = dic_data[index]['keyword'];
                        var str_content = dic_data[index]['content_post'];
                        var keyword_re = keyword.trimEnd().split(" ");

                        for (var i = 0; i < keyword_re.length; i++){
                            var key = keyword_re[i];
                            arr.push(key);
                        }

                        // for (var j = 0; j < arr.length; j++){
                        //     var key = arr[j];
                        //     var regex = new RegExp(key,'g');
                        //     str_content_last =  str_content.replace(regex,'<a id="mark-key">'+key+'</a>');                            
                        // }

                        html_show = '<div post-id="'+ dic_data[index]['id_post'] +'" class ="post-userid" >';
                        html_show += '<div class="flex" >';
                        html_show += '<ul style=" display:inline;  text-align: right; ">';
                        html_show += '<li class="head-post"><i ><FONT SIZE="-1" align = "right" color = "black"> '+ dic_data[index]['time_post'] +'</FONT></i> '
                        html_show += '<i><FONT SIZE="-1" align = "right" color = "black"> , </FONT></i>'
                        html_show += '<i><FONT SIZE="-1" align = "right" color = "black"> '+ dic_data[index]['location_post'] +'</FONT></i>'
                        html_show += '<i><FONT SIZE="-1" align = "right" color = "black">  </FONT></i>'
                        html_show += '<a class = "link-open"; href="'+ dic_data[index]['link_post'] +'"; target="_blank";><i class="fa fa-arrow-circle-right" aria-hidden="true" style="font-size:18px;"></i></a>&nbsp&nbsp'
                        html_show += '</li>'
                        html_show += '</ul>';
                        html_show += '</div>';
                        html_show += '	<p class="post_detail" style="word-wrap:break-word;">'+ str_content +'</p>';
                        html_show += '<div class="flex" >';
                        html_show += '<ul   style=" display:inline;  text-align: right; ">';
                        html_show += '<li class="display-full" ><i ><FONT SIZE="-6" align = "right" > Like: '+ dic_data[index]['likes_post'] +'</FONT></i> '
                        html_show += '<i><FONT SIZE="-6" align = "right" >   </FONT></i>'
                        html_show += '<i><FONT SIZE="-6" align = "right" > Share: '+ dic_data[index]['shares_post'] +'  </FONT></i>'
                        html_show += '<i><FONT SIZE="-6" align = "right" >   </FONT></i>'
                        html_show += '<i><FONT SIZE="-6" align = "right" > Comment: '+ dic_data[index]['comment_post'] +'</FONT></i>&nbsp&nbsp</li>'
                        html_show += '</ul>';
                        html_show += '<br>';
                        html_show += '</div>';
                        html_show += '</div>';
    
                        $('#PostUserData').append(html_show);
                    }
                }
            }
        }
    });
}

function create_comment(url_comment,data){

    $.ajax({  
        type: 'post',  
        url: url_comment,
        data: data,
        beforeSend: function () {
            $('#comment-usericon').fadeOut("slow");
            $('#CommentUserData').html("");
        },
        error: function () {
            var alert_text = "Error Send Data";
            $('#CommentUserData').append(alert_text);
            
        },
        success: function(response) {
            if (response)
            {
                var dic_data = JSON.parse(response);
                var index;
                var html_show;
                for (index = 0; index < dic_data.length; index++) {
                    html_show = '<div comment-id="'+ dic_data[index]['id_comment'] +'" class ="comment-userid"></div>';
                    html_show += '<div class="flex">';
                    html_show += '<ul style=" display:inline;  text-align: right; ">';
                    html_show += '<li class="display-full" style="background-color:#e6e6e6;"><i ><FONT SIZE="-1" align = "right" color = "black"> '+ dic_data[index]['time_comment'] +'</FONT></i>'
                    html_show += '<i><FONT SIZE="-1" align = "right" color = "black">   </FONT></i>'
                    html_show += '<a class = "link-open"; href="'+ dic_data[index]['link_comment'] +'"; target="_blank";><i class="fa fa-arrow-circle-right" aria-hidden="true" style="font-size:18px;"></i></a>&nbsp&nbsp'
                    html_show += '</li>';
                    html_show += '</ul>';
                    html_show += '</div>';
                    html_show += '<h6>'+ dic_data[index]['user_comment'] +'</h6>';
                    html_show += '  <p class="post_detail" style="word-wrap:break-word;" >'+ dic_data[index]['content_comment'] +'</p>';
                    html_show += '<div class="flex" >';
                    html_show += '<ul   style=" display:inline;  text-align: right; ">';
                    html_show += '<li class="display-full" ><i ><FONT SIZE="-6" align = "right" > Like: '+ dic_data[index]['like_comment'] +'</FONT></i>';
                    html_show += '<i><FONT SIZE="-6" align = "right" >   </FONT></i>&nbsp&nbsp</li>'
                    html_show += '</ul>';
                    html_show += '<br>';
                    html_show += '</div>';
                    html_show += '</div>';
                    $('#CommentUserData').append(html_show);
                }

            }
        }
    });
}

function create_share(url_share,data){

    $.ajax({  
        type: 'post',  
        url: url_share,
        data: data,
        beforeSend: function () {
            $('#shares-usericon').fadeOut("slow");
            $('#ShareUserData').html(""); 
        },
        error: function () {
            var alert_text = "Error Send Data";
            $('#ShareUserData').append(alert_text);
        },
        success: function(response) {
            
            if (response)
            {
                var dic_data = JSON.parse(response);
                var index;
                var html_show;
                for (index = 0; index < dic_data.length; index++) {
                    html_show = '<div comment-id="'+ dic_data[index]['id_share'] +'" class ="comment-userid"></div>';
                    html_show += '<div class="flex">';
                    html_show += '<ul style=" display:inline;  text-align: right; ">';
                    html_show += '<li class="display-full" style="background-color:#e6e6e6;"><i ><FONT SIZE="-1" align = "right" color = "black"> '+ dic_data[index]['time_share'] +'</FONT></i>'
                    html_show += '<i><FONT SIZE="-1" align = "right" color = "black">   </FONT></i>'
                    html_show += '<a class = "link-open"; href="'+ dic_data[index]['link_share'] +'"; target="_blank";><i class="fa fa-arrow-circle-right" aria-hidden="true" style="font-size:18px;"></i></a>&nbsp&nbsp'
                    html_show += '</li>';
                    html_show += '</ul>';
                    html_show += '</div>';
                    html_show += '<h6>'+ dic_data[index]['user_share'] +'</h6>';
                    html_show += '  <p class="post_detail" style="word-wrap:break-word;">'+ dic_data[index]['content_share'] +'</p>';
                    html_show += '<div class="flex" >';
                    html_show += '<ul   style=" display:inline;  text-align: right; ">';
                    html_show += '<li class="display-full" ><i ><FONT SIZE="-6" align = "right" > Like: '+ dic_data[index]['like_share'] +'</FONT></i>';
                    html_show += '<i><FONT SIZE="-6" align = "right" >   </FONT></i>'
                    html_show += '<i><FONT SIZE="-6" align = "right" > Share: '+ dic_data[index]['share_share'] +'  </FONT></i>'
                    html_show += '<i><FONT SIZE="-6" align = "right" >   </FONT></i>'
                    html_show += '<i><FONT SIZE="-6" align = "right" > Comment: '+ dic_data[index]['comment_share'] +'</FONT></i>&nbsp&nbsp</li>'
                    html_show += '</ul>';
                    html_show += '<br>';
                    html_show += '</div>';
                    html_show += '</div>';

                    $('#ShareUserData').append(html_show);

                }

            }
        }
    });
}