<?php
    require_once 'config.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
                              
    $page_title = "Statistics";
    
    require_once "template/header.php";
?>

<!-- sidebar -->
<div class="span3">
  <div class="well sidebar-nav">
    <ul class="nav nav-list">
      <li class="nav-header">Search</li>
      <li><a href="search.php"><i class="icon-search"></i>Search</a></li>
      <li><a href="#"><i class="icon-filter"></i>Advanced</a></li>
      <li><a href="#"><i class="icon-bookmark"></i>Saved searches</a></li>
      <li class="nav-header">Actions</li>
    </ul>
  </div>
</div>
<!-- /sidebar -->

<script>
  window.onload = function(){
    new JsDatePick({
      useMode:2,
      target:"date",
      dateFormat:"%Y-%m-%d",
      imgPath:"resources/img/datepicker"
      /* weekStartDay:1*/
    });
    var dp1_oldof = document.getElementById("date").onfocus;
    document.getElementById("date").onfocus = function() {
      dp1_oldof.call(this);
      document.getElementById("datef_single").checked = true;
    }
  };
</script>

<div class="span9 well">
  <h2>Count</h2>
  <form class="form-horizontal" method="get">
    <fieldset>
<?php
  $selectfilters = array(
  // human label         http+php    postgres
    "Service"  => array("service" , "services"  ),
    "Activity" => array("activity", "activities"),
    "Room"     => array("room"    , "rooms"),
  );
  
  foreach ($selectfilters as $label => $sysname) {
    echo "<div class=\"control-group\">
          <label class=\"control-label\" for=\"{$sysname[0]}\">$label</label>
          <div class=\"controls\">
          <select name=\"{$sysname[0]}\" id=\"{$sysname[0]}\">
          <option value=\"any\" selected> -- Any -- </option>";
    $query = "SELECT id, name FROM {$sysname[1]};";
    $result = pg_query($connection, $query) or
      die("Error in query: $query." . pg_last_error($connection));
    while ($data = pg_fetch_assoc($result)) {
      echo "<option value=\"{$data["id"]}\"";
      if ( $data["id"] == $_GET[$sysname[0]] ) {
        echo " selected";
        $$sysname[0] = $data["name"];
      }
      echo ">{$data["name"]}</option>\n";
    }
    pg_free_result($result);
    echo "</select></div></div>";
  }
?>
      <div class="control-group form-inline">
        <label class="control-label" for="date">Date</label>
        <div class="controls">
          <label class="radio inline">
            <input type="radio" id="datef_any" style="margin-bottom: 9px;" name="datef" value="any" checked>
          </label>
          Any date <br/>
          <label class="radio inline">
            <input type="radio" name="datef" id="datef_single" value="single"<?php echo ($_GET["datef"] == "single" ? "checked" : ""); ?>>
          </label>
          <input type="text" class="input-small" style="height: 28px;" name="date" id="date" value="<?php echo $_GET["datef"] == "single" ? $_GET["date"] : date("Y-m-d"); ?>">
        </div>
      </div>
      <div class="control-group form-inline">
        <label class="control-label">Statistics</label>
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
    "Total"      => "WHERE true",
    "Members"    => "LEFT JOIN data ON data.id = person WHERE (data.visitor = FALSE or data.visitor IS NULL)",
    "Visitors"   => "LEFT JOIN data ON data.id = person WHERE data.visitor = TRUE",
    "Volunteers" => "WHERE volunteer > 1",
  );
  
  foreach ($stats as $name => $querymain) {
    $query = "SELECT count(person) FROM statistics " . $querymain . $queryend;
    //echo $query . "<br/>\n";
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
