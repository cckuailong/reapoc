<?php
/**
 * Create a schema for a How-To as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaHowTo' ) ) :

	/**
	 * How-To schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaHowTo extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'HowTo';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'How-To';


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
					'slug' 				=> 'description', 
					'name' 				=> 'Description', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'video', 
					'name' 				=> 'Video', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'video', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'hasPart', 
					'name' 				=> 'Has Part', 
					'type'				=> 'ItemList',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'clip', 
							'name' 				=> 'Clip', 
							'type'				=> 'Clip',
							'repeatable'		=> true,
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'input' 			=> 'text',
									'recommended'		=> true,
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'hasPart', 'clip' )
								) ),
								new bpfwpSchemaField( array(
									'slug' 				=> 'startOffset', 
									'name' 				=> 'Start Offset', 
									'input' 			=> 'text',
									'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'startOffset', $this->slug, 'hasPart', 'clip' )
								) ),
								new bpfwpSchemaField( array(
									'slug' 				=> 'endOffset', 
									'name' 				=> 'End Offset', 
									'input' 			=> 'text',
									'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'endOffset', $this->slug, 'hasPart', 'clip' )
								) ),
								new bpfwpSchemaField( array(
									'slug' 				=> 'url', 
									'name' 				=> 'URL', 
									'input' 			=> 'url',
									'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'url', $this->slug, 'hasPart', 'clip' )
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