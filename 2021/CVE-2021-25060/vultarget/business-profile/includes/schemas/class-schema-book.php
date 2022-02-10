<?php
/**
 * Create a schema for a Book as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaBook' ) ) :

	/**
	 * Book schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaBook extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Book';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Book';


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
					'slug' 				=> 'author', 
					'name' 				=> 'Author', 
					'type'				=> 'Person',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'author' )
						) )
					)
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'name', 
					'name' 				=> 'Name', 
					'input' 			=> 'text',
					'recommended'		=> true,
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_title', 'name', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'workExample', 
					'name' 				=> 'Work Example',
					'type'				=> 'Book',
					'input' 			=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'isbn', 
							'name' 				=> 'ISBN', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'isbn', $this->slug, 'author' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'bookEdition', 
							'name' 				=> 'Book Edition', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'bookEdition', $this->slug, 'author' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'bookFormat', 
							'name' 				=> 'Book Format', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'bookFormat', $this->slug, 'author' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'author', 
							'name' 				=> 'Author', 
							'type'				=> 'Person',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'author' )
								) )
							)
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

			$child_classes = array ();

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;