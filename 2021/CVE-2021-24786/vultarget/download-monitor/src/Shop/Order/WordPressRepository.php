<?php

namespace Never5\DownloadMonitor\Shop\Order;

use Never5\DownloadMonitor\Shop\Services\Services;

class WordPressRepository implements Repository {

	/**
	 * Prep where statement for WP DB SQL queries
	 *
	 * An example filter is an array like this:
	 * array(
	 *  'key'       => 'id',
	 *  'value'     => 1,
	 *  'operator'  => '='
	 * )
	 *
	 * @param $filters
	 *
	 * @return string
	 */
	private function prep_where_statement( $filters ) {
		global $wpdb;
		// setup where statements
		$where = array( "WHERE 1=1" );
		foreach ( $filters as $filter ) {
			$operator = ( ! empty( $filter['operator'] ) ) ? esc_sql( $filter['operator'] ) : "=";
			if ( 'IN' == $operator && is_array( $filter['value'] ) ) {
				array_walk( $filter['value'], 'esc_sql' );
				$value_str = implode( "','", $filter['value'] );
				$where[]   = "AND `" . esc_sql( $filter['key'] ) . "` " . $operator . " ('" . $value_str . "')";
			} else {
				$where[] = $wpdb->prepare( "AND `" . esc_sql( $filter['key'] ) . "` " . $operator . " '%s'", $filter['value'] );
			}
		}
		$where_str = "";
		if ( count( $where ) > 1 ) {
			$where_str = implode( " ", $where );
		}

		return $where_str;
	}

	/**
	 * Fetch and add orders items to order
	 *
	 * @param Order $order
	 *
	 * @return Order
	 */
	private function add_order_items_to_order( $order ) {
		global $wpdb;

		/** Fetch order items */
		$order_items = array();
		$db_items    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "dlm_order_item` WHERE `order_id` = %d ORDER BY `id` ASC ;", $order->get_id() ) );
		if ( count( $db_items ) > 0 ) {
			foreach ( $db_items as $db_item ) {
				$order_item = new OrderItem();

				$order_item->set_id( $db_item->id );
				$order_item->set_label( $db_item->label );
				$order_item->set_qty( $db_item->qty );
				$order_item->set_product_id( $db_item->product_id );
				$order_item->set_subtotal( $db_item->subtotal );
				$order_item->set_tax_class( $db_item->tax_class );
				$order_item->set_tax_total( $db_item->tax_total );
				$order_item->set_total( $db_item->total );

				$order_items[] = $order_item;
			}
		}

		$order->set_items( $order_items );

		return $order;
	}

	/**
	 * Fetch and add transactions to order
	 *
	 * @param Order $order
	 *
	 * @return Order
	 * @throws \Exception
	 */
	private function add_transactions_to_order( $order ) {
		global $wpdb;

		/** Fetch transactions */
		$order_transactions = array();
		$db_items           = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `" . $wpdb->prefix . "dlm_order_transaction` WHERE `order_id` = %d ORDER BY `id` ASC ;", $order->get_id() ) );
		if ( count( $db_items ) > 0 ) {
			foreach ( $db_items as $db_item ) {
				$order_transaction = new Transaction\OrderTransaction();

				$order_transaction->set_id( $db_item->id );

				if ( ! empty( $db_item->date_created ) ) {
					$order_transaction->set_date_created( new \DateTimeImmutable( $db_item->date_created ) );
				}

				if ( ! empty( $db_item->date_modified ) ) {
					$order_transaction->set_date_modified( new \DateTimeImmutable( $db_item->date_modified ) );
				}

				$order_transaction->set_amount( $db_item->amount );
				$order_transaction->set_status( Services::get()->service( 'order_transaction_factory' )->make_status( $db_item->status ) );
				$order_transaction->set_processor( $db_item->processor );
				$order_transaction->set_processor_nice_name( $db_item->processor_nice_name );
				$order_transaction->set_processor_transaction_id( $db_item->processor_transaction_id );
				$order_transaction->set_processor_status( $db_item->processor_status );

				$order_transactions[] = $order_transaction;
			}
		}

		$order->set_transactions( $order_transactions );

		return $order;
	}


	/**
	 * Retrieve session
	 *
	 * @param array $filters
	 * @param int $limit
	 * @param int $offset
	 * @param string $order_by
	 * @param string order
	 *
	 * @return Order[]
	 *
	 * @throws \Exception
	 */
	public function retrieve( $filters = array(), $limit = 0, $offset = 0, $order_by = 'id', $order = 'DESC' ) {
		global $wpdb;

		// prep order
		$order_by = ( empty( $order_by ) ) ? 'id' : esc_sql( $order_by );
		$order    = strtoupper( $order );
		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
			$order = 'DESC';
		}

		// prep where statement
		$where_str = $this->prep_where_statement( $filters );

		$sql = "
		SELECT O.*, C.* 
		FROM `" . $wpdb->prefix . "dlm_order` O
		INNER JOIN `" . $wpdb->prefix . "dlm_order_customer` C ON O.id=C.order_id
		" . $where_str . "
		ORDER BY O.`" . $order_by . "` " . $order;

		$limit  = absint( $limit );
		$offset = absint( $offset );

		// if we have an offset, we NEED a limit
		if ( $offset > 0 && $limit < 1 ) {
			$limit = 99999;
		}

		if ( $limit > 0 ) {
			$sql .= " LIMIT " . $limit;
		}

		if ( $offset > 0 ) {
			$sql .= " OFFSET " . $offset;
		}

		$sql .= ";";

		// try to fetch session from database
		$results = $wpdb->get_results( $sql );

		// check if result if found
		if ( null === $results ) {
			throw new \Exception( 'SQL error while fetching order' );
		}

		// array that will hold all order objects
		$orders = array();

		foreach ( $results as $result ) {

			$order = new Order();

			$order->set_id( $result->id );
			$order->set_status( Services::get()->service( 'order_status_factory' )->make( $result->status ) );
			$order->set_currency( $result->currency );
			$order->set_hash( $result->hash );

			if ( ! empty( $result->date_created ) ) {
				$order->set_date_created( new \DateTimeImmutable( $result->date_created ) );
			}

			if ( ! empty( $result->date_modified ) ) {
				$order->set_date_modified( new \DateTimeImmutable( $result->date_modified ) );
			}

			// create and set customer
			$order->set_customer( new OrderCustomer(
				$result->first_name,
				$result->last_name,
				$result->company,
				$result->address_1,
				$result->address_2,
				$result->city,
				$result->state,
				$result->postcode,
				$result->country,
				$result->email,
				$result->phone,
				$result->ip_address
			) );

			// add order items to order object
			$order = $this->add_order_items_to_order( $order );

			// add transactions to order object
			$order = $this->add_transactions_to_order( $order );

			// add new order object to array
			$orders[] = $order;

		}

		return $orders;

	}

	/**
	 * Retrieve a single order
	 *
	 * @param $id
	 *
	 * @return Order
	 *
	 * @throws \Exception
	 */
	public function retrieve_single( $id ) {
		$orders = $this->retrieve( array( array( 'key' => 'id', 'value' => $id, 'operator' => '=' ) ), 1 );
		if ( 0 === count( $orders ) ) {
			throw new \Exception( 'Order not found' );
		}

		return $orders[0];
	}

	/**
	 * Returns number of rows for given filters
	 *
	 * @param array $filters
	 *
	 * @return int
	 */
	public function num_rows( $filters = array() ) {
		global $wpdb;

		// prep where statement
		$where_str = $this->prep_where_statement( $filters );

		$num = $wpdb->get_var( "SELECT COUNT(id) FROM `" . $wpdb->prefix . "dlm_order` {$where_str} " );

		if ( null === $num ) {
			$num = 0;
		}

		return $num;
	}

	/**
	 * Persist order
	 *
	 * @param Order $order
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	public function persist( $order ) {
		global $wpdb;

		$date_created = '';
		if ( null !== $order->get_date_created() ) {
			$date_created = $order->get_date_created()->format( 'Y-m-d H:i:s' );
		}

		$date_modified = '';
		if ( null !== $order->get_date_modified() ) {
			$date_modified = $order->get_date_modified()->format( 'Y-m-d H:i:s' );
		}

		$order_id = $order->get_id();

		$customer = $order->get_customer();

		// check if it's a new order or if we need to update an existing one
		if ( empty( $order_id ) ) {
			/** New order */

			// insert order
			$r = $wpdb->insert(
				$wpdb->prefix . 'dlm_order',
				array(
					'status'        => $order->get_status()->get_key(),
					'date_created'  => $date_created,
					'date_modified' => $date_modified,
					'currency'      => $order->get_currency(),
					'hash'          => $order->get_hash()
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s'
				)
			);

			if ( false === $r ) {
				throw new \Exception( "Failed creating Order" );
			}

			// set the new id as order id
			$order->set_id( $wpdb->insert_id );

			// insert customer record
			$r = $wpdb->insert(
				$wpdb->prefix . 'dlm_order_customer',
				array(
					'first_name' => $customer->get_first_name(),
					'last_name'  => $customer->get_last_name(),
					'company'    => $customer->get_company(),
					'address_1'  => $customer->get_address_1(),
					'address_2'  => $customer->get_address_2(),
					'city'       => $customer->get_city(),
					'state'      => $customer->get_state(),
					'postcode'   => $customer->get_postcode(),
					'country'    => $customer->get_country(),
					'email'      => $customer->get_email(),
					'phone'      => $customer->get_phone(),
					'ip_address' => $customer->get_ip_address(),
					'order_id'   => $order->get_id()
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d'
				)
			);

			if ( false === $r ) {
				throw new \Exception( "Failed creating Customer" );
			}

		} else {

			// update an existing order
			$r = $wpdb->update( $wpdb->prefix . 'dlm_order',
				array(
					'status'        => $order->get_status()->get_key(),
					'date_modified' => current_time( 'mysql', 1 ),
					'currency'      => $order->get_currency(),
					'hash'          => $order->get_hash()
				),
				array( 'id' => $order_id ),
				array(
					'%s',
					'%s',
					'%s',
					'%s'
				),
				array( '%d' )
			);

			if ( false === $r ) {
				throw new \Exception( "Failed updating Order" );
			}

			// update customer record
			$r = $wpdb->update(
				$wpdb->prefix . 'dlm_order_customer',
				array(
					'first_name' => $customer->get_first_name(),
					'last_name'  => $customer->get_last_name(),
					'company'    => $customer->get_company(),
					'address_1'  => $customer->get_address_1(),
					'address_2'  => $customer->get_address_2(),
					'city'       => $customer->get_city(),
					'state'      => $customer->get_state(),
					'postcode'   => $customer->get_postcode(),
					'country'    => $customer->get_country(),
					'email'      => $customer->get_email(),
					'phone'      => $customer->get_phone(),
					'ip_address' => $customer->get_ip_address(),
					'order_id'   => $order->get_id()
				),
				array( 'order_id' => $order_id ),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d'
				),
				array( '%d' )
			);

			if ( false === $r ) {
				throw new \Exception( "Failed updating customer" );
			}

		}

		// handle order items
		$order_items = $order->get_items();
		if ( ! empty( $order_items ) ) {
			foreach ( $order_items as $order_item ) {

				// check if this order item exists in DB already
				$order_item_id = $order_item->get_id();
				if ( empty( $order_item_id ) ) {

					// insert new order item
					$r = $wpdb->insert(
						$wpdb->prefix . 'dlm_order_item',
						array(
							'order_id'   => $order->get_id(),
							'label'      => $order_item->get_label(),
							'qty'        => $order_item->get_qty(),
							'product_id' => $order_item->get_product_id(),
							'tax_class'  => $order_item->get_tax_class(),
							'tax_total'  => $order_item->get_tax_total(),
							'subtotal'   => $order_item->get_subtotal(),
							'total'      => $order_item->get_total()
						),
						array(
							'%d',
							'%s',
							'%d',
							'%d',
							'%s',
							'%d',
							'%d',
							'%d',
						)
					);

					if ( false === $r ) {
						throw new \Exception( "Failed creating OrderItem" );
					}

					$order_item->set_id( $wpdb->insert_id );
				} else {

					// update existing order item record
					$r = $wpdb->update(
						$wpdb->prefix . 'dlm_order_item',
						array(
							'order_id'   => $order->get_id(),
							'label'      => $order_item->get_label(),
							'qty'        => $order_item->get_qty(),
							'product_id' => $order_item->get_product_id(),
							'tax_class'  => $order_item->get_tax_class(),
							'tax_total'  => $order_item->get_tax_total(),
							'subtotal'   => $order_item->get_subtotal(),
							'total'      => $order_item->get_total()
						),
						array( 'id' => $order_item_id ),
						array(
							'%d',
							'%s',
							'%d',
							'%d',
							'%s',
							'%d',
							'%d',
							'%d',
						),
						array( '%d' )
					);

					if ( false === $r ) {
						throw new \Exception( "Failed updating OrderItem" );
					}

				}
			}
		}

		// handle transactions
		$transactions = $order->get_transactions();
		if ( ! empty( $transactions ) ) {

			/** @var Transaction\OrderTransaction $transaction */
			foreach ( $transactions as $transaction ) {

				$transaction_id = $transaction->get_id();

				$transaction_date_created = null;
				if ( null !== $transaction->get_date_created() ) {
					$transaction_date_created = $transaction->get_date_created()->format( 'Y-m-d H:i:s' );
				}

				$transaction_date_modified = null;
				if ( null !== $transaction->get_date_modified() ) {
					$transaction_date_modified = $transaction->get_date_modified()->format( 'Y-m-d H:i:s' );
				}

				// check if it's a new transaction or an existing one
				if ( empty( $transaction_id ) ) {

					// it's a new transaction

					$r = $wpdb->insert(
						$wpdb->prefix . 'dlm_order_transaction',
						array(
							'order_id'                 => $order->get_id(),
							'date_created'             => $transaction_date_created,
							'date_modified'            => $transaction_date_modified,
							'amount'                   => $transaction->get_amount(),
							'status'                   => $transaction->get_status()->get_key(),
							'processor'                => $transaction->get_processor(),
							'processor_nice_name'      => $transaction->get_processor_nice_name(),
							'processor_transaction_id' => $transaction->get_processor_transaction_id(),
							'processor_status'         => $transaction->get_processor_status()
						),
						array(
							'%d',
							'%s',
							'%s',
							'%d',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s'
						)
					);

					if ( false === $r ) {
						throw new \Exception( "Failed creating OrderTransaction" );
					}


					$transaction->set_id( $wpdb->insert_id );

				} else {

					// it's an existing transaction

					$r = $wpdb->update(
						$wpdb->prefix . 'dlm_order_transaction',
						array(
							'order_id'                 => $order->get_id(),
							'date_created'             => $transaction_date_created,
							'date_modified'            => $transaction_date_modified,
							'amount'                   => $transaction->get_amount(),
							'status'                   => $transaction->get_status()->get_key(),
							'processor'                => $transaction->get_processor(),
							'processor_nice_name'      => $transaction->get_processor_nice_name(),
							'processor_transaction_id' => $transaction->get_processor_transaction_id(),
							'processor_status'         => $transaction->get_processor_status()
						),
						array( 'id' => $transaction_id ),
						array(
							'%d',
							'%s',
							'%s',
							'%d',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s'
						),
						array( '%d' )
					);

					if ( false === $r ) {
						throw new \Exception( "Failed updating OrderTransaction" );
					}

				}

			}

		}

	}

	/**
	 * Delete order including all attached items (items, transactions, customer, etc.)
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete( $id ) {
		global $wpdb;

		$success = true;

		try {

			if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "dlm_order_transaction` WHERE `order_id` = %d ;", $id ) ) ) {
				throw new \Exception( "Failed deleting transactions" );
			}

			if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "dlm_order_item` WHERE `order_id` = %d ;", $id ) ) ) {
				throw new \Exception( "Failed deleting order items" );
			}

			if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "dlm_order_customer` WHERE `order_id` = %d ;", $id ) ) ) {
				throw new \Exception( "Failed deleting customer" );
			}

			if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM `" . $wpdb->prefix . "dlm_order` WHERE `id` = %d ;", $id ) ) ) {
				throw new \Exception( "Failed deleting customer" );
			}

		} catch ( \Exception $exception ) {
			\DLM_Debug_Logger::log( $exception->getMessage() );
			$success = false;
		}

		return $success;
	}

	/**
	 * Remove all items in trash
	 */
	public function empty_trash() {
		global $wpdb;

		$ids = $wpdb->get_col( "SELECT `id` FROM `" . $wpdb->prefix . "dlm_order` WHERE `status` = 'trash' ;" );

		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			foreach ( $ids as $id ) {
				if ( ! $this->delete( $id ) ) {
					return false;
				}
			}
		}

		return true;
	}
}