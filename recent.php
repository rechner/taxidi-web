<?php
	$page_title = "Home";
	require_once "template/header.php";
?>
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
                <li><a href="register.php"><i class="icon-plus-sign"></i>Register</a></li>
                <li><a href="#"><i class="icon-user"></i>Register Visitor</a></li>
              </ul>
            </div>
          </div>
          <!-- /sidebar -->

          <div class="span9">
            <!-- display -->
            <div class="well">
                <div class="btn-toolbar pull-right" style="margin: 0;">
                    <div class="btn-group">
                    <button class="btn btn-small btn-info dropdown-toggle" data-toggle="dropdown">Room: -Any-<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                      <li><a href="#">Any</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Bunnies</a></li>
                      <li><a href="#">Foxes</a></li>
                      <li><a href="#">Puppies</a></li>
                    </ul>
                    </div><!-- /btn-group -->
                    <div class="btn-group">
                    <button class="btn btn-small btn-info dropdown-toggle" data-toggle="dropdown">Range: Past Week<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                      <li><a href="#">Past Week</a></li>
                      <li><a href="#">Past Month</a></li>
                      <li><a href="#">Past Year</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Custom Range</a></li>
                    </ul>
                    </div><!-- /btn-group -->
                    <div class="btn-group">
                    <button class="btn btn-small btn-primary" type="button">Go</button>
                    </div>
                </div><!-- /btn-toolbar -->
                <h3>Recent Registrations                
                <?php 
                //Get recent registrations
                require_once 'config.php';
                $connection = pg_connect ("host=$dbhost dbname=$dbname 
                                          user=$dbuser password=$dbpass");
                                          
                $query = "SELECT DISTINCT data.id, data.name, lastname, 
                            activities.name, rooms.name, paging, \"joinDate\"
                            FROM \"data\" 
                            LEFT JOIN activities ON data.activity=activities.id
                            LEFT JOIN rooms ON data.room = rooms.id
                            WHERE \"joinDate\" >= current_date - INTERVAL '7 days';";
                $result = pg_query($connection, $query) or 
                    die("Error in query: $query." . pg_last_error($connection));
                
                if (pg_num_rows($result) == 0) {
                    echo "</h3><h4><i>No recent registrations within past 7 days.</i></h4>";
                } else {
                    echo ': '. pg_num_rows($result) . '</h3>';
                    //setup table
                    echo '<table class="table">
                          <thead>
                            <tr>
                              <th>Date</th>
                              <th>Name</th>
                              <th>Activity</th>
                              <th>Room</th>
                              <th>Paging</th>
                            </tr>
                          </thead>
                          <tbody>';
                          
                    // print each row
                    while ($row = pg_fetch_array($result)) {
                        echo '<tr>';
                        echo '<td>' . $row[6] . '</td>';
                        echo '<td><a href="details.php?id='.$row[0].'&query='.$inp.'">' . $row[1] . ' ' . $row[2] .'</a></td>';
                        echo '<td>' . $row[3] . '</td>';
                        echo '<td>' . $row[4] . '</td>';
                        echo '<td>' . $row[5] . '</td>';
                        echo '</tr>';
                    }
                              
                    echo '</tbody>
                        </table>';
                }        
                    
                ?>
                
                
            </div>
            
          </div>
			</div>
<?php require_once "template/footer.php" ; ?>
