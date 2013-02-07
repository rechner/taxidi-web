<?php
  //internationalisation
  $domain = "search";
  require_once 'locale.php';

  $page_title = "Birthdays";
  require_once "template/header.php";
  
  require_once "functions.php";
  require_once 'config.php';
  $dbh = db_connect();
?>
          <!-- sidebar -->
          <div class="span3">
            <div class="well sidebar-nav">
              <ul class="nav nav-list">
                <li class="nav-header"><?php echo _("Search"); ?></li>
                <li><a href="#"><i class="icon-search"></i><?php echo _("Search"); ?></a></li>
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
              if (isset($_GET["next"])) {
                $interval = $_GET["next"];
              } else {
                $interval = "1 week";
              }
              $interval = $dbh->quote($interval);
              
              $sql = "SELECT name || ' ' || lastname AS name, dob, id, age
                        FROM (SELECT *, born + age AS birthday
                          FROM (SELECT *, date_trunc('year', age(born)) + interval '1 year' AS age
                            FROM (SELECT *, cast(dob as date) AS born
                              FROM data
                              WHERE dob != ''
                              ) AS T
                            ) AS T
                          ) AS T
                        WHERE (current_date, current_date + interval $interval) OVERLAPS (birthday, birthday)
                        ORDER BY birthday;";

              $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
              
            ?>
            <table class="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Date</th>
                  <th>Age (will be)</th>
                </tr>
              </thead>
              <tbody>
                <?
                  if ($sth->execute()) {
                    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                      echo sprintf("<tr>
                                      <td>
                                        <a href=\"details.php?id=%d\">%s</a>
                                      </td>
                                      <td>%s</td>
                                      <td>%d</td>
                                    </tr>", $row["id"], $row["name"], $row["dob"], $row["age"]);
                    }
                  }
                ?>
              </tbody>
            </table>
          </div>
            
      </div>
<?php require_once "template/footer.php" ; ?>
