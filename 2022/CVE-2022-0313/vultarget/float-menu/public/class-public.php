<?php
/**
 * Public Class
 *
 * @package     Wow_Plugin
 * @subpackage  Public
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

namespace float_menu_free;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wow_Plugin_Public {

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


		add_shortcode( $this->plugin['shortcode'], array( $this, 'shortcode' ) );
		// Display on the site
		add_action( 'wp_footer', array( $this, 'display' ) );
	}

	static function sub_menu_array( $var ) {
		return ( ! empty( $var ) );
	}

	/**
	 * Display a shortcode
	 *
	 * @param $atts
	 *
	 * @return false|string
	 */
	public function shortcode( $atts ) {
		extract( shortcode_atts( array( 'id' => "" ), $atts ) );
		$id = absint( $atts['id'] );

		global $wpdb;
		$table  = $wpdb->prefix . 'wow_' . $this->plugin['prefix'];
		$sSQL   = $wpdb->prepare( "select * from $table WHERE id = %d", $id );
		$result = $wpdb->get_results( $sSQL, 'OBJECT_K' );

		if ( empty( $result ) ) {
			return false;
		}

		$param = unserialize( $result[ $id ]->param );
		$check = $this->check( $param, $id );

		if ( $check === false ) {
			return false;
		}
		ob_start();
		include( 'partials/public.php' );
		$menu = ob_get_contents();
		ob_end_clean();

		$this->include_style_script( $param, $id );

		return $menu;

	}

	private function include_style_script( $param, $id ) {
		$slug    = $this->plugin['slug'];
		$version = $this->plugin['version'];

		if ( empty( $param['disable_fontawesome'] ) ) {
			$url_icons = $this->plugin['url'] . 'vendors/fontawesome/css/fontawesome-all.min.css';
			wp_enqueue_style( $slug . '-fontawesome', $url_icons, null, '5.11.2' );
		}

		$url_style = plugin_dir_url(__FILE__) . 'assets/css/style.min.css';
		wp_enqueue_style( $slug, $url_style, null, $version );

		$inline_style = self::style( $param, $id );
		wp_add_inline_style( $slug, $inline_style );

		$url_velocity = plugin_dir_url(__FILE__) . 'assets/js/velocity.min.js';
		wp_enqueue_script( 'velocity', $url_velocity, array( 'jquery' ), $version );

		$url_script = plugin_dir_url(__FILE__) . 'assets/js/floatMenu.min.js';
		wp_enqueue_script( $slug, $url_script, array( 'jquery' ), $version );

		$inline_script = self::script( $param, $id );
		wp_add_inline_script( $slug, $inline_script );
	}


	/**
	 * Display the Item on the specific pages, not via the Shortcode
	 */
	public function display() {
		require plugin_dir_path( __FILE__ ) . 'display.php';
	}

	/**
	 * Create Inline style for elements
	 */
	public function style( $param, $id ) {
		$css = '';
		require 'generator-style.php';

		return $css;

	}

	/**
	 * Create Inline script for elements
	 */
	public function script( $param, $id ) {
		$js = '';
		require 'generator-script.php';

		return $js;

	}

	private function check_status( $param ) {
		$status = isset( $param['menu_status'] ) ? $param['menu_status'] : 1;
		if ( empty( $status ) ) {
			return false;
		}

		return true;
	}

	private function check_test_mode( $param ) {
		if ( ! empty( $param['test_mode'] ) && ! current_user_can( 'administrator' ) ) {
			return false;
		}

		return true;
	}

	private function check( $param, $id ) {
		$check     = true;
		$check_arr = array(
			'status'    => $this->check_status( $param ),
			'test_mode' => $this->check_test_mode( $param ),
		);

		foreach ( $check_arr as $value ) {
			if ( $value === false ) {
				$check = false;
				break;
			}
		}

		return $check;
	}

}