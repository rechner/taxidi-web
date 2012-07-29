<!DOCTYPE html>
<!-- vim: tabstop=2:softtabstop=2 -->
<?php
	require_once "config.php";

	$page_title = "Advanced Search";
	require_once "template/header.php";
	
	function getvar($vname) {
		return (array_key_exists($vname, $_POST) ? $_POST[$vname] : $_GET[$vname]);
	}
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
										<input type="text" class="input" name="room" id="room" placeholder="Room" value="<?php echo getvar("room"); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="medical">and medical contains</label>
									<div class="controls">
										<input type="text" class="input" name="medical" id="medical" placeholder="Medical" value="<?php echo getvar("medical"); ?>">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="medical">and was born</label>
									<div class="controls">
										after <input type="text" class="input-small" name="date1" id="date1" value="<?php echo getvar("date1"); ?>">
										and before <input type="text" class="input-small" name="date2" id="date2" value="<?php $foo = getvar("date2"); echo (empty($foo) ? date("Y-m-d") : $foo)?>">
									</div>
								</div>
								<div class="form-actions">
								  <input type="submit" class="btn btn-primary" value="Search" />
								</div>
							</fieldset>
            </form>
						<?php
							if (!empty($_GET)) {
								$connection = pg_connect ("host=$dbhost dbname=$dbname 
                                          user=$dbuser password=$dbpass");
								$searchfilters = array( 
									"name"    => "data.name || ' ' || data.lastname ILIKE '%" . getvar("name") . "%'",
									"room"    => "rooms.name ILIKE '%" . getvar("room") . "%'",
									"medical" => "data.medical ILIKE '%" . getvar("medical") . "%'",
									"date1"   => "data.dob > '" . getvar("date1") . "'",
									"date2"   => "data.dob < '" . getvar("date2") . "'",
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
									$query .= " WHERE " . implode(" and ", $wherequery); 
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
					new JsDatePick({
						useMode:2,
						target:"date1",
						dateFormat:"%Y-%m-%d",
						imgPath:"resources/img/datepicker"
						/* weekStartDay:1*/
					});
				};
			</script>
<?php require_once "template/footer.php" ; ?>
