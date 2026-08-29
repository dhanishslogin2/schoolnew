-- ============================================================================
-- Wizard Enhancement Migration
-- School Management Software
-- Run once on database to support enhanced Step 2 Academic Details
-- Safe to run multiple times (uses IF NOT EXISTS / ALTER IGNORE).
-- ============================================================================

-- Add is_deleted to tbl_students if not present
SET @col_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'tbl_students'
      AND COLUMN_NAME  = 'is_deleted'
);
SET @sql = IF(@col_exists = 0,
    "ALTER TABLE `tbl_students` ADD COLUMN `is_deleted` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `status`",
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tbl_student_previous_school -------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_student_previous_school` (
  `prev_school_id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id`             INT UNSIGNED NOT NULL,
  `school_name`            VARCHAR(200) NOT NULL,
  `school_address`         TEXT NULL,
  `school_board`           VARCHAR(100) NULL,
  `previous_class`         VARCHAR(50)  NULL,
  `previous_academic_year` VARCHAR(50)  NULL,
  `date_of_leaving`        DATE         NULL,
  `reason_for_leaving`     TEXT         NULL,
  `tc_number`              VARCHAR(100) NULL,
  `tc_document_id`         INT UNSIGNED NULL,
  `previous_percentage`    DECIMAL(5,2) NULL,
  `status`                 TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`             DATETIME     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`prev_school_id`),
  KEY `idx_psch_student` (`student_id`),
  CONSTRAINT `fk_psch_student`
      FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- tbl_student_activities ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_student_activities` (
  `activity_id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id`      INT UNSIGNED NOT NULL,
  `category`        ENUM('Academic','Extracurricular') NOT NULL DEFAULT 'Academic',
  `activity_type`   VARCHAR(100) NOT NULL,
  `activity_name`   VARCHAR(200) NOT NULL,
  `description`     TEXT         NULL,
  `level`           VARCHAR(100) NULL,
  `position_result` VARCHAR(200) NULL,
  `year`            SMALLINT UNSIGNED NULL,
  `status`          TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME     NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_id`),
  KEY `idx_act_student`  (`student_id`),
  KEY `idx_act_category` (`category`),
  CONSTRAINT `fk_act_student`
      FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
