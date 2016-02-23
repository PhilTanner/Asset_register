-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 23, 2016 at 10:56 AM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `itequipment`
--
CREATE DATABASE IF NOT EXISTS `itequipment` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `itequipment`;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE IF NOT EXISTS `equipment` (
  `AssetNumber` varchar(20) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Type` varchar(25) NOT NULL DEFAULT 'Unknown',
  `Manufacturer` varchar(50) NOT NULL,
  `ModelNumber` varchar(50) NOT NULL,
  `SerialNumber` varchar(50) NOT NULL,
  `Location` varchar(150) NOT NULL,
  PRIMARY KEY (`AssetNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE IF NOT EXISTS `loans` (
  `loan_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `AssetNumber` varchar(20) NOT NULL,
  `from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to` timestamp NULL DEFAULT NULL,
  `borrower` varchar(100) NOT NULL,
  `returned` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`loan_id`),
  KEY `AssetNumber` (`AssetNumber`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`AssetNumber`) REFERENCES `equipment` (`AssetNumber`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
