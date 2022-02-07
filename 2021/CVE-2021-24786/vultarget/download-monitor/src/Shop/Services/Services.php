<?php

namespace Never5\DownloadMonitor\Shop\Services;

use Never5\DownloadMonitor\Dependencies\Pimple;

class Services {

	/** @var Services */
	private static $instance = null;

	/** @var Pimple\Container */
	private $container;

	/**
	 * Services constructor.
	 */
	private function __construct() {
		$this->container = new Pimple\Container();
		$provider        = new ServiceProvider();
		$provider->register( $this->container );
	}

	/**
	 * Singleton get method
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return Services
	 */
	public static function get() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get service with $service as key in Pimple container
	 *
	 * @param string $service
	 *
	 * @return mixed
	 */
	public function service( $service ) {
		return $this->container[ $service ];
	}

	/**
	 * Replace an existing service with a new one.
	 * Used mostly for inject unit test mocks.
	 * Don't use this on _real_ websites unless you REALLY know what you're doing.
	 *
	 * @param $service_key
	 * @param $new_service
	 */
	public function replace( $service_key, $new_service ) {
		$this->container[ $service_key ] = $new_service;
	}

}