<!DOCTYPE html>
<?php
  /* vim: tabstop=2:expandtab:softtabstop=2 */
  require_once 'config.php';

  $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
                              
  $register = FALSE; // placeholder to show success message

  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    $result = pg_query($connection, "SELECT prefix FROM activities WHERE id = '".$_POST["activity"]."';") or
        die("Error in query: $query." . pg_last_error($connection));
    
    $prefix = pg_fetch_result($result, 0, 0);
    $paging = $prefix . "-" . substr($_POST["phone"], -4);
    
    $query = "INSERT INTO data " .
                "(name, lastname, phone, \"mobileCarrier\", paging, grade, " .
                "dob, activity, room, medical, parent1, parent2, "   .
                "\"parentEmail\", notes, visitor, \"joinDate\", "    .
                "\"lastSeen\", \"lastModified\", count) " .                  
              " VALUES (" .
                "'"    . $_POST["name"]         .
                "', '" . $_POST["lastname"]     .
                "', '" . $_POST["phone"]        . 
                "', '" . (isset($_POST["mobileCarrier"]) ? "1" : "0") . 
                "', '" . $paging .
                "', '" . $_POST["grade"]        .
                "', '" . $_POST["dob"]          . 
                "', '" . (is_numeric($_POST["activity"]) ? $_POST["activity"] : 0 ) . 
                "', '" . (is_numeric($_POST["room"]) ? $_POST["room"] : 0 )         . 
                "', '" . $_POST["medical"]      . 
                "', '" . $_POST["parent1"]      . 
                "', '" . $_POST["parent2"]      . 
                "', '" . $_POST["parent_email"] . 
                "', '" . $_POST["notes"]        . 
                "', 'f" .
                "', '" . date("Y-m-d", $_SERVER["REQUEST_TIME"]) .
                "', '" . date("Y-m-d", $_SERVER["REQUEST_TIME"]) .
                "', '" . date("Y-m-d H:i:s.u", $_SERVER["REQUEST_TIME"]) . 
              "', '0');";
          
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    pg_free_result($result);
    
    $register = TRUE;
  }
  
  $page_title = "Register";
  
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
      </ul>
      <ul class="nav nav-list">
        <li class="nav-header">Actions</li>
        <li class="active"><a href="#"><i class="icon-plus-sign"></i>Register</a></li>
        <li><a href="#"><i class="icon-user"></i>Register Visitor</a></li>
    </div>
  </div>
  <!-- /sidebar -->
  
  <div class="span9 well" style="overflow-x: auto;">
    <ul class="thumbnails">
      <li class="span6">
        <div class="page-header">
          <h1>Registration</h2>
        </div>
      </li>
    </ul>   
    
    <?php
      if ($register == TRUE) {
        echo "<div class=\"alert alert-success\">" .
          "<a class=\"close\" data-dismiss=\"alert\" href=\"#\">×</a>" .
          "<h4 class=\"alert-heading\">Registration Complete</h4>" .
          "Successfully registered " . 
          $_POST["name"] . " " . $_POST["lastname"] . "." .
          "</div>";
      }
    ?>
    
    <form class="form-horizontal" action="" method="post" name="details">
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
          <label class="control-label" for="p1_name">Medical</label>
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
          <input type="submit" class="btn btn-primary" value="Create Record" />
          <button class="btn" href="register.php">Cancel</button>
        </div>
      </fieldset>
    </form>
  </div>


<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>

