<?php

namespace Never5\DownloadMonitor\Shop\Shortcode;

use Never5\DownloadMonitor\Shop\Services\Services;

class Buy {

	/**
	 * Register the shortcode
	 */
	public function register() {
		add_shortcode( 'dlm_buy', array( $this, 'content' ) );
	}

	/**
	 * Shortcode content
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function content( $atts, $content = '' ) {

		// extract shortcode atts
		extract( shortcode_atts( array(
			'id'       => '',
			'autop'    => false,
			'template' => ''
		), $atts ) );

		// Check id
		if ( empty( $id ) ) {
			return "";
		}

		// shortcode output
		$output = '';

		// create download object
		try {
			/** @var \Never5\DownloadMonitor\Shop\Product\Product $download */
			$product = Services::get()->service( 'product_repository' )->retrieve_single( $id );

			$atc_url = Services::get()->service( 'page' )->get_add_to_cart_url( $product->get_id() );

			// if we have content, wrap in a link only
			if ( $content ) {
				$output = '<a href="' . $atc_url . '">' . $content . '</a>';
			} else {

				// buffer
				ob_start();

				// load template
				download_monitor()->service( 'template_handler' )->get_template_part( 'shop/button/add-to-cart', $template, '', array(
					'product' => $product,
					'atc_url' => $atc_url
				) );

				// get output
				$output = ob_get_clean();

				// check if we need to wpautop()
				if ( 'true' === $autop || true === $autop ) {
					$output = wpautop( $output );
				}
			}
		} catch ( \Exception $e ) {
			$output = '[' . __( 'Product not found', 'download-monitor' ) . ']';
		}

		return $output;

	}

}