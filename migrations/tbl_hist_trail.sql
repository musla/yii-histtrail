-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 16, 2012 at 06:54 AM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nhbs_w`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hist_trail`
--

CREATE TABLE IF NOT EXISTS `tbl_hist_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_value` text COLLATE utf8_unicode_ci,
  `new_value` text COLLATE utf8_unicode_ci,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `stamp` datetime NOT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valid_from` date,
  `valid_to` date,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `tbl_hist_trail` 
ADD INDEX `idx_hist_trail_user_id` (`user_id` ASC) 
, ADD INDEX `idx_hist_trail_model_id` (`model_id` ASC) 
, ADD INDEX `idx_hist_trail_model` (`model` ASC) 
, ADD INDEX `idx_hist_trail_field` (`field` ASC) 
, ADD INDEX `idx_hist_trail_action` (`action` ASC) 
, ADD INDEX `idx_hist_trail_valid_from` (`valid_from` ASC) 
, ADD INDEX `idx_hist_trail_valid_to` (`valid_to` ASC) 

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
