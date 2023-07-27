<div id="CategoryData" class="fancybox-body">
    <div class="panel panel-default card-view">
        <div class="panel-heading">
            <div class="title_left">
                <h3>Add Category</h3>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
            <div class="panel-body">
                <div class="x_panel" id="CategoryList">
                    <form id="formAddCategory" class="form-horizontal form-label-left" novalidate="">
                        <div class="x_content">
                            <div class="to_do list-category">
                                <?php foreach($category as $k_row=>$v_row) {  ?>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p><input name="category_id[]" type="checkbox" class="flat" value="<?php echo $v_row['category_id'];?>"> <?php echo $v_row['category_name'];?></p>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                            <br />
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input id="category_name" name="category_name" type="text" class="form-control required inputAddList" placeholder="Category" maxlength="255">
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="ln_solid"></div>
                                <div class="btn-group" data-toggle="buttons">
                                    <button type="button" class="btn btn-primary btn-round fa fa-plus btnAddCategory" target="CategoryList" targetForm="formAddCategory"> Add</button>
                                    <button type="button" class="btn btn-primary btn-round fa fa-trash btnDelCategory" disabled target="CategoryList"> Delete</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="pull-right"><button type="button" class="btn btn-success btn-round"><i class="fa fa-save"></i> Submit</button></div>
        <div class="clearfix"></div>
        <br />
    </div>
</div>
<input type="hidden" id="add_category" name="add_category" value="<?php echo $add_category;?>" />
<style>
#alert-confirm,#alert-error {
    z-index: 9999;
}
.fancybox-overlay-fixed {
  z-index: 8000;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    $("input.flat")[0] && $(document).ready(function() {
        $("input.flat").iCheck({
            checkboxClass: "icheckbox_flat",
            radioClass: "iradio_flat"
        })
    });


    $(document).delegate('input.flat', 'ifChanged',function() {
        var target = $(this).parents('.x_panel').attr("id");
        if($("#"+target).find("input[type=checkbox]").is(":checked")==false) {
            $("#"+target).find('.fa-trash:first').prop("disabled",true);
        } else {
            $("#"+target).find('.fa-trash:first').prop("disabled",false);
        }
    });

    $("#CategoryData .btn-success").click(function(){
        showOnLoading();
        window.location.reload(0);
    });

    $("#CategoryData .btnAddCategory").click(function(){

        var targetForm = $(this).attr("targetForm");
        $("#"+targetForm).parsley().validate();
        if(!$("#"+targetForm).parsley().validate()) {
            $("#"+targetForm).find('.parsley-errors-list').show();
        } else {
            var url  = urlbase+"marketing/cmdAddCategory";
            var data = $("#"+targetForm).serialize();

            var target = $(this).attr('target');
            var inputAddList = $("#"+target).find('.inputAddList').val();

            $.ajax({
                type : 'post',
                dataType : 'json',
                data: data,
                url: url,
                beforeSend: function() {
                    $.fancybox.showLoading();
                    $(".btnAddCategory").prop("disabled",true);
                },
                error: function() {
                    $.fancybox.hideLoading();
                    dialog_error("Error");
                    $(".btnAddCategory").prop("disabled",false);
                },
                success : function(res) {
                    if(res.status) {
                        $("#"+target).find('.inputAddList').val("");

                        var _value_ = res.category_id;
                        var params = ' value="'+_value_+'" ';
                        $("#"+target+" .to_do").append('<div class="col-md-6 col-sm-6 col-xs-12"><p><input name="category_id[]" type="checkbox" class="flat" "'+params+'"> '+inputAddList+'</p></div>');
                        $("#"+target+" .to_do div:last").hide().fadeIn("slow");
                        $("#"+target+" input.flat:last").iCheck({
                            checkboxClass: "icheckbox_flat",
                            radioClass: "iradio_flat"
                        });
                        $("#"+target+" input.flat:last").iCheck("check");
                    } else {
                        dialog_error(res.message);
                    }
                    $.fancybox.hideLoading();
                    $(".btnAddCategory").prop("disabled",false);
                }
            });
        }
    });

    $("#CategoryData button.btnDelCategory").click(function(){
        dialog_confirm("คุณต้องการลบ Category คุณยืนยันที่จะลบหรือไม่","cmd_del_category();");
    });

});

function cmd_del_category()
{
    var url  = urlbase+"marketing/cmdDelCategory";
    var data = $("#formAddCategory").serialize();

    $.ajax({
        type : 'post',
        dataType : 'json',
        data: data,
        url: url,
        beforeSend: function() {
            $.fancybox.showLoading();
        },
        error: function() {
            $.fancybox.hideLoading();
            dialog_error("Error");
        },
        success : function(res) {
            if(res.status) {
                showOnLoading();
                window.location.reload(0);
            } else {
                dialog_error(res.message);
            }
            $.fancybox.hideLoading();
        }
    });
}
</script>