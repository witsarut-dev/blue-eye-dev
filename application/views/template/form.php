<?php $this->load->view("template/header.php");?>
<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="page-title">
      <div class="title_left">
        <?php $this->load->view("template/breadcrumb.php"); ?>
      </div>
    </div>

    <div class="clearfix"></div>

    <div id="formCtrl" ng-controller="formCtrl" class="row">
      <div class="col-md-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>{{title}}</h2>
            <div class="pull-right">
              <button ng-if="action=='update'" ng-click="setRefresh('webadmin')" type="button" class="btn btn-dark btn-back">
                <span class="glyphicon glyphicon-chevron-left"></span> Back to list
              </button>
              <button ng-if="action!='update'" ng-click="setRefresh(module)" type="button" class="btn btn-dark btn-back">
                <span class="glyphicon glyphicon-chevron-left"></span> Back to list
              </button>
            </div>
            <div class="clearfix"></div>
          </div>
          <?php $alert_massage = $this->session->flashdata('ALERT_MESSAGE'); ?>
          <?php if($alert_massage!="") { ?>
          <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo $alert_massage; ?>
          </div>
          <?php } ?>
          <div class="x_content">
            <form id="myForm" name="myForm" ng-action="{{module}}/" class="form-data form-horizontal form_{{action}}" role="form" method="post" enctype="multipart/form-data" ref="{{module}}/changeLog">

              <ul id="myTab" class="nav nav-tabs">
                <li ng-repeat="item in tabForm" class="{{$index==0 ? 'active' : ''}}"><a href="#tab-{{item.id}}" data-toggle="tab"><i class="fa fa-file-o"></i> {{item.label}}</a></li>
                <li ng-if="(action=='edit' || action=='display') && log_mode!='OFF'"><a href="#change_log" data-toggle="tab"><i class="fa fa-clock-o"></i> Change Log</a></li>
              </ul>
              <div id="myTabContent" class="tab-content">
                <div ng-repeat="item in tabForm" class="tab-pane {{$index==0 ? 'active' : 'fade'}}" id="tab-{{item.id}}">
                  
                </div>
                <div ng-if="(action=='edit' || action=='display') && log_mode!='OFF'" class="tab-pane fade" id="change_log">
                  <?php $this->load->view("template/log.php"); ?>
                </div>
              </div>

              <div ng-if="action=='add' || action=='edit'" class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <div class="btn-group">
                    <button ng-if="publish_mode!='OFF'" rel="{{id}}" type="button" class="btn btn-dark btn-save-publish"><span class="glyphicon glyphicon-floppy-open"></span> Save & Publish</button>
                    <button rel="{{id}}" type="button" class="btn btn-dark btn-save"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
                    <!-- <button ng-if="action=='add'" rel="{{id}}" type="button" class="btn btn-dark btn-save-list"><span class="glyphicon glyphicon-floppy-save"></span> Save to list</button> -->
                    <button ng-if="action=='edit' && publish_mode!='OFF'" rel="{{id}}" type="button" class="btn btn-dark btn-publish-list"><span class="glyphicon glyphicon-globe"></span> Publish</button>
                    <button ng-if="action=='edit' && delete_mode!='OFF'" rel="{{id}}" type="button" class="btn btn-dark btn-delete-list"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                  </div>
                </div>
              </div>
    
              <div ng-if="action=='display' && edit_mode!='OFF'" class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <div class="btn-group">
                    <button ng-click="setRefresh(module+'/formEdit/'+id)" type="button" class="btn btn-dark btn-edit"><span class="glyphicon glyphicon-pencil"></span> Edit</button>
                  </div>
                </div>
              </div>

              <div ng-if="action=='update'" class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <div class="btn-group">
                    <button rel="{{id}}" type="button" class="btn btn-dark btn-update"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
                  </div>
                </div>
              </div>

              <input type="hidden" id="id" name="id" value="{{id}}" />
              <input type="hidden" name="module" id="module" value="{{module}}" />
              <input type="hidden" name="filepath" id="filepath" value="" />
              <input type="hidden" name="filename" id="filename" value="" />
              <button type="submit" class="btn btn-submit" style="display:none"></button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->
<?php $this->load->view("template/footer.php");?>
<script type="text/javascript">
$(function(){
  if(app_mode.log_mode && (app_mode.action=="edit" || app_mode.action=="display")) {
    form_post = $('#myForm').serializeArray();
    load_page_ajax();
  }
  default_date_picker();
});
function resize_iframe(child,height) {
  $("#iframe-"+child).height(height+100);
}
</script>