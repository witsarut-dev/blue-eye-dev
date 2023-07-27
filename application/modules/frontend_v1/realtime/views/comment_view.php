<?php foreach($comments as $k_row=>$v_row) { ?>
<li>
  <div class="message_date">
    <h3 class="date text-info"><?php echo date("d",strtotime($v_row['post_time']));?></h3>
    <p class="month"><?php echo date("M",strtotime($v_row['post_time']));?> <?php echo date("y",strtotime($v_row['post_time']));?></p>
  </div>
  <div class="message_wrapper">
    <h4 class="heading" style="cursor: default;"><?php echo $v_row['post_name'];?></h4>
  	<blockquote class="message">
      <?php $display_keyword = $this->master_model->get_display_keyword($v_row);?>
      <?php echo tag_keyword($v_row['post_detail'],$display_keyword);?>
      <br />
      <br />
      <?php if(count($display_keyword)>0) { ?>
      <span class="text-success">keyword : <?php echo implode(", ",array_keys($display_keyword));?></span>
      <?php } ?>
    </blockquote>
  </div>
</li>
<?php } ?>