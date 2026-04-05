<?php
define("BASEPATH", "1");
define("ENVIRONMENT", "development");
require "application/config/database.php";
$mysqli = new mysqli($db["default"]["hostname"], $db["default"]["username"], $db["default"]["password"], $db["default"]["database"]);
$res = $mysqli->query("DESCRIBE testimonial");
while($row = $res->fetch_assoc()){ print_r($row); }

