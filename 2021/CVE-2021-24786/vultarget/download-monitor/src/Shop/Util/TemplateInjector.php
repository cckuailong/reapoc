<?php

namespace Never5\DownloadMonitor\Shop\Util;

use Never5\DownloadMonitor\Shop\Services\Services;

class TemplateInjector {

	/**
	 * Init
	 */
	public function init() {
		add_filter( 'the_content', array( $this, 'inject_singular_content' ) );
	}

	/**
	 * Inject vehicle singular content into singular page
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function inject_singular_content( $content ) {
		global $post;

		// check if we need to inject
		if ( ! is_singular( PostType::KEY ) || ! in_the_loop() ) {
			return $content;
		}

		// remove filter to prevent crazy loops
		remove_filter( 'the_content', array( $this, 'inject_singular_content' ) );

		// check if vehicle actually the post type that's being looped
		if ( PostType::KEY === $post->post_type ) {

			try {

				$product = Services::get()->service( 'product_repository' )->retrieve_single( $post->ID );

				ob_start();

				/**
				 * dlm_product_before_single_content hook
				 */
				do_action( 'dlm_product_before_single_content', $product );

				// load content-single-product
				download_monitor()->service( 'template_handler' )->get_template_part( 'shop/content', 'single-product', '', array(
					'product' => $product
				) );

				/**
				 * dlm_product_after_single_content hook
				 */
				do_action( 'dlm_product_after_single_content', $product );

				// set new content
				$content = ob_get_clean();

			} catch ( \Exception $exception ) {

			}


		}

		// add filter back in place
		add_filter( 'the_content', array( $this, 'inject_singular_content' ) );

		// return content
		return apply_filters( 'dlm_content_single_product', $content, $post );
	}

}