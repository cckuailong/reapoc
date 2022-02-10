<?php
/**
 * Notice for the Stellar Sale
 *
 * @since 4.14.2
 */

namespace Tribe\Admin\Notice\Marketing;

/**
 * Class Stellar_Sale
 *
 * @since 4.14.2
 *
 * @package Tribe\Admin\Notice\Marketing
 */
class Stellar_Sale extends \Tribe\Admin\Notice\Date_Based {
	/**
	 * {@inheritDoc}
	 */
	public $slug = 'stellar-sale';

	/**
	 * {@inheritDoc}
	 */
	public $start_date = 'July 28th, 2021';

	/**
	 * {@inheritDoc}
	 *
	 * 1pm UTC is 6am PDT (-7) and 9am EDT (-4)
	 */
	public $start_time = 13;

	/**
	 * {@inheritDoc}
	 */
	public $end_date = 'August 4th, 2021';

	/**
	 * {@inheritDoc}
	 *
	 * 5am UTC is 9pm PST (-8) and 12am EST (-5)
	 */
	public $end_time = 5;

	/**
	 * {@inheritDoc}
	 */
	public function display_notice() {
		\Tribe__Assets::instance()->enqueue( [ 'tribe-common-admin' ] );

		// Used in the template.
		$cta_url  = 'https://evnt.is/1aqi';
		$icon_url = \Tribe__Main::instance()->plugin_url . 'src/resources/images/icons/sale-burst.svg';

		ob_start();

		include \Tribe__Main::instance()->plugin_path . 'src/admin-views/notices/tribe-stellar-sale.php';

		return ob_get_clean();
	}
}
