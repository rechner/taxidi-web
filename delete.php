<?php
  //get input:
  if (is_numeric($id = $_GET["id"])) { 
    require_once 'config.php';
    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
                              
    $query = "SELECT * FROM data WHERE id = $id;";
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $edata = pg_fetch_assoc($result);
    
    if (!$edata) {
      header("HTTP/1.1 400 Bad Request");
      die("ID " . $id . " does not exist.");
    } else {
      $query = "DELETE FROM data WHERE id = $id;";
      $result = pg_query($connection, $query) or
        die("Error in query: $query." . pg_last_error($connection));
        
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
  
    pg_free_result($result);
    exit;
  
  } else {
    header("HTTP/1.1 400 Bad Request");
    die("Missing or malformed ID parameter");
  }
?>
