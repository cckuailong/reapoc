<?php
namespace WPO\WC\PDF_Invoices\Compatibility;

/**
 * Derived from SkyVerge WooCommerce Plugin Framework https://github.com/skyverge/wc-plugin-framework/
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\\WPO\\WC\\PDF_Invoices\\Compatibility\\WC_Core' ) ) :

/**
 * WooCommerce Compatibility Utility Class
 *
 * The unfortunate purpose of this class is to provide a single point of
 * compatibility functions for dealing with supporting multiple versions
 * of WooCommerce and various extensions.
 *
 * The expected procedure is to remove methods from this class, using the
 * latest ones directly in code, as support for older versions of WooCommerce
 * are dropped.
 *
 * Current Compatibility
 * + Core 2.5.5 - 3.0.x
 *
 *
 * @since 2.0.0
 */
class WC_Core {

	/**
	 * Backports wc_get_order() to pre-2.2.0
	 *
	 * @since 4.3.0
	 * @return \WC_Order $order order object
	 */
	public static function get_order( $order_id ) {

		if ( function_exists( 'wc_get_order' ) ) {

			return wc_get_order( $order_id );

		} else {

			return new \WC_Order( $order_id );
		}
	}


	/**
	 * Backports wc_checkout_is_https() to 2.4.x
	 *
	 * @since 4.3.0
	 * @return bool
	 */
	public static function wc_checkout_is_https() {

		if ( self::is_wc_version_gte_2_5() ) {

			return wc_checkout_is_https();

		} else {

			return wc_site_is_https() || 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) || class_exists( 'WordPressHTTPS' ) || strstr( wc_get_page_permalink( 'checkout' ), 'https:' );
		}
	}


	/**
	 * Backports wc_help_tip() to WC 2.4.x
	 *
	 * @link https://github.com/woothemes/woocommerce/pull/9417
	 *
	 * @since 4.2.0
	 * @param string $tip help tip content, HTML allowed if $has_html is true
	 * @param bool $has_html false by default, true to indicate tip content has HTML
	 * @return string help tip HTML, a <span> in WC 2.5, <img> in WC 2.4
	 */
	public static function wc_help_tip( $tip, $has_html = false ) {

		if ( self::is_wc_version_gte_2_5() ) {

			return wc_help_tip( $tip, $has_html );

		} else {

			$tip = $has_html ? wc_sanitize_tooltip( $tip ) : esc_attr( $tip );

			return sprintf( '<img class="help_tip" data-tip="%1$s" src="%2$s" height="16" width="16" />', $tip, esc_url( WC()->plugin_url() ) . '/assets/images/help.png' );
		}
	}


	/**
	 * Helper method to get the version of the currently installed WooCommerce
	 *
	 * @since 3.0.0
	 * @return string woocommerce version number or null
	 */
	protected static function get_wc_version() {

		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}


	/**
	 * Determines if the installed version of WooCommerce is 2.2.0 or greater.
	 *
	 * @since 4.2.0
	 * @return bool
	 */
	public static function is_wc_version_gte_2_2() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.2', '>=' );
	}


	/**
	 * Determines if the installed version of WooCommerce is less than 2.2.0
	 *
	 * @since 4.2.0
	 * @return bool
	 */
	public static function is_wc_version_lt_2_2() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.2', '<' );
	}


	/**
	 * Determines if the installed version of WooCommerce is 2.5.0 or greater.
	 *
	 * @since 4.2.0
	 * @return bool
	 */
	public static function is_wc_version_gte_2_5() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.5', '>=' );
	}


	/**
	 * Determines if the installed version of WooCommerce is less than 2.5.0
	 *
	 * @since 4.2.0
	 * @return bool
	 */
	public static function is_wc_version_lt_2_5() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.5', '<' );
	}


	/**
	 * Determines if the installed version of WooCommerce is 2.6.0 or greater.
	 *
	 * @since 4.4.0
	 * @return bool
	 */
	public static function is_wc_version_gte_2_6() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.6', '>=' );
	}


	/**
	 * Determines if the installed version of WooCommerce is less than 2.6.0
	 *
	 * @since 4.4.0
	 * @return bool
	 */
	public static function is_wc_version_lt_2_6() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.6', '<' );
	}


	/**
	 * Determines if the installed version of WooCommerce is 3.0.0 or greater.
	 *
	 * @since 4.6.0-dev
	 * @return bool
	 */
	public static function is_wc_version_gte_3_0() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '3.0', '>=' );
	}


	/**
	 * Determines if the installed version of WooCommerce is less than 3.0.0
	 *
	 * @since 4.6.0-dev
	 * @return bool
	 */
	public static function is_wc_version_lt_3_0() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '3.0', '<' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than $version
	 *
	 * @since 2.0.0
	 * @param string $version the version to compare
	 * @return boolean true if the installed version of WooCommerce is > $version
	 */
	public static function is_wc_version_gt( $version ) {
		return self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
	}


	/** WordPress core ******************************************************/


	/**
	 * Normalizes a WooCommerce page screen ID.
	 *
	 * Needed because WordPress uses a menu title (which is translatable), not slug, to generate screen ID.
	 * See details in: https://core.trac.wordpress.org/ticket/21454
	 * TODO: Add WP version check when https://core.trac.wordpress.org/ticket/18857 is addressed {BR 2016-12-12}
	 *
	 * @since 4.6.0-dev
	 * @param string $slug The slug for the screen ID to normalize (minus `woocommerce_page_`).
	 * @return string Normalized screen ID.
	 */
	public static function normalize_wc_screen_id( $slug = 'wc-settings' ) {

		// The textdomain usage is intentional here, we need to match the menu title.
		$prefix = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );

		return $prefix . '_page_' . $slug;
	}


}


endif; // Class exists check
