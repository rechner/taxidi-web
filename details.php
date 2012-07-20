<!DOCTYPE html>
<!-- vim: tabstop=2:softtabstop=2 -->
<?php
/* vim: tabstop=2:expandtab:softtabstop=2 */
  /* TODO list
		* Successful update notification?
    * Phone mask
    * DOB mask
    * Proper error message for bad id
    *
    * Changes: Added a few info displays, removed explicit script reference
  */
  
  //get input:
  if (is_numeric($id = $_GET["id"])) { 
    require_once 'config.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
      
      $query = "UPDATE data SET " .
                    "   name = '"              . $_POST["name"]         .
                    "', lastname = '"          . $_POST["lastname"]     .
                    "', phone = '"             . $_POST["phone"]        . 
                    "', \"mobileCarrier\" = '" . (isset($_POST["mobileCarrier"]) ? "1" : "0") . 
                    "', grade = '"             . $_POST["grade"]        .
                    "', dob = '"               . $_POST["dob"]          . 
                    "', activity = '"          . (is_numeric($_POST["activity"]) ? $_POST["activity"] : 0 ) . 
                    "', room = '"              . (is_numeric($_POST["room"]) ? $_POST["room"] : 0 )         . 
                    "', medical = '"           . $_POST["medical"]      . 
                    "', parent1 = '"           . $_POST["parent1"]      . 
                    "', parent2 = '"           . $_POST["parent2"]      . 
                    "', \"parentEmail\" = '"   . $_POST["parent_email"] . 
                    "', notes = '"             . $_POST["notes"]        . 
                    "', \"lastModified\" = '"  . date("Y-m-d H:i:s.u", $_SERVER["REQUEST_TIME"]) . 
                  "' WHERE id = $id;";
      
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      pg_free_result($result);
			$modifysuccess = true;
    }
                              
    ///*
    $query = "SELECT name, lastname, dob, activity, room, grade, phone, 
                     \"mobileCarrier\", paging, parent1, parent2,
                     \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                     \"lastModified\", count, visitor, expiry, \"noParentTag\",
                     barcode, picture, notes
                FROM data WHERE id = $id;";
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $edata = pg_fetch_assoc($result);
    
    if (!$edata) {
      header("HTTP/1.1 400 Bad Request");
      die("ID " . $id . " does not exist.");
    }
    
    $page_title = "{$edata["name"]} {$edata["lastname"]}";
    
    // The parent search term, if applicable:
    if (array_key_exists('query', $_GET)) {
      $search = $_GET['query'];
    } else {
      $search = '';
    }
    
    $age = date_diff(new DateTime($edata["dob"]), new DateTime("now"));
    if ($age->y >= 1) {
      $agestr = $age->format("Age: %y");
    } else {
      $agestr = $age->format("Age: %m months");
    }

    pg_free_result($result);
  
  } else {
    header("HTTP/1.1 400 Bad Request");
    die("Missing or malformed ID parameter");
  }

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
	    <li><a href="print.php?id=<?php echo $_GET["id"]; ?>" target="_blank"><i class="icon-print"></i>Print details</a></li>
	    <li><a data-toggle="modal" href="#downloadModal"><i class="icon-download"></i>Download details</a></li>
	    <li><a href="#"><i class="icon-bullhorn"></i>Create incident report</a></li>
	    <li><a data-toggle="modal" href="#deleteModal"><i class="icon-trash"></i>Delete record</a></li>
	  </ul>
	</div>
</div>
<!-- /sidebar -->

<div id="downloadModal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Download Record Data</h3>
	</div>
	<div class="modal-body">
		<h4>Download format</h4>
		<form class="form-horizontal">
		  <fieldset>	
		    <div class="control-group">
		      <label class="control-label" for="inlineCheckboxes">Select Format:</label>
		      <div class="controls">
		        <label class="radio inline">
		          <input type="radio" name="format" id="inlineCheckbox1" value="option1" checked> csv
		        </label>
		        <label class="radio inline">
		          <input type="radio" name="format" id="inlineCheckbox2" value="option2"> xml
		        </label>
		        <label class="radio inline">
		          <input type="radio" name="format" id="inlineCheckbox3" value="option3"> yaml
		        </label>
		      </div>        
		    </div>
		  </fieldset>
		</form>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal" >Close</a>
		<a href="#" class="btn btn-primary">Download</a>
	</div>
</div>

<div id="deleteModal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Delete Record</h3>
	</div>
	<div class="modal-body">
		<h4>Are you sure you want to delete this record?</h4>
		<p>This action cannot be undone.</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal" >Close</a>
		<a href="delete.php?id=<?php 
					  if ($query != '') {
					    echo $id . "&query=" . $search;
					  } else {
					    echo $id;
					  } ?>" class="btn btn-danger">Delete</a>
	</div>
</div>

<?php
	if ($modifysuccess) {
		echo "<div class=\"span9 well\" style=\"overflow-x: auto;\">
						<div class=\"form-horizontal\">
							<fieldset>
								<div class=\"control-group\">
								  <div class=\"controls\" style=\"font-weight: bold; font-size: 110%;\">
										Changes successfully saved.
									</div>
								</div>
							</fieldset>
						</div>
					</div>";
	}
?>

<div class="span9 well" style="overflow-x: auto;">
	<ul class="thumbnails">
		<li class="span3">
		  <a href="#" class="thumbnail">
		    <!--<img src="http://placehold.it/480x480" alt="">-->
		     <img src="photo.php<?php echo "?id=" . $edata["picture"] ?>"/>
		  </a>
		</li>
		<li class="span6">
		  <div class="page-header">
		    <h1><?php echo "{$edata["name"]}</h1> <h2>{$edata["lastname"]}"; ?></h2>
		    <?php echo ($edata["visitor"] == "f" ? "Member" : "Visitor") . "<br>"; 
		          echo ($edata["visitor"] == "f" ? "" : "Expiry: " . $edata["expiry"]) . "<br>";
		          echo "Created: " . date("j M Y", strtotime($edata["joinDate"])) . "<br>";
		          echo "Last Seen: " . date("j M Y", strtotime($edata["lastSeen"])) . "<br>";
		          echo "Modified: " . date("j M Y H:i:s", strtotime($edata["lastModified"])) . "<br>";
		          echo "Count: " . $edata["count"];
		    ?>
		  </div>
		</li>
	</ul>   
    <form class="form-horizontal" action="" method="post">
      <fieldset>
        <div class="control-group">
          <label class="control-label" for="name">Name</label>
          <div class="controls">
            <input type="text" class="input" name="name" id="name" placeholder="Name" value="<?php echo $edata["name"]; ?>">
            <input type="text" class="input" name="lastname" id="lastname" placeholder="Lastname" value="<?php echo $edata["lastname"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="phone">Phone</label>
          <div class="controls">
            <input type="tel" class="input-medium" name="phone" id="phone" placeholder="Phone" value="<?php echo $edata["phone"]; ?>">
            <label class="checkbox">
              <input type="checkbox" name="mobileCarrier" <?php echo $edata["mobileCarrier"] ? "checked" : ""?>> Mobile phone
            </label>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="grade">Grade</label>
          <div class="controls">
            <div style="width: 220px; float: left; margin-right: 4px;">
              <input type="text" class="input-small" name="grade" id="grade" placeholder="Grade" value="<?php echo $edata["grade"]; ?>">
              <label for="dob" style="float: right; padding-top: 5px; margin-right: 16px;">Birthdate</label>
            </div>
            <div>
              <input type="text" class="input-small" name="dob" id="dob" placeholder="YYYY-MM-DD" value="<?php echo $edata["dob"]; ?>"> <?php echo $agestr ?>
            </div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="activity">Activity</label>
          <div class="controls">
            <select name="activity" id="activity">
              <?php
                echo (is_null($edata["activity"]) ? "<option disabled selected>Activity</option>\n" : "");
                $query = "SELECT id, name FROM activities;";
                $result = pg_query($connection, $query) or
                    die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . ($data["id"] == $edata["activity"] ? " selected" : "") . ">{$data["name"]}</option>\n";
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
              <?php
                echo (is_null($edata["room"]) ? "<option disabled selected>Room</option>\n" : "");
                $query = "SELECT id, name FROM rooms;";
                $result = pg_query($connection, $query) or
                  die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . ($data["id"] == $edata["room"] ? " selected" : "") . ">{$data["name"]}</option>\n";
                }
                pg_free_result($result);
              ?>
            </select>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name">Medical Info</label>
          <div class="controls">
            <input type="text" class="input" name="medical" id="medical" placeholder="Medical" value="<?php echo $edata["medical"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name">Parent 1</label>
          <div class="controls">
            <input type="text" class="input" name="parent1" id="parent1" placeholder="Name" value="<?php echo $edata["parent1"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p2_name">Parent 2</label>
          <div class="controls">
            <input type="text" class="input" name="parent2" id="parent2" placeholder="Name" value="<?php echo $edata["parent2"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="p1_name">Parent's Email</label>
          <div class="controls">
            <input type="text" class="input" name="parent_email" id="parent_email" placeholder="Email" 
              value="<?php echo $edata["parentEmail"]; ?>">
              <button class="btn" type="button" onClick="parent.location='mailto:<?php echo $edata["parentEmail"]; ?>'">
              <i class="icon-envelope"></i></button>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="notes">Notes</label>
          <div class="controls">
            <textarea name="notes" id="notes" placeholder="Notes" style="width: 434px;"><?php echo $edata["notes"]; ?></textarea>
          </div>
        </div>
        <div class="form-actions">
          <input type="submit" class="btn btn-primary" value="Save changes" />
          <button class="btn">Cancel</button>
        </div>
      </fieldset>
    </form>
  </div>
</div>
<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>
