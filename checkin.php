<?php
  require_once 'config.php';
  require_once 'functions.php';
  
  function get_code() {
    ob_start();
    include "/usr/lib/cgi-bin/secure-code.php";
    ob_end_clean();
    return $read[$i];
  }
  
  /*"""INSERT INTO statistics(person, date, service, expires,
                  checkin, checkout, code, location, activity, room)
                  VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s);""", 
                  (person, str(datetime.date.today()), service, expiry,
                  datetime.datetime.now(), None, code, location, activity, room)*/
  // date = "current_date"
  // service name = "SELECT name FROM services WHERE id = $servid;"
  // expires = "23:59:59"
  // checkin = "now()::timestamp"
  // code = get_code();
  // location = TODO
  // activity = "SELECT name FROM activities WHERE id = $actid;"
  // room = "SELECT name FROM rooms WHERE id = $roomid;"
  // sql function(int person, int service, string location, int activity, int room)
  
  $sql = "INSERT INTO statistics(person, date, service, expires, checkin, code, location, activity, room)
            VALUES (
              :person,
              current_date,
              (SELECT name
                FROM services
                WHERE id = :service),
              '23:59:59',
              now()::timestamp,
              :code,
              :location,
              (SELECT name
                FROM activities
                WHERE id = :activity),
              (SELECT name
                FROM rooms
                WHERE id = :room));";
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
  echo "\nPDO::errorInfo():\n";
  print_r($dbh->errorInfo());
  
?>
