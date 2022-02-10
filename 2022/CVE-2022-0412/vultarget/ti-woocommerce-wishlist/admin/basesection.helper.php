<?php
/**
 * Basic admin section helper class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Helper
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Basic admin section helper class
 */
abstract class TInvWL_Admin_BaseSection extends TInvWL_Admin_Base {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 10;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		$menu           = $this->menu();
		if ( ! empty( $menu ) ) {
			add_action( 'tinvwl_admin_menu', array( $this, 'adminmenu' ), $this->priority );
		}
		$this->load_function();
	}

	/**
	 * Add item to admin menu
	 *
	 * @param array $data Menu.
	 *
	 * @return array
	 */
	function adminmenu( $data ) {

		if ( ! is_array( $data ) ) {
			$data = array();
		}

		$data[] = $this->menu();

		return $data;
	}

	/**
	 * Menu array
	 */
	function menu() {

	}

	/**
	 * Load function. Default load form for sections
	 */
	function load_function() {
		$this->form();
	}

	/**
	 * General print
	 *
	 * @param integer $id Id parameter.
	 * @param string $cat Category parameter.
	 */
	function _print_general( $id = 0, $cat = '' ) {
		$title  = $this->menu();
		$slug   = $title['slug'];
		$title  = isset( $title['page_title'] ) ? $title['page_title'] : $title['title'];
		$data   = array(
			'_header' => $title,
		);
		$method = $cat . '_data';
		if ( ! method_exists( $this, $method ) ) {
			$method = 'constructor_data';
		}

		$data = apply_filters( "tinvwl_{$cat}_data", $data );
		if ( method_exists( $this, $method ) ) {
			$sections = apply_filters( 'tinvwl_prepare_admsections_' . $method, $this->$method() );
			$sections = apply_filters( 'tinvwl_prepare_admsections', $sections );
			$view     = new TInvWL_ViewSection( $this->_name, $this->_version );
			$view->load_data( $sections );
			$method = $cat . '_save';
			if ( ! method_exists( $this, $method ) ) {
				$method = 'constructor_save';
			}
			if ( method_exists( $this, $method ) ) {
				$this->$method( apply_filters( 'tinvwl_prepare_admsections_' . $method, $view->post_form() ) );
			}
			$method = $cat . '_load';
			if ( ! method_exists( $this, $method ) ) {
				$method = 'constructor_load';
			}
			if ( method_exists( $this, $method ) ) {
				$view->load_value( apply_filters( 'tinvwl_prepare_admsections_' . $method, $this->$method( $sections ) ) );
			}
			TInvWL_View::render( $view, $view->form_data( $data ) );
		} else {
			TInvWL_View::render( $slug, $data );
		}
	}

	/**
	 * Method for default settings array
	 *
	 * @param array $sections Sections array.
	 *
	 * @return array
	 */
	function get_defaults( $sections ) {
		$defaults = array();
		if ( ! is_array( $sections ) ) {
			return $defaults;
		}
		$sections = apply_filters( 'tinvwl_prepare_admsections', $sections );
		foreach ( $sections as $section ) {
			if ( array_key_exists( 'noform', $section ) && $section['noform'] ) {
				continue;
			}

			if ( array_key_exists( 'fields', $section ) ) {
				$fields = $section['fields'];
			} else {
				continue;
			}
			$id = array_key_exists( 'id', $section ) ? $section['id'] : '';
			if ( ! array_key_exists( $id, $defaults ) ) {
				$defaults[ $id ] = array();
			}
			foreach ( $fields as $field ) {
				$name = array_key_exists( 'name', $field ) ? $field['name'] : '';
				$std  = array_key_exists( 'std', $field ) ? $field['std'] : '';

				$defaults[ $id ][ $name ] = $std;
			}
			if ( array_key_exists( '', $defaults[ $id ] ) ) {
				unset( $defaults[ $id ][''] );
			}
		}

		return $defaults;
	}

	/**
	 * Form for section
	 */
	function form() {
		add_filter( 'tinvwl_section_before', array( $this, 'start_form' ) );
		add_filter( 'tinvwl_section_after', array( $this, 'end_form' ) );
	}

	/**
	 * Form start for section
	 *
	 * @param string $content Sections content.
	 *
	 * @return string
	 */
	function start_form( $content ) {
		$content .= '<form method="POST" autocomplete="off">';

		return $content;
	}

	/**
	 * Form end for section
	 *
	 * @param string $content Sections content.
	 *
	 * @return string
	 */
	function end_form( $content ) {
		$content .= '</form>';

		return $content;
	}

	/**
	 * Load value from database
	 *
	 * @param array $sections Sections array.
	 *
	 * @return array
	 */
	function constructor_load( $sections ) {
		$sections = $this->get_defaults( $sections );
		$sections = array_keys( $sections );
		$data     = array();
		foreach ( $sections as $section ) {
			$data[ $section ] = tinv_get_option( $section );
		}

		return $data;
	}

	/**
	 * Save value to database
	 *
	 * @param array $data Post section data.
	 */
	function constructor_save( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}
		foreach ( $data as $key => $value ) {
			tinv_update_option( $key, '', $value );
		}
	}
}
