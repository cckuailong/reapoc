<?php

/**
 * Admin Class
 *
 * @package     Wow_Plugin
 * @subpackage  Admin
 * @author      Wow-Company <helper@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

namespace float_menu_free;


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
		add_filter( 'admin_footer_text', array( $this, 'rate_us' ) );
		add_action( 'wp_ajax_' . $this->plugin['prefix'] . '_item_save', array( $this, 'item_save' ) );
		add_action( 'wp_ajax_float_menu_message', array( $this, 'wow_message_callback' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_init', [ $this, 'export_data' ] );
		add_action( 'admin_init', [ $this, 'import_data' ] );

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
		echo "<span class='wow-help dashicons dashicons-editor-help' title='" . wp_kses_post( $tooltip ) . "'></span>";
	}

	/**
	 * @param array $arg parameters for creating field in the backend
	 *
	 * @return string field for displaying
	 */
	static function option( $arg ) {
		$id          = isset( $arg['id'] ) ? $arg['id'] : null;
		$name        = isset( $arg['name'] ) ? $arg['name'] : '';
		$type        = isset( $arg['type'] ) ? $arg['type'] : '';
		$func        = ! empty( $arg['func'] ) ? ' onchange="' . $arg['func'] . '"' : '';
		$options     = isset( $arg['option'] ) ? $arg['option'] : '';
		$val         = $arg['val'];
		$separator   = isset( $arg['sep'] ) ? $arg['sep'] : '';
		$extra_class = '';
		if ( $type == 'text' || $type == 'number' || $type == 'time' ) {
			$extra_class = 'input ';
		}
		$class = isset( $arg['class'] ) ? ' class="' . $extra_class . $arg['class'] . '"' : ' class="' . $extra_class . '"';
		$field = '';

		switch ( $type ) {
			case 'radio':
				include( 'fields/radio.php' );
				break;
			case 'checkbox':
				include( 'fields/checkbox.php' );
				break;
			case 'text':
			case 'number':
			case 'hidden':
			case 'time':
				include( 'fields/input.php' );
				break;
			case 'select':
				include( 'fields/select.php' );
				break;
			case 'color':
				include( 'fields/color.php' );
				break;
			case 'editor':
				include( 'fields/editor.php' );
				break;
		}
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
		echo '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $classes ) . '" title="' . wp_kses_post( $tooltip ) . '"></a>';
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
		require_once 'page-main.php';
	}


	/**
	 * Include Styles and Scripts on the plugin admin page
	 *
	 * @since 1.0
	 */
	public function admin_scripts( $hook ) {
		$page = 'wow-plugins_page_' . $this->plugin['slug'];

		if ( $page != $hook ) {
			return false;
		}
		$slug       = $this->plugin['slug'];
		$version    = $this->plugin['version'];
		$url_assets = plugin_dir_url( __FILE__ ) . 'assets/';

		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// include the main style
		wp_enqueue_style( $slug . '-admin', $url_assets . 'css/style' . $pre_suffix . '.css', false, $version );

		// include fontAwesome icon
		$url_fontawesome = $this->plugin['url'] . 'vendors/fontawesome/css/fontawesome-all.min.css';
		wp_enqueue_style( $slug . '-fontawesome', $url_fontawesome, null, '5.15.3' );

		// include fonticonpicker styles & scripts
		$fonticonpicker_js = $url_assets . 'fonticonpicker/fonticonpicker.min.js';
		wp_enqueue_script( $slug . '-fonticonpicker', $fonticonpicker_js, array( 'jquery' ) );

		$fonticonpicker_css = $url_assets . 'fonticonpicker/css/fonticonpicker.min.css';
		wp_enqueue_style( $slug . '-fonticonpicker', $fonticonpicker_css );

		$fonticonpicker_dark_css = $url_assets . 'fonticonpicker/fonticonpicker.darkgrey.min.css';
		wp_enqueue_style( $slug . '-fonticonpicker-darkgrey', $fonticonpicker_dark_css );

		// include the color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		$url_alpha = $url_assets . 'js/wp-color-picker-alpha.min.js';
		wp_enqueue_script( 'wp-color-picker-alpha', $url_alpha, array( 'wp-color-picker' ) );

		// include tooltip script
		wp_enqueue_script( 'jquery-ui-tooltip' );

		// include sortable
		wp_enqueue_script( 'jquery-ui-sortable' );


		// include the plugin admin script
		$url_script = $url_assets . 'js/script' . $pre_suffix . '.js';
		wp_enqueue_script( $slug . '-admin', $url_script, array( 'jquery' ), $version, true );

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
		$settings_link
			= '<a href="admin.php?page=' . esc_attr( $this->plugin['slug'] ) . '">' . esc_attr__( 'Settings',
				$this->plugin['text'] ) . '</a>';
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
			$rate_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">' . esc_attr( $this->plugin['name'] )
			                          . '</a>! Please <a href="%2$s" target="_blank">rate us on '
			                          . esc_attr( $this->rating['website'] ) . '</a>', $this->plugin['text'] ), $this->url['home'],
				$this->rating['url'] );

			return str_replace( '</span>', '', $footer_text ) . ' | ' . $rate_text . '</span>';
		} else {
			return $footer_text;
		}
	}

	function item_save() {

		$response = 'No';
		if ( isset( $_POST[ $this->plugin['slug'] . '_nonce' ] ) ) {
			if ( ! empty( $_POST )
			     && wp_verify_nonce( $_POST[ $this->plugin['slug'] . '_nonce' ], $this->plugin['slug'] . '_action' )
			     && current_user_can( 'manage_options' )
			) {
				$response = self:: save_data();
			}
		}

		wp_send_json( $response );

		wp_die();

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
		$add                   = ( isset( $_REQUEST['add'] ) ) ? absint( $_REQUEST['add'] ) : '';
		$table                 = $wpdb->prefix . 'wow_' . $this->plugin['prefix'];
		$id                    = absint( $_POST['tool_id'] );
		$title                 = sanitize_text_field( $_POST['title'] );
		$param                 = map_deep( $_POST['param'], array( $this, 'sanitize_param' ) );
		$param['popupcontent'] = wp_kses_post( $_POST['param']['popupcontent'] );
		$param                 = serialize( $param );

		$response = array(
			'status'  => 'NO',
			'message' => esc_attr__( 'Something went wrong. Contact the plugin developer', $this->plugin['text'] ),
		);
		if ( 1 === $add ) {

			$insert = $wpdb->query(
				$wpdb->prepare( " INSERT INTO {$table} ( id, title, param ) VALUES ( %d, %s, %s )",
					$id,
					$title,
					$param
				) );
			if ( $insert ) {
				$response = array(
					'status'  => 'OK',
					'message' => esc_attr__( 'Item Added', 'side-menu' ),
				);
			}

		} elseif ( 2 === $add ) {
			$update =
				$wpdb->query(
					$wpdb->prepare( " UPDATE  {$table} SET title = %s, param = %s  WHERE id= %d;",
						$title,
						$param,
						$id
					) );

			if ( ! empty( $update ) ) {
				$response = array(
					'status'  => 'OK',
					'message' => esc_attr__( 'Item Updated', 'side-menu' ),
				);
			}
		}

		return $response;
	}

	public function sanitize_param( $value ) {
		return wp_unslash( sanitize_text_field( $value ) );
	}

	public function import_data() {

		if ( empty( $_POST[ $this->plugin['slug'] . '_import_nonce' ] ) ) {
			return;
		}


		if ( ! wp_verify_nonce( $_POST[ $this->plugin['slug'] . '_import_nonce' ], $this->plugin['slug'] . '_import_nonce' ) ) {
			return;
		}


		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->get_file_extension( $_FILES['import_file']['name'] ) != 'json' ) {
			wp_die( esc_attr__( 'Please upload a valid .json file', $this->plugin['text'] ), __( 'Error', $this->plugin['text'] ), array( 'response' => 400 ) );
		}

		$import_file = $_FILES['import_file']['tmp_name'];

		if ( empty( $import_file ) ) {
			wp_die( esc_attr__( 'Please upload a file to import', $this->plugin['text'] ), __( 'Error', $this->plugin['text'] ), array( 'response' => 400 ) );
		}

		// Retrieve the settings from the file and convert the json object to an array
		$settings = json_decode( file_get_contents( $import_file ) );

		$update = ! empty( $_POST['wow_import_update'] ) ? '1' : '';

		global $wpdb;
		$table = $wpdb->prefix . "wow_" . $this->plugin['prefix'];

		foreach ( $settings as $key => $val ) {
			$id    = $val->id;
			$title = $val->title;
			$param = $val->param;

			$check_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ) );

			if ( ! empty( $update ) && ! empty( $check_row ) ) {
				$wpdb->query(
					$wpdb->prepare( " UPDATE  {$table} SET title = %s, param = %s  WHERE id= %d;",
						$title,
						$param,
						$id
					) );

			} elseif ( ! empty( $check_row ) ) {
				$wpdb->query(
					$wpdb->prepare( " INSERT INTO {$table} ( title, param ) VALUES (  %s, %s )",
						$title,
						$param
					) );
			} else {
				$wpdb->query(
					$wpdb->prepare( " INSERT INTO {$table} ( id, title, param ) VALUES ( %d, %s, %s )",
						$id,
						$title,
						$param
					) );
			}

		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . esc_attr( $this->plugin['slug'] ) ) );
		exit;


	}

	public function export_data() {

		if ( empty( $_POST[ $this->plugin['slug'] . '_export_nonce' ] ) ) {
			return;
		}


		if ( ! wp_verify_nonce( $_POST[ $this->plugin['slug'] . '_export_nonce' ], $this->plugin['slug'] . '_export_nonce' ) ) {
			return;
		}


		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$file_name = $this->plugin['shortcode'] . '-database-' . date( 'm-d-Y' ) . '.json';

		global $wpdb;
		$table  = $wpdb->prefix . "wow_" . $this->plugin['prefix'];
		$result = $wpdb->get_results( "SELECT * FROM " . $table . " order by id asc" );

		$settings = array();
		if ( count( $result ) > 0 ) {
			foreach ( $result as $key => $val ) {
				$settings[] = array(
					'id'    => $val->id,
					'title' => $val->title,
					'param' => $val->param,
				);
			}
		}
		ignore_user_abort( true );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( "Expires: 0" );

		echo json_encode( $settings );
		exit;


	}


	function get_file_extension( $str ) {
		$parts = explode( '.', $str );

		return end( $parts );
	}


}
