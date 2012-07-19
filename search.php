<!DOCTYPE html>
<!-- vim: tabstop=2:softtabstop=2 -->
<?php require_once "template/header.php"; ?>
					<!-- sidebar -->
          <div class="span3">
            <div class="well sidebar-nav">
              <ul class="nav nav-list">
                <li class="nav-header">Search</li>
                <li class="active"><a href="#"><i class="icon-search"></i>Search</a></li>
                <li><a href="#"><i class="icon-filter"></i>Advanced</a></li>
                <li><a href="#"><i class="icon-bookmark"></i>Saved searches</a></li>
              </ul>
              <ul class="nav nav-list">
                <li class="nav-header">Actions</li>
                <li><a href="#"><i class="icon-plus-sign"></i>Register</a></li>
                <li><a href="#"><i class="icon-user"></i>Register Visitor</a></li>
                <li><a href="#"><i class="icon-print"></i>Print Search</a></li>
                <li><a href="#"><i class="icon-download-alt"></i>Download Results</a></li>
            </div>
          </div>
          <!-- /sidebar -->

          <div class="span9">
            <!-- Search form -->
            <form class="well form-search" name="search" action="search.php" method="post">
              <input type="text" class="input-medium search-query" name="search" placeholder="Search…">
              <button type="submit" class="btn">Search</button>
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
                require_once 'config.php';
                $connection = pg_connect ("host=$dbhost dbname=$dbname 
                                          user=$dbuser password=$dbpass");
                //Perform a query:
                if ($inp == '*') {
                  $query = "SELECT DISTINCT data.id, data.name, lastname, 
                            activities.name, rooms.name, paging
                            FROM \"data\" 
                            LEFT JOIN activities ON data.activity=activities.id
                            LEFT JOIN rooms ON data.room = rooms.id
                            ORDER BY lastname;";
                } else {
                  $query = "SELECT DISTINCT data.id, data.name, lastname, 
                              activities.name, rooms.name, paging
                              FROM \"data\" 
                              LEFT JOIN activities ON data.activity=activities.id
                              LEFT JOIN rooms ON data.room = rooms.id WHERE
                                data.name ILIKE '$inp'
                                OR lastname ILIKE '$inp'
                                OR parent1 ILIKE '$inp'
                                OR parent2 ILIKE '$inp'
                                OR phone LIKE '%$inp'
                                ORDER BY lastname;";
                }
                
                $result = pg_query($connection, $query) or 
                  die("Error in query: $query." . pg_last_error($connection));
                  
                $roomsql = "SELECT id, name FROM rooms;";
                $rooms = pg_query($connection, $roomsql)
                    or die("Error in query: $query." . pg_last_error($connection));


                                
                if (pg_num_rows($result) == 0) {
                  echo '<div class="alert alert-error">';
                  echo '<a class="close" data-dismiss="alert" href="#">×</a>';
                  echo "<h4 class=\"alert-heading\">No results for &ldquo;$inp&rdquo;</h4>";
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
                              <th>Name</th>
                              <th>Activity</th>
                              <th>Room</th>
                              <th>Paging</th>
                            </tr>
                          </thead>
                          <tbody>';
                  // iterate over result set
                  // print each row
                  while ($row = pg_fetch_array($result)) {
                    
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

<?php require_once "template/footer.php" ; ?>
