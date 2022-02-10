<?php

/**
 * Allow including of Gutenberg Template
 *
 * @since 4.9
 */
class Tribe__Tickets__Editor__Template extends Tribe__Template {
	/**
	 * Building of the Class template configuration
	 *
	 * @since 4.9
	 */
	public function __construct() {
		$this->set_template_origin( Tribe__Tickets__Main::instance() );

		$this->set_template_folder( 'src/views' );

		// Configures this templating class to extract variables.
		$this->set_template_context_extract( true );

		// Uses the public folders.
		$this->set_template_folder_lookup( true );
	}

	/**
	 * Return the attributes of the template
	 *
	 * @since 4.9
	 *
	 * @param array $default_attributes
	 * @return array
	 */
	public function attributes( $default_attributes = [] ) {
		return wp_parse_args(
			$this->get( 'attributes', [] ),
			$default_attributes
		);
	}

	/**
	 * Return a specific attribute
	 *
	 * @since 4.9
	 *
	 * @param  mixed $default
	 * @return mixed
	 */
	public function attr( $index, $default = null ) {
		$attribute = $this->get( array_merge( [ 'attributes' ], (array) $index ), $default );

		return $attribute;
	}
}

