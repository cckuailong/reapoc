<?php

/**
 * Admin Class
 *
 * @package     Wow_Plugin
 * @subpackage  Admin
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

namespace button_generator;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wow_Plugin_Admin
 *
 * @package wow_plugin
 *
 * @property array plugin - base information about the plugin
 * @property array url    - home, pro and other URL for plugin
 * @property array rating - website and link for rating
 *
 */
class Wow_Plugin_Admin {

	/**
	 * Setup to admin panel of the plugin
	 *
	 * @param array $info general information about the plugin
	 *
	 * @since 1.0
	 */
	public function __construct( $info ) {
		$this->plugin = $info['plugin'];
		$this->url    = $info['url'];
		$this->rating = $info['rating'];

		add_filter( 'plugin_action_links', array( $this, 'action_links' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'admin_footer_text', array( $this, 'rate_us' ) );
		add_action( 'wp_ajax_button_generation_message', array( $this, 'wow_message_callback' ) );
		add_action( 'wp_ajax_wow_item_save', array( $this, 'item_save' ) );
	}

	public function wow_message_callback() {
		update_option( 'wow_' . $this->plugin['prefix'] . '_message', 'read' );
		wp_die();
	}

	/**
	 * @param string|array $arg text which need show in the tooltip
	 *
	 * @return string tooltip for the element
	 */
	static function tooltip( $arg ) {
		$tooltip = '';
		foreach ( $arg as $key => $value ) {
			if ( $key == 'title' ) {
				$tooltip .= $value . '<p/>';
			} elseif ( $key == 'ul' ) {
				$tooltip .= '<ul>';
				$arr     = $value;
				foreach ( $arr as $val ) {
					$tooltip .= '<li>' . $val . '</li>';
				}
				$tooltip .= '</ul>';
			} else {
				$tooltip .= $value;
			}
		}
		$tooltip = "<span class='wow-help dashicons dashicons-editor-help' title='" . $tooltip . "'></span>";

		return $tooltip;
	}

	/**
	 * @param array $arg parameters for creating field in the backend
	 *
	 * @return string field for displaying
	 */
	static function option( $arg ) {
		$id        = isset( $arg['id'] ) ? $arg['id'] : null;
		$name      = isset( $arg['name'] ) ? $arg['name'] : '';
		$type      = isset( $arg['type'] ) ? $arg['type'] : '';
		$func      = ! empty( $arg['func'] ) ? ' onchange="' . $arg['func'] . '"' : '';
		$options   = isset( $arg['option'] ) ? $arg['option'] : '';
		$val       = $arg['val'];
		$separator = isset( $arg['sep'] ) ? $arg['sep'] : '';
		$class     = isset( $arg['class'] ) ? ' class="' . $arg['class'] . '"' : '';
		$field     = '';

		if ( $type == 'radio' ) {
			// create radio fields
			$option = '';
			foreach ( $options as $key => $value ) {
				$select = ( $key == $val ) ? 'checked="checked"' : '';
				$option .= '<input name="' . $name . '" type="radio" value="' . $key . '" id="' . $id . '_' . $key . '" ' .
				           $select . $func . $class . '><label for="' . $id . '_' . $key . '"> ' . $value . '</label>' .
				           $separator;
			}
			$field = $option;
		} elseif ( $type == 'checkbox' ) {
			// create checkbox field
			$select = ! empty( $val ) ? 'checked="checked"' : '';
			$field  = '<input type="checkbox" ' . $select . $func . $class . ' id="' . $id . '" >' . $separator;
			$field  .= '<input type="hidden" name="' . $name . '" value="">';
		} elseif ( $type == 'text' || $type == 'number' || $type == 'hidden' ) {
			// create text field
			$option = '';
			if ( is_array( $options ) ) {
				foreach ( $options as $key => $value ) {
					$option .= ' ' . $key . '="' . $value . '"';
				}
			}
			$field =
				'<input name="' . $name . '" type="' . $type . '" value="' . $val . '" id="' . $id . '"' . $func . $option .
				$class . '>' . $separator;
		} elseif ( $type == 'color' ) {
			// create color field
			$field = '<input name="' . $name . '" type="text" value="' . $val .
			         '" class="wp-color-picker-field" data-alpha="true" id="' . $id . '">' . $separator;
		} // create select field
		elseif ( $type == 'select' ) {
			$disabled = isset( $arg['disabled'] ) ? ' disabled' : '';
			$readonly = isset( $arg['readonly'] ) ? ' readonly' : '';
			$option   = '';
			foreach ( $options as $key => $value ) {
				if ( strrpos( $key, '_start' ) != false ) {
					$option .= '<optgroup label="' . $value . '">';
				} elseif ( strrpos( $key, '_end' ) != false ) {
					$option .= '</optgroup>';
				} else {
					$select = ( $key == $val ) ? 'selected="selected"' : '';
					$option .= '<option value="' . $key . '" ' . $select . '>' . $value . '</option>';
				}
			}
			$field = '<select name="' . $name . '"' . $func . $class . $disabled . $readonly . ' id="' . $id . '">';
			$field .= $option;
			$field .= '</select>' . $separator;
		} elseif ( $type == 'editor' ) {
			// create editor field
			$settings = array(
				'wpautop'       => 0,
				'textarea_name' => '' . $name . '',
				'textarea_rows' => 15,
			);
			$field    = wp_editor( $val, $id, $settings );
		} elseif ( $type == 'textarea' ) {
			// create textarea field
			$field = '<textarea name="' . $name . '" id="' . $id . '">' . $val . '</textarea>' . $separator;
		}

		return $field;
	}

	/**
	 * @param string $tooltip tooltip for element
	 *
	 * @return string
	 */
	public function pro( $tooltip = null ) {
		$link    = admin_url() . 'admin.php?page=' . $this->plugin['slug'] . '&tab=extension';
		$title   = esc_attr__( 'More features in the PRO version', $this->plugin['text'] );
		$classes = 'wow-help dashicons dashicons-lock';
		$tooltip = ! empty( $tooltip ) ? $title . '<br/>' . $tooltip : $title;
		$pro     = '<a href="' . $link . '" class="' . $classes . '" title="' . $tooltip . '"></a>';

		return $pro;
	}

	/**
	 * Add the plugin page in admin menu
	 *
	 * @since 1.0
	 */
	public function add_admin_page() {
		$parent     = 'wow-company';
		$title      = $this->plugin['name'] . ' version ' . $this->plugin['version'];
		$menu_title = $this->plugin['menu'];
		$capability = 'manage_options';
		$slug       = $this->plugin['slug'];
		$function   = array( $this, 'plugin_page' );
		add_submenu_page( $parent, $title, $menu_title, $capability, $slug, $function );
	}

	/**
	 * Include main plugin page with Style and Script
	 *
	 * @since 1.0
	 */
	public function plugin_page() {
		global $wow_plugin_page;
		$wow_plugin_page = $this->plugin['slug'];
		require_once 'partials/main.php';
		// self:: admin_style_script();
	}

	/**
	 * Include Styles and Scripts on the plugin admin page
	 *
	 * @since 1.0
	 */
	public function admin_scripts( $hook ) {

		$page = 'wow-plugins_page_' . $this->plugin['slug'];

		if ( $page != $hook ) {
			return;
		}

		$slug       = $this->plugin['slug'];
		$version    = $this->plugin['version'];
		$url_assets = $this->plugin['url'] . 'assets/';


		// include the main style
		wp_enqueue_style( $slug . '-admin', $url_assets . 'css/admin-style.css', false, $version );

		// include fontAwesome icon
		$url_fontawesome = $url_assets . 'vendors/fontawesome/css/fontawesome-all.min.css';
		wp_enqueue_style( $slug . '-fontawesome', $url_fontawesome, null, '5.6.3' );

		// include fonticonpicker styles & scripts
		$fonticonpicker_js = $url_assets . 'vendors/fonticonpicker/fonticonpicker.min.js';
		wp_enqueue_script( $slug . '-fonticonpicker', $fonticonpicker_js, array( 'jquery' ) );

		$fonticonpicker_css = $url_assets . 'vendors/fonticonpicker/css/fonticonpicker.min.css';
		wp_enqueue_style( $slug . '-fonticonpicker', $fonticonpicker_css );

		$fonticonpicker_dark_css = $url_assets . 'vendors/fonticonpicker/fonticonpicker.darkgrey.min.css';
		wp_enqueue_style( $slug . '-fonticonpicker-darkgrey', $fonticonpicker_dark_css );

		// include the color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// include tooltip script
		wp_enqueue_script( 'jquery-ui-tooltip' );

		// include an alpha rgba color picker
		$url_alpha = $url_assets . 'js/wp-color-picker-alpha.min.js';
		wp_enqueue_script( 'wp-color-picker-alpha', $url_alpha, array( 'wp-color-picker' ) );

		// include the plugin admin script
		$url_script = $url_assets . 'js/admin-script.js';
		wp_enqueue_script( $slug . '-admin', $url_script, array( 'jquery', 'jquery-ui-tooltip', 'wp-color-picker' ), $version, true );
		wp_localize_script( $slug . '-admin', 'btg_count', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		// include the plugin preview script
		$url_preview = $url_assets . 'js/preview.min.js';
		wp_enqueue_script( $slug . '-preview', $url_preview, array( 'jquery' ), $version, true );

	}

	/**
	 * Add the link to the plugin page on Plugins page
	 *
	 * @param $actions
	 * @param $plugin_file - the plugin main file
	 *
	 * @return mixed
	 */
	public function action_links( $actions, $plugin_file ) {
		if ( false === strpos( $plugin_file, plugin_basename( $this->plugin['file'] ) ) ) {
			return $actions;
		}
		$settings_link =
			'<a href="admin.php?page=' . $this->plugin['slug'] . '">' . esc_attr__( 'Settings', $this->plugin['text'] ) . '</a>';
		array_unshift( $actions, $settings_link );

		return $actions;
	}

	/**
	 * Add custom text in the footer on the wow plugin page
	 *
	 * @param $footer_text - text in the footer
	 *
	 * @return string - end text in the footer
	 * @since 1.0
	 */
	public function rate_us( $footer_text ) {
		global $wow_plugin_page;
		if ( $wow_plugin_page == $this->plugin['slug'] ) {
			$rate_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">' . $this->plugin['name'] .
			                          '</a>! Please <a href="%2$s" target="_blank">rate us on ' . $this->rating['website'] .
			                          '</a>', $this->plugin['text'] ), $this->url['home'], $this->rating['url'] );

			return str_replace( '</span>', '', $footer_text ) . ' | ' . $rate_text . '</span>';
		} else {
			return $footer_text;
		}
	}


	/**
	 * Save and Update the Item into the plugin Database
	 *
	 * @return array response from DB
	 *
	 * @since 1.0
	 */
	public function save_data() {
		global $wpdb;

		$add   = ( isset( $_REQUEST['add'] ) ) ? absint( $_REQUEST['add'] ) : '';
		$table = ( isset( $_REQUEST['data'] ) ) ? sanitize_text_field( $_REQUEST['data'] ) : '';
		$param = array_map( 'wp_kses_post', wp_unslash( $_POST['param'] ) );
		$id    = absint( $_POST['tool_id'] );

		$data = array(
			'id'    => $id,
			'title' => wp_unslash( sanitize_text_field( $_POST['title'] ) ),
			'param' => serialize( $param ),
		);

		if ( $add === 1 ) {
			$wpdb->insert( $table, $data );
		} elseif ( $add === 2 ) {
			$wpdb->update( $table, $data, array( 'id' => $id ), $format = null, $where_format = null );
		}

		$response = array(
			'status'  => 'OK',
			'message' => esc_attr__( 'Item Updated', $this->plugin['text'] ),
		);

		return $response;
	}

	function item_save() {

		$response = 'No';
		if ( isset( $_POST[ $this->plugin['slug'] . '_nonce' ] ) ) {
			if ( ! empty( $_POST ) &&
			     wp_verify_nonce( $_POST[ $this->plugin['slug'] . '_nonce' ], $this->plugin['slug'] . '_action' ) &&
			     current_user_can( 'manage_options' ) ) {
				$response = self:: save_data();
			}
		}

		wp_send_json( $response );

		wp_die();

	}

}
