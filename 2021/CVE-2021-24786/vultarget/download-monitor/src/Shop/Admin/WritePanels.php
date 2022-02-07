<?php

namespace Never5\DownloadMonitor\Shop\Admin;

use Never5\DownloadMonitor\Shop\Product\Product;
use Never5\DownloadMonitor\Shop\Services\Services;
use Never5\DownloadMonitor\Shop\Util\PostType;

class WritePanels {

	/**
	 * Setup the actions
	 */
	public function setup() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );
		add_action( 'dlm_product_save', array( $this, 'save_meta_boxes' ), 1, 2 );
	}

	/**
	 * Add the meta boxes
	 */
	public function add_meta_box() {
		add_meta_box( 'download-monitor-product-info', __( 'Product Information', 'download-monitor' ), array(
			$this,
			'display_product_information'
		), PostType::KEY, 'normal', 'high' );
	}

	/**
	 * save_post function.
	 *
	 * @access public
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	public function save_post( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}
		if ( is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if ( empty( $_POST['dlm_product_nonce'] ) || ! wp_verify_nonce( $_POST['dlm_product_nonce'], 'save_meta_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( $post->post_type != PostType::KEY ) {
			return;
		}

		// unset nonce because it's only valid of 1 post
		unset( $_POST['dlm_product_nonce'] );

		do_action( 'dlm_product_save', $post_id, $post );
	}

	/**
	 * save function.
	 *
	 * @access public
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	public function save_meta_boxes( $post_id, $post ) {

		/**
		 * Fetch old download object
		 * There are certain props we don't need to manually persist here because WP does this automatically for us.
		 * These props are:
		 * - Product Title
		 * - Product Status
		 * - Product Author
		 * - Product Description & Excerpt
		 *
		 */
		/** @var Product $product */
		try {
			$product = Services::get()->service( 'product_repository' )->retrieve_single( $post_id );
		} catch ( \Exception $e ) {
			// product not found, no point in continuing
			return;
		}

		$product->set_price_from_user_input( $_POST['_dlm_price'] );
		$product->set_download_ids( $_POST['_dlm_downloads'] );

		// persist download
		Services::get()->service( 'product_repository' )->persist( $product );
	}

	/**
	 * @param \WP_Post $post
	 */
	public function display_product_information( $post ) {

		try {
			/** @var Product $product */
			$product = Services::get()->service( 'product_repository' )->retrieve_single( $post->ID );
		} catch ( \Exception $e ) {
			$product = Services::get()->service( 'product_factory' )->make();
		}

		$price     = "";
		$taxable   = false;
		$tax_class = "";

		$price = $product->get_price_for_user_input();

		/**
		 * Fetch downloads
		 */
		/** @todo fetch actual downloads */
		$downloads = download_monitor()->service( 'download_repository' )->retrieve( array(
			'orderby' => 'title',
			'order'   => 'ASC'
		) );

		wp_nonce_field( 'save_meta_data', 'dlm_product_nonce' );

		download_monitor()->service( 'view_manager' )->display( 'meta-box/product-information', array(
				'product'              => $product,
				'price'                => $price,
				'taxable'              => $taxable,
				'tax_class'            => $tax_class,
				'downloads'            => $downloads,
				'current_download_ids' => $product->get_download_ids()
			)
		);
	}


}