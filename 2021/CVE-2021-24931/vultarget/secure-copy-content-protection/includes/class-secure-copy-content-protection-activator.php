<?php
/**
 * Fired during plugin activation
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Secure_Copy_Content_Protection
 * @subpackage Secure_Copy_Content_Protection/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Secure_Copy_Content_Protection
 * @subpackage Secure_Copy_Content_Protection/includes
 * @author     Security Team <info@ays-pro.com>
 */
class Secure_Copy_Content_Protection_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		global $wpdb;
		$installed_ver = get_option("sccp_db_version");

		$charset_collate = $wpdb->get_charset_collate();
		if ($installed_ver != SCCP_DB_VERSION) {
			$sql = "CREATE TABLE `" . SCCP_TABLE . "` (
                      `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `protection_text` TEXT NOT NULL,
                      `except_post_types` TEXT NOT NULL,
                      `protection_status` TINYINT NOT NULL,
                      `blocked_ips` TEXT NOT NULL,
                      `styles` TEXT NOT NULL,
                      `options` TEXT NOT NULL,
                      `audio` TEXT NOT NULL,
                      PRIMARY KEY (`id`)
                    )" . SCCP_CHARSET . ";";

        $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                       WHERE table_schema = '".DB_NAME."' AND table_name = '".SCCP_TABLE."' ";
        $results = $wpdb->get_results($sql_schema);

        if(empty($results)){
            $wpdb->query( $sql );
        }else{
            dbDelta( $sql );
        }
        
			$sql = "CREATE TABLE `" . SCCP_BLOCK_CONTENT . "` (
                      `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `password` TEXT NOT NULL,
                      `options` TEXT NOT NULL,
                      PRIMARY KEY (`id`)
                    )".SCCP_CHARSET.";";

        $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                       WHERE table_schema = '".DB_NAME."' AND table_name = '".SCCP_BLOCK_CONTENT."' ";
        $results = $wpdb->get_results($sql_schema);

        if(empty($results)){
            $wpdb->query( $sql );
        }else{
            dbDelta( $sql );
        }

			$sql = "CREATE TABLE `" . SCCP_BLOCK_SUBSCRIBE . "` (
                      `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `options` TEXT NOT NULL,
                      PRIMARY KEY (`id`)
                    )".SCCP_CHARSET.";";

        $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                       WHERE table_schema = '".DB_NAME."' AND table_name = '".SCCP_BLOCK_SUBSCRIBE."' ";
        $results = $wpdb->get_results($sql_schema);

        if(empty($results)){
            $wpdb->query( $sql );
        }else{
            dbDelta( $sql );
        }

			$sql = "CREATE TABLE `" . SCCP_SETTINGS . "` (
                      `id` INT(11) NOT NULL AUTO_INCREMENT,
                      `meta_key` TEXT NULL DEFAULT NULL,
                      `meta_value` TEXT NULL DEFAULT NULL,
	                  `note` TEXT NULL DEFAULT NULL,
	                  `options` TEXT NULL DEFAULT NULL,
                      PRIMARY KEY (`id`)
                    )".SCCP_CHARSET.";";

        $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                       WHERE table_schema = '".DB_NAME."' AND table_name = '".SCCP_SETTINGS."' ";
        $results = $wpdb->get_results($sql_schema);

        if(empty($results)){
            $wpdb->query( $sql );
        }else{
            dbDelta( $sql );
        }


			$sql = "CREATE TABLE `" . SCCP_REPORTS . "` (
                      `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `subscribe_id` INT(11) UNSIGNED NOT NULL,
                      `subscribe_email` TEXT NULL DEFAULT NULL,
                      `user_name` TEXT NULL DEFAULT NULL,
                      `user_ip` VARCHAR(128) NOT NULL,
                      `user_id` INT(11) DEFAULT 0,
                      `vote_date` DATETIME NOT NULL,
                      `unread` INT(1) DEFAULT 1,
                      `other_info` TEXT DEFAULT '',
                      `user_address` TEXT DEFAULT '',
                      PRIMARY KEY (`id`)
                    )".SCCP_CHARSET.";";

        $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                       WHERE table_schema = '".DB_NAME."' AND table_name = '".SCCP_REPORTS."' ";
        $results = $wpdb->get_results($sql_schema);

        if(empty($results)){
            $wpdb->query( $sql );
        }else{
            dbDelta( $sql );
        }

			self::insert_default_data();
			update_option('sccp_db_version', SCCP_DB_VERSION);
		}

		$metas = array(
            "mailchimp",
            "options"
        );
        
        foreach($metas as $meta_key){
            $meta_val = "";            
            $sql = "SELECT COUNT(*) FROM `".SCCP_SETTINGS."` WHERE `meta_key` = '".$meta_key."'";
            $result = $wpdb->get_var($sql);
            if(intval($result) == 0){
                $result = $wpdb->insert(
                    SCCP_SETTINGS,
                    array(
                        'meta_key'    => $meta_key,
                        'meta_value'  => $meta_val,
                        'note'        => "",
                        'options'     => ""
                    ),
                    array( '%s', '%s', '%s', '%s' )
                );
            }
        }
	}

	public static function insert_default_data() {
		global $wpdb;
		$sccp_table = esc_sql(SCCP_TABLE);
		$subscribe_table = esc_sql(SCCP_BLOCK_SUBSCRIBE);
		$count = $wpdb->get_var("SELECT COUNT(*) FROM " . $sccp_table);
		if ($count == 0) {
			$wpdb->insert(
				$sccp_table,
				array(
					'protection_text'   => __('You cannot copy content of this page', 'secure-copy-content-protection'),
					'except_post_types' => '',
					'protection_status' => 1,
					'blocked_ips'       => '',
					'styles'            => '{"bg_color":"#ffffff","text_color":"#ff0000","border_color":"#b7b7b7","border_width":"1","border_radius":"3","border_style":"solid","tooltip_position":"mouse"}',
					'options'           => '{"developer_tools":"checked","context_menu":"checked","drag_start":"checked","mobile_img":"","ctrlc":"checked","ctrlv":"checked","ctrls":"checked","ctrla":"checked","ctrlx":"checked","ctrlu":"checked","ctrlf":"","ctrlp":"","f12":"checked","enable_text_selecting":""}',
					'audio'             => ''
				),
				array('%s', '%s', '%d', '%s', '%s', '%s')
			);

			$wpdb->insert(
				$subscribe_table, 
				array(
					'options' => '{"require_verification":"off"}',				
				),
				array('%s')
			);
		}
	}

  public static function ays_sccp_update_db_check(){
    if (get_site_option('sccp_db_version') != SCCP_DB_VERSION) {
        self::activate();
    }
  }

}
