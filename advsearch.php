<!DOCTYPE html>
<!-- vim: tabstop=2:softtabstop=2 -->
<?php
	require_once "config.php";

	$page_title = "Advanced Search";
	require_once "template/header.php";
	
	function getvar($vname) {
		return (array_key_exists($vname, $_POST) ? $_POST[$vname] : $_GET[$vname]);
	}
	
	$connection = pg_connect ("host=$dbhost dbname=$dbname 
                            user=$dbuser password=$dbpass");
?>
					<!-- sidebar -->
          <div class="span3">
            <div class="well sidebar-nav">
              <ul class="nav nav-list">
                <li class="nav-header">Search</li>
                <li><a href="#"><i class="icon-search"></i>Search</a></li>
                <li class="active"><a href="#"><i class="icon-filter"></i>Advanced</a></li>
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
            <form class="well form-horizontal" method="get">
							<fieldset>
								<div class="control-group">
									<label class="control-label" for="name">Name contains</label>
									<div class="controls">
										<input type="text" class="input" name="name" id="name" placeholder="Name" value="<?php echo getvar("name"); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="room">and room is</label>
									<div class="controls">
										<select name="room" id="room">
											<option value="" selected>Any Room</option>
								      <?php
												$rooms = array();
								        $query = "SELECT id, name FROM rooms;";
								        $result = pg_query($connection, $query) or
								          die("Error in query: $query." . pg_last_error($connection));
								        while ($data = pg_fetch_assoc($result)) {
								          echo "<option value=\"{$data["id"]}\"" . ($data["id"] == getvar("room") ? " selected" : "") . ">{$data["name"]}</option>\n";
													$rooms[$data["id"]] = $data["name"];
								        }
								        pg_free_result($result);
								      ?>
								    </select>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="room">and activity is</label>
									<div class="controls">
										<select name="activity" id="activity">
											<option value="" selected>Any Activity</option>
								      <?php
												$activities = array();
								        $query = "SELECT id, name FROM activities;";
								        $result = pg_query($connection, $query) or
								          die("Error in query: $query." . pg_last_error($connection));
								        while ($data = pg_fetch_assoc($result)) {
								          echo "<option value=\"{$data["id"]}\"" . ($data["id"] == getvar("room") ? " selected" : "") . ">{$data["name"]}</option>\n";
													$activities[$data["id"]] = $data["name"];
								        }
								        pg_free_result($result);
								      ?>
								    </select>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="medical">and was born</label>
									<div class="controls">
										after <input type="text" class="input-small" name="date1" id="date1" value="<?php echo getvar("date1"); ?>">
										and before <input type="text" class="input-small" name="date2" id="date2" value="<?php echo getvar("date2"); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="medical">and joined</label>
									<div class="controls">
										after <input type="text" class="input-small" name="date3" id="date3" value="<?php echo getvar("date3"); ?>">
										and before <input type="text" class="input-small" name="date4" id="date4" value="<?php echo getvar("date4"); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="medical">and last seen</label>
									<div class="controls">
										after <input type="text" class="input-small" name="date5" id="date5" value="<?php echo getvar("date5"); ?>">
										and before <input type="text" class="input-small" name="date6" id="date6" value="<?php echo getvar("date6"); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="medical">Invert query</label>
									<div class="controls">
										<input type="checkbox" name="invert" id="invert" <?php echo (getvar("invert") == "on" ? "checked" : ""); ?>>
									</div>
								</div>
								<div class="form-actions">
								  <input type="submit" class="btn btn-primary" value="Search" />
									<input type="reset" class="btn" value="Reset">
								</div>
							</fieldset>
            </form>
						<?php
							if (!empty($_GET)) {
								$searchfilters = array( 
									"name"     => "data.name || ' ' || data.lastname ILIKE '%" . getvar("name") . "%'",
									"room"     => "data.room = " . getvar("room"),
									"activity" => "data.activity = " . getvar("activity"),
									"date1"    => "data.dob > '" . getvar("date1") . "'",
									"date2"    => "data.dob < '" . getvar("date2") . "'",
									"date3"    => "data.\"joinDate\" > '" . getvar("date3") . "'",
									"date4"    => "data.\"joinDate\" < '" . getvar("date4") . "'",
									"date5"    => "data.\"lastSeen\" > '" . getvar("date5") . "'",
									"date6"    => "data.\"lastSeen\" < '" . getvar("date6") . "'",
								);
								$wherequery = array();
								foreach ($searchfilters as $n => $q) {
									$v = getvar($n);
									if (!empty($v)) {
										$wherequery[] = $q;
									}
								}
                $query = "SELECT DISTINCT data.id, data.name, lastname, activities.name AS aname, rooms.name AS rname, paging
                          FROM data
													LEFT JOIN rooms ON data.room = rooms.id
													LEFT JOIN activities ON data.activity = activities.id";
								if (count($wherequery) > 0) {
									$query .= " WHERE " . (getvar("invert") == "on" ? " NOT " : "") . "(" . implode(" and ", $wherequery) . ")"; 
								}
								$query .= ";";
								$result = pg_query($connection, $query) or 
                  die("Error in query: $query." . pg_last_error($connection));
								
								if (pg_num_rows($result) == 0) {
                  echo '<div class="alert alert-error">
		                <a class="close" data-dismiss="alert" href="#">×</a>
		                <h4 class=\"alert-heading\">No results.</h4>
		                </div>';
                } else {
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
									while ($row = pg_fetch_assoc($result)) {
                    echo '<tr><td style="width:30px"><input type="checkbox" name="foo"></td>';
                    echo "<td><a href=\"details.php?id={$row["id"]}\">{$row["name"]} {$row["lastname"]}</a></td>";
                    echo '<td>' . $row["aname"] . '</td>';
                    echo '<td>' . $row["rname"] . '</td>';
                    echo '<td>' . $row["paging"] . '</td>';
                    echo '</tr>';
                  }
                  echo '</tbody></table>';
								}
							}
						?>
          </div>
			</div>
			<script>
				window.onload = function(){
					for(i = 1; i <= 6; i++) {
						new JsDatePick({
							useMode:2,
							target:("date" + i),
							dateFormat:"%Y-%m-%d",
							imgPath:"resources/img/datepicker"
							/* weekStartDay:1*/
						});
					}
				};
			</script>
<?php require_once "template/footer.php" ; ?>
