<?php
    require_once 'config.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
    
    require_once "template/header.php";
?>

<div class="span9 well" style="overflow-x: auto;">
  <form class="form-horizontal" action="" method="get">
    <fieldset>
      <div class="control-group">
        <label class="control-label" for="activity">Activity</label>
        <div class="controls">
          <select name="activity" id="activity">
            <option value="any" selected> -- Any -- </option>
            <?php
              $query = "SELECT id, name FROM activities;";
              $result = pg_query($connection, $query) or
                die("Error in query: $query." . pg_last_error($connection));
              while ($data = pg_fetch_assoc($result)) {
                echo "<option value=\"{$data["id"]}\"" . ($data["id"] == $_GET["activity"] ? " selected" : "") . ">{$data["name"]}</option>\n";
              }
              pg_free_result($result);
            ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="room">Room</label>
        <div class="controls">
          <select name="room" id="room">
            <option value="any" selected> -- Any -- </option>
            <?php
              $query = "SELECT id, name FROM rooms;";
              $result = pg_query($connection, $query) or
                die("Error in query: $query." . pg_last_error($connection));
              while ($data = pg_fetch_assoc($result)) {
                echo "<option value=\"{$data["id"]}\"" . ($data["id"] == $_GET["room"] ? " selected" : "") . ">{$data["name"]}</option>\n";
              }
              pg_free_result($result);
            ?>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Filter" />
      </div>
    </fieldset>
  </form>
<?php

    $fba = (is_numeric($_GET["activity"]) and $_GET["activity"] >= 1);
    $fbr = (is_numeric($_GET["room"]) and ($_GET["room"]) >= 1);
    $filterstr = "";
    if ($fba or $fbr) {
      $filterstr .= " WHERE";
      $filterstr .= $fba ? " activity = {$_GET["activity"]}" : "";
      $filterstr .= ($fba and $fbr) ? " and" : "";
      $filterstr .= $fbr ? " room = {$_GET["room"]}" : "";
    }
    $query = "SELECT id, visitor FROM data$filterstr;";

    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    
    echo $query . "<br/>\n";
    
    while ($row = pg_fetch_assoc($result)) {
         print_r($row);
         echo "<br/>\n";
    } 
    
    pg_free_result($result);
?> 
</div>
<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>
