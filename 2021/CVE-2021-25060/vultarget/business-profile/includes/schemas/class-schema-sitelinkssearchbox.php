<?php
/**
 * Create a schema for a Sitelinks Searchbox as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaSitelinksSearchbox' ) ) :

	/**
	 * Sitelinks Searchbox schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaSitelinksSearchbox extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'SitelinksSearchbox';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Sitelinks Searchbox';


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
					'slug' 				=> 'url', 
					'name' 				=> 'URL', 
					'input' 			=> 'url',
					'recommended'		=> true,
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'url', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'potentialAction', 
					'name' 				=> 'Potential Action', 
					'type'				=> 'SearchAction',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'target', 
							'name' 				=> 'Target', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'target', $this->slug, 'potentialAction' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'query-input', 
							'name' 				=> 'Query Input', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'query-input', $this->slug, 'potentialAction' )
						) )
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