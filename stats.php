<?php
    require_once 'config.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");
    
    require_once "template/header.php";
?>

<div class="span9 well" style="overflow-x: auto;">
<?php

    $filterstr = "";
    if (array_key_exists("activity", $_GET) or array_key_exists("room", $_GET)) {
      $filterstr .= " WHERE";
      $filterstr .= array_key_exists("activity", $_GET) ? " activity = {$_GET["activity"]}" : "";
      $filterstr .= (array_key_exists("activity", $_GET) and array_key_exists("room", $_GET)) ? "," : "";
      $filterstr .= array_key_exists("room", $_GET) ? " room = {$_GET["room"]}" : "";
    }
    $query = "SELECT id, visitor FROM data$filterstr;";

    $result = pg_query($connection, $query) or 
      die("Error in query: $query." . pg_last_error($connection));
    
    echo $query . "<br/>\n";
    
    while ($row = pg_fetch_assoc($result)) {
         print_r($row);
         echo "<br/>\n";
    } 
    
    pg_free_result($result);
?> 

</div>
<?php
  require_once "template/footer.php" ;
  pg_close($connection);
?>
