-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 31, 2014 at 08:51 AM
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
-- Table structure for table `purposes`
--

CREATE TABLE IF NOT EXISTS `purposes` (
  `id` varchar(5) NOT NULL,
  `description` text NOT NULL,
  `arrangement` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purposes`
--

INSERT INTO `purposes` (`id`, `description`, `arrangement`) VALUES
('p000', '--select--', 0),
('p1', 'Transaction between Uganda Residents', 1),
('p10', 'Income Payments - Interest paid on external liabilities', 10),
('p11', 'Income Payments - Dividends/profits paid', 11),
('p12', 'Income Payments - Wages/Salaries', 12),
('p13', 'Service Payments - Transportation - Freight', 13),
('p14', 'Service Payments - Transportation - Passanger', 14),
('p15', 'Service Payments - Transportation - Other', 15),
('p16', 'Service Payments - Communication services', 16),
('p17', 'Service Payments - Construction services', 17),
('p18', 'Service Payments - Insurance & Re-insurance', 18),
('p19', 'Service Payments - Financial services', 19),
('p2', 'Currency Holdings/Deposits', 2),
('p20', 'Service Payments - Travel - Business/Official', 20),
('p21', 'Service Payments - Travel - Education', 21),
('p22', 'Service Payments - Travel - Medical', 22),
('p23', 'Service Payments - Travel - Other Personal', 23),
('p24', 'Service Payments - Computer & info services', 24),
('p25', 'Service Payments - Royalties & licence fees', 25),
('p26', 'Service Payments - Other business services', 26),
('p27', 'Service Payments - Personal, cultural, & recreational services', 27),
('p28', 'Service Payments - Governmen services, n.i.e', 28),
('p29', 'Transfers - NGO outflows', 29),
('p3', 'Govt. Imports', 3),
('p30', 'Transfers - Government Grants', 30),
('p31', 'Transfers - Worker''s remittances', 31),
('p32', 'Transfers - Other transfers', 32),
('p33', 'Foreign Direct equity Investment', 33),
('p34', 'Portfolio Investment - By Government', 34),
('p35', 'Portfolio Investment - By Banks', 35),
('p36', 'Portfolio Investment - By Other', 36),
('p37', 'Portfolio Investment - Other transfers', 37),
('p38', 'Loans Extended abroad - By commercial Banks - Short term', 38),
('p39', 'Loans Extended abroad - By commercial Banks - Long term', 39),
('p4', 'Private Imports - Oil', 4),
('p40', 'Loans Extended abroad - By Others - Private-Short term', 40),
('p41', 'Loans Extended abroad - By Others - Private-Long term', 41),
('p42', 'Loans Extended abroad - By Others - Government', 42),
('p43', 'Loan Repaymen (Principal)', 43),
('p44', 'Bank/bureaux', 44),
('p45', 'Interbank', 45),
('p46', 'Interbureaux', 46),
('p5', 'Private Imports - Gold', 5),
('p6', 'Private Imports - Repair', 6),
('p7', 'Private Imports - Goods produced in ports by carriers', 7),
('p8', 'Private Imports - Goods for processing', 8),
('p9', 'Private Imports - Other Imports', 9);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
