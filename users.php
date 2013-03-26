<?php
  //internationalisation
  $domain = "profile";
  require_once 'locale.php';
  
  require_once "config.php";
  require_once "functions.php";
  
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    session_start();
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
        <li class="active"><a href="#"><i class="icon-plus-sign"></i><?php echo _("Add User"); ?></a></li>
        <li><a href="#"><i class="icon-user"></i><?php echo _("Modify User"); ?></a></li>
        <li><a href="#"><i class="icon-remove"></i><?php echo _("Delete User"); ?></a></li>
      </ul>
    </div>
  </div>
  <!-- /sidebar -->

  <!-- main content -->
  <div class="span9">
  </div>
  
  
<?php require_once "template/footer.php" ; ?>
