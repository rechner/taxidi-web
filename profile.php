<?php
  //internationalisation
  $domain = "profile";
  require_once 'locale.php';
  
  require_once "config.php";
  require_once "functions.php";
  
              if ($_SERVER['REQUEST_METHOD'] == "POST") {
                session_start();
                $errorFlag = false; // let us know further down if there was a problem
                $passwordcorrect = false;
                $dbh = db_connect();
    
                // from login.php:33
                $sql  = "SELECT id, \"user\", hash, salt, name FROM users WHERE \"user\" = :username";
                $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                                    
                $sth->execute(array(":username" => $_SESSION["username"]));
                $data = $sth->fetch(PDO::FETCH_ASSOC);
                
                if ($data && hash("sha256", $_POST["oldpass"] . $data["salt"]) == $data["hash"]) {
                  // password was correct.
                  if ($_POST["newpass"] != "" && $_POST["newpass"] == $_POST["confirmpass"])  {
                    // update password
                    $salt = generateSalt(40);
                    $hash = hash("sha256", $_POST["newpass"] . $salt);
                    $sql = "UPDATE users SET (hash, salt, name) = (:hash, :salt, :name) WHERE id = :id";
                    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    
                    $sth->execute(array(":id" => $_SESSION["userid"],
                                        ":hash" => $hash,
                                        ":salt" => $salt,
                                        ":name" => $_POST["name"]));
                    $data = $sth->fetch(PDO::FETCH_ASSOC);
                  } else if ($_POST["newpass"] != "" && $_POST["newpass"] != $_POST["confirmpass"]) {
                    // new passwords do not match
                    echo '<div class="alert alert-error">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <strong>New passwords do not match.</strong> Please try again.
                        </div>';
                    $errorFlag = true;
                  } else {
                    // just update settings
                    $sql = "UPDATE users SET name = :name WHERE id = :id";
                    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    
                    $sth->execute(array(":id" => $_SESSION["userid"],
                                        ":name" => $_POST["name"]));
                    
                  }
                  
                  //update the session
                  $_SESSION["usernick"] = $_POST["name"];  
                  
                  if (!$errorFlag)
                    $passwordcorrect = true;
                    
                }
                  
              }

  $page_title = "Profile Settings";
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
              </ul>
            </div>
          </div>
          <!-- /sidebar -->

          <div class="span9">
            <!-- password form-->
            <?php
              if ($_SERVER['REQUEST_METHOD'] == "POST") {
                if ($passwordcorrect) {
                  echo '<div class="alert alert-success">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <strong>Password Correct.</strong> Changes committed successfully.
                    </div>';
                } else {
                  echo '<div class="alert alert-error">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <strong>Password Incorrect.</strong> Please try again.
                    </div>';
                }
              }
            ?>
            <form class="well form-horizontal" name="search" action="profile.php" method="post">
              <fieldset>
                <legend>User Profile <small>Change Password</small></legend>
                <div class="control-group">
                  <label class="control-label" for="oldpass">Current Password</label>
                  <div class="controls">
                    <input type="password" id="oldpass" name="oldpass" 
                     placeholder="Current Password" required>
                  </div>
                </div>
                <hr>
                <div class="control-group">
                  <label class="control-label" for="newpass">New Password</label>
                  <div class="controls">
                    <input type="password" style="float:left;" 
                    id="newpass" name="newpass" class="password_check" placeholder="New Password">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="confirmpass">Confirm Password</label>
                  <div class="controls">
                    <input type="password" style="float:left;" name="confirmpass"
                    id="confirmpass" class="password_confirm" placeholder="Confirm Password">
                  </div>
                </div>
              </fieldset>  
              <fieldset>
                <legend>Personal Settings</legend>
                <div class="control-group">
                  <label class="control-label" for="name">Display Name</label>
                  <div class="controls">
                    <input type="text" id="name" name="name" 
                     value="<?php echo $_SESSION["usernick"]; ?>"
                     placeholder="Display Name">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="name">Volunteer Link</label>
                  <div class="controls">
                    <input type="text" id="name" class="input-small"
                     placeholder="Reference" disabled>
                    <button class="btn" name="test" disabled>Test</button>
                  </div>
                </div>
                <div class="control-group">
                  <div class="controls">
                    <label class="checkbox">
                      <input type="checkbox" name="lefthanded" id="lefthanded">
                      I am left handed, and prefer the keypad on the left
                    </label>
                  </div>
                </div>
                <div class="form-actions">
                  <button type="submit" class="btn btn-primary">Save changes</button>
                  <button type="button" class="btn">Cancel</button>
                </div>
              </fieldset>
            </form>
            
          </div>
      </div>

<link rel="stylesheet" type="text/css" href="resources/css/password.css">
<script src="bootstrap/js/jquery.js"></script>
<script src="resources/js/passwordStrength.js"></script>
<script>
			$(document).ready( function() {

				//BASIC
				$(".password_check").passStrength({
            shortPass: 		"top_shortPass",	//optional
            badPass:		"top_badPass",		//optional
            goodPass:		"top_goodPass",		//optional
            strongPass:		"top_strongPass",	//optional
            baseStyle:		"top_testresult",	//optional
            username:			"#oldpass",		//required override
            messageloc:		1			//before == 0 or after == 1
        });
        
        $(".password_confirm").passConfirm({
            confirmPass: "top_badPasslong",
            goodPass:		"top_goodPass",
            baseStyle:   "top_testresult",
            password:    "#newpass",
            strongPass:		"top_strongPass",
            messageloc: 1
        });
      });
</script>
<?php require_once "template/footer.php" ; ?>
