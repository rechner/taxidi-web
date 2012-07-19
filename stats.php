<?php
    require_once 'config.php';

    $connection = pg_connect ("host=$dbhost dbname=$dbname 
                              user=$dbuser password=$dbpass");

    $query = "SELECT id FROM data WHERE ;";
?> 
