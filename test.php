<?php
define("BASEPATH", "1");
define("ENVIRONMENT", "development");
require "application/config/database.php";
$mysqli = new mysqli($db["default"]["hostname"], $db["default"]["username"], $db["default"]["password"], $db["default"]["database"]);
$res = $mysqli->query("SELECT img FROM users WHERE role_id = 5 LIMIT 1");
$row = $res->fetch_assoc();
print_r($row);

