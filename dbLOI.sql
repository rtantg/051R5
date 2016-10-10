SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE `gebruikers` (
  `gebruikersnaam` varchar(15) NOT NULL,
  `naam` varchar(25) NOT NULL,
  `wachtwoord` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`gebruikersnaam`),
  UNIQUE KEY `Email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
