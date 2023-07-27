<div id="KeywordSetting" class="fancybox-body">
    <div class="panel panel-default card-view">
        <div class="panel-heading">
            <div class="title_left">
                <h3>Keyword Setting</h3>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-wrapper collapse in">
            <div class="panel-body">
                <div class="x_panel">
                    <form id="formSaveKeywordSetting" class="form-horizontal form-label-left" novalidate="">
                        <div class="x_content" style="border: none;">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <h5><?php echo $post['keyword_name']; ?></h5>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="ln_solid"></div>
                                <div class="checkbox checkbox-primary">
                                    <input id="thai_only_chk2" type="checkbox" name="thai_only" value="1" <?php if($post['thai_only']=='1') echo 'checked="checked"'; ?>>
                                    <label for="thai_only_chk2">for thai language only</label>
                                </div>
                                <div class="checkbox  checkbox-primary">
                                    <input id="primary_keyword_chk2" type="checkbox" name="primary_keyword" value="1" <?php if($post['primary_keyword']=='1') echo 'checked="checked"'; ?>>
                                    <label for="primary_keyword_chk2">for primary keyword </label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="keyword_id" value="<?php echo $post['keyword_id']; ?>" />
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php if($setting_allow) { ?>
        <div class="pull-right"><button type="button" class="btn btn-success btn-round btnSaveKeywordSetting"><i class="fa fa-save"></i> Submit</button></div>
        <div class="clearfix"></div>
        <?php } ?>
        <br />
    </div>
</div>
<style>
.fancybox-overlay-fixed {
  z-index: 8000;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    // $("#KeywordSetting .test").click(function(){
    //     var url  = urlbase+"setting/cmdSaveKeywordSetting";
    //     var data = $("#formSaveKeywordSetting").serialize();
    //     alert(data)
    // });
    $("#KeywordSetting .btnSaveKeywordSetting").click(function(){

        var url  = urlbase+"setting/cmdSaveKeywordSetting";
        var data = $("#formSaveKeywordSetting").serialize();

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
                    $.fancybox.close();
                    var keyword_id = $("#formSaveKeywordSetting input[name=keyword_id").val();
                    var key_parent = $("#formAddKeyword input[name^=keyword_id][value="+keyword_id+"]").parents("p");
                    $(key_parent).find(".flag").remove();
                    if($("#formSaveKeywordSetting input[name=thai_only]").is(":checked")) {
                        $(key_parent).find('a').append(' <i class="flag flag-th"></i>');
                    }
                } else {
                    dialog_error(res.message);
                }
                $.fancybox.hideLoading();
            }
        });
    });

});
</script>