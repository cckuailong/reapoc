<?php
/**
 * Helps to ensure the original ecommerce-engine specific ticketing plugins
 * remain functional alongside The Events Calendar and Event Tickets.
 *
 * @todo consider removing this class once we are satisfied that enough users
 *       have transitioned away from legacy ticketing solutions
 */
class Tribe__Tickets__Legacy_Provider_Support {
	protected $active_legacy_modules = array();


	public function __construct() {
		add_action( 'init', array( $this, 'on_init' ), 100 );
	}

	/**
	 * Expects to be called late during the "init" action (giving ticket modules sufficient
	 * opportunity to register themselves).
	 */
	public function on_init() {
		$this->find_active_legacy_modules();

		if ( ! count( $this->active_legacy_modules ) ) {
			return;
		}

		add_action( 'tribe_events_tickets_metabox_edit_advanced', array( $this, 'add_fields' ), 5 );
		add_filter( 'tribe_events_tickets_ajax_ticket_edit', array( $this, 'add_fields_ajax' ) );
	}

	/**
	 * Scan for the presence of any "old school" ticketing providers.
	 *
	 * For each (if any) that is discovered, the main class name of that plugin is
	 * added to the $this->active_legacy_modules array.
	 */
	protected function find_active_legacy_modules() {
		$legacy_classes = array(
			'Tribe__Events__Tickets__Woo__Main',
			'Tribe__Events__Tickets__EDD__Main',
			'Tribe__Events__Tickets__Shopp__Main',
			'Tribe__Events__Tickets__Wpec__Main',
		);

		$active_ticket_modules = Tribe__Tickets__Tickets::modules();

		$this->active_legacy_modules = array_intersect(
			array_keys( $active_ticket_modules ),
			$legacy_classes
		);
	}

	/**
	 * Legacy ticketing modules relied on core The Events Calendar code to generate the price field,
	 * this method takes over that responsibility.
	 */
	public function add_fields( $price = null, $regular_price = null ) {
		$metabox_template = Tribe__Tickets__Main::instance()->plugin_path . 'src/admin-views/legacy-ticket-fields.php';

		foreach ( $this->active_legacy_modules as $legacy_identifier ) {
			include $metabox_template;
		}
	}

	/**
	 * When existing tickets are edited, the information used to populate the edit-ticket form
	 * is supplied by ajax along with any advanced meta fields.
	 *
	 * This method filters the response data to inject the necessary extra fields.
	 *
	 * @param array $response_data
	 *
	 * @return array
	 */
	public function add_fields_ajax( array $response_data ) {
		$sale_price    = $response_data['price'];
		$regular_price = $response_data['regular_price'];

		// If not on sale, remove the sale price (no point displaying it)
		if ( ! $response_data['on_sale'] ) {
			$sale_price = '';
		}

		ob_start();
		$this->add_fields( $sale_price, $regular_price );
		$extra_fields = ob_get_clean();

		$response_data['advanced_fields'] = $extra_fields . $response_data['advanced_fields'];
		return $response_data;
	}
}
