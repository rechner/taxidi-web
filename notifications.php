<?php
  require_once 'config.php';
  require_once 'functions.php';
  $dbh = db_connect();
  
  session_assert_valid();
  
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = 'INSERT INTO notifications (
      message, priority, location) VALUES (
      :message, :priority, :location);';
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(
      ':message'  => $_REQUEST['message'],
      ':priority' => $_REQUEST['priority'],
      ':location' => $_REQUEST['location']
    ));
  }
?>
