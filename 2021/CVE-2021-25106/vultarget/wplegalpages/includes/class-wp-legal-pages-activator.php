<?php
/**
 * Fired during WPLegalPages activation
 *
 * @link       http://wplegalpages.com/
 * @since      1.5.2
 *
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/includes
 */

/**
 * Fired during WPLegalPages activation.
 *
 * This class defines all code necessary to run during the WPLegalPages's activation.
 *
 * @since      1.5.2
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/includes
 * @author     WPEka <support@wplegalpages.com>
 */
if ( ! class_exists( 'WP_Legal_Pages_Activator' ) ) {
	/**
	 * Fired during WPLegalPages activation.
	 *
	 * This class defines all code necessary to run during the WPLegalPages's activation.
	 *
	 * @since      1.5.2
	 * @package    WP_Legal_Pages
	 * @subpackage WP_Legal_Pages/includes
	 * @author     WPEka <support@wplegalpages.com>
	 */
	class WP_Legal_Pages_Activator {
		/**
		 * Short Description. (use period)
		 *
		 * Long Description.
		 *
		 * @since    1.5.2
		 */
		public static function activate() {

			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			if ( is_multisite() ) {
				// Get all blogs in the network and activate plugin on each one.
				$blog_ids = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs ); // db call ok; no-cache ok.
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::install_db();
					restore_current_blog();
				}
			} else {
				self::install_db();
			}
		}

		/**
		 * Install required tables.
		 */
		public static function install_db() {
			global $wpdb;

			$legal_pages = new WP_Legal_Pages();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$search_query = "SHOW TABLES LIKE '%" . $legal_pages->tablename . "%'";
			if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$sql = 'CREATE TABLE IF NOT EXISTS ' . $legal_pages->tablename . ' (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `title` text NOT NULL,
                              `content` longtext NOT NULL,
                              `notes` text NOT NULL,
                              `contentfor` varchar(200) NOT NULL,
                              PRIMARY KEY (`id`)
                            );';
				dbDelta( $sql );
			}
			$like         = 'is_active';
			$column_count = $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM ' . $legal_pages->tablename . ' LIKE %s', array( $like ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( empty( $column_count ) ) {
				$alter_sql = 'ALTER TABLE ' . $legal_pages->tablename . ' ADD `is_active` BOOLEAN NULL DEFAULT NULL AFTER `notes`;';
				$wpdb->query( $alter_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			}
			$search_query = "SHOW TABLES LIKE '%" . $legal_pages->popuptable . "%'";
			if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$popup_sql = 'CREATE TABLE IF NOT EXISTS ' . $legal_pages->popuptable . ' (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `popup_name` text NOT NULL,
                              `content` longtext NOT NULL,
                              PRIMARY KEY (`id`)
                            );';
				dbDelta( $popup_sql );
			}
			$like         = 'popupName';
			$column_count = $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM ' . $legal_pages->popuptable . ' LIKE %s', array( $like ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( ! empty( $column_count ) ) {
				$alter_popup_sql = 'ALTER TABLE ' . $legal_pages->popuptable . ' CHANGE `popupName` `popup_name` TEXT;';
				$wpdb->query( $alter_popup_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			}
			$privacy      = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/privacy.html' );
			$dmca         = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/dmca.html' );
			$terms_latest = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/Terms-of-use.html' );
			$ccpa         = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/CCPA.html' );
			$terms_fr     = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/Terms-of-use-fr.html' );
			$terms_de     = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/Terms-of-use-de.html' );

			$privacy_policy_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $legal_pages->tablename . ' WHERE contentfor=%s', array( 'kCjTeYOZxB' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( '0' === $privacy_policy_count ) {
				$wpdb->insert(
					$legal_pages->tablename,
					array(
						'title'      => 'Privacy Policy',
						'content'    => $privacy,
						'contentfor' => 'kCjTeYOZxB',
						'is_active'  => '1',
					),
					array( '%s', '%s', '%s', '%d' )
				); // db call ok; no-cache ok.
			} else {
				$wpdb->update(
					$legal_pages->tablename,
					array(
						'is_active'  => '1',
						'content'    => $privacy,
						'contentfor' => 'kCjTeYOZxB',
					),
					array( 'title' => 'Privacy Policy' ),
					array( '%d', '%s', '%s' ),
					array( '%s' )
				); // db call ok; no-cache ok.
			}
			$dmca_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $legal_pages->tablename . ' WHERE contentfor=%s', array( '1r4X6y8tssz0j' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( '0' === $dmca_count ) {
				$wpdb->insert(
					$legal_pages->tablename,
					array(
						'title'      => 'DMCA',
						'content'    => $dmca,
						'contentfor' => '1r4X6y8tssz0j',
						'is_active'  => '1',
					),
					array( '%s', '%s', '%s', '%d' )
				); // db call ok; no-cache ok.
			} else {
				$wpdb->update(
					$legal_pages->tablename,
					array(
						'is_active'  => '1',
						'content'    => $dmca,
						'contentfor' => 'r4X6y8tssz',
					),
					array( 'title' => 'DMCA' ),
					array( '%d', '%s', '%s' ),
					array( '%s' )
				); // db call ok; no-cache ok.
			}
			$terms_of_use_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $legal_pages->tablename . ' WHERE contentfor=%s', array( 'n1bmPjZ6Xj' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( '0' === $terms_of_use_count ) {
				$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$legal_pages->tablename,
					array(
						'title'      => 'Terms of Use',
						'content'    => $terms_latest,
						'contentfor' => 'n1bmPjZ6Xj',
						'is_active'  => '1',
					),
					array( '%s', '%s', '%s', '%d' )
				);
			} else {
				$wpdb->update(
					$legal_pages->tablename,
					array(
						'is_active'  => '1',
						'content'    => $terms_latest,
						'contentfor' => 'n1bmPjZ6Xj',
					),
					array( 'title' => 'Terms of Use' ),
					array( '%d', '%s', '%s' ),
					array( '%s' )
				); // db call ok; no-cache ok.
			}
			$terms_of_use_fr_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $legal_pages->tablename . ' WHERE contentfor=%s', array( 'MMFqUJfC3m' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( '0' === $terms_of_use_fr_count ) {
				$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$legal_pages->tablename,
					array(
						'title'      => 'Terms of Use - FR',
						'content'    => $terms_fr,
						'contentfor' => 'MMFqUJfC3m',
						'is_active'  => '1',
					),
					array( '%s', '%s', '%s', '%d' )
				);
			} else {
				$wpdb->update(
					$legal_pages->tablename,
					array(
						'is_active'  => '1',
						'content'    => $terms_fr,
						'contentfor' => 'MMFqUJfC3m',
					),
					array( 'title' => 'Terms of Use - FR' ),
					array( '%d', '%s', '%s' ),
					array( '%s' )
				); // db call ok; no-cache ok.
			}
			$terms_of_use_de_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $legal_pages->tablename . ' WHERE contentfor=%s', array( 'fbBlC5Y4yZ' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( '0' === $terms_of_use_de_count ) {
				$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$legal_pages->tablename,
					array(
						'title'      => 'Terms of Use - DE',
						'content'    => $terms_de,
						'contentfor' => 'fbBlC5Y4yZ',
						'is_active'  => '1',
					),
					array( '%s', '%s', '%s', '%d' )
				);
			} else {
				$wpdb->update(
					$legal_pages->tablename,
					array(
						'is_active'  => '1',
						'content'    => $terms_de,
						'contentfor' => 'fbBlC5Y4yZ',
					),
					array( 'title' => 'Terms of Use - DE' ),
					array( '%d', '%s' ),
					array( '%s' )
				); // db call ok; no-cache ok.
			}
			$ccpa_count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $legal_pages->tablename . ' WHERE contentfor=%s', array( 'JRevVk8nkP' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( '0' === $ccpa_count ) {
				$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$legal_pages->tablename,
					array(
						'title'      => 'CCPA - California Consumer Privacy Act',
						'content'    => $ccpa,
						'contentfor' => 'JRevVk8nkP',
						'is_active'  => '1',
					),
					array( '%s', '%s', '%s', '%d' )
				);
			} else {
				$wpdb->update(
					$legal_pages->tablename,
					array(
						'is_active'  => '1',
						'content'    => $ccpa,
						'contentfor' => 'JRevVk8nkP',
					),
					array( 'title' => 'CCPA - California Consumer Privacy Act' ),
					array( '%d', '%s', '%s' ),
					array( '%s' )
				); // db call ok; no-cache ok.
			}

			delete_option( '_lp_db_updated' );
			delete_option( '_lp_terms_updated' );
			delete_option( '_lp_terms_fr_de_updated' );
			add_option( '_lp_templates_updated', true );
			add_option( '_lp_effective_date_templates_updated', true );
			add_option( 'lp_excludePage', 'true' );
			add_option( 'lp_general', '' );
			add_option( 'lp_accept_terms', '0' );
			add_option( 'lp_eu_cookie_title', 'A note to our visitors' );
			$message_body = 'This website has updated its privacy policy in compliance with changes to European Union data protection law, for all members globally. We’ve also updated our Privacy Policy to give you more information about your rights and responsibilities with respect to your privacy and personal information. Please read this to review the updates about which cookies we use and what information we collect on our site. By continuing to use this site, you are agreeing to our updated privacy policy.';
			add_option( 'lp_eu_cookie_message', htmlentities( $message_body ) );
			add_option( 'lp_eu_cookie_enable', 'OFF' );
			add_option( 'lp_eu_box_color', '#000000' );
			add_option( 'lp_eu_button_text', 'I agree' );
			add_option( 'lp_eu_theme_css', '1' );
			add_option( 'lp_eu_cookie_message', htmlentities( $message_body ) );
			add_option( 'lp_eu_cookie_enable', 'OFF' );
			add_option( 'lp_eu_box_color', '#000000' );
			add_option( 'lp_eu_button_color', '#e3e3e3' );
			add_option( 'lp_eu_button_text_color', '#333333' );
			add_option( 'lp_eu_text_color', '#FFFFFF' );
			add_option( 'lp_eu_link_color', '#8f0410' );
			add_option( 'lp_eu_text_size', '12' );

		}
	}


}
