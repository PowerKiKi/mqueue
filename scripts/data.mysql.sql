-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 17, 2009 at 02:43 AM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;

--
-- Database: `mqueue`
--

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`id`, `status`, `date_update`, `title`) VALUES
('0120569', 0, '0000-00-00 00:00:00', '"Conan" (1997)'),
('0337978', 0, '0000-00-00 00:00:00', 'Live Free or Die Hard (2007)'),
('0340163', 0, '0000-00-00 00:00:00', 'Hostage (2005/I)'),
('0349903', 0, '0000-00-00 00:00:00', 'Ocean''s Twelve (2004)'),
('0401792', 0, '0000-00-00 00:00:00', 'Sin City (2005)'),
('0437745', 0, '0000-00-00 00:00:00', '"Robot Chicken" (2005)'),
('0462322', 0, '0000-00-00 00:00:00', 'Grindhouse (2007)'),
('0485621', 0, '0000-00-00 00:00:00', 'Conan: Red Nails (2009) (V)'),
('1038686', 0, '0000-00-00 00:00:00', 'Legion (2010)'),
('1077258', 0, '0000-00-00 00:00:00', 'Planet Terror (2007)'),
('1179160', 0, '0000-00-00 00:00:00', '"Fasaanit" (1990)');

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nickname`, `email`, `password`) VALUES
(1, 'kiki', 'kiki@mail.com', 'password');

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `id_user`, `id_movie`, `rating`) VALUES
(20, 1, '1038686', 1),
(21, 1, '0462322', 3),
(22, 1, '0337978', 4),
(23, 1, '1077258', 3),
(24, 1, '0401792', 1),
(25, 1, '0349903', 2),
(26, 1, '0340163', 3),
(27, 1, '0485621', 5),
(28, 1, '0437745', 5),
(29, 1, '0120569', 5);

COMMIT;
