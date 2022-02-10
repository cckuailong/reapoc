<?php
/**
 * Yoast i18n module API class
 *
 * @since             1.5.0
 * @package           TInvWishlist\API
 * @subpackage        Yoast-i18n-module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'TInvWL_Yoast_I18n_v2' ) ) {
	require_once TINVWL_PATH . 'includes/api/yoasti18n/i18n-module.php';
}

if ( ! class_exists( 'TInvWL_Yoast_I18n_WordPressOrg_v2' ) ) {
	require_once TINVWL_PATH . 'includes/api/yoasti18n/i18n-module-wordpressorg.php';
}


/**
 * Yoast i18n module API class
 */
class TInvWL_Includes_API_Yoasti18n {

	/**
	 * Self object
	 *
	 * @var \TInvWL_Includes_API_Yoasti18n
	 */
	protected static $_instance;
	/**
	 * Initiated Yoast I18n module
	 *
	 * @var \TInvWL_Yoast_I18n_WordPressOrg_V2
	 */
	public $i18n;

	/**
	 * Create object
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 *
	 * @return /TInvWL_Includes_API_Yoasti18n
	 */
	public static function instance( $plugin_name = '', $version = '' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $version );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	function __construct() {
		if ( is_admin() ) {
			$this->load_i18n();
		}
	}

	/**
	 * Initiate Yoast I18n module
	 */
	public function load_i18n() {
		if ( empty( $this->i18n ) ) {
			$this->i18n = new TInvWL_Yoast_I18n_WordPressOrg_V2( array(
				'textdomain'  => 'ti-woocommerce-wishlist',
				'plugin_name' => 'WooCommerce Wishlist Plugin',
				'hook'        => 'tinvwl_view_header',
			) );
		}

		return $this->i18n;
	}
}
