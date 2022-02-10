<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cffrtbField' ) ) {
/**
 * Field class to add, edit, save, update, validate and process a
 * custom field added to the booking form.
 *
 * @since 0.1
 */
class cffrtbField {

	/**
	 * ID
	 *
	 * @since 0.1
	 */
	public $ID;

	/**
	 * Type
	 *
	 * eg - text, options, confirm
	 *
	 * @since 0.1
	 */
	public $type;

	/**
	 * Subtype
	 *
	 * eg - text/textarea, select/checkbox/radio, confirm
	 *
	 * @since 0.1
	 */
	public $subtype;

	/**
	 * Label
	 *
	 * @since 0.1
	 */
	public $title;

	/**
	 * Slug
	 *
	 * @since 0.1
	 */
	public $slug;

	/**
	 * Required
	 *
	 * @since 0.1
	 */
	public $required;

	/**
	 * Fieldset
	 *
	 * @since 0.1
	 */
	public $fieldset;

	/**
	 * Order of the field in its fieldset
	 *
	 * @since 0.1
	 */
	public $order;

	/**
	 * Options (used in some field types)
	 *
	 * @since 0.1
	 */
	public $options;

	/**
	 * Post status
	 *
	 * @since 0.1
	 */
	public $status;

	/**
	 * Get it started
	 *
	 * @since 0.1
	 */
	public function __construct( $args = array() ) {

		// Load from the db if an ID is passed
		if ( !empty( $args['ID'] ) ) {
			$args = array_merge( $this->load_from_db( $args['ID'] ), $args );
		}

		// Set up the field object
		$this->setup_field( $args );

	}

	/**
	 * Load an object based on the arguments passed
	 *
	 * @since 0.1
	 */
	public function load_from_db( $id ) {

		$post = get_post( $id, 'ARRAY_A' );

		$post['title'] = $post['post_title'];
		$post['slug'] = $post['post_name'];
		$post['order'] = $post['menu_order'];
		$post['status'] = $post['post_status'];

		$post_meta = get_post_meta( $id, 'cffrtb', true );

		if( !empty( $post_meta['type'] ) ) { $post['type'] = $post_meta['type']; }
		if( !empty( $post_meta['subtype'] ) ) { $post['subtype'] = $post_meta['subtype']; }
		if( !empty( $post_meta['required'] ) ) { $post['required'] = $post_meta['required']; }
		if( !empty( $post_meta['fieldset'] ) ) { $post['fieldset'] = $post_meta['fieldset']; }
		if( !empty( $post_meta['options'] ) ) { $post['options'] = $post_meta['options']; }

		return $post;
	}

	/**
	 * Set up a new field object
	 *
	 * @since 0.1
	 */
	public function setup_field( $args ) {
		global $rtb_controller;

		// Use defaults for missing args
		$args = array_merge( $rtb_controller->fields->get_default_field_values(), $args );

		// Set up the object
		$this->ID = empty( $args['ID'] ) ? null : (int) $args['ID'];
		$this->type = empty( $args['type'] ) ? $this->get_valid_type( '' ) : $this->get_valid_type( $args['type'] );
		$this->subtype = empty( $args['subtype'] ) ? $this->get_valid_subtype( '' ) : $this->get_valid_subtype( $args['subtype'] );
		$this->title = empty( $args['title'] ) ? null : sanitize_text_field( $args['title'] );
		$this->slug = empty( $args['slug'] ) ? null :  sanitize_key( $args['slug'] );
		$this->required = empty( $args['required'] ) ? false : (bool) $args['required'];
		$this->fieldset = empty( $args['fieldset'] ) ? null : sanitize_key( $args['fieldset'] );
		$this->order = empty( $args['order'] ) ? null : (int) $args['order'];
		$this->options = empty( $args['options'] ) ? null : $this->get_valid_options( $args['options'] );
		$this->status = empty( $args['status'] ) ? 'publish' : sanitize_key( $args['status'] );

		do_action( 'cffrtb_setup_field_object', $this, $args );
	}

	/**
	 * Check if type is valid
	 *
	 * @since 0.1
	 */
	public function is_valid_type( $type ) {
		global $rtb_controller;

		$types = $rtb_controller->fields->get_valid_field_types();
		return !empty( $type ) && array_key_exists( $type, $types );
	}

	/**
	 * Return a valid type. Falls back to default if specified type is
	 * not valid. Also allows a `fieldset` type to pass
	 *
	 * @since 0.1
	 */
	public function get_valid_type( $type ) {
		global $rtb_controller;

		if ( $type == 'fieldset' ) {
			return $type;
		}

		if ( $this->is_valid_type( $type ) ) {
			return $type;
		}

		$defaults = $rtb_controller->fields->get_default_field_values();
		return $defaults['type'];
	}

	/**
	 * Return a valid subtype. Falls back to first subtype of type if
	 * subtype is not valid.
	 *
	 * @since 0.1
	 */
	public function get_valid_subtype( $subtype ) {
		global $rtb_controller;

		if( $subtype == 'fieldset' ) {
			return $subtype;
		}

		$types = $rtb_controller->fields->get_valid_field_types();
		if ( array_key_exists( $subtype, $types[ $this->type ]['subtypes'] ) ) {
			return $subtype;
		}

		reset( $types[ $this->type ][ 'subtypes' ] );

		return key( $types[ $this->type ][ 'subtypes' ] );
	}

	/**
	 * Sanitize options
	 *
	 * @since 0.1
	 */
	public function get_valid_options( $options ) {
		global $rtb_controller;

		$new_options = array();
		foreach( $options as $key => $option ) {

			// Store key for fields that were generated before the change in
			// version 1.1, when the key represented the field id
			if ( !isset( $option['id'] ) ) {
				$option['id'] = $key;
			}

			$new_options[] = array(
				'id' => substr( $option['id'], 0, 3 ) == 'new' ? sanitize_text_field( $option['id'] ) : absint( $option['id'] ),
				'value' => isset( $option['value'] ) ? sanitize_text_field( $option['value'] ) : '',
				'disabled' => empty( $option['disabled'] ) ? false : true,
				// An order param may not exist if the option was created in an
				// earlier version of the plugin
				'order' => isset( $option['order'] ) ? absint( $option['order'] ) : 0,
			);
		}

		usort( $new_options, array( $rtb_controller->fields, 'sort_by_order' ) );

		return $new_options;
	}

	/**
	 * Prepare an options array for input as `post_meta`
	 *
	 * Each option must have a unique, permanent ID. If no ID exists, create a
	 * unique ID
	 *
	 * @since 0.1
	 */
	public function prepare_options_input() {

		// Load existing options
		$options = array();
		if ( !empty( $this->ID ) ) {
			$meta = get_post_meta( $this->ID, 'cffrtb', true );
			if ( isset( $meta['options'] ) ) {
				$options = $meta['options'];
			}
		}

		// Disable options that are no longer available
		foreach( $options as $i => $option ) {

			// Store key for fields that were generated before the change in
			// version 1.1, when the key represented the field id
			if ( !isset( $option['id'] ) ) {
				$option['id'] = $i;
			}

			$exists = false;
			foreach( $this->options as $new_option ) {
				if ( $new_option['id'] == $option['id'] ) {
					$exists = true;
					break;
				}
			}

			if ( !$exists ) {
				$options[$i]['disabled'] = true;
			}
		}

		// Generate new options array with properly assigned IDs for new options
		$i = count( $options );
		foreach( $this->options as $key => $option ) {
			if ( substr( $option['id'], 0, 3 ) == 'new' ) {
				$option['id'] = $i;
				$i++;
			}
			$options[$key] = $option;
		}

		return $options;
	}

	/**
	 * Check if this field has an id
	 *
	 * @since 0.1
	 */
	public function has_id() {
		return !empty( $this->ID );
	}

	/**
	 * Check if an option is valid
	 *
	 * @since 0.1
	 */
	public function is_valid_option( $value ) {

		if ( !is_array( $this->options ) ) {
			return false;
		}

		return array_key_exists( $value, $this->options );
	}

	/**
	 * Save the label of this field
	 *
	 * @since 0.1
	 */
	public function save_label() {
		global $rtb_controller;

		// A default field shouldn't be saved as a custom field post
		// type. Instead we need to overwrite the label for the slug.
		if ( !$this->has_id() ) {

			if ( empty( $this->slug ) ) {
				return array(
					false,
					array(
						'error' => 'missing_slug',
						'msg'	=> __( 'No field slug was sent with this request to update a default field.', 'custom-fields-for-rtb' ) . ' ' . $rtb_controller->custom_fields->common_error_msg,
						'data'	=> array( 'field' => $field, 'post_vars' => $_POST )
					)
				);
			}

			$modified = get_option( $rtb_controller->custom_fields->modified_option_key );

			$new = $modified;

			if ( empty( $new ) ) {
				$new = array();
			}

			if ( empty( $new[ $this->slug ] ) ) {
				$new[ $this->slug ] = array();
			}

			$new[ $this->slug ][ 'title'] = $this->title;
			$new[ $this->slug ][ 'fieldset'] = $this->fieldset;

			if ( update_option( $rtb_controller->custom_fields->modified_option_key, $new ) ) {
				return array(
					true,
					array(
						'field'	=> $this
					)
				);

			} else {

				/**
				 * update_option() returns false if the new option is
				 * the same as the old option, so let's check if that's
				 * why we got a false response and send out a true
				 * response if there's no error with the save process.
				 */
				$same = true;

				if ( empty( $modified ) || empty( $modified[ $this->slug ] ) ) {
					$same = false;
				}

				if ( ( isset( $modified[ $this->slug ]['title'] ) && $modified[ $this->slug ]['title'] !== $this->title ) ||
					( !isset( $modified[ $this->slug ]['title'] ) && !empty( $this->title ) ) ) {
					$same = false;
				}

				if ( empty( $modified[ $this->slug ]['fieldset'] ) || $modified[ $this->slug ]['fieldset'] !== $this->fieldset ) {
					$same = false;
				}

				if ( $same ) {
					return array(
						true,
						array(
							'field'	=> $this
						)
					);

				} else {
					return array(
						false,
						array(
							'error' 	=> 'save_failed',
							'msg' 		=> __( 'An error occurred while updating the label for this default field. Please try again.', 'custom-fields-for-rtb' ),
							'field'		=> $this,
							'option'	=> $new,
						)
					);
				}
			}

		// Custom field
		} else {

			$field = array(
				'ID'			=> $this->ID,
				'post_title'	=> $this->title,
			);

			if ( wp_update_post( $field ) ) {
				return array(
					true,
					array(
						'title'	=> get_the_title( $this->ID ),
					)
				);

			} else {
				return array(
					false,
					array(
						'error' 	=> 'save_failed',
						'msg' 		=> __( 'An error occurred while updating the label for this custom field. Please try again.', 'custom-fields-for-rtb' ),
						'field'		=> $this,
					)
				);
			}
		}
	}

	/**
	 * Save a field
	 *
	 * @since 0.1
	 */
	public function save_field() {
		global $rtb_controller;

		if ( empty( $this->title ) || empty( $this->type ) || ( $this->type !== 'fieldset' && empty( $this->subtype ) ) ) {
			return array(
				false,
				array(
					'error' 	=> 'missing_data',
					'msg' 		=> __( 'This field could not be saved because required data was missing.', 'custom-fields-for-rtb' ),
					'field'		=> $this,
				)
			);
		}

		$post = array(
			'post_title'		=> $this->title,
			'post_type'			=> 'cffrtb_field',
			'post_status'		=> $this->status,
		);
		if ( $this->has_id() ) { $post['ID'] = $this->ID; }
		if ( !empty( $this->slug ) ) { $post['post_name'] = $this->slug; }
		if ( !empty( $this->order ) ) { $post['menu_order'] = $this->order; }

		$post = apply_filters( 'cffrtb_insert_field_data', $post, $this );

		$id = wp_insert_post( $post );
		if ( is_wp_error( $id ) || empty( $id ) ) {
			return array(
				false,
				array(
					'error' 	=> 'save_post_failed',
					'msg' 		=> __( 'An error occurred while saving this field. Please try again. If the problem persists, please refresh the page.', 'custom-fields-for-rtb' ),
					'field'		=> $this,
					'wp_error'	=> $id
				)
			);
		}

		$is_new_field = !$this->has_id();

		$this->ID = $id;

		$post_item = get_post( $id );
		$this->title = get_the_title( $id );
		$this->slug = $post_item->post_name;

		$post_meta = array(
			'type'		=> $this->type,
		);
		if ( !empty( $this->subtype ) ) { $post_meta['subtype'] = $this->subtype; }
		if ( !empty( $this->required ) ) { $post_meta['required'] = $this->required; }
		if ( !empty( $this->fieldset ) ) { $post_meta['fieldset'] = $this->fieldset; }
		if ( !empty( $this->options ) && $this->type == 'options' ) { $post_meta['options'] = $this->prepare_options_input(); }

		$post_meta = apply_filters( 'cffrtb_insert_field_metadata', $post_meta, $this );

		update_post_meta( $id, 'cffrtb', $post_meta );

		$field = array(
			'ID'	=> $this->ID,
			'title'	=> $this->title,
		);

		if ( $this->type == 'fieldset' ) {
			$type = 'fieldset';
			$field['legend'] = $this->title;
		} else {
			$type = 'field';
		}

		return array(
			true,
			array(
				'ID'			=> $this->ID,
				'is_new_field'	=> $is_new_field,
				'field'			=> $rtb_controller->editor->print_field( $this->slug, $field, $type ),
				'type'			=> $this->type,
			)
		);
	}

	/**
	 * Validate the input for a field
	 *
	 * @since 0.1
	 */
	public function validate_input( $booking ) {

		// Don't validate fieldsets
		if ( $this->type === 'fieldset' ) {
			return;
		}

		// Instantiate the custom fields array if it's missing
		if ( !isset( $booking->custom_fields ) ) {
			$booking->custom_fields = array();
		}

		$input = isset( $_POST['rtb-' . $this->slug ] ) ? $_POST['rtb-' . $this->slug] : '';

		// Skip empty fields but do not skip checkboxes.
		// required checks are performed by base plugin validation

		$checkbox = 'options' === $this->type && 'checkbox' === $this->subtype;

		if ( ( is_string( $input ) && trim( $input ) == '' ) ||
			( is_array( $input ) && empty( $input ) ) ) {
			
			// When a checkbox is unselected, it will not override the previously selected
			// value because HTML Form will not submit empty checkboxes
			if($checkbox) {
				$input = [];
			}
			else {
				return;
			}
		}

		// Option fields
		if ( $this->type == 'options' ) {

			if ( !is_array( $input ) ) {
				$input = array( $input );
			}

			foreach( $input as $input_i ) {
				if ( !$this->is_valid_option( $input_i ) ) {
					$booking->validation_errors[] = array(
						'field'			=> $this->slug,
						'post_variable'	=> $input,
						'message'		=> __( 'The option you selected is not valid. Please make another choice.', 'custom-fields-for-rtb' )
					);

					return;
				}
			}

			if ( $this->subtype === 'select' || $this->subtype === 'radio' ) {
				$val = absint( $input[0] );
				if ( isset( $this->options[ $val ] ) ) {
					$booking->custom_fields[ $this->slug ] = $val;
				}
			} elseif ( $this->subtype === 'checkbox' ) {
				$val = array_map( 'absint', $input );
				$new_val = array();
				foreach( $val as $i ) {
					if ( isset( $this->options[ $i ] ) ) {
						$new_val[] = $i;
					}
				}
				$booking->custom_fields[ $this->slug ] = $new_val;
			}

		// Confirm fields (always true if we've reached this stage)
		} elseif ( $this->type === 'confirm' ) {
			$booking->custom_fields[ $this->slug ] = true;

		// Text fields just need to be sanitized
		} elseif ( $this->type === 'text' ) {
			$booking->custom_fields[ $this->slug ] = sanitize_text_field( $input );
		}
	}

}
} // endif;
