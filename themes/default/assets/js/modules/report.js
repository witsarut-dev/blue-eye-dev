var result_feed = true;
var result_rows = 1;
var word_cloud = "";
var post_user_id  = "";

$(function() {
    
    get_word_data();
    // get_top_share();
    get_top_user();
    set_gap_time();
    
    $(document).delegate('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive', 'click', function() {
        if($(this).hasClass("search-selected")) {
            $(".panel_toolbox button").removeClass("search-selected");
            Sentiment = "";
        } else {
            $(".panel_toolbox button").removeClass("search-selected");
            $(this).addClass("search-selected");
            Sentiment = $(this).attr("sentiment");
        }
        console.log(word_cloud);
        if(word_cloud!="" || post_user_id!="") {
            result_feed = true;
            result_rows = 1;
            get_result_feed();
        }
    });

    /*report*/
    if($("#ResultBox .scroll-pane").size()>0) {
    	$("#ResultBox .scroll-pane").height(320).jScrollPane({autoReinitialise: true})
		.bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
			if(isAtBottom) get_result_feed();
		});
    }

    $(".box-keyword .delete-keyword").click(function() {
        word_cloud = "";
        post_user_id  = "";
        $(".box-keyword .label-keyword").text('');
        $(".box-keyword").hide();
        $("#ResultBox .choose-remark").show();
        $("#ResultBox .scroll-pane .jspPane").html('');
        $(".countResult").text('');
        // get_top_share();
        get_top_user();
    });

    $(document).delegate('.btnDeleteReport', 'click', function() {
        if(window.confirm("คุณยืนยันที่จะลบข้อมูลหรือไม่")) {
            var obj = this;
            var post_id = $(obj).parents("td").attr("post-id");
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
                        get_word_data();
                        // get_top_share();
                        get_top_user();
                    }
                    $.fancybox.hideLoading();
                }
            });
        }
    });

});

function get_result_feed()
{
	if(result_feed) {
		result_feed = false;
		get_feed();
	}
}

function get_result_list(keyword,count)
{
    result_feed = true;
    result_rows = 1;
    word_cloud = keyword;
    post_user_id = "";
    $("#ResultBox .scroll-pane .jspPane").html("");
    get_feed();
    // get_top_share();
    get_top_user();

    $(".countResult").text(number_format(count,0));
    $(".counterAnim").counterUp({ delay: 10,time: 1000});

    $(".box-keyword .label-keyword").text(keyword);
    $(".box-keyword").hide().fadeIn();
}

function get_result_user(keyword,count)
{
    result_feed = true;
    result_rows = 1;
    post_user_id = keyword;
    //word_cloud = "";
    $("#ResultBox .scroll-pane .jspPane").html("");
    get_feed();

    $(".countResult").text(number_format(count,0));
    $(".counterAnim").counterUp({ delay: 10,time: 1000});


    $("html,body").animate({ scrollTop: $("#ResultBox").offset().top - 150 }, 500);
}

function get_feed()
{
	var post_rows = 1;
	var url  = urlbase+"report/get_feed";

	post_rows = result_rows;

    $.ajax({
        type : 'post',
        dataType : 'html',
        data: {post_type:"ResultBox",post_rows:post_rows,keyword:word_cloud,post_user_id:post_user_id,sentiment:Sentiment},
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            //dialog_error("Error");
            result_feed = true;
        },
        success : function(html) {
            $("#ResultBox .choose-remark").hide();
            if(result_rows==1) {
                $("#ResultBox .scroll-pane .jspPane").html(html);
            } else {
                $("#ResultBox .scroll-pane .jspPane").append(html);
            }
        	if(html!="") {
            	result_feed = true; 
                result_rows++;
        		load_fancybox();
            } else {
                result_feed = false;
            }
            $.fancybox.hideLoading();
        }
    });
}

function generate_report()
{
	var url  = urlbase+"report/generate_report";

    $.ajax({
        type : 'post',
        dataType : 'html',
        data: {period:PeriodType},
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            //dialog_error("Error");
        },
        success : function(html) {
            $.fancybox.hideLoading();
        }
    });
}

function get_word_data()
{
	var url  = urlbase+"report/get_word_data";

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {post_type:"wordData"},
        url: url,
        beforeSend: function() {
            $('#tc-view,#tc-data').hide();
            $("#box-tc .icon-container").show();
        },
        error: function() {
        },
        success : function(res) {
            wordData = res;
            if($("#tc-view").size()>0) getTagcloud();
            if(word_cloud!="") {
                for(var i in res) {
                    var obj = res[i];
                    if(obj.key==word_cloud) {
                        get_result_list(obj.key,obj.share_count);
                    }
                }
            }
            $('#tc-view,#tc-data').show();
            $("#box-tc .icon-container").hide();
        }
    });
}

function get_top_share()
{
	var url  = urlbase+"report/get_top_share";

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {keyword:word_cloud,post_user_id:post_user_id},
        url: url,
        beforeSend: function() {
            $("#TopShare-list tbody").html("");
            // $("#ShareBox .icon-container").show();
        },
        error: function() {
        },
        success : function(res) {
            var html = "";
            for(var i in res) {
                var obj = res[i];
                html += '<tr>';
                html += '<td>'+obj.icon+'</td>';
                html += '<td post-id="'+obj.post_id+'"><a href="'+obj.post_link+'" class="underline" target="_blank">'+obj.post_detail+'</a><br />';
                html += '<a href="javascript:;" class="margin-right btnDeleteReport"><i class="fa fa-trash-o text-danger"></i></a> ';
                html += '<gap class="post-time" time="'+obj.post_time+'">'+obj.text_time+'</gap></td>';
                html += '<td style="text-align:center;font-size: 12px;"><span class="counter-anim">'+number_format(obj.count_share,0)+'</span></td>';
                html += '</tr>';
            }
            $("#TopShare-list tbody").html(html);
            // $("#ShareBox .icon-container").hide();
        }
    });
}

function get_top_user()
{
	var url  = urlbase+"report/get_top_user";

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {keyword:word_cloud, post_user_id:post_user_id},
        url: url,
        beforeSend: function() {
            $("#TopUser-list tbody").html("");
            // $("#TopUser .icon-container").show();
        },
        error: function() {
        },
        success : function(res) {
            var html = "";
            for(var i in res) {
                var obj = res[i];
                var k_row = parseInt(i)+1;
                html += '<tr>';
                html += '<td rowspan="1" style="padding-top: -5px; width: 20px; text-align: center;">' + k_row + '</td>';
                html += '<td style="width: 50px; text-align: center;">' + obj.icon + '</td>';
                html += '<td style="padding-top: -5px; text-align: left;">' + obj.post_name + '</td>';
                html += '<td rowspan="1" style="padding-top: -5px; width: 50px; text-align: center;">' + number_format(obj.count_post, 0) + '</td>';
                html += '</tr>';
            }
            $("#TopUser-list tbody").html(html);
            // $("#TopUser .icon-container").hide();
        }
    });
}