<?php
/**
 * Create a schema for a Music Group as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaMusicGroup' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-performinggroup.php';

	/**
	 * Music Group schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaMusicGroup extends bpfwpSchemaPerformingGroup {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'MusicGroup';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Music Group';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			parent::set_fields();

			$additional_fields = array(
				new bpfwpSchemaField( array(
					'slug' 				=> 'genre', 
					'name' 				=> 'Genre', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'genre', $this->slug )
				) ),
			);

			$fields = apply_filters( 'bpfwp_schema_additional_fields', $additional_fields, $this->slug );

			array_splice($this->fields, 1, 0, $fields);
		}


		/**
		 * Load the schema's child classes
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function initialize_children( $depth ) {
			$depth--;

			$child_classes = array();

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
