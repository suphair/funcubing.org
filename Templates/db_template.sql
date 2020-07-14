-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: localhost	Database: suphair_funcubing
-- ------------------------------------------------------
-- Server version 	5.7.26
-- Date: Mon, 13 Jul 2020 14:35:29 +0000

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
-- Table structure for table `Competitor`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Competitor` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `WCAID` varchar(255) DEFAULT '',
  `Country` varchar(255) DEFAULT NULL,
  `WID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`,`Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Goal`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Goal` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Discipline` varchar(255) DEFAULT NULL,
  `Competition` varchar(255) DEFAULT NULL,
  `Format` varchar(255) DEFAULT NULL,
  `Result` varchar(255) DEFAULT NULL,
  `TimeFixed` datetime DEFAULT NULL,
  `Record` varchar(255) DEFAULT NULL,
  `Progress` varchar(255) DEFAULT NULL,
  `Goal` varchar(255) DEFAULT NULL,
  `Complete` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `GoalCompetitor` (`Competitor`),
  KEY `GoalDiscipline` (`Discipline`),
  KEY `GoalCompetition` (`Competition`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GoalCompetition`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GoalCompetition` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `DateStart` date DEFAULT NULL,
  `DateEnd` date DEFAULT NULL,
  `WCA` varchar(255) DEFAULT NULL,
  `Country` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `TimeUpdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Events` varchar(255) DEFAULT NULL,
  `Result` bit(1) DEFAULT b'0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GoalCompetitor`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GoalCompetitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competitorWid` varchar(255) DEFAULT NULL,
  `competitionWca` varchar(255) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  `eventCode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competitorWid` (`competitorWid`,`competitionWca`,`eventCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GoalDiscipline`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GoalDiscipline` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MailUpcomingCompetitions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MailUpcomingCompetitions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` bigint(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Status` varchar(255) DEFAULT '0',
  `announced_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Meeting`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Meeting` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Competitor` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Details` text,
  `Secret` varchar(10) DEFAULT NULL,
  `SecretRegistration` varchar(255) DEFAULT NULL,
  `Show` tinyint(4) DEFAULT '0',
  `Website` varchar(255) DEFAULT NULL,
  `ShareRegistration` smallint(6) DEFAULT '0',
  `Date` date DEFAULT NULL,
  `Public` bit(1) DEFAULT b'0',
  `Organizer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Competitor` (`Competitor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MeetingCompetitor`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeetingCompetitor` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Meeting` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Session` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Meeting` (`Meeting`),
  CONSTRAINT `meetingcompetitor_ibfk_1` FOREIGN KEY (`Meeting`) REFERENCES `Meeting` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MeetingCompetitorDiscipline`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeetingCompetitorDiscipline` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MeetingCompetitor` int(11) DEFAULT NULL,
  `MeetingDiscipline` int(11) DEFAULT NULL,
  `Attempt1` varchar(255) DEFAULT NULL,
  `Attempt2` varchar(255) DEFAULT NULL,
  `Attempt3` varchar(255) DEFAULT NULL,
  `Attempt4` varchar(255) DEFAULT NULL,
  `Attempt5` varchar(255) DEFAULT NULL,
  `Best` varchar(255) DEFAULT NULL,
  `Average` varchar(255) DEFAULT NULL,
  `Mean` varchar(255) DEFAULT NULL,
  `Place` int(11) DEFAULT NULL,
  `Attempts` varchar(255) DEFAULT NULL,
  `MilisecondsOrder` double DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `MettingCompetitor` (`MeetingCompetitor`),
  KEY `MettingDiscipline` (`MeetingDiscipline`),
  CONSTRAINT `meetingcompetitordiscipline_ibfk_1` FOREIGN KEY (`MeetingCompetitor`) REFERENCES `MeetingCompetitor` (`ID`),
  CONSTRAINT `meetingcompetitordiscipline_ibfk_2` FOREIGN KEY (`MeetingDiscipline`) REFERENCES `MeetingDiscipline` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MeetingDiscipline`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeetingDiscipline` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Meeting` int(11) DEFAULT NULL,
  `MeetingDisciplineList` int(11) DEFAULT NULL,
  `Round` int(11) DEFAULT NULL,
  `MeetingFormat` int(11) DEFAULT NULL,
  `Comment` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Amount` bit(1) DEFAULT b'0',
  PRIMARY KEY (`ID`),
  KEY `Meeting` (`Meeting`),
  KEY `MeetingDisciplineList` (`MeetingDisciplineList`),
  KEY `MeetingFormat` (`MeetingFormat`),
  CONSTRAINT `meetingdiscipline_ibfk_1` FOREIGN KEY (`Meeting`) REFERENCES `Meeting` (`ID`),
  CONSTRAINT `meetingdiscipline_ibfk_2` FOREIGN KEY (`MeetingDisciplineList`) REFERENCES `MeetingDisciplineList` (`ID`),
  CONSTRAINT `meetingdiscipline_ibfk_3` FOREIGN KEY (`MeetingFormat`) REFERENCES `MeetingFormat` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MeetingDisciplineList`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeetingDisciplineList` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `Code` varchar(255) DEFAULT NULL,
  `Amount` bit(1) DEFAULT b'0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MeetingFormat`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeetingFormat` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Format` varchar(255) DEFAULT NULL,
  `Attempts` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MeetingOrganizer`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MeetingOrganizer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Meeting` int(11) DEFAULT NULL,
  `WCAID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
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
  UNIQUE KEY `wcaid` (`wcaid`,`wid`) USING BTREE
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
  UNIQUE KEY `friend` (`friend`,`user`)
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `competitor_name_date` (`competitor`,`name`,`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `competition_name` (`competition`,`name`),
  KEY `competition` (`competition`),
  CONSTRAINT `unofficial_competitors_ibfk_1` FOREIGN KEY (`competition`) REFERENCES `unofficial_competitions` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
  UNIQUE KEY `competitor_round` (`competitor_round`) USING BTREE,
  CONSTRAINT `fk_unofficial_competitors_result_competitor_round` FOREIGN KEY (`competitor_round`) REFERENCES `unofficial_competitors_round` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
  UNIQUE KEY `competition_event` (`competition`,`event_dict`) USING BTREE,
  KEY `competition` (`competition`),
  KEY `event_dict` (`event_dict`),
  KEY `format` (`format_dict`),
  KEY `result` (`result_dict`),
  CONSTRAINT `fk_unofficial_event_competition` FOREIGN KEY (`competition`) REFERENCES `unofficial_competitions` (`id`),
  CONSTRAINT `fk_unofficial_event_format_dict` FOREIGN KEY (`format_dict`) REFERENCES `unofficial_formats_dict` (`id`),
  CONSTRAINT `fk_unofficial_event_result_dict` FOREIGN KEY (`result_dict`) REFERENCES `unofficial_results_dict` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_round` (`event`,`round`) USING BTREE,
  KEY `event` (`event`),
  KEY `round` (`round`),
  CONSTRAINT `fk_unofficial_events_rounds_event` FOREIGN KEY (`event`) REFERENCES `unofficial_events` (`id`),
  CONSTRAINT `fk_unofficial_events_rounds_round` FOREIGN KEY (`round`) REFERENCES `unofficial_rounds_dict` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `format_attempt` (`format`,`attempts`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unofficial_results_dict`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unofficial_results_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Mon, 13 Jul 2020 14:35:30 +0000
