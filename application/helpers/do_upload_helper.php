<?php
function doUploadImage($name = "" ,$module = "")
{
	$BASEPATH = str_replace("system/","",BASEPATH);
	include_once(APPPATH."libraries/class.upload.php");

	$CI =& get_instance();
    $session_id = $CI->session->userdata("session_id");

	$return = "";
	$fileName = $name.date("ymdHis").$session_id;
	$fileExt = end(explode(".",@$_FILES[$name]["name"]));

	$handle = new upload(@$_FILES[$name]);

	if ($handle->uploaded) {

		$handle->file_new_name_body   = $fileName;
	    $handle->file_new_name_ext = $fileExt;
	    $handle->process($BASEPATH."upload/".$module."");
	    $handle->processed;

	    $handle->file_new_name_body   = $fileName;
	    $handle->file_new_name_ext = $fileExt;
	    $handle->image_resize          = true;
		$handle->image_ratio_crop      = true;
		$handle->image_y               = 120;
		$handle->image_x               = 190;
	    $handle->process($BASEPATH."upload/".$module."/thumb_edit/");
 		$handle->processed;

 		$handle->file_new_name_body   = $fileName;
	    $handle->file_new_name_ext = $fileExt;
	    $handle->image_resize          = true;
		$handle->image_ratio_crop      = true;
		$handle->image_y               = 25;
		$handle->image_x               = 50;
	    $handle->process($BASEPATH."upload/".$module."/thumb_list/");
 		$handle->processed;

		$handle->clean();
		$return = $fileName.".".$fileExt;
	}

	return $return;
}

function doUploadFile($name ="",$module = "")
{
	$BASEPATH = str_replace("system/","",BASEPATH);
	$return = "";
	$CI =& get_instance();
    $session_id = $CI->session->userdata("session_id");
	if(@$_FILES[$name]["name"]!="") :
		$fileName = $name.date("ymdHis").$session_id;
		$fileExt = end(explode(".",@$_FILES[$name]["name"]));
		$return = $fileName.".".$fileExt;
    	@move_uploaded_file($_FILES[$name]["tmp_name"],$BASEPATH."upload/".$module."/".$return);
    endif;
    return $return;
}
?>