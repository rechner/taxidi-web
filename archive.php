<?php
  require_once 'config.php';
  require_once 'functions.php';
  
  $dbh = db_connect();
  $sth = $dbh->prepare("INSERT INTO archive_numeric (date, name, value)
                   VALUES (
                     :archive_date, :service || '.' || :activity || '.' || 'total', (
                       SELECT COUNT(*) FROM statistics WHERE date = :archive_date AND activity = :activity AND service = :service
                     )
                   )");
  $archive_date = "2012-11-18";
  foreach ($dbh->query("SELECT name FROM activities") as $activity) {
    foreach ($dbh->query("SELECT name FROM services") as $service) {
      $sth->execute(array(
        ":archive_date" => $archive_date,
        ":activity"     => $activity[0],
        ":service"      => $service[0]
      ));
    }
  }
?>
