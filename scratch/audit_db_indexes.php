<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error . "\n");
}

$tables_res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $tables_res->fetch_row()) {
    $tables[] = $row[0];
}

echo "=== DATABASE SCHEMA & INDEX AUDIT ===\n";
echo "Total Tables: " . count($tables) . "\n\n";

$missing_indexes = [];
$table_stats = [];

foreach ($tables as $t) {
    $cnt_res = $mysqli->query("SELECT COUNT(*) FROM `$t`");
    $cnt = $cnt_res ? $cnt_res->fetch_row()[0] : 0;
    
    $idx_res = $mysqli->query("SHOW INDEX FROM `$t`");
    $indexes = [];
    while ($idx = $idx_res->fetch_assoc()) {
        $indexes[$idx['Key_name']][] = $idx['Column_name'];
    }

    $col_res = $mysqli->query("SHOW COLUMNS FROM `$t`");
    $cols = [];
    while ($c = $col_res->fetch_assoc()) {
        $cols[] = $c['Field'];
    }

    $table_stats[$t] = [
        'rows' => $cnt,
        'indexes' => $indexes,
        'cols' => $cols
    ];

    // Check for common foreign keys without indexes
    $common_fk_cols = [
        'academic_year_id', 'student_id', 'staff_id', 'class_id', 
        'section_id', 'subject_id', 'user_id', 'role_id', 'exam_id',
        'fee_category_id', 'fee_structure_id', 'route_id', 'vehicle_id',
        'is_deleted', 'status'
    ];

    $indexed_first_cols = [];
    foreach ($indexes as $key_name => $idx_cols) {
        $indexed_first_cols[$idx_cols[0]] = true;
    }

    foreach ($cols as $col) {
        if (in_array($col, $common_fk_cols) && !isset($indexed_first_cols[$col])) {
            $missing_indexes[$t][] = $col;
        }
    }
}

echo "=== CANDIDATE COLUMNS FREQUENTLY FILTERED/JOINED WITHOUT LEADING INDEX ===\n";
foreach ($missing_indexes as $t => $cols) {
    echo "Table `$t` (Rows: {$table_stats[$t]['rows']}): Missing leading index on: " . implode(', ', $cols) . "\n";
}
