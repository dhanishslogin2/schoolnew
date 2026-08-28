<?php
$ch = curl_init('http://localhost/schoolnew/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'email' => 'admin@gmail.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie_test.txt');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch);

// Now request students/add
curl_setopt($ch, CURLOPT_URL, 'http://localhost/schoolnew/students/add');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie_test.txt');
$res1 = curl_exec($ch);
$info1 = curl_getinfo($ch);

// Request fees/collection
curl_setopt($ch, CURLOPT_URL, 'http://localhost/schoolnew/fees/collection');
$res2 = curl_exec($ch);
$info2 = curl_getinfo($ch);

echo "students/add URL: " . $info1['url'] . " Code: " . $info1['http_code'] . "\n";
echo "students/add Has form: " . (strpos($res1, 'student-add-form') !== false ? 'YES' : 'NO') . "\n";
if (strpos($res1, 'student-add-form') === false) {
    echo "First 500 bytes of students/add:\n" . substr(strip_tags($res1), 0, 500) . "\n";
}

echo "fees/collection URL: " . $info2['url'] . " Code: " . $info2['http_code'] . "\n";
echo "fees/collection Has search: " . (strpos($res2, 'fee-search-input') !== false ? 'YES' : 'NO') . "\n";
if (strpos($res2, 'fee-search-input') === false) {
    echo "First 500 bytes of fees/collection:\n" . substr(strip_tags($res2), 0, 500) . "\n";
}
