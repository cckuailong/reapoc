<?php
/**
 * Create a schema for a Diagnostic Lab as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaDiagnosticLab' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-medicalorganization.php';

	/**
	 * Diagnostic Lab schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaDiagnosticLab extends bpfwpSchemaMedicalOrganization {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'DiagnosticLab';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Diagnostic Lab';


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
					'slug' 				=> 'availableTest', 
					'name' 				=> 'Available Test', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'availableTest', $this->slug )
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
