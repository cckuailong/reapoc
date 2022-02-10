<?php
/**
 * Create a schema for a Software App as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaSoftwareApp' ) ) :

	/**
	 * Software App schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaSoftwareApp extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'SoftwareApp';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Software App';


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
					'slug' 				=> 'operatingSystem', 
					'name' 				=> 'Operating System', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'operatingSystem', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'applicationCategory', 
					'name' 				=> 'Application Category', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'applicationCategory', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'aggregateRating', 
					'name' 				=> 'Aggregate Rating', 
					'type'				=> 'AggregateRating',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'ratingValue', 
							'name' 				=> 'Rating', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingValue', $this->slug, 'aggregateRating' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'ratingCount', 
							'name' 				=> 'Rating Count', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingCount', $this->slug, 'aggregateRating' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'offers', 
					'name' 				=> 'Offers', 
					'type'				=> 'Offer',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'price', 
							'name' 				=> 'price', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'price', $this->slug, 'offers' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'priceCurrency', 
							'name' 				=> 'Price Currency', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'priceCurrency', $this->slug, 'offers' )
						) ),
					)
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