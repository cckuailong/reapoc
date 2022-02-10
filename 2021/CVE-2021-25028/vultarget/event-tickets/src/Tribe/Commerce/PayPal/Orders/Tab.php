<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Orders__Tab
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Orders__Tab extends Tribe__Tabbed_View__Tab {
	/**
	 * @var bool
	 */
	protected $visible = true;

	/**
	 * Returns this tab slug.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_slug() {
		return Tribe__Tickets__Commerce__PayPal__Orders__Report::$tab_slug;
	}

	/**
	 * Returns this tab label
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'PayPal Orders', 'event-tickets' );
	}
}
