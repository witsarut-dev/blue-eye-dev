<div role="tabpanel" class="tab-pane fade" id="top-share" aria-labelledby="top-user-tab">
    <div id="TopShare">
        <div class="panel panel-primary card-view x_panel" style="padding-top:0px;">
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <table id="TopShare-list" class="table table-bordered jambo_table table-striped datatable-fixed-header dataTable">
                        <thead>
                            <tr class="th-text-bold">
                                <th style="vertcal-align: middle; padding-top: -5px; width: 20px;">No</th>
                                <th style="vertcal-align: middle; padding-top: -5px; width: 50px;">Source</th>
                                <th style="vertcal-align: middle; padding-top: -5px;">Topic</th>
                                <th style="vertcal-align: middle; padding-top: -5px;">Reach</th>
                            </tr>
                        </thead>
                        <tbody id="tableGraph_data" class="rows">
                            <?php for ($i=0; $i < count($top_share); $i++) { ?>
                                <tr>
                                    <td rowspan="1" style="padding-top: -5px; width: 20px; text-align: center;"><?php echo $i+1;?></td>
                                    <?php if ($top_share[$i]['sourceid'] == 1) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/facebook-color.png" width="20" height="20"></td>
                                    <?php } elseif ($top_share[$i]['sourceid'] == 2) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/twitter-color.png" width="20" height="20"></td>
                                    <?php } elseif ($top_share[$i]['sourceid'] == 3) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/youtube-color.png" width="20" height="20"></td>
                                    <?php } elseif ($top_share[$i]['sourceid'] == 4) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/news-color.png" width="20" height="20"></td>
                                    <?php } elseif ($top_share[$i]['sourceid'] == 5) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/webboard-color.png" width="20" height="20"></td>
                                    <?php } elseif ($top_share[$i]['sourceid'] == 6) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/instagram-color.png" width="20" height="20"></td>
                                    <?php } elseif ($top_share[$i]['sourceid'] == 7) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/tik-tok-color.png" width="20" height="20"></td>
                                    <?php } elseif ($top_share[$i]['sourceid'] == 9) { ?>
                                        <td style="width: 50px; text-align: center;"><img src="themes/default/assets/images/interface/line-color.png" width="20" height="20"></td>
                                    <?php }?>
                                    <td style="padding-top: -5px; text-align: left;"><a class="go-link" href="<?php echo $top_share[$i]['post_link'] ?>" target="_blank"><?php echo $top_share[$i]['post_detail']?></a></td>
                                    <td rowspan="1" style="padding-top: -5px; width: 50px; text-align: center;"><?php echo $top_share[$i]['count_share']?></td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>