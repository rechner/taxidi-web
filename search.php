<?php
  //internationalisation
  $domain = "search";
  require_once 'locale.php';

  $page_title = "Search";
  require_once "template/header.php";
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
            <!-- Search form -->
            <form class="well form-search" name="search" action="search.php" method="post">
              <input type="text" class="input-medium search-query" name="search" 
                placeholder="<?php echo _("Search"); ?>…" autofocus>
              <button type="submit" class="btn"><?php echo _("Search"); ?></button>
            </form>
            
            <?php
              //get input:
              $inp = $_POST["search"];
              //check for get:
              if (array_key_exists('search', $_GET)) {
                $inp = $_GET['search'];
              }
              if ($inp == "") {
                echo '';
              } else {
                // do query
                require_once "functions.php";
                require_once 'config.php';
                $dbh = db_connect();
                
                $inp = trim($inp);
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
                $sth->execute(array(":inp" => "%".$inp."%"));
                                
                if ($sth->rowCount() == 0) {
                  echo '<div class="alert alert-error">';
                  echo '<a class="close" data-dismiss="alert" href="#">×</a>';
                  echo "<h4 class=\"alert-heading\">" . 
                    sprintf(_("No results for &ldquo;%s&rdquo;"), $inp). "</h4>";
                  echo '</div>';
                } else {
                  // setup table:
                  echo '<table class="table">
                          <thead>
                            <tr>
                              <script language="JavaScript">
                                function toggle(source) {
                                  checkboxes = document.getElementsByName(\'foo\');
                                  for(var i in checkboxes)
                                    checkboxes[i].checked = source.checked;
                                }
                              </script>
                              <th><input type="checkbox" onClick="toggle(this)"></th>
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
                    echo '<td><a href="details.php?id='.$row[0].'&query='.$inp.'">' . $row[1] . ' ' . $row[2] .'</a></td>';
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
