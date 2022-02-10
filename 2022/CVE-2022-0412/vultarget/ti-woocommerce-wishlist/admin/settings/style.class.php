<?php
/**
 * Admin settings class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 * @subpackage        Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin settings class
 */
class TInvWL_Admin_Settings_Style extends TInvWL_Admin_BaseStyle {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 100;

	/**
	 * This class
	 *
	 * @var \TInvWL_Admin_Settings_Style
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Admin_Settings_Style
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
			'title'      => __( 'Style Options', 'ti-woocommerce-wishlist' ),
			'page_title' => __( 'Wishlist Style Options', 'ti-woocommerce-wishlist' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'style-settings',
			'capability' => 'tinvwl_style_settings',
		);
	}

	/**
	 * The modifiable attributes for the Default theme
	 *
	 * @return array
	 */
	function default_style_settings() {
		$font_family = apply_filters( 'tinvwl_prepare_fonts', array(
			'inherit'                                                            => __( 'Use Default Font', 'ti-woocommerce-wishlist' ),
			'Georgia, serif'                                                     => 'Georgia',
			"'Times New Roman', Times, serif"                                    => 'Times New Roman, Times',
			'Arial, Helvetica, sans-serif'                                       => 'Arial, Helvetica',
			"'Courier New', Courier, monospace"                                  => 'Courier New, Courier',
			"Georgia, 'Times New Roman', Times, serif"                           => 'Georgia, Times New Roman, Times',
			'Verdana, Arial, Helvetica, sans-serif'                              => 'Verdana, Arial, Helvetica',
			'Geneva, Arial, Helvetica, sans-serif'                               => 'Geneva, Arial, Helvetica',
			"'Source Sans Pro', 'Open Sans', sans-serif"                         => 'Source Sans Pro, Open Sans',
			"'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif"             => 'Helvetica Neue, Helvetica, Roboto, Arial',
			'Arial, sans-serif'                                                  => 'Arial',
			"'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif" => 'Lucida Grande, Verdana, Arial, Bitstream Vera Sans',
		) );

		return array(
			array(
				'type'       => 'group',
				'title'      => __( 'text', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .tinv-header h2',
				'element'  => 'color',
				'text'     => __( 'Title Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-header h2',
				'element'  => 'font-size',
				'text'     => __( 'Title Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist',
				'element'  => 'color',
				'text'     => __( 'Content Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist, .tinv-wishlist input, .tinv-wishlist select, .tinv-wishlist textarea, .tinv-wishlist button, .tinv-wishlist input[type="button"], .tinv-wishlist input[type="reset"], .tinv-wishlist input[type="submit"]',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'Product Title link', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist a:not(.button):not(.social)',
				'element'  => 'color',
				'text'     => __( 'Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist a:not(.button):not(.social):active, .tinv-wishlist a:not(.button):not(.social):focus, .tinv-wishlist a:not(.button):not(.social):hover',
				'element'  => 'color',
				'text'     => __( 'Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist a:not(.button):not(.social)',
				'element'  => 'text-decoration',
				'text'     => __( 'Underline', 'ti-woocommerce-wishlist' ),
				'options'  => array(
					'underline'       => __( 'Yes', 'ti-woocommerce-wishlist' ),
					'none !important' => __( 'No', 'ti-woocommerce-wishlist' ),
				),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist a:not(.button):not(.social)',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'fields', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist input[type="text"], .tinv-wishlist input[type="email"], .tinv-wishlist input[type="url"], .tinv-wishlist input[type="password"], .tinv-wishlist input[type="search"], .tinv-wishlist input[type="tel"], .tinv-wishlist input[type="number"], .tinv-wishlist textarea, .tinv-wishlist select, .tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist input[type="text"], .tinv-wishlist input[type="email"], .tinv-wishlist input[type="url"], .tinv-wishlist input[type="password"], .tinv-wishlist input[type="search"], .tinv-wishlist input[type="tel"], .tinv-wishlist input[type="number"], .tinv-wishlist textarea, .tinv-wishlist select, .tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'border-color',
				'text'     => __( 'Border Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist input[type="text"], .tinv-wishlist input[type="email"], .tinv-wishlist input[type="url"], .tinv-wishlist input[type="password"], .tinv-wishlist input[type="search"], .tinv-wishlist input[type="tel"], .tinv-wishlist input[type="number"], .tinv-wishlist textarea, .tinv-wishlist select, .tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist input[type="text"], .tinv-wishlist input[type="email"], .tinv-wishlist input[type="url"], .tinv-wishlist input[type="password"], .tinv-wishlist input[type="search"], .tinv-wishlist input[type="tel"], .tinv-wishlist input[type="number"], .tinv-wishlist textarea, .tinv-wishlist select, .tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist input[type="text"], .tinv-wishlist input[type="email"], .tinv-wishlist input[type="url"], .tinv-wishlist input[type="password"], .tinv-wishlist input[type="search"], .tinv-wishlist input[type="tel"], .tinv-wishlist input[type="number"], .tinv-wishlist textarea, .tinv-wishlist select, .tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist select',
				'element'  => 'font-size',
				'text'     => __( 'Select Font Size', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( '"Add to Wishlist" product page button', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:hover, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button.icon-white:hover:before, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button.icon-black:hover:before',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button.icon-white:before, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button.icon-black:before',
				'element'  => 'color',
				'text'     => __( 'Button Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:hover, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button.icon-white:hover:before, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button.icon-black:hover:before',
				'element'  => 'color',
				'text'     => __( 'Button Text Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.icon-white:before, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.icon-black:before',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button:hover, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.icon-white:hover:before, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.icon-black:hover:before',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'     => 'select',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.icon-black:before, .woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.icon-white:before',
				'element'  => 'font-size',
				'text'     => __( 'Icon Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( '"Add to Wishlist" product listing button', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-white:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-black:before',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:hover, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-white:hover:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-black:hover:before',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-white:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-black:before',
				'element'  => 'color',
				'text'     => __( 'Button Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:hover, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-white:hover:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button.icon-black:hover:before',
				'element'  => 'color',
				'text'     => __( 'Button Text Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-white:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-black:before',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button:hover, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-white:hover:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-black:hover:before',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'     => 'select',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-white:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-black:before',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-white:before, .woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.icon-black:before',
				'element'  => 'font-size',
				'text'     => __( 'Icon Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( '"Apply Action" button ', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit, .woocommerce.tinv-wishlist a.button, .woocommerce.tinv-wishlist button.button, .woocommerce.tinv-wishlist input.button',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit:hover, .woocommerce.tinv-wishlist a.button:hover, .woocommerce.tinv-wishlist button.button:hover, .woocommerce.tinv-wishlist input.button:hover',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit, .woocommerce.tinv-wishlist a.button, .woocommerce.tinv-wishlist button.button, .woocommerce.tinv-wishlist input.button',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit:hover, .woocommerce.tinv-wishlist a.button:hover, .woocommerce.tinv-wishlist button.button:hover, .woocommerce.tinv-wishlist input.button:hover',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit, .woocommerce.tinv-wishlist a.button, .woocommerce.tinv-wishlist button.button, .woocommerce.tinv-wishlist input.button',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit, .woocommerce.tinv-wishlist a.button, .woocommerce.tinv-wishlist button.button, .woocommerce.tinv-wishlist input.button',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit, .woocommerce.tinv-wishlist a.button, .woocommerce.tinv-wishlist button.button, .woocommerce.tinv-wishlist input.button',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'add to cart button', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt, .woocommerce.tinv-wishlist a.button.alt, .woocommerce.tinv-wishlist button.button.alt, .woocommerce.tinv-wishlist input.button.alt',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt:hover, .woocommerce.tinv-wishlist a.button.alt:hover, .woocommerce.tinv-wishlist button.button.alt:hover, .woocommerce.tinv-wishlist input.button.alt:hover',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt, .woocommerce.tinv-wishlist a.button.alt, .woocommerce.tinv-wishlist button.button.alt, .woocommerce.tinv-wishlist input.button.alt',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt:hover, .woocommerce.tinv-wishlist a.button.alt:hover, .woocommerce.tinv-wishlist button.button.alt:hover, .woocommerce.tinv-wishlist input.button.alt:hover',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt, .woocommerce.tinv-wishlist a.button.alt, .woocommerce.tinv-wishlist button.button.alt, .woocommerce.tinv-wishlist input.button.alt',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt, .woocommerce.tinv-wishlist a.button.alt, .woocommerce.tinv-wishlist button.button.alt, .woocommerce.tinv-wishlist input.button.alt',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt, .woocommerce.tinv-wishlist a.button.alt, .woocommerce.tinv-wishlist button.button.alt, .woocommerce.tinv-wishlist input.button.alt',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'table', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist table, .tinv-wishlist table td',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist table, .tinv-wishlist table td, .tinv-wishlist table th',
				'element'  => 'border-color',
				'text'     => __( 'Border Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'background-color',
				'text'     => __( 'Table Head Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'color',
				'text'     => __( 'Table Head Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'font-family',
				'text'     => __( 'Table Head Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'font-size',
				'text'     => __( 'Table Head Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist table td',
				'element'  => 'color',
				'text'     => __( 'Content Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist table td',
				'element'  => 'font-family',
				'text'     => __( 'Content Text Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist table td',
				'element'  => 'font-size',
				'text'     => __( 'Content Text Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist td.product-price',
				'element'  => 'color',
				'text'     => __( 'Price Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist td.product-price',
				'element'  => 'font-family',
				'text'     => __( 'Price Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist td.product-price',
				'element'  => 'font-size',
				'text'     => __( 'Price Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist td.product-price ins span.amount',
				'element'  => 'color',
				'text'     => __( 'Special Price Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist td.product-price ins span.amount',
				'element'  => 'background-color',
				'text'     => __( 'Special Price Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'       => 'group',
				'title'      => __( 'Share buttons', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .social-buttons li a',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .social-buttons li a:hover',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .social-buttons li a.white, .tinv-wishlist .social-buttons li a.dark',
				'element'  => 'color',
				'text'     => __( 'Icon Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .social-buttons li a.white:hover, .tinv-wishlist .social-buttons li a.dark:hover',
				'element'  => 'color',
				'text'     => __( 'Icon Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .social-buttons li a',
				'element'  => 'font-size',
				'text'     => __( 'Icon Size', 'ti-woocommerce-wishlist' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'popups', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner',
				'element'  => 'color',
				'text'     => __( 'Content Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner,.tinv-wishlist .tinv-modal .tinv-modal-inner select',
				'element'  => 'font-family',
				'text'     => __( 'Content Text Font', 'ti-woocommerce-wishlist' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner',
				'element'  => 'font-size',
				'text'     => __( 'Content Text Font Size', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-close-modal, .tinv-wishlist .tinv-modal button.button',
				'element'  => 'background-color',
				'text'     => __( 'Normal Buttons Background Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-close-modal:hover, .tinv-wishlist .tinv-modal button.button:hover',
				'element'  => 'background-color',
				'text'     => __( 'Normal Buttons Background Hover Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-close-modal, .tinv-wishlist .tinv-modal button.button',
				'element'  => 'color',
				'text'     => __( 'Normal Buttons Text Color', 'ti-woocommerce-wishlist' ),
			),
			array(
				'type'     => 'color',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-close-modal:hover, .tinv-wishlist .tinv-modal button.button:hover',
				'element'  => 'color',
				'text'     => __( 'Normal Buttons Text Hover Color', 'ti-woocommerce-wishlist' ),
			),
		);
	}
}
