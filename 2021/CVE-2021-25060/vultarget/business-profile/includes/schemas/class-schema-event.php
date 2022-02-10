<?php
/**
 * Create a schema for an Event as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaEvent' ) ) :

	/**
	 * Event schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaEvent extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Event';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Event';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-field.php';

			$fields = array(
				new bpfwpSchemaField( array( 
					'slug' 				=> 'name', 
					'name' 				=> 'Name', 
					'input' 			=> 'text',
					'recommended'		=> true,
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_title', 'name', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'location', 
					'name' 				=> 'Location', 
					'type'				=> 'Place',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'location' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'address', 
							'name' 				=> 'Address', 
							'type'				=> 'PostalAddress',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'streetAddress', 
									'name' 				=> 'Street Address', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'streetAddress', $this->slug, 'location', 'address' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'addressLocality', 
									'name' 				=> 'Location (City, Province)', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'addressLocality', $this->slug, 'location', 'address' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'postalCode', 
									'name' 				=> 'Postal/Zip Code', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'postalCode', $this->slug, 'location', 'address' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'addressRegion', 
									'name' 				=> 'Address Region', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'addressRegion', $this->slug, 'location', 'address' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'addressCountry', 
									'name' 				=> 'Address Country', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'addressCountry', $this->slug, 'location', 'address' )
								) ),
							)
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'performer', 
					'name' 				=> 'Performer', 
					'type'				=> 'PerformingGroup',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'performer' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'description', 
					'name' 				=> 'Description', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'image', 
					'name' 				=> 'Image', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'image', $this->slug )
				) ),
			);

			$this->fields = apply_filters( 'bpfwp_schema_fields', $fields, $this->slug );
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

			$child_classes = array ();

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;