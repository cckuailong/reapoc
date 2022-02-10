<?php
/**
 * Class CFF_Resizer
 *
 * Image resizing and local storage is done when there are no "medium"
 * sized images available from the API. This class handles this process
 * using the raw API data and a list of post IDs that need resizing.
 *
 * @since 3.14
 */

namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Resizer {

	public function __construct( $post_ids_need_resizing, $feed_id, $posts, $feed_options ) {
	}

	public function get_new_resized_image_data() {
		return array();
	}

	public function do_resizing() {
	}

	public function do_resizing_group() {
	}

	public static function image_sizes( $feed_options ) {
		$image_sizes = array( 400, 250 );
		return $image_sizes;
	}

	public static function get_resized_image_data_for_set( $ids_or_feed_id, $args = array() ) {
		return [];
	}

	public static function delete_resizing_table_and_images() {
		$upload = wp_upload_dir();

		global $wpdb;

		$posts_table_name = $wpdb->prefix . CFF_POSTS_TABLE;
		$feeds_posts_table_name = $wpdb->prefix . CFF_FEEDS_POSTS_TABLE;

		$image_files = glob( trailingslashit( $upload['basedir'] ) . trailingslashit( CFF_UPLOADS_NAME ) . '*'  ); // get all file names
		foreach ( $image_files as $file ) { // iterate files
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}

		//Delete tables
		$wpdb->query( "DROP TABLE IF EXISTS $posts_table_name" );
		$wpdb->query( "DROP TABLE IF EXISTS $feeds_posts_table_name" );
	}

	public static function create_resizing_table_and_uploads_folder() {
		$upload = wp_upload_dir();

		$upload_dir = $upload['basedir'];
		$upload_dir = trailingslashit( $upload_dir ) . CFF_UPLOADS_NAME;
		if ( ! file_exists( $upload_dir ) ) {
			$created = wp_mkdir_p( $upload_dir );
			if ( $created ) {
				\cff_main()->cff_error_reporter->remove_error( 'upload_dir' );
			} else {
				\cff_main()->cff_error_reporter->add_error( 'upload_dir', array( __( 'There was an error creating the folder for storing resized images.', 'custom-facebook-feed' ), $upload_dir ) );

			}
		} else {
			\cff_main()->cff_error_reporter->remove_error( 'upload_dir' );
		}
		return \cff_main()->cff_create_database_table();
	}

	public static function delete_least_used_image() {
	}

	/**
	 * Calculates how many records are in the database and whether or not it exceeds the limit
	 *
	 * @return bool
	 *
	 * @since 3.14
	 */
	public function max_total_records_reached() {
	}

	/**
	 * The plugin caps how many new images are created in a 15 minute window to
	 * avoid overloading servers
	 *
	 * @return bool
	 *
	 * @since 3.14
	 */
	public static function max_resizing_per_time_period_reached() {
	}

	/**
	 * @return bool
	 *
	 * @since 3.14
	 */
	public function image_resizing_disabled() {
	}

	/**
	 * Used to skip image resizing if the tables were never successfully
	 * created
	 *
	 * @return bool
	 *
	 * @since 3.14
	 */
	public function does_resizing_tables_exist() {
	}

}