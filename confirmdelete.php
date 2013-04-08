<?php
  require_once 'config.php';
  require_once 'functions.php';
  $dbh = db_connect();
  
  $todelete = array();
  foreach ($_REQUEST as $k => $v)
    if (is_numeric($k))
      $todelete[] = $k;
  
  $confirmed = array_key_exists('action', $_POST) && $_POST['action'] == 'delete';
  $sth = $dbh->prepare((!$confirmed ? 'SELECT id,name,lastname' : 'DELETE') .
    ' FROM data WHERE id IN (' . substr(str_repeat(',?', count($todelete)), 1) . ')');
  count($todelete) > 0 && $sth->execute($todelete);
  
  $returnuri = sprintf('search.php?search=%1$s&service=%2$d',
      $_REQUEST['search'], $_REQUEST['service']);
  
  if ($confirmed || count($todelete) == 0)
    header('Location: ' . $returnuri) && exit;
  else
    ($page_title = 'Confirm Delete') && require_once 'template/header.php';
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span4 offset4">
      <form action="" method="post">
        <?php
          echo 'Are you sure you wish to delete the following ', count($todelete), ' people?<br><ul>';
          while ($row = $sth->fetch(PDO::FETCH_ASSOC))
            echo sprintf('<li><input type="checkbox" checked style="display:none" name="%1$d">%2$s %3$s</li>',
              $row['id'], $row['name'], $row['lastname']);
          echo '</ul>';
        ?>
        <button type="submit" name="action" value="delete" class="btn btn-danger" type="button">Delete Selected</button>
        <a class="btn" href="<?php echo $returnuri; ?>">Cancel</a>
      </form>
    </div>
  </div>
</div>
<?php require_once "template/footer.php" ?>
