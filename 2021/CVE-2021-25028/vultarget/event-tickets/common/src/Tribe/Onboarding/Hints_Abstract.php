<?php
namespace Tribe\Onboarding;

/**
 * Class Hints Abstract.
 *
 * @since TBD
 */
abstract class Hints_Abstract {

	/**
	 * The hints ID.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public $hints_id;

	/**
	 * Times to display the hints.
	 *
	 * @since TBD
	 *
	 * @var int
	 */
	public $times_to_display;

	/**
	 * Return if it's on page where it should be displayed.
	 *
	 * @since TBD
	 *
	 * @return bool True if it is on page.
	 */
	public function is_on_page() {
		return false;
	}

	/**
	 * Should the hints display.
	 *
	 * @since TBD
	 *
	 * @return boolean True if it should display.
	 */
	public function should_display() {
		// Bail if it's not on the page we want to display.
		if ( ! $this->is_on_page() ) {
			return false;
		}

		// Bail if the `Times to display` is set and it was reached.
		if (
			is_numeric( $this->times_to_display )
			&& ( tribe( 'onboarding' )->get_views( $this->hints_id ) > $this->times_to_display )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Return the hints data.
	 *
	 * @since TBD
	 *
	 * @return array The hints.
	 */
	abstract function hints();

	/**
	 * Return the CSS classes.
	 *
	 * @since TBD
	 *
	 * @return array The CSS classes.
	 */
	public function css_classes() {
		return [];
	}

	/**
	 * The hints data, publicly accessible.
	 *
	 * @since TBD.
	 *
	 * @param array $data An array with the hints data.
	 * @return array
	 */
	public function hints_data( array $data = [] ) {
		$data['hints']   = $this->hints();
		$data['classes'] = $this->css_classes();

		return $data;
	}
}
