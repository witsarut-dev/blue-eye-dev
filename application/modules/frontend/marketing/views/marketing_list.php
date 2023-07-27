<?php foreach($rowsdata as $k_row=>$v_row) { ?>
	<?php if($v_row['post_detail']!="") { ?>
		<div class="x_content item-show" media-type="<?php echo $v_row['post_type'];?>" post-id="<?php echo $v_row['post_id']?>" post-block="<?php echo $v_row['post_user_id']?>" style="border: 0px;">
			<div class="x_link">
				<a class="go-link" href="<?php echo get_post_link($v_row['post_link'], $v_row);?>" target="_blank"><i class="fa fa-external-link"></i></a>
			</div>
			<div class="clearfix"></div>
			<div class="flex">
				<ul class="list-inline widget_profile_box">
					<?php echo get_post_type($v_row['post_type']);?>
					<li class="display-name">
						<h3 class="name"><a href="<?php echo site_url("realtime/post_detail/".$v_row['post_id']."/".$v_row['com_id']); ?>" class="fancybox"><?php echo display_post_name($v_row['post_name'], $v_row['sourceid'], 1);?></a></h3>
					</li>
				</ul>
			</div>
			<div class="post_detail col-lg-9 col-md-9 col-sm-9 col-xs-9">
				<p class="post_detail">
					<?php
					$v_row['post_detail'] = strip_tags($v_row['post_detail']);
					echo mb_substr($v_row['post_detail'], 0, 200);
					if (mb_strlen($v_row['post_detail']) > 200) echo "...";
					?>
				</p>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
				<p style="float: right; padding: 10px 5px 0 5px; font-size: 12px;">
					<?php echo get_sentiment($v_row['sentiment'], 'display-full', $v_row['match_id']); ?>
				</p>
			</div>
			<div class="flex col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="list-inline count2">
					<li>
						<i class="fa fa-clock-o"></i> <?php echo getDatetimeformat($v_row['post_time']); ?>&nbsp;&nbsp;
						- Gap <gap class="post-time" time="<?php echo $v_row['post_time']; ?>"><?php echo get_post_time($v_row['post_time']); ?></gap>
					</li>
					<li>
						<?php if ($v_row['post_like'] != NULL || $v_row['post_like'] != 0) { ?>
							<i class="fa fa-thumbs-up"></i> &nbsp;<?php echo $v_row['post_like']; ?>&nbsp;&nbsp;
						<?php } ?>
						<?php if ($v_row['post_comment'] != NULL || $v_row['post_comment'] != 0) { ?>
							<i class="fa fa-comments"></i> &nbsp;<?php echo $v_row['post_comment']; ?>&nbsp;&nbsp;
						<?php } ?>
						<?php if ($v_row['post_share'] != NULL || $v_row['post_share'] != 0) { ?>
							<i class="fa fa-share"></i> &nbsp;<?php  echo $v_row['post_share']; ?>
						<?php }?>
						<?php if ($v_row['post_view'] != NULL || $v_row['post_view'] != 0) { ?>
							<i class="fa fa-eye"></i> &nbsp;<?php  echo $v_row['post_view']; ?>
						<?php } ?>
					</li>
					<li style="float: right;">
						<?php if ($v_row['sourceid'] != "4") { ?>
							<a href="javascript:;" class="margin-left btnBlockPost"><i class="fa fa-ban text-danger"></i></a>&nbsp;&nbsp;
						<?php } ?>
						<a href="javascript:;" class="margin-right btnDeletePost"><i class="fa fa-trash-o text-danger"></i></a>
					</li>
				</ul>
			</div>
		</div>
	<?php } ?>
<?php } ?>