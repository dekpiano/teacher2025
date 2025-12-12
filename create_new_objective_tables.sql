-- Table to store the definitions of objectives for each club
CREATE TABLE `tb_club_objectives` (
  `objective_id` INT NOT NULL AUTO_INCREMENT,
  `club_id` INT NOT NULL,
  `objective_name` VARCHAR(255) NOT NULL,
  `objective_description` TEXT NULL,
  `objective_order` INT NOT NULL DEFAULT 0,
  `created_by` VARCHAR(20) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`objective_id`),
  INDEX `idx_club` (`club_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Table to track the progress of each student on each objective
CREATE TABLE `tb_club_student_progress` (
  `progress_id` INT NOT NULL AUTO_INCREMENT,
  `objective_id` INT NOT NULL,
  `student_id` VARCHAR(17) NOT NULL,
  `club_id` INT NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = Not Passed, 1 = Passed',
  `updated_by` VARCHAR(20) NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`progress_id`),
  UNIQUE INDEX `idx_student_objective` (`student_id`, `objective_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Table to store the summary data for each student in a club for a specific term/year
CREATE TABLE `tb_club_student_summary` (
  `summary_id` INT NOT NULL AUTO_INCREMENT,
  `club_id` INT NOT NULL,
  `student_id` VARCHAR(17) NOT NULL,
  `academic_year` VARCHAR(4) NOT NULL,
  `academic_term` INT(1) NOT NULL,
  `objective_result` VARCHAR(20) NULL COMMENT 'e.g., ผ่าน, ไม่ผ่าน',
  `result_level` VARCHAR(20) NULL,
  `activity_notes` TEXT NULL,
  `correction_notes` TEXT NULL,
  `updated_by` VARCHAR(20) NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`summary_id`),
  UNIQUE INDEX `idx_club_student_term` (`club_id`, `student_id`, `academic_year`, `academic_term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
