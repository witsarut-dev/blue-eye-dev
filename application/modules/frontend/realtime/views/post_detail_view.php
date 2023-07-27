<?php $v_row = $post_detail; ?>
<div class="fancybox-body">
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="panel panel-default card-view x_panel user-detail" post-id="<?php echo $v_row['post_id'] ?>" post-block="<?php echo $v_row['post_user_id'] ?>">
        <div class="panel-heading">
          <div class="title_left">
            <h3>Post Detail</h3>
          </div>
          <div class="x_link">
            <a class="go-link" href="<?php echo get_post_link($v_row['post_link'], $v_row); ?>" target="_blank"><i class="fa fa-arrow-circle-right"></i></a>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
          <div class="panel-body">
            <div class="">
              <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
                <h3><?php echo display_post_name($v_row['post_name'], $v_row['sourceid'], 0); ?></h3>
                <br />
                <br />
                <div class="row">
                  <div class="col-md-8 col-sm-8 col-xs-8">
                    <ul class="list-unstyled user_data">
                      <?php echo get_post_detail_type($v_row['post_type']); ?>
                      <li><i class="fa fa-clock-o"></i> <?php echo getDatetimeformat($v_row['post_time']); ?></li>
                      <?php echo get_sentiment_detail($v_row['sentiment'], 'm-top-xs', $v_row['match_id']); ?>
                    </ul>
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-4">
                    <?php if ($v_row['post_like'] != NULL || $v_row['post_like'] != 0) { ?>
                      <i class="fa fa-thumbs-up"></i> &nbsp;<?php echo $v_row['post_like']; ?>&nbsp;&nbsp;
                    <?php } ?>
                    <?php if ($v_row['post_comment'] != NULL || $v_row['post_comment'] != 0) { ?>
                      <i class="fa fa-comments"></i> &nbsp;<?php echo $v_row['post_comment']; ?>&nbsp;&nbsp;
                    <?php } ?>
                    <?php if ($v_row['post_share'] != NULL || $v_row['post_share'] != 0) { ?>
                      <i class="fa fa-share"></i> &nbsp;<?php echo $v_row['post_share']; ?>&nbsp;&nbsp;
                    <?php } ?>
                    <?php if ($v_row['post_view'] != NULL || $v_row['post_view'] != 0) { ?>
                      <i class="fa fa-eye"></i> &nbsp;<?php echo $v_row['post_view']; ?>
                    <?php } ?>
                  </div>
                </div>
                <br /><br />
                <div class="user-detail-tool">
                  <?php if ($v_row['sourceid'] != "4") { ?>
                    <a href="javascript:;" class="margin-left btnBlockPostDetail"><i class="fa fa-ban text-danger"></i></a>
                  <?php } ?>
                  <a href="javascript:;" class="margin-right btnDeletePostDetail"><i class="fa fa-trash-o text-danger"></i></a>
                </div>
              </div>
              <div id="CommmentBox" class="col-md-9 col-sm-9 col-xs-12">
                <ul class="messages scroll-pane">
                  <li>
                    <div class="message_date">
                      <h3 class="date text-danger"><?php echo date("d", strtotime($v_row['post_time'])); ?></h3>
                      <p class="month"><?php echo date("M", strtotime($v_row['post_time'])); ?> <?php echo date("y", strtotime($v_row['post_time'])); ?></p>
                    </div>
                    <div class="message_wrapper">
                      <h4 class="heading" style="cursor: default;"><?php echo display_post_name($v_row['post_name'], $v_row['sourceid'], 1); ?></h4>
                      <blockquote class="message">
                        <?php $display_keyword = $this->master_model->get_display_keyword($v_row); ?>
                        <?php echo tag_keyword($v_row['post_detail'], $display_keyword); ?>
                        <br />
                        <br />
                        <span class="text-success">keyword : <?php echo implode(", ", array_keys($display_keyword)); ?></span>
                      </blockquote>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
<style>
  ul.messages li:nth-child(1) .message_wrapper {
    margin-left: 0px;
  }
</style>
<script type="text/javascript">
  $(function() {
    $("#CommmentBox .scroll-pane").hide();
    if ($("#CommmentBox .scroll-pane").size() > 0) {
      $("#CommmentBox .scroll-pane").height(310).jScrollPane({
          autoReinitialise: true
        })
        .bind('jsp-scroll-y', function(event, scrollPositionY, isAtTop, isAtBottom) {
          if (isAtBottom) get_commment_feed();
        });
    }
    setTimeout(function() {
      get_commment_feed();
    }, 500);
  });

  var commment_feed = true;
  var commment_rows = 1;

  function get_commment_feed() {
    if (commment_feed) {
      commment_feed = false;
      get_comment();
    }
  }

  function get_comment() {
    var post_rows = 1;
    var url = urlbase + "realtime/get_comment";
    var match_type = "<?php echo $v_row['match_type']; ?>";

    post_rows = commment_rows;

    $.ajax({
      type: 'post',
      dataType: 'html',
      data: {
        post_id: "<?php echo $v_row['post_id']; ?>",
        com_id: "<?php echo $v_row['com_id']; ?>",
        match_type: match_type,
        post_rows: post_rows
      },
      url: url,
      beforeSend: function() {
        $.fancybox.showLoading();
      },
      error: function() {
        $.fancybox.hideLoading();
        dialog_error("Error");
      },
      success: function(html) {
        $("#CommmentBox .scroll-pane").show();
        if (html != "") {
          if (match_type == "Feed") {
            $("#CommmentBox .scroll-pane .jspPane").append(html);
          } else {
            $("#CommmentBox .scroll-pane .jspPane").prepend(html);
          }
          commment_feed = true;
          commment_rows++;
        } else {
          commment_feed = false;
        }
        $.fancybox.hideLoading();
      }
    });
  }
</script>