<?php
if ( ! defined( 'myCRED_STATS_VERSION' ) ) exit;

/**
 * Shortcode: Circulation
 * @see http://codex.mycred.me/shortcodes/mycred_chart_circulation/
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_chart_circulation' ) ) :
	function mycred_render_chart_circulation( $atts, $no_data = '' ) {

		extract( shortcode_atts( array(
			'type'    => 'pie',
			'title'   => '',
			'animate' => 1,
			'bezier'  => 1,
			'labels'  => 1,
			'legend'  => 1,
			'height'  => '',
			'width'   => ''
		), $atts, MYCRED_SLUG . '_chart_circulation' ) );

		// Make sure we request a chart type that we support
		$type  = ( ! in_array( $type, array( 'pie', 'doughnut', 'line', 'bar', 'radar', 'polarArea' ) ) ) ? 'pie' : $type;

		// Get data
		$data  = mycred_get_circulation_data();
		if ( empty( $data ) ) return $no_data;

		// New Chart Object
		$chart = mycred_create_chart( array(
			'type'     => $type,
			'title'    => $title,
			'animate'  => (bool) $animate,
			'bezier'   => (bool) $bezier,
			'x_labels' => (bool) $labels,
			'legend'   => (bool) $legend,
			'height'   => $height,
			'width'    => $width
		) );

		return $chart->generate_canvas( $type, $data );

	}
endif;

/**
 * Shortcode: Gains vs. Losses
 * @see http://codex.mycred.me/shortcodes/mycred_chart_gain_loss/
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_chart_gain_vs_loss' ) ) :
	function mycred_render_chart_gain_vs_loss( $atts, $no_data = '' ) {

		extract( shortcode_atts( array(
			'type'    => 'pie',
			'ctype'   => MYCRED_DEFAULT_TYPE_KEY,
			'title'   => '',
			'animate' => 1,
			'bezier'  => 1,
			'labels'  => 1,
			'legend'  => 1,
			'height'  => '',
			'width'   => '',
			'gains'   => '',
			'losses'  => ''
		), $atts, MYCRED_SLUG . '_chart_gain_loss' ) );

		// Make sure we request a chart type that we support
		$type  = ( ! in_array( $type, array( 'pie', 'doughnut', 'line', 'bar', 'polarArea' ) ) ) ? 'pie' : $type;

		// Get data
		$data  = mycred_get_gains_vs_losses_data( $ctype );
		if ( empty( $data ) ) return $no_data;

		// If we want to customize labels
		if ( ! empty( $gains ) )
			$data[0]->label = $gains;

		if ( ! empty( $losses ) )
			$data[1]->label = $losses;

		// New Chart Object
		$chart = mycred_create_chart( array(
			'type'     => $type,
			'title'    => $title,
			'animate'  => (bool) $animate,
			'bezier'   => (bool) $bezier,
			'x_labels' => (bool) $labels,
			'legend'   => (bool) $legend,
			'height'   => $height,
			'width'    => $width
		) );

		return $chart->generate_canvas( $type, $data );

	}
endif;

/**
 * Shortcode: Point History
 * @see http://codex.mycred.me/shortcodes/mycred_chart_history/
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_chart_history' ) ) :
	function mycred_render_chart_history( $atts, $no_data = '' ) {

		extract( shortcode_atts( array(
			'type'    => 'line',
			'ctype'   => MYCRED_DEFAULT_TYPE_KEY,
			'period'  => 'days',
			'number'  => 10,
			'order'   => 'DESC',
			'title'   => '',
			'animate' => 1,
			'bezier'  => 1,
			'labels'  => 1,
			'legend'  => 1,
			'height'  => '',
			'width'   => ''
		), $atts, MYCRED_SLUG . '_chart_history' ) );

		// Make sure we request a chart type that we support
		$type  = ( ! in_array( $type, array( 'line', 'bar' ) ) ) ? 'line' : $type;

		// Get data
		$data  = mycred_get_history_data( $ctype, $period, $number, $order );
		if ( empty( $data ) ) return $no_data;

		// New Chart Object
		$chart = mycred_create_chart( array(
			'type'     => $type,
			'title'    => $title,
			'animate'  => (bool) $animate,
			'bezier'   => (bool) $bezier,
			'x_labels' => (bool) $labels,
			'legend'   => (bool) $legend,
			'height'   => $height,
			'width'    => $width
		) );

		return $chart->generate_canvas( $type, $data );

	}
endif;

/**
 * Shortcode: Top Balances
 * @see http://codex.mycred.me/shortcodes/mycred_chart_top_balances/
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_chart_top_balances' ) ) :
	function mycred_render_chart_top_balances( $atts, $no_data = '' ) {

		extract( shortcode_atts( array(
			'type'    => 'bar',
			'ctype'   => MYCRED_DEFAULT_TYPE_KEY,
			'number'  => 10,
			'order'   => 'DESC',
			'title'   => '',
			'animate' => 1,
			'bezier'  => 1,
			'labels'  => 1,
			'legend'  => 1,
			'height'  => '',
			'width'   => ''
		), $atts, MYCRED_SLUG . '_chart_top_balances' ) );

		// Make sure we request a chart type that we support
		$type  = ( ! in_array( $type, array( 'pie', 'doughnut', 'line', 'bar', 'radar', 'polarArea' ) ) ) ? 'bar' : $type;

		// Get data
		$data  = mycred_get_top_balances_data( $ctype, $number, $order );
		if ( empty( $data ) ) return $no_data;

		// New Chart Object
		$chart = mycred_create_chart( array(
			'type'     => $type,
			'title'    => $title,
			'animate'  => (bool) $animate,
			'bezier'   => (bool) $bezier,
			'x_labels' => (bool) $labels,
			'legend'   => (bool) $legend,
			'height'   => $height,
			'width'    => $width
		) );

		return $chart->generate_canvas( $type, $data );

	}
endif;

/**
 * Shortcode: Top Instances
 * @see http://codex.mycred.me/shortcodes/mycred_chart_top_instances/
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_chart_top_instances' ) ) :
	function mycred_render_chart_top_instances( $atts, $no_data = '' ) {

		extract( shortcode_atts( array(
			'type'    => 'bar',
			'ctype'   => MYCRED_DEFAULT_TYPE_KEY,
			'number'  => 10,
			'order'   => 'DESC',
			'title'   => '',
			'animate' => 1,
			'bezier'  => 1,
			'labels'  => 1,
			'legend'  => 1,
			'height'  => '',
			'width'   => ''
		), $atts, MYCRED_SLUG . '_chart_top_instances' ) );

		// Make sure we request a chart type that we support
		$type  = ( ! in_array( $type, array( 'pie', 'doughnut', 'line', 'bar', 'radar', 'polarArea' ) ) ) ? 'pie' : $type;

		// Get data
		$data  = mycred_get_top_instances_data( $ctype, $number, $order );
		if ( empty( $data ) ) return $no_data;

		// New Chart Object
		$chart = mycred_create_chart( array(
			'type'     => $type,
			'title'    => $title,
			'animate'  => (bool) $animate,
			'bezier'   => (bool) $bezier,
			'x_labels' => (bool) $labels,
			'legend'   => (bool) $legend,
			'height'   => $height,
			'width'    => $width
		) );

		return $chart->generate_canvas( $type, $data );

	}
endif;

/**
 * Shortcode: Balance History
 * @see http://codex.mycred.me/shortcodes/mycred_chart_balance_history/
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_chart_balance_history' ) ) :
	function mycred_render_chart_balance_history( $atts, $no_data = '' ) {

		extract( shortcode_atts( array(
			'type'    => 'line',
			'ctype'   => MYCRED_DEFAULT_TYPE_KEY,
			'user'    => 'current',
			'period'  => 'days',
			'number'  => 10,
			'order'   => 'DESC',
			'title'   => '',
			'animate' => 1,
			'bezier'  => 1,
			'labels'  => 1,
			'legend'  => 1,
			'height'  => '',
			'width'   => ''
		), $atts, MYCRED_SLUG . '_chart_balance_history' ) );

		if ( $user == 'current' && ! is_user_logged_in() ) return $no_data;

		$user_id = mycred_get_user_id( $user );

		// Make sure we request a chart type that we support
		$type  = ( ! in_array( $type, array( 'line', 'bar' ) ) ) ? 'line' : $type;

		// Get data
		$data  = mycred_get_users_history_data( $user_id, $ctype, $period, $number, $order );
		if ( empty( $data ) ) return $no_data;

		// New Chart Object
		$chart = mycred_create_chart( array(
			'type'     => $type,
			'title'    => $title,
			'animate'  => (bool) $animate,
			'bezier'   => (bool) $bezier,
			'x_labels' => (bool) $labels,
			'legend'   => (bool) $legend,
			'height'   => $height,
			'width'    => $width
		) );

		return $chart->generate_canvas( $type, $data );

	}
endif;

/**
 * Shortcode: Reference History
 * @see http://codex.mycred.me/shortcodes/mycred_chart_instance_history/
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_chart_instance_history' ) ) :
	function mycred_render_chart_instance_history( $atts, $no_data = '' ) {

		extract( shortcode_atts( array(
			'type'    => 'line',
			'ctype'   => MYCRED_DEFAULT_TYPE_KEY,
			'ref'     => '',
			'period'  => 'days',
			'number'  => 10,
			'order'   => 'DESC',
			'title'   => '',
			'animate' => 1,
			'bezier'  => 1,
			'labels'  => 1,
			'legend'  => 1,
			'height'  => '',
			'width'   => ''
		), $atts, MYCRED_SLUG . '_chart_instance_history' ) );

		if ( empty( $ref ) ) return $no_data;

		// Make sure we request a chart type that we support
		$type  = ( ! in_array( $type, array( 'line', 'bar', 'radar' ) ) ) ? 'line' : $type;

		// Get data
		$data  = mycred_get_ref_history_data( $ref, $ctype, $period, $number, $order );
		if ( empty( $data ) ) return $no_data;

		// New Chart Object
		$chart = mycred_create_chart( array(
			'type'     => $type,
			'title'    => $title,
			'animate'  => (bool) $animate,
			'bezier'   => (bool) $bezier,
			'x_labels' => (bool) $labels,
			'legend'   => (bool) $legend,
			'height'   => $height,
			'width'    => $width
		) );

		return $chart->generate_canvas( $type, $data );

	}
endif;
