<?php
  function session_create_fingerprint() {
    return hash("sha256", $_SERVER['REMOTE_ADDR'] .";". $_SERVER['HTTP_USER_AGENT'] .";". $_SESSION["salt"]);
  }
  
  function session_verify_fingerprint() {
    return $_SESSION["fingerprint"] == session_create_fingerprint();
  }
  
  function session_assert_valid() {
    if (!session_verify_fingerprint()) {
      $params = session_get_cookie_params();
      setcookie(session_name(), "", time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
      );
      header("Location: login.php");
    }
    // TODO: send to login page with redirect to page they were on previously
  }
?>
