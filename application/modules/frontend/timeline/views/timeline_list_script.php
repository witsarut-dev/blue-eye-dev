<script type="text/javascript">
var timeline_id = '<?php echo $timeline_id;?>';
var msg_date = '<?php echo $msg_date;?>';
var start_date = '<?php echo $start_date;?>';
var end_id = '<?php echo $end_id;?>';
var timeline_feed = true;
var timeline_rows = 1;

$(function(){
    
    get_feed();

    if($(".timeline-feed-list .scroll-pane").size()>0) {
        $(".timeline-feed-list .scroll-pane").height(280).jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if(isAtBottom && timeline_feed) {
                get_feed();
            }
        });
    }
});


function get_feed()
{
    if(timeline_feed) {
        timeline_feed = false;
        get_feed_list();
    }
}

function get_feed_list()
{
    var post_rows = 1;
    var url  = urlbase+"timeline/get_feed_list";

    post_rows = timeline_rows;

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {
            timeline_id : timeline_id,
            msg_date : msg_date,
            start_date : start_date,
            end_id : end_id,
            post_rows : post_rows
        },
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            timeline_feed = true;
        },
        success : function(res) {

            var html = "";
            for(var i in res) {
                var obj = res[i];
                var class_invert = (i%2!=0) ? 'timeline-inverted' : '';
                var circle_start = (obj.post_order=='start') ? '<div class="timeline-badge bg-green"><span style="position: relative;top: -2px;">Start</span></div>' : '';
                var circle_end   = (obj.post_order=='end') ? '<div class="timeline-badge bg-red"><span style="position: relative;top: -2px;">End</span></div>' : '';
                var text_sen = "";

                if(obj.sentiment > 0) {
                    text_sen = '<spn class="text-success">Sen. '+obj.sentiment+'%</span>';
                } else if(obj.sentiment < 0) {
                    text_sen = '<spn class="text-danger">Sen. '+obj.sentiment+'%</span>';
                } else {
                    text_sen = '<spn class="">Sen. 0%</span>';
                }

                html += '<li class="'+class_invert+'">'+circle_start+circle_end+'<div class="timeline-panel pa-30">';
                html += '<a href="'+obj.post_link+'" class="timeline-link" target="_blank"><i class="fa fa-arrow-circle-right"></i></a>';
                html += '<div class="timeline-body">';
                html += '<h4 class="mb-5">'+obj.post_icon+' <span class="timeline-name">'+obj.post_user+'</span></h4>';
                html += '<br /><p class="timeline-content">'+obj.post_detail.replace(/\?/g,'')+' ...</p><hr class="line"/>';
                html += '<div class="pull-left"><i class="fa fa-clock-o"></i> '+obj.msg_time+'</div>';
                html += '<div class="pull-right">'+text_sen+'</div>';
                html += '</div>';
                html += '</div></li>';
            }

            if(post_rows==1)  {
                $(".timeline-feed-list .scroll-pane .jspPane .timeline").html(html);
                setTimeout(function(){
                    $(".timeline-feed-list .timeline").css("visibility","visible");
                },1000);
            } else {
                $(".timeline-feed-list .scroll-pane .jspPane .timeline").append(html);
            }

            $(".timeline-feed-list .scroll-pane .jspPane .timeline").find(".no-float").remove();
            $(".timeline-feed-list .scroll-pane .jspPane .timeline li").removeClass("timeline-end");
            $(".timeline-feed-list .scroll-pane .jspPane .timeline li:last").addClass("timeline-end");
            $(".timeline-feed-list .scroll-pane .jspPane .timeline").append('<li class="clearfix no-float"></li>');

            if(res.length>0) {
                timeline_feed = true; timeline_rows++;
                load_fancybox();
            } else {
                timeline_feed = false;
            }
            $.fancybox.hideLoading();
        }
    });
}
</script>