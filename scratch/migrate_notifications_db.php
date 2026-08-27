<?php
// Migration and Upgrade Script for Communication / Notifications Second Phase

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== Running Communication & Notification Second Phase DB Migration ===\n";

// 1. Upgrade tbl_communication_templates
echo "1. Upgrading tbl_communication_templates...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_communication_templates` (
  `template_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(150) NOT NULL,
  `template_code` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'General',
  `communication_type` ENUM('General', 'Attendance', 'Fee Reminder', 'Homework', 'Examination', 'Leave', 'Transport', 'Certificate', 'Emergency') NOT NULL DEFAULT 'General',
  `channel` ENUM('In-App', 'SMS', 'WhatsApp', 'Email') NOT NULL DEFAULT 'SMS',
  `subject` VARCHAR(255) NULL,
  `message_template` TEXT NOT NULL,
  `variables` VARCHAR(255) NOT NULL DEFAULT '{student_name}, {parent_name}, {date}, {school_name}',
  `character_limit` INT UNSIGNED NULL,
  `description` TEXT NULL,
  `is_system` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `uk_comm_tmpl_code` (`template_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Add missing columns if tbl_communication_templates existed
$existing_cols = [];
$c_res = $mysqli->query("SHOW COLUMNS FROM tbl_communication_templates");
while($c = $c_res->fetch_assoc()) $existing_cols[] = $c['Field'];

if (!in_array('template_code', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_templates ADD COLUMN `template_code` VARCHAR(100) NOT NULL AFTER `template_name`");
}
if (!in_array('category', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_templates ADD COLUMN `category` VARCHAR(50) NOT NULL DEFAULT 'General' AFTER `template_code`");
}
if (!in_array('character_limit', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_templates ADD COLUMN `character_limit` INT UNSIGNED NULL AFTER `variables`");
}
if (!in_array('description', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_templates ADD COLUMN `description` TEXT NULL AFTER `character_limit`");
}
if (!in_array('is_system', $existing_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_templates ADD COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT 0 AFTER `description`");
}
$mysqli->query("ALTER TABLE tbl_communication_templates MODIFY COLUMN `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'");

// 2. Create tbl_notification_rules
echo "2. Creating tbl_notification_rules...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_notification_rules` (
  `rule_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rule_name` VARCHAR(150) NOT NULL,
  `event_name` VARCHAR(100) NOT NULL,
  `source_module` ENUM('Attendance', 'Fees', 'Homework', 'Examination', 'Leave', 'Transport', 'Certificates', 'General') NOT NULL DEFAULT 'General',
  `template_id` INT UNSIGNED NOT NULL,
  `channel` ENUM('In-App', 'SMS', 'WhatsApp', 'Email') NOT NULL DEFAULT 'In-App',
  `recipient_type` ENUM('Student', 'Parent', 'Teacher', 'Staff', 'Principal', 'Admin', 'Class', 'Section', 'Group') NOT NULL DEFAULT 'Parent',
  `conditions_json` JSON NULL,
  `frequency` ENUM('Once per event', 'Once per day', 'Once per week') NOT NULL DEFAULT 'Once per event',
  `cooldown_minutes` INT UNSIGNED NOT NULL DEFAULT 60,
  `priority` ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal',
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rule_id`),
  KEY `idx_nr_event` (`event_name`),
  KEY `idx_nr_module` (`source_module`),
  KEY `idx_nr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 3. Upgrade tbl_communication_messages
echo "3. Upgrading tbl_communication_messages...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_communication_messages` (
  `message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_name` VARCHAR(100) NULL,
  `source_module` ENUM('Attendance', 'Fees', 'Homework', 'Examination', 'Leave', 'Transport', 'Certificates', 'Direct', 'General') NOT NULL DEFAULT 'Direct',
  `source_ref_id` INT UNSIGNED NULL,
  `channel` ENUM('In-App', 'SMS', 'WhatsApp', 'Email') NOT NULL DEFAULT 'In-App',
  `template_id` INT UNSIGNED NULL,
  `template_code` VARCHAR(100) NULL,
  `sender_id` INT UNSIGNED NULL,
  `recipient_type` ENUM('All', 'Role', 'Class', 'Section', 'Individual', 'Student', 'Parent', 'Teacher', 'Staff') NOT NULL DEFAULT 'Individual',
  `recipient_id` INT UNSIGNED NULL,
  `recipient_name` VARCHAR(150) NULL,
  `recipient_contact` VARCHAR(150) NULL,
  `subject` VARCHAR(255) NULL,
  `message` TEXT NOT NULL,
  `rendered_message` LONGTEXT NULL,
  `attachment` VARCHAR(255) NULL,
  `priority` ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal',
  `idempotency_key` VARCHAR(100) NULL,
  `scheduled_at` DATETIME NULL,
  `sent_at` DATETIME NULL,
  `delivered_at` DATETIME NULL,
  `status` ENUM('Pending', 'Scheduled', 'Processing', 'Sent', 'Delivered', 'Failed', 'Cancelled') NOT NULL DEFAULT 'Sent',
  `failure_reason` TEXT NULL,
  `retry_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `max_retries` INT UNSIGNED NOT NULL DEFAULT 3,
  `last_attempt_at` DATETIME NULL,
  `next_retry_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `idx_msg_channel` (`channel`),
  KEY `idx_msg_status` (`status`),
  KEY `idx_msg_scheduled` (`scheduled_at`),
  KEY `idx_msg_source` (`source_module`, `source_ref_id`),
  KEY `idx_msg_idempotency` (`idempotency_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$msg_cols = [];
$m_res = $mysqli->query("SHOW COLUMNS FROM tbl_communication_messages");
while($m = $m_res->fetch_assoc()) $msg_cols[] = $m['Field'];

if (!in_array('event_name', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `event_name` VARCHAR(100) NULL AFTER `message_id`");
}
if (!in_array('template_code', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `template_code` VARCHAR(100) NULL AFTER `template_id`");
}
if (!in_array('rendered_message', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `rendered_message` LONGTEXT NULL AFTER `message`");
}
if (!in_array('priority', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `priority` ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal' AFTER `attachment`");
}
if (!in_array('idempotency_key', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `idempotency_key` VARCHAR(100) NULL AFTER `priority`");
}
if (!in_array('max_retries', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `max_retries` INT UNSIGNED NOT NULL DEFAULT 3 AFTER `retry_count`");
}
if (!in_array('last_attempt_at', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `last_attempt_at` DATETIME NULL AFTER `max_retries`");
}
if (!in_array('next_retry_at', $msg_cols)) {
    $mysqli->query("ALTER TABLE tbl_communication_messages ADD COLUMN `next_retry_at` DATETIME NULL AFTER `last_attempt_at`");
}
$mysqli->query("ALTER TABLE tbl_communication_messages MODIFY COLUMN `status` ENUM('Pending', 'Scheduled', 'Processing', 'Sent', 'Delivered', 'Failed', 'Cancelled') NOT NULL DEFAULT 'Sent'");

// 4. Create tbl_notification_preferences
echo "4. Creating tbl_notification_preferences...\n";
$mysqli->query("CREATE TABLE IF NOT EXISTS `tbl_notification_preferences` (
  `preference_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `user_type` ENUM('Parent', 'Teacher', 'Student', 'Staff') NOT NULL DEFAULT 'Parent',
  `preference_key` VARCHAR(50) NOT NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`preference_id`),
  UNIQUE KEY `uk_user_pref` (`user_id`, `user_type`, `preference_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// 5. Seed Standard Notification Templates
echo "5. Seeding Standard Templates & Automation Rules...\n";

$templates = [
    [
        'name' => 'Student Absent Alert',
        'code' => 'ATTENDANCE_ABSENT',
        'category' => 'Attendance',
        'type' => 'Attendance',
        'channel' => 'SMS',
        'subject' => 'Student Absence Alert - {student_name}',
        'body' => 'Dear {parent_name}, your child {student_name} (Adm: {admission_number}) was marked ABSENT today, {date}. Please contact the school office if this is in error. - {school_name}',
        'char_limit' => 160,
        'desc' => 'Instant SMS sent to parent when student is marked absent.'
    ],
    [
        'name' => 'Student Late Arrival',
        'code' => 'ATTENDANCE_LATE',
        'category' => 'Attendance',
        'type' => 'Attendance',
        'channel' => 'In-App',
        'subject' => 'Late Arrival Notification',
        'body' => 'Dear {parent_name}, your child {student_name} arrived late to school at {time} on {date}. - {school_name}',
        'char_limit' => 200,
        'desc' => 'Notification for tardy or late student arrival.'
    ],
    [
        'name' => 'Fee Due Reminder',
        'code' => 'FEE_DUE',
        'category' => 'Fees',
        'type' => 'Fee Reminder',
        'channel' => 'SMS',
        'subject' => 'Fee Reminder for {student_name}',
        'body' => 'Dear {parent_name}, fee of Rs.{amount} for {student_name} (Class {class}) is due on {due_date}. Please pay before due date. - {school_name}',
        'char_limit' => 160,
        'desc' => 'Upcoming fee due date alert for parents.'
    ],
    [
        'name' => 'Fee Overdue Alert',
        'code' => 'FEE_OVERDUE',
        'category' => 'Fees',
        'type' => 'Fee Reminder',
        'channel' => 'WhatsApp',
        'subject' => 'URGENT: Outstanding Fee Overdue',
        'body' => 'Dear {parent_name}, this is an urgent reminder that fee of Rs.{amount} for {student_name} ({admission_number}) was due on {due_date} and is now OVERDUE. Please clear the balance immediately.',
        'char_limit' => 300,
        'desc' => 'WhatsApp alert for overdue student fees.'
    ],
    [
        'name' => 'Fee Payment Received',
        'code' => 'FEE_PAYMENT_RECEIVED',
        'category' => 'Fees',
        'type' => 'Fee Reminder',
        'channel' => 'WhatsApp',
        'subject' => 'Fee Payment Confirmation',
        'body' => 'Dear {parent_name}, we have received payment of Rs.{amount} for {student_name} on {date}. Thank you. - {school_name}',
        'char_limit' => 200,
        'desc' => 'Receipt acknowledgment for fee collection.'
    ],
    [
        'name' => 'New Homework Assigned',
        'code' => 'HOMEWORK_ASSIGNED',
        'category' => 'Homework',
        'type' => 'Homework',
        'channel' => 'In-App',
        'subject' => 'New Assignment in {subject}',
        'body' => 'A new assignment "{assignment}" in {subject} has been published for Class {class}-{section}. Due date: {due_date}.',
        'char_limit' => 250,
        'desc' => 'In-app notification when teacher publishes assignment.'
    ],
    [
        'name' => 'Homework Due Soon',
        'code' => 'HOMEWORK_DUE',
        'category' => 'Homework',
        'type' => 'Homework',
        'channel' => 'In-App',
        'subject' => 'Homework Due Reminder',
        'body' => 'Reminder: Assignment "{assignment}" in {subject} is due tomorrow ({due_date}). Please submit on time.',
        'char_limit' => 200,
        'desc' => 'Student reminder for approaching assignment deadline.'
    ],
    [
        'name' => 'Exam Schedule Published',
        'code' => 'EXAM_SCHEDULED',
        'category' => 'Examination',
        'type' => 'Examination',
        'channel' => 'In-App',
        'subject' => 'Examination Timetable Released - {exam_name}',
        'body' => 'The timetable for {exam_name} has been published. Exams commence on {date}. Please check the student portal for details.',
        'char_limit' => 300,
        'desc' => 'Alert when exam schedules are finalized.'
    ],
    [
        'name' => 'Examination Result Published',
        'code' => 'RESULT_PUBLISHED',
        'category' => 'Examination',
        'type' => 'Examination',
        'channel' => 'Email',
        'subject' => 'Term Results Published for {student_name}',
        'body' => '<p>Dear {parent_name},</p><p>We are pleased to inform you that the results for <strong>{exam_name}</strong> have been published for <strong>{student_name}</strong> (Class {class}-{section}).</p><p>You can access the report card directly from the student portal.</p><p>Regards,<br>{school_name}</p>',
        'char_limit' => NULL,
        'desc' => 'Email report card publishing alert.'
    ],
    [
        'name' => 'Staff Leave Approved',
        'code' => 'LEAVE_APPROVED',
        'category' => 'Leave',
        'type' => 'Leave',
        'channel' => 'In-App',
        'subject' => 'Leave Application Approved',
        'body' => 'Dear {staff_name}, your {leave_type} request for {date} has been APPROVED by the Principal.',
        'char_limit' => 200,
        'desc' => 'Staff notification upon leave sanction.'
    ],
    [
        'name' => 'Staff Leave Rejected',
        'code' => 'LEAVE_REJECTED',
        'category' => 'Leave',
        'type' => 'Leave',
        'channel' => 'In-App',
        'subject' => 'Leave Application Update',
        'body' => 'Dear {staff_name}, your {leave_type} request for {date} was not approved. Please consult administration.',
        'char_limit' => 200,
        'desc' => 'Staff notification upon leave disapproval.'
    ],
    [
        'name' => 'Transport Route Update',
        'code' => 'TRANSPORT_UPDATE',
        'category' => 'Transport',
        'type' => 'Transport',
        'channel' => 'SMS',
        'subject' => 'Transport Route Update',
        'body' => 'Dear {parent_name}, please note a timing update for Route {route} at Stop {stop} starting {date}. Bus will arrive at {time}. - {school_name}',
        'char_limit' => 160,
        'desc' => 'Bus route and timing changes.'
    ],
    [
        'name' => 'Certificate Ready for Pickup',
        'code' => 'CERTIFICATE_READY',
        'category' => 'Certificate',
        'type' => 'Certificate',
        'channel' => 'In-App',
        'subject' => 'Certificate Issued - {student_name}',
        'body' => 'The certificate for {student_name} (Adm: {admission_number}) has been generated and is ready for collection at the school office. - {school_name}',
        'char_limit' => 200,
        'desc' => 'Notice when student certificate is generated.'
    ]
];

foreach ($templates as $t) {
    $stmt = $mysqli->prepare("INSERT INTO tbl_communication_templates 
        (template_name, template_code, category, communication_type, channel, subject, message_template, character_limit, description, is_system, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'Active')
        ON DUPLICATE KEY UPDATE template_name = VALUES(template_name), message_template = VALUES(message_template), channel = VALUES(channel)");
    $stmt->bind_param("sssssssis", $t['name'], $t['code'], $t['category'], $t['type'], $t['channel'], $t['subject'], $t['body'], $t['char_limit'], $t['desc']);
    $stmt->execute();
}

// Seed Notification Automation Rules
$rules = [
    [
        'name' => 'Daily Student Absent SMS to Parents',
        'event' => 'Attendance Absent',
        'module' => 'Attendance',
        'code' => 'ATTENDANCE_ABSENT',
        'channel' => 'SMS',
        'recipient' => 'Parent',
        'priority' => 'Important'
    ],
    [
        'name' => 'Fee Overdue WhatsApp Notification',
        'event' => 'Fee Overdue',
        'module' => 'Fees',
        'code' => 'FEE_OVERDUE',
        'channel' => 'WhatsApp',
        'recipient' => 'Parent',
        'priority' => 'Urgent'
    ],
    [
        'name' => 'Assignment Published In-App Alert',
        'event' => 'Homework Published',
        'module' => 'Homework',
        'code' => 'HOMEWORK_ASSIGNED',
        'channel' => 'In-App',
        'recipient' => 'Student',
        'priority' => 'Normal'
    ],
    [
        'name' => 'Exam Results Published Email',
        'event' => 'Result Published',
        'module' => 'Examination',
        'code' => 'RESULT_PUBLISHED',
        'channel' => 'Email',
        'recipient' => 'Parent',
        'priority' => 'Important'
    ],
    [
        'name' => 'Staff Leave Decision Alert',
        'event' => 'Leave Approved',
        'module' => 'Leave',
        'code' => 'LEAVE_APPROVED',
        'channel' => 'In-App',
        'recipient' => 'Staff',
        'priority' => 'Normal'
    ]
];

foreach ($rules as $r) {
    $tmpl = $mysqli->query("SELECT template_id FROM tbl_communication_templates WHERE template_code = '{$r['code']}'")->fetch_assoc();
    $tmpl_id = $tmpl ? (int)$tmpl['template_id'] : 1;
    
    $check = $mysqli->query("SELECT rule_id FROM tbl_notification_rules WHERE event_name = '{$r['event']}' AND channel = '{$r['channel']}'");
    if ($check->num_rows == 0) {
        $stmt = $mysqli->prepare("INSERT INTO tbl_notification_rules 
            (rule_name, event_name, source_module, template_id, channel, recipient_type, frequency, cooldown_minutes, priority, status)
            VALUES (?, ?, ?, ?, ?, ?, 'Once per event', 60, ?, 'Active')");
        $stmt->bind_param("sssisss", $r['name'], $r['event'], $r['module'], $tmpl_id, $r['channel'], $r['recipient'], $r['priority']);
        $stmt->execute();
    }
}

// Seed sample historical and queued messages
$check_msg = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_messages");
if ($check_msg->fetch_assoc()['cnt'] < 5) {
    $mysqli->query("INSERT INTO tbl_communication_messages 
    (event_name, source_module, source_ref_id, channel, template_id, template_code, recipient_type, recipient_id, recipient_name, recipient_contact, subject, message, rendered_message, priority, status, sent_at, delivered_at) VALUES
    ('Attendance Absent', 'Attendance', 1, 'SMS', 1, 'ATTENDANCE_ABSENT', 'Parent', 1, 'Suresh Nair', '+91 98470 11223', 'Student Absence Alert', 'Dear {parent_name}, your child {student_name} was marked ABSENT today.', 'Dear Suresh Nair, your child Aarav Nair (Adm: EDU2026001) was marked ABSENT today, 20-08-2026. Please contact the school office if this is in error. - School Public School', 'Important', 'Delivered', '2026-08-20 09:15:00', '2026-08-20 09:15:12'),
    ('Fee Overdue', 'Fees', 3, 'WhatsApp', 4, 'FEE_OVERDUE', 'Parent', 3, 'Thomas Varghese', '+91 90480 33556', 'URGENT: Outstanding Fee Overdue', 'Dear {parent_name}, fee of Rs.{amount} for {student_name} is overdue.', 'Dear Thomas Varghese, this is an urgent reminder that fee of Rs.14,500.00 for Kiran Thomas (EDU2026003) was due on 10-08-2026 and is now OVERDUE. Please clear the balance immediately.', 'Urgent', 'Sent', '2026-08-20 10:30:00', NULL),
    ('Homework Published', 'Homework', 2, 'In-App', 6, 'HOMEWORK_ASSIGNED', 'Student', 1, 'Aarav Nair', 'aarav.nair@email.com', 'New Assignment in Mathematics', 'A new assignment has been published.', 'A new assignment \"Quadratic Equations Problem Set 3\" in Mathematics has been published for Class Grade 10-A. Due date: 2026-08-25.', 'Normal', 'Delivered', '2026-08-20 11:00:00', '2026-08-20 11:00:05'),
    ('Exam Schedule Published', 'Examination', 1, 'In-App', 8, 'EXAM_SCHEDULED', 'Parent', 2, 'Ramesh Menon', '+91 94470 22314', 'Examination Timetable Released', 'The timetable for Mid-Term Exams has been published.', 'The timetable for Mid-Term Examination 2026 has been published. Exams commence on 2026-08-18. Please check the student portal for details.', 'Important', 'Pending', NULL, NULL),
    ('Transport Route Update', 'Transport', 1, 'SMS', 12, 'TRANSPORT_UPDATE', 'Parent', 1, 'Suresh Nair', '+91 98470 11223', 'Transport Route Update', 'Timing update for Route.', 'Dear Suresh Nair, please note a timing update for Route North Route - Morning at Stop Aluva Bypass starting 2026-08-22. Bus will arrive at 07:15 AM. - School Public School', 'Normal', 'Scheduled', NULL, NULL)");
}

echo "=== Communication & Notification Migration Completed Successfully! ===\n";
