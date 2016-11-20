-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ssshout
-- ------------------------------------------------------
-- Server version	5.5.38-0ubuntu0.14.04.1

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

--
-- Table structure for table `php_session`
--

DROP TABLE IF EXISTS `php_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `php_session` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `user_id` varchar(16) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_data` longtext,
  PRIMARY KEY (`session_id`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
-- TODO: ALTER TABLE php_session CONVERT TO CHARACTER SET utf8;
--
-- Table structure for table `tbl_email`
--

DROP TABLE IF EXISTS `tbl_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_email` (
  `int_email_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `var_layers` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `var_title` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `var_body` mediumtext COLLATE utf8_bin,
  `date_when_received` datetime DEFAULT NULL,
  `var_whisper` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`int_email_id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_feed`
--

DROP TABLE IF EXISTS `tbl_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_feed` (
  `int_feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `var_unique_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `date_when_shouted` datetime DEFAULT NULL,
  `int_layer_id` int(10) DEFAULT NULL,
  `int_uid_id` int(10) DEFAULT NULL,
  `int_mail_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`int_feed_id`),
  KEY `unique_feed_item` (`var_unique_id`),
  KEY `date_feed_item` (`date_when_shouted`,`var_unique_id`),
  KEY `layer` (`int_layer_id`,`int_uid_id`),
  KEY `mail` (`int_mail_id`,`int_uid_id`)
) ENGINE=MyISAM AUTO_INCREMENT=167 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_group`
--

DROP TABLE IF EXISTS `tbl_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_group` (
  `int_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `int_user1_id` int(10) unsigned NOT NULL,
  `int_user2_id` int(10) unsigned NOT NULL,
  `int_more_in_group_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`int_group_id`),
  KEY `tbl_group12_id` (`int_user1_id`,`int_user2_id`),
  KEY `tbl_group21_id` (`int_user2_id`,`int_user1_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_layer`
--

DROP TABLE IF EXISTS `tbl_layer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_layer` (
  `int_layer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enm_access` enum('public','public-admin-write-only','private') CHARACTER SET latin1 DEFAULT NULL,
  `passcode` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `int_group_id` int(10) unsigned DEFAULT NULL,
  `var_public_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `var_owner_string` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `date_owner_start` datetime DEFAULT NULL,
  PRIMARY KEY (`int_layer_id`),
  KEY `tbl_advert_FKIndex1` (`int_layer_id`),
  KEY `tbl_advert_passcode` (`passcode`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_layer_subscription`
--

DROP TABLE IF EXISTS `tbl_layer_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_layer_subscription` (
  `int_layer_id` int(11) NOT NULL,
  `int_user_id` int(11) NOT NULL,
  `enm_active` enum('active','inactive') COLLATE utf8_bin DEFAULT NULL,
  `enm_sms` enum('send','none') COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_search_suggest`
--

DROP TABLE IF EXISTS `tbl_search_suggest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_search_suggest` (
  `int_search_suggest_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `var_search_suggest` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `enm_fullscreen` enum('true','false') COLLATE utf8_bin DEFAULT 'false',
  PRIMARY KEY (`int_search_suggest_id`),
  KEY `search_suggest_search` (`var_search_suggest`)
) ENGINE=MyISAM AUTO_INCREMENT=245082 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_ssshout`
--

DROP TABLE IF EXISTS `tbl_ssshout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ssshout` (
  `int_ssshout_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `var_shouted` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `dec_latitude` decimal(14,7) DEFAULT NULL,
  `dec_longitude` decimal(15,7) DEFAULT NULL,
  `date_when_shouted` datetime DEFAULT NULL,
  `int_peano1` int(10) unsigned DEFAULT NULL,
  `int_peano2` int(10) unsigned DEFAULT NULL,
  `int_peano1inv` int(10) unsigned DEFAULT NULL,
  `int_peano2inv` int(10) unsigned DEFAULT NULL,
  `int_layer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `enm_active` enum('true','false') COLLATE utf8_bin DEFAULT 'true',
  `var_whisper_to` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `var_ip` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `int_whisper_to_id` int(10) unsigned DEFAULT NULL,
  `int_author_id` int(10) unsigned DEFAULT NULL,
  `enm_status` enum('typing','final') COLLATE utf8_bin DEFAULT 'final',
  `var_shouted_processed` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `flt_sentiment` decimal(10,5) DEFAULT NULL,
  PRIMARY KEY (`int_ssshout_id`),
  KEY `tbl_advert_FKIndex1` (`int_ssshout_id`),
  KEY `tbl_advert_peano1` (`enm_active`,`int_layer_id`,`int_peano1`,`int_ssshout_id`),
  KEY `tbl_advert_peano2` (`enm_active`,`int_layer_id`,`int_peano2`,`int_ssshout_id`),
  KEY `tbl_advert_peano1iv` (`enm_active`,`int_layer_id`,`int_peano1inv`,`int_ssshout_id`),
  KEY `tbl_advert_peano2iv` (`enm_active`,`int_layer_id`,`int_peano2inv`,`int_ssshout_id`),
  KEY `cutoff` (`enm_active`,`int_layer_id`,`int_peano1`,`date_when_shouted`),
  KEY `shouted_already` (`var_ip`,`var_shouted`(255),`date_when_shouted`),
  KEY `tbl_status` (`enm_active`,`enm_status`,`date_when_shouted`),
  KEY `flt_sent` (`flt_sentiment`)
) ENGINE=InnoDB AUTO_INCREMENT=2867 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_subdomain`
--

DROP TABLE IF EXISTS `tbl_subdomain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_subdomain` (
  `int_subdomain_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `var_owner_string` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `date_owner_start` datetime DEFAULT NULL,
  `date_owner_end` datetime DEFAULT NULL,
  `var_subdomain` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `enm_fullscreen` enum('true','false') COLLATE utf8_bin DEFAULT 'false',
  PRIMARY KEY (`int_subdomain_id`),
  KEY `subdomain_search` (`var_owner_string`),
  KEY `subdomain_true_search` (`var_subdomain`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user` (
  `int_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `var_last_ip` varchar(50) DEFAULT NULL,
  `var_email` varchar(255) DEFAULT NULL,
  `enm_alerts` enum('on','off') DEFAULT 'on',
  `var_phone` varchar(50) DEFAULT NULL,
  `dec_balance` decimal(8,3) DEFAULT '0.000',
  `var_confirmcode` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `enm_confirmed` enum('unconfirmed','confirmed') DEFAULT 'unconfirmed',
  `var_pass` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`int_user_id`),
  KEY `tbl_user_FKIndex1` (`int_user_id`),
  KEY `tbl_user_last_ip` (`var_last_ip`),
  KEY `tbl_user_email` (`var_email`)
) ENGINE=InnoDB AUTO_INCREMENT=414 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-11-25  2:48:37



-- Modify for social
ALTER TABLE tbl_feed MODIFY COLUMN `int_uid_id` BIGINT DEFAULT NULL;
ALTER TABLE tbl_feed ADD COLUMN `int_social_id` INT(10) DEFAULT NULL;
CREATE INDEX social ON tbl_feed (int_social_id, int_uid_id);


ALTER TABLE tbl_subdomain ADD COLUMN `var_search_words` VARCHAR(255) DEFAULT NULL;
ALTER TABLE tbl_subdomain ADD COLUMN `int_freq` INT(10) DEFAULT NULL;
ALTER TABLE tbl_subdomain ADD COLUMN `var_whisper_to` VARCHAR(255) DEFAULT NULL;


CREATE INDEX recent_ssshout ON tbl_ssshout (int_layer_id, date_when_shouted);

-- Modify for international UTF-8 everywhere
ALTER TABLE php_session CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE tbl_user CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin;
ALTER TABLE tbl_group CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin;

-- Modify for ordered messages based on date/time, not id.
CREATE INDEX ordered_ssshout ON tbl_ssshout (date_when_shouted);

