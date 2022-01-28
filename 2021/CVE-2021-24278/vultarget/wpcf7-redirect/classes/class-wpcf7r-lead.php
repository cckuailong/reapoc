<?php
/**
 * Class WPCF7R_Lead - Container class that handles lead
 */

defined( 'ABSPATH' ) || exit;

class WPCF7R_Lead {

	/**
	 * Create an instance
	 * Save the post id and post reference
	 *
	 * @param string $post_id
	 */
	public function __construct( $post_id = '' ) {
		if ( is_object( $post_id ) ) {
			$this->post_id = $post_id->ID;
			$this->post    = $post_id;
		} else {
			$this->post_id = $post_id;
			$this->post    = $post_id;
		}
	}

	/**
	 * Update submitted form data
	 *
	 * @param  array  $args
	 */
	public function update_lead_data( $args = array() ) {
		if ( $args ) {
			foreach ( $args as $meta_key => $meta_value ) {
				update_post_meta( $this->post_id, $meta_key, $meta_value );
			}
		}
	}

	/**
	 * Return the lead ID
	 */
	public function get_id() {
		return $this->post_id;
	}

	/**
	 * Get the post title
	 */
	public function get_title() {
		return get_the_title( $this->get_id() );
	}

	/**
	 * Update the type of lead
	 *
	 * @param  $lead_type
	 */
	public function update_lead_type( $lead_type ) {
		update_post_meta( $this->post_id, 'lead_type', $lead_type );
	}

	/**
	 * Save the action reference and results
	 * @param $action_id
	 * @param $action_type
	 * @param $action_results
	 */
	public function add_action_debug( $action_id, $action_type, $action_results ) {
		$action_details = array(
			'action_id' => $action_id,
			'results'   => $action_results,
		);
		add_post_meta( $this->post_id, $action_type, $action_details );
	}

	/**
	 * Get the creation post of the lead
	 */
	public function get_date() {
		return get_the_date( get_option( 'date_format' ), $this->post_id );
	}

	/**
	 * Get the creation post of the lead
	 */
	public function get_time() {
		return get_the_date( get_option( 'time_format' ), $this->post_id );
	}

	/**
	 * Get the lead type
	 */
	public function get_lead_type() {
		return get_post_meta( $this->post_id, 'lead_type', true );
	}

	/**
	 * Save the user submitted files
	 *
	 * @return void
	 */
	public function update_lead_files( $files ) {
		update_post_meta( $this->post_id, 'files', $files );
	}

	/**
	 * Get lead fields
	 */
	public function get_lead_fields() {
		$action_id = get_post_meta( $this->post_id, 'cf7_action_id', true );

		$custom_meta_fields = get_post_custom( $this->post_id );
		$lead_fields        = array();

		$action = WPCF7R_Action::get_action( (int) $action_id );

		if ( $action_id && $action ) {

			$fields = maybe_unserialize( $action->get( 'leads_map' ) );

			foreach ( $fields as $field_key => $field_value ) {
				if ( 'lead_id' === $field_key ) {
					continue;
				} else {
					if ( isset( $custom_meta_fields[ $field_key ] ) ) {
						$value = maybe_unserialize( $custom_meta_fields[ $field_key ][0] );
					} elseif ( isset( $custom_meta_fields['files'] ) && $custom_meta_fields['files'] ) {
						$value = maybe_unserialize( $custom_meta_fields['files'][0] );
						$value = isset( $value[ $field_key ] ) && $value[ $field_key ] ? $value[ $field_key ] : '';
					}
				}

				if ( is_array( $value ) ) {
					if ( isset( $value['path'] ) ) {
						$lead_fields[ $field_key ] = array(
							'type'        => 'download',
							'placeholder' => '',
							'value'       => $value['base64_file'],
							'filetype'    => $value['type'],
							'filename'    => $value['name'],
							'label'       => isset( $field_value['tag'] ) && $field_value['tag'] ? $field_value['tag'] : __( 'File', 'wpcf7-redirect' ),
							'name'        => $field_key,
							'prefix'      => '',
						);
					} else {
						foreach ( $value as $value_field_key => $value_field_value ) {
							$lead_fields[ $field_key . '-' . $value_field_key ] = array(
								'type'        => 'text',
								'placeholder' => '',
								'label'       => isset( $field_value['tag'] ) && $field_value['tag'] ? $field_value['tag'] : $field_key,
								'name'        => $field_key,
								'value'       => maybe_unserialize( $value_field_value ),
								'prefix'      => '',
							);
						}
					}
				} else {
					$lead_fields[ $field_key ] = array(
						'type'        => 'text',
						'placeholder' => '',
						'value'       => $value,
						'label'       => isset( $field_value['tag'] ) && $field_value['tag'] ? $field_value['tag'] : $field_key,
						'name'        => $field_key,
						'prefix'      => '',
					);
				}
			}
		} else {
			foreach ( $custom_meta_fields as $field_key => $field_value ) {
				$value = maybe_unserialize( $field_value[0] );
				if ( is_array( $value ) ) {
					foreach ( $value as $value_field_key => $value_field_value ) {
						$lead_fields[ $field_key . '-' . $value_field_key ] = array(
							'type'        => 'text',
							'placeholder' => '',
							'label'       => $field_key,
							'name'        => $field_key,
							'value'       => maybe_unserialize( $value_field_value ),
							'prefix'      => '',
						);
					}
				} else {
					$lead_fields[ $field_key ] = array(
						'type'        => 'text',
						'placeholder' => '',
						'value'       => $value,
						'label'       => $field_key,
						'name'        => $field_key,
						'prefix'      => '',
					);
				}
			}
		}

		return apply_filters( 'wpcf7r_fields' , $lead_fields );
	}
}
