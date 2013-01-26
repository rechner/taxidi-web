<!-- Be sure to call locale.php before header.php -->
<?php
  $old_domain = textdomain(NULL);
  bindtextdomain('header', "locale"); 
  textdomain('header');
  bind_textdomain_codeset('header', 'UTF-8');
  
  require_once "functions.php";
  session_assert_valid();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!--<script src="bootstrap/js/jquery.js"></script>-->
    <script src="resources/js/jquery.min.js"> </script>
    <script src="resources/js/jquery-migrate-1.0.0.min.js"> </script>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title><?php echo $page_title; ?> · Taxídí</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web interface for Taxidi check-in system">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      
      /* <!-- hide spin buttons in webkit browsers --> */
      input::-webkit-outer-spin-button,
      input::-webkit-inner-spin-button {
        /* display: none; <!-- Crashes Chrome on hover -->*/
        -webkit-appearance: none;
        margin: 0; /* <!-- Apparently some margin are still 
        there even though it's hidden --> */
      }
    </style>
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="resources/img/favicon.ico" />
    <!--<link rel="apple-touch-icon-precomposed" sizes="144x144" href="bootstrap/ico/apple-touch-icon-144-precomposed.png"> -->
    <!--<link rel="apple-touch-icon-precomposed" sizes="114x114" href="bootstrap/ico/apple-touch-icon-114-precomposed.png"> -->
    <!--<link rel="apple-touch-icon-precomposed" sizes="72x72" href="bootstrap/ico/apple-touch-icon-72-precomposed.png"> -->
    <link rel="apple-touch-icon-precomposed" href="resources/img/apple-touch-icon-57-optimized.png">
  </head>

  <body>

    <div class="navbar navbar-fixed-top navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="index.php">Taxídí</a>
            <div class="btn-group pull-right">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="icon-user"></i> <?php echo $_SESSION["usernick"]; ?>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="profile.php"><?php echo _('Profile') ?></a></li>
                <li class="divider"></li>
                <li><a href="logout.php"><?php echo _('Sign Out') ?></a></li>
              </ul>
            </div>
          <div class="nav-collapse">
            <ul class="nav">
              <li<?php echo (basename($_SERVER['PHP_SELF']) == "index.php" ? " class=\"active\"" : "");?>><a href="index.php"><?php echo _('Home') ?></a></li>
              <li<?php echo (basename($_SERVER['PHP_SELF']) == "search.php" ? " class=\"active\"" : "");?>><a href="search.php"><?php echo _('Search') ?></a></li>
              <li<?php echo (basename($_SERVER['PHP_SELF']) == "register.php" ? " class=\"active\"" : "");?>><a href="register.php"><?php echo _('Register') ?></a></li>
              <li<?php echo (basename($_SERVER['PHP_SELF']) == "stats.php" ? " class=\"active\"" : "");?>><a href="stats.php"><?php echo _('Statistics') ?></a></li>
              <li class="divider-vertical"></li>
            </ul>
            <form class="navbar-search pull-left" action="search.php" method="post">
                <input name="search" type="text" class="search-query input-medium"
                  placeholder="<?php echo _("Search"); ?>">
              </form>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <!-- sidebar -->
    <div>
      <div class="container-fluid">
        <div class="row-fluid">

<?php
  textdomain($old_domain);  //restore the old domain
?>
