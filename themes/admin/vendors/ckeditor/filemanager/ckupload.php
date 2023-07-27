<? 

$dir = $_SERVER['DOCUMENT_ROOT'].'/ufo/upload/'.time()."_".$_FILES['upload']['name'];
$url = "http://".$_SERVER['HTTP_HOST'].'/ufo/upload/'.time()."_".$_FILES['upload']['name'];

 //extensive suitability check before doing anything with the file...
    $ext = strtolower(end(explode(".",@$_FILES['upload']["name"])));
    if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name'])) )
    {
      $message = "No file uploaded.";
    }
    else if ($_FILES['upload']["size"] == 0)
    {
      $message = "The file is of zero length.";
    }
    else if (preg_match('/\p{Thai}/u', @$_FILES['upload']['name']) === 1) {
      $message ='Cannot upload a file name thai character';
    }
    else if(@$_GET['type']=='Images' && ($ext!='png' && $ext!='gif' && $ext!='jpg' && $ext!='jpeg')) 
    {
      $message = "The image must be in either GIF , JPG or PNG format. Please upload a JPG or PNG instead.";
    }
    else if(@$_GET['type']=='Flash' && ($ext!='swf' && $ext!='flv')) 
    {
      $message = "The image must be in either SWF or FLV format. Please upload a SWF or FLV instead.";
    }
    else if(@$_GET['type']=='Files' && ($ext!='doc' && $ext!='docx' && $ext!='pdf' && $ext!='xls' && $ext!='xlsd' && $ext!='txt')) 
    {
      $message = "The image must be in either SWF or FLV format. Please upload a SWF or FLV instead.";
    }
	  else if (!is_uploaded_file($_FILES['upload']["tmp_name"]))
    {
       $message = "You may be attempting to hack our server. We're on to you; expect a knock on the door sometime soon.";
    }
    else {
      $message = "";
	
      $move =  move_uploaded_file($_FILES['upload']['tmp_name'], $dir);
      if(!$move)
      {
         $message = "Error moving uploaded file. Check the script is granted Read/Write/Modify permissions.";
      }
      //$url = "../" . $url;
    }

	
	if($message != "")
	{
		$url = "";
	}

	$funcNum = $_GET['CKEditorFuncNum'] ;
	echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";

?>