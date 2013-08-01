<?php
  function session_create_fingerprint() {
    return hash("sha256", $_SERVER["REMOTE_ADDR"] .";". $_SERVER["HTTP_USER_AGENT"] .";". $_SESSION["salt"]);
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
  
  function db_connect() {
    global $dbdsn;
    return new PDO($dbdsn, null, null, array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION
    ));
  }
  
  function get_client_ip() {
    foreach (array("HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_X_CLUSTER_CLIENT_IP", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR") as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(",", $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                  }
              }
          }
      }
  }
    
   /**
   * This function generates a password salt as a string of x (default = 15) characters
   * in the a-zA-Z0-9!@#$%&*? range.
   * @param $max integer The number of characters in the string
   * @return string
   * @author AfroSoft <info@afrosoft.tk>
   */
  function generateSalt($max = 15) {
          $characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?";
          $i = 0;
          $salt = "";
          while ($i <= $max) {
              $salt .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
              $i++;
          }
          return $salt;
  }

?>
