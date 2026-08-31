<?php
define('BASEPATH', true);
define('ENVIRONMENT', 'development');
require_once __DIR__ . '/../application/config/database.php';
$db_cfg = $db['default'];
$mysqli = new mysqli($db_cfg['hostname'], $db_cfg['username'], $db_cfg['password'], $db_cfg['database']);

echo "======================================================\n";
echo "TESTING STUDENT QUERY BUILDER AND AMBIGUOUS COLUMN FIX\n";
echo "======================================================\n\n";

function run_test_query($name, $sql) {
    global $mysqli;
    echo "Testing: $name\n";
    $res = $mysqli->query($sql);
    if (!$res) {
        echo "FAILED: " . $mysqli->error . "\n\n";
        return false;
    } else {
        echo "PASSED (Returned " . $res->num_rows . " rows)\n\n";
        return true;
    }
}

// 1. All Students Query
$sql1 = "
SELECT `st`.*, `c`.`class_name`,
       COALESCE(`sec`.`section_name`, 'A') AS section_name,
       `y`.`year_name`
FROM `tbl_students` `st`
LEFT JOIN `tbl_classes` `c` ON `c`.`class_id` = `st`.`class_id`
LEFT JOIN `tbl_sections` `sec` ON `sec`.`section_id` = `st`.`section_id`
LEFT JOIN `tbl_academic_years` `y` ON `y`.`academic_year_id` = `st`.`academic_year_id`
WHERE `st`.`status` >= 0
  AND `st`.`is_deleted` = 'n'
ORDER BY `st`.`student_id` ASC
";
run_test_query("Case 1: All Students Query", $sql1);

// 2. Academic Year + Class Filter
$sql2 = "
SELECT `st`.*, `c`.`class_name`,
       COALESCE(`sec`.`section_name`, 'A') AS section_name,
       `y`.`year_name`
FROM `tbl_students` `st`
LEFT JOIN `tbl_classes` `c` ON `c`.`class_id` = `st`.`class_id`
LEFT JOIN `tbl_sections` `sec` ON `sec`.`section_id` = `st`.`section_id`
LEFT JOIN `tbl_academic_years` `y` ON `y`.`academic_year_id` = `st`.`academic_year_id`
WHERE `st`.`status` >= 0
  AND `st`.`is_deleted` = 'n'
  AND `st`.`academic_year_id` = 1
  AND `st`.`class_id` = 1
ORDER BY `st`.`student_id` ASC
";
run_test_query("Case 2: Academic Year + Class Filter", $sql2);

// 3. Academic Year + Class + Section A Filter (with NULL/0 fallback)
$sql3 = "
SELECT `st`.*, `c`.`class_name`,
       COALESCE(`sec`.`section_name`, 'A') AS section_name,
       `y`.`year_name`
FROM `tbl_students` `st`
LEFT JOIN `tbl_classes` `c` ON `c`.`class_id` = `st`.`class_id`
LEFT JOIN `tbl_sections` `sec` ON `sec`.`section_id` = `st`.`section_id`
LEFT JOIN `tbl_academic_years` `y` ON `y`.`academic_year_id` = `st`.`academic_year_id`
WHERE `st`.`status` >= 0
  AND `st`.`is_deleted` = 'n'
  AND `st`.`academic_year_id` = 1
  AND `st`.`class_id` = 1
  AND (`st`.`section_id` = 12 OR `st`.`section_id` IS NULL OR `st`.`section_id` = 0)
ORDER BY `st`.`student_id` ASC
";
run_test_query("Case 3: Academic Year + Class + Section A Filter", $sql3);

// 4. Search Filter with Grouping
$sql4 = "
SELECT `st`.*, `c`.`class_name`,
       COALESCE(`sec`.`section_name`, 'A') AS section_name,
       `y`.`year_name`
FROM `tbl_students` `st`
LEFT JOIN `tbl_classes` `c` ON `c`.`class_id` = `st`.`class_id`
LEFT JOIN `tbl_sections` `sec` ON `sec`.`section_id` = `st`.`section_id`
LEFT JOIN `tbl_academic_years` `y` ON `y`.`academic_year_id` = `st`.`academic_year_id`
WHERE `st`.`status` >= 0
  AND `st`.`is_deleted` = 'n'
  AND (`st`.`first_name` LIKE '%a%' OR `st`.`last_name` LIKE '%a%' OR `st`.`admission_number` LIKE '%a%')
ORDER BY `st`.`student_id` ASC
LIMIT 10 OFFSET 0
";
run_test_query("Case 4: Search Filter Query", $sql4);

// 5. Count Query
$sql5 = "
SELECT COUNT(*) as total
FROM `tbl_students` `st`
WHERE `st`.`status` >= 0
  AND `st`.`is_deleted` = 'n'
  AND `st`.`academic_year_id` = 1
  AND `st`.`class_id` = 1
  AND (`st`.`section_id` = 12 OR `st`.`section_id` IS NULL OR `st`.`section_id` = 0)
";
run_test_query("Case 5: Count Filtered Query", $sql5);
