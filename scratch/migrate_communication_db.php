<?php
// Database Migration Script for Communication Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Communication Module Database Migration ===\n";

// 1. Upgrade tbl_notices
echo "1. Upgrading tbl_notices...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_notices` (
  `notice_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `category` ENUM('General', 'Academic', 'Examination', 'Holiday', 'Fee', 'Attendance', 'Event', 'Emergency', 'Other') NOT NULL DEFAULT 'General',
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NULL,
  `target_role` VARCHAR(100) NOT NULL DEFAULT 'All',
  `target_type` ENUM('Entire School', 'Class', 'Section', 'Individual') NOT NULL DEFAULT 'Entire School',
  `class_id` INT UNSIGNED NULL,
  `section_id` INT UNSIGNED NULL,
  `target_ids` TEXT NULL,
  `priority` ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal',
  `attachment` VARCHAR(255) NULL,
  `publish_date` DATE NOT NULL,
  `expiry_date` DATE NULL,
  `posted_by` VARCHAR(100) NOT NULL DEFAULT 'Admin',
  `posted_by_id` INT UNSIGNED NULL,
  `status` ENUM('Draft', 'Published', 'Scheduled', 'Expired', 'Archived') NOT NULL DEFAULT 'Published',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`notice_id`),
  KEY `idx_not_date` (`publish_date`),
  KEY `idx_not_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Check and add columns if tbl_notices existed with old schema
$cols = [];
$res = $mysqli->query("SHOW COLUMNS FROM tbl_notices");
while ($r = $res->fetch_assoc()) $cols[] = $r['Field'];

if (!in_array('academic_year_id', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `notice_id`");
if (!in_array('category', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `category` ENUM('General', 'Academic', 'Examination', 'Holiday', 'Fee', 'Attendance', 'Event', 'Emergency', 'Other') NOT NULL DEFAULT 'General' AFTER `academic_year_id`");
if (!in_array('target_role', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `target_role` VARCHAR(100) NOT NULL DEFAULT 'All' AFTER `content`");
if (!in_array('target_type', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `target_type` ENUM('Entire School', 'Class', 'Section', 'Individual') NOT NULL DEFAULT 'Entire School' AFTER `target_role`");
if (!in_array('class_id', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `class_id` INT UNSIGNED NULL AFTER `target_type`");
if (!in_array('section_id', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `section_id` INT UNSIGNED NULL AFTER `class_id`");
if (!in_array('target_ids', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `target_ids` TEXT NULL AFTER `section_id`");
if (!in_array('priority', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `priority` ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal' AFTER `target_ids`");
if (!in_array('attachment', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `attachment` VARCHAR(255) NULL AFTER `priority`");
if (!in_array('publish_date', $cols)) {
    if (in_array('notice_date', $cols)) {
        $mysqli->query("ALTER TABLE tbl_notices CHANGE COLUMN `notice_date` `publish_date` DATE NOT NULL");
    } else {
        $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `publish_date` DATE NOT NULL");
    }
}
if (!in_array('expiry_date', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `expiry_date` DATE NULL AFTER `publish_date`");
if (!in_array('posted_by_id', $cols)) $mysqli->query("ALTER TABLE tbl_notices ADD COLUMN `posted_by_id` INT UNSIGNED NULL AFTER `posted_by`");
$mysqli->query("ALTER TABLE tbl_notices MODIFY COLUMN `status` ENUM('Draft', 'Published', 'Scheduled', 'Expired', 'Archived') NOT NULL DEFAULT 'Published'");

// 2. Upgrade tbl_announcements
echo "2. Upgrading tbl_announcements...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_announcements` (
  `announcement_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'General',
  `content` TEXT NULL,
  `audience` VARCHAR(100) NOT NULL DEFAULT 'Whole School',
  `target_role` VARCHAR(100) NOT NULL DEFAULT 'All',
  `announcement_date` DATE NOT NULL,
  `expiry_date` DATE NULL,
  `priority` ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal',
  `attachment` VARCHAR(255) NULL,
  `posted_by` VARCHAR(100) NOT NULL DEFAULT 'Principal',
  `status` ENUM('Draft', 'Published', 'Scheduled', 'Archived') NOT NULL DEFAULT 'Published',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$acols = [];
$res = $mysqli->query("SHOW COLUMNS FROM tbl_announcements");
while ($r = $res->fetch_assoc()) $acols[] = $r['Field'];

if (!in_array('category', $acols)) $mysqli->query("ALTER TABLE tbl_announcements ADD COLUMN `category` VARCHAR(100) NOT NULL DEFAULT 'General' AFTER `title`");
if (!in_array('target_role', $acols)) $mysqli->query("ALTER TABLE tbl_announcements ADD COLUMN `target_role` VARCHAR(100) NOT NULL DEFAULT 'All' AFTER `audience`");
if (!in_array('expiry_date', $acols)) $mysqli->query("ALTER TABLE tbl_announcements ADD COLUMN `expiry_date` DATE NULL AFTER `announcement_date`");
if (!in_array('priority', $acols)) $mysqli->query("ALTER TABLE tbl_announcements ADD COLUMN `priority` ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal' AFTER `expiry_date`");
if (!in_array('attachment', $acols)) $mysqli->query("ALTER TABLE tbl_announcements ADD COLUMN `attachment` VARCHAR(255) NULL AFTER `priority`");
if (!in_array('posted_by', $acols)) $mysqli->query("ALTER TABLE tbl_announcements ADD COLUMN `posted_by` VARCHAR(100) NOT NULL DEFAULT 'Principal' AFTER `attachment`");
$mysqli->query("ALTER TABLE tbl_announcements MODIFY COLUMN `status` ENUM('Draft', 'Published', 'Scheduled', 'Archived') NOT NULL DEFAULT 'Published'");

// 3. tbl_communication_templates
echo "3. Creating tbl_communication_templates...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_communication_templates` (
  `template_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(150) NOT NULL,
  `communication_type` ENUM('General', 'Attendance', 'Fee Reminder', 'Homework', 'Examination', 'Event', 'Emergency') NOT NULL DEFAULT 'General',
  `channel` ENUM('In-App', 'SMS', 'WhatsApp', 'Email') NOT NULL DEFAULT 'SMS',
  `subject` VARCHAR(255) NULL,
  `message_template` TEXT NOT NULL,
  `variables` VARCHAR(255) NOT NULL DEFAULT '{student_name}, {parent_name}, {date}, {school_name}',
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$tmpl_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_templates")->fetch_assoc()['cnt'];
if ($tmpl_cnt == 0) {
    echo "Seeding default notification templates...\n";
    $default_templates = [
        ['Daily Absent Notification', 'Attendance', 'SMS', 'Absent Alert', 'Dear {parent_name}, your child {student_name} was marked ABSENT on {date}. Please contact the school office if this was unplanned. - {school_name}', '{student_name}, {parent_name}, {date}, {school_name}'],
        ['Fee Due Reminder', 'Fee Reminder', 'WhatsApp', 'Fee Due Notice', 'Dear {parent_name}, this is a gentle reminder that an amount of Rs. {amount} for {student_name} ({class}) is due on {due_date}. Kindly clear the dues. - {school_name}', '{student_name}, {parent_name}, {class}, {amount}, {due_date}, {school_name}'],
        ['New Homework Assigned', 'Homework', 'In-App', 'New Assignment Alert', 'New assignment for {subject}: {assignment} has been assigned to {student_name}. Due Date: {due_date}.', '{student_name}, {subject}, {assignment}, {due_date}'],
        ['Examination Schedule Announcement', 'Examination', 'Email', 'Upcoming Examination Timetable', '<p>Dear {parent_name},</p><p>The examination schedule for <strong>{exam_name}</strong> starting on <strong>{date}</strong> has been published. Please review the detailed timetable in the student portal.</p><p>Regards,<br>{school_name}</p>', '{student_name}, {parent_name}, {exam_name}, {date}, {school_name}'],
        ['Emergency School Closure', 'Emergency', 'SMS', 'Urgent Announcement', 'URGENT: {school_name} will remain CLOSED on {date} due to {reason}. Online classes will be conducted as per schedule.', '{school_name}, {date}, {reason}']
    ];
    $stmt = $mysqli->prepare("INSERT INTO tbl_communication_templates (template_name, communication_type, channel, subject, message_template, variables, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
    foreach ($default_templates as $dt) {
        $stmt->bind_param("ssssss", $dt[0], $dt[1], $dt[2], $dt[3], $dt[4], $dt[5]);
        $stmt->execute();
    }
}

// 4. tbl_communication_messages (Centralized Message Dispatcher & Queue)
echo "4. Creating tbl_communication_messages...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_communication_messages` (
  `message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source_module` ENUM('Direct', 'Attendance', 'Fees', 'Homework', 'Examination', 'Timetable') NOT NULL DEFAULT 'Direct',
  `source_ref_id` INT UNSIGNED NULL,
  `channel` ENUM('In-App', 'SMS', 'WhatsApp', 'Email') NOT NULL DEFAULT 'In-App',
  `template_id` INT UNSIGNED NULL,
  `sender_id` INT UNSIGNED NULL,
  `recipient_type` ENUM('All', 'Role', 'Class', 'Section', 'Individual') NOT NULL DEFAULT 'Individual',
  `recipient_id` INT UNSIGNED NULL,
  `recipient_name` VARCHAR(150) NULL,
  `recipient_contact` VARCHAR(150) NULL,
  `subject` VARCHAR(255) NULL,
  `message` TEXT NOT NULL,
  `attachment` VARCHAR(255) NULL,
  `scheduled_at` DATETIME NULL,
  `sent_at` DATETIME NULL,
  `delivered_at` DATETIME NULL,
  `status` ENUM('Draft', 'Scheduled', 'Processing', 'Sent', 'Delivered', 'Failed', 'Cancelled') NOT NULL DEFAULT 'Sent',
  `failure_reason` TEXT NULL,
  `retry_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `idx_msg_channel` (`channel`),
  KEY `idx_msg_status` (`status`),
  KEY `idx_msg_scheduled` (`scheduled_at`),
  KEY `idx_msg_source` (`source_module`, `source_ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. tbl_conversations & tbl_conversation_participants
echo "5. Creating tbl_conversations & participants...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_conversations` (
  `conversation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_type` ENUM('Parent-Teacher', 'Internal', 'Group') NOT NULL DEFAULT 'Internal',
  `group_id` INT UNSIGNED NULL,
  `title` VARCHAR(255) NULL,
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`conversation_id`),
  KEY `idx_conv_type` (`conversation_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_conversation_participants` (
  `participant_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `user_type` ENUM('Staff', 'Parent', 'Student') NOT NULL DEFAULT 'Staff',
  `unread_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `last_read_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`participant_id`),
  UNIQUE KEY `uk_conv_user` (`conversation_id`, `user_id`, `user_type`),
  KEY `idx_cp_user` (`user_id`, `user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 6. tbl_messages (Chat Messages within Conversations)
echo "6. Creating tbl_messages...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_messages` (
  `message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` INT UNSIGNED NOT NULL,
  `sender_id` INT UNSIGNED NOT NULL,
  `sender_type` ENUM('Staff', 'Parent', 'Student') NOT NULL DEFAULT 'Staff',
  `message_text` TEXT NOT NULL,
  `attachments` TEXT NULL,
  `status` ENUM('Sent', 'Delivered', 'Read') NOT NULL DEFAULT 'Sent',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `idx_msg_conv` (`conversation_id`),
  KEY `idx_msg_sender` (`sender_id`, `sender_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 7. tbl_communication_groups
echo "7. Creating tbl_communication_groups...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_communication_groups` (
  `group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(150) NOT NULL,
  `description` VARCHAR(255) NULL,
  `group_type` ENUM('Teachers', 'Parents', 'Management', 'Custom') NOT NULL DEFAULT 'Custom',
  `member_user_ids` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$grp_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_groups")->fetch_assoc()['cnt'];
if ($grp_cnt == 0) {
    echo "Seeding default communication groups...\n";
    $mysqli->query("INSERT INTO tbl_communication_groups (group_name, description, group_type, member_user_ids, status, created_by)
        VALUES ('All Teachers', 'All active teaching faculty members across classes', 'Teachers', '[1, 2, 3, 4]', 1, 1),
               ('Class 10 Parents', 'Parents and guardians of Grade 10 students', 'Parents', '[1, 2, 3]', 1, 1),
               ('Administrative Staff', 'Administrative and office management staff', 'Management', '[1, 2]', 1, 1)");
}

// 8. tbl_communication_settings
echo "8. Creating tbl_communication_settings...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_communication_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_inapp` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_sms` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_whatsapp` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_email` TINYINT(1) NOT NULL DEFAULT 1,
  `sms_provider` VARCHAR(50) NOT NULL DEFAULT 'Generic SMS Gateway',
  `sms_sender_id` VARCHAR(20) NOT NULL DEFAULT 'EDUSCH',
  `whatsapp_provider` VARCHAR(50) NOT NULL DEFAULT 'WhatsApp Business API',
  `email_from_name` VARCHAR(100) NOT NULL DEFAULT 'School International School',
  `email_from_address` VARCHAR(100) NOT NULL DEFAULT 'notifications@school.school',
  `enable_scheduled_jobs` TINYINT(1) NOT NULL DEFAULT 1,
  `max_retries` INT UNSIGNED NOT NULL DEFAULT 3,
  `retry_interval_minutes` INT UNSIGNED NOT NULL DEFAULT 15,
  `parent_teacher_direct_messaging` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$set_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_settings")->fetch_assoc()['cnt'];
if ($set_cnt == 0) {
    echo "Inserting default communication settings...\n";
    $mysqli->query("INSERT INTO tbl_communication_settings (
        setting_id, enable_inapp, enable_sms, enable_whatsapp, enable_email,
        sms_provider, sms_sender_id, whatsapp_provider, email_from_name, email_from_address,
        enable_scheduled_jobs, max_retries, retry_interval_minutes, parent_teacher_direct_messaging
    ) VALUES (
        1, 1, 1, 1, 1, 'Generic SMS Gateway', 'EDUSCH', 'WhatsApp Business API',
        'School Model School', 'notifications@school.school', 1, 3, 15, 1
    )");
}

// 9. tbl_communication_audit_logs
echo "9. Creating tbl_communication_audit_logs...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_communication_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_comm_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

echo "=== Communication Module Database Migration Completed Successfully! ===\n";
