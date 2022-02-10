<?php

/**
 * The Settings Page
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use CustomFacebookFeed\CFF_Cache;
use CustomFacebookFeed\CFF_Group_Posts;
use CustomFacebookFeed\CFF_View;
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Resizer;
use CustomFacebookFeed\CFF_Response;
use CustomFacebookFeed\CFF_HTTP_Request;
use CustomFacebookFeed\CFF_GDPR_Integrations;
use CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager;
use CustomFacebookFeed\Builder\CFF_Db;
use CustomFacebookFeed\Builder\CFF_Feed_Builder;
use CustomFacebookFeed\Builder\CFF_Source;
use CustomFacebookFeed\Admin\Traits\CFF_Settings;


class CFF_Global_Settings {
	use CFF_Settings;
	/**
	 * Admin menu page slug.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	const SLUG = 'cff-settings';

	/**
	 * Initializing the class
	 *
	 * @since 4.0
	 */
	function __construct(){
		$this->init();
	}

	/**
	 * Determining if the user is viewing the our page, if so, party on.
	 *
	 * @since 4.0
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		// if cff_cache_cron exists and it's free version then remove cff_cache_cron schedule
		if ( wp_get_schedule( 'cff_cache_cron' ) && !CFF_Utils::cff_is_pro_version() ) {
			wp_clear_scheduled_hook('cff_cache_cron');
		}

		add_action('admin_menu', [$this, 'register_menu']);
		add_filter( 'admin_footer_text', [$this, 'remove_admin_footer_text'] );

		add_action( 'wp_ajax_cff_save_settings', [$this, 'cff_save_settings'] );
		add_action( 'wp_ajax_cff_activate_license', [$this, 'cff_activate_license'] );
		add_action( 'wp_ajax_cff_deactivate_license', [$this, 'cff_deactivate_license'] );
		add_action( 'wp_ajax_cff_activate_extension_license', [$this, 'cff_activate_extension_license'] );
		add_action( 'wp_ajax_cff_deactivate_extension_license', [$this, 'cff_deactivate_extension_license'] );
		add_action( 'wp_ajax_cff_test_connection', [$this, 'cff_test_connection'] );
		add_action( 'wp_ajax_cff_import_settings_json', [$this, 'cff_import_settings_json'] );
		add_action( 'wp_ajax_cff_export_settings_json', [$this, 'cff_export_settings_json'] );
		add_action( 'wp_ajax_cff_clear_cache', [$this, 'cff_clear_cache'] );
		add_action( 'wp_ajax_cff_clear_image_resize_cache', [$this, 'cff_clear_image_resize_cache'] );
		add_action( 'wp_ajax_cff_dpa_reset', [$this, 'cff_dpa_reset'] );

		CFF_Upgrader::hooks();
	}

	/**
	 * CFF Save Settings
	 *
	 * This will save the data fron the settings page
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_save_settings() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$data = $_POST;
		$model = isset( $data[ 'model' ] ) ? $data['model'] : null;

		// return if the model is null
		if ( null === $model ) {
			return;
		}

		// get the cff license key and extensions license key
		$cff_license_key = sanitize_text_field( $_POST['cff_license_key'] );
		$extensions_license_key = json_decode( stripslashes($_POST['extensions_license_key']) );

		// Only update the cff_license_key value when it's inactive
		if ( get_option( 'cff_license_status') == 'inactive' ) {
			if ( empty( $cff_license_key ) || strlen( $cff_license_key ) < 1 ) {
				delete_option( 'cff_license_key' );
			} else {
				update_option( 'cff_license_key', $cff_license_key );
			}
		}

		// Only update the extension license key when it's not activated
		if ( count( $extensions_license_key ) > 0 ) {
			foreach( $extensions_license_key as $extension => $license ) {
				// if license is not valid then allow to update or remove license keys
				if ( ! get_option( 'cff_license_status_' . $extension ) || 'valid' != get_option( 'cff_license_status_' . $extension ) ) {
					// if license status is not valid then either delete or update
					if ( empty( $license ) || strlen( $license ) < 1 ) {
						delete_option( 'cff_license_key_' . $extension );
					} else {
						update_option( 'cff_license_key_' . $extension, $license_key );
					}
				}
			}
		}

		$model = (array) \json_decode( \stripslashes( $model ) );
		$general = (array) $model['general'];
		$feeds = (array) $model['feeds'];
		$translation = (array) $model['translation'];
		$advanced = (array) $model['advanced'];

		// Get the values and sanitize
		$cff_locale 							= sanitize_text_field( $feeds['selectedLocale'] );
		$cff_style_settings 					= get_option( 'cff_style_settings' );
		$cff_style_settings[ 'cff_timezone' ] 	= sanitize_text_field( $feeds['selectedTimezone'] );
		$cff_style_settings[ 'cff_custom_css' ] = $feeds['customCSS'];
		$cff_style_settings[ 'cff_custom_js' ] 	= $feeds['customJS'];
		$cff_style_settings[ 'gdpr' ] 			= sanitize_text_field( $feeds['gdpr'] );
		$cachingType 							= sanitize_text_field( $feeds['cachingType'] );
		$cronInterval 							= sanitize_text_field( $feeds['cronInterval'] );
		$cronTime 								= sanitize_text_field( $feeds['cronTime'] );
		$cronAmPm 								= sanitize_text_field( $feeds['cronAmPm'] );
		$cacheTime 								= sanitize_text_field( $feeds['cacheTime'] );
		$cacheTimeUnit 							= sanitize_text_field( $feeds['cacheTimeUnit'] );

		// Save general settings data
		update_option( 'cff_preserve_settings', $general['preserveSettings'] );

		// Save feeds settings data
		update_option( 'cff_locale', $cff_locale );

		// Caching option for the Pro version
		if ( CFF_Utils::cff_is_pro_version() ) {
			update_option( 'cff_caching_type', $cachingType );
			update_option( 'cff_cache_cron_interval', $cronInterval );
			update_option( 'cff_cache_cron_time', $cronTime );
			update_option( 'cff_cache_cron_am_pm', $cronAmPm );
		}

		// Caching options for the Free version
		if ( ! CFF_Utils::cff_is_pro_version() ) {
			// cff_caching_type should be 'page' for the free version
			update_option( 'cff_caching_type', 'page' );
			update_option( 'cff_cache_time', (int) $cacheTime );
			update_option( 'cff_cache_time_unit', $cacheTimeUnit );
		}

		// Save translation settings data
		foreach( $translation as $key => $val ) {
			$cff_style_settings[ $key ] = $val;
		}

		// Save advanced settings data
		$cff_ajax = sanitize_text_field( $advanced['cff_ajax'] );

		foreach( $advanced as $key => $val ) {
			if ( $key == 'cff_disable_resize' || $key == 'disable_admin_notice' ) {
				$cff_style_settings[ $key ] = !$val;
			} else {
				$cff_style_settings[ $key ] = $val;
			}
		}

		$usage_tracking = get_option( 'cff_usage_tracking', array( 'last_send' => 0, 'enabled' => CFF_Utils::cff_is_pro_version() ) );
		if ( isset( $advanced['email_notification_addresses'] ) ) {
			$usage_tracking['enabled'] = false;
			if ( isset( $advanced['usage_tracking'] ) ) {
				if ( ! is_array( $usage_tracking ) ) {
					$usage_tracking = array(
						'enabled' => true,
						'last_send' => 0,
					);
				} else {
					$usage_tracking['enabled'] = true;
				}
			}
			update_option( 'cff_usage_tracking', $usage_tracking, false );
		}
		update_option( 'cff_ajax', $cff_ajax );

		// Update the cff_style_settings option that contains data for translation and advanced tabs
		update_option( 'cff_style_settings', $cff_style_settings );

		// clear cron caches
		$this->cff_clear_cache();

		new CFF_Response( true, array(
			'cronNextCheck' => $this->get_cron_next_check()
		) );
	}

	/**
	 * CFF Activate License Key
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_activate_license() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		// do the form validation to check if license_key is not empty
		if ( empty( $_POST[ 'license_key' ] ) ) {
			new CFF_Response( false, array(
				'message' => __( 'License key required!', 'custom-facebook-feed' ),
			) );
		}
		$license_key = sanitize_text_field( $_POST[ 'license_key' ] );
		// make the remote api call and get license data
		$cff_license_data = $this->get_license_data( $license_key, 'activate_license', WPW_SL_ITEM_NAME );
		// update the license data
		if( !empty( $cff_license_data ) ) {
			update_option( 'cff_license_data', $cff_license_data );
		}
		// update the licnese key only when the license status is activated
		update_option( 'cff_license_key', $license_key );
		// update the license status
		update_option( 'cff_license_status', $cff_license_data['license'] );

		// Check if there is any error in the license key then handle it
		$cff_license_data = CFF_Global_Settings::get_license_error_message( $cff_license_data );

		// Send ajax response back to client end
		$data = array(
			'licenseStatus' => $cff_license_data['license'],
			'licenseData' => $cff_license_data
		);
		new CFF_Response( true, $data );
	}

	/**
	 * CFF Deactivate License Key
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_deactivate_license() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$license_key = trim( get_option( 'cff_license_key' ) );
		$cff_license_data = $this->get_license_data( $license_key, 'deactivate_license', WPW_SL_ITEM_NAME );
		// update the license data
		if( !empty( $cff_license_data ) ) {
			update_option( 'cff_license_data', $cff_license_data );
		}
		if ( ! $cff_license_data['success'] ) {
			new CFF_Response( false, array() );
		}
		// remove the license keys and update license key status
		if( $cff_license_data['license'] == 'deactivated' ) {
			update_option( 'cff_license_status', 'inactive' );
			$data = array(
				'licenseStatus' => 'inactive'
			);
			new CFF_Response( true, $data );
		}
	}

	/**
	 * CFF Activate Extension License Key
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_activate_extension_license() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		// do the form validation to check if license_key is not empty
		if ( empty( $_POST[ 'license_key' ] ) ) {
			new CFF_Response( false, array(
				'message' => __( 'License key required!', 'custom-facebook-feed' ),
			) );
		}
		$license_key = sanitize_text_field( $_POST[ 'license_key' ] );
		$extension_name = sanitize_text_field( $_POST[ 'extension_name' ] );
		$extension_item_name = sanitize_text_field( $_POST[ 'extension_item_name' ] );

		// make the remote api call and get license data
		$cff_license_data = $this->get_license_data( $license_key, 'activate_license', $extension_item_name );
		// update the licnese key only when the license status is activated
		update_option( 'cff_license_key_' . $extension_name, $license_key );
		// update the license status
		update_option( 'cff_license_status_' . $extension_name, $cff_license_data['license'] );

		// Send ajax response back to client end
		$data = array(
			'licenseStatus' => $cff_license_data['license'],
			'licenseData' => $cff_license_data
		);
		new CFF_Response( true, $data );
	}

	/**
	 * CFF Deactivate Extension License Key
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_deactivate_extension_license() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$extension_name = sanitize_text_field( $_POST[ 'extension_name' ] );
		$extension_item_name = sanitize_text_field( $_POST[ 'extension_item_name' ] );
		$license_key = get_option( 'cff_license_key_' . $extension_name );
		$license_status = get_option( 'cff_license_status_' . $extension_name );

		$cff_license_data = $this->get_license_data( $license_key, 'deactivate_license', $extension_item_name );

		if ( ! $cff_license_data['success'] ) {
			new CFF_Response( false, array() );
		}

		// remove the license keys and update license key status
		if( $cff_license_data['license'] == 'deactivated' ) {
			delete_option( 'cff_license_status_' . $extension_name );
			$data = array(
				'licenseStatus' => $cff_license_data['license']
			);
			new CFF_Response( true, $data );
		}
	}

	/**
	 * CFF Test Connection
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_test_connection() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$license_key = get_option( 'cff_license_key' );
		$cff_api_params = array(
			'edd_action'=> 'check_license',
			'license'   => $license_key,
			'item_name' => urlencode( WPW_SL_ITEM_NAME ) // the name of our product in EDD
		);
		$url = add_query_arg( $cff_api_params, WPW_SL_STORE_URL );
		$args = array(
			'timeout' => 60,
			'sslverify' => false
		);
		// Make the remote API request
		$request = CFF_HTTP_Request::request( 'GET', $url, $args );
		if ( CFF_HTTP_Request::is_error( $request ) ) {
			ray($request);
			new CFF_Response( false, array(
				'hasError' => true
			) );
		}

		new CFF_Response( true, array(
			'hasError' => false
		) );
	}

	/**
	 * CFF Import Feed Settings JSON
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_import_settings_json() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$filename = $_FILES['file']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ( 'json' !== $ext ) {
			new CFF_Response( false, [] );
		}
		$imported_settings = file_get_contents( $_FILES["file"]["tmp_name"] );
		// check if the file is empty
		if ( empty( $imported_settings ) ) {
			new CFF_Response( false, [] );
		}
		$feed_return = CFF_Feed_Saver_Manager::import_feed( $imported_settings );
		// check if there's error while importing
		if ( ! $feed_return['success'] ) {
			new CFF_Response( false, [] );
		}
		// Once new feed has imported lets export all the feeds to update in front end
		$exported_feeds = CFF_Db::feeds_query();
		$feeds = array();
		foreach( $exported_feeds as $feed_id => $feed ) {
			$feeds[] = array(
				'id' => $feed['id'],
				'name' => $feed['feed_name']
			);
		}

		new CFF_Response( true, array(
			'feeds' => $feeds
		) );
	}

	/**
	 * CFF Export Feed Settings JSON
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function cff_export_settings_json() {
		//Security Checks
		if(check_ajax_referer( 'cff_admin_nonce' , 'nonce', false) || check_ajax_referer( 'cff-admin' , 'nonce', false) ){

			$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
			$cap = apply_filters( 'cff_settings_pages_capability', $cap );
			if ( ! current_user_can( $cap ) ) {
				wp_send_json_error(); // This auto-dies.
			}

			if ( ! isset( $_GET['feed_id'] ) ) {
				return;
			}
			$feed_id = filter_var( $_GET['feed_id'], FILTER_SANITIZE_NUMBER_INT );
			$feed = CFF_Feed_Saver_Manager::get_export_json( $feed_id );
			$feed_info = CFF_Db::feeds_query( array('id' => $feed_id) );
			$feed_name = strtolower( $feed_info[0]['feed_name'] );
			$filename = 'cff-feed-' . $feed_name . '.json';
			// create a new empty file in the php memory
			$file  = fopen( 'php://memory', 'w' );
			fwrite( $file, $feed );
			fseek( $file, 0 );
			header( 'Content-type: application/json' );
			header( 'Content-disposition: attachment; filename = "' . $filename . '";' );
			fpassthru( $file );
		}
		exit;
	}

	/**
	 * CFF Clear Cache
	 *
	 * @since 4.0
	 */
	public function cff_clear_cache() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}


		// Get the settings model data
		$model = isset( $_POST[ 'model' ] ) ? $_POST['model'] : null;

		// Caching clear option for the free version
		if ( !CFF_Utils::cff_is_pro_version() ) {
			if ( $model !== null ) {
				$model = (array) \json_decode( \stripslashes( $model ) );
				$feeds = (array) $model['feeds'];
			}

			$cff_cache_time = sanitize_text_field( $feeds['cacheTime'] );
			$cff_cache_time_unit = sanitize_text_field( $feeds['cacheTimeUnit'] );

			update_option( 'cff_cache_time', (int) $cff_cache_time );
			update_option( 'cff_cache_time_unit', $cff_cache_time_unit );

			//Clear the existing cron event
			wp_clear_scheduled_hook( 'cff_cron_job' );

			//Set the event schedule based on what the caching time is set to
			$cff_cron_schedule = 'hourly';
			if ( $cff_cache_time_unit == 'hours' && $cff_cache_time > 5 ) {
				$cff_cron_schedule = 'twicedaily';
			}
			if ( $cff_cache_time_unit == 'days' ) {
				$cff_cron_schedule = 'daily';
			}

			wp_schedule_event(time(), $cff_cron_schedule, 'cff_cron_job');

			$this->clear_stored_caches();

			new CFF_Response( true, array() );
		}

		// Get the updated cron schedule interval and time settings from user input and update the database
		if ( $model !== null ) {
			$model = (array) \json_decode( \stripslashes( $model ) );
			$feeds = (array) $model['feeds'];
			update_option( 'cff_cache_cron_interval', sanitize_text_field( $feeds['cronInterval'] ) );
			update_option( 'cff_cache_cron_time', sanitize_text_field( $feeds['cronTime'] ) );
			update_option( 'cff_cache_cron_am_pm', sanitize_text_field( $feeds['cronAmPm'] ) );
		}

		// Now get the updated cron schedule interval and time values
		$cff_cache_cron_interval_val = get_option( 'cff_cache_cron_interval', '12hours' );
		$cff_cache_cron_time_val = get_option( 'cff_cache_cron_time', '1' );
		$cff_cache_cron_am_pm_val = get_option( 'cff_cache_cron_am_pm', 'am' );

		// Default Timezone
		$defaults = array(
			'cff_timezone' => 'America/Chicago',
			'cff_load_more' => true,
			'cff_num_mobile' => ''
		);
		$style_options = get_option( 'cff_style_settings', $defaults );
		$cff_timezone = $style_options[ 'cff_timezone' ];

		// Clear the stored caches in the database
		$this->clear_stored_caches();

		//Clear the existing cron event
		wp_clear_scheduled_hook('cff_cache_cron');
		switch ($cff_cache_cron_interval_val ) {
			case "30mins":
				$cff_cron_schedule = '30mins';
				break;
			case "1hour":
				$cff_cron_schedule = 'hourly';
				break;
			case "12hours":
				$cff_cron_schedule = 'twicedaily';
				break;
			default:
				$cff_cron_schedule = 'daily';
		}

		// If the 30mins or 1hour are selected then use the current time and set it to start at the next 30mins/hour
		$cff_cache_cron_time_unix = strtotime( $cff_cache_cron_time_val . $cff_cache_cron_am_pm_val . ' ' . $cff_timezone );
		if( $cff_cache_cron_interval_val == '30mins' || $cff_cache_cron_interval_val == '1hour' ) $cff_cache_cron_time_unix = time();

		CFF_Group_Posts::group_reschedule_event($cff_cache_cron_time_unix, $cff_cron_schedule);
		wp_schedule_event($cff_cache_cron_time_unix, $cff_cron_schedule, 'cff_cache_cron');

		new CFF_Response( true, array(
			'cronNextCheck' => $this->get_cron_next_check()
		) );
	}

	/**
	 * Clear the stored caches from the database and from other caching plugins
	 *
	 * @since 4.0
	 */
	public function clear_stored_caches() {
		CFF_Cache::clear_legacy();
		CFF_Cache::clear_all_builder();
		CFF_Cache::clear_page_caches();
	}

	/**
	 * CFF Clear Image Resize Cache
	 *
	 * @since 4.0
	 */
	public function cff_clear_image_resize_cache() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		// Caching option is a Pro only feature
		if ( !CFF_Utils::cff_is_pro_version() ) {
			return;
		}

		CFF_Resizer::delete_resizing_table_and_images();
		\cff_main()->cff_error_reporter->add_action_log( 'Reset resizing tables.' );
		if ( !CFF_Resizer::create_resizing_table_and_uploads_folder() ) {
			return;
		}

		new CFF_Response( true, [] );
	}

	/**
	 * CFF Get License Data from our license API
	 *
	 * @since 4.0
	 *
	 * @param string $license_key
	 * @param string $license_action
	 *
	 * @return void|array $cff_license_data
	 */
	public function get_license_data( $license_key, $license_action = 'check_license', $item_name = WPW_SL_ITEM_NAME ) {
		$cff_api_params = array(
			'edd_action'=> $license_action,
			'license'   => $license_key,
			'item_name' => urlencode( $item_name ) // the name of our product in EDD
		);
		$url = add_query_arg( $cff_api_params, WPW_SL_STORE_URL );
		$args = array(
			'timeout' => 60,
			'sslverify' => false
		);
		// Make the remote API request
		$request = CFF_HTTP_Request::request( 'GET', $url, $args );
		if ( CFF_HTTP_Request::is_error( $request ) ) {
			return;
		}
		$cff_license_data = (array) CFF_HTTP_Request::data( $request );
		return $cff_license_data;
	}

	/**
	 * Get license error message depending on license status
	 *
	 * @since 4.0
	 *
	 * @param array $cff_license_data
	 *
	 * @return array $cff_license_data
	 */
	public static function get_license_error_message( $cff_license_data ) {

		global $cff_download_id;

		$license_key = null;
		if ( get_option('cff_license_key') ) {
			$license_key = get_option('cff_license_key');
		}

		$upgrade_url 	= sprintf('https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-license', $license_key);
		$renew_url 		= sprintf('https://smashballoon.com/checkout/?license_key=%s&download_id=%s&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-license&utm_content=renew-license', $license_key, $cff_download_id);
		$learn_more_url = 'https://smashballoon.com/doc/my-license-key-wont-activate/?utm_campaign=facebook-free&utm_source=settings&utm_medium=license&utm_content=learn-more';

		// Check if the license key reached max site installations
		if ( isset( $cff_license_data['error'] ) && 'no_activations_left' === $cff_license_data['error'] )  {
			$cff_license_data['errorMsg'] = sprintf(
				'%s (%s/%s). %s <a href="%s" target="_blank">%s</a> %s <a href="%s" target="_blank">%s</a>',
				__( 'You have reached the maximum number of sites available in your plan', 'custom-facebook-feed' ),
				$cff_license_data['site_count'],
				$cff_license_data['max_sites'],
				__( 'Learn more about it', 'custom-facebook-feed' ),
				$learn_more_url,
				'here',
				__( 'or upgrade your plan.', 'custom-facebook-feed' ),
				$upgrade_url,
				__( 'Upgrade', 'custom-facebook-feed' )
			);
		} elseif ( // Check if the license key has expired
			( isset( $cff_license_data['license'] ) && 'expired' === $cff_license_data['license'] ) ||
			( isset( $cff_license_data['error'] ) && 'expired' === $cff_license_data['error'] )
		)  {
			$cff_license_data['error'] = true;
			$expired_date = new \DateTime( $cff_license_data['expires'] );
			$expired_date = $expired_date->format('F d, Y');
			$cff_license_data['errorMsg'] = sprintf(
				'%s %s. %s <a href="%s" target="_blank">%s</a>',
				__( 'The license expired on ', 'custom-facebook-feed' ),
				$expired_date,
				__( 'Please renew it and try again.', 'custom-facebook-feed' ),
				$renew_url,
				__( 'Renew', 'custom-facebook-feed' )
			);
		} elseif ( // check if invalid license
			( isset( $cff_license_data['success'] ) && false === $cff_license_data['success'] ) ||
			( isset( $cff_license_data['license'] ) && 'invalid' === $cff_license_data['license'] )
		)  {
			$cff_license_data['errorMsg'] = __( 'This license is invalid.', 'custom-facebook-feed' );
		}

		return $cff_license_data;
	}

	/**
	 * Remove admin footer message
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function remove_admin_footer_text() {
		return;
	}

	/**
	 * Register Menu.
	 *
	 * @since 4.0
	 */
	function register_menu() {
		// remove admin page update footer
		add_filter( 'update_footer', [$this, 'remove_admin_footer_text'] );

        $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
        $cap = apply_filters( 'cff_settings_pages_capability', $cap );

		$notice = '';
		if ( \cff_main()->cff_error_reporter->are_critical_errors() ) {
			$notice = ' <span class="update-plugins cff-error-alert"><span>!</span></span>';
		}

       $global_settings = add_submenu_page(
           'cff-top',
           __( 'Settings', 'custom-facebook-feed' ),
		   __( 'Settings ' . $notice , 'custom-facebook-feed' ),
           $cap,
           'cff-settings',
           [$this, 'global_settings'],
           1
       );
       add_action( 'load-' . $global_settings, [$this,'builder_enqueue_admin_scripts']);
   }

	/**
	 * Enqueue Builder CSS & Script.
	 *
	 * Loads only for builder pages
	 *
	 * @since 4.0
	 */
    public function builder_enqueue_admin_scripts(){
        if( ! get_current_screen() ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! 'facebook-feed_page_cff-settings' === $screen->id ) {
			return;
		}
		$cff_status  = 'inactive';
		$model = $this->get_settings_data();
		$exported_feeds = CFF_Db::feeds_query();
		$feeds = array();
		foreach( $exported_feeds as $feed_id => $feed ) {
			$feeds[] = array(
				'id' => $feed['id'],
				'name' => $feed['feed_name']
			);
		}
		$licenseErrorMsg = null;
		$license_key = trim( get_option( 'cff_license_key' ) );
		if ( $license_key ) {
			$license_last_check = get_option( 'cff_license_last_check_timestamp' );
			$date = time() - (DAY_IN_SECONDS * 90);
			if ( $date > $license_last_check ) {
				// make the remote api call and get license data
				$cff_license_data = $this->get_license_data( $license_key );
				if( !empty($cff_license_data) ) update_option( 'cff_license_data', $cff_license_data );
				update_option( 'cff_license_last_check_timestamp', time() );
			} else {
				$cff_license_data = get_option( 'cff_license_data' );
			}
			// update the license data with proper error messages when necessary
			$cff_license_data = CFF_Global_Settings::get_license_error_message( $cff_license_data );
			$cff_status = $cff_license_data['license'];
			$licenseErrorMsg = ( isset( $cff_license_data['error'] ) && isset( $cff_license_data['errorMsg'] ) ) ? $cff_license_data['errorMsg'] : null;
		}

		wp_enqueue_style(
			'settings-style',
			CFF_PLUGIN_URL . 'admin/assets/css/settings.css',
			false,
			CFFVER
		);

		CFF_Feed_Builder::global_enqueue_ressources_scripts(true);


		wp_enqueue_script(
			'settings-app',
			CFF_PLUGIN_URL.'admin/assets/js/settings.js',
			null,
			CFFVER,
			true
		);

		$license_key = null;
		if ( get_option('cff_license_key') ) {
			$license_key = get_option('cff_license_key');
		}

		$has_license_error = false;
		if (
			( isset( $cff_license_data['license'] ) && 'expired' === $cff_license_data['license'] ) ||
			( isset( $cff_license_data['error'] ) && ( $cff_license_data['error'] || 'expired' == $cff_license_data['error'] ) )
		)  {
			$has_license_error = true;
		}

		$upgrade_url			= 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=settings';
		$footer_upgrade_url		= 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=settings&utm_medium=footer-banner&utm_content=Try Demo';
		$usage_tracking_url 	= 'https://smashballoon.com/custom-facebook-feed/docs/usage-tracking/';
		$feed_issue_email_url 	= 'https://smashballoon.com/email-report-is-not-in-my-inbox/';

		$sources_list = CFF_Feed_Builder::get_source_list();

		// Extract only license keys and build array for extensions license keys
		$extensions_license_key = array();
		foreach( $this->get_extensions_license() as $item ) {
			if ( $item['licenseKey'] != false ) {
				$extensions_license_key[ $item['name'] ] = $item['licenseKey'];
			}
		}

		$cff_settings = array(
			'admin_url' 		=> admin_url(),
			'ajax_handler'		=> admin_url( 'admin-ajax.php' ),
            'nonce'             => wp_create_nonce( 'cff_admin_nonce' ),
            'supportPageUrl'    => admin_url( 'admin.php?page=cff-support' ),
			'builderUrl'		=> admin_url( 'admin.php?page=cff-feed-builder' ),
			'links'				=> $this->get_links_with_utm(),
			'licenseType'		=> CFF_Utils::cff_is_pro_version() ? 'pro' : 'free',
			'licenseKey'		=> $license_key,
			'licenseStatus'		=> $cff_status,
			'licenseErrorMsg'	=> $licenseErrorMsg,
			'extensionsLicense' => $this->get_extensions_license(),
			'extensionsLicenseKey' => $extensions_license_key,
			'hasError'			=> $has_license_error,
			'upgradeUrl'		=> $upgrade_url,
			'footerUpgradeUrl'	=> $footer_upgrade_url,
			'isDevSite'			=> CFF_Upgrader::is_dev_url( home_url() ),
			'model'				=> $model,
			'feeds'				=> $feeds,
			'sources'			=> $sources_list,
			'locales'			=> CFF_Settings::locales(),
			'timezones'			=> CFF_Settings::timezones(),
			'socialWallLinks'   => CFF_Feed_Builder::get_social_wall_links(),
			'socialWallActivated' => is_plugin_active( 'social-wall/social-wall.php' ),
			'genericText'       => CFF_Feed_Builder::get_generic_text(),
			'generalTab'		=> array(
				'licenseBox'	=> array(
					'title' => __( 'License Key', 'custom-facebook-feed' ),
					'description' => __( 'Your license key provides access to updates and support', 'custom-facebook-feed' ),
					'activeText' => __( 'Your <b>Custom Facebook Feed Pro</b> license is Active!', 'custom-facebook-feed' ),
					'inactiveText' => __( 'Your <b>Custom Facebook Feed Pro</b> license is Inactive!', 'custom-facebook-feed' ),
					'freeText'	=> __( 'Already purchased? Simply enter your license key below to activate Custom Facebook Feed Pro.', 'custom-facebook-feed'),
					'inactiveFieldPlaceholder' => __( 'Paste license key here', 'custom-facebook-feed' ),
					'upgradeText1' => __( 'You are using the Lite version of the plugin–no license needed. Enjoy! 🙂 To unlock more features, consider <a href="'. $upgrade_url .'&utm_medium=license-key&utm_content=Upgrade to Pro link" target="_blank">Upgrading to Pro</a>.', 'custom-facebook-feed' ),
					'upgradeText2' => __( 'As a valued user of our Lite plugin, you receive 50% OFF - automatically applied at checkout!', 'custom-facebook-feed' ),
					'manageLicense' => __( 'Manage License', 'custom-facebook-feed' ),
					'test' => __( 'Test Connection', 'custom-facebook-feed' ),
					'connectionSuccessful' => __( 'Connection successful', 'custom-facebook-feed' ),
					'connectionFailed' => __( 'Connection failed', 'custom-facebook-feed' ),
					'viewError' => __( 'View error', 'custom-facebook-feed' ),
					'upgrade' => __( 'Upgrade', 'custom-facebook-feed' ),
					'deactivate' => __( 'Deactivate', 'custom-facebook-feed' ),
					'activate' => __( 'Activate', 'custom-facebook-feed' ),
					'installPro' => __( 'Install Pro', 'custom-facebook-feed' ),
				),
				'manageSource'	=> array(
					'title'	=> __( 'Manage Sources', 'custom-facebook-feed' ),
					'description'	=> __( 'Add or remove connected Facebook accounts', 'custom-facebook-feed' ),
				),
				'preserveBox'	=> array(
					'title'	=> __( 'Preserve settings if plugin is removed', 'custom-facebook-feed' ),
					'description'	=> __( 'This will make sure that all of your feeds and settings are still saved even if the plugin is uninstalled', 'custom-facebook-feed' ),
				),
				'importBox'		=> array(
					'title'	=> __( 'Import Feed Settings', 'custom-facebook-feed' ),
					'description'	=> __( 'You will need a JSON file previously exported from the Custom Facebook Feed Plugin', 'custom-facebook-feed' ),
					'button'	=> __( 'Import', 'custom-facebook-feed' ),
				),
				'exportBox'		=> array(
					'title'	=> __( 'Export Feed Settings', 'custom-facebook-feed' ),
					'description'	=> __( 'Export settings for one or more of your feeds', 'custom-facebook-feed' ),
					'button'	=> __( 'Export', 'custom-facebook-feed' ),
				)
			),
			'feedsTab'			=> array(
				'localizationBox' => array(
					'title'	=> __( 'Localization', 'custom-facebook-feed' ),
					'tooltip' => '<p>This controls the language of any predefined text strings provided by Facebook. For example, the descriptive text that accompanies some timeline posts (eg: Smash Balloon created an event) and the text in the \'Like Box\' widget. To find out how to translate the other text in the plugin see <a href="https://smashballoon.com/cff-how-does-the-plugin-handle-text-and-language-translation/">this FAQ</a>.</p>'
				),
				'timezoneBox' => array(
					'title'	=> __( 'Timezone', 'custom-facebook-feed' )
				),
				'cachingBox' => array(
					'title'	=> __( 'Caching', 'custom-facebook-feed' ),
					'pageLoads'	=> __( 'When the Page loads', 'custom-facebook-feed' ),
					'inTheBackground' => __( 'In the Background', 'custom-facebook-feed' ),
					'inTheBackgroundOptions' => array(
						'30mins'	=> __( 'Every 30 minutes', 'custom-facebook-feed' ),
						'1hour'	=> __( 'Every hour', 'custom-facebook-feed' ),
						'12hours'	=> __( 'Every 12 hours', 'custom-facebook-feed' ),
						'24hours'	=> __( 'Every 24 hours', 'custom-facebook-feed' ),
					),
					'timeUnits' => array(
						'minutes'	=> __( 'Minutes', 'custom-facebook-feed' ),
						'hours'		=> __( 'Hours', 'custom-facebook-feed' ),
						'days'	=> __( 'Days', 'custom-facebook-feed' ),
					),
					'am'		=> __( 'AM', 'custom-facebook-feed' ),
					'pm'		=> __( 'PM', 'custom-facebook-feed' ),
					'clearCache' => __( 'Clear All Caches', 'custom-facebook-feed' ),
					'promoText' => __( 'Update feeds asynchronously in the background with Facebook Feed Pro', 'custom-facebook-feed' )
				),
				'gdprBox' => array(
					'title'	=> __( 'GDPR', 'custom-facebook-feed' ),
					'automatic'	=> __( 'Automatic', 'custom-facebook-feed' ),
					'yes'	=> __( 'Yes', 'custom-facebook-feed' ),
					'no'	=> __( 'No', 'custom-facebook-feed' ),
					'infoAuto'	=> $this->get_gdpr_auto_info(),
					'infoYes'	=> __( 'No requests will be made to third-party websites. To accomodate this, some features of the plugin will be limited.', 'custom-facebook-feed' ),
					'infoNo'	=> __( 'The plugin will function as normal and load images and videos directly from Facebook', 'custom-facebook-feed' ),
					'someFacebook' => __( 'Some Facebook Feed features will be limited for visitors to ensure GDPR compliance, until they give consent.', 'custom-facebook-feed'),
					'whatLimited' => __( 'What will be limited?', 'custom-facebook-feed'),
					'tooltip' => '<p><b>If set to “Yes”,</b> it prevents all images and videos from being loaded directly from Facebook’s servers (CDN) to prevent any requests to external websites in your browser. To accommodate this, some features of your plugin will be disabled or limited. </p>
                    <p><b>If set to “No”,</b> the plugin will still make some requests to load and display images and videos directly from Facebook.</p>
                    <p><b>If set to “Automatic”,</b> it will only load images and videos directly from Facebook if consent has been given by one of these integrated GDPR cookie Plugins.</p>
                    <p><a href="#">Learn More</a></p>',
					'gdprTooltipFeatureInfo' => array(
						'headline' => __( 'Features that would be disabled or limited include: ', 'custom-facebook-feed'),
						'features' => array(
							__( 'Only local images (not directly from Facebook) will be displayed in the feed', 'custom-facebook-feed'),
							__( 'Placeholder blank images will be displayed until images are available', 'custom-facebook-feed'),
							__( 'The images in the Visual header will not be displayed', 'custom-facebook-feed'),
							__( 'To play videos visitors will click a link to view the video in Facebook.', 'custom-facebook-feed'),
							__( 'The “Load more” button will be disabled', 'custom-facebook-feed'),
							__( 'The “Like Box” widget will be disabled', 'custom-facebook-feed'),
							__( 'For album feeds, only the album cover image is available in lightbox', 'custom-facebook-feed'),
							__( 'The maximum image resoltuion will be 700px wide in the lightbox. If your images are smaller, reset the “resized images” using the button in “Advanced” section.', 'custom-facebook-feed'),
						)
					)
				),
				'customCSSBox' => array(
					'title'	=> __( 'Custom CSS', 'custom-facebook-feed' ),
					'placeholder' => __( 'Enter any custom CSS here', 'custom-facebook-feed' ),
					'message' => sprintf( __( 'The Custom CSS field has been deprecated. Your CSS has been moved into the native WordPress Custom CSS field instead. This is located %shere%s at <i>Appearance > Customize > Additional CSS.</i>', '' ), '<a href="' . esc_url( wp_customize_url() ) . '" target="_blank" rel="noopener noreferrer">', '</a>' )
				),
				'customJSBox' => array(
					'title'	=> __( 'Custom JS', 'custom-facebook-feed' ),
					'placeholder' => __( 'Enter any custom JS here', 'custom-facebook-feed' ),
					'message' => sprintf( __( 'The Custom JS field has been deprecated. Your JavaScript is displayed below. To continue using this JavaScript, please first review the code below and follow the directions in %sthis doc%s.', '' ), '<a href="https://smashballoon.com/doc/moving-custom-javascript-code-out-of-our-plugins/?utm_campaign=facebook&utm_source=settings&utm_medium=move-js" target="_blank" rel="noopener noreferrer">', '</a>' )
				)
			),
			'translationTab' => array(
				'title'	=> __( 'Custom Text/Translate', 'custom-facebook-feed' ),
				'description'	=> __( 'Enter custom text for the words below, or translate it into the language you would like to use.', 'custom-facebook-feed' ),
				'table'	=> array(
					'originalText' => __( 'Original Text', 'custom-facebook-feed' ),
					'customText' => __( 'Custom text/translation', 'custom-facebook-feed' ),
					'context' => __( 'Context', 'custom-facebook-feed' ),
					'postText' => __( 'Post Text', 'custom-facebook-feed' ),
					'seeMore' => __( 'See More', 'custom-facebook-feed' ),
					'seeLess' => __( 'See Less', 'custom-facebook-feed' ),
					'usedWhen' => __( 'Used when truncating the post text', 'custom-facebook-feed' ),
					'events' => __( 'Events', 'custom-facebook-feed' ),
					'map' => __( 'Map', 'custom-facebook-feed' ),
					'addedAfter' => __( 'Added after the address of an event', 'custom-facebook-feed' ),
					'noUpcoming' => __( 'No upcoming events', 'custom-facebook-feed' ),
					'shownWhen' => __( 'Shown when there are no upcoming events to display', 'custom-facebook-feed' ),
					'interested' => __( 'interested', 'custom-facebook-feed' ),
					'usedFor' => __( 'Used for the number of people interested in an event', 'custom-facebook-feed' ),
					'going' => __( 'going', 'custom-facebook-feed' ),
					'usedFor2' => __( 'Used for the number of people going to an event', 'custom-facebook-feed' ),
					'buyTickets' => __( 'Buy tickets', 'custom-facebook-feed' ),
					'shownWhen2' => __( 'Shown when there is a link to buy event tickets', 'custom-facebook-feed' ),
					'postAction' => __( 'Post Action Links', 'custom-facebook-feed' ),
					'viewOnFB' => __( 'View on Facebook', 'custom-facebook-feed' ),
					'usedFor3' => __( 'Used for the link to the post on Facebook', 'custom-facebook-feed' ),
					'share' => __( 'Share', 'custom-facebook-feed' ),
					'usedFor4' => __( 'Used for sharing the Facebook post via Social Media', 'custom-facebook-feed' ),
					'loadMoreBtn' => __( '“Load More” Button', 'custom-facebook-feed' ),
					'loadMore' => __( 'Load More', 'custom-facebook-feed' ),
					'media' => __( 'Media', 'custom-facebook-feed' ),
					'photo' => __( 'Photo', 'custom-facebook-feed' ),
					'video' => __( 'Video', 'custom-facebook-feed' ),
					'usedTo1' => __( 'Used to link photos on Facebook', 'custom-facebook-feed' ),
					'usedTo2' => __( 'Used to link videos on Facebook', 'custom-facebook-feed' ),
					'usedIn' => __( 'Used in the button that loads more posts', 'custom-facebook-feed' ),
					'noMorePosts' => __( 'No more posts', 'custom-facebook-feed' ),
					'usedWhen2' => __( 'Used when there are no more posts to load', 'custom-facebook-feed' ),
					'likeShareComment' => __( 'Likes, Shares and Comments', 'custom-facebook-feed' ),
					'viewMore' => __( 'View more comments', 'custom-facebook-feed' ),
					'usedIn2' => __( 'Used in the comments section (when applicable)', 'custom-facebook-feed' ),
					'commentOnFB' => __( 'Comment on Facebook', 'custom-facebook-feed' ),
					'usedAt' => __( 'Used at the bottom of the comments section', 'custom-facebook-feed' ),
					'photos' => __( 'photos', 'custom-facebook-feed' ),
					'addedTo' => __( 'Added to the end of an album name. Eg. (6 photos)', 'custom-facebook-feed' ),
					'likeThis' => __( 'like this', 'custom-facebook-feed' ),
					'likesThis' => __( 'likes this', 'custom-facebook-feed' ),
					'egLikeThis' => __( 'Eg. __ and __ like this', 'custom-facebook-feed' ),
					'egLikesThis' => __( 'Eg. __ likes this', 'custom-facebook-feed' ),
					'reactedToThis' => __( 'reacted to this', 'custom-facebook-feed' ),
					'egReactedToThis' => __( 'Eg. __ reacted to this', 'custom-facebook-feed' ),
					'and' => __( 'and', 'custom-facebook-feed' ),
					'other' => __( 'other', 'custom-facebook-feed' ),
					'eg1otherLike' => __( 'Eg. __, __ and 1 other like this', 'custom-facebook-feed' ),
					'others' => __( 'others', 'custom-facebook-feed' ),
					'eg10othersLike' => __( 'Eg. __, __ and 10 others like this', 'custom-facebook-feed' ),
					'reply' => __( 'reply', 'custom-facebook-feed' ),
					'eg1reply' => __( 'Eg. 1 reply', 'custom-facebook-feed' ),
					'replies' => __( 'replies', 'custom-facebook-feed' ),
					'eg5replies' => __( 'Eg. 5 replies', 'custom-facebook-feed' ),
					'callToBTN' => __( 'Call to Action Buttons', 'custom-facebook-feed' ),
					'learnMore' => __( 'Learn More', 'custom-facebook-feed' ),
					'usedFor5' => __( "Used for the 'Learn More' button", 'custom-facebook-feed' ),
					'shopNow' => __( 'Shop Now', 'custom-facebook-feed' ),
					'usedFor6' => __( "Used for the 'Shop Now' button", 'custom-facebook-feed' ),
					'usedFor7' => __( "Used for the 'Message Page' button", 'custom-facebook-feed' ),
					'usedFor8' => __( "Used for the 'Get Directions' button", 'custom-facebook-feed' ),
					'messagePage' => __( 'Message Page', 'custom-facebook-feed' ),
					'getDirections' => __( 'Get Directions', 'custom-facebook-feed' ),
					'date' => __( 'Date', 'custom-facebook-feed' ),
					'second' => __( 'second', 'custom-facebook-feed' ),
					'seconds' => __( 'second', 'custom-facebook-feed' ),
					'usedFor9' => __( 'Used for “Posted a second ago”', 'custom-facebook-feed' ),
					'usedFor10' => __( 'Used for “Posted __ seconds ago”', 'custom-facebook-feed' ),
					'usedFor11' => __( 'Used for “Posted a minute ago”', 'custom-facebook-feed' ),
					'usedFor12' => __( 'Used for “Posted __ minutes ago”', 'custom-facebook-feed' ),
					'usedFor13' => __( 'Used for “Posted a hour ago”', 'custom-facebook-feed' ),
					'usedFor14' => __( 'Used for “Posted __ hours ago”', 'custom-facebook-feed' ),
					'usedFor15' => __( 'Used for “Posted a day ago”', 'custom-facebook-feed' ),
					'usedFor16' => __( 'Used for “Posted __ days ago”', 'custom-facebook-feed' ),
					'usedFor17' => __( 'Used for “Posted a week ago”', 'custom-facebook-feed' ),
					'usedFor18' => __( 'Used for “Posted __ weeks ago”', 'custom-facebook-feed' ),
					'usedFor19' => __( 'Used for “Posted a month ago”', 'custom-facebook-feed' ),
					'usedFor20' => __( 'Used for “Posted __ months ago”', 'custom-facebook-feed' ),
					'usedFor21' => __( 'Used for “Posted a year ago”', 'custom-facebook-feed' ),
					'usedFor22' => __( 'Used for “Posted __ years ago”', 'custom-facebook-feed' ),
					'usedFor23' => __( 'Used for “Posted __ seconds ago”', 'custom-facebook-feed' ),
					'minute' => __( 'minute', 'custom-facebook-feed' ),
					'minutes' => __( 'minutes', 'custom-facebook-feed' ),
					'hour' => __( 'hour', 'custom-facebook-feed' ),
					'hours' => __( 'hours', 'custom-facebook-feed' ),
					'day' => __( 'day', 'custom-facebook-feed' ),
					'days' => __( 'days', 'custom-facebook-feed' ),
					'week' => __( 'week', 'custom-facebook-feed' ),
					'weeks' => __( 'weeks', 'custom-facebook-feed' ),
					'month' => __( 'month', 'custom-facebook-feed' ),
					'months' => __( 'months', 'custom-facebook-feed' ),
					'year' => __( 'year', 'custom-facebook-feed' ),
					'years' => __( 'years', 'custom-facebook-feed' ),
					'ago' => __( 'ago', 'custom-facebook-feed' ),
				)
			),
			'advancedTab'	=> array(
				'optimizeBox' => array(
					'title' => __( 'Optimize Images', 'custom-facebook-feed' ),
					'helpText' => __( 'This will create multiple local copies of images in different sizes. The plugin then displays the smallest version based on the size of the feed.', 'custom-facebook-feed' ),
					'reset' => __( 'Reset', 'custom-facebook-feed' ),
					'promoText' => __( 'Optimize images with Facebook Feed Pro', 'custom-facebook-feed' ),
				),
				'usageBox' => array(
					'title' => __( 'Usage Tracking', 'custom-facebook-feed' ),
					'helpText' => __( 'This helps to prevent plugin and theme conflicts by sending a report in the background once per week about your settings and relevant site stats. It does not send sensitive information like access tokens, email addresses, or user info. This will also not affect your site performace. <a href="'. $usage_tracking_url .'" target="_blank">Learn More</a>', 'custom-facebook-feed' ),
				),
				'ajaxBox' => array(
					'title' => __( 'AJAX theme loading fix', 'custom-facebook-feed' ),
					'helpText' => __( 'Fixes issues caused by Ajax loading themes. It can also be used to workaround JavaScript errors on the page.', 'custom-facebook-feed' ),
				),
				'showCreditBox' => array(
					'title' => __( 'Show Credit Link', 'custom-facebook-feed' ),
					'helpText' => __( 'Display a link at the bottom of the feed to the Smash Balloon website. Thank you! :)', 'custom-facebook-feed' ),
				),
				'fixTextBox' => array(
					'title' => __( 'Fix a text shortening issue caused by some themes', 'custom-facebook-feed' ),
				),
				'adminErrorBox' => array(
					'title' => __( 'Admin Error Notice', 'custom-facebook-feed' ),
					'helpText' => __( 'This will disable or enable the feed error notice that displays in the bottom right corner of your site for logged-in admins.', 'custom-facebook-feed' ),
				),
				'feedIssueBox' => array(
					'title' => __( 'Feed Issue Email Reports', 'custom-facebook-feed' ),
					'helpText' => __( 'If the feed is down due to a critical issue, we will switch to a cached version and notify you based on these settings. <a href="'. $feed_issue_email_url .'" target="_blank">View Documentation</a>', 'custom-facebook-feed' ),
					'sendReport' => __( 'Send a report every', 'custom-facebook-feed' ),
					'to' => __( 'to', 'custom-facebook-feed' ),
					'placeholder' => __( 'Enter one or more email address separated by comma', 'custom-facebook-feed' ),
					'weekDays' => array(
						array(
							'val' => 'monday',
							'label' => __( 'Monday', 'custom-facebook-feed' )
						),
						array(
							'val' => 'tuesday',
							'label' => __( 'Tuesday', 'custom-facebook-feed' )
						),
						array(
							'val' => 'wednesday',
							'label' => __( 'Wednesday', 'custom-facebook-feed' )
						),
						array(
							'val' => 'thursday',
							'label' => __( 'Thursday', 'custom-facebook-feed' )
						),
						array(
							'val' => 'friday',
							'label' => __( 'Friday', 'custom-facebook-feed' )
						),
						array(
							'val' => 'saturday',
							'label' => __( 'Saturday', 'custom-facebook-feed' )
						),
						array(
							'val' => 'sunday',
							'label' => __( 'Sunday', 'custom-facebook-feed' )
						),
					)
				),
				'dpaClear' => array(
					'title' => __( 'Manage Data', 'custom-facebook-feed' ),
					'helpText' => __( 'Warning: Clicking this button will permanently delete all Facebook data, including all connected accounts, cached posts, and stored images.', 'custom-facebook-feed' ),
					'clear' => __( 'Delete all Platform Data', 'custom-facebook-feed' ),
				),
			),
			'dialogBoxPopupScreen'  => array(
				'deleteSource' => array(
					'heading' =>  __( 'Delete "#"?', 'custom-facebook-feed' ),
					'description' => __( 'This source is being used in a feed on your site. If you delete this source then new posts can no longer be retrieved for these feeds.', 'custom-facebook-feed' ),
				),
			),

			'selectSourceScreen' => CFF_Feed_Builder::select_source_screen_text(),

			'nextCheck'	=> $this->get_cron_next_check(),
			'loaderSVG' => '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>',
			'checkmarkSVG' => '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>',
			'uploadSVG' => '<svg class="btn-icon" width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M0.166748 14.6667H11.8334V13H0.166748V14.6667ZM0.166748 6.33333H3.50008V11.3333H8.50008V6.33333H11.8334L6.00008 0.5L0.166748 6.33333Z" fill="#141B38"/></svg>',
			'exportSVG' => '<svg class="btn-icon" width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M0.166748 14.6667H11.8334V13H0.166748V14.6667ZM11.8334 5.5H8.50008V0.5H3.50008V5.5H0.166748L6.00008 11.3333L11.8334 5.5Z" fill="#141B38"/></svg>',
			'reloadSVG' => '<svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M15.8335 3.66667L12.5002 7H15.0002C15.0002 8.32608 14.4734 9.59785 13.5357 10.5355C12.598 11.4732 11.3262 12 10.0002 12C9.16683 12 8.3585 11.7917 7.66683 11.4167L6.45016 12.6333C7.51107 13.3085 8.74261 13.667 10.0002 13.6667C11.7683 13.6667 13.464 12.9643 14.7142 11.714C15.9644 10.4638 16.6668 8.76811 16.6668 7H19.1668L15.8335 3.66667ZM5.00016 7C5.00016 5.67392 5.52695 4.40215 6.46463 3.46447C7.40231 2.52678 8.67408 2 10.0002 2C10.8335 2 11.6418 2.20833 12.3335 2.58333L13.5502 1.36667C12.4893 0.691461 11.2577 0.332984 10.0002 0.333334C8.23205 0.333334 6.53636 1.03571 5.28612 2.28596C4.03587 3.5362 3.3335 5.23189 3.3335 7H0.833496L4.16683 10.3333L7.50016 7" fill="#141B38"/></svg>',
			'tooltipHelpSvg' => '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.1665 8H10.8332V6.33333H9.1665V8ZM9.99984 17.1667C6.32484 17.1667 3.33317 14.175 3.33317 10.5C3.33317 6.825 6.32484 3.83333 9.99984 3.83333C13.6748 3.83333 16.6665 6.825 16.6665 10.5C16.6665 14.175 13.6748 17.1667 9.99984 17.1667ZM9.99984 2.16666C8.90549 2.16666 7.82186 2.38221 6.81081 2.801C5.79976 3.21979 4.8811 3.83362 4.10728 4.60744C2.54448 6.17024 1.6665 8.28986 1.6665 10.5C1.6665 12.7101 2.54448 14.8298 4.10728 16.3926C4.8811 17.1664 5.79976 17.7802 6.81081 18.199C7.82186 18.6178 8.90549 18.8333 9.99984 18.8333C12.21 18.8333 14.3296 17.9554 15.8924 16.3926C17.4552 14.8298 18.3332 12.7101 18.3332 10.5C18.3332 9.40565 18.1176 8.32202 17.6988 7.31097C17.28 6.29992 16.6662 5.38126 15.8924 4.60744C15.1186 3.83362 14.1999 3.21979 13.1889 2.801C12.1778 2.38221 11.0942 2.16666 9.99984 2.16666ZM9.1665 14.6667H10.8332V9.66666H9.1665V14.6667Z" fill="#434960"/></svg>',
			'svgIcons' => CFF_Feed_Builder::builder_svg_icons()
		);

		$newly_retrieved_source_connection_data = CFF_Source::maybe_source_connection_data();
		if ( $newly_retrieved_source_connection_data ) {
			$cff_settings['newSourceData'] = $newly_retrieved_source_connection_data;
		}
		//Check For Manual Source Popup
		$cff_settings['newManualSourcePopup'] = (isset($_GET['manualsource']) && $_GET['manualsource'] == 'true') ? true : false;


		wp_localize_script(
			'settings-app',
			'cff_settings',
			$cff_settings
		);
    }

	/**
	 * Get Extensions License Information
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get_extensions_license() {
		$data = array();
        $cff_ext = is_plugin_active( 'cff-extensions/cff-extensions.php' );

		// check whether the extensions plugin is activated or not
		if ( $cff_ext ) {
			$license_key = get_option( 'cff_license_key_extensions' );
			$license_status  = get_option( 'cff_license_status_extensions' );
			$status_text = '';
			if ( $license_status !== false && $license_status == 'valid' ) {
				$status_text = __('Your <b>Extensions</b> license is Active!', 'custom-facebook-feed');
			} else {
				$status_text = __('Your <b>Extensions</b> license is Inactive!', 'custom-facebook-feed');
			}
			$upgrade_url = sprintf(
				'https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-license',
				$license_key
			);

			$data[] = array(
				'name' => 'extensions',
				'itemName' => SB_ITEM_NAME_EXTENSIONS,
				'upgradeUrl' => $upgrade_url,
				'statusText' => $status_text,
				'licenseKey' => $license_key,
				'licenseStatus' => $license_status,
			);

			return $data;
		}

		// Define the variables
		$multifeed_active = is_plugin_active( 'cff-multifeed/cff-multifeed.php' );
		$reviews_active = is_plugin_active( 'cff-reviews/cff-reviews.php' );
		$carousel_active = is_plugin_active( 'cff-carousel/cff-carousel.php' );
		$date_range_active = is_plugin_active( 'cff-date-range/cff-date-range.php' );
		$featured_active = is_plugin_active( 'cff-featured-post/cff-featured-post.php' );
		$album_active = is_plugin_active( 'cff-album/cff-album.php' );

		// If multifeed extension is activated
		if ( $multifeed_active ) {
			$license_key = get_option( 'cff_license_key_multifeed' );
			$license_status  = get_option( 'cff_license_status_multifeed' );
			$status_text = '';
			if ( $license_status !== false && $license_status == 'valid' ) {
				$status_text = __('Your <b>Multifeed</b> license is Active!', 'custom-facebook-feed');
			} else {
				$status_text = __('Your <b>Multifeed</b> license is Inactive!', 'custom-facebook-feed');
			}
			$upgrade_url = sprintf(
				'https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-licen',
				$license_key
			);
			$data[] = array(
				'name' => 'multifeed',
				'itemName' => SB_ITEM_NAME_MULTIFEED,
				'upgradeUrl' => $upgrade_url,
				'statusText' => $status_text,
				'licenseKey' => $license_key,
				'licenseStatus' => $license_status,
			);
		}

		// If reviews extension is activated
		if ( $reviews_active ) {
			$license_key = get_option( 'cff_license_key_ext_reviews' );
			$license_status  = get_option( 'cff_license_status_ext_reviews' );
			$status_text = '';
			if ( $license_status !== false && $license_status == 'valid' ) {
				$status_text = __('Your <b>Reviews</b> license is Active!', 'custom-facebook-feed');
			} else {
				$status_text = __('Your <b>Reviews</b> license is Inactive!', 'custom-facebook-feed');
			}
			$upgrade_url = sprintf(
				'https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-licen',
				$license_key
			);
			$data[] = array(
				'name' => 'ext_reviews',
				'itemName' => SB_ITEM_NAME_EXT_REVIEWS,
				'upgradeUrl' => $upgrade_url,
				'statusText' => $status_text,
				'licenseKey' => $license_key == 'false' ? false : $license_key,
				'licenseStatus' => $license_status,
			);
		}

		// If carousel extension is activated
		if ( $carousel_active ) {
			$license_key = get_option( 'cff_license_key_ext_carousel' );
			$license_status  = get_option( 'cff_license_status_ext_carousel' );
			$status_text = '';
			if ( $license_status !== false && $license_status == 'valid' ) {
				$status_text = __('Your <b>Carousel</b> license is Active!', 'custom-facebook-feed');
			} else {
				$status_text = __('Your <b>Carousel</b> license is Inactive!', 'custom-facebook-feed');
			}
			$upgrade_url = sprintf(
				'https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-licen',
				$license_key
			);
			$data[] = array(
				'name' => 'ext_carousel',
				'itemName' => SB_ITEM_NAME_CAROUSEL,
				'upgradeUrl' => $upgrade_url,
				'statusText' => $status_text,
				'licenseKey' => $license_key == 'false' ? false : $license_key,
				'licenseStatus' => $license_status,
			);
		}

		// If date range extension is activated
		if ( $date_range_active ) {
			$license_key = get_option( 'cff_license_key_ext_date' );
			$license_status  = get_option( 'cff_license_status_ext_date' );
			$status_text = '';
			if ( $license_status !== false && $license_status == 'valid' ) {
				$status_text = __('Your <b>Date Range</b> license is Active!', 'custom-facebook-feed');
			} else {
				$status_text = __('Your <b>Date Range</b> license is Inactive!', 'custom-facebook-feed');
			}
			$upgrade_url = sprintf(
				'https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-licen',
				$license_key
			);
			$data[] = array(
				'name' => 'ext_date',
				'itemName' => SB_ITEM_NAME_EXT_DATE,
				'upgradeUrl' => $upgrade_url,
				'statusText' => $status_text,
				'licenseKey' => $license_key == 'false' ? false : $license_key,
				'licenseStatus' => $license_status,
			);
		}

		// If featured post extension is activated
		if ( $featured_active ) {
			$license_key = get_option( 'cff_license_key_featured_post' );
			$license_status  = get_option( 'cff_license_status_featured_post' );
			$status_text = '';
			if ( $license_status !== false && $license_status == 'valid' ) {
				$status_text = __('Your <b>Featured Post</b> license is Active!', 'custom-facebook-feed');
			} else {
				$status_text = __('Your <b>Featured Post</b> license is Inactive!', 'custom-facebook-feed');
			}
			$upgrade_url = sprintf(
				'https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-licen',
				$license_key
			);
			$data[] = array(
				'name' => 'featured_post',
				'itemName' => SB_ITEM_NAME_FEATURED,
				'upgradeUrl' => $upgrade_url,
				'statusText' => $status_text,
				'licenseKey' => $license_key == 'false' ? false : $license_key,
				'licenseStatus' => $license_status,
			);
		}

		// If album extension is activated
		if ( $album_active ) {
			$license_key = get_option( 'cff_license_key_album' );
			$license_status  = get_option( 'cff_license_status_album' );
			$status_text = '';
			if ( $license_status !== false && $license_status == 'valid' ) {
				$status_text = __('Your <b>Album</b> license is Active!', 'custom-facebook-feed');
			} else {
				$status_text = __('Your <b>Album</b> license is Inactive!', 'custom-facebook-feed');
			}
			$upgrade_url = sprintf(
				'https://smashballoon.com/custom-facebook-feed/pricing/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=settings&utm_medium=upgrade-licen',
				$license_key
			);
			$data[] = array(
				'name' => 'album',
				'itemName' => SB_ITEM_NAME_ALBUM,
				'upgradeUrl' => $upgrade_url,
				'statusText' => $status_text,
				'licenseKey' => $license_key == 'false' ? false : $license_key,
				'licenseStatus' => $license_status,
			);
		}

		return $data;
	}

	/**
	 * Get Links with UTM
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_links_with_utm() {
		$license_key = null;
		if ( get_option('cff_license_key') ) {
			$license_key = get_option('cff_license_key');
		}
		$all_access_bundle_popup = sprintf('https://smashballoon.com/all-access/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=balloon&utm_medium=all-access', $license_key);

		return array(
			'manageLicense' => 'https://smashballoon.com/account/downloads/?utm_campaign=facebook-free&utm_source=settings&utm_medium=manage-license',
			'popup' => array(
				'allAccessBundle' => $all_access_bundle_popup,
				'fbProfile' => 'https://www.facebook.com/SmashBalloon/',
				'twitterProfile' => 'https://twitter.com/smashballoon',
			),
			'proCachingLink' => 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=settings&utm_medium=pro-caching',
			'optimizeImagesLink' => 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=settings&utm_medium=optimize-images'
		);
	}

	/**
	 * The Settings Data
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get_settings_data() {
		$cff_preserve_setitngs = get_option( 'cff_preserve_settings' );
		$cff_locale = get_option( 'cff_locale', 'en_US' );
		$cff_style_settings = wp_parse_args( get_option( 'cff_style_settings' ), $this->default_settings_options() );
		$cff_caching_type = get_option( 'cff_caching_type', 'background' );
    	$cff_cache_cron_interval = get_option( 'cff_cache_cron_interval', '12hours' );
    	$cff_cache_cron_time = get_option( 'cff_cache_cron_time', 1 );
    	$cff_cache_cron_am_pm = get_option( 'cff_cache_cron_am_pm', 'am' );
		$usage_tracking = get_option( 'cff_usage_tracking', array( 'last_send' => 0, 'enabled' => CFF_Utils::cff_is_pro_version() ) );
		$cff_ajax = get_option('cff_ajax');
		$active_gdpr_plugin = CFF_GDPR_Integrations::gdpr_plugins_active();
		$cff_cache_time = get_option( 'cff_cache_time', 1 );
		$cff_cache_time_unit = get_option( 'cff_cache_time_unit', 'hours' );
		$custom_js_text = ! empty( $cff_style_settings['cff_custom_js'] ) && trim( wp_unslash( $cff_style_settings['cff_custom_js'] ) ) !== '' ? wp_unslash( $cff_style_settings['cff_custom_js'] ) : '';
		if ( ! empty( $custom_js_text ) ) {
			$js_wrapper_array = [
				esc_html('<!-- Custom Facebook Feed JS -->' ) . "\n",
				esc_html('<script type="text/javascript">' ) . "\n",
				esc_html('function cff_custom_js($){' ) . "\n",
				esc_html('    var $ = jQuery;' ) . "\n",
				esc_html('}cff_custom_js($);' )  . "\n",
				esc_html('</script>' ) . "\n"
			];
			foreach ($js_wrapper_array as $single_wrapper) {
				$custom_js_text = str_replace($single_wrapper, '', $custom_js_text);
			}

			$js_html = esc_html( '<!-- Custom Facebook Feed JS -->' ) . "\n";
			$js_html .= esc_html( '<script type="text/javascript">' ) . "\n";
			$js_html .= esc_html( 'function cff_custom_js($){' ) . "\n";
			$js_html .= esc_html( '    var $ = jQuery;' ) . "\n";
			$js_html .= esc_html( $custom_js_text ) . "\n";
			$js_html .= esc_html( '}cff_custom_js($);' ) . "\n";
			$js_html .= esc_html( '</script>' ) . "\n";

			$custom_js_text = $js_html;
		}

		return array(
			'general' => array(
				'preserveSettings' => $cff_preserve_setitngs
			),
			'feeds'	=> array(
				'selectedLocale' 	=> $cff_locale,
				'selectedTimezone'	=> $cff_style_settings['cff_timezone'],
				'cachingType'		=> 'background',
				'cronInterval'		=> $cff_cache_cron_interval,
				'cronTime'			=> $cff_cache_cron_time,
				'cronAmPm'			=> $cff_cache_cron_am_pm,
				'cacheTime'			=> $cff_cache_time,
				'cacheTimeUnit'		=> $cff_cache_time_unit,
				'gdpr'				=> $cff_style_settings['gdpr'],
				'gdprPlugin'		=> $active_gdpr_plugin,
				'customCSS'			=> isset( $cff_style_settings['cff_custom_css_read_only'] ) ? esc_html( stripslashes( trim( $cff_style_settings['cff_custom_css_read_only'] ) ) ) : '',
				'customJS'			=> $custom_js_text,
			),
			'translation' => array(
				'cff_see_more_text' => $cff_style_settings['cff_see_more_text'],
				'cff_see_less_text' => $cff_style_settings['cff_see_less_text'],
				'cff_map_text' => $cff_style_settings['cff_map_text'],
				'cff_no_events_text' => $cff_style_settings['cff_no_events_text'],
				'cff_interested_text' => $cff_style_settings['cff_interested_text'],
				'cff_going_text' => $cff_style_settings['cff_going_text'],
				'cff_buy_tickets_text' => $cff_style_settings['cff_buy_tickets_text'],
				'cff_facebook_link_text' => $cff_style_settings['cff_facebook_link_text'],
				'cff_facebook_share_text' => $cff_style_settings['cff_facebook_share_text'],
				'cff_load_more_text' => $cff_style_settings['cff_load_more_text'],
				'cff_no_more_posts_text' => $cff_style_settings['cff_no_more_posts_text'],
				'cff_translate_view_previous_comments_text' => $cff_style_settings['cff_translate_view_previous_comments_text'],
				'cff_translate_comment_on_facebook_text' => $cff_style_settings['cff_translate_comment_on_facebook_text'],
				'cff_translate_photos_text' => $cff_style_settings['cff_translate_photos_text'],
				'cff_translate_photo_text' => $cff_style_settings['cff_translate_photo_text'],
				'cff_translate_video_text' => $cff_style_settings['cff_translate_video_text'],
				'cff_translate_like_this_text' => $cff_style_settings['cff_translate_like_this_text'],
				'cff_translate_likes_this_text' => $cff_style_settings['cff_translate_likes_this_text'],
				'cff_translate_reacted_text' => $cff_style_settings['cff_translate_reacted_text'],
				'cff_translate_and_text' => $cff_style_settings['cff_translate_and_text'],
				'cff_translate_other_text' => $cff_style_settings['cff_translate_other_text'],
				'cff_translate_others_text' => $cff_style_settings['cff_translate_others_text'],
				'cff_translate_reply_text' => $cff_style_settings['cff_translate_reply_text'],
				'cff_translate_replies_text' => $cff_style_settings['cff_translate_replies_text'],
				'cff_translate_learn_more_text' => $cff_style_settings['cff_translate_learn_more_text'],
				'cff_translate_shop_now_text' => $cff_style_settings['cff_translate_shop_now_text'],
				'cff_translate_message_page_text' => $cff_style_settings['cff_translate_message_page_text'],
				'cff_translate_get_directions_text' => $cff_style_settings['cff_translate_get_directions_text'],
				'cff_translate_second' => $cff_style_settings['cff_translate_second'],
				'cff_translate_seconds' => $cff_style_settings['cff_translate_seconds'],
				'cff_translate_minute' => $cff_style_settings['cff_translate_minute'],
				'cff_translate_minutes' => $cff_style_settings['cff_translate_minutes'],
				'cff_translate_hour' => $cff_style_settings['cff_translate_hour'],
				'cff_translate_hours' => $cff_style_settings['cff_translate_hours'],
				'cff_translate_day' => $cff_style_settings['cff_translate_day'],
				'cff_translate_days' => $cff_style_settings['cff_translate_days'],
				'cff_translate_week' => $cff_style_settings['cff_translate_week'],
				'cff_translate_weeks' => $cff_style_settings['cff_translate_weeks'],
				'cff_translate_month' => $cff_style_settings['cff_translate_month'],
				'cff_translate_months' => $cff_style_settings['cff_translate_months'],
				'cff_translate_year' => $cff_style_settings['cff_translate_year'],
				'cff_translate_years' => $cff_style_settings['cff_translate_years'],
				'cff_translate_ago' => $cff_style_settings['cff_translate_ago'],
			),
			'advanced' => array(
				'cff_disable_resize' => !$cff_style_settings['cff_disable_resize'],
				'usage_tracking' => $usage_tracking['enabled'],
				'cff_ajax' => $cff_ajax,
				'cff_show_credit' => $cff_style_settings['cff_show_credit'],
				'cff_format_issue' => $cff_style_settings['cff_format_issue'],
				'disable_admin_notice' => !$cff_style_settings['disable_admin_notice'],
				'enable_email_report' => $cff_style_settings['enable_email_report'],
				'email_notification' => $cff_style_settings['email_notification'],
				'email_notification_addresses' => $cff_style_settings['email_notification_addresses'],
			)
		);
	}

	/**
	 * Return the default settings options for cff_style_settings option
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function default_settings_options() {
		return array(
			'cff_timezone' 									=> 'America/Chicago',
			'gdpr'											=> 'auto',
			'cff_see_more_text'         					=> 'See More',
			'cff_see_less_text'         					=> 'See Less',
			'cff_map_text'              					=> 'Map',
			'cff_no_events_text'        					=> 'No upcoming events',
			'cff_facebook_link_text'    					=> 'View on Facebook',
			'cff_facebook_share_text'   					=> 'Share',
			'cff_buy_tickets_text'      					=> 'Buy Tickets',
			'cff_interested_text'       					=> 'interested',
			'cff_going_text'            					=> 'going',
			'cff_load_more_text'        					=> 'Load more',
			'cff_no_more_posts_text'    					=> 'No more posts',
			'cff_translate_view_previous_comments_text'     => 'View more comments',
			'cff_translate_comment_on_facebook_text'        => 'Comment on Facebook',
			'cff_translate_photos_text'                     => 'photos',
			'cff_translate_photo_text'                     	=> 'Photo',
			'cff_translate_video_text'                     	=> 'Video',
			'cff_translate_likes_this_text'                 => 'likes this',
			'cff_translate_like_this_text'                  => 'like this',
			'cff_translate_reacted_text'                    => 'reacted to this',
			'cff_translate_and_text'                        => 'and',
			'cff_translate_other_text'                      => 'other',
			'cff_translate_others_text'                     => 'others',
			'cff_translate_reply_text'                      => 'Reply',
			'cff_translate_replies_text'                    => 'Replies',
			'cff_translate_learn_more_text' 				=> 'Learn More',
			'cff_translate_shop_now_text'   				=> 'Shop Now',
			'cff_translate_message_page_text' 				=> 'Message Page',
			'cff_translate_get_directions_text' 			=> 'Get Directions',
			'cff_translate_second'      					=> 'second',
			'cff_translate_seconds'     					=> 'seconds',
			'cff_translate_minute'      					=> 'minute',
			'cff_translate_minutes'     					=> 'minutes',
			'cff_translate_hour'        					=> 'hour',
			'cff_translate_hours'       					=> 'hours',
			'cff_translate_day'         					=> 'day',
			'cff_translate_days'        					=> 'days',
			'cff_translate_week'        					=> 'week',
			'cff_translate_weeks'       					=> 'weeks',
			'cff_translate_month'       					=> 'month',
			'cff_translate_months'      					=> 'months',
			'cff_translate_year'        					=> 'year',
			'cff_translate_years'       					=> 'years',
			'cff_translate_ago'         					=> 'ago',
			'cff_show_credit'		    					=> false,
			'cff_format_issue'		    					=> false,
			'cff_disable_resize'		    				=> false,
			'disable_admin_notice'		    				=> false,
			'enable_email_report'		    				=> 'on',
			'email_notification'							=> 'monday',
        	'email_notification_addresses' 					=> get_option( 'admin_email' )
		);
	}

	/**
	 * Get GDPR Automatic state information
	 *
	 * @since 4.0
	 *
	 * @return string $output
	 */
	public function get_gdpr_auto_info() {
		$gdpr_doc_url 			= 'https://smashballoon.com/doc/custom-facebook-feed-gdpr-compliance/?facebook';
		$output = '';
		$active_gdpr_plugin = CFF_GDPR_Integrations::gdpr_plugins_active();
		if ( $active_gdpr_plugin ) {
			$output = $active_gdpr_plugin;
		} else {
			$output = __( 'No GDPR consent plugin detected. Install a compatible <a href="'. $gdpr_doc_url .'" target="_blank">GDPR consent plugin</a>, or manually enable the setting to display a GDPR compliant version of the feed to all visitors.', 'custom-facebook-feed' );
		}
		return $output;
	}

	/**
	 * CFF Get cron next check time
	 *
	 * @since 4.0
	 *
	 * @return string $output
	 */
	public function get_cron_next_check() {
		$output = '';

		if ( wp_next_scheduled( 'cff_cache_cron' ) ) {
			//Get the timezone
			$cff_orig_timezone = date_default_timezone_get();
			$options = get_option('cff_style_settings');
			if ( isset( $options[ 'cff_timezone' ] ) ) {
				date_default_timezone_set( $options[ 'cff_timezone' ] );
			}

			$schedule = wp_get_schedule( 'cff_cache_cron' );
			if( $schedule == '30mins' ) $schedule = 'every 30 minutes';
			if( $schedule == 'twicedaily' ) $schedule = 'every 12 hours';
			$cff_next_cron_event = wp_next_scheduled( 'cff_cache_cron' );
			$output = '<b>Next check: ' . date('g:i a', $cff_next_cron_event) . ' (' . $schedule . ')</b> - Note: Clicking "Clear All Caches" will reset this schedule.';

			//Reset the timezone
			date_default_timezone_set( $cff_orig_timezone );
		} else {
			$output = 'Nothing currently scheduled';
		}

		return $output;
	}

   	/**
	 * Settings Page View Template
	 *
	 * @since 4.0
	 */
	public function global_settings(){
		return CFF_View::render( 'settings.index' );
	}

	/**
	 * CFF Clear Everything
	 *
	 * @since 4.1
	 */
	public function cff_dpa_reset() {
		//Security Checks
		check_ajax_referer( 'cff_admin_nonce', 'nonce'  );

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		cff_delete_all_platform_data();
		new CFF_Response( true, [] );
	}
}
