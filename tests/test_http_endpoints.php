<?php
/**
 * Test HTTP request to examinations/ajax_get_subjects and marks_entry with session login
 */

$cookie_file = tempnam(sys_get_temp_dir(), 'ci_cookie_');

function http_req($url, $post = null) {
    global $cookie_file;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $eff = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return ['body' => $res, 'code' => $code, 'url' => $eff];
}

echo "=== 1. Login as Admin ===\n";
// Fetch login page for CSRF token
$login_page = http_req('http://localhost/schoolnew/auth/login');
preg_match('/name="csrf_test_name" value="([a-f0-9]+)"/i', $login_page['body'], $matches);
$csrf = $matches[1] ?? '';

// Post credentials
$login_res = http_req('http://localhost/schoolnew/auth/login', [
    'csrf_test_name' => $csrf,
    'email'          => 'admin@gmail.com',
    'password'       => '123456'
]);

echo "Login Response URL: {$login_res['url']}\n";

echo "\n=== 2. Test ajax_get_subjects for LKG (Class ID: 1) ===\n";
$ajax_lkg = http_req('http://localhost/schoolnew/examinations/ajax_get_subjects/1');
echo "HTTP Code: {$ajax_lkg['code']}\n";
$subjects_lkg = json_decode($ajax_lkg['body'], true);
echo "Returned Subjects count: " . (is_array($subjects_lkg) ? count($subjects_lkg) : 'NOT JSON') . "\n";
if (is_array($subjects_lkg)) {
    foreach ($subjects_lkg as $s) {
        echo "  - [ID: {$s['subject_id']}] {$s['subject_name']} ({$s['subject_code']})\n";
    }
}

echo "\n=== 3. Test ajax_get_subjects for Grade 1 (Class ID: 14) ===\n";
$ajax_g1 = http_req('http://localhost/schoolnew/examinations/ajax_get_subjects/14');
$subjects_g1 = json_decode($ajax_g1['body'], true);
echo "Returned Subjects count: " . (is_array($subjects_g1) ? count($subjects_g1) : 'NOT JSON') . "\n";
if (is_array($subjects_g1)) {
    foreach ($subjects_g1 as $s) {
        echo "  - [ID: {$s['subject_id']}] {$s['subject_name']} ({$s['subject_code']})\n";
    }
}

echo "\n=== 4. Test ajax_get_subjects for Grade 5 (Class ID: 18) ===\n";
$ajax_g5 = http_req('http://localhost/schoolnew/examinations/ajax_get_subjects/18');
$subjects_g5 = json_decode($ajax_g5['body'], true);
echo "Returned Subjects count: " . (is_array($subjects_g5) ? count($subjects_g5) : 'NOT JSON') . "\n";
if (is_array($subjects_g5)) {
    foreach ($subjects_g5 as $s) {
        echo "  - [ID: {$s['subject_id']}] {$s['subject_name']} ({$s['subject_code']})\n";
    }
}

echo "\n=== 5. Test marks_entry view for Class = LKG ===\n";
$me_page = http_req('http://localhost/schoolnew/examinations/marks_entry?class_id=1');
echo "HTTP Code: {$me_page['code']}\n";

// Verify Subject dropdown options in HTML
preg_match('/<select name="subject_id"[^>]*>(.*?)<\/select>/is', $me_page['body'], $sel_matches);
if (!empty($sel_matches[1])) {
    echo "Rendered Subject select options for LKG:\n";
    preg_match_all('/<option value="([^"]*)"[^>]*>(.*?)<\/option>/is', $sel_matches[1], $opts, PREG_SET_ORDER);
    foreach ($opts as $o) {
        echo "  - Value: '{$o[1]}' | Text: " . trim($o[2]) . "\n";
    }
} else {
    echo "Subject select not found in HTML response.\n";
}

echo "\n=== 6. Test marks_entry view for Class = Grade 5 (Class ID: 18) ===\n";
$me_g5 = http_req('http://localhost/schoolnew/examinations/marks_entry?class_id=18');
preg_match('/<select name="subject_id"[^>]*>(.*?)<\/select>/is', $me_g5['body'], $sel_g5);
if (!empty($sel_g5[1])) {
    echo "Rendered Subject select options for Grade 5:\n";
    preg_match_all('/<option value="([^"]*)"[^>]*>(.*?)<\/option>/is', $sel_g5[1], $opts, PREG_SET_ORDER);
    foreach ($opts as $o) {
        echo "  - Value: '{$o[1]}' | Text: " . trim($o[2]) . "\n";
    }
}

@unlink($cookie_file);
