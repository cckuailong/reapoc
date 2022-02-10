<?php
/**
 * Social actions buttons functional
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Social actions buttons functional
 */
class TInvWL_Public_Wishlist_Social {

	/**
	 * Share url this wishlist
	 *
	 * @var string
	 */
	static $url;

	/**
	 * Image url
	 *
	 * @var string
	 */
	static $image;

	/**
	 * First run method
	 *
	 * @param array $wishlist Set from action.
	 *
	 * @return boolean
	 */
	public static function init( $wishlist ) {
		if ( empty( $wishlist ) || 'private' === $wishlist['status'] ) {
			return false;
		}

		self::$image = TInvWL_Public_Wishlist_View::instance()->social_image;
		self::$url   = TInvWL_Public_Wishlist_View::instance()->wishlist_url;

		self::htmloutput( $wishlist );
	}

	/**
	 * Output social buttons
	 *
	 * @param array $wishlist Set from action.
	 */
	public static function htmloutput( $wishlist ) {

		$social = tinv_get_option( 'social' );

		$share_on = apply_filters( 'tinvwl_share_on_text', tinv_get_option( 'social', 'share_on' ) );

		foreach ( $social as $name => $soc_network ) {
			if ( $soc_network && method_exists( __CLASS__, $name ) ) {
				$social[ $name ]        = self::$name();
				$social_titles[ $name ] = self::$name( true );
				if ( 'clipboard' === $name ) {
					wp_enqueue_script( 'tinvwl-clipboard' );
				}
			} else {
				$social[ $name ] = '';
			}
		}

		$social = apply_filters( 'tinvwl_view_social', $social, array(
			'wishlist' => $wishlist,
			'image'    => self::$image,
			'url'      => self::$url,
		) );
		$social = array_filter( $social );
		if ( empty( $social ) ) {
			return false;
		}
		$data = array(
			'social'        => $social,
			'social_titles' => $social_titles,
			'share_on'      => $share_on,
		);
		tinv_wishlist_template( 'ti-wishlist-social.php', $data );
	}

	/**
	 * Create facebook share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function facebook( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Facebook', 'ti-woocommerce-wishlist' );
		}

		$data = array(
			'u' => self::$url,
		);

		return 'https://www.facebook.com/sharer/sharer.php?' . http_build_query( $data );
	}

	/**
	 * Create twitter share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function twitter( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Twitter', 'ti-woocommerce-wishlist' );
		}

		$data = array(
			'url' => self::$url,
		);

		return 'https://twitter.com/share?' . http_build_query( $data );
	}

	/**
	 * Create pinterest share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function pinterest( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Pinterest', 'ti-woocommerce-wishlist' );
		}

		$data = array(
			'url'   => self::$url,
			'media' => self::$image,
		);

		return 'http://pinterest.com/pin/create/button/?' . http_build_query( $data );
	}

	/**
	 * Create email share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function email( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Email', 'ti-woocommerce-wishlist' );
		}

		$data = array(
			'body' => self::$url,
		);

		return 'mailto:?' . http_build_query( $data );
	}

	/**
	 * Create copy to clipboard url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function clipboard( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Clipboard', 'ti-woocommerce-wishlist' );
		}

		return self::$url;
	}

	/**
	 * Create WhatsApp share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function whatsapp( $title = false ) {
		if ( $title ) {
			return esc_html__( 'WhatsApp', 'ti-woocommerce-wishlist' );
		}

		$data = array(
			'text' => self::$url,
		);

		return 'https://api.whatsapp.com/send?' . http_build_query( $data );
	}
}
