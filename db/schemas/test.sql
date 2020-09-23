-- MySQL dump 10.13  Distrib 5.7.31, for Linux (x86_64)
--
-- Host: localhost    Database: easyrogs_test
-- ------------------------------------------------------
-- Server version	5.7.31-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''));
SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

--
-- Table structure for table `attorney`
--

DROP TABLE IF EXISTS `attorney`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attorney` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `case_id` int(11) NOT NULL,
  `attorney_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attorney_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fkaddressbookid` int(11) NOT NULL,
  `attorney_type` int(11) NOT NULL COMMENT '1: My team, 2: Service List, 3: Case Team',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=615 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attorneys_cases`
--

DROP TABLE IF EXISTS `attorneys_cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attorneys_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) NOT NULL,
  `attorney_id` bigint(20) NOT NULL COMMENT 'attorney_id = pkaddressbookid',
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=383 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_team`
--

DROP TABLE IF EXISTS `case_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fkcaseid` int(11) NOT NULL,
  `attorney_id` int(11) NOT NULL,
  `is_deleted` int(11) DEFAULT '0' COMMENT '1: Yes 0: No',
  `email_sent` int(11) DEFAULT '0' COMMENT '1: Yes 0: No',
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=574 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `case_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plaintiff` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `defendant` text COLLATE utf8mb4_unicode_ci,
  `case_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jurisdiction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `county_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `judge_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `court_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_filed` date DEFAULT NULL,
  `attorney_id` bigint(20) NOT NULL COMMENT 'Coming from Addressbook table',
  `allow_reminders` tinyint(4) NOT NULL COMMENT '1: Yes 2: No',
  `trial` date DEFAULT NULL,
  `discovery_cutoff` date DEFAULT NULL,
  `filed` date DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `is_draft` int(11) NOT NULL DEFAULT '1',
  `case_attorney` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `masterhead` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_attorney`
--

DROP TABLE IF EXISTS `client_attorney`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_attorney` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `attorney_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=516 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `case_id` bigint(20) unsigned NOT NULL,
  `other_attorney_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Only when client type = others',
  `other_attorney_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Only when client type = others',
  `other_attorney_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'id from case_attorney table',
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=386 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discoveries`
--

DROP TABLE IF EXISTS `discoveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discoveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discovery_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `uid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL COMMENT '2: Internal 1: External',
  `case_id` bigint(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `attorney_id` int(11) NOT NULL,
  `set_number` int(11) NOT NULL,
  `question_number_start_from` int(11) NOT NULL DEFAULT '0',
  `propounding` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proponding_attorney` int(11) NOT NULL COMMENT 'Comes when Internal discovery',
  `propounding_uid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responding` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responding_uid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `served` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discovery_instrunctions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `discovery_introduction` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `send_date` datetime DEFAULT NULL,
  `is_send` int(11) NOT NULL DEFAULT '0' COMMENT '1: Send to client 0: Not send yet',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `incidentoption` tinyint(4) NOT NULL COMMENT 'Field use for instruction',
  `incidenttext` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Field use for instruction',
  `personnames1` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Field use for instruction',
  `personnames2` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Field use for instruction',
  `pos_state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'EXTERNAL Disocvery only',
  `pos_text` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'EXTERNAL Disocvery only',
  `pos_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'EXTERNAL Disocvery only',
  `pos_updated_by` int(11) NOT NULL COMMENT 'EXTERNAL Disocvery only',
  `pos_updated_at` datetime NOT NULL COMMENT 'EXTERNAL Disocvery only',
  `is_served` tinyint(4) NOT NULL COMMENT 'EXTERNAL Disocvery only 1: Yes 0: ',
  `declaration_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `declaration_updated_by` int(11) NOT NULL,
  `declaration_updated_at` datetime NOT NULL,
  `in_conjunction` tinyint(4) NOT NULL COMMENT '1: Yes 0: No',
  `conjunction_with` int(11) NOT NULL COMMENT 'Discovery id which has conjunction with this discovery',
  `conjunction_setnumber` int(11) NOT NULL,
  `interogatory_type` tinyint(4) NOT NULL COMMENT '1 :General, 2: EMPLOYMENT',
  `is_final_draft_created` tinyint(4) NOT NULL COMMENT '1: Yes 0: No',
  `finaldraft_instruction` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dec_state` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dec_city` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parentid` int(11) NOT NULL COMMENT 'If parentid = 0 its parent',
  `grand_parent_id` int(11) NOT NULL,
  `response_create` tinyint(4) NOT NULL COMMENT '1: Yes 0: No',
  PRIMARY KEY (`id`),
  UNIQUE KEY `discovery_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=589 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discovery_questions`
--

DROP TABLE IF EXISTS `discovery_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discovery_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) NOT NULL,
  `discovery_id` int(11) NOT NULL,
  `extra_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6386 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) NOT NULL,
  `attorney_id` int(11) NOT NULL,
  `case_id` bigint(20) NOT NULL,
  `document_notes` text COLLATE utf8mb4_unicode_ci,
  `document_file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `discovery_id` bigint(20) NOT NULL,
  `fkresponse_id` int(11) NOT NULL COMMENT 'Response ID for RPDs Documents. Because in this documents are attached with response.',
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_log`
--

DROP TABLE IF EXISTS `email_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discovery_id` int(11) NOT NULL,
  `loggedin_id` int(11) NOT NULL,
  `email_subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email Subject',
  `send_from` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'From Email',
  `send_to` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Comma seprated emails',
  `email_salutation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_body` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Body of email',
  `email_bcc` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'BCC of this email',
  `email_cc` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CC of this email',
  `sender_type` int(11) NOT NULL COMMENT '1: from attorney side 2: From responding side',
  `receiver_type` int(11) NOT NULL COMMENT '1: Attorney, 2: Responding party',
  `sending_script` int(11) NOT NULL COMMENT '1: discoveryaction, 2:discoveryfrontaction',
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_area`
--

DROP TABLE IF EXISTS `faq_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faq_area` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `area_title` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faqs` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forms` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_form_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_instructions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `allow_custom_questions` tinyint(4) NOT NULL,
  `state_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `year` int(11) NOT NULL,
  `fkstateid` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instructions`
--

DROP TABLE IF EXISTS `instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructions` (
  `pkinstructionid` int(11) NOT NULL AUTO_INCREMENT,
  `show_on` varchar(255) NOT NULL COMMENT 'Label where we show that instructions',
  `title` text NOT NULL,
  `statute` varchar(100) NOT NULL,
  `placement` varchar(255) NOT NULL COMMENT 'top,left,right,bottom',
  PRIMARY KEY (`pkinstructionid`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invitations`
--

DROP TABLE IF EXISTS `invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attorney_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1: Active, 2:Expired',
  `link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=702 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs_queue`
--

DROP TABLE IF EXISTS `jobs_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8mb4_unicode_ci,
  `priority` tinyint(4) NOT NULL,
  `unique_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `is_taken` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb`
--

DROP TABLE IF EXISTS `kb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_id` int(10) unsigned NOT NULL DEFAULT '0',
  `issue` text COLLATE utf8mb4_unicode_ci,
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `solution` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb_kb_section`
--

DROP TABLE IF EXISTS `kb_kb_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_kb_section` (
  `kb_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`kb_id`,`section_id`),
  KEY `fk_id_idx` (`kb_id`),
  KEY `fk_section_id_idx` (`section_id`),
  CONSTRAINT `fk_kb_id` FOREIGN KEY (`kb_id`) REFERENCES `kb` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_section_id` FOREIGN KEY (`section_id`) REFERENCES `kb_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb_section`
--

DROP TABLE IF EXISTS `kb_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `membership_whitelist`
--

DROP TABLE IF EXISTS `membership_whitelist`;

--
-- Table structure for table `question_admit_results`
--

DROP TABLE IF EXISTS `question_admit_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_admit_results` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `discovery_question_id` int(11) NOT NULL,
  `question_admit_id` int(11) NOT NULL,
  `fkresponse_id` int(11) NOT NULL,
  `sub_answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `objection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question_admits`
--

DROP TABLE IF EXISTS `question_admits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_admits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_no` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question_types`
--

DROP TABLE IF EXISTS `question_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_type_id` int(11) NOT NULL,
  `parent_question_id` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Parent Question in case it is a sub-part',
  `form_id` int(11) NOT NULL DEFAULT '0',
  `discovery_id` bigint(20) NOT NULL DEFAULT '0',
  `question_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `have_main_question` int(11) NOT NULL DEFAULT '0' COMMENT '1:Yes,0:No,2:No Sub parts',
  `question_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_depended_parent` int(11) DEFAULT NULL COMMENT 'Other depends on it 1: Yes 0 No',
  `depends_on_question` int(11) DEFAULT NULL COMMENT 'Depended question show on the basis on this id ',
  `display_order` int(11) NOT NULL,
  `sub_part` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_pre_defined` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No, 1=Yes ... Pre-defined or user defined',
  `attorney_id` int(11) NOT NULL DEFAULT '0' COMMENT 'if created by attorney for a discovery then put addressbookid in it',
  `is_anserable` int(11) NOT NULL COMMENT '1: Yes 2: No (But subparts are)',
  `is_display` int(11) NOT NULL DEFAULT '1' COMMENT '1: Yes 2: No',
  `has_extra_text` tinyint(4) NOT NULL COMMENT '1: Yes 0:No',
  `extra_text_field_label` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2672 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `response_questions`
--

DROP TABLE IF EXISTS `response_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fkdiscovery_question_id` bigint(20) NOT NULL,
  `fkresponse_id` int(11) NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `answer_detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `objection` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'objection added by attr',
  `final_response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `final_response_updated_on` datetime NOT NULL,
  `answered_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4059 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `responsename` text NOT NULL,
  `fkdiscoveryid` int(11) NOT NULL,
  `isserved` int(11) NOT NULL COMMENT 'INTERNAL Disocvery: response served or not 1: Yes 0: No',
  `servedate` datetime NOT NULL COMMENT 'INTERNAL Disocvery: response serve date',
  `submitted_by` int(11) NOT NULL COMMENT 'Who submitted that response',
  `fkresponseid` int(11) NOT NULL COMMENT 'Id of parent response',
  `submit_date` datetime NOT NULL,
  `is_submitted` tinyint(4) NOT NULL COMMENT 'Submitted from front end 1: Yes 0: No',
  `verification_by_name` varchar(255) NOT NULL,
  `discovery_verification` text NOT NULL,
  `verification_state` varchar(255) NOT NULL,
  `verification_city` varchar(255) NOT NULL,
  `verification_signed_by` varchar(255) NOT NULL,
  `verification_datetime` datetime NOT NULL,
  `posstate` varchar(255) NOT NULL COMMENT 'INTERNAL Disocvery only',
  `poscity` varchar(255) NOT NULL COMMENT 'INTERNAL Disocvery only',
  `postext` text NOT NULL COMMENT 'INTERNAL Disocvery only',
  `posupdated_at` datetime NOT NULL COMMENT 'INTERNAL Disocvery only',
  `posupdated_by` int(11) NOT NULL COMMENT 'INTERNAL Disocvery only',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=256 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schema_migrations`
--

DROP TABLE IF EXISTS `schema_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schema_migrations` (
  `timestamp` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sides`
--

DROP TABLE IF EXISTS `sides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) unsigned NOT NULL,
  `masthead` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_attorney_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sides_cases1_idx` (`case_id`),
  KEY `fk_sides_attorney1_idx` (`primary_attorney_id`),
  CONSTRAINT `fk_sides_attorney1` FOREIGN KEY (`primary_attorney_id`) REFERENCES `attorney` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sides_cases1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sides_clients`
--

DROP TABLE IF EXISTS `sides_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sides_clients` (
  `side_id` int(11) NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`side_id`,`client_id`),
  KEY `fk_sides_has_clients_clients1_idx` (`client_id`),
  KEY `fk_sides_has_clients_sides1_idx` (`side_id`),
  CONSTRAINT `fk_sides_has_clients_clients1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sides_has_clients_sides1` FOREIGN KEY (`side_id`) REFERENCES `sides` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sides_users`
--

DROP TABLE IF EXISTS `sides_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sides_users` (
  `side_id` int(11) NOT NULL,
  `system_addressbook_id` int(11) NOT NULL,  
  PRIMARY KEY (`side_id`,`system_addressbook_id`),
  KEY `fk_sides_has_users_users1_idx` (`system_addressbook_id`),
  KEY `fk_sides_has_users_sides1_idx` (`side_id`),
  CONSTRAINT `fk_sides_has_users_sides1` FOREIGN KEY (`side_id`) REFERENCES `sides` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sides_has_users_users1` FOREIGN KEY (`system_addressbook_id`) REFERENCES `system_addressbook` (`pkaddressbookid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_action`
--

DROP TABLE IF EXISTS `system_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_action` (
  `pkactionid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fkactiontypeid` tinyint(4) NOT NULL,
  `actionlabel` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `acttionlabelherbew` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `actioncodecustom` text COLLATE utf8_unicode_ci NOT NULL,
  `fkscreenid` tinyint(3) unsigned NOT NULL,
  `sortorder` int(11) NOT NULL,
  `selection` tinyint(4) NOT NULL,
  `phpfile` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `childdiv` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `buttonclass` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `iconclass` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `confirmation` tinyint(4) NOT NULL DEFAULT '0',
  `actionparam` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkactionid`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_actiontype`
--

DROP TABLE IF EXISTS `system_actiontype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_actiontype` (
  `pkactiontypeid` int(11) NOT NULL AUTO_INCREMENT,
  `actiontypelabel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `actioncode` text COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkactiontypeid`),
  UNIQUE KEY `pkactiontypeid` (`pkactiontypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_activity`
--

DROP TABLE IF EXISTS `system_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_activity` (
  `pkactivityid` int(11) NOT NULL AUTO_INCREMENT,
  `fkaddressbookid` int(11) DEFAULT NULL,
  `what` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activitydatetime` datetime DEFAULT NULL,
  `oldrecord` text COLLATE utf8_unicode_ci,
  `newrecord` text COLLATE utf8_unicode_ci,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkactivityid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_activitytype`
--

DROP TABLE IF EXISTS `system_activitytype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_activitytype` (
  `pkactivitytypeid` int(11) NOT NULL AUTO_INCREMENT,
  `activitytype` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkactivitytypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_addressbook`
--

DROP TABLE IF EXISTS `system_addressbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_addressbook` (
  `pkaddressbookid` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `middlename` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `lastname` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `uid` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `latitude` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactperson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `street` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fkcountryid` int(11) DEFAULT NULL,
  `fkstateid` int(11) DEFAULT NULL,
  `fkadmittedstateid` int(11) NOT NULL,
  `barnumber` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fkcityid` int(11) DEFAULT NULL,
  `cityname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fkgroupid` int(11) DEFAULT NULL,
  `iscustomer` int(11) NOT NULL COMMENT '1=Lead,2=Customer',
  `organizationnumber` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uploadfile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `emailverified` tinyint(4) DEFAULT NULL,
  `agentpercentage` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agentshortcode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `passworduid` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signupdate` datetime DEFAULT NULL,
  `signupip` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `userimage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isblocked` int(11) NOT NULL COMMENT '0=Not blocked & 1=blocked',
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `companyname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `attorney_info` text COLLATE utf8_unicode_ci NOT NULL,
  `isclosed` tinyint(4) NOT NULL,
  `orignal_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `accountid` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Account ID from accounts ssytem',
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `last_signed_city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_signed_state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pkaddressbookid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_browser`
--

DROP TABLE IF EXISTS `system_browser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_browser` (
  `pkbrowserid` int(11) NOT NULL AUTO_INCREMENT,
  `browsername` varchar(255) CHARACTER SET latin1 NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkbrowserid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_chart`
--

DROP TABLE IF EXISTS `system_chart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_chart` (
  `pkchartid` int(11) NOT NULL AUTO_INCREMENT,
  `fkcharttypeid` int(11) NOT NULL,
  `chartname` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `charttitle` varchar(250) NOT NULL,
  `chartsubtitle` varchar(250) NOT NULL,
  `xaxis_staticname` varchar(250) DEFAULT NULL,
  `xaxis_queryname` varchar(250) DEFAULT NULL,
  `yaxis_staticname` varchar(250) DEFAULT NULL,
  `yaxis_queryname` varchar(250) DEFAULT NULL,
  `yaxis_staticdata` varchar(250) DEFAULT NULL,
  `yaxis_querydata` varchar(250) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkchartid`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_charttype`
--

DROP TABLE IF EXISTS `system_charttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_charttype` (
  `pkcharttypeid` int(11) NOT NULL AUTO_INCREMENT,
  `charttypename` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkcharttypeid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_city`
--

DROP TABLE IF EXISTS `system_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_city` (
  `pkcityid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fkstateid` bigint(20) NOT NULL,
  `cityname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `timezone` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `dmaId` varchar(255) CHARACTER SET latin1 NOT NULL,
  `citycode` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkcityid`,`fkstateid`)
) ENGINE=InnoDB AUTO_INCREMENT=42902 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_contactus`
--

DROP TABLE IF EXISTS `system_contactus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_contactus` (
  `pkcontactusid` int(11) NOT NULL AUTO_INCREMENT,
  `contactusphone` int(11) NOT NULL,
  `address` int(11) NOT NULL,
  `map` text COLLATE utf8_unicode_ci NOT NULL,
  `sendemailtoowner` tinyint(4) NOT NULL COMMENT '0=No & 1=Yes',
  `fkowneremailid` tinyint(4) NOT NULL,
  `sendthanksemail` tinyint(4) NOT NULL COMMENT '0=No & 1=Yes',
  `fkthanksemailid` tinyint(4) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkcontactusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_country`
--

DROP TABLE IF EXISTS `system_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_country` (
  `pkcountryid` bigint(20) NOT NULL AUTO_INCREMENT,
  `countryname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `fips104` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `iso2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `iso3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `ison` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `internet` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `capital` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `mapreference` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `nationalitysingular` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `nationalityplural` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `currencycode` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `population` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkcountryid`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_county`
--

DROP TABLE IF EXISTS `system_county`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_county` (
  `pkcountyid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `countyname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`pkcountyid`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_currency`
--

DROP TABLE IF EXISTS `system_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_currency` (
  `pkcurrencyid` int(11) NOT NULL AUTO_INCREMENT,
  `currency` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `currencyrate` float NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkcurrencyid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_email`
--

DROP TABLE IF EXISTS `system_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_email` (
  `pkemailid` int(11) NOT NULL AUTO_INCREMENT,
  `fromname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fromemail` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bodyhtml` text COLLATE utf8_unicode_ci NOT NULL,
  `bodytext` text COLLATE utf8_unicode_ci NOT NULL,
  `cc` text COLLATE utf8_unicode_ci NOT NULL,
  `bcc` text COLLATE utf8_unicode_ci NOT NULL,
  `purpose` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkemailid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_email_setting`
--

DROP TABLE IF EXISTS `system_email_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_email_setting` (
  `pkemailsettingid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fromname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fromemail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `bcc` text COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkemailsettingid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_emailmarker`
--

DROP TABLE IF EXISTS `system_emailmarker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_emailmarker` (
  `pkemailmarkerid` int(11) NOT NULL AUTO_INCREMENT,
  `fkemailid` int(11) NOT NULL,
  `fkmarkerid` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkemailmarkerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_errors`
--

DROP TABLE IF EXISTS `system_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_errors` (
  `pkerrorid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `errormsg` varchar(255) CHARACTER SET latin1 NOT NULL,
  `errormsgother` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `errortype` int(11) NOT NULL COMMENT '1:Info,2:success,3:Warning,4:Error',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkerrorid`)
) ENGINE=InnoDB AUTO_INCREMENT=337 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_field`
--

DROP TABLE IF EXISTS `system_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_field` (
  `pkfieldid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldlabel` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `fieldlabelherbew` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'For Herbew Language',
  `fieldname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `fkscreenid` smallint(5) unsigned NOT NULL,
  `sortorder` tinyint(4) NOT NULL,
  `iseditable` tinyint(4) NOT NULL COMMENT '0=No & 1=Yes',
  `dbfieldname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkfieldid`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_filter`
--

DROP TABLE IF EXISTS `system_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_filter` (
  `pkfilterid` int(11) NOT NULL AUTO_INCREMENT,
  `fkformfieldtypeid` int(11) NOT NULL,
  `filterlabelname` varchar(255) NOT NULL,
  `filterfieldname` varchar(200) NOT NULL,
  `filtervalue` varchar(255) NOT NULL,
  `filterlabel` varchar(255) NOT NULL,
  `filterselect` tinyint(4) NOT NULL COMMENT '1: Single, 2:Multiple',
  `filterquery` text NOT NULL,
  `updatedon` datetime NOT NULL,
  `updatedby` int(11) NOT NULL,
  `filterstatus` int(11) NOT NULL,
  `fkscreenid` int(11) NOT NULL,
  `filtersortorder` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkfilterid`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_form`
--

DROP TABLE IF EXISTS `system_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_form` (
  `pkformid` int(11) NOT NULL AUTO_INCREMENT,
  `formtitle` varchar(255) NOT NULL,
  `method` varchar(4) NOT NULL,
  `formaction` text NOT NULL,
  `isajax` tinyint(4) NOT NULL,
  `javascript` text NOT NULL,
  `formid` varchar(255) NOT NULL,
  `formname` varchar(255) NOT NULL,
  `cssclass` text NOT NULL,
  `cssinline` text NOT NULL,
  `enctype` varchar(255) NOT NULL,
  `querystring` text NOT NULL,
  `gridstyle` tinyint(4) NOT NULL,
  `redirecturl` text NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkformid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_formfield`
--

DROP TABLE IF EXISTS `system_formfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_formfield` (
  `pkformfieldid` int(11) NOT NULL AUTO_INCREMENT,
  `fkformid` int(11) NOT NULL,
  `fieldname` varchar(255) NOT NULL,
  `fieldid` varchar(255) NOT NULL,
  `cssclass` text NOT NULL,
  `style` text NOT NULL,
  `fieldjavascript` text NOT NULL,
  `label` varchar(255) NOT NULL,
  `instructions` text NOT NULL,
  `isrequired` tinyint(4) NOT NULL,
  `sqlquery` text NOT NULL,
  `queryfieldvalue` varchar(255) NOT NULL,
  `queryfieldlabel` varchar(255) NOT NULL,
  `dbfieldshow` int(11) NOT NULL,
  `fieldplaceholder` varchar(255) NOT NULL,
  `fkformfieldtypeid` int(11) NOT NULL,
  `fklisttypeid` int(11) NOT NULL,
  `recordFrom` int(11) NOT NULL DEFAULT '0',
  `fkformfieldid` int(11) NOT NULL COMMENT 'Field to be load onChange',
  `loadfilename` varchar(255) NOT NULL,
  `displayorder` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `isdependent` int(11) NOT NULL,
  `dependentfield` int(11) NOT NULL,
  `dependentfieldevent` varchar(255) NOT NULL,
  `showbydefault` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkformfieldid`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_formfieldoption`
--

DROP TABLE IF EXISTS `system_formfieldoption`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_formfieldoption` (
  `pkformfieldoptionid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldoptionvalue` varchar(255) NOT NULL,
  `fieldoptionlabel` varchar(255) NOT NULL,
  `fkformfieldid` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkformfieldoptionid`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_formfieldtype`
--

DROP TABLE IF EXISTS `system_formfieldtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_formfieldtype` (
  `pkformfieldtypeid` int(11) NOT NULL AUTO_INCREMENT,
  `formfieldlabel` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `formfieldtype` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL COMMENT '0: Active , 1:Inactive',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkformfieldtypeid`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_general_setting`
--

DROP TABLE IF EXISTS `system_general_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_general_setting` (
  `pkgeneralsettingid` int(11) NOT NULL AUTO_INCREMENT,
  `generalsettingname` varchar(255) NOT NULL,
  `howtoqalibrary` tinyint(4) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkgeneralsettingid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_groupaction`
--

DROP TABLE IF EXISTS `system_groupaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_groupaction` (
  `pkgroupactionid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fkgroupid` tinyint(3) unsigned NOT NULL,
  `fkactionid` smallint(5) unsigned NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkgroupactionid`)
) ENGINE=InnoDB AUTO_INCREMENT=513 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_groupfield`
--

DROP TABLE IF EXISTS `system_groupfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_groupfield` (
  `pkgroupfieldid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fkgroupid` tinyint(3) unsigned NOT NULL,
  `fkfieldid` smallint(5) unsigned NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkgroupfieldid`)
) ENGINE=InnoDB AUTO_INCREMENT=900 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_groups`
--

DROP TABLE IF EXISTS `system_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_groups` (
  `pkgroupid` int(11) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(45) DEFAULT NULL,
  `fkaddressbookid` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkgroupid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_groupscreen`
--

DROP TABLE IF EXISTS `system_groupscreen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_groupscreen` (
  `pkgroupscreenid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fkscreenid` smallint(5) unsigned NOT NULL,
  `fkgroupid` tinyint(3) unsigned NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkgroupscreenid`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_label`
--

DROP TABLE IF EXISTS `system_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_label` (
  `pklabelid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'This will serve as id',
  `fkformid` int(11) NOT NULL,
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `labelhebrew` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'label for Hebrew',
  `sortorder` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pklabelid`)
) ENGINE=InnoDB AUTO_INCREMENT=419 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_list`
--

DROP TABLE IF EXISTS `system_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_list` (
  `pklistid` int(11) NOT NULL AUTO_INCREMENT,
  `listname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `fklisttypeid` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pklistid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_listtype`
--

DROP TABLE IF EXISTS `system_listtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_listtype` (
  `pklisttypeid` int(11) NOT NULL AUTO_INCREMENT,
  `listtypename` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pklisttypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_login`
--

DROP TABLE IF EXISTS `system_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_login` (
  `pklogin_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(222) NOT NULL,
  `login_email` varchar(222) NOT NULL,
  `login_pswrd` varchar(222) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pklogin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_marker`
--

DROP TABLE IF EXISTS `system_marker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_marker` (
  `pkmarkerid` int(11) NOT NULL AUTO_INCREMENT,
  `marker` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `replacement` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkmarkerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_message`
--

DROP TABLE IF EXISTS `system_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_message` (
  `pksysmessageid` int(11) NOT NULL AUTO_INCREMENT,
  `sysmessage` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `msgtype` tinyint(4) NOT NULL COMMENT '0=error & 1=success',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pksysmessageid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_module`
--

DROP TABLE IF EXISTS `system_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_module` (
  `pkmoduleid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `modulename` varchar(50) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkmoduleid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_notification`
--

DROP TABLE IF EXISTS `system_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_notification` (
  `pknotificationid` int(11) NOT NULL AUTO_INCREMENT,
  `notificationtitle` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `notificationdatetime` datetime NOT NULL,
  `isread` tinyint(4) NOT NULL COMMENT '1=read, 0:unread',
  `uid` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fknotificationtypeid` int(11) NOT NULL,
  `fkaddressbookid` int(11) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pknotificationid`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_notificationtype`
--

DROP TABLE IF EXISTS `system_notificationtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_notificationtype` (
  `pknotificationtypeid` int(11) NOT NULL AUTO_INCREMENT,
  `typename` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `iconname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pknotificationtypeid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_os`
--

DROP TABLE IF EXISTS `system_os`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_os` (
  `pkosid` int(11) NOT NULL AUTO_INCREMENT,
  `osname` varchar(255) CHARACTER SET latin1 NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkosid`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_screen`
--

DROP TABLE IF EXISTS `system_screen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_screen` (
  `pkscreenid` int(11) NOT NULL AUTO_INCREMENT,
  `screentype` tinyint(4) NOT NULL COMMENT '1=Gird, 2=Custom',
  `screenname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `screennamehebrew` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Screen Name in Hebrew',
  `url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `formurl` varchar(255) CHARACTER SET latin1 NOT NULL,
  `deletefilename` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `displayorder` int(11) NOT NULL,
  `fkmoduleid` int(11) NOT NULL,
  `fksectionid` int(11) NOT NULL,
  `visibility` tinyint(1) NOT NULL COMMENT '1=main, 2=global, 3=local',
  `shortcut` varchar(10) CHARACTER SET latin1 NOT NULL,
  `shortkey` varchar(1) CHARACTER SET latin1 NOT NULL,
  `showtoadmin` tinyint(4) NOT NULL,
  `showontop` tinyint(4) NOT NULL,
  `query` text CHARACTER SET latin1 NOT NULL,
  `tablename` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pkid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `orderby` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isdeleted` tinyint(4) NOT NULL DEFAULT '0',
  `issystemscreen` tinyint(4) NOT NULL COMMENT '1: System 0: No',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkscreenid`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_section`
--

DROP TABLE IF EXISTS `system_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_section` (
  `pksectionid` int(11) NOT NULL AUTO_INCREMENT,
  `sectionname` varchar(25) CHARACTER SET latin1 NOT NULL,
  `sectionnamehebrew` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT ' Section Name in Hebrew',
  `sectionicon` varchar(300) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(1) NOT NULL,
  `sortorder` int(11) NOT NULL,
  `issystemsection` tinyint(4) NOT NULL COMMENT '1: System 0: No',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pksectionid`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_setting`
--

DROP TABLE IF EXISTS `system_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_setting` (
  `pksettingid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `jstimeformat` varchar(20) NOT NULL,
  `jsdateformat` varchar(20) NOT NULL,
  `currencyicon` varchar(20) NOT NULL,
  `iconposition` varchar(20) NOT NULL,
  `defaultfaicon` varchar(30) NOT NULL,
  `pickerposition` varchar(20) NOT NULL,
  `pagingoptions` varchar(255) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pksettingid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_state`
--

DROP TABLE IF EXISTS `system_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_state` (
  `pkstateid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fkcountryid` bigint(20) NOT NULL,
  `statename` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `statecode` varchar(9) CHARACTER SET latin1 DEFAULT NULL,
  `adm1code` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkstateid`,`fkcountryid`)
) ENGINE=InnoDB AUTO_INCREMENT=5400 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblfilesetting`
--

DROP TABLE IF EXISTS `tblfilesetting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblfilesetting` (
  `pkfilesettingid` int(11) NOT NULL AUTO_INCREMENT,
  `fkformfieldid` int(11) NOT NULL,
  `filetype` text NOT NULL COMMENT 'comma seprated file types',
  `filesize` float NOT NULL,
  `paralleluploads` int(11) NOT NULL,
  `maxFiles` int(11) NOT NULL,
  `uploadMultiple` tinyint(4) NOT NULL COMMENT '0:Single, 1:Multiple',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkfilesettingid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblfiletype`
--

DROP TABLE IF EXISTS `tblfiletype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblfiletype` (
  `pkfiletypeid` int(11) NOT NULL AUTO_INCREMENT,
  `filetypename` varchar(250) NOT NULL,
  `extension` varchar(250) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`pkfiletypeid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `system_addressbook_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_teams_users_idx` (`system_addressbook_id`),
  CONSTRAINT `fk_teams_users` FOREIGN KEY (`system_addressbook_id`) REFERENCES `system_addressbook` (`pkaddressbookid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_teams`
--

DROP TABLE IF EXISTS `users_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_teams` (
  `system_addressbook_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`system_addressbook_id`,`team_id`),
  KEY `fk_users_has_teams_teams1_idx` (`team_id`),
  KEY `fk_users_has_teams_users1_idx` (`system_addressbook_id`),
  CONSTRAINT `fk_users_has_teams_teams1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_users_has_teams_users1` FOREIGN KEY (`system_addressbook_id`) REFERENCES `system_addressbook` (`pkaddressbookid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for meet confer tables
--

DROP TABLE IF EXISTS `meet_confer_arguments`;
DROP TABLE IF EXISTS `meet_confers`;


/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-08-17 21:06:41
