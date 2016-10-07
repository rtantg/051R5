-- MySQL dump 10.13  Distrib 5.7.15, for osx10.12 (x86_64)
--
-- Host: localhost    Database: dbLOI
-- ------------------------------------------------------
-- Server version	5.7.15

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
-- Table structure for table `competitieschema`
--

DROP TABLE IF EXISTS `competitieschema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competitieschema` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `thuis_club` varchar(50) NOT NULL,
  `uit_club` varchar(50) NOT NULL,
  `datum` date NOT NULL,
  `thuis_score` tinyint(4) DEFAULT NULL,
  `uit_score` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `thuis_club` (`thuis_club`,`uit_club`,`datum`),
  KEY `uit_club` (`uit_club`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competitieschema`
--

LOCK TABLES `competitieschema` WRITE;
/*!40000 ALTER TABLE `competitieschema` DISABLE KEYS */;
/*!40000 ALTER TABLE `competitieschema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gebruikers`
--

DROP TABLE IF EXISTS `gebruikers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gebruikers` (
  `gebruikersnaam` varchar(15) NOT NULL,
  `naam` varchar(25) NOT NULL,
  `wachtwoord` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`gebruikersnaam`),
  UNIQUE KEY `Email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gebruikers`
--

LOCK TABLES `gebruikers` WRITE;
/*!40000 ALTER TABLE `gebruikers` DISABLE KEYS */;
/*!40000 ALTER TABLE `gebruikers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voetbalteams`
--

DROP TABLE IF EXISTS `voetbalteams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voetbalteams` (
  `naam` varchar(50) NOT NULL,
  `locatie` varchar(35) NOT NULL,
  `stadion` varchar(50) DEFAULT NULL,
  `opgericht` date NOT NULL,
  `website` varchar(50) DEFAULT NULL,
  `logo` varchar(50) NOT NULL,
  PRIMARY KEY (`naam`),
  UNIQUE KEY `Name` (`naam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voetbalteams`
--

LOCK TABLES `voetbalteams` WRITE;
/*!40000 ALTER TABLE `voetbalteams` DISABLE KEYS */;
/*!40000 ALTER TABLE `voetbalteams` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-07 11:47:55
