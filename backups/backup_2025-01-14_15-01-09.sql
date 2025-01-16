-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_doc_management_system
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `tbl_activity_log`
--

DROP TABLE IF EXISTS `tbl_activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(200) NOT NULL,
  `details` text NOT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_activity_log`
--

LOCK TABLES `tbl_activity_log` WRITE;
/*!40000 ALTER TABLE `tbl_activity_log` DISABLE KEYS */;
INSERT INTO `tbl_activity_log` VALUES (1,14,'Add Document','Added document: Introduction of Intelligence System','2025-01-14 05:48:39'),(2,1,'Login','Logged in successfully.','2025-01-14 06:16:31'),(3,1,'Add Document','Added document: Introduction to Intelligence System','2025-01-14 06:17:53'),(4,1,'Add Document','Added document: Introduction to System Design & Architecture for Intelligent Systems','2025-01-14 06:50:14'),(5,1,'Made document inaccessible','Document: Introduction to Intelligence System','2025-01-14 07:16:26'),(6,1,'Login','Logged in successfully.','2025-01-14 07:32:25'),(7,1,'Login','Logged in successfully.','2025-01-14 07:33:15'),(8,1,'Login','Logged in successfully.','2025-01-14 07:33:46'),(9,1,'Made document accessible','Document: Introduction to Intelligence System','2025-01-14 07:35:35'),(10,1,'Made document inaccessible','Document: Introduction to Intelligence System','2025-01-14 07:36:14'),(11,1,'Login','Logged in successfully.','2025-01-14 07:37:07'),(12,1,'Made document accessible','Document: Introduction to Intelligence System','2025-01-14 07:37:12'),(13,1,'Made document inaccessible','Document: Introduction to System Design & Architecture for Intelligent Systems','2025-01-14 07:37:14'),(14,1,'add_user','Added user: Sok Heng (sok.heng@gmail.com)','2025-01-14 07:55:19'),(15,2,'Login','Logged in successfully.','2025-01-14 07:55:37'),(16,1,'Login','Logged in successfully.','2025-01-14 08:01:00');
/*!40000 ALTER TABLE `tbl_activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_documents`
--

DROP TABLE IF EXISTS `tbl_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(150) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `is_accessible` tinyint(1) NOT NULL DEFAULT 1,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_documents`
--

LOCK TABLES `tbl_documents` WRITE;
/*!40000 ALTER TABLE `tbl_documents` DISABLE KEYS */;
INSERT INTO `tbl_documents` VALUES (1,'Introduction to Intelligence System','The document \"Introduction to Intelligence Systems\" by Sek Socheat outlines the fundamentals of intelligent systems, including their definition, types (such as rule-based, expert, machine learning, neural networks, cognitive, and robotic systems), and applications across various fields like healthcare, finance, and transportation. It details key components like perception, data processing, learning engines, knowledge bases, actuation, and feedback loops.','Sek Socheat','../../uploads/documents/introduction_of_intelligence_systems.pdf','pdf',1,1,'2025-01-14 06:17:53','2025-01-14 07:37:12'),(2,'Introduction to System Design & Architecture for Intelligent Systems','The document is an introduction to system design and architecture for intelligent systems, specifically focusing on the development of AI applications from 2024 to 2025. It outlines key topics such as problem formulation, system design principles, and ethical considerations.','Sek Socheat','../../uploads/documents/file2.pdf','pdf',1,0,'2025-01-14 06:50:14','2025-01-14 07:37:14');
/*!40000 ALTER TABLE `tbl_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_password_resets`
--

DROP TABLE IF EXISTS `tbl_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(250) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_password_resets`
--

LOCK TABLES `tbl_password_resets` WRITE;
/*!40000 ALTER TABLE `tbl_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_system_settings`
--

DROP TABLE IF EXISTS `tbl_system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) NOT NULL,
  `max_upload_size` int(11) NOT NULL,
  `allowed_file_types` varchar(255) NOT NULL,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_system_settings`
--

LOCK TABLES `tbl_system_settings` WRITE;
/*!40000 ALTER TABLE `tbl_system_settings` DISABLE KEYS */;
INSERT INTO `tbl_system_settings` VALUES (1,'DMS',10,'pdf,doc,docx,ppt,csv,jpg,jpeg,png',0);
/*!40000 ALTER TABLE `tbl_system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role` varchar(50) NOT NULL,
  `image_path` varchar(250) NOT NULL,
  `two_factor_auth` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` VALUES (1,'John Doe','john.doe@example.com','$2y$10$h.HS2abO..d1Hd.nVOkm0uOEypCK6xRiABQXG1w5xV1uX.5Jc4Z.6','superadmin','',0,'2025-01-14 05:38:41','2025-01-14 05:38:41'),(2,'Sok Heng','sok.heng@gmail.com','$2y$10$G7NnCc3kFyqDmbQi1geV9.Au2vQgNvb/Dy6gfPTOsv2lhdAv74PWu','admin','',0,'2025-01-14 07:55:19','2025-01-14 07:55:19');
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-14 15:01:09
