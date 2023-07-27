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

    <div id="listCtrl" ng-controller="listCtrl" class="row">
      <div class="col-md-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>{{title}}</h2>
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
            <div id="boxSearch">
              <form name="formSearch" id="formSearch" method="post" action="{{module}}" class="form-horizontal" role="form" ref="{{module}}/ajaxList">
                <ul id="myTabSearch" class="nav nav-tabs">
                    <li class="{{post.TabSelected!='#TabAdvance'? 'active' : ''}}"><a href="#TabKeyword" data-toggle="tab"><i class="fa fa-tag"></i> Keyword search</a></li>
                    <li ng-if="searchForm.length>0" class="{{post.TabSelected=='#TabAdvance'? 'active' : ''}}"><a href="#TabAdvance" data-toggle="tab"><i class="fa fa-tags"></i> Advance search</a></li>
                </ul>
                <div id="myTabSearchContent" class="tab-content">
                  <div class="tab-pane fade {{post.TabSelected!='#TabAdvance'? 'active in' : ''}}" id="TabKeyword">
                    <div class="form-group">
                      <div class="col-sm-8">
                        <ul ng-if="publish_mode!='OFF'" class="nav nav-pills" id="item-sys_action">
                            <li class="active"><a href="javascript:;" rel="">All <span class="badge" ng-bind="rows_all">0</span></a></li>
                            <li class=""><a href="javascript:;" rel="publish">Publish <span class="badge" ng-bind="rows_publish">0</span></a></li>
                            <li class=""><a href="javascript:;" rel="modified">Modified <span class="badge" ng-bind="rows_modified">0</span></a></li>
                            <li class=""><a href="javascript:;" rel="unpublish">Draft <span class="badge" ng-bind="rows_unpublish">0</span></a></li>
                        </ul>
                      </div>
                      <div class="col-sm-4">
                        <input type="text" class="form-control " name="keyword" id="keyword" placeholder="Keyword" value="{{post.keyword}}" maxlength="255" />
                      </div>
                    </div>
                  </div>
                  <div ng-if="searchForm.length>0" class="tab-pane fade {{post.TabSelected=='#TabAdvance'? 'active in' : ''}}" id="TabAdvance">
                    <div id="advanceForm"></div>
                  </div>
                </div>
                <button type="button" ng-click="setRefresh(module)" class="btn btn-default btn-refresh"><span class="glyphicon glyphicon-refresh"></span> Refresh</button>
                <button type="submit" class="btn btn-dark btn-search"><span class="glyphicon glyphicon-search"></span> Search</button>
                <input type="hidden" id="TabSelected" name="TabSelected" value="{{post.TabSelected}}" />
                <input type="hidden" name="sys_action" id="sys_action" value="" />
              </form>
            </div>
            <?php $this->load->view("template/control.php");?>
            <div class="row">
              <div class="col-md-12 col-xs-12 num_rows">Items <span class="badge" ng-bind="rows"></span></div>
            </div>
            <div class="table-responsive">
              <form name="formList" id="formList" method="post" ng-action="{{module}}/" ref="{{module}}" class="form-data">
                <table align="center" style="width:{{tbwidth}};" class="table table-hover table-striped table-bordered jambo_table bulk_action">
                  <thead>
                    <tr>
                      <th ng-if="checkbox_mode!='OFF'" class="center" width="30"><input type="checkbox" id="check-all" onclick="check_all(this)" /></th>
                      <th ng-if="publish_mode!='OFF'" class="center" width="30">Sta</th>
                      <th ng-if="control_mode!='OFF'"  class="center" width="80">Control</th>
                      <th ng-repeat="col in column" width="{{col.width}}">
                        <div ng-if="col.sort=='T'" class="btn-sort" sorting="{{col.name}}">{{col.label}}</div>
                        <div ng-if="col.sort!='T' && col.sort!='F' && col.sort!=''" class="btn-sort" sorting="{{col.sort}}.{{col.name}}">{{col.label}}</div>
                        <div ng-if="col.sort=='F' || col.sort==''" ng-bind="col.label"></div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="item in items">
                      <td ng-if="checkbox_mode!='OFF'" class="center"><input type="checkbox" name="id[]" value="{{getObj(item,module+'_id')}}" class="check-all" /></td>
                      <td ng-if="publish_mode!='OFF'" class="center"><a href="javascript:;" rel="{{getObj(item,module+'_id')}}" class="glyphicon glyphicon-globe btn-{{item.sys_action}}" data-toggle="tooltip" title="change status"></a></td>
                      <td ng-if="control_mode!='OFF'" class="center">
                        <a ng-if="edit_mode!='OFF'" href="{{module}}/formEdit/{{getObj(item,module+'_id')}}" class="glyphicon glyphicon-pencil btn-edit" data-toggle="tooltip" title="click to edit"></a>
                        <a ng-if="delete_mode!='OFF'" href="javascript:;" rel="{{getObj(item,module+'_id')}}" class="glyphicon glyphicon-trash btn-delete" data-toggle="tooltip" title="click to delete"></a>
                        <a ng-if="display_mode!='OFF'" href="{{module}}/formDisplay/{{getObj(item,module+'_id')}}" class="glyphicon glyphicon-eye-open btn-display" data-toggle="tooltip" title="click to display"></a>
                      </td>
                      <td ng-repeat="col in column" width="{{col.width}}" class="{{col.class}}">
                        <div ng-if="col.type=='image'" class="image"><img ng-src="upload/{{module}}/thumb_list/{{getObj(item,col.name)}}" width="38" height="14" onerror="this.onerror=null;this.src='themes/admin/assets/images/no-image.png';" /></div>
                        <div ng-if="col.type=='file'"><a ng-href="upload/{{module}}/{{getObj(item,col.name)}}" target="_blank">Download</a></div>
                        <div ng-if="col.type!='image' && col.type!='file'">{{formatValue(getObj(item,col.name),col.type)}}</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </form>
              <div ng-if="rows==0" class="col-md-12 col-xs-12 not_found">- Data not found -</div>
            </div>
            <?php $this->load->view("template/control.php");?>
            <input type="hidden" name="totalpage" id="totalpage" value="{{totalpage}}" />
            <input type="hidden" name="thispage" id="thispage" value="1" />
            <input type="hidden" name="pagesize" id="pagesize" value="{{pagesize}}" />
            <input type="hidden" name="sorting" id="sorting" value="{{sorting}}" />
            <input type="hidden" name="orderby" id="orderby" value="{{orderby}}" />
            <input type="hidden" name="module" id="module" value="{{module}}" />
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->
<?php $this->load->view("template/footer.php");?>
<script type="text/javascript">
$(function() {
   form_post = $('#formSearch').serializeArray();
   load_page_ajax();
});
</script>