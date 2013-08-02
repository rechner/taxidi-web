<?php
  require_once 'config.php';
  require_once 'functions.php';
  
  session_assert_valid();
  $dbh = db_connect();
  
  $method = $_SERVER['REQUEST_METHOD'];
  if ($method == 'POST') {
    $sql = 'INSERT INTO notifications (
      message, priority, location) VALUES (
      :message, :priority, :location);';
    $sth = $dbh->prepare($sql);
    $sth->execute(array(
      ':message'  => $_REQUEST['message'],
      ':priority' => $_REQUEST['priority'],
      ':location' => $_REQUEST['location']
    ));
  } elseif ($method == 'GET') {
    ignore_user_abort(false);
    $sql = 'SELECT MAX(datetime) FROM notifications';
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $old = $sth->fetchColumn();
    $sth->closeCursor(); 
    $last = $old;
    while ($last == $old) {
      usleep(1000);
      $sth->execute();
      $last = $sth->fetchColumn(); 
      $sth->closeCursor();
    }
    $sth = $dbh->prepare('SELECT * FROM notifications
      WHERE isunread = true AND datetime > :olddate
      ORDER BY datetime');
    $sth->execute(array(':olddate' => $old));
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      print_r($row);
    }
    $sth->closeCursor();
  }
?>
