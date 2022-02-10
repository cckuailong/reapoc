<?php
/**
 * Creates a class based on a schema custom post type's data.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaCPT' ) ) :

	/**
	 * Schema CPT-based class for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaCPT {

		/**
		 * The the WP $post_id, if any, for this Schema CPT
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    int
		 */
		public $post_id = '';
		/**
		 * The targeted WP-object type
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $target_type = '';

		/**
		 * The value for the target
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $target_value = '';

		/**
		 * The schema type that should be added
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $schema_type = '';

		/**
		 * The schema class that defines the fields, etc. used in the selected schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    bpfwpSchema child class
		 */
		public $schema_class;

		/**
		 * The default values that should be applied to each schema field
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    array
		 */
		public $field_defaults = array();

		/**
		 * Whether this schema should be applied to all matching posts by default
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    boolean
		 */
		public $default_display = false;

		/**
		 * Initialize the class and recursively initialize child classes.
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $args ) {

			$this->set_properties( $args );

			if ( isset($this->schema_class) ){
				add_action( 'admin_init', array( $this, 'set_admin_hooks' ) );
				add_action( 'wp_footer', array( $this, 'set_display_hooks' ), 1 );
			}
		}

		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_properties( $args ) {

			if ( isset($args['post_id']) ) { $this->post_id = $args['post_id']; }
			if ( isset($args['target_type']) ) { $this->target_type = $args['target_type']; }
			if ( isset($args['target_value']) ) { $this->target_value = $args['target_value']; }
			if ( isset($args['schema_type']) ) { $this->schema_type = $args['schema_type']; }
			if ( isset($args['field_defaults']) ) { $this->field_defaults = $args['field_defaults']; }
			if ( isset($args['default_display']) ) { $this->default_display = $args['default_display']; }

			if ( isset($args['schema_type']) and $this->schema_type and is_file( BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . strtolower( $this->schema_type ) . '.php' ) ) {
				include_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . strtolower( $this->schema_type ) . '.php';

				$class_name = 'bpfwpSchema' . $this->schema_type;
				$this->schema_class = new $class_name( array( 'depth' => 0 ) );
			}
		}
 

		/**
		 * Set admin hooks to display and save the schema meta boxes based on the target type and value
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_admin_hooks() {
			add_action( 'edit_form_after_title', array( $this, 'add_meta_nonce' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta' ) );

			wp_enqueue_script( 'schema-cpt', BPFWP_PLUGIN_URL . '/assets/js/schema-cpt.js', array( 'jquery'), BPFWP_VERSION );
		}
		

		/**
		 * Set admin hooks to display and save the schema meta boxes based on the target type and value
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_display_hooks() {
			if ( $this->validate_target() ) {
				add_filter( 'bpfwp_ld_json_output', array( $this, 'output_ld_json_data' ) );
			}
		}

		/**
		 * Output a hidden nonce field to secure the saving of post meta
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function add_meta_nonce() {
			if ( $this->validate_target() ) {
				wp_nonce_field( 'bpfwp_schema_meta', 'bpfwp_schema_meta_nonce' );
			}
		}

		/**
		 * Check whether meta boxes, nonces, etc. should be displayed during the current WP load process
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function validate_target( $target = null) {
			global $post;
			
			if ( ! $target ) {
				$target = $post;
			}

			$valid_target = false;

			if ( $this->target_type == 'post' and isset($target) and $target->ID == $this->target_value ) {
				$valid_target = true;
			}

			if ( $this->target_type == 'post_type' and isset($target) and $target->post_type == $this->target_value && ! is_archive() ) {
				$valid_target = true;
			}

			if ( $this->target_type == 'page' and isset($target) and $target->ID == $this->target_value ) {
				$valid_target = true;
			}

			return $valid_target;
		}

		/**
		 * Registers a meta box for this schema CPT
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  WP_Post $post The current post object.
		 * @return void
		 */
		public function add_meta_box( $post ) { 
			if ( ! $this->validate_target() ) { return; }

			// Metabox to enter schema type.
			$meta_box = array(
				'id'        => 'bpfwp_schema_' . strtolower( $this->schema_type ) . '_metabox',
				'title'     => $this->schema_type . __( ' Details', 'business-profile' ),
				'callback'  => array( $this, 'print_schema_metabox' ),
				'context'   => 'normal',
				'priority'  => 'default',
			);

			if ( $this->target_type == 'post' ) { $meta_box['post_type'] = 'post'; }
			elseif ( $this->target_type == 'page' ) { $meta_box['post_type'] = 'page'; }
			elseif ( $this->target_type == 'post_type' ) { $meta_box['post_type'] = $this->target_value; }

			// Create filter so addons can modify the metaboxes.
			$meta_box = apply_filters( 'bpfwp_schema_cpt_meta_box', $meta_box );

			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				$meta_box['callback'],
				$meta_box['post_type'],
				$meta_box['context'],
				$meta_box['priority']
			);
		}


		/**
		 * Output the metabox HTML to customize a schema's meta values
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  WP_Post $post The current post object.
		 * @return void
		 */
		public function print_schema_metabox( $post ) { 
			$specified_values = get_post_meta( $post->ID, 'bpfwp_values_' . $this->schema_type, true );

			?>

			<div class="bpfwp-meta-input bpfwp-meta-post_type">
				<h3>
					<?php esc_html_e( 'Schema Field Values', 'business-profile' ); ?>
				</h3>

				<?php foreach ( $this->schema_class->fields as $field ) {
						$this->display_field( $field, $post, $specified_values); 
				} ?>

			</div>
		<?php }

		/**
		 * Display a field to be edited for this CPT schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  bpfwpSchemaField $field The field to be displayed.
		 * @param  WP_Post $post The current post object.
		 * @param  array $specified_values The values that were saved for this CPT schema previously.
		 * @param  int $count The number of times this field has been displayed.
		 * @return void
		 */
		public function display_field( $field, $post, $specified_values, $field_prefix = '', $count = 1 ) { 

			$child_depth = 1;

			if ( isset($specified_values[$field_prefix . '_' . $field->slug][$count]) ) { $value = $specified_values[$field_prefix . '_' . $field->slug][$count]; }

			$field->callback = ( isset($this->field_defaults[$field_prefix . '_' . $field->slug]) and $this->field_defaults[$field_prefix . '_' . $field->slug] != '' ) ? $this->field_defaults[$field_prefix . '_' . $field->slug] : $field->callback;
			
			$placeholder = $field->get_default_value( $post->ID, 'post' );

			switch ( $field->input ) {
				case 'SchemaField': 
					$field_prefix .= '_' . $field->slug;

					for ( $i = 1; $i <= $child_depth; $i++ ) {
						echo '<h4 class="' . ( $field->recommended ? 'recommended' : '' ) . '" data-field_name="' . esc_attr( $field->name ) . '">' . esc_html( $field->name ) . '</h4>';
					
						echo '<div class="bpfwp-schema-field-container" data-field_name="' . esc_attr( $field->name ) . '">';

						foreach ( $field->children as $field_child ) { $child_depth = max($child_depth, $this->display_field( $field_child, $post, $specified_values, $field_prefix, $i ) ); }

						echo '</div>';
					}

					if ( $field->repeatable ) { 
						echo '<div class="bpfwp-clear"></div>';
						echo '<input type="hidden" name="count_' . esc_attr( $this->schema_type ) . '[' . esc_attr( $field_prefix ) . ']" value="' . esc_attr( $child_depth ) . '" />';
						echo '<button class="bpfwp-add-repeatable-field" data-schema_type="' . esc_attr( $this->schema_type ) . '" data-field_name="' . esc_attr( $field->name ) . '" data-field_prefix="' . esc_attr( $field_prefix ) . '" data-field_slug="' . esc_attr( $field->slug ) . '">';
						echo __('Add Another ', 'business-profile') . esc_html( $field->name );
						echo '</button>';
					}

					// reset depth in case you're going up to a schema field a level above
					$child_depth = 1;

				break;

				case 'textarea':
					// update the child_depth parameter if in a non-schema field
					if ( isset($specified_values[$field_prefix . '_' . $field->slug]) and is_array($specified_values[$field_prefix . '_' . $field->slug]) ) { $child_depth = sizeOf( $specified_values[$field_prefix . '_' . $field->slug] ); }

					echo '<label class="' . ( $field->recommended ? 'recommended' : '' ) . '" for="' . esc_attr( $this->schema_type ) . '[' . esc_attr( $field_prefix ) . '_' . esc_attr( $field->slug ) . '][' . esc_attr( $count ) . ']">' . esc_html( $field->name ) . '</label>';

					echo '<textarea name="' . esc_attr( $this->schema_type . '[' . $field_prefix . '_' . $field->slug . '][' . $count ) . ']" placeholder="' . ( isset($placeholder) ? esc_attr( $placeholder ) : "" ) . '">';
						echo isset($value) ? esc_textarea( $value ) : '';
					echo '</textarea>';
				break;

				default:
					// update the child_depth parameter if in a non-schema field
					if ( isset($specified_values[$field_prefix . '_' . $field->slug]) and is_array($specified_values[$field_prefix . '_' . $field->slug]) ) { $child_depth = sizeOf( $specified_values[$field_prefix . '_' . $field->slug] ); }

					echo '<label class="' . ( $field->recommended ? 'recommended' : '' ) . '" for="' . esc_attr( $this->schema_type ) . '[' . esc_attr( $field_prefix ) . '_' . esc_attr( $field->slug ) . '][' . esc_attr( $count ) . ']">' . esc_html( $field->name ) . '</label>';

					echo '<input type="' . esc_attr( $field->input ) . '" name="' . esc_attr( $this->schema_type ) . '[' . esc_attr( $field_prefix ) . '_' . esc_attr( $field->slug ) . '][' . esc_attr( $count ) . ']" placeholder="' . ( isset($placeholder) ? esc_attr( $placeholder ) : '' ) . '" value="' . ( isset($value) ? esc_attr( $value ) : '' ) . '" />';
			}

			return $child_depth;
		}


		/**
		 * Sanitize and save the schema post meta
		 *
		 * The actual sanitization and validation should be
		 * performed in a bpfwpLocation object which will
		 * handle all the location data, and perform loading
		 * and saving.
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  int $post_id The current post ID.
		 * @return int $post_id The current post ID.
		 */
		public function save_meta( $post_id ) {
			global $bpfwp_controller;

			if ( ! isset( $_POST['bpfwp_schema_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bpfwp_schema_meta_nonce'] ), 'bpfwp_schema_meta' ) ) { // Input var okay.
				return $post_id;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			$post = get_post( $post_id );

			if ($post->post_type !== $bpfwp_controller->cpts->schema_cpt_slug) {
				return;
			}
 
			if ( ! $this->validate_target( get_post( $post_id ) ) ) { 
				return $post_id; 
			}
			
			global $values;
			$values = array();
			foreach ( $this->schema_class->fields as $field ) {
				$this->get_field_save_value('_', $field);
			}

			update_post_meta( $post_id, 'bpfwp_values_' . $this->schema_type, $values );

			return $post_id;
		}

		/**
		 * Get the value of a particular field that has been sent via POST
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  bpfwpSchemaField $field The field to be displayed.
		 * @param  int $count The number of times this field has been displayed.
		 * @return mixed $field_value;
		 */
		private function get_field_save_value( $field_prefix, $field, $count = 1 ) {
			global $values;

			if ( $field->input == 'SchemaField' ) {
				foreach ( $field->children as $field_child ) {
					if ( $field->repeatable ) { $max_count = intval( $_POST['count_' . $this->schema_type][$field_prefix . $field->slug] ); }
					else { $max_count = 1; }

					for ( $i = 1; $i <= $max_count; $i++ ) {
						$new_field_prefix = $field_prefix . $field->slug . '_';
						$value[$new_field_prefix . $field_child->slug] = $this->get_field_save_value($new_field_prefix, $field_child, $i);
					}
				}
			}
			else {
				$values[$field_prefix . $field->slug][$count] = sanitize_text_field( $_POST[$this->schema_type][$field_prefix . $field->slug][$count] );
			}

			return $value;
		}

		/**
		 * Creates an output array based on this schema's class values
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  array $ld_json The ld+json data that will be output eventually.
		 * @return array $ld_json;
		 */
		public function output_ld_json_data( $ld_json ) {
			global $post;
			// @to-do: THIS NEEDS TO BE CHANGED TO WORK WITH NON-POST OBJECTS
			$values = get_post_meta( $post->ID, 'bpfwp_values_' . $this->schema_type, true );
			$values = is_array( $values ) ? $values : array();

			$output = array(); 

			$output['@context'] = 'http://schema.org';
			$output['@type'] = $this->schema_type;

			foreach ( $this->schema_class->fields as $field ) {
				$output[$field->slug] = $this->get_field_output_value($values, $field, 1);
			}

			$ld_json[] = $output;

			return $ld_json;
		}

		/**
		 * Creates an output array based on this schema's class values
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  array $values The values that have been saved for this particular Schema CPT.
		 * @param  bpfwpSchemaField $field The field that we're getting the value for.
		 * @param  int $count Which iteration of this field are we retrieving.
		 * @return mixed $value;
		 */
		public function get_field_output_value( $values, $field, $count = 1, $field_prefix = '' ) {
			global $post;

			$field->callback = ( isset($this->field_defaults[$field_prefix . '_' . $field->slug]) and $this->field_defaults[$field_prefix . '_' . $field->slug] != '' ) ? $this->field_defaults[$field_prefix . '_' . $field->slug] : $field->callback;

			if ( $field->input == 'SchemaField' ) {
				//$max_count = sizeOf($values[$field->slug]);
				$max_count = 1;

				$field_prefix .= '_' . $field->slug;

				for ( $i = 1; $i <= $max_count; $i++ ) {

					foreach ( $field->children as $field_child ) {

						//$value[$field_child->slug][$i] = $this->get_field_output_value($values[$field->slug], $field_child, $i);
						
						$values[$field->slug] = isset( $values['_' . $field->slug] ) ? $values['_' . $field->slug] : array();
						
						$value[$field_child->slug] = $this->get_field_output_value( $values[$field->slug], $field_child, $i, $field_prefix );
					}
				}
			}
			else {
				$user_value = isset( $values['_' . $field->slug] ) ? $values['_' . $field->slug] : '';

				if ( is_array( $user_value) ) { $user_value = $user_value[$count]; }

				if ( ! $user_value ) { $default_value = $field->get_default_value( $post->ID, 'post' ); }

				$value = $user_value ? $user_value : ( $default_value ? $default_value : '' );
			}

			return $value;
		}
	}
endif;
