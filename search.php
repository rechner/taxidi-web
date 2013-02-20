<?php
  //internationalisation
  $domain = "search";
  require_once 'locale.php';

  $page_title = "Search";
  require_once "template/header.php";
  
  require_once 'config.php';
  require_once "functions.php";
  $dbh = db_connect();
  
  $_REQUEST["search"] = trim($_REQUEST["search"]);
?>
          <!-- sidebar -->
          <div class="span3">
            <div class="well sidebar-nav">
              <ul class="nav nav-list">
                <li class="nav-header"><?php echo _("Search"); ?></li>
                <li class="active"><a href="#"><i class="icon-search"></i><?php echo _("Search"); ?></a></li>
                <li><a href="advsearch.php"><i class="icon-filter"></i><?php echo _("Advanced"); ?></a></li>
                <li><a href="#"><i class="icon-bookmark"></i><?php echo _("Saved Searches"); ?></a></li>
              </ul>
              <ul class="nav nav-list">
                <li class="nav-header"><?php echo _("Actions"); ?></li>
                <li><a href="register.php"><i class="icon-plus-sign"></i><?php echo _("Register"); ?></a></li>
                <li><a href="#"><i class="icon-user"></i><?php echo _("Register Visitor"); ?></a></li>
                <li><a href="#"><i class="icon-print"></i><?php echo _("Print Search"); ?></a></li>
                <li><a href="#"><i class="icon-download-alt"></i><?php echo _("Download Results"); ?></a></li>
              </ul>
            </div>
          </div>
          <!-- /sidebar -->

          <div class="span9">
            <?php
              if(array_key_exists("checkedin", $_GET)) {
                echo '<div class="span9"><div class="alert alert-success">';
                echo '<a class="close" data-dismiss="alert" href="#">×</a>';
                echo "<h4 class=\"alert-heading\">{$_GET["checkedin"]} checked in successfully.";
                echo '</h4></div>';
              }
            ?>
            <!-- Search form -->
            <form class="well form-search" name="search" action="search.php" method="get">
              <input type="text" class="input-medium search-query" name="search" 
                placeholder="<?php echo _("Search"); ?>…"
                <?php echo ($_REQUEST["search"] != "" ? "value=\"{$_REQUEST["search"]}\"" : "" )?> autofocus>
              <button type="submit" class="btn"><?php echo _("Search"); ?></button>
              <div class="pull-right">
                Service:
                <select class="input-medium" name="service">
                  <?php
                    $service = $_REQUEST["service"];
                    $now = strtotime(date("H:m:s"));
                    $before = false;
                    foreach ($dbh->query("SELECT id, name, \"endTime\" FROM services ORDER BY \"endTime\";") as $row) {
                      $selected = $service != "" ? ($service == $row["id"]) : (!$before and ($now < strtotime($row["endTime"])));
                      $before = $now < strtotime($row["endTime"]);
                      echo "<option value=\"{$row["id"]}\"" . ($selected ? " selected" : "") . ">{$row["name"]}</option>\n";
                    }
                  ?>
                </select> 
              </div>
            </form>
            
            <?php
              if ($_REQUEST["search"] == "") {
                echo '';
              } else {
                //Perform a query:
                if ($inp == '*') {
                  $sql = "SELECT DISTINCT data.id, data.name, lastname, 
                            activities.name, rooms.name, paging
                            FROM \"data\" 
                            LEFT JOIN activities ON data.activity=activities.id
                            LEFT JOIN rooms ON data.room = rooms.id
                            ORDER BY lastname;";
                } else {
                  $sql = "SELECT DISTINCT data.id, data.name, lastname, 
                            activities.name, rooms.name, paging
                            FROM \"data\" 
                            LEFT JOIN activities ON data.activity=activities.id
                            LEFT JOIN rooms ON data.room = rooms.id WHERE
                              data.name || ' ' || lastname ILIKE :inp
                              OR parent1 ILIKE :inp
                              OR parent2 ILIKE :inp
                              OR phone LIKE :inp
                              ORDER BY lastname;";
                }
                
                $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth->execute(array(":inp" => "%".$_REQUEST["search"]."%"));
                                
                if ($sth->rowCount() == 0) {
                  echo '<div class="alert alert-error">';
                  echo '<a class="close" data-dismiss="alert" href="#">×</a>';
                  echo "<h4 class=\"alert-heading\">" . 
                    sprintf(_("No results for &ldquo;%s&rdquo;"), $_REQUEST["search"]). "</h4>";
                  echo '</div>';
                } else {
                  // setup table:
                  echo '<table class="table">
                          <thead>
                            <tr>
                              <th><input type="checkbox" class="select-all"></th>
                              <th>' . _("Name") . '</th>
                              <th>' . _("Activity") . '</th>
                              <th>' . _("Room") . '</th>
                              <th>' . _("Paging") . '</th>
                            </tr>
                          </thead>
                          <tbody>';
                  // iterate over result set
                  // print each row
                  foreach ($sth as $row) {
                    echo '<tr>
                          <td style="width:30px"><input type="checkbox" name="foo"></td>';
                    echo '<td><a href="details.php?id='.$row[0].'&query='.$_REQUEST["search"].'&service='.$_REQUEST["service"].'">' . $row[1] . ' ' . $row[2] .'</a></td>';
                    echo '<td>' . $row[3] . '</td>';
                    echo '<td>' . $row[4] . '</td>';
                    echo '<td>' . $row[5] . '</td>';
                    echo '</tr>';
                    
                  }
                  echo '</tbody></table>';
                }
              }
            ?>
            
          </div>
      </div>
<?php require_once "template/footer.php" ; ?>
