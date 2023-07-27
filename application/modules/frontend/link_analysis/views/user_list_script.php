<script type="text/javascript">
var msg_id = '<?php echo $msg_id;?>';
var user_like = true;
var user_share = true;
var user_comment = true;
var like_rows = 1;
var share_rows = 1;
var comment_rows = 1;
var is_success = 1;

$(function(){

    get_user_like();
    get_user_share();
    get_user_comment();

    if($("#tab-like .scroll-pane").size()>0) {
        $("#tab-like .scroll-pane").height(280).jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if(isAtBottom && user_like) {
                is_success = 3;
                get_user_like();
            }
        });
    }
    if($("#tab-share .scroll-pane").size()>0) {
        $("#tab-share .scroll-pane").height(280).jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if(isAtBottom && user_share) {
                is_success = 3;
                get_user_share();
            }
        });
    }
    if($("#tab-comment .scroll-pane").size()>0) {
        $("#tab-comment .scroll-pane").height(280).jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if(isAtBottom && user_comment) {
                is_success = 3;
                get_user_comment();
            }
        });
    }

    $('.users-list a[data-toggle="tab"]').click(function (e) {
        $(".users-list .nw-user-list").css("visibility","hidden");
    });

    $('.users-list a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        setTimeout(function(){
            $(".users-list .nw-user-list").css("visibility","visible");
        },500);
    });

});

function get_user_like()
{
    if(user_like) {
        user_like = false;
        get_user_list("like");
    }
}

function get_user_share()
{
    if(user_share) {
        user_share = false;
        get_user_list("share");
    }
}

function get_user_comment()
{
    if(user_comment) {
        user_comment = false;
        get_user_list("comment");
    }
}

function get_user_list(user_type)
{
    var post_rows = 1;
    var url  = urlbase+"link_analysis/get_user_list";

    if(user_type=="like") post_rows = like_rows;
    if(user_type=="share")  post_rows = share_rows;
    if(user_type=="comment") post_rows = comment_rows;

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {
            msg_id : msg_id,
            user_type: user_type,
            post_rows: post_rows
        },
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            if(user_type=="like") user_like = true;
            if(user_type=="share") user_share = true;
            if(user_type=="comment") user_comment = true;
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
                $("#tab-"+user_type+" .scroll-pane .jspPane .nw-user-list").html(html);
                setTimeout(function(){
                    $("#tab-"+user_type+" .nw-user-list").css("visibility","visible");
                },500);
            } else {
                $("#tab-"+user_type+" .scroll-pane .jspPane .nw-user-list").append(html);
            }
            if(res.length>0) {
                if(user_type=="like") {user_like = true; like_rows++;}
                if(user_type=="share")   {user_share = true; share_rows++;}
                if(user_type=="comment")  {user_comment = true; comment_rows++;}
                load_fancybox();
            } else {
                if(user_type=="like") user_like = false;
                if(user_type=="share") user_share = false;
                if(user_type=="comment") user_comment = false;
            }
            if(is_success==3) {
                $.fancybox.hideLoading();
            } else {
                is_success++;
            }
        }
    });
}
</script>