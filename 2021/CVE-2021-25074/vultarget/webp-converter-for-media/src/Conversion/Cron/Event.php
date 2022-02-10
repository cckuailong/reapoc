<?php

namespace WebpConverter\Conversion\Cron;

use WebpConverter\HookableInterface;
use WebpConverter\PluginData;
use WebpConverter\Settings\Option\ExtraFeaturesOption;

/**
 * Adds cron event that converts images.
 */
class Event implements HookableInterface {

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	public function __construct( PluginData $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

	const CRON_ACTION = 'webpc_regenerate_all';

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'init', [ $this, 'add_cron_event' ] );
	}

	/**
	 * Initializes cron event to convert all images.
	 *
	 * @return void
	 * @internal
	 */
	public function add_cron_event() {
		if ( wp_next_scheduled( self::CRON_ACTION )
			|| ! ( $settings = $this->plugin_data->get_plugin_settings() )
			|| ! in_array( ExtraFeaturesOption::OPTION_VALUE_CRON_ENABLED, $settings[ ExtraFeaturesOption::OPTION_NAME ] ) ) {
			return;
		}

		wp_schedule_event( time(), Schedules::CRON_SCHEDULE, self::CRON_ACTION );
	}
}
