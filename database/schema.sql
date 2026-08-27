-- ============================================================================
-- School Management Software - Relational Database Schema
-- Database: db_school
-- Engine: InnoDB | Character Set: utf8mb4 | Collation: utf8mb4_unicode_ci
-- All tables strictly follow the 'tbl_' prefix convention.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `db_school` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_school`;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. Table: tbl_roles
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_roles`;
CREATE TABLE `tbl_roles` (
  `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(50) NOT NULL,
  `role_code` VARCHAR(50) NOT NULL,
  `user_type` VARCHAR(50) NOT NULL DEFAULT 'Staff',
  `description` TEXT NULL,
  `is_system` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uk_role_name` (`role_name`),
  UNIQUE KEY `uk_role_code` (`role_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_permissions`;
CREATE TABLE `tbl_permissions` (
  `permission_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` VARCHAR(50) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `permission_key` VARCHAR(100) NOT NULL,
  `permission_name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `uk_perm_key` (`permission_key`),
  KEY `idx_perm_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_role_permissions`;
CREATE TABLE `tbl_role_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_perm` (`role_id`, `permission_id`),
  KEY `idx_rp_role` (`role_id`),
  KEY `idx_rp_perm` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_user_permissions`;
CREATE TABLE `tbl_user_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `override_type` ENUM('Grant', 'Revoke') NOT NULL DEFAULT 'Grant',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_perm` (`user_id`, `permission_id`),
  KEY `idx_up_user` (`user_id`),
  KEY `idx_up_perm` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Table: tbl_departments
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_departments`;
CREATE TABLE `tbl_departments` (
  `department_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_name` VARCHAR(100) NOT NULL,
  `head_of_department_id` INT UNSIGNED NULL,
  `description` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `uk_department_name` (`department_name`),
  KEY `idx_dept_head` (`head_of_department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Table: tbl_designations
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_designations`;
CREATE TABLE `tbl_designations` (
  `designation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `designation_name` VARCHAR(100) NOT NULL,
  `category` ENUM('Administration', 'Teaching', 'Finance', 'Support') NOT NULL DEFAULT 'Teaching',
  `description` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`designation_id`),
  UNIQUE KEY `uk_designation_name` (`designation_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Table: tbl_staff
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_staff`;
CREATE TABLE `tbl_staff` (
  `staff_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_code` VARCHAR(50) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `alternate_phone` VARCHAR(20) NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
  `date_of_birth` DATE NULL,
  `blood_group` VARCHAR(10) NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Teacher',
  `staff_type` ENUM('teacher', 'non_teaching') NOT NULL DEFAULT 'teacher',
  `department_id` INT UNSIGNED NULL,
  `designation_id` INT UNSIGNED NULL,
  `joining_date` DATE NOT NULL,
  `salary` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `qualification` VARCHAR(100) NULL,
  `experience` VARCHAR(50) NULL,
  `specialization` VARCHAR(100) NULL,
  `employment_status` ENUM('Active', 'On Leave', 'Probation', 'Resigned', 'Suspended') NOT NULL DEFAULT 'Active',
  `address` TEXT NULL,
  `photo` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `uk_employee_code` (`employee_code`),
  KEY `idx_staff_department` (`department_id`),
  KEY `idx_staff_designation` (`designation_id`),
  CONSTRAINT `fk_staff_department` FOREIGN KEY (`department_id`) REFERENCES `tbl_departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_staff_designation` FOREIGN KEY (`designation_id`) REFERENCES `tbl_designations` (`designation_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4b. Table: tbl_staff_documents
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_staff_documents`;
CREATE TABLE `tbl_staff_documents` (
  `document_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(100) NOT NULL,
  `document_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `idx_staff_doc` (`staff_id`),
  CONSTRAINT `fk_staff_doc` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4c. Table: tbl_teacher_workload
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_teacher_workload`;
CREATE TABLE `tbl_teacher_workload` (
  `workload_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NULL,
  `periods` INT UNSIGNED NOT NULL DEFAULT 5,
  `working_days` VARCHAR(100) NOT NULL DEFAULT 'Mon,Tue,Wed,Thu,Fri',
  `remarks` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`workload_id`),
  KEY `idx_workload_staff` (`staff_id`),
  KEY `idx_workload_class` (`class_id`),
  KEY `idx_workload_subject` (`subject_id`),
  CONSTRAINT `fk_workload_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4d. Table: tbl_staff_attendance
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_staff_attendance`;
CREATE TABLE `tbl_staff_attendance` (
  `attendance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` INT UNSIGNED NOT NULL,
  `attendance_date` DATE NOT NULL,
  `attendance_status` ENUM('Present', 'Absent', 'Leave', 'Half Day') NOT NULL DEFAULT 'Present',
  `remarks` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `uk_staff_att_date` (`staff_id`, `attendance_date`),
  KEY `idx_staff_att_staff` (`staff_id`),
  CONSTRAINT `fk_staff_att` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4e. Table: tbl_staff_leave
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_staff_leave`;
CREATE TABLE `tbl_staff_leave` (
  `leave_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `staff_id` INT UNSIGNED NOT NULL,
  `leave_type` VARCHAR(50) NOT NULL,
  `from_date` DATE NOT NULL,
  `to_date` DATE NOT NULL,
  `total_days` INT UNSIGNED NOT NULL DEFAULT 1,
  `reason` TEXT NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `applied_date` DATE NOT NULL,
  `approved_by` INT UNSIGNED NULL,
  `remarks` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`leave_id`),
  KEY `idx_staff_leave_staff` (`staff_id`),
  CONSTRAINT `fk_staff_leave` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Table: tbl_users
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE `tbl_users` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` INT UNSIGNED NOT NULL,
  `username` VARCHAR(100) NULL,
  `user_type` VARCHAR(50) NOT NULL DEFAULT 'Admin',
  `staff_id` INT UNSIGNED NULL,
  `student_id` INT UNSIGNED NULL,
  `parent_id` INT UNSIGNED NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `avatar` VARCHAR(255) NULL,
  `password` VARCHAR(255) NOT NULL,
  `failed_login_attempts` INT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  `last_login_at` DATETIME NULL,
  `status` ENUM('Active', 'Inactive', 'Suspended', 'Locked') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uk_user_email` (`email`),
  UNIQUE KEY `uk_user_username` (`username`),
  KEY `idx_user_role` (`role_id`),
  KEY `idx_user_staff` (`staff_id`),
  KEY `idx_user_student` (`student_id`),
  KEY `idx_user_status` (`status`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`role_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_user_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_user_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_parent_students`;
CREATE TABLE `tbl_parent_students` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_user_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `relationship` VARCHAR(50) NOT NULL DEFAULT 'Parent',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_parent_student` (`parent_user_id`, `student_id`),
  KEY `idx_ps_parent` (`parent_user_id`),
  KEY `idx_ps_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_user_login_activity`;
CREATE TABLE `tbl_user_login_activity` (
  `activity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `username` VARCHAR(100) NOT NULL,
  `ip_address` VARCHAR(50) NULL,
  `user_agent` VARCHAR(255) NULL,
  `status` ENUM('Successful', 'Failed', 'Locked') NOT NULL DEFAULT 'Successful',
  `failure_reason` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_id`),
  KEY `idx_la_user` (`user_id`),
  KEY `idx_la_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_permission_audit_logs`;
CREATE TABLE `tbl_permission_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `target_type` ENUM('Role', 'User', 'Permission', 'Security') NOT NULL DEFAULT 'Role',
  `target_id` INT UNSIGNED NOT NULL,
  `previous_value` TEXT NULL,
  `new_value` TEXT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_pal_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_user_security_settings`;
CREATE TABLE `tbl_user_security_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `max_failed_attempts` INT UNSIGNED NOT NULL DEFAULT 5,
  `lockout_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 30,
  `session_timeout_minutes` INT UNSIGNED NOT NULL DEFAULT 120,
  `password_min_length` INT UNSIGNED NOT NULL DEFAULT 8,
  `require_special_chars` TINYINT(1) NOT NULL DEFAULT 1,
  `require_numbers` TINYINT(1) NOT NULL DEFAULT 1,
  `password_expiry_days` INT UNSIGNED NOT NULL DEFAULT 90,
  `allow_concurrent_sessions` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Table: tbl_academic_years
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_academic_years`;
CREATE TABLE `tbl_academic_years` (
  `academic_year_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `year_name` VARCHAR(50) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Only one row should be 1',
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`academic_year_id`),
  UNIQUE KEY `uk_academic_year_name` (`year_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Table: tbl_classes
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_classes`;
CREATE TABLE `tbl_classes` (
  `class_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_name` VARCHAR(50) NOT NULL,
  `class_code` VARCHAR(20) NOT NULL,
  `class_teacher_id` INT UNSIGNED NULL,
  `capacity` INT UNSIGNED NOT NULL DEFAULT 40,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`class_id`),
  KEY `idx_class_academic_year` (`academic_year_id`),
  KEY `idx_class_teacher` (`class_teacher_id`),
  CONSTRAINT `fk_class_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_class_teacher` FOREIGN KEY (`class_teacher_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Table: tbl_sections
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_sections`;
CREATE TABLE `tbl_sections` (
  `section_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_id` INT UNSIGNED NOT NULL,
  `section_name` VARCHAR(20) NOT NULL,
  `class_teacher_id` INT UNSIGNED NULL,
  `room_no` VARCHAR(50) NULL,
  `capacity` INT UNSIGNED NOT NULL DEFAULT 40,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`section_id`),
  KEY `idx_section_class` (`class_id`),
  KEY `idx_section_teacher` (`class_teacher_id`),
  CONSTRAINT `fk_section_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_section_teacher` FOREIGN KEY (`class_teacher_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. Table: tbl_subjects
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_subjects`;
CREATE TABLE `tbl_subjects` (
  `subject_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_id` INT UNSIGNED NULL,
  `subject_name` VARCHAR(100) NOT NULL,
  `subject_code` VARCHAR(30) NOT NULL,
  `subject_type` ENUM('Core', 'Elective', 'Language', 'Practical') NOT NULL DEFAULT 'Core',
  `teacher_id` INT UNSIGNED NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`subject_id`),
  KEY `idx_subject_class` (`class_id`),
  KEY `idx_subject_teacher` (`teacher_id`),
  CONSTRAINT `fk_subject_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_subject_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9b. Table: tbl_class_teachers
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_class_teachers`;
CREATE TABLE `tbl_class_teachers` (
  `class_teacher_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `staff_id` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`class_teacher_id`),
  UNIQUE KEY `uk_ct_year_sec` (`academic_year_id`, `class_id`, `section_id`),
  KEY `idx_ct_staff` (`staff_id`),
  CONSTRAINT `fk_ct_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ct_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ct_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ct_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9c. Table: tbl_subject_teachers
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_subject_teachers`;
CREATE TABLE `tbl_subject_teachers` (
  `subject_teacher_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `staff_id` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`subject_teacher_id`),
  UNIQUE KEY `uk_st_assignment` (`academic_year_id`, `class_id`, `section_id`, `subject_id`, `staff_id`),
  KEY `idx_st_subject` (`subject_id`),
  KEY `idx_st_staff` (`staff_id`),
  CONSTRAINT `fk_st_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_subject` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9d. Table: tbl_periods
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_periods`;
CREATE TABLE `tbl_periods` (
  `period_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `period_name` VARCHAR(50) NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `period_order` INT UNSIGNED NOT NULL DEFAULT 1,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`period_id`),
  UNIQUE KEY `uk_period_name` (`period_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9e. Table: tbl_timetable
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_timetable`;
CREATE TABLE `tbl_timetable` (
  `timetable_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `day` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL,
  `period_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `teacher_id` INT UNSIGNED NOT NULL,
  `room_no` VARCHAR(50) NULL,
  `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`timetable_id`),
  UNIQUE KEY `uk_class_day_period` (`academic_year_id`, `class_id`, `section_id`, `day`, `period_id`),
  KEY `idx_tt_teacher` (`teacher_id`),
  KEY `idx_tt_period` (`period_id`),
  KEY `idx_tt_subject` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9f. Table: tbl_subject_allocations
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_subject_allocations`;
CREATE TABLE `tbl_subject_allocations` (
  `allocation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `teacher_id` INT UNSIGNED NULL,
  `weekly_periods_target` INT UNSIGNED NOT NULL DEFAULT 5,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`allocation_id`),
  UNIQUE KEY `uk_class_sec_sub` (`academic_year_id`, `class_id`, `section_id`, `subject_id`),
  KEY `idx_alloc_teacher` (`teacher_id`),
  KEY `idx_alloc_subject` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9g. Table: tbl_timetable_publish
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_timetable_publish`;
CREATE TABLE `tbl_timetable_publish` (
  `publish_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `status` ENUM('Draft', 'Published', 'Locked') NOT NULL DEFAULT 'Draft',
  `published_at` DATETIME NULL,
  `published_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`publish_id`),
  UNIQUE KEY `uk_tt_publish_class` (`academic_year_id`, `class_id`, `section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9h. Table: tbl_teacher_substitutions
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_teacher_substitutions`;
CREATE TABLE `tbl_teacher_substitutions` (
  `substitution_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `timetable_id` INT UNSIGNED NOT NULL,
  `substitution_date` DATE NOT NULL,
  `original_teacher_id` INT UNSIGNED NOT NULL,
  `substitute_teacher_id` INT UNSIGNED NOT NULL,
  `reason` VARCHAR(255) NULL,
  `status` ENUM('Scheduled', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`substitution_id`),
  KEY `idx_sub_tt` (`timetable_id`),
  KEY `idx_sub_date` (`substitution_date`),
  KEY `idx_sub_orig` (`original_teacher_id`),
  KEY `idx_sub_subst` (`substitute_teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9i. Table: tbl_timetable_settings
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_timetable_settings`;
CREATE TABLE `tbl_timetable_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `working_days` VARCHAR(255) NOT NULL DEFAULT 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
  `max_periods_per_day` INT UNSIGNED NOT NULL DEFAULT 8,
  `max_consecutive_periods` INT UNSIGNED NOT NULL DEFAULT 3,
  `allow_teacher_overlap` TINYINT(1) NOT NULL DEFAULT 0,
  `auto_publish` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 10. Table: tbl_students
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_students`;
CREATE TABLE `tbl_students` (
  `student_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admission_number` VARCHAR(50) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `middle_name` VARCHAR(50) NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
  `date_of_birth` DATE NOT NULL,
  `blood_group` VARCHAR(10) NULL,
  `nationality` VARCHAR(50) NOT NULL DEFAULT 'Indian',
  `religion` VARCHAR(50) NULL,
  `address` TEXT NULL,
  `guardian_name` VARCHAR(100) NOT NULL,
  `guardian_relation` VARCHAR(50) NULL DEFAULT 'Father',
  `guardian_phone` VARCHAR(20) NOT NULL,
  `guardian_email` VARCHAR(100) NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `roll_number` VARCHAR(20) NULL,
  `photo` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive, 2=Pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `uk_admission_number` (`admission_number`),
  KEY `idx_student_academic_year` (`academic_year_id`),
  KEY `idx_student_class` (`class_id`),
  KEY `idx_student_section` (`section_id`),
  CONSTRAINT `fk_student_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_student_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_student_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 11. Table: tbl_student_documents
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_student_documents`;
CREATE TABLE `tbl_student_documents` (
  `document_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(100) NOT NULL,
  `document_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `idx_doc_student` (`student_id`),
  CONSTRAINT `fk_doc_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 11b. Table: tbl_student_promotions
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_student_promotions`;
CREATE TABLE `tbl_student_promotions` (
  `promotion_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `from_academic_year_id` INT UNSIGNED NOT NULL,
  `from_class_id` INT UNSIGNED NOT NULL,
  `from_section_id` INT UNSIGNED NOT NULL,
  `to_academic_year_id` INT UNSIGNED NOT NULL,
  `to_class_id` INT UNSIGNED NOT NULL,
  `to_section_id` INT UNSIGNED NOT NULL,
  `promotion_date` DATE NOT NULL,
  `promotion_type` ENUM('Promoted', 'Retained', 'Demoted', 'Transferred') NOT NULL DEFAULT 'Promoted',
  `remarks` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`promotion_id`),
  KEY `idx_promo_student` (`student_id`),
  CONSTRAINT `fk_promo_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 11c. Table: tbl_student_transfers
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_student_transfers`;
CREATE TABLE `tbl_student_transfers` (
  `transfer_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `tc_number` VARCHAR(50) NOT NULL,
  `transfer_date` DATE NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `previous_class_id` INT UNSIGNED NULL,
  `academic_year_id` INT UNSIGNED NULL,
  `conduct` VARCHAR(50) NOT NULL DEFAULT 'Good',
  `dues_cleared` TINYINT(1) NOT NULL DEFAULT 1,
  `status` ENUM('Requested', 'Approved', 'Issued', 'Cancelled') NOT NULL DEFAULT 'Issued',
  `remarks` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transfer_id`),
  UNIQUE KEY `uk_tc_number` (`tc_number`),
  KEY `idx_transfer_student` (`student_id`),
  CONSTRAINT `fk_transfer_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 11d. Table: tbl_admissions
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_admissions`;
CREATE TABLE `tbl_admissions` (
  `admission_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_number` VARCHAR(50) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
  `date_of_birth` DATE NOT NULL,
  `blood_group` VARCHAR(10) NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NULL,
  `guardian_name` VARCHAR(100) NOT NULL,
  `guardian_relation` VARCHAR(50) NULL DEFAULT 'Father',
  `guardian_phone` VARCHAR(20) NOT NULL,
  `guardian_email` VARCHAR(100) NULL,
  `address` TEXT NULL,
  `application_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Admitted', 'Rejected', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `student_id` INT UNSIGNED NULL,
  `remarks` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`admission_id`),
  UNIQUE KEY `uk_app_number` (`application_number`),
  KEY `idx_admission_student` (`student_id`),
  KEY `idx_admission_class` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 12. Table: tbl_attendance
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_attendance`;
CREATE TABLE `tbl_attendance` (
  `attendance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `attendance_date` DATE NOT NULL,
  `attendance_type` ENUM('Daily', 'Period-wise') NOT NULL DEFAULT 'Daily',
  `period_id` INT UNSIGNED NULL,
  `attendance_status` ENUM('Present', 'Absent', 'Late', 'Excused', 'Leave') NOT NULL DEFAULT 'Present',
  `remarks` VARCHAR(255) NULL,
  `marked_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attendance_id`),
  KEY `idx_student_date_type_period` (`student_id`, `attendance_date`, `attendance_type`, `period_id`),
  KEY `idx_att_date_class_sec` (`attendance_date`, `class_id`, `section_id`),
  KEY `idx_att_student` (`student_id`),
  KEY `idx_att_period` (`period_id`),
  CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_att_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_att_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_att_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 12b. Table: tbl_attendance_notifications
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_attendance_notifications`;
CREATE TABLE `tbl_attendance_notifications` (
  `notification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `parent_name` VARCHAR(100) NULL,
  `parent_phone` VARCHAR(20) NULL,
  `parent_email` VARCHAR(100) NULL,
  `attendance_id` INT UNSIGNED NULL,
  `attendance_date` DATE NOT NULL,
  `notification_type` ENUM('Absent', 'Late', 'Excused', 'Attendance Summary') NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('Pending', 'Sent', 'Failed') NOT NULL DEFAULT 'Pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `idx_notif_student` (`student_id`),
  KEY `idx_notif_date` (`attendance_date`),
  KEY `idx_notif_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 12c. Table: tbl_attendance_settings
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_attendance_settings`;
CREATE TABLE `tbl_attendance_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_present` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_absent` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_late` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_excused` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_period_attendance` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_absent_notification` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_late_notification` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_summary_notification` TINYINT(1) NOT NULL DEFAULT 1,
  `absent_template` TEXT NULL,
  `late_template` TEXT NULL,
  `excused_template` TEXT NULL,
  `summary_template` TEXT NULL,
  `notification_timing` VARCHAR(50) NOT NULL DEFAULT 'On Marking',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 13. Table: tbl_fee_heads
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_fee_heads`;
CREATE TABLE `tbl_fee_heads` (
  `fee_head_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `head_name` VARCHAR(100) NOT NULL,
  `category_code` VARCHAR(20) NULL,
  `description` TEXT NULL,
  `applicable_to` VARCHAR(50) NOT NULL DEFAULT 'All Students',
  `frequency` ENUM('One Time', 'Monthly', 'Quarterly', 'Half Yearly', 'Yearly', 'Custom') NOT NULL DEFAULT 'Yearly',
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fee_head_id`),
  UNIQUE KEY `uk_fee_head_name` (`head_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 14. Table: tbl_fee_structures
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_fee_structures`;
CREATE TABLE `tbl_fee_structures` (
  `fee_structure_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fee_head_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `frequency` VARCHAR(50) NOT NULL DEFAULT 'Yearly',
  `due_date` DATE NOT NULL,
  `applicable_from` DATE NULL,
  `applicable_to` DATE NULL,
  `is_optional` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fee_structure_id`),
  KEY `idx_fee_head` (`fee_head_id`),
  KEY `idx_fee_academic_year` (`academic_year_id`),
  KEY `idx_fee_class` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 15. Table: tbl_student_fees
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_student_fees`;
CREATE TABLE `tbl_student_fees` (
  `student_fee_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_no` VARCHAR(50) NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `class_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `section_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `fee_structure_id` INT UNSIGNED NOT NULL,
  `original_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `concession_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `due_date` DATE NOT NULL,
  `payment_status` ENUM('Pending', 'Partially Paid', 'Paid', 'Overdue', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `remarks` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_fee_id`),
  KEY `idx_studfee_student` (`student_id`),
  KEY `idx_studfee_structure` (`fee_structure_id`),
  KEY `idx_studfee_status` (`payment_status`),
  KEY `idx_studfee_due` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 16. Table: tbl_fee_payments
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_fee_payments`;
CREATE TABLE `tbl_fee_payments` (
  `payment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_fee_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `amount_paid` DECIMAL(10,2) NOT NULL,
  `payment_mode` ENUM('Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Card', 'Other') NOT NULL DEFAULT 'Cash',
  `transaction_reference` VARCHAR(100) NULL,
  `payment_date` DATE NOT NULL,
  `receipt_no` VARCHAR(50) NOT NULL,
  `collected_by` INT UNSIGNED NULL,
  `remarks` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `uk_receipt_no` (`receipt_no`),
  KEY `idx_pay_student_fee` (`student_fee_id`),
  KEY `idx_pay_student` (`student_id`),
  KEY `idx_pay_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 17. Table: tbl_fee_discounts
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_fee_discounts`;
CREATE TABLE `tbl_fee_discounts` (
  `discount_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `discount_type` ENUM('Percentage', 'Fixed Amount') NOT NULL DEFAULT 'Fixed Amount',
  `discount_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) NULL,
  `applicable_classes` TEXT NULL,
  `applicable_categories` TEXT NULL,
  `is_concession` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`discount_id`),
  UNIQUE KEY `uk_discount_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 18. Table: tbl_fee_adjustments
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_fee_adjustments`;
CREATE TABLE `tbl_fee_adjustments` (
  `adjustment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_fee_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `adjustment_type` ENUM('Waiver', 'Adjustment', 'Correction', 'Concession') NOT NULL,
  `previous_amount` DECIMAL(10,2) NOT NULL,
  `new_amount` DECIMAL(10,2) NOT NULL,
  `adjustment_amount` DECIMAL(10,2) NOT NULL,
  `reason` TEXT NOT NULL,
  `adjusted_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`adjustment_id`),
  KEY `idx_adj_fee` (`student_fee_id`),
  KEY `idx_adj_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 19. Table: tbl_fee_refunds
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_fee_refunds`;
CREATE TABLE `tbl_fee_refunds` (
  `refund_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_id` INT UNSIGNED NOT NULL,
  `student_fee_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `refund_amount` DECIMAL(10,2) NOT NULL,
  `refund_reason` TEXT NOT NULL,
  `refund_mode` VARCHAR(50) NOT NULL DEFAULT 'Bank Transfer',
  `approved_by` INT UNSIGNED NULL,
  `status` ENUM('Pending', 'Approved', 'Processed', 'Rejected') NOT NULL DEFAULT 'Approved',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refund_id`),
  KEY `idx_ref_pay` (`payment_id`),
  KEY `idx_ref_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 20. Table: tbl_fee_reminders
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_fee_reminders`;
CREATE TABLE `tbl_fee_reminders` (
  `reminder_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `parent_name` VARCHAR(100) NULL,
  `parent_phone` VARCHAR(20) NULL,
  `parent_email` VARCHAR(100) NULL,
  `student_fee_id` INT UNSIGNED NULL,
  `reminder_type` ENUM('Upcoming Due', 'Due Today', 'Overdue', 'Payment Confirmation') NOT NULL,
  `message` TEXT NOT NULL,
  `scheduled_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Sent', 'Failed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reminder_id`),
  KEY `idx_rem_student` (`student_id`),
  KEY `idx_rem_fee` (`student_fee_id`),
  KEY `idx_rem_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 21. Table: tbl_finance_settings
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_finance_settings`;
CREATE TABLE `tbl_finance_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_symbol` VARCHAR(10) NOT NULL DEFAULT '₹',
  `currency_code` VARCHAR(10) NOT NULL DEFAULT 'INR',
  `receipt_prefix` VARCHAR(20) NOT NULL DEFAULT 'REC-',
  `next_receipt_number` INT UNSIGNED NOT NULL DEFAULT 1001,
  `receipt_footer` TEXT NULL,
  `authorized_signature_title` VARCHAR(100) NOT NULL DEFAULT 'Accounts Officer',
  `allow_partial_payments` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_overpayment` TINYINT(1) NOT NULL DEFAULT 0,
  `require_transaction_ref` TINYINT(1) NOT NULL DEFAULT 0,
  `grace_period_days` INT UNSIGNED NOT NULL DEFAULT 7,
  `discount_approval_required` TINYINT(1) NOT NULL DEFAULT 1,
  `reminder_template_upcoming` TEXT NULL,
  `reminder_template_overdue` TEXT NULL,
  `reminder_template_payment` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 22. Table: tbl_finance_audit_logs
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_finance_audit_logs`;
CREATE TABLE `tbl_finance_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `previous_value` TEXT NULL,
  `new_value` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_fin_action` (`action`),
  KEY `idx_fin_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  CONSTRAINT `fk_pay_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 17. Communication Module Tables
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_notices`;
CREATE TABLE `tbl_notices` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_announcements`;
CREATE TABLE `tbl_announcements` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_communication_templates`;
CREATE TABLE `tbl_communication_templates` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_notification_rules`;
CREATE TABLE `tbl_notification_rules` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_communication_messages`;
CREATE TABLE `tbl_communication_messages` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_notification_preferences`;
CREATE TABLE `tbl_notification_preferences` (
  `preference_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `user_type` ENUM('Parent', 'Teacher', 'Student', 'Staff') NOT NULL DEFAULT 'Parent',
  `preference_key` VARCHAR(50) NOT NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`preference_id`),
  UNIQUE KEY `uk_user_pref` (`user_id`, `user_type`, `preference_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_conversations`;
CREATE TABLE `tbl_conversations` (
  `conversation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_type` ENUM('Parent-Teacher', 'Internal', 'Group') NOT NULL DEFAULT 'Internal',
  `group_id` INT UNSIGNED NULL,
  `title` VARCHAR(255) NULL,
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`conversation_id`),
  KEY `idx_conv_type` (`conversation_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_conversation_participants`;
CREATE TABLE `tbl_conversation_participants` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_messages`;
CREATE TABLE `tbl_messages` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_communication_groups`;
CREATE TABLE `tbl_communication_groups` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_communication_settings`;
CREATE TABLE `tbl_communication_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_inapp` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_sms` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_whatsapp` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_email` TINYINT(1) NOT NULL DEFAULT 1,
  `sms_provider` VARCHAR(50) NOT NULL DEFAULT 'Generic SMS Gateway',
  `sms_sender_id` VARCHAR(20) NOT NULL DEFAULT 'SCHOLL',
  `whatsapp_provider` VARCHAR(50) NOT NULL DEFAULT 'WhatsApp Business API',
  `email_from_name` VARCHAR(100) NOT NULL DEFAULT 'School Management',
  `email_from_address` VARCHAR(100) NOT NULL DEFAULT 'notifications@school.edu',
  `enable_scheduled_jobs` TINYINT(1) NOT NULL DEFAULT 1,
  `max_retries` INT UNSIGNED NOT NULL DEFAULT 3,
  `retry_interval_minutes` INT UNSIGNED NOT NULL DEFAULT 15,
  `parent_teacher_direct_messaging` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_communication_audit_logs`;
CREATE TABLE `tbl_communication_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_comm_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 19. Table: tbl_events
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_events`;
CREATE TABLE `tbl_events` (
  `event_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `event_date` DATE NOT NULL,
  `audience` VARCHAR(100) NOT NULL DEFAULT 'Whole School',
  `venue` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 20. Table: tbl_certificates
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_certificates`;
CREATE TABLE `tbl_certificates` (
  `certificate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `certificate_type` VARCHAR(100) NOT NULL,
  `certificate_no` VARCHAR(50) NOT NULL,
  `issue_date` DATE NOT NULL,
  `remarks` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`certificate_id`),
  UNIQUE KEY `uk_certificate_no` (`certificate_no`),
  KEY `idx_cert_student` (`student_id`),
  CONSTRAINT `fk_cert_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 21. Table: tbl_school_settings
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_school_settings`;
CREATE TABLE `tbl_school_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_name` VARCHAR(255) NOT NULL,
  `school_code` VARCHAR(50) NOT NULL,
  `established_year` VARCHAR(10) NOT NULL,
  `principal_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `website` VARCHAR(100) NOT NULL,
  `logo` VARCHAR(255) NULL,
  `address` TEXT NOT NULL,
  `description` TEXT NULL,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ----------------------------------------------------------------------------
-- 21. Examination & Results Module Tables
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_exam_types`;
CREATE TABLE `tbl_exam_types` (
  `exam_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`exam_type_id`),
  UNIQUE KEY `uk_exam_type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_exams`;
CREATE TABLE `tbl_exams` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_exam_schedules`;
CREATE TABLE `tbl_exam_schedules` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_exam_marks`;
CREATE TABLE `tbl_exam_marks` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_grades`;
CREATE TABLE `tbl_grades` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_student_results`;
CREATE TABLE `tbl_student_results` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_examination_settings`;
CREATE TABLE `tbl_examination_settings` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_exam_audit_logs`;
CREATE TABLE `tbl_exam_audit_logs` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 22. Homework & Assignments Module
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_assignment_types`;
CREATE TABLE `tbl_assignment_types` (
  `type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_assignments`;
CREATE TABLE `tbl_assignments` (
  `assignment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `class_id` INT UNSIGNED NOT NULL,
  `section_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `teacher_id` INT UNSIGNED NOT NULL,
  `assignment_type_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `instructions` TEXT NULL,
  `assigned_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `due_time` TIME NULL,
  `max_marks` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
  `allow_remarks` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_file_submission` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_text_submission` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_multiple_files` TINYINT(1) NOT NULL DEFAULT 0,
  `allow_resubmission` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_late_submission` TINYINT(1) NOT NULL DEFAULT 1,
  `target_type` ENUM('Class', 'Section', 'Individual') NOT NULL DEFAULT 'Section',
  `target_student_ids` TEXT NULL,
  `status` ENUM('Draft', 'Published', 'Active', 'Closed', 'Archived') NOT NULL DEFAULT 'Published',
  `attachments` TEXT NULL,
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignment_id`),
  KEY `idx_asgn_class` (`class_id`, `section_id`),
  KEY `idx_asgn_subject` (`subject_id`),
  KEY `idx_asgn_teacher` (`teacher_id`),
  KEY `idx_asgn_due` (`due_date`),
  KEY `idx_asgn_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_assignment_submissions`;
CREATE TABLE `tbl_assignment_submissions` (
  `submission_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `submission_version` INT UNSIGNED NOT NULL DEFAULT 1,
  `submitted_text` TEXT NULL,
  `submitted_files` TEXT NULL,
  `submitted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_late` TINYINT(1) NOT NULL DEFAULT 0,
  `late_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('Pending', 'Submitted', 'Late', 'Reviewed', 'Returned') NOT NULL DEFAULT 'Submitted',
  `marks_obtained` DECIMAL(5,2) NULL,
  `grade` VARCHAR(10) NULL,
  `teacher_remarks` TEXT NULL,
  `correction_reason` TEXT NULL,
  `reviewed_by` INT UNSIGNED NULL,
  `reviewed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`submission_id`),
  UNIQUE KEY `uk_asgn_student` (`assignment_id`, `student_id`),
  KEY `idx_subm_asgn` (`assignment_id`),
  KEY `idx_subm_student` (`student_id`),
  KEY `idx_subm_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_submission_history`;
CREATE TABLE `tbl_submission_history` (
  `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `submitted_text` TEXT NULL,
  `submitted_files` TEXT NULL,
  `submitted_at` DATETIME NOT NULL,
  `marks_obtained` DECIMAL(5,2) NULL,
  `grade` VARCHAR(10) NULL,
  `teacher_remarks` TEXT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Submitted',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_hist_submission` (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_homework_notifications`;
CREATE TABLE `tbl_homework_notifications` (
  `notification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `parent_name` VARCHAR(100) NULL,
  `parent_phone` VARCHAR(20) NULL,
  `parent_email` VARCHAR(100) NULL,
  `notification_type` ENUM('New Assignment', 'Upcoming Due', 'Overdue', 'Submission Received', 'Submission Reviewed', 'Returned') NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('Pending', 'Sent', 'Failed') NOT NULL DEFAULT 'Pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `idx_hw_notif_asgn` (`assignment_id`),
  KEY `idx_hw_notif_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_homework_settings`;
CREATE TABLE `tbl_homework_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `default_submission_deadline_days` INT UNSIGNED NOT NULL DEFAULT 3,
  `allow_late_submissions_default` TINYINT(1) NOT NULL DEFAULT 1,
  `max_upload_size_mb` INT UNSIGNED NOT NULL DEFAULT 10,
  `allowed_file_extensions` VARCHAR(255) NOT NULL DEFAULT 'pdf,doc,docx,jpg,jpeg,png,zip,txt',
  `enable_grading` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_parent_notifications` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_homework_audit_logs`;
CREATE TABLE `tbl_homework_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `previous_value` TEXT NULL,
  `new_value` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_hw_action` (`action`),
  KEY `idx_hw_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 23. Leave Management Module
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_leave_types`;
CREATE TABLE `tbl_leave_types` (
  `type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `type_code` VARCHAR(20) NOT NULL,
  `applicable_to` ENUM('Students', 'Staff', 'Both') NOT NULL DEFAULT 'Both',
  `description` VARCHAR(255) NULL,
  `max_days` INT UNSIGNED NOT NULL DEFAULT 12,
  `requires_document` TINYINT(1) NOT NULL DEFAULT 0,
  `requires_approval` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_half_day` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_carry_forward` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_leave_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_leave_applications`;
CREATE TABLE `tbl_leave_applications` (
  `application_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `applicant_type` ENUM('Student', 'Staff') NOT NULL DEFAULT 'Student',
  `student_id` INT UNSIGNED NULL,
  `staff_id` INT UNSIGNED NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `class_id` INT UNSIGNED NULL,
  `section_id` INT UNSIGNED NULL,
  `leave_type_id` INT UNSIGNED NOT NULL,
  `from_date` DATE NOT NULL,
  `to_date` DATE NOT NULL,
  `duration_days` DECIMAL(4,1) NOT NULL DEFAULT 1.0,
  `is_half_day` TINYINT(1) NOT NULL DEFAULT 0,
  `half_day_type` ENUM('Full Day', 'First Half', 'Second Half') NOT NULL DEFAULT 'Full Day',
  `reason` TEXT NOT NULL,
  `attachment` VARCHAR(255) NULL,
  `status` ENUM('Draft', 'Pending', 'Clarification Required', 'Approved', 'Rejected', 'Cancelled', 'Completed') NOT NULL DEFAULT 'Pending',
  `rejection_reason` TEXT NULL,
  `clarification_notes` TEXT NULL,
  `approved_by` INT UNSIGNED NULL,
  `approved_at` DATETIME NULL,
  `applied_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  KEY `idx_leave_app_type` (`applicant_type`),
  KEY `idx_leave_app_student` (`student_id`),
  KEY `idx_leave_app_staff` (`staff_id`),
  KEY `idx_leave_app_dates` (`from_date`, `to_date`),
  KEY `idx_leave_app_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_leave_balances`;
CREATE TABLE `tbl_leave_balances` (
  `balance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `entity_type` ENUM('Student', 'Staff') NOT NULL DEFAULT 'Staff',
  `entity_id` INT UNSIGNED NOT NULL,
  `leave_type_id` INT UNSIGNED NOT NULL,
  `allocated_days` DECIMAL(4,1) NOT NULL DEFAULT 12.0,
  `used_days` DECIMAL(4,1) NOT NULL DEFAULT 0.0,
  `pending_days` DECIMAL(4,1) NOT NULL DEFAULT 0.0,
  `carry_forward_days` DECIMAL(4,1) NOT NULL DEFAULT 0.0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`balance_id`),
  UNIQUE KEY `uk_leave_balance` (`academic_year_id`, `entity_type`, `entity_id`, `leave_type_id`),
  KEY `idx_lb_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_leave_history`;
CREATE TABLE `tbl_leave_history` (
  `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `performed_by` INT UNSIGNED NULL,
  `performed_by_type` VARCHAR(50) NOT NULL DEFAULT 'Staff',
  `previous_status` VARCHAR(50) NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `comments` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_lh_app` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_leave_settings`;
CREATE TABLE `tbl_leave_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_student_leave` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_staff_leave` TINYINT(1) NOT NULL DEFAULT 1,
  `enable_half_day` TINYINT(1) NOT NULL DEFAULT 1,
  `working_days_only` TINYINT(1) NOT NULL DEFAULT 1,
  `student_approval_workflow` VARCHAR(100) NOT NULL DEFAULT 'Class Teacher -> Principal',
  `staff_approval_workflow` VARCHAR(100) NOT NULL DEFAULT 'Department Head -> Principal',
  `enable_balance_tracking` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_carry_forward` TINYINT(1) NOT NULL DEFAULT 1,
  `max_carry_forward_days` INT UNSIGNED NOT NULL DEFAULT 5,
  `require_document_default` TINYINT(1) NOT NULL DEFAULT 0,
  `max_file_size_mb` INT UNSIGNED NOT NULL DEFAULT 10,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_leave_audit_logs`;
CREATE TABLE `tbl_leave_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_leave_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 24. Transport Management Module
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_vehicles`;
CREATE TABLE `tbl_vehicles` (
  `vehicle_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_number` VARCHAR(50) NOT NULL,
  `registration_number` VARCHAR(50) NOT NULL,
  `vehicle_type` ENUM('School Bus', 'Van', 'Mini Bus', 'Other') NOT NULL DEFAULT 'School Bus',
  `manufacturer` VARCHAR(100) NULL,
  `model` VARCHAR(100) NULL,
  `manufacturing_year` YEAR NULL,
  `seating_capacity` INT UNSIGNED NOT NULL DEFAULT 40,
  `vehicle_color` VARCHAR(50) NULL DEFAULT 'Yellow',
  `registration_date` DATE NULL,
  `registration_expiry` DATE NULL,
  `insurance_number` VARCHAR(100) NULL,
  `insurance_expiry` DATE NULL,
  `fitness_number` VARCHAR(100) NULL,
  `fitness_expiry` DATE NULL,
  `pollution_number` VARCHAR(100) NULL,
  `pollution_expiry` DATE NULL,
  `permit_number` VARCHAR(100) NULL,
  `permit_expiry` DATE NULL,
  `assigned_driver_id` INT UNSIGNED NULL,
  `assigned_route_id` INT UNSIGNED NULL,
  `status` ENUM('Active', 'Inactive', 'Maintenance', 'Retired') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vehicle_id`),
  UNIQUE KEY `uk_vehicle_reg` (`registration_number`),
  KEY `idx_veh_driver` (`assigned_driver_id`),
  KEY `idx_veh_route` (`assigned_route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_transport_drivers`;
CREATE TABLE `tbl_transport_drivers` (
  `driver_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `driver_name` VARCHAR(100) NOT NULL,
  `staff_id` INT UNSIGNED NULL,
  `photo` VARCHAR(255) NULL,
  `dob` DATE NULL,
  `phone` VARCHAR(20) NOT NULL,
  `alternate_phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `license_number` VARCHAR(50) NOT NULL,
  `license_type` VARCHAR(50) NOT NULL DEFAULT 'Heavy Commercial Vehicle (HCV)',
  `license_issue_date` DATE NULL,
  `license_expiry_date` DATE NOT NULL,
  `experience_years` INT UNSIGNED NOT NULL DEFAULT 5,
  `assigned_vehicle_id` INT UNSIGNED NULL,
  `status` ENUM('Active', 'Inactive', 'On Leave', 'Suspended') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`driver_id`),
  UNIQUE KEY `uk_driver_license` (`license_number`),
  KEY `idx_driver_staff` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_transport_routes`;
CREATE TABLE `tbl_transport_routes` (
  `route_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `route_name` VARCHAR(100) NOT NULL,
  `route_code` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `start_point` VARCHAR(100) NOT NULL,
  `end_point` VARCHAR(100) NOT NULL,
  `estimated_distance_km` DECIMAL(5,2) NOT NULL DEFAULT 15.00,
  `estimated_travel_time_min` INT UNSIGNED NOT NULL DEFAULT 45,
  `assigned_vehicle_id` INT UNSIGNED NULL,
  `assigned_driver_id` INT UNSIGNED NULL,
  `status` ENUM('Active', 'Inactive', 'Suspended') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`route_id`),
  UNIQUE KEY `uk_route_code` (`route_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_route_stops`;
CREATE TABLE `tbl_route_stops` (
  `stop_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `route_id` INT UNSIGNED NOT NULL,
  `stop_name` VARCHAR(100) NOT NULL,
  `stop_code` VARCHAR(50) NULL,
  `sequence_order` INT UNSIGNED NOT NULL DEFAULT 1,
  `pickup_time` TIME NOT NULL DEFAULT '07:30:00',
  `drop_time` TIME NOT NULL DEFAULT '15:30:00',
  `landmark` VARCHAR(150) NULL,
  `distance_km` DECIMAL(5,2) NOT NULL DEFAULT 2.00,
  `fare_amount` DECIMAL(10,2) NOT NULL DEFAULT 1200.00,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stop_id`),
  KEY `idx_stop_route` (`route_id`),
  KEY `idx_stop_seq` (`route_id`, `sequence_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_student_transport_assignments`;
CREATE TABLE `tbl_student_transport_assignments` (
  `assignment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `student_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NULL,
  `section_id` INT UNSIGNED NULL,
  `route_id` INT UNSIGNED NOT NULL,
  `pickup_stop_id` INT UNSIGNED NOT NULL,
  `drop_stop_id` INT UNSIGNED NOT NULL,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `transport_type` ENUM('Two Way', 'One Way', 'Pickup Only', 'Drop Only') NOT NULL DEFAULT 'Two Way',
  `monthly_fee` DECIMAL(10,2) NOT NULL DEFAULT 1500.00,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `status` ENUM('Active', 'Suspended', 'Cancelled') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `uk_student_trans_assign` (`academic_year_id`, `student_id`, `status`),
  KEY `idx_ta_route` (`route_id`),
  KEY `idx_ta_vehicle` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_transport_assignment_history`;
CREATE TABLE `tbl_transport_assignment_history` (
  `history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `previous_route_id` INT UNSIGNED NULL,
  `new_route_id` INT UNSIGNED NULL,
  `previous_stop_id` INT UNSIGNED NULL,
  `new_stop_id` INT UNSIGNED NULL,
  `previous_vehicle_id` INT UNSIGNED NULL,
  `new_vehicle_id` INT UNSIGNED NULL,
  `effective_date` DATE NOT NULL,
  `changed_by` INT UNSIGNED NULL,
  `comments` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_tah_student` (`student_id`),
  KEY `idx_tah_assign` (`assignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_vehicle_maintenance`;
CREATE TABLE `tbl_vehicle_maintenance` (
  `maintenance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `maintenance_type` ENUM('Routine Service', 'Engine', 'Tyres', 'Brake', 'Electrical', 'Insurance', 'Fitness', 'Cleaning', 'Repair', 'Other') NOT NULL DEFAULT 'Routine Service',
  `service_date` DATE NOT NULL,
  `next_service_date` DATE NOT NULL,
  `description` TEXT NOT NULL,
  `service_provider` VARCHAR(150) NOT NULL,
  `cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `invoice_number` VARCHAR(100) NULL,
  `remarks` VARCHAR(255) NULL,
  `document_file` VARCHAR(255) NULL,
  `status` ENUM('Completed', 'Scheduled', 'Overdue') NOT NULL DEFAULT 'Completed',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`maintenance_id`),
  KEY `idx_vm_vehicle` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_transport_documents`;
CREATE TABLE `tbl_transport_documents` (
  `document_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type` ENUM('Vehicle', 'Driver') NOT NULL DEFAULT 'Vehicle',
  `entity_id` INT UNSIGNED NOT NULL,
  `document_type` ENUM('Registration', 'Insurance', 'Fitness Certificate', 'Pollution Certificate', 'Permit', 'Driving License', 'ID Proof', 'Medical Certificate', 'Police Verification', 'Other') NOT NULL DEFAULT 'Registration',
  `document_number` VARCHAR(100) NULL,
  `issue_date` DATE NULL,
  `expiry_date` DATE NULL,
  `file_path` VARCHAR(255) NULL,
  `status` ENUM('Active', 'Expiring Soon', 'Expired') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `idx_td_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_transport_settings`;
CREATE TABLE `tbl_transport_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enable_transport` TINYINT(1) NOT NULL DEFAULT 1,
  `enforce_capacity` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_capacity_override` TINYINT(1) NOT NULL DEFAULT 0,
  `default_monthly_fee` DECIMAL(10,2) NOT NULL DEFAULT 1500.00,
  `fee_frequency` VARCHAR(50) NOT NULL DEFAULT 'Monthly',
  `maintenance_reminder_days` INT UNSIGNED NOT NULL DEFAULT 15,
  `document_expiry_reminder_days` INT UNSIGNED NOT NULL DEFAULT 30,
  `driver_license_reminder_days` INT UNSIGNED NOT NULL DEFAULT 30,
  `allow_one_way` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_pickup_only` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_drop_only` TINYINT(1) NOT NULL DEFAULT 1,
  `allow_bulk_assignment` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_transport_audit_logs`;
CREATE TABLE `tbl_transport_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_trans_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 25. Certificate & Document Management Module
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_certificate_types`;
CREATE TABLE `tbl_certificate_types` (
  `type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` VARCHAR(100) NOT NULL,
  `type_code` VARCHAR(50) NOT NULL,
  `prefix` VARCHAR(20) NOT NULL DEFAULT 'CERT-',
  `description` TEXT NULL,
  `is_system` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_cert_type_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_certificate_templates`;
CREATE TABLE `tbl_certificate_templates` (
  `template_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(100) NOT NULL,
  `type_code` VARCHAR(50) NOT NULL,
  `header_content` TEXT NULL,
  `body_content` LONGTEXT NOT NULL,
  `footer_content` TEXT NULL,
  `logo_position` ENUM('Top-Left', 'Top-Center', 'Top-Right', 'None') NOT NULL DEFAULT 'Top-Center',
  `signature_layout` ENUM('Principal-Only', 'Principal-And-Officer', 'Officer-Only') NOT NULL DEFAULT 'Principal-Only',
  `paper_size` ENUM('A4', 'Letter', 'Legal') NOT NULL DEFAULT 'A4',
  `orientation` ENUM('Portrait', 'Landscape') NOT NULL DEFAULT 'Portrait',
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`template_id`),
  KEY `idx_tmpl_code` (`type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_certificate_requests`;
CREATE TABLE `tbl_certificate_requests` (
  `request_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `certificate_type_id` INT UNSIGNED NOT NULL,
  `reason` TEXT NOT NULL,
  `requested_date` DATE NOT NULL,
  `required_date` DATE NULL,
  `remarks` TEXT NULL,
  `supporting_document` VARCHAR(255) NULL,
  `status` ENUM('Draft', 'Pending', 'Under Verification', 'Correction Required', 'Approved', 'Rejected', 'Generated', 'Printed', 'Issued', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `rejection_reason` TEXT NULL,
  `requested_by` INT UNSIGNED NULL,
  `verified_by` INT UNSIGNED NULL,
  `approved_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `idx_cr_student` (`student_id`),
  KEY `idx_cr_type` (`certificate_type_id`),
  KEY `idx_cr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_certificates`;
CREATE TABLE `tbl_certificates` (
  `certificate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_no` VARCHAR(50) NOT NULL,
  `request_id` INT UNSIGNED NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `academic_year_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `certificate_type_id` INT UNSIGNED NULL,
  `certificate_type` VARCHAR(100) NOT NULL,
  `template_id` INT UNSIGNED NULL,
  `issue_date` DATE NOT NULL,
  `student_data_snapshot` JSON NULL,
  `generated_content` LONGTEXT NULL,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `is_reissued` TINYINT(1) NOT NULL DEFAULT 0,
  `reissue_reason` TEXT NULL,
  `remarks` TEXT NULL,
  `generated_by` INT UNSIGNED NULL,
  `issued_by` INT UNSIGNED NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'Generated',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`certificate_id`),
  UNIQUE KEY `uk_cert_no` (`certificate_no`),
  KEY `idx_cert_student` (`student_id`),
  KEY `idx_cert_req` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_certificate_versions`;
CREATE TABLE `tbl_certificate_versions` (
  `version_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificate_id` INT UNSIGNED NOT NULL,
  `version_number` INT UNSIGNED NOT NULL,
  `certificate_no` VARCHAR(50) NOT NULL,
  `content_snapshot` LONGTEXT NOT NULL,
  `reason` TEXT NOT NULL,
  `changed_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`version_id`),
  KEY `idx_cv_cert` (`certificate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_document_categories`;
CREATE TABLE `tbl_document_categories` (
  `category_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `description` TEXT NULL,
  `applicable_to` ENUM('All', 'Student', 'Staff') NOT NULL DEFAULT 'Student',
  `is_required` TINYINT(1) NOT NULL DEFAULT 0,
  `expiry_required` TINYINT(1) NOT NULL DEFAULT 0,
  `verification_required` TINYINT(1) NOT NULL DEFAULT 1,
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `uk_doc_cat_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_student_documents`;
CREATE TABLE `tbl_student_documents` (
  `document_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NULL,
  `document_type` VARCHAR(100) NOT NULL,
  `document_name` VARCHAR(255) NOT NULL,
  `document_number` VARCHAR(100) NULL,
  `issue_date` DATE NULL,
  `expiry_date` DATE NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `remarks` TEXT NULL,
  `verification_status` ENUM('Pending', 'Verified', 'Rejected', 'Expired') NOT NULL DEFAULT 'Pending',
  `rejection_reason` TEXT NULL,
  `verified_by` INT UNSIGNED NULL,
  `verified_at` DATETIME NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `idx_sd_student` (`student_id`),
  KEY `idx_sd_cat` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_certificate_settings`;
CREATE TABLE `tbl_certificate_settings` (
  `setting_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numbering_format` VARCHAR(100) NOT NULL DEFAULT '{PREFIX}{YEAR}-{NUMBER}',
  `number_sequence_length` INT UNSIGNED NOT NULL DEFAULT 5,
  `require_approval` TINYINT(1) NOT NULL DEFAULT 1,
  `require_document_verification` TINYINT(1) NOT NULL DEFAULT 0,
  `require_fee_clearance_for_tc` TINYINT(1) NOT NULL DEFAULT 1,
  `require_library_clearance_for_tc` TINYINT(1) NOT NULL DEFAULT 0,
  `require_transport_clearance_for_tc` TINYINT(1) NOT NULL DEFAULT 0,
  `principal_signature_path` VARCHAR(255) NULL,
  `authorized_signature_path` VARCHAR(255) NULL,
  `watermark_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `default_paper_size` VARCHAR(20) NOT NULL DEFAULT 'A4',
  `default_orientation` VARCHAR(20) NOT NULL DEFAULT 'Portrait',
  `document_expiry_reminder_days` INT UNSIGNED NOT NULL DEFAULT 30,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tbl_certificate_audit_logs`;
CREATE TABLE `tbl_certificate_audit_logs` (
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `details` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_cert_audit_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- SEED DATA (Populating complete realistic records matching prototype views)
-- ============================================================================

-- Roles
INSERT INTO `tbl_roles` (`role_id`, `role_name`, `description`, `status`) VALUES
(1, 'Super Admin', 'Full system access and school configuration', 1),
(2, 'Principal', 'Access to Dashboard, Students, Staff, and Reports', 1),
(3, 'Teacher', 'Access to assigned students, Attendance, and Homework', 1),
(4, 'Accountant', 'Access to Fee collection and financial reports', 1),
(5, 'Receptionist', 'Access to Student registration and Certificates', 1);

-- Departments
INSERT INTO `tbl_departments` (`department_id`, `department_name`, `head_of_department_id`, `description`, `status`) VALUES
(1, 'Mathematics', NULL, 'Mathematics & Statistics department', 1),
(2, 'Science', NULL, 'Physics, Chemistry, and Biology department', 1),
(3, 'English', NULL, 'English Literature and Linguistics department', 1),
(4, 'Administration', NULL, 'School Leadership and Operations', 1),
(5, 'Finance', NULL, 'Fee collection and accounts department', 1),
(6, 'Physical Education', NULL, 'Sports, fitness, and outdoor activities', 1);

-- Designations
INSERT INTO `tbl_designations` (`designation_id`, `designation_name`, `category`, `description`, `status`) VALUES
(1, 'Principal', 'Administration', 'Head of the School', 1),
(2, 'Head of Department', 'Teaching', 'Department supervisor and lead educator', 1),
(3, 'Senior Teacher', 'Teaching', 'Senior classroom faculty', 1),
(4, 'Teacher', 'Teaching', 'Subject teacher and classroom educator', 1),
(5, 'Accountant', 'Finance', 'Financial officer', 1),
(6, 'Front Desk', 'Administration', 'Receptionist and front office coordinator', 1),
(7, 'Clerk', 'Administration', 'Administrative office assistant', 1);

-- Staff Members
INSERT INTO `tbl_staff` (`staff_id`, `employee_code`, `full_name`, `email`, `phone`, `gender`, `date_of_birth`, `category`, `department_id`, `designation_id`, `joining_date`, `salary`, `address`, `status`) VALUES
(1, 'EMP1001', 'Priya Varma', 'priya.varma@gmail.com', '+91 98471 00001', 'Female', '1985-04-12', 'Teacher', 1, 3, '2018-06-01', 45000.00, 'Kakkanad, Kochi, Kerala', 1),
(2, 'EMP1002', 'George Mathew', 'george.mathew@gmail.com', '+91 98471 00002', 'Male', '1980-09-20', 'Teacher', 2, 2, '2015-03-14', 55000.00, 'Palarivattom, Kochi, Kerala', 1),
(3, 'EMP1003', 'Fathima Beevi', 'fathima.beevi@gmail.com', '+91 98471 00003', 'Female', '1988-11-05', 'Accountant', 5, 5, '2020-01-09', 38000.00, 'Aluva, Ernakulam, Kerala', 1),
(4, 'EMP1004', 'Sunil Kumar', 'sunil.kumar@gmail.com', '+91 98471 00004', 'Male', '1992-06-18', 'Receptionist', 4, 6, '2021-08-22', 28000.00, 'Edappally, Kochi, Kerala', 1),
(5, 'EMP1005', 'Lakshmi Pillai', 'lakshmi.pillai@gmail.com', '+91 98471 00005', 'Female', '1989-02-28', 'Teacher', 3, 4, '2019-07-11', 40000.00, 'Tripunithura, Kochi, Kerala', 1),
(6, 'EMP1006', 'Antony Xavier', 'antony.xavier@gmail.com', '+91 98471 00006', 'Male', '1975-01-15', 'Principal', 4, 1, '2012-04-01', 75000.00, 'Marine Drive, Kochi, Kerala', 1),
(7, 'EMP1007', 'Reshma Nair', 'reshma.nair@gmail.com', '+91 98471 00007', 'Female', '1995-10-10', 'Office Staff', 4, 7, '2022-02-03', 24000.00, 'Vyttila, Kochi, Kerala', 0),
(8, 'EMP1008', 'Vinod Kumar', 'vinod.kumar@gmail.com', '+91 98471 00008', 'Male', '1987-08-14', 'Teacher', 6, 4, '2017-06-01', 42000.00, 'Kalamassery, Kochi, Kerala', 1),
(9, 'EMP1009', 'Nisha Roy', 'nisha.roy@gmail.com', '+91 98471 00009', 'Female', '1990-03-22', 'Teacher', 3, 4, '2021-05-15', 36000.00, 'Kadavanthra, Kochi, Kerala', 1),
(10, 'EMP1010', 'Manoj Das', 'manoj.das@gmail.com', '+91 98471 00010', 'Male', '1986-12-04', 'Teacher', 1, 4, '2019-06-10', 40000.00, 'Thrikkakara, Kochi, Kerala', 1),
(11, 'EMP1011', 'Ancy Thomas', 'ancy.thomas@gmail.com', '+91 98471 00011', 'Female', '1991-07-19', 'Teacher', 2, 4, '2020-08-01', 38000.00, 'Cheranallur, Kochi, Kerala', 1),
(12, 'EMP1012', 'Rahul Sharma', 'rahul.sharma@gmail.com', '+91 98471 00012', 'Male', '1984-05-30', 'Teacher', 1, 3, '2016-09-01', 46000.00, 'Kaloor, Kochi, Kerala', 1);

-- Update department heads
UPDATE `tbl_departments` SET `head_of_department_id` = 1 WHERE `department_id` = 1;
UPDATE `tbl_departments` SET `head_of_department_id` = 2 WHERE `department_id` = 2;
UPDATE `tbl_departments` SET `head_of_department_id` = 5 WHERE `department_id` = 3;
UPDATE `tbl_departments` SET `head_of_department_id` = 6 WHERE `department_id` = 4;
UPDATE `tbl_departments` SET `head_of_department_id` = 3 WHERE `department_id` = 5;
UPDATE `tbl_departments` SET `head_of_department_id` = 8 WHERE `department_id` = 6;

-- System Users (Password for all seeded accounts is 'Admin@123')
INSERT INTO `tbl_users` (`user_id`, `role_id`, `staff_id`, `name`, `email`, `phone`, `password`, `status`) VALUES
(1, 1, NULL, 'Anjali Menon', 'anjali.menon@gmail.com', '+91 98470 00000', '$2y$10$OXjoe7w618tyFJwMPR6oru2vSZKfhj3xIiDAkwN7r/O8afXkaPIl.', 1),
(2, 2, 6, 'Antony Xavier', 'antony.xavier@gmail.com', '+91 98471 00006', '$2y$10$OXjoe7w618tyFJwMPR6oru2vSZKfhj3xIiDAkwN7r/O8afXkaPIl.', 1),
(3, 3, 1, 'Priya Varma', 'priya.varma@gmail.com', '+91 98471 00001', '$2y$10$OXjoe7w618tyFJwMPR6oru2vSZKfhj3xIiDAkwN7r/O8afXkaPIl.', 1),
(4, 4, 3, 'Fathima Beevi', 'fathima.beevi@gmail.com', '+91 98471 00003', '$2y$10$OXjoe7w618tyFJwMPR6oru2vSZKfhj3xIiDAkwN7r/O8afXkaPIl.', 1),
(5, 5, 4, 'Sunil Kumar', 'sunil.kumar@gmail.com', '+91 98471 00004', '$2y$10$OXjoe7w618tyFJwMPR6oru2vSZKfhj3xIiDAkwN7r/O8afXkaPIl.', 1);

-- Academic Years (Only 2026-2027 is active)
INSERT INTO `tbl_academic_years` (`academic_year_id`, `year_name`, `start_date`, `end_date`, `is_active`, `status`) VALUES
(1, '2026-2027', '2026-06-01', '2027-03-31', 1, 1),
(2, '2025-2026', '2025-06-01', '2026-03-31', 0, 1),
(3, '2024-2025', '2024-06-01', '2025-03-31', 0, 1);

-- Classes
INSERT INTO `tbl_classes` (`class_id`, `academic_year_id`, `class_name`, `class_code`, `class_teacher_id`, `capacity`, `status`) VALUES
(1, 1, 'LKG', 'CLS-LKG', 9, 28, 1),
(2, 1, 'Grade 1', 'CLS-01', 10, 32, 1),
(3, 1, 'Grade 3', 'CLS-03', 11, 35, 1),
(4, 1, 'Grade 5', 'CLS-05', 11, 35, 1),
(5, 1, 'Grade 6', 'CLS-06', 8, 36, 1),
(6, 1, 'Grade 8', 'CLS-08', 12, 38, 1),
(7, 1, 'Grade 9', 'CLS-09', 2, 38, 1),
(8, 1, 'Grade 10', 'CLS-10', 1, 40, 1),
(9, 1, 'Grade 11', 'CLS-11', 1, 40, 1),
(10, 1, 'Grade 12', 'CLS-12', 2, 36, 1);

-- Sections
INSERT INTO `tbl_sections` (`section_id`, `class_id`, `section_name`, `class_teacher_id`, `room_no`, `capacity`, `status`) VALUES
(1, 8, 'A', 1, 'Room 201', 40, 1),
(2, 8, 'B', 5, 'Room 202', 40, 1),
(3, 8, 'C', 4, 'Room 203', 40, 1),
(4, 7, 'A', 2, 'Room 105', 38, 1),
(5, 7, 'B', 2, 'Room 106', 38, 1),
(6, 5, 'A', 8, 'Room 102', 36, 1),
(7, 3, 'C', 10, 'Room 101', 35, 1),
(8, 1, 'A', 9, 'Kindergarten 1', 28, 1),
(9, 9, 'A', 1, 'Room 301', 40, 1),
(10, 10, 'C', 2, 'Room 303', 36, 1);

-- Subjects
INSERT INTO `tbl_subjects` (`subject_id`, `class_id`, `subject_name`, `subject_code`, `subject_type`, `teacher_id`, `status`) VALUES
(1, 8, 'Mathematics', 'MATH10', 'Core', 1, 1),
(2, 8, 'Physics', 'PHY10', 'Core', 2, 1),
(3, 8, 'Malayalam', 'MAL10', 'Language', 5, 1),
(4, 8, 'Physical Education', 'PE10', 'Practical', 8, 1),
(5, 8, 'Computer Science', 'CS10', 'Elective', 4, 1),
(6, 7, 'Mathematics', 'MATH09', 'Core', 1, 1),
(7, 7, 'Science', 'SCI09', 'Core', 2, 1),
(8, 7, 'English', 'ENG09', 'Language', 5, 1);

-- Students
INSERT INTO `tbl_students` (`student_id`, `admission_number`, `first_name`, `middle_name`, `last_name`, `gender`, `date_of_birth`, `blood_group`, `nationality`, `religion`, `address`, `guardian_name`, `guardian_relation`, `guardian_phone`, `guardian_email`, `academic_year_id`, `class_id`, `section_id`, `roll_number`, `status`) VALUES
(1, 'EDU2026001', 'Aarav', '', 'Nair', 'Male', '2011-06-12', 'B+', 'Indian', 'Hindu', 'Thaikkattukara, Aluva, Ernakulam, Kerala', 'Suresh Nair', 'Father', '+91 98470 11223', 'suresh.nair@email.com', 1, 8, 1, '01', 1),
(2, 'EDU2026002', 'Diya', '', 'Menon', 'Female', '2012-02-03', 'O+', 'Indian', 'Hindu', 'Kakkanad, Ernakulam, Kerala', 'Ramesh Menon', 'Father', '+91 94470 22314', 'ramesh.menon@email.com', 1, 7, 5, '02', 1),
(3, 'EDU2026003', 'Kiran', '', 'Thomas', 'Male', '2009-09-19', 'A+', 'Indian', 'Christian', 'Kaloor, Ernakulam, Kerala', 'Thomas Varghese', 'Father', '+91 90480 33556', 'thomas.varghese@email.com', 1, 10, 10, '03', 1),
(4, 'EDU2026004', 'Ananya', '', 'Pillai', 'Female', '2014-11-27', 'AB+', 'Indian', 'Hindu', 'Tripunithura, Ernakulam, Kerala', 'Vinod Pillai', 'Father', '+91 99460 44778', 'vinod.pillai@email.com', 1, 5, 6, '04', 1),
(5, 'EDU2026005', 'Rohan', '', 'Iqbal', 'Male', '2013-01-05', 'B-', 'Indian', 'Muslim', 'Edappally, Ernakulam, Kerala', 'Iqbal Rahman', 'Father', '+91 98950 55990', 'iqbal.rahman@email.com', 1, 6, 4, '05', 0),
(6, 'EDU2026006', 'Sara', '', 'Joseph', 'Female', '2020-04-14', 'O+', 'Indian', 'Christian', 'Palarivattom, Ernakulam, Kerala', 'Jibin Joseph', 'Father', '+91 97460 66112', 'jibin.joseph@email.com', 1, 1, 8, '06', 1),
(7, 'EDU2026007', 'Vishnu', '', 'Krishnan', 'Male', '2010-08-30', 'A-', 'Indian', 'Hindu', 'Vyttila, Ernakulam, Kerala', 'Krishnan Kutty', 'Father', '+91 88480 77334', 'krishnan.kutty@email.com', 1, 9, 9, '07', 1),
(8, 'EDU2026008', 'Meera', '', 'Suresh', 'Female', '2016-07-22', 'B+', 'Indian', 'Hindu', 'Kadavanthra, Ernakulam, Kerala', 'Suresh Babu', 'Father', '+91 97350 88556', 'suresh.babu@email.com', 1, 3, 7, '08', 2);

-- Student Documents for Student 1
INSERT INTO `tbl_student_documents` (`document_id`, `student_id`, `document_type`, `document_name`, `file_path`, `status`) VALUES
(1, 1, 'Birth Certificate', 'Aarav_Nair_Birth_Certificate.pdf', 'uploads/documents/doc_001.pdf', 1),
(2, 1, 'Previous School TC', 'Transfer_Certificate_2025.pdf', 'uploads/documents/doc_002.pdf', 1),
(3, 1, 'ID Proof', 'Aadhaar_Card_Aarav.pdf', 'uploads/documents/doc_003.pdf', 1);

-- Attendance (Today: 2026-08-17)
INSERT INTO `tbl_attendance` (`attendance_id`, `student_id`, `academic_year_id`, `class_id`, `section_id`, `attendance_date`, `attendance_status`, `remarks`) VALUES
(1, 1, 1, 8, 1, '2026-08-17', 'Absent', 'Medical leave reported'),
(2, 2, 1, 7, 5, '2026-08-17', 'Present', ''),
(3, 3, 1, 10, 10, '2026-08-17', 'Present', ''),
(4, 4, 1, 5, 6, '2026-08-17', 'Present', ''),
(5, 5, 1, 6, 4, '2026-08-17', 'Present', ''),
(6, 6, 1, 1, 8, '2026-08-17', 'Absent', 'Uninformed absence'),
(7, 7, 1, 9, 9, '2026-08-17', 'Present', ''),
(8, 8, 1, 3, 7, '2026-08-17', 'Present', '');

-- Fee Heads & Structure
INSERT INTO `tbl_fee_heads` (`fee_head_id`, `head_name`, `description`, `status`) VALUES
(1, 'Term 1 Fees', 'Tuition and academic facilities for Term 1', 1),
(2, 'Term 2 Fees', 'Tuition and academic facilities for Term 2', 1),
(3, 'Term 3 Fees', 'Tuition and academic facilities for Term 3', 1),
(4, 'Annual Activities & Lab Fee', 'Computer lab, science lab and sports fee', 1);

INSERT INTO `tbl_fee_structures` (`fee_structure_id`, `fee_head_id`, `academic_year_id`, `class_id`, `amount`, `due_date`, `status`) VALUES
(1, 2, 1, 8, 12000.00, '2026-09-15', 1),
(2, 2, 1, 7, 12000.00, '2026-09-15', 1),
(3, 2, 1, 10, 14500.00, '2026-08-10', 1),
(4, 2, 1, 5, 9800.00, '2026-09-15', 1);

-- Student Fee Records
INSERT INTO `tbl_student_fees` (`student_fee_id`, `student_id`, `fee_structure_id`, `amount`, `paid_amount`, `due_date`, `payment_status`, `status`) VALUES
(1, 1, 1, 12000.00, 12000.00, '2026-09-15', 'Paid', 1),
(2, 2, 2, 12000.00, 0.00, '2026-09-15', 'Pending', 1),
(3, 3, 3, 14500.00, 0.00, '2026-08-10', 'Overdue', 1),
(4, 4, 4, 9800.00, 9800.00, '2026-09-15', 'Paid', 1);

-- Fee Payments
INSERT INTO `tbl_fee_payments` (`payment_id`, `student_fee_id`, `student_id`, `amount_paid`, `payment_mode`, `transaction_reference`, `payment_date`, `receipt_no`, `status`) VALUES
(1, 1, 1, 12000.00, 'UPI', 'UPI/2026/89472', '2026-08-17', 'REC-2026-001', 1),
(2, 4, 4, 9800.00, 'Bank Transfer', 'NEFT/2026/11094', '2026-08-16', 'REC-2026-002', 1);

-- Notices
INSERT INTO `tbl_notices` (`notice_id`, `title`, `posted_by`, `audience`, `notice_date`, `content`, `status`) VALUES
(1, 'Revised Bus Timings from Monday', 'Transport Office', 'All', '2026-08-15', 'Morning pickup routes will commence 10 minutes earlier due to road expansion on the bypass.', 1),
(2, 'Annual Sports Day Registrations Open', 'Physical Education', 'Grades 6-12', '2026-08-13', 'Students can register with their physical education teachers for track and field events.', 1),
(3, 'Library Books Due for Return', 'Library', 'All', '2026-08-11', 'All borrowed library books must be returned or renewed by Friday.', 1),
(4, 'Mid-Term Exam Timetable', 'Academics', 'Grades 6-12', '2026-08-05', 'Mid-Term examination timetable and syllabus have been published on the student portal.', 1);

-- Announcements
INSERT INTO `tbl_announcements` (`announcement_id`, `title`, `audience`, `announcement_date`, `content`, `status`) VALUES
(1, 'Independence Day Celebration on 22nd Aug', 'Whole School', '2026-08-11', 'Special assembly and cultural events organized by the secondary section.', 1),
(2, 'New Uniform Vendor Empanelled', 'Parents', '2026-08-09', 'Parents can purchase uniforms from the new verified campus counter.', 1),
(3, 'School Reopens After Onam Break', 'Whole School', '2026-08-01', 'Regular classes resume for all grades.', 1);

-- Upcoming Events
INSERT INTO `tbl_events` (`event_id`, `title`, `event_date`, `audience`, `venue`, `description`, `status`) VALUES
(1, 'Mid-Term Exams Begin', '2026-08-18', 'Grades 6-12 · All Sections', 'Examination Halls', 'First paper: English Language & Literature', 1),
(2, 'Independence Day Celebration', '2026-08-22', 'Whole School · Assembly Ground', 'Assembly Ground', 'Flag hoisting, parade and speech by Principal', 1),
(3, 'PTA Meeting', '2026-08-29', 'Grades 1-5 · 3:00 PM', 'Auditorium', 'Discussion on student academic progress and upcoming term events', 1);

-- Certificates
INSERT INTO `tbl_certificates` (`certificate_id`, `student_id`, `certificate_type`, `certificate_no`, `issue_date`, `remarks`, `status`) VALUES
(1, 1, 'Bonafide Certificate', 'CERT-2026-001', '2026-08-10', 'Issued for passport verification purpose', 1),
(2, 2, 'Transfer Certificate', 'CERT-2026-002', '2026-08-08', 'Issued on guardian request', 1),
(3, 3, 'Character Certificate', 'CERT-2026-003', '2026-08-05', 'Issued for college admission application', 1);

-- School Settings
INSERT INTO `tbl_school_settings` (`setting_id`, `school_name`, `school_code`, `established_year`, `principal_name`, `phone`, `email`, `website`, `address`, `description`) VALUES
(1, 'School', 'SCH-KL-2026', '1998', 'Antony Xavier', '+91 484 234 5678', 'info@school.edu', 'www.school.edu', 'Kakkanad, Ernakulam, Kerala - 682030', 'A CBSE-affiliated school known for excellence in academics and sports.');
