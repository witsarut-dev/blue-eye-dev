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
              </form>
            </div>
            <div id="BoxPage" class="row">
              <div class="col-md-8"></div>
              <div ng-if="control==true" class="col-md-4 control">
                <div class="btn-group">
                  <button ng-if="add_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-add" ref="{{module}}">
                    <span class="glyphicon glyphicon-plus-sign"></span> Added
                  </button>
                  <button ng-if="delete_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-delete-all">
                    <span class="glyphicon glyphicon-trash"></span> Delete
                  </button>
                  <button ng-if="publish_mode!='OFF'" type="button" class="btn btn-xs btn-dark btn-publish-all">
                    <span class="glyphicon glyphicon-globe"></span> Publish
                  </button>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 col-xs-12 num_rows">Items <span class="badge" ng-bind="rows"></span></div>
            </div>
            <div class="table-responsive">
              <form name="formList" id="formList" method="post" ng-action="{{module}}/" ref="{{module}}" class="form-data">
   
              </form>
              <div ng-if="rows==0" class="col-md-12 col-xs-12 not_found">- Data not found -</div>
            </div>
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
var app_module = '<?php echo $module;?>';
var dd_html = "";
var nestList = [];
$(function() {
  $("#pagesize").val(app_list.rows);
  form_post = $('#formSearch').serializeArray();
  load_page_ajax();
});

function load_on_success(res)
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

  $('#nestable').nestable({
    rootClass : 'root-dd',
    group: 0,
    maxDepth : maxDepth
  })
  .on('change', updateOutput);
  $('#nestable').nestable('collapseAll');
}

function search_parent(nestList,parent_id)
{
  dd_html += ' <ol class="dd-list">';
  $.each(nestList, function (key, item) {
    if(parent_id == item.parent_id) {
      var item_id = getObj(item,app_module+'_id');
      dd_html += '<li class="dd-item" data-id="'+item_id+'">';
      dd_html += '<div class="dd-handle dd3-handle">Drag</div>';
      dd_html += '<div class="dd3-content">';
      if(app_mode.checkbox_mode!="OFF") dd_html += ' <input type="checkbox" name="id[]" value="'+item_id+'" class="check-all" />';
      for(var i in app_list.column) {
        var col = app_list.column[i];
        if(col.type=='image') {
          dd_html += ' | <img src="upload/'+app_module+'/thumb_list/'+getObj(item,col.name)+'" width="38" height="14" onerror="this.onerror=null;this.src=\'themes/admin/assets/images/no-image.png\';" />';
        } else if(col.type=='file') {
          dd_html += ' | <a href="upload/'+app_module+'/thumb_list/'+getObj(item,col.name)+'" target="_blank">Download</a>';
        } else {
          dd_html += ' | '+formatValue(col.type,getObj(item,col.name));
        }
      }
      dd_html += '<div class="pull-right">';
      if(app_mode.publish_mode!="OFF") dd_html += ' <a href="javascript:;" rel="'+item_id+'" class="glyphicon glyphicon-globe btn-'+item.sys_action+'" data-toggle="tooltip" title="change status"></a> | ';
      if(app_mode.edit_mode!="OFF") dd_html += ' <a href="'+app_module+'/formEdit/'+item_id+'" class="glyphicon glyphicon-pencil btn-edit" data-toggle="tooltip" title="click to edit"></a>';
      if(app_mode.delete_mode!="OFF") dd_html += ' <a href="javascript:;" rel="'+item_id+'" class="glyphicon glyphicon-trash btn-delete" data-toggle="tooltip" title="click to delete"></a>';
      if(app_mode.display_mode!="OFF") dd_html += ' <a href="'+app_module+'/formDisplay/'+item_id+'" class="glyphicon glyphicon-eye-open btn-display" data-toggle="tooltip" title="click to display"></a>';
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
            url: app_module + "/cmdUpdateParent",
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