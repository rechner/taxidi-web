<?php
  require_once 'config.php';
  require_once 'functions.php';
  
  $sql = "INSERT INTO statistics(person, date, service, expires, checkin, code, location, activity, room)
            SELECT :person, current_date, (SELECT name FROM services WHERE id = :service),
              '23:59:59', now()::timestamp, :code, :location, activities.name, rooms.name FROM data 
              LEFT JOIN activities ON data.activity=activities.id
              LEFT JOIN rooms ON data.room = rooms.id
              WHERE data.id = :person;";
  $dbh = db_connect();
  $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
  ob_start();
  include "/usr/lib/cgi-bin/secure-code.php";
  $sth->execute(array(
    ":person"   => $_REQUEST["person"],
    ":service"  => $_REQUEST["service"],
    ":code"     => ob_get_clean(),
    ":location" => get_client_ip()
  ));
  
?>