<?php
/**
 * Public Class
 *
 * @package     Wow_Plugin
 * @subpackage  Public
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
 * Class Wow_Plugin_Public
 *
 * @package wow_plugin
 *
 * @property array plugin   - base information about the plugin
 * @property array url      - home, pro and other URL for plugin
 * @property array rating   - website and link for rating
 * @property string basedir - filesystem directory path for the plugin
 * @property string baseurl - URL directory path for the plugin
 */
class Wow_Plugin_Public {

	/**
	 * Setup to frontend of the plugin
	 *
	 * @param array $info general information about the plugin
	 *
	 * @since 1.0
	 */

	public function __construct( $info ) {

		$this->plugin = $info['plugin'];
		$this->url    = $info['url'];
		$this->rating = $info['rating'];

		$upload        = wp_upload_dir();
		$this->basedir = $upload['basedir'] . '/' . $this->plugin['slug'] . '/';
		$this->baseurl = $upload['baseurl'] . '/' . $this->plugin['slug'] . '/';

		// Add plugin style in header
		//add_action( 'wp_enqueue_scripts', array( $this, 'plugin_scripts' ) );

		add_shortcode( $this->plugin['shortcode'], array( $this, 'shortcode' ) );

		// Display on the site
		add_action( 'wp_footer', array( $this, 'display' ) );

		// Counter
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'wp_ajax_btg_count', array( $this, 'btg_count' ) );
			add_action( 'wp_ajax_nopriv_btg_count', array( $this, 'btg_count' ) );
		}

		// The button view counter
		add_action( 'btg_counter', array( $this, 'view_counter' ) );

	}

	public function plugin_scripts() {
		$url_style = $this->plugin['url'] . 'assets/css/style.min.css';
		wp_enqueue_style( $this->plugin['slug'], $url_style, array(), $this->plugin['version'] );
	}

	/**
	 * Display a shortcode
	 *
	 * @param $atts
	 *
	 * @return false|string
	 */
	public function shortcode( $atts ) {
		ob_start();
		require plugin_dir_path( __FILE__ ) . 'shortcode.php';
		$shortcode = ob_get_contents();
		ob_end_clean();

		return $shortcode;
	}

	/**
	 * Display the Item on the specific pages, not via the Shortcode
	 */
	public function display() {
		require plugin_dir_path( __FILE__ ) . 'display.php';
	}

	public function inline_style( $param, $id ) {
		$css = '';
		require 'generator/style.php';
		return $css;
	}

	/**
	 * Counting the number of views of the item
	 *
	 * @param integer $id - ID of the Item
	 */
	public function view_counter( $id ) {
		$prefix       = $this->plugin['prefix'];
		$should_count = true;
		$useragent    = $_SERVER['HTTP_USER_AGENT'];
		$notbot       = "Mozilla|Opera";
		$bot          = "Bot/|robot|Slurp/|yahoo";
		if ( ! preg_match( "/$notbot/i", $useragent ) || preg_match( "!$bot!i", $useragent ) ) {
			$should_count = false;
		}
		if ( $should_count == true ) {
			$option_name = '_' . $prefix . '_view_counter_' . $id;
			$tool_view   = get_option( $option_name, '0' );
			update_option( $option_name, ( $tool_view + 1 ) );
		}
	}

	/**
	 * Reset counter and counting the action (click by button)
	 */
	public function btg_count() {
		$prefix = $this->plugin['prefix'];
		$type   = sanitize_text_field( $_POST['count_type'] );
		$id     = absint( $_POST['tool_id'] );
		if ( $type == 'reset' ) {
			$option_name_view   = '_' . $prefix . '_view_counter_' . $id;
			$option_name_action = '_' . $prefix . '_action_counter_' . $id;
			$delete_view        = delete_option( $option_name_view );
			$delete_action      = delete_option( $option_name_action );
			if ( $delete_view == true ) {
				$response = array(
					"result" => 'OK',
				);
				wp_send_json( $response );
			}
			exit();
		}

		$option_name = '_' . $prefix . '_' . $type . '_counter_' . $id;
		$tool_view   = get_option( $option_name, '0' );
		$updated     = update_option( $option_name, ( $tool_view + 1 ) );
		if ( true == $updated ) {
			$response = 'OK';
			wp_send_json( $response );
		}
		exit();
	}
}
