ALTER TABLE  `sold_receipts` ADD  `remote_addr` VARCHAR( 20 ) NOT NULL DEFAULT  '127.0.0.1';
ALTER TABLE  `purchased_receipts` ADD  `remote_addr` VARCHAR( 20 ) NOT NULL DEFAULT  '127.0.0.1';
ALTER TABLE  `multiple_print_receipts` ADD  `remote_addr` VARCHAR( 20 ) NOT NULL DEFAULT  '127.0.0.1';
ALTER TABLE `users` ADD `printing_place` INT( 1 ) NOT NULL DEFAULT '1' COMMENT '1-MainPC, 2-OperationalPC' AFTER `officer_phone` ;
ALTER TABLE `purchased_receipts` ADD `phone_number` VARCHAR( 20 ) NULL;
ALTER TABLE `sold_receipts` ADD `phone_number` VARCHAR( 20 ) NULL;
ALTER TABLE  `users` ADD  `can_edit_receipt` TINYINT( 1 ) NOT NULL DEFAULT  '0',
ADD  `can_delete_receipt` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `users` ADD  `password_last_changed_on` DATE NULL ;
ALTER TABLE `users` ADD `can_view_safe` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE  `foxes` ADD  `reports_notification_emails` TEXT NULL ;
ALTER TABLE `users` ADD `can_download_FIA_large_cash` TINYINT( 1 ) NOT NULL DEFAULT '0',
ADD `can_view_cashflow` TINYINT( 1 ) NOT NULL DEFAULT '0',
ADD `can_view_currency_summary` TINYINT( 1 ) NOT NULL DEFAULT '0',
ADD `can_download_receipts_excelfile` TINYINT( 1 ) NOT NULL DEFAULT '0',
ADD `can_view_sales_and_purchase_returns` TINYINT( 1 ) NOT NULL DEFAULT '0',
ADD `can_view_large_cash_receipts` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `users` ADD `can_view_closing_balance_summary` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE  `users` ADD  `require_board_rate` TINYINT NOT NULL DEFAULT  '1';
ALTER TABLE  `users` CHANGE  `require_board_rate`  `require_board_rate` TINYINT( 1 ) NOT NULL DEFAULT  '1';
ALTER TABLE `users` ADD `identication_type` VARCHAR( 30 ) NULL ,
ADD `identication_number` VARCHAR( 50 ) NULL ,
ADD `address` VARCHAR( 255 ) NULL ,
ADD `other_details` TEXT NULL ;

CREATE TABLE IF NOT EXISTS `safe_transactions` (
  `id` bigint(13) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `currency` varchar(10) NOT NULL,
  `transaction_from` varchar(255) DEFAULT NULL,
  `transaction_to` varchar(255) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL DEFAULT 'TRANSFER',
  `status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `comment` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `report_notification_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `report_type_id` int(11) NOT NULL,
  `emails` text NOT NULL,
  `frequency_number` int(11) NOT NULL DEFAULT '1' COMMENT 'e.g 1,2',
  `frequency_type` varchar(10) NOT NULL COMMENT 'Minutes|Days|Weeks|Months',
  `recursive` tinyint(1) NOT NULL DEFAULT '1',
  `records_time_ago_number` int(11) NOT NULL DEFAULT '1',
  `records_time_ago_type` varchar(10) NOT NULL,
  `start_at` datetime DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `last_notification_time` datetime DEFAULT NULL,
  `next_notification_time` datetime NOT NULL,
  `succeded` int(11) NOT NULL DEFAULT '0' COMMENT 'number of success tyms for the first email',
  `failed` int(11) NOT NULL DEFAULT '0' COMMENT 'number of failed tyms for the first email',
  `retry` int(11) NOT NULL DEFAULT '0' COMMENT 'should this job be retried or not',
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `report_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `sort_order` int(2) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
);

INSERT INTO `report_types` (`id`, `name`, `description`, `sort_order`, `enabled`) VALUES
(1, 'Sales and Purchase Receipts', 'Sales and Purchase Receipts', 1, 1),
(2, 'Sales and Purchase Returns Weekly', 'Sales and Purchase Returns Weekly', 2, 1),
(3, 'Sales and Purchase Returns Monthly', 'Sales and Purchase Returns Monthly', 3, 1),
(4, 'BOU Large Cash', 'BOU Large Cash', 4, 1),
(5, 'FIA LargeCash', 'FIA LargeCash', 5, 1),
(6, 'Cash Flow', 'Cash Flow', 6, 1),
(7, 'Currency Summary', 'Currency Summary', 7, 0);
ALTER TABLE `foxes` ADD `server_public_ip` VARCHAR( 16 ) NOT NULL DEFAULT '127.0.0.1' AFTER `reports_notification_emails` ;
ALTER TABLE `openings` ADD `transfers_made` TEXT NULL;

ALTER TABLE `users` ADD `balance_with_all_purchases_from_other_cashiers` TINYINT NOT NULL DEFAULT '0';