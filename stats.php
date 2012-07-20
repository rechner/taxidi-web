<?php
    require_once 'config.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
    
    require_once "template/header.php";
?>

<div class="span12 well">
  <h2>Count</h2>
  <form class="form-horizontal" action="" method="get">
    <fieldset>
      <div class="control-group">
        <label class="control-label" for="activity">Services</label>
        <div class="controls">
          <select name="service" id="service">
            <option value="any" selected> -- Any -- </option>
            <?php
              $query = "SELECT id, name FROM services;";
              $result = pg_query($connection, $query) or
                die("Error in query: $query." . pg_last_error($connection));
              while ($data = pg_fetch_assoc($result)) {
                echo "<option value=\"{$data["id"]}\"";
                if ( $data["id"] == $_GET["service"] ) {
                  echo " selected";
                  $service = $data["name"];
                }
                echo ">{$data["name"]}</option>\n";
              }
              pg_free_result($result);
            ?>
          </select>
        </div>
      </div>
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
                echo "<option value=\"{$data["id"]}\"";
                if ( $data["id"] == $_GET["activity"] ) {
                  echo" selected";
                  $activity = $data["name"];
                }
                echo ">{$data["name"]}</option>\n";
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
                echo "<option value=\"{$data["id"]}\"";
                if ( $data["id"] == $_GET["room"] ) {
                  echo" selected";
                  $room = $data["name"];
                }
                echo ">{$data["name"]}</option>\n";
              }
              pg_free_result($result);
            ?>
          </select>
        </div>
      </div>
      <div class="control-group form-inline">
        <label class="control-label" for="date">Date</label>
        <div class="controls">
	        <label class="radio inline">
	          <input type="radio" style="margin-bottom: 9px;" name="datef" value="any" checked>
	        </label>
          Any date
          <br/>
	        <label class="radio inline">
	          <input type="radio" name="datef" value="single"<?php echo ($_GET["datef"] == "single" ? "checked" : ""); ?>>
	        </label>
          <input type="text" class="input-small" style="height: 28px;" name="date" id="date" value="<?php echo date("Y-m-d"); ?>">
        </div>
      </div>
      <div class="control-group form-inline">
        <label class="control-label" >Statistics</label>
        <div class="controls">
<?php

    $filters = array();
    if (is_numeric($_GET["service"]) and $_GET["service"] >= 1) {
      $filters[] = "service LIKE '$service'";
    }
    if (is_numeric($_GET["activity"]) and $_GET["activity"] >= 1) {
      $filters[] = "activity LIKE '$activity'";
    }
    if (is_numeric($_GET["room"]) and $_GET["room"] >= 1) {
      $filters[] = "room LIKE '$room'";
    }
    if ($_GET["datef"] == "single") {
      $filters[] = "date = DATE '{$_GET["date"]}'";
    }
    
    $query = "SELECT count(*) FROM statistics" . (count($filters) > 0 ? " WHERE " . implode(" and ", $filters) : "") . ";";
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $row = pg_fetch_assoc($result);
    echo "Total: " . $row["count"] . "<br/>";
    pg_free_result($result);
    
    $query = "SELECT count(*) FROM statistics WHERE volunteer > 1" . (count($filters) > 0 ? " and " . implode(" and ", $filters) : "") . ";";
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $row = pg_fetch_assoc($result);
    echo "Volunteers: " . $row["count"] . "<br/>";
    pg_free_result($result);
    
    print_r($entries);
?> 
        </div>
      </div>
      <div class="form-actions">
        <input type="submit" class="btn btn-primary" value="Filter" />
      </div>
    </fieldset>
  </form>
</div>
<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>
