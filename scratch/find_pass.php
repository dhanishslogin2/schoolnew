<?php
$hash = '$2y$10$YrFettmslSp77VgzfFtGPOZe/oWJpfypluQpxEuXTrcN87HdpUG6O';
$candidates = [
    'admin', 'Admin', 'admin123', 'Admin123', 'admin@123', 'Admin@123',
    'password', 'Password', 'password123', 'Password123', 'Password@123',
    '123456', '12345678', 'school', 'School', 'school123', 'School123',
    'Secret123', 'Secret@123', 'Parent@123', 'Student@123', 'root', 'superadmin',
    'Admin#123', 'Admin2024', 'Admin2025', 'Admin2026', 'admin@school.com',
    'admin@gmail.com', 'admin_123', 'Admin_123'
];

foreach ($candidates as $cand) {
    if (password_verify($cand, $hash)) {
        echo "FOUND MATCH: " . $cand . "\n";
        exit;
    }
}
echo "No match in standard candidates.\n";
