<?php
$base_url = 'http://localhost/schoolnew/';
$cookie_file = __DIR__ . '/cookie.txt';
if (file_exists($cookie_file)) unlink($cookie_file);

// 1. Fetch Login Page to get CSRF token
$ch = curl_init($base_url . 'auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
$html = curl_exec($ch);
curl_close($ch);

// 2. Perform Login with Admin credentials
// Let's check password for admin or update password hash for testing if needed
// Let's test standard password (admin123, password, 123456, Admin@123, etc.)
// Let's directly create a session in CI or test login POST
echo "Attempting login...\n";
$post_data = [
    'email' => 'admin@gmail.com',
    'password' => 'password123'
];

$ch = curl_init($base_url . 'auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$res = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "Login Response URL: " . $info['effective_url'] . " Code: " . $info['http_code'] . "\n";

$routes = [
    'dashboard',
    'students/overview',
    'students/list',
    'students/register',
    'staff/overview',
    'staff/directory',
    'academics/overview',
    'academics/years',
    'academics/classes',
    'academics/sections',
    'academics/subjects',
    'attendance',
    'attendance/daily',
    'attendance/reports',
    'examinations',
    'examinations/exams',
    'examinations/results',
    'fees',
    'fees/structures',
    'fees/student_fees',
    'fees/payments',
    'timetable',
    'homework',
    'communication',
    'leave',
    'transport',
    'certificates',
    'users',
    'settings'
];

echo "\n--- BENCHMARKING PAGE RESPONSE TIMES ---\n";
printf("%-35s %-8s %-12s %-12s %-10s\n", "Route", "Status", "Total Time", "TTFB", "Size (KB)");
echo str_repeat("-", 80) . "\n";

$results = [];
foreach ($routes as $route) {
    $ch = curl_init($base_url . $route);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $start = microtime(true);
    $response = curl_exec($ch);
    $dur = microtime(true) - $start;
    $info = curl_getinfo($ch);
    $code = $info['http_code'];
    $ttfb = $info['starttransfer_time'];
    $size = strlen($response) / 1024;
    curl_close($ch);

    printf("%-35s %-8d %-10.4fs %-10.4fs %-10.2f\n", $route, $code, $dur, $ttfb, $size);
    $results[$route] = [
        'code' => $code,
        'time' => $dur,
        'ttfb' => $ttfb,
        'size' => $size
    ];
}
