<?php
  /* vim: tabstop=2:expandtab:softtabstop=2 */
  require_once 'config.php';

  $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
                              
  //internationalisation
  $domain = "details";
  require_once 'locale.php';
                              
  $register = FALSE; // placeholder to show success message

  if ($_SERVER['REQUEST_METHOD'] == "POST" and array_key_exists('activity', $_POST))  {
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
        <li class="nav-header"><?php echo _("Search"); ?></li>
        <li><a href="search.php"><i class="icon-search"></i><?php echo _("Search"); ?></a></li>
        <li><a href="#"><i class="icon-filter"></i><?php echo _("Advanced"); ?></a></li>
        <li><a href="#"><i class="icon-bookmark"></i><?php echo _("Saved Searches"); ?></a></li>
      </ul>
      <ul class="nav nav-list">
        <li class="nav-header"><?php echo _("Actions"); ?></li>
        <li class="active"><a href="#"><i class="icon-plus-sign"></i><?php echo _("Register"); ?></a></li>
        <li><a href="#"><i class="icon-user"></i><?php echo _("Register Visitor"); ?></a></li>
      </ul>
    </div>
  </div>
  <!-- /sidebar -->
  
  <div class="span9 well" style="overflow-x: auto;">
    <ul class="thumbnails">
      <li class="span6">
        <div class="page-header">
          <h1><?php echo _("Registration"); ?></h1>
        </div>
      </li>
    </ul>   
    
    <?php
      if ($register == TRUE) {
        echo "<div class=\"alert alert-success\">" .
          "<a class=\"close\" data-dismiss=\"alert\" href=\"#\">×</a>" .
          "<h4 class=\"alert-heading\">" . _("Registration Complete") . "</h4>" .
          sprintf(_("Successfully registered %s."),
            $_POST["name"] . " " . $_POST["lastname"]) .
          "</div>";
      }
    ?>
    
    <form class="form-horizontal" method="post" name="details">
      <fieldset>
        <div class="control-group">
          <label class="control-label" for="name"><?php echo _("Name"); ?></label>
          <div class="controls">
            <div class="container-fluid" style="padding: 0;">
              <div class="row-fluid">
                <input type="text" class="input span5" name="name" id="name" 
                    placeholder="<?php echo _("Name"); ?>" value="<?php echo $edata["name"]; ?>">
                <div style="display: inline-block;"> </div>
                <input type="text" class="input span5" name="lastname" id="lastname"
                    placeholder="<?php echo _("Lastname"); ?>" value="<?php echo $edata["lastname"]; ?>">
              </div>
            </div>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="phone"><?php echo _("Phone"); ?></label>
          <div class="controls">
            <input type="tel" class="input-medium" name="phone" id="phone" 
                onKeyDown="javascript:return dFilter (event.keyCode, this, '<?php echo _("(###) ###-####"); ?>');"
                placeholder="<?php echo _("Phone"); ?>" value="<?php echo $edata["phone"]; ?>">
            <label class="checkbox">
              <input type="checkbox" name="mobileCarrier" 
                <?php echo $edata["mobileCarrier"] ? "checked" : ""?>> <?php echo _("Mobile Phone"); ?>
            </label>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="grade"><?php echo _("Grade"); ?></label>
          <div class="controls">
            <div style="width: 220px; float: left; margin-right: 4px;">
              <input type="number" class="input-small" name="grade" id="grade"
                placeholder="<?php echo _("Grade"); ?>" value="<?php echo $edata["grade"]; ?>">
              <label for="dob" style="float: right; padding-top: 5px; margin-right: 16px;">
                <?php echo _("Birthdate"); ?></label>
            </div>
            <div>
              <input type="number" class="input-small" name="dob" id="dob"
                placeholder=<?php echo _("YYYY-MM-DD"); ?> 
                value="<?php echo $edata["dob"]; ?>"> <?php echo $agestr ?>
            </div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="activity">
              <?php echo _("Activity"); ?>
          </label>
          <div class="controls">
            <select name="activity" id="activity">
              <?php
                echo (is_null($edata["activity"]) ? "<option disabled selected>" .
                    _("Activity") . "</option>\n" : "");
                $query = "SELECT id, name FROM activities;";
                $result = pg_query($connection, $query) or
                    die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . 
                    ($data["id"] == $edata["activity"] ? " selected" : "") . 
                    ">{$data["name"]}</option>\n";
                }
                pg_free_result($result);
              ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="room"><?php echo _("Room"); ?></label>
          <div class="controls">
            <select name="room" id="room">
              <?php
                echo (is_null($edata["room"]) ? "<option disabled selected>" .
                    _("Room") . "</option>\n" : "");
                $query = "SELECT id, name FROM rooms;";
                $result = pg_query($connection, $query) or
                  die("Error in query: $query." . pg_last_error($connection));
                while ($data = pg_fetch_assoc($result)) {
                  echo "<option value=\"{$data["id"]}\"" . 
                    ($data["id"] == $edata["room"] ? " selected" : "") .
                    ">{$data["name"]}</option>\n";
                }
                pg_free_result($result);
              ?>
            </select>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="medical"><?php echo _("Medical"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="medical" id="medical"
                placeholder="<?php echo _("Medical"); ?>"
                value="<?php echo $edata["medical"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent1"><?php echo _("Parent 1"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent1" id="parent1"
              placeholder="<?php echo _("Parent 1"); ?>"
              value="<?php echo $edata["parent1"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent2"><?php echo _("Parent 2"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent2" id="parent2" 
              placeholder="<?php echo _("Parent 2"); ?>"
              value="<?php echo $edata["parent2"]; ?>">
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="parent_email"><?php echo _("Parent's Email"); ?></label>
          <div class="controls">
            <input type="text" class="input" name="parent_email" id="parent_email"
              placeholder="<?php echo _("Email"); ?>" 
              value="<?php echo $edata["parentEmail"]; ?>">
              <button class="btn" type="button" 
                onClick="parent.location='mailto:<?php echo $edata["parentEmail"]; ?>'">
              <i class="icon-envelope"></i></button>
          </div>
        </div>
        <div class="control-group form-inline">
          <label class="control-label" for="notes"><?php echo _("Notes"); ?></label>
          <div class="controls">
            <div class="container-fluid" style="padding: 0;">
              <div class="row-fluid">
                <textarea name="notes" id="notes" class="span12"
                  placeholder="<?php echo _("Notes"); ?>">
                  <?php echo $edata["notes"]; ?></textarea>
              </div>
            <div>
          </div>
        </div>
        <div class="form-actions">
          <input type="submit" class="btn btn-primary" value="<?php echo _("Create Record"); ?>" />
          <button class="btn" href="register.php"><?php echo _("Cancel"); ?></button>
        </div>
      </fieldset>
    </form>
  </div>
</div>

<script src="resources/js/dFilter.js"></script>

<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>


