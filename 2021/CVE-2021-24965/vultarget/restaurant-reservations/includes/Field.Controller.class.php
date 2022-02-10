<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbFieldController' ) ) {
/**
 * Controller class to generate, store and retrieve custom fields.
 *
 * @since 0.1
 */
class rtbFieldController {

	/**
	 * System fields
	 *
	 * @since 0.1
	 */
	public $system_fields;

	/**
	 * Default fields
	 *
	 * @var array
	 * @since 0.1
	 */
	public $default_fields;

	/**
	 * Valid field types
	 *
	 * @since 0.1
	 */
	public $valid_field_types;


	/**
	* Markup for a checkbox icon used to display results
	*
	* @param string checkbox_icon
	* @since 0.1
	*/
	public $checkbox_icon;

	/**
	 * Go!
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Store default fields
		add_filter( 'rtb_booking_form_fields', array( $this, 'get_default_fields' ), 1, 2 );

		// Modify fields based on modified options array
		add_filter( 'rtb_booking_form_fields', array( $this, 'modify_form_fields' ), 20, 2 );

		// Modify bookings table columns
		add_filter( 'rtb_bookings_table_columns', array( $this, 'modify_bookings_columns' ) );
		add_filter( 'rtb_bookings_all_table_columns', array( $this, 'modify_all_bookings_columns' ) );
		add_filter( 'rtb_bookings_table_column', array( $this, 'add_custom_column_value' ), 10, 3 );
	}

	/**
	 * Store the default form fields before anyone has hooked in to
	 * change them
	 *
	 * @since 0.1
	 */
	public function get_default_fields( $fields, $request ) {

		$this->default_fields = $fields;

		return $fields;
	}

	/**
	 * Retrieve a field from the fields array
	 *
	 * @since 0.1
	 */
	public function get_nested_field( $slug, $fields ) {

		foreach( $fields as $fieldset_slug => $fieldset ) {
			if ( array_key_exists( $slug, $fieldset['fields'] ) ) {
				return $fieldset['fields'][ $slug ];
			}
		}
	}

	/**
	 * Get a field by it's slug when passed an array of field objects
	 *
	 * @since 0.1
	 */
	public function get_field_in_array( $slug, $fields ) {

		foreach( $fields as $field ) {
			if ( $field->slug === $slug ) {
				return $field;
			}
		}
	}

	/**
	 * Get system fields and fieldsets
	 *
	 * @note these fields should have limited options for editing
	 * because they play a critical role in the plugin or are
	 * managed elsewhere.
	 * @since 0.1
	 */
	public function get_system_fields() {

		$this->system_fields = apply_filters(
			'cffrtb_system_fields',
			array(
				'location',
				'date',
				'time',
				'party',
				'name',
				'email',
			)
		);

		$this->system_fieldsets = apply_filters(
			'cffrtb_system_fieldsets',
			array(
				'reservation',
			)
		);
	}

	/**
	 * Default values for a field object
	 *
	 * @since 0.1
	 */
	public function get_default_field_values() {

		if ( !empty( $this->default_values ) ) {
			return $this->default_values;
		}

		$defaults = array(
			'type'		=> 'text',
			'subtype'	=> 'text',
			'label'		=> __( 'Field', 'custom-fields-for-rtb' ),
			'slug'		=> null,
			'required'	=> false,
		);

		$this->default_values = apply_filters( 'cffrtb_default_field_values', $defaults );

		return $this->default_values;
	}

	/**
	 * Get valid field types
	 *
	 * @since 0.1
	 */
	public function get_valid_field_types() {

		if( !empty( $this->valid_field_types ) ) {
			return $this->valid_field_types;
		}

		$field_types = array(
			'text'		=> array(
				'title'		=> __( 'Text', 'custom-fields-for-rtb' ),
				'subtypes'	=> array(
					'text'		=> array(
						'title'		=> __( 'Small', 'custom-fields-for-rtb' ),
						'callback'	=> 'rtb_print_form_text_field',
					),
					'textarea'	=> array(
						'title'		=> __( 'Large', 'custom-fields-for-rtb' ),
						'callback'	=> 'rtb_print_form_textarea_field',
					),
				)
			),
			'options'	=> array(
				'title'		=> __( 'Options', 'custom-fields-for-rtb' ),
				'subtypes'	=> array(
					'select'	=> array(
						'title'		=> __( 'Dropdown', 'custom-fields-for-rtb' ),
						'callback'	=> 'rtb_print_form_select_field',
					),
					'checkbox'	=> array(
						'title'		=> __( 'Checkbox', 'custom-fields-for-rtb' ),
						'callback'	=> 'rtb_print_form_checkbox_field',
					),
					'radio'		=> array(
						'title'		=> __( 'Radio', 'custom-fields-for-rtb' ),
						'callback'	=> 'rtb_print_form_radio_field',
					),
				),
			),
			'confirm'	=> array(
				'title'		=> __( 'Confirm', 'custom-fields-for-rtb' ),
				'subtypes'	=> array(
					'confirm'	=> array(
						'title'		=> __( 'Confirm', 'custom-fields-for-rtb' ),
						'callback'	=> 'rtb_print_form_confirm_field',
					),
				)
			)
		);

		$this->valid_field_types = apply_filters( 'cffrtb_valid_field_types', $field_types );

		return $this->valid_field_types;
	}

	/**
	 * Get the callback function for a field type
	 *
	 * @since 0.1
	 */
	public function get_callback_function( $type, $subtype ) {

		$types = $this->get_valid_field_types();

		// Safe fallback
		if ( empty( $types[ $type ] ) || empty( $types[ $type ]['subtypes'][ $subtype ] ) ) {
			return 'rtb_print_form_text_field';
		}

		return $this->valid_field_types[ $type ]['subtypes'][ $subtype ]['callback'];
	}

	/**
	 * Modify fields based on modified options array
	 *
	 * @todo this does not handle disabled fields or fieldsets yet
	 * @since 0.1
	 */
	public function modify_form_fields( $fields, $request ) {
		global $rtb_controller;

		$modified = get_option( $rtb_controller->custom_fields->modified_option_key );

		// Strip out a location field when it has been saved to the modified option,
		// but the multi-locations feature is no longer enabled.
		if ( !empty( $modified['location'] ) && !$rtb_controller->locations->post_type ) {
			unset( $modified['location'] );
		}

		$fields_query = new WP_Query( array( 'post_type' => 'cffrtb_field', 'post_status' => 'publish', 'posts_per_page' => 1000 ) );

		if ( empty( $modified ) && !$fields_query->have_posts() ) {
			wp_reset_query();
			return $fields;
		}

		// Add custom fields
		global $post;
		while( $fields_query->have_posts() ) {
			$fields_query->the_post();

			$meta = get_post_meta( get_the_ID(), 'cffrtb', true );

			if ( $meta['type'] == 'fieldset' ) {

				$pre_existing_fields = !empty( $fields[ $post->post_name ] ) && !empty( $fields[ $post->post_name ]['fields'] ) ? $fields[ $post->post_name ]['fields'] : array();

				$fields[ $post->post_name ] = array(
					'legend'	=> get_the_title(),
					'order'		=> $post->menu_order,
					'fields'	=> $pre_existing_fields,
					'ID'		=> get_the_ID(),
				);

				continue;

			}

			if ( empty( $meta['fieldset'] ) ) {
				end( $fields );
				$meta['fieldset'] = key( $fields );
				reset( $fields );
			}

			if ( empty( $fields[ $meta['fieldset'] ] ) ) {
				$fields[ $meta['fieldset'] ] = array( 'fields' => array() );
			}

			$fields[ $meta['fieldset'] ]['fields'][ $post->post_name ] = array(
				'title'				=> get_the_title(),
				'request_input'		=> rtb_get_request_input( $post->post_name, $request ),
				'callback'			=> $this->get_callback_function( $meta['type'], $meta['subtype'] ),
				'required'			=> !empty( $meta['required'] ),
				'order'				=> $post->menu_order,
				'ID'				=> get_the_ID(),
			);

			if ( !empty( $meta['options'] ) ) {
				$fields[ $meta['fieldset'] ]['fields'][ $post->post_name ]['callback_args'] = array(
					'options' => $this->format_option_field_values( $meta['options'] ),
				);
			}
		}

		// Disable default fields
		if ( !empty( $modified ) ) {
			$orphans = array();
			$disabled = array();
			foreach( $modified as $slug => $item ) {

				if ( empty( $item['fieldset'] ) ) {
					continue;
				}

				if ( !empty( $item['disabled'] ) ) {

					// Disable a fieldset and store any orphaned fields
					if ( $item['fieldset'] == $slug ) {
						if ( isset( $fields[ $slug ] ) ) {
							$orphans = array_merge( $orphans, $fields[ $slug ]['fields'] );
							unset( $fields[ $slug ] );
						}

					// Find and disable a field
					} else {
						foreach( $fields as $fieldset_slug => $fieldset ) {
							if ( array_key_exists( $slug, $fieldset['fields'] ) ) {
								unset( $fields[ $fieldset_slug ]['fields'][ $slug ] );
								$disabled[] = $slug;
								break;
							}
						}
					}

					$disabled[] = $slug;
				}
			}

			// Add any orphan'd fields into an extra fieldset so they can be
			// moved
			if ( count( $orphans ) ) {
				$fields['cffrtb-extra-fields'] = array(
					'fields' => $orphans
				);
			}

			// Remove disabled fields from the $modified array so we're
			// no longer messing with them
			foreach( $disabled as $slug ) {
				unset( $modified[$slug] );
			}

			// Move default fields
			foreach( $modified as $slug => $item ) {

				if ( empty( $item['fieldset'] ) || $item['fieldset'] == $slug ) {
					continue;
				}

				// Field exists in fieldset - doesn't need to be moved
				if ( !empty( $fields[ $item['fieldset'] ] ) && !empty( $fields[ $item['fieldset'] ]['fields'][ $slug ] ) ) {
					continue;

				// Field needs to be moved
				} else {

					if ( empty( $fields[ $item['fieldset'] ] ) ) {
						$fields[ $item['fieldset'] ] = array(
							'fields'	=> array()
						);
					}

					// Find, copy and then remove the existing field
					foreach( $fields as $fieldset_slug => $fieldset ) {
						if ( array_key_exists( $slug, $fieldset['fields'] ) ) {
							$fields[ $item['fieldset'] ]['fields'][ $slug ] = $fieldset['fields'][ $slug ];
							unset( $fields[ $fieldset_slug ]['fields'][ $slug ] );
							break;
						}
					}
				}
			}

			// Modify default titles for fields and fieldsets and add order
			foreach( $modified as $slug => $field ) {

				if ( empty( $field['fieldset'] ) ) {
					continue; // @todo this suggests an error in the data
				}

				// Fieldset
				if ( $slug == $field['fieldset'] ) {

					if ( !empty( $field['title'] ) ) {
						$fields[ $slug ]['legend'] = $field['title'];
					}

					if ( array_key_exists( 'order', $field ) ) {
						$fields[ $slug ]['order'] = $field['order'];
					}

				// Field
				} else {

					if ( !empty( $field['title'] ) ) {
						$fields[ $field['fieldset'] ]['fields'][ $slug ]['title'] = $field['title'];
					}

					if ( array_key_exists( 'order', $field ) ) {
						$fields[ $field['fieldset'] ]['fields'][ $slug ]['order'] = $field['order'];
					}
				}
			}
		}

		// Remove the extra fieldset if it has been created but is no
		// longer used
		if ( isset( $fields['cffrtb-extra-fields'] ) && empty( $fields['cffrtb-extra=fields']['fields'] ) ) {
			unset( $fields['cffrtb-extra-fields'] );
		}

		// Sort the fieldsets and fields by order
		$first = reset( $fields );
		if ( isset( $first['order'] ) ) {
			uasort( $fields, array( $this, 'sort_by_order' ) );
			foreach( $fields as $slug => $fieldset ) {

				// Sort fields in fieldset
				if ( isset( $fields[ $slug ]['fields'] ) ) {
					uasort( $fields[ $slug ]['fields'], array( $this, 'sort_by_order' ) );

				// Pass an empty array of fields in the fieldset to reduce
				// chance of unwanted PHP notices in 3rd party code
				} else {
					$fields[ $slug ]['fields'] = array();
				}
			}
		}

		wp_reset_query();

		return $fields;
	}

	/**
	 * Sort an associative array by the value's order parameter
	 *
	 * @usedby self::modify_form_fields()
	 * @since 0.1
	 */
	public function sort_by_order( $a, $b ) {

		if ( empty( $a['order'] ) ) {
			$a['order'] = 0;
		}

		if ( empty( $b['order'] ) ) {
			// This prevents it from changing the sort order of fields
			// or fieldsets that have an identical order number. It
			// could interfere with other ordering routines, but it
			// should only really be an issue with rare third-party
			// manipulations of the $fields array, which define or load
			// order values in unexpected ways.
			$b['order'] = $a['order']++;
		}

		return $a['order'] - $b['order'];
	}

	/**
	 * Format option field values in order to pass them to a select field
	 * callback. Should be assoc array of key/value pairs
	 *
	 * @since 0.1
	 */
	public function format_option_field_values( $options ) {

		// Don't show disabled options
		$options = array_filter( $options, array( $this, 'filter_active_option' ) );

		$formatted = array();
		foreach( $options as $key => $option ) {

			// Store key for fields that were generated before the change in
			// version 1.1, when the key represented the field id
			if ( !isset( $option['id'] ) ) {
				$option['id'] = $key;
			}

			$formatted[ $option['id'] ] = $option['value'];
		}

		return $formatted;
	}

	/**
	 * Filter an option based on its disabled state. To be used in `array_filter`
	 *
	 * @since 0.1
	 */
	public function filter_active_option( $option ) {
		return empty( $option['disabled'] );
	}

	/**
	 * Convert a custom field value to its display value
	 *
	 * @since 0.1
	 */
	public function get_display_value( $val, $field, $checkbox_icon = '', $use_html = true ) {

		$display_val = '';

		// Checkboxes
		if ( $field->type == 'options' && is_array( $val ) ) {
			$display_vals = array();
			foreach( $val as $val_i ) {
				if ( isset( $field->options[ $val_i ] ) ) {
					$display_vals[] = esc_html( $this->get_option_val_by_id( $val_i, $field->options ) );
				}
			}

			if ( $use_html ) {
				$display_val = '<ul><li>' . $checkbox_icon . join( '</li><li>' . $checkbox_icon, $display_vals ) . '</li></ul>';
			} else {
				$display_val = join( esc_html_x( ', ', 'separator between two selected options', 'custom-fields-for-rtb' ), $display_vals );
			}

		// Select/radio fields
		} elseif ( $field->type == 'options' ) {
			$display_val = esc_html( $this->get_option_val_by_id( $val, $field->options ) );

		// Confirmation checkboxes
		} elseif ( $field->type == 'confirm' ) {
			$display_val = apply_filters( 'cffrtb_confirm_checkmark', sprintf( __( '%s Checked', 'custom-fields-for-rtb' ), $checkbox_icon ), $val, $field );

		// Text fields
		} else {
			$display_val = esc_html( $val );
		}

		return $display_val;
	}

	/**
	 * Retrieve a booking's custom field values in a sorted array ready to be
	 * displayed in markup
	 *
	 * @since 0.1
	 */
	public function get_booking_fields_display_array( $booking ) {
		global $rtb_controller;

		if ( !isset( $booking->custom_fields ) ) {
			return array();
		}

		$fields = rtb_get_custom_fields();

		$checkbox_icon = $this->get_checkbox_icon();

		$return = array();
		foreach( $booking->custom_fields as $slug => $val ) {
			$field = $rtb_controller->fields->get_field_in_array( $slug, $fields );
			$return[] = $this->get_booking_field_display_array( $field, $slug, $val, $checkbox_icon, $booking );
		}

		uasort( $return, array( $this, 'sort_by_order' ) );

		return $return;
	}

	/**
	 * Retrieve a custom field's value
	 *
	 * @since 1.5
	 */
	public function get_booking_field_display_array( $field, $slug, $val, $checkbox_icon, $booking ) {

		// If the field is no longer available, let's try to fall
		// back to something kind of meaningful
		if ( empty( $field ) ) {
			$title = $slug;
			$display_val = $val;
			$classes = array( $slug );
			$order = 9999; // after everything else!

		// Otherwise let's get good display values
		} else {
			$title = $field->title;
			$display_val = apply_filters( 'cffrtb_display_value_admin_list', $this->get_display_value( $val, $field, $checkbox_icon ), $val, $field, $booking );
			$classes = array($slug, $field->type, $field->subtype );
			$order = $field->order;
		}

		return array(
			'title' => $title,
			'display_val' => $display_val,
			'classes' => $classes,
			'order' => $order,
		);
	}

	/**
	 * Retrieve an option value from array by its id
	 *
	 * @since 1.1
	 */
	public function get_option_val_by_id( $id, $options ) {

		foreach( $options as $key => $option ) {

			// Store key for fields that were generated before the change in
			// version 1.1, when the key represented the field id
			if ( !isset( $option['id'] ) ) {
				$option['id'] = $key;
			}

			if ( $option['id'] == $id ) {
				return $option['value'];
			}
		}

		return '';
	}

	/**
	 * Add custom fields to the array of all possible bookings columns
	 *
	 * @since 1.2
	 */
	public function modify_all_bookings_columns( $columns ) {

		$fields_query = new WP_Query( array( 'post_type' => 'cffrtb_field', 'post_status' => 'publish', 'posts_per_page' => 1000 ) );

		if ( !$fields_query->have_posts() ) {
			wp_reset_query();
			return $columns;
		}

		global $post;
		while( $fields_query->have_posts() ) {
			$fields_query->the_post();

			if ( isset( $columns[ $post->post_name ] ) ) {
				continue;
			}

			$meta = get_post_meta( get_the_ID(), 'cffrtb', true );
			if ( $meta['type'] == 'fieldset' ) {
				continue;
			}

			$columns[ $post->post_name ] = $post->post_title;
		}

		wp_reset_query();

		return $columns;
	}

	/**
	 * Add columns for custom fields that have been enabled
	 *
	 * @since 1.2
	 */
	public function modify_bookings_columns( $columns ) {
		global $rtb_controller;

		// Change names of default columns if they've been edited
		$modified = get_option( $rtb_controller->custom_fields->modified_option_key );
		if ( !empty( $modified ) ) {
			foreach( $modified as $field_key => $field ) {
				if ( isset( $columns[$field_key] ) && !empty( $field['title'] ) ) {
					$columns[$field_key] = $field['title'];
				}
			}
		}

		global $rtb_controller;
		$visible_columns = $rtb_controller->settings->get_setting( 'bookings-table-columns' );
		if ( empty( $visible_columns ) ) {
			return $columns;
		}

		$fields_query = new WP_Query( array( 'post_type' => 'cffrtb_field', 'post_status' => 'publish', 'posts_per_page' => 1000 ) );

		if ( !$fields_query->have_posts() ) {
			wp_reset_query();
			return $columns;
		}

		global $post;
		while( $fields_query->have_posts() ) {
			$fields_query->the_post();

			if ( !in_array( $post->post_name, $visible_columns ) ) {
				continue;
			}

			$columns[ $post->post_name ] = $post->post_title;
		}

		wp_reset_query();

		// Keep the details column last
		// Only exists in rtb v1.5+
		if ( isset( $columns['details'] ) ) {
			$details = $columns['details'];
			unset( $columns['details'] );
			$columns['details'] = $details;
		}


		return $columns;
	}

	/**
	 * Print custom fields in a custom column in the bookings list table
	 *
	 * @since 0.1
	 */
	public function add_custom_column_value( $value, $booking, $column_name ) {
		global $rtb_controller;

		if ( empty( $booking->custom_fields ) || !isset( $booking->custom_fields[$column_name] ) ) {
			return $value;
		}

		$fields = rtb_get_custom_fields();
		$field = $rtb_controller->fields->get_field_in_array( $column_name, $fields );
		$checkbox_icon = $this->get_checkbox_icon();
		$field_data = $this->get_booking_field_display_array( $field, $field->slug, $booking->custom_fields[$column_name], $checkbox_icon, $booking );

		return $value . $field_data['display_val'];
	}

	/**
	* Add custom fields output to details column
	*
	* @since 1.5
	*/
	public function add_fields_to_details_panel( $details, $booking ) {
		global $rtb_controller;

		if ( !isset( $booking->custom_fields ) || empty( $booking->custom_fields ) ) {
			return $details;
		}

		global $rtb_controller;
		$visible_columns = $rtb_controller->settings->get_setting( 'bookings-table-columns' );
		if ( !is_array( $visible_columns ) ) {
			return $details;
		}

		$fields = rtb_get_custom_fields();
		foreach( $booking->custom_fields as $slug => $val ) {

			// Skip fields with their own columns
			if ( in_array( $slug, $visible_columns ) ) {
				continue;
			}

			$field = $rtb_controller->fields->get_field_in_array( $slug, $fields );
			$checkbox_icon = $this->get_checkbox_icon();
			$field_data = $this->get_booking_field_display_array( $field, $field->slug, $booking->custom_fields[$slug], $checkbox_icon, $booking );
			$details[] = array(
				'label' => $field_data['title'],
				'value' => $field_data['display_val'],
			);
		}

		return $details;
	}

	/**
	 * Retrieve checkbox icon markup
	 *
	 * @since 1.5
	 */
	public function get_checkbox_icon() {
		return is_string( $this->checkbox_icon ) ? $this->checkbox_icon : apply_filters( 'cffrtb_checkbox_icon', '<span class="dashicons dashicons-yes"></span> ' );
	}
}
} // endif
