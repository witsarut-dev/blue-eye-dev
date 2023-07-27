<script type="text/javascript">
var link_id = '<?php echo $link_id;?>';
var k_nodes = '<?php echo $k_nodes;?>';
var user_relate = true;
var relate_rows = 1;

$(function(){

    get_user_relate();

    if($(".users-list .scroll-pane").size()>0) {
        $(".users-list .scroll-pane").height(280).jScrollPane({autoReinitialise: true})
        .bind('jsp-scroll-y',function(event, scrollPositionY, isAtTop, isAtBottom){
            if(isAtBottom && user_relate) {
                get_user_relate();
            }
        });
    }
});


function get_user_relate()
{
    if(user_relate) {
        user_relate = false;
        get_relate_list();
    }
}

function get_relate_list()
{
    var post_rows = 1;
    var url  = urlbase+"link_analysis/get_relate_list";

    post_rows = relate_rows;

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: {
            link_id : link_id,
            k_nodes : k_nodes,
            post_rows : post_rows
        },
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            user_relate = true;
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
                user_relate = true; relate_rows++;
                load_fancybox();
            } else {
                user_relate = false;
            }
            $.fancybox.hideLoading();
        }
    });
}
</script>