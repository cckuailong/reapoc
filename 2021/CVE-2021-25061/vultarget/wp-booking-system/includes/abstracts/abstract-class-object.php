<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all object
 *
 */
abstract class WPBS_Base_Object {


	/**
	 * Constructor
	 *
	 */
	public function __construct( $object ) {

		foreach( get_object_vars( $object ) as $key => $value ) {

			if( ! property_exists( $object, $key ) )
				continue;

			$this->$key = $value;

		}

	}


	/**
	 * Getter
	 *
	 * @param string $property
	 *
	 */
	public function get( $property = '' ) {

		if( method_exists( $this, 'get_' . $property ) )
			return $this->{'get_' . $property}();
		else
			return $this->$property;

	}


	/**
	 * Returns the object attributes and their values as an array 
	 *
	 */
	public function to_array() {

		return get_object_vars( $this );

	}

}