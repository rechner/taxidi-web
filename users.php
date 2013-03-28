<?php
  //internationalisation
  $domain = "profile";
  require_once 'locale.php';
  
  require_once "config.php";
  require_once "functions.php";
  
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errorFlag = false; // let us know further down if there was a problem
    
  }

  $page_title = "User Management";
  require_once "template/header.php";
?>

  <!-- sidebar -->
  <div class="span3">
    <div class="well sidebar-nav">
      <ul class="nav nav-list">
        <li class="nav-header"><?php echo _("Actions"); ?></li>
        <li <?php echo ($SESSION['useradmin'] >= true ? 'class="disabled"' : 'class="active"'); ?>
          ><a href="#"><i class="icon-plus-sign"></i><?php echo _("Add User"); ?></a></li>
        <li <?php echo ($SESSION['useradmin'] >= true ? 'class="disabled"' : ''); ?>
          ><a href="#"><i class="icon-user"></i><?php echo _("Modify User"); ?></a></li>
        <li <?php echo ($SESSION['useradmin'] >= true ? 'class="disabled"' : ''); ?>
          ><a href="#"><i class="icon-remove"></i><?php echo _("Delete User"); ?></a></li>
      </ul>
    </div>
  </div>
  <!-- /sidebar -->

  <!-- main content -->
  <div class="span9">
    <?php
      if ($SESSION['useradmin']) {
        echo  '<div class="well"><div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Incorrect permissions.</strong><br>
                Only administrators are allowed to modify, add, and remove users.
                If you believe this is an error, contact your system
                administrator.
              </div></div>';
      } else { ?>
    
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
