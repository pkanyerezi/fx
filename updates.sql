ALTER TABLE `foxes` ADD `balance_with_safe` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
CREATE TABLE IF NOT EXISTS `opening_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currency_details` text,
  PRIMARY KEY (`id`)
);