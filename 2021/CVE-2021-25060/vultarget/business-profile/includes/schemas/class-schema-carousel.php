<?php
/**
 * Create a schema for a Carousel as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaCarousel' ) ) :

	/**
	 * Carousel schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaCarousel extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Carousel';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Carousel';


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
					'type'				=> 'ItemList',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'listItem', 
							'name' 				=> 'List Item', 
							'type'				=> 'ListItem',
							'repeatable'		=> true,
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
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function display_name get_the_author_meta', 'name', $this->slug, 'ListItem author' )
										) )
									)
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'url', 
									'name' 				=> 'URL', 
									'input' 			=> 'url',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'url', $this->slug, 'ListItem' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'image', 
									'name' 				=> 'Image', 
									'input' 			=> 'text',
									'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function bpfwp_get_post_image_url', 'image', $this->slug, 'ListItem' )
								) ),
								new bpfwpSchemaField( array(
									'slug' 				=> 'description', 
									'name' 				=> 'Description', 
									'input' 			=> 'text',
									'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug, 'ListItem' )
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