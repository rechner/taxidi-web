<?php
	// vim: tabstop=2:softtabstop=2
	
	if(!isset($_SERVER['HTTPS'])) {  //Force use of SSL
	    header("location: https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	}

	require_once "config.php";
	require_once "functions.php";
	
	// I18N support information here
	if (empty($locale))
		$language = 'en';
	if (isset($_GET['locale']) && !empty($_GET['locale']))
		$language = $_GET['locale'];
	
	putenv("LANG=$language"); 
	setlocale(LC_ALL, $language);
	
	// Set the text domain as 'login'
	$domain = 'login';
	bindtextdomain($domain, "locale"); 
	textdomain($domain);
	bind_textdomain_codeset($domain, 'UTF-8');
	
	session_start();
	
	$successpage = "index.php";
	if(!empty($_SESSION["userid"])) {
		header("Location: $successpage");
	} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
		if (!empty($_POST["username"]) && !empty($_POST["password"])) {
			$conn   = pg_connect ("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");
			$query  = "SELECT id, \"user\", hash, salt, name from users WHERE \"user\" = '{$_POST["username"]}';";
			$result = pg_query($conn, $query) or die("Error in query: $query." . pg_last_error($connection));
			$data   = pg_fetch_assoc($result);

			if ($data && hash("sha256", $_POST["password"] . $data["salt"]) == $data["hash"]) {
				$_SESSION["userid"]   = $data["id"];
				$_SESSION["username"] = $data[$data["name"] ? "name" : "user"];
				
				$_SESSION["salt"]        = hash("sha256", $_SERVER["REQUEST_TIME"]);
				$_SESSION["fingerprint"] = session_create_fingerprint();
				
				if (array_key_exists("redirectto",$_GET)) {
					header("Location: " . rawurldecode($_GET["redirectto"]));
				} elseif (array_key_exists("backto",$_SESSION)) {
					header("Location: " . $_SESSION["backto"]);
				} elseif (array_key_exists("HTTP_REFERER",$_SERVER)) {
					header("Location: " . rawurldecode($_SERVER["HTTP_REFERER"]));
				} else {
					header("Location: $successpage");
				}
			} else {
				$error = _("Incorrect username or password");
			}
		} else {
			$error = _("Form not completed");
		}
	}
	
?>
<!DOCTYPE html>
<html lang="<?php echo $language ?>">
  <head>
    <script src="bootstrap/js/jquery.js"></script>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title><?php echo _('Login'); ?> · Taxídí</title>
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
              <li class="active"><a href="login.php"><?php echo _('Login') ?></a></li>
            </ul>
	    <ul class="nav pull-right">
	      <li id="fat-menu" class="dropdown">
		<a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
		  <?php echo _('Language') ?>: <strong>
		    <?php if ($language=="en") echo "English (UK)";
			  if ($language=="de_DE") echo "Deutsch";
			  if ($language=="fr_FR.utf8") echo "français";
		    ?>
		  </strong><b class="caret"></b></a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
		  <li><a tabindex="-1" href="login.php?locale=en">English</a></li>
		  <li><a tabindex="-1" href="login.php?locale=de_DE">Deutsch</a></li>
		  <li><a tabindex="-1" href="login.php?locale=fr_FR.utf8">françias</a></li>
		  <li class="divider"></li>
		  <li><a tabindex="-1" href="#"><?php echo _('Translate this application') ?></a></li>
		</ul>
	      </li>
	    </ul>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal hide fade" id="banner" tabindex="-1" role="dialog" aria-labelledby="bannerLabel" aria-hidden="true" style="margin-top: -270px;">
      <div class="modal-header" style="background-color: #850505; border-bottom-color: #600000; color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="bannerLabel" style="margin: 0;">
	  <i class="icon-warning-sign icon-white" style="margin-top: 4px;">
	  </i> Warning / Achtung / Avertissement</h3>
      </div>
      <div class="modal-body" style="background-color: #800000; color: white;">
        <h2 style="text-align: center"><i class="icon-lock icon-white" style="margin-top: 6px;"></i> 
          <?php echo _('Private Computer System') ?> 
	  <i class="icon-lock icon-white" style="margin-top: 6px;"></i></h2><br>
        <p style="text-align: justify">
	  This   computer  system  including  all  related  equipment,   network  devices
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
        <button class="btn" data-dismiss="modal" aria-hidden="true">
	  <?php echo _('Continue'); ?> <i class="icon-arrow-right"></i>
	</button>
      </div>
    </div>
    
    <div class="row-fluid">
      <div class="span4"></div>
        <div class="span4">
	<?php echo $error ? "<div class=\"alert alert-error\">$error</div>": ""; ?>
        <form class="form-horizontal" method="POST">
	  <legend><strong><?php echo _('Login') ?></strong></legend>
          <div class="control-group">
            <label class="control-label" for="username"><?php echo _('Username') ?></label>
            <div class="controls">
              <input type="text" id="username" name="username"
		     placeholder="<?php echo _('Username') ?>" autofocus>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="password"><?php echo _('Password') ?></label>
            <div class="controls">
              <input type="password" id="password" name="password"
		     placeholder="<?php echo _('Password') ?>">
            </div>
          </div>
          <div class="control-group">
            <div class="controls form-inline">
	      <button type="submit" class="btn btn-primary"><?php echo _('Sign in') ?></button>
              <!--<label class="checkbox pull-right">
                <input type="checkbox"> Remember me
              </label>-->
            </div>
          </div>
        </form>
    
    <script>
      $(function(){
        <? echo $_SERVER['REQUEST_METHOD'] != "POST" ? "$('#banner').modal('show');" : ""; ?>
        $("#banner").on("hide", function() {$("#username").focus();});
      });
    </script>

					
<?php require_once "template/footer.php" ; ?>
