<?php

namespace ProfilePress\Core\RegisterActivation;

use ProfilePress\Core\Base as CoreBase;

class CreateDBTables
{
    public static function make()
    {
        global $wpdb;

        $collate = '';
        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $forms_table              = CoreBase::form_db_table();
        $forms_meta_table         = CoreBase::form_meta_db_table();
        $meta_data_table          = CoreBase::meta_data_db_table();

        $sqls[] = "CREATE TABLE IF NOT EXISTS $forms_table (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  name varchar(100) NOT NULL,
                  form_id bigint(20) NOT NULL,
                  form_type varchar(20) NOT NULL DEFAULT '',
                  builder_type varchar(20) NOT NULL DEFAULT '',
                  date datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
                  PRIMARY KEY (id),
                  UNIQUE KEY name (name),
                  KEY form_id (form_id)
				) $collate;
				";
        $sqls[] = "CREATE TABLE IF NOT EXISTS $forms_meta_table (
                  meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  form_id bigint(20) NOT NULL,
                  form_type varchar(20) DEFAULT NULL,
                  meta_key varchar(255) DEFAULT NULL,
                  meta_value longtext,
                  PRIMARY KEY (meta_id),
                  KEY form_id (form_id),
                  KEY form_type (form_type),
                  KEY meta_key (meta_key)
				) $collate;
				";
        $sqls[] = "CREATE TABLE IF NOT EXISTS $meta_data_table (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  meta_key varchar(50) DEFAULT NULL,
                  meta_value longtext,
                  flag varchar(20) DEFAULT NULL,
                  PRIMARY KEY (id),
                  KEY meta_key (meta_key),
                  KEY flag (flag)
				) $collate;
				";

        $sqls = apply_filters('ppress_create_database_tables', $sqls, $collate);

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($sqls as $sql) {
            dbDelta($sql);
        }
    }
}