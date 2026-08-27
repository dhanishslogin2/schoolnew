<?php
// Comprehensive Integration Test Suite for Communication Module

$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("DB Connection failed: " . $mysqli->connect_error . "\n");
}

echo "=== STARTING COMMUNICATION MODULE TEST SUITE ===\n\n";

$passCount = 0;
$testCount = 0;

function assertTest($description, $condition) {
    global $passCount, $testCount;
    $testCount++;
    if ($condition) {
        $passCount++;
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
    }
}

// TEST 1: Database Tables & Default Data
echo "1. Testing Database Schema & Configurations:\n";
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($r = $res->fetch_row()) $tables[] = $r[0];

assertTest("tbl_notices exists", in_array('tbl_notices', $tables));
assertTest("tbl_announcements exists", in_array('tbl_announcements', $tables));
assertTest("tbl_communication_templates exists", in_array('tbl_communication_templates', $tables));
assertTest("tbl_communication_messages exists", in_array('tbl_communication_messages', $tables));
assertTest("tbl_conversations exists", in_array('tbl_conversations', $tables));
assertTest("tbl_conversation_participants exists", in_array('tbl_conversation_participants', $tables));
assertTest("tbl_messages exists", in_array('tbl_messages', $tables));
assertTest("tbl_communication_groups exists", in_array('tbl_communication_groups', $tables));
assertTest("tbl_communication_settings exists", in_array('tbl_communication_settings', $tables));
assertTest("tbl_communication_audit_logs exists", in_array('tbl_communication_audit_logs', $tables));

$tmpl_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_templates")->fetch_assoc()['cnt'];
assertTest("Notification templates seeded ({$tmpl_cnt} templates)", $tmpl_cnt >= 4);

// TEST 2: Notice Creation & Audience Targeting
echo "\n2. Testing Notice Creation & Targeting:\n";
$notice_title = "Annual Sports Meet Circular " . time();
$mysqli->query("INSERT INTO tbl_notices (
    academic_year_id, category, title, content, target_role, target_type, class_id, priority, publish_date, posted_by, status
) VALUES (
    1, 'Event', '{$notice_title}', 'All students must assemble at the main ground.', 'All', 'Entire School', 1, 'Important', CURDATE(), 'Principal', 'Published'
)");
$notice_id = $mysqli->insert_id;
assertTest("Notice published with target 'Entire School' (ID: {$notice_id})", $notice_id > 0);

// TEST 3: Announcement Creation
echo "\n3. Testing Announcement Publishing:\n";
$ann_title = "School Reopening Schedule " . time();
$mysqli->query("INSERT INTO tbl_announcements (
    title, category, content, audience, target_role, announcement_date, priority, posted_by, status
) VALUES (
    '{$ann_title}', 'General', 'Academic term begins on Monday.', 'Whole School', 'All', CURDATE(), 'Urgent', 'Principal', 'Published'
)");
$ann_id = $mysqli->insert_id;
assertTest("Urgent Announcement created (ID: {$ann_id})", $ann_id > 0);

// TEST 4: Dynamic Template Variable Parsing
echo "\n4. Testing Template Variable Parsing:\n";
$tmpl_str = "Dear {parent_name}, your child {student_name} has a pending fee of Rs. {amount} due on {due_date}. - {school_name}";

foreach ($vars as $k => $v) {
    $tmpl_str = str_ireplace($k, $v, $tmpl_str);
}
assertTest("Dynamic variables resolved correctly", strpos($tmpl_str, 'Alice Doe') !== false && strpos($tmpl_str, 'Rs. 4500') !== false);

// TEST 5: Multi-Channel Dispatching (SMS, WhatsApp, Email)
echo "\n5. Testing Multi-Channel Message Dispatches:\n";
$mysqli->query("INSERT INTO tbl_communication_messages (
    source_module, channel, recipient_type, recipient_name, recipient_contact, message, status, sent_at
) VALUES (
    'Direct', 'SMS', 'Individual', 'John Doe', '+919876543210', '{$tmpl_str}', 'Sent', NOW()
)");
$sms_msg_id = $mysqli->insert_id;

$mysqli->query("INSERT INTO tbl_communication_messages (
    source_module, channel, recipient_type, recipient_name, recipient_contact, message, status, sent_at
) VALUES (
    'Direct', 'WhatsApp', 'All Parents', 'Class 10 Parents', '+919876543210', 'WhatsApp alert test', 'Delivered', NOW()
)");
$wa_msg_id = $mysqli->insert_id;

assertTest("SMS message recorded in centralized delivery log (ID: {$sms_msg_id})", $sms_msg_id > 0);
assertTest("WhatsApp message recorded in centralized delivery log (ID: {$wa_msg_id})", $wa_msg_id > 0);

// TEST 6: Scheduled Queue & Cancellation
echo "\n6. Testing Scheduled Notifications Queue:\n";
$mysqli->query("INSERT INTO tbl_communication_messages (
    source_module, channel, recipient_type, recipient_name, recipient_contact, message, scheduled_at, status
) VALUES (
    'Fees', 'Email', 'Individual', 'Parent', 'parent@example.com', 'Scheduled fee notice', DATE_ADD(NOW(), INTERVAL 2 DAY), 'Scheduled'
)");
$sched_id = $mysqli->insert_id;
assertTest("Message added to scheduled queue (ID: {$sched_id})", $sched_id > 0);

$mysqli->query("UPDATE tbl_communication_messages SET status = 'Cancelled' WHERE message_id = {$sched_id}");
$check_cancel = $mysqli->query("SELECT status FROM tbl_communication_messages WHERE message_id = {$sched_id}")->fetch_assoc();
assertTest("Scheduled message cancelled successfully", $check_cancel['status'] === 'Cancelled');

// TEST 7: Parent-Teacher Conversation & Messaging
echo "\n7. Testing Parent-Teacher Conversation Workflow:\n";
$mysqli->query("INSERT INTO tbl_conversations (conversation_type, title, created_by) VALUES ('Parent-Teacher', 'Math Progress Discussion', 1)");
$conv_id = $mysqli->insert_id;

$mysqli->query("INSERT INTO tbl_conversation_participants (conversation_id, user_id, user_type, unread_count) VALUES ({$conv_id}, 1, 'Staff', 0)");
$mysqli->query("INSERT INTO tbl_conversation_participants (conversation_id, user_id, user_type, unread_count) VALUES ({$conv_id}, 1, 'Parent', 1)");

$mysqli->query("INSERT INTO tbl_messages (conversation_id, sender_id, sender_type, message_text, status) VALUES ({$conv_id}, 1, 'Staff', 'Hello, Alice has shown great improvement in geometry.', 'Sent')");
$msg_id = $mysqli->insert_id;

assertTest("Parent-Teacher conversation created (ID: {$conv_id})", $conv_id > 0);
assertTest("Message dispatched inside conversation thread (ID: {$msg_id})", $msg_id > 0);

// TEST 8: Communication Groups & Settings
echo "\n8. Testing Groups & Settings Configuration:\n";
$grp_cnt = $mysqli->query("SELECT COUNT(*) as cnt FROM tbl_communication_groups WHERE status = 1")->fetch_assoc()['cnt'];
assertTest("Communication cohorts available ({$grp_cnt} groups)", $grp_cnt >= 1);

$set = $mysqli->query("SELECT * FROM tbl_communication_settings WHERE setting_id = 1")->fetch_assoc();
assertTest("Communication gateway settings loaded (SMS: {$set['sms_provider']}, Email: {$set['email_from_address']})", !empty($set));

// Cleanup test records
$mysqli->query("DELETE FROM tbl_messages WHERE conversation_id = {$conv_id}");
$mysqli->query("DELETE FROM tbl_conversation_participants WHERE conversation_id = {$conv_id}");
$mysqli->query("DELETE FROM tbl_conversations WHERE conversation_id = {$conv_id}");
$mysqli->query("DELETE FROM tbl_communication_messages WHERE message_id IN ({$sms_msg_id}, {$wa_msg_id}, {$sched_id})");
$mysqli->query("DELETE FROM tbl_announcements WHERE announcement_id = {$ann_id}");
$mysqli->query("DELETE FROM tbl_notices WHERE notice_id = {$notice_id}");

echo "\n=== COMMUNICATION MODULE TEST SUMMARY: {$passCount}/{$testCount} Tests Passed ===\n";
