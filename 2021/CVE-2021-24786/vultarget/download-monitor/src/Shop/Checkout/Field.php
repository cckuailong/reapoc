<?php

namespace Never5\DownloadMonitor\Shop\Checkout;

use Never5\DownloadMonitor\Shop\Services\Services;

class Field {

	private $fields;

	/**
	 * Field constructor.
	 */
	public function __construct() {

		$this->fields = array(
			'first_name' => array(
				'name'        => 'first_name',
				'type'        => 'text',
				'class'       => array(),
				'row-class'   => array( 'half', 'first' ),
				'label'       => __( 'First name', 'download-monitor' ),
				'placeholder' => "",
				'required'    => true,
				'row-type'    => 'half'
			),
			'last_name'  => array(
				'name'        => 'last_name',
				'type'        => 'text',
				'class'       => array(),
				'row-class'   => array( 'half', 'last' ),
				'label'       => __( 'Last name', 'download-monitor' ),
				'placeholder' => "",
				'required'    => true,
			),
			'company'    => array(
				'name'        => 'company',
				'type'        => 'text',
				'class'       => array(),
				'row-class'   => array(),
				'label'       => __( 'Company name', 'download-monitor' ),
				'placeholder' => "",
				'required'    => false,
			),
			'email'      => array(
				'name'        => 'email',
				'type'        => 'text',
				'class'       => array(),
				'row-class'   => array(),
				'label'       => __( 'Email address', 'download-monitor' ),
				'placeholder' => "",
				'required'    => true,
			),
			'address_1'  => array(
				'name'        => 'address_1',
				'type'        => 'text',
				'class'       => array(),
				'row-class'   => array(),
				'label'       => __( 'Address', 'download-monitor' ),
				'placeholder' => "",
				'required'    => true,
			),
			'postcode'   => array(
				'name'        => 'postcode',
				'type'        => 'text',
				'class'       => array(),
				'row-class'   => array(),
				'label'       => __( 'Postcode / ZIP', 'download-monitor' ),
				'placeholder' => "",
				'required'    => true,
			),
			'city'       => array(
				'name'        => 'city',
				'type'        => 'text',
				'class'       => array(),
				'row-class'   => array(),
				'label'       => __( 'City', 'download-monitor' ),
				'placeholder' => "",
				'required'    => true,
			),
			'country'    => array(
				'name'        => 'country',
				'type'        => 'select',
				'options'     => Services::get()->service( 'country' )->get_countries(),
				'class'       => array(),
				'row-class'   => array(),
				'label'       => __( 'Country', 'download-monitor' ),
				'placeholder' => download_monitor()->service( 'settings' )->get_option( 'base_country' ),
				'required'    => true,
			)
		);

	}

	/**
	 * Generate field on options
	 *
	 * @param array $options
	 * @param string $value
	 *
	 * @return string
	 */
	private function do_field( $options, $value ) {
		switch ( $options['type'] ) {
			case 'select':

				$selected = ( $value !== "" ) ? $value : $options['placeholder'];

				$return = sprintf( '<select name="dlm_%s" id="dlm_%s" class="dlm-checkout-field">', esc_attr( $options['name'] ), esc_attr( $options['name'] ) );
				if ( ! empty( $options['options'] ) ) {
					foreach ( $options['options'] as $k => $v ) {
						$return .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $selected, $k, false ), esc_html( $v ) );
					}
				}
				$return .= '</select>';

				return $return;
				break;
			case 'text':
			default:
				return sprintf( '<input type="text" class="dlm-checkout-field" id="dlm_%s" name="dlm_%s" value="%s" placeholder="%s" />', esc_attr( $options['name'] ), esc_attr( $options['name'] ), esc_attr( $value ), esc_attr( $options['placeholder'] ) );
				break;
		}

		return '';
	}

	/**
	 * Returns fields
	 *
	 * @return array
	 */
	public function get_fields() {
		return apply_filters( 'dlm_shop_checkout_fields', $this->fields );
	}

	/**
	 * @return array
	 */
	public function get_required_fields() {
		$required_fields = array();
		$fields          = $this->get_fields();
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				if ( ! empty( $field['required'] ) && true === $field['required'] ) {
					$required_fields[] = $field['name'];
				}
			}
		}

		return $required_fields;
	}


	/**
	 * Generate field based on given options
	 *
	 * @param array $options
	 * @param string $value
	 *
	 * @return string
	 */
	public function generate( $options, $value = "" ) {
		$output = "";

		$row_class = 'dlm-checkout-row' . ( isset( $options['row-class'] ) ? ' dlm-checkout-row-' . implode( ' dlm-checkout-row-', $options['row-class'] ) : '' );

		$output .= '<div class="' . esc_attr( $row_class ) . '">';

		$output .= '<label for="dlm_' . esc_attr( $options['name'] ) . '">';

		$output .= esc_html( $options['label'] );

		if ( isset( $options['required'] ) && true === $options['required'] ) {
			$output .= '<span class="dlm-checkout-required">*</span>';
		}

		$output .= '</label>';

		$output .= '<span class="dlm-checkout-input-wrapper">';

		$output .= $this->do_field( $options, $value );

		$output .= '</span>';
		$output .= '</div>';

		return $output;

	}

	/**
	 * Generate and output all checkout fields
	 *
	 * @var array $values ([ 'option_key' => 'option_value' ])
	 */
	public function output_all_fields( $values = array() ) {
		$fields = $this->get_fields();

		foreach ( $fields as $field ) {
			$val = ( isset( $values[ $field['name'] ] ) ) ? $values[ $field['name'] ] : "";
			echo $this->generate( $field, $val );
		}

	}

}