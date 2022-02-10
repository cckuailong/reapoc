<?php
/**
 * Admin settings class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 * @subpackage        Upgrade page
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin settings class
 */
class TInvWL_Admin_Settings_Upgrade extends TInvWL_Admin_BaseSection {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 200;

	/**
	 * This class
	 *
	 * @var \TInvWL_Admin_Settings_Upgrade
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Admin_Settings_Upgrade
	 */
	public static function instance( $plugin_name = TINVWL_PREFIX, $plugin_version = TINVWL_FVERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $plugin_version );
		}

		return self::$_instance;
	}

	/**
	 * Menu array
	 *
	 * @return array
	 */
	function menu() {
		return array(
			'title'      => __( 'Upgrade to Premium', 'ti-woocommerce-wishlist' ),
			'page_title' => __( 'Premium Features', 'ti-woocommerce-wishlist' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'upgrade',
			'capability' => 'tinvwl_upgrade',
		);
	}
}
