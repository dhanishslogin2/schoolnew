<?php
$_SERVER['CI_ENV'] = 'testing';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('ENVIRONMENT', 'development');

require_once __DIR__ . '/../index.php';

$CI =& get_instance();
$CI->load->database();
$users = $CI->db->select('user_id, username, email, role_id, status')->get('users')->result_array();
echo "Found " . count($users) . " users.\n";
print_r($users);
