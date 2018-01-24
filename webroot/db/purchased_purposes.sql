-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 31, 2014 at 08:50 AM
-- Server version: 5.6.14
-- PHP Version: 5.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forexbuerau`
--

-- --------------------------------------------------------

--
-- Table structure for table `purchased_purposes`
--

CREATE TABLE IF NOT EXISTS `purchased_purposes` (
  `id` varchar(5) NOT NULL,
  `description` text NOT NULL,
  `arrangement` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchased_purposes`
--

INSERT INTO `purchased_purposes` (`id`, `description`, `arrangement`) VALUES
('p000', '--select--', 0),
('p1', 'Transaction between Uganda Residents', 1),
('p10', 'Income Receipts - Wages/Salaries', 10),
('p11', 'Service Receipts - Transportation -Freight', 11),
('p12', 'Service Receipts - Transportation -Passanger', 12),
('p13', 'Service Receipts - Transportation -Other', 13),
('p14', 'Service Receipts - Communication services', 14),
('p15', 'Service Receipts - Construction services', 15),
('p16', 'Service Receipts - Insurance & Re-insurance', 16),
('p17', 'Service Receipts - Financial serivces', 17),
('p18', 'Service Receipts - Travel - Business/Official', 18),
('p19', 'Service Receipts - Travel - Education', 19),
('p2', 'Currency Holdings/Deposits', 2),
('p20', 'Service Receipts - Travel - Medical', 20),
('p21', 'Service Receipts - Travel - Other Personal', 21),
('p22', 'Service Receipts - Computer & information services', 22),
('p23', 'Service Receipts - Royalties & licence fees', 23),
('p24', 'Service Receipts - Other business services', 24),
('p25', 'Service Receipts - Personal, cultural, & recreational services', 25),
('p26', 'Service Receipts - Government services, n.i.e', 26),
('p27', 'Transfers - NGO inflows', 27),
('p28', 'Transfers - Government Grants', 28),
('p29', 'Transfers - Workers remittances', 29),
('p3', 'Export of Goods - Gold Exports (non-monetary gold)', 3),
('p30', 'Transfers - Other transfers', 30),
('p31', 'Interbureaux', 31),
('p32', 'Foreign Direct Equity Investment', 32),
('p33', 'Portfolio investment - Government', 33),
('p34', 'Portfolio investment - Bank', 34),
('p35', 'Portfolio investment - Other', 35),
('p36', 'Loan - Loan Received - By commercial Banks - Short term', 36),
('p37', 'Loan - Loan Received - By commercial Banks - Long term', 37),
('p38', 'Loan - Loan Received - By Others - Private Short term', 38),
('p39', 'Loan - Loan Received - By Others - Private Long term', 39),
('p4', 'Export of Goods - Repair on goods ', 4),
('p40', 'Loan - Loan Received - By Others - Government', 40),
('p41', 'Loan - Loan Repayment (Principal)', 41),
('p42', 'Interbank', 42),
('p43', 'Bank/bureaux', 43),
('p5', 'Export of Goods - Goods procured in ports by carriers', 5),
('p6', 'Export of Goods - Goods for processig', 6),
('p7', 'Export of Goods - Coffee and other Exports', 7),
('p8', 'Income Receipts - Interest received on External assets', 8),
('p9', 'Income Receipts - Dividends/ profits received', 9);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
