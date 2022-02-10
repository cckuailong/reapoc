<?php
/**
 * Create a schema for an Airline as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaAirline' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-organization.php';

	/**
	 * Airline schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaAirline extends bpfwpSchemaOrganization {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Airline';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Airline';


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
					'slug' 				=> 'boardingPolicy', 
					'name' 				=> 'Boarding Policy', 
					'type'				=> 'BoardingPolicyType',
					'input'				=> 'SchemaField', 
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'contactType', 
							'name' 				=> 'Contact Type', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'contactType', $this->slug, 'contactPoint' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'contactOption', 
							'name' 				=> 'Contact Option', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'contactOption', $this->slug, 'contactPoint' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'iataCode', 
					'name' 				=> 'IATA Code', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'iataCode', $this->slug )
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
		public function initialize_children(  $depth ) {
			$depth--;

			$child_classes = array();

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
