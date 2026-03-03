-- nielit_salary.sql
-- Database dump for NIELIT Salary Slip Generator
-- Created: <provided here for import>

CREATE DATABASE IF NOT EXISTS `nielit_salary` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nielit_salary`;

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NOTE: password stored below is a placeholder plain text 'admin123'
-- If your authenticate.php expects hashed passwords, run the PHP helper to hash it.
INSERT INTO `admins` (`username`, `password`, `fullname`) VALUES
('admin','admin123','Administrator');

-- ----------------------------
-- Table structure for employees
-- ----------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `designation` VARCHAR(255) DEFAULT NULL,
  `place_of_posting` VARCHAR(255) DEFAULT NULL,
  `wing_section` VARCHAR(255) DEFAULT NULL,
  `pay_matrix_cell` VARCHAR(255) DEFAULT NULL,
  `pan_aadhar` VARCHAR(255) DEFAULT NULL,
  `epf_account` VARCHAR(255) DEFAULT NULL,
  `uan` VARCHAR(255) DEFAULT NULL,
  `bank_name` VARCHAR(255) DEFAULT NULL,
  `bank_acc` VARCHAR(255) DEFAULT NULL,
  `mode_of_payment` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample employee
INSERT INTO `employees` (`name`, `designation`, `place_of_posting`, `wing_section`, `pay_matrix_cell`, `pan_aadhar`, `epf_account`, `uan`, `bank_name`, `bank_acc`, `mode_of_payment`)
VALUES
('Demo Employee', 'Trainer', 'Bhubaneswar', 'Training', 'PayMatrix-Example', 'PANXXXX1234/AADHARXXXX', 'EPF000111', 'UAN000111', 'State Bank of India, Bhubaneswar', 'XXXXXXXXXXXX', 'Bank Transfer');

-- ----------------------------
-- Table structure for slips
-- ----------------------------
DROP TABLE IF EXISTS `slips`;
CREATE TABLE `slips` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `employee_id` INT DEFAULT NULL,            -- optional FK to employees.id
  `employee_name` VARCHAR(255) DEFAULT NULL,
  `designation` VARCHAR(255) DEFAULT NULL,
  `month_year` VARCHAR(50) DEFAULT NULL,
  `basic` DECIMAL(12,2) DEFAULT 0,
  `da` DECIMAL(12,2) DEFAULT 0,
  `hra` DECIMAL(12,2) DEFAULT 0,
  `ta` DECIMAL(12,2) DEFAULT 0,
  `da_on_ta` DECIMAL(12,2) DEFAULT 0,
  `other_earnings` DECIMAL(12,2) DEFAULT 0,
  `gross_salary` DECIMAL(12,2) DEFAULT 0,
  `epf_amount` DECIMAL(12,2) DEFAULT 0,
  `professional_tax` DECIMAL(12,2) DEFAULT 0,
  `income_tax` DECIMAL(12,2) DEFAULT 0,
  `other_deductions` DECIMAL(12,2) DEFAULT 0,
  `total_deductions` DECIMAL(12,2) DEFAULT 0,
  `net_salary` DECIMAL(12,2) DEFAULT 0,
  `date_of_payment` DATE DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample slip inserted using Demo Employee name
INSERT INTO `slips` (`employee_id`,`employee_name`,`designation`,`month_year`,`basic`,`da`,`hra`,`ta`,`da_on_ta`,`other_earnings`,`gross_salary`,`epf_amount`,`professional_tax`,`income_tax`,`other_deductions`,`total_deductions`,`net_salary`,`date_of_payment`)
VALUES
(1,'Demo Employee','Trainer','August 2025',69700.00,40626.00,13940.00,3600.00,2088.00,0.00,129754.00,11012.60,200.00,11500.00,0.00,22712.60,107041.40,'2025-08-31');

-- Optional: Add foreign key if you plan to use employee_id relationally (commented out by default)
-- ALTER TABLE `slips` ADD CONSTRAINT `fk_slips_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

