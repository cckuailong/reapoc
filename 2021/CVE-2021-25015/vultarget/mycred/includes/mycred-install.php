<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Install class
 * Used when the plugin is activated/de-activated or deleted. Installs core settings and
 * base templates, checks compatibility and uninstalls.
 * @since 0.1
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Install' ) ) :
	final class myCRED_Install{

		// Instnace
		protected static $_instance = NULL;

		/**
		 * Setup Instance
		 * @since 1.7
		 * @version 1.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Not allowed
		 * @since 1.7
		 * @version 1.0
		 */
		public function __clone() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.7' ); }

		/**
		 * Not allowed
		 * @since 1.7
		 * @version 1.0
		 */
		public function __wakeup() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.7' ); }

		/**
		 * Construct
		 */
		function __construct() { }

		/**
		 * Compat
		 * Check to make sure we reach minimum requirements for this plugin to work propery.
		 * @since 0.1
		 * @version 1.3
		 */
		public static function compat() {

			global $wpdb;

			$message = array();

			// WordPress check
			$wp_version = $GLOBALS['wp_version'];
			if ( version_compare( $wp_version, '4.0', '<' ) && MYCRED_FOR_OLDER_WP === false )
				$message[] = __( 'myCRED requires WordPress 4.0 or higher. Version detected:', 'mycred' ) . ' ' . $wp_version;

			// PHP check
			$php_version = phpversion();
			if ( version_compare( $php_version, '5.3', '<' ) )
				$message[] = __( 'myCRED requires PHP 5.3 or higher. Version detected: ', 'mycred' ) . ' ' . $php_version;

			// SQL check
			$sql_version = $wpdb->db_version();
			if ( version_compare( $sql_version, '5.0', '<' ) )
				$message[] = __( 'myCRED requires SQL 5.0 or higher. Version detected: ', 'mycred' ) . ' ' . $sql_version;

			// Not empty $message means there are issues
			if ( ! empty( $message ) ) {

				die( __( 'Sorry but your WordPress installation does not reach the minimum requirements for running myCRED. The following errors were given:', 'mycred' ) . "\n" . implode( "\n", $message ) );

			}

		}

		/**
		 * First time activation
		 * @since 0.1
		 * @version 1.4
		 */
		public static function activate() {

			require_once myCRED_INCLUDES_DIR . 'mycred-setup.php';

			$setup = new myCRED_Setup();
			$setup->load();

			do_action( 'mycred_activation' );

			if ( isset( $_GET['activate-multi'] ) )
				return;

			flush_rewrite_rules();
		}

		/**
		 * Re-activation
		 * @since 0.1
		 * @version 1.4
		 */
		public static function reactivate() {

			$version = get_option( 'mycred_version', false );
			do_action( 'mycred_reactivation', $version );

			self::update_to_latest( $version );

			// Update version number
			update_option( 'mycred_version', myCRED_VERSION );

		}

		/**
		 * Update to Latest
		 * @since 1.7.6
		 * @version 1.0.1
		 */
		public static function update_to_latest( $version ) {

			global $wpdb;

			// Reset cached pending payments (buyCRED add-on)
			$wpdb->delete(
				$wpdb->usermeta,
				array( 'meta_key' => 'buycred_pending_payments' ),
				array( '%s' )
			);

			if ( version_compare( $version, myCRED_VERSION, '<' ) ) {

				/**
				 * Add support for showing all point types in the WooCommerce
				 * currency dropdown. If this is a currency store, we need to switch the currency code.
				 */
				$woo_currency = apply_filters( 'woocommerce_currency', get_option( 'woocommerce_currency', false ) );
				if ( $woo_currency === 'MYC' ) {

					$settings = get_option( 'woocommerce_mycred_settings', false );
					if ( $settings !== false && is_array( $settings ) && array_key_exists( 'point_type', $settings ) ) {

						update_option( 'woocommerce_currency', $settings['point_type'] );

					}

				}

			}

		}

        /**
         * Checks if set to remove on installation
         * @param $key
         * @return bool
         * @since 2.1.1
         * @version 1.0
         */
        public static function remove_setting( $key ) {

            $hooks = mycred_get_option( 'mycred_pref_core', false );

            if ( is_array( $hooks ) && in_array( $key, $hooks ) )
                if ( $hooks['uninstall'][$key] == 1 )
                    return true;

            return false;
        }

		/**
		 * Uninstall
		 * Removes all myCRED related data from the database.
		 * @since 0.1
		 * @version 1.5.1
		 */
		public static function uninstall() {

            global $wpdb;

			$mycred_types = mycred_get_types();

			// Options to delete
			$options_to_delete  = array(
				'mycred_pref_bank',
				'mycred_pref_remote',
				'woocommerce_mycred_settings',
				'mycred_sell_content_one_seven_updated',
				'mycred_network',
				'widget_mycred_widget_balance',
				'widget_mycred_widget_list',
				'widget_mycred_widget_transfer',
				'mycred_ref_hook_counter',
				'mycred_espresso_gateway_prefs',
				'mycred_eventsmanager_gateway_prefs',
				MYCRED_SLUG . '-cache-stats-keys',
				MYCRED_SLUG . '-cache-leaderboard-keys',
				MYCRED_SLUG . '-last-clear-stats',
				'mycred_deactivated_on'
			);

            $can_remove_hooks = self::remove_setting( 'hooks' );

            //Delete Hooks
            if ( $can_remove_hooks ) {

                $options_to_delete[] = 'mycred_pref_hooks';

                foreach ( $mycred_types as $type => $label ) {
					$options_to_delete[] = 'mycred_pref_hooks_' . $type;
				}
            
            }

            $can_remove_addons = self::remove_setting( 'addon' );

            //Delete All Addons Settings
            if ( $can_remove_addons )
                $options_to_delete[] = 'mycred_pref_addons';

            $can_remove_types = self::remove_setting( 'types' );

			if ( $can_remove_types ) {
                
                $options_to_delete[] = 'mycred_setup_completed';
				$options_to_delete[] = 'mycred_version';
				$options_to_delete[] = 'mycred_version_db';
				$options_to_delete[] = 'mycred_key';
                $options_to_delete[] = 'mycred_types';
                $options_to_delete[] = 'mycred_pref_core';

                foreach ( $mycred_types as $type => $label ) {
					$options_to_delete[] = 'mycred_pref_core_' . $type;
					$options_to_delete[] = 'mycred-cache-total-' . $type;
				}

			}

			// Unschedule cron jobs
			$mycred_crons_to_clear = apply_filters( 'mycred_uninstall_schedules', array(
				'mycred_reset_key',
				'mycred_banking_recurring_payout',
				'mycred_banking_do_batch',
				'mycred_banking_interest_compound',
				'mycred_banking_do_compound_batch',
				'mycred_banking_interest_payout',
				'mycred_banking_interest_do_batch',
				'mycred_send_email_notices'
			) );

			if ( ! empty( $mycred_crons_to_clear ) ) {
				foreach ( $mycred_crons_to_clear as $schedule_id )
					wp_clear_scheduled_hook( $schedule_id );
			}

			// Delete all custom post types created by myCRED
			$post_types       = array( 'buycred_payment' );

			// Coupons
			$post_types[] = ( defined( 'MYCRED_COUPON_KEY' ) ) ? MYCRED_COUPON_KEY : 'mycred_coupon';

			if ( ! defined( 'MYCRED_RANK_KEY' ) ) define( 'MYCRED_RANK_KEY', 'mycred_rank' );
			if ( ! defined( 'MYCRED_BADGE_KEY' ) ) define( 'MYCRED_BADGE_KEY', 'mycred_badge' );
			if ( ! defined( 'MYCRED_BADGE_CATEGORY' ) ) define( 'MYCRED_BADGE_CATEGORY', 'mycred_badge' );

			// Badges
			$can_remove_badges  = self::remove_setting( 'badges' );

			if ( $can_remove_badges ) {
				
				$post_types[] = MYCRED_BADGE_KEY;

				$terms = get_terms( 
					array(
                    	'hide_empty' => false
                	) 	
                );

                foreach ( $terms as $term ) {

                    if ( $term->taxonomy == MYCRED_BADGE_CATEGORY )
                        wp_delete_term( $term->term_id, MYCRED_BADGE_CATEGORY );
 
                }
				
			}

			// Ranks
			$can_remove_ranks = self::remove_setting( 'ranks' );

			if ( $can_remove_ranks ) {
				
				$post_types[] = MYCRED_RANK_KEY;

			}

			$mycred_post_types_to_delete = apply_filters( 'mycred_uninstall_post_types', $post_types );

			if ( ! empty( $mycred_post_types_to_delete ) ) {
				foreach ( $mycred_post_types_to_delete as $post_type ) {

					$posts = new WP_Query( array( 'posts_per_page' => -1, 'post_type' => $post_type, 'fields' => 'ids' ) );
					if ( $posts->have_posts() ) {

						// wp_delete_post() will also handle all post meta deletions
						foreach ( $posts->posts as $post_id )
							wp_delete_post( $post_id, true );

					}
					wp_reset_postdata();

				}
			}

            $can_remove_users_data =  self::remove_setting( 'users' );
			
			//Delete Users' Data
            if ( $can_remove_users_data ) {
                // Delete user meta
                // 'meta_key' => true (exact key) / false (use LIKE)
                $mycred_usermeta_to_delete = array(
                    MYCRED_RANK_KEY                => true,
                    'mycred-last-send'             => true,
                    'mycred-last-linkclick'        => true,
                    'mycred-last-transfer'         => true,
                    'mycred_affiliate_link'        => true,
                    'mycred_email_unsubscriptions' => true,
                    'mycred_transactions'          => true,
                    MYCRED_BADGE_KEY . '%'         => false,
                    MYCRED_RANK_KEY . '%'          => false,
                    'mycred_epp_%'                 => false,
                    'mycred_payments_%'            => false,
                    'mycred_comment_limit_post_%'  => false,
                    'mycred_comment_limit_day_%'   => false,
                    'mycred-last-clear-stats'      => true
                );

                if ( MYCRED_UNINSTALL_CREDS ) {

                    foreach ( $mycred_types as $type => $label ) {

                        $mycred_usermeta_to_delete[ $type ]                                = true;
                        $mycred_usermeta_to_delete[ $type . '_total' ]                     = true;
                        $mycred_usermeta_to_delete[ 'mycred_ref_counts-' . $type ]         = true;
                        $mycred_usermeta_to_delete[ 'mycred_ref_sums-' . $type ]           = true;
                        $mycred_usermeta_to_delete[ $type . '_comp' ]                      = true;
                        $mycred_usermeta_to_delete[ 'mycred_banking_rate_' . $type ]       = true;
                        $mycred_usermeta_to_delete[ 'mycred_buycred_rates_' . $type ]      = true;
                        $mycred_usermeta_to_delete[ 'mycred_sell_content_share_' . $type ] = true;
                        $mycred_usermeta_to_delete[ 'mycred_transactions_' . $type ]       = true;

                    }

                }
                $mycred_usermeta_to_delete = apply_filters( 'mycred_uninstall_usermeta', $mycred_usermeta_to_delete );

                if ( ! empty( $mycred_usermeta_to_delete ) ) {
                    foreach ( $mycred_usermeta_to_delete as $meta_key => $exact ) {

                        if ( $exact )
                            delete_metadata( 'user', 0, $meta_key, '', true );
                        else
                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s;", $meta_key ) );

                    }
                }
            }

            $table_name = '';

            if( defined( 'MYCRED_LOG_TABLE' ) ) {
                     
                $table_name = MYCRED_LOG_TABLE;
            
            }
            else {

                if ( mycred_centralize_log() )
                    $table_name = $wpdb->base_prefix . 'myCRED_log';
                else
                    $table_name = $wpdb->prefix . 'myCRED_log';

            }

            $can_remove_logs = self::remove_setting( 'logs' );

            if ( ! $can_remove_types && $can_remove_logs ) {
            	
            	if( ! is_multisite() || ( is_multisite() && mycred_centralize_log() ) ) {

                    $wpdb->query( "TRUNCATE TABLE {$table_name};" );

                }
                else {

                    $site_ids = get_sites( array( 'fields' => 'ids' ) );
                    foreach ( $site_ids as $site_id ) {

                        $site_id = absint( $site_id );
                        if ( $site_id === 0 ) continue;

                        $table = $wpdb->base_prefix . $site_id . '_myCRED_log';
                        if ( $site === 1 )
                            $table_name = $wpdb->base_prefix . 'myCRED_log';

                        $wpdb->query( "TRUNCATE TABLE {$table_name};" );

                    }

                }

            }

			//Delete Logs
			if( $can_remove_types ) {

                //Delete log table
                if( MYCRED_UNINSTALL_LOG ) {
                    
                    if( ! is_multisite() || ( is_multisite() && mycred_centralize_log() ) ) {

                        $wpdb->query( "DROP TABLE IF EXISTS {$table_name};" );

                    }
                    else {

                        $site_ids = get_sites( array( 'fields' => 'ids' ) );
                        foreach ( $site_ids as $site_id ) {

                            $site_id = absint( $site_id );
                            if ( $site_id === 0 ) continue;

                            $table = $wpdb->base_prefix . $site_id . '_myCRED_log';
                            if ( $site === 1 )
                                $table_name = $wpdb->base_prefix . 'myCRED_log';

                            $wpdb->query( "DROP TABLE IF EXISTS {$table_name};" );

                        }

                    }

                }

            }

			$options_to_delete = apply_filters( 'mycred_uninstall_options', $options_to_delete );

            if ( ! empty( $options_to_delete ) ) {

                // Multisite installations where we are not using the "Master Template" feature
                if ( is_multisite() && ! mycred_override_settings() ) {

                    // Remove settings on all sites where myCRED was enabled
                    $site_ids = get_sites( array( 'fields' => 'ids' ) );
                    foreach ( $site_ids as $site_id ) {

                        // Check if myCRED was installed
                        $installed = get_blog_option( $site_id, 'mycred_setup_completed', false );
                        if ( $installed === false ) continue;

                        foreach ( $options_to_delete as $option_id )
                            delete_blog_option( $site_id, $option_id );

                    }

                }

                else {

                    foreach ( $options_to_delete as $option_id )
                        delete_option( $option_id );

                }

            }

			// Clear stats data (if enabled)
			if ( function_exists( 'mycred_delete_stats_data' ) )
				mycred_delete_stats_data();

			// Good bye.
			flush_rewrite_rules();

		}

	}
endif;

/**
 * Get Installer
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_installer' ) ) :
	function mycred_installer() {
		return myCRED_Install::instance();
	}
endif;
