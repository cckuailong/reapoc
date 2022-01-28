<?php


namespace MEC\Settings;


use MEC\Singleton;

class Settings extends Singleton {

	private $options;

	public function __construct() {

		$this->options = (array) get_option( 'mec_options' );
	}

	/**
	 * @param string|null $key
	 *
	 * @return false|mixed|void
	 */
	public function get_options( $key = null ) {

		if ( !is_null( $key ) ) {

			return isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
		}

		return $this->options;
	}

	/**
	 * @param string|null $key
	 *
	 * @return false|mixed|void
	 */
	public function get_settings( $key = null ) {


		if ( !is_null( $key ) ) {

			return isset( $this->options['settings'][ $key ] ) ? $this->options['settings'][ $key ] : null;
		}

		return isset( $this->options['settings'] ) ? $this->options['settings'] : array();
	}


}