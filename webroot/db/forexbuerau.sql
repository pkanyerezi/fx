-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 01, 2014 at 12:35 PM
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
-- Table structure for table `action_logs`
--

CREATE TABLE IF NOT EXISTS `action_logs` (
  `id` varchar(100) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `action_performed` text NOT NULL,
  `date_created` date NOT NULL,
  `date_time_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `additional_profits`
--

CREATE TABLE IF NOT EXISTS `additional_profits` (
  `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `amount` bigint(13) unsigned NOT NULL,
  `additional_info` text,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_at_bank_foreigns`
--

CREATE TABLE IF NOT EXISTS `cash_at_bank_foreigns` (
  `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `amount` bigint(13) unsigned NOT NULL,
  `bank_name` varchar(20) NOT NULL,
  `currency_id` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cash_at_bank_ugxes`
--

CREATE TABLE IF NOT EXISTS `cash_at_bank_ugxes` (
  `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `amount` bigint(13) unsigned NOT NULL,
  `bank_name` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` varchar(100) NOT NULL,
  `contact_list_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone_number` bigint(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `contact_list_id`, `name`, `phone_number`, `email`, `date`) VALUES
('a6fce3ed9b8bec401adfd38611a4a859', '8a869ca9c10a66cc6e470143b3eed772', 'namanya', 256704543171, '', '2013-10-15');

-- --------------------------------------------------------

--
-- Table structure for table `contact_lists`
--

CREATE TABLE IF NOT EXISTS `contact_lists` (
  `id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'unknow',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contact_lists`
--

INSERT INTO `contact_lists` (`id`, `name`, `date`) VALUES
('8a869ca9c10a66cc6e470143b3eed772', 'eforex', '2013-10-15 01:27:08');

-- --------------------------------------------------------

--
-- Table structure for table `creditors`
--

CREATE TABLE IF NOT EXISTS `creditors` (
  `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `customer` varchar(30) NOT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `amount` bigint(13) unsigned NOT NULL,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` varchar(5) NOT NULL,
  `description` varchar(10) NOT NULL,
  `arrangement` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `description`, `arrangement`) VALUES
('c00', '--select--', 0),
('c1', 'USD', 1),
('c2', 'Euro', 3),
('c3', 'GBP', 2),
('c4', 'Kshs', 4),
('c5', 'Tzshs', 5),
('c6', 'SAR', 6),
('c7', 'SP', 7),
('c8', 'Others', 8);

-- --------------------------------------------------------

--
-- Table structure for table `daily_buying_returns`
--

CREATE TABLE IF NOT EXISTS `daily_buying_returns` (
  `id` varchar(20) NOT NULL,
  `fox_id` int(11) NOT NULL,
  `daily_return_id` bigint(20) NOT NULL,
  `c1` double NOT NULL DEFAULT '0',
  `c2` double NOT NULL DEFAULT '0',
  `c3` double NOT NULL DEFAULT '0',
  `c4` double NOT NULL DEFAULT '0',
  `c5` double NOT NULL DEFAULT '0',
  `c6` double NOT NULL DEFAULT '0',
  `c7` double NOT NULL DEFAULT '0',
  `c8` double NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `daily_returns`
--

CREATE TABLE IF NOT EXISTS `daily_returns` (
  `id` bigint(20) NOT NULL,
  `fox_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `daily_buying_return_id` bigint(20) NOT NULL,
  `daily_selling_return_id` bigint(20) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `daily_selling_returns`
--

CREATE TABLE IF NOT EXISTS `daily_selling_returns` (
  `id` varchar(20) NOT NULL,
  `fox_id` int(11) NOT NULL,
  `daily_return_id` bigint(20) NOT NULL,
  `c1` double NOT NULL DEFAULT '0',
  `c2` double NOT NULL DEFAULT '0',
  `c3` double NOT NULL DEFAULT '0',
  `c4` double NOT NULL DEFAULT '0',
  `c5` double NOT NULL DEFAULT '0',
  `c6` double NOT NULL DEFAULT '0',
  `c7` double NOT NULL DEFAULT '0',
  `c8` double NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `debtors`
--

CREATE TABLE IF NOT EXISTS `debtors` (
  `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `customer` varchar(30) NOT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `amount` bigint(13) unsigned NOT NULL,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `deleted_purchased_receipts`
--

CREATE TABLE IF NOT EXISTS `deleted_purchased_receipts` (
  `id` varchar(20) NOT NULL,
  `fox_id` bigint(11) NOT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `amount` double NOT NULL,
  `purchased_purpose_id` varchar(5) NOT NULL DEFAULT 'p0',
  `rate` double NOT NULL DEFAULT '0',
  `amount_ugx` double NOT NULL DEFAULT '0',
  `currency_id` varchar(5) NOT NULL DEFAULT 'c0',
  `date` date NOT NULL,
  `t_time` time DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-printed,0-not_printed',
  `is_uploaded` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-receipt is not yet sent/uploaded to BOU online,1-means the opposite',
  `nationality` varchar(20) NOT NULL DEFAULT 'Uganda',
  `address` varchar(50) NOT NULL DEFAULT 'Kampala',
  `passport_number` varchar(20) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `other_name` varchar(10) DEFAULT NULL,
  `orig_amount` double DEFAULT NULL,
  `orig_rate` double DEFAULT NULL,
  `other_currency_id` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deleted_sold_receipts`
--

CREATE TABLE IF NOT EXISTS `deleted_sold_receipts` (
  `id` varchar(20) NOT NULL,
  `fox_id` bigint(11) NOT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `amount` double NOT NULL,
  `purpose_id` varchar(5) NOT NULL DEFAULT 'p0',
  `rate` double NOT NULL DEFAULT '0',
  `amount_ugx` double NOT NULL DEFAULT '0',
  `currency_id` varchar(5) NOT NULL DEFAULT 'c0',
  `instrument` varchar(15) DEFAULT NULL,
  `date` date NOT NULL,
  `t_time` time DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-printed,0-not_printed',
  `is_uploaded` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-not iploaded,1-uploaded',
  `nationality` varchar(20) NOT NULL DEFAULT 'Uganda',
  `address` varchar(50) NOT NULL DEFAULT 'Kampala',
  `passport_number` varchar(20) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `other_name` varchar(10) DEFAULT NULL,
  `orig_amount` double DEFAULT NULL,
  `orig_rate` double DEFAULT NULL,
  `other_currency_id` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` varchar(100) NOT NULL,
  `item_id` varchar(100) NOT NULL,
  `description` text,
  `amount` double NOT NULL,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `foxes`
--

CREATE TABLE IF NOT EXISTS `foxes` (
  `id` bigint(32) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `location` text NOT NULL,
  `un` varchar(20) NOT NULL,
  `pwd` varchar(20) NOT NULL,
  `k` varchar(20) NOT NULL,
  `url` varchar(100) NOT NULL,
  `weekends` varchar(30) NOT NULL,
  `prev_d` date DEFAULT NULL,
  `initial_position` double NOT NULL DEFAULT '0',
  `last_backup` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `foxes`
--

INSERT INTO `foxes` (`id`, `name`, `location`, `un`, `pwd`, `k`, `url`, `weekends`, `prev_d`, `initial_position`, `last_backup`) VALUES
(9265236542, 'Blueprint Softwares', 'Hotel Equatorial, Tel +256 0414253565, 0776931665', 'fxbomboo', 'password', 'FOREXBOU', 'http://efox.blueprintug.com', '', '2014-03-31', 0, '2014-03-12');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` varchar(100) NOT NULL,
  `name` varchar(35) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`) VALUES
('09fd5b614e5c4d2548b15cfc2fca12b8', 'Electricity'),
('20b10ccb0ae3975a72566e9c868e3c2b', 'Water'),
('3b307e7d792fcf2a43a51b1ab389f561', 'Internet'),
('3cd577f9955a877ea969d1ca2c15a71c', 'Auditor'),
('3da7e01d75dd84256b57dd59bbf60f3f', 'Stationary'),
('84adc56d50462697be95920076c1068f', 'Rent'),
('a7a96e8e70b623814a2f67a88e5e9da6', 'Air Time'),
('ad8989363bdd11d7e4d9e15407d7b4eb', 'News papers'),
('c360bc2b7700398d5e15e025f098ad02', 'Salary'),
('ef4dca39a73e25f2ac648572220891ad', 'Other Office expenses'),
('fcdb3677983417e173a7c18d37feab76', 'Staff lunch and transport');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` varchar(36) NOT NULL,
  `user_id` varchar(36) DEFAULT NULL,
  `sender_id` varchar(36) DEFAULT NULL,
  `type` text,
  `message` text,
  `target` text,
  `is_read` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `openings`
--

CREATE TABLE IF NOT EXISTS `openings` (
  `id` varchar(100) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `opening_ugx` double NOT NULL DEFAULT '0',
  `c1a` double NOT NULL DEFAULT '0',
  `c1r` double NOT NULL DEFAULT '0',
  `c2a` double NOT NULL DEFAULT '0',
  `c2r` double NOT NULL DEFAULT '0',
  `c3a` double NOT NULL DEFAULT '0',
  `c3r` double NOT NULL DEFAULT '0',
  `c4a` double NOT NULL DEFAULT '0',
  `c4r` double NOT NULL DEFAULT '0',
  `c5a` double NOT NULL DEFAULT '0',
  `c5r` double NOT NULL DEFAULT '0',
  `c6a` double NOT NULL DEFAULT '0',
  `c6r` double NOT NULL DEFAULT '0',
  `c7a` double NOT NULL DEFAULT '0',
  `c7r` double NOT NULL DEFAULT '0',
  `c8a` double NOT NULL DEFAULT '0',
  `c8r` double NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `other_currencies` text COMMENT 'stores json string of the other currency openings',
  `total_profit` double NOT NULL DEFAULT '0',
  `total_gross_profit` double NOT NULL DEFAULT '0',
  `total_expenses` double NOT NULL DEFAULT '0',
  `receivable_cash` double NOT NULL DEFAULT '0',
  `withdrawal_cash` double NOT NULL DEFAULT '0',
  `additional_profits` double NOT NULL DEFAULT '0',
  `total_sales_ugx` double NOT NULL DEFAULT '0',
  `total_purchases_ugx` double NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-new,1-old',
  `cash_at_bank_foreign` double NOT NULL DEFAULT '0',
  `cash_at_bank_ugx` double NOT NULL DEFAULT '0',
  `debtors` double NOT NULL DEFAULT '0',
  `creditors` double NOT NULL DEFAULT '0',
  `close_ugx` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `other_currencies`
--

CREATE TABLE IF NOT EXISTS `other_currencies` (
  `id` varchar(5) NOT NULL,
  `name` varchar(5) NOT NULL,
  `description` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `purchased_receipts`
--

CREATE TABLE IF NOT EXISTS `purchased_receipts` (
  `id` varchar(20) NOT NULL,
  `fox_id` bigint(11) NOT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `amount` double NOT NULL,
  `purchased_purpose_id` varchar(5) NOT NULL DEFAULT 'p0',
  `rate` double NOT NULL DEFAULT '0',
  `amount_ugx` double NOT NULL DEFAULT '0',
  `currency_id` varchar(5) NOT NULL DEFAULT 'c0',
  `date` date NOT NULL,
  `t_time` time DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-printed,0-not_printed',
  `is_uploaded` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-receipt is not yet sent/uploaded to BOU online,1-means the opposite',
  `nationality` varchar(20) NOT NULL DEFAULT 'Uganda',
  `address` varchar(50) NOT NULL DEFAULT 'Kampala',
  `passport_number` varchar(20) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `other_name` varchar(10) DEFAULT NULL,
  `orig_amount` double DEFAULT NULL,
  `orig_rate` double DEFAULT NULL,
  `other_currency_id` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `receipt_tracks`
--

CREATE TABLE IF NOT EXISTS `receipt_tracks` (
  `id` int(5) NOT NULL,
  `my_count_sold_receipts` bigint(20) NOT NULL DEFAULT '1',
  `my_count_purchased_receipts` bigint(20) NOT NULL DEFAULT '1',
  `year` year(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `receipt_tracks`
--

INSERT INTO `receipt_tracks` (`id`, `my_count_sold_receipts`, `my_count_purchased_receipts`, `year`) VALUES
(1001, 0, 0, 2013);

-- --------------------------------------------------------

--
-- Table structure for table `receivables`
--

CREATE TABLE IF NOT EXISTS `receivables` (
  `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `customer` varchar(30) NOT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `amount` bigint(13) unsigned NOT NULL,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rest_logs`
--

CREATE TABLE IF NOT EXISTS `rest_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `controller` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `model_id` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `requested` datetime NOT NULL,
  `apikey` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `httpcode` smallint(3) unsigned NOT NULL,
  `error` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ratelimited` tinyint(1) unsigned NOT NULL,
  `data_in` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `meta` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_out` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `responded` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `saves`
--

CREATE TABLE IF NOT EXISTS `saves` (
  `id` int(11) NOT NULL,
  `opening_ugx` double NOT NULL DEFAULT '0',
  `c1a` double NOT NULL DEFAULT '0',
  `c2a` double NOT NULL DEFAULT '0',
  `c3a` double NOT NULL DEFAULT '0',
  `c4a` double NOT NULL DEFAULT '0',
  `c5a` double NOT NULL DEFAULT '0',
  `c6a` double NOT NULL DEFAULT '0',
  `c7a` double NOT NULL DEFAULT '0',
  `c8a` double NOT NULL DEFAULT '0',
  `other_currencies` text COMMENT 'stores json string of the other currency openings',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `saves`
--

INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES
(1111111111, 0, 0, 0, 0, 0, 0, 0, 0, 0, '{"data":[]}');

-- --------------------------------------------------------

--
-- Table structure for table `sold_receipts`
--

CREATE TABLE IF NOT EXISTS `sold_receipts` (
  `id` varchar(20) NOT NULL,
  `fox_id` bigint(11) NOT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `amount` double NOT NULL,
  `purpose_id` varchar(5) NOT NULL DEFAULT 'p0',
  `rate` double NOT NULL DEFAULT '0',
  `amount_ugx` double NOT NULL DEFAULT '0',
  `currency_id` varchar(5) NOT NULL DEFAULT 'c0',
  `instrument` varchar(15) DEFAULT NULL,
  `date` date NOT NULL,
  `t_time` time DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-printed,0-not_printed',
  `is_uploaded` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-not iploaded,1-uploaded',
  `nationality` varchar(20) NOT NULL DEFAULT 'Uganda',
  `address` varchar(50) NOT NULL DEFAULT 'Kampala',
  `passport_number` varchar(20) DEFAULT NULL,
  `user_id` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `other_name` varchar(10) DEFAULT NULL,
  `orig_amount` double DEFAULT NULL,
  `orig_rate` double DEFAULT NULL,
  `other_currency_id` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `username` varchar(30) NOT NULL,
  `slug` varchar(50) NOT NULL DEFAULT '''''',
  `password` varchar(100) NOT NULL,
  `password_token` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_token` varchar(255) DEFAULT NULL,
  `email_token_expires` datetime DEFAULT NULL,
  `tos` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_action` datetime DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `profile_image` varchar(50) NOT NULL DEFAULT 'default.png',
  `role` varchar(15) NOT NULL DEFAULT 'regular',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `officer_name` varchar(30) DEFAULT NULL,
  `officer_title` varchar(15) DEFAULT NULL,
  `officer_phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9631 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `slug`, `password`, `password_token`, `email`, `email_verified`, `email_token`, `email_token_expires`, `tos`, `active`, `last_login`, `last_action`, `is_admin`, `profile_image`, `role`, `date`, `officer_name`, `officer_title`, `officer_phone`) VALUES
(2, 'Super Admin', 'username', '''''', 'd6884171db9483334bd95325fd531df68d89263b', NULL, 'superadmin@gmail.com', 0, NULL, NULL, 0, 0, NULL, NULL, 1, 'default.png', 'super_admin', '2013-04-02 21:00:00', NULL, NULL, NULL),
(3, 'Cashier1', 'username1', '''''', 'd6884171db9483334bd95325fd531df68d89263b', NULL, 'cashier1@gmail.com', 0, NULL, NULL, 0, 0, NULL, NULL, 0, 'default.png', 'regular', '2013-06-04 17:50:03', NULL, NULL, NULL),
(4, 'client1', '3a30a0f31ac1eeb2e609ec448e931a', '''''', 'b076873179aa77a8b50396f4f318485ee8ee6753', NULL, '0704543171', 0, NULL, NULL, 0, 0, NULL, NULL, 0, 'default.png', 'customer', '0000-00-00 00:00:00', NULL, NULL, NULL),
(9630, 'client2', '877d4745a3cf325d86cd2a3cefc1ef', '''''', '694c13fa381b583ff3ebc4275fe416023ecc57b7', NULL, '0700883738', 0, NULL, NULL, 0, 0, NULL, NULL, 0, 'default.png', 'customer', '0000-00-00 00:00:00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE IF NOT EXISTS `withdrawals` (
  `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(100) NOT NULL,
  `customer` varchar(30) NOT NULL,
  `amount` bigint(13) unsigned NOT NULL,
  `additional_info` text,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
