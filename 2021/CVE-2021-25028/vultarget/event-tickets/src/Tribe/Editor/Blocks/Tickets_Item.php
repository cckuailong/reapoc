<?php

/**
 * Tickets block Setup
 */
class Tribe__Tickets__Editor__Blocks__Tickets_Item extends Tribe__Editor__Blocks__Abstract {

	/**
	 * Which is the name/slug of this block
	 *
	 * @since 4.9.2
	 *
	 * @return string
	 */
	public function slug() {
		return 'tickets-item';
	}

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it
	 *
	 * @since 4.9.2
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes = [] ) {
		// This block has no render.
		return '';
	}
}
