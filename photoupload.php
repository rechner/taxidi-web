<?php

require_once "config.php";

$target_path = "uploads/" . $_POST["id"]; 

//TODO json response
//TODO add accepted image types as array to config.php
//TODO loop through accepted image types in both this file and details.php with javascript

if ($_FILES["photo"]["type"] == "image/png" or $_FILES["photo"]["type"] == "image/jpeg") {
  if ($_FILES["photo"]["size"] < $photo_maxsize) {
    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
    $query = "SELECT picture FROM data WHERE id = {$_POST["id"]};";
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $rdata = pg_fetch_assoc($result);
    pg_free_result($result);
    
    /*if (is_numeric($rdata["picture"])) {
      //TODO remove all other possible pictures.
      $filebase = $photo_path . str_pad($rdata["picture"], 6, "0", 0);
      $files = glob($filebase . "*");
      if (unlink($files[0])) {
        $parts = explode("/", $_FILES["photo"]["type"]);
        $target_path = $filebase . "." . $parts[1];
        echo $target_path;
        if (move_uploaded_file($_FILES["photo"]['tmp_name'], $target_path)) {
          echo "SUCCESS";
        } else {
          echo "Cannot move new photo";
        }
      } else {
        echo "Cannot remove old photo";
      }
    } else {*/
      $query = "SELECT max(picture) FROM data;";
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      $rdata = pg_fetch_assoc($result);
      pg_free_result($result);
      
      //TODO remove all other possible pictures.
      //TODO check for error with deleting
      $files = glob($photo_path . str_pad(intval($rdata["max"]), 6, "0", 0) . "*");
      unlink($files[0]);
      
      $parts = explode("/", $_FILES["photo"]["type"]);
      $filen = intval($rdata["max"]) + 1;
      $target_path = $photo_path . str_pad($filen, 6, "0", 0) . "." . $parts[1];
      echo $target_path;
      if (move_uploaded_file($_FILES["photo"]['tmp_name'], $target_path)) {
        echo "SUCCESS! New photo id: " . $filen ;
      } else {
        echo "Cannot move new photo";
      }
      
      //TODO make a new method of getting a new id that doesn't fill up
      $query = "UPDATE data SET picture = '$filen' WHERE id = {$_POST["id"]};";
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      pg_free_result($result);
    //}
    
    pg_close($connection);
  } else {
    echo "File too large";
  }
} else {
  echo "File type not supported.";
}

?>
