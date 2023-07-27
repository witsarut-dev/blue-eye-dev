<?php foreach($rowsdata as $k_row=>$v_row) { ?>
<?php if($v_row['post_detail']!="") { ?>
<div class="x_content item-show" media-type="<?php echo $v_row['post_type'];?>" post-id="<?php echo $v_row['post_id']?>" post-block="<?php echo $v_row['post_user_id']?>">
	<div class="x_link">
		<a class="go-link" href="<?php echo get_post_link($v_row['post_link'],$v_row);?>" target="_blank"><i class="fa fa-arrow-circle-right"></i></a>
	</div>
	<div class="clearfix"></div>
	<div class="flex">
		<ul class="list-inline widget_profile_box">
			<?php echo get_post_type($v_row['post_type']);?>
			<li class="display-name">
				<h3 class="name"><a href="<?php echo site_url("realtime/post_detail/".$v_row['post_id']."/".$v_row['com_id']); ?>" class="fancybox"><?php echo display_post_name($v_row['post_name'],$v_row['sourceid'],1);?></a></h3>
			</li>
		</ul>
	</div>
	<p class="post_detail">
		<?php 
			echo mb_substr($v_row['post_detail'],0,200);
			if(mb_strlen($v_row['post_detail'])>200) echo "...";
		?>
	</p>
	<div class="flex">
		<ul class="list-inline count2">
			<li class="display-full">
				<i class="fa fa-clock-o"> <?php echo getDatetimeformat($v_row['post_time']);?></i>
				<?php if($v_row['sourceid']!="4") { ?>
				<a href="javascript:;" class="margin-left btnBlockPost"><i class="fa fa-ban text-danger"></i></a>
				<?php } ?>
				<a href="javascript:;" class="margin-right btnDeletePost"><i class="fa fa-trash-o text-danger"></i></a>
				Gap <gap class="post-time" time="<?php echo $v_row['post_time'];?>"><?php echo get_post_time($v_row['post_time']);?></gap>
			</li>
			<?php echo get_sentiment($v_row['sentiment'],'display-full');?>
		</ul>
	</div>
</div>
<?php } ?>
<?php } ?>