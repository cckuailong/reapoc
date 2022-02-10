<?php
/**
 * Fired during WPLegalPages deactivation
 *
 * @link       http://wplegalpages.com/
 * @since      1.5.2
 *
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/includes
 */

/**
 * Fired during WPLegalPages deactivation.
 *
 * This class defines all code necessary to run during the WPLegalPages's deactivation.
 *
 * @since      1.5.2
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/includes
 * @author     WPEka <support@wplegalpages.com>
 */
if ( ! class_exists( 'WP_Legal_Pages_Delete' ) ) {
	/**
	 * Fired during WPLegalPages deactivation.
	 *
	 * This class defines all code necessary to run during the WPLegalPages's deactivation.
	 *
	 * @since      1.5.2
	 * @package    WP_Legal_Pages
	 * @subpackage WP_Legal_Pages/includes
	 * @author     WPEka <support@wplegalpages.com>
	 */
	class WP_Legal_Pages_Delete {
		/**
		 * Short Description. (use period)
		 *
		 * Long Description.
		 *
		 * @since    1.5.2
		 */
		public static function delete() {
			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			if ( is_multisite() ) {
				// Get all blogs in the network and activate plugin on each one.
				$blog_ids = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs ); // db call ok; no-cache ok.
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::delete_db();
					restore_current_blog();
				}
			} else {
				self::delete_db();
			}
		}

		/**
		 * Delete database tables on plugin uninstall hook.
		 */
		public static function delete_db() {
			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$legal_pages = new WP_Legal_Pages();
			$drop_sql    = 'DROP TABLE IF EXISTS ' . $legal_pages->tablename;
			$wpdb->query( $drop_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$drop_popup_sql = 'DROP TABLE IF EXISTS ' . $legal_pages->popuptable;
			$wpdb->query( $drop_popup_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			delete_option( '_lp_db_updated' );
			delete_option( 'lp_accept_terms' );
			delete_option( 'lp_excludePage' );
			delete_option( 'lp_general' );
			delete_option( 'lp_accept_terms' );
			delete_option( 'lp_eu_cookie_title' );
			delete_option( 'lp_eu_cookie_message' );
			delete_option( 'lp_eu_cookie_enable' );
			delete_option( 'lp_eu_box_color' );
			delete_option( 'lp_eu_button_color' );
			delete_option( 'lp_eu_button_text_color' );
			delete_option( 'lp_eu_text_color' );
			delete_option( 'lp_eu_text_size' );
			delete_option( 'lp_eu_link_color' );
			delete_option( '_lp_templates_updated' );
		}

	}
}
