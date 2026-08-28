<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
$res = $mysqli->query("SELECT user_id, username, email, role_id, status FROM tbl_users");
echo "Users:\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
