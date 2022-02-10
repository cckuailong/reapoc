<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: mycred_send
 * This shortcode allows the current user to send a pre-set amount of points
 * to a pre-set user. A simpler version of the mycred_transfer shortcode.
 * @see http://codex.mycred.me/shortcodes/mycred_send/ 
 * @since 1.1
 * @version 1.3.1
 */
if ( ! function_exists( 'mycred_render_shortcode_send' ) ) :
	function mycred_render_shortcode_send( $atts, $content = '' ) {

		if ( ! is_user_logged_in() ) return;

		extract( shortcode_atts( array(
			'amount' => 0,
			'to'     => '',
			'log'    => '',
			'ref'    => 'gift',
			'type'   => MYCRED_DEFAULT_TYPE_KEY,
			'class'  => 'button button-primary btn btn-primary',
			'reload' => 0
		), $atts, MYCRED_SLUG . '_send' ) );

		if ( ! mycred_point_type_exists( $type ) ) return 'Point type not found.';

		global $post;

		// Send points to the post author (assuming this shortcode is used inside the loop)
		$to            = mycred_get_user_id( $to );

		// We will not render for ourselves.
		$user_id       = get_current_user_id();
		$recipient     = absint( $to );
		if ( $recipient === $user_id || $recipient === 0 ) return;

		global $mycred_sending_points;

		$mycred_sending_points = false;

		$mycred        = mycred( $type );

		// Make sure current user or recipient is not excluded!
		if ( $mycred->exclude_user( $recipient ) || $mycred->exclude_user( $user_id ) ) return;

		$account_limit = $mycred->number( apply_filters( 'mycred_transfer_acc_limit', 0 ) );
		$balance       = $mycred->get_users_balance( $user_id, $type );
		$amount        = $mycred->number( $amount );

		// Insufficient Funds
		if ( $balance-$amount < $account_limit ) return;

		// We are ready!
		$mycred_sending_points = true;

		if ( $class != '' )
			$class = ' ' . sanitize_text_field( $class );

		$reload = absint( $reload );

		$render = '<button type="button" class="mycred-send-points-button btn btn-primary' . $class . '" data-reload="' . $reload . '" data-to="' . $recipient . '" data-ref="' . esc_attr( $ref ) . '" data-log="' . esc_attr( $log ) . '" data-amount="' . $amount . '" data-type="' . esc_attr( $type ) . '">' . $mycred->template_tags_general( $content ) . '</button>';

		return apply_filters( 'mycred_send', $render, $atts, $content );

	}
endif;
add_shortcode( MYCRED_SLUG . '_send', 'mycred_render_shortcode_send' );

/**
 * myCRED Send Points Ajax
 * @since 0.1
 * @version 1.4.1
 */
if ( ! function_exists( 'mycred_shortcode_send_points_ajax' ) ) :
	function mycred_shortcode_send_points_ajax() {

		// Security
		check_ajax_referer( 'mycred-send-points', 'token' );

		$user_id       = get_current_user_id();

		if ( mycred_force_singular_session( $user_id, 'mycred-last-send' ) )
			wp_send_json( 'error' );

		$point_type    = MYCRED_DEFAULT_TYPE_KEY;
		if ( isset( $_POST['type'] ) )
			$point_type = sanitize_text_field( $_POST['type'] );

		// Make sure the type exists
		if ( ! mycred_point_type_exists( $point_type ) ) die();

		// Prep
		$recipient     = (int) sanitize_text_field( $_POST['recipient'] );
		$reference     = sanitize_text_field( $_POST['reference'] );
		$log_entry     = strip_tags( trim( $_POST['log'] ), '<a>' );

		// No sending to ourselves
		if ( $user_id == $recipient )
			wp_send_json( 'error' );

		$mycred        = mycred( $point_type );

		// Prep amount
		$amount        = sanitize_text_field( $_POST['amount'] );
		$amount        = $mycred->number( abs( $amount ) );

		// Check solvency
		$account_limit = $mycred->number( apply_filters( 'mycred_transfer_acc_limit', $mycred->zero() ) );
		$balance       = $mycred->get_users_balance( $user_id, $point_type );
		$new_balance   = $balance-$amount;

		$data          = array( 'ref_type' => 'user' );

		// Insufficient Funds
		if ( $new_balance < $account_limit )
			die();

		// After this transfer our account will reach zero
		elseif ( $new_balance == $account_limit )
			$reply = 'zero';

		// Check if this is the last time we can do these kinds of amounts
		elseif ( $new_balance - $amount < $account_limit )
			$reply = 'minus';

		// Else everything is fine
		else
			$reply = 'done';

		// First deduct points
		if ( $mycred->add_creds(
			$reference,
			$user_id,
			0 - $amount,
			$log_entry,
			$recipient,
			$data,
			$point_type
		) ) {

			// Then add to recipient
			$mycred->add_creds(
				$reference,
				$recipient,
				$amount,
				$log_entry,
				$user_id,
				$data,
				$point_type
			);

		}
		else {
			$reply = 'error';
		}

		// Share the good news
		wp_send_json( $reply );

	}
endif;
add_action( 'wp_ajax_mycred-send-points', 'mycred_shortcode_send_points_ajax' );
