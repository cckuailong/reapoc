CREATE TABLE IF NOT EXISTS `#__mec_events` (
  `id` int(10) NOT NULL,
  `post_id` int(10) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `repeat` tinyint(4) NOT NULL DEFAULT '0',
  `rinterval` varchar(10) COLLATE [:COLLATE:] DEFAULT NULL,
  `year` varchar(80) COLLATE [:COLLATE:] DEFAULT NULL,
  `month` varchar(80) COLLATE [:COLLATE:] DEFAULT NULL,
  `day` varchar(80) COLLATE [:COLLATE:] DEFAULT NULL,
  `week` varchar(80) COLLATE [:COLLATE:] DEFAULT NULL,
  `weekday` varchar(80) COLLATE [:COLLATE:] DEFAULT NULL,
  `weekdays` varchar(80) COLLATE [:COLLATE:] DEFAULT NULL
) DEFAULT CHARSET=[:CHARSET:] COLLATE=[:COLLATE:] AUTO_INCREMENT=1;

ALTER TABLE `#__mec_events` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `ID` (`id`), ADD UNIQUE KEY `post_id` (`post_id`);
ALTER TABLE `#__mec_events` MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

ALTER TABLE `#__mec_events` ADD `days` TEXT NULL DEFAULT NULL, ADD `time_start` INT(10) NOT NULL DEFAULT '0', ADD `time_end` INT(10) NOT NULL DEFAULT '0';

ALTER TABLE `#__mec_events` ADD `not_in_days` TEXT NOT NULL DEFAULT '' AFTER `days`;
ALTER TABLE `#__mec_events` CHANGE `days` `days` TEXT NOT NULL DEFAULT '';

ALTER TABLE `#__mec_events` ADD INDEX (`start`, `end`, `repeat`, `rinterval`, `year`, `month`, `day`, `week`, `weekday`, `weekdays`, `time_start`, `time_end`);

CREATE TABLE IF NOT EXISTS `#__mec_dates` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) NOT NULL,
  `dstart` date NOT NULL,
  `dend` date NOT NULL,
  `tstart` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `tend` int(11) UNSIGNED NOT NULL DEFAULT '0'
) DEFAULT CHARSET=[:CHARSET:] COLLATE=[:COLLATE:];

ALTER TABLE `#__mec_dates` ADD PRIMARY KEY (`id`), ADD KEY `post_id` (`post_id`), ADD KEY `tstart` (`tstart`), ADD KEY `tend` (`tend`);
ALTER TABLE `#__mec_dates` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__mec_dates` ADD `public` INT(4) UNSIGNED NOT NULL DEFAULT 1 AFTER `tend`;

CREATE TABLE IF NOT EXISTS `#__mec_occurrences` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `occurrence` int(10) UNSIGNED NOT NULL,
  `params` text COLLATE [:COLLATE:]
) DEFAULT CHARSET=[:CHARSET:] COLLATE=[:COLLATE:];

ALTER TABLE `#__mec_occurrences` ADD PRIMARY KEY (`id`), ADD KEY `post_id` (`post_id`), ADD KEY `occurrence` (`occurrence`);
ALTER TABLE `#__mec_occurrences` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `#__mec_users` (
  `id` int(10) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(127) NOT NULL,
  `reg` TEXT NULL DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET=[:CHARSET:] COLLATE=[:COLLATE:];

ALTER TABLE `#__mec_users` ADD PRIMARY KEY (`id`);
ALTER TABLE `#__mec_users` MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__mec_users` AUTO_INCREMENT=1000000;
ALTER TABLE `#__mec_users` ADD UNIQUE KEY `email` (`email`);

CREATE TABLE IF NOT EXISTS `#__mec_bookings` (
  `id` int UNSIGNED NOT NULL,
  `booking_id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED NOT NULL,
  `ticket_ids` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `confirmed` tinyint NOT NULL DEFAULT '0',
  `verified` tinyint NOT NULL DEFAULT '0',
  `all_occurrences` tinyint NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `timestamp` int UNSIGNED NOT NULL
) DEFAULT CHARSET=[:CHARSET:] COLLATE=[:COLLATE:];

ALTER TABLE `#__mec_bookings` ADD PRIMARY KEY (`id`);
ALTER TABLE `#__mec_bookings` MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `#__mec_bookings` ADD KEY `event_id` (`event_id`,`ticket_ids`,`status`,`confirmed`,`verified`,`date`);
ALTER TABLE `#__mec_bookings` ADD KEY `booking_id` (`booking_id`);
ALTER TABLE `#__mec_bookings` ADD KEY `timestamp` (`timestamp`);
ALTER TABLE `#__mec_bookings` ADD `transaction_id` VARCHAR(20) NULL AFTER `booking_id`;

ALTER TABLE `#__mec_bookings` ADD `user_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `booking_id`;
ALTER TABLE `#__mec_bookings` ADD INDEX (`user_id`);