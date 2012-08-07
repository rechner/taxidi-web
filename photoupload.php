<?php

require_once "config.php";

$target_path = "uploads/" . $_POST["id"]; 

/* JSON RESPONSE FIELDS
  success       bool    true if the next get request will return the correct photo
  oldphotoid    string  may be null if there was no photo previously
  newphotoid    string  new photo id
  errorid       int     error code, see below
  filetype      string  the mime type of the given file
  filesize_old  string  the original size of the given file
  errormsg      string  custom error message sent with errorid < 0
*/
/* PHOTO UPLOAD ERRORS
  <0  Custom, error message is sent with response.  TODO
  00  Unknown error (only if success = false)
  01  No photo given                                TODO
  02  File type not supported                       TODO
  03  File too large                                TODO
  04  Invalid member id                             TODO
  05  Old photo cannot be removed                   TODO
  06  New photo cannot be added                     TODO
*/

//TODO json response
//TODO add accepted image types as array to config.php
//TODO loop through accepted image types in both this file and details.php with javascript

header("Content-Type: application/json");

$output = array(
  "success" => false,
  "errorid" => 0,
);

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
      $result = pg_query($connection, "SELECT max(picture) FROM data;") or 
        die("Error in query: $query." . pg_last_error($connection));
      $rdata = pg_fetch_assoc($result);
      pg_free_result($result);
      
      //TODO remove all other possible pictures.
      //TODO check for error with deleting
      if (is_numeric($rdata["picture"])) {
        $files = glob($photo_path . str_pad(intval($rdata["max"]), 6, "0", 0) . "*");
        unlink($files[0]);
      }
      
      $parts = explode("/", $_FILES["photo"]["type"]);
      $filen = intval($rdata["max"]) + 1;
      $target_path = $photo_path . str_pad($filen, 6, "0", 0) . "." . $parts[1];
      if (move_uploaded_file($_FILES["photo"]['tmp_name'], $target_path)) {
        $output["success"] = true;
        $output["newphotoid"] = $filen;
        unset($output["errorid"]);
      } else {
        $output["errorid"] = 6;
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

echo json_encode($output);

?>
