<?php
/**
 * Basic admin style helper class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Helper
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Basic admin style helper class
 */
abstract class TInvWL_Admin_BaseStyle extends TInvWL_Admin_BaseSection {

	/**
	 * Prepare sections for template attributes
	 *
	 * @return array
	 */
	function prepare_sections() {
		$fields_data = array();
		$fields      = $this->default_style_settings();
		$theme_file  = TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'assets', 'css', 'theme.css' ) );
		if ( file_exists( $theme_file ) ) {
			$fields_data = $this->break_css( file_get_contents( $theme_file ) ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.file_get_contents
		}
		$_fields = $this->prepare_fields( $fields, $fields_data );
		foreach ( $_fields as &$_field ) {
			if ( ! array_key_exists( 'skin', $_field ) ) {
				switch ( $_field['type'] ) {
					case 'group':
					case 'groupHTML':
						$_field['skin'] = 'section-group-style';
						break;
					default:
						$_field['skin'] = 'section-field-style';
						break;
				}
			}
		}

		return $_fields;
	}

	/**
	 * Create section for this settings.
	 *
	 * @return array
	 */
	function constructor_data() {
		return array(
			array(
				'id'         => 'style',
				'title'      => __( 'Templates', 'ti-woocommerce-wishlist' ),
				'desc'       => '',
				'show_names' => false,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'customstyle',
						'text'  => __( 'Use Theme style', 'ti-woocommerce-wishlist' ),
						'std'   => true,
						'extra' => array( 'tiwl-hide' => '.tinvwl-style-options' ),
						'class' => 'tinvwl-header-row',
					),
				),
			),
			array(
				'id'         => 'style_options',
				'title'      => __( 'Template Options', 'ti-woocommerce-wishlist' ),
				'show_names' => true,
				'class'      => 'tinvwl-style-options',
				'fields'     => $this->prepare_sections(),
				'skin'       => 'section-general',
			),
			array(
				'id'         => 'style_plain',
				'title'      => __( 'Template Custom CSS', 'ti-woocommerce-wishlist' ),
				'desc'       => '',
				'show_names' => false,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'allow',
						'text'  => __( 'Template Custom CSS', 'ti-woocommerce-wishlist' ),
						'std'   => false,
						'extra' => array( 'tiwl-show' => '.tiwl-style-custom-allow' ),
						'class' => 'tinvwl-header-row',
					),
					array(
						'type'  => 'group',
						'id'    => 'custom',
						'class' => 'tiwl-style-custom-allow',
					),
					array(
						'type' => 'textarea',
						'name' => 'css',
						'text' => '',
						'std'  => '',
					),
				),
			),
			array(
				'id'     => 'save_buttons',
				'class'  => 'only-button',
				'noform' => true,
				'fields' => array(
					array(
						'type'  => 'button_submit',
						'name'  => 'setting_save',
						'std'   => '<span><i class="ftinvwl ftinvwl-check"></i></span>' . __( 'Save Settings', 'ti-woocommerce-wishlist' ),
						'extra' => array( 'class' => 'tinvwl-btn split status-btn-ok' ),
					),
					array(
						'type'  => 'button_submit',
						'name'  => 'setting_reset',
						'std'   => '<span><i class="ftinvwl ftinvwl-times"></i></span>' . __( 'Reset', 'ti-woocommerce-wishlist' ),
						'extra' => array( 'class' => 'tinvwl-btn split status-btn-ok tinvwl-confirm-reset' ),
					),
					array(
						'type' => 'button_submit_quick',
						'name' => 'setting_save_quick',
						'std'  => '<span><i class="ftinvwl ftinvwl-floppy-o"></i></span>' . __( 'Save', 'ti-woocommerce-wishlist' ),
					),
				),
			),
		);
	}

	/**
	 * Basic function for default theme fields
	 *
	 * @return array
	 */
	function default_style_settings() {
		return array();
	}

	/**
	 * Prepare style fields for sections fields
	 *
	 * @param array $fields Array of fields list.
	 * @param array $data Array of default values for fields.
	 *
	 * @return array
	 */
	function prepare_fields( $fields = array(), $data = array() ) {
		foreach ( $fields as &$field ) {
			if ( ! array_key_exists( 'selector', $field ) || ! array_key_exists( 'element', $field ) ) {
				continue;
			}
			$field['name'] = $this->create_selectorkey( $field['selector'], $field['element'] );
			if ( ! array_key_exists( 'std', $field ) ) {
				$field['std'] = '';
			}
			if ( isset( $data[ $field['selector'] ][ $field['element'] ] ) ) {
				$value = $data[ $field['selector'] ][ $field['element'] ];
				if ( array_key_exists( 'format', (array) $field ) ) {
					$pregx = preg_replace( '/(\[|\]|\\|\/|\^|\$|\%|\.|\||\?|\*|\+|\(|\)|\{|\})/', '\\\${1}', $field['format'] );
					$pregx = str_replace( '\{0\}', '(.*?)', $pregx );
					$pregx = '/^' . $pregx . '$/i';
					if ( preg_match( $pregx, $value, $matches ) ) {
						if ( isset( $matches[1] ) ) {
							$field['std'] = trim( $matches[1] );
							$field['std'] = preg_replace( '/^\.\.\//', TINVWL_URL . 'assets/', $field['std'] );
						}
					}
				} else {
					$field['std'] = $value;
				}
			}
			unset( $field['selector'], $field['element'], $field['format'] );
		}

		return $fields;
	}

	/**
	 * Save value to database
	 *
	 * @param array $data Post section data.
	 *
	 * @return boolean
	 */
	function constructor_save( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}
		if ( array_key_exists( 'style', (array) $data ) && array_key_exists( 'style_options', (array) $data ) ) {
			if ( false === $data['style']['customstyle'] ) {
				$data['style_options']['css'] = $this->convert_styles( $data['style_options'] );
			} else {
//				$data['style_options'] = array();
			}
			delete_transient( $this->_name . '_dynamic_' );
		}
		if ( array_key_exists( 'style_plain', (array) $data ) ) {
			if ( ! $data['style_plain']['allow'] ) {
				$data['style_plain']['css'] = '';
			}
			if ( empty( $data['style_plain']['css'] ) ) {
				$data['style_plain']['allow'] = false;
			}
		}
		if ( filter_input( INPUT_POST, 'save_buttons-setting_reset' ) ) {
			foreach ( array_keys( $data ) as $key ) {
				if ( ! in_array( $key, array( 'style' ) ) ) {
					$data[ $key ] = array();
				}
			}
		}
		parent::constructor_save( $data );
		if ( filter_input( INPUT_POST, 'save_buttons-setting_reset' ) ) {
			tinv_update_option( 'style_options', '', array() );
		}
	}

	/**
	 * Generate fields name for form
	 *
	 * @param string $selector Selector for fields.
	 * @param string $element Attribute name.
	 *
	 * @return string
	 */
	function create_selectorkey( $selector, $element ) {
		return md5( $selector . '||' . $element );
	}

	/**
	 * Create array of css attributes
	 *
	 * @param string $css CSS content.
	 *
	 * @return array
	 */
	function break_css( $css ) {
		$results = array();
		$css     = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css     = preg_replace( '/(\r|\n|\t| {2,})/', '', $css );
		$css     = str_replace( array( '{', '}' ), array( ' { ', ' } ' ), $css );
		preg_match_all( '/(.+?)\s*?\{\s*?(.+?)\s*?\}/', $css, $matches );
		foreach ( array_keys( $matches[0] ) as $i ) {
			foreach ( explode( ';', $matches[2][ $i ] ) as $attr ) {
				if ( strlen( trim( $attr ) ) > 0 ) {
					list( $name, $value ) = explode( ':', $attr );
					$results[ trim( $matches[1][ $i ] ) ][ trim( $name ) ] = trim( $value );
				}
			}
		}

		return $results;
	}

	/**
	 * Convert settings to css
	 *
	 * @param array $style Array of style attributes.
	 *
	 * @return string
	 */
	function convert_styles( $style = array() ) {
		$fields = $this->default_style_settings();
		$styles = array();
		foreach ( $fields as $field ) {
			if ( ! array_key_exists( 'selector', $field ) || ! array_key_exists( 'element', $field ) ) {
				continue;
			}
			$key = $this->create_selectorkey( $field['selector'], $field['element'] );
			if ( array_key_exists( $key, (array) $style ) ) {
				$value = $style[ $key ];
				if ( array_key_exists( 'format', $field ) ) {
					$value = str_replace( '{0}', $value, $field['format'] );
				}
				$styles[ $field['selector'] ][ $field['element'] ] = $value;
			}
		}
		foreach ( $styles as $selector => &$elements ) {
			foreach ( $elements as $key => &$element ) {
				$element = sprintf( '%s:%s;', $key, $element );
			}
			$elements = implode( '', $elements );
			$elements = sprintf( '%s {%s}', $selector, $elements );
		}
		$styles = implode( ' ', $styles );

		return $styles;
	}
}
