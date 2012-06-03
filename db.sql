-- Adminer 3.3.3 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `eligibilities` (
  `projectID` int(10) unsigned NOT NULL,
  `jurorID` int(10) unsigned NOT NULL,
  `notEligible` tinyint(1) unsigned NOT NULL,
  `reasons` text COLLATE utf8_czech_ci NOT NULL,
  `phase` enum('firstRating','secondRating') COLLATE utf8_czech_ci NOT NULL,
  UNIQUE KEY `projectID_jurorID_phase` (`projectID`,`jurorID`,`phase`),
  KEY `projectID` (`projectID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `jurors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` mediumint(8) unsigned NOT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `role` enum('chairman','juror','webmaster') NOT NULL DEFAULT 'juror',
  PRIMARY KEY (`id`),
  KEY `role` (`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `event` varchar(255) COLLATE utf8_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `roundID` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `amount` int(11) NOT NULL,
  `negotiatedAmount` int(11) NOT NULL,
  `applicant` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `firstRating` float NOT NULL,
  `firstRatingIneligibility` smallint(6) NOT NULL,
  `secondRating` float NOT NULL,
  `secondRatingIneligibility` smallint(6) NOT NULL,
  `applicationLink` text COLLATE utf8_czech_ci NOT NULL,
  `otherLinks` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `ratingCategories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projectID` int(10) unsigned NOT NULL,
  `ratingCategoryID` int(10) unsigned NOT NULL,
  `jurorID` int(10) unsigned NOT NULL,
  `phase` enum('firstRating','secondRating') COLLATE utf8_czech_ci NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projectID_ratingCategoryID_jurorID_phase` (`projectID`,`ratingCategoryID`,`jurorID`,`phase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `roundXratingCategory` (
  `roundID` int(10) unsigned NOT NULL,
  `categoryID` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `rounds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `amount` int(11) NOT NULL,
  `createdDATE` datetime NOT NULL,
  `deliberationTimePlace` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `deliberationMinutes` text COLLATE utf8_czech_ci NOT NULL,
  `phase` enum('preparation','firstRating','deliberation','secondRating','results') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2012-06-03 12:05:23