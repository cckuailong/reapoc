<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Notices
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Notices {

	/**
	 * Triggers the display of the missing PDT identity token notice.
	 *
	 * @since 4.7
	 */
	public function show_missing_identity_token_notice() {
		set_transient( $this->slug( 'show-missing-identity-token' ), '1', DAY_IN_SECONDS );
	}

	/**
	 * Hooks the class method to relevant filters and actions.
	 *
	 * @since 4.7
	 *
	 */
	public function hook() {
		tribe_notice(
			$this->slug( 'pdt-missing-identity-token' ),
			array( $this, 'render_missing_identity_token_notice' ),
			array(),
			array( $this, 'should_render_missing_identity_token_notice' )
		);
	}

	/**
	 * Renders (echoes) the missing PDT identity token admin notice.
	 *
	 * @since 4.7
	 */
	public function render_missing_identity_token_notice() {
		Tribe__Admin__Notices::instance()->render_paragraph(
			$this->slug( 'pdt-missing-identity-token' ),
			sprintf( '%s, <a href="%s" target="_blank">%s</a>.',
				esc_html__( 'PayPal is using PDT data but you have not set the PDT identity token', 'event-tickets' ),
				esc_url( admin_url() . '?page=tribe-common&tab=event-tickets#tribe-field-ticket-paypal-identity-token' ),
				esc_html__( 'set it here', 'event-tickets' )
			)
		);
	}

	/**
	 * Whether the missing PDT identity token notice should be rendered or not.
	 *
	 * @since 4.7
	 *
	 * @return bool
	 */
	public function should_render_missing_identity_token_notice() {
		$transient      = get_transient( $this->slug( 'show-missing-identity-token' ) );
		$identity_token = tribe_get_option( 'ticket-paypal-identity-token' );

		return ! empty( $transient ) && empty( $identity_token );
	}

	/**
	 * Builds a slug used by the class.
	 *
	 * @since 4.7
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function slug( $string ) {
		return 'tickets-commerce-paypal-' . $string;
	}
}
