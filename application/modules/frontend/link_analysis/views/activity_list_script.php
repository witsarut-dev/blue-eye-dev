<script type="text/javascript">
var msg_id = '<?php echo $msg_id;?>';
var msg_type = '<?php echo $msg_type;?>';
var link_type = '<?php echo $link_type;?>';
var user_activity = true;
var activity_rows = 1;

$(function(){

    get_user_activity();

    if($(".users-list .scroll-pane").size()>0) {
        $(".users-list .scroll-pane").height(280).jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if(isAtBottom && user_activity) {
                get_user_activity();
            }
        });
    }
});


function get_user_activity()
{
    if(user_activity) {
        user_activity = false;
        get_activity_list();
    }
}

function get_activity_list()
{
    var post_rows = 1;
    var url  = urlbase+"link_analysis/get_activity_list";

    post_rows = activity_rows;

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {
            msg_id : msg_id,
            msg_type : msg_type,
            link_type : link_type,
            post_rows : post_rows
        },
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            user_activity = true;
        },
        success : function(res) {
            var html = "";
            for(var i in res) {
                var obj = res[i];
                html += '<div class="col-md-4 col-sm-4 col-xs-12 nw-user-box">';
                html += '<div class="nw-ico-user">';
                html += '<a href="'+obj.url+'" target="_blank"><img src="'+obj.pic+'" class="nw-img-user"/></a>';
                html += '</div>';
                html += '<div class="nw-name-user">';
                html += '<p>'+obj.name+'</p>';
                html +=  obj.icon;
                html += '</div>';
                html += '</div>';
            }

            if(post_rows==1)  {
                $(".users-list .scroll-pane .jspPane .nw-user-list").html(html);
                setTimeout(function(){
                    $(".users-list .nw-user-list").css("visibility","visible");
                },500);
            } else {
                $(".users-list .scroll-pane .jspPane .nw-user-list").append(html);
            }
            if(res.length>0) {
                user_activity = true; activity_rows++;
                load_fancybox();
            } else {
                user_activity = false;
            }
            $.fancybox.hideLoading();
        }
    });
}
</script>