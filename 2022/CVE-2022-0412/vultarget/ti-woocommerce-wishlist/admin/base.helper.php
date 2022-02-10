<?php
/**
 * Basic admin helper class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Helper
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Basic admin helper class
 */
abstract class TInvWL_Admin_Base {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	public $_name;
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		$this->load_function();
	}

	/**
	 * Load function
	 */
	function load_function() {

	}

	/**
	 * Formatted admin url.
	 *
	 * @param string $page Page title.
	 * @param string $cat Category title.
	 * @param array $arg Arguments array.
	 *
	 * @return string
	 */
	public function admin_url( $page, $cat = '', $arg = array() ) {
		$protocol = is_ssl() ? 'https' : 'http';
		$glue     = '-';
		$params   = array(
			'page' => implode( $glue, array_filter( array( $this->_name, $page ) ) ),
			'cat'  => $cat,
		);
		if ( is_array( $arg ) ) {
			$params = array_merge( $params, $arg );
		}
		$params = array_filter( $params );
		$params = http_build_query( $params );
		if ( is_string( $arg ) ) {
			$params = $params . '&' . $arg;
		}

		return admin_url( sprintf( 'admin.php?%s', $params ), $protocol );
	}

	/**
	 * Basic print admin page. By attributes page and cat, determined sub function for print
	 *
	 * @return boolean
	 */
	public function _print_() {

		$default = 'general';
		$params  = filter_input_array( INPUT_GET, array(
			'page' => FILTER_SANITIZE_STRING,
			'cat'  => FILTER_SANITIZE_STRING,
			'id'   => FILTER_VALIDATE_INT,
		) );
		extract( $params ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.extract

		$glue      = '-';
		$page      = explode( $glue, $page );
		$page_last = array_shift( $page );
		if ( $this->_name != $page_last ) { // WPCS: loose comparison ok.
			return false;
		}

		$cat  = empty( $cat ) ? $default : $cat;
		$glue = '_';
		array_push( $page, $cat );
		$cat           = implode( $glue, $page );
		$function_name = __FUNCTION__ . $cat;

		if ( method_exists( $this, $function_name ) && __FUNCTION__ != $function_name ) { // WPCS: loose comparison ok.
			return $this->$function_name();
		} else {
			$function_name = __FUNCTION__ . $default;
			if ( method_exists( $this, $function_name ) ) {
				return $this->$function_name( $cat );
			}
		}

		return false;
	}
}
