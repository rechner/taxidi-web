<?php
  require_once "template/header.php";
  
  require_once 'config.php';
  require_once "functions.php";
  $dbh = db_connect();
  
  $todelete = array();
  foreach ($_REQUEST as $k => $v)
    if (is_numeric($k))
      $todelete[] = $k;
?>
<form action="" method="post">
  <?php>
    $sth = $dbh->prepare('SELECT id,name,lastname FROM data WHERE id IN (' .
      substr(str_repeat(',?', count($todelete)), 1) . ')');
    echo 'Are you sure you wish to delete the following ', count($todelete), ' people?<br><ul>';
    if($sth->execute($todelete))
      while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        echo '<li>', $row["name"], " ", $row["lastname"], '</li>';
        echo sprintf('<li><input type="checkbox" style="display:none" name="%1$d">%2$s %3$s</li>');
      }
    echo '</ul>';
  ?>
  <button type="submit" name="action" value="delete" class="btn btn-danger" type="button">Delete Selected</button>
</form>
<?php require_once "template/footer.php" ?>
