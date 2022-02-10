<?php
/**
 * Handles registering and setup for assets on Tickets.
 *
 * @since   5.1.6
 *
 * @package TEC\Tickets
 */

namespace TEC\Tickets;

use \tad_DI52_ServiceProvider;

/**
 * Class Assets.
 *
 * @since   5.1.6
 *
 * @package TEC\Tickets
 */
class Assets extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.1.6
	 */
	public function register() {
		$plugin = tribe( 'tickets.main' );

		tribe_asset(
			$plugin,
			'tribe-tickets-provider',
			'tickets-provider.js',
			[
				'tribe-common',
			],
			null,
			[
				'localize' => [
					'name' => 'tecTicketsSettings',
					'data' => [
						'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG
					],
				],
			]
		);

	}
}
