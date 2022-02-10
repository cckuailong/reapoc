<?php
namespace A3Rev\PageViewsCount;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Shortcode
{

	public function __construct() {
		add_shortcode( 'pvc_stats', array( $this, 'parse_shortcode') );

		add_filter( 'pvc_stats_shortcode', array( $this, 'pvc_stats_shortcode' ), 10, 4 );
	}

	public function parse_shortcode( $attr = array() ) {
		if ( is_admin() ) {
			return '';
		}

		$attr = shortcode_atts(
			array(
				'postid'           => '',
				'increase'         => 1,
				'show_views_today' => 1,
			), $attr );

		$postid           = esc_attr( $attr['postid'] ); // XSS ok
		$increase         = intval( $attr['increase'] );
		$show_views_today = intval( $attr['show_views_today'] );

		$output = apply_filters( 'pvc_stats_shortcode', '', $postid, $increase, $show_views_today );

		return $output;
	}

	public function pvc_stats_shortcode( $output = '', $postid = '', $increase = 1, $show_views_today = 1 ) {
		if ( empty( $postid ) ) {
			global $post;
			if ( $post ) {
				$postid = $post->ID;
			}
		}

		if ( empty( $postid ) ) {
			return '';
		}

		$attributes = array( 'views_type' => ( 0 == $show_views_today ? 'total_only' : 'all' ) );

		if ( 1 == $increase ) {
			$output = A3_PVC::custom_stats_update_echo( $postid, 0, $attributes );
		} else {
			$output = A3_PVC::custom_stats_echo( $postid, 0, $attributes );
		}

		return $output;
	}
}
