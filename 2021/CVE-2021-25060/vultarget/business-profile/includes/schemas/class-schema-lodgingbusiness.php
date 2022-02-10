<?php
/**
 * Create a schema for a Lodging Business as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaLodgingBusiness' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-localbusiness.php';

	/**
	 * Lodging Business schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaLodgingBusiness extends bpfwpSchemaLocalBusiness {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'LodgingBusiness';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Lodging Business';


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
					'slug' 				=> 'checkinTime', 
					'name' 				=> 'Check-in Time', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'checkinTime', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'checkoutTime', 
					'name' 				=> 'Check-out Time', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'checkoutTime', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'petsAllowed', 
					'name' 				=> 'Are Pets Allowed', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'No', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'starRating', 
					'name' 				=> 'Rating', 
					'type'				=> 'Rating',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'ratingValue', 
							'name' 				=> 'User Rating', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingValue', $this->slug, 'review', 'reviewRating' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'bestRating', 
							'name' 				=> 'Maximum Rating', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'bestRating', $this->slug, 'review', 'reviewRating' )
						) )
					)
				) ),
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
				'bedandbreakfast' => 'BedAndBreakfast',
				'campground' => 'Campground',
				'hostel' => 'Hostel',
				'hotel' => 'Hotel',
				'motel' => 'Motel',
				'resort' => 'Resort',
			);

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
