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
    </div>
    <div class="clearfix"></div>
    <br />
    <div class="">
        <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
            <div id="Sentiment" class="x_panel">
                <div class="x_title">
                    <h2>Sentiment</h2>
                    <?php if($setting_allow) { ?>
                    <div class="pull-right">
                        <a href="<?php echo site_url($module."/add_category");?>" class="btn btn-success btn-round fancybox"><i class="fa fa-plus"></i> Add Category</a>
                    </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="min-height: 300px;">
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
                    <table style="width:<?php echo $tb_w;?>" class="table table-bordered jambo_table table-striped">
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
                                        echo '<td class="text-success" style="vertical-align: middle;">'.abs($value).'</td>';
                                    } else if($value<0) {
                                        echo '<td class="text-danger" style="vertical-align: middle;">-'.abs($value).'</td>';
                                    } else {
                                        echo '<td>'.abs($value).'</td>';
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
        <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div id="ChartPositive" style="height:350px"></div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div id="ChartNegative" style="height:350px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="">
        <?php foreach($company as $k_com=>$v_com) { ?>
        <div class="col-md-4 col-sm-6 col-xs-12 CategoryBox">
            <div class="x_panel">
                <div class="x_title">
                    <h2 class="center"><?php echo $v_com['company_keyword_name'];?></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="scroll-pane" com-id="<?php echo $v_com['company_keyword_id'];?>">
                    <?php
                    foreach($category as $k_row=>$v_row) {
                        $arrow = ($k_row==0) ? '<a><i class="fa fa-chevron-up"></i></a>' : '<a><i class="fa fa-chevron-down"></i></a>';
                    ?>
                    <div class="CategoryList" cate-id="<?php echo $v_row['category_id'];?>">
                        <div class="x_title">
                            <h2><?php echo $v_row['category_name'];?></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><?php echo $arrow;?></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>