<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error . "\n");
}
$res = $mysqli->query("SHOW TABLES");
if (!$res) {
    die("Query error: " . $mysqli->error . "\n");
}
echo "Tables in db_school:\n";
while ($row = $res->fetch_row()) {
    echo "- " . $row[0] . "\n";
}
