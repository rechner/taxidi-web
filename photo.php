<?php
	require_once 'config.php';
	
	if (is_numeric($_GET["id"])) {
	header("Content-Type: image/jpeg");
		$file = $photo_path.str_pad($_GET["id"], 6, "0", 0).".jpg";
	} else {
		header("Content-Type: image/png");
		$file = "resources/nophoto.png";
	}
  
	if(array_key_exists("HTTP_IF_MODIFIED_SINCE",$_SERVER)) {
    $ims = strtotime(preg_replace('/;.*$/','',$_SERVER["HTTP_IF_MODIFIED_SINCE"]));
    if($ims >= filemtime($file)) {
        header("HTTP/1.0 304 Not Modified");
        exit();
    }
  }
  
  header("Last-Modified: " . date("r", filemtime($file)));
  readfile($file);
  
?>
