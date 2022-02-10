<?php
/**
 * Shortcode
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract( shortcode_atts( array( 'id' => "" ), $atts ) );
global $wpdb;
$table  = $wpdb->prefix . 'wow_' . $this->plugin['prefix'];
$sSQL   = $wpdb->prepare( "select * from $table WHERE id = %d", $id );
$result = $wpdb->get_results( $sSQL );

if ( count( $result ) > 0 ) {

	foreach ( $result as $key => $val ) {
		$param = unserialize( $val->param );
		$check = $this->check( $param, $id );
		if ( $check === false ) {
			return false;
		}

		if ( empty( $val->status ) ) {
			return false;
		}

		include( 'partials/public.php' );

		$slug       = $this->plugin['slug'];
		$version    = $this->plugin['version'];
		$url_asset  = plugin_dir_url( __FILE__ );
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$url_style = $url_asset . 'assets/css/style' . $pre_suffix . '.css';
		wp_enqueue_style( $slug, $url_style, null, $version );

		wp_add_inline_style( $slug, $val->style );

		$effects_url = $url_asset . 'assets/js/jquery.effects' . $pre_suffix . '.js';
		wp_enqueue_script( $slug . '-effects', $effects_url, array( 'jquery' ), $version );

		$modal_url = $url_asset . 'assets/js/jquery.modalWindow' . $pre_suffix . '.js';
		wp_enqueue_script( $slug, $modal_url, array( 'jquery' ), $version );

		$inline_js = 'jQuery(function() {jQuery("#wow-modal-overlay-' . $id . '").ModalWindow(' . $val->script . '); });';
		wp_add_inline_script( $slug, $inline_js );


	}

}
