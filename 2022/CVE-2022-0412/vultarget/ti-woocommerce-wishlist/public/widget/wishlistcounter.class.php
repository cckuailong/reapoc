<?php
/**
 * Widget "Popular product"
 *
 * @since             1.0.0
 * @package           TInvWishlist\Widget
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Widget "Popular product"
 */
class TInvWL_Public_Widget_WishlistCounter extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'tinvwl widget_wishlist_products_counter';
		$this->widget_description = __( 'Displays the number of products in the wishlist on your site.', 'ti-woocommerce-wishlist' );
		$this->widget_id          = 'widget_top_wishlist';
		$this->widget_name        = __( 'TI Wishlist Products Counter', 'ti-woocommerce-wishlist' );
		$this->settings           = array(
			'show_icon' => array(
				'type'  => 'checkbox',
				'std'   => ( (bool) tinv_get_option( 'topline', 'icon' ) ) ? 1 : 0,
				'label' => __( 'Show counter icon', 'ti-woocommerce-wishlist' ),
			),
			'show_text' => array(
				'type'  => 'checkbox',
				'std'   => tinv_get_option( 'topline', 'show_text' ) ? 1 : 0,
				'label' => __( 'Show counter text', 'ti-woocommerce-wishlist' ),
			),
			'text'      => array(
				'type'  => 'text',
				'std'   => apply_filters( 'tinvwl_wishlist_products_counter_text', tinv_get_option( 'topline', 'text' ) ),
				'label' => __( 'Counter Text', 'ti-woocommerce-wishlist' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @param array $args Artguments.
	 * @param array $instance Instance.
	 */
	public function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		foreach ( $instance as $key => $value ) {
			if ( 'on' === $value ) {
				$instance[ $key ] = 1;
			}
		}

		$this->widget_start( $args, $instance );
		$content = tinvwl_shortcode_products_counter( array(
			'show_icon' => isset( $instance['show_icon'] ) ? absint( $instance['show_icon'] ) : $this->settings['show_icon']['std'],
			'show_text' => isset( $instance['show_text'] ) ? absint( $instance['show_text'] ) : $this->settings['show_text']['std'],
			'text'      => isset( $instance['text'] ) ? $instance['text'] : $this->settings['text']['std'],
		) );
		echo $content; // WPCS: xss ok.
		$this->widget_end( $args, $instance );
		$this->cache_widget( $args, $content );
	}
}
