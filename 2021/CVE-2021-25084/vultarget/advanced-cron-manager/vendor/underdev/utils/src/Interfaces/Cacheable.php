<?php
/**
 * Cacheable interface
 * Implemented by Cache classes
 */

namespace underDEV\Utils\Interfaces;

/**
 * Cacheable interface
 */
interface Cacheable {

	/**
	 * Sets cache value
	 * @param mixed $value value to store
	 */
	public function set( $value );

	/**
	 * Adds cache if it's not already set
	 * @param mixed $value value to store
	 */
	public function add( $value );

	/**
	 * Gets value from cache
	 * @param  boolean $force true if cache will be forced to get from storage
	 * @return mixed          cached value
	 */
	public function get( $force );

	/**
	 * Deletes value from cache
	 */
	public function delete();

}
