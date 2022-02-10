<?php
/**
 * Create a schema for an Local Business Listing as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaLocalBusinessListing' ) ) :

	/**
	 * Local Business Listing schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaLocalBusinessListing extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'LocalBusinessListing';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Local Business Listing';


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
					'slug' 				=> 'image', 
					'name' 				=> 'Image', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function bpfwp_get_post_image_url', 'image', $this->slug )
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
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'streetAddress', $this->slug, 'address' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'addressLocality', 
							'name' 				=> 'Location (City, Province)', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'addressLocality', $this->slug, 'address' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'postalCode', 
							'name' 				=> 'Postal/Zip Code', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'postalCode', $this->slug, 'address' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'addressRegion', 
							'name' 				=> 'Address Region', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'addressRegion', $this->slug, 'address' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'addressCountry', 
							'name' 				=> 'Address Country', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'addressCountry', $this->slug, 'address' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'geo', 
					'name' 				=> 'Geo Coordinates', 
					'type'				=> 'GeoCoordinates',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'latitude', 
							'name' 				=> 'Latitude', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'latitude', $this->slug, 'geo' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'longitude', 
							'name' 				=> 'Longitude', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'longitude', $this->slug, 'geo' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'url', 
					'name' 				=> 'URL', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_permalink', 'url', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'priceRange', 
					'name' 				=> 'Price Range', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'priceRange', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'telephone', 
					'name' 				=> 'Telephone', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'telephone', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'openingHoursSpecification', 
					'name' 				=> 'Opening Hours Specification', 
					'type'				=> 'OpeningHoursSpecification',
					'repeatable'		=> true,
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'dayOfWeek', 
							'name' 				=> 'Day of Week', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'dayOfWeek', $this->slug, 'openingHoursSpecification' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'opens', 
							'name' 				=> 'Open Time', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'opens', $this->slug, 'openingHoursSpecification' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'closes', 
							'name' 				=> 'Close Time', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'closes', $this->slug, 'openingHoursSpecification' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'department', 
					'name' 				=> 'Department', 
					'type'				=> 'LocalBusiness',
					'repeatable'		=> true,
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'department' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'image', 
							'name' 				=> 'Image', 
							'input' 			=> 'url',
							'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'image', $this->slug, 'department' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'telephone', 
							'name' 				=> 'Telephone', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'telephone', $this->slug, 'department' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'openingHoursSpecification', 
							'name' 				=> 'Opening Hours Specification', 
							'type'				=> 'OpeningHoursSpecification',
							'repeatable'		=> true,
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'dayOfWeek', 
									'name' 				=> 'Day of Week', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'dayOfWeek', $this->slug, 'openingHoursSpecification' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'opens', 
									'name' 				=> 'Open Time', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'opens', $this->slug, 'openingHoursSpecification' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'closes', 
									'name' 				=> 'Close Time', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'closes', $this->slug, 'openingHoursSpecification' )
								) ),
							)
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