<?php
/**
 * Models an Tickets Commerce Attendee.
 *
 * @since    5.1.9
 *
 * @package  TEC\Tickets\Commerce\Models
 */

namespace TEC\Tickets\Commerce\Models;

use Tribe\Models\Post_Types\Base;
use TEC\Tickets\Commerce\Attendee;
use Tribe\Tickets\Plus\Attendee_Registration\IAC;
use Tribe__Utils__Array as Arr;

/**
 * Class Attendee.
 *
 * @since    5.1.9
 *
 * @package  TEC\Tickets\Commerce\Models
 */
class Attendee_Model extends Base {
	/**
	 * {@inheritDoc}
	 */
	protected function build_properties( $filter ) {
		try {
			$cache_this = $this->get_caching_callback( $filter );

			$post_id = $this->post->ID;

			$post_meta = get_post_meta( $post_id );

			$ticket_id = Arr::get( $post_meta, [ Attendee::$ticket_relation_meta_key, 0 ] );
			$order_id  = Arr::get( $post_meta, [ Attendee::$order_relation_meta_key, 0 ] );
			$event_id  = Arr::get( $post_meta, [ Attendee::$event_relation_meta_key, 0 ] );
			$user_id   = Arr::get( $post_meta, [ Attendee::$user_relation_meta_key, 0 ] );

			$ticket = tec_tc_get_ticket( $ticket_id );
			$order  = tec_tc_get_order( $this->post->post_parent );

			$is_product_deleted = empty( $ticket ) && ! $ticket instanceof \WP_Post;

			$checked_in           = Arr::get( $post_meta, [ Attendee::$checked_in_meta_key, 0 ] );
			$security             = Arr::get( $post_meta, [ Attendee::$security_code_meta_key, 0 ] );
			$opt_out              = tribe_is_truthy( Arr::get( $post_meta, [ Attendee::$optout_meta_key, 0 ] ) );
			$status               = $order->status_obj->get_name();
			$ticket_sent          = (int) Arr::get( $post_meta, [ Attendee::$ticket_sent_meta_key, 0 ] );
			$deleted_ticket_title = Arr::get( $post_meta, [ Attendee::$deleted_ticket_meta_key, 0 ] );
			$full_name            = Arr::get( $post_meta, [ Attendee::$full_name_meta_key, 0 ] );
			$email                = Arr::get( $post_meta, [ Attendee::$email_meta_key, 0 ] );
			$price_paid           = Arr::get( $post_meta, [ Attendee::$price_paid_meta_key, 0 ] );
			$currency             = Arr::get( $post_meta, [ Attendee::$currency_meta_key, 0 ] );
			$is_subscribed        = tribe_is_truthy( Arr::get( $post_meta, [ Attendee::$subscribed_meta_key, 0 ] ) );

			// Tries to determine an Attendee Unique ID.
			$ticket_unique_id = Arr::get( $post_meta, [ '_unique_id', 0 ] );
			$ticket_unique_id = empty( $ticket_unique_id ) ? $post_id : $ticket_unique_id;

			$ticket_title = ( ! $is_product_deleted ? $ticket->post_title : $deleted_ticket_title . ' ' . __( '(deleted)', 'event-tickets' ) );

			$is_purchaser = $email === $order->purchaser_email;

			$properties = [
				'order_id'        => $this->post->post_parent,
				'order_status'    => $status,
				'optout'          => $opt_out,
				'ticket'          => $ticket_title,
				'attendee_id'     => $post_id,
				'security'        => $security,
				'product_id'      => $ticket_id,
				'check_in'        => $checked_in,
				'ticket_sent'     => $ticket_sent,
				'price_paid'      => $price_paid,
				'currency'        => $currency,

				// Provider.
				'provider'        => $order->provider,
				'provider_slug'   => $order->provider_slug,

				// Purchaser.
				'purchaser_id'    => $order->purchaser['user_id'],
				'purchaser_name'  => $order->purchaser['full_name'],
				'purchaser_email' => $order->purchaser['email'],

				// Fields for Email Tickets.
				'event_id'        => $event_id,
				'ticket_name'     => $ticket_title,
				'user_id'         => $user_id,
				'holder_name'     => $full_name,
				'holder_email'    => $email,
				'ticket_id'       => $ticket_id,
				'qr_ticket_id'    => $post_id,
				'security_code'   => $security,

				// Handle initial Attendee flags.
				'is_subscribed'   => $is_subscribed,
				'is_purchaser'    => $is_purchaser,
			];
		} catch ( \Exception $e ) {
			return [];
		}

		return $properties;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_cache_slug() {
		return 'tc_attendees';
	}
}
