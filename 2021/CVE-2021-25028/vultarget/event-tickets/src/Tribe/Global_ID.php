<?php

/**
 * Class Tribe__Tickets__Global_ID
 *
 * @since 4.8
 */
class Tribe__Tickets__Global_ID extends Tribe__Utils__Global_ID {

	/**
	 * Overrides the method from the base class to allow all types.
	 *
	 * @since 4.8
	 *
	 * @param string $name
	 *
	 * @return bool|mixed|null|string
	 */
	public function type( $name = null ) {
		if ( null !== $name ) {
			$this->type = $name;
		}

		return $this->type;
	}
}
