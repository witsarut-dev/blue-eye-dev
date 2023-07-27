var lookup = [];
var form_post = [];

function createForm(form, post, module, child) {
    var $html = "";
    var $h2 = [];
    var $groups = "";

    for (var $i in form) {

        var $item = form[$i];

        if (typeof $item.groups !== 'undefined' && $item.groups != $groups) {
            if ($i==0) {
                $html += "<h2>" + $item.groups + "</h2>";
            } else {
                $html += "<hr /><h2>" + $item.groups + "</h2>";
            }
            $groups = $item.groups;
        }

        var $itemClass = (typeof $item.class !== 'undefined') ? $item.class : '';
        var $itemRows = (typeof $item.rows !== 'undefined') ? $item.rows : '';
        var $itemValue = (typeof $item.value !== 'undefined') ? $item.value : '';
        var $itemWidth = (typeof $item.width !== 'undefined') ? $item.width : '';
        var $itemMultiple = (typeof $item.multiple !== 'undefined') ? $item.multiple : '';
        var $itemMaxlength = (typeof $item.maxlength  !== 'undefined') ? $item.maxlength  : '';
        var $required = (typeof $item.validate !== 'undefined' && $item.validate.search("notEmpty") > -1) ? '<span class="required"> * </span>' : '';

        $html += '<div class="form-group">';
        if ($item.type == "child") {
            $html += '<div id="' + $item.name + '" child="' + $item.id + '" class="col-sm-offset-1 col-sm-11 child">';
            $html += '</div>';
        } else if ($item.type == "text" || $item.type == "email" || $item.type == "number") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $value = (typeof post[$item.name] !== 'undefined' && post[$item.name] !== null) ? post[$item.name] : $itemValue;
            $html += '<input type="text" class="form-control ' + $itemClass + '" name="' + $item.name + '" id="' + $item.id + '" placeholder="' + $item.label + '" value="' + $value + '" style="width:' + $itemWidth + '" maxlength="' + $itemMaxlength  + '" />';
            if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            $html += '</div>';
        } else if ($item.type == "password") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $value = (typeof post[$item.name] !== 'undefined' && post[$item.name] !== null) ? post[$item.name] : $itemValue;
            $html += '<input type="password" class="form-control ' + $itemClass + '" name="' + $item.name + '" id="' + $item.id + '" placeholder="' + $item.label + '" value="' + $value + '" style="width:' + $itemWidth + '" maxlength="' + $itemMaxlength  + '" />';
            if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            $html += '</div>';
        } else if ($item.type == "date") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $value = (typeof post[$item.name] !== 'undefined' && post[$item.name] !== null) ? getDateFormat(post[$item.name]) : "";
            $html += '<input type="text" class="form-control date ' + $itemClass + '" name="' + $item.name + '" id="' + $item.id + '" placeholder="' + $item.label + '" value="' + $value + '" style="width:' + $itemWidth + '" maxlength="' + $itemMaxlength  + '" />';
            if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            $html += '</div>';
        } else if ($item.type == "datetime") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $value = (typeof post[$item.name] !== 'undefined' && post[$item.name] !== null) ? getDatetimeFormat(post[$item.name]) : "";
            $html += '<input type="text" class="form-control datetime ' + $itemClass + '" name="' + $item.name + '" id="' + $item.id + '" placeholder="' + $item.label + '" value="' + $value + '" style="width:' + $itemWidth + '" maxlength="' + $itemMaxlength  + '" />';
            if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            $html += '</div>';
        } else if ($item.type == "file" || $item.type == "image") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $value = (typeof post[$item.name] !== 'undefined' && post[$item.name] !== null) ? post[$item.name] : "";
            var $itemFiletype = (typeof $item.filetype !== 'undefined') ? $item.filetype : "";
            var $isImage = ($item.type == "image") ? true : false;
            //$filetype = explode("|",@$item["filetype"]);
            var $editpath = urlbase + 'upload/' + module + '/thumb_edit/' + $value;
            var $urlpath = urlbase + 'upload/' + module + '/' + $value;
            var showUpload = true;
            if ($isImage && $value!="") {
                if (child) {
                    $html += '<img src="' + $editpath + '" onerror="this.onerror=null;this.src=\'themes/admin/assets/images/no-image.png\';" class="img-preview-child" /> <a href="javascript:;" rel="' + $value + '" ref="' + $item.name + '" class="glyphicon glyphicon-remove btn-delete-file-child" data-toggle="tooltip" title="click to delete"></a>';
                } else {
                    $html += '<img src="' + $editpath + '" onerror="this.onerror=null;this.src=\'themes/admin/assets/images/no-image.png\';" class="img-preview" /> <a href="javascript:;" rel="' + $value + '" ref="' + $item.name + '" class="glyphicon glyphicon-remove btn-delete-file" data-toggle="tooltip" title="click to delete"></a>';
                }
                showUpload = false;
            }
            if (!$isImage && $value!="") {
                if (child) {
                    $html += '<a href="' + $urlpath + '" target="_blank"/>' + $value + '</a> <a href="javascript:;" rel="' + $value + '" ref="' + $item.name + '" class="glyphicon glyphicon-remove btn-delete-file-child" data-toggle="tooltip" title="click to delete"></a>';
                } else {
                    $html += '<a href="' + $urlpath + '" target="_blank"/>' + $value + '</a> <a href="javascript:;" rel="' + $value + '" ref="' + $item.name + '" class="glyphicon glyphicon-remove btn-delete-file" data-toggle="tooltip" title="click to delete"></a>';
                }
                showUpload = false;
            }
            if(showUpload) {
                $html += '<input type="file" class="' + $itemClass + '" name="' + $item.name + '"  id="' + $item.id + '" filetype="' + $itemFiletype + '" value="' + $value + '" />';
                if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            }
            $html += '</div>';
        } else if ($item.type == "textarea") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $value = (typeof post[$item.name] !== 'undefined' && post[$item.name] !== null) ? post[$item.name] : $itemValue;
            $html += '<textarea class="form-control ' + $itemClass + '" name="' + $item.name + '" id="' + $item.id + '" rows="' + $itemRows + '" style="width:' + $itemWidth + '">' + $value + '</textarea>';
            if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            $html += '</div>';
        } else if ($item.type == "select" || $item.type == "lookup") {

            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $value = (typeof post[$item.name] !== 'undefined' && post[$item.name] !== null) ? post[$item.name] : $itemValue;
            $html += '<select id="' + $item.id + '" name="' + $item.name + '" ' + $itemMultiple + ' class="form-control ' + $itemClass + '" style="width:' + $itemWidth + '">';
            $html += '<option value="">Selected</option>';

            if($item.type=="lookup")  {
                var token = $.md5($item.query);
                var newitem = {id:$item.id,token:token,value:$value};
                lookup.push(newitem);
            } else {
                for (var $i2 in $item.data) {
                    var $item2 = $item.data[$i2];
                    var $selected = ($value == $item2.value) ? 'selected="selected"' : "";
                    $html += '<option value="' + $item2.value + '" ' + $selected + '>' + $item2.label + '</option>';
                }
            }
            $html += '</select>';
            if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            $html += '</div>';
        } else if ($item.type == "checkbox") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            //var $inline = ($item.data.length>1) ? '' : 'checkbox';
            $html += '<div class="">';
            for (var $i2 in $item.data) {
                var $item2 = $item.data[$i2];
                var $checked = (typeof post[$item2.name] !== 'undefined' && post[$item2.name] == $item2.value) ? 'checked="checked"' : "";
                $html += '<label class="checkbox-inline">';
                $html += '<input type="checkbox" id="' + $item2.id + '" name="' + $item2.name + '" value="' + $item2.value + '" ' + $checked + ' /> ' + $item2.label;
                $html += '</label>';
            }
            $html += '</div>';
            if (typeof $item.alert !== 'undefined') $html += '<div class="alert alert-warning">' + $item.alert + '</div>';
            $html += '</div>';
        } else if ($item.type == "radio") {
            $html += '<label for="' + $item.name + '" class="col-sm-3 control-label">' + $required + $item.label + '</label>';
            $html += '<div class="col-sm-9">';
            var $inline = ($item.data.length > 1) ? '' : 'radio';
            $html += '<div class="' + $inline + '">';
            for (var $i2 in $item.data) {
                var $item2 = $item.data[$i2];
                var $checked = (typeof post[$item2.name] !== 'undefined' && post[$item2.name] == $item2.value) ? 'checked="checked"' : "";
                $html += '<label class="radio-inline">';
                $html += '<input type="radio" id="' + $item2.id + '" name="' + $item2.name + '" value="' + $item2.value + '" ' + $checked + ' /> ' + $item2.label;
                $html += '</label>';
            }
            $html += '</div>';
            $html += '</div>';
        } 
        $html += '</div>';
    }// end for
    return $html;
}

function create_lookup()
{
    var lookup_rows = 0;
    for(var i in lookup) {
        var item = lookup[i];
        if($("#refid").size()>0) {
            item.refid = $("#refid").val();
        }
        $.post(urlpath+'create_lookup',item,function(res){
            for(i2 in res) {
                var item2 = res[i2];
                var $selected = (item2.post == item2.value) ? 'selected="selected"' : "";
                $("#"+item2.id).append("<option value=\""+item2.value+"\" "+$selected+">"+item2.label+"</option>");
            }
            lookup_rows++;
            if(lookup_rows==lookup.length) load_lookup_success();
        },'json');
    }
}

function load_lookup_success()
{
    //custom lookup success
    if($('#formSearch').size()>0) {
        form_post = $('#formSearch').serializeArray();
        load_page_ajax();
    }
}

function getDateFormat(datetime) {
    if (datetime.search('/') < 0 && datetime.search('-') > -1) {
        var dt = datetime.split(" ");
        var date = dt[0].split("-");
        return date[2] + "/" + date[1] + "/" + date[0];
    } else {
        return datetime;
    }
}

function getDatetimeFormat(datetime) {

    if (datetime.search('/') < 0 && datetime.search('-') > -1) {
        var dt = datetime.split(" ");
        var date = dt[0].split("-");
        if (dt.length > 1) {
            var time = dt[1].split(":");
        } else {
            var time = ['00', '00'];
        }
        return date[2] + "/" + date[1] + "/" + date[0] + " " + time[0] + ":" + time[1];
    } else {
        return datetime;
    }
}