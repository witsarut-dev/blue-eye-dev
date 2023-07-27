<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left" id="toolPeriod">
        <?php echo $this->load->view("include/period_view");?>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
  <br />
  <div class="">
    <div class="col-lg-2 col-md-5 col-sm-5 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>List Graph</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="BoxMyGarph">
            <?php
            foreach($client_graph as $k_row=>$v_row) {
              $selected = ($v_row['graph_id']==$this->input->get("graph_id")) ? "active" : "";
            ?>
            <div class="graph-list <?php echo $selected;?>" graph-id="<?php echo $v_row['graph_id'];?>">
              <div class="pull-left GraphName"><?php echo $v_row['graph_name'];?></div>
              <div class="pull-right">
                <a href="javascript:;" class="btnEditGraph"><i class="fa fa-cog"></i></a>
                <a href="javascript:;" class="btnDeleteGraph"><i class="fa fa-times"></i></a>
              </div>
              <div class="clearfix"></div>
            </div>
            <?php } ?>
          </div>
          <br />
          <div id="BoxAddGraph">
            <form id="formAddGraph" class="form-horizontal form-label-left" novalidate="">
              <div class="form-group">
                <div class="input-group">
                  <button type="button" class="btn btn-primary btnSaveGraph" target="formAddGraph"><i class="fa fa-save"></i></button>
                  <input id="txtGraphName" type="text" class="form-control required" placeholder="Graph Name" maxlength="255">
                </div>
              </div>
            </form>
          </div>
          <button class="btn btn-primary btn-round" id="btnAddGraph">Add Graph</button>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-7 col-sm-7 col-xs-12">
      <div class="x_panel">
        <div class="x_content" id="GraphType">
          <div class="panel panel-success">
            <div class="panel-heading">
              <h1 class="panel-title">ตัวแปรเชิงปริมาณ Y</h1>
              <?php $graph_y = array("Sentiment"=>"Sentiment","Mention"=>"Mention"); ?>
            </div>
            <div id="container_y1" class="panel-body box-container">
              <?php
              foreach($graph_y as $k_row=>$v_row) {
                if($k_row!=$this->input->get("graph_y")) {
              ?>
              <div itemid="<?php echo $k_row;?>" class="btn btn-default box-item"><span class="text-success"><?php echo $v_row;?></span></div>
              <?php } } ?>
            </div>
            <div class="drag-drop">Drag & Drop</div>
          </div>
          <div class="panel panel-success">
            <div class="panel-heading">
              <h1 class="panel-title">Y</h1>
            </div>
            <div id="container_y2" class="panel-body box-container">
              <?php
              foreach($graph_y as $k_row=>$v_row) {
                if($k_row==$this->input->get("graph_y")) {
              ?>
              <div itemid="<?php echo $k_row;?>" class="btn btn-default box-item"><span class="text-success"><?php echo $v_row;?></span></div>
              <?php } } ?>
            </div>
            <div class="drag-drop">Drag & Drop</div>
          </div>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h1 class="panel-title">ตัวแปรเชิงคุณภาพ X</h1>
              <?php $graph_x = array("MediaType"=>"Media Type","Company"=>"Company","GroupKeyword"=>"Group Keyword","KeywordTop5"=>"Keyword Top 5"); ?>
            </div>
            <div id="container_x1" class="panel-body box-container">
              <?php
              foreach($graph_x as $k_row=>$v_row) {
                if($k_row!=$this->input->get("graph_x")) {
              ?>
              <div itemid="<?php echo $k_row;?>" class="btn btn-default box-item"><span class="text-info"><?php echo $v_row;?></span></div>
              <?php } } ?>
            </div>
            <div class="drag-drop">Drag & Drop</div>
          </div>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h1 class="panel-title">X</h1>
            </div>
            <div id="container_x2" class="panel-body box-container">
              <?php
              foreach($graph_x as $k_row=>$v_row) {
                if($k_row==$this->input->get("graph_x")) {
              ?>
              <div itemid="<?php echo $k_row;?>" class="btn btn-default box-item"><span class="text-info"><?php echo $v_row;?></span></div>
              <?php } } ?>
            </div>
            <div class="drag-drop">Drag & Drop</div>
          </div>

          <p>Graph Type</p>
          <a class="btn btn-app <?php if($this->input->get('graph_type')=='Pie') echo 'selected';?>" graph-type="Pie"><i class="fa fa-pie-chart"></i> Pie</a>
          <a class="btn btn-app <?php if($this->input->get('graph_type')=='Bar') echo 'selected';?>" graph-type="Bar"><i class="fa fa-bar-chart"></i> Bar</a>
          <a class="btn btn-app <?php if($this->input->get('graph_type')=='Line') echo 'selected';?>" graph-type="Line"><i class="fa fa-line-chart"></i> Line</a>
          <a class="btn btn-app <?php if($this->input->get('graph_type')=='Table') echo 'selected';?>" graph-type="Table"><i class="fa fa-th"></i> Table</a>
        </div>
      </div>
    </div>
    <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Display Graph</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <h5 class="choose-remark text-danger"><i class="fa fa-check-square-o"></i> Please select axis Y,X and Graph Type.</h5>
          <div id="ChartContainer" class="ChartContainer">

          </div>
          <div id="ChartTable" style="display: none;">
            <div class="">
              <div class="btn-export"><button class="btn btn-success" onclick="window.location.href='<?php echo site_url($module."/cmdExport");?>'">Excel</button></div>
              <table id="tableGraph" style="width:1200px;" class="table table-bordered jambo_table table-striped datatable-fixed-header">
                <thead>
                  <tr>
                    <th style="vertical-align: middle;">Msg ID</th>
                    <th style="vertical-align: middle;"">Media Type</th>
                    <th style="vertical-align: middle;">Feed Type</th>
                    <th style="vertical-align: middle;">Url</th>
                    <th style="vertical-align: middle;">Author</th>
                    <th style="vertical-align: middle;">Body</th>
                    <th style="vertical-align: middle;">Share</th>
                    <th style="vertical-align: middle;">Like</th>
                    <th style="vertical-align: middle;">Time</th>
                    <th style="vertical-align: middle;">Sentiment</th>
                  </tr>
                </thead>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="clearfix"></div>
</div>
