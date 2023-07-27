<div class="right_col_fix" role="main">
	<div class="">
		<div class="page-title">
			<div class="title_left" id="toolPeriod">
				<?php echo $this->load->view("include/period_view");?>
			</div>
			 <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                    <div class="x_title">
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="none-link">Filter</a></li>
                         	<li><button type="button" class="btnSearchPositive" sentiment="Positive"><i class="fa fa-plus"></i></button></li>
							<li><button type="button" class="btnSearchNormal" sentiment="Normal"><i class="fa fa-circle-o"></i></button></li>
							<li><button type="button" class="btnSearchNegative" sentiment="Negative"><i class="fa fa-minus"></i></button></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
		</div>
		<div class="clearfix"></div>
		<br />
		<div class="">
			<div id="ShareBox" class="col-lg-4 col-md-6 col-sm-12 col-xs-12 ScrollBox">
				<div class="x_panel">
					<div class="x_title">
						<h2>Top Share</h2>
						<div class="clearfix"></div>
					</div>
					<div>
						<table  id="TableTopShare" style="width: 100%;" class="table table-striped jambo_table">
							<thead>
								<tr>
									<th style="vertical-align: middle;width:10%">เว็บ</th>
									<th style="vertical-align: middle;width:65%">หัวข้อ / เวลา</th>
									<th style="vertical-align: middle;width:20%;text-align:center">Share</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($topShare as $k_row=>$v_row) { ?>
								<tr>
									<td><?php echo get_icon_post_type($v_row['sourceid']); ?></td>
									<td post-id="<?php echo $v_row['post_id']; ?>"><a href="<?php echo $v_row['post_link']; ?>" class="underline" target="_blank"><?php echo $v_row['post_detail'];?></a><br />
									<gap class="post-time" time="<?php echo $v_row['post_time'];?>"><?php echo get_post_time($v_row['post_time']);?></gap></td>
									<td style="text-align:center;font-size: 12px;"><?php echo number_format($v_row['count_share']);?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div id="UserBox" class="col-lg-4 col-md-6 col-sm-12 col-xs-12 ScrollBox">
				<div class="x_panel containment" id="box-tc">
					<div class="x_title">
						<h2>Word Cloud</h2>
						<div class="clearfix"></div>
					</div>
					<section class="stage">
						<figure class="ball">
							<canvas id="tc-view"></canvas>
						<ul class="weighted" id="tc-data"></ul>
						<span class="shadow"></span>
						</figure>
					</section>
				</div>
				<div class="x_panel">
					<div class="x_title">
						<h2>Top User</h2>
						<div class="clearfix"></div>
					</div>
					<?php 
					foreach($topUser as $k_row=>$v_row) { 
						$post_user = addslashes($v_row['post_name']);
					?>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="flex">
							<strong class="name"><?php echo ($k_row+1);?>. <a href="javascript:;" onclick="get_result_user('<?php echo $v_row['post_user_id']; ?>')"><?php echo $v_row['post_name'];?></a></strong>
						</div>
						<div class="pull-left" style="width: 100%;">
							<ul class="list-inline">
								<li class="" style="width: 50%;">
									<?php echo get_icon_post_type($v_row['sourceid']); ?>
									<?php echo number_format($v_row['count_post']); ?>
								</li>
								<?php echo get_sentiment($v_row['sentiment'],'display-full');?>
							</ul>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div id="ResultBox" class="col-lg-4 col-md-6 col-sm-12 col-xs-12 CategoryBox">
			<div class="x_panel">
				<div class="x_title">
					<h2>Result</h2>
					<div class="clearfix"></div>
				</div>
				<div class="scroll-pane">
					<h5 class="choose-remark text-danger"><i class="fa fa-check-square-o"></i> Please select keyword in word cloud or user</h5>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
</div>