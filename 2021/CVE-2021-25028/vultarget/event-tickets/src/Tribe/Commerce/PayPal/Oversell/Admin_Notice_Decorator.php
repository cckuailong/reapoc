<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Oversell__Admin_Notice_Decorator
 *
 * Decorates a policy to add an admin notice functionality.
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Oversell__Admin_Notice_Decorator implements Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface {

	/**
	 * @var Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface
	 */
	protected $policy;

	/**
	 * Tribe__Tickets__Commerce__PayPal__Oversell__Admin_Notice_Decorator constructor.
	 *
	 * @since 4.7
	 *
	 * @paramTribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface $instance
	 */
	public function __construct( $policy ) {
		$this->policy = $policy;
	}

	/**
	 * Whether this policy allows overselling or not.
	 *
	 * @since 4.7
	 *
	 * @return bool
	 */
	public function allows_overselling() {
		return $this->policy->allows_overselling();
	}

	/**
	 * Modifies the quantity of tickets that can actually be over-sold according to
	 * this policy.
	 *
	 * @since 4.7
	 *
	 * @param int $qty       The requested quantity
	 * @param int $inventory The current inventory value
	 *
	 * @return int The updated quantity
	 */
	public function modify_quantity( $qty, $inventory ) {
		$modified = $this->policy->modify_quantity( $qty, $inventory );

		$output = $this->style();
		$output .= $this->header_html( $qty, $inventory );

		/**
		 * Filters the default policy that should be used to handle overselling.
		 *
		 * @since 4.7
		 *
		 * @param string $default
		 * @param int    $post_id   The post ID
		 * @param int    $ticket_id The ticket post ID
		 * @param string $order_id  The Order PayPal ID (hash)
		 */
		$default = apply_filters( 'tribe_tickets_commerce_paypal_oversell_default_policy', 'sell-all', $this->get_post_id(), $this->get_ticket_id(), $this->get_order_id() );

		$output .= $this->options_html( $default );

		tribe_transient_notice( $this->notice_slug(), $output, 'type=warning' );

		return $modified;
	}

	/**
	 * Returns the embedded styles for the notice.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	protected function style() {
		return '<style>
			.tribe-tickets-paypal-oversell-radio + .tribe-tickets-paypal-oversell-radio {
				margin-top: .25em;
			}
			.tribe-tickets-paypal-oversell-submit {
				margin: .5em auto;
				padding-bottom: 2px;
			}
		}</style>';
	}

	/**
	 * Returns the notice header HTML.
	 *
	 * @since 4.7
	 * @since 4.10.9 Use customizable ticket name functions.
	 *
	 * @param int $qty
	 * @param int $inventory
	 *
	 * @return string
	 */
	protected function header_html( $qty, $inventory ) {
		$post_id         = $this->get_post_id();
		$post            = get_post( $post_id );
		$post_type       = empty( $post ) ? null : get_post_type_object( $post->post_type );

		if ( empty( $post ) ) {
			$post_title = __( 'An event', 'event-tickets' );
		} else {
			$edit_link  = $this->get_user_insensible_edit_link( $post_type, $post_id );
			$post_title = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $edit_link ),
				apply_filters( 'the_title', $post->post_title, $post_id )
			);
		}

		/** @var Tribe__Tickets__Commerce__PayPal__Links $links */
		$links             = tribe( 'tickets.commerce.paypal.links' );
		$order_paypal_link = $links->order_link( 'tag', $this->get_order_id(), __( 'in your PayPal account', 'event-tickets' ) );

		$message = __(
			'%1$s is oversold: there are more %2$s sold than the available capacity. This can occur when the PayPal transaction is not completed immediately, delaying the decrease in %3$s availability. Order %4$s includes %5$s %3$s(s). There are only %6$s %3$s(s) left. %7$s emails have not yet been sent for this order. Choose how to process this order from the options below.',
			'event-tickets'
		);

		$qty       = esc_html( $qty );
		$inventory = esc_html( $inventory );

		return sprintf(
			'<p class="tribe-tickets-paypal-oversell-header">%s</p>',
			sprintf(
				esc_html( $message ),
				esc_html( $post_title ),
				esc_html( tribe_get_ticket_label_plural_lowercase( 'oversold_message' ) ),
				esc_html( tribe_get_ticket_label_singular_lowercase( 'oversold_message' ) ),
				esc_html( $this->get_order_id() ),
				"<strong>{$qty}</strong>",
				"<strong>{$inventory}</strong>",
				esc_html( tribe_get_ticket_label_singular( 'oversold_message' ) )
			)
		);
	}

	/**
	 * Returns the policy post ID.
	 *
	 * @since 4.7
	 *
	 * @return int
	 */
	public function get_post_id() {
		return $this->policy->get_post_id();
	}

	/**
	 * Returns the post edit link skipping the `current_user_can` check.
	 *
	 * This might happen in the context of a PayPal request handling and the
	 * current user will be set to `0`.
	 *
	 * @param object $post_type
	 * @param int    $post_id
	 *
	 * @return string
	 */
	protected function get_user_insensible_edit_link( $post_type, $post_id ) {
		$action = '&amp;action=edit';

		return admin_url( sprintf( $post_type->_edit_link . $action, $post_id ) );
	}

	/**
	 * Returns the policy PayPal Order ID (hash).
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_order_id() {
		return $this->policy->get_order_id();
	}

	/**
	 * Returns the policy ticket post ID.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_ticket_id() {
		return $this->policy->get_ticket_id();
	}

	/**
	 * Returns the notice options HTML.
	 *
	 * @since 4.7
	 *
	 * @param string $default The default oversell policy that should be used.
	 *
	 * @return string
	 */
	protected function options_html( $default ) {
		$form_inside = '';

		$hidden_inputs = array(
			'tpp_action'   => Tribe__Tickets__Commerce__PayPal__Oversell__Request::$oversell_action,
			'tpp_order_id' => $this->get_order_id(),
			'tpp_slug'     => $this->notice_slug(),
		);

		foreach ( $hidden_inputs as $name => $value ) {
			$form_inside .= sprintf( '<input type="hidden" name="%s" value="%s">', esc_attr( $name ), esc_attr( $value ) );
		}

		$options = array();
		$options['sell-all']          = __( 'Create attendee records and send emails for all tickets in this order (overselling the event).', 'event-tickets' );
		// This option seems like an edge case, so, we are removing it for now
		// $options['sell-available'] = __( 'Create attendee records and send emails for some tickets in this order without overselling the event', 'event-tickets' );

		/** @var Tribe__Tickets__Commerce__PayPal__Links $links */
		$links             = tribe( 'tickets.commerce.paypal.links' );
		$options['no-oversell']       = sprintf(
			__( 'Delete all attendees for this order and do not email tickets. You may also want to refund the order %1$sin your PayPal account%2$s.', 'event-tickets' ),
			'<a href="' . esc_url( $links->order_link( 'link', $this->get_order_id() ) ). '">',
			'</a>'
		);

		foreach ( $options as $policy => $label ) {
			$form_inside .= sprintf(
				'<div class="tribe-tickets-paypal-oversell-radio"><input type="radio" radiogroup="order-%1$s-actions" value="%2$s" name="tpp_policy" '
				. checked( $default, $policy, false )
				. '><label >%3$s</label></div>',
				$this->get_order_id(),
				$policy,
				$label
			);
		}

		$form_inside .= sprintf(
			'<div class="tribe-tickets-paypal-oversell-submit"><input type="submit" value="%s" class="button button-secondary"></div>',
			__( 'Process order', 'event-tickets' )
		);

		return sprintf(
			'<div class="tribe-tickets-paypal-oversell-form"><form action="%s" method="get">%s</form></div>',
			$this->oversell_url(),
			$form_inside
		);
	}

	/**
	 * Returns the notice slug for this decorator.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	protected function notice_slug() {
		return "tickets-paypal-oversell-{$this->get_order_id()}-{$this->get_post_id()}";
	}

	/**
	 * Returns the URL that will be used to trigger an oversell for the Order from the admin UI.
	 *
	 * Note there is no nonce as the order might be generated during a POST request where the user
	 * is `0`.
	 *
	 * @return string
	 */
	protected function oversell_url() {
		return admin_url();
	}

	/**
	 * Returns the policy nice name.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->policy->get_name();
	}

	/**
	 * Handles surplus attendees generated from an oversell.
	 *
	 * @since 4.7
	 *
	 * @param array $oversold_attendees
	 *
	 * @return array A list of deleted attendees post IDs if any.
	 */
	public function handle_oversold_attendees( array $oversold_attendees ) {
		return $this->policy->handle_oversold_attendees( $oversold_attendees );
	}
}
