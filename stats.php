<?php
    require_once 'config.php';
    
    //internationalisation
    $domain = "search";
    require_once 'locale.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
                              
    $page_title = "Statistics";
    
    require_once "template/header.php";
?>

<!-- sidebar -->
<div class="span3">
  <div class="well sidebar-nav">
    <ul class="nav nav-list">
      <li class="nav-header"><?php echo _("Search") ?></li>
      <li><a href="search.php"><i class="icon-search"></i><?php echo _("Search") ?></a></li>
      <li><a href="#"><i class="icon-filter"></i><?php echo _("Advanced") ?></a></li>
      <li><a href="#"><i class="icon-bookmark"></i><?php echo _("Saved Searches") ?></a></li>
      <li class="nav-header"><?php echo _("Reporting") ?></li>
      <?php
        parse_str($_SERVER['QUERY_STRING'], $query);
        $query["mode"] = "stats";
        echo "<li" . ((!array_key_exists("mode",$_GET) or $_GET["mode"] == "stats") ? " class=\"active\"" : "") . 
          "><a href=\"?" . http_build_query($query) . 
          "\"><i class=\"icon-th-list\"></i>" . _("Summary &amp; Count") 
          . "</a></li>";
        $query["mode"] = "full";
        echo "<li" . ($_GET["mode"] == "full" ? " class=\"active\"" : "") . 
          "><a href=\"?" . http_build_query($query) . 
          "\"><i class=\"icon-th-list\"></i>" . _("Attendance") 
          . "</a></li>";
        $query["mode"] = "medical";
        echo "<li" . ($_GET["mode"] == "medical" ? " class=\"active\"" : "") . 
        "><a href=\"?" . http_build_query($query) . 
        "\"><i class=\"icon-th-list\"></i>" . _("Medical") . "</a></li>";
      ?>
    </ul>
  </div>
</div>
<!-- /sidebar -->

<div class="span9">
  <form class="form-horizontal well" method="get">
    <h2><?php echo _("Count") ?></h2>
    <fieldset>
<?php
  $selectfilters = array(
  // human label         http+php    postgres
    _("Service")  => array("service" , "services"  ),
    _("Activity") => array("activity", "activities"),
    _("Room")    => array("room"    , "rooms"),
  );
  
  foreach ($selectfilters as $label => $sysname) {
    echo "<div class=\"control-group\">
          <label class=\"control-label\" for=\"{$sysname[0]}\">$label</label>
          <div class=\"controls\">
          <select name=\"{$sysname[0]}\" id=\"{$sysname[0]}\">
          <option value=\"any\" selected> -- " . _("Any") . " -- </option>";
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
        <label class="control-label" for="date"><?php echo _("Date"); ?></label>
        <div class="controls">
          <label class="radio inline">
            <input type="radio" id="datef_any" style="margin-bottom: 9px;" 
              name="datef" value="any" checked>
          </label>
          <?php echo _("Any Date"); ?> <br/>
          <label class="radio inline">
            <input type="radio" name="datef" id="datef_single" 
            value="single"<?php echo ($_GET["datef"] == "single" ? "checked" : ""); ?>>
          </label>
          <input type="text" class="input-small datepicker" style="height: 28px;" id="date"
            name="date" value="<?php echo $_GET["datef"] == "single" ? $_GET["date"] : date("Y-m-d"); ?>">
        </div>
      </div>
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
  $queryend = (count($filters) > 0 ? " and " . implode(" and ", $filters): "");
    
  if (!array_key_exists("mode",$_GET) or $_GET["mode"] == "stats") {
    $stats = array(
      "<b>Total</b>"      => "WHERE true",
      "Members"    => "LEFT JOIN data ON data.id = person WHERE (data.visitor = FALSE or data.visitor IS NULL)",
      "Visitors"   => "LEFT JOIN data ON data.id = person WHERE data.visitor = TRUE",
      "Volunteers" => "WHERE volunteer > 1",
    );
    echo "<div class=\"control-group form-inline\">
        <label class=\"control-label\">Statistics</label>
        <div class=\"controls\">";
    foreach ($stats as $name => $querymain) {
      $query = "SELECT count(person) FROM statistics " . $querymain . $queryend . ";" ;
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      $row = pg_fetch_assoc($result);
      echo "$name: {$row["count"]}<br/>";
      pg_free_result($result);
    }
    echo "</div></div>";
  }
?>
      <div class="form-actions">
        <?php
          if(array_key_exists("mode",$_GET)) {
            echo "<input type=\"hidden\" name=\"mode\" value=\"" . $_GET["mode"] . "\"/>";
          }
        ?>
        <input type="submit" class="btn btn-primary" value="<?php _("Filter"); ?>" />
      </div>
    </fieldset>
  </form>
  <?php
    if ($_GET["mode"] == "full" or $_GET["mode"] == "medical") {
      echo "<table class=\"table\" style=\"font-size: small;\"><thead><tr><th>" .
        _("Name") . "</th>";
      if ($_GET["mode"] == "full") {
        echo "<th>" . _("Activity") . "</th><th>" .
          _("Room") . "</th><th>" . _("Paging") . "</th><th>" .
          _("Security") . "<br/>" . _("Code"). "</th>";
        $query = "SELECT DISTINCT person, data.name, lastname, statistics.activity, (
            SELECT rooms.name FROM rooms WHERE id = data.room
          ) AS roomname, paging, code
          FROM data
          LEFT JOIN statistics ON data.id = person
          LEFT JOIN activities ON data.activity=activities.id
          LEFT JOIN rooms ON data.room = rooms.id
          WHERE true $queryend
          ORDER BY person;";
      } elseif ($_GET["mode"] == "medical") {
        echo "<th>" . _("Birthday") . "</th><th>" . _("Allergies") . "</th>";
        $query = "SELECT DISTINCT person, data.name, lastname, dob
          FROM data
          LEFT JOIN statistics ON data.id = person
          WHERE true $queryend
          ORDER BY person;";
      }
      echo "</tr></thead><tbody>";
      //echo $query;
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      while ($row = pg_fetch_assoc($result)) {
        echo "<tr><td>";
        //echo "{$row["person"]}</td><td>";
        if ($_GET["mode"] == "full") {
          echo "{$row["name"]} {$row["lastname"]}</td><td>{$row["activity"]}</td><td>{$row["roomname"]}</td><td>{$row["paging"]}</td><td>{$row["code"]}";
        } elseif ($_GET["mode"] == "medical") {
          echo "{$row["name"]} {$row["lastname"]}</td><td>{$row["dob"]}</td><td>{$row["medical"]}";
        }
        //echo print_r($row);
        echo "</td></tr>";
      }
      pg_free_result($result);
      echo "</tbody></table>";
    }
  ?>
  </div>
</div>
<script>
  $(function(){
    $("#date").datepicker().on("focus", function() {$("#datef_single").prop("checked", true);});
  });
</script>
<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>
