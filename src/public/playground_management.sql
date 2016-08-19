-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2016 at 12:12 PM
-- Server version: 5.6.30
-- PHP Version: 5.5.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `playground_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `id` int(10) unsigned NOT NULL,
  `barcodeID` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `playgroundID` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `userID` smallint(5) unsigned NOT NULL,
  `exitTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `price` decimal(6,2) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `barcodeID`, `name`, `data`, `time`, `playgroundID`, `userID`, `exitTime`, `price`) VALUES
(1, 2, '`c', '{"nume":"`c","detalii":""}', '2016-08-17 12:48:54', 1, 1, '0000-00-00 00:00:00', 0.00),
(2, 2, '`c', '{"nume":"`c","detalii":""}', '2016-08-17 12:48:58', 1, 1, '0000-00-00 00:00:00', 0.00),
(3, 2, '`c', '{"nume":"`c","detalii":""}', '2016-08-17 12:50:56', 1, 1, '0000-00-00 00:00:00', 0.00),
(4, 2, '12', '{"nume":"12","detalii":""}', '2016-08-17 12:52:24', 1, 1, '0000-00-00 00:00:00', 0.00),
(5, 2, 'daniel', '{"nume":"daniel","detalii":"1234"}', '2016-08-18 14:09:47', 1, 1, '0000-00-00 00:00:00', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `monetar`
--

CREATE TABLE IF NOT EXISTS `monetar` (
  `id` int(10) unsigned NOT NULL,
  `userID` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  `total` decimal(10,2) unsigned NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `playgroundID` tinyint(4) NOT NULL DEFAULT '1',
  `operatiune` set('seara','dimineata','bon','factura','zet','retragere','faraDocumente') NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `monetar`
--

INSERT INTO `monetar` (`id`, `userID`, `data`, `total`, `time`, `playgroundID`, `operatiune`) VALUES
(1, 1, '{"cincisute":"1","douasute":"0","unasuta":"0","cincizeci":"0","zece":"0","cinci":"0","unleu":"0","bani50":"0","bani10":"0"}', 500.00, '2016-08-16 13:49:44', 1, 'seara'),
(2, 1, '{"cincisute":"1","douasute":"0","unasuta":"0","cincizeci":"0","zece":"0","cinci":"0","unleu":"0","bani50":"0","bani10":"0"}', 500.00, '2016-08-16 13:50:35', 1, 'bon'),
(3, 1, '{"cincisute":"1","douasute":"0","unasuta":"0","cincizeci":"0","zece":"0","cinci":"0","unleu":"0","bani50":"0","bani10":"0"}', 0.00, '2016-08-16 13:55:58', 1, 'bon'),
(4, 1, '{"cincisute":"1","douasute":"0","unasuta":"0","cincizeci":"0","zece":"0","cinci":"0","unleu":"0","bani50":"0","bani10":"0"}', 0.00, '2016-08-16 13:56:15', 1, 'bon'),
(5, 1, '{"cincisute":"1","douasute":"0","unasuta":"0","cincizeci":"0","zece":"0","cinci":"0","unleu":"0","bani50":"0","bani10":"0"}', 0.00, '2016-08-16 13:56:44', 1, 'bon'),
(6, 1, '{"cincisute":"1","douasute":"0","unasuta":"0","cincizeci":"0","zece":"0","cinci":"0","unleu":"0","bani50":"0","bani10":"0"}', 0.00, '2016-08-16 13:59:18', 1, 'bon'),
(7, 1, '{"firma":"a","descriereServicii":"d","bon":"1230","valoare":"12"}', 0.00, '2016-08-16 13:59:40', 1, 'bon'),
(8, 1, '{"firma":"a","descriereServicii":"d","bon":"1230","valoare":"12"}', 0.00, '2016-08-16 14:00:22', 1, 'bon'),
(9, 1, '{"firma":"a","descriereServicii":"d","bon":"1230","valoare":"12"}', 0.00, '2016-08-16 14:01:18', 1, 'bon'),
(10, 1, '{"firma":"a","descriereServicii":"d","bon":"1230","valoare":"12"}', 12.00, '2016-08-16 14:06:46', 1, 'bon'),
(11, 1, '{"valoare":"120"}', 120.00, '2016-08-16 14:19:55', 1, 'retragere'),
(12, 1, '{"valoare":"120"}', 120.00, '2016-08-16 14:20:50', 1, 'retragere'),
(13, 1, '{"valoare":"120"}', 120.00, '2016-08-16 14:21:00', 1, 'retragere');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL,
  `barcodeID` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `owner` enum('Coca Cola','','','') NOT NULL,
  `addedby` int(10) unsigned NOT NULL,
  `addedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `qty` tinyint(3) unsigned NOT NULL,
  `playgroundID` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `um` varchar(5) NOT NULL,
  `price` decimal(5,2) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `barcodeID`, `name`, `owner`, `addedby`, `addedDate`, `qty`, `playgroundID`, `um`, `price`) VALUES
(1, 123, 'cola 2.5', 'Coca Cola', 1, '2016-08-19 08:27:00', 6, 1, 'bax', 7.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monetar`
--
ALTER TABLE `monetar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `monetar`
--
ALTER TABLE `monetar`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
