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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_activity_log`
--

LOCK TABLES `tbl_activity_log` WRITE;
/*!40000 ALTER TABLE `tbl_activity_log` DISABLE KEYS */;
INSERT INTO `tbl_activity_log` VALUES (1,1,'Login','Logged in successfully.','2025-01-16 02:43:37'),(2,1,'add_user','Added user: Kim Sour (kim.sour@gamil.com)','2025-01-16 03:16:33'),(3,2,'Login','Logged in successfully.','2025-01-16 03:34:45'),(4,2,'add_user','Added user: Bun Sour (bun.sour@gmail.com)','2025-01-16 03:47:32'),(5,2,'add_user','Added user: Lim Rathana (lim.rathana@gmail.com)','2025-01-16 03:49:01'),(6,2,'add_user','Added user: So Phat (so.phat@gmail.com)','2025-01-16 03:49:29'),(7,2,'add_user','Added user: Kim Vichea (kim.vichea@gmail.com)','2025-01-16 03:50:08'),(8,2,'add_user','Added user: Heng Sinath (heng.sinath@gmail.com)','2025-01-16 03:54:19'),(9,2,'Add Document','Added document: Introduction of Intelligence System','2025-01-16 03:57:13'),(10,2,'Add Document','Added document: Software Engineering','2025-01-16 04:03:04'),(11,2,'Add Document','Added document: Software Development Processes','2025-01-16 04:08:26'),(12,2,'Login','Logged in successfully.','2025-01-16 04:09:29'),(13,3,'Login','Logged in successfully.','2025-01-16 04:09:47'),(14,3,'Add Document','Added document: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 04:19:08'),(15,3,'view','Viewed file: Software Development Processes','2025-01-16 04:19:35'),(16,3,'view','Viewed file: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 04:19:44'),(17,3,'view','Viewed file: Introduction of Intelligence System','2025-01-16 04:19:53'),(18,3,'view','Viewed file: Introduction of Intelligence System','2025-01-16 04:19:57'),(19,3,'view','Viewed file: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 04:38:33'),(20,3,'view','Viewed file: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 04:44:15'),(21,3,'Made document inaccessible','Document: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 04:44:58'),(22,3,'Made document accessible','Document: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 04:44:59'),(23,3,'view','Viewed file: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 04:57:28'),(24,3,'Update Document','Updated document: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 05:06:12'),(25,3,'Update Document','Updated document: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 05:07:35'),(26,3,'Add Document','Added document: Supervised Learning in Machine Learning','2025-01-16 05:14:18'),(27,3,'view','Viewed file: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 05:20:36'),(28,3,'view','Viewed file: Supervised Learning in Machine Learning','2025-01-16 05:20:39'),(29,3,'Update Document','Updated document: Supervised Learning in Machine Learning','2025-01-16 05:40:07'),(30,3,'Update Document','Updated document: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 05:40:40'),(31,3,'Add Document','Added document: Unsupervised Learning in Machine Learning','2025-01-16 05:45:30'),(32,3,'Update Document','Updated document: Introduction to System Design & Architecture for Intelligence Systems','2025-01-16 05:45:52'),(33,4,'Login','Logged in successfully.','2025-01-16 05:51:54'),(34,4,'Add Document','Added document: Semi-supervised Learning in Machine Learning','2025-01-16 05:58:02'),(35,4,'Add Document','Added document: Reinforcement Learning in Machine Learning','2025-01-16 05:58:41'),(36,4,'Add Document','Added document: Designing an IoT Smart Parking Prototype System','2025-01-16 06:06:01'),(37,4,'Add Document','Added document: Investigation of Smart Parking Systems and Their Technologies','2025-01-16 06:07:18'),(38,4,'Add Document','Added document: Natural Language Processing (NLP)','2025-01-16 06:08:44'),(39,4,'Add Document','Added document: Fundamentals of Computer Vision','2025-01-16 06:10:07'),(40,4,'Add Document','Added document: Machine Learning Integration in Intelligence Systems','2025-01-16 06:14:37'),(41,4,'Made document inaccessible','Document: Reinforcement Learning in Machine Learning','2025-01-16 06:54:05'),(42,4,'Made document inaccessible','Document: Designing an IoT Smart Parking Prototype System','2025-01-16 06:54:05'),(43,4,'Made document accessible','Document: Reinforcement Learning in Machine Learning','2025-01-16 06:54:13'),(44,4,'Made document accessible','Document: Designing an IoT Smart Parking Prototype System','2025-01-16 06:54:13'),(45,5,'Login','Logged in successfully.','2025-01-16 06:55:06'),(46,5,'view','Viewed file: Introduction of Intelligence System','2025-01-16 07:03:28'),(47,7,'Login','Logged in successfully.','2025-01-16 07:03:51'),(48,7,'view','Viewed file: Introduction of Intelligence System','2025-01-16 07:04:04'),(49,1,'Login','Logged in successfully.','2025-01-16 07:13:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_documents`
--

LOCK TABLES `tbl_documents` WRITE;
/*!40000 ALTER TABLE `tbl_documents` DISABLE KEYS */;
INSERT INTO `tbl_documents` VALUES (1,'Introduction of Intelligence System','The document \"Introduction to Intelligence Systems\" provides an overview of Intelligent Systems, detailing their development, types, applications, and essential components. It covers various software development tools and frameworks necessary for creating these systems, including programming languages, AI frameworks, and data processing tools. Additionally, it highlights practical activities to enhance understanding and application of the concepts discussed.','Sek Socheat','../../uploads/documents/doc_1.pdf','pdf',2,1,'2025-01-16 03:57:13','2025-01-16 03:57:13'),(2,'Software Engineering','The document provides a comprehensive overview of software engineering, detailing its processes, goals, and key concepts. It distinguishes between software and software engineering, emphasizing the importance of systematic approaches for effective analysis, design, implementation, and maintenance. It discusses various types of software, characteristics of quality software like maintainability, dependability, efficiency, and usability, as well as challenges within the industry, such as cost overruns and evolving requirements. The document outlines definitions from various authors and identifies software engineering as a discipline integrating processes, methods, and tools for producing reliable and efficient applications.','Sry Chrea','../../uploads/documents/doc_2.pdf','pdf',2,1,'2025-01-16 04:03:04','2025-01-16 04:03:04'),(3,'Software Development Processes','The document \"Software Development Processes\" provides an overview of various software development methodologies, including the Waterfall Model, Iterative Refinement, Spiral Development, and Agile Development. It emphasizes standard steps in software projects—Requirements, User Interface Design, System Design, Program Development, and Acceptance and Release—while contrasting heavyweight processes with comprehensive documentation and minimal changes against lightweight processes that accept changes with less documentation. Each methodology has its own strengths, focusing on aspects such as client collaboration in Agile and risk management in Spiral approaches.','Sry Chrea','../../uploads/documents/doc_3.pdf','pdf',2,1,'2025-01-16 04:08:26','2025-01-16 04:08:26'),(4,'Introduction to System Design & Architecture for Intelligence Systems','The document titled \"Introduction to System Design & Architecture for Intelligent Systems\" discusses key elements related to developing intelligent systems, focusing on three main areas: problem formulation, which includes identifying real-world problems suitable for AI; system design, which covers architecture, modularity, and integration; and ethical and regulatory considerations associated with intelligent systems. It also incorporates practical activities and examples across various industries, emphasizing how AI can enhance efficiency and innovation.','Sek Socheat','../../uploads/documents/doc_4_1737006352.pdf','pdf',3,1,'2025-01-16 04:19:08','2025-01-16 05:45:52'),(5,'Supervised Learning in Machine Learning','The document \"Supervised Learning in Machine Learning\" explores the fundamentals of supervised learning, a key area of machine learning characterized by the use of labeled data. It focuses on two primary types: **classification**, which aims to predict discrete categories (e.g., spam detection), and **regression**, which predicts continuous values (e.g., house prices). The content delves into various algorithms, real-world applications, and the processes involved in training, predicting, and evaluating models in these two domains.','Sek Socheat','../../uploads/documents/doc_5.pdf','pdf',3,1,'2025-01-16 05:14:18','2025-01-16 05:40:07'),(6,'Unsupervised Learning in Machine Learning','Unsupervised learning is a branch of machine learning that analyzes datasets without labeled outputs to discover patterns and structures. Key types include clustering, dimensionality reduction, and anomaly detection, with popular algorithms such as K-Means, Hierarchical Clustering, DBSCAN, PCA, and Autoencoders. This approach is widely used for applications like customer segmentation and image processing. Challenges include the absence of labeled data for evaluation, interpretability issues, and sensitivity to algorithm parameters.','Sek Socheat','../../uploads/documents/doc_6.pdf','pdf',3,1,'2025-01-16 05:45:30','2025-01-16 05:45:30'),(7,'Semi-supervised Learning in Machine Learning','Semi-supervised learning (SSL) is a machine learning approach that combines a small amount of labeled data with a large set of unlabeled data to improve model accuracy. SSL reduces the need for expensive labeled data while enhancing generalization by leveraging the underlying data distribution. Common methods used in SSL include self-training, co-training, generative models, and graph-based learning, with algorithms such as Variational Autoencoders and Generative Adversarial Networks. Applications span various fields like natural language processing, computer vision, and healthcare, though SSL can face challenges like assumption dependence and label noise.','Sek Socheat','../../uploads/documents/doc_7.pdf','pdf',4,1,'2025-01-13 05:58:02','2025-01-13 05:58:02'),(8,'Reinforcement Learning in Machine Learning','Reinforcement Learning (RL) is a branch of machine learning where agents learn optimal behaviors through interaction with their environment, focusing on maximizing cumulative rewards. Key characteristics include agent-environment interaction, trial-and-error learning, and the use of policies and value functions. RL is divided into model-based and model-free types, with algorithms such as Q-learning, SARSA, and Actor-Critic. Applications range from gaming and robotics to finance and healthcare, although challenges like sample efficiency and exploration-exploitation trade-off remain significant.','Sek Socheat','../../uploads/documents/doc_8.pdf','pdf',4,1,'2025-01-14 05:58:41','2025-01-16 06:54:13'),(9,'Designing an IoT Smart Parking Prototype System','The research paper presents a design and development of an IoT-based smart parking system that utilizes existing USB CCTV cameras to improve parking services in urban areas. Emphasizing User-Centered Design (UCD) principles, the study integrates stakeholder feedback to address common parking challenges. The proposed low-cost system avoids extensive infrastructural changes by leveraging current CCTV infrastructure to monitor parking space availability. This approach aligns with Smart City initiatives and highlights the importance of stakeholder involvement in creating effective IT tools for better parking management.','Muftah Fraifer, Mikael Fernström','../../uploads/documents/doc_9.pdf','pdf',4,1,'2025-01-14 06:06:01','2025-01-16 06:54:13'),(10,'Investigation of Smart Parking Systems and Their Technologies','The paper titled \"Investigation of Smart Parking Systems and their technologies\" provides a comprehensive literature review of smart parking systems, synthesizing findings from over 60 academic publications from the past 15 years. It highlights advancements in sensing and communication technologies while identifying significant research gaps, particularly the lack of stakeholder involvement in evaluating smart parking solutions. The research underscores the urgency in addressing traffic congestion and environmental issues related to parking searches, suggesting that traditional systems fail to provide real-time availability information for parking spaces. Overall, the study emphasizes the need for more innovative parking solutions to tackle contemporary urban challenges.','Muftah Fraifer, Mikael Fernström','../../uploads/documents/doc_10.pdf','pdf',4,1,'2025-01-15 06:07:18','2025-01-15 06:07:18'),(11,'Natural Language Processing (NLP)','The document discusses the integration of Natural Language Processing (NLP) into intelligence systems, emphasizing its role in enhancing interactions and automation. NLP enables machines to interpret and generate human language, which is crucial for applications in healthcare, finance, and customer support. Key tasks include information retrieval, semantic understanding, dialogue systems, and sentiment analysis. A practical example provided is a grammar-based translation system that converts English to Khmer. The architecture of NLP integration is outlined as a pipeline involving data preprocessing, feature extraction, and reasoning components, facilitating nuanced user interactions and decision-making.','Sek Socheat','../../uploads/documents/doc_11.pdf','pdf',4,1,'2025-01-16 06:08:44','2025-01-16 06:08:44'),(12,'Fundamentals of Computer Vision','The document provides a comprehensive overview of Computer Vision, a field that enables machines to interpret visual data, with applications ranging from object recognition and autonomous vehicles to medical imaging and augmented reality. It outlines the vision pipeline, which includes image acquisition, preprocessing, and feature extraction techniques, such as edge detection (Sobel and Canny), keypoint detection (SIFT, SURF, ORB), and Histogram of Oriented Gradients (HOG). Additionally, it features practical coding activities in Python using libraries like OpenCV, enabling hands-on experience with these concepts.','Sek Socheat','../../uploads/documents/doc_12.pdf','pdf',4,1,'2025-01-16 06:10:07','2025-01-16 06:10:07'),(13,'Machine Learning Integration in Intelligence Systems','The document examines the integration of Machine Learning (ML) into Intelligent Systems (IS) to enhance decision-making. IS, which emulates human decision-making using knowledge bases and inference engines, is widely applied in fields like healthcare and business. ML enables systems to learn from data, with key discussions focusing on challenges in combining ML with traditional IS, the role of Reinforcement Learning (RL), and Adaptive Intelligence Systems that evolve with user feedback. Practical examples, such as an IT Helpdesk Chatbot, highlight how ML improves IS through sentiment analysis, personalized responses, and the importance of explainability and continuous updates.','Sek Socheat','../../uploads/documents/doc_13.pdf','pdf',4,1,'2025-01-16 06:14:37','2025-01-16 06:14:37');
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` VALUES (1,'Ly Heng','ly.heng@gmail.com','$2y$10$WdpQGFX6tTJO2W4580hrXul8VTohO4/BFtTH3vUZmB4swD7lMpRg.','superadmin','',0,'2025-01-16 02:39:37','2025-01-16 02:39:37'),(2,'Kim Sour','kim.sour@gamil.com','$2y$10$LIrHn6BDXdnyjV4Fo.IJ9OPa6xYH..DToNeLgd8QDV/va9vzaAI1a','admin','',0,'2025-01-16 03:16:33','2025-01-16 03:16:33'),(3,'Bun Sour','bun.sour@gmail.com','$2y$10$PkKW93uOFZiifLZ5r14Lm.NcRLTbXy9n4DcVRRcTrrhM5SSPusfAG','lecturer','',0,'2025-01-16 03:47:32','2025-01-16 03:47:32'),(4,'Lim Rathana','lim.rathana@gmail.com','$2y$10$9in/naEnuo.f4RWUklzLpO8rotG2sF5XnwJcUaU1X5/rTV..W4xPG','lecturer','',0,'2025-01-16 03:49:01','2025-01-16 03:49:01'),(5,'So Phat','so.phat@gmail.com','$2y$10$97IvXdoaBrSfO2rERe7Q4uJ5odt5/f38NDqno52DHe1CIeVSm/diy','student','',0,'2025-01-16 03:49:29','2025-01-16 03:49:29'),(6,'Kim Vichea','kim.vichea@gmail.com','$2y$10$RfHKKOihE7aGRVR19gSqb.Wd54UjPD27bsYxYz3YClbBWtEIAJKL6','student','',0,'2025-01-16 03:50:08','2025-01-16 03:50:08'),(7,'Heng Sinath','heng.sinath@gmail.com','$2y$10$5veRbWUzL72R/Qc.bApVVusX95AAcQBTtEoKIaHYYuBxWdVXKxujq','outsider','',0,'2025-01-16 03:54:19','2025-01-16 03:54:19');
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

-- Dump completed on 2025-01-16 14:14:01
