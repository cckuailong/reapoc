<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdupcpSEO' ) ) {
/**
 * Class to handle interactions with the Yoast SEO platform
 *
 * @since 5.0.0
 */
class ewdupcpSEO {

	public function __construct() {
		
		add_action( 'init', array( $this, 'add_hooks' ) );
	}

	/**
	 * Adds in the necessary hooks to handle WooCommerce integration
	 * @since 5.0.0
	 */
	public function add_hooks() {
		global $ewd_upcp_controller;

		if ( $ewd_upcp_controller->settings->get_setting( 'seo-plugin' ) != 'yoast' ) { return; }

		// @to-do: Deprecated, see https://github.com/Yoast/wordpress-seo/issues/14698 for how to replace
		//add_filter( 'wpseo_opengraph_image', 	array( $this, 'add_open_graph_image' ) );

		add_filter( 'wpseo_canonical', 			array( $this, 'change_canonical_url' ) );

		add_filter( 'wpseo_metadesc', 			array( $this, 'change_description' ) );
		add_filter( 'wpseo_opengraph_desc',		array( $this, 'change_description' ) );

		add_filter( 'wpseo_title', 				array( $this, 'change_title' ) );
		add_filter( 'wpseo_opengraph_title', 	array( $this, 'change_title' ) );
	}

	/**
	 * Adds the product's image to the open graph information (used by Facebook)
	 * @since 5.0.0
	 */
	public function add_open_graph_image( $image ) {
		global $ewd_upcp_controller;

		if ( empty( get_query_var('single_product') ) and empty( $_GET['singleproduct'] ) ) { return $image; }

		if ( ! empty( $_GET['singleproduct'] ) ) { $selected_product_id = intval( $_GET['singleproduct'] ); }
		if ( ! empty( get_query_var( 'single_product' ) ) ) { 

			$post = get_page_by_path( sanitize_text_field( trim( get_query_var( 'single_product' ), '/? ' ) ), OBJECT, EWD_UPCP_PRODUCT_POST_TYPE );

			$selected_product_id = ! empty( $post ) ? $post->ID : 0; 
		}

		if ( empty( $selected_product_id ) ) { return $image; }

		$product = new ewdupcpProduct();

		$product->load_post( $selected_product_id );

		return $product->get_main_image_url();

		//$GLOBALS['wpseo_og']->image_output( $product->get_main_image_url() );
	}

	/**
	 * Updates the canonical URL of the product, so that it points to the correct place
	 * @since 5.0.0
	 */
	public function change_canonical_url( $str ) {
		global $ewd_upcp_controller;

		if ( ! empty( get_query_var( 'single_product' ) ) ) {

			return $str . $ewd_upcp_controller->settings->get_setting( 'permalink-base' ) . '/' . get_query_var( 'single_product' ) . '/';
		}
		elseif ( ! empty( $_GET['singleproduct'] ) ) {

			return $str . "?singleproduct=" . intval( $_GET['singleproduct'] );
		}

		return $str;
	}

	/**
	 * Updates the SEO title of the page, when using the Yoast SEO plugin
	 * @since 5.0.0
	 */
	public function change_description( $str ) {
		global $ewd_upcp_controller;

		if ( ! empty( $_GET['singleproduct'] ) ) { $selected_product_id = intval( $_GET['singleproduct'] ); }
		if ( ! empty( get_query_var( 'single_product' ) ) ) { 

			$post = get_page_by_path( sanitize_text_field( trim( get_query_var( 'single_product' ), '/? ' ) ), OBJECT, EWD_UPCP_PRODUCT_POST_TYPE );

			$selected_product_id = ! empty( $post ) ? $post->ID : 0; 
		}
		
		if ( empty( $selected_product_id ) ) { return $str; }

		$str = $ewd_upcp_controller->settings->get_setting( 'seo-integration' ) == 'replace' ? get_post_meta( $selected_product_id, '_yoast_wpseo_metadesc',  true ) : $str . get_post_meta( $selected_product_id, '_yoast_wpseo_metadesc',  true );

		return $str;
	}

	/**
	 * Updates the SEO title of the page, when using the Yoast SEO plugin
	 * @since 5.0.0
	 */
	public function change_title( $str ) {
		global $ewd_upcp_controller;

		if ( ! empty( $_GET['singleproduct'] ) ) { $selected_product_id = intval( $_GET['singleproduct'] ); }
		if ( ! empty( get_query_var( 'single_product' ) ) ) { 

			$post = get_page_by_path( sanitize_text_field( trim( get_query_var( 'single_product' ), '/? ' ) ), OBJECT, EWD_UPCP_PRODUCT_POST_TYPE );

			$selected_product_id = ! empty( $post ) ? $post->ID : 0; 
		}
		
		if ( empty( $selected_product_id ) ) { return $str; }

		$product = new ewdupcpProduct();

		$product->load_post( $selected_product_id );

		$search = array(
			'[page-title]',
			'[product-name]',
			'[category-name]',
			'[subcategory-name]'
		);

		$replace = array(
			$str,
			$product->name,
			$product->get_category_names(),
			$product->get_subcategory_names()
		);

		return str_replace( $search, $replace, $ewd_upcp_controller->settings->get_setting( 'seo-title' ) );
	}
}

}