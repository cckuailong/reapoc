-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:3306
-- Generation Time: Sep 14, 2012 at 12:16 PM
-- Server version: 5.1.57
-- PHP Version: 5.2.6
-- 
-- PMPro Version: 1.5.2
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_discount_codes`
-- 

CREATE TABLE `wp_pmpro_discount_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `starts` date NOT NULL,
  `expires` date NOT NULL,
  `uses` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `starts` (`starts`),
  KEY `expires` (`expires`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_discount_codes_levels`
-- 

CREATE TABLE `wp_pmpro_discount_codes_levels` (
  `code_id` int(11) unsigned NOT NULL,
  `level_id` int(11) unsigned NOT NULL,
  `initial_payment` decimal(18,8) NOT NULL DEFAULT '0.00',
  `billing_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
  `cycle_number` int(11) NOT NULL DEFAULT '0',
  `cycle_period` enum('Day','Week','Month','Year') DEFAULT 'Month',
  `billing_limit` int(11) NOT NULL COMMENT 'After how many cycles should billing stop?',
  `trial_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
  `trial_limit` int(11) NOT NULL DEFAULT '0',
  `expiration_number` int(10) unsigned NOT NULL,
  `expiration_period` enum('Hour','Day','Week','Month','Year') NOT NULL,
  PRIMARY KEY (`code_id`,`level_id`),
  KEY `initial_payment` (`initial_payment`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_discount_codes_uses`
-- 

CREATE TABLE `wp_pmpro_discount_codes_uses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `timestamp` (`timestamp`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_membership_levels`
-- 

CREATE TABLE `wp_pmpro_membership_levels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `confirmation` longtext NOT NULL,
  `initial_payment` decimal(18,8) NOT NULL DEFAULT '0.00',
  `billing_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
  `cycle_number` int(11) NOT NULL DEFAULT '0',
  `cycle_period` enum('Day','Week','Month','Year') DEFAULT 'Month',
  `billing_limit` int(11) NOT NULL COMMENT 'After how many cycles should billing stop?',
  `trial_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
  `trial_limit` int(11) NOT NULL DEFAULT '0',
  `allow_signups` tinyint(4) NOT NULL DEFAULT '1',
  `expiration_number` int(10) unsigned NOT NULL,
  `expiration_period` enum('Hour','Day','Week','Month','Year') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `allow_signups` (`allow_signups`),
  KEY `initial_payment` (`initial_payment`),
  KEY `name` (`name`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_membership_levelmeta`
-- 

CREATE TABLE `wp_pmpro_membership_levelmeta` (
  `meta_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pmpro_membership_level_id` int(10) unsigned NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `pmpro_membership_level_id` (`pmpro_membership_level_id`),
  KEY `meta_key` (`meta_key`)
);

-- --------------------------------------------------------

--
-- Table structure for table `wp_pmpro_membership_ordermeta`
--

CREATE TABLE `wp_pmpro_membership_ordermeta` (
  `meta_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pmpro_membership_order_id` int(10) unsigned NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `pmpro_membership_order_id` (`pmpro_membership_order_id`),
  KEY `meta_key` (`meta_key`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_membership_orders`
-- 

CREATE TABLE `wp_pmpro_membership_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `session_id` varchar(64) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `membership_id` int(11) unsigned NOT NULL DEFAULT '0',
  `paypal_token` varchar(64) NOT NULL DEFAULT '',
  `billing_name` varchar(128) NOT NULL DEFAULT '',
  `billing_street` varchar(128) NOT NULL DEFAULT '',
  `billing_city` varchar(128) NOT NULL DEFAULT '',
  `billing_state` varchar(32) NOT NULL DEFAULT '',
  `billing_zip` varchar(16) NOT NULL DEFAULT '',
  `billing_country` varchar(128) NOT NULL,
  `billing_phone` varchar(32) NOT NULL,
  `subtotal` varchar(16) NOT NULL DEFAULT '',
  `tax` varchar(16) NOT NULL DEFAULT '',
  `couponamount` varchar(16) NOT NULL DEFAULT '',
  `checkout_id` int(11) NOT NULL DEFAULT '0',
  `certificate_id` int(11) NOT NULL DEFAULT '0',
  `certificateamount` varchar(16) NOT NULL DEFAULT '',
  `total` varchar(16) NOT NULL DEFAULT '',
  `payment_type` varchar(64) NOT NULL DEFAULT '',
  `cardtype` varchar(32) NOT NULL DEFAULT '',
  `accountnumber` varchar(32) NOT NULL DEFAULT '',
  `expirationmonth` char(2) NOT NULL DEFAULT '',
  `expirationyear` varchar(4) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT '',
  `gateway` varchar(64) NOT NULL,
  `gateway_environment` varchar(64) NOT NULL,
  `payment_transaction_id` varchar(64) NOT NULL,
  `subscription_transaction_id` varchar(32) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `affiliate_id` varchar(32) NOT NULL,
  `affiliate_subid` varchar(32) NOT NULL,
  `notes` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
   KEY `session_id` (`session_id`),
	KEY `user_id` (`user_id`),
	KEY `membership_id` (`membership_id`),
	KEY `status` (`status`),
	KEY `timestamp` (`timestamp`),
	KEY `gateway` (`gateway`),
	KEY `gateway_environment` (`gateway_environment`),
	KEY `payment_transaction_id` (`payment_transaction_id`),
	KEY `subscription_transaction_id` (`subscription_transaction_id`),
	KEY `affiliate_id` (`affiliate_id`),
	KEY `affiliate_subid` (`affiliate_subid`),
	KEY `checkout_id` (`checkout_id`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_memberships_categories`
-- 

CREATE TABLE `wp_pmpro_memberships_categories` (
  `membership_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `membership_category` (`membership_id`,`category_id`),
  UNIQUE KEY `category_membership` (`category_id`,`membership_id`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_memberships_pages`
-- 

CREATE TABLE `wp_pmpro_memberships_pages` (
  `membership_id` int(11) unsigned NOT NULL,
  `page_id` int(11) unsigned NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `category_membership` (`page_id`,`membership_id`),
  UNIQUE KEY `membership_page` (`membership_id`,`page_id`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `wp_pmpro_memberships_users`
-- 

CREATE TABLE `wp_pmpro_memberships_users` (
   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
   `user_id` int(11) unsigned NOT NULL,
   `membership_id` int(11) unsigned NOT NULL,
   `code_id` int(11) unsigned NOT NULL,
   `initial_payment` decimal(18,8) NOT NULL,
   `billing_amount` decimal(18,8) NOT NULL,
   `cycle_number` int(11) NOT NULL,
   `cycle_period` enum('Day','Week','Month','Year') NOT NULL DEFAULT 'Month',
   `billing_limit` int(11) NOT NULL,
   `trial_amount` decimal(18,8) NOT NULL,
   `trial_limit` int(11) NOT NULL,
   `status` varchar(20) NOT NULL DEFAULT 'active',
   `startdate` datetime NOT NULL,
   `enddate` datetime DEFAULT NULL,
   `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `membership_id` (`membership_id`),
   KEY `modified` (`modified`),
   KEY `code_id` (`code_id`),
   KEY `enddate` (`enddate`),
   KEY `user_id` (`user_id`),
   KEY `status` (`status`)
);
