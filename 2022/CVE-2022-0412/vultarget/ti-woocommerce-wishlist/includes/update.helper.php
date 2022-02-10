<?php
/**
 * Update plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Update plugin class
 */
class TInvWL_Update {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * Version
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Previous Version
	 *
	 * @var string
	 */
	public $_prev;

	/**
	 * Regular expression for sorting version function
	 *
	 * @var string
	 */
	const REGEXP = '/^up_/i';

	/**
	 * Get update methods and apply
	 *
	 * @param string $version Version.
	 * @param string $previous_version Previous Version.
	 *
	 * @return boolean
	 */
	function __construct( $version, $previous_version = 0 ) {
		$lists          = get_class_methods( $this );
		$this->_name    = TINVWL_PREFIX;
		$this->_version = $version;
		$this->_prev    = $previous_version;
		$lists          = array_filter( $lists, array( $this, 'filter' ) );
		if ( empty( $lists ) ) {
			return false;
		}
		uasort( $lists, array( $this, 'sort' ) );
		foreach ( $lists as $method ) {
			call_user_func( array( $this, $method ), $previous_version );
		}

		return true;
	}

	/**
	 * Filter methods
	 *
	 * @param string $method Method name from this class.
	 *
	 * @return boolean
	 */
	public function filter( $method ) {
		if ( ! preg_match( self::REGEXP, $method ) ) {
			return false;
		}
		if ( version_compare( $this->_prev, $this->prepare( $method ), 'ge' ) ) {
			return false;
		}

		return version_compare( $this->_version, $this->prepare( $method ), 'ge' );
	}

	/**
	 * Sort methods
	 *
	 * @param string $method1 Method name first from this class.
	 * @param string $method2 Method name second from this class.
	 *
	 * @return type
	 */
	public function sort( $method1, $method2 ) {
		return version_compare( $this->prepare( $method1 ), $this->prepare( $method2 ) );
	}

	/**
	 * Conver method name to version
	 *
	 * @param string $method Method name from this class.
	 *
	 * @return string
	 */
	public function prepare( $method ) {
		$method = preg_replace( self::REGEXP, '', $method );
		$method = str_replace( '_', '.', $method );

		return $method;
	}

	/**
	 * Example of the method updating
	 *
	 * @param string $previous_version Previous Version.
	 */
	function up_0_0_0( $previous_version = 0 ) {

	}

	/**
	 * Set runed wizard
	 *
	 * @param string $previous_version Previous version value.
	 */
	function up_1_1_10_1( $previous_version = 0 ) {
		update_option( 'tinvwl_wizard', true );
	}

	/**
	 * Fix name field
	 */
	function up_p_1_5_4() {
		if ( $value = tinv_get_option( 'product_table', 'add_to_card' ) ) {
			tinv_update_option( 'product_table', 'add_to_cart', $value );
		}
		if ( $value = tinv_get_option( 'product_table', 'text_add_to_card' ) ) {
			tinv_update_option( 'product_table', 'text_add_to_cart', $value );
		}
		if ( $value = tinv_get_option( 'table', 'add_select_to_card' ) ) {
			tinv_update_option( 'table', 'add_select_to_cart', $value );
		}
		if ( $value = tinv_get_option( 'table', 'text_add_select_to_card' ) ) {
			tinv_update_option( 'table', 'text_add_select_to_cart', $value );
		}
		if ( $value = tinv_get_option( 'table', 'add_all_to_card' ) ) {
			tinv_update_option( 'table', 'add_all_to_cart', $value );
		}
		if ( $value = tinv_get_option( 'table', 'text_add_all_to_card' ) ) {
			tinv_update_option( 'table', 'text_add_all_to_cart', $value );
		}
	}

	/**
	 * Clean up empty wishlists.
	 */
	function up_p_1_6_1() {
		global $wpdb;
		$wishlists_table       = sprintf( '%s%s_%s', $wpdb->prefix, $this->_name, 'lists' );
		$wishlists_items_table = sprintf( '%s%s_%s', $wpdb->prefix, $this->_name, 'items' );
		$sql                   = "DELETE FROM wl USING `{$wishlists_table}` AS wl WHERE NOT EXISTS( SELECT * FROM `{$wishlists_items_table}` WHERE {$wishlists_items_table}.wishlist_id = wl.ID ) AND wl.type='default'";
		$wpdb->get_results( $sql, ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
	}

	/**
	 * Buttons class fallback.
	 */
	function up_p_1_8_9() {
		$class = tinv_get_option( 'add_to_wishlist_catalog', 'class' );
		if ( 'button' == tinv_get_option( 'add_to_wishlist_catalog', 'type' ) && empty( $class ) ) {
			tinv_update_option( 'add_to_wishlist_catalog', 'class', 'button tinvwl-button' );
		}

		$class = tinv_get_option( 'add_to_wishlist', 'class' );
		if ( 'button' == tinv_get_option( 'add_to_wishlist', 'type' ) && empty( $class ) ) {
			tinv_update_option( 'add_to_wishlist', 'class', 'button tinvwl-button' );
		}
	}

	/**
	 * Buttons class fallback.
	 */
	function up_1_16_1() {
		{
			wp_schedule_single_event( time(), 'tinvwl_flush_rewrite_rules' );
		}
	}
}
