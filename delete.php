<?php
  //get input:
  if (is_numeric($id = $_GET["id"])) { 
    require_once 'config.php';
    require_once 'functions.php';
    $dbh = db_connect();
                              
    $sth = $dbh->prepare("SELECT * FROM data WHERE id = :id;");
    $sth->execute(array(":id" => $_GET["id"]));
    $edata = $sth->fetch(PDO::FETCH_ASSOC);
    
    if (!$edata) {
      header("HTTP/1.1 400 Bad Request");
      die("ID " . $id . " does not exist.");
    } else {
      $sth = $dbh->prepare("DELETE FROM data WHERE id = :id;");
      $sth->execute(array(":id" => $_GET["id"]));
        
      //delete photos
      $files = glob($photo_path . str_pad($edata["picture"], 6, "0", 0) . "*");
      unlink($files[0]);
      $files = glob($photo_path . "/thumbs/" . str_pad($edata["picture"], 6, "0", 0) . "*");
      unlink($files[0]);
        
      $host  = $_SERVER['HTTP_HOST'];
      $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
      if (array_key_exists('query', $_GET)) {
          $query = $_GET['query'];
          header("Location: http://$host$uri/search.php?search=$query");
      } else {
          header( "Location: http://$host$uri/search.php" );
      }
    }
  
  } else {
    header("HTTP/1.1 400 Bad Request");
    die("Missing or malformed ID parameter");
  }
?>
