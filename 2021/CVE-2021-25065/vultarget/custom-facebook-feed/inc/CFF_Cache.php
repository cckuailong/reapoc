<?php
/**
 * Custom Facebook Feed Cache
 *
 * For the new feed builder
 *
 * @since 4.0
 */

namespace CustomFacebookFeed;
use CustomFacebookFeed\SB_Facebook_Data_Encryption;

class CFF_Cache {

	/**
	 * @var int
	 */
	private $feed_id;

	/**
	 * @var int
	 */
	private $page;

	/**
	 * @var string
	 */
	private $suffix;

	/**
	 * @var bool
	 */
	private $is_legacy;

	/**
	 * @var int
	 */
	private $cache_time;

	/**
	 * @var array
	 */
	private $posts;

	/**
	 * @var array
	 */
	private $posts_page;

	/**
	 * @var bool
	 */
	private $is_expired;

	/**
	 * @var array
	 */
	private $header;

	/**
	 * @var array
	 */
	private $resized_images;

	/**
	 * @var array
	 */
	private $meta;

	/**
	 * @var array
	 */
	private $posts_backup;

	/**
	 * @var array
	 */
	private $header_backup;

	/**
	 * @var object|SB_Facebook_Data_Encryption
	 */
	protected $encryption;

	/**
	 * CFF_Cache constructor. Set the feed id, cache key, legacy
	 *
	 * @param string $feed_id
	 * @param int $page
	 * @param int $cache_time
	 * @param bool $is_legacy
	 */
	public function __construct( $feed_id, $page = 1, $cache_time = 0, $is_legacy = false ) {
		$this->cache_time = (int)$cache_time;
		$this->is_legacy = $is_legacy;
		$this->page = $page;

		if ( $this->page === 1 ) {
			$this->suffix = '';
		} else {
			$this->suffix = '_' . $this->page;
		}

		$this->feed_id = $feed_id;
		$this->encryption = new SB_Facebook_Data_Encryption();
	}

	/**
	 * Set all caches based on available data.
	 *
	 * @since 4.0
	 */
	public function retrieve_and_set() {

		if ( ! $this->is_legacy ) {
			$expired = true;
			$existing_caches = $this->query_cff_feed_caches();

			foreach ( $existing_caches as $cache ) {
				switch( $cache['cache_key'] ) {
					case 'posts':
						$this->posts = $cache['cache_value'];
						if ( strtotime( $cache['last_updated'] ) > time() - $this->cache_time ) {
							$expired = false;
						}

						if ( empty( $cache['cache_value'] ) ) {
							$expired = true;
						}
						break;
					case 'posts' . $this->suffix:
						$this->posts_page = $cache['cache_value'];
						break;
					case 'header':
						$this->header = $cache['cache_value'];
						break;
					case 'resized_images'. $this->suffix:
						$this->resized_images = $cache['cache_value'];
						break;
					case 'meta'. $this->suffix:
						$this->meta = $cache['cache_value'];
						break;
					case 'posts_backup'. $this->suffix:
						$this->posts_backup = $cache['cache_value'];
						break;
					case 'header_backup'. $this->suffix:
						$this->posts_backup = $cache['cache_value'];
						break;
				}

			}

			$this->is_expired = $expired;

		} else {
			$transient_cache = get_transient( $this->feed_id );

			if ( $transient_cache ) {
				if ( strpos( $this->feed_id, 'cff_header_') !== false ) {
					$this->header = $transient_cache;
					$this->is_expired = false;
				} else {
					$this->posts = $transient_cache;
					$this->is_expired = false;
				}

			} else {
				$this->posts = array();
				$this->is_expired = true;
			}
			$this->posts_backup = get_transient( '!cff_backup_' . $this->feed_id );
		}

		if ( $this->cache_time < 1 ) {
			$this->is_expired = true;
		}

	}

	/**
	 * Whether or not the cache needs to be refreshed
	 *
	 * @param string $cache_type
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function is_expired( $cache_type = 'posts' ) {

		if ( $cache_type !== 'posts' ) {
			$cache = $this->get( $cache_type );

			return (empty( $cache ) || $this->is_expired);
		}
		if ( $this->page === 1 ) {
			return $this->is_expired;
		} else {
			if ( $this->is_expired ) {
				return true;
			}
			if ( empty( $this->posts_page ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get data currently stored in the database for the type
	 *
	 * @param string $type
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get( $type ) {
		$return = array();
		switch( $type ) {
			case 'posts':
				$return = $this->posts;
				break;
			case 'posts' . $this->suffix:
				$return = $this->posts_page;
				break;
			case 'header':
				$return = $this->header;
				break;
			case 'resized_images':
				$return = $this->resized_images;
				break;
			case 'meta':
				$return = $this->meta;
				break;
			case 'posts_backup':
				$return = $this->posts_backup;
				break;
			case 'header_backup':
				$return = $this->header_backup;
				break;
		}
		return $this->maybe_decrypt( $return );
	}

	/**
	 * @param string $type
	 * @param array $cache_value
	 *
	 * @since 4.0
	 */
	public function set( $type, $cache_value ) {
		switch( $type ) {
			case 'posts':
				$this->posts = $cache_value;
				break;
			case 'posts' . $this->suffix:
				$this->posts_page = $cache_value;
				break;
			case 'header':
				$this->header = $cache_value;
				break;
			case 'resized_images':
				$this->resized_images = $cache_value;
				break;
			case 'meta':
				$this->meta = $cache_value;
				break;
			case 'posts_backup':
				$this->posts_backup = $cache_value;
				break;
			case 'header_backup':
				$this->header_backup = $cache_value;
				break;
		}
	}

	/**
	 * Update a single cache with new data. Try to accept any data and convert it
	 * to JSON if needed
	 *
	 * @param string $cache_type
	 * @param array|object|string $cache_value
	 * @param bool $include_backup
	 * @param bool $cron_update
	 *
	 * @return int
	 *
	 * @since 4.0
	 */
	public function update_or_insert( $cache_type, $cache_value, $include_backup = true, $cron_update = true ) {
		$this->clear_wp_cache();

		if ( $this->page > 1 || ($cache_type !== 'posts' && $cache_type !== 'header') ) {
			$cron_update = false;
		}

		$cache_key = $cache_type . $this->suffix;

		$this->set( $cache_key, $cache_value );

		if ( is_array( $cache_value ) || is_object( $cache_value ) ) {
			$cache_value = CFF_Utils::cff_json_encode( $cache_value );
		}

		$encrypted_cache_value = $this->maybe_encrypt( $cache_value );

		if ( $this->is_legacy ) {
			if ( $cache_key === 'posts' ) {
				set_transient( $this->feed_id, $encrypted_cache_value, $this->cache_time );
				if ( $include_backup ) {
					set_transient( '!cff_backup_' . $this->feed_id, $encrypted_cache_value, YEAR_IN_SECONDS );
				}
			} elseif ( strpos( $cache_key, 'posts' ) !== false ) {
				set_transient( $this->feed_id, $encrypted_cache_value, $this->cache_time );
			} elseif ( strpos( $cache_key, 'header' ) !== false ) {
				set_transient( $this->feed_id, $encrypted_cache_value, $this->cache_time );
			}

			return 1;
		}

		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'cff_feed_caches';

		$sql = $wpdb->prepare( "
			SELECT * FROM $cache_table_name
			WHERE feed_id = %d
			AND cache_key = %s", $this->feed_id, $cache_key );

		$existing = $wpdb->get_results( $sql, ARRAY_A );
		$data = array();
		$where = array();
		$format = array();

		$data['cache_value'] = $this->maybe_encrypt( $cache_value );
		$format[] = '%s';

		$data['last_updated'] = date( 'Y-m-d H:i:s' );
		$format[] = '%s';

		if ( ! empty( $existing[0] ) ) {
			$where['feed_id'] = $this->feed_id;
			$where_format[] = '%d';

			$where['cache_key'] = $cache_key;
			$where_format[] = '%s';

			$affected = $wpdb->update( $cache_table_name, $data, $where, $format, $where_format );
		} else {
			$data['cache_key'] = $cache_key;
			$format[] = '%s';

			$data['cron_update'] = $cron_update === true ? 'yes' : '';
			$format[] = '%s';

			$data['feed_id'] = $this->feed_id;
			$format[] = '%d';

			$affected = $wpdb->insert( $cache_table_name, $data, $format );
		}

		return $affected;
	}

	/**
	 * Tasks to do after a new set of posts are retrieved
	 *
	 * @since 4.0
	 */
	public function after_new_posts_retrieved() {
		if ( $this->page === 1 ) {
			$this->clear( 'all' );
		}
	}

	/**
	 * Resets caches after they expire
	 *
	 * @param string $type
	 *
	 * @return bool|false|int
	 */
	public function clear( $type ) {
		$this->clear_wp_cache();

		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'cff_feed_caches';

		if ( $type === 'all' ) {
			$affected = $wpdb->query( $wpdb->prepare(
				"UPDATE $cache_table_name
				SET cache_value = ''
				WHERE feed_id = %d
				AND cache_key NOT IN ( 'posts', 'posts_backup', 'header_backup' );",
				$this->feed_id ) );
		} else {

			$data = [ 'cache_value' => '' ];
			$format = [ '%s' ];

			$where['feed_id'] = $this->feed_id;
			$where_format[] = '%d';

			$where['cache_key'] = $type . $this->suffix;
			$where_format[] = '%s';

			$affected = $wpdb->update( $cache_table_name, $data, $where, $format, $where_format );
		}

		return $affected;
	}

	/**
	 * Clears caches in the WP Options table used mostly by legacy feeds.
	 */
	public static function clear_legacy(){
		global $wpdb;

		$cache_table_name = $wpdb->prefix . 'cff_feed_caches';

		$sql = "
		    UPDATE $cache_table_name
		    SET cache_value = ''
		    WHERE cache_key = 'posts';";
		$wpdb->query( $sql );

		$table_name = $wpdb->prefix . "options";
		$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_%')
        " );
		$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_ej\_%')
        " );
		$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_tle\_%')
        " );
		$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_cff\_album\_%')
        " );
		$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_cff\_%')
        " );

	}

	/**
	 * Clears caches in the CFF Feed Caches table.
	 */
	public static function clear_all_builder(){
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'cff_feed_caches';

		$affected = $wpdb->query( $wpdb->prepare(
			"UPDATE $cache_table_name
			SET cache_value = '', last_updated = '0000-00-00 00:00:00'
			WHERE cron_update = %s;",
			'yes' ) );

		return $affected;
	}

	/**
	 * Clears popular page caching solutions
	 */
	public static function clear_page_caches(){
		//Clear cache of major caching plugins
		if(isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')){
			$GLOBALS['wp_fastest_cache']->deleteCache();
		}
		//WP Super Cache
		if (function_exists('wp_cache_clear_cache')) {
			wp_cache_clear_cache();
		}
		//W3 Total Cache
		if (function_exists('w3tc_flush_all')) {
			w3tc_flush_all();
		}
		if (function_exists('sg_cachepress_purge_cache')) {
			sg_cachepress_purge_cache();
		}

		// Litespeed Cache (older method)
		if ( method_exists( 'LiteSpeed_Cache_API', 'purge' ) ) {
			LiteSpeed_Cache_API::purge( 'esi.custom-facebook-feed' );
		}

		// Litespeed Cache (new method)
		if(has_action('litespeed_purge')) {
			do_action( 'litespeed_purge', 'esi.custom-facebook-feed' );
		}
	}

	/**
	 * Get all available caches from the cff_cache table.
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	private function query_cff_feed_caches() {
		$feed_cache = wp_cache_get( $this->get_wp_cache_key() );
		if ( false === $feed_cache ) {
			global $wpdb;
			$cache_table_name = $wpdb->prefix . 'cff_feed_caches';

			if ( $this->page === 1 ) {
				$sql = $wpdb->prepare( "
				SELECT * FROM $cache_table_name
				WHERE feed_id = %s", $this->feed_id );
			} else {
				$sql = $wpdb->prepare( "
				SELECT * FROM $cache_table_name
				WHERE feed_id = %s
				AND cache_key IN ( 'posts', %s, %s, %s )",
					$this->feed_id,
					'posts_' . $this->page,
					'resized_images_' . $this->page,
					'meta_' . $this->page  );
			}

			$feed_cache = $wpdb->get_results( $sql, ARRAY_A );

			wp_cache_set( $this->get_wp_cache_key(), $feed_cache );
		}

		return $feed_cache;
	}

	/**
	 * Delete the wp_cache
	 *
	 * @since 4.0
	 */
	private function clear_wp_cache() {
		wp_cache_delete( $this->get_wp_cache_key() );
	}

	/**
	 * Key used to get the wp cache key
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	private function get_wp_cache_key() {
		return 'cff_feed_'. $this->feed_id . '_' . $this->page;
	}
	/**
	 * Uses a raw value and attempts to encrypt it
	 *
	 * @param $value
	 *
	 * @return bool|string
	 */
	private function maybe_encrypt( $value ) {
		if ( ! empty( $value ) && ! is_string( $value ) ) {
			$value = cff_json_encode( $value );
		}
		if ( empty( $value ) ) {
			return $value;
		}

		return $this->encryption->encrypt( $value );
	}

	/**
	 * Uses a raw value and attempts to decrypt it
	 *
	 * @param $value
	 *
	 * @return bool|string
	 */
	private function maybe_decrypt( $value ) {
		if ( ! is_string( $value ) ) {
			return $value;
		}
		if ( strpos( $value, '{' ) === 0 ) {
			return $value;
		}

		$decrypted = $this->encryption->decrypt( $value );

		if ( ! $decrypted ) {
			return $value;
		}

		return $decrypted;
	}
}