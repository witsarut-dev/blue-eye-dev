<!DOCTYPE html>
<html ng-app="myApp-child" id="myApp-child" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Web Admin</title>
		<?php $this->load->view("template/script.php"); ?>
	</head>
	<body class="nav-md" style="background:#FFF">
		<div id="listCtrl-child" ng-controller="listCtrl-child">
			<div class="col-md-12 col-xs-12">
				<div class="x_content">
					<div class="row">
						<div class="col-md-6">
						</div>
						<div class="col-md-6 control pull-right">
							<div class="btn-group">
								<a href="{{module}}/{{child}}/formAdd" ng-if="add_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-add-child fancybox_iframe" ref="{{child}}">
									<span class="glyphicon glyphicon-plus-sign"></span> Added
								</a>
								<button child="{{child}}" ng-if="delete_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-delete-all-child">
									<span class="glyphicon glyphicon-trash"></span> Delete
								</button>
								<button child="{{child}}" ng-if="publish_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-publish-all-child">
									<span class="glyphicon glyphicon-globe"></span> Publish
								</button>
							</div>
							&nbsp;&nbsp;Items <span class="badge" id="badge_{{child}}">{{rows}}</span>
						</div>
					</div>
					<div class="table-responsive" style="margin: auto;">
						<form name="formList" id="childList-{{child}}" method="post" ng-action="{{module}}/{{child}}" ref="{{child}}" class="form-child">
							<table align="center" style="width:100%" class="table table-hover table-striped table-bordered jambo_table bulk_action">
								<thead>
									<tr>
										<th ng-if="checkbox_mode!='OFF'" class="center" width="30"><input type="checkbox" id="check-all-{{child}}" onclick="check_all(this)" /></th>
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
										<td ng-if="checkbox_mode!='OFF'" class="center"><input type="checkbox" name="child_id[]" value="{{getObj(item,child+'_id')}}" class="check-all-{{child}}" /></td>
										<td ng-if="publish_mode!='OFF'" class="center"><a child="{{child}}" href="javascript:;" rel="{{getObj(item,child+'_id')}}" class="glyphicon glyphicon-globe btn-{{item.sys_action}}-child" data-toggle="tooltip" title="change status"></a></td>
										<td ng-if="control_mode!='OFF'" class="center">
											<a child="{{child}}" ng-if="edit_mode!='OFF'" href="{{module}}/{{child}}/formEdit/{{getObj(item,child+'_id')}}" class="glyphicon glyphicon-pencil btn-edit-child fancybox_iframe" data-toggle="tooltip" title="click to edit"></a>
											<a child="{{child}}" ng-if="delete_mode!='OFF'" href="javascript:;" rel="{{getObj(item,child+'_id')}}" class="glyphicon glyphicon-trash btn-delete-child" data-toggle="tooltip" title="click to delete"></a>
											<a child="{{child}}" ng-if="display_mode!='OFF'" href="{{module}}/{{child}}/formDisplay/{{getObj(item,child+'_id')}}" class="glyphicon glyphicon-eye-open btn-display-child fancybox_iframe" data-toggle="tooltip" title="click to display"></a>
										</td>
										<td ng-repeat="col in column" width="{{col.width}}" class="{{col.class}}">
											<div ng-if="col.type=='image'" class="image"><img ng-src="upload/{{child}}/thumb_list/{{getObj(item,col.name)}}" width="38" height="14" onerror="this.onerror=null;this.src='themes/admin/assets/images/no-image.png';" /></div>
											<div ng-if="col.type=='file'"><a ng-href="upload/{{child}}/{{getObj(item,col.name)}}" target="_blank">Download</a></div>
											<div ng-if="col.type!='image' && col.type!='file'">{{formatValue(getObj(item,col.name),col.type)}}</div>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
						<div ng-if="rows==0" class="col-md-12 col-xs-12 not_found">- Data not found -</div>
					</div>
					<div ng-if="totalpage>1" class="row">
						<div class="col-md-12">
							<ul class="pagination pagination_{{child}}">
								<li><a href="javascript:;">&laquo;</a></li>
								<li class="active"><a href="javascript:;">1</a></li>
								<li><a href="javascript:;">&raquo;</a></li>
							</ul>
						</div>
					</div>
					<input type="hidden" name="totalpage_{{child}}" id="totalpage_{{child}}" value="{{totalpage}}" />
					<input type="hidden" name="thispage_{{child}}" id="thispage_{{child}}" value="1" />
					<input type="hidden" name="pagesize_{{child}}" id="pagesize_{{child}}" value="{{pagesize}}" />
					<input type="hidden" name="sorting_{{child}}" id="sorting_{{child}}" value="{{sorting}}" />
					<input type="hidden" name="orderby_{{child}}" id="orderby_{{child}}" value="{{orderby}}" />
					<input type="hidden" name="module" id="module" value="{{module}}" />
					<input type="hidden" name="child" id="child" value="{{child}}" />
					<input type="hidden" name="id" id="id" value="{{id}}" />
				</div>
			</div>
		</div>
	</body>
</html>
<script type="text/javascript">
var app_child = '<?php echo $child; ?>';
$(function(){

    //load js
    load_child_page(app_child);
    load_date_picker();
    dialog_child();

    if(typeof parent.resize_iframe == 'function') {
    	parent.resize_iframe(app_child,$("html").height());
    }

});
</script>