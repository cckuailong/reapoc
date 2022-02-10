<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Get The Transfer Object
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_transfer' ) ) :
	function mycred_transfer( $transfer_id = false ) {

		global $mycred_transfer;

		$transfer_id     = sanitize_text_field( $transfer_id );

		if ( isset( $mycred_transfer )
			&& ( $mycred_transfer instanceof myCRED_Transfer )
			&& ( $transfer_id === $mycred_transfer->transfer_id )
		) {
			return $mycred_transfer;
		}

		$mycred_transfer = new myCRED_Transfer( $transfer_id );

		do_action( 'mycred_transfer' );

		return $mycred_transfer;

	}
endif;

/**
 * Get Transfer
 * @see http://codex.mycred.me/functions/mycred_get_transfer/
 * @param $transfer_id (string) required transfer id to retreave.
 * @returns myCRED_Transfer object on success else false.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_transfer' ) ) :
	function mycred_get_transfer( $transfer_id = false ) {

		if ( $transfer === false ) return false;

		$transfer    = mycred_transfer( $transfer_id );
		$transaction = $transfer->get_transfer();

		// Transaction not found
		if ( $transaction === false ) return false;

		// Populate object
		foreach ( $transaction as $key => $value )
			$transfer->$key = $value;

		return $transfer;

	}
endif;

/**
 * New Transfer
 * @see http://codex.mycred.me/functions/mycred_new_transfer/
 * @param $request (array) the required transfer request array.
 * @param $post (array) optional posted data from the transfer form.
 * @returns error code if transfer failed else an array or transfer details.
 * @since 1.7.6
 * @version 1.1
 */
if ( ! function_exists( 'mycred_new_transfer' ) ) :
	function mycred_new_transfer( $request = array(), $post = array() ) {

		$transfer       = mycred_transfer();

		// Validate the request first
		$valid_transfer = $transfer->is_valid_transfer_request( $request, $post );
		if ( $valid_transfer !== true )
			return $valid_transfer;

		// Attempt to make the transfer
		return $transfer->new_transfer();

	}
endif;

/**
 * Refund Transfer
 * @see http://codex.mycred.me/functions/mycred_refund_transfer/
 * @param $transfer_id (string) required transfer id to refund.
 * @returns error message (string) or true on success.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_refund_transfer' ) ) :
	function mycred_refund_transfer( $transfer_id = false ) {

		if ( $transfer === false ) return false;

		$transfer    = mycred_transfer( $transfer_id );
		$transaction = $transfer->get_transfer();

		// Transaction could not be found
		if ( $transaction === false ) return false;

		// Populate object
		foreach ( $transaction as $key => $value )
			$transfer->$key = $value;

		return $transfer->refund();

	}
endif;

/**
 * Get Transfer Limits
 * @see http://codex.mycred.me/functions/mycred_get_transfer_limits/
 * @param $settings (array) optional transfer settings.
 * @returns array of limits.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_transfer_limits' ) ) :
	function mycred_get_transfer_limits( $settings = false ) {

		if ( $settings === false )
			$settings = mycred_get_addon_settings( 'transfers' );

		$limits = array(
			'none'    => __( 'No limits.', 'mycred' ),
			'daily'   => __( 'Impose daily limit.', 'mycred' ),
			'weekly'  => __( 'Impose weekly limit.', 'mycred' ),
			'monthly' => __( 'Impose monthly limit.', 'mycred' )
		);

		return apply_filters( 'mycred_transfer_limits', $limits, $settings );

	}
endif;

/**
 * Get Transfer Limits
 * @see http://codex.mycred.me/functions/mycred_get_transfer_autofill_by/
 * @param $settings (array) optional transfer settings.
 * @returns array of autofill options.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_transfer_autofill_by' ) ) :
	function mycred_get_transfer_autofill_by( $settings = false ) {

		if ( $settings === false )
			$settings = mycred_get_addon_settings( 'transfers' );

		$autofills = array(
			'user_login' => __( 'User Login (user_login)', 'mycred' ),
			'user_email' => __( 'User Email (user_email)', 'mycred' )
		);

		return apply_filters( 'mycred_transfer_autofill_by', $autofills, $settings );

	}
endif;

/**
 * Get Transfer Recipient
 * @see http://codex.mycred.me/functions/mycred_get_transfer_recipient/
 * @param $value (int|string) a value that identifies a particular user in WordPress.
 * @returns false if no recipient was found else the users id (int).
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_transfer_recipient' ) ) :
	function mycred_get_transfer_recipient( $value = '' ) {

		$settings     = mycred_get_addon_settings( 'transfers' );
		$recipient_id = false;

		if ( ! empty( $value ) ) {

			// A numeric ID has been provided that we need to validate
			if ( is_numeric( $value ) ) {

				$user = get_userdata( $value );
				if ( isset( $user->ID ) )
					$recipient_id = $user->ID;

			}

			// A username has been provided
			elseif ( $settings['autofill'] == 'user_login' ) {

				$user = get_user_by( 'login', $value );
				if ( isset( $user->ID ) )
					$recipient_id = $user->ID;

			}

			// An email address has been provided
			elseif ( $settings['autofill'] == 'user_email' ) {

				$user = get_user_by( 'email', $value );
				if ( isset( $user->ID ) )
					$recipient_id = $user->ID;

			}

		}

		return apply_filters( 'mycred_transfer_get_recipient', $recipient_id, $value, $settings );

	}
endif;

/**
 * User Can Transfer
 * @see http://codex.mycred.me/functions/mycred_user_can_transfer/
 * @param $user_id (int) requred user id
 * @param $amount (int) optional amount to check against balance
 * @returns true if no limit is set, 'limit' (string) if user is over limit else the amount of creds left
 * @since 0.1
 * @version 1.4.1
 */
if ( ! function_exists( 'mycred_user_can_transfer' ) ) :
	function mycred_user_can_transfer( $user_id = NULL, $amount = NULL, $type = MYCRED_DEFAULT_TYPE_KEY, $reference = NULL ) {

		if ( $user_id === NULL )
			$user_id = get_current_user_id();

		if ( $reference === NULL )
			$reference = 'transfer';

		if ( ! mycred_point_type_exists( $type ) )
			$type = MYCRED_DEFAULT_TYPE_KEY;

		// Grab Settings
		$settings = mycred_get_addon_settings( 'transfers' );
		$mycred   = mycred( $type );
		$zero     = $mycred->zero();

		// Get users balance
		$balance  = $mycred->get_users_balance( $user_id, $type );

		// Get Transfer Max
		$max      = apply_filters( 'mycred_transfer_limit', $mycred->number( $settings['limit']['amount'] ), $user_id, $amount, $settings, $reference );

		// If an amount is given, deduct this amount to see if the transaction
		// brings us over the account limit
		if ( $amount !== NULL )
			$balance = $mycred->number( $balance - $amount );

		// Zero
		// The lowest amount a user can have on their account. By default, this
		// is zero. But you can override this via the mycred_transfer_acc_limit hook.
		$account_limit = $mycred->number( apply_filters( 'mycred_transfer_acc_limit', $zero, $type, $user_id, $reference ) );

		// Check if we would go minus
		if ( $balance < $account_limit ) return 'low';

		// If there are no limits, return the current balance
		if ( $settings['limit']['limit'] == 'none' ) return $balance;

		// Else we have a limit to impose
		$now = current_time( 'timestamp' );
		$max = $mycred->number( $settings['limit']['amount'] );

		// Daily limit
		if ( $settings['limit']['limit'] == 'daily' )
			$total = mycred_get_total_by_time( 'today', 'now', $reference, $user_id, $type );

		// Weekly limit
		elseif ( $settings['limit']['limit'] == 'weekly' ) {
			$this_week = mktime( 0, 0, 0, date( 'n', $now ), date( 'j', $now ) - date( 'n', $now ) + 1 );
			$total     = mycred_get_total_by_time( $this_week, 'now', $reference, $user_id, $type );
		}

		// Custom limits will need to return the result
		// here and now. Accepted answers are 'limit', 'low' or the amount left on limit.
		else {
			return apply_filters( 'mycred_user_can_transfer', 'limit', $user_id, $amount, $settings, $reference );
		}

		// We are adding up point deducations.
		$total = abs( $total );

		if ( $amount !== NULL ) {

			$total = $mycred->number( $total + $amount );

			// Transfer limit reached
			if ( $total > $max ) return 'limit';

		}

		else {

			// Transfer limit reached
			if ( $total >= $max ) return 'limit';

		}

		// Return whats remaining of limit
		return $mycred->number( $max - $total );

	}
endif;

/**
 * Render Transfer Message
 * @see http://codex.mycred.me/functions/mycred_transfer_render_message/
 * @since 1.7.6
 * @version 1.1
 */
if ( ! function_exists( 'mycred_transfer_render_message' ) ) :
	function mycred_transfer_render_message( $original = '', $data = array() ) {
		
		$message = '';

		if ( empty( $original ) || empty( $data ) ) return $original;

		// Default message
		$original = apply_filters( 'mycred_transfer_default_message', $original, $data );

		// Get saved message
		if ( ! empty( $data ) && array_key_exists( 'message', $data ) && ! empty( $data['message'] ) )
			 $original .= ' - ' . $data['message'];

		$content = str_replace( '%transfer_message%', $message, $original );

		return apply_filters( 'mycred_transfer_message', $content, $original, $message, $data );

	}
endif;

/**
 * Get Users Transfer History
 * @since 1.3.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_users_transfer_history' ) ) :
	function mycred_get_users_transfer_history( $user_id, $type = MYCRED_DEFAULT_TYPE_KEY, $key = NULL ) {

		if ( $key === NULL )
			$key = 'mycred_transactions';

		if ( $type != MYCRED_DEFAULT_TYPE_KEY && $type != '' )
			$key .= '_' . $type;

		$default = array(
			'frame'  => '',
			'amount' => 0
		);
		return mycred_apply_defaults( $default, mycred_get_user_meta( $user_id, $key, '', true ) );

	}
endif;

/**
 * Update Users Transfer History
 * @since 1.3.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_update_users_transfer_history' ) ) :
	function mycred_update_users_transfer_history( $user_id, $history, $type = MYCRED_DEFAULT_TYPE_KEY, $key = NULL ) {

		if ( $key === NULL )
			$key = 'mycred_transactions';

		if ( $type != MYCRED_DEFAULT_TYPE_KEY && $type != '' )
			$key .= '_' . $type;

		// Get current history
		$current = mycred_get_users_transfer_history( $user_id, $type, $key );

		// Reset
		if ( $history === true )
			$new_history = array(
				'frame'  => '',
				'amount' => 0
			);

		// Update
		else $new_history = mycred_apply_defaults( $current, $history );

		mycred_update_user_meta( $user_id, $key, '', $new_history );

	}
endif;
