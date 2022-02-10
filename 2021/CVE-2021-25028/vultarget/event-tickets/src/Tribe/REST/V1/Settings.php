<?php

/**
 * Class Tribe__Tickets__REST__V1__Settings
 *
 * Adds and manages the ET REST API settings.
 */
class Tribe__Tickets__REST__V1__Settings {

	/**
	 * @var Tribe__Tickets__REST__V1__System
	 */
	protected $system;

	/**
	 * Tribe__Tickets__REST__V1__Settings constructor.
	 *
	 * @since 4.7.5
	 *
	 * @param Tribe__Tickets__REST__V1__System $system
	 */
	public function __construct( Tribe__Tickets__REST__V1__System $system ) {
		$this->system = $system;
	}

	/**
	 * @param array $fields
	 *
	 * @since 4.7.5
	 *
	 * @return array
	 */
	public function filter_tribe_addons_tab_fields( array $fields = array() ) {
		if ( ! $this->system->supports_wp_rest_api() ) {
			return $fields;
		}

		if ( ! $this->system->supports_et_rest_api() ) {
			return $fields;
		}

		return $this->add_fields( $fields );
	}

	/**
	 * @param array $fields
	 *
	 * @since 4.7.5
	 *
	 * @return array
	 */
	protected function add_fields( array $fields = array() ) {
		return $fields;
	}
}
