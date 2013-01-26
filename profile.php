<?php
  //internationalisation
  $domain = "profile";
  require_once 'locale.php';

  $page_title = "Profile Settings";
  require_once "template/header.php";
  
  require_once "config.php";
  require_once "functions.php";
  
  /**
   * This function generates a password salt as a string of x (default = 15) characters
   * in the a-zA-Z0-9!@#$%&*? range.
   * @param $max integer The number of characters in the string
   * @return string
   * @author AfroSoft <info@afrosoft.tk>
   */
  function generateSalt($max = 15) {
          $characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?";
          $i = 0;
          $salt = "";
          while ($i <= $max) {
              $salt .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
              $i++;
          }
          return $salt;
  }
?>
          <!-- sidebar -->
          <div class="span3">
            <div class="well sidebar-nav">
              <ul class="nav nav-list">
                <li class="nav-header"><?php echo _("Search"); ?></li>
                <li class="active"><a href="#"><i class="icon-search"></i><?php echo _("Search"); ?></a></li>
                <li><a href="#"><i class="icon-filter"></i><?php echo _("Advanced"); ?></a></li>
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
              if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $dbh = db_connect();
    
                // from login.php:33
                $sql  = "SELECT id, \"user\", hash, salt, name FROM users WHERE \"user\" = :username";
                $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                                    
                $sth->execute(array(":username" => $_SESSION["username"]));
                $data = $sth->fetch(PDO::FETCH_ASSOC);
                
                if ($data && hash("sha256", $_POST["oldpass"] . $data["salt"]) == $data["hash"]) {
                  // password was correct.
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
            <!-- password form-->
            <form class="well form-horizontal" name="search" action="profile.php" method="post">
              <fieldset>
                <legend>User Profile <small>Change Password</small></legend>
                <div class="control-group">
                  <label class="control-label" for="oldpass">Current Password</label>
                  <div class="controls">
                    <input type="password" id="oldpass" name="oldpass" placeholder="Current Password">
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
                    <input type="password" style="float:left;" 
                    id="confirmpass" class="password_confirm" placeholder="Confirm Password">
                  </div>
                </div>
              </fieldset>  
              <fieldset>
                <legend>Personal Settings</legend>
                <div class="control-group">
                  <label class="control-label" for="name">Display Name</label>
                  <div class="controls">
                    <input type="text" id="name" placeholder="Display Name">
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
            confirmpass: "top_badPass",
            goodPass:		"top_goodPass",
            baseStyle:   "top_testresult",
            password:    "#newpass",
            messageloc: 1
        });
      });
</script>
<?php require_once "template/footer.php" ; ?>
