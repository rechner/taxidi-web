<?php

require_once "config.php";
require_once 'functions.php';

$target_path = "uploads/" . $_POST["id"]; 

// localisation
$domain = "error";
require_once "locale.php";

/* JSON RESPONSE FIELDS
  success       bool    true if the next get request will return the correct photo
  newphotoid    string  new photo id
  modified      string  last modified date
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

if (array_key_exists("photo", $_FILES)) {
  if ($_FILES["photo"]["type"] == "image/png" or $_FILES["photo"]["type"] == "image/jpeg") {
    if ($_FILES["photo"]["size"] < $photo_maxsize) {
      $dbh = db_connect();
      $sth = $dbh->prepare('SELECT picture FROM data WHERE id = :id');
      $sth->execute(array(":id" => $_POST["id"]));
      $rdata = $sth->fetch(PDO::FETCH_ASSOC);
      
      //TODO remove all other possible pictures.
      //TODO check for error with deleting
      if (is_numeric($rdata["picture"])) {
        $files = glob($photo_path . str_pad(intval($rdata["picture"]), 6, "0", 0) . "*");
        unlink($files[0]);
      }

      $sth = $dbh->query("SELECT max(picture) FROM data;");
      $rdata = $sth->fetch(PDO::FETCH_ASSOC);
      
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
      $lastmodified = date("Y-m-d H:i:s.u", $_SERVER["REQUEST_TIME"]);
      $sql = "UPDATE data SET picture = :filelen, \"lastModified\" = :lastmod WHERE id = :id;";
      $sth = $dbh->prepare($sql);
      $sth->execute(array(":id" => $_POST["id"], ":lastmod" => $lastmodified, ":filelen" => $filen));
      $output["modified"] = $lastmodified;
    } else {
      echo _("File too large");
    }
  } else {
    echo _("File type not supported.");
  }
} else {
  
}

echo json_encode($output);

?>
