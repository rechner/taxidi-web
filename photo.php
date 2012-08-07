<?php
	require_once 'config.php';
	
	if (is_numeric($_GET["id"])) {
    //TODO: check if file exists.
		$files = glob($photo_path . str_pad($_GET["id"], 6, "0", 0) . "*");
		$file = $files[0];
	} else {
		$file = "resources/nophoto.png";
	}
  
	if(array_key_exists("HTTP_IF_MODIFIED_SINCE",$_SERVER)) {
		$ims = strtotime(preg_replace('/;.*$/','',$_SERVER["HTTP_IF_MODIFIED_SINCE"]));
		if($ims >= filemtime($file)) {
      header("HTTP/1.0 304 Not Modified");
      exit();
    }
 	}
  
	//TODO: check for valid image mime type
	header("Content-Type: " . mime_content_type($file));
  header("Last-Modified: " . date("r", filemtime($file)));
  readfile($file);
  
?>
