<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Transfer Shortcode Render
 * Renders a transfer form that allows users to send points to other users.
 * @see http://codex.mycred.me/functions/mycred_transfer_render/
 * @since 0.1
 * @version 1.8
 */
if ( ! function_exists( 'mycred_transfer_render' ) ) :
	function mycred_transfer_render( $atts, $content = NULL ) {

		global $mycred_do_transfer;

		// Get Attributes
		$atts    = shortcode_atts( array(
			'button'          => '',
			'button_class'    => 'btn btn-primary btn-block btn-lg',
			'pay_to'          => '',
			'show_balance'    => 0,
			'show_limit'      => 0,
			'ref'             => 'transfer',
			'amount'          => '',
			'amount_placeholder' => '',
			'min'             => 0,
			'placeholder'     => '',
			'message_placeholder'   => '',
			'recipient_placeholder' => '',
			'types'           => '',
			'excluded'        => '',
			'recipient_label' => __( 'Recipient', 'mycred' ),
			'amount_label'    => __( 'Amount', 'mycred' ),
			'balance_label'   => __( 'Balance', 'mycred' ),
			'message_label'   => __( 'Message', 'mycred' )
		), $atts, MYCRED_SLUG . '_transfer' );

		// Prep
		$mycred_do_transfer = false;
		$transfer           = mycred_transfer();
		$output             = '';

		// Visitors can't do much
		if ( ! is_user_logged_in() ) {

			$output = do_shortcode( $transfer->get_error_message( 'login' ) );

		}

		// We are logged in
		else {

			// Create a new request. This will check if we meet the minimum requirements
			// and make sure we can initiate a transfer based on our setup
			// This function will also populate the transfer object to help us render the form
			if ( $transfer->new_instance( array(
				'reference'   => $atts['ref'],
				'minimum'     => $atts['min'],
				'amount'      => $atts['amount'],
				'recipient'   => $atts['pay_to'],
				'point_types' => $atts['types'],
				
			) ) ) {

				// We meet the minimum requirements! Yay, now let the get_transfer_form() function render our form
				$output = do_shortcode( $transfer->get_transfer_form( $atts ) );

			}

			// We can not make a new transfer
			else {

				// We either have no funds, exceeded limits (if used) or are excluded
				$output = do_shortcode( $transfer->get_error_message() );

			}

		}

		return do_shortcode( apply_filters( 'mycred_transfer_render', $output, $atts, $transfer ) );

	}
endif;
