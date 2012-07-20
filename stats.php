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
  foreach (array("service", "activity", "room") as $filter) {
    if (is_numeric($_GET[$filter]) and $_GET[$filter] >= 1) {
      $filters[] = "statistics.$filter LIKE '{$$filter}'";
    }
  }
  if ($_GET["datef"] == "single") {
    $filters[] = "statistics.date = DATE '{$_GET["date"]}'";
  }
  
  $queryend = (count($filters) > 0 ? " and " . implode(" and ", $filters): "") . ";" ;
  $stats = array(
    "Total"      => "                                         WHERE true",
    "Members"    => "JOIN data ON data.id = statistics.person WHERE data.visitor = FALSE",
    "Visitors"   => "JOIN data ON data.id = statistics.person WHERE data.visitor = TRUE or data.visitor IS NULL",
    "Volunteers" => "                                         WHERE volunteer > 1",
  );
  
  foreach ($stats as $name => $querymain) {
    $query = "SELECT count(person) FROM statistics " . $querybase . $querymain;
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $row = pg_fetch_assoc($result);
    echo "$name: {$row["count"]}<br/>";
    pg_free_result($result);
  }
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
