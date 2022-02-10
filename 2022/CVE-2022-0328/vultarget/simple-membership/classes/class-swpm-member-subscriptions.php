<?php

class SWPM_Member_Subscriptions {

	private $active_statuses   = array( 'trialing', 'active' );
	private $active_subs_count = 0;
	private $subs_count        = 0;
	private $subs              = array();
	private $member_id;

	public function __construct( $member_id ) {

		$this->member_id = $member_id;

		$subscr_id = SwpmMemberUtils::get_member_field_by_id( $member_id, 'subscr_id' );

		$query_args = array(
			'post_type'  => 'swpm_transactions',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'     => 'member_id',
						'value'   => $member_id,
						'compare' => '=',
					),
					array(
						'key'     => 'subscr_id',
						'value'   => $subscr_id,
						'compare' => '=',
					),
				),
				array(
					'key'     => 'gateway',
					'value'   => 'stripe-sca-subs',
					'compare' => '=',
				),
			),
		);

		$found_subs = new WP_Query( $query_args );

		$this->subs_count = $found_subs->post_count;

		foreach ( $found_subs->posts as $found_sub ) {
			$sub            = array();
			$post_id        = $found_sub->ID;
			$sub['post_id'] = $post_id;
			$sub_id         = get_post_meta( $post_id, 'subscr_id', true );

			$sub['sub_id'] = $sub_id;

			$status = get_post_meta( $post_id, 'subscr_status', true );

			$sub['status'] = $status;

			if ( $this->is_active( $status ) ) {
				$this->active_subs_count++;
			}

			$cancel_token = get_post_meta( $post_id, 'subscr_cancel_token', true );

			if ( empty( $cancel_token ) ) {
				$cancel_token = md5( $post_id . $sub_id . uniqid() );
				update_post_meta( $post_id, 'subscr_cancel_token', $cancel_token );
			}

			$sub['cancel_token'] = $cancel_token;

			$is_live        = get_post_meta( $post_id, 'is_live', true );
			$is_live        = empty( $is_live ) ? false : true;
			$sub['is_live'] = $is_live;

			$sub['payment_button_id'] = get_post_meta( $post_id, 'payment_button_id', true );

			$this->subs[ $sub_id ] = $sub;
		}

		$this->recheck_status_if_needed();

	}

	public function get_active_subs_count() {
		return $this->active_subs_count;
	}

	public function is_active( $status ) {
		return  in_array( $status, $this->active_statuses, true );
	}

	private function recheck_status_if_needed() {
		foreach ( $this->subs as $sub_id => $sub ) {
			if ( ! empty( $sub['status'] ) ) {
				continue;
			}
			try {
				$api_keys = SwpmMiscUtils::get_stripe_api_keys_from_payment_button( $sub['payment_button_id'], $sub['is_live'] );

				SwpmMiscUtils::load_stripe_lib();

				\Stripe\Stripe::setApiKey( $api_keys['secret'] );

				$stripe_sub = \Stripe\Subscription::retrieve( $sub_id );

				$this->subs[ $sub_id ]['status'] = $stripe_sub['status'];

				if ( $this->is_active( $stripe_sub['status'] ) ) {
					$this->active_subs_count++;
				}

				update_post_meta( $sub['post_id'], 'subscr_status', $stripe_sub['status'] );
			} catch ( \Exception $e ) {
				return false;
			}
		}
	}

	public function get_stripe_subs_cancel_url( $args, $sub_id = false ) {
		if ( empty( $this->active_subs_count ) ) {
			return SwpmUtils::_( 'No active subscriptions' );
		}
		if ( false === $sub_id ) {
			$sub_id = array_key_first( $this->subs );
		}
		$sub = $this->subs[ $sub_id ];

		$token = $sub['cancel_token'];

		$nonce = wp_nonce_field( $token, 'swpm_cancel_sub_nonce', false, false );

                $anchor_text = isset( $args['anchor_text'] ) ? $args['anchor_text'] : SwpmUtils::_( 'Cancel Subscription' );
		$out = '<form method="POST">%s<input type="hidden" name="swpm_cancel_sub_token" value="%s"></input>
		<button type="submit" name="swpm_do_cancel_sub" value="1" onclick="return confirm(\'' . esc_js( SwpmUtils::_( 'Are you sure that you want to cancel the subscription?' ) ) . '\');">' . $anchor_text . '</button></form>';

		$out = sprintf( $out, $nonce, $token );

		return $out;
	}

	public function find_by_token( $token ) {
		foreach ( $this->subs as $sub_id => $sub ) {
			if ( $sub['cancel_token'] === $token ) {
				return $sub;
			}
		}
	}

	public function cancel( $sub_id ) {
		$sub = $this->subs[ $sub_id ];

		try {
			$api_keys = SwpmMiscUtils::get_stripe_api_keys_from_payment_button( $sub['payment_button_id'], $sub['is_live'] );

			SwpmMiscUtils::load_stripe_lib();

			\Stripe\Stripe::setApiKey( $api_keys['secret'] );

			$stripe_sub = \Stripe\Subscription::retrieve( $sub_id );

			if ( $this->is_active( $stripe_sub['status'] ) ) {
				$stripe_sub->cancel();
			}

			update_post_meta( $sub['post_id'], 'subscr_status', $stripe_sub['status'] );
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
		return true;
	}

}
