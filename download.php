<?php
  
  if (is_numeric($id = $_GET["id"]) && array_key_exists("format", $_GET)) { 
      require_once 'config.php';

      $connection = pg_connect ("host=$dbhost dbname=$dbname 
                                user=$dbuser password=$dbpass");
      $query = "SELECT name, lastname, dob, activity, room, grade, phone, 
                       \"mobileCarrier\", paging, parent1, parent2,
                       \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                       \"lastModified\", count, visitor, expiry, \"noParentTag\",
                       barcode, picture, notes
                  FROM data WHERE id = $id;";
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      $edata = pg_fetch_assoc($result);
		
      if (!$edata) {
        header("HTTP/1.0 400 Bad Request");
        die("ID " . $id . " does not exist.");
      }
      
      // basic demonstration for download formats
      // each format generator could go into it's own file, or at very least it's own function in this file
      if ($_GET["format"] == "yaml") {
        header("Content-Type: application/x-yaml"); // set proper content type
        header("Content-Disposition: attachment; filename=\"{$id}.yaml\""); // set as attachment and set file name
        
        echo "id: " . $id . 
                "\nname: " .
                "\n  first: " . $edata["name"] . 
                "\n  last: " . $edata["lastname"] . 
                "\ninfo: " . 
                "\n  visitor: " . ($edata["visitor"] == "f" ? "No" : "Yes") .
                ($edata["visitor"] == "f" ? "" : "\n  expiry: " . $edata["expiry"]) . 
                "\n  created: " . $edata["created"] .
                "\n  lastSeen: " . $edata["lastSeen"] .
                "\n  lastModified: " . $edata["lastModified"] .
                "\n  count: " . $edata["count"] .
                "\nphone: " .
                "\n  number: " . $edata["phone"] . 
                "\n  mobile: " . ($edata["mobileCarrier"] == "f" ? "No" : "Yes") . 
                "\ngrade: " . $edata["grade"] . 
                "\nbirthdate: " . $edata["dob"] . 
                //"\nactivity: " .      look up activity name
                //"\nroom: " .          look up room name
                "\nmedical: " . $edata["medical"] . 
                "\nparent1: " . $edata["parent2"] . 
                "\nparentEmail: " . $edata["parentEmail"] . 
                "\nnotes: " . $edata["notes"];
      }
  } else {
    header("HTTP/1.0 400 Bad Request");
    die("Problem.");
  }
?>
<?php
  
  if (is_numeric($id = $_GET["id"]) && array_key_exists("format", $_GET)) { 
      require_once 'config.php';

      $connection = pg_connect ("host=$dbhost dbname=$dbname 
                                user=$dbuser password=$dbpass");
      $query = "SELECT name, lastname, dob, activity, room, grade, phone, 
                       \"mobileCarrier\", paging, parent1, parent2,
                       \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                       \"lastModified\", count, visitor, expiry, \"noParentTag\",
                       barcode, picture, notes
                  FROM data WHERE id = $id;";
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      $edata = pg_fetch_assoc($result);
		
      if (!$edata) {
        header("HTTP/1.0 400 Bad Request");
        die("ID " . $id . " does not exist.");
      }
      
      // basic demonstration for download formats
      // each format generator could go into it's own file, or at very least it's own function in this file
      if ($_GET["format"] == "yaml") {
        header("Content-Type: application/x-yaml"); // set proper content type
        header("Content-Disposition: attachment; filename=\"{$id}.yaml\""); // set as attachment and set file name
        
        echo "id: " . $id . 
                "\nname: " .
                "\n  first: " . $edata["name"] . 
                "\n  last: " . $edata["lastname"] . 
                "\ninfo: " . 
                "\n  visitor: " . ($edata["visitor"] == "f" ? "No" : "Yes") .
                ($edata["visitor"] == "f" ? "" : "\n  expiry: " . $edata["expiry"]) . 
                "\n  created: " . $edata["created"] .
                "\n  lastSeen: " . $edata["lastSeen"] .
                "\n  lastModified: " . $edata["lastModified"] .
                "\n  count: " . $edata["count"] .
                "\nphone: " .
                "\n  number: " . $edata["phone"] . 
                "\n  mobile: " . ($edata["mobileCarrier"] == "f" ? "No" : "Yes") . 
                "\ngrade: " . $edata["grade"] . 
                "\nbirthdate: " . $edata["dob"] . 
                //"\nactivity: " .      look up activity name
                //"\nroom: " .          look up room name
                "\nmedical: " . $edata["medical"] . 
                "\nparent1: " . $edata["parent2"] . 
                "\nparentEmail: " . $edata["parentEmail"] . 
                "\nnotes: " . $edata["notes"];
      }
  } else {
    header("HTTP/1.0 400 Bad Request");
    die("Problem.");
  }
?>
<?php
  
  if (is_numeric($id = $_GET["id"]) && array_key_exists("format", $_GET)) { 
      require_once 'config.php';

      $connection = pg_connect ("host=$dbhost dbname=$dbname 
                                user=$dbuser password=$dbpass");
      $query = "SELECT name, lastname, dob, activity, room, grade, phone, 
                       \"mobileCarrier\", paging, parent1, parent2,
                       \"parentEmail\", medical, \"joinDate\", \"lastSeen\",
                       \"lastModified\", count, visitor, expiry, \"noParentTag\",
                       barcode, picture, notes
                  FROM data WHERE id = $id;";
      $result = pg_query($connection, $query) or 
        die("Error in query: $query." . pg_last_error($connection));
      $edata = pg_fetch_assoc($result);
		
      if (!$edata) {
        header("HTTP/1.0 400 Bad Request");
        die("ID " . $id . " does not exist.");
      }
      
      // basic demonstration for download formats
      // each format generator could go into it's own file, or at very least it's own function in this file
      if ($_GET["format"] == "yaml") {
        header("Content-Type: application/x-yaml"); // set proper content type
        header("Content-Disposition: attachment; filename=\"{$id}.yaml\""); // set as attachment and set file name
        
        echo "id: " . $id . 
                "\nname: " .
                "\n  first: " . $edata["name"] . 
                "\n  last: " . $edata["lastname"] . 
                "\ninfo: " . 
                "\n  visitor: " . ($edata["visitor"] == "f" ? "No" : "Yes") .
                ($edata["visitor"] == "f" ? "" : "\n  expiry: " . $edata["expiry"]) . 
                "\n  created: " . $edata["created"] .
                "\n  lastSeen: " . $edata["lastSeen"] .
                "\n  lastModified: " . $edata["lastModified"] .
                "\n  count: " . $edata["count"] .
                "\nphone: " .
                "\n  number: " . $edata["phone"] . 
                "\n  mobile: " . ($edata["mobileCarrier"] == "f" ? "No" : "Yes") . 
                "\ngrade: " . $edata["grade"] . 
                "\nbirthdate: " . $edata["dob"] . 
                //"\nactivity: " .      look up activity name
                //"\nroom: " .          look up room name
                "\nmedical: " . $edata["medical"] . 
                "\nparent1: " . $edata["parent2"] . 
                "\nparentEmail: " . $edata["parentEmail"] . 
                "\nnotes: " . $edata["notes"];
      }
  } else {
    header("HTTP/1.0 400 Bad Request");
    die("Problem.");
  }
?>
