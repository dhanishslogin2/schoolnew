<?php
$urls = [
    'http://localhost/schoolnew/auth/login',
    'http://localhost/school/auth/login'
];

foreach ($urls as $url) {
    $t0 = microtime(true);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    $dur = microtime(true) - $t0;
    echo "URL: $url\n";
    echo "HTTP Code: " . ($info['http_code'] ?? 'N/A') . "\n";
    echo "Total Time: " . number_format($dur, 4) . "s\n";
    if ($err) echo "Curl Error: $err\n";
    echo "Response Length: " . strlen($res) . " bytes\n\n";
}
