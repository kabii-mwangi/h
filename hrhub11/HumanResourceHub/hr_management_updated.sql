-- MySQL dump 10.13  Distrib 8.4.5, for Linux (x86_64)
--
-- Host: localhost    Database: hr_management_fresh
-- ------------------------------------------------------
-- Server version	8.4.5-0ubuntu0.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES (1,'Admin','Manages employee relations and company policies','2025-07-18 19:22:36','2025-07-18 19:22:36'),(2,'Commercial','Handles sales, marketing, and customer relations','2025-07-18 19:22:36','2025-07-18 19:22:36'),(3,'Technical','Manages technical operations and development','2025-07-18 19:22:36','2025-07-18 19:22:36'),(4,'Corporate Affairs','Handles legal, compliance, and corporate governance','2025-07-18 19:22:36','2025-07-18 19:22:36'),(5,'Fort-Aqua','Water management and supply operations','2025-07-18 19:22:36','2025-07-18 19:22:36');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `date_of_birth` date DEFAULT NULL,
  `hire_date` date NOT NULL,
  `department_id` int DEFAULT NULL,
  `section_id` int DEFAULT NULL,
  `employee_type` enum('officer','section_head','manager','dept_head','managing_director','bod_chairman') DEFAULT 'officer',
  `status` enum('active','inactive','terminated') DEFAULT 'active',
  `user_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_id` (`user_id`),
  KEY `idx_employees_department` (`department_id`),
  KEY `idx_employees_section` (`section_id`),
  KEY `idx_employees_status` (`status`),
  KEY `idx_employees_type` (`employee_type`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` (`id`, `employee_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `date_of_birth`, `hire_date`, `department_id`, `section_id`, `employee_type`, `status`, `user_id`, `created_at`, `updated_at`) VALUES (1,'EMP001','John','Doe','john.doe@company.com','123-456-7890',NULL,NULL,'2023-01-15',1,1,'manager','active',NULL,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(2,'EMP002','Jane','Smith','jane.smith@company.com','123-456-7891',NULL,NULL,'2023-02-20',2,3,'section_head','active',NULL,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(3,'EMP003','Mike','Johnson','mike.johnson@company.com','123-456-7892',NULL,NULL,'2023-03-10',3,6,'officer','active',NULL,'2025-07-18 19:22:36','2025-07-18 19:22:36');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `department_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sections_department` (`department_id`),
  CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` (`id`, `name`, `description`, `department_id`, `created_at`, `updated_at`) VALUES (1,'Human Resources','Employee management and policies',1,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(2,'Finance','Financial planning and accounting',1,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(3,'Sales','Direct sales operations',2,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(4,'Marketing','Brand promotion and advertising',2,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(5,'Customer Service','Customer support and relations',2,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(6,'Software Development','Application and system development',3,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(7,'IT Support','Technical support and maintenance',3,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(8,'Network Operations','Network infrastructure management',3,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(9,'Legal Affairs','Legal compliance and contracts',4,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(10,'Public Relations','Media and public communications',4,'2025-07-18 19:22:36','2025-07-18 19:22:36'),(11,'Water Supply','Water distribution and supply management',5,'2025-07-18 19:22:36','2025-07-18 19:22:36');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('super_admin','hr_manager','dept_head','section_head','manager','employee') DEFAULT 'employee',
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `profile_image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `email`, `first_name`, `last_name`, `password`, `role`, `phone`, `address`, `profile_image_url`, `created_at`, `updated_at`) VALUES ('admin-001','admin@company.com','Admin','User','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','super_admin',NULL,NULL,NULL,'2025-07-18 19:22:36','2025-07-18 19:22:36'),('dept-001','depthead@company.com','Department','Head','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','dept_head',NULL,NULL,NULL,'2025-07-18 19:22:36','2025-07-18 19:22:36'),('hr-001','hr@company.com','HR','Manager','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','hr_manager',NULL,NULL,NULL,'2025-07-18 19:22:36','2025-07-18 19:22:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-18 19:26:21
