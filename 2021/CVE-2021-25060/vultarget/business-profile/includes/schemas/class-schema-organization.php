<?php
/**
 * Create a schema for an Organization as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaOrganization' ) ) :

	/**
	 * Organization schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaOrganization extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Organization';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Organization';


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
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'option blogname', 'name', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'image', 
					'name' 				=> 'Image', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function bpfwp_get_post_image_url', 'image', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'email', 
					'name' 				=> 'Email', 
					'input' 			=> 'email',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'option admin_email', 'email', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'telephone', 
					'name' 				=> 'Telephone', 
					'input' 			=> 'tel',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'telephone', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'url', 
					'name' 				=> 'URL', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_permalink', 'url', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'address', 
					'name' 				=> 'Address', 
					'type'				=> 'PostalAddress',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'addressLocality', 
							'name' 				=> 'Location (City, Province)', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'telephone', $this->slug, 'address' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'postalCode', 
							'name' 				=> 'Postal/Zip Code', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'contactType', $this->slug, 'address' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'streetAddress', 
							'name' 				=> 'Street Address', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'contactOption', $this->slug, 'address' )
						) )
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'contactPoint', 
					'name' 				=> 'Contact', 
					'type'				=> 'ContactPoint',
					'input'				=> 'SchemaField', 
					'repeatable'		=> true,
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'telephone', 
							'name' 				=> 'Telephone', 
							'input' 			=> 'tel',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'telephone', $this->slug, 'contactPoint' )
						) ),
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
						new bpfwpSchemaField( array( 
							'slug' 				=> 'areaServed', 
							'name' 				=> 'Area Served', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'areaServed', $this->slug, 'contactPoint' )
						) )
					)
				) )
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

			$child_classes = array (
				'airline' => 'Airline',
				'consortium' => 'Consortium',
				'corporation' => 'Corporation',
				'educationalorganization' => 'EducationalOrganization',
				'fundingscheme' => 'FundingScheme',
				'governmentorganization' => 'GovernmentOrganization',
				'libarysystem' => 'LibrarySystem',
				'localbusiness' => 'LocalBusiness',
				'medicalorganization' => 'MedicalOrganization',
				'ngo' => 'NGO',
				'newsmediaorganization' => 'NewsMediaOrganization',
				'performinggroup' => 'PerformingGroup',
				'project' => 'Project',
				'sportsorganization' => 'SportsOrganization',
				'workersunion' => 'WorkersUnion',
			);

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
