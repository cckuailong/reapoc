<?php

namespace Never5\DownloadMonitor\Shop\Admin;

use Never5\DownloadMonitor\Shop\Services\Services;
use Never5\DownloadMonitor\Shop\Util\PostType;

class ProductTableColumns {

	/**
	 * Setup product columns
	 */
	public function setup() {
		add_filter( 'manage_edit-' . PostType::KEY . '_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_' . PostType::KEY . '_posts_custom_column', array( $this, 'column_data' ), 10, 2 );
		add_filter( 'manage_edit-' . PostType::KEY . '_sortable_columns', array( $this, 'sortable_columns' ) );
	}

	/**
	 * columns function.
	 *
	 * @access public
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_columns( $columns ) {
		$columns = array();

		$columns["cb"]    = "<input type=\"checkbox\" />";
		$columns["thumb"] = '<span>' . __( "Image", 'download-monitor' ) . '</span>';
		$columns["title"] = __( "Title", 'download-monitor' );
		$columns["price"] = __( "Price", 'download-monitor' );
		$columns["date"]  = __( "Date", 'download-monitor' );

		return $columns;
	}

	/**
	 * custom_columns function.
	 *
	 * @access public
	 *
	 * @param string $column
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function column_data( $column, $post_id ) {

		/** @var \Never5\DownloadMonitor\Shop\Product\Product $product */
		try {
			$product = Services::get()->service( 'product_repository' )->retrieve_single( $post_id );
		} catch ( \Exception $exception ) {
			return;
		}

		switch ( $column ) {
			case "thumb" :
				echo $product->get_image();
				break;
			case "price" :
				echo dlm_format_money( $product->get_price() );
				break;
		}
	}

	/**
	 * sortable_columns function.
	 *
	 * @access public
	 *
	 * @param mixed $columns
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$custom = array(
			'price' => 'price'
		);

		return wp_parse_args( $custom, $columns );
	}
}