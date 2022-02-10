<?php
// Only load these resources if we are running an export, can be limited to a single export type
function woo_ce_load_export_types( $export_type = false ) {

	if( !empty( $export_type ) ) {
		if( file_exists( WOO_CE_PATH . 'includes/' . $export_type . '.php' ) )
			include_once( WOO_CE_PATH . 'includes/' . $export_type . '.php' );
		if( file_exists( WOO_CE_PATH . 'includes/' . $export_type . '-extend.php' ) )
			include_once( WOO_CE_PATH . 'includes/' . $export_type . '-extend.php' );
		if( file_exists( WOO_CE_PATH . 'includes/admin/' . $export_type . '.php' ) )
			include_once( WOO_CE_PATH . 'includes/admin/' . $export_type . '.php' );
		return;
	}

	include_once( WOO_CE_PATH . 'includes/product.php' );
	include_once( WOO_CE_PATH . 'includes/product-extend.php' );
	include_once( WOO_CE_PATH . 'includes/category.php' );
	include_once( WOO_CE_PATH . 'includes/tag.php' );
	include_once( WOO_CE_PATH . 'includes/brand.php' );
	include_once( WOO_CE_PATH . 'includes/order.php' );
	include_once( WOO_CE_PATH . 'includes/customer.php' );
	include_once( WOO_CE_PATH . 'includes/user.php' );
	include_once( WOO_CE_PATH . 'includes/user-extend.php' );
	include_once( WOO_CE_PATH . 'includes/review.php' );
	include_once( WOO_CE_PATH . 'includes/coupon.php' );
	include_once( WOO_CE_PATH . 'includes/subscription.php' );
	include_once( WOO_CE_PATH . 'includes/product_vendor.php' );
	include_once( WOO_CE_PATH . 'includes/commission.php' );
	include_once( WOO_CE_PATH . 'includes/shipping_class.php' );
	include_once( WOO_CE_PATH . 'includes/ticket.php' );
	include_once( WOO_CE_PATH . 'includes/attribute.php' );
	include_once( WOO_CE_PATH . 'includes/booking.php' );

	// Load the export type resources first
	if( is_admin() ) {
		include_once( WOO_CE_PATH . 'includes/admin/product.php' );
		include_once( WOO_CE_PATH . 'includes/admin/category.php' );
		include_once( WOO_CE_PATH . 'includes/admin/tag.php' );
		include_once( WOO_CE_PATH . 'includes/admin/brand.php' );
		include_once( WOO_CE_PATH . 'includes/admin/order.php' );
		include_once( WOO_CE_PATH . 'includes/admin/customer.php' );
		include_once( WOO_CE_PATH . 'includes/admin/user.php' );
		include_once( WOO_CE_PATH . 'includes/admin/coupon.php' );
		include_once( WOO_CE_PATH . 'includes/admin/subscription.php' );
		include_once( WOO_CE_PATH . 'includes/admin/commission.php' );
		include_once( WOO_CE_PATH . 'includes/admin/shipping_class.php' );
	}

}


// Check if we are using PHP 5.3 and above
if( version_compare( phpversion(), '5.3' ) >= 0 )
	include_once( WOO_CE_PATH . 'includes/legacy.php' );
include_once( WOO_CE_PATH . 'includes/formatting.php' );

include_once( WOO_CE_PATH . 'includes/export-csv.php' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( WOO_CE_PATH . 'includes/admin.php' );

	include_once( WOO_CE_PATH . 'includes/admin/scheduled_export.php' );
	include_once( WOO_CE_PATH . 'includes/export_template.php' );

	include_once( WOO_CE_PATH . 'includes/settings.php' );

	function woo_ce_export_init() {

		// Process any pre-export notice confirmations
		$action = ( function_exists( 'woo_get_action' ) ? woo_get_action() : false );
		switch( $action ) {

			// Reset all dismissed notices within Store Exporter Deluxe
			case 'nuke_notices':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_notices' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_nuke_dismissed_notices();
					$message = __( 'All dimissed notices within Store Exporter Deluxe have been restored.', 'woocommerce-exporter' );
					woo_ce_admin_notice( $message );
				}
				break;

			// Delete all WordPress Options associated with Store Exporter Deluxe
			case 'nuke_options':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_options' ) ) {
					// Delete WordPress Options used by Store Exporter Deluxe (Uninstall)
					if( woo_ce_nuke_options() ) {
						$message = __( 'All Store Exporter Deluxe WordPress Options have been deleted from your WordPress site, you can now de-activate and delete Store Exporter Deluxe.', 'woocommerce-exporter' );
						woo_ce_admin_notice( $message );
					} else {
						$message = __( 'Not all Store Exporter Deluxe WordPress Options could be deleted from your WordPress site, please see the WordPress Options table for Options prefixed by <code>woo_ce_</code>.', 'woocommerce-exporter' );
						woo_ce_admin_notice( $message, 'error' );
					}
				}
				break;

			// Delete all Archives
			case 'nuke_archives':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_nuke_archives' ) ) {
					// Delete saved exports
					if( woo_ce_nuke_archive_files() ) {
						$message = __( 'All existing Archives and their export files have been deleted from your WordPress site.', 'woocommerce-exporter' );
						woo_ce_admin_notice( $message );
					} else {
						$message = __( 'There were no existing Archives to be deleted from your WordPress site.', 'woocommerce-exporter' );
						woo_ce_admin_notice( $message, 'error' );
					}
				}
				break;

			// Prompt on Export screen when insufficient memory (less than 64M is allocated)
			case 'dismiss_memory_prompt':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_memory_prompt' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_memory_prompt', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			// Prompt on Export screen when PHP configuration option max_execution_time cannot be increased
			case 'dismiss_execution_time_prompt':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_execution_time_prompt' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_execution_time_prompt', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			// Prompt on Export screen when PHP 5.2 or lower is installed
			case 'dismiss_php_legacy':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_php_legacy' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_php_legacy', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'dismiss_subscription_prompt':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_subscription_prompt' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_subscription_prompt', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'dismiss_overview_prompt':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_overview_prompt' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_overview_prompt', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'dismiss_quick_export_prompt':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_quick_export_prompt' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_quick_export_prompt', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'dismiss_upgrade_prompt':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_upgrade_prompt' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_upgrade_prompt', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'dismiss_archives_prompt':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_dismiss_archives_prompt' ) ) {
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'dismiss_archives_prompt', 1 );
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'hide_archives_tab':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_hide_archives_tab' ) ) {
					// Remember to hide the Archives tab
					woo_ce_update_option( 'hide_archives_tab', 1 );
					$url = add_query_arg( array( 'tab' => 'export', 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'restore_archives_tab':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_restore_archives_tab' ) ) {
					// Remember to show the Archives tab
					woo_ce_update_option( 'hide_archives_tab', 0 );
					$url = add_query_arg( array( 'tab' => 'archive', 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			// Reset the Transient counters for all export types
			case 'refresh_export_type_counts':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_refresh_export_type_counts' ) ) {
					woo_ce_load_export_types();
					$transients = array(
						'product',
						'category',
						'tag',
						'user'
					);
					foreach( $transients as $transient ) {
						// Delete the existing count Transients
						delete_transient( WOO_CE_PREFIX . sprintf( '_%s_count', $transient ) );
						// Refresh the count Transients
						woo_ce_get_export_type_count( $transient );
					}
					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			case 'refresh_module_counts':
				// We need to verify the nonce.
				if( !empty( $_GET ) && check_admin_referer( 'woo_ce_refresh_module_counts' ) ) {

					// Delete the existing count Transients
					delete_transient( WOO_CE_PREFIX . '_modules_active' );
					delete_transient( WOO_CE_PREFIX . '_modules_all_count' );
					delete_transient( WOO_CE_PREFIX . '_modules_active_count' );
					delete_transient( WOO_CE_PREFIX . '_modules_inactive_count' );

					// Refresh the count Transients
					woo_ce_modules_list();
					woo_ce_refresh_active_export_plugins();

					$url = add_query_arg( array( 'action' => null, '_wpnonce' => null ) );
					wp_redirect( $url );
					exit();
				}
				break;

			// Save skip overview preference
			case 'skip_overview':
				// We need to verify the nonce.
				if( !empty( $_POST ) && check_admin_referer( 'skip_overview', 'woo_ce_skip_overview' ) ) {
					$skip_overview = false;
					if( isset( $_POST['skip_overview'] ) )
						$skip_overview = 1;
					// Remember that we've dismissed this notice
					woo_ce_update_option( 'skip_overview', $skip_overview );

					if( $skip_overview == 1 ) {
						$url = add_query_arg( array( 'tab' => 'export', '_wpnonce' => null ) );
						wp_redirect( $url );
						exit();
					}
				}
				break;

		}

	}

	// Displays a HTML notice when a WordPress or Store Exporter error is encountered
	function woo_ce_admin_fail_notices() {

		$troubleshooting_url = 'https://www.visser.com.au/documentation/store-exporter-deluxe/troubleshooting/';

		// If the failed flag is set then prepare for an error notice
		if( isset( $_GET['failed'] ) ) {
			$message = '';
			if( isset( $_GET['message'] ) )
				$message = urldecode( $_GET['message'] );
			if( $message ) {
				$message = sprintf( __( 'A WordPress or server error caused the export to fail, the exporter was provided with a reason: <em>%s</em>', 'woocommerce-exporter' ), $message );
				$message .= ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			} else {
				$message = __( 'A WordPress or server error caused the exporter to fail, no reason was provided, if this persists please get in touch so we can reproduce and resolve this with you.', 'woocommerce-exporter' );
				$message .= ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			}
			woo_ce_admin_notice_html( $message, 'error' );
		}

		// Displays a notice where the maximum execution time cannot be set
		if( !woo_ce_get_option( 'dismiss_execution_time_prompt', 0 ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
			// Check if max_execution is unlimited
			if( $max_execution_time == false || $max_execution_time > 0 ) {
				$response = ( function_exists( 'ini_set' ) ? @ini_set( 'max_execution_time', 120 ) : false );
				if( $response === false || ( $response != absint( $max_execution_time ) ) ) {
					$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_execution_time_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_execution_time_prompt' ) ) ) );
					if( $max_execution_time == false && $max_execution_time == false ) {
						$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . __( 'We could not detect or override the PHP configuration option <code>max_execution_time</code>, this is not an indicatative of an issue but may limit the size of large exports.', 'woocommerce-exporter' );
						$message .= __( ' If exports fail after a period of loading/inactivity then this limit has likely been reached and may need to be increased by contacting your hosting provider.', 'woocommerce-exporter' );
					} else {
						$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'We could not override the PHP configuration option <code>max_execution_time</code> which is set at %d seconds, this is typical of most hosting providers and is not an indicatation of an issue but may limit the size of large exports.', 'woocommerce-exporter' ), $max_execution_time );
						$message .= sprintf( __( ' If exports fail after %d seconds of loading/inactivity then this limit has likely been reached and may need to be increased by contacting your hosting provider.', 'woocommerce-exporter' ), $max_execution_time );
					}
					$message .= sprintf( __( ' See: <a href="%s" target="_blank">Increasing the PHP max_execution_time configuration option</a>', 'woocommerce-exporter' ), $troubleshooting_url ); 
					woo_ce_admin_notice_html( $message, 'notice' );
				}
			}
		}

		// Displays a notice where the memory allocated to WordPress falls below 64MB
		if( !woo_ce_get_option( 'dismiss_memory_prompt', 0 ) ) {
			$memory_limit = absint( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_memory_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_memory_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'We recommend setting memory to at least %dMB, your site has only %dMB allocated to it. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'woocommerce-exporter' ), $minimum_memory_limit, $memory_limit, $troubleshooting_url );
				woo_ce_admin_notice_html( $message, 'error' );
			}
		}

		// Displays a notice if PHP 5.2 or lower is installed
		if( version_compare( phpversion(), '5.3', '<' ) && !woo_ce_get_option( 'dismiss_php_legacy', 0 ) ) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_php_legacy', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_php_legacy' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . sprintf( __( 'Your PHP version (%s) is not supported and is very much out of date, since 2010 all users are strongly encouraged to upgrade to PHP 5.3+ and above. Contact your hosting provider to make this happen. See: <a href="%s" target="_blank">Migrating from PHP 5.2 to 5.3</a>', 'woocommerce-exporter' ), phpversion(), $troubleshooting_url . '#General_troubleshooting' );
			woo_ce_admin_notice_html( $message, 'error' );
		}

		// Displays notice if there are more than 2500 Subscriptions
		if( !woo_ce_get_option( 'dismiss_subscription_prompt', 0 ) ) {
			if( class_exists( 'WC_Subscriptions' ) ) {
				$wcs_version = ( function_exists( 'woo_ce_get_wc_subscriptions_version' ) ? woo_ce_get_wc_subscriptions_version() : false );
				if( version_compare( $wcs_version, '2.0.1', '<' ) ) {
					if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
						// Does this store have roughly more than 3000 Subscriptions
						if( WC_Subscriptions::is_large_site() ) {
							$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_subscription_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_subscription_prompt' ) ) ) );
							$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>';
							$message .= __( 'We\'ve detected the <em>is_large_site</em> flag has been set within WooCommerce Subscriptions. Please get in touch if exports are incomplete as we need to spin up an alternative export process to export Subscriptions from large stores.', 'woocommerce-exporter' );
							$message .= ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
							woo_ce_admin_notice_html( $message, 'notice' );
						}
					}
				}
			}
		}

		// If the export failed the WordPress Transient will still exist
		if( get_transient( WOO_CE_PREFIX . '_running' ) ) {
			// Check if a fatal PHP error has been detected
			if( get_transient( WOO_CE_PREFIX . '_crashed' ) ) {
				$message = __( 'A WordPress or server error caused the exporter to fail with a blank screen, this is usually isolated to a memory or timeout issue, if this persists please get in touch so we can reproduce and resolve this.', 'woocommerce-exporter' );
				$message .= ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
				woo_ce_admin_notice_html( $message, 'error' );
				delete_transient( WOO_CE_PREFIX . '_crashed' );
				delete_transient( WOO_CE_PREFIX . '_running' );
			} else {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_export_running_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_export_running_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>';
				$message .= __( 'It looks like an export is currently running in the background. Unfortunately we cannot tell if the background export has completed just that it hasn\'t yet finished. If you are confident there are no background exports running click Dismiss to hide this notice.', 'woocommerce-exporter' );
				$message .= ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
				woo_ce_admin_notice_html( $message, 'notice' );
			}
		}

		// Archives
		if( ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '' ) == 'archive' ) {

			// Displays a notice if Archives is disabled and the Archives tab is opened
			if(
				woo_ce_get_option( 'delete_file', '1' ) == 1
				&& ( !woo_ce_get_option( 'dismiss_archives_prompt', 0 ) )
			) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_archives_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_archives_prompt' ) ) ) );
				$override_url = esc_url( add_query_arg( array( 'action' => 'hide_archives_tab', '_wpnonce' => wp_create_nonce( 'woo_ce_hide_archives_tab' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>';
				$message .= __( 'It looks like the saving of export archives is disabled from the Enabled Archives option on the Settings tab, would you like to hide the Archives tab aswell?', 'woocommerce-exporter' ) . '<br /><br /><a href="' . $override_url . '" class="button-primary">' . __( 'Hide Archives tab', 'woocommerce-exporter' ) . '</a>';
				woo_ce_admin_notice_html( $message, 'notice' );
			}

		}

		// Displays a notice if Archives are detected without a Post Status of private
		if( woo_ce_get_unprotected_archives( array( 'count' => true ) ) && !woo_ce_get_option( 'dismiss_archives_privacy_prompt', 0 ) ) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_archives_privacy_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_archives_privacy_prompt' ) ) ) );
			$override_url = esc_url( add_query_arg( array( 'action' => 'override_archives_privacy', '_wpnonce' => wp_create_nonce( 'woo_ce_override_archives_privacy' ) ) ) );
			$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>';
			$message .= __( 'It looks like some archived exports require updating, would you like to update these archived exports now?', 'woocommerce-exporter' ) . '<br /><br /><a href="' . $override_url . '" class="button-primary">' . __( 'Update export archives', 'woocommerce-exporter' ) . '</a>';
			woo_ce_admin_notice_html( $message, 'notice' );
		}

	}

	// Saves the state of Export fields for next export
	function woo_ce_save_fields( $export_type = '', $fields = array(), $sorting = array() ) {

		// Default fields
		if( $fields == false && !is_array( $fields ) )
			$fields = array();
		$export_types = array_keys( woo_ce_get_export_types() );
		if( in_array( $export_type, $export_types ) && !empty( $fields ) ) {
			woo_ce_update_option( $export_type . '_fields', array_map( 'sanitize_text_field', $fields ) );
			woo_ce_update_option( $export_type . '_sorting', array_map( 'absint', $sorting ) );
		}

	}

	// Returns number of an Export type prior to export, used on Store Exporter screen
	function woo_ce_get_export_type_count( $export_type = '', $args = array() ) {

		global $wpdb;

		$count_sql = null;
		$woocommerce_version = woo_get_woo_version();

		switch( $export_type ) {

			case 'product':
				$count = woo_ce_get_export_type_product_count();
				break;

			case 'category':
				$count = woo_ce_get_export_type_category_count();
				break;

			case 'tag':
				$count = woo_ce_get_export_type_tag_count();
				break;

			case 'order':
				$count = woo_ce_get_export_type_order_count();
				break;

			case 'customer':
				$count = woo_ce_get_export_type_customer_count();
				break;

			case 'user':
				$count = woo_ce_get_export_type_user_count();
				break;

			case 'review':
				$count = woo_ce_get_export_type_review_count();
				break;

			case 'coupon':
				$count = woo_ce_get_export_type_coupon_count();
				break;

			case 'shipping_class':
				$count = woo_ce_get_export_type_shipping_class_count();
				break;

			case 'attribute':
				$count = woo_ce_get_export_type_attribute_count();
				break;

			// Allow Plugin/Theme authors to populate their own custom export type counts
			default:
				$count = 0;
				$count = apply_filters( 'woo_ce_get_export_type_count', $count, $export_type, $args );
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				if( is_object( $count ) ) {
					$count = (array)$count;
					$count = absint( array_sum( $count ) );
				}
				return $count;
			} else {
				if( $count_sql )
					$count = $wpdb->get_var( $count_sql );
				else
					$count = 0;
			}
			return $count;
		} else {
			return 0;
		}

	}

	// In-line display of export file and export details when viewed via WordPress Media screen
	function woo_ce_read_export_file( $post = false ) {

		if( empty( $post ) ) {
			if( isset( $_GET['post'] ) )
				$post = get_post( $_GET['post'] );
		}

		if( $post->post_type != 'attachment' )
			return;

		// Check if the Post matches one of our Post Mime Types
		if( !in_array( $post->post_mime_type, array_values( woo_ce_get_mime_types() ) ) )
			return;

		$filepath = get_attached_file( $post->ID );

		// We can only read CSV, TSV and XML file types, the others are encoded
		if( in_array( $post->post_mime_type, array( 'text/csv', 'text/tab-separated-values', 'application/xml', 'application/rss+xml' ) ) ) {

			$contents = __( 'No export entries were found, please try again with different export filters.', 'woocommerce-exporter' );
			if( file_exists( $filepath ) ) {
				$contents = file_get_contents( $filepath );
			} else {
				// This resets the _wp_attached_file Post meta key to the correct value
				update_attached_file( $post->ID, $post->guid );
				// Try grabbing the file contents again
				$filepath = get_attached_file( $post->ID );
				if( file_exists( $filepath ) ) {
					$handle = fopen( $filepath, "r" );
					$contents = stream_get_contents( $handle );
					fclose( $handle );
				}
			}
			if( !empty( $contents ) )
				include_once( WOO_CE_PATH . 'templates/admin/media-csv_file.php' );

		}

		// We can still show the Export Details for any supported Post Mime Type
		$export_type = get_post_meta( $post->ID, '_woo_export_type', true );
		$columns = get_post_meta( $post->ID, '_woo_columns', true );
		$rows = get_post_meta( $post->ID, '_woo_rows', true );
		$scheduled_id = get_post_meta( $post->ID, '_scheduled_id', true );
		$start_time = get_post_meta( $post->ID, '_woo_start_time', true );
		$end_time = get_post_meta( $post->ID, '_woo_end_time', true );
		$idle_memory_start = get_post_meta( $post->ID, '_woo_idle_memory_start', true );
		$data_memory_start = get_post_meta( $post->ID, '_woo_data_memory_start', true );
		$data_memory_end = get_post_meta( $post->ID, '_woo_data_memory_end', true );
		$idle_memory_end = get_post_meta( $post->ID, '_woo_idle_memory_end', true );

		include_once( WOO_CE_PATH . 'templates/admin/media-export_details.php' );

	}
	add_action( 'edit_form_after_editor', 'woo_ce_read_export_file' );

	// Returns a list of archived exports
	function woo_ce_get_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array_values( woo_ce_get_mime_types() ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
			'posts_per_page' => -1
		);
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( !empty( $filter ) )
				$args['meta_value'] = $filter;
		}
		$files = get_posts( $args );
		return $files;

	}

	function woo_ce_nuke_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array_values( woo_ce_get_mime_types() ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$post_query = new WP_Query( $args );
		if( !empty( $post_query->found_posts ) ) {
			foreach( $post_query->posts as $post_ID )
				wp_delete_attachment( $post_ID, true );
			return true;
		}

	}

	// Delete all WordPress Options generated by Store Exporter
	function woo_ce_nuke_options() {

		global $wpdb;

		$prefix = 'woo_ce_%';

		// Get a list of WordPress Options prefixed by woo_ce_
		$options_sql = $wpdb->prepare( "SELECT `option_name` FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE %s", $prefix );
		$options = $wpdb->get_col( $options_sql );
		if( !empty( $options ) ) {
			$count = 0;
			// Get a count of WordPress Options to be deleted
			$size = count( $options );
			foreach( $options as $option ) {
				// Get a count of deleted WordPress Options
				if( delete_option( $option ) )
					$count++;
			}
			// Compare the count of WordPress Options vs deleted WordPress Options
			if( $count == $size )
				return true;
		}

	}

	// Reset all dismissed notices within Store Exporter
	function woo_ce_nuke_dismissed_notices() {

		global $wpdb;

		$prefix = 'woo_ce_dismiss_%';

		// Get a list of WordPress Options prefixed by woo_ce_dismiss_
		$options_sql = $wpdb->prepare( "SELECT `option_name` FROM `" . $wpdb->prefix . "options` WHERE `option_name` LIKE %s", $prefix );
		$options = $wpdb->get_col( $options_sql );
		if( !empty( $options ) ) {
			foreach( $options as $option )
				delete_option( $option );
		}

	}

	// Returns a list of Attachments which are exposed to the public
	function woo_ce_get_unprotected_archives( $postarr = array() ) {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array_values( woo_ce_get_mime_types() ),
			'meta_key' => $meta_key,
			'post_status' => 'inherit',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$args = wp_parse_args( $postarr, $args );
		$post_query = new WP_Query( $args );
		if( !empty( $post_query->found_posts ) ) {
			// Check if we are returning a count or list
			if( isset( $postarr['count'] ) ) {
				return $post_query->found_posts;
			}
			return $post_query->posts;
		}

	}

	function woo_ce_update_archives_privacy() {

		$attachments = woo_ce_get_unprotected_archives();
		if( !empty( $attachments ) ) {
			foreach( $attachments as $post_ID ) {
				$args = array(
					'ID' => $post_ID,
					'post_status' => 'private'
				);
				wp_update_post( $args );
			}
			return true;
		}

	}

	// Returns an archived export with additional details
	function woo_ce_get_archive_file( $file = '' ) {

		$upload_dir = wp_upload_dir();
		$file->export_type = get_post_meta( $file->ID, '_woo_export_type', true );
		$file->export_type_label = woo_ce_export_type_label( $file->export_type );
		if( empty( $file->export_type ) )
			$file->export_type = __( 'Unassigned', 'woocommerce-exporter' );
		if( empty( $file->guid ) )
			$file->guid = $upload_dir['url'] . '/' . basename( $file->post_title );
		$file->post_mime_type = get_post_mime_type( $file->ID );
		if( !$file->post_mime_type )
			$file->post_mime_type = __( 'N/A', 'woocommerce-exporter' );
		$file->media_icon = wp_get_attachment_image( $file->ID, array( 80, 60 ), true );
		if( $author_name = get_user_by( 'id', $file->post_author ) )
			$file->post_author_name = $author_name->display_name;
		$file->post_date = woo_ce_format_archive_date( $file->ID );
		unset( $author_name, $t_time, $time );

		return $file;

	}

	// HTML template for displaying the current export type filter on the Archives screen
	function woo_ce_archives_quicklink_current( $current = '' ) {

		$output = '';
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( $filter == $current )
				$output = ' class="current"';
		} else if( $current == 'all' ) {
			$output = ' class="current"';
		}
		echo $output;

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function woo_ce_archives_quicklink_count( $type = '' ) {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'post_status' => 'any',
			'fields' => 'ids'
		);
		if( !empty( $type ) )
			$args['meta_value'] = $type;
		$post_query = new WP_Query( $args );

		return absint( $post_query->found_posts );

	}

	/* End of: WordPress Administration */

}

function woo_ce_raise_export_memory_limit() {

	// Check if WP_MAX_MEMORY_LIMIT is the same as the WP_MEMORY_LIMIT
	if( wp_convert_hr_to_bytes( WP_MAX_MEMORY_LIMIT ) < wp_convert_hr_to_bytes( WP_MEMORY_LIMIT ) ) {
		return WP_MEMORY_LIMIT;
	}

}

// Export process for CSV file
function woo_ce_export_dataset( $export_type = null, &$output = null ) {

	global $export;

	$separator = $export->delimiter;
	$export->columns = array();
	$export->total_rows = 0;
	$export->total_columns = 0;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

	set_transient( WOO_CE_PREFIX . '_running', time(), woo_ce_get_option( 'timeout', HOUR_IN_SECONDS ) );

	// Load up the fatal error notice if we 500 Internal Server Error (memory), hit a server timeout or encounter a fatal PHP error
	add_action( 'shutdown', 'woo_ce_fatal_error' );

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );
	add_filter( 'attribute_escape', 'woo_ce_filter_attribute_escape', 10, 2 );

	switch( $export_type ) {

		// Products
		case 'product':
			$fields = woo_ce_get_product_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_product_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			add_filter( 'woo_ce_export_dataset_product', 'woo_ce_export_dataset_override_product', 10, 2 );
			$output = apply_filters( 'woo_ce_export_dataset_product', $output, $export_type );
			remove_filter( 'woo_ce_export_dataset_product', 'woo_ce_export_dataset_override_product' );
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Categories
		case 'category':
			$fields = woo_ce_get_category_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_category_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			add_filter( 'woo_ce_export_dataset_category', 'woo_ce_export_dataset_override_category', 10, 2 );
			$output = apply_filters( 'woo_ce_export_dataset_category', $output, $export_type );
			remove_filter( 'woo_ce_export_dataset_category', 'woo_ce_export_dataset_override_category' );
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Tags
		case 'tag':
			$fields = woo_ce_get_tag_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_tag_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			add_filter( 'woo_ce_export_dataset_tag', 'woo_ce_export_dataset_override_tag', 10, 2 );
			$output = apply_filters( 'woo_ce_export_dataset_tag', $output, $export_type );
			remove_filter( 'woo_ce_export_dataset_tag', 'woo_ce_export_dataset_override_tag' );
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Orders
		case 'order':
			$fields = woo_ce_get_order_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_order_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			add_filter( 'woo_ce_export_dataset_order', 'woo_ce_export_dataset_override_order', 10, 2 );
			$output = apply_filters( 'woo_ce_export_dataset_order', $output, $export_type );
			remove_filter( 'woo_ce_export_dataset_order', 'woo_ce_export_dataset_override_order' );
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Users
		case 'user':
			$fields = woo_ce_get_user_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_user_field( $key );
			}
			$export->total_columns = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			add_filter( 'woo_ce_export_dataset_user', 'woo_ce_export_dataset_override_user', 10, 2 );
			$output = apply_filters( 'woo_ce_export_dataset_user', $output, $export_type );
			remove_filter( 'woo_ce_export_dataset_user', 'woo_ce_export_dataset_override_user' );
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );
	remove_filter( 'attribute_escape', 'woo_ce_filter_attribute_escape' );

	// Remove our fatal error notice so not to conflict with the CRON or scheduled export engine	
	remove_action( 'shutdown', 'woo_ce_fatal_error' );

	// Export completed successfully
	delete_transient( WOO_CE_PREFIX . '_running' );

	// Check that the export file is populated, export columns have been assigned and rows counted
	if( $output && $export->total_rows && $export->total_columns ) {
		if( in_array( $export->export_format, array( 'csv' ) ) ) {
			$output = woo_ce_file_encoding( $output );
			if( $export->export_format == 'csv' && $export->bom && ( WOO_CE_DEBUG == false ) )
				$output = "\xEF\xBB\xBF" . $output;
		}
		if( WOO_CE_DEBUG && !$export->cron ) {
			$response = set_transient( WOO_CE_PREFIX . '_debug_log', base64_encode( $output ), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );
			if( $response !== true ) {
				$message = __( 'The export contents were too large to store in a single WordPress transient, use the Volume offset / Limit volume options to reduce the size of your export and try again.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
				if( function_exists( 'woo_ce_admin_notice' ) )
					woo_ce_admin_notice( $message, 'error' );
				else
					error_log( sprintf( '[store-exporter] woo_ce_export_dataset() - %s', $message ) );
			}
		} else {
			return $output;
		}
	}

}

function woo_ce_fatal_error() {

	global $export;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter/usage/';

	$error = error_get_last();
	if( $error !== null ) {
		set_transient( WOO_CE_PREFIX . '_crashed', 1, woo_ce_get_option( 'timeout', HOUR_IN_SECONDS ) );
		$message = '';
		$notice = sprintf( __( 'Refer to the following error and contact us on http://wordpress.org/plugins/woocommerce-exporter/ for further assistance. Error: <code>%s</code>', 'woocommerce-exporter' ), $error['message'] );
		if ( substr( $error['message'], 0, 22 ) === 'Maximum execution time' ) {
			$message = __( 'The server\'s maximum execution time is too low to complete this export. This is commonly due to a low timeout limit set by your hosting provider or PHP Safe Mode being enabled.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
		} elseif ( substr( $error['message'], 0, 19 ) === 'Allowed memory size' ) {
			$message = __( 'The server\'s maximum memory size is too low to complete this export. Consider increasing available memory to WordPress or reducing the size of your export.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
		} else if( $error['type'] === E_ERROR ) {
			// Test if it's WP All Import conflicting with the PHPExcel library
			if( substr( $error['message'], 0, 33 ) == "Class 'PHPExcel_Writer_Excel2007'" && ( strstr( $error['file'], 'wp-all-import' ) !== false ) ) {
				$message = __( 'A fatal PHP error was encountered during the export process, this was due to the Plugin WP All Import pre-loading the PHPExcel library. Contact the Plugin author Soflyy for more information.', 'woocommerce-exporter' );
			} else {
				$message = __( 'A fatal PHP error was encountered during the export process, we couldn\'t detect or diagnose it further.', 'woocommerce-exporter' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woocommerce-exporter' ) . '</a>)';
			}
		}
		if( !empty( $message ) ) {

			// Save a record to the PHP error log
			woo_ce_error_log( sprintf( __( 'Fatal error: %s - PHP response: %s in %s on line %s', 'woocommerce-exporter' ), $message, $error['message'], $error['file'], $error['line'] ) );
			error_log( sprintf( __( 'Fatal error: %s - PHP response: %s in %s on line %s', 'woocommerce-exporter' ), $message, $error['message'], $error['file'], $error['line'] ) );

			// Only display the message if this is a manual export
			if( ( !$export->cron && !$export->scheduled_export ) ) {
				$output = '<div id="message" class="error"><p>' . sprintf( __( '<strong>[store-exporter]</strong> An unexpected error occurred. %s', 'woocommerce-exporter' ), $message ) . '</p><p>' . $notice . '</p></div>';
				echo $output;
			}

		}
	}

}

// List of Export types used on Store Exporter screen
function woo_ce_get_export_types( $plural = true ) {

	$export_types = array(
		'product' => ( $plural ? __( 'Products', 'woocommerce-exporter' ) : __( 'Product', 'woocommerce-exporter' ) ),
		'category' => ( $plural ? __( 'Categories', 'woocommerce-exporter' ) : __( 'Category', 'woocommerce-exporter' ) ),
		'tag' => ( $plural ? __( 'Tags', 'woocommerce-exporter' ) : __( 'Tag', 'woocommerce-exporter' ) ),
		'brand' => ( $plural ? __( 'Brands', 'woocommerce-exporter' ) : __( 'Brand', 'woocommerce-exporter' ) ),
		'order' => ( $plural ? __( 'Orders', 'woocommerce-exporter' ) : __( 'Order', 'woocommerce-exporter' ) ),
		'customer' => ( $plural ? __( 'Customers', 'woocommerce-exporter' ) : __( 'Customer', 'woocommerce-exporter' ) ),
		'user' => ( $plural ? __( 'Users', 'woocommerce-exporter' ) : __( 'User', 'woocommerce-exporter' ) ),
		'review' => ( $plural ? __( 'Reviews', 'woocommerce-exporter' ) : __( 'Review', 'woocommerce-exporter' ) ),
		'coupon' => ( $plural ? __( 'Coupons', 'woocommerce-exporter' ) : __( 'Coupon', 'woocommerce-exporter' ) ),
		'subscription' => ( $plural ? __( 'Subscriptions', 'woocommerce-exporter' ) : __( 'Subscription', 'woocommerce-exporter' ) ),
		'product_vendor' => ( $plural ? __( 'Product Vendors', 'woocommerce-exporter' ) : __( 'Product Vendor', 'woocommerce-exporter' ) ),
		'commission' => ( $plural ? __( 'Commissions', 'woocommerce-exporter' ) : __( 'Commission', 'woocommerce-exporter' ) ),
		'shipping_class' => ( $plural ? __( 'Shipping Classes', 'woocommerce-exporter' ) : __( 'Shipping Class', 'woocommerce-exporter' ) ),
		'ticket' => ( $plural ? __( 'Tickets', 'woocommerce-exporter' ) : __( 'Ticket', 'woocommerce-exporter' ) ),
		'booking' => ( $plural ? __( 'Bookings', 'woocommerce-exporter' ) : __( 'Booking', 'woocommerce-exporter' ) ),
		'attribute' => ( $plural ? __( 'Attributes', 'woocommerce-exporter' ) : __( 'Attribute', 'woocommerce-exporter' ) )
	);
	$export_types = apply_filters( 'woo_ce_export_types', $export_types, $plural );

	return $export_types;

}

// Returns label of Export type slug used on Store Exporter screen
function woo_ce_export_type_label( $export_type = '', $echo = false, $plural = true ) {

	$output = '';
	if( !empty( $export_type ) ) {
		$export_types = woo_ce_get_export_types( $plural );
		if( array_key_exists( $export_type, $export_types ) )
			$output = $export_types[$export_type];
	}

	if( $echo )
		echo $output;
	else
		return $output;

}

function woo_ce_get_export_type_label( $export_type = '', $plural = true ) {

	$output = $export_type;
	if( !empty( $export_type ) ) {
		$export_types = woo_ce_get_export_types( $plural );
		// Check our export type exists
		$output = ( isset( $export_types[$export_type] ) ? $export_types[$export_type] : $output );
	}

	return $output;

}

// Returns the Post object of the export file saved as an attachment to the WordPress Media library
function woo_ce_save_file_attachment( $filename = '', $post_mime_type = 'text/csv' ) {

	if( !empty( $filename ) ) {
		$post_type = 'woo-export';
		$args = array(
			'post_status' => 'private',
			'post_title' => $filename,
			'post_type' => $post_type,
			'post_mime_type' => $post_mime_type
		);
		$post_ID = wp_insert_attachment( $args, $filename );
		if( is_wp_error( $post_ID ) )
			woo_ce_error_log( sprintf( 'Warning: %s', sprintf( 'save_file_attachment() - $s: %s', $filename, $result->get_error_message() ) ) );
		else
			return $post_ID;
	}

}

// Updates the GUID of the export file attachment to match the correct file URL
function woo_ce_save_file_guid( $post_ID, $export_type, $upload_url = '' ) {

	add_post_meta( $post_ID, '_woo_export_type', $export_type );
	if( !empty( $upload_url ) ) {
		$args = array(
			'ID' => $post_ID,
			'guid' => $upload_url
		);
		wp_update_post( $args );
	}

}

// Save critical export details against the archived export
function woo_ce_save_file_details( $post_ID ) {

	global $export;

	add_post_meta( $post_ID, '_woo_start_time', $export->start_time );
	add_post_meta( $post_ID, '_woo_idle_memory_start', $export->idle_memory_start );
	add_post_meta( $post_ID, '_woo_columns', $export->total_columns );
	add_post_meta( $post_ID, '_woo_rows', $export->total_rows );
	add_post_meta( $post_ID, '_woo_data_memory_start', $export->data_memory_start );
	add_post_meta( $post_ID, '_woo_data_memory_end', $export->data_memory_end );

}

// Update detail of existing archived export
function woo_ce_update_file_detail( $post_ID, $detail, $value ) {

	if( strstr( $detail, '_woo_' ) !== false )
		update_post_meta( $post_ID, $detail, $value );

}

// Returns a list of allowed Export type statuses, can be overridden on a per-Export type basis
function woo_ce_post_statuses( $extra_status = array(), $override = false ) {

	$output = array(
		'publish',
		'pending',
		'draft',
		'future',
		'private',
		'trash'
	);
	if( $override && !empty( $extra_status ) ) {
		$output = $extra_status;
	} else {
		if( $extra_status )
			$output = array_merge( $output, $extra_status );
	}

	return $output;

}

function woo_ce_get_mime_types() {

	$mime_types = array(
		'csv' => 'text/csv',
		'tsv' => 'text/tab-separated-values',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xml' => 'application/xml',
		'rss' => 'application/rss+xml'
	);

	// Allow Plugin/Theme authors to add support for additional export formats
	$mime_types = apply_filters( 'woo_ce_get_mime_types', $mime_types );

	return $mime_types;

}

function woo_ce_get_mime_type_extension( $mime_type, $search_by = 'extension' ) {

	$mime_types = woo_ce_get_mime_types();
	if( $search_by == 'extension' ) {
		if( isset( $mime_types[$mime_type] ) )
			return $mime_types[$mime_type];
	} else if( $search_by == 'mime_type' ) {
		if( $key = array_search( $mime_type, $mime_types ) )
			return strtoupper( $key );
	}

}

function woo_ce_add_missing_mime_type( $mime_types = array() ) {

	// Add CSV mime type if it has been removed
	if( !isset( $mime_types['csv'] ) )
		$mime_types['csv'] = 'text/csv';
	// Add TSV mime type if it has been removed
	if( !isset( $mime_types['tsv'] ) )
		$mime_types['tsv'] = 'text/tab-separated-values';
	// Add XLS mime type if it has been removed
	if( !isset( $mime_types['xls'] ) )
		$mime_types['xls'] = 'application/vnd.ms-excel';
	// Add XLSX mime type if it has been removed
	if( !isset( $mime_types['xlsx'] ) )
		$mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	// Add XML mime type if it has been removed
	if( !isset( $mime_types['xml'] ) )
		$mime_types['xml'] = 'application/xml';
	// Add RSS mime type if it has been removed
	if( !isset( $mime_types['rss'] ) )
		$mime_types['rss'] = 'application/rss+xml';

	// Allow Plugin/Theme authors to add support for additional export formats
	$mime_types = apply_filters( 'woo_ce_add_missing_mime_type', $mime_types );

	return $mime_types;

}
add_filter( 'upload_mimes', 'woo_ce_add_missing_mime_type' );

if( !function_exists( 'woo_ce_sort_fields' ) ) {
	function woo_ce_sort_fields( $key ) {

		return $key;

	}
}

// Add Store Export to filter types on the WordPress Media screen
function woo_ce_add_post_mime_type( $post_mime_types = array() ) {

	$post_mime_types['text/csv'] = array( __( 'Store Exports (CSV)', 'woocommerce-exporter' ), __( 'Manage Store Exports (CSV)', 'woocommerce-exporter' ), _n_noop( 'Store Export - CSV <span class="count">(%s)</span>', 'Store Exports - CSV <span class="count">(%s)</span>' ) );

	return $post_mime_types;

}
add_filter( 'post_mime_types', 'woo_ce_add_post_mime_type' );

function woo_ce_current_memory_usage() {

	$output = '';
	if( function_exists( 'memory_get_usage' ) )
		$output = round( memory_get_usage( true ) / 1024 / 1024, 2 );

	return $output;

}

function woo_ce_get_start_of_week_day() {

	global $wp_locale;

	$output = 'Monday';
	$start_of_week = get_option( 'start_of_week', 0 );
	for( $day_index = 0; $day_index <= 6; $day_index++ ) {
		if( $start_of_week == $day_index ) {
			$output = $wp_locale->get_weekday( $day_index );
			break;
		}
	}

	return $output;

}

function woo_ce_detect_product_brands() {

	if( class_exists( 'WC_Brands' ) || class_exists( 'woo_brands' ) || taxonomy_exists( apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' ) ) )
		return true;

}

// Return whether WPML exists
function woo_ce_detect_wpml() {

	if( defined( 'ICL_LANGUAGE_CODE' ) )
		return true;

}

// List of WordPress Plugins that Store Exporter integrates with
function woo_ce_modules_list( $module_status = false ) {

	$modules = array();
	$modules[] = array(
		'name' => 'aioseop',
		'title' => __( 'All in One SEO Pack', 'woocommerce-exporter' ),
		'description' => __( 'Optimize your WooCommerce Products for Search Engines. Requires Store Toolkit for All in One SEO Pack integration.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/extend/plugins/all-in-one-seo-pack/',
		'slug' => 'all-in-one-seo-pack',
		'function' => 'aioseop_activate'
	);
	$modules[] = array(
		'name' => 'store_toolkit',
		'title' => __( 'Store Toolkit', 'woocommerce-exporter' ),
		'description' => __( 'Store Toolkit includes a growing set of commonly-used WooCommerce administration tools aimed at web developers and store maintainers.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/extend/plugins/woocommerce-store-toolkit/',
		'slug' => 'woocommerce-store-toolkit',
		'function' => 'woo_st_admin_init'
	);
	$modules[] = array(
		'name' => 'ultimate_seo',
		'title' => __( 'SEO Ultimate', 'woocommerce-exporter' ),
		'description' => __( 'This all-in-one SEO plugin gives you control over Product details.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/extend/plugins/seo-ultimate/',
		'slug' => 'seo-ultimate',
		'function' => 'su_wp_incompat_notice'
	);
	$modules[] = array(
		'name' => 'gpf',
		'title' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' ),
		'description' => __( 'Easily configure data to be added to your Google Merchant Centre feed.', 'woocommerce-exporter' ),
		'url' => 'https://woocommerce.com/products/google-product-feed/',
		'function' => 'woocommerce_gpf_install'
	);
	$modules[] = array(
		'name' => 'wpseo',
		'title' => __( 'Yoast SEO', 'woocommerce-exporter' ),
		'description' => __( 'The first true all-in-one SEO solution for WordPress. Formally WordPress SEO by Yoast.', 'woocommerce-exporter' ),
		'url' => 'http://yoast.com/wordpress/seo/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpseoplugin',
		'slug' => 'wordpress-seo',
		'function' => 'wpseo_admin_init'
	);
	$modules[] = array(
		'name' => 'wpseo_wc',
		'title' => __( 'Yoast SEO: WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'This extension to WooCommerce and WordPress SEO by Yoast makes sure there\'s perfect communication between the two plugins.', 'woocommerce-exporter' ),
		'url' => 'https://yoast.com/wordpress/plugins/yoast-woocommerce-seo/',
		'function' => 'initialize_yoast_woocommerce_seo'
	);
	$modules[] = array(
		'name' => 'wc_msrp',
		'title' => __( 'WooCommerce MSRP Pricing', 'woocommerce-exporter' ),
		'description' => __( 'Define and display MSRP prices (Manufacturer\'s suggested retail price) to your customers.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/msrp-pricing/',
		'function' => 'woocommerce_msrp_activate'
	);
	$modules[] = array(
		'name' => 'wc_brands',
		'title' => __( 'WooCommerce Brands Addon', 'woocommerce-exporter' ),
		'description' => __( 'Create, assign and list brands for products, and allow customers to filter by brand.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/brands/',
		'class' => 'WC_Brands'
	);
	$modules[] = array(
		'name' => 'wc_cog',
		'title' => __( 'Cost of Goods', 'woocommerce-exporter' ),
		'description' => __( 'Easily track total profit and cost of goods by adding a Cost of Good field to simple and variable products.', 'woocommerce-exporter' ),
		'url' => 'http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/',
		'class' => 'WC_COG'
	);
	$modules[] = array(
		'name' => 'per_product_shipping',
		'title' => __( 'Per Product Shipping', 'woocommerce-exporter' ),
		'description' => __( 'Define separate shipping costs per product which are combined at checkout to provide a total shipping cost.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/per-product-shipping/',
		'class' => 'WC_Shipping_Per_Product_Init'
	);
	$modules[] = array(
		'name' => 'vendors',
		'title' => __( 'Product Vendors', 'woocommerce-exporter' ),
		'description' => __( 'Turn your store into a multi-vendor marketplace (such as Etsy or Creative Market).', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/product-vendors/',
		'class' => 'WC_Product_Vendors'
	);
	$modules[] = array(
		'name' => 'wc_vendors',
		'title' => __( 'WC Vendors', 'woocommerce-exporter' ),
		'description' => __( 'Allow vendors to sell their own products and receive a commission for each sale.', 'woocommerce-exporter' ),
		'url' => 'http://wcvendors.com',
		'class' => 'WC_Vendors'
	);
	$modules[] = array(
		'name' => 'wc_marketplace',
		'title' => __( 'WC Marketplace', 'woocommerce-exporter' ),
		'description' => __( 'The most user recommended multi-vendor marketplace plugin for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wc-marketplace.com/',
		'function' => 'wcmp_plugin_init'
	);
	$modules[] = array(
		'name' => 'acf',
		'title' => __( 'Advanced Custom Fields', 'woocommerce-exporter' ),
		'description' => __( 'Powerful fields for WordPress developers.', 'woocommerce-exporter' ),
		'url' => 'http://www.advancedcustomfields.com',
		'class' => 'acf'
	);
	$modules[] = array(
		'name' => 'product_addons',
		'title' => __( 'Product Add-ons', 'woocommerce-exporter' ),
		'description' => __( 'Allow your customers to customise your products by adding input boxes, dropdowns or a field set of checkboxes.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/product-add-ons/',
		'class' => array( 'Product_Addon_Admin', 'Product_Addon_Display', 'WC_Product_Addons' )
	);
	$modules[] = array(
		'name' => 'seq',
		'title' => __( 'WooCommerce Sequential Order Numbers', 'woocommerce-exporter' ),
		'description' => __( 'This plugin extends the WooCommerce e-commerce plugin by setting sequential order numbers for new orders.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-sequential-order-numbers/',
		'slug' => 'woocommerce-sequential-order-numbers',
		'class' => 'WC_Seq_Order_Number'
	);
	$modules[] = array(
		'name' => 'seq_pro',
		'title' => __( 'WooCommerce Sequential Order Numbers Pro', 'woocommerce-exporter' ),
		'description' => __( 'Tame your WooCommerce Order Numbers.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/sequential-order-numbers-pro/',
		'class' => 'WC_Seq_Order_Number_Pro'
	);
	$modules[] = array(
		'name' => 'print_invoice_delivery_note',
		'title' => __( 'WooCommerce Print Invoice & Delivery Note', 'woocommerce-exporter' ),
		'description' => __( 'Print invoices and delivery notes for WooCommerce orders.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-delivery-notes/',
		'slug' => 'woocommerce-delivery-notes',
		'class' => 'WooCommerce_Delivery_Notes'
	);
	$modules[] = array(
		'name' => 'pdf_invoices_packing_slips',
		'title' => __( 'WooCommerce PDF Invoices & Packing Slips', 'woocommerce-exporter' ),
		'description' => __( 'Create, print & automatically email PDF invoices & packing slips for WooCommerce orders.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/',
		'slug' => 'woocommerce-pdf-invoices-packing-slips',
		'class' => 'WooCommerce_PDF_Invoices'
	);
	$modules[] = array(
		'name' => 'pdf_invoices_packing_slips_pro',
		'title' => __( 'WooCommerce PDF Invoices & Packing Slips Professional', 'woocommerce-exporter' ),
		'description' => __( 'Extended functionality for the WooCommerce PDF Invoices & Packing Slips Plugin.', 'woocommerce-exporter' ),
		'url' => 'https://wpovernight.com/downloads/woocommerce-pdf-invoices-packing-slips-professional/',
		'class' => 'WooCommerce_PDF_IPS_Pro'
	);
	$modules[] = array(
		'name' => 'checkout_manager',
		'title' => __( 'WooCommerce Checkout Manager & WooCommerce Checkout Manager Pro', 'woocommerce-exporter' ),
		'description' => __( 'Manages the WooCommerce Checkout page and WooCommerce Checkout processes.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-checkout-manager/',
		'slug' => 'woocommerce-checkout-manager',
		'function' => array( 'wccs_install', 'wooccm_install', 'wccs_install_pro' )
	);
	$modules[] = array(
		'name' => 'wc_pgsk',
		'title' => __( 'Poor Guys Swiss Knife', 'woocommerce-exporter' ),
		'description' => __( 'A Swiss Knife for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/plugins/woocommerce-poor-guys-swiss-knife/',
		'slug' => 'woocommerce-poor-guys-swiss-knife',
		'function' => 'wcpgsk_init'
	);
	$modules[] = array(
		'name' => 'checkout_field_editor',
		'title' => __( 'Checkout Field Editor', 'woocommerce-exporter' ),
		'description' => __( 'Add, edit and remove fields shown on your WooCommerce checkout page.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-checkout-field-editor/',
		'function' => 'woocommerce_init_checkout_field_editor'
	);
	$modules[] = array(
		'name' => 'checkout_field_editor_pro',
		'title' => __( 'Checkout Field Editor Pro', 'woocommerce-exporter' ),
		'description' => __( 'Design woocommerce checkout form in your own way, customize checkout fields (Add, Edit, Delete and re arrange fields).', 'woocommerce-exporter' ),
		'url' => 'https://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/',
		'class' => 'WCFE_Checkout_Field_Editor'
	);
	$modules[] = array(
		'name' => 'checkout_field_manager',
		'title' => __( 'Checkout Field Manager', 'woocommerce-exporter' ),
		'description' => __( 'Quickly and effortlessly add, remove and re-orders fields in the checkout process.', 'woocommerce-exporter' ),
		'url' => 'http://61extensions.com/shop/woocommerce-checkout-field-manager/',
		'function' => 'sod_woocommerce_checkout_manager_settings'
	);
	$modules[] = array(
		'name' => 'checkout_addons',
		'title' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' ),
		'description' => __( 'Add fields at checkout for add-on products and services while optionally setting a cost for each add-on.', 'woocommerce-exporter' ),
		'url' => 'http://www.skyverge.com/product/woocommerce-checkout-add-ons/',
		'function' => 'init_woocommerce_checkout_add_ons'
	);
	$modules[] = array(
		'name' => 'local_pickup_plus',
		'title' => __( 'Local Pickup Plus', 'woocommerce-exporter' ),
		'description' => __( 'Let customers pick up products from specific locations.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/local-pickup-plus/',
		'class' => 'WC_Local_Pickup_Plus'
	);
	$modules[] = array(
		'name' => 'gravity_forms',
		'title' => __( 'Gravity Forms', 'woocommerce-exporter' ),
		'description' => __( 'Gravity Forms is hands down the best contact form plugin for WordPress powered websites.', 'woocommerce-exporter' ),
		'url' => 'http://www.gravityforms.com/',
		'class' => 'RGForms'
	);
	$modules[] = array(
		'name' => 'woocommerce_gravity_forms',
		'title' => __( 'WooCommerce Gravity Forms Product Add-Ons', 'woocommerce-exporter' ),
		'description' => __( 'Allows you to use Gravity Forms on individual WooCommerce products.', 'woocommerce-exporter' ),
		'url' => 'https://www.woothemes.com/products/gravity-forms-add-ons/',
		'class' => array( 'woocommerce_gravityforms', 'WC_GFPA_Main' )
	);
	$modules[] = array(
		'name' => 'currency_switcher',
		'title' => __( 'WooCommerce Currency Switcher', 'woocommerce-exporter' ),
		'description' => __( 'Currency Switcher for WooCommerce allows your shop to display prices and accept payments in multiple currencies.', 'woocommerce-exporter' ),
		'url' => 'http://aelia.co/shop/currency-switcher-woocommerce/',
		'class' => 'WC_Aelia_CurrencySwitcher'
	);
	$modules[] = array(
		'name' => 'subscriptions',
		'title' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
		'description' => __( 'WC Subscriptions makes it easy to create and manage products with recurring payments.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-subscriptions/',
		'class' => array( 'WC_Subscriptions', 'WC_Subscriptions_Manager' )
	);
	$modules[] = array(
		'name' => 'extra_product_options',
		'title' => __( 'WooCommerce Extra Product Options', 'woocommerce-exporter' ),
		'description' => __( 'Create extra price fields globally or per-Product', 'woocommerce-exporter' ),
		'url' => 'http://codecanyon.net/item/woocommerce-extra-product-options/7908619',
		'class' => array( 'TM_Extra_Product_Options', 'Themecomplete_Extra_Product_Options_Setup' )
	);
	$modules[] = array(
		'name' => 'woocommerce_jetpack',
		'title' => __( 'Booster for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Supercharge your WooCommerce site with these awesome powerful features (formally WooCommerce Jetpack).', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-jetpack/',
		'slug' => 'woocommerce-jetpack',
		'class' => 'WC_Jetpack'
	);
	$modules[] = array(
		'name' => 'woocommerce_jetpack_plus',
		'title' => __( 'Booster Plus', 'woocommerce-exporter' ),
		'description' => __( 'Unlock all WooCommerce Booster features and supercharge your WordPress WooCommerce site even more (formally WooCommerce Jetpack Plus).', 'woocommerce-exporter' ),
		'url' => 'http://woojetpack.com/shop/wordpress-woocommerce-jetpack-plus/',
		'class' => 'WC_Jetpack_Plus'
	);
	$modules[] = array(
		'name' => 'woocommerce_brands',
		'title' => __( 'WooCommerce Brands', 'woocommerce-exporter' ),
		'description' => __( 'Woocommerce Brands Plugin. After Install and active this plugin you\'ll have some shortcode and some widget for display your brands in fornt-end website.', 'woocommerce-exporter' ),
		'url' => 'http://proword.net/Woocommerce_Brands/',
		'class' => 'woo_brands'
	);
	$modules[] = array(
		'name' => 'woocommerce_bookings',
		'title' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
		'description' => __( 'Setup bookable products such as for reservations, services and hires.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-bookings/',
		'class' => 'WC_Bookings'
	);
	$modules[] = array(
		'name' => 'eu_vat',
		'title' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' ),
		'description' => __( 'The EU VAT Number extension lets you collect and validate EU VAT numbers during checkout to identify B2B transactions verses B2C.', 'woocommerce-exporter' ),
		'url' => 'https://www.woothemes.com/products/eu-vat-number/',
		'function' => '__wc_eu_vat_number_init',
		'class' => 'WC_EU_VAT_Number_Init'
	);
	$modules[] = array(
		'name' => 'aelia_eu_vat',
		'title' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' ),
		'description' => __( 'Assists with EU VAT compliance, for the new VAT regime beginning 1st January 2015.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-eu-vat-assistant/',
		'slug' => 'woocommerce-eu-vat-assistant',
		'class' => 'WC_Aelia_EU_VAT_Assistant'
	);
	$modules[] = array(
		'name' => 'hear_about_us',
		'title' => __( 'WooCommerce Hear About Us', 'woocommerce-exporter' ),
		'description' => __( 'Ask where your new customers come from at Checkout.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-hear-about-us/',
		'slug' => 'woocommerce-hear-about-us', // Define this if the Plugin is hosted on the WordPress repo
		'class' => 'WooCommerce_HearAboutUs'
	);
	$modules[] = array(
		'name' => 'wholesale_pricing',
		'title' => __( 'WooCommerce Wholesale Pricing', 'woocommerce-exporter' ),
		'description' => __( 'Allows you to set wholesale prices for products and variations.', 'woocommerce-exporter' ),
		'url' => 'http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/',
		'class' => 'woocommerce_wholesale_pricing'
	);
	$modules[] = array(
		'name' => 'wc_barcodes',
		'title' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Allows you to add GTIN (former EAN) codes natively to your products.', 'woocommerce-exporter' ),
		'url' => 'http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/',
		'function' => 'wpps_requirements_met'
	);
	$modules[] = array(
		'name' => 'wc_smart_coupons',
		'title' => __( 'WooCommerce Smart Coupons', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Smart Coupons lets customers buy gift certificates, store credits or coupons easily.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/smart-coupons/',
		'class' => 'WC_Smart_Coupons'
	);
	$modules[] = array(
		'name' => 'wc_preorders',
		'title' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' ),
		'description' => __( 'Sell pre-orders for products in your WooCommerce store.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-pre-orders/',
		'class' => 'WC_Pre_Orders'
	);
	$modules[] = array(
		'name' => 'order_numbers_basic',
		'title' => __( 'WooCommerce Basic Ordernumbers', 'woocommerce-exporter' ),
		'description' => __( 'Lets the user freely configure the order numbers in WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://open-tools.net/woocommerce/advanced-ordernumbers-for-woocommerce.html',
		'class' => 'OpenToolsOrdernumbersBasic'
	);
	$modules[] = array(
		'name' => 'admin_custom_order_fields',
		'title' => __( 'WooCommerce Admin Custom Order Fields', 'woocommerce-exporter' ),
		'description' => __( 'Easily add custom fields to your WooCommerce orders and display them in the Orders admin, the My Orders section and order emails.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-admin-custom-order-fields/',
		'function' => 'init_woocommerce_admin_custom_order_fields'
	);
	$modules[] = array(
		'name' => 'table_rate_shipping_plus',
		'title' => __( 'WooCommerce Table Rate Shipping Plus', 'woocommerce-exporter' ),
		'description' => __( 'Calculate shipping costs based on destination, weight and price.', 'woocommerce-exporter' ),
		'url' => 'http://mangohour.com/plugins/woocommerce-table-rate-shipping',
		'function' => 'mh_wc_table_rate_plus_init'
	);
	$modules[] = array(
		'name' => 'wc_extra_checkout_fields_brazil',
		'title' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' ),
		'description' => __( 'Adds Brazilian checkout fields in WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/',
		'slug' => 'woocommerce-extra-checkout-fields-for-brazil',
		'class' => 'Extra_Checkout_Fields_For_Brazil'
	);
	$modules[] = array(
		'name' => 'wc_quickdonation',
		'title' => __( 'WooCommerce Quick Donation', 'woocommerce-exporter' ),
		'description' => __( 'Turns WooCommerce into online donation.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-quick-donation/',
		'slug' => 'woocommerce-quick-donation',
		'class' => 'WooCommerce_Quick_Donation'
	);
	$modules[] = array(
		'name' => 'wc_easycheckout',
		'title' => __( 'Easy Checkout Fields Editor', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Easy Checkout Fields Editor.', 'woocommerce-exporter' ),
		'url' => 'http://codecanyon.net/item/woocommerce-easy-checkout-field-editor/9799777',
		'function' => 'pcmfe_admin_form_field'
	);
	$modules[] = array(
		'name' => 'wc_productfees',
		'title' => __( 'Product Fees', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Product Fees allows you to add additional fees at checkout based on products that are in the cart.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-product-fees/',
		'slug' => 'woocommerce-product-fees',
		'class' => 'WooCommerce_Product_Fees'
	);
	$modules[] = array(
		'name' => 'fooevents',
		'title' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Adds event and ticketing features to WooCommerce, formally FooEvents for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://www.woocommerceevents.com/',
		'class' => array( 'WooCommerce_Events', 'FooEvents' )
	);
	$modules[] = array(
		'name' => 'wc_tabmanager',
		'title' => __( 'WooCommerce Tab Manager', 'woocommerce-exporter' ),
		'description' => __( 'A product tab manager for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-tab-manager/',
		'class' => 'WC_Tab_Manager'
	);
	$modules[] = array(
		'name' => 'wc_customfields',
		'title' => __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ),
		'description' => __( 'Create custom fields for WooCommerce product, checkout, order and customer pages.', 'woocommerce-exporter' ),
		'url' => 'http://www.rightpress.net/woocommerce-custom-fields',
		'class' => array( 'RP_WCCF', 'WCCF' )
	);
	$modules[] = array(
		'name' => 'barcode_isbn',
		'title' => __( 'WooCommerce Barcode & ISBN', 'woocommerce-exporter' ),
		'description' => __( 'A plugin to add a barcode & ISBN to WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-barcode-isbn/',
		'slug' => 'woocommerce-barcode-isbn',
		'function' => 'woo_add_barcode'
	);
	$modules[] = array(
		'name' => 'woo_add_gtin',
		'title' => __( 'WooCommerce UPC, EAN, and ISBN', 'woocommerce-exporter' ),
		'description' => __( 'Add GTIN codes like UPC, EAN, and ISBN to your WooCommerce Products.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woo-add-gtin/',
		'slug' => 'woo-add-gtin',
		'class' => 'Woo_GTIN'
	);
	$modules[] = array(
		'name' => 'video_product_tab',
		'title' => __( 'WooCommerce Video Product Tab', 'woocommerce-exporter' ),
		'description' => __( 'Extends WooCommerce to allow you to add a Video to the Product page.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-video-product-tab/',
		'slug' => 'woocommerce-video-product-tab',
		'class' => 'WooCommerce_Video_Product_Tab'
	);
	$modules[] = array(
		'name' => 'external_featured_image',
		'title' => __( 'Nelio External Featured Image', 'woocommerce-exporter' ),
		'description' => __( 'Use external images from anywhere as the featured image of your pages and posts.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/external-featured-image/',
		'slug' => 'external-featured-image', // Define this if the Plugin is hosted on the WordPress repo
		'function' => '_nelioefi_url'
	);
	$modules[] = array(
		'name' => 'variation_swatches_photos',
		'title' => __( 'WooCommerce Variation Swatches and Photos', 'woocommerce-exporter' ),
		'description' => __( 'Configure colors and photos for shoppers on your site to use when picking variations.', 'woocommerce-exporter' ),
		'url' => 'https://www.woothemes.com/products/variation-swatches-and-photos/',
		'class' => 'WC_SwatchesPlugin'
	);
	$modules[] = array(
		'name' => 'wc_uploads',
		'title' => __( 'WooCommerce Uploads', 'woocommerce-exporter' ),
		'description' => __( 'Upload files in WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wpfortune.com/shop/plugins/woocommerce-uploads/',
		'class' => 'WPF_Uploads'
	);
	$modules[] = array(
		'name' => 'wc_posr',
		'title' => __( 'WooCommerce Profit of Sales Report', 'woocommerce-exporter' ),
		'description' => __( 'This plugin provides Profit of Sales Report based on Cost of Goods.', 'woocommerce-exporter' ),
		'url' => 'http://codecanyon.net/item/woocommerce-profit-of-sales-report/9190590',
		'function' => 'POSRFront'
	);
	$modules[] = array(
		'name' => 'orddd_free',
		'title' => __( 'Order Delivery Date for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Allow the customers to choose an order delivery date on the checkout page for WooCommerce store owners.', 'woocommerce-exporter' ),
		'slug' => 'order-delivery-date-for-woocommerce',
		'url' => 'https://wordpress.org/plugins/order-delivery-date-for-woocommerce/',
		'class' => 'order_delivery_date_lite'
	);
	$modules[] = array(
		'name' => 'orddd',
		'title' => __( 'Order Delivery Date Pro for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Allows customers to choose their preferred Order Delivery Date & Delivery Time during checkout.', 'woocommerce-exporter' ),
		'url' => 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/',
		'class' => 'order_delivery_date'
	);
	$modules[] = array(
		'name' => 'wc_eu_vat_compliance',
		'title' => __( 'WooCommerce EU VAT Compliance', 'woocommerce-exporter' ),
		'description' => __( 'Provides features to assist WooCommerce with EU VAT compliance.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-eu-vat-compliance/',
		'slug' => 'woocommerce-eu-vat-compliance',
		'class' => 'WC_EU_VAT_Compliance'
	);
	$modules[] = array(
		'name' => 'wc_eu_vat_compliance_pro',
		'title' => __( 'WooCommerce EU VAT Compliance (Premium)', 'woocommerce-exporter' ),
		'description' => __( 'Provides features to assist WooCommerce with EU VAT compliance.', 'woocommerce-exporter' ),
		'url' => 'https://www.simbahosting.co.uk/s3/product/woocommerce-eu-vat-compliance/',
		'slug' => 'woocommerce-eu-vat-compliance',
		'class' => 'WC_EU_VAT_Compliance_Premium'
	);
	$modules[] = array(
		'name' => 'yith_cm',
		'title' => __( 'YITH WooCommerce Checkout Manager', 'woocommerce-exporter' ),
		'description' => __( 'YITH WooCommerce Checkout Manager lets you add, edit or remove checkout fields.', 'woocommerce-exporter' ),
		'url' => 'https://yithemes.com/themes/plugins/yith-woocommerce-checkout-manager/',
		'function' => 'ywccp_init'
	);
	$modules[] = array(
		'name' => 'vt_dp',
		'title' => __( 'Discontinued Product for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Adds the ability to flag a product as discontinued to WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/discontinued-product-for-woocommerce/',
		'slug' => 'discontinued-product-for-woocommerce',
		'function' => 'discontinued_product_for_woocommerce_init'
	);
	$modules[] = array(
		'name' => 'yith_vendor',
		'title' => __( 'YITH WooCommerce Multi Vendor Premium', 'woocommerce-exporter' ),
		'description' => __( 'Switch your website into a platform hosting more than one shop.', 'woocommerce-exporter' ),
		'url' => 'http://yithemes.com/themes/plugins/yith-woocommerce-product-vendors/',
		'function' => 'YITH_Vendors'
	);
	$modules[] = array(
		'name' => 'wc_memberships',
		'title' => __( 'WooCommerce Memberships', 'woocommerce-exporter' ),
		'description' => __( 'Sell memberships that provide access to restricted content, products, discounts, and more!', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/woocommerce-memberships/',
		'function' => 'init_woocommerce_memberships',
		'class' => 'WC_Memberships_Loader'
	);
	$modules[] = array(
		'name' => 'wc_product_bundles',
		'title' => __( 'WooCommerce Product Bundles', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce extension for creating simple product bundles, kits and assemblies.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/product-bundles/',
		'class' => 'WC_Bundles'
	);
	$modules[] = array(
		'name' => 'wc_min_max',
		'title' => __( 'WooCommerce Min/Max Quantities', 'woocommerce-exporter' ),
		'description' => __( 'Lets you define minimum/maximum allowed quantities for products, variations and orders.', 'woocommerce-exporter' ),
		'url' => 'https://woocommerce.com/products/minmax-quantities/',
		'class' => 'WC_Min_Max_Quantities'
	);
	$modules[] = array(
		'name' => 'wc_followupemails',
		'title' => __( 'WooCommerce Follow Ups', 'woocommerce-exporter' ),
		'description' => __( 'Follow-Ups makes it easy to automate communications via email and Twitter to keep your customers engaged and spending money.', 'woocommerce-exporter' ),
		'url' => 'https://woocommerce.com/products/follow-up-emails/',
		'class' => 'FollowUpEmails'
	);
	$modules[] = array(
		'name' => 'wc_ship_multiple',
		'title' => __( 'Ship to Multiple Addresses', 'woocommerce-exporter' ),
		'description' => __( 'Allow your customers to ship individual items in a single order to multiple addresses.', 'woocommerce-exporter' ),
		'url' => 'https://woocommerce.com/products/shipping-multiple-addresses/',
		'class' => 'WC_Ship_Multiple'
	);
	$modules[] = array(
		'name' => 'ups_ap_shipping',
		'title' => __( 'WooCommerce UPS Access Point Shipping', 'woocommerce-exporter' ),
		'description' => __( '(UK) This plugin integrates with UPS Access Point&trade; services to deliver parcels at the nearest convenience store.', 'woocommerce-exporter' ),
		'url' => 'https://shop.renoovodesign.co.uk/product/ups-access-point-plugin-woocommerce/',
		'function' => 'run_woocommerce_ups_ap_shipping'
	);
	$modules[] = array(
		'name' => 'awebooking',
		'title' => __( 'AweBooking', 'woocommerce-exporter' ),
		'description' => __( 'You can easily create a booking/reservation system into your WordPress website without any hassle', 'woocommerce-exporter' ),
		'url' => 'https://codecanyon.net/item/awebooking-online-hotel-booking-for-wordpress/12323878',
		'function' => 'awebooking_clean_room'
	);
	$modules[] = array(
		'name' => 'yith_delivery_pro',
		'title' => __( 'YITH WooCommerce Delivery Date Premium', 'woocommerce-exporter' ),
		'description' => __( 'Let your customers choose a delivery date for their orders', 'woocommerce-exporter' ),
		'url' => 'http://yithemes.com/themes/plugins/yith-woocommerce-delivery-date/',
		'function' => 'yith_delivery_date_init_plugin'
	);
	$modules[] = array(
		'name' => 'yith_brands_pro',
		'title' => __( 'YITH WooCommerce Brands Add-On', 'woocommerce-exporter' ),
		'description' => __( 'YITH WooCommerce Brands Add-on allows you to add brands functionality to the default WooCommerce Plugin.', 'woocommerce-exporter' ),
		'url' => 'http://yithemes.com/themes/plugins/yith-woocommerce-brands-add-on/',
		'function' => 'yith_brands_constructor'
	);
	$modules[] = array(
		'name' => 'ign_tiered',
		'title' => __( 'WooCommerce Tiered Pricing', 'woocommerce-exporter' ),
		'description' => __( 'Allows you to set price tiers for products and variations based on user roles.', 'woocommerce-exporter' ),
		'url' => 'http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-tiered-pricing/',
		'function' => 'ign_tiered_init'
	);
	$modules[] = array(
		'name' => 'wc_books',
		'title' => __( 'WooCommerce BookStore', 'woocommerce-exporter' ),
		'description' => __( 'Convert your WooCommerce store to online book store. Sell books using WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://www.wpini.com/woocommerce-bookstore-plugin/',
		'function' => 'woo_bookstore_init'
	);
	$modules[] = array(
		'name' => 'wc_point_of_sales',
		'title' => __( 'WooCommerce Point of Sale', 'woocommerce-exporter' ),
		'description' => __( 'Extend your online WooCommerce store by adding a brick and mortar Point of Sale (POS) interface.', 'woocommerce-exporter' ),
		'url' => 'https://codecanyon.net/item/woocommerce-point-of-sale-pos/7869665',
		'function' => 'WC_POS'
	);
	$modules[] = array(
		'name' => 'wc_pdf_product_vouchers',
		'title' => __( 'WooCommerce PDF Product Vouchers', 'woocommerce-exporter' ),
		'description' => __( 'Customize and sell PDF product vouchers with WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://www.woothemes.com/products/pdf-product-vouchers/',
		'function' => 'init_woocommerce_pdf_product_vouchers'
	);
	$modules[] = array(
		'name' => 'wpml',
		'title' => __( 'WPML Multilingual CMS', 'woocommerce-exporter' ),
		'description' => __( 'WPML Multilingual CMS.', 'woocommerce-exporter' ),
		'url' => 'https://wpml.org/',
		'function' => 'icl_sitepress_activate'
	);
	$modules[] = array(
		'name' => 'wpml_wc',
		'title' => __( 'WooCommerce Multilingual', 'woocommerce-exporter' ),
		'description' => __( 'Allows running fully multilingual e-Commerce sites with WooCommerce and WPML.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-multilingual/',
		'slug' => 'woocommerce-multilingual',
		'class' => 'woocommerce_wpml'
	);
	$modules[] = array(
		'name' => 'wootabs',
		'title' => __( 'WooTabs', 'woocommerce-exporter' ),
		'description' => __( 'WooTabs allows you to add extra tabs (as many as you want) to the WooCommerce Product Details page.', 'woocommerce-exporter' ),
		'url' => 'https://codecanyon.net/item/wootabsadd-extra-tabs-to-woocommerce-product-page/7891253',
		'function' => 'on_woocommerce_wootabs_loaded'
	);
	$modules[] = array(
		'name' => 'wc_ean',
		'title' => __( 'WooCommerce EAN Payment Gateway', 'woocommerce-exporter' ),
		'description' => __( 'This plugin adds an EAN13 Payment Gateway for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://plugins.yanco.dk/woocommerce-ean-payment-gateway',
		'function' => 'WOOCMMERCE_EAN_PAYMENT_GATEWAY'
	);
	$modules[] = array(
		'name' => 'wc_germanized',
		'title' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
		'description' => __( 'Extends WooCommerce to become a legally compliant store for the German market.', 'woocommerce-exporter' ),
		'url' => 'https://www.vendidero.de/woocommerce-germanized',
		'function' => 'WC_germanized'
	);
	$modules[] = array(
		'name' => 'wc_germanized_pro',
		'title' => __( 'WooCommerce Germanized Pro', 'woocommerce-exporter' ),
		'description' => __( 'Extends WooCommerce Germanized with professional features such as PDF invoices, legal text generators and many more.', 'woocommerce-exporter' ),
		'url' => 'https://www.vendidero.de/woocommerce-germanized',
		'function' => 'WC_germanized_pro'
	);
	$modules[] = array(
		'name' => 'wc_umcs',
		'title' => __( 'WooCommerce Ultimate Multi Currency Suite', 'woocommerce-exporter' ),
		'description' => __( 'Multi currency e-commerce plugin for WordPress-WooCommerce systems.', 'woocommerce-exporter' ),
		'url' => 'https://codecanyon.net/item/woocommerce-ultimate-multi-currency-suite/11997014',
		'class' => 'WooCommerce_Ultimate_Multi_Currency_Suite_Main'
	);
	$modules[] = array(
		'name' => 'wc_entrada',
		'title' => __( 'Entrada', 'woocommerce-exporter' ),
		'description' => __( 'Declares a plugin that will create custom taxonomy to WooCommerce Products.', 'woocommerce-exporter' ),
		'url' => 'https://themeforest.net/item/tour-booking-adventure-tour-wordpress-theme-entrada/16867379',
		'function' => 'entrada_create_product_taxnomy'
	);
	$modules[] = array(
		'name' => 'wc_deliveryslots',
		'title' => __( 'WooCommerce Delivery Slots', 'woocommerce-exporter' ),
		'description' => __( 'Allow your customers to select a delivery slot for their order.', 'woocommerce-exporter' ),
		'url' => 'https://iconicwp.com/products/woocommerce-delivery-slots/',
		'class' => 'jckWooDeliverySlots'
	);
	$modules[] = array(
		'name' => 'wc_products_purchase_price',
		'title' => __( 'Products Purchase Price for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Plug-in for WooCommerce that allows you to insert the cost (or purchase price) of your products!', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/products-purchase-price-for-woocommerce/',
		'slug' => 'products-purchase-price-for-woocommerce',
		'function' => 'product_purchase_price_admin_scripts'
	);
	$modules[] = array(
		'name' => 'wc_product_custom_options',
		'title' => __( 'WooCommerce Product Custom Options Lite', 'woocommerce-exporter' ),
		'description' => __( 'Give your Ecommerce website the space to add customized options for your products.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-custom-options-lite/',
		'slug' => 'woocommerce-custom-options-lite',
		'class' => 'Product_Custom_Options'
	);
	$modules[] = array(
		'name' => 'wc_wholesale_prices',
		'title' => __( 'WooCommerce Wholesale Prices', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Extension to provide Wholesale Prices functionality.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-wholesale-prices/',
		'slug' => 'woocommerce-wholesale-prices',
		'function' => 'wwp_global_plugin_deactivate'
	);
	$modules[] = array(
		'name' => 'wc_show_single_variations',
		'title' => __( 'WooCommerce Show Single Variations', 'woocommerce-exporter' ),
		'description' => __( 'Show product variations in the main product loops.', 'woocommerce-exporter' ),
		'url' => 'https://codecanyon.net/item/woocommerce-show-single-variations/13523915',
		'class' => 'JCK_WSSV'
	);
	$modules[] = array(
		'name' => 'wc_deposits',
		'title' => __( 'WooCommerce Deposits', 'woocommerce-exporter' ),
		'description' => __( 'Adds deposits support to WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://woocommerce.com/products/woocommerce-deposits/',
		'class' => 'WC_Deposits'
	);
	$modules[] = array(
		'name' => 'wc_unitofmeasure',
		'title' => __( 'WooCommerce Unit of Measure', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Unit Of Measure allows the user to add a unit of measure after the price on WooCommerce products.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-unit-of-measure/',
		'slug' => 'woocommerce-unit-of-measure',
		'class' => 'Woo_UOM'
	);
	$modules[] = array(
		'name' => 'wc_easybooking',
		'title' => __( 'WooCommerce Easy Booking', 'woocommerce-exporter' ),
		'description' => __( 'Easily rent or book your products with WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-easy-booking-system/',
		'slug' => 'woocommerce-easy-booking-system',
		'class' => 'Easy_booking'
	);
	$modules[] = array(
		'name' => 'wc_advanced_quantities',
		'title' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
		'description' => __( 'Easily require your customers to buy a minimum/maximum/incremental amount of products to continue with their Checkout.', 'woocommerce-exporter' ),
		'url' => 'http://www.wpbackoffice.com/plugins/woocommerce-incremental-product-quantities/',
		'function' => 'IPQ'
	);
	$modules[] = array(
		'name' => 'wc_chained_products',
		'title' => __( 'WooCommerce Chained Products', 'woocommerce-exporter' ),
		'description' => __( 'Create discounted product bundles and combo packs and boost your sales. Automatically add linked / related products to order.', 'woocommerce-exporter' ),
		'url' => 'https://woocommerce.com/products/chained-products/',
		'class' => 'WC_Chained_Products'
	);
	$modules[] = array(
		'name' => 'wc_sample',
		'title' => __( 'WooCommerce Sample', 'woocommerce-exporter' ),
		'description' => __( 'Include Get Sample Button in products of your online store.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-sample/',
		'slug' => 'woocommerce-sample',
		'class' => 'WooCommerce_Sample'
	);
	$modules[] = array(
		'name' => 'wc_product_importer_deluxe',
		'title' => __( 'WooCommerce Product Importer Deluxe', 'woocommerce-exporter' ),
		'description' => __( 'Bulk import hundreds, even thousands of linked Products and Product images into your WooCommerce store.', 'woocommerce-exporter' ),
		'url' => 'https://www.visser.com.au/plugins/product-importer-deluxe/',
		'function' => 'woo_pd_i18n'
	);
	$modules[] = array(
		'name' => 'wc_ag_barcode_pro',
		'title' => __( 'AG WooCommerce Barcode / ISBN & Amazon ASIN - PRO', 'woocommerce-exporter' ),
		'description' => __( 'A plugin to add a barcode, ISBN & Amazon ASIN fields to WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://www.weareag.co.uk/product/woocommerce-barcodeisbn-amazon-asin-pro/',
		'function' => array( 'AGD_load_updater', 'woo_add_barcode' )
	);
	$modules[] = array(
		'name' => 'wc_nm_personalizedproduct',
		'title' => __( 'N-Media WooCommerce Personalized Product Meta Manager', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Personalized Product Option Plugin allows site admin to add unlimited input fields on product page.', 'woocommerce-exporter' ),
		'url' => 'http://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/',
		'class' => 'NM_PersonalizedProduct'
	);
	$modules[] = array(
		'name' => 'wc_appointments',
		'title' => __( 'WooCommerce Appointments', 'woocommerce-exporter' ),
		'description' => __( 'Setup appointable products for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'http://www.bizzthemes.com/plugins/woocommerce-appointments/',
		'class' => 'WC_Appointments'
	);
	$modules[] = array(
		'name' => 'seo_squirrly',
		'title' => __( 'SEO Squirrly', 'woocommerce-exporter' ),
		'description' => __( 'SEO Squirrly is for the NON-SEO experts. Get Excellent SEO with Better Content, Ranking and Analytics. For Both Humans and Search Bots.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/squirrly-seo/',
		'slug' => 'squirrly-seo',
		'function' => 'sq_phpError'
	);
	$modules[] = array(
		'name' => 'tickera',
		'title' => __( 'Tickera', 'woocommerce-exporter' ),
		'description' => __( 'Simple event ticketing system.', 'woocommerce-exporter' ),
		'url' => 'https://tickera.com/',
		'slug' => 'tickera',
		'class' => 'TC'
	);
	$modules[] = array(
		'name' => 'wc_measurement_price_calc',
		'title' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce plugin to provide price and quantity calculations based on product measurements.', 'woocommerce-exporter' ),
		'url' => 'http://www.woocommerce.com/products/measurement-price-calculator/',
		'function' => 'init_woocommerce_measurement_price_calculator'
	);
	$modules[] = array(
		'name' => 'wc_stripe',
		'title' => __( 'WooCommerce Stripe Payment Gateway', 'woocommerce-exporter' ),
		'description' => __( 'Take credit card payments on your store using Stripe.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-gateway-stripe/',
		'slug' => 'woocommerce-gateway-stripe',
		'class' => 'WC_Stripe'
	);
	$modules[] = array(
		'name' => 'yith_ywpi',
		'title' => __( 'YITH WooCommerce PDF Invoice and Shipping List', 'woocommerce-exporter' ),
		'description' => __( 'Generate PDF invoices for WooCommerce orders. Set manual or automatic invoice generation and shipping list document.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/yith-woocommerce-pdf-invoice/',
		'slug' => 'yith-woocommerce-pdf-invoice',
		'function' => 'yith_ywpi_init'
	);
	$modules[] = array(
		'name' => 'yith_pdf_invoice',
		'title' => __( 'YITH WooCommerce PDF Invoice and Shipping List Premium', 'woocommerce-exporter' ),
		'description' => __( 'Generate PDF invoices, credit notes, pro-forma invoice and packing slip for WooCommerce orders.', 'woocommerce-exporter' ),
		'url' => 'http://yithemes.com/themes/plugins/yith-woocommerce-pdf-invoice/',
		'function' => 'YITH_PDF_Invoice'
	);
	$modules[] = array(
		'name' => 'alg_con',
		'title' => __( 'Custom Order Numbers for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Custom order numbers for WooCommerce.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/custom-order-numbers-for-woocommerce/',
		'slug' => 'custom-order-numbers-for-woocommerce',
		'class' => 'Alg_WC_Custom_Order_Numbers'
	);
	$modules[] = array(
		'name' => 'ign_gift_certs',
		'title' => __( 'WooCommerce Gift Certificates Pro', 'woocommerce-exporter' ),
		'description' => __( 'WooCommerce Gift Certificates Pro allows you to sell gift certificates / store credits / coupon codes as products in your store.', 'woocommerce-exporter' ),
		'url' => 'https://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-gift-certificates-pro/',
		'class' => 'Ignite_Gift_Certs'
	);
	$modules[] = array(
		'name' => 'wc_fields_factory',
		'title' => __( 'WC Fields Factory', 'woocommerce-exporter' ),
		'description' => __( 'Sell your products with customized, personalised options. Add custom fields or fields group to your products, your admin screens and customize everything.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/wc-fields-factory/',
		'slug' => 'wc-fields-factory',
		'class' => 'wcff'
	);
	$modules[] = array(
		'name' => 'wc_upload_files',
		'title' => __( 'WooCommerce Upload Files', 'woocommerce-exporter' ),
		'description' => __( 'WCUF plugin allows your customers to attach files to their orders according to the purchased products.', 'woocommerce-exporter' ),
		'url' => 'https://codecanyon.net/item/woocommerce-upload-files/11442983',
		'function' => 'wcuf_init'
	);
	$modules[] = array(
		'name' => 'wc_ecr_gcr',
		'title' => __( 'Google Customer Reviews for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Integrates Google Merchant Center\'s Google Customer Reviews survey opt-in and badge into your WooCommerce store.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/ecr-google-customer-reviews/',
		'slug' => 'ecr-google-customer-reviews',
		'function' => 'ecr_gcr_missing_wc_notice'
	);
	$modules[] = array(
		'name' => 'wpla_wplister',
		'title' => __( 'WP-Lister Pro for Amazon', 'woocommerce-exporter' ),
		'description' => __( 'List your products on Amazon the easy way.', 'woocommerce-exporter' ),
		'url' => 'https://www.wplab.com/plugins/wp-lister-for-amazon/',
		'class' => 'WPLA_WPLister'
	);
	$modules[] = array(
		'name' => 'wple_wplister',
		'title' => __( 'WP-Lister Pro for eBay', 'woocommerce-exporter' ),
		'description' => __( 'List your products on eBay the easy way.', 'woocommerce-exporter' ),
		'url' => 'https://www.wplab.com/plugins/wp-lister/',
		'class' => 'WPL_WPLister'
	);
	$modules[] = array(
		'name' => 'alidropship',
		'title' => __( 'AliDropship for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'AliDropship is a WordPress Plugin to import AliExpress products into your WooCommerce Shop.', 'woocommerce-exporter' ),
		'url' => 'https://alidropship.com/',
		'function' => 'adsw_check_server'
	);
	$modules[] = array(
		'name' => 'wc_alldiscounts_lite',
		'title' => __( 'WooCommerce All Discounts Lite', 'woocommerce-exporter' ),
		'description' => __( 'Manage your shop discounts like a pro.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woo-advanced-discounts/',
		'slug' => 'woo-advanced-discounts',
		'function' => 'run_wad'
	);
	$modules[] = array(
		'name' => 'atum_inventory',
		'title' => __( 'ATUM Inventory Management for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'ATUM is the most advanced free WooCommerce inventory management tool in the WordPress Plugins repository.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/atum-stock-manager-for-woocommerce/',
		'slug' => 'atum-stock-manager-for-woocommerce',
		'class' => 'Atum\Inc\Helpers'
	);
	$modules[] = array(
		'name' => 'wc_bookings_appointments_pro',
		'title' => __( 'Bookings and Appointments For WooCommerce Premium', 'woocommerce-exporter' ),
		'description' => __( 'Woocommerce Bookings and Appointments Premium converts your time, products or services etc into a bookable resource.', 'woocommerce-exporter' ),
		'url' => 'https://www.pluginhive.com/product/woocommerce-booking-and-appointments/',
		'class' => 'phive_booking_initialze_premium'
	);
	$modules[] = array(
		'name' => 'wc_shipment_tracking',
		'title' => __( 'WooCommerce Shipment Tracking', 'woocommerce-exporter' ),
		'description' => __( 'Add tracking numbers to orders allowing customers to track their orders via a link.', 'woocommerce-exporter' ),
		'url' => 'https://woocommerce.com/products/shipment-tracking/',
		'class' => 'WC_Shipment_Tracking'
	);
	$modules[] = array(
		'name' => 'wc_ups_shipping',
		'title' => __( 'UPS WooCommerce Shipping', 'woocommerce-exporter' ),
		'description' => __( 'Obtain Real time shipping rates, Print shipping labels and Track Shipment via the UPS Shipping API.', 'woocommerce-exporter' ),
		'url' => 'https://www.pluginhive.com/product/woocommerce-ups-shipping-plugin-with-print-label/',
		'class' => 'UPS_WooCommerce_Shipping'
	);
	$modules[] = array(
		'name' => 'wc_ppom',
		'title' => __( 'PPOM for WooCommerce', 'woocommerce-exporter' ),
		'description' => __( 'Adds input fields on product page to personalized your product.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woocommerce-product-addon/',
		'slug' => 'woocommerce-product-addon',
		'function' => 'PPOM'
	);
	$modules[] = array(
		'name' => 'wc_pwb',
		'title' => __( 'Perfect WooCommerce Brands', 'woocommerce-exporter' ),
		'description' => __( 'Perfect WooCommerce Brands allows you to show product brands in your WooCommerce based store.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/perfect-woocommerce-brands/',
		'slug' => 'perfect-woocommerce-brands',
		'class' => '\Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands'
	);
	$modules[] = array(
		'name' => 'wc_piva',
		'title' => __( 'WooCommerce P.IVA e Codice Fiscale per Italia', 'woocommerce-exporter' ),
		'description' => __( 'Il plugin che rende compatibile woocommerce per il mercato italiano.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woo-piva-codice-fiscale-e-fattura-pdf-per-italia/',
		'slug' => 'woo-piva-codice-fiscale-e-fattura-pdf-per-italia',
		'class' => 'WC_Piva_Cf_Invoice_Ita'
	);
	$modules[] = array(
		'name' => 'wc_easy_cf_piva',
		'title' => __( 'WooCommerce Easy Codice Fiscale Partita Iva', 'woocommerce-exporter' ),
		'description' => __( 'Add the Partita IVA e Codice Fiscale fields in WooCommerce for the italian market.', 'woocommerce-exporter' ),
		'url' => 'https://wordpress.org/plugins/woo-easy-codice-fiscale-partita-iva/',
		'slug' => 'woo-easy-codice-fiscale-partita-iva',
		'function' => 'run_mdt_wc_easy_cf_piva'
	);
	$modules[] = array(
		'name' => 'wc_serial_numbers',
		'title' => __( 'WooCommerce Serial Numbers', 'woocommerce-exporter' ),
		'description' => __( 'The best WooCommerce Plugin to sell license keys, redeem cards and other secret numbers!', 'woocommerce-exporter' ),
		'url' => 'http://wordpress.org/extend/plugins/wc-serial-numbers/',
		'slug' => 'wc-serial-numbers',
		'class' => 'WCSerialNumbers'
	);

	// Ship to Multiple Addresses - WC_Ship_Multiple

/*
	$modules[] = array(
		'name' => '',
		'title' => __( '', 'woocommerce-exporter' ),
		'description' => __( '', 'woocommerce-exporter' ),
		'url' => '',
		'slug' => '', // Define this if the Plugin is hosted on the WordPress repo
		'function' => '' // Define this for function detection, if Class rename attribute to class
	);
*/

	$modules = apply_filters( 'woo_ce_modules_addons', $modules );

	// Check if the existing Transient exists
	$modules_all = count( $modules );
	$cached = get_transient( WOO_CE_PREFIX . '_modules_all_count' );
	if( $cached == false ) {
		set_transient( WOO_CE_PREFIX . '_modules_all_count', $modules_all, DAY_IN_SECONDS );
	}

	$modules_active = 0;
	$modules_inactive = 0;

	if( !empty( $modules ) ) {
		$user_capability = 'install_plugins';
		foreach( $modules as $key => $module ) {
			$modules[$key]['status'] = 'inactive';
			// Check if each module is activated
			if( isset( $module['function'] ) ) {
				if( is_array( $module['function'] ) ) {
					$size = count( $module['function'] );
					for( $i = 0; $i < $size; $i++ ) {
						if( function_exists( $module['function'][$i] ) ) {
							$modules[$key]['status'] = 'active';
							$modules_active++;
							break;
						}
					}
				} else {
					if( function_exists( $module['function'] ) ) {
						$modules[$key]['status'] = 'active';
						$modules_active++;
					}
				}
			} else if( isset( $module['class'] ) ) {
				if( is_array( $module['class'] ) ) {
					$size = count( $module['class'] );
					for( $i = 0; $i < $size; $i++ ) {
						if( class_exists( $module['class'][$i] ) ) {
							$modules[$key]['status'] = 'active';
							$modules_active++;
							break;
						}
					}
				} else {
					if( class_exists( $module['class'] ) ) {
						$modules[$key]['status'] = 'active';
						$modules_active++;
					}
				}
			}
			// Filter Modules by Module Status
			if( !empty( $module_status ) ) {
				switch( $module_status ) {

					case 'active':
						if( $modules[$key]['status'] == 'inactive' )
							unset( $modules[$key] );
						break;

					case 'inactive':
						if( $modules[$key]['status'] == 'active' )
							unset( $modules[$key] );
						break;

				}
			}
			// Check that we've got these resources available
			if( isset( $modules[$key] ) && function_exists( 'current_user_can' ) && did_action( 'init' ) ) {
				// Check if the Plugin has a slug and if User can install Plugins
				if( current_user_can( $user_capability ) && isset( $module['slug'] ) )
					$modules[$key]['url'] = admin_url( sprintf( 'plugin-install.php?tab=search&type=term&s=%s', $module['slug'] ) );
			}
		}
	}

	// Check if the existing Transient exists
	$cached = get_transient( WOO_CE_PREFIX . '_modules_active_count' );
	if( $cached == false ) {
		set_transient( WOO_CE_PREFIX . '_modules_active_count', $modules_active, DAY_IN_SECONDS );
	}

	// Check if the existing Transient exists
	$cached = get_transient( WOO_CE_PREFIX . '_modules_inactive_count' );
	if( $cached == false ) {
		$modules_inactive = $modules_all - $modules_active;
		set_transient( WOO_CE_PREFIX . '_modules_inactive_count', $modules_inactive, DAY_IN_SECONDS );
	}

	return $modules;

}

// Returns whether a supported export Plugin is activated
function woo_ce_detect_export_plugin( $plugin_name = '' ) {

	if( empty( $plugin_name ) )
		return;

	// Check if a cached list of active modules is available
	if ( false === ( $active_modules = get_transient( WOO_CE_PREFIX . '_modules_active' ) ) ) {
		$active_modules = woo_ce_refresh_active_export_plugins();
	}

	// Check if the requested Plugin is in the list of active export Plugins
	if( !empty( $active_modules ) ) {
		if( in_array( $plugin_name, $active_modules ) )
			return true;
	}

}

function woo_ce_refresh_active_export_plugins() {

	// Delete the existing count Transients
	delete_transient( WOO_CE_PREFIX . '_modules_all_count' );
	delete_transient( WOO_CE_PREFIX . '_modules_active_count' );
	delete_transient( WOO_CE_PREFIX . '_modules_inactive_count' );

	// Refresh the count Transients
	$modules = woo_ce_modules_list( 'active' );

	// Create a list of active export Plugins
	$active_modules = false;
	if( !empty( $modules ) ) {
		$active_modules = array();
		foreach( $modules as $module )
			$active_modules[] = $module['name'];
	}
	unset( $modules );

	// Save the list of active export Plugins
	set_transient( WOO_CE_PREFIX . '_modules_active', $active_modules, DAY_IN_SECONDS );

	return $active_modules;

}
function woo_ce_error_log( $message = '', $level = false ) {

	if( $message == '' )
		return;

	if( class_exists( 'WC_Logger' ) && apply_filters( 'woo_ce_error_log_use_wc_logger', true ) ) {
		$logger = new WC_Logger();
		if( version_compare( woo_get_woo_version(), '2.7', '>=' ) ) {
			$notice_level = ( !empty( $level ) ? $level : apply_filters( 'woo_ce_error_log_default_level', WC_Log_Levels::NOTICE ) );
			// Format notice levels to match WC_Log_Levels
			switch( $notice_level ) {

				// System is unusable.
				case 'emergency':
					$notice_level = WC_Log_Levels::EMERGENCY;
					break;

				// Action must be taken immediately. Example: Entire website down, database unavailable, etc.
				case 'alert':
					$notice_level = WC_Log_Levels::ALERT;
					break;

				// Critical conditions. Example: Application component unavailable, unexpected exception.
				case 'critical':
					$notice_level = WC_Log_Levels::CRITICAL;
					break;

				// Runtime errors that do not require immediate action but should typically be logged and monitored.
				case 'error':
					$notice_level = WC_Log_Levels::ERROR;
					break;

				// Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
				case 'warning':
					$notice_level = WC_Log_Levels::WARNING;
					break;

				// Normal but significant events.
				case 'notice':
					$notice_level = WC_Log_Levels::NOTICE;
					break;

				// Interesting events.
				case 'info':
					$notice_level = WC_Log_Levels::INFO;
					break;

				// Detailed debug information.
				case 'debug':
					$notice_level = WC_Log_Levels::DEBUG;
					break;

			}
			$logger->log( $notice_level, $message, array( 'source' => WOO_CE_PREFIX ) );
		} else {
			$logger->add( WOO_CE_PREFIX, $message );
		}

		return true;

	} else {
		// Fallback where the WooCommerce logging engine is unavailable
		error_log( sprintf( '[store-exporter] %s', $message ) );
	}

}

function woo_ce_error_get_last_message() {

	$output = '-';
	if( function_exists( 'error_get_last' ) ) {
		$last_error = error_get_last();
		if( isset( $last_error ) && isset( $last_error['message'] ) ) {
			$output = $last_error['message'];
		}
		unset( $last_error );
	}

	return $output;

}

function woo_ce_get_option( $option = null, $default = false, $allow_empty = false ) {

	$output = false;
	if( $option !== null ) {
		$separator = '_';
		$output = get_option( WOO_CE_PREFIX . $separator . $option, $default );
		if( $allow_empty == false && $output != 0 && ( $output == false || $output == '' ) )
			$output = $default;
	}

	return $output;

}

function woo_ce_update_option( $option = null, $value = null ) {

	$output = false;
	if( $option !== null && $value !== null ) {
		$separator = '_';
		$output = update_option( WOO_CE_PREFIX . $separator . $option, $value );
	}

	return $output;

}