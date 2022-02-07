<?php

namespace Never5\DownloadMonitor\Shop\Session;

class WordPressRepository implements Repository {

	/**
	 * @param string $key
	 * @param string $hash
	 *
	 * @return Session
	 * @throws \Exception
	 */
	public function retrieve( $key, $hash ) {
		global $wpdb;

		// try to fetch session from database
		$r = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM `" . $wpdb->prefix . "dlm_session` WHERE `key` = %s AND `hash` = %s ;",
				$key,
				$hash
			)
		);

		// check if result if found
		if ( null == $r ) {
			throw new \Exception( 'Session not found' );
		}

		// json decode data field
		$data = json_decode( $r->data );

		// create session object
		$session = new Session();
		$session->set_key( $r->key );
		$session->set_hash( $r->hash );
		$session->set_expiry( new \DateTimeImmutable( $r->expiry ) );

		if ( isset( $data->items ) ) {
			$session_items = array();
			foreach ( $data->items as $db_item ) {
				$item = new Item\Item();
				$item->set_key( $db_item->key );
				$item->set_product_id( $db_item->product_id );
				$item->set_qty( $db_item->qty );
				$session_items[ $db_item->key ] = $item;
			}
			$session->set_items( $session_items );
		}

		if ( isset( $data->coupons ) ) {
			$session->set_coupons( $data->coupons );
		}

		return $session;
	}

	/**
	 * @param Session $session
	 *
	 * @return bool|void
	 */
	public function persist( $session ) {
		global $wpdb;

		// prep items for JSON
		$items_array = array();
		foreach ( $session->get_items() as $k => $v ) {
			$items_array[ $k ] = $v->to_array();
		}

		// prepare data
		$data = json_encode( array(
			'items'   => $items_array,
			'coupons' => $session->get_coupons()
		) );

		// delete previous session in database
		$this->remove( $session->get_key(), $session->get_hash() );

		// insert new session
		$wpdb->insert(
			$wpdb->prefix . 'dlm_session',
			array(
				'key'    => $session->get_key(),
				'hash'   => $session->get_hash(),
				'expiry' => $session->get_expiry()->format( 'Y-m-d H:i:s' ),
				'data'   => $data
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s'
			)
		);

	}

	/**
	 * Removes session
	 *
	 * @param string $key
	 * @param string $hash
	 *
	 * @return bool
	 */
	public function remove( $key, $hash ) {
		global $wpdb;

		$wpdb->delete( $wpdb->prefix . 'dlm_session', array(
			'key'  => $key,
			'hash' => $hash
		), array( '%s', '%s' ) );

		return true;
	}

}