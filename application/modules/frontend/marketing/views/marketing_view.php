<div class="container-fluid">
    <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default card-view" style="padding: 10px 5px 10px 0px; margin-top: 10px; margin-bottom: 15px;">
				<div class="panel-wrapper collapse in">
					<div class="panel-body" style="padding: 0px;">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
							<div class="form-group pull-left top_search">
								<div class="x_title">
									<div class="btn-group panel_toolbox" data-toggle="buttons">
										<button type="button" class="btn btnSearchPositive search-style" sentiment="Positive" style="color: #25D366; width: 100px; border: #1C6CB9; border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Positive</label></button>
										<button type="button" class="btn btnSearchNormal search-style" sentiment="Normal" style="color: #CACACA; width: 100px; border: #1C6CB9; border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Neutral</label></button>
										<button type="button" class="btn btnSearchNegative search-style" sentiment="Negative" style="color: #FF3F3F; width: 100px; border: #1C6CB9; border-style: solid; border-width: 1px; border-radius: 0px;"><label style="font-size: 11px;">Negative</label></button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="padding-right: 5px;">
							<div id="toolPeriod" class="pull-right" style="margin-top: 5px;">
								<?php echo $this->load->view("include/period_view");?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="row">
        <div class="">
            <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                <div id="Sentiment" class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark">Sentiment</h6></div>
                        <?php if($setting_allow) { ?>
                        <div class="pull-right">
                            <a href="<?php echo site_url($module."/add_category");?>" class="btn btn-success btn-round fancybox"><i class="fa fa-plus"></i> Add Category</a>
                        </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="x_content" style="min-height: 330px; border: 0px;">
                                <?php 
                                $count_cate = count($category);
                                if($count_cate>4) { 
                                    $tb_w = 150+($count_cate*80)."px";
                                    $td_w = "80px";
                                    $th_w = "150px";
                                ?>
                                <div class="table-responsive">
                                <? 
                                } else {
                                    $tb_w = "100%";
                                    $td_w = "18%";
                                    $th_w = "25%";
                                }
                                ?>
                                <table style="width:<?php echo $tb_w;?>" class="table display jambo_table mb-0 border border-solid">
                                    <thead>
                                        <tr>
                                            <th style="width:<?php echo $th_w;?>;text-align:left !important;" class="no-border">Company</th>
                                            <?php foreach($category as $k_row=>$v_row) { ?>
                                            <th style="vertical-align: middle;<?php echo $td_w;?>"><?php echo $v_row['category_name'];?></th>
                                            <?Php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($company as $k_com=>$v_com) { ?>
                                        <tr>
                                            <td style="text-align:left !important;" class="no-border"><?php echo $v_com["company_keyword_name"];?></td>
                                            <?php
                                            foreach($category as $k_row=>$v_row) {
                                                $company_keyword_id = $v_com["company_keyword_id"];
                                                $category_id = $v_row["category_id"];
                                                $value = floatval(@$sentiment[$company_keyword_id][$category_id]);
                                                if($value>0) {
                                                    echo '<td class="text-success" style="vertical-align: middle;"><span class="counter-anim">'.abs($value).'</span></td>';
                                                } else if($value<0) {
                                                    echo '<td class="text-danger" style="vertical-align: middle;"><span class="counter-anim">-'.abs($value).'</span></td>';
                                                } else {
                                                    echo '<td><span class="counter-anim">'.abs($value).'</span></td>';
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <?php if(count($category)>4) { ?></div><? } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12"> 
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="panel panel-success card-view">
                            <div class="panel-heading">
                                <div class="pull-left"><h6 class="panel-title txt-dark">Positive</h6></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="ChartPositive" style="height:350px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="panel panel-danger card-view">
                            <div class="panel-heading">
                                <div class="pull-left"><h6 class="panel-title txt-dark">Negative</h6></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="ChartNegative" style="height:350px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="">
            <?php foreach($company as $k_com=>$v_com) { ?>
            <div class="col-md-6 col-sm-6 col-xs-12 CategoryBox">
                <div class="panel panel-default card-view x_panel">
                    <div class="panel-heading">
                        <div class="pull-left"><h6 class="panel-title txt-dark"><?php echo $v_com['company_keyword_name'];?></h6></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-wrapper collapse in">
                        <div class="panel-body">
                            <div class="scroll-pane" com-id="<?php echo $v_com['company_keyword_id'];?>">
                                <?php
                                foreach($category as $k_row=>$v_row) {
                                    $arrow = ($k_row==0) ? '<a><i class="fa fa-chevron-up"></i></a>' : '<a><i class="fa fa-chevron-down"></i></a>';
                                ?>
                                <div class="CategoryList" cate-id="<?php echo $v_row['category_id'];?>">
                                    <div class="x_title">
                                        <h2 class="pull-left"><?php echo $v_row['category_name'];?></h2>
                                        <ul class="nav pull-right panel_toolbox">
                                            <li><?php echo $arrow;?></li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>