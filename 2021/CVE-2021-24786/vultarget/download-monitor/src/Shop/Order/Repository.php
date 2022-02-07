<?php

namespace Never5\DownloadMonitor\Shop\Order;

interface Repository {

	/**
	 * Retrieve session
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string $order_by
	 * @param string $order
	 *
	 * @return Order
	 *
	 * @throws \Exception
	 */
	public function retrieve( $limit, $offset, $order_by, $order );

	/**
	 * Retrieve a single order
	 *
	 * @param $id
	 *
	 * @return Order
	 *
	 * @throws \Exception
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
	 * Persist order
	 *
	 * @param Order $order
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	public function persist( $order );

}