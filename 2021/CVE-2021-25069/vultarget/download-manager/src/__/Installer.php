<?php

namespace WPDM\__;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Installer
    {
        /**
         * @var float
         */
        private $dbVersion = 534.0;

        function __construct()
        {

        }

        /**
         * @return float
         */
        public static function dbVersion(){
            $inst = new Installer();
            return $inst->dbVersion;
        }

        public static function dbUpdateRequired(){
            return (Installer::dbVersion() !== (double)get_option('__wpdm_db_version'));
        }

        public static function init(){
            self::updateDB();
        }

        public static function updateDB()
        {

            global $wpdb;

            delete_option('wpdm_latest');

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ahm_download_stats` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `pid` bigint(20) NOT NULL,
              `uid` int(11) NOT NULL,
              `oid` varchar(100) NOT NULL,
              `year` int(4) NOT NULL,
              `month` int(2) NOT NULL,
              `day` int(2) NOT NULL,
              `timestamp` int(11) NOT NULL,
              `ip` varchar(20) NOT NULL,
              `filename` text,
              `agent` text,         
              PRIMARY KEY (`id`)
            )";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ahm_emails` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `email` varchar(255) NOT NULL,
              `pid` bigint(20) NOT NULL,
              `date` int(11) NOT NULL,
              `custom_data` text NOT NULL,
              `request_status` INT( 1 ) NOT NULL,
              PRIMARY KEY (`id`)
            )";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ahm_social_conns` (
              `ID` bigint(20) NOT NULL AUTO_INCREMENT,
              `pid` bigint(20) NOT NULL,
              `email` varchar(200) NOT NULL,
              `name` varchar(200) NOT NULL,
              `user_data` text NOT NULL,
              `access_token` text NOT NULL,
              `refresh_token` text NOT NULL,
              `source` varchar(200) NOT NULL,
              `timestamp` int(11) NOT NULL,
              `processed` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`ID`)
            )";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ahm_assets` (
              `ID` bigint(20) NOT NULL AUTO_INCREMENT,
              `path` text NOT NULL,
              `owner` int(11) NOT NULL,
              `activities` text NOT NULL,
              `comments` text NOT NULL,
              `access` text NOT NULL,
              `metadata` text NOT NULL,
              PRIMARY KEY (`ID`)
            )";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ahm_asset_links` (
              `ID` bigint(20) NOT NULL AUTO_INCREMENT,
              `asset_ID` bigint(20) NOT NULL,
              `asset_key` varchar(255) NOT NULL,
              `access` text NOT NULL,
              `time` int(11) NOT NULL,
              PRIMARY KEY (`ID`)
            )";

            $sqls[] = "DROP TABLE IF EXISTS `{$wpdb->prefix}ahm_sessions`";
            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ahm_sessions` (
              `ID` bigint(20) NOT NULL AUTO_INCREMENT,
              `deviceID` varchar(255) NOT NULL,
              `name` varchar(255) NOT NULL,
              `value` text NOT NULL,
              `lastAccess` int(11) NOT NULL,
              `expire` int(11) NOT NULL,
              PRIMARY KEY (`ID`)
            )";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ahm_user_download_counts` (
              `ID` int(11) NOT NULL AUTO_INCREMENT,
              `user` varchar(255) NOT NULL,
              `package_id` int(11) NOT NULL,
              `download_count` int(11) NOT NULL,
              PRIMARY KEY (`ID`)                          
            )";

            foreach ($sqls as $qry) {
                $wpdb->query($qry);
            }

            $installer = new Installer();

            $installer->addColumn('ahm_download_stats', 'version', 'varchar(255) NOT NULL');
            $installer->addColumn('ahm_download_stats', 'agent', 'TEXT');
            $installer->addColumn('ahm_download_stats', 'filename', 'TEXT');
            $installer->addColumn('ahm_emails', 'request_status', "INT(1) NOT NULL");
            $installer->uniqueKey('ahm_asset_links', "asset_key");

            $ach = get_option("__wpdm_activation_history", array());
            $ach = maybe_unserialize($ach);
            $ach[] = time();
            update_option("__wpdm_activation_history", $ach, false);
            update_option('__wpdm_db_version', $installer->dbVersion, false);
	        $__wpdm_ui_colors = get_option('__wpdm_ui_colors', false);
	        if(!$__wpdm_ui_colors)
				update_option('__wpdm_ui_colors', 'a:18:{s:7:"primary";s:7:"#4a8eff";s:13:"primary_hover";s:7:"#5998ff";s:14:"primary_active";s:7:"#3281ff";s:9:"secondary";s:7:"#6c757d";s:15:"secondary_hover";s:7:"#6c757d";s:16:"secondary_active";s:7:"#6c757d";s:4:"info";s:7:"#2CA8FF";s:10:"info_hover";s:7:"#2CA8FF";s:11:"info_active";s:7:"#2CA8FF";s:7:"success";s:7:"#018e11";s:13:"success_hover";s:7:"#0aad01";s:14:"success_active";s:7:"#0c8c01";s:7:"warning";s:7:"#FFB236";s:13:"warning_hover";s:7:"#FFB236";s:14:"warning_active";s:7:"#FFB236";s:6:"danger";s:7:"#ff5062";s:12:"danger_hover";s:7:"#ff5062";s:13:"danger_active";s:7:"#ff5062";}', false);

        }


        function addColumn($table, $column, $type_n_default = 'TEXT NOT NULL')
        {
            global $wpdb;
            $result = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}{$table}` LIKE '$column'");
            $exists = count($result) > 0;
            if (!$exists)
                $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` ADD `{$column}` {$type_n_default}");
        }

        function changeColumn($table, $column, $newName, $type_n_default = 'TEXT NOT NULL')
        {
            global $wpdb;
            $result = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}{$table}` LIKE '$newName'");
            $exists = count($result) > 0;
            if ($exists)
                $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` CHANGE `{$column}` `{$newName}` {$type_n_default}");
        }

        function primaryKey($table, $column)
        {
            global $wpdb;
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` ADD PRIMARY KEY(`{$column}`)");
        }

        function uniqueKey($table, $column)
        {
            global $wpdb;
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` ADD UNIQUE(`{$column}`)");
        }
    }



