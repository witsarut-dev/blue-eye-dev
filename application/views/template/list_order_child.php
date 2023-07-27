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
	
						</form>
						<div ng-if="rows==0" class="col-md-12 col-xs-12 not_found">- Data not found -</div>
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
var app_module = '<?php echo $module;?>';
var dd_html = "";
var nestList = [];
$(function(){

	$("#pagesize_"+app_child).val(app_list.rows);

    //load js
    load_child_page(app_child);
    load_date_picker();
    dialog_child();
});

function load_on_child_success(res)
{

    if ($("#maxDepth").size()==0) {
        var maxDepth = 3;
    } else {
        var maxDepth = parseInt($("#maxDepth").val());
    }
    nestList = res;
    dd_html += '<div class="root-dd" id="nestable">';
    search_parent(nestList,0);
    dd_html += '</div>';

    $("#formList").html(dd_html);
    $("ol.dd-list").each(function(){
        if($(this).find('li').size()==0) $(this).remove();
    });

    $("#childList-"+app_child).html(dd_html);
    $('#nestable').nestable({
        rootClass : 'root-dd',
        group: 0,
        maxDepth : maxDepth
    })
    .on('change', updateOutput);
    $('#nestable').nestable('collapseAll');

    if(typeof parent.resize_iframe == 'function') {
        var height = ($("#pagesize_"+app_child).val()*40);
        parent.resize_iframe(app_child,height);
    }
}

function search_parent(nestList,parent_id)
{
  dd_html += ' <ol class="dd-list">';
  $.each(nestList, function (key, item) {
    if(parent_id == item.parent_id) {
      var item_id = getObj(item,app_child+'_id');
      dd_html += '<li class="dd-item" data-id="'+item_id+'">';
      dd_html += '<div class="dd-handle dd3-handle">Drag</div>';
      dd_html += '<div class="dd3-content">';
      if(app_mode.checkbox_mode!="OFF") dd_html += ' <input type="checkbox" name="child_id[]" value="'+item_id+'" class="check-all-'+app_child+'" />';
      for(var i in app_list.column) {
        var col = app_list.column[i];
        if(col.type=='image') {
          dd_html += ' | <img src="upload/'+app_child+'/thumb_list/'+getObj(item,col.name)+'" width="38" height="14" onerror="this.onerror=null;this.src=\'themes/admin/assets/images/no-image.png\';" />';
        } else if(col.type=='file') {
          dd_html += ' | <a href="upload/'+app_child+'/thumb_list/'+getObj(item,col.name)+'" target="_blank">Download</a>';
        } else {
          dd_html += ' | '+formatValue(col.type,getObj(item,col.name));
        }
      }
      dd_html += '<div class="pull-right">';
      if(app_mode.publish_mode!="OFF") dd_html += ' <a child="'+app_child+'" href="javascript:;" rel="'+item_id+'" class="glyphicon glyphicon-globe btn-'+item.sys_action+'-child" data-toggle="tooltip" title="change status"></a> | ';
      if(app_mode.edit_mode!="OFF") dd_html += ' <a child="'+app_child+'" href="'+app_module+'/'+app_child+'/formEdit/'+item_id+'" class="glyphicon glyphicon-pencil btn-edit-child fancybox_iframe" data-toggle="tooltip" title="click to edit"></a>';
      if(app_mode.delete_mode!="OFF") dd_html += ' <a child="'+app_child+'" href="javascript:;" rel="'+item_id+'" class="glyphicon glyphicon-trash btn-delete-child" data-toggle="tooltip" title="click to delete"></a>';
      if(app_mode.display_mode!="OFF") dd_html += ' <a child="'+app_child+'" href="'+app_module+'/'+app_child+'/formDisplay/'+item_id+'" class="glyphicon glyphicon-eye-open btn-display-child fancybox_iframe" data-toggle="tooltip" title="click to display"></a>';
      dd_html += '</div>';
      dd_html += '</div>';
      search_parent(nestList,item_id);
      dd_html += '</li>';
    }
  });
  dd_html += ' </ol>';
}

var isDragStop = false;
function updateOutput(e)
{
  if(isDragStop) {
    var list   = e.length ? e : $(e.target),
        output = list.data('output');
    if (window.JSON) {
        if(typeof list.nestable('serialize') == "object") {
          $.ajax({
            dataType: "html",
            type: "POST",
            url: app_module+ "/" + app_child + "/cmdUpdateParent",
            data: "json="+window.JSON.stringify(list.nestable('serialize')),
            beforeSend: function() {
              $.fancybox.showLoading();
            },
            success: function(res) {
              $.fancybox.hideLoading();
              isDragStop = false;
            }
          });
        }
    } else {
        alert('JSON browser support required for this demo.');
    }
  }
}
</script>