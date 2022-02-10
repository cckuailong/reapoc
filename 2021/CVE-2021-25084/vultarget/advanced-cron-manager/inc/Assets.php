<?php
/**
 * Assets class
 * Loads plugin assets
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager;

use underDEV\Utils;

/**
 * Assets class
 */
class Assets {

	/**
	 * Current plugin version
	 *
	 * @var string
	 */
	public $plugin_version;

	/**
	 * Files class
	 *
	 * @var object
	 */
	public $files;

	/**
	 * ScreenRegisterer
	 *
	 * @var object
	 */
	public $screen;

	/**
	 * Constructor
	 *
	 * @param string           $version Plugin version.
	 * @param Utils\Files      $files   Files object.
	 * @param ScreenRegisterer $screen  ScreenRegisterer object.
	 */
	public function __construct( $version, Utils\Files $files, ScreenRegisterer $screen ) {

		$this->plugin_version = $version;
		$this->files          = $files;
		$this->screen         = $screen;

	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $current_page_hook Page hook name.
	 * @return void
	 */
	public function enqueue_admin( $current_page_hook ) {

		if ( $current_page_hook !== $this->screen->get_page_hook() ) {
			return;
		}

		wp_register_script( 'sprintf', $this->files->vendor_asset_url( 'sprintf', 'sprintf.min.js' ), array(), '1.1.1', true );

		if ( ! wp_script_is( 'wp-hooks', 'registered' ) ) {
			wp_register_script( 'wp-hooks', $this->files->vendor_asset_url( 'wp', 'hooks.js' ), array( 'jquery' ), $this->plugin_version, true );
		}

		wp_register_script( 'materialize', $this->files->vendor_asset_url( 'materialize', 'js/materialize.min.js' ), array( 'jquery' ), '0.98.2', true );
		wp_register_style( 'materialize', $this->files->vendor_asset_url( 'materialize', 'css/materialize.min.css' ), array(), '0.98.2' );

		wp_enqueue_style( 'advanced-cron-manager', $this->files->asset_url( 'css', 'style.css' ), array(), $this->plugin_version );
		wp_enqueue_script( 'advanced-cron-manager', $this->files->asset_url( 'js', 'scripts.min.js' ), array( 'jquery', 'sprintf', 'materialize', 'wp-hooks' ), $this->plugin_version, true );

		wp_localize_script( 'advanced-cron-manager', 'advanced_cron_manager', array(
			'i18n' => array(
				'executed_with_errors' => __( 'Event has been executed with errors', 'advanced-cron-manager' ),
				'events'               => __( 'events', 'advanced-cron-manager' ),
				'removing'             => __( 'Removing...', 'advanced-cron-manager' ),
				'pausing'              => __( 'Pausing...', 'advanced-cron-manager' ),
				'saving'               => __( 'Saving...', 'advanced-cron-manager' ),
			),
		) );

		do_action( 'advanced-cron-manager/screen/enqueue', $current_page_hook );

	}

}
