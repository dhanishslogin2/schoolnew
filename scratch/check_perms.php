<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
$res = $mysqli->query("SELECT permission_id, permission_key FROM tbl_permissions WHERE permission_key LIKE 'students%'");
while ($row = $res->fetch_assoc()) {
    echo $row['permission_id'] . " => " . $row['permission_key'] . "\n";
}
