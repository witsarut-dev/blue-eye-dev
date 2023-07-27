<?php $visibility = ($period=="3M" || $period=="Custom") ? "hidden" :  "visible";?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default card-view-2">
                <div class="col-md-6 col-sm-6 col-xs-12" id="toolPeriod">
                    <?php echo $this->load->view("include/period_view");?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="row">
      <div id="Mapkeyfil" class="col-lg-12">
        <div class="panel panel-default card-view">
					<div class="panel-heading">
						<div class="pull-left"><h6 class="panel-title txt-dark">Keyword</h6></div>
						<div class="clearfix"></div>
					</div>
          <div class="panel-wrapper collapse in">
            <div class="panel-body">
        			<div class="scroll-pane horizontal-only" style="white-space: nowrap;">
        			</div>
            </div>
          </div>
        </div>
			</div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
      <div id="keywordfeed" class="col-lg-4 ">
        <div class="panel panel-default card-view">
					<div class="panel-heading">
						<div class="pull-left"><h7 class="panel-title txt-dark">Keyword content</h7></div>
						<div class="clearfix"></div>
					</div>
          <div class="panel-wrapper collapse in">
            <div class="panel-body">
        			<div class="scroll-pane">
                  <h4 style="color:red">Please select Keyword</h4>
        			</div>
            </div>
          </div>
        </div>
			</div>
      <div class="col-lg-8">
        <div class="panel panel-default card-view">
            <div class="panel-wrapper collapse in">
                <div id="icon-loading" class="icon-container">
                  <i class="fa fa-spin fa-circle-o-notch">
                  </i>
                </div>
                <div id="map-iframe"  style="width:100%;height:510px;">
                </div>
            </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
</div>
