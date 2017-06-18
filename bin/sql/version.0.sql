-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2010 at 06:51 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `mqueue`
--

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE IF NOT EXISTS `movie` (
  `id` varchar(7) collate utf8_bin NOT NULL,
  `dateUpdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `title` varchar(512) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL auto_increment,
  `idUser` int(11) NOT NULL,
  `idMovie` varchar(7) collate utf8_bin NOT NULL,
  `rating` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_status` (`idUser`,`idMovie`),
  KEY `idUser` (`idUser`),
  KEY `idMovie` (`idMovie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=427 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL auto_increment,
  `nickname` varchar(32) collate utf8_bin NOT NULL,
  `email` varchar(100) collate utf8_bin NOT NULL,
  `password` varchar(32) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nickname` (`nickname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` varchar(64) COLLATE utf8_bin NOT NULL,
  `value` longtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `status_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `status_ibfk_2` FOREIGN KEY (`idMovie`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
