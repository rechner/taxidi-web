<?php
  function session_create_fingerprint() {
    return hash("sha256", $_SERVER['REMOTE_ADDR'] .";". $_SERVER['HTTP_USER_AGENT'] .";". $_SESSION["salt"]);
  }
  
  function session_verify_fingerprint() {
    if (empty($_SESSION["userid"]))
      return false;
    return $_SESSION["fingerprint"] == session_create_fingerprint();
  }
  
  function session_assert_valid() {
    session_start();
    if (!session_verify_fingerprint()) {
      /*$params = session_get_cookie_params();
      setcookie(session_name(), "", time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
      );*/
      session_regenerate_id();
      $_SESSION=array();
      $_SESSION["backto"] = $_SERVER["REQUEST_URI"];
      header("Location: login.php");
    }
  }
?>
