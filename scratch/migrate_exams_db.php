<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Examination & Results Database Migration ===\n";

// 1. tbl_exam_types
echo "1. Creating/Verifying tbl_exam_types...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_exam_types` (
  `exam_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`exam_type_id`),
  UNIQUE KEY `uk_exam_type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed default exam types if empty
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_exam_types");
if ($res->fetch_assoc()['cnt'] == 0) {
    echo "Seeding default exam types...\n";
    $types = [
        ['Unit Test', 'Periodic unit test evaluations'],
        ['Class Test', 'Classroom monthly assessment test'],
        ['First Term Examination', 'First term comprehensive exam'],
        ['Mid Term Examination', 'Half-yearly / mid-term examination'],
        ['Second Term Examination', 'Second term evaluations'],
        ['Annual Examination', 'Final annual academic examination'],
        ['Model Examination', 'Pre-board / model preparation exam'],
        ['Practical Examination', 'Laboratory and practical assessment']
    ];
    $stmt = $mysqli->prepare("INSERT INTO tbl_exam_types (type_name, description, status) VALUES (?, ?, 1)");
    foreach ($types as $t) {
        $stmt->bind_param("ss", $t[0], $t[1]);
        $stmt->execute();
    }
}

// 2. tbl_exams
echo "2. Creating/Modifying tbl_exams...\n";
// Re-create or modify tbl_exams
$mysqli->query("DROP TABLE IF EXISTS `tbl_exams`");
$mysqli->query("CREATE TABLE `tbl_exams` (
  `exam_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_name` VARCHAR(100) NOT NULL,
  `exam_type_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `description` TEXT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `applicable_classes` TEXT NULL,
  `status` ENUM('Draft', 'Scheduled', 'Ongoing', 'Completed', 'Marks Pending', 'Under Verification', 'Published', 'Cancelled') NOT NULL DEFAULT 'Draft',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`exam_id`),
  KEY `idx_exam_year` (`academic_year_id`),
  KEY `idx_exam_type` (`exam_type_id`),
  KEY `idx_exam_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed sample exam
$mysqli->query("INSERT INTO tbl_exams (exam_id, exam_name, exam_type_id, academic_year_id, description, start_date, end_date, applicable_classes, status)
    VALUES (1, 'First Term Examination 2026', 3, 1, 'First term comprehensive assessment for all middle & secondary classes', '2026-09-01', '2026-09-15', '[1,2,3,4,5,6,7,8,9,10]', 'Scheduled')");

// 3. tbl_exam_schedules
echo "3. Creating/Verifying tbl_exam_schedules...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_exam_schedules` (
  `schedule_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `teacher_id` INT UNSIGNED NULL,
  `exam_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `max_marks` DECIMAL(6,2) NOT NULL DEFAULT 100.00,
  `passing_marks` DECIMAL(6,2) NOT NULL DEFAULT 35.00,
  `room_no` VARCHAR(50) NULL,
  `instructions` TEXT NULL,
  `status` ENUM('Scheduled', 'Ongoing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`schedule_id`),
  UNIQUE KEY `uk_exam_class_sec_subj` (`exam_id`, `class_id`, `section_id`, `subject_id`),
  KEY `idx_sched_exam` (`exam_id`),
  KEY `idx_sched_class_sec` (`class_id`, `section_id`),
  KEY `idx_sched_subject` (`subject_id`),
  KEY `idx_sched_teacher` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed sample schedule items for Class 1 (Grade 10)
$subjects_res = $mysqli->query("SELECT subject_id FROM tbl_subjects WHERE status = 1 LIMIT 4");
$subj_ids = [];
while ($row = $subjects_res->fetch_assoc()) $subj_ids[] = $row['subject_id'];

if (count($subj_ids) > 0) {
    $sec_res = $mysqli->query("SELECT class_id, section_id FROM tbl_sections WHERE class_id = 1 LIMIT 1");
    if ($sec_row = $sec_res->fetch_assoc()) {
        $c_id = $sec_row['class_id'];
        $s_id = $sec_row['section_id'];
        $d = 1;
        foreach ($subj_ids as $sub_id) {
            $eDate = sprintf("2026-09-%02d", $d + 1);
            $mysqli->query("INSERT IGNORE INTO tbl_exam_schedules (exam_id, academic_year_id, class_id, section_id, subject_id, teacher_id, exam_date, start_time, end_time, max_marks, passing_marks, room_no)
                VALUES (1, 1, {$c_id}, {$s_id}, {$sub_id}, 1, '{$eDate}', '09:30:00', '12:30:00', 100.00, 35.00, 'Hall A-101')");
            $d += 2;
        }
    }
}

// 4. tbl_exam_marks
echo "4. Creating/Verifying tbl_exam_marks...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_exam_marks` (
  `mark_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` INT UNSIGNED NOT NULL,
  `schedule_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `marks_obtained` DECIMAL(6,2) NULL,
  `is_absent` TINYINT(1) NOT NULL DEFAULT 0,
  `is_exempted` TINYINT(1) NOT NULL DEFAULT 0,
  `grade` VARCHAR(10) NULL,
  `grade_point` DECIMAL(4,2) NULL,
  `status` ENUM('Draft', 'Submitted', 'Under Verification', 'Approved', 'Rejected') NOT NULL DEFAULT 'Draft',
  `remarks` VARCHAR(255) NULL,
  `entered_by` INT UNSIGNED NULL,
  `submitted_at` DATETIME NULL,
  `approved_by` INT UNSIGNED NULL,
  `approved_at` DATETIME NULL,
  `rejection_reason` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mark_id`),
  UNIQUE KEY `uk_exam_student_subject` (`exam_id`, `student_id`, `subject_id`),
  KEY `idx_mark_exam` (`exam_id`),
  KEY `idx_mark_student` (`student_id`),
  KEY `idx_mark_schedule` (`schedule_id`),
  KEY `idx_mark_class_sec` (`class_id`, `section_id`),
  KEY `idx_mark_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. tbl_grades
echo "5. Creating/Verifying tbl_grades...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_grades` (
  `grade_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `grade_name` VARCHAR(10) NOT NULL,
  `min_percentage` DECIMAL(5,2) NOT NULL,
  `max_percentage` DECIMAL(5,2) NOT NULL,
  `grade_point` DECIMAL(4,2) NOT NULL,
  `description` VARCHAR(100) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `uk_grade_name` (`grade_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Seed default grades if empty
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_grades");
if ($res->fetch_assoc()['cnt'] == 0) {
    echo "Seeding default grade scales...\n";
    $grades = [
        ['A+', 90.00, 100.00, 10.00, 'Outstanding'],
        ['A',  80.00, 89.99,  9.00,  'Excellent'],
        ['B+', 70.00, 79.99,  8.00,  'Very Good'],
        ['B',  60.00, 69.99,  7.00,  'Good'],
        ['C',  50.00, 59.99,  6.00,  'Average'],
        ['D',  40.00, 49.99,  5.00,  'Pass'],
        ['F',  0.00,  39.99,  0.00,  'Fail']
    ];
    $stmt = $mysqli->prepare("INSERT INTO tbl_grades (grade_name, min_percentage, max_percentage, grade_point, description, status) VALUES (?, ?, ?, ?, ?, 1)");
    foreach ($grades as $g) {
        $stmt->bind_param("sddds", $g[0], $g[1], $g[2], $g[3], $g[4]);
        $stmt->execute();
    }
}

// 6. tbl_student_results
echo "6. Creating/Verifying tbl_student_results...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_student_results` (
  `result_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `total_marks` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `max_marks` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `overall_grade` VARCHAR(10) NOT NULL DEFAULT 'F',
  `gpa` DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  `pass_status` ENUM('Pass', 'Fail', 'Withheld') NOT NULL DEFAULT 'Pass',
  `failed_subjects_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `class_rank` INT UNSIGNED NULL,
  `section_rank` INT UNSIGNED NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  `published_by` INT UNSIGNED NULL,
  `teacher_remarks` TEXT NULL,
  `principal_remarks` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`result_id`),
  UNIQUE KEY `uk_result_exam_student` (`exam_id`, `student_id`),
  KEY `idx_res_exam` (`exam_id`),
  KEY `idx_res_student` (`student_id`),
  KEY `idx_res_class_sec` (`class_id`, `section_id`),
  KEY `idx_res_published` (`is_published`),
  KEY `idx_res_rank` (`class_rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 7. tbl_examination_settings
echo "7. Creating/Verifying tbl_examination_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_examination_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `decimal_precision` INT UNSIGNED NOT NULL DEFAULT 2,
  `default_max_marks` DECIMAL(6,2) NOT NULL DEFAULT 100.00,
  `default_passing_marks` DECIMAL(6,2) NOT NULL DEFAULT 35.00,
  `subject_pass_mark_rule` TINYINT(1) NOT NULL DEFAULT 1,
  `overall_pass_percentage` DECIMAL(5,2) NOT NULL DEFAULT 35.00,
  `single_subject_fail_overall` TINYINT(1) NOT NULL DEFAULT 1,
  `rank_criteria` ENUM('Percentage', 'Total Marks', 'GPA') NOT NULL DEFAULT 'Percentage',
  `include_failed_in_rank` TINYINT(1) NOT NULL DEFAULT 0,
  `show_rank_on_report_card` TINYINT(1) NOT NULL DEFAULT 1,
  `show_attendance_on_report_card` TINYINT(1) NOT NULL DEFAULT 1,
  `report_card_header` TEXT NULL,
  `principal_signature_title` VARCHAR(100) NOT NULL DEFAULT 'Principal',
  `teacher_signature_title` VARCHAR(100) NOT NULL DEFAULT 'Class Teacher',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Insert default exam settings
$res = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_examination_settings");
if ($res->fetch_assoc()['cnt'] == 0) {
    echo "Inserting default examination settings...\n";
    $mysqli->query("INSERT INTO tbl_examination_settings (
        setting_id, decimal_precision, default_max_marks, default_passing_marks,
        subject_pass_mark_rule, overall_pass_percentage, single_subject_fail_overall,
        rank_criteria, include_failed_in_rank, show_rank_on_report_card, show_attendance_on_report_card,
        report_card_header, principal_signature_title, teacher_signature_title
    ) VALUES (
        1, 2, 100.00, 35.00,
        1, 35.00, 1,
        'Percentage', 0, 1, 1,
        'School International Model School - Official Academic Report Card', 'Principal', 'Class Teacher'
    )");
}

// 8. tbl_exam_audit_logs
echo "8. Creating/Verifying tbl_exam_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_exam_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

echo "=== Examination & Results Migration Completed Successfully! ===\n";
