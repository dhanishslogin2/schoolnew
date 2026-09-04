-- =========================================================================
-- MIGRATION: ACADEMIC GROUP -> CLASS -> DIVISION STRUCTURE
-- =========================================================================

-- 1. Create tbl_academic_groups table
CREATE TABLE IF NOT EXISTS `tbl_academic_groups` (
  `academic_group_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `display_order` INT(11) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` ENUM('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`academic_group_id`),
  KEY `idx_group_status` (`status`, `is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Populate default academic groups
INSERT INTO `tbl_academic_groups` (`academic_group_id`, `group_name`, `description`, `display_order`, `status`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, "KG's", "Kindergarten (LKG & UKG)", 1, 1, NOW(), NOW(), 'n'),
(2, "LP", "Lower Primary (Classes 1, 2, 3, 4)", 2, 1, NOW(), NOW(), 'n'),
(3, "UP", "Upper Primary (Classes 5, 6, 7)", 3, 1, NOW(), NOW(), 'n'),
(4, "HS", "High School (Classes 8, 9, 10)", 4, 1, NOW(), NOW(), 'n'),
(5, "SS", "Senior Secondary (+1 & +2 / Classes 11 & 12)", 5, 1, NOW(), NOW(), 'n')
ON DUPLICATE KEY UPDATE `group_name` = VALUES(`group_name`), `description` = VALUES(`description`), `display_order` = VALUES(`display_order`), `status` = 1, `is_deleted` = 'n';

-- 3. Create tbl_academic_group_classes master table (database-driven class options per group)
CREATE TABLE IF NOT EXISTS `tbl_academic_group_classes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_group_id` INT(11) UNSIGNED NOT NULL,
  `class_name` VARCHAR(50) NOT NULL,
  `display_order` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_group_class_group` (`academic_group_id`),
  CONSTRAINT `fk_group_class_group` FOREIGN KEY (`academic_group_id`) REFERENCES `tbl_academic_groups` (`academic_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pre-seed allowed class names per academic group
INSERT INTO `tbl_academic_group_classes` (`academic_group_id`, `class_name`, `display_order`) VALUES
(1, 'LKG', 1),
(1, 'UKG', 2),
(2, '1', 1),
(2, '2', 2),
(2, '3', 3),
(2, '4', 4),
(3, '5', 1),
(3, '6', 2),
(3, '7', 3),
(4, '8', 1),
(4, '9', 2),
(4, '10', 3),
(5, '11', 1),
(5, '12', 2);

-- 4. Add academic_group_id column to tbl_classes if it does not already exist
SET @col_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'tbl_classes' 
    AND COLUMN_NAME = 'academic_group_id'
);

SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `tbl_classes` ADD COLUMN `academic_group_id` INT(11) UNSIGNED DEFAULT NULL AFTER `academic_year_id`',
  'SELECT "Column academic_group_id already exists in tbl_classes"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index/FK for academic_group_id
SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
  WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'tbl_classes' 
    AND CONSTRAINT_NAME = 'fk_class_academic_group'
);

SET @sql_fk = IF(@fk_exists = 0,
  'ALTER TABLE `tbl_classes` ADD CONSTRAINT `fk_class_academic_group` FOREIGN KEY (`academic_group_id`) REFERENCES `tbl_academic_groups` (`academic_group_id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT "Constraint fk_class_academic_group already exists"'
);
PREPARE stmt_fk FROM @sql_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;

-- 5. Map existing classes to their Academic Groups
-- LKG (ID 1) -> KG's (ID 1)
UPDATE `tbl_classes` SET `academic_group_id` = 1 WHERE `class_name` LIKE '%LKG%' OR `class_id` = 1;

-- UKG (ID 13) -> KG's (ID 1)
UPDATE `tbl_classes` SET `academic_group_id` = 1 WHERE `class_name` LIKE '%UKG%' OR `class_id` = 13;

-- Classes 1-4 -> LP (ID 2)
UPDATE `tbl_classes` SET `academic_group_id` = 2 
WHERE `class_name` IN ('1', '2', '3', '4', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4');

-- Classes 5-7 -> UP (ID 3)
UPDATE `tbl_classes` SET `academic_group_id` = 3 
WHERE `class_name` IN ('5', '6', '7', 'Class 5', 'Class 6', 'Class 7', 'Grade 5', 'Grade 6', 'Grade 7');

-- Classes 8-10 -> HS (ID 4)
UPDATE `tbl_classes` SET `academic_group_id` = 4 
WHERE `class_name` IN ('8', '9', '10', 'Class 8', 'Class 9', 'Class 10', 'Grade 8', 'Grade 9', 'Grade 10') OR `class_id` = 8;

-- Classes 11-12 (+1, +2) -> SS (ID 5)
UPDATE `tbl_classes` SET `academic_group_id` = 5 
WHERE `class_name` IN ('11', '12', 'Class 11', 'Class 12', 'Grade 11', 'Grade 12', '+1', '+2', 'Plus One', 'Plus Two') OR `class_id` IN (9, 10);
