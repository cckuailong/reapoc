<?php
namespace Tribe\Onboarding;

/**
 * Class Tour Abstract.
 *
 * @since TBD
 */
abstract class Tour_Abstract {

	/**
	 * The tour ID.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public $tour_id;

	/**
	 * Times to display the tour.
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
	 * Should the tour display.
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
			&& ( tribe( 'onboarding' )->get_views( $this->tour_id ) > $this->times_to_display )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Return the tour steps.
	 *
	 * @since TBD
	 *
	 * @return array The tour steps.
	 */
	abstract function steps();

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
	 * The tour data, publicly accessible.
	 *
	 * @since TBD.
	 *
	 * @param array $data An array with the tour data.
	 * @return array
	 */
	public function tour_data( array $data = [] ) {
		$data['steps']   = $this->steps();
		$data['classes'] = $this->css_classes();

		return $data;
	}
}
