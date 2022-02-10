<?php
/**
 * Create a schema for a Food Establishment as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaFoodEstablishment' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-localbusiness.php';

	/**
	 * Food Establishment schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaFoodEstablishment extends bpfwpSchemaLocalBusiness {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'FoodEstablishment';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Food Establishment';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			parent::set_fields();

			$additional_fields = array(
				new bpfwpSchemaField( array(
					'slug' 				=> 'acceptsReservations', 
					'name' 				=> 'Reservations URL (or false in none)', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'acceptsReservations', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'hasMenu', 
					'name' 				=> 'Menu URL (or false in none)', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'hasMenu', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'servesCuisine', 
					'name' 				=> 'Cuisine', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'servesCuisine', $this->slug )
				) )
			);

			$fields = apply_filters( 'bpfwp_schema_additional_fields', $additional_fields, $this->slug );

			array_splice($this->fields, 1, 0, $fields);
		}


		/**
		 * Load the schema's child classes
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function initialize_children( $depth ) {
			$depth--;

			$child_classes = array(
				'bakery' => 'Bakery',
				'barorpub' => 'BarOrPub',
				'brewery' => 'Brewery',
				'cafeorcoffeeshop' => 'CafeOrCoffeeShop',
				'distillery' => 'Distillery',
				'fastfoodrestaurant' => 'FastFoodRestaurant',
				'icecreamshop' => 'IceCreamShop',
				'restaurant' => 'Restaurant',
				'winery' => 'Winery',
			);

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
