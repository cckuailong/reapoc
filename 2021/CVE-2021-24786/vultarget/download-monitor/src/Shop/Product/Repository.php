<?php

namespace Never5\DownloadMonitor\Shop\Product;

interface Repository {

	/**
	 * Retrieve items
	 *
	 * @param array $filters
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array<Product>
	 */
	public function retrieve( $filters=array(), $limit=0, $offset=0 );

	/**
	 * Retrieve single item
	 *
	 * @param int $id
	 *
	 * @return Product
	 */
	public function retrieve_single( $id );

	/**
	 * Returns number of rows for given filters
	 *
	 * @param array $filters
	 *
	 * @return int
	 */
	public function num_rows( $filters=array() );

	/**
	 * @param Product $product
	 *
	 * @return bool
	 */
	public function persist( $product );
}