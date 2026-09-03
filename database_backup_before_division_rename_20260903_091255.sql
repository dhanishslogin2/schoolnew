-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_school
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_academic_calendar`
--

DROP TABLE IF EXISTS `tbl_academic_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_academic_calendar` (
  `calendar_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `event_type` enum('Holiday','Exam','Event','Activity','Meeting','Term Break','Other') NOT NULL DEFAULT 'Event',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `audience` varchar(100) NOT NULL DEFAULT 'Whole School',
  `venue` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`calendar_id`),
  KEY `academic_year_id` (`academic_year_id`),
  KEY `start_date` (`start_date`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_academic_calendar`
--

LOCK TABLES `tbl_academic_calendar` WRITE;
/*!40000 ALTER TABLE `tbl_academic_calendar` DISABLE KEYS */;
INSERT INTO `tbl_academic_calendar` VALUES (1,1,'Independence Day Celebration','Holiday','2026-08-15','2026-08-15','Whole School','School Main Ground','Flag hoisting ceremony and cultural performances.',1,'2026-08-18 17:03:02',NULL,'n'),(2,1,'First Term Mid-Term Examinations','Exam','2026-09-14','2026-09-22','Students','Examination Halls','Mid-term assessments for all grades from Grade 1 to 12.',1,'2026-08-18 17:03:02',NULL,'n'),(3,1,'Parent-Teacher Conference (Term 1)','Meeting','2026-10-03','2026-10-03','Parents','Auditorium & Classrooms','One-on-one parent teacher discussion on student academic performance.',1,'2026-08-18 17:03:02',NULL,'n'),(4,1,'Annual Inter-School Sports Meet','Activity','2026-10-24','2026-10-26','Whole School','Sports Complex','Track and field athletics competitions, football, basketball and relay events.',1,'2026-08-18 17:03:02',NULL,'n'),(5,1,'Diwali & Autumn Vacation','Term Break','2026-11-06','2026-11-12','Whole School',NULL,'School closed for festive Diwali holidays and autumn term break.',1,'2026-08-18 17:03:02',NULL,'n'),(6,1,'Annual Science & Robotics Exhibition','Event','2026-11-28','2026-11-28','Whole School','School Quadrangle','Student innovations, working models, and robotics lab exhibits.',1,'2026-08-18 17:03:02',NULL,'n'),(7,1,'PTA metting','Meeting','2026-09-02','2026-09-02','Whole School','Auditorio,','',0,'2026-09-02 08:40:54','2026-09-02 12:11:17','y'),(8,1,'Automated Test Holiday','Holiday','2026-12-25','2026-12-25','Whole School','Campus','Christmas Holiday test',0,'2026-09-02 09:03:16','2026-09-02 12:33:16','y');
/*!40000 ALTER TABLE `tbl_academic_calendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_academic_years`
--

DROP TABLE IF EXISTS `tbl_academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_academic_years` (
  `academic_year_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Only one row should be 1',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`academic_year_id`),
  UNIQUE KEY `uk_academic_year_name` (`year_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_academic_years`
--

LOCK TABLES `tbl_academic_years` WRITE;
/*!40000 ALTER TABLE `tbl_academic_years` DISABLE KEYS */;
INSERT INTO `tbl_academic_years` VALUES (1,'2026-2027','2026-06-01','2027-03-31',1,1,'2026-08-17 10:32:48','2026-08-27 16:09:38','n'),(2,'2025-2026','2025-06-01','2026-03-31',0,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,'2024-2025','2024-06-01','2025-03-31',0,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,'2028 - 2029','2028-06-01','2029-03-31',0,0,'2026-08-18 10:19:58','2026-09-02 11:03:38','y');
/*!40000 ALTER TABLE `tbl_academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_admissions`
--

DROP TABLE IF EXISTS `tbl_admissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_admissions` (
  `admission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Male',
  `date_of_birth` date NOT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned DEFAULT NULL,
  `guardian_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guardian_relation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Father',
  `guardian_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guardian_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_date` date NOT NULL,
  `status` enum('Pending','Approved','Admitted','Rejected','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `student_id` int(10) unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`admission_id`),
  UNIQUE KEY `uk_app_number` (`application_number`),
  KEY `idx_admission_student` (`student_id`),
  KEY `idx_admission_class` (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_admissions`
--

LOCK TABLES `tbl_admissions` WRITE;
/*!40000 ALTER TABLE `tbl_admissions` DISABLE KEYS */;
INSERT INTO `tbl_admissions` VALUES (1,'APP2026001','Aarav','Nair','Male','2011-06-12','B+',1,8,1,'Suresh Nair','Father','+91 98470 11223','suresh.nair@email.com','Thaikkattukara, Aluva','2026-05-15','Admitted',1,NULL,'2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(2,'APP2026002','Diya','Menon','Female','2012-02-03','O+',1,7,5,'Ramesh Menon','Father','+91 94470 22314','ramesh.menon@email.com','Kakkanad, Ernakulam','2026-05-16','Admitted',2,NULL,'2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(3,'APP2026010','Devika','Nambiar','Female','2013-08-14','A+',1,6,NULL,'Sreejith Nambiar','Father','+91 98460 77112','sreejith.n@email.com','Palarivattom, Kochi','2026-08-10','Rejected',NULL,NULL,'2026-08-18 12:34:23','2026-08-18 12:44:11','n'),(4,'APP2026011','Rahul','Varma','Male','2012-11-20','B+',1,7,NULL,'Govind Varma','Father','+91 97450 33445','govind.v@email.com','Fort Kochi, Ernakulam','2026-08-12','Approved',NULL,NULL,'2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(5,'APP2026012','Hina','Fathima','Female','2015-03-08','O-',1,4,NULL,'Faisal Ahmed','Father','+91 96330 88990','faisal.a@email.com','Mattancherry, Kochi','2026-08-14','Approved',NULL,NULL,'2026-08-18 12:34:23','2026-08-18 12:44:07','n');
/*!40000 ALTER TABLE `tbl_admissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_announcements`
--

DROP TABLE IF EXISTS `tbl_announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_announcements` (
  `announcement_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `audience` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Whole School',
  `target_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All',
  `announcement_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `priority` enum('Normal','Important','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posted_by` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Principal',
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Published','Scheduled','Archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Published',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_announcements`
--

LOCK TABLES `tbl_announcements` WRITE;
/*!40000 ALTER TABLE `tbl_announcements` DISABLE KEYS */;
INSERT INTO `tbl_announcements` VALUES (1,'Independence Day Celebration on 22nd Aug','General','Whole School','All','2026-08-11',NULL,'Normal',NULL,'Principal','Special assembly and cultural events organized by the secondary section.','Draft','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,'New Uniform Vendor Empanelled','General','Parents','All','2026-08-09',NULL,'Normal',NULL,'Principal','Parents can purchase uniforms from the new verified campus counter.','Draft','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,'School Reopens After Onam Break','General','Whole School','All','2026-08-01',NULL,'Normal',NULL,'Principal','Regular classes resume for all grades.','Draft','2026-08-17 10:32:48','2026-08-17 10:32:48','n');
/*!40000 ALTER TABLE `tbl_announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_assignment_submissions`
--

DROP TABLE IF EXISTS `tbl_assignment_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_assignment_submissions` (
  `submission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `submission_version` int(10) unsigned NOT NULL DEFAULT 1,
  `submitted_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_files` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `late_duration_minutes` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('Pending','Submitted','Late','Reviewed','Returned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Submitted',
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teacher_remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correction_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_by` int(10) unsigned DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`submission_id`),
  UNIQUE KEY `uk_asgn_student` (`assignment_id`,`student_id`),
  KEY `idx_subm_asgn` (`assignment_id`),
  KEY `idx_subm_student` (`student_id`),
  KEY `idx_subm_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_assignment_submissions`
--

LOCK TABLES `tbl_assignment_submissions` WRITE;
/*!40000 ALTER TABLE `tbl_assignment_submissions` DISABLE KEYS */;
INSERT INTO `tbl_assignment_submissions` VALUES (1,2,1,1,'Here are my solutions to all 10 problems.','[{\"orig_name\":\"algebra_solution.pdf\",\"file_name\":\"asgn_test.pdf\",\"file_size\":2048}]','2026-08-20 12:50:22',0,0,'Submitted',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-20 12:50:22','2026-08-20 12:50:22','n');
/*!40000 ALTER TABLE `tbl_assignment_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_assignment_types`
--

DROP TABLE IF EXISTS `tbl_assignment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_assignment_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_assignment_types`
--

LOCK TABLES `tbl_assignment_types` WRITE;
/*!40000 ALTER TABLE `tbl_assignment_types` DISABLE KEYS */;
INSERT INTO `tbl_assignment_types` VALUES (1,'Homework','Standard daily subject homework tasks',1,'2026-08-20 12:44:58','n'),(2,'Classwork','In-class exercises and problem sets',1,'2026-08-20 12:44:58','n'),(3,'Project','Term projects, research, and collaborative assignments',1,'2026-08-20 12:44:58','n'),(4,'Worksheet','Practice worksheets and practice questionnaires',1,'2026-08-20 12:44:58','n'),(5,'Reading','Assigned chapter readings and literature study',1,'2026-08-20 12:44:58','n'),(6,'Practical','Laboratory experiment reports and practical write-ups',1,'2026-08-20 12:44:58','n'),(7,'Activity','Creative activities, drawings, and extracurricular tasks',1,'2026-08-20 12:44:58','n');
/*!40000 ALTER TABLE `tbl_assignment_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_assignments`
--

DROP TABLE IF EXISTS `tbl_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_assignments` (
  `assignment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `teacher_id` int(10) unsigned NOT NULL,
  `assignment_type_id` int(10) unsigned NOT NULL DEFAULT 1,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_date` date NOT NULL,
  `due_date` date NOT NULL,
  `due_time` time DEFAULT NULL,
  `max_marks` decimal(5,2) NOT NULL DEFAULT 10.00,
  `allow_remarks` tinyint(1) NOT NULL DEFAULT 1,
  `allow_file_submission` tinyint(1) NOT NULL DEFAULT 1,
  `allow_text_submission` tinyint(1) NOT NULL DEFAULT 1,
  `allow_multiple_files` tinyint(1) NOT NULL DEFAULT 0,
  `allow_resubmission` tinyint(1) NOT NULL DEFAULT 1,
  `allow_late_submission` tinyint(1) NOT NULL DEFAULT 1,
  `target_type` enum('Class','Section','Individual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Section',
  `target_student_ids` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Published','Active','Closed','Archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Published',
  `attachments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`assignment_id`),
  KEY `idx_asgn_class` (`class_id`,`section_id`),
  KEY `idx_asgn_subject` (`subject_id`),
  KEY `idx_asgn_teacher` (`teacher_id`),
  KEY `idx_asgn_due` (`due_date`),
  KEY `idx_asgn_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_assignments`
--

LOCK TABLES `tbl_assignments` WRITE;
/*!40000 ALTER TABLE `tbl_assignments` DISABLE KEYS */;
INSERT INTO `tbl_assignments` VALUES (1,1,1,1,1,1,1,'Quadratic Equations Practice Set','Solve exercises 4.1 and 4.2 from textbook Chapter 4.','Show complete working steps for all questions. Upload clear scanned PDF or image copy.','2026-08-20','2026-08-24','17:00:00',20.00,1,1,1,1,1,1,'Section',NULL,'Published',NULL,1,'2026-08-20 12:44:58','2026-08-20 12:44:58','n'),(2,1,1,1,1,1,1,'Unit 3 Algebra Worksheet 1787210422','Test description','Complete all problems','2026-08-20','2026-08-23','23:59:00',25.00,1,1,1,1,1,1,'Section',NULL,'Published',NULL,1,'2026-08-20 12:50:22','2026-08-20 12:50:22','n');
/*!40000 ALTER TABLE `tbl_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_attendance`
--

DROP TABLE IF EXISTS `tbl_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_attendance` (
  `attendance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_type` enum('Daily','Period-wise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Daily',
  `period_id` int(10) unsigned DEFAULT NULL,
  `subject_id` int(10) unsigned DEFAULT NULL,
  `attendance_status` enum('Present','Absent','Late','Late Coming','Half Day','Excused','Leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Present',
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marked_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`attendance_id`),
  KEY `idx_att_date_class_sec` (`attendance_date`,`class_id`,`section_id`),
  KEY `idx_att_student` (`student_id`),
  KEY `fk_att_academic_year` (`academic_year_id`),
  KEY `fk_att_class` (`class_id`),
  KEY `fk_att_section` (`section_id`),
  KEY `idx_att_period` (`period_id`),
  KEY `idx_student_date_type_period` (`student_id`,`attendance_date`,`attendance_type`,`period_id`),
  KEY `idx_attendance_date_year` (`attendance_date`,`academic_year_id`,`is_deleted`),
  CONSTRAINT `fk_att_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_att_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_att_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_attendance`
--

LOCK TABLES `tbl_attendance` WRITE;
/*!40000 ALTER TABLE `tbl_attendance` DISABLE KEYS */;
INSERT INTO `tbl_attendance` VALUES (1,1,1,8,1,'2026-08-17','Daily',NULL,NULL,'Absent','Medical leave reported',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,2,1,7,5,'2026-08-17','Daily',NULL,NULL,'Present','',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,3,1,10,10,'2026-08-17','Daily',NULL,NULL,'Present','',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,4,1,5,6,'2026-08-17','Daily',NULL,NULL,'Present','',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(5,5,1,6,4,'2026-08-17','Daily',NULL,NULL,'Present','',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(6,6,1,1,8,'2026-08-17','Daily',NULL,NULL,'Absent','Uninformed absence',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(7,7,1,9,9,'2026-08-17','Daily',NULL,NULL,'Present','',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(8,8,1,3,7,'2026-08-17','Daily',NULL,NULL,'Present','',NULL,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(9,2,1,7,5,'2026-08-20','Daily',NULL,NULL,'Present','On time',1,'2026-08-20 11:47:16','2026-08-20 11:47:16','n'),(10,3,1,10,10,'2026-08-20','Daily',NULL,NULL,'Absent','Sick leave',1,'2026-08-20 11:47:16','2026-08-20 11:47:16','n'),(11,4,1,5,6,'2026-08-20','Daily',NULL,NULL,'Late','Bus delay',1,'2026-08-20 11:47:16','2026-08-20 11:47:16','n'),(12,2,1,7,5,'2026-08-20','Period-wise',1,NULL,'Present','Maths period',1,'2026-08-20 11:47:16','2026-08-20 11:47:16','n'),(13,2,1,7,5,'2026-08-20','Period-wise',2,NULL,'Late','Science period',1,'2026-08-20 11:47:16','2026-08-20 11:47:16','n'),(14,31,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(15,25,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(16,26,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(17,30,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(18,27,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(19,28,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(20,29,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(21,18,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(22,21,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(23,17,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(24,15,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(25,24,1,1,12,'2026-09-02','Daily',NULL,NULL,'Present','',15,'2026-09-02 12:26:54','2026-09-02 12:26:54','n'),(26,31,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(27,25,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(28,26,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(29,30,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(30,27,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(31,28,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(32,29,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(33,18,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(34,21,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(35,17,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(36,6,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(37,15,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(38,24,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(39,9,1,1,12,'2026-09-03','Daily',NULL,NULL,'Present','',15,'2026-09-03 06:20:47','2026-09-03 06:20:47','n'),(40,31,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(41,25,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(42,26,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(43,30,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(44,27,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(45,28,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(46,29,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(47,18,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(48,21,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(49,17,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(50,6,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(51,15,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(52,24,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n'),(53,9,1,1,12,'2026-09-03','Period-wise',1,NULL,'Present','',15,'2026-09-03 07:27:25','2026-09-03 07:27:25','n');
/*!40000 ALTER TABLE `tbl_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_attendance_notifications`
--

DROP TABLE IF EXISTS `tbl_attendance_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_attendance_notifications` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `parent_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance_id` int(10) unsigned DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `notification_type` enum('Absent','Late','Excused','Attendance Summary') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Pending','Sent','Failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`notification_id`),
  KEY `idx_notif_student` (`student_id`),
  KEY `idx_notif_date` (`attendance_date`),
  KEY `idx_notif_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_attendance_notifications`
--

LOCK TABLES `tbl_attendance_notifications` WRITE;
/*!40000 ALTER TABLE `tbl_attendance_notifications` DISABLE KEYS */;
INSERT INTO `tbl_attendance_notifications` VALUES (1,3,'Parent of Kiran',NULL,NULL,10,'2026-08-20','Absent','Dear Parent, your child was marked absent today.','Pending','2026-08-20 11:47:16','2026-08-20 11:47:16','n'),(2,4,'Parent of Ananya',NULL,NULL,11,'2026-08-20','Late','Dear Parent, your child was marked late today.','Pending','2026-08-20 11:47:16','2026-08-20 11:47:16','n');
/*!40000 ALTER TABLE `tbl_attendance_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_attendance_settings`
--

DROP TABLE IF EXISTS `tbl_attendance_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_attendance_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enable_present` tinyint(1) NOT NULL DEFAULT 1,
  `enable_absent` tinyint(1) NOT NULL DEFAULT 1,
  `enable_late` tinyint(1) NOT NULL DEFAULT 1,
  `enable_excused` tinyint(1) NOT NULL DEFAULT 1,
  `enable_period_attendance` tinyint(1) NOT NULL DEFAULT 1,
  `enable_absent_notification` tinyint(1) NOT NULL DEFAULT 1,
  `enable_late_notification` tinyint(1) NOT NULL DEFAULT 1,
  `enable_summary_notification` tinyint(1) NOT NULL DEFAULT 1,
  `absent_template` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `late_template` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excused_template` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary_template` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_timing` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'On Marking',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_attendance_settings`
--

LOCK TABLES `tbl_attendance_settings` WRITE;
/*!40000 ALTER TABLE `tbl_attendance_settings` DISABLE KEYS */;
INSERT INTO `tbl_attendance_settings` VALUES (1,1,1,1,1,1,1,1,1,'Dear Parent, your child {student_name} was marked absent on {date}.','Dear Parent, your child {student_name} was marked late on {date}.','Dear Parent, your child {student_name} has been excused on {date}.','Attendance summary for {student_name}: Present {present_days}, Absent {absent_days}, Late {late_days}, Excused {excused_days}.','On Marking','2026-08-20 11:39:28','2026-08-20 11:39:28','n');
/*!40000 ALTER TABLE `tbl_attendance_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_certificate_audit_logs`
--

DROP TABLE IF EXISTS `tbl_certificate_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_certificate_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_cert_audit_action` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_certificate_audit_logs`
--

LOCK TABLES `tbl_certificate_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_certificate_audit_logs` DISABLE KEYS */;
INSERT INTO `tbl_certificate_audit_logs` VALUES (1,1,'Certificate Generated','Certificate',0,'Integration test generation verification','2026-08-20 15:32:30','n'),(2,1,'Certificate Generated','Certificate',0,'Integration test generation verification','2026-08-20 15:32:57','n'),(3,1,'Certificate Generated','Certificate',5,'Integration test generation verification','2026-08-20 15:38:20','n');
/*!40000 ALTER TABLE `tbl_certificate_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_certificate_requests`
--

DROP TABLE IF EXISTS `tbl_certificate_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_certificate_requests` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `certificate_type_id` int(10) unsigned NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_date` date NOT NULL,
  `required_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supporting_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Pending','Under Verification','Correction Required','Approved','Rejected','Generated','Printed','Issued','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requested_by` int(10) unsigned DEFAULT NULL,
  `verified_by` int(10) unsigned DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`request_id`),
  KEY `idx_cr_student` (`student_id`),
  KEY `idx_cr_type` (`certificate_type_id`),
  KEY `idx_cr_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_certificate_requests`
--

LOCK TABLES `tbl_certificate_requests` WRITE;
/*!40000 ALTER TABLE `tbl_certificate_requests` DISABLE KEYS */;
INSERT INTO `tbl_certificate_requests` VALUES (1,1,1,1,'Passport application verification requirement','2026-08-10','2026-08-15',NULL,NULL,'Approved',NULL,1,NULL,NULL,'2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(2,2,1,2,'Relocating to another city due to parental job transfer','2026-08-12','2026-08-20',NULL,NULL,'Pending',NULL,1,NULL,NULL,'2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(3,3,1,3,'National scholarship application submission','2026-08-14','2026-08-22',NULL,NULL,'Pending',NULL,1,NULL,NULL,'2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(4,4,1,4,'Participation in state athletic championship','2026-08-16','2026-08-25',NULL,NULL,'Pending',NULL,1,NULL,NULL,'2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(5,1,1,1,'Passport application verification','2026-08-20',NULL,NULL,NULL,'Approved',NULL,1,NULL,1,'2026-08-20 15:32:30','2026-08-20 15:32:30','n'),(6,1,1,1,'Passport application verification','2026-08-20',NULL,NULL,NULL,'Approved',NULL,1,NULL,1,'2026-08-20 15:32:57','2026-08-20 15:32:57','n'),(7,1,1,1,'Passport application verification','2026-08-20',NULL,NULL,NULL,'Approved',NULL,1,NULL,1,'2026-08-20 15:38:20','2026-08-20 15:38:20','n');
/*!40000 ALTER TABLE `tbl_certificate_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_certificate_settings`
--

DROP TABLE IF EXISTS `tbl_certificate_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_certificate_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numbering_format` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '{PREFIX}{YEAR}-{NUMBER}',
  `number_sequence_length` int(10) unsigned NOT NULL DEFAULT 5,
  `require_approval` tinyint(1) NOT NULL DEFAULT 1,
  `require_document_verification` tinyint(1) NOT NULL DEFAULT 0,
  `require_fee_clearance_for_tc` tinyint(1) NOT NULL DEFAULT 1,
  `require_library_clearance_for_tc` tinyint(1) NOT NULL DEFAULT 0,
  `require_transport_clearance_for_tc` tinyint(1) NOT NULL DEFAULT 0,
  `principal_signature_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `authorized_signature_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `watermark_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `default_paper_size` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A4',
  `default_orientation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Portrait',
  `document_expiry_reminder_days` int(10) unsigned NOT NULL DEFAULT 30,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_certificate_settings`
--

LOCK TABLES `tbl_certificate_settings` WRITE;
/*!40000 ALTER TABLE `tbl_certificate_settings` DISABLE KEYS */;
INSERT INTO `tbl_certificate_settings` VALUES (1,'{PREFIX}{YEAR}-{NUMBER}',5,1,0,1,0,0,NULL,NULL,1,'A4','Portrait',30,'2026-08-20 15:26:20','2026-08-20 15:26:20','n');
/*!40000 ALTER TABLE `tbl_certificate_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_certificate_templates`
--

DROP TABLE IF EXISTS `tbl_certificate_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_certificate_templates` (
  `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_position` enum('Top-Left','Top-Center','Top-Right','None') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Top-Center',
  `signature_layout` enum('Principal-Only','Principal-And-Officer','Officer-Only') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Principal-Only',
  `paper_size` enum('A4','Letter','Legal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A4',
  `orientation` enum('Portrait','Landscape') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Portrait',
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`template_id`),
  KEY `idx_tmpl_code` (`type_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_certificate_templates`
--

LOCK TABLES `tbl_certificate_templates` WRITE;
/*!40000 ALTER TABLE `tbl_certificate_templates` DISABLE KEYS */;
INSERT INTO `tbl_certificate_templates` VALUES (1,'Standard Bonafide Certificate','BONAFIDE','BONAFIDE CERTIFICATE','<p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>This is to certify that <strong>{student_name}</strong> (Admission No: <strong>{admission_number}</strong>), child of <strong>{parent_name}</strong>, is a bonafide student of <strong>{school_name}</strong> studying in <strong>{class} - {section}</strong> during the academic year <strong>{academic_year}</strong>.</p><p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>According to the school admission register, the date of birth of the student is <strong>{date_of_birth}</strong>. The student bears a good moral character.</p>','This certificate is issued on request for official verification purposes.','Top-Center','Principal-Only','A4','Portrait','Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(2,'Standard Transfer Certificate','TC','TRANSFER CERTIFICATE','<table style=\'width:100%; border-collapse: collapse; font-size: 14px; margin-top: 15px;\' cellpadding=\'8\'>\n      <tr><td style=\'width:40%; font-weight: bold;\'>1. Name of the Pupil:</td><td>{student_name}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>2. Admission Number:</td><td>{admission_number}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>3. Father\'s / Guardian\'s Name:</td><td>{parent_name}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>4. Date of Birth (as per register):</td><td>{date_of_birth}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>5. Class in which pupil last studied:</td><td>{class} - {section}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>6. Academic Year / Session:</td><td>{academic_year}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>7. Date of Admission to School:</td><td>{admission_date}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>8. Date of Pupil\'s Leaving:</td><td>{date_of_leaving}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>9. Reason for Leaving:</td><td>{reason_for_leaving}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>10. Total Working / Attendance Days:</td><td>{attendance_summary}</td></tr>\n      <tr><td style=\'font-weight: bold;\'>11. General Conduct and Character:</td><td>Good</td></tr>\n      <tr><td style=\'font-weight: bold;\'>12. School Fee Clearance:</td><td>Fully Cleared</td></tr>\n    </table>','Certified that the above entries are verified from the institution admission ledger.','Top-Center','Principal-And-Officer','A4','Portrait','Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(3,'Standard Study Certificate','STUDY','STUDY & ENROLLMENT CERTIFICATE','<p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>This is to certify that <strong>{student_name}</strong>, Admission No: <strong>{admission_number}</strong>, has successfully pursued education at <strong>{school_name}</strong> in <strong>{class}</strong> during the academic session <strong>{academic_year}</strong>. The student was admitted on <strong>{admission_date}</strong> and has maintained satisfactory academic progress.</p>','Issued under official seal for higher studies / scholarship.','Top-Center','Principal-Only','A4','Portrait','Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(4,'Standard Conduct Certificate','CONDUCT','CERTIFICATE OF CHARACTER & CONDUCT','<p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>This is to certify that <strong>{student_name}</strong>, son/daughter of <strong>{parent_name}</strong>, was a student of Class <strong>{class}</strong> in <strong>{school_name}</strong>. During the period of study in this institution, the conduct, character, and general behavior of the student have been found to be <strong>Exemplary and Good</strong>.</p>','We wish the student every success in all future endeavors.','Top-Center','Principal-Only','A4','Portrait','Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n');
/*!40000 ALTER TABLE `tbl_certificate_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_certificate_types`
--

DROP TABLE IF EXISTS `tbl_certificate_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_certificate_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CERT-',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_cert_type_code` (`type_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_certificate_types`
--

LOCK TABLES `tbl_certificate_types` WRITE;
/*!40000 ALTER TABLE `tbl_certificate_types` DISABLE KEYS */;
INSERT INTO `tbl_certificate_types` VALUES (1,'Bonafide Certificate','BONAFIDE','BON-','Official verification that student is studying in this school',1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(2,'Transfer Certificate','TC','TC-','Official leaving certificate issued when leaving the institution',1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(3,'Study Certificate','STUDY','STU-','Official document certifying course of study and academic period',1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(4,'Conduct Certificate','CONDUCT','CON-','Official certificate certifying character and conduct of student',1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(5,'Character Certificate','CHARACTER','CHAR-','Official character and discipline reference certificate',1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n');
/*!40000 ALTER TABLE `tbl_certificate_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_certificate_versions`
--

DROP TABLE IF EXISTS `tbl_certificate_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_certificate_versions` (
  `version_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `certificate_id` int(10) unsigned NOT NULL,
  `version_number` int(10) unsigned NOT NULL,
  `certificate_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_snapshot` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`version_id`),
  KEY `idx_cv_cert` (`certificate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_certificate_versions`
--

LOCK TABLES `tbl_certificate_versions` WRITE;
/*!40000 ALTER TABLE `tbl_certificate_versions` DISABLE KEYS */;
INSERT INTO `tbl_certificate_versions` VALUES (1,0,1,'BON-2026-00001','<p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>This is to certify that <strong>Aarav Nair</strong> (Admission No: <strong>EDU2026001</strong>), child of <strong>Suresh Nair</strong>, is a bonafide student of <strong>EduCore Public School</strong> studying in <strong>Grade 10 - A</strong> during the academic year <strong>2026-2027</strong>.</p><p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>According to the school admission register, the date of birth of the student is <strong>12-06-2011</strong>. The student bears a good moral character.</p>','Name spelling correction requested by guardian',1,'2026-08-20 15:32:30','n'),(2,0,1,'BON-2026-00001','<p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>This is to certify that <strong>Aarav Nair</strong> (Admission No: <strong>EDU2026001</strong>), child of <strong>Suresh Nair</strong>, is a bonafide student of <strong>EduCore Public School</strong> studying in <strong>Grade 10 - A</strong> during the academic year <strong>2026-2027</strong>.</p><p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>According to the school admission register, the date of birth of the student is <strong>12-06-2011</strong>. The student bears a good moral character.</p>','Name spelling correction requested by guardian',1,'2026-08-20 15:32:57','n'),(3,5,1,'BON-2026-00001','<p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>This is to certify that <strong>Aarav Nair</strong> (Admission No: <strong>EDU2026001</strong>), child of <strong>Suresh Nair</strong>, is a bonafide student of <strong>EduCore Public School</strong> studying in <strong>Grade 10 - A</strong> during the academic year <strong>2026-2027</strong>.</p><p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>According to the school admission register, the date of birth of the student is <strong>12-06-2011</strong>. The student bears a good moral character.</p>','Name spelling correction requested by guardian',1,'2026-08-20 15:38:20','n');
/*!40000 ALTER TABLE `tbl_certificate_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_certificates`
--

DROP TABLE IF EXISTS `tbl_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_certificates` (
  `certificate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `certificate_type_id` int(10) unsigned DEFAULT NULL,
  `certificate_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `certificate_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_id` int(10) unsigned DEFAULT NULL,
  `issue_date` date NOT NULL,
  `student_data_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`student_data_snapshot`)),
  `generated_content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `is_reissued` tinyint(1) NOT NULL DEFAULT 0,
  `reissue_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_by` int(10) unsigned DEFAULT NULL,
  `issued_by` int(10) unsigned DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Generated',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`certificate_id`),
  UNIQUE KEY `uk_certificate_no` (`certificate_no`),
  KEY `idx_cert_student` (`student_id`),
  CONSTRAINT `fk_cert_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_certificates`
--

LOCK TABLES `tbl_certificates` WRITE;
/*!40000 ALTER TABLE `tbl_certificates` DISABLE KEYS */;
INSERT INTO `tbl_certificates` VALUES (1,1,1,NULL,'Bonafide Certificate',NULL,'CERT-2026-001',NULL,'2026-08-10',NULL,NULL,1,0,NULL,'Issued for passport verification purpose',NULL,NULL,'1','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,2,1,NULL,'Transfer Certificate',NULL,'CERT-2026-002',NULL,'2026-08-08',NULL,NULL,1,0,NULL,'Issued on guardian request',NULL,NULL,'1','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,3,1,NULL,'Character Certificate',NULL,'CERT-2026-003',NULL,'2026-08-05',NULL,NULL,1,0,NULL,'Issued for college admission application',NULL,NULL,'1','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,13,1,NULL,'Transfer Certificate',NULL,'TC/2026/338',NULL,'2026-08-18',NULL,NULL,1,0,NULL,'Relocation to Bangalore',NULL,NULL,'1','2026-08-18 12:38:47','2026-08-18 12:38:47','n'),(5,1,1,1,'Bonafide Certificate',1,'BON-2026-00001',7,'2026-08-20',NULL,'<p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>This is to certify that <strong>Aarav Nair</strong> (Admission No: <strong>EDU2026001</strong>), child of <strong>Suresh Nair</strong>, is a bonafide student of <strong>EduCore Public School</strong> studying in <strong>Grade 10 - A</strong> during the academic year <strong>2026-2027</strong>.</p><p style=\'font-size: 16px; line-height: 1.8; text-align: justify;\'>According to the school admission register, the date of birth of the student is <strong>12-06-2011</strong>. The student bears a good moral character.</p>',2,1,'Name spelling correction',NULL,1,NULL,'Generated','2026-08-20 15:38:20','2026-08-20 15:38:20','n'),(6,2,1,NULL,'Transfer Certificate',NULL,'TC/2026/396',NULL,'2026-08-27',NULL,NULL,1,0,NULL,'Parent Relocation / Job Transfer',NULL,NULL,'1','2026-08-27 16:06:32','2026-08-27 16:06:32','n');
/*!40000 ALTER TABLE `tbl_certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_class_teachers`
--

DROP TABLE IF EXISTS `tbl_class_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_class_teachers` (
  `class_teacher_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `staff_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`class_teacher_id`),
  UNIQUE KEY `uk_ct_year_sec` (`academic_year_id`,`class_id`,`section_id`),
  KEY `idx_ct_staff` (`staff_id`),
  KEY `fk_ct_class` (`class_id`),
  KEY `fk_ct_section` (`section_id`),
  CONSTRAINT `fk_ct_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ct_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ct_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ct_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_class_teachers`
--

LOCK TABLES `tbl_class_teachers` WRITE;
/*!40000 ALTER TABLE `tbl_class_teachers` DISABLE KEYS */;
INSERT INTO `tbl_class_teachers` VALUES (1,1,8,1,1,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(2,1,8,2,2,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(3,1,7,4,5,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(4,1,7,5,8,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n');
/*!40000 ALTER TABLE `tbl_class_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_classes`
--

DROP TABLE IF EXISTS `tbl_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_classes` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_teacher_id` int(10) unsigned DEFAULT NULL,
  `capacity` int(10) unsigned NOT NULL DEFAULT 40,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`class_id`),
  KEY `idx_class_academic_year` (`academic_year_id`),
  KEY `idx_class_teacher` (`class_teacher_id`),
  KEY `idx_classes_year_status` (`academic_year_id`,`status`,`is_deleted`),
  CONSTRAINT `fk_class_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_class_teacher` FOREIGN KEY (`class_teacher_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_classes`
--

LOCK TABLES `tbl_classes` WRITE;
/*!40000 ALTER TABLE `tbl_classes` DISABLE KEYS */;
INSERT INTO `tbl_classes` VALUES (1,1,'LKG','CLS-LKG',9,28,NULL,1,'2026-08-17 10:32:48','2026-08-21 14:22:39','n'),(8,1,'Grade 10','CLS-10',1,40,NULL,1,'2026-08-17 10:32:48','2026-08-21 14:22:39','n'),(9,1,'Grade 11','CLS-11',1,40,NULL,1,'2026-08-17 10:32:48','2026-08-21 14:22:39','n'),(10,1,'Grade 12','CLS-12',2,36,NULL,1,'2026-08-17 10:32:48','2026-08-21 14:22:39','n'),(13,1,'UKG','CLS-UKG',NULL,20,'',1,'2026-09-02 07:34:15','2026-09-02 11:04:15','n');
/*!40000 ALTER TABLE `tbl_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_communication_audit_logs`
--

DROP TABLE IF EXISTS `tbl_communication_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_communication_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_comm_action` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_communication_audit_logs`
--

LOCK TABLES `tbl_communication_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_communication_audit_logs` DISABLE KEYS */;
INSERT INTO `tbl_communication_audit_logs` VALUES (1,1,'Notification Retried','Message',12,'Test manual retry execution verification','2026-08-20 15:53:41','n'),(2,1,'Notification Retried','Message',16,'Test manual retry execution verification','2026-08-20 16:37:02','n');
/*!40000 ALTER TABLE `tbl_communication_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_communication_groups`
--

DROP TABLE IF EXISTS `tbl_communication_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_communication_groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_type` enum('Teachers','Parents','Management','Custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Custom',
  `member_user_ids` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_communication_groups`
--

LOCK TABLES `tbl_communication_groups` WRITE;
/*!40000 ALTER TABLE `tbl_communication_groups` DISABLE KEYS */;
INSERT INTO `tbl_communication_groups` VALUES (1,'All Teachers','All active teaching faculty members across classes','Teachers','[1, 2, 3, 4]',1,1,'2026-08-20 12:56:29','2026-08-20 12:56:29','n'),(2,'Class 10 Parents','Parents and guardians of Grade 10 students','Parents','[1, 2, 3]',1,1,'2026-08-20 12:56:29','2026-08-20 12:56:29','n'),(3,'Administrative Staff','Administrative and office management staff','Management','[1, 2]',1,1,'2026-08-20 12:56:29','2026-08-20 12:56:29','n');
/*!40000 ALTER TABLE `tbl_communication_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_communication_messages`
--

DROP TABLE IF EXISTS `tbl_communication_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_communication_messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_module` enum('Direct','Attendance','Fees','Homework','Examination','Timetable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Direct',
  `source_ref_id` int(10) unsigned DEFAULT NULL,
  `channel` enum('In-App','SMS','WhatsApp','Email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'In-App',
  `template_id` int(10) unsigned DEFAULT NULL,
  `template_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_id` int(10) unsigned DEFAULT NULL,
  `recipient_type` enum('All','Role','Class','Section','Individual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Individual',
  `recipient_id` int(10) unsigned DEFAULT NULL,
  `recipient_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipient_contact` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rendered_message` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('Normal','Important','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `idempotency_key` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `status` enum('Pending','Scheduled','Processing','Sent','Delivered','Failed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sent',
  `failure_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `retry_count` int(10) unsigned NOT NULL DEFAULT 0,
  `max_retries` int(10) unsigned NOT NULL DEFAULT 3,
  `last_attempt_at` datetime DEFAULT NULL,
  `next_retry_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`message_id`),
  KEY `idx_msg_channel` (`channel`),
  KEY `idx_msg_status` (`status`),
  KEY `idx_msg_scheduled` (`scheduled_at`),
  KEY `idx_msg_source` (`source_module`,`source_ref_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_communication_messages`
--

LOCK TABLES `tbl_communication_messages` WRITE;
/*!40000 ALTER TABLE `tbl_communication_messages` DISABLE KEYS */;
INSERT INTO `tbl_communication_messages` VALUES (4,'Attendance Absent','Attendance',1,'SMS',1,'ATTENDANCE_ABSENT',NULL,'',1,'Suresh Nair','+91 98470 11223','Student Absence Alert','Dear {parent_name}, your child {student_name} was marked ABSENT today.','Dear Suresh Nair, your child Aarav Nair (Adm: EDU2026001) was marked ABSENT today, 20-08-2026. Please contact the school office if this is in error. - EduCore Public School',NULL,'Important',NULL,NULL,'2026-08-20 09:15:00','2026-08-20 09:15:12','Delivered',NULL,0,3,NULL,NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(5,'Fee Overdue','Fees',3,'WhatsApp',4,'FEE_OVERDUE',NULL,'',3,'Thomas Varghese','+91 90480 33556','URGENT: Outstanding Fee Overdue','Dear {parent_name}, fee of Rs.{amount} for {student_name} is overdue.','Dear Thomas Varghese, this is an urgent reminder that fee of Rs.14,500.00 for Kiran Thomas (EDU2026003) was due on 10-08-2026 and is now OVERDUE. Please clear the balance immediately.',NULL,'Urgent',NULL,NULL,'2026-08-20 10:30:00',NULL,'Sent',NULL,0,3,NULL,NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(6,'Homework Published','Homework',2,'In-App',6,'HOMEWORK_ASSIGNED',NULL,'',1,'Aarav Nair','aarav.nair@email.com','New Assignment in Mathematics','A new assignment has been published.','A new assignment \"Quadratic Equations Problem Set 3\" in Mathematics has been published for Class Grade 10-A. Due date: 2026-08-25.',NULL,'Normal',NULL,NULL,'2026-08-20 11:00:00','2026-08-20 11:00:05','Delivered',NULL,0,3,NULL,NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(7,'Exam Schedule Published','Examination',1,'In-App',8,'EXAM_SCHEDULED',NULL,'',2,'Ramesh Menon','+91 94470 22314','Examination Timetable Released','The timetable for Mid-Term Exams has been published.','The timetable for Mid-Term Examination 2026 has been published. Exams commence on 2026-08-18. Please check the student portal for details.',NULL,'Important',NULL,NULL,NULL,NULL,'Pending',NULL,0,3,NULL,NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(8,'Transport Route Update','',1,'SMS',12,'TRANSPORT_UPDATE',NULL,'',1,'Suresh Nair','+91 98470 11223','Transport Route Update','Timing update for Route.','Dear Suresh Nair, please note a timing update for Route North Route - Morning at Stop Aluva Bypass starting 2026-08-22. Bus will arrive at 07:15 AM. - EduCore Public School',NULL,'Normal',NULL,NULL,NULL,NULL,'Scheduled',NULL,0,3,NULL,NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(9,'Attendance Absent','Attendance',1,'SMS',6,'ATTENDANCE_ABSENT',NULL,'',1,'Suresh Nair','+91 98470 11223','Student Absence Alert','Dear {parent_name}, your child {student_name} (Adm: {admission_number}) was marked ABSENT today, {date}. Please contact the school office if this is in error. - {school_name}','Dear Suresh Nair, your child Aarav Nair (Adm: EDU2026001) was marked ABSENT today, 20-08-2026. Please contact the school office if this is in error. - EduCore Public School',NULL,'Important','9fcc8e76ab69cafeaaeed3825a9fdaec',NULL,'2026-08-20 15:53:41','2026-08-20 15:53:41','Delivered',NULL,0,3,NULL,NULL,'2026-08-20 15:53:41','2026-08-20 15:53:41','n'),(10,'Fee Overdue','Fees',1,'WhatsApp',9,'FEE_OVERDUE',NULL,'',1,'Suresh Nair','+91 98470 11223','Fee Overdue Alert','Dear {parent_name}, this is an urgent reminder that fee of Rs.{amount} for {student_name} ({admission_number}) was due on {due_date} and is now OVERDUE. Please clear the balance immediately.','Dear Suresh Nair, this is an urgent reminder that fee of Rs.12,000.00 for Aarav Nair (EDU2026001) was due on 15-09-2026 and is now OVERDUE. Please clear the balance immediately.',NULL,'Urgent',NULL,NULL,'2026-08-20 15:53:41','2026-08-20 15:53:41','Delivered',NULL,0,3,NULL,NULL,'2026-08-20 15:53:41','2026-08-20 15:53:41','n'),(11,'Scheduled Notice','',1,'In-App',NULL,NULL,NULL,'',1,'Aarav','test@email.com','Tomorrow Holiday','School is closed.','School is closed tomorrow.',NULL,'Normal',NULL,NULL,NULL,NULL,'Cancelled',NULL,0,3,NULL,NULL,'2026-08-20 15:53:41','2026-08-20 15:53:41','n'),(12,'SMS Alert','Attendance',2,'SMS',NULL,NULL,NULL,'',1,'Suresh Nair','+91 00000 00000','Alert','Test Msg','Test Rendered Msg',NULL,'Normal',NULL,NULL,'2026-08-20 15:53:41','2026-08-20 15:53:41','Delivered',NULL,2,3,NULL,NULL,'2026-08-20 15:53:41','2026-08-20 15:53:41','n'),(13,'Attendance Absent','Attendance',1,'SMS',6,'ATTENDANCE_ABSENT',NULL,'',1,'Suresh Nair','+91 98470 11223','Student Absence Alert','Dear {parent_name}, your child {student_name} (Adm: {admission_number}) was marked ABSENT today, {date}. Please contact the school office if this is in error. - {school_name}','Dear Suresh Nair, your child Aarav Nair (Adm: EDU2026001) was marked ABSENT today, 20-08-2026. Please contact the school office if this is in error. - EduCore Public School',NULL,'Important','9fcc8e76ab69cafeaaeed3825a9fdaec',NULL,'2026-08-20 16:37:02','2026-08-20 16:37:02','Delivered',NULL,0,3,NULL,NULL,'2026-08-20 16:37:02','2026-08-20 16:37:02','n'),(14,'Fee Overdue','Fees',1,'WhatsApp',9,'FEE_OVERDUE',NULL,'',1,'Suresh Nair','+91 98470 11223','Fee Overdue Alert','Dear {parent_name}, this is an urgent reminder that fee of Rs.{amount} for {student_name} ({admission_number}) was due on {due_date} and is now OVERDUE. Please clear the balance immediately.','Dear Suresh Nair, this is an urgent reminder that fee of Rs.12,000.00 for Aarav Nair (EDU2026001) was due on 15-09-2026 and is now OVERDUE. Please clear the balance immediately.',NULL,'Urgent',NULL,NULL,'2026-08-20 16:37:02','2026-08-20 16:37:02','Delivered',NULL,0,3,NULL,NULL,'2026-08-20 16:37:02','2026-08-20 16:37:02','n'),(15,'Scheduled Notice','',1,'In-App',NULL,NULL,NULL,'',1,'Aarav','test@email.com','Tomorrow Holiday','School is closed.','School is closed tomorrow.',NULL,'Normal',NULL,NULL,NULL,NULL,'Cancelled',NULL,0,3,NULL,NULL,'2026-08-20 16:37:02','2026-08-20 16:37:02','n'),(16,'SMS Alert','Attendance',2,'SMS',NULL,NULL,NULL,'',1,'Suresh Nair','+91 00000 00000','Alert','Test Msg','Test Rendered Msg',NULL,'Normal',NULL,NULL,'2026-08-20 16:37:02','2026-08-20 16:37:02','Delivered',NULL,2,3,NULL,NULL,'2026-08-20 16:37:02','2026-08-20 16:37:02','n');
/*!40000 ALTER TABLE `tbl_communication_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_communication_settings`
--

DROP TABLE IF EXISTS `tbl_communication_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_communication_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enable_inapp` tinyint(1) NOT NULL DEFAULT 1,
  `enable_sms` tinyint(1) NOT NULL DEFAULT 1,
  `enable_whatsapp` tinyint(1) NOT NULL DEFAULT 1,
  `enable_email` tinyint(1) NOT NULL DEFAULT 1,
  `sms_provider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Generic SMS Gateway',
  `sms_sender_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EDUSCH',
  `whatsapp_provider` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WhatsApp Business API',
  `email_from_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EduCore International School',
  `email_from_address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'notifications@educore.school',
  `enable_scheduled_jobs` tinyint(1) NOT NULL DEFAULT 1,
  `max_retries` int(10) unsigned NOT NULL DEFAULT 3,
  `retry_interval_minutes` int(10) unsigned NOT NULL DEFAULT 15,
  `parent_teacher_direct_messaging` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_communication_settings`
--

LOCK TABLES `tbl_communication_settings` WRITE;
/*!40000 ALTER TABLE `tbl_communication_settings` DISABLE KEYS */;
INSERT INTO `tbl_communication_settings` VALUES (1,1,1,1,1,'Generic SMS Gateway','SCHOLL','WhatsApp Business API','School Management','notifications@school.edu',1,3,15,1,'2026-08-20 12:56:29','2026-08-27 10:46:30','n');
/*!40000 ALTER TABLE `tbl_communication_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_communication_templates`
--

DROP TABLE IF EXISTS `tbl_communication_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_communication_templates` (
  `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `communication_type` enum('General','Attendance','Fee Reminder','Homework','Examination','Event','Emergency') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `channel` enum('In-App','SMS','WhatsApp','Email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SMS',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_template` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '{student_name}, {parent_name}, {date}, {school_name}',
  `character_limit` int(10) unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_communication_templates`
--

LOCK TABLES `tbl_communication_templates` WRITE;
/*!40000 ALTER TABLE `tbl_communication_templates` DISABLE KEYS */;
INSERT INTO `tbl_communication_templates` VALUES (1,'Daily Absent Notification','','General','Attendance','SMS','Absent Alert','Dear {parent_name}, your child {student_name} was marked ABSENT on {date}. Please contact the school office if this was unplanned. - {school_name}','{student_name}, {parent_name}, {date}, {school_name}',NULL,NULL,0,'Active','2026-08-20 12:56:29','2026-08-20 12:56:29','n'),(2,'Fee Due Reminder','','General','Fee Reminder','WhatsApp','Fee Due Notice','Dear {parent_name}, this is a gentle reminder that an amount of Rs. {amount} for {student_name} ({class}) is due on {due_date}. Kindly clear the dues. - {school_name}','{student_name}, {parent_name}, {class}, {amount}, {due_date}, {school_name}',NULL,NULL,0,'Active','2026-08-20 12:56:29','2026-08-20 12:56:29','n'),(3,'New Homework Assigned','','General','Homework','In-App','New Assignment Alert','New assignment for {subject}: {assignment} has been assigned to {student_name}. Due Date: {due_date}.','{student_name}, {subject}, {assignment}, {due_date}',NULL,NULL,0,'Active','2026-08-20 12:56:29','2026-08-20 12:56:29','n'),(4,'Examination Schedule Announcement','','General','Examination','Email','Upcoming Examination Timetable','<p>Dear {parent_name},</p><p>The examination schedule for <strong>{exam_name}</strong> starting on <strong>{date}</strong> has been published. Please review the detailed timetable in the student portal.</p><p>Regards,<br>{school_name}</p>','{student_name}, {parent_name}, {exam_name}, {date}, {school_name}',NULL,NULL,0,'Active','2026-08-20 12:56:29','2026-08-20 12:56:29','n'),(5,'Emergency School Closure','','General','Emergency','SMS','Urgent Announcement','URGENT: {school_name} will remain CLOSED on {date} due to {reason}. Online classes will be conducted as per schedule.','{school_name}, {date}, {reason}',NULL,NULL,0,'Active','2026-08-20 12:56:29','2026-08-20 12:56:29','n'),(6,'Student Absent Alert','ATTENDANCE_ABSENT','Attendance','Attendance','SMS','Student Absence Alert - {student_name}','Dear {parent_name}, your child {student_name} (Adm: {admission_number}) was marked ABSENT today, {date}. Please contact the school office if this is in error. - {school_name}','{student_name}, {parent_name}, {date}, {school_name}',160,'Instant SMS sent to parent when student is marked absent.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(7,'Student Late Arrival','ATTENDANCE_LATE','Attendance','Attendance','In-App','Late Arrival Notification','Dear {parent_name}, your child {student_name} arrived late to school at {time} on {date}. - {school_name}','{student_name}, {parent_name}, {date}, {school_name}',200,'Notification for tardy or late student arrival.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(8,'Fee Due Reminder','FEE_DUE','Fees','Fee Reminder','SMS','Fee Reminder for {student_name}','Dear {parent_name}, fee of Rs.{amount} for {student_name} (Class {class}) is due on {due_date}. Please pay before due date. - {school_name}','{student_name}, {parent_name}, {date}, {school_name}',160,'Upcoming fee due date alert for parents.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(9,'Fee Overdue Alert','FEE_OVERDUE','Fees','Fee Reminder','WhatsApp','URGENT: Outstanding Fee Overdue','Dear {parent_name}, this is an urgent reminder that fee of Rs.{amount} for {student_name} ({admission_number}) was due on {due_date} and is now OVERDUE. Please clear the balance immediately.','{student_name}, {parent_name}, {date}, {school_name}',300,'WhatsApp alert for overdue student fees.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(10,'Fee Payment Received','FEE_PAYMENT_RECEIVED','Fees','Fee Reminder','WhatsApp','Fee Payment Confirmation','Dear {parent_name}, we have received payment of Rs.{amount} for {student_name} on {date}. Thank you. - {school_name}','{student_name}, {parent_name}, {date}, {school_name}',200,'Receipt acknowledgment for fee collection.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(11,'New Homework Assigned','HOMEWORK_ASSIGNED','Homework','Homework','In-App','New Assignment in {subject}','A new assignment \"{assignment}\" in {subject} has been published for Class {class}-{section}. Due date: {due_date}.','{student_name}, {parent_name}, {date}, {school_name}',250,'In-app notification when teacher publishes assignment.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(12,'Homework Due Soon','HOMEWORK_DUE','Homework','Homework','In-App','Homework Due Reminder','Reminder: Assignment \"{assignment}\" in {subject} is due tomorrow ({due_date}). Please submit on time.','{student_name}, {parent_name}, {date}, {school_name}',200,'Student reminder for approaching assignment deadline.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(13,'Exam Schedule Published','EXAM_SCHEDULED','Examination','Examination','In-App','Examination Timetable Released - {exam_name}','The timetable for {exam_name} has been published. Exams commence on {date}. Please check the student portal for details.','{student_name}, {parent_name}, {date}, {school_name}',300,'Alert when exam schedules are finalized.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(14,'Examination Result Published','RESULT_PUBLISHED','Examination','Examination','Email','Term Results Published for {student_name}','<p>Dear {parent_name},</p><p>We are pleased to inform you that the results for <strong>{exam_name}</strong> have been published for <strong>{student_name}</strong> (Class {class}-{section}).</p><p>You can access the report card directly from the student portal.</p><p>Regards,<br>{school_name}</p>','{student_name}, {parent_name}, {date}, {school_name}',NULL,'Email report card publishing alert.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(15,'Staff Leave Approved','LEAVE_APPROVED','Leave','','In-App','Leave Application Approved','Dear {staff_name}, your {leave_type} request for {date} has been APPROVED by the Principal.','{student_name}, {parent_name}, {date}, {school_name}',200,'Staff notification upon leave sanction.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(16,'Staff Leave Rejected','LEAVE_REJECTED','Leave','','In-App','Leave Application Update','Dear {staff_name}, your {leave_type} request for {date} was not approved. Please consult administration.','{student_name}, {parent_name}, {date}, {school_name}',200,'Staff notification upon leave disapproval.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(17,'Transport Route Update','TRANSPORT_UPDATE','Transport','','SMS','Transport Route Update','Dear {parent_name}, please note a timing update for Route {route} at Stop {stop} starting {date}. Bus will arrive at {time}. - {school_name}','{student_name}, {parent_name}, {date}, {school_name}',160,'Bus route and timing changes.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(18,'Certificate Ready for Pickup','CERTIFICATE_READY','Certificate','','In-App','Certificate Issued - {student_name}','The certificate for {student_name} (Adm: {admission_number}) has been generated and is ready for collection at the school office. - {school_name}','{student_name}, {parent_name}, {date}, {school_name}',200,'Notice when student certificate is generated.',1,'Active','2026-08-20 15:48:43','2026-08-20 15:48:43','n');
/*!40000 ALTER TABLE `tbl_communication_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_conversation_participants`
--

DROP TABLE IF EXISTS `tbl_conversation_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_conversation_participants` (
  `participant_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_type` enum('Staff','Parent','Student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Staff',
  `unread_count` int(10) unsigned NOT NULL DEFAULT 0,
  `last_read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`participant_id`),
  UNIQUE KEY `uk_conv_user` (`conversation_id`,`user_id`,`user_type`),
  KEY `idx_cp_user` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_conversation_participants`
--

LOCK TABLES `tbl_conversation_participants` WRITE;
/*!40000 ALTER TABLE `tbl_conversation_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_conversation_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_conversations`
--

DROP TABLE IF EXISTS `tbl_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_conversations` (
  `conversation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_type` enum('Parent-Teacher','Internal','Group') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Internal',
  `group_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`conversation_id`),
  KEY `idx_conv_type` (`conversation_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_conversations`
--

LOCK TABLES `tbl_conversations` WRITE;
/*!40000 ALTER TABLE `tbl_conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_departments`
--

DROP TABLE IF EXISTS `tbl_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_departments` (
  `department_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `head_of_department_id` int(10) unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `uk_department_name` (`department_name`),
  KEY `idx_dept_head` (`head_of_department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_departments`
--

LOCK TABLES `tbl_departments` WRITE;
/*!40000 ALTER TABLE `tbl_departments` DISABLE KEYS */;
INSERT INTO `tbl_departments` VALUES (1,'Mathematics',1,'Mathematics & Statistics department',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,'Science',2,'Physics, Chemistry, and Biology department',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,'English',5,'English Literature and Linguistics department',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,'Administration',6,'School Leadership and Operations',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(5,'Finance',3,'Fee collection and accounts department',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(6,'Physical Education',8,'Sports, fitness, and outdoor activities',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n');
/*!40000 ALTER TABLE `tbl_departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_designations`
--

DROP TABLE IF EXISTS `tbl_designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_designations` (
  `designation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `designation_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('Administration','Teaching','Finance','Support') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Teaching',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`designation_id`),
  UNIQUE KEY `uk_designation_name` (`designation_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_designations`
--

LOCK TABLES `tbl_designations` WRITE;
/*!40000 ALTER TABLE `tbl_designations` DISABLE KEYS */;
INSERT INTO `tbl_designations` VALUES (1,'Principal','Administration','Head of the School',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,'Head of Department','Teaching','Department supervisor and lead educator',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,'Senior Teacher','Teaching','Senior classroom faculty',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,'Teacher','Teaching','Subject teacher and classroom educator',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(5,'Accountant','Finance','Financial officer',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(6,'Front Desk','Administration','Receptionist and front office coordinator',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(7,'Clerk','Administration','Administrative office assistant',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n');
/*!40000 ALTER TABLE `tbl_designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_document_categories`
--

DROP TABLE IF EXISTS `tbl_document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_document_categories` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applicable_to` enum('All','Student','Staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Student',
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `expiry_required` tinyint(1) NOT NULL DEFAULT 0,
  `verification_required` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `uk_doc_cat_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_document_categories`
--

LOCK TABLES `tbl_document_categories` WRITE;
/*!40000 ALTER TABLE `tbl_document_categories` DISABLE KEYS */;
INSERT INTO `tbl_document_categories` VALUES (1,'Birth Certificate','BIRTH_CERT','Official birth certificate issued by municipal/registrar authority','Student',1,0,1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(2,'Transfer Certificate (Previous School)','PREV_TC','Transfer certificate from previous institution','Student',0,0,1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(3,'Address Proof / Aadhaar Card','ID_PROOF','National identity document or residential address proof','All',1,0,1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(4,'Medical & Immunization Record','MEDICAL_CERT','Medical fitness and childhood vaccination record','Student',0,1,1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(5,'Passport / Visa Document','PASSPORT','International student passport / visa identification','Student',0,1,1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n'),(6,'Community / Caste Certificate','COMMUNITY_CERT','Government issued reservation or caste category proof','Student',0,0,1,'Active','2026-08-20 15:26:20','2026-08-20 15:26:20','n');
/*!40000 ALTER TABLE `tbl_document_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_events`
--

DROP TABLE IF EXISTS `tbl_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_events` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `audience` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Whole School',
  `venue` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_events`
--

LOCK TABLES `tbl_events` WRITE;
/*!40000 ALTER TABLE `tbl_events` DISABLE KEYS */;
INSERT INTO `tbl_events` VALUES (1,'Mid-Term Exams Begin','2026-08-18','Grades 6-12 · All Sections','Examination Halls','First paper: English Language & Literature',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,'Independence Day Celebration','2026-08-22','Whole School · Assembly Ground','Assembly Ground','Flag hoisting, parade and speech by Principal',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,'PTA Meeting','2026-08-29','Grades 1-5 · 3:00 PM','Auditorium','Discussion on student academic progress and upcoming term events',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n');
/*!40000 ALTER TABLE `tbl_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_exam_audit_logs`
--

DROP TABLE IF EXISTS `tbl_exam_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_exam_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_exam_audit_logs`
--

LOCK TABLES `tbl_exam_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_exam_audit_logs` DISABLE KEYS */;
INSERT INTO `tbl_exam_audit_logs` VALUES (1,1,'RESULT_PUBLISHED','tbl_exams',1,'Published results for First Term Examination 2026','2026-08-20 12:06:40','n'),(2,1,'RESULT_PUBLISHED','tbl_exams',1,'Published results for First Term Examination 2026','2026-08-20 12:06:53','n');
/*!40000 ALTER TABLE `tbl_exam_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_exam_marks`
--

DROP TABLE IF EXISTS `tbl_exam_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_exam_marks` (
  `mark_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int(10) unsigned NOT NULL,
  `schedule_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `marks_obtained` decimal(6,2) DEFAULT NULL,
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `is_exempted` tinyint(1) NOT NULL DEFAULT 0,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_point` decimal(4,2) DEFAULT NULL,
  `status` enum('Draft','Submitted','Under Verification','Approved','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entered_by` int(10) unsigned DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`mark_id`),
  UNIQUE KEY `uk_exam_student_subject` (`exam_id`,`student_id`,`subject_id`),
  KEY `idx_mark_exam` (`exam_id`),
  KEY `idx_mark_student` (`student_id`),
  KEY `idx_mark_schedule` (`schedule_id`),
  KEY `idx_mark_class_sec` (`class_id`,`section_id`),
  KEY `idx_mark_status` (`status`),
  KEY `idx_exam_marks_year_exam` (`academic_year_id`,`exam_id`,`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_exam_marks`
--

LOCK TABLES `tbl_exam_marks` WRITE;
/*!40000 ALTER TABLE `tbl_exam_marks` DISABLE KEYS */;
INSERT INTO `tbl_exam_marks` VALUES (4,1,1,2,1,7,5,1,92.00,0,0,'A+',10.00,'Approved',NULL,1,NULL,NULL,NULL,NULL,'2026-08-20 12:06:53','2026-08-20 12:06:53','n'),(5,1,1,3,1,7,5,1,74.00,0,0,'B+',8.00,'Approved',NULL,1,NULL,NULL,NULL,NULL,'2026-08-20 12:06:53','2026-08-20 12:06:53','n'),(6,1,1,4,1,7,5,1,0.00,1,0,'ABS',0.00,'Approved',NULL,1,NULL,NULL,NULL,NULL,'2026-08-20 12:06:53','2026-08-20 12:06:53','n');
/*!40000 ALTER TABLE `tbl_exam_marks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_exam_schedules`
--

DROP TABLE IF EXISTS `tbl_exam_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_exam_schedules` (
  `schedule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `teacher_id` int(10) unsigned DEFAULT NULL,
  `exam_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_marks` decimal(6,2) NOT NULL DEFAULT 100.00,
  `passing_marks` decimal(6,2) NOT NULL DEFAULT 35.00,
  `room_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Scheduled','Ongoing','Completed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Scheduled',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`schedule_id`),
  UNIQUE KEY `uk_exam_class_sec_subj` (`exam_id`,`class_id`,`section_id`,`subject_id`),
  KEY `idx_sched_exam` (`exam_id`),
  KEY `idx_sched_class_sec` (`class_id`,`section_id`),
  KEY `idx_sched_subject` (`subject_id`),
  KEY `idx_sched_teacher` (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_exam_schedules`
--

LOCK TABLES `tbl_exam_schedules` WRITE;
/*!40000 ALTER TABLE `tbl_exam_schedules` DISABLE KEYS */;
INSERT INTO `tbl_exam_schedules` VALUES (1,1,1,1,8,1,1,'2026-09-02','09:30:00','12:30:00',100.00,35.00,'Hall A-101',NULL,'Scheduled','2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(2,1,1,1,8,2,1,'2026-09-04','09:30:00','12:30:00',100.00,35.00,'Hall A-101',NULL,'Scheduled','2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(3,1,1,1,8,3,1,'2026-09-06','09:30:00','12:30:00',100.00,35.00,'Hall A-101',NULL,'Scheduled','2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(4,1,1,1,8,4,1,'2026-09-08','09:30:00','12:30:00',100.00,35.00,'Hall A-101',NULL,'Scheduled','2026-08-20 11:58:33','2026-08-20 11:58:33','n');
/*!40000 ALTER TABLE `tbl_exam_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_exam_types`
--

DROP TABLE IF EXISTS `tbl_exam_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_exam_types` (
  `exam_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`exam_type_id`),
  UNIQUE KEY `uk_exam_type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_exam_types`
--

LOCK TABLES `tbl_exam_types` WRITE;
/*!40000 ALTER TABLE `tbl_exam_types` DISABLE KEYS */;
INSERT INTO `tbl_exam_types` VALUES (1,'Unit Test','Periodic unit test evaluations',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(2,'Class Test','Classroom monthly assessment test',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(3,'First Term Examination','First term comprehensive exam',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(4,'Mid Term Examination','Half-yearly / mid-term examination',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(5,'Second Term Examination','Second term evaluations',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(6,'Annual Examination','Final annual academic examination',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(7,'Model Examination','Pre-board / model preparation exam',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(8,'Practical Examination','Laboratory and practical assessment',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n');
/*!40000 ALTER TABLE `tbl_exam_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_examination_settings`
--

DROP TABLE IF EXISTS `tbl_examination_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_examination_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `decimal_precision` int(10) unsigned NOT NULL DEFAULT 2,
  `default_max_marks` decimal(6,2) NOT NULL DEFAULT 100.00,
  `default_passing_marks` decimal(6,2) NOT NULL DEFAULT 35.00,
  `subject_pass_mark_rule` tinyint(1) NOT NULL DEFAULT 1,
  `overall_pass_percentage` decimal(5,2) NOT NULL DEFAULT 35.00,
  `single_subject_fail_overall` tinyint(1) NOT NULL DEFAULT 1,
  `rank_criteria` enum('Percentage','Total Marks','GPA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Percentage',
  `include_failed_in_rank` tinyint(1) NOT NULL DEFAULT 0,
  `show_rank_on_report_card` tinyint(1) NOT NULL DEFAULT 1,
  `show_attendance_on_report_card` tinyint(1) NOT NULL DEFAULT 1,
  `report_card_header` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_signature_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Principal',
  `teacher_signature_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Class Teacher',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_examination_settings`
--

LOCK TABLES `tbl_examination_settings` WRITE;
/*!40000 ALTER TABLE `tbl_examination_settings` DISABLE KEYS */;
INSERT INTO `tbl_examination_settings` VALUES (1,2,100.00,35.00,1,35.00,1,'Percentage',0,1,1,'School - Official Academic Report Card','Principal','Class Teacher','2026-08-20 11:58:33','2026-08-27 10:46:30','n');
/*!40000 ALTER TABLE `tbl_examination_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_exams`
--

DROP TABLE IF EXISTS `tbl_exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_exams` (
  `exam_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exam_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exam_type_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `applicable_classes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Scheduled','Ongoing','Completed','Marks Pending','Under Verification','Published','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`exam_id`),
  KEY `idx_exam_year` (`academic_year_id`),
  KEY `idx_exam_type` (`exam_type_id`),
  KEY `idx_exam_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_exams`
--

LOCK TABLES `tbl_exams` WRITE;
/*!40000 ALTER TABLE `tbl_exams` DISABLE KEYS */;
INSERT INTO `tbl_exams` VALUES (1,'First Term Examination 2026',3,1,'First term comprehensive assessment for all middle & secondary classes','2026-09-01','2026-09-15','[1,2,3,4,5,6,7,8,9,10]','Published','2026-08-20 11:58:33','2026-08-20 12:06:40','n');
/*!40000 ALTER TABLE `tbl_exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fee_adjustments`
--

DROP TABLE IF EXISTS `tbl_fee_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_fee_adjustments` (
  `adjustment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_fee_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `adjustment_type` enum('Waiver','Adjustment','Correction','Concession') COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_amount` decimal(10,2) NOT NULL,
  `new_amount` decimal(10,2) NOT NULL,
  `adjustment_amount` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjusted_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`adjustment_id`),
  KEY `idx_adj_fee` (`student_fee_id`),
  KEY `idx_adj_student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fee_adjustments`
--

LOCK TABLES `tbl_fee_adjustments` WRITE;
/*!40000 ALTER TABLE `tbl_fee_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_fee_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fee_discounts`
--

DROP TABLE IF EXISTS `tbl_fee_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_fee_discounts` (
  `discount_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('Percentage','Fixed Amount') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Fixed Amount',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `applicable_classes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applicable_categories` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_concession` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`discount_id`),
  UNIQUE KEY `uk_discount_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fee_discounts`
--

LOCK TABLES `tbl_fee_discounts` WRITE;
/*!40000 ALTER TABLE `tbl_fee_discounts` DISABLE KEYS */;
INSERT INTO `tbl_fee_discounts` VALUES (1,'Sibling Concession','Percentage',15.00,5000.00,NULL,NULL,1,1,'2026-08-20 12:14:15','2026-08-20 12:14:15','n'),(2,'Staff Ward Scholarship','Percentage',50.00,15000.00,NULL,NULL,1,1,'2026-08-20 12:14:15','2026-08-20 12:14:15','n'),(3,'Merit Scholarship','Percentage',25.00,8000.00,NULL,NULL,1,1,'2026-08-20 12:14:15','2026-08-20 12:14:15','n'),(4,'Early Bird Discount','Fixed Amount',1000.00,1000.00,NULL,NULL,0,1,'2026-08-20 12:14:15','2026-08-20 12:14:15','n'),(5,'Financial Hardship Relief','Percentage',40.00,10000.00,NULL,NULL,1,1,'2026-08-20 12:14:15','2026-08-20 12:14:15','n');
/*!40000 ALTER TABLE `tbl_fee_discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fee_heads`
--

DROP TABLE IF EXISTS `tbl_fee_heads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_fee_heads` (
  `fee_head_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `head_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applicable_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All Students',
  `frequency` enum('One Time','Monthly','Quarterly','Half Yearly','Yearly','Custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Yearly',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`fee_head_id`),
  UNIQUE KEY `uk_fee_head_name` (`head_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fee_heads`
--

LOCK TABLES `tbl_fee_heads` WRITE;
/*!40000 ALTER TABLE `tbl_fee_heads` DISABLE KEYS */;
INSERT INTO `tbl_fee_heads` VALUES (1,'Term 1 Fees',NULL,'Tuition and academic facilities for Term 1','All Students','Yearly',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,'Term 2 Fees',NULL,'Tuition and academic facilities for Term 2','All Students','Yearly',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,'Term 3 Fees',NULL,'Tuition and academic facilities for Term 3','All Students','Yearly',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,'Annual Activities & Lab Fee',NULL,'Computer lab, science lab and sports fee','All Students','Yearly',1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(5,'test','ew','edwed','All Students','Monthly',1,'2026-08-27 15:31:23','2026-08-27 15:31:23','n');
/*!40000 ALTER TABLE `tbl_fee_heads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fee_payments`
--

DROP TABLE IF EXISTS `tbl_fee_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_fee_payments` (
  `payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_fee_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_mode` enum('Cash','Bank Transfer','UPI','Cheque','Card') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date NOT NULL,
  `receipt_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `collected_by` int(10) unsigned DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `uk_receipt_no` (`receipt_no`),
  KEY `idx_pay_student_fee` (`student_fee_id`),
  KEY `idx_pay_student` (`student_id`),
  CONSTRAINT `fk_pay_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pay_student_fee` FOREIGN KEY (`student_fee_id`) REFERENCES `tbl_student_fees` (`student_fee_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fee_payments`
--

LOCK TABLES `tbl_fee_payments` WRITE;
/*!40000 ALTER TABLE `tbl_fee_payments` DISABLE KEYS */;
INSERT INTO `tbl_fee_payments` VALUES (1,1,1,12000.00,'UPI','UPI/2026/89472','2026-08-17','REC-2026-001',NULL,NULL,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,4,4,9800.00,'Bank Transfer','NEFT/2026/11094','2026-08-16','REC-2026-002',NULL,NULL,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n');
/*!40000 ALTER TABLE `tbl_fee_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fee_refunds`
--

DROP TABLE IF EXISTS `tbl_fee_refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_fee_refunds` (
  `refund_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL,
  `student_fee_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `refund_mode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bank Transfer',
  `approved_by` int(10) unsigned DEFAULT NULL,
  `status` enum('Pending','Approved','Processed','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Approved',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`refund_id`),
  KEY `idx_ref_pay` (`payment_id`),
  KEY `idx_ref_student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fee_refunds`
--

LOCK TABLES `tbl_fee_refunds` WRITE;
/*!40000 ALTER TABLE `tbl_fee_refunds` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_fee_refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fee_reminders`
--

DROP TABLE IF EXISTS `tbl_fee_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_fee_reminders` (
  `reminder_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `parent_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_fee_id` int(10) unsigned DEFAULT NULL,
  `reminder_type` enum('Upcoming Due','Due Today','Overdue','Payment Confirmation') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `scheduled_date` date NOT NULL,
  `status` enum('Pending','Sent','Failed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`reminder_id`),
  KEY `idx_rem_student` (`student_id`),
  KEY `idx_rem_fee` (`student_fee_id`),
  KEY `idx_rem_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fee_reminders`
--

LOCK TABLES `tbl_fee_reminders` WRITE;
/*!40000 ALTER TABLE `tbl_fee_reminders` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_fee_reminders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_fee_structures`
--

DROP TABLE IF EXISTS `tbl_fee_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_fee_structures` (
  `fee_structure_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fee_head_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `frequency` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Yearly',
  `due_date` date NOT NULL,
  `applicable_from` date DEFAULT NULL,
  `applicable_to` date DEFAULT NULL,
  `is_optional` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`fee_structure_id`),
  KEY `idx_fee_head` (`fee_head_id`),
  KEY `idx_fee_academic_year` (`academic_year_id`),
  KEY `idx_fee_class` (`class_id`),
  CONSTRAINT `fk_feestruct_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_feestruct_head` FOREIGN KEY (`fee_head_id`) REFERENCES `tbl_fee_heads` (`fee_head_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_feestruct_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_fee_structures`
--

LOCK TABLES `tbl_fee_structures` WRITE;
/*!40000 ALTER TABLE `tbl_fee_structures` DISABLE KEYS */;
INSERT INTO `tbl_fee_structures` VALUES (1,2,1,8,12000.00,'Yearly','2026-09-15',NULL,NULL,0,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,2,1,7,12000.00,'Yearly','2026-09-15',NULL,NULL,0,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,2,1,10,14500.00,'Yearly','2026-08-10',NULL,NULL,0,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,2,1,5,9800.00,'Yearly','2026-09-15',NULL,NULL,0,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n');
/*!40000 ALTER TABLE `tbl_fee_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_finance_audit_logs`
--

DROP TABLE IF EXISTS `tbl_finance_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_finance_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_fin_action` (`action`),
  KEY `idx_fin_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_finance_audit_logs`
--

LOCK TABLES `tbl_finance_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_finance_audit_logs` DISABLE KEYS */;
INSERT INTO `tbl_finance_audit_logs` VALUES (1,1,'FEE_TEST_VERIFIED','tbl_student_fees',6,'Completed automated finance lifecycle test',NULL,NULL,NULL,'2026-08-20 12:22:55','n'),(2,15,'FEE_CATEGORY_CREATED','tbl_fee_heads',5,'Saved fee category: test (Monthly)',NULL,NULL,NULL,'2026-08-27 12:01:23','n'),(3,15,'FEE_ASSIGNED_INDIVIDUAL','tbl_student_fees',7,'Assigned fee structure ID 3 to student ID 3',NULL,NULL,NULL,'2026-08-27 12:02:03','n');
/*!40000 ALTER TABLE `tbl_finance_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_finance_settings`
--

DROP TABLE IF EXISTS `tbl_finance_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_finance_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currency_symbol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '₹',
  `currency_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `receipt_prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'REC-',
  `next_receipt_number` int(10) unsigned NOT NULL DEFAULT 1001,
  `receipt_footer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `authorized_signature_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Accounts Officer',
  `allow_partial_payments` tinyint(1) NOT NULL DEFAULT 1,
  `allow_overpayment` tinyint(1) NOT NULL DEFAULT 0,
  `require_transaction_ref` tinyint(1) NOT NULL DEFAULT 0,
  `grace_period_days` int(10) unsigned NOT NULL DEFAULT 7,
  `discount_approval_required` tinyint(1) NOT NULL DEFAULT 1,
  `reminder_template_upcoming` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_template_overdue` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_template_payment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_finance_settings`
--

LOCK TABLES `tbl_finance_settings` WRITE;
/*!40000 ALTER TABLE `tbl_finance_settings` DISABLE KEYS */;
INSERT INTO `tbl_finance_settings` VALUES (1,'₹','INR','REC-2026-',1003,'Thank you for your timely fee payment. This is a computer generated official fee receipt.','Senior Accounts Officer',1,0,0,7,1,'Dear Parent, the fee amount of {amount} for {student_name} is due on {due_date}. Please pay to avoid late charges.','Dear Parent, the fee amount of {amount} for {student_name} is overdue by {days_overdue} days. Kindly settle immediately.','Payment of {amount} has been successfully received for {student_name}. Official Receipt No: {receipt_no}.','2026-08-20 12:14:15','2026-08-20 12:14:15','n');
/*!40000 ALTER TABLE `tbl_finance_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_grades`
--

DROP TABLE IF EXISTS `tbl_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_grades` (
  `grade_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade_name` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_percentage` decimal(5,2) NOT NULL,
  `max_percentage` decimal(5,2) NOT NULL,
  `grade_point` decimal(4,2) NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `uk_grade_name` (`grade_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_grades`
--

LOCK TABLES `tbl_grades` WRITE;
/*!40000 ALTER TABLE `tbl_grades` DISABLE KEYS */;
INSERT INTO `tbl_grades` VALUES (1,'A+',90.00,100.00,10.00,'Outstanding',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(2,'A',80.00,89.99,9.00,'Excellent',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(3,'B+',70.00,79.99,8.00,'Very Good',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(4,'B',60.00,69.99,7.00,'Good',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(5,'C',50.00,59.99,6.00,'Average',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(6,'D',40.00,49.99,5.00,'Pass',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n'),(7,'F',0.00,39.99,0.00,'Fail',1,'2026-08-20 11:58:33','2026-08-20 11:58:33','n');
/*!40000 ALTER TABLE `tbl_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_homework`
--

DROP TABLE IF EXISTS `tbl_homework`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_homework` (
  `homework_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `teacher_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_date` date NOT NULL,
  `submission_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`homework_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_homework`
--

LOCK TABLES `tbl_homework` WRITE;
/*!40000 ALTER TABLE `tbl_homework` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_homework` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_homework_audit_logs`
--

DROP TABLE IF EXISTS `tbl_homework_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_homework_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_hw_action` (`action`),
  KEY `idx_hw_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_homework_audit_logs`
--

LOCK TABLES `tbl_homework_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_homework_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_homework_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_homework_notifications`
--

DROP TABLE IF EXISTS `tbl_homework_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_homework_notifications` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `parent_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_type` enum('New Assignment','Upcoming Due','Overdue','Submission Received','Submission Reviewed','Returned') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Pending','Sent','Failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`notification_id`),
  KEY `idx_hw_notif_asgn` (`assignment_id`),
  KEY `idx_hw_notif_student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_homework_notifications`
--

LOCK TABLES `tbl_homework_notifications` WRITE;
/*!40000 ALTER TABLE `tbl_homework_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_homework_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_homework_settings`
--

DROP TABLE IF EXISTS `tbl_homework_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_homework_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `default_submission_deadline_days` int(10) unsigned NOT NULL DEFAULT 3,
  `allow_late_submissions_default` tinyint(1) NOT NULL DEFAULT 1,
  `max_upload_size_mb` int(10) unsigned NOT NULL DEFAULT 10,
  `allowed_file_extensions` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf,doc,docx,jpg,jpeg,png,zip,txt',
  `enable_grading` tinyint(1) NOT NULL DEFAULT 1,
  `enable_parent_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_homework_settings`
--

LOCK TABLES `tbl_homework_settings` WRITE;
/*!40000 ALTER TABLE `tbl_homework_settings` DISABLE KEYS */;
INSERT INTO `tbl_homework_settings` VALUES (1,3,1,10,'pdf,doc,docx,jpg,jpeg,png,zip,txt',1,1,'2026-08-20 12:44:58','2026-08-20 12:44:58','n');
/*!40000 ALTER TABLE `tbl_homework_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_id_card_settings`
--

DROP TABLE IF EXISTS `tbl_id_card_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_id_card_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'STUDENT IDENTITY CARD',
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validity_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accent_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#091426',
  `secondary_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#006c4a',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_id_card_settings`
--

LOCK TABLES `tbl_id_card_settings` WRITE;
/*!40000 ALTER TABLE `tbl_id_card_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_id_card_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_leave_applications`
--

DROP TABLE IF EXISTS `tbl_leave_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_leave_applications` (
  `application_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_type` enum('Student','Staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Student',
  `student_id` int(10) unsigned DEFAULT NULL,
  `staff_id` int(10) unsigned DEFAULT NULL,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `class_id` int(10) unsigned DEFAULT NULL,
  `section_id` int(10) unsigned DEFAULT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `duration_days` decimal(4,1) NOT NULL DEFAULT 1.0,
  `is_half_day` tinyint(1) NOT NULL DEFAULT 0,
  `half_day_type` enum('Full Day','First Half','Second Half') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Full Day',
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Pending','Clarification Required','Approved','Rejected','Cancelled','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clarification_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `applied_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`application_id`),
  KEY `idx_leave_app_type` (`applicant_type`),
  KEY `idx_leave_app_student` (`student_id`),
  KEY `idx_leave_app_staff` (`staff_id`),
  KEY `idx_leave_app_dates` (`from_date`,`to_date`),
  KEY `idx_leave_app_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_leave_applications`
--

LOCK TABLES `tbl_leave_applications` WRITE;
/*!40000 ALTER TABLE `tbl_leave_applications` DISABLE KEYS */;
INSERT INTO `tbl_leave_applications` VALUES (1,'Student',1,NULL,1,8,1,1,'2026-08-25','2026-08-26',2.0,0,'Full Day','Medical appointment with physician',NULL,'Approved',NULL,NULL,NULL,NULL,'2026-08-20','2026-08-20 13:56:49','2026-08-20 13:56:49','n'),(2,'Staff',NULL,1,1,NULL,NULL,1,'2026-08-28','2026-08-28',0.5,1,'First Half','Personal urgent bank appointment',NULL,'Cancelled',NULL,NULL,1,'2026-08-20 13:56:49','2026-08-20','2026-08-20 13:56:49','2026-08-20 13:56:49','n'),(3,'Student',1,NULL,1,8,1,1,'2026-09-01','2026-09-03',3.0,0,'Full Day','Family trip during mid-term exam week',NULL,'Rejected','Cannot approve leave during mid-term examination week.',NULL,1,'2026-08-20 13:56:49','2026-08-20','2026-08-20 13:56:49','2026-08-20 13:56:49','n'),(4,'Student',1,NULL,1,8,1,1,'2026-09-10','2026-09-12',3.0,0,'Full Day','Medical absence',NULL,'Clarification Required',NULL,'Please upload doctor prescription letter.',NULL,NULL,'2026-08-20','2026-08-20 13:56:49','2026-08-20 13:56:49','n');
/*!40000 ALTER TABLE `tbl_leave_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_leave_audit_logs`
--

DROP TABLE IF EXISTS `tbl_leave_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_leave_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_leave_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_leave_audit_logs`
--

LOCK TABLES `tbl_leave_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_leave_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_leave_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_leave_balances`
--

DROP TABLE IF EXISTS `tbl_leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_leave_balances` (
  `balance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `entity_type` enum('Student','Staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Staff',
  `entity_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `allocated_days` decimal(4,1) NOT NULL DEFAULT 12.0,
  `used_days` decimal(4,1) NOT NULL DEFAULT 0.0,
  `pending_days` decimal(4,1) NOT NULL DEFAULT 0.0,
  `carry_forward_days` decimal(4,1) NOT NULL DEFAULT 0.0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`balance_id`),
  UNIQUE KEY `uk_leave_balance` (`academic_year_id`,`entity_type`,`entity_id`,`leave_type_id`),
  KEY `idx_lb_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_leave_balances`
--

LOCK TABLES `tbl_leave_balances` WRITE;
/*!40000 ALTER TABLE `tbl_leave_balances` DISABLE KEYS */;
INSERT INTO `tbl_leave_balances` VALUES (1,1,'Staff',1,1,12.0,0.0,0.0,0.0,'2026-08-20 13:56:49','2026-08-20 13:56:49','n'),(2,1,'Student',1,1,12.0,0.0,0.0,0.0,'2026-08-21 06:22:49','2026-08-21 09:52:49','n');
/*!40000 ALTER TABLE `tbl_leave_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_leave_history`
--

DROP TABLE IF EXISTS `tbl_leave_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_leave_history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int(10) unsigned NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `performed_by` int(10) unsigned DEFAULT NULL,
  `performed_by_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Staff',
  `previous_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`history_id`),
  KEY `idx_lh_app` (`application_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_leave_history`
--

LOCK TABLES `tbl_leave_history` WRITE;
/*!40000 ALTER TABLE `tbl_leave_history` DISABLE KEYS */;
INSERT INTO `tbl_leave_history` VALUES (1,1,'Approved',1,'Staff','Pending','Approved','Approved by principal.','2026-08-20 13:56:49','n');
/*!40000 ALTER TABLE `tbl_leave_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_leave_requests`
--

DROP TABLE IF EXISTS `tbl_leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_leave_requests` (
  `leave_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_type` enum('Staff','Student') COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_id` int(10) unsigned DEFAULT NULL,
  `student_id` int(10) unsigned DEFAULT NULL,
  `leave_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`leave_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_leave_requests`
--

LOCK TABLES `tbl_leave_requests` WRITE;
/*!40000 ALTER TABLE `tbl_leave_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_leave_settings`
--

DROP TABLE IF EXISTS `tbl_leave_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_leave_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enable_student_leave` tinyint(1) NOT NULL DEFAULT 1,
  `enable_staff_leave` tinyint(1) NOT NULL DEFAULT 1,
  `enable_half_day` tinyint(1) NOT NULL DEFAULT 1,
  `working_days_only` tinyint(1) NOT NULL DEFAULT 1,
  `student_approval_workflow` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Class Teacher -> Principal',
  `staff_approval_workflow` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Department Head -> Principal',
  `enable_balance_tracking` tinyint(1) NOT NULL DEFAULT 1,
  `allow_carry_forward` tinyint(1) NOT NULL DEFAULT 1,
  `max_carry_forward_days` int(10) unsigned NOT NULL DEFAULT 5,
  `require_document_default` tinyint(1) NOT NULL DEFAULT 0,
  `max_file_size_mb` int(10) unsigned NOT NULL DEFAULT 10,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_leave_settings`
--

LOCK TABLES `tbl_leave_settings` WRITE;
/*!40000 ALTER TABLE `tbl_leave_settings` DISABLE KEYS */;
INSERT INTO `tbl_leave_settings` VALUES (1,1,1,1,1,'Class Teacher -> Principal','Department Head -> Principal',1,1,5,0,10,'2026-08-20 13:52:11','2026-08-20 13:52:11','n');
/*!40000 ALTER TABLE `tbl_leave_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_leave_types`
--

DROP TABLE IF EXISTS `tbl_leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_leave_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicable_to` enum('Students','Staff','Both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Both',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_days` int(10) unsigned NOT NULL DEFAULT 12,
  `requires_document` tinyint(1) NOT NULL DEFAULT 0,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1,
  `allow_half_day` tinyint(1) NOT NULL DEFAULT 1,
  `allow_carry_forward` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `uk_leave_code` (`type_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_leave_types`
--

LOCK TABLES `tbl_leave_types` WRITE;
/*!40000 ALTER TABLE `tbl_leave_types` DISABLE KEYS */;
INSERT INTO `tbl_leave_types` VALUES (1,'Casual Leave','CL','Staff','General casual leave for personal reasons',12,0,1,1,1,1,'2026-08-20 13:52:11','2026-08-20 13:52:11','n'),(2,'Sick / Medical Leave','SL','Both','Leave taken due to illness or medical appointment',10,1,1,1,0,1,'2026-08-20 13:52:11','2026-08-20 13:52:11','n'),(3,'Earned Leave','EL','Staff','Annual accrued privilege leave',15,0,1,0,1,1,'2026-08-20 13:52:11','2026-08-20 13:52:11','n'),(4,'Family Function Leave','FFL','Students','Leave for attending family weddings or ceremonies',5,0,1,1,0,1,'2026-08-20 13:52:11','2026-08-20 13:52:11','n'),(5,'Maternity Leave','ML','Staff','Maternity leave for female staff members',90,1,1,0,0,1,'2026-08-20 13:52:11','2026-08-20 13:52:11','n'),(6,'Paternity Leave','PL','Staff','Paternity leave for male staff members',15,1,1,0,0,1,'2026-08-20 13:52:11','2026-08-20 13:52:11','n'),(7,'Emergency Leave','EML','Both','Unplanned emergency or bereavement leave',5,0,1,1,0,1,'2026-08-20 13:52:11','2026-08-20 13:52:11','n');
/*!40000 ALTER TABLE `tbl_leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_messages`
--

DROP TABLE IF EXISTS `tbl_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL,
  `sender_type` enum('Staff','Parent','Student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Staff',
  `message_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Sent','Delivered','Read') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sent',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`message_id`),
  KEY `idx_msg_conv` (`conversation_id`),
  KEY `idx_msg_sender` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_messages`
--

LOCK TABLES `tbl_messages` WRITE;
/*!40000 ALTER TABLE `tbl_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_notices`
--

DROP TABLE IF EXISTS `tbl_notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_notices` (
  `notice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `category` enum('General','Academic','Examination','Holiday','Fee','Attendance','Event','Emergency','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_by` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_by_id` int(10) unsigned DEFAULT NULL,
  `audience` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All',
  `publish_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'All',
  `target_type` enum('Entire School','Class','Section','Individual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Entire School',
  `class_id` int(10) unsigned DEFAULT NULL,
  `section_id` int(10) unsigned DEFAULT NULL,
  `target_ids` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('Normal','Important','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Published','Scheduled','Expired','Archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Published',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`notice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_notices`
--

LOCK TABLES `tbl_notices` WRITE;
/*!40000 ALTER TABLE `tbl_notices` DISABLE KEYS */;
INSERT INTO `tbl_notices` VALUES (1,1,'General','Revised Bus Timings from Monday','Transport Office',NULL,'All','2026-08-15',NULL,'Morning pickup routes will commence 10 minutes earlier due to road expansion on the bypass.','All','Entire School',NULL,NULL,NULL,'Normal',NULL,'Draft','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,1,'General','Annual Sports Day Registrations Open','Physical Education',NULL,'Grades 6-12','2026-08-13',NULL,'Students can register with their physical education teachers for track and field events.','All','Entire School',NULL,NULL,NULL,'Normal',NULL,'Draft','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,1,'General','Library Books Due for Return','Library',NULL,'All','2026-08-11',NULL,'All borrowed library books must be returned or renewed by Friday.','All','Entire School',NULL,NULL,NULL,'Normal',NULL,'Draft','2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,1,'General','Mid-Term Exam Timetable','Academics',NULL,'Grades 6-12','2026-08-05',NULL,'Mid-Term examination timetable and syllabus have been published on the student portal.','All','Entire School',NULL,NULL,NULL,'Normal',NULL,'Draft','2026-08-17 10:32:48','2026-08-17 10:32:48','n');
/*!40000 ALTER TABLE `tbl_notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_notification_preferences`
--

DROP TABLE IF EXISTS `tbl_notification_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_notification_preferences` (
  `preference_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_type` enum('Parent','Teacher','Student','Staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Parent',
  `preference_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`preference_id`),
  UNIQUE KEY `uk_user_pref` (`user_id`,`user_type`,`preference_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_notification_preferences`
--

LOCK TABLES `tbl_notification_preferences` WRITE;
/*!40000 ALTER TABLE `tbl_notification_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_notification_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_notification_rules`
--

DROP TABLE IF EXISTS `tbl_notification_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_notification_rules` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_module` enum('Attendance','Fees','Homework','Examination','Leave','Transport','Certificates','General') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `template_id` int(10) unsigned NOT NULL,
  `channel` enum('In-App','SMS','WhatsApp','Email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'In-App',
  `recipient_type` enum('Student','Parent','Teacher','Staff','Principal','Admin','Class','Section','Group') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Parent',
  `conditions_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions_json`)),
  `frequency` enum('Once per event','Once per day','Once per week') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Once per event',
  `cooldown_minutes` int(10) unsigned NOT NULL DEFAULT 60,
  `priority` enum('Normal','Important','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`rule_id`),
  KEY `idx_nr_event` (`event_name`),
  KEY `idx_nr_module` (`source_module`),
  KEY `idx_nr_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_notification_rules`
--

LOCK TABLES `tbl_notification_rules` WRITE;
/*!40000 ALTER TABLE `tbl_notification_rules` DISABLE KEYS */;
INSERT INTO `tbl_notification_rules` VALUES (1,'Daily Student Absent SMS to Parents','Attendance Absent','Attendance',6,'SMS','Parent',NULL,'Once per event',60,'Important','Active',NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(2,'Fee Overdue WhatsApp Notification','Fee Overdue','Fees',9,'WhatsApp','Parent',NULL,'Once per event',60,'Urgent','Active',NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(3,'Assignment Published In-App Alert','Homework Published','Homework',11,'In-App','Student',NULL,'Once per event',60,'Normal','Active',NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(4,'Exam Results Published Email','Result Published','Examination',14,'Email','Parent',NULL,'Once per event',60,'Important','Active',NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n'),(5,'Staff Leave Decision Alert','Leave Approved','Leave',15,'In-App','Staff',NULL,'Once per event',60,'Normal','Active',NULL,'2026-08-20 15:48:43','2026-08-20 15:48:43','n');
/*!40000 ALTER TABLE `tbl_notification_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_parent_students`
--

DROP TABLE IF EXISTS `tbl_parent_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_parent_students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_user_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `relationship` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Parent',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_parent_student` (`parent_user_id`,`student_id`),
  KEY `idx_ps_parent` (`parent_user_id`),
  KEY `idx_ps_student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_parent_students`
--

LOCK TABLES `tbl_parent_students` WRITE;
/*!40000 ALTER TABLE `tbl_parent_students` DISABLE KEYS */;
INSERT INTO `tbl_parent_students` VALUES (1,6,1,'Father','2026-08-20 16:09:13','n'),(4,0,1,'Father','2026-08-28 14:37:07','n');
/*!40000 ALTER TABLE `tbl_parent_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_periods`
--

DROP TABLE IF EXISTS `tbl_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_periods` (
  `period_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_number` int(10) unsigned NOT NULL DEFAULT 1,
  `period_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `period_order` int(10) unsigned NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `academic_year_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`period_id`),
  UNIQUE KEY `uk_period_name` (`period_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_periods`
--

LOCK TABLES `tbl_periods` WRITE;
/*!40000 ALTER TABLE `tbl_periods` DISABLE KEYS */;
INSERT INTO `tbl_periods` VALUES (1,1,'Period 1','09:00:00','09:45:00',1,1,NULL,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(2,2,'Period 2','09:45:00','10:30:00',2,1,NULL,'2026-08-18 13:24:49','2026-08-20 11:39:28','n'),(3,3,'Period 3','10:45:00','11:30:00',3,1,NULL,'2026-08-18 13:24:49','2026-08-20 11:39:28','n'),(4,4,'Period 4','11:30:00','12:15:00',4,1,NULL,'2026-08-18 13:24:49','2026-08-20 11:39:28','n'),(5,5,'Period 5','13:00:00','13:45:00',5,1,NULL,'2026-08-18 13:24:49','2026-08-20 11:39:28','n'),(6,6,'Period 6','13:45:00','14:30:00',6,1,NULL,'2026-08-18 13:24:49','2026-08-20 11:39:28','n'),(7,7,'Period 7','14:30:00','15:15:00',7,1,NULL,'2026-08-18 13:24:49','2026-09-03 12:02:24','y');
/*!40000 ALTER TABLE `tbl_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_permission_audit_logs`
--

DROP TABLE IF EXISTS `tbl_permission_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_permission_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` enum('Role','User','Permission','Security') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Role',
  `target_id` int(10) unsigned NOT NULL,
  `previous_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_pal_action` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_permission_audit_logs`
--

LOCK TABLES `tbl_permission_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_permission_audit_logs` DISABLE KEYS */;
INSERT INTO `tbl_permission_audit_logs` VALUES (1,1,'User Override Set','User',8,NULL,NULL,'Test override audit logging verification','2026-08-20 16:27:22','n'),(2,1,'User Override Set','User',9,NULL,NULL,'Test override audit logging verification','2026-08-20 16:28:09','n'),(3,1,'User Override Set','User',10,NULL,NULL,'Test override audit logging verification','2026-08-20 16:37:02','n'),(4,1,'User Created','User',11,NULL,'{\"username\":\"andhu\",\"role_id\":1}','Created user account: andhu','2026-08-21 07:49:12','n'),(5,1,'Role Changed','User',11,'Role #1','Role #2','Updated role for user: andhu','2026-08-21 07:49:27','n'),(6,1,'User Override Set','User',11,NULL,'Grant Perm #49','Set override for user: andhu','2026-08-21 07:49:49','n'),(7,1,'User Override Set','User',11,NULL,'Revoke Perm #41','Set override for user: andhu','2026-08-21 07:49:54','n'),(8,1,'User Override Set','User',11,NULL,'Revoke Perm #13','Set override for user: andhu','2026-08-21 07:50:04','n'),(9,1,'User Override Removed','User',11,NULL,'Removed Perm #13','Reset override for user: andhu','2026-08-21 07:50:06','n'),(10,1,'User Override Set','User',11,NULL,'Grant Perm #51','Set override for user: andhu','2026-08-21 07:50:18','n'),(11,1,'User Override Set','User',11,NULL,'Grant Perm #47','Set override for user: andhu','2026-08-21 07:50:21','n'),(12,1,'Permissions Updated','Role',1,NULL,'53 permissions','Updated permission matrix for role: Super Admin','2026-08-21 07:57:21','n'),(13,1,'Permissions Updated','Role',3,NULL,'15 permissions','Updated permission matrix for role: Teacher','2026-08-21 07:57:47','n'),(14,1,'Security Updated','Security',1,NULL,'{\"max_failed_attempts\":5,\"lockout_duration_minutes\":30,\"session_timeout_minutes\":120,\"password_min_length\":8,\"require_special_chars\":1,\"require_numbers\":1,\"password_expiry_days\":90,\"allow_concurrent_sessions\":0}','Updated global security policies','2026-08-21 07:59:32','n'),(15,1,'Permissions Updated','Role',1,NULL,'53 permissions','Updated permission matrix for role: Super Admin','2026-08-21 08:16:04','n'),(16,1,'Permissions Updated','Role',3,NULL,'3 permissions','Updated permission matrix for role: Teacher','2026-08-21 09:26:57','n'),(17,1,'User Created','User',14,NULL,'{\"username\":\"freshuser777\",\"role_id\":1}','Created user account: freshuser777','2026-08-21 11:48:54','n'),(18,1,'User Created','User',15,NULL,'{\"username\":\"Admin\",\"role_id\":1}','Created user account: Admin','2026-08-21 11:57:56','n'),(19,1,'User Created','User',16,NULL,'{\"username\":\"newuser101\",\"role_id\":1}','Created user account: newuser101','2026-08-21 11:57:56','n'),(20,1,'User Override Set','User',58,NULL,NULL,'Test override audit logging verification','2026-08-28 14:37:07','n'),(21,15,'Password Reset','User',15,NULL,NULL,'Admin reset password','2026-08-29 11:50:07','n');
/*!40000 ALTER TABLE `tbl_permission_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_permissions`
--

DROP TABLE IF EXISTS `tbl_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_permissions` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `uk_perm_key` (`permission_key`),
  KEY `idx_perm_module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_permissions`
--

LOCK TABLES `tbl_permissions` WRITE;
/*!40000 ALTER TABLE `tbl_permissions` DISABLE KEYS */;
INSERT INTO `tbl_permissions` VALUES (1,'Dashboard','view','dashboard.view','View Dashboard','Access primary dashboard and KPI metrics','2026-08-20 16:09:13','n'),(2,'Students','view','students.view','View Students','Browse student directory and profile data','2026-08-20 16:09:13','n'),(3,'Students','create','students.create','Admit Student','Create new student admission','2026-08-20 16:09:13','n'),(4,'Students','edit','students.edit','Edit Student','Modify existing student profile','2026-08-20 16:09:13','n'),(5,'Students','delete','students.delete','Delete / Archive Student','Deactivate or delete student record','2026-08-20 16:09:13','n'),(6,'Students','promote','students.promote','Promote Students','Perform batch academic student promotion','2026-08-20 16:09:13','n'),(7,'Students','export','students.export','Export Students','Download student CSV/Excel exports','2026-08-20 16:09:13','n'),(8,'Staff','view','staff.view','View Staff','Browse staff directory and profiles','2026-08-20 16:09:13','n'),(9,'Staff','create','staff.create','Add Staff','Register new employee or faculty','2026-08-20 16:09:13','n'),(10,'Staff','edit','staff.edit','Edit Staff','Update staff profile details','2026-08-20 16:09:13','n'),(11,'Staff','delete','staff.delete','Delete Staff','Deactivate or remove staff record','2026-08-20 16:09:13','n'),(12,'Academics','view','academics.view','View Academic Setup','View classes, sections, and subjects','2026-08-20 16:09:13','n'),(13,'Academics','manage','academics.manage','Manage Academics','Create and modify classes, sections, and subjects','2026-08-20 16:09:13','n'),(14,'Attendance','view','attendance.view','View Attendance','View student attendance logs and stats','2026-08-20 16:09:13','n'),(15,'Attendance','mark','attendance.mark','Mark Attendance','Record daily and period-wise attendance','2026-08-20 16:09:13','n'),(16,'Attendance','edit','attendance.edit','Edit Attendance','Modify previously saved attendance records','2026-08-20 16:09:13','n'),(17,'Attendance','reports','attendance.reports','Attendance Reports','View and export attendance analytics','2026-08-20 16:09:13','n'),(18,'Examination','view','exams.view','View Exams','View exam schedules and results','2026-08-20 16:09:13','n'),(19,'Examination','create','exams.create','Create Exams','Create exam terms, schedules, and subject allocations','2026-08-20 16:09:13','n'),(20,'Examination','marks_entry','exams.marks_entry','Enter Marks','Input student test marks and grades','2026-08-20 16:09:13','n'),(21,'Examination','publish','exams.publish','Publish Results','Calculate positions and publish report cards','2026-08-20 16:09:13','n'),(22,'Examination','reports','exams.reports','Exam Reports','Access examination reports and cards','2026-08-20 16:09:13','n'),(23,'Fees','view','fees.view','View Fee Dashboard','View fee structures and student ledgers','2026-08-20 16:09:13','n'),(24,'Fees','manage_structure','fees.manage_structure','Manage Fee Structure','Create fee heads and category fee structures','2026-08-20 16:09:13','n'),(25,'Fees','assign','fees.assign','Assign Fees','Allocate fees to classes or students','2026-08-20 16:09:13','n'),(26,'Fees','collect','fees.collect','Collect Fees','Record fee payments and issue receipts','2026-08-20 16:09:13','n'),(27,'Fees','refund','fees.refund','Refunds & Adjustments','Process fee refunds and ledger adjustments','2026-08-20 16:09:13','n'),(28,'Fees','reports','fees.reports','Finance Reports','Generate fee collection and due reports','2026-08-20 16:09:13','n'),(29,'Timetable','view','timetable.view','View Timetable','View class and teacher timetables','2026-08-20 16:09:13','n'),(30,'Timetable','manage','timetable.manage','Build Timetable','Create, edit, and publish weekly timetables','2026-08-20 16:09:13','n'),(31,'Homework','view','homework.view','View Assignments','Browse homework listings','2026-08-20 16:09:13','n'),(32,'Homework','create','homework.create','Create Assignment','Publish new homework assignments','2026-08-20 16:09:13','n'),(33,'Homework','review','homework.review','Review Submissions','Evaluate student submissions and grade','2026-08-20 16:09:13','n'),(34,'Communication','view','communication.view','View Communication','Access notices, logs, and dashboard','2026-08-20 16:09:13','n'),(35,'Communication','send','communication.send','Send Messages','Dispatch notices, SMS, WhatsApp, and Email','2026-08-20 16:09:13','n'),(36,'Communication','manage_templates','communication.manage_templates','Manage Templates','Create and edit notification templates','2026-08-20 16:09:13','n'),(37,'Communication','automated_rules','communication.automated_rules','Manage Automation Rules','Configure automated event rules','2026-08-20 16:09:13','n'),(38,'Leave','view','leave.view','View Leaves','Browse student and staff leave requests','2026-08-20 16:09:13','n'),(39,'Leave','apply','leave.apply','Apply Leave','Submit personal leave application','2026-08-20 16:09:13','n'),(40,'Leave','approve','leave.approve','Approve / Reject Leave','Sanction or reject leave applications','2026-08-20 16:09:13','n'),(41,'Transport','view','transport.view','View Transport','Browse vehicles, routes, and stops','2026-08-20 16:09:13','n'),(42,'Transport','manage','transport.manage','Manage Transport','Configure fleet, maintenance, and student assignments','2026-08-20 16:09:13','n'),(43,'Certificates','view','certificates.view','View Certificates','Browse issued certificates and documents','2026-08-20 16:09:13','n'),(44,'Certificates','generate','certificates.generate','Generate Certificates','Generate bonafide, TC, study certificates','2026-08-20 16:09:13','n'),(45,'Certificates','verify_docs','certificates.verify_docs','Verify Documents','Approve or reject student identity documents','2026-08-20 16:09:13','n'),(46,'Reports','view','reports.view','Access Reports','Access institutional consolidated reports','2026-08-20 16:09:13','n'),(47,'Users','view','users.view','View Users','Browse user accounts, roles, and logs','2026-08-20 16:09:13','n'),(48,'Users','create','users.create','Create Users','Register new user login accounts','2026-08-20 16:09:13','n'),(49,'Users','edit','users.edit','Edit Users','Update accounts, reset passwords, change roles','2026-08-20 16:09:13','n'),(50,'Users','delete','users.delete','Deactivate Users','Deactivate or lock user accounts','2026-08-20 16:09:13','n'),(51,'Users','manage_roles','users.manage_roles','Manage Roles & Permissions','Configure RBAC permission matrix','2026-08-20 16:09:13','n'),(52,'Settings','view','settings.view','View Settings','View school configurations','2026-08-20 16:09:13','n'),(53,'Settings','edit','settings.edit','Edit Settings','Modify school profile and global parameters','2026-08-20 16:09:13','n');
/*!40000 ALTER TABLE `tbl_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_role_permissions`
--

DROP TABLE IF EXISTS `tbl_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_role_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_perm` (`role_id`,`permission_id`),
  KEY `idx_rp_role` (`role_id`),
  KEY `idx_rp_perm` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_role_permissions`
--

LOCK TABLES `tbl_role_permissions` WRITE;
/*!40000 ALTER TABLE `tbl_role_permissions` DISABLE KEYS */;
INSERT INTO `tbl_role_permissions` VALUES (54,2,1,'2026-08-20 16:09:13','n'),(55,2,2,'2026-08-20 16:09:13','n'),(56,2,3,'2026-08-20 16:09:13','n'),(57,2,4,'2026-08-20 16:09:13','n'),(58,2,6,'2026-08-20 16:09:13','n'),(59,2,7,'2026-08-20 16:09:13','n'),(60,2,8,'2026-08-20 16:09:13','n'),(61,2,9,'2026-08-20 16:09:13','n'),(62,2,10,'2026-08-20 16:09:13','n'),(63,2,12,'2026-08-20 16:09:13','n'),(64,2,13,'2026-08-20 16:09:13','n'),(65,2,14,'2026-08-20 16:09:13','n'),(66,2,15,'2026-08-20 16:09:13','n'),(67,2,16,'2026-08-20 16:09:13','n'),(68,2,17,'2026-08-20 16:09:13','n'),(69,2,18,'2026-08-20 16:09:13','n'),(70,2,19,'2026-08-20 16:09:13','n'),(71,2,20,'2026-08-20 16:09:13','n'),(72,2,21,'2026-08-20 16:09:13','n'),(73,2,22,'2026-08-20 16:09:13','n'),(74,2,23,'2026-08-20 16:09:13','n'),(75,2,28,'2026-08-20 16:09:13','n'),(76,2,29,'2026-08-20 16:09:13','n'),(77,2,30,'2026-08-20 16:09:13','n'),(78,2,31,'2026-08-20 16:09:13','n'),(79,2,32,'2026-08-20 16:09:13','n'),(80,2,33,'2026-08-20 16:09:13','n'),(81,2,34,'2026-08-20 16:09:13','n'),(82,2,35,'2026-08-20 16:09:13','n'),(83,2,36,'2026-08-20 16:09:13','n'),(84,2,37,'2026-08-20 16:09:13','n'),(85,2,38,'2026-08-20 16:09:13','n'),(86,2,39,'2026-08-20 16:09:13','n'),(87,2,40,'2026-08-20 16:09:13','n'),(88,2,41,'2026-08-20 16:09:13','n'),(89,2,42,'2026-08-20 16:09:13','n'),(90,2,43,'2026-08-20 16:09:13','n'),(91,2,44,'2026-08-20 16:09:13','n'),(92,2,45,'2026-08-20 16:09:13','n'),(93,2,46,'2026-08-20 16:09:13','n'),(94,2,52,'2026-08-20 16:09:13','n'),(110,4,1,'2026-08-20 16:09:13','n'),(111,4,2,'2026-08-20 16:09:13','n'),(112,4,7,'2026-08-20 16:09:13','n'),(113,4,23,'2026-08-20 16:09:13','n'),(114,4,24,'2026-08-20 16:09:13','n'),(115,4,25,'2026-08-20 16:09:13','n'),(116,4,26,'2026-08-20 16:09:13','n'),(117,4,27,'2026-08-20 16:09:13','n'),(118,4,28,'2026-08-20 16:09:13','n'),(119,4,34,'2026-08-20 16:09:13','n'),(120,4,35,'2026-08-20 16:09:13','n'),(121,4,39,'2026-08-20 16:09:13','n'),(122,5,1,'2026-08-20 16:09:13','n'),(123,5,2,'2026-08-20 16:09:13','n'),(124,5,41,'2026-08-20 16:09:13','n'),(125,5,42,'2026-08-20 16:09:13','n'),(126,5,34,'2026-08-20 16:09:13','n'),(127,5,35,'2026-08-20 16:09:13','n'),(128,5,39,'2026-08-20 16:09:13','n'),(129,6,1,'2026-08-20 16:09:13','n'),(130,6,2,'2026-08-20 16:09:13','n'),(131,6,3,'2026-08-20 16:09:13','n'),(132,6,43,'2026-08-20 16:09:13','n'),(133,6,44,'2026-08-20 16:09:13','n'),(134,6,45,'2026-08-20 16:09:13','n'),(135,6,34,'2026-08-20 16:09:13','n'),(136,6,35,'2026-08-20 16:09:13','n'),(137,6,39,'2026-08-20 16:09:13','n'),(138,7,1,'2026-08-20 16:09:13','n'),(139,7,2,'2026-08-20 16:09:13','n'),(140,7,14,'2026-08-20 16:09:13','n'),(141,7,18,'2026-08-20 16:09:13','n'),(142,7,23,'2026-08-20 16:09:13','n'),(143,7,29,'2026-08-20 16:09:13','n'),(144,7,31,'2026-08-20 16:09:13','n'),(145,7,34,'2026-08-20 16:09:13','n'),(146,7,39,'2026-08-20 16:09:13','n'),(147,8,1,'2026-08-20 16:09:13','n'),(148,8,14,'2026-08-20 16:09:13','n'),(149,8,18,'2026-08-20 16:09:13','n'),(150,8,23,'2026-08-20 16:09:13','n'),(151,8,29,'2026-08-20 16:09:13','n'),(152,8,31,'2026-08-20 16:09:13','n'),(153,8,34,'2026-08-20 16:09:13','n'),(222,1,13,'2026-08-21 11:46:04','n'),(223,1,12,'2026-08-21 11:46:04','n'),(224,1,16,'2026-08-21 11:46:04','n'),(225,1,15,'2026-08-21 11:46:04','n'),(226,1,17,'2026-08-21 11:46:04','n'),(227,1,14,'2026-08-21 11:46:04','n'),(228,1,44,'2026-08-21 11:46:04','n'),(229,1,45,'2026-08-21 11:46:04','n'),(230,1,43,'2026-08-21 11:46:04','n'),(231,1,37,'2026-08-21 11:46:04','n'),(232,1,36,'2026-08-21 11:46:04','n'),(233,1,35,'2026-08-21 11:46:04','n'),(234,1,34,'2026-08-21 11:46:04','n'),(235,1,1,'2026-08-21 11:46:04','n'),(236,1,19,'2026-08-21 11:46:04','n'),(237,1,20,'2026-08-21 11:46:04','n'),(238,1,21,'2026-08-21 11:46:04','n'),(239,1,22,'2026-08-21 11:46:04','n'),(240,1,18,'2026-08-21 11:46:04','n'),(241,1,25,'2026-08-21 11:46:04','n'),(242,1,26,'2026-08-21 11:46:04','n'),(243,1,24,'2026-08-21 11:46:04','n'),(244,1,27,'2026-08-21 11:46:04','n'),(245,1,28,'2026-08-21 11:46:04','n'),(246,1,23,'2026-08-21 11:46:04','n'),(247,1,32,'2026-08-21 11:46:04','n'),(248,1,33,'2026-08-21 11:46:04','n'),(249,1,31,'2026-08-21 11:46:04','n'),(250,1,39,'2026-08-21 11:46:04','n'),(251,1,40,'2026-08-21 11:46:04','n'),(252,1,38,'2026-08-21 11:46:04','n'),(253,1,46,'2026-08-21 11:46:04','n'),(254,1,53,'2026-08-21 11:46:04','n'),(255,1,52,'2026-08-21 11:46:04','n'),(256,1,9,'2026-08-21 11:46:04','n'),(257,1,11,'2026-08-21 11:46:04','n'),(258,1,10,'2026-08-21 11:46:04','n'),(259,1,8,'2026-08-21 11:46:04','n'),(260,1,3,'2026-08-21 11:46:04','n'),(261,1,5,'2026-08-21 11:46:04','n'),(262,1,4,'2026-08-21 11:46:04','n'),(263,1,7,'2026-08-21 11:46:04','n'),(264,1,6,'2026-08-21 11:46:04','n'),(265,1,2,'2026-08-21 11:46:04','n'),(266,1,30,'2026-08-21 11:46:04','n'),(267,1,29,'2026-08-21 11:46:04','n'),(268,1,42,'2026-08-21 11:46:04','n'),(269,1,41,'2026-08-21 11:46:04','n'),(270,1,48,'2026-08-21 11:46:04','n'),(271,1,50,'2026-08-21 11:46:04','n'),(272,1,49,'2026-08-21 11:46:04','n'),(273,1,51,'2026-08-21 11:46:04','n'),(274,1,47,'2026-08-21 11:46:04','n'),(275,3,12,'2026-08-21 12:56:57','n'),(276,3,15,'2026-08-21 12:56:57','n'),(277,3,14,'2026-08-21 12:56:57','n');
/*!40000 ALTER TABLE `tbl_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_roles`
--

DROP TABLE IF EXISTS `tbl_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Staff',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uk_role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_roles`
--

LOCK TABLES `tbl_roles` WRITE;
/*!40000 ALTER TABLE `tbl_roles` DISABLE KEYS */;
INSERT INTO `tbl_roles` VALUES (1,'Super Admin','SUPER_ADMIN','Admin','Unrestricted master access to all school modules and settings.',1,'Active','2026-08-17 10:32:48','2026-08-20 16:09:13','n'),(2,'Principal','PRINCIPAL','Principal','Executive academic leadership, staff oversight, and institutional reports.',1,'Active','2026-08-17 10:32:48','2026-08-20 16:09:13','n'),(3,'Teacher','TEACHER','Teacher','Classroom teaching faculty, student attendance, exams, homework.',1,'Active','2026-08-17 10:32:48','2026-08-20 16:09:13','n'),(4,'Accountant','ACCOUNTANT','Accountant','Fee collection, structure configuration, receipts, and financial ledger.',1,'Active','2026-08-17 10:32:48','2026-08-20 16:09:13','n'),(5,'Transport Manager','TRANSPORT_MGR','Transport Manager','Fleet, routes, stops, vehicle maintenance, and student bus assignments.',1,'Active','2026-08-17 10:32:48','2026-08-20 16:09:13','n'),(6,'Receptionist','RECEPTIONIST','Receptionist','Front desk reception, visitor inquiries, certificates, basic notices.',1,'Active','2026-08-20 16:09:13','2026-08-20 16:09:13','n'),(7,'Parent','PARENT','Parent','Parent portal access to monitor attendance, fee dues, marks, notices for own children.',1,'Active','2026-08-20 16:09:13','2026-08-20 16:09:13','n'),(8,'Student','STUDENT','Student','Student portal for assignments, class timetable, results, attendance.',1,'Active','2026-08-20 16:09:13','2026-08-20 16:09:13','n'),(9,'Librarian','LIBRARIAN','Librarian','Library catalog, issue/return circulation, fine collection, and inventory.',1,'Active','2026-08-20 16:09:13','2026-08-20 16:09:13','n');
/*!40000 ALTER TABLE `tbl_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_route_stops`
--

DROP TABLE IF EXISTS `tbl_route_stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_route_stops` (
  `stop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_id` int(10) unsigned NOT NULL,
  `stop_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stop_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sequence_order` int(10) unsigned NOT NULL DEFAULT 1,
  `pickup_time` time NOT NULL DEFAULT '07:30:00',
  `drop_time` time NOT NULL DEFAULT '15:30:00',
  `landmark` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distance_km` decimal(5,2) NOT NULL DEFAULT 2.00,
  `fare_amount` decimal(10,2) NOT NULL DEFAULT 1200.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`stop_id`),
  KEY `idx_stop_route` (`route_id`),
  KEY `idx_stop_seq` (`route_id`,`sequence_order`),
  KEY `idx_route_stops_status` (`route_id`,`status`,`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_route_stops`
--

LOCK TABLES `tbl_route_stops` WRITE;
/*!40000 ALTER TABLE `tbl_route_stops` DISABLE KEYS */;
INSERT INTO `tbl_route_stops` VALUES (1,1,'Punalur Junction','PNL-01',1,'07:15:00','16:00:00','Opposite Municipal Office',0.00,1600.00,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(2,1,'Kalluvathukkal Temple','KVK-02',2,'07:30:00','15:45:00','Near Temple Gate',6.50,1500.00,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(3,1,'Chemmanthoor Bridge','CMB-03',3,'07:45:00','15:30:00','Bridge East Entry',14.00,1400.00,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(4,1,'School Campus','SCH-04',4,'08:00:00','15:15:00','Main Bus Bay',22.50,0.00,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(5,2,'Anchal Market','ANC-01',1,'07:20:00','15:55:00','Market Main Gate',0.00,1500.00,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(6,2,'Bypass Junction','BYP-02',2,'07:35:00','15:40:00','Traffic Island',7.00,1350.00,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(7,2,'School Campus','SCH-03',3,'07:55:00','15:15:00','Main Bus Bay',18.00,0.00,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(8,1,'Punalur Junction','PNL-01',1,'07:15:00','16:00:00','Opposite Municipal Office',0.00,1600.00,1,'2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(9,1,'Kalluvathukkal Temple','KVK-02',2,'07:30:00','15:45:00','Near Temple Gate',6.50,1500.00,1,'2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(10,1,'Chemmanthoor Bridge','CMB-03',3,'07:45:00','15:30:00','Bridge East Entry',14.00,1400.00,1,'2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(11,1,'School Campus','SCH-04',4,'08:00:00','15:15:00','Main Bus Bay',22.50,0.00,1,'2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(12,2,'Anchal Market','ANC-01',1,'07:20:00','15:55:00','Market Main Gate',0.00,1500.00,1,'2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(13,2,'Bypass Junction','BYP-02',2,'07:35:00','15:40:00','Traffic Island',7.00,1350.00,1,'2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(14,2,'School Campus','SCH-03',3,'07:55:00','15:15:00','Main Bus Bay',18.00,0.00,1,'2026-08-20 14:53:30','2026-08-20 14:53:30','n');
/*!40000 ALTER TABLE `tbl_route_stops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_school_settings`
--

DROP TABLE IF EXISTS `tbl_school_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_school_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `established_year` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `principal_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_school_settings`
--

LOCK TABLES `tbl_school_settings` WRITE;
/*!40000 ALTER TABLE `tbl_school_settings` DISABLE KEYS */;
INSERT INTO `tbl_school_settings` VALUES (1,'Login2','EDU-KL-2026','1998','Antony Xavier','+91 484 234 5678','info@gmail.edu','www.school.edu',NULL,'Kakkanad, Ernakulam, Kerala - 682030','A CBSE-affiliated school known for excellence in academics and sports.','2026-08-29 12:54:59','n');
/*!40000 ALTER TABLE `tbl_school_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_sections`
--

DROP TABLE IF EXISTS `tbl_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_sections` (
  `section_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) unsigned NOT NULL,
  `section_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_teacher_id` int(10) unsigned DEFAULT NULL,
  `room_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int(10) unsigned NOT NULL DEFAULT 40,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`section_id`),
  KEY `idx_section_class` (`class_id`),
  KEY `idx_section_teacher` (`class_teacher_id`),
  CONSTRAINT `fk_section_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_section_teacher` FOREIGN KEY (`class_teacher_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_sections`
--

LOCK TABLES `tbl_sections` WRITE;
/*!40000 ALTER TABLE `tbl_sections` DISABLE KEYS */;
INSERT INTO `tbl_sections` VALUES (12,1,'A',NULL,'',20,'',1,'2026-08-21 10:57:29','2026-08-21 14:27:29','n');
/*!40000 ALTER TABLE `tbl_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_staff`
--

DROP TABLE IF EXISTS `tbl_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_staff` (
  `staff_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Male',
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Teacher',
  `staff_type` enum('teacher','non_teaching') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'teacher',
  `department_id` int(10) unsigned DEFAULT NULL,
  `designation_id` int(10) unsigned DEFAULT NULL,
  `joining_date` date NOT NULL,
  `qualification` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_status` enum('Active','On Leave','Probation','Resigned','Suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `uk_employee_code` (`employee_code`),
  KEY `idx_staff_department` (`department_id`),
  KEY `idx_staff_designation` (`designation_id`),
  KEY `idx_staff_status_deleted` (`status`,`is_deleted`,`staff_type`),
  CONSTRAINT `fk_staff_department` FOREIGN KEY (`department_id`) REFERENCES `tbl_departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_staff_designation` FOREIGN KEY (`designation_id`) REFERENCES `tbl_designations` (`designation_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_staff`
--

LOCK TABLES `tbl_staff` WRITE;
/*!40000 ALTER TABLE `tbl_staff` DISABLE KEYS */;
INSERT INTO `tbl_staff` VALUES (1,'EMP1001','Priya Varma','priya.varma@gmail.com','+91 98471 00001','','Female',NULL,'1985-04-12','Teacher','teacher',1,3,'2018-06-01','M.Sc. Mathematics, B.Ed','8 Years','Algebra & Calculus','Active',45000.00,'Kakkanad, Kochi, Kerala','staff_1_20260902073118_6b8eb7.jpg',1,'2026-08-17 10:32:48','2026-09-02 07:31:18','n'),(2,'EMP1002','George Mathew','george.mathew@gmail.com','+91 98471 00002',NULL,'Male',NULL,'1980-09-20','Teacher','teacher',2,2,'2015-03-14','M.Sc. Physics, B.Ed','12 Years','Mechanics & Optics','Active',55000.00,'Palarivattom, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(3,'EMP1003','Fathima Beevi','fathima.beevi@gmail.com','+91 98471 00003',NULL,'Female',NULL,'1988-11-05','Accountant','non_teaching',5,5,'2020-01-09','M.Com, CA Inter','6 Years','Financial Accounting','Active',38000.00,'Aluva, Ernakulam, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(4,'EMP1004','Sunil Kumar','sunil.kumar@gmail.com','+91 98471 00004',NULL,'Male',NULL,'1992-06-18','Receptionist','non_teaching',4,6,'2021-08-22','B.Com','4 Years','Front Office','Active',28000.00,'Edappally, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(5,'EMP1005','Lakshmi Pillai','lakshmi.pillai@gmail.com','+91 98471 00005',NULL,'Female',NULL,'1989-02-28','Teacher','teacher',3,4,'2019-07-11','M.A. English, B.Ed','10 Years','English Literature','Active',40000.00,'Tripunithura, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(6,'EMP1006','Antony Xavier','antony.xavier@gmail.com','+91 98471 00006',NULL,'Male',NULL,'1975-01-15','Principal','non_teaching',4,1,'2012-04-01','Ph.D. Education, M.Sc','20 Years','School Administration','Active',75000.00,'Marine Drive, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(7,'EMP1007','Reshma Nair','reshma.nair@gmail.com','+91 98471 00007',NULL,'Female',NULL,'1995-10-10','Office Staff','non_teaching',4,7,'2022-02-03','B.A.','3 Years','Office Administration','Active',24000.00,'Vyttila, Kochi, Kerala',NULL,0,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(8,'EMP1008','Vinod Kumar','vinod.kumar@gmail.com','+91 98471 00008',NULL,'Male',NULL,'1987-08-14','Teacher','teacher',6,4,'2017-06-01','MCA','7 Years','Computer Science & AI','Active',42000.00,'Kalamassery, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(9,'EMP1009','Nisha Roy','nisha.roy@gmail.com','+91 98471 00009',NULL,'Female',NULL,'1990-03-22','Teacher','teacher',3,4,'2021-05-15','M.A. Hindi, B.Ed','5 Years','Hindi Language','Active',36000.00,'Kadavanthra, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(10,'EMP1010','Manoj Das','manoj.das@gmail.com','+91 98471 00010',NULL,'Male',NULL,'1986-12-04','Teacher','teacher',1,4,'2019-06-10','M.Sc. Chemistry, B.Ed','9 Years','Organic Chemistry','Active',40000.00,'Thrikkakara, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(11,'EMP1011','Ancy Thomas','ancy.thomas@gmail.com','+91 98471 00011',NULL,'Female',NULL,'1991-07-19','Teacher','teacher',2,4,'2020-08-01','M.Sc. Biology, B.Ed','6 Years','Botany & Zoology','Active',38000.00,'Cheranallur, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(12,'EMP1012','Rahul Sharma','rahul.sharma@gmail.com','+91 98471 00012',NULL,'Male',NULL,'1984-05-30','Teacher','teacher',1,3,'2016-09-01','M.A. Social Science, B.Ed','11 Years','History & Civics','Active',46000.00,'Kaloor, Kochi, Kerala',NULL,1,'2026-08-17 10:32:48','2026-08-18 12:56:56','n'),(13,'EMP2896','Suresh Gopinath','suresh.gopinath@gmail.com','+91 98470 55443','+91 98470 55444','Male','O+','1985-03-22','Teacher','teacher',1,4,'2026-08-01','M.Sc Mathematics, B.Ed, M.Phil','10 Years','Trigonometry & Statistics','Active',48000.00,'Edappally, Kochi, Kerala',NULL,1,'2026-08-18 09:31:21','2026-08-18 13:01:21','n'),(14,'EMP3831','Rajan K','rajan.k@gmail.com','+91 98471 22334',NULL,'Male','A+','1990-11-12','Non-Teaching','non_teaching',4,7,'2026-08-05','B.Com','3 Years',NULL,'Resigned',26000.00,'Vyttila, Kochi',NULL,0,'2026-08-18 09:31:21','2026-08-18 13:01:22','n'),(22,'EMP1199','rsthbs','ertgdsg@gmail.com','1234567890',NULL,'Male',NULL,'1988-06-15','Teacher','teacher',1,1,'2026-09-01',NULL,NULL,NULL,'Active',40000.00,'erfgeagf','staff_new_20260901111249_b314ea.jpg',1,'2026-09-01 11:12:49','2026-09-01 14:42:49','n');
/*!40000 ALTER TABLE `tbl_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_staff_attendance`
--

DROP TABLE IF EXISTS `tbl_staff_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_staff_attendance` (
  `attendance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_status` enum('Present','Absent','Leave','Half Day') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Present',
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `uk_staff_att_date` (`staff_id`,`attendance_date`),
  KEY `idx_staff_att_staff` (`staff_id`),
  CONSTRAINT `fk_staff_att` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_staff_attendance`
--

LOCK TABLES `tbl_staff_attendance` WRITE;
/*!40000 ALTER TABLE `tbl_staff_attendance` DISABLE KEYS */;
INSERT INTO `tbl_staff_attendance` VALUES (1,1,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(2,2,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(3,3,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(4,4,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(5,5,'2026-08-18','Leave','Casual Leave','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(6,6,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(7,7,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(8,8,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(9,9,'2026-08-18','Half Day','Morning session duty','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(10,10,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(11,11,'2026-08-18','Absent','Medical leave','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(12,12,'2026-08-18','Present','','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(13,13,'2026-08-18','Present','On time','2026-08-18 09:31:21','2026-08-18 13:01:21','n'),(14,14,'2026-08-18','Present','Office desk','2026-08-18 09:31:21','2026-08-18 13:01:21','n');
/*!40000 ALTER TABLE `tbl_staff_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_staff_custom_field_values`
--

DROP TABLE IF EXISTS `tbl_staff_custom_field_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_staff_custom_field_values` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `field_value` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`value_id`),
  KEY `idx_staff_id` (`staff_id`),
  KEY `idx_field_id` (`field_id`),
  KEY `idx_staff_field` (`staff_id`,`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_staff_custom_field_values`
--

LOCK TABLES `tbl_staff_custom_field_values` WRITE;
/*!40000 ALTER TABLE `tbl_staff_custom_field_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_staff_custom_field_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_staff_custom_fields`
--

DROP TABLE IF EXISTS `tbl_staff_custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_staff_custom_fields` (
  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(150) NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Other',
  `field_type` enum('text','number','date','dropdown','document','data_document') NOT NULL DEFAULT 'document',
  `field_options` text DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `allow_multiple` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `help_text` varchar(255) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`field_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_category` (`category`),
  KEY `idx_order` (`display_order`),
  KEY `idx_deleted` (`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_staff_custom_fields`
--

LOCK TABLES `tbl_staff_custom_fields` WRITE;
/*!40000 ALTER TABLE `tbl_staff_custom_fields` DISABLE KEYS */;
INSERT INTO `tbl_staff_custom_fields` VALUES (1,'Aadhaar','aadhaar','Identity','data_document',NULL,1,1,0,1,'Enter 12-digit Aadhaar Number & upload copy',1,'2026-09-01 12:05:57','2026-09-01 08:50:34','n'),(2,'PAN','pan','Identity','data_document',NULL,0,1,0,2,'Enter 10-character PAN Number & upload copy',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(3,'DOB Certificate','dob_certificate','DOB','document',NULL,1,1,0,3,'Upload Birth Certificate or Secondary School Certificate',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(4,'Bank Passbook','bank_passbook','Banking','document',NULL,0,1,0,4,'Upload Bank Passbook front page or Cancelled Cheque',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(5,'Experience Certificate','experience_certificate','Experience','document',NULL,0,1,1,5,'Upload previous service/experience certificates (multiple allowed)',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(6,'10th Certificate','tenth_certificate','Education','document',NULL,0,1,0,6,'Upload Secondary School leaving certificate / Marksheet',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(7,'+2 Certificate','plus_two_certificate','Education','document',NULL,0,1,0,7,'Upload Higher Secondary / 12th Certificate / Marksheet',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(8,'UG Certificate','ug_certificate','Education','document',NULL,0,1,0,8,'Upload Undergraduate Degree Certificate',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(9,'PG Certificate','pg_certificate','Education','document',NULL,0,1,0,9,'Upload Postgraduate Degree Certificate',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(10,'Other','other','Other','document',NULL,0,1,1,10,'Upload any other custom staff document (specify document name)',1,'2026-09-01 12:05:57','2026-09-01 12:05:57','n'),(16,'COVID Vaccination Certificate','covid_vaccination_certificate','Other','document',NULL,0,0,0,99,'Upload fully vaccinated certificate copy',NULL,'2026-09-01 08:50:34','2026-09-01 08:50:34','y');
/*!40000 ALTER TABLE `tbl_staff_custom_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_staff_document_types`
--

DROP TABLE IF EXISTS `tbl_staff_document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_staff_document_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) NOT NULL DEFAULT 'n',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_is_deleted` (`is_deleted`),
  KEY `idx_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_staff_document_types`
--

LOCK TABLES `tbl_staff_document_types` WRITE;
/*!40000 ALTER TABLE `tbl_staff_document_types` DISABLE KEYS */;
INSERT INTO `tbl_staff_document_types` VALUES (1,'Aadhaar','Aadhaar Identity Card Copy','Active',1,1,'2026-09-01 12:45:15','2026-09-01 14:44:08','n',NULL),(2,'PAN','PAN Card Copy','Inactive',2,1,'2026-09-01 12:45:15','2026-09-01 12:51:01','y','2026-09-01 09:21:01'),(3,'Experience Certificate','Previous Employment/Experience Certificate','Active',3,1,'2026-09-01 12:45:15','2026-09-01 12:45:15','n',NULL),(4,'Education Certificate','Highest Degree / Qualification Certificate','Active',4,1,'2026-09-01 12:45:15','2026-09-01 12:45:15','n',NULL);
/*!40000 ALTER TABLE `tbl_staff_document_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_staff_documents`
--

DROP TABLE IF EXISTS `tbl_staff_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_staff_documents` (
  `document_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `document_type_id` int(10) unsigned DEFAULT NULL,
  `field_id` int(10) unsigned DEFAULT NULL,
  `document_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_document_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int(10) unsigned DEFAULT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`document_id`),
  KEY `idx_staff_doc` (`staff_id`),
  KEY `idx_field_id` (`field_id`),
  KEY `idx_staff_doc_type` (`staff_id`,`document_type_id`,`is_deleted`),
  CONSTRAINT `fk_staff_doc` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_staff_documents`
--

LOCK TABLES `tbl_staff_documents` WRITE;
/*!40000 ALTER TABLE `tbl_staff_documents` DISABLE KEYS */;
INSERT INTO `tbl_staff_documents` VALUES (1,1,NULL,NULL,'Qualification Certificate','Priya_Varma_MSc_Degree.pdf',NULL,'uploads/staff_docs/doc_priya_degree.pdf',NULL,NULL,NULL,NULL,NULL,1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(2,1,NULL,NULL,'Aadhaar Card','Priya_Varma_Aadhaar.pdf',NULL,'uploads/staff_docs/doc_priya_aadhaar.pdf',NULL,NULL,NULL,NULL,NULL,1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(3,2,NULL,NULL,'Experience Certificate','George_Mathew_Experience.pdf',NULL,'uploads/staff_docs/doc_george_exp.pdf',NULL,NULL,NULL,NULL,NULL,1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(4,3,NULL,NULL,'Appointment Letter','Fathima_Beevi_Appointment.pdf',NULL,'uploads/staff_docs/doc_fathima_app.pdf',NULL,NULL,NULL,NULL,NULL,1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(21,22,1,NULL,'Aadhaar','Aadhaar',NULL,'uploads/staff_docs/doc_22_1_1788253969_1907c932.jpg','backgrounddefault.jpg','image/jpeg',2586107,'image/jpeg',15,1,'2026-09-01 11:12:49','2026-09-01 14:42:49','n'),(22,22,3,NULL,'Experience Certificate','Experience Certificate',NULL,'uploads/staff_docs/doc_22_3_1788253969_2ed3aabe.jpg','backgrounddefault.jpg','image/jpeg',2586107,'image/jpeg',15,1,'2026-09-01 11:12:49','2026-09-01 14:42:49','n'),(23,22,4,NULL,'Education Certificate','Education Certificate',NULL,'uploads/staff_docs/doc_22_4_1788253969_fd0a7301.jpg','backgrounddefault.jpg','image/jpeg',2586107,'image/jpeg',15,1,'2026-09-01 11:12:49','2026-09-01 14:42:49','n');
/*!40000 ALTER TABLE `tbl_staff_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_staff_leave`
--

DROP TABLE IF EXISTS `tbl_staff_leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_staff_leave` (
  `leave_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `leave_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `total_days` int(10) unsigned NOT NULL DEFAULT 1,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `applied_date` date NOT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`leave_id`),
  KEY `idx_staff_leave_staff` (`staff_id`),
  CONSTRAINT `fk_staff_leave` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_staff_leave`
--

LOCK TABLES `tbl_staff_leave` WRITE;
/*!40000 ALTER TABLE `tbl_staff_leave` DISABLE KEYS */;
INSERT INTO `tbl_staff_leave` VALUES (1,5,'Casual Leave','2026-08-18','2026-08-18',1,'Family function in Kottayam','Approved','2026-08-15',NULL,'Approved by Principal','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(2,11,'Medical Leave','2026-08-18','2026-08-20',3,'Viral fever recovery','Approved','2026-08-17',NULL,'Medical certificate submitted','2026-08-18 12:56:57','2026-08-18 12:56:57','n'),(3,4,'Casual Leave','2026-08-22','2026-08-22',1,'Personal work at bank','Approved','2026-08-18',1,'','2026-08-18 12:56:57','2026-08-18 09:35:50','n'),(4,13,'Casual Leave','2026-08-21','2026-08-22',2,'Personal emergency at home','Approved','2026-08-18',1,'Approved by Principal','2026-08-18 09:31:21','2026-08-18 09:31:21','n');
/*!40000 ALTER TABLE `tbl_staff_leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_activities`
--

DROP TABLE IF EXISTS `tbl_student_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `category` enum('Academic','Extracurricular') NOT NULL DEFAULT 'Academic',
  `activity_type` varchar(100) NOT NULL,
  `activity_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `level` varchar(100) DEFAULT NULL,
  `position_result` varchar(200) DEFAULT NULL,
  `year` smallint(6) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`activity_id`),
  KEY `idx_act_student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_activities`
--

LOCK TABLES `tbl_student_activities` WRITE;
/*!40000 ALTER TABLE `tbl_student_activities` DISABLE KEYS */;
INSERT INTO `tbl_student_activities` VALUES (1,45,'Academic','Achievement','handball State team ',NULL,NULL,'4th',2026,1,'2026-09-02 11:22:44','2026-09-02 14:52:44'),(2,45,'Extracurricular','Sports','Handball',NULL,'State','5th',2026,1,'2026-09-02 11:22:44','2026-09-02 14:52:44');
/*!40000 ALTER TABLE `tbl_student_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_documents`
--

DROP TABLE IF EXISTS `tbl_student_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_documents` (
  `document_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `document_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_status` enum('Pending','Verified','Rejected','Expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` int(10) unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`document_id`),
  KEY `idx_doc_student` (`student_id`),
  CONSTRAINT `fk_doc_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_documents`
--

LOCK TABLES `tbl_student_documents` WRITE;
/*!40000 ALTER TABLE `tbl_student_documents` DISABLE KEYS */;
INSERT INTO `tbl_student_documents` VALUES (1,1,NULL,'Birth Certificate','Aarav_Nair_Birth_Certificate.pdf',NULL,NULL,NULL,'uploads/documents/doc_001.pdf','Pending',NULL,NULL,NULL,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,1,NULL,'Previous School TC','Transfer_Certificate_2025.pdf',NULL,NULL,NULL,'uploads/documents/doc_002.pdf','Pending',NULL,NULL,NULL,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,1,NULL,'ID Proof','Aadhaar_Card_Aarav.pdf',NULL,NULL,NULL,'uploads/documents/doc_003.pdf','Pending',NULL,NULL,NULL,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,2,NULL,'Birth Certificate','Diya_Menon_Birth_Certificate.pdf',NULL,NULL,NULL,'uploads/documents/doc_diya_bc.pdf','Pending',NULL,NULL,NULL,1,'2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(5,2,NULL,'Aadhaar Card','Diya_Menon_Aadhaar.pdf',NULL,NULL,NULL,'uploads/documents/doc_diya_aadhaar.pdf','Pending',NULL,NULL,NULL,1,'2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(6,3,NULL,'Transfer Certificate','Kiran_Thomas_Prev_TC.pdf',NULL,NULL,NULL,'uploads/documents/doc_kiran_tc.pdf','Pending',NULL,NULL,NULL,1,'2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(7,4,NULL,'Medical Certificate','Ananya_Pillai_Fitness.pdf',NULL,NULL,NULL,'uploads/documents/doc_ananya_med.pdf','Pending',NULL,NULL,NULL,1,'2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(8,13,NULL,'Birth Certificate','Vikram_Birth_Cert.pdf',NULL,NULL,NULL,'uploads/documents/doc_1787036926.pdf','Pending',NULL,NULL,NULL,1,'2026-08-18 09:08:46','2026-08-18 12:38:46','n'),(9,1,1,'Birth Certificate','Aarav_Birth_Cert.pdf','BC-998811','2011-06-20',NULL,'uploads/student_documents/test.pdf','Verified',NULL,1,'2026-08-20 15:32:30',1,'2026-08-20 15:32:30','2026-08-20 15:32:30','n'),(13,24,NULL,'Transfer Certificate','TC - TC/2026/0891',NULL,NULL,NULL,'uploads/documents/tc_24_tc_1787997493_247223e80c3a.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 11:58:13','2026-08-29 15:28:13','n'),(14,25,NULL,'Transfer Certificate','TC - TC/2026/258024',NULL,NULL,NULL,'uploads/documents/tc_25_tc_1787998259_e21732d54738.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 12:11:02','2026-08-29 15:41:02','n'),(15,26,NULL,'Transfer Certificate','TC - TC/2026/284514',NULL,NULL,NULL,'uploads/documents/tc_26_tc_1787998285_5a290bac544b.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 12:11:29','2026-08-29 15:41:29','n'),(16,27,NULL,'Transfer Certificate','TC - TC/2026/494655',NULL,NULL,NULL,'uploads/documents/tc_27_tc_1787998495_0fa9cd2a5a55.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 12:14:58','2026-08-29 15:44:58','n'),(17,28,NULL,'Transfer Certificate','TC - TC/2026/540763',NULL,NULL,NULL,'uploads/documents/tc_28_tc_1787999541_36e461c4447b.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 12:32:24','2026-08-29 16:02:24','n'),(18,29,NULL,'Transfer Certificate','TC - TC/2026/567111',NULL,NULL,NULL,'uploads/documents/tc_29_tc_1787999568_ab223acd9080.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 12:32:51','2026-08-29 16:02:51','n'),(19,30,NULL,'Transfer Certificate','TC - TC/2026/411367',NULL,NULL,NULL,'uploads/documents/tc_30_tc_1788000412_cc5d1fb538b4.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 12:46:54','2026-08-29 16:16:54','n'),(20,31,NULL,'Transfer Certificate','TC - TC/2026/200679',NULL,NULL,NULL,'uploads/documents/tc_31_tc_1788004202_c784f363ffb0.pdf','Pending',NULL,NULL,NULL,1,'2026-08-29 13:50:05','2026-08-29 17:20:05','n');
/*!40000 ALTER TABLE `tbl_student_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_fees`
--

DROP TABLE IF EXISTS `tbl_student_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_fees` (
  `student_fee_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `class_id` int(10) unsigned NOT NULL DEFAULT 1,
  `section_id` int(10) unsigned NOT NULL DEFAULT 1,
  `fee_structure_id` int(10) unsigned NOT NULL,
  `original_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `concession_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_date` date NOT NULL,
  `payment_status` enum('Pending','Partially Paid','Paid','Overdue','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`student_fee_id`),
  KEY `idx_studfee_student` (`student_id`),
  KEY `idx_studfee_structure` (`fee_structure_id`),
  KEY `idx_student_fees_year_status` (`academic_year_id`,`is_deleted`,`payment_status`),
  CONSTRAINT `fk_studfee_structure` FOREIGN KEY (`fee_structure_id`) REFERENCES `tbl_fee_structures` (`fee_structure_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_studfee_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_fees`
--

LOCK TABLES `tbl_student_fees` WRITE;
/*!40000 ALTER TABLE `tbl_student_fees` DISABLE KEYS */;
INSERT INTO `tbl_student_fees` VALUES (1,'INV-2026-0001',1,1,1,1,1,12000.00,0.00,0.00,12000.00,12000.00,12000.00,0.00,'2026-09-15','Paid',NULL,1,'2026-08-17 10:32:48','2026-08-20 12:14:15','n'),(2,'INV-2026-0002',2,1,1,1,2,12000.00,0.00,0.00,12000.00,12000.00,0.00,12000.00,'2026-09-15','Pending',NULL,1,'2026-08-17 10:32:48','2026-08-20 12:14:15','n'),(3,'INV-2026-0003',3,1,1,1,3,14500.00,0.00,0.00,14500.00,14500.00,0.00,14500.00,'2026-08-10','Overdue',NULL,1,'2026-08-17 10:32:48','2026-08-20 12:14:15','n'),(4,'INV-2026-0004',4,1,1,1,4,9800.00,0.00,0.00,9800.00,9800.00,9800.00,0.00,'2026-09-15','Paid',NULL,1,'2026-08-17 10:32:48','2026-08-20 12:14:15','n'),(7,'INV-2026-0007',3,1,10,10,3,14500.00,0.00,0.00,14500.00,0.00,0.00,14500.00,'2026-08-10','Pending','',1,'2026-08-27 12:02:03','2026-08-27 15:32:03','n');
/*!40000 ALTER TABLE `tbl_student_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_id_cards`
--

DROP TABLE IF EXISTS `tbl_student_id_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_id_cards` (
  `id_card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `class_id` int(10) unsigned DEFAULT NULL,
  `section_id` int(10) unsigned DEFAULT NULL,
  `card_version` int(10) unsigned NOT NULL DEFAULT 1,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Generated',
  `generated_by` int(10) unsigned DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_printed_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_card_id`),
  KEY `idx_idcard_student` (`student_id`),
  KEY `idx_idcard_year` (`academic_year_id`),
  KEY `idx_idcard_card_no` (`card_number`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_id_cards`
--

LOCK TABLES `tbl_student_id_cards` WRITE;
/*!40000 ALTER TABLE `tbl_student_id_cards` DISABLE KEYS */;
INSERT INTO `tbl_student_id_cards` VALUES (1,'IDC-2026-00003',3,1,10,10,3,'Printed',15,'2026-08-31 07:15:16','2026-08-31 07:15:16','2026-08-31 07:15:16'),(2,'IDC-2026-00022',22,1,9,12,5,'Printed',15,'2026-08-31 12:08:33','2026-08-31 12:08:33','2026-08-31 12:08:33'),(3,'IDC-2026-00032',32,1,8,12,4,'Printed',15,'2026-08-31 08:28:09','2026-08-31 08:28:09','2026-08-31 08:28:09'),(4,'IDC-2026-00033',33,1,10,12,2,'Printed',15,'2026-08-31 10:50:47','2026-08-31 10:50:47','2026-08-31 10:50:47'),(5,'IDC-2026-00040',40,1,8,12,2,'Printed',15,'2026-09-01 11:51:08','2026-09-01 11:51:08','2026-09-01 11:51:08'),(6,'IDC-2026-00001',1,1,8,1,2,'Printed',15,'2026-09-01 12:46:12','2026-09-01 12:46:12','2026-09-01 12:46:12'),(7,'IDC-2026-00015',15,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(8,'IDC-2026-00017',17,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(9,'IDC-2026-00018',18,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(10,'IDC-2026-00021',21,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(11,'IDC-2026-00024',24,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(12,'IDC-2026-00025',25,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(13,'IDC-2026-00026',26,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(14,'IDC-2026-00027',27,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(15,'IDC-2026-00028',28,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(16,'IDC-2026-00029',29,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(17,'IDC-2026-00030',30,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50'),(18,'IDC-2026-00031',31,1,1,12,2,'Printed',15,'2026-09-02 11:28:50','2026-09-02 11:28:50','2026-09-02 11:28:50');
/*!40000 ALTER TABLE `tbl_student_id_cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_previous_school`
--

DROP TABLE IF EXISTS `tbl_student_previous_school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_previous_school` (
  `prev_school_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `school_name` varchar(200) NOT NULL,
  `school_address` text DEFAULT NULL,
  `school_board` varchar(100) DEFAULT NULL,
  `previous_class` varchar(50) DEFAULT NULL,
  `previous_academic_year` varchar(50) DEFAULT NULL,
  `date_of_leaving` date DEFAULT NULL,
  `reason_for_leaving` text DEFAULT NULL,
  `tc_number` varchar(100) DEFAULT NULL,
  `tc_document_id` int(11) DEFAULT NULL,
  `previous_percentage` decimal(5,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`prev_school_id`),
  KEY `idx_psch_student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_previous_school`
--

LOCK TABLES `tbl_student_previous_school` WRITE;
/*!40000 ALTER TABLE `tbl_student_previous_school` DISABLE KEYS */;
INSERT INTO `tbl_student_previous_school` VALUES (1,24,'Greenwood Public School',NULL,'CBSE','Grade 4','2025-2026','2026-03-31','Relocation','TC/2026/0891',13,89.50,1,'2026-08-29 11:58:13','2026-08-29 15:28:13'),(2,25,'St. Xavier High School','','','','',NULL,'','TC/2026/258024',14,NULL,1,'2026-08-29 12:11:02','2026-08-29 15:41:02'),(3,26,'St. Xavier High School','','','','',NULL,'','TC/2026/284514',15,NULL,1,'2026-08-29 12:11:29','2026-08-29 15:41:29'),(4,27,'St. Xavier High School','','','','',NULL,'','TC/2026/494655',16,NULL,1,'2026-08-29 12:14:58','2026-08-29 15:44:58'),(5,28,'St. Xavier High School','','','','',NULL,'','TC/2026/540763',17,NULL,1,'2026-08-29 12:32:24','2026-08-29 16:02:24'),(6,29,'St. Xavier High School','','','','',NULL,'','TC/2026/567111',18,NULL,1,'2026-08-29 12:32:51','2026-08-29 16:02:51'),(7,30,'St. Xavier High School','','','','',NULL,'','TC/2026/411367',19,NULL,1,'2026-08-29 12:46:54','2026-08-29 16:16:54'),(8,31,'St. Xavier High School','','','','',NULL,'','TC/2026/200679',20,NULL,1,'2026-08-29 13:50:05','2026-08-29 17:20:05');
/*!40000 ALTER TABLE `tbl_student_previous_school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_promotions`
--

DROP TABLE IF EXISTS `tbl_student_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_promotions` (
  `promotion_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `from_academic_year_id` int(10) unsigned NOT NULL,
  `from_class_id` int(10) unsigned NOT NULL,
  `from_section_id` int(10) unsigned NOT NULL,
  `to_academic_year_id` int(10) unsigned NOT NULL,
  `to_class_id` int(10) unsigned NOT NULL,
  `to_section_id` int(10) unsigned NOT NULL,
  `promotion_date` date NOT NULL,
  `promotion_type` enum('Promoted','Retained','Demoted','Transferred') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Promoted',
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`promotion_id`),
  KEY `idx_promo_student` (`student_id`),
  CONSTRAINT `fk_promo_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_promotions`
--

LOCK TABLES `tbl_student_promotions` WRITE;
/*!40000 ALTER TABLE `tbl_student_promotions` DISABLE KEYS */;
INSERT INTO `tbl_student_promotions` VALUES (1,1,2,7,4,1,8,1,'2026-05-30','Promoted','Promoted with 92% marks in Grade 9 annual exams','2026-08-18 12:34:23','n'),(2,2,2,6,6,1,7,5,'2026-05-30','Promoted','Promoted with A grade in Grade 6 annual exams','2026-08-18 12:34:23','n'),(3,3,2,9,9,1,10,10,'2026-05-30','Promoted','Promoted to Grade 10 CBSE batch','2026-08-18 12:34:23','n'),(4,13,1,8,1,1,9,9,'2026-08-18','Promoted','Promoted to Grade 9 after term evaluation','2026-08-18 12:38:47','n');
/*!40000 ALTER TABLE `tbl_student_promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_results`
--

DROP TABLE IF EXISTS `tbl_student_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_results` (
  `result_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `total_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `max_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `overall_grade` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F',
  `gpa` decimal(4,2) NOT NULL DEFAULT 0.00,
  `pass_status` enum('Pass','Fail','Withheld') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pass',
  `failed_subjects_count` int(10) unsigned NOT NULL DEFAULT 0,
  `class_rank` int(10) unsigned DEFAULT NULL,
  `section_rank` int(10) unsigned DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `published_by` int(10) unsigned DEFAULT NULL,
  `teacher_remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`result_id`),
  UNIQUE KEY `uk_result_exam_student` (`exam_id`,`student_id`),
  KEY `idx_res_exam` (`exam_id`),
  KEY `idx_res_student` (`student_id`),
  KEY `idx_res_class_sec` (`class_id`,`section_id`),
  KEY `idx_res_published` (`is_published`),
  KEY `idx_res_rank` (`class_rank`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_results`
--

LOCK TABLES `tbl_student_results` WRITE;
/*!40000 ALTER TABLE `tbl_student_results` DISABLE KEYS */;
INSERT INTO `tbl_student_results` VALUES (4,1,2,1,7,5,92.00,100.00,92.00,'A+',10.00,'Pass',0,1,1,1,'2026-08-20 12:06:53',NULL,NULL,NULL,'2026-08-20 12:06:53','2026-08-21 06:30:48','n'),(5,1,3,1,10,10,74.00,100.00,74.00,'B+',8.00,'Pass',0,1,1,1,'2026-08-20 12:06:53',NULL,NULL,NULL,'2026-08-20 12:06:53','2026-08-21 06:30:48','n'),(6,1,4,1,5,6,0.00,100.00,0.00,'F',0.00,'Fail',1,NULL,NULL,1,'2026-08-20 12:06:53',NULL,NULL,NULL,'2026-08-20 12:06:53','2026-08-21 06:30:48','n');
/*!40000 ALTER TABLE `tbl_student_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_transfers`
--

DROP TABLE IF EXISTS `tbl_student_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_transfers` (
  `transfer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `tc_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfer_date` date NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_class_id` int(10) unsigned DEFAULT NULL,
  `academic_year_id` int(10) unsigned DEFAULT NULL,
  `conduct` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Good',
  `dues_cleared` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('Requested','Approved','Issued','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Issued',
  `remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`transfer_id`),
  UNIQUE KEY `uk_tc_number` (`tc_number`),
  KEY `idx_transfer_student` (`student_id`),
  CONSTRAINT `fk_transfer_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_transfers`
--

LOCK TABLES `tbl_student_transfers` WRITE;
/*!40000 ALTER TABLE `tbl_student_transfers` DISABLE KEYS */;
INSERT INTO `tbl_student_transfers` VALUES (1,5,'TC/2026/001','2026-08-01','Parent relocation to Dubai, UAE',6,1,'Exemplary',1,'Issued','All fee dues cleared. Caution deposit refunded.','2026-08-18 12:34:23','2026-08-18 12:34:23','n'),(2,13,'TC/2026/338','2026-08-18','Relocation to Bangalore',9,1,'Exemplary',1,'Issued','Cleared all lab and library books','2026-08-18 09:08:47','2026-08-18 12:38:47','n'),(3,2,'TC/2026/396','2026-08-27','Parent Relocation / Job Transfer',7,1,'Exemplary',1,'Issued','','2026-08-27 12:36:32','2026-08-27 16:06:32','n');
/*!40000 ALTER TABLE `tbl_student_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_student_transport_assignments`
--

DROP TABLE IF EXISTS `tbl_student_transport_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_student_transport_assignments` (
  `assignment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL DEFAULT 1,
  `student_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned DEFAULT NULL,
  `section_id` int(10) unsigned DEFAULT NULL,
  `route_id` int(10) unsigned NOT NULL,
  `pickup_stop_id` int(10) unsigned NOT NULL,
  `drop_stop_id` int(10) unsigned NOT NULL,
  `vehicle_id` int(10) unsigned NOT NULL,
  `transport_type` enum('Two Way','One Way','Pickup Only','Drop Only') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Two Way',
  `monthly_fee` decimal(10,2) NOT NULL DEFAULT 1500.00,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Active','Suspended','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `uk_student_trans_assign` (`academic_year_id`,`student_id`,`status`),
  KEY `idx_ta_route` (`route_id`),
  KEY `idx_ta_vehicle` (`vehicle_id`),
  KEY `idx_transport_route_status` (`route_id`,`status`,`is_deleted`),
  KEY `idx_transport_veh_status` (`vehicle_id`,`status`,`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_student_transport_assignments`
--

LOCK TABLES `tbl_student_transport_assignments` WRITE;
/*!40000 ALTER TABLE `tbl_student_transport_assignments` DISABLE KEYS */;
INSERT INTO `tbl_student_transport_assignments` VALUES (1,1,1,8,1,1,1,1,1,'Two Way',1600.00,'2026-08-20','2026-08-20','Active','2026-08-20 14:56:45','2026-08-20 14:56:45','n');
/*!40000 ALTER TABLE `tbl_student_transport_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_students`
--

DROP TABLE IF EXISTS `tbl_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_students` (
  `student_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admission_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Male',
  `date_of_birth` date NOT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Indian',
  `religion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guardian_relation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Father',
  `guardian_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guardian_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `roll_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive, 2=Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `uk_admission_number` (`admission_number`),
  KEY `idx_student_academic_year` (`academic_year_id`),
  KEY `idx_student_class` (`class_id`),
  KEY `idx_student_section` (`section_id`),
  KEY `idx_students_academic_status` (`academic_year_id`,`is_deleted`,`status`),
  CONSTRAINT `fk_student_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_student_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_student_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_students`
--

LOCK TABLES `tbl_students` WRITE;
/*!40000 ALTER TABLE `tbl_students` DISABLE KEYS */;
INSERT INTO `tbl_students` VALUES (1,'EDU2026001','Aarav','','Nair','Male','2011-06-12','B+','Indian','Hindu','Thaikkattukara, Aluva, Ernakulam, Kerala','Suresh Nair','Father','+91 98470 11223','suresh.nair@email.com',1,8,1,'01',NULL,0,'2026-08-17 10:32:48','2026-08-18 12:42:43','n'),(2,'EDU2026002','Diya','','Menon','Female','2012-02-03','O+','Indian','Hindu','Kakkanad, Ernakulam, Kerala','Ramesh Menon','Father','+91 94470 22314','ramesh.menon@email.com',1,7,5,'02',NULL,0,'2026-08-17 10:32:48','2026-08-27 12:36:32','n'),(3,'EDU2026003','Kiran','','Thomas','Male','2009-09-19','A+','Indian','Christian','Kaloor, Ernakulam, Kerala','Thomas Varghese','Father','+91 90480 33556','thomas.varghese@email.com',1,10,10,'03',NULL,1,'2026-08-17 10:32:48','2026-08-18 11:38:42','n'),(4,'EDU2026004','Ananya','','Pillai','Female','2014-11-27','AB+','Indian','Hindu','Tripunithura, Ernakulam, Kerala','Vinod Pillai','Father','+91 99460 44778','vinod.pillai@email.com',1,5,6,'04',NULL,1,'2026-08-17 10:32:48','2026-08-18 11:38:42','n'),(5,'EDU2026005','Rohan','','Iqbal','Male','2013-01-05','B-','Indian','Muslim','Edappally, Ernakulam, Kerala','Iqbal Rahman','Father','+91 98950 55990','iqbal.rahman@email.com',1,6,4,'05',NULL,0,'2026-08-17 10:32:48','2026-08-18 11:38:42','n'),(6,'EDU2026006','Sara','','Joseph','Female','2020-04-14','O+','Indian','Christian','Palarivattom, Ernakulam, Kerala','Jibin Joseph','Father','+91 97460 66112','jibin.joseph@email.com',1,1,8,'06',NULL,1,'2026-08-17 10:32:48','2026-08-18 11:38:42','n'),(7,'EDU2026007','Vishnu','','Krishnan','Male','2010-08-30','A-','Indian','Hindu','Vyttila, Ernakulam, Kerala','Krishnan Kutty','Father','+91 88480 77334','krishnan.kutty@email.com',1,9,9,'07',NULL,1,'2026-08-17 10:32:48','2026-08-18 11:38:42','n'),(8,'EDU2026008','Meera','','Suresh','Female','2016-07-22','B+','Indian','Hindu','Kadavanthra, Ernakulam, Kerala','Suresh Babu','Father','+91 97350 88556','suresh.babu@email.com',1,3,7,'08',NULL,2,'2026-08-17 10:32:48','2026-08-18 11:38:42','n'),(9,'EDU2026810','Anandhu S',NULL,'Uthaman','Male','2010-10-12','A+','Indian',NULL,'Saraswathy vilasam, Punalur, Kerala','Saraswathy','Mother','+919037326395',NULL,1,1,12,'42','student_9_20260903072616_fbf2a4.jpg',1,'2026-08-18 08:10:40','2026-09-03 07:26:16','n'),(10,'EDU2026746','Anandhu S',NULL,'Uthamaan Edited','Male','2010-10-12','A+','Indian',NULL,'Sarswathy vilasam venchempu po punalur','Uthaman','','+919037326395',NULL,1,1,8,'16',NULL,0,'2026-08-18 08:38:23','2026-08-18 12:43:14','n'),(11,'EDU2026676','Anandhu S',NULL,'Uthaman','Male','2012-06-15','A+','Indian',NULL,'Sarswathy vilasam venchempu po punalur','Uthaman',NULL,'+919037326395',NULL,1,1,8,'12',NULL,0,'2026-08-18 08:40:14','2026-08-18 12:43:44','n'),(12,'EDU2026502','Anandhu S',NULL,'Uthaman','Male','2012-06-15','A+','Indian',NULL,'Sarswathy vilasam venchempu po punalur','Uthaman',NULL,'+919037326395',NULL,1,1,8,'34',NULL,0,'2026-08-18 08:41:45','2026-08-18 12:43:48','n'),(13,'EDU2026106','Vikram',NULL,'Sethi','Male','2012-04-18','B+','Indian',NULL,'Marine Drive, Kochi, Kerala','Anil Sethi','Father','+91 98471 99887','anil.sethi@gmail.com',1,9,9,'31',NULL,0,'2026-08-18 09:08:46','2026-08-18 09:08:47','n'),(15,'EDU2026366','TEst',NULL,'123','Male','2012-06-15','A+','Indian',NULL,'asdfgfg','test ','Father','+91 998765432',NULL,1,1,12,'23',NULL,1,'2026-08-27 12:34:36','2026-08-27 16:04:36','n'),(17,'EDU2026TEST971951','TestAarav971951',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','','Father','9876543210',NULL,1,1,12,'',NULL,1,'2026-08-28 11:56:13','2026-08-28 15:26:13','n'),(18,'EDU2026TEST376592','TestAarav376592',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','','Father','9876543210',NULL,1,1,12,'',NULL,1,'2026-08-28 12:52:57','2026-08-28 16:22:57','n'),(19,'EDU2026486','TEst',NULL,'123','Male','2012-06-15','A+','Indian',NULL,'','test','Father','','',1,8,12,'23',NULL,1,'2026-08-29 06:46:41','2026-08-29 10:16:41','n'),(21,'EDU2026TEST866561','TestAarav866561',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Suresh Verma','Father','9847011223','',1,1,12,'',NULL,1,'2026-08-29 11:14:29','2026-08-29 14:44:29','n'),(22,'EDU2026123','TEst',NULL,'','Male','2012-06-15','A+','Indian',NULL,'','yukytuk','Father','','',1,9,12,'','photo_1787997168_5b3f2df62e74.jpg',1,'2026-08-29 11:52:48','2026-08-29 15:22:48','n'),(24,'TEST20264473','Aditya',NULL,'Sharma','Male','2014-04-10','O+','Indian',NULL,'12 Palm Grove, Green Avenue','Rajesh Sharma','Father','9847055443','rajesh.sharma@example.com',1,1,12,'42','photo_1787997493_414cfa193e29.png',1,'2026-08-29 11:58:13','2026-08-29 15:28:13','n'),(25,'ADM2026258024','Aarav258024',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Rajesh Verma','Father','9847011223','',1,1,12,'','photo_1787998262_2bba9c6947e3.png',1,'2026-08-29 12:11:02','2026-08-29 15:41:02','n'),(26,'ADM2026284514','Aarav284514',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Rajesh Verma','Father','9847011223','',1,1,12,'','photo_1787998289_5de9b30e53f9.png',1,'2026-08-29 12:11:29','2026-08-29 15:41:29','n'),(27,'ADM2026494655','Aarav494655',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Rajesh Verma','Father','9847011223','',1,1,12,'','photo_1787998498_fed4a9f7b16c.png',1,'2026-08-29 12:14:58','2026-08-29 15:44:58','n'),(28,'ADM2026540763','Aarav540763',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Rajesh Verma','Father','9847011223','',1,1,12,'','photo_1787999544_29feef29413f.png',1,'2026-08-29 12:32:24','2026-08-29 16:02:24','n'),(29,'ADM2026567111','Aarav567111',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Rajesh Verma','Father','9847011223','',1,1,12,'','photo_1787999571_66c754440b06.png',1,'2026-08-29 12:32:51','2026-08-29 16:02:51','n'),(30,'ADM2026411367','Aarav411367',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Rajesh Verma','Father','9847011223','',1,1,12,'','photo_1788000414_0525a15806c9.png',1,'2026-08-29 12:46:54','2026-08-29 16:16:54','n'),(31,'ADM2026200679','Aarav200679',NULL,'Verma','Male','2012-06-15','A+','Indian',NULL,'','Rajesh Verma','Father','9847011223','',1,1,12,'','photo_1788004205_8ff50c8b5a1d.png',1,'2026-08-29 13:50:05','2026-08-29 17:20:05','n'),(32,'EDU2026015','test new',NULL,'','Male','2012-06-15','A+','Indian',NULL,'','test','Father','','',1,8,12,'','photo_1788156611_a09846ee6c39.jpg',1,'2026-08-31 08:10:11','2026-08-31 11:40:11','n'),(33,'EDU2026148','Andhu',NULL,'U','Male','2012-06-15','A+','Indian',NULL,'kollam','Uthaman','Father','+91 1232121123',NULL,1,10,12,'','photo_1788166200_cd6af7efbfb3.jpg',1,'2026-08-31 10:50:00','2026-08-31 14:20:08','n'),(34,'EDU2026635','4ertgeag',NULL,'srthgbsr','Male','2012-06-15','A+','Indian',NULL,'','wq4aert5h','Father','+91 243232332','',1,10,12,'','photo_1788166921_086b845119ec.jpg',1,'2026-08-31 11:02:01','2026-08-31 14:32:01','n'),(40,'EDU2026374','testttttttttttttttttttttttttTEst',NULL,'','Male','2012-06-15','A+','Indian',NULL,'','testeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee','Father','','',1,8,12,'','photo_1788256251_4084c53c4fb7.jpg',1,'2026-09-01 11:50:51','2026-09-01 15:20:51','n'),(45,'EDU2026277','rsthgbs',NULL,'srthb','Male','2012-03-08','A+','Indian',NULL,'','wrthbw','Father','+91 4334343434','',1,8,12,'34','photo_1788340964_8941c01ddd64.jpg',1,'2026-09-02 11:22:44','2026-09-02 14:52:44','n');
/*!40000 ALTER TABLE `tbl_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_subject_allocations`
--

DROP TABLE IF EXISTS `tbl_subject_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_subject_allocations` (
  `allocation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `teacher_id` int(10) unsigned DEFAULT NULL,
  `weekly_periods_target` int(10) unsigned NOT NULL DEFAULT 5,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`allocation_id`),
  UNIQUE KEY `uk_class_sec_sub` (`academic_year_id`,`class_id`,`section_id`,`subject_id`),
  KEY `idx_alloc_teacher` (`teacher_id`),
  KEY `idx_alloc_subject` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_subject_allocations`
--

LOCK TABLES `tbl_subject_allocations` WRITE;
/*!40000 ALTER TABLE `tbl_subject_allocations` DISABLE KEYS */;
INSERT INTO `tbl_subject_allocations` VALUES (1,1,1,1,1,1,6,1,'2026-08-20 12:33:33','2026-08-20 12:33:33','n'),(2,1,1,1,2,2,6,1,'2026-08-20 12:33:33','2026-08-20 12:33:33','n'),(3,1,1,1,3,5,6,1,'2026-08-20 12:33:33','2026-08-20 12:33:33','n'),(4,1,1,1,4,8,6,1,'2026-08-20 12:33:33','2026-08-20 12:33:33','n'),(5,1,1,1,5,9,6,1,'2026-08-20 12:33:33','2026-08-20 12:33:33','n'),(7,1,8,1,6,1,6,1,'2026-08-21 10:07:14','2026-08-21 13:37:14','n'),(8,1,8,1,7,2,6,1,'2026-08-21 10:07:31','2026-08-21 13:37:31','n');
/*!40000 ALTER TABLE `tbl_subject_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_subject_teachers`
--

DROP TABLE IF EXISTS `tbl_subject_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_subject_teachers` (
  `subject_teacher_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `staff_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`subject_teacher_id`),
  UNIQUE KEY `uk_st_assignment` (`academic_year_id`,`class_id`,`section_id`,`subject_id`,`staff_id`),
  KEY `idx_st_subject` (`subject_id`),
  KEY `idx_st_staff` (`staff_id`),
  KEY `fk_st_class` (`class_id`),
  KEY `fk_st_section` (`section_id`),
  CONSTRAINT `fk_st_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_subject` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_st_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_subject_teachers`
--

LOCK TABLES `tbl_subject_teachers` WRITE;
/*!40000 ALTER TABLE `tbl_subject_teachers` DISABLE KEYS */;
INSERT INTO `tbl_subject_teachers` VALUES (1,1,8,1,1,1,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(2,1,8,1,2,2,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(3,1,8,1,3,5,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(4,1,8,1,4,12,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n'),(5,1,8,1,5,8,1,'2026-08-18 13:24:49','2026-08-18 13:24:49','n');
/*!40000 ALTER TABLE `tbl_subject_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_subjects`
--

DROP TABLE IF EXISTS `tbl_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_subjects` (
  `subject_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) unsigned DEFAULT NULL,
  `subject_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` enum('Core','Elective','Language','Practical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Core',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teacher_id` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`subject_id`),
  KEY `idx_subject_class` (`class_id`),
  KEY `idx_subject_teacher` (`teacher_id`),
  CONSTRAINT `fk_subject_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_subject_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_subjects`
--

LOCK TABLES `tbl_subjects` WRITE;
/*!40000 ALTER TABLE `tbl_subjects` DISABLE KEYS */;
INSERT INTO `tbl_subjects` VALUES (1,8,'Mathematics','MATH10','Core',NULL,1,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(2,8,'Physics','PHY10','Core',NULL,2,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(3,8,'Malayalam','MAL10','Language',NULL,5,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(4,8,'Physical Education','PE10','Practical',NULL,8,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(5,8,'Computer Science','CS10','Elective',NULL,4,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(6,7,'Mathematics','MATH09','Core',NULL,1,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(7,7,'Science','SCI09','Core',NULL,2,1,'2026-08-17 10:32:48','2026-08-17 10:32:48','n'),(8,7,'English','ENG09','Language',NULL,5,0,'2026-08-17 10:32:48','2026-08-21 13:18:01','n');
/*!40000 ALTER TABLE `tbl_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_submission_history`
--

DROP TABLE IF EXISTS `tbl_submission_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_submission_history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` int(10) unsigned NOT NULL,
  `assignment_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `submitted_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_files` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` datetime NOT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teacher_remarks` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Submitted',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`history_id`),
  KEY `idx_hist_submission` (`submission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_submission_history`
--

LOCK TABLES `tbl_submission_history` WRITE;
/*!40000 ALTER TABLE `tbl_submission_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_submission_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_teacher_substitutions`
--

DROP TABLE IF EXISTS `tbl_teacher_substitutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_teacher_substitutions` (
  `substitution_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timetable_id` int(10) unsigned NOT NULL,
  `substitution_date` date NOT NULL,
  `original_teacher_id` int(10) unsigned NOT NULL,
  `substitute_teacher_id` int(10) unsigned NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Scheduled',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`substitution_id`),
  KEY `idx_sub_tt` (`timetable_id`),
  KEY `idx_sub_date` (`substitution_date`),
  KEY `idx_sub_orig` (`original_teacher_id`),
  KEY `idx_sub_subst` (`substitute_teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_teacher_substitutions`
--

LOCK TABLES `tbl_teacher_substitutions` WRITE;
/*!40000 ALTER TABLE `tbl_teacher_substitutions` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_teacher_substitutions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_teacher_workload`
--

DROP TABLE IF EXISTS `tbl_teacher_workload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_teacher_workload` (
  `workload_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `academic_year_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned DEFAULT NULL,
  `periods` int(10) unsigned NOT NULL DEFAULT 5,
  `working_days` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Mon,Tue,Wed,Thu,Fri',
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`workload_id`),
  KEY `idx_workload_staff` (`staff_id`),
  KEY `idx_workload_class` (`class_id`),
  KEY `idx_workload_subject` (`subject_id`),
  CONSTRAINT `fk_workload_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_teacher_workload`
--

LOCK TABLES `tbl_teacher_workload` WRITE;
/*!40000 ALTER TABLE `tbl_teacher_workload` DISABLE KEYS */;
INSERT INTO `tbl_teacher_workload` VALUES (1,1,1,1,8,1,5,'Mon,Tue,Wed,Thu,Fri','Grade 10 Mathematics Core Class',1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(2,1,1,6,7,4,4,'Mon,Tue,Wed,Thu','Grade 9 Mathematics Class',1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(3,2,1,2,8,1,5,'Mon,Tue,Wed,Thu,Fri','Grade 10 Physics & Science',1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(4,2,1,7,7,5,4,'Mon,Tue,Thu,Fri','Grade 9 Science Theory & Lab',1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(5,8,1,5,8,1,4,'Tue,Wed,Thu,Fri','Computer Science & Python Coding',1,'2026-08-18 12:56:56','2026-08-18 12:56:56','n'),(6,13,1,1,8,1,6,'Mon,Tue,Wed,Thu,Fri','Class 10 Advanced Math',1,'2026-08-18 09:31:21','2026-08-18 13:01:21','n');
/*!40000 ALTER TABLE `tbl_teacher_workload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_timetable`
--

DROP TABLE IF EXISTS `tbl_timetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_timetable` (
  `timetable_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `teacher_id` int(10) unsigned NOT NULL,
  `room_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`timetable_id`),
  UNIQUE KEY `uk_class_day_period` (`academic_year_id`,`class_id`,`section_id`,`day`,`period_id`),
  KEY `idx_tt_teacher` (`teacher_id`),
  KEY `fk_tt_class` (`class_id`),
  KEY `fk_tt_section` (`section_id`),
  KEY `fk_tt_period` (`period_id`),
  KEY `fk_tt_subject` (`subject_id`),
  CONSTRAINT `fk_tt_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tt_period` FOREIGN KEY (`period_id`) REFERENCES `tbl_periods` (`period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tt_section` FOREIGN KEY (`section_id`) REFERENCES `tbl_sections` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tt_subject` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tt_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tt_year` FOREIGN KEY (`academic_year_id`) REFERENCES `tbl_academic_years` (`academic_year_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_timetable`
--

LOCK TABLES `tbl_timetable` WRITE;
/*!40000 ALTER TABLE `tbl_timetable` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_timetable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_timetable_publish`
--

DROP TABLE IF EXISTS `tbl_timetable_publish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_timetable_publish` (
  `publish_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `academic_year_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `status` enum('Draft','Published','Locked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `published_at` datetime DEFAULT NULL,
  `published_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`publish_id`),
  UNIQUE KEY `uk_tt_publish_class` (`academic_year_id`,`class_id`,`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_timetable_publish`
--

LOCK TABLES `tbl_timetable_publish` WRITE;
/*!40000 ALTER TABLE `tbl_timetable_publish` DISABLE KEYS */;
INSERT INTO `tbl_timetable_publish` VALUES (1,1,1,1,'Draft','2026-08-20 12:38:18',1,'2026-08-20 12:38:18','2026-08-20 12:38:18','n');
/*!40000 ALTER TABLE `tbl_timetable_publish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_timetable_settings`
--

DROP TABLE IF EXISTS `tbl_timetable_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_timetable_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `working_days` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
  `max_periods_per_day` int(10) unsigned NOT NULL DEFAULT 8,
  `max_consecutive_periods` int(10) unsigned NOT NULL DEFAULT 3,
  `allow_teacher_overlap` tinyint(1) NOT NULL DEFAULT 0,
  `auto_publish` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_timetable_settings`
--

LOCK TABLES `tbl_timetable_settings` WRITE;
/*!40000 ALTER TABLE `tbl_timetable_settings` DISABLE KEYS */;
INSERT INTO `tbl_timetable_settings` VALUES (1,'Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',8,3,0,0,'2026-08-20 12:33:33','2026-08-20 12:33:33','n');
/*!40000 ALTER TABLE `tbl_timetable_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transport_assignment_history`
--

DROP TABLE IF EXISTS `tbl_transport_assignment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_transport_assignment_history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_route_id` int(10) unsigned DEFAULT NULL,
  `new_route_id` int(10) unsigned DEFAULT NULL,
  `previous_stop_id` int(10) unsigned DEFAULT NULL,
  `new_stop_id` int(10) unsigned DEFAULT NULL,
  `previous_vehicle_id` int(10) unsigned DEFAULT NULL,
  `new_vehicle_id` int(10) unsigned DEFAULT NULL,
  `effective_date` date NOT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`history_id`),
  KEY `idx_tah_student` (`student_id`),
  KEY `idx_tah_assign` (`assignment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transport_assignment_history`
--

LOCK TABLES `tbl_transport_assignment_history` WRITE;
/*!40000 ALTER TABLE `tbl_transport_assignment_history` DISABLE KEYS */;
INSERT INTO `tbl_transport_assignment_history` VALUES (1,1,1,'Assigned',NULL,1,NULL,1,NULL,1,'2026-08-20',1,'Assigned via test suite','2026-08-20 14:56:45','n');
/*!40000 ALTER TABLE `tbl_transport_assignment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transport_audit_logs`
--

DROP TABLE IF EXISTS `tbl_transport_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_transport_audit_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`log_id`),
  KEY `idx_trans_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transport_audit_logs`
--

LOCK TABLES `tbl_transport_audit_logs` WRITE;
/*!40000 ALTER TABLE `tbl_transport_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_transport_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transport_documents`
--

DROP TABLE IF EXISTS `tbl_transport_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_transport_documents` (
  `document_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` enum('Vehicle','Driver') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Vehicle',
  `entity_id` int(10) unsigned NOT NULL,
  `document_type` enum('Registration','Insurance','Fitness Certificate','Pollution Certificate','Permit','Driving License','ID Proof','Medical Certificate','Police Verification','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Registration',
  `document_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Active','Expiring Soon','Expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`document_id`),
  KEY `idx_td_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transport_documents`
--

LOCK TABLES `tbl_transport_documents` WRITE;
/*!40000 ALTER TABLE `tbl_transport_documents` DISABLE KEYS */;
INSERT INTO `tbl_transport_documents` VALUES (1,'Vehicle',1,'Fitness Certificate','FIT-KL02-8871',NULL,'2027-04-01',NULL,'Active','2026-08-20 14:56:45','2026-08-20 14:56:45','n'),(2,'Driver',1,'Driving License','KL-02-2015-0012345',NULL,'2028-05-15',NULL,'Active','2026-08-20 14:56:45','2026-08-20 14:56:45','n');
/*!40000 ALTER TABLE `tbl_transport_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transport_drivers`
--

DROP TABLE IF EXISTS `tbl_transport_drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_transport_drivers` (
  `driver_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_id` int(10) unsigned DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Heavy Commercial Vehicle (HCV)',
  `license_issue_date` date DEFAULT NULL,
  `license_expiry_date` date NOT NULL,
  `experience_years` int(10) unsigned NOT NULL DEFAULT 5,
  `assigned_vehicle_id` int(10) unsigned DEFAULT NULL,
  `status` enum('Active','Inactive','On Leave','Suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`driver_id`),
  UNIQUE KEY `uk_driver_license` (`license_number`),
  KEY `idx_driver_staff` (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transport_drivers`
--

LOCK TABLES `tbl_transport_drivers` WRITE;
/*!40000 ALTER TABLE `tbl_transport_drivers` DISABLE KEYS */;
INSERT INTO `tbl_transport_drivers` VALUES (1,'Ramesh Kumar',NULL,NULL,NULL,'9847123456',NULL,NULL,'KL-02-2015-0012345','Heavy Commercial Vehicle (HCV)',NULL,'2028-05-15',12,1,'Active','2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(2,'Suresh Babu',NULL,NULL,NULL,'9847654321',NULL,NULL,'KL-02-2017-0054321','Heavy Commercial Vehicle (HCV)',NULL,'2027-11-20',8,2,'Active','2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(3,'Mohan Das',NULL,NULL,NULL,'9847987654',NULL,NULL,'KL-02-2019-0098765','Medium Passenger Vehicle',NULL,'2026-10-10',6,3,'Active','2026-08-20 14:31:14','2026-08-20 14:31:14','n');
/*!40000 ALTER TABLE `tbl_transport_drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transport_routes`
--

DROP TABLE IF EXISTS `tbl_transport_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_transport_routes` (
  `route_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_point` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_point` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_distance_km` decimal(5,2) NOT NULL DEFAULT 15.00,
  `estimated_travel_time_min` int(10) unsigned NOT NULL DEFAULT 45,
  `assigned_vehicle_id` int(10) unsigned DEFAULT NULL,
  `assigned_driver_id` int(10) unsigned DEFAULT NULL,
  `status` enum('Active','Inactive','Suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`route_id`),
  UNIQUE KEY `uk_route_code` (`route_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transport_routes`
--

LOCK TABLES `tbl_transport_routes` WRITE;
/*!40000 ALTER TABLE `tbl_transport_routes` DISABLE KEYS */;
INSERT INTO `tbl_transport_routes` VALUES (1,'Route 1 - Punalur Express','RT-01','Primary northern highway route covering Punalur and Kalluvathukkal','Punalur Town','School Campus',22.50,45,1,1,'Active','2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(2,'Route 2 - Anchal Bypass','RT-02','Southern township route through Anchal market and bypass junction','Anchal Main Gate','School Campus',18.00,35,2,2,'Active','2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(3,'Route 3 - Valakom Shuttle','RT-03','Suburban feeder route covering Valakom and surrounding suburbs','Valakom Junction','School Campus',12.00,25,3,3,'Active','2026-08-20 14:53:30','2026-08-20 14:53:30','n');
/*!40000 ALTER TABLE `tbl_transport_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transport_settings`
--

DROP TABLE IF EXISTS `tbl_transport_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_transport_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enable_transport` tinyint(1) NOT NULL DEFAULT 1,
  `enforce_capacity` tinyint(1) NOT NULL DEFAULT 1,
  `allow_capacity_override` tinyint(1) NOT NULL DEFAULT 0,
  `default_monthly_fee` decimal(10,2) NOT NULL DEFAULT 1500.00,
  `fee_frequency` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Monthly',
  `maintenance_reminder_days` int(10) unsigned NOT NULL DEFAULT 15,
  `document_expiry_reminder_days` int(10) unsigned NOT NULL DEFAULT 30,
  `driver_license_reminder_days` int(10) unsigned NOT NULL DEFAULT 30,
  `allow_one_way` tinyint(1) NOT NULL DEFAULT 1,
  `allow_pickup_only` tinyint(1) NOT NULL DEFAULT 1,
  `allow_drop_only` tinyint(1) NOT NULL DEFAULT 1,
  `allow_bulk_assignment` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transport_settings`
--

LOCK TABLES `tbl_transport_settings` WRITE;
/*!40000 ALTER TABLE `tbl_transport_settings` DISABLE KEYS */;
INSERT INTO `tbl_transport_settings` VALUES (1,1,1,0,1500.00,'Monthly',15,30,30,1,1,1,1,'2026-08-20 14:31:14','2026-08-20 14:31:14','n');
/*!40000 ALTER TABLE `tbl_transport_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_user_login_activity`
--

DROP TABLE IF EXISTS `tbl_user_login_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user_login_activity` (
  `activity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Successful','Failed','Locked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Successful',
  `failure_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`activity_id`),
  KEY `idx_la_user` (`user_id`),
  KEY `idx_la_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=402 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_user_login_activity`
--

LOCK TABLES `tbl_user_login_activity` WRITE;
/*!40000 ALTER TABLE `tbl_user_login_activity` DISABLE KEYS */;
INSERT INTO `tbl_user_login_activity` VALUES (4,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','Successful',NULL,'2026-08-21 11:59:35','n'),(5,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 05:45:01','n'),(6,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 05:45:08','n'),(7,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 05:45:39','n'),(8,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 05:45:50','n'),(9,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Locked','Invalid Password','2026-08-27 05:49:32','n'),(10,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 06:40:49','n'),(11,15,'Admin','::1','curl/8.21.0','Successful',NULL,'2026-08-27 06:53:14','n'),(12,15,'Admin','::1','curl/8.21.0','Successful',NULL,'2026-08-27 06:53:24','n'),(13,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:53:50','n'),(14,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:53:50','n'),(15,21,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 06:53:50','n'),(16,21,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 06:53:50','n'),(17,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 06:53:51','n'),(18,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 06:54:05','n'),(19,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 06:54:12','n'),(20,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:54:18','n'),(21,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:54:45','n'),(22,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:54:45','n'),(23,24,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 06:54:45','n'),(24,24,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 06:54:46','n'),(25,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 06:54:46','n'),(26,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:56:28','n'),(27,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:56:28','n'),(28,27,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 06:56:29','n'),(29,27,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 06:56:29','n'),(30,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 06:56:30','n'),(31,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:56:31','n'),(32,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 06:56:31','n'),(33,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 06:58:22','n'),(34,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 06:58:28','n'),(35,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-27 06:58:32','n'),(36,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-27 06:59:20','n'),(37,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:21:24','n'),(38,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:21:25','n'),(39,30,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 07:21:25','n'),(40,30,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 07:21:25','n'),(41,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 07:21:26','n'),(42,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:21:27','n'),(43,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:21:27','n'),(44,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:28:19','n'),(45,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:28:20','n'),(46,33,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 07:28:20','n'),(47,33,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 07:28:20','n'),(48,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 07:28:21','n'),(49,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:28:22','n'),(50,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 07:28:22','n'),(51,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:12:02','n'),(52,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:12:03','n'),(53,36,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:12:03','n'),(54,36,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:12:03','n'),(55,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 09:12:04','n'),(56,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:12:04','n'),(57,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:12:05','n'),(58,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:16:12','n'),(59,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:16:13','n'),(60,39,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:16:13','n'),(61,39,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:16:13','n'),(62,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 09:16:14','n'),(63,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:16:15','n'),(64,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:16:15','n'),(65,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:21:49','n'),(66,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:21:49','n'),(67,42,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:21:49','n'),(68,42,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:21:50','n'),(69,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 09:21:50','n'),(70,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:21:51','n'),(71,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:21:51','n'),(72,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:28:34','n'),(73,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:28:34','n'),(74,45,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:28:35','n'),(75,45,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 09:28:35','n'),(76,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 09:28:35','n'),(77,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:28:36','n'),(78,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 09:28:36','n'),(79,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 11:27:43','n'),(80,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 11:27:43','n'),(81,48,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 11:27:43','n'),(82,48,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 11:27:44','n'),(83,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 11:27:44','n'),(84,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 11:27:45','n'),(85,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 11:27:45','n'),(86,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-27 11:48:42','n'),(87,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 12:16:03','n'),(88,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:21:50','n'),(89,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:22:02','n'),(90,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:22:03','n'),(91,51,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 12:22:03','n'),(92,51,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 12:22:04','n'),(93,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 12:22:04','n'),(94,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:22:06','n'),(95,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:22:06','n'),(96,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:30:21','n'),(97,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:30:39','n'),(98,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:30:40','n'),(99,54,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 12:30:40','n'),(100,54,'teacher_test','::1','Browser','Successful',NULL,'2026-08-27 12:30:41','n'),(101,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-27 12:30:41','n'),(102,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:30:43','n'),(103,15,'Admin','::1','Browser','Successful',NULL,'2026-08-27 12:30:44','n'),(104,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-28 06:01:13','n'),(105,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-28 06:27:02','n'),(106,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 06:27:51','n'),(107,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 06:28:04','n'),(108,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 06:28:29','n'),(109,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 06:28:44','n'),(110,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 06:29:06','n'),(111,57,'teststudent','::1','Browser','Successful',NULL,'2026-08-28 06:29:07','n'),(112,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 06:43:32','n'),(113,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 06:43:33','n'),(114,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Failed','Invalid Password','2026-08-28 09:01:17','n'),(115,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Failed','Invalid Password','2026-08-28 09:02:29','n'),(116,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Failed','Invalid Password','2026-08-28 09:02:34','n'),(117,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-28 09:45:02','n'),(118,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Locked','Invalid Password','2026-08-28 09:45:08','n'),(119,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Successful',NULL,'2026-08-28 10:18:24','n'),(120,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-28 10:18:51','n'),(121,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Successful',NULL,'2026-08-28 10:34:52','n'),(122,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Successful',NULL,'2026-08-28 10:37:05','n'),(123,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Successful',NULL,'2026-08-28 10:38:31','n'),(124,15,'Admin','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168','Successful',NULL,'2026-08-28 10:41:50','n'),(125,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-28 10:53:47','n'),(126,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 10:55:26','n'),(127,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-28 10:56:20','n'),(128,58,'test.user.1787908027','127.0.0.1','Mozilla/5.0 PHP Test Engine','Successful',NULL,'2026-08-28 14:37:07','n'),(129,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 11:08:19','n'),(130,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-28 11:09:15','n'),(131,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-28 11:42:18','n'),(132,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-28 11:42:25','n'),(133,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:47:31','n'),(134,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-28 11:47:32','n'),(135,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:47:34','n'),(136,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:47:36','n'),(137,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:47:43','n'),(138,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:47:51','n'),(139,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:47:59','n'),(140,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:48:13','n'),(141,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:48:15','n'),(142,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:48:21','n'),(143,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:12','n'),(144,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-28 11:49:13','n'),(145,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:16','n'),(146,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:18','n'),(147,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:26','n'),(148,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:35','n'),(149,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:43','n'),(150,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:56','n'),(151,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:49:58','n'),(152,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:50:03','n'),(153,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:50:37','n'),(154,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-28 11:50:38','n'),(155,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:50:41','n'),(156,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:50:43','n'),(157,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:50:51','n'),(158,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:50:59','n'),(159,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:51:07','n'),(160,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:51:21','n'),(161,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:51:23','n'),(162,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:51:28','n'),(163,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 11:51:47','n'),(164,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:52:29','n'),(165,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-28 11:52:30','n'),(166,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:52:33','n'),(167,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:52:34','n'),(168,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:52:43','n'),(169,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:52:51','n'),(170,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:53:00','n'),(171,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:53:13','n'),(172,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:53:16','n'),(173,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:53:21','n'),(174,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:53:22','n'),(175,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:53:31','n'),(176,15,'Admin','::1','Browser','Successful',NULL,'2026-08-28 11:54:11','n'),(177,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:54:34','n'),(178,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-28 11:54:35','n'),(179,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:54:37','n'),(180,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:54:39','n'),(181,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:54:47','n'),(182,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:54:55','n'),(183,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:55:04','n'),(184,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:55:17','n'),(185,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:55:19','n'),(186,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:55:24','n'),(187,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:04','n'),(188,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-28 11:56:05','n'),(189,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:09','n'),(190,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:11','n'),(191,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:14','n'),(192,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:17','n'),(193,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:20','n'),(194,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:23','n'),(195,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:25','n'),(196,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 11:56:30','n'),(197,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-28 12:40:54','n'),(198,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:52:50','n'),(199,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-28 12:52:52','n'),(200,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:52:54','n'),(201,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:52:55','n'),(202,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:52:59','n'),(203,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:53:01','n'),(204,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:53:04','n'),(205,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:53:07','n'),(206,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:53:10','n'),(207,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-28 12:53:15','n'),(208,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-29 05:50:32','n'),(209,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-29 07:39:25','n'),(210,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-29 09:21:43','n'),(211,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:03','n'),(212,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 09:30:04','n'),(213,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:06','n'),(214,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:18','n'),(215,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 09:30:19','n'),(216,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:20','n'),(217,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:22','n'),(218,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:29','n'),(219,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:37','n'),(220,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:39','n'),(221,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:42','n'),(222,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:44','n'),(223,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:30:47','n'),(224,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:31:23','n'),(225,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 09:31:24','n'),(226,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:31:26','n'),(227,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:31:27','n'),(228,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:31:58','n'),(229,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:32:00','n'),(230,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:32:03','n'),(231,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:32:05','n'),(232,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:32:07','n'),(233,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:32:10','n'),(234,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:32:55','n'),(235,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:33:26','n'),(236,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:33:52','n'),(237,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 09:33:52','n'),(238,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:33:54','n'),(239,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:33:55','n'),(240,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:34:26','n'),(241,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:34:28','n'),(242,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:34:31','n'),(243,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:34:33','n'),(244,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:34:35','n'),(245,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 09:34:38','n'),(246,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:09:41','n'),(247,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:10:13','n'),(248,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:11:25','n'),(249,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 11:11:27','n'),(250,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:11:30','n'),(251,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:11:32','n'),(252,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:12:04','n'),(253,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:12:07','n'),(254,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:12:11','n'),(255,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:12:14','n'),(256,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:12:17','n'),(257,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:12:22','n'),(258,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:18','n'),(259,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 11:14:20','n'),(260,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:23','n'),(261,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:25','n'),(262,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:31','n'),(263,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:34','n'),(264,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:37','n'),(265,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:40','n'),(266,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:43','n'),(267,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 11:14:48','n'),(268,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-29 11:49:19','n'),(269,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-29 11:50:23','n'),(270,15,'Admin','127.0.0.1','Browser','Failed','Invalid Password','2026-08-29 11:54:34','n'),(271,15,'Admin','127.0.0.1','Browser','Failed','Invalid Password','2026-08-29 11:55:18','n'),(272,15,'Admin','127.0.0.1','Browser','Failed','Invalid Password','2026-08-29 11:55:36','n'),(273,15,'Admin','127.0.0.1','Browser','Successful',NULL,'2026-08-29 11:57:14','n'),(274,15,'Admin','127.0.0.1','Browser','Successful',NULL,'2026-08-29 11:58:12','n'),(275,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 11:58:38','n'),(276,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 11:59:09','n'),(277,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 11:59:39','n'),(278,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 12:00:17','n'),(279,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Locked','Invalid Password','2026-08-29 12:00:49','n'),(280,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:07:26','n'),(281,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:08:15','n'),(282,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:08:46','n'),(283,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:08:48','n'),(284,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:09:24','n'),(285,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:09:56','n'),(286,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:09:59','n'),(287,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:10:18','n'),(288,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:10:29','n'),(289,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:10:32','n'),(290,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:10:57','n'),(291,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:03','n'),(292,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:06','n'),(293,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:18','n'),(294,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 12:11:19','n'),(295,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:22','n'),(296,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:23','n'),(297,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:30','n'),(298,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:33','n'),(299,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:35','n'),(300,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:38','n'),(301,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:41','n'),(302,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:44','n'),(303,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:11:48','n'),(304,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:14:48','n'),(305,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 12:14:49','n'),(306,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:14:52','n'),(307,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:14:53','n'),(308,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:14:59','n'),(309,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:15:02','n'),(310,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:15:04','n'),(311,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:15:07','n'),(312,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:15:10','n'),(313,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:15:12','n'),(314,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:15:16','n'),(315,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:30:26','n'),(316,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 12:30:27','n'),(317,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:30:29','n'),(318,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:30:30','n'),(319,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:01','n'),(320,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:31','n'),(321,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:33','n'),(322,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:36','n'),(323,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:39','n'),(324,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:41','n'),(325,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:43','n'),(326,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:31:44','n'),(327,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:14','n'),(328,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:16','n'),(329,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:19','n'),(330,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:25','n'),(331,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:27','n'),(332,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:41','n'),(333,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 12:32:42','n'),(334,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:44','n'),(335,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:46','n'),(336,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:52','n'),(337,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:55','n'),(338,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:32:57','n'),(339,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:33:00','n'),(340,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:33:03','n'),(341,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:33:05','n'),(342,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:33:08','n'),(343,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:46:46','n'),(344,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 12:46:47','n'),(345,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:46:49','n'),(346,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:46:50','n'),(347,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:46:55','n'),(348,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:46:57','n'),(349,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:46:59','n'),(350,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:47:02','n'),(351,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:47:04','n'),(352,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:47:06','n'),(353,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 12:47:09','n'),(354,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:49:53','n'),(355,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-08-29 13:49:54','n'),(356,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:49:57','n'),(357,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:49:59','n'),(358,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:50:07','n'),(359,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:50:10','n'),(360,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:50:12','n'),(361,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:50:16','n'),(362,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:50:20','n'),(363,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:50:22','n'),(364,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-08-29 13:50:28','n'),(365,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-31 05:43:58','n'),(366,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Failed','Invalid Password','2026-08-31 05:44:07','n'),(367,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-08-31 05:44:12','n'),(368,15,'Admin','::1','Browser','Failed','Invalid Password','2026-08-31 11:28:39','n'),(369,15,'Admin','::1','Browser','Successful',NULL,'2026-08-31 11:29:25','n'),(370,15,'Admin','::1','Browser','Successful',NULL,'2026-08-31 11:30:23','n'),(371,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-09-01 06:43:54','n'),(372,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-09-01 09:20:01','n'),(373,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:13:26','n'),(374,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:13:40','n'),(375,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Failed','Invalid Password','2026-09-01 11:13:42','n'),(376,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:13:46','n'),(377,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:13:48','n'),(378,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:13:54','n'),(379,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:45:17','n'),(380,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:45:27','n'),(381,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:47:27','n'),(382,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:47:37','n'),(383,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:48:30','n'),(384,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:49:01','n'),(385,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:51:27','n'),(386,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:51:30','n'),(387,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 11:51:41','n'),(388,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 12:44:51','n'),(389,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 12:45:27','n'),(390,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 12:45:58','n'),(391,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 12:46:02','n'),(392,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 12:46:05','n'),(393,15,'Admin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.7922.34 Safari/537.36','Successful',NULL,'2026-09-01 12:46:07','n'),(394,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-09-02 07:30:47','n'),(395,15,'Admin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Successful',NULL,'2026-09-03 05:33:59','n'),(396,15,'Admin','::1','Browser','Successful',NULL,'2026-09-03 06:34:15','n'),(397,15,'Admin','::1','Browser','Successful',NULL,'2026-09-03 06:34:55','n'),(398,15,'Admin','::1','Browser','Successful',NULL,'2026-09-03 06:55:48','n'),(399,15,'Admin','::1','Browser','Successful',NULL,'2026-09-03 07:29:57','n'),(400,15,'Admin','::1','Browser','Successful',NULL,'2026-09-03 07:32:38','n'),(401,15,'Admin','::1','Browser','Successful',NULL,'2026-09-03 07:40:19','n');
/*!40000 ALTER TABLE `tbl_user_login_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_user_permissions`
--

DROP TABLE IF EXISTS `tbl_user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `override_type` enum('Grant','Revoke') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Grant',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_perm` (`user_id`,`permission_id`),
  KEY `idx_up_user` (`user_id`),
  KEY `idx_up_perm` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_user_permissions`
--

LOCK TABLES `tbl_user_permissions` WRITE;
/*!40000 ALTER TABLE `tbl_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_user_security_settings`
--

DROP TABLE IF EXISTS `tbl_user_security_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user_security_settings` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `max_failed_attempts` int(10) unsigned NOT NULL DEFAULT 5,
  `lockout_duration_minutes` int(10) unsigned NOT NULL DEFAULT 30,
  `session_timeout_minutes` int(10) unsigned NOT NULL DEFAULT 120,
  `password_min_length` int(10) unsigned NOT NULL DEFAULT 8,
  `require_special_chars` tinyint(1) NOT NULL DEFAULT 1,
  `require_numbers` tinyint(1) NOT NULL DEFAULT 1,
  `password_expiry_days` int(10) unsigned NOT NULL DEFAULT 90,
  `allow_concurrent_sessions` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_user_security_settings`
--

LOCK TABLES `tbl_user_security_settings` WRITE;
/*!40000 ALTER TABLE `tbl_user_security_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_user_security_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin',
  `staff_id` int(10) unsigned DEFAULT NULL,
  `student_id` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_login_attempts` int(10) unsigned NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `status` enum('Active','Inactive','Suspended','Locked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`user_id`),
  KEY `idx_user_role` (`role_id`),
  KEY `idx_user_staff` (`staff_id`),
  KEY `idx_user_email` (`email`),
  KEY `idx_user_username` (`username`),
  KEY `idx_users_status_deleted` (`status`,`is_deleted`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `tbl_roles` (`role_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_user_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` VALUES (15,1,'Admin','Admin',NULL,NULL,NULL,'Admin','admin@gmail.com','+919037326395',NULL,'$2y$10$Knwfc5tE8/xLUd79GCFWfOYZnkBVrYye0Vc4j31tEQsz6.MCXlLw.',0,NULL,'2026-09-03 07:40:19','Active','2026-08-21 15:27:56','2026-09-03 11:10:19','n'),(57,8,'teststudent','Student',NULL,NULL,NULL,'Test Student','teststudent@school.com',NULL,NULL,'$2y$10$iiQDVEHZXCv0PgPw1IJOnuVf9jxiJXb8Ex8BhVApAw9UnI7ZTTuuW',0,NULL,'2026-08-28 06:29:07','Active','2026-08-28 09:59:07','2026-08-28 09:59:07','n');
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_vehicle_maintenance`
--

DROP TABLE IF EXISTS `tbl_vehicle_maintenance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_vehicle_maintenance` (
  `maintenance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(10) unsigned NOT NULL,
  `maintenance_type` enum('Routine Service','Engine','Tyres','Brake','Electrical','Insurance','Fitness','Cleaning','Repair','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Routine Service',
  `service_date` date NOT NULL,
  `next_service_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_provider` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `invoice_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Completed','Scheduled','Overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Completed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`maintenance_id`),
  KEY `idx_vm_vehicle` (`vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_vehicle_maintenance`
--

LOCK TABLES `tbl_vehicle_maintenance` WRITE;
/*!40000 ALTER TABLE `tbl_vehicle_maintenance` DISABLE KEYS */;
INSERT INTO `tbl_vehicle_maintenance` VALUES (1,1,'Routine Service','2026-07-10','2026-10-10','Periodic 10,000 km general servicing, oil filter replacement, coolant top-up.','Tata Authorized Service Centre',8500.00,'INV-TT-9982',NULL,NULL,'Completed','2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(2,2,'Tyres','2026-06-25','2026-12-25','Replaced two front radial tyres and wheel alignment.','MRF Tyres & Service',16000.00,'INV-MRF-4412',NULL,NULL,'Completed','2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(3,3,'Routine Service','2026-08-01','2026-08-25','Quarterly brake pad inspection and clutch fluid bleeding.','AutoCare Express',3500.00,'INV-AC-1021',NULL,NULL,'Scheduled','2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(4,1,'Routine Service','2026-07-10','2026-10-10','Periodic 10,000 km general servicing, oil filter replacement, coolant top-up.','Tata Authorized Service Centre',8500.00,'INV-TT-9982',NULL,NULL,'Completed','2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(5,2,'Tyres','2026-06-25','2026-12-25','Replaced two front radial tyres and wheel alignment.','MRF Tyres & Service',16000.00,'INV-MRF-4412',NULL,NULL,'Completed','2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(6,3,'Routine Service','2026-08-01','2026-08-25','Quarterly brake pad inspection and clutch fluid bleeding.','AutoCare Express',3500.00,'INV-AC-1021',NULL,NULL,'Scheduled','2026-08-20 14:53:30','2026-08-20 14:53:30','n'),(7,1,'Repair','2026-08-15','2026-11-15','Wiper motor replacement and headlight beam focus.','Tata Service',2500.00,'INV-TS-112',NULL,NULL,'Completed','2026-08-20 14:56:45','2026-08-20 14:56:45','n');
/*!40000 ALTER TABLE `tbl_vehicle_maintenance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_vehicles`
--

DROP TABLE IF EXISTS `tbl_vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_vehicles` (
  `vehicle_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` enum('School Bus','Van','Mini Bus','Other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'School Bus',
  `manufacturer` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturing_year` year(4) DEFAULT NULL,
  `seating_capacity` int(10) unsigned NOT NULL DEFAULT 40,
  `vehicle_color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Yellow',
  `registration_date` date DEFAULT NULL,
  `registration_expiry` date DEFAULT NULL,
  `insurance_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `fitness_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fitness_expiry` date DEFAULT NULL,
  `pollution_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pollution_expiry` date DEFAULT NULL,
  `permit_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permit_expiry` date DEFAULT NULL,
  `assigned_driver_id` int(10) unsigned DEFAULT NULL,
  `assigned_route_id` int(10) unsigned DEFAULT NULL,
  `status` enum('Active','Inactive','Maintenance','Retired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`vehicle_id`),
  UNIQUE KEY `uk_vehicle_reg` (`registration_number`),
  KEY `idx_veh_driver` (`assigned_driver_id`),
  KEY `idx_veh_route` (`assigned_route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_vehicles`
--

LOCK TABLES `tbl_vehicles` WRITE;
/*!40000 ALTER TABLE `tbl_vehicles` DISABLE KEYS */;
INSERT INTO `tbl_vehicles` VALUES (1,'Bus 01','KL-02-AB-1234','School Bus','Tata Motors','Starbus Ultra 40',2022,40,'School Yellow','2022-04-10','2037-04-09','POL-INS-98721','2027-04-15','FIT-KL02-8871','2027-04-01','POL-KL-44321','2026-12-31','PRM-KL-9921','2027-03-31',1,1,'Active','2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(2,'Bus 02','KL-02-CD-5678','School Bus','Ashok Leyland','Sunshine 42',2023,42,'School Yellow','2023-06-15','2038-06-14','POL-INS-66542','2027-06-20','FIT-KL02-9982','2027-06-10','POL-KL-55432','2027-01-15','PRM-KL-8832','2027-06-01',2,2,'Active','2026-08-20 14:31:14','2026-08-20 14:31:14','n'),(3,'Van 01','KL-02-EF-9012','Van','Force Motors','Traveller 18',2021,18,'School Yellow','2021-08-01','2036-07-31','POL-INS-33219','2026-09-15','FIT-KL02-6651','2026-09-01','POL-KL-11982','2026-11-20','PRM-KL-4421','2026-08-30',3,3,'Active','2026-08-20 14:31:14','2026-08-20 14:31:14','n');
/*!40000 ALTER TABLE `tbl_vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-03 12:42:56
