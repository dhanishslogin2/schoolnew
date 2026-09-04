-- Migration: Move Period Setup to Timetable & Academic Group-Based Period Management
-- Database: School Management System

-- 1. Add academic_group_id and period_type columns to tbl_periods
ALTER TABLE `tbl_periods`
  ADD COLUMN `academic_group_id` INT(10) UNSIGNED NULL AFTER `period_id`,
  ADD COLUMN `period_type` ENUM('Period', 'Break', 'Lunch Break') NOT NULL DEFAULT 'Period' AFTER `period_name`;

-- 2. Add foreign key on academic_group_id
ALTER TABLE `tbl_periods`
  ADD CONSTRAINT `fk_period_academic_group`
  FOREIGN KEY (`academic_group_id`) REFERENCES `tbl_academic_groups` (`academic_group_id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

-- 3. Drop old global unique key on period_name so each group can have Period 1, Period 2, etc.
ALTER TABLE `tbl_periods`
  DROP INDEX `uk_period_name`;

-- 4. Add index for fast group and type lookups
ALTER TABLE `tbl_periods`
  ADD INDEX `idx_period_group_type` (`academic_group_id`, `period_type`, `status`, `is_deleted`);

-- 4. Map existing +1/+2 period records (IDs 1 through 7) to Academic Group 'SS' (academic_group_id = 5)
UPDATE `tbl_periods`
SET `academic_group_id` = 5,
    `period_type` = 'Period'
WHERE `period_id` IN (1, 2, 3, 4, 5, 6, 7);

-- 5. Insert Breaks for Group 'SS' (Group ID 5)
-- Break 1: 10:30 - 10:45
INSERT INTO `tbl_periods` (`academic_group_id`, `period_number`, `period_name`, `period_type`, `start_time`, `end_time`, `period_order`, `status`, `is_deleted`)
VALUES (5, 0, 'Morning Break', 'Break', '10:30:00', '10:45:00', 3, 1, 'n');

-- Lunch Break: 12:15 - 13:00
INSERT INTO `tbl_periods` (`academic_group_id`, `period_number`, `period_name`, `period_type`, `start_time`, `end_time`, `period_order`, `status`, `is_deleted`)
VALUES (5, 0, 'Lunch Break', 'Lunch Break', '12:15:00', '13:00:00', 6, 1, 'n');

-- Adjust period_order for SS periods to accommodate breaks
UPDATE `tbl_periods` SET `period_order` = 1 WHERE `period_id` = 1; -- Period 1 (09:00 - 09:45)
UPDATE `tbl_periods` SET `period_order` = 2 WHERE `period_id` = 2; -- Period 2 (09:45 - 10:30)
-- Morning Break is order 3 (10:30 - 10:45)
UPDATE `tbl_periods` SET `period_order` = 4 WHERE `period_id` = 3; -- Period 3 (10:45 - 11:30)
UPDATE `tbl_periods` SET `period_order` = 5 WHERE `period_id` = 4; -- Period 4 (11:30 - 12:15)
-- Lunch Break is order 6 (12:15 - 13:00)
UPDATE `tbl_periods` SET `period_order` = 7 WHERE `period_id` = 5; -- Period 5 (13:00 - 13:45)
UPDATE `tbl_periods` SET `period_order` = 8 WHERE `period_id` = 6; -- Period 6 (13:45 - 14:30)
UPDATE `tbl_periods` SET `period_order` = 9 WHERE `period_id` = 7; -- Period 7 (14:30 - 15:15)

-- 6. Seed Default Initial Period Structure for Group 1: KG's (5 periods + 2 breaks)
INSERT INTO `tbl_periods` (`academic_group_id`, `period_number`, `period_name`, `period_type`, `start_time`, `end_time`, `period_order`, `status`, `is_deleted`) VALUES
(1, 1, 'Period 1', 'Period', '09:00:00', '09:45:00', 1, 1, 'n'),
(1, 2, 'Period 2', 'Period', '09:45:00', '10:30:00', 2, 1, 'n'),
(1, 0, 'Morning Break', 'Break', '10:30:00', '10:50:00', 3, 1, 'n'),
(1, 3, 'Period 3', 'Period', '10:50:00', '11:35:00', 4, 1, 'n'),
(1, 4, 'Period 4', 'Period', '11:35:00', '12:20:00', 5, 1, 'n'),
(1, 0, 'Lunch Break', 'Lunch Break', '12:20:00', '13:05:00', 6, 1, 'n'),
(1, 5, 'Period 5', 'Period', '13:05:00', '13:50:00', 7, 1, 'n');

-- 7. Seed Default Initial Period Structure for Group 2: LP (Classes 1-4: 7 periods + 2 breaks)
INSERT INTO `tbl_periods` (`academic_group_id`, `period_number`, `period_name`, `period_type`, `start_time`, `end_time`, `period_order`, `status`, `is_deleted`) VALUES
(2, 1, 'Period 1', 'Period', '09:00:00', '09:45:00', 1, 1, 'n'),
(2, 2, 'Period 2', 'Period', '09:45:00', '10:30:00', 2, 1, 'n'),
(2, 0, 'Morning Break', 'Break', '10:30:00', '10:45:00', 3, 1, 'n'),
(2, 3, 'Period 3', 'Period', '10:45:00', '11:30:00', 4, 1, 'n'),
(2, 4, 'Period 4', 'Period', '11:30:00', '12:15:00', 5, 1, 'n'),
(2, 0, 'Lunch Break', 'Lunch Break', '12:15:00', '13:00:00', 6, 1, 'n'),
(2, 5, 'Period 5', 'Period', '13:00:00', '13:45:00', 7, 1, 'n'),
(2, 6, 'Period 6', 'Period', '13:45:00', '14:30:00', 8, 1, 'n'),
(2, 7, 'Period 7', 'Period', '14:30:00', '15:15:00', 9, 1, 'n');

-- 8. Seed Default Initial Period Structure for Group 3: UP (Classes 5-7: 7 periods + 2 breaks)
INSERT INTO `tbl_periods` (`academic_group_id`, `period_number`, `period_name`, `period_type`, `start_time`, `end_time`, `period_order`, `status`, `is_deleted`) VALUES
(3, 1, 'Period 1', 'Period', '09:00:00', '09:45:00', 1, 1, 'n'),
(3, 2, 'Period 2', 'Period', '09:45:00', '10:30:00', 2, 1, 'n'),
(3, 0, 'Morning Break', 'Break', '10:30:00', '10:45:00', 3, 1, 'n'),
(3, 3, 'Period 3', 'Period', '10:45:00', '11:30:00', 4, 1, 'n'),
(3, 4, 'Period 4', 'Period', '11:30:00', '12:15:00', 5, 1, 'n'),
(3, 0, 'Lunch Break', 'Lunch Break', '12:15:00', '13:00:00', 6, 1, 'n'),
(3, 5, 'Period 5', 'Period', '13:00:00', '13:45:00', 7, 1, 'n'),
(3, 6, 'Period 6', 'Period', '13:45:00', '14:30:00', 8, 1, 'n'),
(3, 7, 'Period 7', 'Period', '14:30:00', '15:15:00', 9, 1, 'n');

-- 9. Seed Default Initial Period Structure for Group 4: HS (Classes 8-10: 8 periods + 2 breaks)
INSERT INTO `tbl_periods` (`academic_group_id`, `period_number`, `period_name`, `period_type`, `start_time`, `end_time`, `period_order`, `status`, `is_deleted`) VALUES
(4, 1, 'Period 1', 'Period', '09:00:00', '09:45:00', 1, 1, 'n'),
(4, 2, 'Period 2', 'Period', '09:45:00', '10:30:00', 2, 1, 'n'),
(4, 0, 'Morning Break', 'Break', '10:30:00', '10:45:00', 3, 1, 'n'),
(4, 3, 'Period 3', 'Period', '10:45:00', '11:30:00', 4, 1, 'n'),
(4, 4, 'Period 4', 'Period', '11:30:00', '12:15:00', 5, 1, 'n'),
(4, 0, 'Lunch Break', 'Lunch Break', '12:15:00', '13:00:00', 6, 1, 'n'),
(4, 5, 'Period 5', 'Period', '13:00:00', '13:45:00', 7, 1, 'n'),
(4, 6, 'Period 6', 'Period', '13:45:00', '14:30:00', 8, 1, 'n'),
(4, 7, 'Period 7', 'Period', '14:30:00', '15:15:00', 9, 1, 'n'),
(4, 8, 'Period 8', 'Period', '15:15:00', '16:00:00', 10, 1, 'n');
