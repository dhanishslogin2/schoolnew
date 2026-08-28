<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
$res = $mysqli->query("SELECT user_id, username, email, password, status FROM tbl_users WHERE user_id = 15");
$user = $res->fetch_assoc();
print_r($user);
// Check if password_verify works with common passwords
$passwords = ['admin123', 'admin', 'password', '123456', 'Admin@123', 'admin@123', 'root'];
foreach ($passwords as $p) {
    if (password_verify($p, $user['password'])) {
        echo "MATCH FOUND: '$p'\n";
    }
}
