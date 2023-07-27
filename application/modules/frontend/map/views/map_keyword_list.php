<ul role="tablist" class="nav nav-pills" id="keyword-overall">
        <li onclick="change_keywordfeed(this.id)" role="presentation" style="display:inline-block; float:none;">
            <a aria-expanded="true" data-toggle="tab" role="tab">
                Over all<?php echo "<br/>".$total_mention;?>
            </a>
        </li>
<?php foreach($rowsdata as $k_row=>$v_row) { ?>
<?php if($v_row['keyword_id']!="") { ?>
		<li onclick="change_keywordfeed(this.id)" id="<?php echo $v_row['keyword_id'];?>" role="presentation" style="display:inline-block; float:none;" >
            <a aria-expanded="true" data-toggle="tab" role="tab">
                <?php echo $v_row['keyword_name']."<br/>".$v_row['mention'];?>
            </a>
        </li>
<?php }?>
<?php }?>
</ul>
