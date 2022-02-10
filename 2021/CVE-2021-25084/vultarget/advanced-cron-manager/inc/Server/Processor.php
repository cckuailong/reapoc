<?php
/**
 * Processor class
 * Blocks WP Cron execution
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Server;

/**
 * Processor class.
 */
class Processor {

	/**
	 * Settings class
	 *
	 * @var object
	 */
	public $settings;

	/**
	 * Constructor
	 *
	 * @param Settings $settings Settings class.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Blocks WP Cron default spawning on init action
	 *
	 * @return void
	 */
	public function block_cron_executions() {

		$settings = $this->settings->get_settings();

		if ( isset( $settings['server_enable'] ) && ! empty( $settings['server_enable'] ) ) {

			if ( ! defined( 'DISABLE_WP_CRON' ) ) {
				define( 'DISABLE_WP_CRON', true );
			}

			// Just in case the constant is already set to true.
			remove_action( 'init', 'wp_cron' );

		}

	}

}
