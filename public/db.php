<?php
$conn = new mysqli('localhost', 'root', '', 'u148138600_dblittle');
echo "TABLE: comments\n";
$res= $conn->query("SHOW COLUMNS FROM comments");
while($r = $res->fetch_assoc()) { echo $r['Field'] . " (" . $r['Type'] . ")\n"; }
