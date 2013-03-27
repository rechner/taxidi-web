<!DOCTYPE html>
<?php
  //get input:
  if (is_numeric($id = $_GET["id"])) { 
    require_once 'config.php';
    require_once 'functions.php';
    $dbh = db_connect();
                              
    $domain = "print";
    require_once 'locale.php';
                              
    ///*
    $sql = "SELECT data.name, lastname, dob, activities.name as activity, rooms.name as room,
                     grade, phone, \"mobileCarrier\", paging, parent1, parent2,
                     \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                     \"lastModified\", count, visitor, expiry, \"noParentTag\",
                     barcode, picture, notes
                FROM data 
                LEFT JOIN activities ON data.activity=activities.id
                LEFT JOIN rooms ON data.room = rooms.id
                WHERE data.id = :id;";
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(":id" => $id));
    $edata = $sth->fetch(PDO::FETCH_ASSOC);
    
    if (!$edata) {
      header("HTTP/1.1 400 Bad Request");
      die("ID " . $id . " does not exist.");
    }
    
    $page_title = "{$edata["name"]} {$edata["lastname"]}";
  
  } else {
    header("HTTP/1.1 400 Bad Request");
    die("Missing or malformed ID parameter");
  }
  
?>

<html>
  <head>
    <title><?php echo $edata["name"] . " " . $edata["lastname"]; ?></title>
		<script>
			function Print(){document.body.offsetHeight;window.print()};
		</script>
    <style type="text/css">
        p.date {
            text-align: right;
            font-family: sans;
            font-size: 12px;
        }
        p {
            font-family: sans;
            font-size: 16px;
        }
        p.info {
            margin-left: 10px;
        }
        p.float {
            text-align: right;
            display: inline-table;
        }
        p.head {
            text-align: center;
        }

        .circle {
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            margin-left: 10px;
            margin-top: 5px;
            margin-bottom: 0px;
            float: middle;

        }
        #circle1 {
            width: 20px;
            height: 20px;
            background: #EAEAEA;
            border: 4px solid #000000;
        }
        #circle2 {
            width: 5px;
            height: 5px;
            background: #000000;
            border: 4px solid #000000;
        }

        #checkbox {
            vertical-align: top;
            margin-top: 4px;
            margin-left: 5px;
            margin-right: 2px;
        }

        table.field {
            border-width: 1px;
            border-spacing: 0px;
            border-style: solid;
            border-color: white;
            border-collapse: separate;
            background-color: white;
        }
        table.field th {
            border-width: 1px;
            padding: 0px;
            border-style: solid;
            border-color: grey;
            background-color: white;
        }
        table.field td {
            border-width: 2px;
            padding: 0px;
            border-style: solid;
            border-color: black;
            background-color: white;
        }

        hr {
            color: #000000;
            background-color: #000000;
            height: 2px;
        }
    </style>
  </head>
  <body onload="Print()">
    <table width=800px><tr>
      <td width=200>
          <img src="photo.php?id=<?php echo $edata["picture"] ?>" width=200>
      </td>
      <td>
          <p class="info" style="font-size: 24px"><b><?php echo $edata["lastname"] ?>,<br/>
          <?php echo $edata["name"]?></b><br/></p><p class="info">
          <?php echo _("Activity") . ": " . $edata["activity"]; ?><br/>
          <?php echo _("Room") . ": " . $edata["room"]; ?><br/>
          </p>
          <p class="info" style="font-size: 12px">
              <?php echo sprintf(_("Created: %s"), strftime(_("%e %B %Y"), strtotime($edata["joinDate"]))) ?><br>
              <?php echo sprintf(_("Last Seen: %s"), strftime(_("%e %B %Y"), strtotime($edata["lastSeen"]))) ?><br>
              <?php echo sprintf(_("Modified: %s"), strftime(_("%e %b %Y %H:%M:%S"), strtotime($edata["lastModified"]))) ?><br>
              <?php echo sprintf(_("Check-in Count: %s"), $edata["count"]) ?>
          </p>
      </td>
      <td valign="top">
          <!-- keeping RFC date format for interoperability purposes-->
          <p class="date"><?php echo date("r") ?></p> 
          <img src="resources/logo.png" width=300 style="float:right; vertical-align:top">
      </td>
      </tr></table>
  
      <table class="field" width=800px><tr>
          <td width=90px>
              <p class="head"><b><?php echo _("Name"); ?></b></p>
          </td>
          <td width=287px><p class="info"><?php echo $edata["name"]?></p></td>
          <td width=120px><p class="head"><b><?php echo _("Last name"); ?></b></p></td>
          <td width=287px><p class="info"><?php echo $edata["lastname"]?></p></td>
      </tr></table>
      <table class="field" width=800px height=50px><tr>
          <td width=90px>
              <p class="head"><b><?php echo _("Phone"); ?></b></p>
          </td>
          <td width=286px><p class="info"><?php echo $edata["phone"]?></p></td>
          <td>
              <img src="resources/check-<?php echo $edata["mobileCarrier"] ? "on" : "off"?>.png" id="checkbox">
              <p class="float"><?php echo _("Mobile?"); ?> </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=90px>
              <p class="head"><b><?php echo _("Grade"); ?></b></p>
          </td>
          <td width=40px><p class="head"><?php echo $edata["grade"]?></p></td>
          <td width=140px>
              <p class="head"><b><?php echo _("DOB"); ?></b> <?php echo _("D/M/Y"); ?></p>
          </td>
          <td width=140px><p class="info"><?php echo strftime(_("%d/%m/%Y"), strtotime($edata["dob"]))?></p></td>
          <td width=80px>
              <p class="head"><b><?php echo _("Email"); ?></b></p>
          </td>
          <td><p class="info"><?php echo $edata["parentEmail"]?></p></td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b><?php echo _("Parent 1"); ?></b></p>
          </td>
          <td width=286px><p class="info"><?php echo $edata["parent1"]?></p></td>
          <td width=100px>
              <p class="head"><b><?php echo _("Parent 2"); ?></b></p>
          </td>
          <td><p class="info"><?php echo $edata["parent2"]?></p></td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=110px>
              <p class="head"><b><?php echo _("Medical &amp; Allergies"); ?></b></p>
          </td>
          <td width=276px><p class="info"><?php echo $edata["medical"]?></p></td>
          <td width=110px>
              <p style="text-align: center"><b><?php echo _("Special Instructions"); ?></b></p>
          </td>
          <td><p class="info"><?php echo $edata["notes"]?></p></td>
      </tr></table>
  
      <img src="resources/black.gif" width=800 height=3 style="margin-top: 10px">
      <p><?php echo _("Additional information and emergency contact (optional):"); ?></p>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b><?php echo _("Address"); ?></b></p>
          </td>
          <td width=676px><p class="info"> </p></td>
      </tr></table>
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b><?php echo _("City"); ?></b></p>
          </td>
          <td width=190px>
              <p class="info"> </p>
          </td>
          <td width=100px>
              <p class="head"><b><?php echo _("State"); ?></b></p>
          </td>
          <td width=100px>
              <p class="info"> </p>
          </td>
          <td width=100px>
              <p class="head"><b><?php echo _("ZIP"); ?></b></p>
          </td>
          <td width=170px>
              <p class="info"> </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b><?php echo _("Parent 1 Phone"); ?></b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info"><?php echo _("Mobile"); ?>: </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b><?php echo _("Parent 2 Phone"); ?></b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info"><?php echo _("Mobile"); ?>: </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=120px>
              <p class="head"><b><?php echo _("Emergency Contact 1"); ?></b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info"><?php echo _("Relationship"); ?>: </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=120px>
              <p class="head"><b><?php echo _("Emergency Contact 2"); ?></b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info"><?php echo _("Relationship"); ?>: </p>
          </td>
      </tr></table>
  </body>
</html>
