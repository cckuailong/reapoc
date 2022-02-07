<?php

namespace Never5\DownloadMonitor\Shop\Product;

use Never5\DownloadMonitor\Shop\Services\Services;
use Never5\DownloadMonitor\Shop\Util\PostType;

class WordPressRepository implements Repository {

	/**
	 * Filter query arguments for download WP_Query queries
	 *
	 * @param array $args
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array
	 */
	private function filter_query_args( $args = array(), $limit = 0, $offset = 0 ) {

		// limit must be int, not abs
		$limit = intval( $limit );

		// most be absint
		$offset = absint( $offset );

		// start with removing reserved keys
		unset( $args['post_type'] );
		unset( $args['posts_per_page'] );
		unset( $args['offset'] );
		unset( $args['paged'] );
		unset( $args['nopaging'] );

		// setup our reserved keys
		$args['post_type']      = PostType::KEY;
		$args['posts_per_page'] = - 1;

		// set limit if set
		if ( $limit > 0 ) {
			$args['posts_per_page'] = $limit;
		}

		// set offset if set
		if ( $offset > 0 ) {
			$args['offset'] = $offset;
		}

		return $args;
	}

	/**
	 * Returns number of rows for given filters
	 *
	 * @param array $filters
	 *
	 * @return int
	 */
	public function num_rows( $filters = array() ) {
		$q = new \WP_Query();
		$q->query( $this->filter_query_args( $filters ) );

		return $q->found_posts;
	}

	/**
	 * Retrieve single download
	 *
	 * @param int $id
	 *
	 * @return Product
	 * @throws \Exception
	 */
	public function retrieve_single( $id ) {
		$downloads = $this->retrieve( array( 'p' => absint( $id ) ) );

		if ( count( $downloads ) != 1 ) {
			throw new \Exception( "Product not found" );
		}

		return array_shift( $downloads );
	}

	/**
	 * Retrieve products
	 *
	 * @param array $filters
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array<Product>
	 */
	public function retrieve( $filters = array(), $limit = 0, $offset = 0 ) {

		$items = array();

		$q = new \WP_Query();

		$posts = $q->query( $this->filter_query_args( $filters, $limit, $offset ) );

		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $post ) {

				/**
				 * @var Product $product
				 */
				$product = Services::get()->service( 'product_factory' )->make();
				$product->set_id( $post->ID );
				$product->set_status( $post->post_status );
				$product->set_title( $post->post_title );
				$product->set_content( $post->post_content );
				$product->set_author( $post->post_author );
				$product->set_excerpt( $post->post_excerpt );
				$product->set_price( get_post_meta( $post->ID, '_price', true ) );
				$product->set_taxable( ( 1 == get_post_meta( $post->ID, '_taxable', true ) ) );
				$product->set_tax_class( get_post_meta( $post->ID, '_tax_class', true ) );
				$product->set_download_ids( get_post_meta( $post->ID, '_downloads' ) );
				// add download to return array
				$items[] = $product;
			}
		}

		return $items;
	}

	/**
	 * @param Product $product
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	public function persist( $product ) {

		// check if new download or existing
		if ( 0 == $product->get_id() ) {

			// create
			$product_id = wp_insert_post( array(
				'post_title'   => $product->get_title(),
				'post_content' => $product->get_content(),
				'post_excerpt' => $product->get_excerpt(),
				'post_author'  => $product->get_author(),
				'post_type'    => PostType::KEY,
				'post_status'  => $product->get_status()
			) );

			if ( is_wp_error( $product_id ) ) {
				throw new \Exception( 'Unable to insert download in WordPress database' );
			}
			// set new vehicle ID
			$product->set_id( $product_id );

		} else {

			// update
			$product_id = wp_update_post( array(
				'ID'           => $product->get_id(),
				'post_title'   => $product->get_title(),
				'post_content' => $product->get_content(),
				'post_excerpt' => $product->get_excerpt(),
				'post_author'  => $product->get_author(),
				'post_status'  => $product->get_status()
			) );

			if ( is_wp_error( $product_id ) ) {
				throw new \Exception( 'Unable to update download in WordPress database' );
			}

		}

		update_post_meta( $product_id, '_price', $product->get_price() );
		update_post_meta( $product_id, '_taxable', 0 );
		update_post_meta( $product_id, '_tax_class', $product->get_tax_class() );

		// delete all linked downloads before linking set ones
		delete_post_meta( $product_id, '_downloads' );
		$downloads = $product->get_download_ids();
		if ( ! empty( $downloads ) ) {
			foreach ( $downloads as $download_id ) {
				add_post_meta( $product_id, '_downloads', intval( $download_id ) );
			}
		}

		return true;
	}

}