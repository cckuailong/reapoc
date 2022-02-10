<?php


/**
 * Class Tribe__Tickets__Status__Manager
 *
 * @since 4.10
 */
class Tribe__Tickets__Status__Manager {

	/**
	 * Initial Active Modules using Plugin Names
	 *
	 * @var array
	 */
	public $initial_active_modules;

	/**
	 * Active Modules Slugs
	 *
	 * @var array
	 */
	protected $module_slugs = [
		'Tribe__Tickets_Plus__Commerce__EDD__Main'         => 'edd',
		'Tribe__Tickets__RSVP'                             => 'rsvp',
		'Tribe__Tickets__Commerce__PayPal__Main'           => 'tpp',
		'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' => 'woo',
		\TEC\Tickets\Commerce\Module::class                => \TEC\Tickets\Commerce::ABBR,
	];
	/**
	 * Active Modules
	 *
	 * @var array
	 */
	protected $active_modules;

	/**
	 * An array of status objects for WooCommerce Tickets
	 *
	 * @var array
	 */
	protected $status_managers = [
		'edd'                       => 'Tribe__Tickets_Plus__Commerce__EDD__Status_Manager',
		'rsvp'                      => 'Tribe__Tickets__RSVP__Status_Manager',
		'tpp'                       => 'Tribe__Tickets__Commerce__PayPal__Status_Manager',
		'woo'                       => 'Tribe__Tickets_Plus__Commerce__WooCommerce__Status_Manager',
	];

	/**
	 * An array of status objects for all active commerces
	 *
	 * @var array
	 */
	protected $statuses = [];

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @since 4.10
	 *
	 * @static
	 * @return Tribe__Tickets__Status__Manager
	 */
	public static function get_instance() {
		return tribe( 'tickets.status' );
	}

	/**
	 * Hook
	 *
	 * @since 4.10
	 */
	public function hook() {
		add_action( 'init', [ $this, 'setup' ] );
	}

	/**
	 * Setup the Manager Class
	 *
	 * @since 4.10
	 *
	 */
	public function setup() {
		$this->initial_active_modules = Tribe__Tickets__Tickets::modules();
		$this->convert_initial_active_modules();
		$this->get_statuses_by_provider();
	}

	/**
	 * Convert Name of Active Modules to slugs
	 *
	 * @since 4.10
	 *
	 */
	protected function convert_initial_active_modules() {

		foreach ( $this->initial_active_modules as $module_class => $module_name ) {
			if ( isset( $this->module_slugs[ $module_class ] ) ) {
				$this->active_modules[ $module_class ] = $this->module_slugs[ $module_class ];
			}
		}

	}

	/**
	 * Get the statuses for each provider that is active and has a manager
	 *
	 * @since 4.10
	 *
	 */
	protected function get_statuses_by_provider() {

		$status_managers = $this->get_status_managers();

		if ( ! is_array( $this->active_modules ) ) {
			return;
		}

		foreach ( $this->active_modules as $module_class => $module_name ) {

			if ( ! isset( $status_managers[ $module_name ] ) || ! class_exists( $status_managers[ $module_name ] ) ) {
				continue;
			}

			$status_class                   = $status_managers[ $module_name ];
			$this->statuses[ $module_name ] = new $status_class();
		}

	}

	/**
	 * Get the Active Modules
	 *
	 * @since 4.10
	 *
	 * @return array
	 */
	public function get_active_modules() {
		$this->convert_initial_active_modules();

		return $this->active_modules;
	}

	/**
	 * Get the Status Manager Array
	 *
	 * @since 4.10
	 *
	 * @return array
	 */
	public function get_status_managers() {
		return $this->status_managers;
	}

	/**
	 * Get the Trigger Status for Ticket Generation or Sending for a given eCommerce
	 *
	 * @since 4.10
	 *
	 * @param $commerce string a string of the Commerce System to get statuses from
	 *
	 * @return array an array of the commerce's statuses and name matching the provide action
	 */
	public function get_trigger_statuses( $commerce ) {

		$trigger_statuses = [];

		$commerce = $this->get_provider_slug( $commerce );

		if ( ! isset( $this->statuses[ $commerce ]->statuses ) ) {
			return $trigger_statuses;
		}

		$filtered_statuses = wp_list_filter(
			$this->statuses[ $commerce ]->statuses,
			[ 'trigger_option' => true ]
		);

		foreach ( $filtered_statuses as $status ) {
			$trigger_statuses[ $status->provider_name ] = $status->name;
		}

		return $trigger_statuses;

	}

	/**
	 * Return an array of Statuses for an action with the provider Commerce
	 *
	 * @since 4.10
	 * @since 4.10.5 - add nicename parameter
	 *
	 * @param $action   string a string of the action to filter
	 * @param $commerce string a string of the Commerce System to get statuses from
	 * @param $operator string a string of the default 'AND', 'OR', 'NOT' to change the criteria
	 * @param $nicename bool a boolean of whether to return the name of the status
	 *
	 * @return array an array of the commerce's statuses matching the provide action
	 */
	public function get_statuses_by_action( $action, $commerce, $operator = 'AND', $nicename = false ) {

		$trigger_statuses = [];

		$commerce = $this->get_provider_slug( $commerce );

		if ( ! isset( $this->statuses[ $commerce ]->statuses ) ) {
			return $trigger_statuses;
		}

		if ( 'all' === $action ) {
			$filtered_statuses = $this->statuses[ $commerce ]->statuses;
		} elseif ( is_array( $action ) ) {
			$criteria = [];
			foreach ( $action as $name ) {
				$criteria[ $name ] = true;
			}
			$filtered_statuses = wp_list_filter( $this->statuses[ $commerce ]->statuses, $criteria, $operator );
		} else {
			$filtered_statuses = wp_list_filter(
				$this->statuses[ $commerce ]->statuses,
				[ $action => true ]
			);
		}

		foreach ( $filtered_statuses as $status ) {

			// if nicename is true then only return that name for a given status
			if ( $nicename ) {
				$trigger_statuses[] = $status->name;
				continue;
			}

			$trigger_statuses[] = $status->provider_name;

			if ( ! empty( $status->additional_names ) ) {
				$trigger_statuses = $this->add_additional_names_to_array( $trigger_statuses, $status->additional_names );
			}
		}

		return $trigger_statuses;

	}

	/**
	 * Return an array of Statuses for a provider Commerce
	 *
	 * @since 4.10
	 *
	 * @param $commerce string a string of the Commerce System to get statuses from
	 *
	 * @return array an array of the commerce's statuses
	 */
	public function get_all_provider_statuses( $commerce ) {

		$trigger_statuses = [];

		$commerce = $this->get_provider_slug( $commerce );

		if ( ! isset( $this->statuses[ $commerce ]->statuses ) ) {
			return $trigger_statuses;
		}

		return $this->statuses[ $commerce ]->statuses;

	}

	/**
	 * Return an array of Statuses for a Commerce with label and stock attributes
	 *
	 * @since 4.10
	 *
	 * @param $commerce string a string of the Commerce System to get statuses from
	 *
	 * @return array an array of statues with label and stock attributes
	 */
	public function get_status_options( $commerce ) {

		static $status_options;

		$commerce = $this->get_provider_slug( $commerce );

		if ( ! isset( $this->statuses[ $commerce ]->statuses ) ) {
			return [];
		}

		if ( ! empty( $status_options[ $commerce ] ) ) {
			return $status_options[ $commerce ];
		}

		$filtered_statuses = $this->statuses[ $commerce ]->statuses;

		foreach ( $filtered_statuses as $status ) {
			$status_options[ $commerce ][ $status->provider_name ] = [
				'label'             => __( $status->name, 'event-tickets' ),
				'decrease_stock_by' => empty( $status->count_completed ) ? 0 : 1,
			];
		}

		return $status_options[ $commerce ];

	}

	/**
	 * Get all the Status Classes for a given Commerce
	 *
	 * @since 4.10
	 *
	 * @param $commerce string a string of the Commerce System to get statuses from
	 *
	 * @return
	 */
	public function get_providers_status_classes( $commerce ) {

		$commerce = $this->get_provider_slug( $commerce );

		if ( ! isset( $this->statuses[ $commerce ] ) ) {
			return [];
		}

		return $this->statuses[ $commerce ];

	}

	/**
	 * Get the Completed Status by Commerce Provider Class Name
	 *
	 * @since 4.10.5
	 *
	 * @param string|object $provider_name an object or string of a commerce main class name
	 *
	 * @return array
	 */
	public function get_completed_status_by_provider_name( $provider_name ) {

		if ( is_object( $provider_name ) ) {
			$provider_name = get_class( $provider_name );
		}

		$abbreviated_name = $this->get_provider_slug( $provider_name );

		$filtered_statuses = wp_list_filter(
			$this->statuses[ $abbreviated_name ]->statuses,
			[ 'count_completed' => true ]
		);


		foreach ( $filtered_statuses as $status ) {
			$trigger_statuses[] = $status->provider_name;

			if ( ! empty( $status->additional_names ) ) {
				$trigger_statuses = $this->add_additional_names_to_array( $trigger_statuses, $status->additional_names );
			}

		}


		return $trigger_statuses;

	}

	/**
	 * Add additional names a status might be known as
	 *
	 * @since 4.10.5
	 *
	 * @param array $trigger_statuses an array of statues
	 * @param array $additional_names an array of additional names a status might be known as
	 *
	 * @return array an array of trigger statuses
	 */
	protected function add_additional_names_to_array( $trigger_statuses, $additional_names ) {

		foreach ( $additional_names as $name ) {
			$trigger_statuses[] = $name;
		}

		return $trigger_statuses;

	}

	/**
	 * Get the Provider Slug from the Module Class.
	 *
	 * @since 4.11.0
	 * @since 4.12.3 Added support for passing slug (such as to confirm slug is valid) and class instance.
	 *
	 * @param string|Tribe__Tickets__Tickets $module The string of the module main class name, its slug, or instance.
	 *
	 * @return string|false Provider slug or false if not found.
	 */
	public function get_provider_slug( $module ) {
		if ( $module instanceof Tribe__Tickets__Tickets ) {
			$module = $module->class_name;
		}

		$slugs_to_classes = array_flip( $this->module_slugs );

		// If already a slug (case-sensitive).
		if ( array_key_exists( $module, $slugs_to_classes ) ) {
			return $module;
		}

		// Get slug from class name.
		$result = Tribe__Utils__Array::get( $this->module_slugs, $module );
		if ( ! empty( $result ) ) {
			return $result;
		}

		return false;
	}

	/**
	 * Get the Provider class name from its slug.
	 *
	 * @since 4.12.3
	 *
	 * @param string|Tribe__Tickets__Tickets $slug The string of the slug, its module main class name, or instance.
	 *
	 * @return string|false Provider class name or false if not found.
	 */
	public function get_provider_class_from_slug( $slug ) {
		// Change \\ to \ for class namespacing.
		$slug = str_replace( '\\\\', '\\', $slug );

		if ( $slug instanceof Tribe__Tickets__Tickets ) {
			$slug = $slug->class_name;
		}

		// If already a class name (case-sensitive).
		if ( array_key_exists( $slug, $this->module_slugs ) ) {
			return $slug;
		}

		$slugs_to_classes = array_flip( $this->module_slugs );

		// Get class name from slug.
		$result = Tribe__Utils__Array::get( $slugs_to_classes, $slug );
		if ( ! empty( $result ) ) {
			return $result;
		}

		return false;
	}
}
