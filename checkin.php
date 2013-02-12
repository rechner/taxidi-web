<?php
  require_once 'config.php';
  require_once 'functions.php';
  
  function get_code() {
    ob_start();
    include "/usr/lib/cgi-bin/secure-code.php";
    ob_end_clean();
    return $read[$i];
  }
  
  $sql = "INSERT INTO statistics(person, date, service, expires, checkin, code, location, activity, room)
            VALUES (
              :person,
              current_date,
              (SELECT name FROM services WHERE id = :service),
              '23:59:59',
              now()::timestamp,
              :code,
              :location,
              (SELECT name FROM activities WHERE id = :activity),
              (SELECT name FROM rooms WHERE id = :room));";
  $dbh = db_connect();
  $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  $sth->execute(array(
    ":person"   => $_GET["person"],
    ":service"  => $_GET["service"],
    ":code"     => get_code(),
    ":location" => get_client_ip(),
    ":activity" => $_GET["activity"],
    ":room"     => $_GET["room"]
  ));
  
?>
