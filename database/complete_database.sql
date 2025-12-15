-- =====================================================
-- Barangay Healthcare Management System - Database Schema
-- Generated from Laravel Migrations
-- Date: December 8, 2025
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- CORE AUTHENTICATION & SESSION TABLES
-- =====================================================

-- Drop existing tables
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `sessions`;

-- Create users table
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(255) NOT NULL,
  `middle_name` VARCHAR(255) NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `dark_mode` TINYINT(1) NOT NULL DEFAULT 0,
  `text_size` VARCHAR(255) NOT NULL DEFAULT 'medium',
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `username` VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(255) NOT NULL DEFAULT 'employee',
  `contact_number` VARCHAR(255) NULL,
  `address` VARCHAR(500) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create password_reset_tokens table
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) PRIMARY KEY,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create sessions table
CREATE TABLE `sessions` (
  `id` VARCHAR(255) PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  INDEX `sessions_user_id_index` (`user_id`),
  INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CACHE TABLES
-- =====================================================

DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `cache_locks`;

-- Create cache table
CREATE TABLE `cache` (
  `key` VARCHAR(255) PRIMARY KEY,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cache_locks table
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) PRIMARY KEY,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- JOB QUEUE TABLES
-- =====================================================

DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `failed_jobs`;

-- Create jobs table
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create job_batches table
CREATE TABLE `job_batches` (
  `id` VARCHAR(255) PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL,
  `cancelled_at` INT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create failed_jobs table
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(255) NOT NULL UNIQUE,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ROLES & AUDIT TABLES
-- =====================================================

DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `audit_logs`;

-- Create roles table
CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `badge_color` VARCHAR(255) NULL,
  `permissions` JSON NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create audit_logs table
CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `user_role` VARCHAR(255) NULL,
  `action` VARCHAR(255) NOT NULL,
  `module` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'success',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- EVENTS/CALENDAR TABLE
-- =====================================================

DROP TABLE IF EXISTS `events`;

-- Create events table
CREATE TABLE `events` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `start_time` TIME NULL,
  `end_time` TIME NULL,
  `location` VARCHAR(255) NULL,
  `color` VARCHAR(7) NOT NULL DEFAULT '#4a90a4',
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX `events_start_date_index` (`start_date`),
  INDEX `events_end_date_index` (`end_date`),
  INDEX `events_created_by_index` (`created_by`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PATIENT MANAGEMENT TABLES
-- =====================================================

DROP TABLE IF EXISTS `patients`;

-- Create patients table
CREATE TABLE `patients` (
  `PatientID` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `dateRegistered` DATE NOT NULL,
  `patientNo` VARCHAR(255) NULL,
  `sex` ENUM('M', 'F') NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `birthday` DATE NOT NULL,
  `contactNumber` VARCHAR(255) NULL,
  `address` TEXT NOT NULL,
  `nhtsIdNo` VARCHAR(255) NULL,
  `pwdIdNo` VARCHAR(255) NULL,
  `phicIdNo` VARCHAR(255) NULL,
  `fourPsCctIdNo` VARCHAR(255) NULL,
  `ethnicGroup` VARCHAR(255) NULL,
  `diabetesDate` DATE NULL,
  `hypertensionDate` DATE NULL,
  `copdDate` DATE NULL,
  `asthmaDate` DATE NULL,
  `cataractDate` DATE NULL,
  `eorDate` DATE NULL,
  `diabeticRetinopathyDate` DATE NULL,
  `otherEyeDiseaseDate` DATE NULL,
  `alcoholismDate` DATE NULL,
  `substanceAbuseDate` DATE NULL,
  `otherMentalDisordersDate` DATE NULL,
  `atRiskSuicideDate` DATE NULL,
  `philpenDate` DATE NULL,
  `currentSmoker` TINYINT(1) NOT NULL DEFAULT 0,
  `passiveSmoker` TINYINT(1) NOT NULL DEFAULT 0,
  `stoppedSmoking` TINYINT(1) NOT NULL DEFAULT 0,
  `drinksAlcohol` TINYINT(1) NOT NULL DEFAULT 0,
  `hadFiveDrinks` TINYINT(1) NOT NULL DEFAULT 0,
  `dietaryRiskFactors` TINYINT(1) NOT NULL DEFAULT 0,
  `physicalInactivity` TINYINT(1) NOT NULL DEFAULT 0,
  `height` DECIMAL(5, 2) NULL,
  `weight` DECIMAL(5, 2) NULL,
  `waistCircumference` DECIMAL(5, 2) NULL,
  `bmi` DECIMAL(5, 2) NULL,
  `whoDasDate` DATE NULL,
  `part1` VARCHAR(255) NULL,
  `part2Score` INT NULL,
  `top1Domain` VARCHAR(255) NULL,
  `top2Domain` VARCHAR(255) NULL,
  `top3Domain` VARCHAR(255) NULL,
  `lengthDiabetes` VARCHAR(255) NULL,
  `lengthHypertension` VARCHAR(255) NULL,
  `floaters` TINYINT(1) NOT NULL DEFAULT 0,
  `blurredVision` TINYINT(1) NOT NULL DEFAULT 0,
  `fluctuatingVision` TINYINT(1) NOT NULL DEFAULT 0,
  `impairedColorVision` TINYINT(1) NOT NULL DEFAULT 0,
  `darkEmptyAreas` TINYINT(1) NOT NULL DEFAULT 0,
  `visionLoss` TINYINT(1) NOT NULL DEFAULT 0,
  `visualAcuityLeft` VARCHAR(255) NULL,
  `visualAcuityRight` VARCHAR(255) NULL,
  `ophthalmoscopyResults` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ASSESSMENTS TABLE
-- =====================================================

DROP TABLE IF EXISTS `assessments`;

-- Create assessments table
CREATE TABLE `assessments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `PatientID` BIGINT UNSIGNED NOT NULL,
  `date` DATE NULL,
  `age` VARCHAR(255) NULL,
  `cvdRisk` VARCHAR(255) NULL,
  `bpSystolic` VARCHAR(255) NULL,
  `bpDiastolic` VARCHAR(255) NULL,
  `wt` VARCHAR(255) NULL,
  `ht` VARCHAR(255) NULL,
  `fbsRbs` VARCHAR(255) NULL,
  `lipidProfile` VARCHAR(255) NULL,
  `urineKetones` VARCHAR(255) NULL,
  `urineProtein` VARCHAR(255) NULL,
  `footCheck` VARCHAR(255) NULL,
  `chiefComplaint` TEXT NULL,
  `historyPhysical` TEXT NULL,
  `management` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`PatientID`) REFERENCES `patients`(`PatientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PRENATAL CARE TABLES
-- =====================================================

DROP TABLE IF EXISTS `prenatal_records`;
DROP TABLE IF EXISTS `prenatal_visits`;

-- Create prenatal_records table
CREATE TABLE `prenatal_records` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `record_no` VARCHAR(255) NULL,
  `mother_name` VARCHAR(255) NOT NULL,
  `purok` VARCHAR(255) NULL,
  `age` TINYINT UNSIGNED NULL,
  `dob` DATE NULL,
  `occupation` VARCHAR(255) NULL,
  `education` VARCHAR(255) NULL,
  `is_4ps` TINYINT(1) NOT NULL DEFAULT 0,
  `four_ps_no` VARCHAR(255) NULL,
  `cell` VARCHAR(255) NULL,
  `lmp` DATE NULL,
  `edc` DATE NULL,
  `urinalysis` VARCHAR(255) NULL,
  `gravida` TINYINT UNSIGNED NULL,
  `para` TINYINT UNSIGNED NULL,
  `abortion` TINYINT UNSIGNED NULL,
  `delivery_count` TINYINT UNSIGNED NULL,
  `last_delivery_date` DATE NULL,
  `delivery_type` VARCHAR(255) NULL,
  `hemoglobin_first` VARCHAR(255) NULL,
  `hemoglobin_second` VARCHAR(255) NULL,
  `blood_type` VARCHAR(255) NULL,
  `urinalysis_protein` VARCHAR(255) NULL,
  `urinalysis_sugar` VARCHAR(255) NULL,
  `husband_name` VARCHAR(255) NULL,
  `husband_occupation` VARCHAR(255) NULL,
  `husband_education` VARCHAR(255) NULL,
  `family_religion` VARCHAR(255) NULL,
  `amount_prepared` VARCHAR(255) NULL,
  `philhealth_member` VARCHAR(255) NULL,
  `delivery_location` VARCHAR(255) NULL,
  `delivery_partner` VARCHAR(255) NULL,
  `td1` DATE NULL,
  `td2` DATE NULL,
  `td3` DATE NULL,
  `td4` DATE NULL,
  `td5` DATE NULL,
  `tdl` DATE NULL,
  `fbs` VARCHAR(255) NULL,
  `rbs` VARCHAR(255) NULL,
  `ogtt` VARCHAR(255) NULL,
  `vdrl` VARCHAR(255) NULL,
  `hbsag` VARCHAR(255) NULL,
  `hiv` VARCHAR(255) NULL,
  `extra` JSON NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create prenatal_visits table
CREATE TABLE `prenatal_visits` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `prenatal_record_id` BIGINT UNSIGNED NOT NULL,
  `date` DATE NULL,
  `trimester` VARCHAR(255) NULL,
  `risk` VARCHAR(255) NULL,
  `first_visit` VARCHAR(255) NULL,
  `subjective` TEXT NULL,
  `aog` VARCHAR(255) NULL,
  `weight` VARCHAR(255) NULL,
  `height` VARCHAR(255) NULL,
  `bp` VARCHAR(255) NULL,
  `pr` VARCHAR(255) NULL,
  `fh` VARCHAR(255) NULL,
  `fht` VARCHAR(255) NULL,
  `presentation` VARCHAR(255) NULL,
  `bmi` VARCHAR(255) NULL,
  `rr` VARCHAR(255) NULL,
  `hr` VARCHAR(255) NULL,
  `assessment` TEXT NULL,
  `plan` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`prenatal_record_id`) REFERENCES `prenatal_records`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FAMILY PLANNING TABLE
-- =====================================================

DROP TABLE IF EXISTS `family_planning_records`;

-- Create family_planning_records table
CREATE TABLE `family_planning_records` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `record_no` VARCHAR(255) NULL,
  `client_name` VARCHAR(255) NOT NULL,
  `dob` DATE NULL,
  `age` TINYINT UNSIGNED NULL,
  `address` VARCHAR(255) NULL,
  `contact` VARCHAR(255) NULL,
  `occupation` VARCHAR(255) NULL,
  `spouse_name` VARCHAR(255) NULL,
  `spouse_age` TINYINT UNSIGNED NULL,
  `children_count` TINYINT UNSIGNED NULL,
  `client_type` VARCHAR(255) NULL,
  `reason` JSON NULL,
  `medical_history` JSON NULL,
  `gravida` TINYINT UNSIGNED NULL,
  `para` TINYINT UNSIGNED NULL,
  `last_delivery` DATE NULL,
  `last_period` DATE NULL,
  `menstrual_flow` VARCHAR(255) NULL,
  `dysmenorrhea` VARCHAR(255) NULL,
  `sti_risk` JSON NULL,
  `vaw_risk` JSON NULL,
  `bp` VARCHAR(255) NULL,
  `weight` VARCHAR(255) NULL,
  `height` VARCHAR(255) NULL,
  `exam_findings` TEXT NULL,
  `counseled_by` VARCHAR(255) NULL,
  `client_signature` VARCHAR(255) NULL,
  `consent_date` DATE NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- NATIONAL IMMUNIZATION PROGRAM (NIP) TABLES
-- =====================================================

DROP TABLE IF EXISTS `nip_records`;
DROP TABLE IF EXISTS `nip_visits`;

-- Create nip_records table
CREATE TABLE `nip_records` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `record_no` VARCHAR(255) NULL,
  `date` DATE NULL,
  `child_name` VARCHAR(255) NOT NULL,
  `dob` DATE NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `mother_name` VARCHAR(255) NULL,
  `father_name` VARCHAR(255) NULL,
  `contact` VARCHAR(255) NULL,
  `place_delivery` VARCHAR(255) NULL,
  `attended_by` VARCHAR(255) NULL,
  `sex_baby` VARCHAR(1) NULL,
  `nhts_4ps_id` VARCHAR(255) NULL,
  `phic_id` VARCHAR(255) NULL,
  `tt_status_mother` VARCHAR(255) NULL,
  `birth_length` VARCHAR(255) NULL,
  `birth_weight` VARCHAR(255) NULL,
  `delivery_type` VARCHAR(255) NULL,
  `initiated_breastfeeding` VARCHAR(255) NULL,
  `birth_order` TINYINT UNSIGNED NULL,
  `newborn_screening_date` DATE NULL,
  `newborn_screening_result` VARCHAR(255) NULL,
  `hearing_test_screened` VARCHAR(255) NULL,
  `vit_k` VARCHAR(255) NULL,
  `bcg` VARCHAR(255) NULL,
  `hepa_b_24h` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create nip_visits table
CREATE TABLE `nip_visits` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nip_record_id` BIGINT UNSIGNED NOT NULL,
  `visit_date` DATE NULL,
  `age_months` TINYINT UNSIGNED NULL,
  `weight` VARCHAR(255) NULL,
  `length` VARCHAR(255) NULL,
  `status` VARCHAR(255) NULL,
  `breastfeeding` VARCHAR(255) NULL,
  `temperature` VARCHAR(255) NULL,
  `vaccine` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`nip_record_id`) REFERENCES `nip_records`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MEDICINE INVENTORY TABLES
-- =====================================================

DROP TABLE IF EXISTS `medicines`;
DROP TABLE IF EXISTS `medicine_batches`;
DROP TABLE IF EXISTS `medicine_dispenses`;
DROP TABLE IF EXISTS `medicine_dispense_batches`;

-- Create medicines table
CREATE TABLE `medicines` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `generic_name` VARCHAR(255) NULL,
  `dosage_form` VARCHAR(255) NULL,
  `strength` VARCHAR(255) NULL,
  `unit` VARCHAR(255) NOT NULL DEFAULT 'tablet',
  `reorder_level` INT NOT NULL DEFAULT 0,
  `remarks` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create medicine_batches table
CREATE TABLE `medicine_batches` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `medicine_id` BIGINT UNSIGNED NOT NULL,
  `batch_code` VARCHAR(100) NULL,
  `quantity_on_hand` INT NOT NULL DEFAULT 0,
  `expiry_date` DATE NOT NULL,
  `date_received` DATE NOT NULL,
  `supplier` VARCHAR(255) NULL,
  `unit_price` DECIMAL(10, 2) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create medicine_dispenses table
CREATE TABLE `medicine_dispenses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `medicine_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `dispensed_to` VARCHAR(255) NULL,
  `reference_no` VARCHAR(255) NULL,
  `dispensed_at` DATE NULL,
  `remarks` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create medicine_dispense_batches table
CREATE TABLE `medicine_dispense_batches` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `medicine_dispense_id` BIGINT UNSIGNED NOT NULL,
  `medicine_batch_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`medicine_dispense_id`) REFERENCES `medicine_dispenses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medicine_batch_id`) REFERENCES `medicine_batches`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MEDICAL SUPPLIES TABLES
-- =====================================================

DROP TABLE IF EXISTS `medical_supplies`;
DROP TABLE IF EXISTS `supply_history`;

-- Create medical_supplies table
CREATE TABLE `medical_supplies` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `item_name` VARCHAR(255) NOT NULL UNIQUE,
  `category` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `unit_of_measure` VARCHAR(255) NULL,
  `quantity_on_hand` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create supply_history table
CREATE TABLE `supply_history` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `medical_supply_id` BIGINT UNSIGNED NOT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL,
  `received_from` VARCHAR(255) NULL,
  `date_received` DATE NOT NULL,
  `handled_by` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  FOREIGN KEY (`medical_supply_id`) REFERENCES `medical_supplies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RE-ENABLE FOREIGN KEY CHECKS
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- END OF DATABASE SCHEMA
-- =====================================================
