<?php

/**
 * Db creater class
 *
 * Initializes or creates the whole database of the plugin
 *
 * @link       http://registration_magic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/includes
 */
class RM_Table_Tech
{

    public static $instance;
    public static $table_name_for;

    public function __construct()
    {
        //global $wpdb;

        $prefix = 'rm_';
        self::$table_name_for = array();
        self::$table_name_for['FORMS'] = $prefix . 'forms';
        self::$table_name_for['FIELDS'] = $prefix . 'fields';
        self::$table_name_for['SUBMISSIONS'] = $prefix . 'submissions';
        self::$table_name_for['SUBMISSION_FIELDS'] = $prefix . 'submission_fields';
//      self::$table_name_for['FORM_RESPONSES'] = $prefix . 'form_responses';
        self::$table_name_for['PAYPAL_FIELDS'] = $prefix . 'paypal_fields';
        self::$table_name_for['PAYPAL_LOGS'] = $prefix . 'paypal_logs';
        self::$table_name_for['FRONT_USERS'] = $prefix . 'front_users';
        self::$table_name_for['STATS'] = $prefix . 'stats';
        self::$table_name_for['NOTES'] = $prefix . 'notes';
        self::$table_name_for['WP_USERS'] = 'users';
        self::$table_name_for['WP_USERS_META'] = 'usermeta';
        self::$table_name_for['SESSIONS'] = $prefix.'sessions';
        self::$table_name_for['SENT_EMAILS'] = $prefix.'sent_mails';
        self::$table_name_for['LOGIN'] = $prefix . 'login';
        self::$table_name_for['LOGIN_LOG'] = $prefix . 'login_log';
    }

    public function __wakeup()
    {
        
    }

    public function __clone()
    {
        
    }

    public static function get_instance()
    {
        if (null === static::$instance)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    //Multi-site specific hook functions
    //hook: wpmu_new_blog
    public static function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta)
    {
        // Makes sure the plugin is defined before trying to use it
        if (!function_exists('is_plugin_active_for_network'))
        {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

        if (is_plugin_active_for_network(RM_PLUGIN_BASENAME))
        {
            switch_to_blog($blog_id);
            self::create_tables_per_site();
            RM_Utilities::create_submission_page();
            restore_current_blog();
        }
    }

    //hook: wpmu_drop_tables
    public static function on_delete_blog($tables)
    {
        global $wpdb;

        foreach (self::$table_name_for as $table_name)
            $tables[] = $table_name;

        return $tables;
    }

    public static function create_tables($network_wide)
    {  
        global $wpdb;

        if (is_multisite() && $network_wide)
        {
            // store the current blog id
            $current_blog = $wpdb->blogid;
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_ids as $blog_id)
            {
                switch_to_blog($blog_id);
                self::create_tables_per_site();
                restore_current_blog();
            }
            
        } else
        {
            self::create_tables_per_site();
        }
    }

    public static function create_tables_per_site()
    {
        require_once( ABSPATH . 'wp-includes/wp-db.php');
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        global $wpdb;

        $table_name = self::get_table_name_for('FORMS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `form_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `form_name` VARCHAR(1000 ),
                    `form_type` INT(6),
                    `form_user_role` VARCHAR(1000),
                    `default_user_role` VARCHAR(255),
                    `form_should_send_email` TINYINT(1),
                    `form_redirect` VARCHAR(10),
                    `form_redirect_to_page` VARCHAR(500),
                    `form_redirect_to_url` VARCHAR(500),
                    `form_should_auto_expire` TINYINT(1),
                    `form_options` TEXT,
                    `published_pages` TEXT,
                    `created_on` DATETIME,
                    `created_by` INT(6),
                    `modified_on` DATETIME,
                    `modified_by` INT(6)
                    )$charset_collate;";

            dbDelta($sql);
        }

        $table_name = self::get_table_name_for('FIELDS');
        //Alteration sql: ALTER TABLE `$wpfx_rm_fields` ADD `page_no` INT(6) NOT NULL DEFAULT '1' AFTER `form_id`;
        //if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        //{
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    field_id INT(6) UNSIGNED AUTO_INCREMENT,
                    form_id INT(6),
                    page_no INT(6) UNSIGNED NOT NULL DEFAULT 1,
                    field_label TEXT,
                    field_type TEXT,
                    field_value MEDIUMTEXT,
                    field_order INT(6),
                    field_show_on_user_page TINYINT(1),
                    is_field_primary TINYINT(1) NOT NULL DEFAULT 0,
                    field_is_editable TINYINT(1) NOT NULL DEFAULT '0',
                    is_deletion_allowed TINYINT(1) NOT NULL DEFAULT '1',
                    field_options MEDIUMTEXT,
                    PRIMARY KEY  (field_id)
                    )$charset_collate;";

            dbDelta($sql);
        //}

        $table_name = self::get_table_name_for('SUBMISSIONS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `submission_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `form_id` INT(6),
                    `data` TEXT,
                    `user_email` VARCHAR(250),
                    `child_id` INT(6) NOT NULL DEFAULT 0,
                    `last_child` INT(6) NOT NULL DEFAULT 0,
                    `is_read` TINYINT(1) NOT NULL DEFAULT '0',
                    `submitted_on` DATETIME,
                    `unique_token` VARCHAR(250)
                    )$charset_collate;";

            dbDelta($sql);
        }

        $table_name = self::get_table_name_for('SUBMISSION_FIELDS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `sub_field_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `submission_id` INT(6),
                    `field_id` INT(6),
                    `form_id` INT(6),
                    `value` TEXT
                    )$charset_collate;";

            dbDelta($sql);
        }

        /* $table_name = self::get_table_name_for('FORM_RESPONSES');

          if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
          {
          //Ensures proper charset support. Also limits support for WP v3.5+.
          $charset_collate = $wpdb->get_charset_collate();

          $sql = "CREATE TABLE $table_name (
          `response_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `form_id` INT(6),
          `response_code` varchar(50),
          `response` MEDIUMTEXT
          )$charset_collate;";

          dbDelta($sql);
          } */

        $table_name = self::get_table_name_for('PAYPAL_FIELDS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `field_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `type` VARCHAR(50),
                    `name` VARCHAR(256),
                    `value` LONGTEXT,
                    `class` VARCHAR(256),
                    `option_label` LONGTEXT,
                    `option_price` LONGTEXT,
                    `option_value` LONGTEXT,
                    `description` LONGTEXT,
                    `require` LONGTEXT,
                    `order` INT(11),
                    `extra_options` LONGTEXT
                    )$charset_collate;";

            dbDelta($sql);
        }

        $table_name = self::get_table_name_for('PAYPAL_LOGS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `submission_id` INT(6),
                    `form_id` INT(6),
                    `invoice` VARCHAR(50),
                    `txn_id` VARCHAR(600),
                    `status` VARCHAR(200),
                    `total_amount` DOUBLE,
                    `currency` VARCHAR(5),
                    `log` LONGTEXT,
                    `posted_date` VARCHAR(50),
                    `pay_proc` VARCHAR(50),
                    `bill` LONGTEXT,    
                    `ex_data` LONGTEXT
                    )$charset_collate;";

            dbDelta($sql);
        }

        $table_name = self::get_table_name_for('FRONT_USERS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();
            
            /*
            $sql = "CREATE TABLE $table_name (
                    `id` int(11) AUTO_INCREMENT,
                    `email` varchar(255),
                    `otp_code` varchar(255) NOT NULL,
                    `last_activity_time` DATETIME,
                    `created_date` DATETIME, 
                    PRIMARY KEY (`Id`)
                    )$charset_collate;";
            */
            
            $sql = "CREATE TABLE $table_name (
                id int(11) UNSIGNED AUTO_INCREMENT,
                email varchar(255),
                otp_code varchar(255) NOT NULL,
                last_activity_time DATETIME,
                created_date DATETIME, 
                expiry DATETIME,
                attempts INT(3),
                type VARCHAR(2),
                PRIMARY KEY (id)
                )$charset_collate;";

            dbDelta($sql);
        }

        $table_name = self::get_table_name_for('STATS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `stat_id` int(11) AUTO_INCREMENT PRIMARY KEY,
                    `form_id` INT(6),
                    `user_ip` varchar(50),
                    `ua_string` varchar(255),
                    `browser_name` varchar(50),
                    `visited_on` varchar(50),
                    `submitted_on` varchar(50),
                    `time_taken` INT(11),
                    `submission_id` INT(6)
                    )$charset_collate;";

            dbDelta($sql);
        }


        $table_name = self::get_table_name_for('NOTES');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                      `note_id` int(11) NOT NULL AUTO_INCREMENT,
                      `submission_id` int(11) NOT NULL,
                      `notes` longtext DEFAULT NULL,
                      `status` varchar(255) DEFAULT NULL,
                      `publication_date` datetime NOT NULL,
                      `published_by` bigint(20) DEFAULT NULL,
                      `last_edit_date` datetime DEFAULT NULL,
                      `last_edited_by` bigint(20) DEFAULT NULL,
                      `note_options` longtext DEFAULT NULL,  
                       PRIMARY KEY (`note_id`))$charset_collate;";
            //`type` longtext NOT NULL,
            dbDelta($sql);
        }
        
        $table_name = self::get_table_name_for('SENT_EMAILS');

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                      `mail_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                      `type` int(11) NOT NULL,
                      `to` varchar(255) DEFAULT NULL,
                      `sub` longtext DEFAULT NULL,
                      `body` longtext DEFAULT NULL,
                      `sent_on` datetime,                      
                      `headers` varchar(255) DEFAULT NULL,    
                      `form_id` int(11) DEFAULT NULL,
                      `exdata` varchar(255) DEFAULT NULL,
                      `is_read_by_user` TINYINT(1) NOT NULL DEFAULT 0,
                      `was_sent_success` TINYINT(1) NOT NULL DEFAULT 1)$charset_collate;";
            //`type` longtext NOT NULL,
            dbDelta($sql);
        }
        
        self::create_login_tables();
        self::create_session_table();
        do_action("rm_per_site_tables_created");
    }
    
    
    public static function create_login_tables(){
        require_once( ABSPATH . 'wp-includes/wp-db.php');
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        global $wpdb;
        
        $table_name = self::get_table_name_for('LOGIN');

            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    id INT(6) UNSIGNED AUTO_INCREMENT,
                    m_key VARCHAR(255),
                    value TEXT,
                    PRIMARY KEY  (id)
                    )$charset_collate;";

            dbDelta($sql);
        
        $table_name = self::get_table_name_for('LOGIN_LOG');
            //Ensures proper charset support. Also limits support for WP v3.5+.
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                `id` INT(6) UNSIGNED AUTO_INCREMENT,
                `email` VARCHAR(255),
                `username_used` VARCHAR(255),
                `time` datetime,
                `status` int(1),
                `ip` VARCHAR(255),
                `browser` varchar(255),
                `type` VARCHAR(10),
                `ban` int(1),
                `result` varchar(255),
                `failure_reason` varchar(255),
                `ban_til` datetime,
                `login_url` varchar(255),
                `social_type` VARCHAR(50),
                PRIMARY KEY  (id)
                )$charset_collate;";

        dbDelta($sql);
    }
    
    public static function create_session_table()
    {
        if(!function_exists('dbDelta'))
        {
            require_once( ABSPATH . 'wp-includes/wp-db.php');
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();        
        $table_name = self::get_table_name_for('SESSIONS');
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            $sql= "CREATE TABLE IF NOT EXISTS $table_name (
                    `id` varchar(128) NOT NULL,
                    `data` mediumtext NOT NULL,
                    `timestamp` int(255) NOT NULL,
                    PRIMARY KEY (`id`)) $charset_collate;";
            
             dbDelta($sql);
        }
    }

    /**
     * Gets unique ids name for a table
     *
     * @param string $model_identifier
     * @return boolean|string
     */
    public static function get_unique_id_name($model_identifier)
    {

        switch ($model_identifier)
        {
            case 'FORMS':
                $unique_id_name = 'form_id';
                break;

            case 'FIELDS':
                $unique_id_name = 'field_id';
                break;

            case 'SUBMISSIONS':
                $unique_id_name = 'submission_id';
                break;

            case 'SUBMISSION_FIELDS':
                $unique_id_name = 'sub_field_id';
                break;

//            case 'FORM_RESPONSES':
//                $unique_id_name = 'response_id';
//                break;
//            
            case 'PAYPAL_FIELDS':
                $unique_id_name = 'field_id';
                break;

            case 'PAYPAL_LOGS':
                $unique_id_name = 'id';
                break;

            case 'FRONT_USERS' :
                $unique_id_name = 'id';
                break;

            case 'STATS' :
                $unique_id_name = 'stat_id';
                break;

            case 'NOTES' :
                $unique_id_name = 'note_id';
                break;
            
            case 'WP_USERS' :
                $unique_id_name = 'ID';
                break;
            
            case 'WP_USERS_META' :
                $unique_id_name = 'umeta_id';
                break;
            
            case 'SENT_EMAILS' :
                $unique_id_name = 'mail_id';
                break;
                
            case 'RES' :
                $unique_id_name = 'res_id';
                break;
            
            case 'LOGIN_LOG' :
                $unique_id_name = 'id';
                break;
            
            case 'LOGIN' :
                $unique_id_name = 'id';
                break;

            default:
                return false;
        }

        return $unique_id_name;
    }

    /**
     * returns the table name according to its identifier
     *
     * @param string $model_identifier
     * @return string
     */
    public static function get_table_name_for($model_identifier)
    {
        global $wpdb;
        
        //These are global tables, hence wpdb->prefix should not be used.
        //wpdb->prefix is site specifc in mulitsite environment.
        if($model_identifier == 'WP_USERS') return $wpdb->users;
        if($model_identifier == 'WP_USERS_META') return $wpdb->usermeta;
        
        if (isset(self::$table_name_for[$model_identifier]))
            return $wpdb->prefix . self::$table_name_for[$model_identifier];
        
        if (defined('REGMAGIC_ADDON'))
            return RM_Table_Tech_Addon::get_table_name_for($model_identifier);
    }

    public static function delete_and_reset_table($identifier)
    {
        global $wpdb;

        $table_name = self::get_table_name_for($identifier);

        $qry = "TRUNCATE `$table_name`";
        $wpdb->query($qry);
    }
    
    //Restores intended strucure of tables by adding any missing column
    public static function repair_tables()
    {
        global $wpdb;
        $db_name = DB_NAME;
        
        self::create_tables_per_site();
        $sub_table_name = $wpdb->prefix."rm_submissions";
        $field_table_name = $wpdb->prefix."rm_fields";
        $stat_table_name = $wpdb->prefix."rm_stats";
        
        ob_start();
        $wpdb->query("ALTER TABLE `$field_table_name` ADD `page_no` INT(6) NOT NULL DEFAULT '1' AFTER `form_id`");
        $wpdb->query("ALTER TABLE `$sub_table_name` ADD `child_id` INT(6) NOT NULL DEFAULT '0' AFTER `user_email`;");
        $wpdb->query("ALTER TABLE `$sub_table_name` ADD `is_read` TINYINT(1) NOT NULL DEFAULT '1' AFTER `child_id`");
        $wpdb->query("ALTER TABLE `$sub_table_name` ADD `last_child` INT(6) NOT NULL DEFAULT '0' AFTER `child_id`");
        $wpdb->query("ALTER TABLE `$field_table_name` ADD `field_is_editable` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_field_primary`;");
        $wpdb->query("ALTER TABLE `$stat_table_name` ADD `submission_id` INT(6)");
        ob_get_clean();
        
        //Insert primary email fields in the forms which have none
        $form_table_name = $wpdb->prefix."rm_forms";
        $Query = "SELECT `form_id` FROM $form_table_name WHERE `form_id` NOT IN (SELECT `form_id` FROM $field_table_name WHERE `field_type` = 'Email' AND `is_field_primary` = 1)";
        $broken_forms = $wpdb->get_results($Query, OBJECT_K);
        $service = new RM_Services;
        
        if($broken_forms != null && count($broken_forms) > 0)
        {
            $broken_forms = array_keys($broken_forms);
            foreach($broken_forms as $form_id)
                $service->create_default_email_field($form_id);
        }
        
    }

}

$RM_Table_Tech = RM_Table_Tech::get_instance();