<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Get Email Notice
 * Returns the email notice object.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_email_notice' ) ) :
	function mycred_get_email_notice( $notice_id = false ) {

		global $mycred_email;

		if ( $notice_id === false || absint( $notice_id ) === 0 ) return false;

		if ( isset( $mycred_email )
			&& ( $mycred_email instanceof myCRED_Email )
			&& ( $notice_id === $mycred_email->post_id )
		) {
			return $mycred_email;
		}

		$mycred_email = new myCRED_Email( $notice_id );

		do_action( 'mycred_get_email_notice' );

		return $mycred_email;

	}
endif;

/**
 * User Wants Email Notice
 * Returns true if user has not selected to unsubscribe from this email address else false if they did.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_user_wants_email' ) ) :
	function mycred_user_wants_email( $user_id = false, $notice_id = false ) {

		if ( $user_id === false || absint( $user_id ) === 0 ) return false;

		$wants_email = true;
		$account     = mycred_get_account( $user_id );

		if ( isset( $account->email_block ) && ! empty( $account->email_block ) && in_array( $notice_id, $account->email_block ) )
			$wants_email = false;

		elseif ( ! isset( $account->email_block ) ) {

			$unsubscriptions = (array) mycred_get_user_meta( $user_id, 'mycred_email_unsubscriptions', '', true );
			if ( ! empty( $unsubscriptions ) && in_array( $notice_id, $unsubscriptions ) )
				$wants_email = false;

		}

		return apply_filters( 'mycred_email_notice_user_wants', $wants_email, $user_id, $notice_id );

	}
endif;

/**
 * Get Email Triggers
 * Retreaves the saved email triggers for a given point type.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_email_triggers' ) ) :
	function mycred_get_email_triggers( $point_type = MYCRED_DEFAULT_TYPE_KEY, $force = false ) {

		$generic_events = array(
			'any'          => array(),
			'positive'     => array(),
			'negative'     => array(),
			'zero'         => array(),
			'minus'        => array(),
			'badge_new'    => array(),
			'badge_level'  => array(),
			'rank_up'      => array(),
			'rank_down'    => array(),
			'transfer_out' => array(),
			'transfer_in'  => array(),
			'cashcred_approved'   => array(),
			'cashcred_pending'    => array(),
			'cashcred_cancel'	=>array()
		);

		$defaults = array(
			'generic'  => $generic_events,
			'specific' => array()
		);

		$setup    = (array) mycred_get_option( 'mycred-email-triggers-' . $point_type, $defaults );

		if ( empty( $setup ) || $force )
			$setup = $defaults;

		return apply_filters( 'mycred_get_email_triggers', $setup, $point_type, $force );

	}
endif;

/**
 * Add Email Trigger
 * Adds an email post to the nominated instance for a particular point type.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_add_email_trigger' ) ) :
	function mycred_add_email_trigger( $event_type = '', $instance = '', $notice_id = false, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$event_type = sanitize_key( $event_type );
		$instance   = sanitize_key( $instance );
		$notice_id  = absint( $notice_id );

		if ( empty( $event_type ) || empty( $instance ) || $notice_id === 0 ) return false;

		$triggers   = mycred_get_email_triggers( $point_type );

		if ( array_key_exists( $event_type, $triggers ) ) {

			if ( ! array_key_exists( $instance, $triggers[ $event_type ] ) ) {

				if ( ! is_array( $triggers[ $event_type ] ) )
					$triggers[ $event_type ] = array();

				$triggers[ $event_type ][ $instance ]   = array();
				$triggers[ $event_type ][ $instance ][] = $notice_id;

			}
			else {

				if ( empty( $triggers[ $event_type ] ) || ! in_array( $notice_id, $triggers[ $event_type ][ $instance ] ) )
					$triggers[ $event_type ][ $instance ][] = $notice_id;

			}

			$triggers = apply_filters( 'mycred_update_email_triggers', $triggers, $event_type, $instance, $notice_id, $point_type );

			if ( ! empty( $triggers ) )
				mycred_update_option( 'mycred-email-triggers-' . $point_type, $triggers );

			return true;

		}

		return false;

	}
endif;

/**
 * Add Email Trigger
 * Adds an email post to the nominated instance for a particular point type.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_delete_email_trigger' ) ) :
	function mycred_delete_email_trigger( $notice_id = false, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$notice_id  = absint( $notice_id );
		$point_type = sanitize_key( $point_type );

		if ( $notice_id === 0 ) return false;

		$triggers   = mycred_get_email_triggers( $point_type );
		$original   = $triggers;

		if ( ! empty( $triggers ) ) {

			// Generics - here the keys needs to be preserved, even if it's an empty array.
			if ( array_key_exists( 'generic', $triggers ) ) {
				foreach ( $triggers['generic'] as $instance => $notice_ids ) {

					if ( ! empty( $notice_ids ) && in_array( $notice_id, $notice_ids ) ) {

						$new_list = array();
						foreach ( $notice_ids as $id ) {

							$id = absint( $id );
							if ( $id !== 0 && $id !== $notice_id )
								$new_list[] = $id;

						}

						$triggers['generic'][ $instance ] = $new_list;

					}

				}

			}

			// Specific - here we only keep instances that have notice IDs, no empty values.
			if ( array_key_exists( 'specific', $triggers ) ) {
				foreach ( $triggers['specific'] as $instance => $notice_ids ) {

					// If our notice is in this array, remove it by building a new array
					// take this opportuniy to make sure we have integers and no zero values
					if ( ! empty( $notice_ids ) && in_array( $notice_id, $notice_ids ) ) {

						$new_list = array();
						foreach ( $notice_ids as $id ) {

							$id = absint( $id );
							if ( $id !== 0 && $id !== $notice_id )
								$new_list[] = $id;

						}

						if ( ! empty( $new_list ) )
							$triggers['specific'][ $instance ] = $new_list;

						else {

							unset( $triggers['specific'][ $instance ] );

						}

					}

					// No notice ID = should not be in here.
					elseif ( empty( $notice_ids ) ) {

						unset( $triggers['specific'][ $instance ] );

					}

				}
			}

			$triggers = apply_filters( 'mycred_delete_email_triggers', $triggers, $notice_id, $original, $point_type );

			if ( ! empty( $triggers ) )
				mycred_update_option( 'mycred-email-triggers-' . $point_type, $triggers );

			return true;

		}

		return false;

	}
endif;

/**
 * Get Email Triggers
 * Retreaves the saved email triggers for a given point type.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_triggered_emails' ) ) :
	function mycred_get_triggered_emails( $mycred_event = array(), $new_balance = 0 ) {

		extract( shortcode_atts( array(
			'ref'     => '',
			'user_id' => 0,
			'amount'  => 0,
			'entry'   => '',
			'ref_id'  => 0,
			'data'    => '',
			'type'    => MYCRED_DEFAULT_TYPE_KEY
		), $mycred_event ) );

		$notices  = array();
		if ( empty( $ref ) || $user_id == 0 ) return $notices;

		$triggers = mycred_get_email_triggers( $type );

		$gain     = ( $amount > 0 ) ? true : false;
		$zero     = ( $new_balance == 0 ) ? true : false;
		$minus    = ( $new_balance < 0 ) ? true : false;

		if ( ! empty( $triggers ) ) {

			// Generic - any event
			if ( ! empty( $triggers['generic']['any'] ) ) {
				foreach ( $triggers['generic']['any'] as $notice_id ) {

					if ( ! in_array( $notice_id, $notices ) )
						$notices[] = $notice_id;

				}
			}

			// Point gains
			if ( $gain && ! empty( $triggers['generic']['positive'] ) ) {
				foreach ( $triggers['generic']['positive'] as $notice_id ) {

					if ( ! in_array( $notice_id, $notices ) )
						$notices[] = $notice_id;

				}
			}

			// Point loss
			elseif ( ! $gain && ! empty( $triggers['generic']['negative'] ) ) {
				foreach ( $triggers['generic']['negative'] as $notice_id ) {

					if ( ! in_array( $notice_id, $notices ) )
						$notices[] = $notice_id;

				}
			}

			// Balance is zero
			if ( $zero && ! empty( $triggers['generic']['zero'] ) ) {
				foreach ( $triggers['generic']['zero'] as $notice_id ) {

					if ( ! in_array( $notice_id, $notices ) )
						$notices[] = $notice_id;

				}
			}

			// Balance is negative
			if ( $minus && ! empty( $triggers['generic']['minus'] ) ) {
				foreach ( $triggers['generic']['minus'] as $notice_id ) {

					if ( ! in_array( $notice_id, $notices ) )
						$notices[] = $notice_id;

				}
			}

            // check if trasfer trigger has notice id
            if ( ! empty( $ref ) && $ref == 'transfer' && floatval( $amount ) > 0 && ! empty( $triggers['generic']['transfer_in'] ) ) {
                foreach ( $triggers['generic']['transfer_in'] as $notice_id ) {

                    if ( ! in_array( $notice_id, $notices ) )
                        $notices[] = $notice_id;

                }
            }

            // check if trasfer trigger has notice ids
            if ( ! empty( $ref ) && $ref == 'transfer' && floatval( $amount ) < 0 && ! empty( $triggers['generic']['transfer_out'] ) ) {
                foreach ( $triggers['generic']['transfer_out'] as $notice_id ) {
                    if ( ! in_array( $notice_id, $notices ) )
                        $notices[] = $notice_id;

                }
            }

            // Specific instances based on reference
			if ( ! empty( $triggers['specific'] ) && array_key_exists( $ref, $triggers['specific'] ) && ! empty( $triggers['specific'][ $ref ] ) ) {
				foreach ( $triggers['specific'][ $ref ] as $notice_id ) {

					if ( ! in_array( $notice_id, $notices ) )
						$notices[] = $notice_id;

				}
			}

		}

		return apply_filters( 'mycred_get_triggered_emails', $notices, $triggers, $mycred_event, $new_balance );

	}
endif;

/**
 * Get Event Emails
 * Returns all the notice IDs that exists for a given event type + instance.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_event_emails' ) ) :
	function mycred_get_event_emails( $point_type = MYCRED_DEFAULT_TYPE_KEY, $event_type = '', $instance = '' ) {

		$triggers = mycred_get_email_triggers( $point_type );
		$notices  = array();

		if ( array_key_exists( $event_type, $triggers ) ) {

			if ( array_key_exists( $instance, $triggers[ $event_type ] ) && ! empty( $triggers[ $event_type ][ $instance ] ) )
				$notices = $triggers[ $event_type ][ $instance ];

		}

		return apply_filters( 'mycred_get_event_emails', $notices, $triggers, $point_type, $event_type, $instance );

	}
endif;

/**
 * Send New Email
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_send_new_email' ) ) :
	function mycred_send_new_email( $notice_id = false, $event = array(), $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( $notice_id === false || get_post_status ( $notice_id ) !== 'publish' ) return false;

		$notice_id  = absint( $notice_id );
		$email      = mycred_get_email_notice( $notice_id );

        //if $email notice object is empty skip this 
        if (!empty($email->settings) ) {

            // Schedule for later
            if ( $email->emailnotices['send'] != '' ){
                $email->schedule( $event, $point_type );

            }

            // Run now
            else {

                $email->send( $event, $point_type );

            }
        }
		return true;

	}
endif;

/**
 * Get Email Content Type
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_email_content_type' ) ) :
	function mycred_get_email_content_type() {

		$format   = 'text/plain';
		$mycred_version = (float) explode(' ', myCRED_VERSION)[0];
			if( $mycred_version >= 1.8 ){
				$settings = mycred_get_addon_settings( 'emailnotices' );
			}else{
				$settings = mycred_get_addon_settings( 'emails' );

			}
		if ( $settings['use_html'] )
			$format = 'text/html';

		return apply_filters( 'mycred_get_email_content_type', $format, $settings );

	}
endif;

/**
 * Cron Schedule Handler
 * @since 1.3
 * @version 1.1
 */
if ( ! function_exists( 'mycred_email_notice_cron_job' ) ) :
	function mycred_email_notice_cron_job() {

		if ( ! class_exists( 'myCRED_Email_Notice_Module' ) ) return;

		global $wpdb;

		$pending = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->usermeta} WHERE meta_key = %s;", mycred_get_meta_key( 'mycred_scheduled_email_notices' ) ) );

		if ( $pending ) {

			foreach ( $pending as $pending_notice ) {

				$notice     = maybe_unserialize( $pending_notice->meta_value );

				$notice_id  = absint( $notice['notice_id'] );
				$email      = mycred_get_email_notice( $notice_id );

				// Send email now
				$email->send( $notice['event'], $notice['point_type'] );

				// Delete record
				mycred_delete_user_meta( $pending_notice->user_id, 'mycred_scheduled_email_notices', '', $notice );

			}

		}

	}
endif;

/**
 * Get Email Settings
 * @since 1.4
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_email_subscriptions' ) ) :
	function mycred_get_email_settings( $post_id ) {

		$emailnotices  = mycred_get_addon_settings( 'emailnotices' );
		$settings      = (array) mycred_get_post_meta( $post_id, 'mycred_email_settings', true );

		if ( $settings == '' || empty($settings) )
			$settings = array();

		// Defaults
		$default = array(
			'recipient'     => 'user',
			'senders_name'  => $emailnotices['from']['name'],
			'senders_email' => $emailnotices['from']['email'],
			'reply_to'      => $emailnotices['from']['reply_to'],
			'label'         => get_the_title($post_id)
		);

		$settings = mycred_apply_defaults( $default, $settings );
		return apply_filters( 'mycred_email_notice_settings', $settings, $post_id );
	}
endif;