<?php

namespace WebpConverter\Conversion\Cron;

use WebpConverter\HookableInterface;

/**
 * Adds time interval to cron event.
 */
class Schedules implements HookableInterface {

	const CRON_SCHEDULE = 'webpc_cron';

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'cron_schedules', [ $this, 'add_cron_interval' ] );
	}

	/**
	 * Adds new cron schedule.
	 *
	 * @param array[] $schedules Cron schedules.
	 *
	 * @return array[] Cron schedules.
	 * @internal
	 */
	public function add_cron_interval( array $schedules ): array {
		$schedules[ self::CRON_SCHEDULE ] = [
			'interval' => apply_filters( 'webpc_cron_interval', HOUR_IN_SECONDS ),
			'display'  => 'WebP Converter for Media',
		];
		return $schedules;
	}
}
