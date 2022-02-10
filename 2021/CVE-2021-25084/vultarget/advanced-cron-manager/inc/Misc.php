<?php
/**
 * Misc class
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager;

use underDEV\Utils;

/**
 * Misc class.
 */
class Misc {

	/**
	 * View class
	 *
	 * @var object
	 */
	public $view;

	/**
	 * Constructor
	 *
	 * @param Utils\View $view View class.
	 */
	public function __construct( Utils\View $view ) {
		$this->view = $view;
	}

	/**
	 * Loads Notification plugin promo part
	 *
	 * @return void
	 */
	public function load_notification_promo_part() {
		$this->view->get_view( 'misc/notification-promo' );
	}

	/**
	 * Adds the plugin action link on Plugins table
	 *
	 * @param array $links links array.
	 * @return array
	 */
	public function plugin_action_link( $links ) {
		$links[] = '<a href="' . esc_url( get_admin_url( null, 'tools.php?page=advanced-cron-manager' ) ) . '">' . esc_html__( 'Cron Manager' ) . '</a>';
		return $links;
	}

}
