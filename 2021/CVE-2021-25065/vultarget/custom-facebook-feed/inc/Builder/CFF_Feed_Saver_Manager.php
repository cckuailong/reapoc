<?php
/**
 * Custom Facebook Feed Feed Saver Manager
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Builder;
class CFF_Feed_Saver_Manager {

	/**
	 * AJAX hooks for various feed data related functionality
	 *
	 * @since 4.0
	 */
	public static function hooks() {
		add_action( 'wp_ajax_cff_feed_saver_manager_builder_update', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'builder_update' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_get_feed_settings', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'get_feed_settings' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_get_feed_list_page', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'get_feed_list_page' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_get_locations_page', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'get_locations_page' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_delete_feeds', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'delete_feed' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_duplicate_feed', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'duplicate_feed' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_clear_single_feed_cache', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'clear_single_feed_cache' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_importer', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'importer' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_fly_preview', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'feed_customizer_fly_preview' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_retrieve_comments', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'retrieve_comments' ) );
		add_action( 'wp_ajax_cff_feed_saver_manager_delete_source', array( 'CustomFacebookFeed\Builder\CFF_Feed_Saver_Manager', 'delete_source' ) );
	}

	/**
	 * Used in an AJAX call to update settings for a particular feed.
	 * Can also be used to create a new feed if no feed_id sent in
	 * $_POST data.
	 *
	 * @since 4.0
	 */
	public static function builder_update() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}


		$settings_data = $_POST;

		$feed_id = false;
		if ( ! empty( $settings_data['feed_id'] ) ) {
			$feed_id = sanitize_text_field( $settings_data['feed_id'] );
			unset( $settings_data['feed_id'] );
		} elseif ( isset( $settings_data['feed_id'] ) ) {
			unset( $settings_data['feed_id'] );
		}
		unset( $settings_data['action'] );

		if ( ! isset( $settings_data['feed_name'] ) ) {
			$settings_data['feed_name'] = '';
		}

		//Check if New
		if ( isset( $settings_data['new_insert'] ) && $settings_data['new_insert'] == 'true' && isset($settings_data['sourcename'])) {
			$settings_data['feed_name'] = CFF_Db::feeds_query_name($settings_data['sourcename']);

			$default_grid = [
				'albums',
				'videos',
				'photos',
				'singlealbum'
			];
			$feed_type = $settings_data['feedtype'];
			if ( in_array( $feed_type, $default_grid ) ) {
				$settings_data['feedlayout'] = 'grid';
			}
		}
		unset( $settings_data['new_insert'] );
		unset( $settings_data['sourcename'] );
		$feed_name = '';
		if ( isset( $settings_data['update_feed'] ) && $settings_data['update_feed'] == 'true') {
			$settings_data['settings']['sources'] = $_POST['sources'];
			$feed_name = sanitize_text_field( wp_unslash( $settings_data['feed_name'] ) );
			$settings_data = $settings_data['settings'];
		}
		if ( isset( $settings_data['album'] ) ) {
			$settings_data['album'] = CFF_Source::extract_id( $settings_data['album'], 'album' );
		}
		if ( isset( $settings_data['playlist'] ) ) {
			$settings_data['playlist'] = CFF_Source::extract_id( $settings_data['playlist'], 'playlist' );
		}

		unset($settings_data['accesstoken']);
		$feed_saver = new CFF_Feed_Saver( $feed_id );
		$feed_saver->set_feed_name( $feed_name );
		$feed_saver->set_data( $settings_data );
		$return = array(
			'success' => false,
			'feed_id' => false
		);

		if ( $feed_saver->update_or_insert() ) {
			$return = array(
				'success' => true,
				'feed_id' => $feed_saver->get_feed_id()
			);
		}

		$feed_cache = new \CustomFacebookFeed\CFF_Cache( $feed_id );
		$feed_cache->clear( 'all' );
		$feed_cache->clear( 'posts' );

		if ( isset( $_POST['include_post_set'] ) &&
		     ! empty( $_POST['include_post_set'] ) ) {
			$post_set = new CFF_Post_Set( $feed_saver->get_feed_id() );

			$post_set->init();
			$post_set->fetch();

			$return['posts'] = $post_set->get_data();
		}

		if ( isset( $_POST['include_header'] ) &&
		     ! empty( $_POST['include_header'] ) ) {

			if ( ! isset( $post_set ) ) {
				$post_set = new CFF_Post_Set( $feed_saver->get_feed_id() );

				$post_set->init();
			}

			$header_details = array();
			$settings = $post_set->get_feed_settings();

			if ( isset( $settings['sources'][0] ) ) {
				$args = array(
					'id' => $settings['sources'][0]
				);
				$results = CFF_Db::source_query( $args );

				$header_details = \CustomFacebookFeed\CFF_Utils::fetch_header_data( $results[0]['account_id'], $results[0]['account_type'] === 'group', $results[0]['access_token'], 0, false, '' );


			}

			$return['header'] = $header_details;
		}

		echo \CustomFacebookFeed\CFF_Utils::cff_json_encode( $return );
		wp_die();
	}

	/**
	 * Retrieve comments AJAX call
	 *
	 * @since 4.0
	 */
	public static function retrieve_comments() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		if ( empty( $_POST['feed_id'] )) {
			echo '{}';
			wp_die();
		}

		$return = [];

		$feed_id  = $_POST['feed_id'];
		$feed_saver = new CFF_Feed_Saver( $feed_id );
		$settings = $feed_saver->get_feed_settings();
		if ( $settings != false ){
			$post_set = new CFF_Post_Set( $feed_id );
			$post_set->init();
			$post_set->fetch();

			$return = $post_set->fetch_comments();
		}

		echo \CustomFacebookFeed\CFF_Utils::cff_json_encode( $return );
		wp_die();
	}


	/**
	 * Used in an AJAX call to delete feeds from the Database
	 * $_POST data.
	 *
	 * @since 4.0
	 */
	public static function delete_feed() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		if ( ! empty( $_POST['feeds_ids'] ) && is_array( $_POST['feeds_ids'] )) {
			CFF_Db::delete_feeds_query( $_POST['feeds_ids'] );
		}
	}


	/**
	 * Used in an AJAX call to delete Soureces from the Database
	 * $_POST data.
	 *
	 * @since 4.0
	 */
	public static function delete_source() {
		check_ajax_referer( 'cff_admin_nonce' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		if ( ! empty( $_POST['source_id'] ) ) {
			CFF_Db::delete_source_query( $_POST['source_id'] );
		}
	}

	/**
	 * Used in an AJAX call to delete a feed cache from the Database
	 * $_POST data.
	 *
	 * @since 4.0
	 */
	public static function clear_single_feed_cache() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$feed_id = sanitize_text_field( $_POST['feedID'] );

		if ( $feed_id === 'legacy' ) {
			\CustomFacebookFeed\CFF_Cache::clear_legacy();
			\CustomFacebookFeed\CFF_Cache::clear_page_caches();
		} else {
			if ( \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'multifeed' ) ) {
				\CustomFacebookFeed\CFF_Cache::clear_legacy();
				\CustomFacebookFeed\CFF_Cache::clear_page_caches();
			}

			$feed_cache = new \CustomFacebookFeed\CFF_Cache( $feed_id );

			$feed_cache->clear( 'all' );
			$feed_cache->clear( 'posts' );
		}

		CFF_Feed_Saver_Manager::feed_customizer_fly_preview();
		wp_die();

	}

	/**
	 * Used in an AJAX call to duplicate a Feed
	 * $_POST data.
	 *
	 * @since 4.0
	 */
	public static function duplicate_feed() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		if ( ! empty( $_POST['feed_id'] ) ) {
			CFF_Db::duplicate_feed_query( $_POST['feed_id'] );
		}
	}


	/**
	 * Import a feed from JSON data
	 *
	 * @since 4.0
	 */
	public static function importer() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		if ( ! empty( $_POST['feed_json'] ) && strpos( $_POST['feed_json'], '{' ) === 0 ) {
			echo json_encode( CFF_Feed_Saver_Manager::import_feed( stripslashes( $_POST['feed_json'] ) ) );
		} else {
			echo json_encode(  array( 'success' => false, 'message' => __( 'Invalid JSON. Must have brackets "{}"', 'custom-facebook-feed' ) ) );
		}
		wp_die();
	}


	/**
	 * Used To check if it's customizer Screens
	 * Returns Feed info or false!
	 *
	 * @param bool $include_comments
	 *
	 * @return array|bool
	 *
	 * @since 4.0
	 */
	public static function maybe_feed_customizer_data( $include_comments = false ) {


		if ( isset( $_GET['feed_id'] ) ){
			$feed_id  = $_GET['feed_id'];
			$feed_saver = new CFF_Feed_Saver( $feed_id );
			$settings = $feed_saver->get_feed_settings();
			$feed_db_data = $feed_saver->get_feed_db_data();

			if($settings != false){
				$return = array(
					'feed_info' => $feed_db_data,
					'settings' => $settings,
					'posts' => array()
				);
				$post_set = new CFF_Post_Set( $feed_id );
				$post_set->init();
				$post_set->fetch();
				$return['posts'] = $post_set->get_data();

				if ( ! isset( $post_set ) ) {
					$post_set = new CFF_Post_Set( $feed_saver->get_feed_id() );
					$post_set->init();
				}

				$header_details = array();
				$settings = $post_set->get_feed_settings();

				if ( isset( $settings['sources'][0] ) ) {
					$results = $settings['sources'];

					$header_details = \CustomFacebookFeed\CFF_Utils::fetch_header_data( $results[0]['account_id'], $results[0]['account_type'] === 'group', $results[0]['access_token'], 0, false, '' );
				}
				$return['header'] = $header_details;

				if ( ! empty( $return['posts'] )
				     && $include_comments ) {
					$post_set->fetch_comments();
					$return['comments'] = $post_set->get_comments_data();
				}


				return $return;

			}
		}
		return false;
	}
	/**
	 * Used to retrieve Feed Posts for preview screen
	 * Returns Feed info or false!
	 *
	 *
	 *
	 * @since 4.0
	 */
	public static function feed_customizer_fly_preview() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		if( isset( $_POST['feedID'] ) &&  isset( $_POST['previewSettings'] ) ){
			$return = array(
				'posts' => array()
			);
			$post_set = new CFF_Post_Set( $_POST['feedID'] );
			$post_set->init( true, $_POST['previewSettings'] );
			$post_set->fetch();

			$return['posts'] = $post_set->get_data();

			$header_details = array();
			$settings = $post_set->get_feed_settings();

			if ( isset( $settings['sources'][0] ) ) {
				$args = array(
					'id' => $settings['sources'][0]
				);
				$results = CFF_Db::source_query( $args );
				$header_details = \CustomFacebookFeed\CFF_Utils::fetch_header_data( $results[0]['account_id'], $results[0]['account_type'] === 'group', $results[0]['access_token'], 0, false, '' );
			}
			$return['header'] = $header_details;

			echo json_encode( $return );
		}
		die();
		return false;

	}

	/**
	 * Used in AJAX call to return settings for an existing feed.
	 *
	 * @since 4.0
	 */
	public static function get_feed_settings() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$feed_id = ! empty( $_POST['feed_id'] ) ? $_POST['feed_id'] : false;

		if ( ! $feed_id ) {
			wp_die( 'no feed id' );
		}

		$feed_saver = new CFF_Feed_Saver( $feed_id );
		$settings = $feed_saver->get_feed_settings();

		$return = array(
			'settings' => $settings,
			'posts' => array()
		);

		if ( isset( $_POST['include_post_set'] ) &&
		     ! empty( $_POST['include_post_set'] ) ) {
			$post_set = new CFF_Post_Set( $feed_id );

			$post_set->init();
			$post_set->fetch();

			$return['posts'] = $post_set->get_data();
		}

		if ( isset( $_POST['include_header'] ) &&
		     ! empty( $_POST['include_header'] ) ) {

			if ( ! isset( $post_set ) ) {
				$post_set = new CFF_Post_Set( $feed_saver->get_feed_id() );

				$post_set->init();


			}

			$header_details = array();
			$settings = $post_set->get_feed_settings();

			if ( isset( $settings['sources'][0] ) ) {
				$args = array(
					'id' => $settings['sources'][0]
				);
				$results = CFF_Db::source_query( $args );

				$header_details = \CustomFacebookFeed\CFF_Utils::fetch_header_data( $results[0]['account_id'], $results[0]['account_type'] === 'group', $results[0]['access_token'], 0, false, '' );
			}

			$return['header'] = $header_details;
		}

		echo \CustomFacebookFeed\CFF_Utils::cff_json_encode( $return );
		wp_die();
	}

	/**
	 * Get a list of feeds with a limit and offset like a page
	 *
	 * @since 4.0
	 */
	public static function get_feed_list_page() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$args = array( 'page' => (int)$_POST['page'] );
		$feeds_data = CFF_Feed_Builder::get_feed_list($args);

		echo \CustomFacebookFeed\CFF_Utils::cff_json_encode( $feeds_data );

		wp_die();
	}

	/**
	 * Get a list of locations with a limit and offset like a page
	 *
	 * @since 4.0
	 */
	public static function get_locations_page() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$args = array( 'page' => (int)$_POST['page'] );

		if ( ! empty( $_POST['is_legacy'] ) ) {
			$args['feed_id'] = sanitize_text_field( $_POST['feed_id'] );
		} else {
			$args['feed_id'] = '*' . (int)$_POST['feed_id'];
		}
		$feeds_data = \CustomFacebookFeed\CFF_Feed_Locator::facebook_feed_locator_query( $args );

		if ( count( $feeds_data ) < CFF_Db::RESULTS_PER_PAGE ) {
			$args['html_location'] = array( 'footer', 'sidebar', 'header' );
			$args['group_by'] = 'html_location';
			$args['page'] = 1;
			$non_content_data = \CustomFacebookFeed\CFF_Feed_Locator::facebook_feed_locator_query( $args );

			$feeds_data = array_merge( $feeds_data, $non_content_data );
		}

		echo \CustomFacebookFeed\CFF_Utils::cff_json_encode( $feeds_data );

		wp_die();
	}

	/**
	 * Return a single JSON string for importing a feed
	 *
	 * @param int $feed_id
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public static function get_export_json( $feed_id ) {
		$feed_saver = new CFF_Feed_Saver( $feed_id );
		$settings = $feed_saver->get_feed_settings();

		return \CustomFacebookFeed\CFF_Utils::cff_json_encode( $settings );
	}

	/**
	 * All export strings for all feeds on the first 'page'
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_all_export_json() {
		$args = array( 'page' => 1 );

		$feeds_data = CFF_Db::feeds_query( $args );

		$return = array();
		foreach ( $feeds_data as $single_feed ) {
			$return[ $single_feed['id'] ] = CFF_Feed_Saver_Manager::get_export_json( $single_feed['id'] );
		}

		return $return;
	}

	/**
	 * Use a JSON string to import a feed with settings and sources. The return
	 * is whether or not the import was successful
	 *
	 * @param string $json
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function import_feed( $json ) {
		$settings_data = json_decode( $json, true );

		$return = array(
			'success' => false,
			'message' => ''
		);

		if ( empty( $settings_data['sources'] ) ) {
			$return['message'] = __( 'No feed source is included. Cannot upload feed.', 'custom-facebook-feed' );
			return $return;
		}

		$sources = $settings_data['sources'];

		unset( $settings_data['sources'] );

		$settings_source = array();
		foreach ( $sources as $source ) {
			if ( isset( $source['account_id'] ) ) {
				$settings_source[] = $source['account_id'];
				$source_data = array(
					'access_token' => sanitize_text_field( $source['access_token'] ),
					'id'           => sanitize_text_field( $source['account_id'] ),
					'type'         => sanitize_text_field( $source['account_type'] ),
					'privilege'    => isset( $source['privilege'] ) ? sanitize_text_field( $source['privilege'] ) : '',
				);

				if ( ! empty( $source['name'] ) ) {
					$source_data['name'] = sanitize_text_field( $source['name'] );
				}

				$header_details = \CustomFacebookFeed\CFF_Utils::fetch_header_data( $source_data['id'], $source_data['type'] === 'group', $source_data['access_token'], 0, false, '' );

				if ( isset( $header_details->shortcode_options ) ) {
					unset( $header_details->shortcode_options );
				}

				if ( isset( $header_details->name ) ) {
					$source_data['name'] = $header_details->name;
				}
				$source_data['info'] = $header_details;

				// don't update or insert the access token if there is an API error
				if ( ! isset( $header_details->error ) ) {
					CFF_Source::update_or_insert( $source_data );
				}
			}

		}
		$settings_data['sources'] = $settings_source;
		$feed_saver = new CFF_Feed_Saver( false );
		$feed_saver->set_data( $settings_data );

		if ( $feed_saver->update_or_insert() ) {
			$return = array(
				'success' => true,
				'feed_id' => $feed_saver->get_feed_id()
			);

			return $return;
		} else {
			$return['message'] = __( 'Could not import feed. Please try again', 'custom-facebook-feed' );
		}
		return $return;
	}

	/**
	 * Determines what table and sanitization should be used
	 * when handling feed setting data.
	 *
	 * TODO: Add settings that need something other than sanitize_text_field
	 *
	 * @param string $key
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_data_type( $key ) {
		switch ( $key ) {
			case 'sources' :
				$return = array(
					'table' => 'feed_settings',
					'sanitization' => 'sanitize_text_field'
				);
				break;
			case 'feed_title' :
				$return = array(
					'table' => 'feeds',
					'sanitization' => 'sanitize_text_field'
				);
				break;
			case 'feed_name' :
				$return = array(
					'table' => 'feeds',
					'sanitization' => 'sanitize_text_field'
				);
				break;
			case 'status' :
				$return = array(
					'table' => 'feeds',
					'sanitization' => 'sanitize_text_field'
				);
				break;
			case 'author' :
				$return = array(
					'table' => 'feeds',
					'sanitization' => 'int'
				);
				break;
			default:
				$return = array(
					'table' => 'feed_settings',
					'sanitization' => 'sanitize_text_field'
				);
				break;
		}

		return $return;
	}

	/**
	 * Uses the appropriate sanitization function and returns the result
	 * for a value
	 *
	 * @param string $type
	 * @param int|string $value
	 *
	 * @return int|string
	 *
	 * @since 4.0
	 */
	public static function sanitize( $type, $value ) {
		switch ( $type ) {
			case 'int':
				$return = intval( $value );
				break;
			default:
				$return = sanitize_text_field( wp_unslash( $value ) );
				break;
		}

		return $return;
	}
}
