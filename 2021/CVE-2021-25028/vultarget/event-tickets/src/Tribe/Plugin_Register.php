<?php

/**
 * Class Tribe__Tickets__Plugin_Register
 */
class Tribe__Tickets__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	protected $main_class   = 'Tribe__Tickets__Main';
	protected $dependencies = array(
		'addon-dependencies' => array(
			'Tribe__Tickets_Plus__Main'               => '5.3.0-dev',
			'Tribe__Events__Community__Tickets__Main' => '4.7.2-dev',
		),
	);

	public function __construct() {
		$this->base_dir = EVENT_TICKETS_MAIN_PLUGIN_FILE;
		$this->version  = Tribe__Tickets__Main::VERSION;

		$this->register_plugin();
	}
}
