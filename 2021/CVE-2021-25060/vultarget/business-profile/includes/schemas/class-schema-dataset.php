<?php
/**
 * Create a schema for a Critic Review as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaCriticReview' ) ) :

	/**
	 * Critic Review schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaCriticReview extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'CriticReview';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Critic Review';


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
					'slug' 				=> 'url', 
					'name' 				=> 'URL', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_permalink', 'url', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'description', 
					'name' 				=> 'Description', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'license', 
					'name' 				=> 'License', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'license', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'keywords', 
					'name' 				=> 'Keywords', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'license', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'creator', 
					'name' 				=> 'Creator', 
					'type'				=> 'Organization',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_author', 'name', $this->slug, 'creator' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'url', 
							'name' 				=> 'URL', 
							'input' 			=> 'url',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'url', $this->slug, 'creator' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'includedInDataCatalog', 
					'name' 				=> 'Included in Data Catalog', 
					'type'				=> 'DataCatalog',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'includedInDataCatalog' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'hasPart', 
					'name' 				=> 'Has Part', 
					'type'				=> 'Dataset',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'subDataset', 
							'name' 				=> 'Sub Dataset', 
							'type'				=> 'Dataset',
							'repeatable'		=> true,
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'input' 			=> 'text',
									'recommended'		=> true,
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'hasPart', 'subDataset' )
								) ),
								new bpfwpSchemaField( array(
									'slug' 				=> 'description', 
									'name' 				=> 'Description', 
									'input' 			=> 'text',
									'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'description', $this->slug, 'hasPart', 'subDataset' )
								) ),
								new bpfwpSchemaField( array(
									'slug' 				=> 'license', 
									'name' 				=> 'License', 
									'input' 			=> 'text',
									'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'license', $this->slug, 'hasPart', 'subDataset' )
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