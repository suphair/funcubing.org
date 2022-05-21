-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: localhost	Database: suphair_funcubing
-- ------------------------------------------------------
-- Server version 	5.7.26
-- Date: Sat, 21 May 2022 13:59:15 +0000

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
-- Table structure for table `announcements`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `countries` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`),
  KEY `ID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_config`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_config` (
  `name` varchar(255) NOT NULL,
  `command` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `last` datetime DEFAULT NULL,
  `next` datetime DEFAULT NULL,
  `period` int(11) DEFAULT NULL COMMENT 'in minutes',
  `schedule` time DEFAULT NULL,
  `argument` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_logs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `begin` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dict_competitors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dict_competitors` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `wcaid` varchar(255) DEFAULT '',
  `country` varchar(255) DEFAULT NULL,
  `wid` int(11) DEFAULT NULL,
  `timestamp_insert` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `nameRU` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  UNIQUE KEY `wcaid` (`wcaid`,`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dict_continents`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dict_continents` (
  `code` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  UNIQUE KEY `Code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dict_countries`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dict_countries` (
  `iso2` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `continent` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  UNIQUE KEY `ISO2` (`iso2`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `friends`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `friend` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `friend_user` (`friend`,`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goals`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competitor` int(11) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `competition` varchar(255) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `result` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `record` varchar(255) DEFAULT NULL,
  `progress` varchar(255) DEFAULT NULL,
  `goal` varchar(255) DEFAULT NULL,
  `complete` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `competitor` (`competitor`,`event`,`competition`,`format`),
  KEY `GoalCompetitor` (`competitor`),
  KEY `GoalDiscipline` (`event`),
  KEY `GoalCompetition` (`competition`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goals_competitions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goals_competitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `dateStart` date DEFAULT NULL,
  `dateEnd` date DEFAULT NULL,
  `wca` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `events` varchar(255) DEFAULT NULL,
  `resultsLoad` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `WCA` (`wca`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goals_competitors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goals_competitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competitorWid` varchar(255) DEFAULT NULL,
  `competitionWca` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `eventCode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competitorWid` (`competitorWid`,`competitionWca`,`eventCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goals_events`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goals_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_colors_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_colors_dict` (
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(12) NOT NULL,
  `order` int(11) DEFAULT NULL,
  `default` bit(1) DEFAULT NULL,
  `border` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_displays_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_displays_dict` (
  `code` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `default` bit(1) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_images`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `folder` varchar(12) DEFAULT NULL,
  `active` bit(1) DEFAULT NULL,
  `custom` varchar(126) DEFAULT NULL,
  `custom_full` bit(1) DEFAULT NULL,
  `custom_use` bit(1) DEFAULT NULL,
  `choose_complete` bit(1) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `mosaic_images_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `mosaic_session` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_pixels_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_pixels_dict` (
  `code` varchar(12) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `default` bit(1) DEFAULT NULL,
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_schemas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_schemas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `step_id` int(11) DEFAULT NULL,
  `schema` varchar(255) DEFAULT NULL,
  `fix` bit(1) DEFAULT NULL,
  `is_custom` bit(1) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `step_schema` (`step_id`,`schema`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_schemas_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_schemas_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_session`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session` varchar(56) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount` int(11) DEFAULT '300',
  `color_dict` varchar(12) DEFAULT NULL,
  `pixel_dict` varchar(12) DEFAULT NULL,
  `folder` varchar(12) DEFAULT NULL,
  `setting_fix` bit(1) DEFAULT NULL,
  `wide` int(11) DEFAULT '3',
  `high` int(11) DEFAULT '4',
  `display_dict` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session` (`session`),
  KEY `color_dict` (`color_dict`),
  KEY `pixel_dict` (`pixel_dict`),
  KEY `display_dict` (`display_dict`),
  CONSTRAINT `mosaic_session_ibfk_1` FOREIGN KEY (`color_dict`) REFERENCES `mosaic_colors_dict` (`code`),
  CONSTRAINT `mosaic_session_ibfk_2` FOREIGN KEY (`pixel_dict`) REFERENCES `mosaic_pixels_dict` (`code`),
  CONSTRAINT `mosaic_session_ibfk_3` FOREIGN KEY (`display_dict`) REFERENCES `mosaic_displays_dict` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mosaic_steps`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mosaic_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(255) DEFAULT NULL,
  `step` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image_step` (`image_id`,`step`),
  KEY `image` (`image_id`),
  CONSTRAINT `mosaic_steps_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `mosaic_images` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauthwca_logs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauthwca_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `me_id` int(11) DEFAULT NULL,
  `me_name` varchar(255) DEFAULT NULL,
  `me_wcaid` varchar(10) DEFAULT NULL,
  `me_countryiso2` varchar(2) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `version` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `smtp_logs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smtp_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `from` varchar(255) DEFAULT NULL,
  `result` varchar(255) DEFAULT NULL,
  `version` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_competition_judges`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_competition_judges` (
  `judge` varchar(255) DEFAULT NULL,
  `competition_id` int(11) DEFAULT NULL,
  `dict_judge_role` int(1) DEFAULT NULL,
  UNIQUE KEY `judge` (`judge`,`competition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_competitions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_competitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competitor` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `details` text,
  `secret` varchar(10) DEFAULT NULL,
  `secretRegistration` varchar(255) DEFAULT NULL,
  `show` tinyint(4) DEFAULT '0',
  `website` varchar(255) DEFAULT NULL,
  `shareRegistration` smallint(6) DEFAULT '0',
  `date` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `ranked` bit(1) DEFAULT NULL,
  `rankedApproved` bit(1) DEFAULT NULL,
  `rankedID` varchar(255) DEFAULT NULL,
  `logo` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `secret` (`secret`),
  UNIQUE KEY `rankedID` (`rankedID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_competitors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_competitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competition` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `FCID` varchar(10) DEFAULT NULL,
  `card` int(11) DEFAULT NULL,
  `non_resident` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competition_name` (`competition`,`name`),
  KEY `competition` (`competition`),
  CONSTRAINT `unofficial_competitors_ibfk_1` FOREIGN KEY (`competition`) REFERENCES `unofficial_competitions` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_competitors_result`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_competitors_result` (
  `competitor_round` int(11) NOT NULL,
  `attempt1` varchar(255) DEFAULT NULL,
  `attempt2` varchar(255) DEFAULT NULL,
  `attempt3` varchar(255) DEFAULT NULL,
  `attempt4` varchar(255) DEFAULT NULL,
  `attempt5` varchar(255) DEFAULT NULL,
  `best` varchar(255) DEFAULT NULL,
  `average` varchar(255) DEFAULT NULL,
  `mean` varchar(255) DEFAULT NULL,
  `attempts` varchar(255) NOT NULL,
  `place` int(11) NOT NULL,
  `order` double NOT NULL,
  `order_best` double DEFAULT NULL,
  `order_average` double DEFAULT NULL,
  UNIQUE KEY `competitor_round` (`competitor_round`),
  CONSTRAINT `fk_unofficial_competitors_result_competitor_round` FOREIGN KEY (`competitor_round`) REFERENCES `unofficial_competitors_round` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_competitors_round`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_competitors_round` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competitor` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competitor_round` (`competitor`,`round`),
  KEY `competitor` (`competitor`),
  KEY `round` (`round`),
  CONSTRAINT `fk_unofficial_competitors_round_competitor` FOREIGN KEY (`competitor`) REFERENCES `unofficial_competitors` (`id`),
  CONSTRAINT `fk_unofficial_competitors_round_round` FOREIGN KEY (`round`) REFERENCES `unofficial_events_rounds` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_events`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competition` int(11) DEFAULT NULL,
  `event_dict` int(11) DEFAULT NULL,
  `format_dict` int(11) DEFAULT NULL,
  `rounds` int(11) DEFAULT NULL,
  `result_dict` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competition_event` (`competition`,`event_dict`),
  KEY `competition` (`competition`),
  KEY `event_dict` (`event_dict`),
  KEY `format` (`format_dict`),
  KEY `result` (`result_dict`),
  CONSTRAINT `fk_unofficial_event_competition` FOREIGN KEY (`competition`) REFERENCES `unofficial_competitions` (`id`),
  CONSTRAINT `fk_unofficial_event_format_dict` FOREIGN KEY (`format_dict`) REFERENCES `unofficial_formats_dict` (`id`),
  CONSTRAINT `fk_unofficial_event_result_dict` FOREIGN KEY (`result_dict`) REFERENCES `unofficial_results_dict` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_events_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_events_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `result_dict` int(11) DEFAULT NULL,
  `special` tinyint(4) DEFAULT '0',
  `order` int(11) DEFAULT NULL,
  `nameRU` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `code` (`code`),
  KEY `result` (`result_dict`),
  CONSTRAINT `result_dict` FOREIGN KEY (`result_dict`) REFERENCES `unofficial_results_dict` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_events_rounds`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_events_rounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` int(11) DEFAULT NULL,
  `round` int(11) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `cutoff` varchar(255) DEFAULT NULL,
  `time_limit` varchar(255) DEFAULT NULL,
  `cumulative` bit(1) DEFAULT NULL,
  `next_round_value` int(11) DEFAULT '75',
  `next_round_procent` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_round` (`event`,`round`),
  KEY `event` (`event`),
  KEY `round` (`round`),
  CONSTRAINT `fk_unofficial_events_rounds_event` FOREIGN KEY (`event`) REFERENCES `unofficial_events` (`id`),
  CONSTRAINT `fk_unofficial_events_rounds_round` FOREIGN KEY (`round`) REFERENCES `unofficial_rounds_dict` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_fc_wca`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_fc_wca` (
  `FCID` varchar(255) NOT NULL,
  `wcaid` varchar(255) DEFAULT NULL,
  `nonwca` bit(1) DEFAULT NULL,
  `wca_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`FCID`),
  UNIQUE KEY `FCID` (`FCID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_formats_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_formats_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `format` varchar(255) DEFAULT NULL,
  `attempts` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `cutoff_attempts` int(11) DEFAULT NULL,
  `cutoff_name` varchar(255) DEFAULT NULL,
  `nameRU` varchar(255) DEFAULT NULL,
  `cutoff_nameRU` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_judge_roles_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_judge_roles_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(255) DEFAULT NULL,
  `roleRU` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_judges`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_judges` (
  `wcaid` varchar(255) NOT NULL,
  `is_archive` bit(1) DEFAULT NULL,
  `rank` varchar(255) DEFAULT NULL,
  `rankRU` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `regionRU` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`wcaid`),
  UNIQUE KEY `wcaid` (`wcaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_organizers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_organizers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competition` int(11) DEFAULT NULL,
  `wcaid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `competition` (`competition`),
  CONSTRAINT `fk_unofficial_organizers_competition` FOREIGN KEY (`competition`) REFERENCES `unofficial_competitions` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_partners`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competitor` int(11) DEFAULT NULL,
  `partner` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competitor` (`competitor`,`partner`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_results_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_results_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `smallName` varchar(255) DEFAULT NULL,
  `nameRU` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_rounds_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_rounds_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `fullName` varchar(255) DEFAULT NULL,
  `smallName` varchar(255) DEFAULT NULL,
  `fullNameRU` varchar(255) DEFAULT NULL,
  `smallNameRU` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_text`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `text` text,
  `is_archive` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitors` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `agent` varchar(255) DEFAULT NULL,
  `timestampt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `request_uri` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wca_api_cash`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wca_api_cash` (
  `key` varchar(255) NOT NULL,
  `value` longtext,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `version` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wca_api_logs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wca_api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request` varchar(255) DEFAULT NULL,
  `response` longtext,
  `context` varchar(255) DEFAULT NULL,
  `status` int(3) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `version` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wca_oauth_logs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wca_oauth_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `me_id` int(11) DEFAULT NULL,
  `me_name` varchar(255) DEFAULT NULL,
  `me_wcaid` varchar(10) DEFAULT NULL,
  `me_countryiso2` varchar(2) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `version` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Sat, 21 May 2022 13:59:15 +0000
