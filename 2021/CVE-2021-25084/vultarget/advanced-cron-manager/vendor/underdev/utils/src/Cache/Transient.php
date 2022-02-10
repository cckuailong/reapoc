<?php
/**
 * Transient Cache
 * @uses wp transient functions
 * @uses Cacheable Interface
 */

namespace underDEV\Utils\Cache;
use underDEV\Utils\Cache\Cache;
use underDEV\Utils\Interfaces\Cacheable;

/**
 * Transient cache
 */
class Transient extends Cache implements Cacheable {

	/**
	 * Cache expiration in seconds
	 * @var integer
	 */
	protected $expiration;

	/**
	 * Constructor
	 * @param string  $key        cache unique key
	 * @param integer $expiration expiration in seconds
	 */
	public function __construct( $key, $expiration = 0 ) {

		parent::__construct( $key );

		$this->expiration = $expiration;

	}

	/**
	 * Sets cache value
	 * @param mixed   $value value to store
	 * @return object $this
	 */
	public function set( $value ) {
		set_transient( $this->key, $value, $this->expiration );
		return $this;
	}

	/**
	 * Adds cache if it's not already set
	 * @param mixed   $value value to store
	 * @return object $this
	 */
	public function add( $value ) {
		if ( false === $this->get() ) {
		    $this->set( $value );
		}
		return $this;
	}

	/**
	 * Gets value from cache
	 * @param  boolean $force not used, transients are always get from storage
	 * @return mixed          cached value
	 */
	public function get( $force = true ) {
		return get_transient( $this->key );
	}

	/**
	 * Deletes value from cache
	 * @return object $this
	 */
	public function delete() {
		delete_transient( $this->key );
		return $this;
	}

}
