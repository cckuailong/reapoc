<?php
/**
 * View sction plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Helper
 * @subpackage        View
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * View sction plugin class
 */
class TInvWL_ViewSection extends TInvWL_View {

	/**
	 * Array form fields
	 *
	 * @var array
	 */
	private $data;
	/**
	 * Array form value fields
	 *
	 * @var array
	 */
	private $value;
	/**
	 * Helper show flag
	 *
	 * @var boolean
	 */
	private $helper;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		self::$_name    = $plugin_name;
		self::$_version = $version;
		$this->data     = array();
		$this->value    = array();
		$this->helper   = false;
	}

	/**
	 * Prepare section info
	 *
	 * @param array $content Array section info.
	 *
	 * @return array
	 */
	function section_data( $content ) {
		$data = array();
		if ( array_key_exists( 'fields', $content ) ) {
			unset( $content['fields'] );
		}
		foreach ( array( 'id', 'title', 'desc', 'show_names' ) as $field ) {
			if ( array_key_exists( $field, $content ) ) {
				$data[ $field ] = $content[ $field ];
				unset( $content[ $field ] );
			} else {
				$data[ $field ] = '';
			}
		}
		$data['extra']       = $content;
		$data['show_names']  = filter_var( $data['show_names'], FILTER_VALIDATE_BOOLEAN );
		$data['show_helper'] = $this->helper;

		return $data;
	}

	/**
	 * Update section info for show helper block
	 *
	 * @param array $section Array section info.
	 */
	function section_helper( $section ) {
		$this->helper = false;
		if ( array_key_exists( 'desc', $section ) ) {
			$this->helper = true;
		}
		if ( array_key_exists( 'fields', $section ) ) {
			foreach ( $section['fields'] as $field ) {
				if ( array_key_exists( 'desc', $field ) ) {
					$this->helper = true;
				}
			}
		}
	}

	/**
	 * Create show section
	 *
	 * @param array $data Array section.
	 *
	 * @return string
	 */
	function section( $data ) {
		$fields = array();
		$this->section_helper( $data );
		if ( array_key_exists( 'fields', $data ) ) {
			$fields = $data['fields'];
		}
		$skin = 'section-general';
		if ( array_key_exists( 'skin', $data ) ) {
			$skin = $data['skin'];
			unset( $data['skin'] );
		}
		$field_counts = count( $fields );
		$data         = $this->section_data( $data );

		$data['extra']['id'] = $this->section_id = $data['id'];
		if ( array_key_exists( 'class', $data['extra'] ) ) {
			$data['extra']['class'] .= ' tinvwl-panel w-bg w-shadow';
		} else {
			$data['extra']['class'] = 'tinvwl-panel w-bg w-shadow';
		}
		if ( array_key_exists( $this->section_id, $this->value ) ) {
			foreach ( $this->value[ $this->section_id ] as $key => $value ) {
				TInvWL_Form::setvalue( sprintf( '%s-%s', $this->section_id, $key ), $value );
			}
		}

		$data['groups'] = $this->groups( $fields );

		$data['extra'] = TInvWL_Form::__atrtostr( $data['extra'] );
		ob_start();
		do_action( "tinvwl_section_{$this->section_id}_before", $data );
		self::view( $skin, $data, '' );
		wp_nonce_field( self::$_name . "_check_{$this->section_id}_{$field_counts}", $this->section_id . '_nonce' );
		do_action( "tinvwl_section_{$this->section_id}_after", $data );

		return ob_get_clean();
	}

	/**
	 * Prepare group fields
	 *
	 * @param array $fields Array group fields.
	 *
	 * @return array
	 */
	function prepare_group( $fields ) {
		$groups = array();
		$group  = array();
		foreach ( $fields as $field ) {
			switch ( $field['type'] ) {
				case 'groupHTML':
					if ( ! empty( $group ) ) {
						$group['type'] = 'group';
						$groups[]      = $group;
					}
					$group         = $field;
					$group['type'] = 'groupHTML';
					$groups[]      = $group;
					$group         = array();
					break;
				case 'group':
					if ( ! empty( $group ) ) {
						$group['type'] = 'group';
						$groups[]      = $group;
					}
					$group = $field;
					break;
				case 'groupend':
					$group['type'] = 'group';
					$groups[]      = $group;
					$group         = array();
					break;
				default:
					$group['fields'][] = $field;
					break;
			}
		}
		if ( ! empty( $group ) ) {
			$group['type'] = 'group';
			$groups[]      = $group;
		}

		return $groups;
	}

	/**
	 * Create show group
	 *
	 * @param array $data Array group.
	 *
	 * @return string
	 */
	function group( $data ) {
		$fields = array();
		if ( array_key_exists( 'fields', $data ) ) {
			$fields = $data['fields'];
		}
		$html         = false;
		$html_content = '';
		if ( 'groupHTML' === $data['type'] ) {
			$html         = true;
			$html_content = $data['html'];
			unset( $data['html'] );
		}
		$skin = 'section-group';
		if ( array_key_exists( 'skin', $data ) ) {
			$skin = $data['skin'];
			unset( $data['skin'] );
		}
		$data = $this->section_data( $data );

		if ( empty( $data['id'] ) ) {
			$this->group_id = $this->section_id;
		} else {
			$data['id'] = sprintf( '%s-%s', $this->section_id, $data['id'] );

			$data['extra']['id'] = $this->group_id = $data['id'];
		}
		if ( array_key_exists( 'class', $data['extra'] ) ) {
			$data['extra']['class'] .= ' content-in';
		} else {
			$data['extra']['class'] = 'content-in';
		}
		unset( $data['extra']['type'] );
		if ( $html ) {
			$data['fields_count'] = 1;
			ob_start();
			self::view( 'section-field-html', array(
				'html'            => $html_content,
				'show_field_desc' => true,
				'show_helper'     => $this->helper,
				'desc'            => '',
				'extra_div'       => '',
			), '' );
			$data['fields'] = ob_get_clean();
		} else {
			$data['fields_count'] = count( $fields ) + 1;
			$data['fields']       = $this->fields( $fields, empty( $data['desc'] ) );
		}
		$data['extra']['id'] = $data['id'];

		$data['extra'] = TInvWL_Form::__atrtostr( $data['extra'] );
		ob_start();
		do_action( "tinvwl_sectiongroup_{$this->group_id}_before", $data );
		self::view( $skin, $data, '' );
		do_action( "tinvwl_sectiongroup_{$this->group_id}_after", $data );

		return ob_get_clean();
	}

	/**
	 * Build groups
	 *
	 * @param array $fields Array fields.
	 *
	 * @return string
	 */
	function groups( $fields ) {
		$groups  = $this->prepare_group( $fields );
		$content = '';
		foreach ( $groups as $data ) {
			$content .= $this->group( $data );
		}

		return $content;
	}

	/**
	 * Prepare fields
	 *
	 * @param array $content Array fields info.
	 *
	 * @return array
	 */
	function field_data( $content ) {
		$data = array();
		if ( array_key_exists( 'fields', $content ) ) {
			unset( $content['fields'] );
		}
		foreach (
			array(
				'type',
				'name',
				'text',
				'std',
				'desc',
				'options',
				'extra',
				'extra_label',
				'validate'
			) as $field
		) {
			if ( array_key_exists( $field, $content ) ) {
				$data[ $field ] = $content[ $field ];
				unset( $content[ $field ] );
			} else {
				$data[ $field ] = '';
			}
		}
		if ( empty( $data['extra'] ) ) {
			$data['extra'] = array();
		}
		$data['extra_div']   = $content;
		$data['show_helper'] = $this->helper;

		return $data;
	}

	/**
	 * Create field
	 *
	 * @param array $data Array feild attributes.
	 * @param array $show_field_desc Show field descrioptions or field decriptions.
	 *
	 * @return string
	 */
	function field( $data, $show_field_desc = true ) {
		$skin = 'section-field';
		if ( array_key_exists( 'skin', $data ) ) {
			$skin = $data['skin'];
			unset( $data['skin'] );
		}
		$data = $this->field_data( $data );
		$type = '_' . $data['type'];
		$name = sprintf( '%s-%s', $this->section_id, $data['name'] );

		$data['extra_div']['id'] = sprintf( '%s--%s', $this->group_id, $data['name'] );
		$data['show_field_desc'] = $show_field_desc;
		$data['extra_div']       = TInvWL_Form::__atrtostr( $data['extra_div'] );
		if ( array_key_exists( 'text', $data ) && $data['text'] ) {
			if ( 0 === strlen( trim( $data['text'] ) ) ) {
				if ( array_key_exists( 'class', (array) $data['extra_label'] ) ) {
					$data['extra_label']['class'] .= ' tinvwl-empty';
				} else {
					if ( ! is_array( $data['extra_label'] ) ) {
						$data['extra_label'] = array();
					}
					$data['extra_label']['class'] = 'tinvwl-empty';
				}
			}
			$data['label'] = apply_filters( "tinvwl_labelfor_{$name}", ( $data['text'] ) ? TInvWL_Form::_label( $name, esc_html( $data['text'] ), $data['extra_label'] ) : '' );
			unset( $data['extra_label'] );
		} else {
			$data['label'] = '';
		}
		if ( is_array( $data['extra'] ) ) {
			if ( array_key_exists( 'class', $data['extra'] ) ) {
				$data['extra']['class'] .= ' form-control';
			} else {
				$data['extra']['class'] = 'form-control';
			}
		} else {
			$data['extra'] .= 'class="form-control"';
		}
		$data['field'] = apply_filters( "tinvwl_field_{$name}_before", '' );
		$data['std']   = apply_filters( "tinvwl_field_{$name}_defaultvalue", $data['std'] );
		$data['extra'] = apply_filters( "tinvwl_field_{$name}_extra", $data['extra'] );
		if ( empty( $data['options'] ) ) {
			$data['field'] .= TInvWL_Form::$type( $name, $data['std'], $data['extra'] );
		} else {
			$data['options'] = apply_filters( "tinvwl_field_{$name}_options", $data['options'] );
			$data['field']   .= TInvWL_Form::$type( $name, $data['std'], $data['extra'], $data['options'] );
		}
		$data['field'] .= apply_filters( "tinvwl_field_{$name}_after", '' );

		ob_start();
		do_action( "tinvwl_sectionfield_{$name}_before", $data );
		self::view( $skin, $data, '' );
		do_action( "tinvwl_sectionfield_{$name}_after", $data );

		return ob_get_clean();
	}

	/**
	 * Build fields
	 *
	 * @param array $fields Array fields.
	 * @param array $show_field_desc Show field descrioptions or field decriptions.
	 *
	 * @return string
	 */
	function fields( $fields, $show_field_desc = true ) {
		$content = '';
		foreach ( $fields as $data ) {
			$content .= $this->field( $data, $show_field_desc );
		}

		return $content;
	}

	/**
	 * Run view section
	 *
	 * @param bollean $echo output or return sections.
	 *
	 * @return string
	 */
	function Run( $echo = true ) {
		$content = apply_filters( 'tinvwl_section_before', '' );
		foreach ( $this->data as $data ) {
			$content .= $this->section( $data );
		}
		$content .= apply_filters( 'tinvwl_section_after', '' );
		if ( $echo ) {
			echo $content; // WPCS: xss ok.
		} else {
			return $content;
		}
	}

	/**
	 * Load section and fields structure
	 *
	 * @param array $sections Array sections.
	 */
	function load_data( $sections ) {
		$this->data = $sections;
	}

	/**
	 * Load value for section fields
	 *
	 * @param array $sections Array values sections fields.
	 */
	function load_value( $sections ) {
		$this->value = $sections;
	}

	/**
	 * Basic attributes for validation form elements
	 *
	 * @link http://php.net/manual/ru/filter.filters.php Types of filters.
	 *
	 * @param string $type Field name.
	 *
	 * @return mixed
	 */
	function validation_type( $type ) {
		// @link http://php.net/manual/ru/filter.filters.php
		$types = array(
			'button'        => FILTER_DEFAULT,
			'button_submit' => FILTER_DEFAULT,
			'checkbox'      => FILTER_SANITIZE_STRING,
			'checkboxonoff' => FILTER_VALIDATE_BOOLEAN,
			'color'         => array(
				'filter'  => FILTER_VALIDATE_REGEXP,
				'options' => array(
					'regexp'  => '/\#[0-9a-f]{6}/i',
					'default' => '#FFFFFF',
				),
			),
			'date'          => FILTER_SANITIZE_STRING,
			'dateperiod'    => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'multicheckbox' => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'multiradio'    => FILTER_SANITIZE_STRING,
			'multiradiobox' => FILTER_SANITIZE_STRING,
			'multiselect'   => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'number'        => FILTER_VALIDATE_INT,
			'numberrange'   => FILTER_VALIDATE_INT,
			'radio'         => FILTER_SANITIZE_STRING,
			'select'        => FILTER_SANITIZE_STRING,
			'text'          => FILTER_SANITIZE_STRING,
			'textarea'      => FILTER_DEFAULT,
			'time'          => FILTER_VALIDATE_INT,
			'timeperiod'    => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'uploadfile'    => FILTER_SANITIZE_STRING,
		);
		if ( array_key_exists( $type, $types ) ) {
			return $types[ $type ];
		}

		return FILTER_DEFAULT;
	}

	/**
	 * Validation field
	 *
	 * @param mixed $value Value.
	 * @param array $field Object fields.
	 *
	 * @return mixed
	 */
	function validation( $value, $field ) {
		$name    = array_key_exists( 'name', $field ) ? $field['name'] : '';
		$options = array_key_exists( 'options', $field ) ? $field['options'] : null;
		$default = array_key_exists( 'std', $field ) ? $field['std'] : '';
		$type    = array_key_exists( 'type', $field ) ? $field['type'] : '';
		$filter  = array_key_exists( 'validate', $field ) ? $field['validate'] : $this->validation_type( $type );
		$flags   = array();
		if ( is_array( $filter ) ) {
			$flags  = $filter;
			$filter = $flags['filter'];
			unset( $flags['filter'] );
		}
		$flags['default'] = $default;

		if ( ! is_array( $value ) ) {
			$value = trim( $value );
		}
		$value = filter_var( $value, $filter, $flags );

		if ( is_array( $options ) ) {
			if ( is_string( $value ) || is_numeric( $value ) ) {
				if ( ! array_key_exists( $value, (array) $options ) ) {
					$value = $default;
				}
			} elseif ( is_array( $value ) ) {
				foreach ( $value as $val ) {
					if ( ! array_key_exists( $val, (array) $options ) ) {
						unset( $value[ $val ] );
					}
				}
				if ( ! $value ) {
					$value = $default;
				}
			} else {
				$value = $default;
			}
		}

		return $value;
	}

	/**
	 * Check post data and validation form fields
	 *
	 * @return array
	 */
	function post_form() {
		$result = array();
		foreach ( $this->data as $data ) {
			if ( array_key_exists( 'noform', $data ) && $data['noform'] ) {
				continue;
			}
			$id     = array_key_exists( 'id', $data ) ? $data['id'] : '';
			$fields = array();
			if ( array_key_exists( 'fields', $data ) ) {
				$fields = $data['fields'];
			}
			$field_counts = count( $fields );
			$nonce        = filter_input( INPUT_POST, $id . '_nonce' );
			if ( $nonce && wp_verify_nonce( $nonce, self::$_name . "_check_{$id}_{$field_counts}" ) ) {
				$result_field = array();

				foreach ( $fields as $field ) {
					$name     = array_key_exists( 'name', $field ) ? $field['name'] : '';
					$postname = sprintf( '%s-%s', $id, $name );
					$value    = filter_input( INPUT_POST, $postname );
					if ( 'multiselect' === $field['type'] ) {
						$value = filter_input( INPUT_POST, $postname, FILTER_DEFAULT, FILTER_FORCE_ARRAY );
					}
					$value = $this->validation( $value, $field );

					$result_field[ $name ] = $value;
				}
				if ( array_key_exists( '', $result_field ) ) {
					unset( $result_field[''] );
				}
				if ( ! empty( $result_field ) ) {
					$result[ $id ] = $result_field;
				}
			}
		}
		if ( empty( $result ) ) {
			return null;
		}

		return $result;
	}

	/**
	 * Returned data field info for custom global template
	 *
	 * @param array $data Sections array.
	 *
	 * @return array
	 */
	function form_data( $data ) {
		return $data;
	}
}
