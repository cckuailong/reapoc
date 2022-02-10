<?php
/**
 * A class to handle email templates for restaurant reservations
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'rtbEmailTemplates' ) ) {
class rtbEmailTemplates {

	/**
	 * Available templates
	 *
	 * @param array $template_options
	 * @since 0.1
	 */
	public $template_options;


	/**
	 * Initialize the plugin and register hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		require_once( RTB_PLUGIN_DIR . '/includes/class-designer.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/load-notifications.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/integrations/business-profile.php' );

		$this->load_template_options();

		add_image_size( 'etfrtb_logo', 200, 200 );
	}

	/**
	 * Load the template options into the controller instance so that they're
	 * available from the customizer and the designer class
	 *
	 * @since 0.1
	 */
	public function load_template_options() {

		$template_options = array(
			'conversations.php' => array(
				'title' => __( 'Conversations', 'restaurant-reservations' ),
				'description' => __( 'A clean, simple email template for talking to directly to your customer.', 'restaurant-reservations' ),
			),
			'impressions.php' => array(
				'title' => __( 'Impressions', 'restaurant-reservations' ),
				'description' => __( 'A small email template that is great for making a quick impression.', 'restaurant-reservations' ),
			),
			'statement.php' => array(
				'title' => __( 'Statement', 'restaurant-reservations' ),
				'description' => __( 'A plain template for delivering a direct message.', 'restaurant-reservations' ),
			),
			'stationary.php' => array(
				'title' => __( 'Stationary', 'restaurant-reservations' ),
				'description' => __( 'An elegant template for sending a message with an air of sophistication.', 'restaurant-reservations' ),
			),
		);

		$this->template_options = apply_filters( 'etfrtb_template_options', $template_options );
	}

}

} // End if

/**
 * Load the customizer early enough to initialize it in "blank slate" mode.
 *
 * @since 0.1
 */
require_once( RTB_PLUGIN_DIR . '/includes/load-customizer.php' );
