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
    <div class="Popup" id="formCtrl-child" ng-controller="formCtrl-child">
      <div class="back-to-close pull-right">
        <a href="javascript:;" onclick="parent.$.fancybox.close();" class="glyphicon glyphicon-remove"></a>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">{{title}}</div>
        <div class="panel-body">
          <form id="childForm-{{child}}" ng-action="{{module}}/{{child}}/" class="form-horizontal form-child form_{{action}}" role="form" method="post" enctype="multipart/form-data" ref="{{child}}">
            
            <div id="tabForm-child"></div>
    
            <div ng-if="action=='add' || action=='edit'" class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <div class="btn-group">
                  <button child="{{child}}" ng-if="publish_mode!='OFF'"  rel="{{id}}" type="button" class="btn btn-dark btn-save-publish-child"><span class="glyphicon glyphicon-floppy-open"></span> Save & Publish</button>
                  <button child="{{child}}" rel="{{id}}" type="button" class="btn btn-dark btn-save-child"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
                  <button child="{{child}}" ng-if="action=='edit' && publish_mode!='OFF'" rel="{{id}}" type="button" class="btn btn-dark btn-publish-list-child"><span class="glyphicon glyphicon-globe"></span> Publish</button>
                  <button child="{{child}}" ng-if="action=='edit' && delete_mode!='OFF'" rel="{{id}}" type="button" class="btn btn-dark btn-delete-list-child"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                </div>
              </div>
            </div>
   
            <div ng-if="action=='display' && checkbox_mode!='OFF'" class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <div class="btn-group">
                  <a child="{{child}}" href="{{module}}/{{child}}/formEdit/{{id}}" type="button" class="btn btn-dark btn-edit-child fancybox_iframe"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                </div>
              </div>
            </div>

            <input type="hidden" id="child_id" name="child_id" value="{{id}}" />
            <input type="hidden" id="id" name="id" value="0" />
            <input type="hidden" name="filepath" id="filepath" value="" />
            <input type="hidden" name="filename" id="filename" value="" />
            <input type="hidden" id="module" name="module" value="{{module}}" />
            <input type="hidden" name="child" id="child" value="{{child}}" />
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
<script type="text/javascript">
var app_child = '<?php echo $child; ?>';
$(function() {

  //load js
  var parent = window.parent.document;
  var parent_id = $(parent).find("#myForm #id").val();
  parent_id = (parent_id=="") ? 0 : parent_id;
  $("#childForm-"+app_child+" #id").val(parent_id);
  $(".form_display select,.form_display textarea").prop('disabled', true);
  $(".form_display input").prop('disabled', true);
  $(".form_display input[type=hidden]").prop('disabled', false);
  $(".form_display .glyphicon-remove").hide();
  $("#childForm-"+app_child+" .btn-delete-file-child").attr("child",app_child);
  dialog_child();
  load_date_picker();

  $(document).delegate("input[type=file]",'change',function(){
    var start = ($(this).val().length-3);
    var end = $(this).val().length;
    var type = $(this).val().substring(start,end);
    var filetype = $(this).attr("filetype");
    var isError = true;
    if(filetype!="") {
      $.each(filetype.split("|"),function(index,value){
        if(value==type) {
          isError = false;
          return false;
        }
      });

      if(isError) {
        alert("Please upload file extensions "+filetype);
        $(this).val('');
      }
    }
  });
});
</script>