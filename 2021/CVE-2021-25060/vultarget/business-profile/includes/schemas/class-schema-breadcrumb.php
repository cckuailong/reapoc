<?php
/**
 * Create a schema for a Breadcrumb as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaBreadcrumb' ) ) :

	/**
	 * Breadcrumb schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaBreadcrumb extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Breadcrumb';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Breadcrumb';


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
					'slug' 				=> 'itemListElement', 
					'name' 				=> 'Item List Element', 
					'type'				=> 'BreadcrumbList',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'listItem', 
							'name' 				=> 'List Item', 
							'type'				=> 'ListItem',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'position', 
									'name' 				=> 'Position', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'position', $this->slug, 'ListItem' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'ListItem' )
								) ),
							)
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'listItem', 
							'name' 				=> 'List Item', 
							'type'				=> 'ListItem',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'position', 
									'name' 				=> 'Position', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'position', $this->slug, 'ListItem' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'ListItem' )
								) ),
							)
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'listItem', 
							'name' 				=> 'List Item', 
							'type'				=> 'ListItem',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'position', 
									'name' 				=> 'Position', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'position', $this->slug, 'ListItem' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'ListItem' )
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