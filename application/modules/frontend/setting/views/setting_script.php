<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/parsleyjs/dist/parsley.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net/js/jquery.dataTables.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net/js/jszip.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script src="<?php echo theme_assets_url(); ?>vendors/bower_components/datatables.net-scroller/js/dataTables.scroller.min.js"></script>

<script src="<?php echo theme_assets_url(); ?>js/jquery.mousewheel.min.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/jquery.jscrollpane.min.js"></script>

<script type="text/javascript" src="<?php echo theme_url("admin"); ?>/vendors/jquery.form.js"></script>
<script src="<?php echo theme_assets_url(); ?>js/modules/setting.js"></script>

<script type="text/javascript" src="<?php echo theme_assets_url(); ?>vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script>
    $('input[name="myRadios"]').change(function() {
        var key_name = $(this).val()
        var key_id = $(this).attr("key-id")
        console.log(key_name);
        $("#lbkeyword").text(key_name);
        $("#lbkeyword").attr("key-id", key_id);
        $("#exampleModal").modal('hide');
        ajax_monitoring(key_id);

    });
    function ajax_monitoring(key_id)
    {
        var url = urlbase+"setting/cmdGetKeywordSetting";
        var my_orders = $("#detal");
        $.ajax({
            type : 'post',
            cache: false,
            data: {id:key_id},
            url: url,
            beforeSend: function() {
                // setting a append empty 
                $('#detal').empty()
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            },
            success : function(data) {
                $.each(JSON.parse(data), function(index, value) {
                    $.each(value, function(i, v) {
                        if (v.type == 'include'){
                            my_orders.append('<li class="keyname green"> ' + v.includeexclude_name + '<a href="javascript:;" class="btnDelPost pull-right" key-id='+key_id+' InEx-id="' + v.includeexclude_id + '"><i class="fa fa-times"></i></a></li>');
                            $('.green').css("color", "green");
                        }
                        else if (v.type == 'exclude'){
                            my_orders.append('<li class="keyname red"> ' + v.includeexclude_name + '<a href="javascript:;" class="btnDelPost pull-right" key-id='+key_id+' InEx-id="' + v.includeexclude_id + '"><i class="fa fa-times"></i></a></li>');
                            $('.red').css("color", "red");
                        }
                    });
                });
            }
        });
    }
    $(document).delegate('.btnDelPost', 'click', function() {
        var key_name = $(".keyname").text()
        var InEx_id = $(this).attr("InEx-id")
        var key_id = $(this).attr("key-id")
        ajax_deleteInEx(InEx_id,key_id);
    });
    function ajax_deleteInEx(InEx_id,key_id)
    {
        var url = urlbase+"setting/cmdDelKeyInEx";
        $.ajax({
            type : 'post',
            data: {id:InEx_id},
            url: url,
            beforeSend: function() {
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            },
            success : function(data) {
                ajax_monitoring(key_id);
            }
        });
    }
    $(document).delegate('.buttonInEx', 'click', function() {
        var type = $(this).text().toLowerCase();
        var key_id = $("#lbkeyword").attr("key-id")
        var key_tag = $("#tagkeyword").val()
        ajax_insertInEx(key_tag,key_id,type);
    });
    function ajax_insertInEx(key_tag,key_id,type)
    {
        var url = urlbase+"setting/cmdInsertKeyInEx";
        $.ajax({
            type : 'post',
            data: {key_tag : key_tag,key_id : key_id, type : type},
            url: url,
            beforeSend: function() {
                if (key_id == "") {
                    alert("กรุณาเลือก keyword");
                } else if (key_tag == ""){
                    alert("กรุณากรอก tag keyword");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            },
            success : function(data) {
                // alert("Add Tag Keyword "+data);
                ajax_monitoring(key_id);
                $('#tagkeyword').val('')
            }
        });
    }
</script>