<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbBlocks' ) ) {
/**
 * Class to create, edit and display blocks for the Gutenberg editor
 *
 * @since 0.0.1
 */
class rtbBlocks {

	/**
	 * Add hooks
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register' ) );

		add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );
	}

	/**
	 * Register blocks
	 */
	public function register() {

		if ( !function_exists( 'register_block_type' ) ) {
			return;
		}

		global $rtb_controller;

		$rtb_controller->register_assets();

		wp_register_script(
			'restaurant-reservations-blocks',
			RTB_PLUGIN_URL . '/assets/js/blocks.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' )
		);

		register_block_type( 'restaurant-reservations/booking-form', array(
			'editor_script' => 'restaurant-reservations-blocks',
			'editor_style' => 'rtb-booking-form',
			'render_callback' => 'rtb_print_booking_form',
			'attributes' => array(
				'location' => array(
					'type' => 'number',
					'default' => 0,
				),
			),
		) );

		add_action( 'admin_init', array( $this, 'register_admin' ) );
	}

	/**
	 * Register admin-only assets for block handling
	 */
	public function register_admin() {

		global $rtb_controller;

		$locations_enabled = !!$rtb_controller->locations->post_type;

		$location_options = array( array( 'value' => 0, 'label' => __('Ask the customer to select a location', 'restaurant-reservations' ) ) );
		if ($locations_enabled) {
			$locations = $rtb_controller->locations->get_location_options();
			foreach ( $locations as $id => $name ) {
				$location_options[] = array( 'value' => $id, 'label' => $name);
			}
		}

		wp_add_inline_script(
			'restaurant-reservations-blocks',
			sprintf(
				'var rtb_blocks = %s;',
				json_encode( array(
					'locationsEnabled' => $locations_enabled,
					'locations' => $location_options,
				) )
			),
			'before'
		);
	}

	/**
	 * Create a new category of blocks to hold our block
	 */
	public function add_block_category( $categories ) {
		
		$categories[] = array(
			'slug'  => 'rtb-blocks',
			'title' => __( 'Five Star Restaurant Reservations', 'restaurant-reservations' ),
		);

		return $categories;
	}	
}
} // endif
