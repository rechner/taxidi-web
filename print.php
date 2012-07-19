<!DOCTYPE html>
<?php
  //get input:
  if (is_numeric($id = $_GET["id"])) { 
    require_once 'config.php';
    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
                              
    ///*
    $query = "SELECT data.name, lastname, dob, activities.name as activity, rooms.name as room,
                     grade, phone, \"mobileCarrier\", paging, parent1, parent2,
                     \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                     \"lastModified\", count, visitor, expiry, \"noParentTag\",
                     barcode, picture, notes
                FROM data 
                LEFT JOIN activities ON data.activity=activities.id
                LEFT JOIN rooms ON data.room = rooms.id
                WHERE data.id = $id;";
    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    $edata = pg_fetch_assoc($result);
    
    if (!$edata) {
      header("HTTP/1.1 400 Bad Request");
      die("ID " . $id . " does not exist.");
    }
    
    $page_title = "{$edata["name"]} {$edata["lastname"]}";

    pg_free_result($result);
  
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
          Activity: <?php echo $edata["activity"]; ?><br/>
          Room: <?php echo $edata["room"]; ?><br/>
          </p>
          <p class="info" style="font-size: 12px">
              Created: <?php echo date("j M Y", strtotime($edata["joinDate"])) ?><br>
              Last Seen: <?php echo date("j M Y", strtotime($edata["lastSeen"])) ?><br>
              Modified: <?php echo date("j M Y H:i:s", strtotime($edata["lastModified"])) ?><br>
              Check-in Count: <?php echo $edata["count"] ?>
          </p>
      </td>
      <td valign="top">
          <p class="date"><?php echo date("r") ?></p>
          <img src="resources/logo.png" width=300 style="float:right; vertical-align:top">
      </td>
      </tr></table>
  
      <table class="field" width=800px><tr>
          <td width=90px>
              <p class="head"><b>Name</b></p>
          </td>
          <td width=287px><p class="info"><?php echo $edata["name"]?></p></td>
          <td width=120px><p class="head"><b>Last Name</b></p></td>
          <td width=287px><p class="info"><?php echo $edata["lastname"]?></p></td>
      </tr></table>
      <table class="field" width=800px height=50px><tr>
          <td width=90px>
              <p class="head"><b>Phone</b></p>
          </td>
          <td width=286px><p class="info"><?php echo $edata["phone"]?></p></td>
          <td>
              <img src="resources/check-<?php echo $edata["mobileCarrier"] ? "on" : "off"?>.png" id="checkbox">
              <p class="float">Mobile? </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=90px>
              <p class="head"><b>Grade</b></p>
          </td>
          <td width=40px><p class="head"><?php echo $edata["grade"]?></p></td>
          <td width=140px>
              <p class="head"><b>DOB</b> (Y/M/D)</p>
          </td>
          <td width=140px><p class="info"><?php echo date("Y/m/d", strtotime($edata["dob"]))?></p></td>
          <td width=80px>
              <p class="head"><b>Email</b></p>
          </td>
          <td><p class="info"><?php echo $edata["parentEmail"]?></p></td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b>Parent 1</b></p>
          </td>
          <td width=286px><p class="info"><?php echo $edata["parent1"]?></p></td>
          <td width=100px>
              <p class="head"><b>Parent 2</b></p>
          </td>
          <td><p class="info"><?php echo $edata["parent2"]?></p></td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=110px>
              <p class="head"><b>Medical &amp; Allergies</b></p>
          </td>
          <td width=276px><p class="info"><?php echo $edata["medical"]?></p></td>
          <td width=110px>
              <p style="text-align: center"><b>Special Instructions</b></p>
          </td>
          <td><p class="info"><?php echo $edata["notes"]?></p></td>
      </tr></table>
  
      <img src="resources/black.gif" width=800 height=3 style="margin-top: 10px">
      <p>Additional information and emergency contact (optional):</p>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b>Address</b></p>
          </td>
          <td width=676px><p class="info"> </p></td>
      </tr></table>
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b>City</b></p>
          </td>
          <td width=190px>
              <p class="info"> </p>
          </td>
          <td width=100px>
              <p class="head"><b>State</b></p>
          </td>
          <td width=100px>
              <p class="info"> </p>
          </td>
          <td width=100px>
              <p class="head"><b>ZIP</b></p>
          </td>
          <td width=170px>
              <p class="info"> </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b>Parent 1 Phone</b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info">Mobile: </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=100px>
              <p class="head"><b>Parent 2 Phone</b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info">Mobile: </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=120px>
              <p class="head"><b>Emergency Contact 1</b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info">Relationship: </p>
          </td>
      </tr></table>
  
      <table class="field" width=800px height=50px><tr>
          <td width=120px>
              <p class="head"><b>Emergency Contact 2</b></p>
          </td>
          <td width=286px><p class="info"> </p></td>
          <td>
              <p class="info">Relationship: </p>
          </td>
      </tr></table>
  </body>
</html>
