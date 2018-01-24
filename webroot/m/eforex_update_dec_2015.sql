ALTER TABLE `withdrawals`  ADD `reason` VARCHAR(20) NOT NULL DEFAULT 'FromBureau'  AFTER `user_id`;
ALTER TABLE `receivables` ADD `reason` VARCHAR(20) NOT NULL DEFAULT 'ToBureau' AFTER `user_id`
