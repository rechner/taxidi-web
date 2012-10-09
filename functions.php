<?php
  function session_create_fingerprint() {
    return hash("sha256", $_SERVER['REMOTE_ADDR'] .";". $_SERVER['HTTP_USER_AGENT'] .";". $_SESSION["salt"]);
  }
?>
