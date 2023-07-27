$(function() {

    set_gap_time();

    if($("#tc-view").size()>0) getTagcloud();
    
    $(document).delegate('.btnSearchNormal,.btnSearchNegative,.btnSearchPositive', 'click', function() {
        if($(this).hasClass("search-selected")) {
            $(".panel_toolbox button").removeClass("search-selected");
            Sentiment = "";
        } else {
            $(".panel_toolbox button").removeClass("search-selected");
            $(this).addClass("search-selected");
            Sentiment = $(this).attr("sentiment");
        }
        if(word_cloud!="" || post_user!="") {
            result_feed = true;
            result_rows = 1;
            get_result_feed();
        }
    });

    /*report*/
    if($("#ResultBox .scroll-pane").size()>0) {
    	$("#ResultBox .scroll-pane").height(820).jScrollPane({autoReinitialise: true})
		.bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
			if(isAtBottom) get_result_feed();
		});
    }

});

var result_feed = true;
var result_rows = 1;
var word_cloud = "";
var post_user  = "";

function get_result_feed()
{
	if(result_feed) {
		result_feed = false;
		get_feed();
	}
}

function get_result_list(keyword)
{
    result_feed = true;
    result_rows = 1;
    word_cloud = keyword;
    post_user_id = "";
    get_feed();
}

function get_result_user(keyword)
{
    result_feed = true;
    result_rows = 1;
    word_cloud = "";
    post_user_id = keyword;
    get_feed();
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
            $("#ResultBox .scroll-pane .choose-remark").remove();
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
