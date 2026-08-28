<?php
$ch = curl_init('http://localhost/schoolnew/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'email' => 'admin@gmail.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/c.txt');
curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, 'http://localhost/schoolnew/students/add');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/c.txt');
$h = curl_exec($ch);

$pos = strpos($h, '<form');
if ($pos !== false) {
    echo "FORM TAG:\n" . substr($h, $pos, 250) . "\n";
} else {
    echo "NO FORM TAG FOUND in response!\n";
}
