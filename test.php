<?php
define("BASEPATH", "1");
define("ENVIRONMENT", "development");
require "application/config/database.php";
$mysqli = new mysqli($db["default"]["hostname"], $db["default"]["username"], $db["default"]["password"], $db["default"]["database"]);
$res = $mysqli->query("SHOW TABLES");
while($row = $res->fetch_array()){ 
    $table = $row[0];
    $cres = $mysqli->query("DESCRIBE $table");
    while($crow = $cres->fetch_assoc()){
        $f = $crow["Field"];
        if(strpos($f, "url") !== false || strpos($f, "foto") !== false || strpos($f, "gambar") !== false || strpos($f, "file") !== false || $f == "image"){
            echo "$table.{$f}\n"; 
        }
    }
}

