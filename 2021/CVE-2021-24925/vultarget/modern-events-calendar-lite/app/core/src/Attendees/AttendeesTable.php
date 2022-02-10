<?php

namespace MEC\Attendees;

class AttendeesTable{

    public static $tbl = 'mec_attendees';
    public static $version = '1.1.0';

    public static function create_table(){

        if ( !function_exists( 'dbDelta' ) ) {

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

        global $wpdb;
        $attendees_table_name = $wpdb->prefix . self::$tbl;
        $db_option_key = 'mec_table_version_'.$attendees_table_name;

        $tables = $wpdb->get_results("SHOW TABLES");
        $tables_group = "Tables_in_".DB_NAME;
        $tables = array_column($tables,$tables_group);
        $table_exists = array_search($attendees_table_name,$tables);

        if(false === $table_exists){

            $charset = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS `{$attendees_table_name}` (
                `attendee_id` bigint(20) NOT NULL AUTO_INCREMENT,
                `post_id` bigint(20) NOT NULL,
                `event_id` bigint(20) NOT NULL,
                `occurrence` int(11) NOT NULL,
                `email` varchar(50) NOT NULL,
                `first_name` varchar(50) NOT NULL,
                `last_name` varchar(50) NOT NULL,
                `data` text NULL,
                `count` int(11) DEFAULT 1,
                `verification` int(1) DEFAULT 0,
                `confirmation` int(1) DEFAULT 0,
                PRIMARY KEY (`attendee_id`)
            ){$charset}";

            dbDelta( $sql );

            update_option( $db_option_key, static::$version );
        }else{

            $db_version = get_option( $db_option_key, '1.0.0' );

            if( version_compare($db_version, '1.1.0', '<') ){

                $wpdb->query( "ALTER TABLE `{$attendees_table_name}` CHANGE  IF EXISTS `name` `first_name` varchar(50) NOT NULL;" );
                $wpdb->query( "ALTER TABLE `{$attendees_table_name}` ADD IF NOT EXISTS `last_name` varchar(50) NOT NULL AFTER `first_name`;" );
                $wpdb->query( "ALTER TABLE `{$attendees_table_name}` ADD IF NOT EXISTS `verification` int(1) NOT NULL DEFAULT 0 AFTER `count`;" );
                $wpdb->query( "ALTER TABLE `{$attendees_table_name}` ADD  IF NOT EXISTS `confirmation` int(1) NOT NULL DEFAULT 0 AFTER `verification`;" );

                update_option( $db_option_key, '1.1.0' );
            }


            update_option( $db_option_key, static::$version );
        }





    }
}