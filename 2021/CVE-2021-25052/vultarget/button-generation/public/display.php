<?php
/**
 * Conditions for display shortcode on the frontend
 *
 * @package     Wow_Plugin
 * @subpackage  Public/Display
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$table  = $wpdb->prefix . "wow_" . $this->plugin['prefix'];
$result = $wpdb->get_results( "SELECT * FROM " . $table . " order by id asc" );

if ( count( $result ) > 0 ) {
	foreach ( $result as $key => $val ) {
		$param         = unserialize( $val->param );
		$param['show'] = ! empty( $param['show'] ) ? $param['show'] : 'all';
		if ( $param['show'] == 'all' ) {
			echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
		} elseif ( $param['show'] == 'onlypost' ) {
			if ( is_single() ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
		} elseif ( $param['show'] == 'onlypage' ) {
			if ( is_page() ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
		} elseif ( $param['show'] == 'posts' ) {
			if ( is_single( preg_split( "/[,]+/", $param['id_post'] ) ) ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
		} elseif ( $param['show'] == 'postsincat' ) {
			if ( in_category( preg_split( "/[,]+/", $param['id_post'] ) ) ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
		} elseif ( $param['show'] == 'pages' ) {
			if ( is_page( preg_split( "/[,]+/", $param['id_post'] ) ) ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
		} elseif ( $param['show'] == 'expost' ) {
			if ( ! is_single( preg_split( "/[,]+/", $param['id_post'] ) ) ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
		} elseif ( $param['show'] == 'expage' ) {
			if ( ! is_page( preg_split( "/[,]+/", $param['id_post'] ) ) ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
		} elseif ( $param['show'] == 'taxonomy' ) {
			$taxonomy = $param['taxonomy'];
			$term     = $param['id_post'];
			$is_in    = is_tax( $taxonomy, array( $term ) );
			if ( $is_in ) {
				echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
			}
			if ( is_single() ) {
				if ( has_term( array( $term ), $taxonomy ) ) {
					echo do_shortcode( '[' . $this->plugin['shortcode'] . ' id=' . $val->id . ']' );
				}
			}
		}
	}
}
	
	
