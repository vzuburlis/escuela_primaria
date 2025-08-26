/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: colegii_db
-- ------------------------------------------------------
-- Server version	10.6.22-MariaDB-0ubuntu0.22.04.1

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
-- Table structure for table `academic_comments`
--

DROP TABLE IF EXISTS `academic_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_level` int(10) unsigned DEFAULT NULL,
  `period_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index` (`grade_level`,`user_id`,`period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_comments`
--

LOCK TABLES `academic_comments` WRITE;
/*!40000 ALTER TABLE `academic_comments` DISABLE KEYS */;
INSERT INTO `academic_comments` VALUES (1,6,1,43,NULL,NULL,NULL),(2,6,1,42,'Mas practicar en matemáticas',NULL,NULL),(3,6,1,41,NULL,NULL,NULL),(4,6,1,40,NULL,NULL,NULL);
/*!40000 ALTER TABLE `academic_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_grade`
--

DROP TABLE IF EXISTS `academic_grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_level` int(10) unsigned DEFAULT NULL,
  `subject_id` int(10) unsigned DEFAULT NULL,
  `period_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `grade` decimal(6,2) DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index` (`grade_level`,`user_id`,`subject_id`,`period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_grade`
--

LOCK TABLES `academic_grade` WRITE;
/*!40000 ALTER TABLE `academic_grade` DISABLE KEYS */;
INSERT INTO `academic_grade` VALUES (1,6,1,1,43,8.00,NULL,NULL),(2,6,2,1,43,8.00,NULL,NULL),(3,6,3,1,43,9.00,NULL,NULL),(4,6,1,1,42,7.00,NULL,NULL),(5,6,2,1,42,8.00,NULL,NULL),(6,6,3,1,42,8.00,NULL,NULL),(7,6,1,1,41,9.00,NULL,NULL),(8,6,2,1,41,9.00,NULL,NULL),(9,6,3,1,41,9.00,NULL,NULL),(10,6,1,1,40,9.00,NULL,NULL),(11,6,2,1,40,9.00,NULL,NULL),(12,6,3,1,40,10.00,NULL,NULL);
/*!40000 ALTER TABLE `academic_grade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_period`
--

DROP TABLE IF EXISTS `academic_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_period`
--

LOCK TABLES `academic_period` WRITE;
/*!40000 ALTER TABLE `academic_period` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_period` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_subject`
--

DROP TABLE IF EXISTS `academic_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `grade_level` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_subject`
--

LOCK TABLES `academic_subject` WRITE;
/*!40000 ALTER TABLE `academic_subject` DISABLE KEYS */;
INSERT INTO `academic_subject` VALUES (1,'Matematicas',6),(2,'Fisica',6),(3,'Lectura',6);
/*!40000 ALTER TABLE `academic_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_year`
--

DROP TABLE IF EXISTS `academic_year`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `periods` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_year`
--

LOCK TABLES `academic_year` WRITE;
/*!40000 ALTER TABLE `academic_year` DISABLE KEYS */;
INSERT INTO `academic_year` VALUES (1,'2025-2026',3);
/*!40000 ALTER TABLE `academic_year` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `block`
--

DROP TABLE IF EXISTS `block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(80) DEFAULT NULL,
  `instances` int(10) unsigned DEFAULT 1,
  `data` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `block`
--

LOCK TABLES `block` WRITE;
/*!40000 ALTER TABLE `block` DISABLE KEYS */;
INSERT INTO `block` VALUES (1,'contacto',1,'{\"_type\":\"text\",\"text\":\"<div class=\\\"text-white p-1\\\" style=\\\"text-align: left;\\\"><h4 class=\\\"el-h4 text-white\\\">Contacto<\\/h4><p>Av. Michoacán 64, Guadalupe del Moral<br>Iztapalapa, C.P. 09300<br>Ciudad de México<\\/p><p> <\\/p><div class=\\\"el-spacer\\\" style=\\\"min-height: 1em;\\\"><\\/div><\\/div><div class=\\\"text-white p-1\\\"><iframe src=\\\"https:\\/\\/maps.google.com\\/maps?width=100%&amp;height=500&amp;hl=en&amp;q=Ciudad%20de%20Mexico&amp;ie=UTF8&amp;t=p&amp;z=16&amp;iwloc=B&amp;output=embed\\\" width=\\\"100%\\\" height=\\\"300px\\\" data-address=\\\"Ciudad de Mexico\\\" class=\\\"el-iframe el-map\\\" style=\\\"vertical-align: middle; border: none;\\\"><\\/iframe><\\/div>\",\"text-align\":\"\",\"justify-items\":\"\",\"align-items\":\"\",\"padding\":\"\",\"grid\":\"\",\"gap\":\"1\",\"hide-grid\":\"\",\"container-class\":\"\",\"container-mw\":\"\",\"is-form\":\"\",\"bg-color\":\"var(--p1color)\",\"padding-top\":\"30px\",\"padding-bottom\":\"30px\",\"id\":\"contacto\"}');
/*!40000 ALTER TABLE `block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blockslog`
--

DROP TABLE IF EXISTS `blockslog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blockslog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(80) DEFAULT NULL,
  `content_id` varchar(80) DEFAULT NULL,
  `draft` tinyint(4) DEFAULT 0,
  `created` int(11) DEFAULT NULL,
  `blocks` text DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `content` (`content`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `isCompany` tinyint(4) DEFAULT 0,
  `isArchived` tinyint(4) DEFAULT 0,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contactmeta`
--

DROP TABLE IF EXISTS `contactmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contactmeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) DEFAULT NULL,
  `metakey` varchar(80) DEFAULT NULL,
  `metavalue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactmeta`
--

LOCK TABLES `contactmeta` WRITE;
/*!40000 ALTER TABLE `contactmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `contactmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contentlog`
--

DROP TABLE IF EXISTS `contentlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contentlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) DEFAULT NULL,
  `content_id` int(10) unsigned DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `content` (`content`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contentlog`
--

LOCK TABLES `contentlog` WRITE;
/*!40000 ALTER TABLE `contentlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `contentlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_log`
--

DROP TABLE IF EXISTS `event_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(30) DEFAULT NULL,
  `user_id` int(11) DEFAULT 0,
  `data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_log`
--

LOCK TABLES `event_log` WRITE;
/*!40000 ALTER TABLE `event_log` DISABLE KEYS */;
INSERT INTO `event_log` VALUES (1,'2025-08-24 18:01:12','user.logout',1,'[]'),(2,'2025-08-24 18:05:51','user.login',1,'[]'),(3,'2025-08-25 04:18:49','user.login',1,'[]');
/*!40000 ALTER TABLE `event_log` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `file_tag`
--

DROP TABLE IF EXISTS `file_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `file_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned DEFAULT NULL,
  `tag` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_tag`
--

LOCK TABLES `file_tag` WRITE;
/*!40000 ALTER TABLE `file_tag` DISABLE KEYS */;
INSERT INTO `file_tag` VALUES (1,1,'logo2'),(2,2,'logo_icon'),(3,3,'ChatGPT Image 24 ago 2025, 11_52_05');
/*!40000 ALTER TABLE `file_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inv_log`
--

DROP TABLE IF EXISTS `inv_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inv_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `qty` int(11) DEFAULT 0,
  `user_id` int(10) unsigned DEFAULT NULL,
  `sku_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inv_log`
--

LOCK TABLES `inv_log` WRITE;
/*!40000 ALTER TABLE `inv_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `inv_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_type` varchar(255) DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL,
  `target_type` varchar(255) DEFAULT NULL,
  `target_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ind_source` (`source_type`,`source_id`),
  KEY `ind_target` (`target_type`,`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
/*!40000 ALTER TABLE `links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu` varchar(250) DEFAULT NULL,
  `data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu` (`menu`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'mainmenu','{\"type\":\"menu\",\"children\":[{\"type\":\"link\",\"url\":\"\",\"name\":\"Inicio\",\"title\":\"Inicio\",\"id\":\"\"},{\"type\":\"link\",\"title\":\"Contacto\",\"url\":\"/#contacto\"},{\"type\":\"link\",\"title\":\"Calificaciones\",\"url\":\"student_ogrades\"}]}');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metadata`
--

DROP TABLE IF EXISTS `metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) DEFAULT NULL,
  `metakey` varchar(255) DEFAULT NULL,
  `metavalue` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metadata`
--

LOCK TABLES `metadata` WRITE;
/*!40000 ALTER TABLE `metadata` DISABLE KEYS */;
INSERT INTO `metadata` VALUES (1,1,'page_group_id','0');
/*!40000 ALTER TABLE `metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metafield`
--

DROP TABLE IF EXISTS `metafield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `metafield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `fkey` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `span` varchar(255) DEFAULT NULL,
  `show_value` tinyint(4) DEFAULT 1,
  `after` varchar(255) DEFAULT NULL,
  `csv` tinyint(4) DEFAULT 1,
  `maxlength` smallint(5) unsigned DEFAULT 255,
  PRIMARY KEY (`id`),
  KEY `content` (`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metafield`
--

LOCK TABLES `metafield` WRITE;
/*!40000 ALTER TABLE `metafield` DISABLE KEYS */;
/*!40000 ALTER TABLE `metafield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metafield_option`
--

DROP TABLE IF EXISTS `metafield_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `metafield_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metafield_id` int(10) unsigned DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `metafield_id` (`metafield_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metafield_option`
--

LOCK TABLES `metafield_option` WRITE;
/*!40000 ALTER TABLE `metafield_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `metafield_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_type`
--

DROP TABLE IF EXISTS `notification_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `email` tinyint(4) DEFAULT 0,
  `autoremove` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_type`
--

LOCK TABLES `notification_type` WRITE;
/*!40000 ALTER TABLE `notification_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `option`
--

DROP TABLE IF EXISTS `option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option` varchar(255) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `option` (`option`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `option`
--

LOCK TABLES `option` WRITE;
/*!40000 ALTER TABLE `option` DISABLE KEYS */;
INSERT INTO `option` VALUES (25,'permissions','{\"2\":[\"teacher\",\"enter_grades\"],\"4\":[\"student\"],\"3\":[\"parent\",\"see_grades\"],\"5\":[\"edit_subjects\",\"edit_students\",\"edit_teachers\",\"edit_parents\",\"writer\"],\"6\":[\"edit_subjects\",\"edit_students\",\"edit_teachers\",\"edit_parents\",\"writer\"],\"1\":[\"admin\",\"admin_user\",\"admin_userrole\"]}'),(26,'title','Colegio'),(27,'description',''),(28,'admin_email','v.zuburlis@gmail.com'),(29,'timezone','America/Mexico_City'),(30,'language','es'),(31,'admin_logo','assets/umedia/3f526ebdb6d866a023a762a5a77458.png'),(32,'login_logo','assets/umedia/3f526ebdb6d866a023a762a5a77458.png'),(33,'favicon','assets/umedia/ba6a350f0ffc5c1cdf55964689f1e4.png'),(34,'packages','{\"3\":\"academy\"}'),(35,'academy.student_role','4'),(36,'academy.teacher_role','2'),(37,'academy.parent_role','3'),(38,'academy.student_ogrades','2'),(39,'theme.selectedColors','1'),(40,'theme.primary-color','#137000'),(41,'theme.accent-color','#cc000a'),(42,'theme.heading-color','cc000a'),(43,'theme.body-color','#242424'),(44,'theme.page-background-color','#FFFFFF'),(45,'theme.logo','assets/umedia/3f526ebdb6d866a023a762a5a77458.png'),(46,'theme.logo-size','50px'),(47,'theme.title',''),(48,'theme.title-color','var(--hcolor)'),(49,'theme.selectedFonts','1'),(50,'theme.heading-font','Roboto Condensed'),(51,'theme.heading-color','cc000a'),(52,'theme.body-font','Roboto'),(53,'theme.body-color','#242424'),(54,'theme.heading-font','Roboto'),(55,'theme.heading-color','cc000a'),(56,'theme.body-font','Roboto'),(57,'theme.body-color','#242424');
/*!40000 ALTER TABLE `option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `publish` tinyint(4) DEFAULT 0,
  `template` varchar(30) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `meta_robots` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blocks` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`),
  KEY `publish` (`publish`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page`
--

LOCK TABLES `page` WRITE;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` VALUES (1,'Escuela Primaria','','','assets/umedia/ba6a350f0ffc5c1cdf55964689f1e4.png',1,'','es','','2025-08-25 04:13:21','[{\"_type\":\"text\",\"text\":\"<div class=\\\"selected-component p-1\\\"><\\/div><div class=\\\"selected-component p-1\\\"><\\/div><div class=\\\"p-2 d-none d-lg-block\\\" style=\\\"background-color: rgb(var(--p1color)); text-align: left;\\\"><h2 class=\\\"el-h2 text-white\\\" style=\\\"text-align: left;\\\">Bienvenidos a\\u00a0nuestro colegio<\\/h2><div class=\\\"el-spacer\\\" style=\\\"min-height: 1em;\\\"><\\/div><p class=\\\"text-white\\\" style=\\\"\\\">Somos una instituci\\u00f3n educativa en Ciudad de M\\u00e9xico, comprometida con la formaci\\u00f3n integral de los ni\\u00f1os y ni\\u00f1as, enfoc\\u00e1ndonos en su desarrollo acad\\u00e9mico, personal y social.<\\/p><\\/div><div class=\\\"p-2 d-lg-none selected-component\\\" style=\\\"background-color: rgb(var(--p1color)); text-align: left; position: static; width: 100%; margin-top: 160px; min-height: 32vh; margin-bottom: -100px;\\\"><h2 class=\\\"el-h2 text-white\\\" style=\\\"text-align: left;\\\">Bienvenidos nuestro Colegio<\\/h2><div class=\\\"el-spacer\\\" style=\\\"min-height: 1em;\\\"><\\/div><p class=\\\"text-white\\\" style=\\\"\\\">Somos una instituci\\u00f3n educativa en Ciudad de M[exico, comprometida con la formaci\\u00f3n integral de los ni\\u00f1os, enfoc\\u00e1ndonos en su desarrollo acad\\u00e9mico, personal y social.<\\/p><\\/div>\",\"background\":\"assets\\/umedia\\/7e1ac4c13bd0c9641c0b8e430815a5.png\",\"padding-top\":\"100px\",\"padding-bottom\":\"100px\",\"positionY\":\"center\",\"bg-color\":\"var(--p5color)\",\"background-color\":\"\",\"alfa\":\"0.3\",\"text-align\":\"center\",\"animation\":\"move-up\",\"display\":\"\"},{\"_type\":\"text@core\",\"text\":\"<div class=\\\"p-4 shadow\\\" style=\\\"background-color: rgb(249, 251, 199);\\\"><div data-svg=\\\"assets\\/app\\/icons\\/map.svg\\\" data-ihref=\\\"\\\" class=\\\"el-svg\\\" style=\\\"display: inline-block; color: rgb(var(--p2color));\\\"><svg xmlns=\\\"http:\\/\\/www.w3.org\\/2000\\/svg\\\" width=\\\"80\\\" height=\\\"null\\\" viewbox=\\\"0 0 24 24\\\" fill=\\\"none\\\" stroke=\\\"currentColor\\\" stroke-width=\\\"2\\\" stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\">\\n  <polygon points=\\\"1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6\\\"><\\/polygon>\\n  <line x1=\\\"8\\\" y1=\\\"2\\\" x2=\\\"8\\\" y2=\\\"18\\\"><\\/line>\\n  <line x1=\\\"16\\\" y1=\\\"6\\\" x2=\\\"16\\\" y2=\\\"22\\\"><\\/line>\\n<\\/svg>\\n<\\/div><h2 style=\\\"\\\">Mision<\\/h2><div class=\\\"el-spacer\\\" style=\\\"min-height: 1em;\\\"><\\/div><p>Nuestra misi\\u00f3n es brindar una educaci\\u00f3n de calidad que promueva el respeto, la creatividad y el aprendizaje continuo en un ambiente seguro y acogedor.<\\/p><\\/div><div class=\\\"p-4 selected-component shadow\\\" style=\\\"background-color: rgb(249, 251, 199);\\\"><div data-svg=\\\"assets\\/app\\/icons\\/loader.svg\\\" data-ihref=\\\"\\\" class=\\\"el-svg\\\" style=\\\"display: inline-block; opacity: 1; color: rgb(var(--p2color));\\\"><svg xmlns=\\\"http:\\/\\/www.w3.org\\/2000\\/svg\\\" width=\\\"80\\\" height=\\\"null\\\" viewbox=\\\"0 0 24 24\\\" fill=\\\"none\\\" stroke=\\\"currentColor\\\" stroke-width=\\\"2\\\" stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\">\\n  <line x1=\\\"12\\\" y1=\\\"2\\\" x2=\\\"12\\\" y2=\\\"6\\\"><\\/line>\\n  <line x1=\\\"12\\\" y1=\\\"18\\\" x2=\\\"12\\\" y2=\\\"22\\\"><\\/line>\\n  <line x1=\\\"4.93\\\" y1=\\\"4.93\\\" x2=\\\"7.76\\\" y2=\\\"7.76\\\"><\\/line>\\n  <line x1=\\\"16.24\\\" y1=\\\"16.24\\\" x2=\\\"19.07\\\" y2=\\\"19.07\\\"><\\/line>\\n  <line x1=\\\"2\\\" y1=\\\"12\\\" x2=\\\"6\\\" y2=\\\"12\\\"><\\/line>\\n  <line x1=\\\"18\\\" y1=\\\"12\\\" x2=\\\"22\\\" y2=\\\"12\\\"><\\/line>\\n  <line x1=\\\"4.93\\\" y1=\\\"19.07\\\" x2=\\\"7.76\\\" y2=\\\"16.24\\\"><\\/line>\\n  <line x1=\\\"16.24\\\" y1=\\\"7.76\\\" x2=\\\"19.07\\\" y2=\\\"4.93\\\"><\\/line>\\n<\\/svg>\\n<\\/div><h2 style=\\\"\\\">Visi\\u00f3n<\\/h2><div class=\\\"el-spacer\\\" style=\\\"min-height: 1em;\\\"><\\/div><p>Ser una instituci\\u00f3n l\\u00edder en la regi\\u00f3n, reconocida por su excelencia educativa y su compromiso con el desarrollo de cada estudiante.<\\/p><\\/div>\",\"text-align\":\"center\",\"justify-items\":\"\",\"align-items\":\"\",\"padding\":\"\",\"grid\":\"\",\"gap\":\"1\",\"hide-grid\":\"\",\"container-class\":\"\",\"container-mw\":\"\",\"is-form\":\"\",\"padding-top\":\"59px\",\"padding-bottom\":\"68px\",\"animation\":\"fade-in\"},{\"_type\":\"text\",\"text\":\"<div class=\\\"p-1 selected-component\\\" style=\\\"\\\">\\r\\n   \\r\\n   <div data-ihref=\\\"\\\" class=\\\"el-btnimage w-75\\\" style=\\\"display: inline-block; overflow: hidden; margin-right: auto; margin-bottom: auto; border-color: rgb(var(--p5color)); border-style: solid; border-width: 8px; min-width: 100%;\\\"><img src=\\\"assets\\/umedia\\/ba6a350f0ffc5c1cdf55964689f1e4.png\\\" alt=\\\"\\\"><\\/div><div data-ihref=\\\"\\\" class=\\\"m-auto el-divimage\\\" style=\'overflow: hidden; background-image: url(\\\"assets\\/umedia\\/66a2c945b855369d54347616eb1284.jpeg\\\"); background-size: cover; background-position: center center; display: inline-block; margin: auto;\'><div style=\\\"height:100%;width:100%\\\"><\\/div><\\/div>\\r\\n   \\r\\n<\\/div>\\r\\n<div class=\\\"p-1 text-white\\\">\\r\\n   <h2 class=\\\"el-h2 text-white\\\" style=\\\"opacity: 1;\\\">Sobre nosotros<\\/h2><div class=\\\"el-divider\\\" style=\\\"margin: 10px auto; height: 3px; width: 100%;\\\"><svg stroke=\\\"currentColor\\\" style=\\\"width:100%;overflow:hidden\\\"><line x1=\\\"0\\\" y1=\\\"0\\\" x2=\\\"2000\\\" y2=\\\"1\\\"><\\/line><\\/svg><\\/div><p class=\\\"el-p\\\" style=\\\"\\\">En nuestra instituci\\u00f3n fomentamos el respeto, la responsabilidad, la solidaridad y el compromiso como pilares de una educaci\\u00f3n integral.<\\/p><p class=\\\"el-p\\\" style=\\\"\\\">Ofrecemos una educaci\\u00f3n integral para todos los niveles de primaria, con un enfoque en el desarrollo de habilidades acad\\u00e9micas, art\\u00edsticas y deportivas.<\\/p>\\r\\n   \\r\\n  \\r\\n   \\r\\n  \\r\\n   \\r\\n<\\/div>\",\"justify-items\":\"center\",\"padding-top\":\"30px\",\"padding-bottom\":\"30px\",\"bg-color\":\"var(--p1color)\",\"grid\":\"1f\",\"align-items\":\"end\"},{\"_type\":\"text@core\",\"text\":\"<div><h2 style=\\\"\\\">Calendario escolar 2024-2025<\\/h2><div class=\\\"el-spacer\\\" style=\\\"min-height: 1em;\\\"><\\/div><div data-ihref=\\\"\\\" class=\\\"el-btnimage\\\" style=\\\"display: inline-block; overflow: hidden;\\\"><img src=\\\"assets\\/umedia\\/5febd29faf96cc57a2e191e2128ced.png\\\" alt=\\\"\\\"><\\/div><div data-ihref=\\\"\\\" class=\\\"el-btnimage\\\" style=\\\"display: inline-block; overflow: hidden;\\\"><img src=\\\"assets\\/umedia\\/f7874d4c1c7bbea12f603e8b12fb3c.png\\\" alt=\\\"\\\"><\\/div><\\/div>\",\"text-align\":\"center\",\"justify-items\":\"\",\"align-items\":\"\",\"padding\":\"\",\"grid\":\"\",\"gap\":\"1\",\"hide-grid\":\"\",\"container-class\":\"\",\"container-mw\":\"\",\"is-form\":\"\",\"bg-color\":\"\",\"padding-top\":\"30px\",\"padding-bottom\":\"30px\"},{\"_type\":\"text\",\"text\":\"<div class=\\\"text-white p-1\\\" style=\\\"text-align: left;\\\"><h4 class=\\\"el-h4 text-white\\\">Contacto<\\/h4><p>Av. Michoac\\u00e1n 64, Guadalupe del Moral<br>Iztapalapa, C.P. 09300<br>Ciudad de M\\u00e9xico<\\/p><p>\\u00a0<\\/p><div class=\\\"el-spacer\\\" style=\\\"min-height: 1em;\\\"><\\/div><\\/div><div class=\\\"text-white p-1\\\"><iframe src=\\\"https:\\/\\/maps.google.com\\/maps?width=100%&amp;height=500&amp;hl=en&amp;q=Ciudad%20de%20Mexico&amp;ie=UTF8&amp;t=p&amp;z=16&amp;iwloc=B&amp;output=embed\\\" width=\\\"100%\\\" height=\\\"300px\\\" data-address=\\\"Ciudad de Mexico\\\" class=\\\"el-iframe el-map\\\" style=\\\"vertical-align: middle; border: none;\\\"><\\/iframe><\\/div>\",\"text-align\":\"\",\"justify-items\":\"\",\"align-items\":\"\",\"padding\":\"\",\"grid\":\"\",\"gap\":\"1\",\"hide-grid\":\"\",\"container-class\":\"\",\"container-mw\":\"\",\"is-form\":\"\",\"bg-color\":\"var(--p1color)\",\"padding-top\":\"30px\",\"padding-bottom\":\"30px\",\"id\":\"contacto\"}]');
/*!40000 ALTER TABLE `page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_method`
--

DROP TABLE IF EXISTS `payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(120) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `cost` decimal(6,2) DEFAULT 0.00,
  `cost_f` decimal(6,2) DEFAULT 0.00,
  `gateway` varchar(30) DEFAULT NULL,
  `pos` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_method`
--

LOCK TABLES `payment_method` WRITE;
/*!40000 ALTER TABLE `payment_method` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `publish` tinyint(4) DEFAULT 0,
  `post` text DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `publish_at` int(10) unsigned DEFAULT NULL,
  `blocks` text DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`),
  KEY `publish` (`publish`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `title` (`title`,`post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postcategory`
--

DROP TABLE IF EXISTS `postcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `postcategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `featured` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postcategory`
--

LOCK TABLES `postcategory` WRITE;
/*!40000 ALTER TABLE `postcategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `postcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_key`
--

DROP TABLE IF EXISTS `product_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_key` varchar(20) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_key`
--

LOCK TABLES `product_key` WRITE;
/*!40000 ALTER TABLE `product_key` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_key` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `redirect`
--

DROP TABLE IF EXISTS `redirect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_slug` varchar(255) DEFAULT NULL,
  `to_slug` varchar(255) DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `redirect`
--

LOCK TABLES `redirect` WRITE;
/*!40000 ALTER TABLE `redirect` DISABLE KEYS */;
/*!40000 ALTER TABLE `redirect` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `gsessionid` varchar(120) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gsessionid` (`gsessionid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `table_options`
--

DROP TABLE IF EXISTS `table_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `table_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(120) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT 0,
  `data` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `table_options`
--

LOCK TABLES `table_options` WRITE;
/*!40000 ALTER TABLE `table_options` DISABLE KEYS */;
INSERT INTO `table_options` VALUES (1,'usergroup',1,'{\"id\":{\"show\":true},\"logo\":{\"show\":false},\"usergroup\":{\"show\":true},\"description\":{\"show\":false},\"users\":{\"show\":true},\"expire_at\":{\"show\":false}}');
/*!40000 ALTER TABLE `table_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tableschema`
--

DROP TABLE IF EXISTS `tableschema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tableschema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `data` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tableschema`
--

LOCK TABLES `tableschema` WRITE;
/*!40000 ALTER TABLE `tableschema` DISABLE KEYS */;
/*!40000 ALTER TABLE `tableschema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template`
--

DROP TABLE IF EXISTS `template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(80) DEFAULT NULL,
  `event` varchar(30) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blocks` text DEFAULT '[{"_type":"text","text":"<div><p>Hello {{user.username}}<br><br>This is an html message, you can start editing it</p></div>"}]',
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template`
--

LOCK TABLES `template` WRITE;
/*!40000 ALTER TABLE `template` DISABLE KEYS */;
/*!40000 ALTER TABLE `template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `pass` varchar(120) DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `language` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Vasilis Zoumpourlis','v.zuburlis@gmail.com','$2y$10$J.rr4SWCUuQ3cNFzFn50qOebdxs3RdzSNTxt/JzJEN0/.XxxZhLRS',1,'2024-10-26 18:11:07','2025-08-24 18:05:51',NULL),(19,'Sophia Ramirez','sophia.ramirez88@example.net','$2y$10$JKlmnOPqrsTUVwxYZ01AbcDEfgHIJklmNOPqrSTuvWXyZaBCdeFgh',1,'2024-11-04 14:00:08','2024-11-18 23:05:30',NULL),(20,'Ethan Johnson','ethan.johnson77@example.org','$2y$10$ZyxWVutSRqPonmLKjiHgfeDCba098zyxWVUTsrQPOlkJIHgfeDCba',1,'2024-11-04 14:00:36','2024-11-16 00:56:38',NULL),(21,'Ava Martinez','ava.martinez55@example.com',NULL,1,'2024-11-05 03:06:27','2024-11-05 03:06:27',NULL),(22,'Noah Smith','noah.smith22@example.net',NULL,1,'2024-11-05 03:06:52','2024-11-05 03:06:52',NULL),(23,'Isabella Brown','isabella.brown44@example.org',NULL,1,'2024-11-05 03:07:50','2024-11-05 03:07:50',NULL),(24,'Mason Wilson','mason.wilson66@example.com',NULL,1,'2024-11-05 03:08:01','2024-11-05 03:08:01',NULL),(25,'Olivia Davis','olivia.davis33@example.net','',1,'2024-11-05 03:08:12','2025-08-25 03:26:45',NULL),(26,'James Anderson','james.anderson11@example.org','',1,'2024-11-05 03:08:28','2025-08-25 03:26:41',NULL),(27,'Amelia Thompson','amelia.thompson77@example.com','',1,'2024-11-05 03:08:39','2024-11-05 03:10:24',NULL),(28,'Benjamin Taylor','benjamin.taylor88@example.net','',1,'2024-11-05 03:09:02','2025-08-25 03:26:31',NULL),(29,'Charlotte White','charlotte.white99@example.org','',1,'2024-11-05 03:09:11','2025-08-25 03:26:26',NULL),(30,'Lucas Harris','lucas.harris21@example.com','',1,'2024-11-05 03:09:21','2025-08-25 03:26:20',NULL),(31,'Mia Lewis','mia.lewis62@example.net','$2y$10$QrstuvWXYZaBCdefGHIjkLMnopQRStuVWxyzaBCDEFGHIJklmnOPq',1,'2024-11-05 03:09:36','2024-11-19 15:45:18',NULL),(32,'Alexander Walker','alex.walker19@example.org','$2y$10$MnOPQRstuVWxyZabCDEfgHIJKlmnoPQRStUvwxYzaBcDeFghIjKLm',1,'2024-11-05 03:09:45','2024-11-14 16:03:37',NULL),(33,'Hernandez/Perez Samuel',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(34,'Johnson/Moore Emily',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(35,'Davis/Garcia Matthew',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(36,'Martinez/Lopez Abigail',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(37,'Robinson/Young Daniel',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(38,'Allen/King Grace',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(39,'Scott/Hill Jacob',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(40,'Torres/Cruz Sofia',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(41,'Green/Bailey Anthony',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(42,'Nelson/Perry Lily',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(43,'Mitchell/Rivera Chloe',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(44,'Adams/Cook Elijah',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(45,'Phillips/Howard Victoria',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(46,'Stewart/Hughes Gabriel',NULL,NULL,1,'2024-11-13 16:39:03','2024-11-13 16:39:03',NULL),(47,'Bennett/Morris Zoey',NULL,NULL,1,'2024-11-13 16:47:04','2024-11-13 16:47:04',NULL),(48,'Collins/Powell Julian',NULL,NULL,1,'2024-11-13 16:47:04','2024-11-13 16:47:04',NULL),(49,'Flores/Ward Penelope',NULL,NULL,1,'2024-11-13 16:47:04','2024-11-13 16:47:04',NULL),(50,'Gonzalez/Butler Nathan',NULL,NULL,1,'2024-11-13 16:47:04','2024-11-13 16:47:04',NULL),(51,'Ramirez/Foster Aurora',NULL,NULL,1,'2024-11-13 16:47:04','2024-11-13 16:47:04',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_file`
--

DROP TABLE IF EXISTS `user_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_file`
--

LOCK TABLES `user_file` WRITE;
/*!40000 ALTER TABLE `user_file` DISABLE KEYS */;
INSERT INTO `user_file` VALUES (1,1,'assets/umedia/3f526ebdb6d866a023a762a5a77458.png',23293,'2025-08-25 04:03:50'),(2,1,'assets/umedia/ba6a350f0ffc5c1cdf55964689f1e4.png',13640,'2025-08-25 04:13:16'),(3,1,'assets/umedia/7e1ac4c13bd0c9641c0b8e430815a5.png',2255006,'2025-08-25 03:55:09');
/*!40000 ALTER TABLE `user_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `expire_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
INSERT INTO `user_group` VALUES (1,51,12,1766568900,1756092607,1756092607),(2,50,12,1766568900,1756092610,1756092610),(3,49,12,1766568900,1756092613,1756092613),(4,48,12,1766568900,1756092616,1756092616),(5,47,12,1766568900,1756092620,1756092620),(6,46,12,1766568900,1756092623,1756092623),(7,45,12,1766568900,1756092626,1756092626),(8,44,12,1766568900,1756092629,1756092629),(9,43,11,1766568900,1756092635,1756092635),(10,42,11,1766568900,1756092638,1756092638),(11,41,11,1766568900,1756092641,1756092641),(12,40,11,1766568900,1756092644,1756092644),(13,39,10,1766568900,1756092651,1756092651),(14,38,10,1766568900,1756092656,1756092656),(15,37,10,1766568900,1756092658,1756092658),(16,36,10,1766568900,1756092665,1756092665),(17,35,9,1766568900,1756092668,1756092668),(18,34,9,1766568900,1756092671,1756092671),(19,33,9,1766568900,1756092675,1756092675);
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notification`
--

DROP TABLE IF EXISTS `user_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `unread` tinyint(4) DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notification`
--

LOCK TABLES `user_notification` WRITE;
/*!40000 ALTER TABLE `user_notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usergroup`
--

DROP TABLE IF EXISTS `usergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usergroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) DEFAULT NULL,
  `usergroup` varchar(255) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `expire_at` int(10) unsigned DEFAULT NULL,
  `grade_level` tinyint(4) DEFAULT NULL,
  `academic_year_id` int(10) unsigned DEFAULT NULL,
  `teacher_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usergroup`
--

LOCK TABLES `usergroup` WRITE;
/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
INSERT INTO `usergroup` VALUES (1,'','1A','',1730008800,1,1,21),(2,'','1B','',1730008800,1,1,25),(3,'','2A','',1730008800,2,1,19),(4,'','2B','',1730008800,2,1,20),(5,NULL,'3A',NULL,1731477600,3,1,28),(6,NULL,'3B',NULL,1731477600,3,1,29),(7,NULL,'4A',NULL,1731477600,4,1,23),(8,NULL,'4B',NULL,1731477600,4,1,30),(9,NULL,'5A',NULL,1731477600,5,1,24),(10,NULL,'5B',NULL,1731477600,5,1,22),(11,NULL,'6A',NULL,1731477600,6,1,20),(12,NULL,'6B',NULL,1731477600,6,1,19);
/*!40000 ALTER TABLE `usergroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usermeta`
--

DROP TABLE IF EXISTS `usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usermeta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `vartype` varchar(80) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usermeta`
--

LOCK TABLES `usermeta` WRITE;
/*!40000 ALTER TABLE `usermeta` DISABLE KEYS */;
INSERT INTO `usermeta` VALUES (5,9,'role','4'),(6,10,'role','4'),(7,11,'role','4'),(8,12,'role','4'),(9,12,'group','3'),(10,13,'role','4'),(11,13,'group','3'),(12,14,'role','4'),(13,14,'group','3'),(14,15,'role','4'),(15,15,'group','3'),(16,16,'role','4'),(17,16,'group','3'),(18,17,'role','4'),(19,17,'group','3'),(20,18,'role','4'),(21,18,'group','3'),(22,8,'role','2'),(23,19,'role','2'),(25,20,'role','2'),(27,21,'role','2'),(29,22,'role','2'),(31,23,'role','2'),(33,24,'role','2'),(51,1,'role','1'),(52,1,'role','3'),(56,33,'role','4'),(57,33,'user.curp','ZAXM820101HDFRRL01'),(58,34,'role','4'),(59,34,'user.curp','MEJL830512MDFLNS02'),(60,35,'role','4'),(61,35,'user.curp','GARC840215HMCNNT03'),(62,36,'role','4'),(63,36,'user.curp','RODR850726HDFPRR04'),(64,37,'role','4'),(65,37,'user.curp','LOPE860919MDFCRS05'),(66,38,'role','4'),(67,38,'user.curp','GOME870304HNLVNL06'),(68,39,'role','4'),(70,40,'role','4'),(72,41,'role','4'),(74,42,'role','4'),(76,43,'role','4'),(78,44,'role','4'),(80,45,'role','4'),(82,46,'role','4'),(84,47,'role','4'),(86,48,'role','4'),(88,49,'role','4'),(90,50,'role','4'),(92,51,'role','4'),(94,30,'role','4'),(95,29,'role','4'),(96,28,'role','4'),(97,27,'role','4'),(98,26,'role','4'),(99,25,'role','4'),(100,51,'user.curp','LOPE000405HDFPNS19'),(101,51,'user.pin','1123'),(102,50,'user.curp','GARC990612MDFTRR18'),(103,50,'user.pin','5463'),(104,49,'user.curp','CLAV980922HDFLNS17'),(105,49,'user.pin','5578'),(106,48,'user.curp','BUST970503MDFCLS16'),(107,48,'user.pin','1634'),(108,47,'user.curp','SANT960729HDFRRL15'),(109,47,'user.pin','9846'),(110,46,'user.curp','ACEV950113MDFPNS14'),(111,46,'user.pin','3387'),(112,45,'user.curp','VILL940806HDFTRR13'),(113,45,'user.pin','1099'),(114,44,'user.curp','TORR930521MDFLNS12'),(115,44,'user.pin','4964'),(116,43,'user.curp','PENA920304HDFCLS11'),(117,43,'user.pin','2789'),(118,42,'user.curp','NEVA910218MDFRRL10'),(119,42,'user.pin','7445'),(120,41,'user.curp','MEDR900710HDFPNS09'),(121,41,'user.pin','2645'),(122,40,'user.curp','PERE890427MDFCLS08'),(123,40,'user.pin','6435'),(124,39,'user.curp','MART880618HDFTRR07'),(125,39,'user.pin','8845'),(126,1,'user_tutee','42');
/*!40000 ALTER TABLE `usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userrole`
--

DROP TABLE IF EXISTS `userrole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userrole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userrole` varchar(80) DEFAULT NULL,
  `level` tinyint(4) DEFAULT 1,
  `redirect_url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userrole`
--

LOCK TABLES `userrole` WRITE;
/*!40000 ALTER TABLE `userrole` DISABLE KEYS */;
INSERT INTO `userrole` VALUES (1,'Admin',10,NULL,NULL),(2,'Maestro',5,'','enter_grades'),(3,'Padre',1,'','student_grades'),(4,'Estudiante',0,'',NULL),(5,'Secretaria',6,'','admin-students'),(6,'Director',6,'','admin-students');
/*!40000 ALTER TABLE `userrole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widget`
--

DROP TABLE IF EXISTS `widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  `data` text DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `pos` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widget`
--

LOCK TABLES `widget` WRITE;
/*!40000 ALTER TABLE `widget` DISABLE KEYS */;
INSERT INTO `widget` VALUES (1,'core-counters','','dashboard',1,'[]',NULL,1),(2,'stats-chart','Page visits','dashboard',1,'{\"legend\":\"\",\"data\":\"web\"}',NULL,2),(3,'shop-category-list','Categorias','sidebar',1,'[]',NULL,1);
/*!40000 ALTER TABLE `widget` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-25 17:03:43
