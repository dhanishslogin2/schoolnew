<?php
$mysqli = new mysqli('localhost', 'root', '', 'db_school');
if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error . "\n");
}

function add_index_if_missing($mysqli, $table, $index_name, $cols) {
    $res = $mysqli->query("SHOW INDEX FROM `$table` WHERE Key_name = '$index_name'");
    if ($res && $res->num_rows > 0) {
        echo "Index `$index_name` on `$table` already exists.\n";
        return;
    }

    $col_str = implode('`, `', $cols);
    $sql = "ALTER TABLE `$table` ADD INDEX `$index_name` (`$col_str`)";
    if ($mysqli->query($sql)) {
        echo "Added index `$index_name` on `$table` (`$col_str`).\n";
    } else {
        echo "Failed to add index on `$table`: " . $mysqli->error . "\n";
    }
}

echo "=== ADDING TARGETED PERFORMANCE INDEXES ===\n";
add_index_if_missing($mysqli, 'tbl_students', 'idx_students_academic_status', ['academic_year_id', 'is_deleted', 'status']);
add_index_if_missing($mysqli, 'tbl_attendance', 'idx_attendance_date_year', ['attendance_date', 'academic_year_id', 'is_deleted']);
add_index_if_missing($mysqli, 'tbl_student_fees', 'idx_student_fees_year_status', ['academic_year_id', 'is_deleted', 'payment_status']);
add_index_if_missing($mysqli, 'tbl_staff', 'idx_staff_status_deleted', ['status', 'is_deleted', 'staff_type']);
add_index_if_missing($mysqli, 'tbl_classes', 'idx_classes_year_status', ['academic_year_id', 'status', 'is_deleted']);
add_index_if_missing($mysqli, 'tbl_student_transport_assignments', 'idx_transport_route_status', ['route_id', 'status', 'is_deleted']);
add_index_if_missing($mysqli, 'tbl_student_transport_assignments', 'idx_transport_veh_status', ['vehicle_id', 'status', 'is_deleted']);
add_index_if_missing($mysqli, 'tbl_route_stops', 'idx_route_stops_status', ['route_id', 'status', 'is_deleted']);
add_index_if_missing($mysqli, 'tbl_exam_marks', 'idx_exam_marks_year_exam', ['academic_year_id', 'exam_id', 'is_deleted']);
add_index_if_missing($mysqli, 'tbl_users', 'idx_users_status_deleted', ['status', 'is_deleted']);

echo "=== INDEX CREATION COMPLETED ===\n";
