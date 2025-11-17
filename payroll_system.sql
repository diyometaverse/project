/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - payroll_system
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`payroll_system` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `payroll_system`;

/*Table structure for table `activity_logs` */

DROP TABLE IF EXISTS `activity_logs`;

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `activity_logs` */

/*Table structure for table `attendance` */

DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  PRIMARY KEY (`attendance_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `attendance` */

insert  into `attendance`(`attendance_id`,`employee_id`,`date`,`time_in`,`time_out`) values 
(3,6,'2025-10-01','08:00:40','16:09:12'),
(4,7,'2025-10-02','11:09:07','11:09:14'),
(5,6,'2025-10-02','11:52:03','17:00:17'),
(6,6,'2025-10-03','13:15:17','13:15:34'),
(20,6,'2025-10-05','21:04:40','21:35:15'),
(21,7,'2025-10-05','21:35:24',NULL),
(22,7,'2025-11-16','18:50:51','18:53:07'),
(23,6,'2025-11-16','18:53:55','23:54:07'),
(24,8,'2025-11-16','19:15:48',NULL);

/*Table structure for table `audit_trail` */

DROP TABLE IF EXISTS `audit_trail`;

CREATE TABLE `audit_trail` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`audit_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `audit_trail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `audit_trail` */

/*Table structure for table `benefits` */

DROP TABLE IF EXISTS `benefits`;

CREATE TABLE `benefits` (
  `benefit_id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`benefit_id`),
  KEY `payroll_id` (`payroll_id`),
  CONSTRAINT `benefits_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`payroll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `benefits` */

/*Table structure for table `deductions` */

DROP TABLE IF EXISTS `deductions`;

CREATE TABLE `deductions` (
  `deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`deduction_id`),
  KEY `payroll_id` (`payroll_id`),
  CONSTRAINT `deductions_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`payroll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `deductions` */

/*Table structure for table `employees` */

DROP TABLE IF EXISTS `employees`;

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_hired` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `rfid_tag` varchar(50) DEFAULT NULL,
  `face_id` varchar(255) DEFAULT NULL,
  `face_image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `rfid_tag` (`rfid_tag`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `employees` */

insert  into `employees`(`employee_id`,`first_name`,`last_name`,`email`,`phone`,`position`,`department`,`daily_rate`,`status`,`date_hired`,`created_at`,`rfid_tag`,`face_id`,`face_image_path`) values 
(6,'Johnlloyd','Buenaflor','johnlloydbuenaflor19@gmail.com','09070220027','Web Developer','IT',650.00,'active','2025-10-01','2025-10-01 14:35:57','0006559565','1135',NULL),
(7,'Lloyd','Buenaflor','admin+1@mepfs.com','09070220027','Web Developer','ITS',750.00,'active','2025-10-01','2025-10-01 14:39:07','0006646380','1532',NULL),
(8,'Juan','Dela Cruz','asdasdasd@asd.com','09070220027','Web Developer','ITS',99999999.99,'inactive','2025-11-16','2025-11-16 19:14:23','0006671353','',NULL);

/*Table structure for table `payroll` */

DROP TABLE IF EXISTS `payroll`;

CREATE TABLE `payroll` (
  `payroll_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `total_hours` decimal(8,2) DEFAULT 0.00,
  `base_salary` decimal(10,2) DEFAULT 0.00,
  `overtime` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `net_pay` decimal(10,2) DEFAULT 0.00,
  `gross_pay` decimal(10,2) DEFAULT 0.00,
  `date_generated` timestamp NULL DEFAULT current_timestamp(),
  `sss` decimal(10,2) DEFAULT 0.00,
  `philhealth` decimal(10,2) DEFAULT 0.00,
  `pagibig` decimal(10,2) DEFAULT 0.00,
  `withholding_tax` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`payroll_id`),
  UNIQUE KEY `unique_employee_period` (`employee_id`,`period_id`),
  KEY `period_id` (`period_id`),
  CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `payroll_periods` (`period_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `payroll` */

insert  into `payroll`(`payroll_id`,`employee_id`,`period_id`,`total_hours`,`base_salary`,`overtime`,`deductions`,`net_pay`,`gross_pay`,`date_generated`,`sss`,`philhealth`,`pagibig`,`withholding_tax`) values 
(109,6,23,8.01,650.81,0.01,119.25,532.38,651.63,'2025-10-05 17:26:52',23.00,17.00,13.00,65.00),
(110,6,5,5.14,417.63,0.00,76.43,341.20,417.63,'2025-10-05 17:27:02',15.00,11.00,8.00,41.00),
(113,7,5,0.00,0.00,0.00,0.00,0.00,0.00,'2025-11-17 12:18:07',0.00,0.00,0.00,0.00);

/*Table structure for table `payroll_periods` */

DROP TABLE IF EXISTS `payroll_periods`;

CREATE TABLE `payroll_periods` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('active','closed') DEFAULT 'active',
  PRIMARY KEY (`period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `payroll_periods` */

insert  into `payroll_periods`(`period_id`,`start_date`,`end_date`,`label`,`created_at`,`status`) values 
(5,'2025-10-01','2025-10-15','Oct 01-Oct 15','2025-10-01 16:15:54','active'),
(23,'2025-11-01','2025-11-16','Nov 1 - Nov 16','2025-10-05 17:24:21','active'),
(24,'2025-10-16','2025-10-31','Oct 16 - Oct 31','2025-10-05 17:26:17','active');

/*Table structure for table `timesheets` */

DROP TABLE IF EXISTS `timesheets`;

CREATE TABLE `timesheets` (
  `timesheet_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `hours_worked` decimal(4,2) DEFAULT 0.00,
  `overtime_hours` decimal(4,2) DEFAULT 0.00,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`timesheet_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `timesheets_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `timesheets` */

insert  into `timesheets`(`timesheet_id`,`employee_id`,`work_date`,`hours_worked`,`overtime_hours`,`status`,`created_at`) values 
(77,6,'2025-11-05',8.01,0.01,'Approved','2025-10-05 17:26:23'),
(78,6,'2025-10-01',8.14,0.14,'Approved','2025-10-05 17:26:34'),
(79,7,'2025-10-02',0.00,0.00,'Approved','2025-10-05 17:26:34'),
(80,6,'2025-10-02',5.14,0.00,'Approved','2025-10-05 17:26:34'),
(81,6,'2025-10-03',0.00,0.00,'Approved','2025-10-05 17:26:34'),
(82,6,'2025-10-04',0.00,0.00,'Approved','2025-10-05 17:26:34'),
(83,7,'2025-10-04',0.00,0.00,'Approved','2025-10-05 17:26:34');

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','hr','staff') DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `user` */

insert  into `user`(`user_id`,`email`,`password`,`role`,`created_at`) values 
(2,'admin@mepfs.com','$2y$10$sH8JE97LdFr9QTDQAEvxZu.deonDUvWwyl5uxvaETsHlRAtY4LOUC','admin','2025-10-05 12:59:01');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','hr','staff') DEFAULT 'staff',
  `employee_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
