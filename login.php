<?php
	// vim: tabstop=2:softtabstop=2
	require_once 'config.php';
  $connection = pg_connect ("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
	
	session_start();

	/* TODO
		* Make error messages better
		* Make sessions more secure
		* Encrpyt passwords over http
		* Make error box look better
	*/
	
	if(!empty($_SESSION["userid"])) {
		header("Location: index.php");
	} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
		if (!empty($_POST["username"]) && !empty($_POST["password"])) {
			$query  = "SELECT id, \"user\", hash, salt from users WHERE \"user\" = '{$_POST["username"]}';";
			$result = pg_query($connection, $query) or die("Error in query: $query." . pg_last_error($connection));
			$data   = pg_fetch_assoc($result);

			if ($data) {		
				if (hash("sha256", $_POST["password"] . $data["salt"]) == $data["hash"]) {
					$_SESSION["userid"] = $data["id"];
					header("Location: index.php");
				} else {
					$error = "password is incorrect";
				}
			} else {
				$error = "user does not exist";
			}
		} else {
			$error = "form not completed";
		}
	}
	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="bootstrap/js/jquery.js"></script>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>Login · Taxídí</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web interface for Taxidi check-in system">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
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

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="index.php">Taxídí</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="login.php">Login</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal hide fade" id="banner" tabindex="-1" role="dialog" aria-labelledby="bannerLabel" aria-hidden="true">
      <div class="modal-header" style="background-color: #850505; border-bottom-color: #600000; color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="bannerLabel"><i class="icon-warning-sign icon-white" style="margin-top: 4px;"></i> Warning / Achtung / Avertissement</h3>
      </div>
      <div class="modal-body" style="background-color: #800000; color: white;">
        <h2 style="text-align: center"><i class="icon-lock icon-white" style="margin-top: 6px;"></i> 
          Private Computer System <i class="icon-lock icon-white" style="margin-top: 6px;"></i></h2><br>
        <p style="text-align: justify">This   computer  system  including  all  related  equipment,   network  devices
          (specifically including Internet access), are provided only for authorized use.
          All computer systems  may be monitored  for all  lawful purposes,  including to
          ensure  that  their  use  is  authorized,  for management  of  the  system,  to 
          facilitate  protection  against  unauthorized  access,  and  to verify security
          procedures, survivability and operational security.  Monitoring includes active
          attacks  by  authorized  personnel  and  their  entities  to test or verify the
          security  of  the  system.  During  monitoring,  information  may  be examined,
          recorded, copied and used for authorized purposes.  All  information  including 
          personal information, placed on or sent over this system may be monitored. Uses
          of this system,  authorized  or unauthorized, constitutes consent to monitoring
          of  this  system.  Unauthorized  use may  subject you  to criminal prosecution.
          Evidence of any  such unauthorized  use collected during monitoring may be used
          for  administrative,  criminal  or other  adverse  action.</p>
        <p><b>Use of this system constitutes consent to monitoring for these purposes.<br></b>
        <i>Die Benutzung diesem System setzt ein Zustimmung zur Überwachung.</i><br>
        L'utilisation de ce système constitue un consentement de ce contrôle.</p>
        </div>
      <div class="modal-footer" style="background-color: #850505; color: white; border-top-color: #600000; box-shadow: 0 1px 0 #700000 inset;">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Continue <i class="icon-arrow-right"></i></button>
      </div>
    </div>
    
    <div class="row-fluid">
      <div class="span4"></div>
      <div class="span4">
				<?php echo $error ? "<div class=\"alert\">$error</div>": ""; ?>
        <form class="form-horizontal" method="POST">
          <div class="control-group">
            <label class="control-label" for="username">Username</label>
            <div class="controls">
              <input type="text" id="username" name="username" placeholder="Username" autofocus>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="password">Password</label>
            <div class="controls">
              <input type="password" id="password" name="password" placeholder="Password">
            </div>
          </div>
          <div class="control-group">
            <div class="controls">
              <label class="checkbox">
                <input type="checkbox"> Remember me
              </label>
              <button type="submit" class="btn">Sign in</button>
            </div>
          </div>
        </form>
        <div class="span4"></div>
      </div>
    </div>
    <script>
      $(function(){
        <? echo $_SERVER['REQUEST_METHOD'] != "POST" ? "$('#banner').modal('show');" : ""; ?>
        $("#banner").on("hide", function() {$("#username").focus();});
      });
    </script>

					
<?php require_once "template/footer.php" ; ?>
