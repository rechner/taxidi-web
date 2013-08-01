<?php
  //internationalisation
  $domain = "profile";
  require_once 'locale.php';
  
  require_once "config.php";
  require_once "functions.php";

  $page_title = "User Management";
  require_once "template/header.php";
  
  $dbh = db_connect();
  $errorFlag = false; // let us know further down if there was a problem
  $successFlag = false;
  
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
          
    // Check if user exists
    if ($_POST["username"] != "") {
        $sql = "SELECT * FROM users WHERE \"user\" = :user";
        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(":user" => $_POST["username"]));
        
        if ($sth->rowCount() != 0) {
            $errorFlag = true;
            $errorMessage = "Username <strong>".$_POST['username']."</strong> already exists.";
        } else {
            // Check if passwords match
            if ($_POST["pass"] === $_POST["confirmpass"]) {
                // Everything's good, so add the row:
                $salt = generateSalt(40);
                $hash = hash("sha256", $_POST["pass"] . $salt);
                
                // INSERT query
                $sql = "INSERT INTO users(\"user\", hash, salt, name, " .
                    "admin, \"leftHanded\") VALUES (:user, :hash, " .
                    ":salt, :name, :admin, :hand);";
                $sth = $dbh->prepare($sql, 
                    array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                try {
                    $sth->execute(array(":user" => $_POST["username"],
                                    ":hash" => $hash,
                                    ":salt" => $salt,
                                    ":name" => $_POST["name"],
                                    ":admin" => ( isset($_POST["admin"]) ? 1 : 0 ),
                                    ":hand" => ( isset($_POST["lefthanded"]) ? 1 : 0)));
                } catch (PDOError $e) {
                    $errorFlag = true;
                    $errorMessage = $e->getMessage();
                    break;
                }
                
                $successFlag = true;
                                    
            } else {
                $errorFlag = true;
                $errorMessage = "Passwords do not match.";
            }
        }
    }  
    
  }

?>

  <!-- sidebar -->
  <div class="span3">
    <div class="well sidebar-nav">
      <ul class="nav nav-list">
        <li class="nav-header"><?php echo _("Actions"); ?></li>
        <li <?php echo ($_SESSION['useradmin'] >= true ? 'class="disabled"' : 'class="active"'); ?>
          ><a href="#"><i class="icon-plus-sign"></i><?php echo _("Add User"); ?></a></li>
        <li <?php echo ($_SESSION['useradmin'] >= true ? 'class="disabled"' : ''); ?>
          ><a href="#"><i class="icon-user"></i><?php echo _("Modify User"); ?></a></li>
        <li <?php echo ($_SESSION['useradmin'] >= true ? 'class="disabled"' : ''); ?>
          ><a href="#"><i class="icon-remove"></i><?php echo _("Delete User"); ?></a></li>
      </ul>
    </div>
  </div>
  <!-- /sidebar -->

  <!-- main content -->
  <div class="span9">
    <?php
      if (!$_SESSION['useradmin']) {
        echo  '<div class="well"><div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Incorrect permissions.</strong><br>
                Only administrators are allowed to modify, add, and remove users.
                If you believe this is an error, contact your system
                administrator.
              </div></div>';
      } else { 
          
        if ($errorFlag) 
           echo '<div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Unable to add user.</strong><br>'. $errorMessage .
                '</div>';
                
        if ($successFlag)
            echo '<div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Success.</strong><br>Sucessfully added the user <strong>' .
                $_POST["username"] . '</strong> to the system.
                </div>';
    ?>
    
        <form class="well form-horizontal" action="users.php" name="add" method="post">
          <fieldset>
            <legend>Add System User</legend>
            <div class="control-group">
              <label class="control-label" for="username">Username</label>
              <div class="controls">
                <input type="text" id="username" name="username" 
                 placeholder="Username" autocapitalize="off" autofocus required>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="pass">Password</label>
              <div class="controls">
                <input type="password" style="float:left;"
                id="pass" name="pass" class="password_check" placeholder="Password">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="confirmpass">Confirm Password</label>
              <div class="controls">
                <input type="password" style="float:left;" name="confirmpass"
                id="confirmpass" class="password_confirm" placeholder="Confirm Password">
              </div>
            </div>
            <!-- TODO: password generation -->
            <div class="control-group">
              <div class="controls">
                <a class="btn btn-info" id="passgen" onclick="passgen();">Generate Random Password</a>
              </div>
            </div>
            <hr>
            <div class="control-group">
              <label class="control-label" for="name">Display Name</label>
              <div class="controls">
                <input type="text" id="name" name="name" 
                 placeholder="Display Name" autocapitalize="on">
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
                  Left handed
                </label>
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <label class="checkbox">
                  <input type="checkbox" name="admin" id="admin">
                  Administrator
                </label>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Add User</button>
              <a class="btn" href="users.php">Cancel</a>
            </div>
          </fieldset>
        </form>
        <link rel="stylesheet" type="text/css" href="resources/css/password.css">
        <script src="bootstrap/js/jquery.js"></script>
        <script src="resources/js/passwordStrength.js"></script>
        <script>
              $(document).ready( function() {
        
                //BASIC
                $(".password_check").passStrength({
                    shortPass:    "top_shortPass",  //optional
                    badPass:    "top_badPass",    //optional
                    goodPass:   "top_goodPass",   //optional
                    strongPass:   "top_strongPass", //optional
                    baseStyle:    "top_testresult", //optional
                    username:     "#username",    //required override
                    messageloc:   1     //before == 0 or after == 1
                });
                
                $(".password_confirm").passConfirm({
                    confirmPass: "top_badPasslong",
                    goodPass:   "top_goodPass",
                    baseStyle:   "top_testresult",
                    password:    "#pass",
                    strongPass:   "top_strongPass",
                    messageloc: 1
                });
              });
              
              function passgen() {
                
                var randomstring = Math.random().toString(36).slice(-8).toUpperCase();
                
                alert("Copy this password into a safe place before clicking OK:\n\n" + randomstring);
                document.add.pass.value = randomstring;
                document.add.confirmpass.value = randomstring;
              }
        </script>
    <?php } ?>
  </div>
  
  
<?php require_once "template/footer.php" ; ?>
