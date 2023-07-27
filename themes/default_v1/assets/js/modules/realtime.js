var social_feed = true;
var web_feed = true;
var news_feed = true;
var social_rows = 1;
var web_rows = 1;
var news_rows = 1;
var is_success = 1;

$(function() {

    set_gap_time();

    $("#filterRealtime .btn-search").click(function(){
        hide_button();
        social_feed = true;
        web_feed = true;
        news_feed = true;
        social_rows = 1;
        web_rows = 1;
        news_rows = 1;
        is_success = 1;
        get_social_feed();
        get_web_feed();
        get_news_feed();
        $.fancybox.close();
    });

	$("#toolMediaType button").click(function(){
		MediaType = $(this).attr("media-type");
        if(MediaType=="All") {
            $("#toolMediaType button").removeClass("btn-primary").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("btn-success");
        } else {
        	$("#toolMediaType button").removeClass("btn-primary").addClass("btn-default");
            $("#toolMediaType button").removeClass("btn-success").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("btn-primary");
        }
        hide_button();
        social_feed = true;
        web_feed = true;
        news_feed = true;
        social_rows = 1;
        web_rows = 1;
        news_rows = 1;
        is_success = 1;
        get_social_feed();
        get_web_feed();
        get_news_feed();
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
        hide_button();
        social_feed = true;
        web_feed = true;
        news_feed = true;
        social_rows = 1;
        web_rows = 1;
        news_rows = 1;
        is_success = 1;
        get_social_feed();
        get_web_feed();
        get_news_feed();
    });

    $("#toolMediaType button[media-type='"+MediaType+"']").trigger("click");
    $(".top_search a[sentiment='"+Sentiment+"']").trigger("click");
    hide_button();

    get_social_feed();
    get_web_feed();
    get_news_feed();

    /*realtime*/
    if($("#MediaBox .scroll-pane").size()>0) {
    	$("#MediaBox .scroll-pane").height(400).jScrollPane({autoReinitialise: true})
		.bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
			if(isAtBottom && social_feed) {
                hide_button();
                is_success = 3;
                get_social_feed();
            }
		});
    }

    if($("#WebBox .scroll-pane").size()>0) {
    	$("#WebBox .scroll-pane").height(400).jScrollPane({autoReinitialise: true})
    	.bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
			if(isAtBottom && web_feed) {
                hide_button();
                is_success = 3;
                get_web_feed();
            }
		});
    }

    if($("#NewsBox .scroll-pane").size()>0) {
    	$("#NewsBox .scroll-pane").height(400).jScrollPane({autoReinitialise: true})
    	.bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
			if(isAtBottom && news_feed) {
                hide_button();
                is_success = 3;
                get_news_feed();
            }
		});
    }
  
});

function get_social_feed()
{
	if(social_feed) {
		social_feed = false;
		get_feed("MediaBox");
	}
}

function get_web_feed()
{
	if(web_feed) {
		web_feed = false;
		get_feed("WebBox");
	}
}

function get_news_feed()
{
	if(news_feed) {
		news_feed = false;
		get_feed("NewsBox");
	}
}

function get_feed(post_type)
{
	var post_rows = 1;
	var url  = urlbase+"realtime/get_feed";
    var keyword_in = [];
    $("input[name^=keyword_id]:checked").each(function(){
        keyword_in.push($(this).val());
    });

	if(post_type=="MediaBox") post_rows = social_rows;
    if(post_type=="WebBox")   post_rows = web_rows;
    if(post_type=="NewsBox")  post_rows = news_rows;

    $.ajax({
        type : 'post',
        dataType : 'html',
        data: {post_type:post_type,
            post_rows:post_rows,
            media_type:MediaType,
            sentiment:Sentiment,
            keyword:GetKeyword,
            time:GetTime,
            period_type:PeriodType,
            'keyword_in[]':keyword_in},
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            //$.fancybox.hideLoading();
            //dialog_error("Error");
            if(post_type=="MediaBox") social_feed = true;
            if(post_type=="WebBox") web_feed = true;
            if(post_type=="NewsBox") news_feed = true;
        },
        success : function(html) {
            if(post_rows==1)  {
                $("#"+post_type+" .scroll-pane .jspPane").html(html);
            } else {
                $("#"+post_type+" .scroll-pane .jspPane").append(html);
            }
        	if(html!="") {
            	if(post_type=="MediaBox") {social_feed = true; social_rows++;}
        		if(post_type=="WebBox")   {web_feed = true; web_rows++;}
        		if(post_type=="NewsBox")  {news_feed = true; news_rows++;}
        		load_fancybox();
            } else {
            	if(post_type=="MediaBox") social_feed = false;
        		if(post_type=="WebBox") web_feed = false;
        		if(post_type=="NewsBox") news_feed = false;
            }
            if(is_success==3) {
                $.fancybox.hideLoading();
                show_button();
            } else {
                is_success++;
            }
        }
    });
}

function add_feed(post_type)
{
    var last_time = 1;
    var url  = urlbase+"realtime/add_feed";
    var keyword_in = [];
    $("input[name^=keyword_id]:checked").each(function(){
        keyword_in.push($(this).val());
    });

    last_time = $("#"+post_type+" .item-show:first gap.post-time").attr("time");

    $.ajax({
        type : 'post',
        dataType : 'html',
        data: {post_type:post_type,
            last_time:last_time,
            media_type:MediaType,
            sentiment:Sentiment,
            period_type:PeriodType,
            'keyword_in[]':keyword_in
        },
        url: url,
        beforeSend: function() {
        },
        error: function() {
        },
        success : function(html) {
            $("#"+post_type+" .scroll-pane .jspPane").prepend(html);
            load_fancybox();
        }
    });
}

function hide_button()
{
    $("#toolMediaType button").attr("disabled","disabled");
    $('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive').attr("disabled","disabled");
}

function show_button()
{
    $("#toolMediaType button").removeAttr("disabled");
    $('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive').removeAttr("disabled");
}