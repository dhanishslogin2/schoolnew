-- Migration: Section to Division
-- Renames tbl_sections to tbl_divisions and all referencing columns/indexes/constraints

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Drop old foreign key constraints referencing tbl_sections
ALTER TABLE tbl_attendance DROP FOREIGN KEY fk_att_section;
ALTER TABLE tbl_class_teachers DROP FOREIGN KEY fk_ct_section;
ALTER TABLE tbl_students DROP FOREIGN KEY fk_student_section;
ALTER TABLE tbl_subject_teachers DROP FOREIGN KEY fk_st_section;
ALTER TABLE tbl_timetable DROP FOREIGN KEY fk_tt_section;

-- 2. Rename tbl_sections -> tbl_divisions and update columns
RENAME TABLE tbl_sections TO tbl_divisions;

ALTER TABLE tbl_divisions 
  CHANGE COLUMN section_id division_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN section_name division_name VARCHAR(20) NOT NULL;

-- Rename indexes on tbl_divisions
ALTER TABLE tbl_divisions DROP INDEX idx_section_class;
ALTER TABLE tbl_divisions DROP INDEX idx_section_teacher;
ALTER TABLE tbl_divisions ADD INDEX idx_division_class (class_id);
ALTER TABLE tbl_divisions ADD INDEX idx_division_teacher (class_teacher_id);

-- 3. Update referencing columns in all tables

-- tbl_students
ALTER TABLE tbl_students CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;
ALTER TABLE tbl_students DROP INDEX idx_student_section;
ALTER TABLE tbl_students ADD INDEX idx_student_division (division_id);

-- tbl_attendance
ALTER TABLE tbl_attendance CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;
ALTER TABLE tbl_attendance DROP INDEX idx_att_date_class_sec;
ALTER TABLE tbl_attendance ADD INDEX idx_att_date_class_div (attendance_date, class_id, division_id);

-- tbl_class_teachers
ALTER TABLE tbl_class_teachers CHANGE COLUMN section_id division_id INT(10) UNSIGNED NOT NULL;
ALTER TABLE tbl_class_teachers DROP INDEX uk_ct_year_sec;
ALTER TABLE tbl_class_teachers ADD UNIQUE KEY uk_ct_year_div (academic_year_id, class_id, division_id);

-- tbl_subject_teachers
ALTER TABLE tbl_subject_teachers CHANGE COLUMN section_id division_id INT(10) UNSIGNED NOT NULL;
ALTER TABLE tbl_subject_teachers DROP INDEX uk_st_assignment;
ALTER TABLE tbl_subject_teachers ADD UNIQUE KEY uk_st_assignment (academic_year_id, class_id, division_id, subject_id, staff_id);

-- tbl_timetable
ALTER TABLE tbl_timetable CHANGE COLUMN section_id division_id INT(10) UNSIGNED NOT NULL;
ALTER TABLE tbl_timetable DROP INDEX uk_class_day_period;
ALTER TABLE tbl_timetable ADD UNIQUE KEY uk_class_day_period (academic_year_id, class_id, division_id, day, period_id);

-- tbl_admissions
ALTER TABLE tbl_admissions CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_assignments
ALTER TABLE tbl_assignments CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_exam_marks
ALTER TABLE tbl_exam_marks CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_exam_schedules
ALTER TABLE tbl_exam_schedules CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;
ALTER TABLE tbl_exam_schedules DROP INDEX uk_exam_class_sec_subj;
ALTER TABLE tbl_exam_schedules DROP INDEX idx_sched_class_sec;
ALTER TABLE tbl_exam_schedules ADD UNIQUE KEY uk_exam_class_div_subj (exam_id, class_id, division_id, subject_id);
ALTER TABLE tbl_exam_schedules ADD INDEX idx_sched_class_div (class_id, division_id);

-- tbl_homework
ALTER TABLE tbl_homework CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_leave_applications
ALTER TABLE tbl_leave_applications CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_notices
ALTER TABLE tbl_notices CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_student_fees
ALTER TABLE tbl_student_fees CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_student_id_cards
ALTER TABLE tbl_student_id_cards CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_student_promotions
ALTER TABLE tbl_student_promotions 
  CHANGE COLUMN from_section_id from_division_id INT(10) UNSIGNED NULL,
  CHANGE COLUMN to_section_id to_division_id INT(10) UNSIGNED NULL;

-- tbl_student_results
ALTER TABLE tbl_student_results 
  CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL,
  CHANGE COLUMN section_rank division_rank INT(10) UNSIGNED NULL;

-- tbl_student_transport_assignments
ALTER TABLE tbl_student_transport_assignments CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_subject_allocations
ALTER TABLE tbl_subject_allocations CHANGE COLUMN section_id division_id INT(10) UNSIGNED NOT NULL;
ALTER TABLE tbl_subject_allocations DROP INDEX uk_class_sec_sub;
ALTER TABLE tbl_subject_allocations ADD UNIQUE KEY uk_class_div_sub (academic_year_id, class_id, division_id, subject_id);

-- tbl_teacher_workload
ALTER TABLE tbl_teacher_workload CHANGE COLUMN section_id division_id INT(10) UNSIGNED NULL;

-- tbl_timetable_publish
ALTER TABLE tbl_timetable_publish CHANGE COLUMN section_id division_id INT(10) UNSIGNED NOT NULL;
ALTER TABLE tbl_timetable_publish DROP INDEX uk_tt_publish_class;
ALTER TABLE tbl_timetable_publish ADD UNIQUE KEY uk_tt_publish_class (academic_year_id, class_id, division_id);

-- 4. Re-establish foreign keys to tbl_divisions
ALTER TABLE tbl_students 
  ADD CONSTRAINT fk_student_division FOREIGN KEY (division_id) REFERENCES tbl_divisions(division_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE tbl_attendance 
  ADD CONSTRAINT fk_att_division FOREIGN KEY (division_id) REFERENCES tbl_divisions(division_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE tbl_class_teachers 
  ADD CONSTRAINT fk_ct_division FOREIGN KEY (division_id) REFERENCES tbl_divisions(division_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tbl_subject_teachers 
  ADD CONSTRAINT fk_st_division FOREIGN KEY (division_id) REFERENCES tbl_divisions(division_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tbl_timetable 
  ADD CONSTRAINT fk_tt_division FOREIGN KEY (division_id) REFERENCES tbl_divisions(division_id) ON DELETE CASCADE ON UPDATE CASCADE;

-- 5. Update permissions description if needed
UPDATE tbl_permissions 
SET description = 'View classes, divisions, and subjects' 
WHERE permission_key = 'academics.view';

UPDATE tbl_permissions 
SET description = 'Create and modify classes, divisions, and subjects' 
WHERE permission_key = 'academics.manage';

SET FOREIGN_KEY_CHECKS = 1;
