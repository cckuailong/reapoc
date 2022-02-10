<?php
/**
 * Shortcodes.
 *
 * @category Calendar
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Primary My Calendar shortcode.
 *
 * @param array  $atts Shortcode attributes.
 * @param string $content Contained content.
 *
 * @return string Calendar.
 */
function my_calendar_insert( $atts, $content = null ) {
	$args = shortcode_atts(
		array(
			'name'     => 'all',
			'format'   => 'calendar',
			'category' => 'all',
			'time'     => 'month',
			'ltype'    => '',
			'lvalue'   => '',
			'author'   => 'all',
			'host'     => 'all',
			'id'       => '',
			'template' => '',
			'above'    => '',
			'below'    => '',
			'year'     => false,
			'month'    => false,
			'day'      => false,
			'site'     => false,
			'months'   => false,
			'search'   => '',
		),
		$atts,
		'my_calendar'
	);

	if ( 'mini' !== $args['format'] ) {
		if ( isset( $_GET['format'] ) ) {
			$args['format'] = $_GET['format'];
		}
	}

	if ( isset( $_GET['search'] ) ) {
		$args['search'] = $_GET['search'];
	}

	global $user_ID;
	if ( 'current' === $args['author'] ) {
		$args['author'] = apply_filters( 'mc_display_author', $user_ID, 'main' );
	}
	if ( 'current' === $args['host'] ) {
		$args['host'] = apply_filters( 'mc_display_host', $user_ID, 'main' );
	}

	return my_calendar( $args );
}

/**
 * Upcoming Events My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string Calendar.
 */
function my_calendar_insert_upcoming( $atts ) {
	$args = shortcode_atts(
		array(
			'before'     => 'default',
			'after'      => 'default',
			'type'       => 'default',
			'category'   => 'default',
			'template'   => 'default',
			'fallback'   => '',
			'order'      => 'asc',
			'skip'       => '0',
			'show_today' => 'yes',
			'author'     => 'default',
			'host'       => 'default',
			'ltype'      => '',
			'lvalue'     => '',
			'from'       => false,
			'to'         => false,
			'site'       => false,
		),
		$atts,
		'my_calendar_upcoming'
	);

	global $user_ID;
	if ( 'current' === $args['author'] ) {
		$args['author'] = apply_filters( 'mc_display_author', $user_ID, 'upcoming' );
	}
	if ( 'current' === $args['host'] ) {
		$args['host'] = apply_filters( 'mc_display_host', $user_ID, 'upcoming' );
	}

	return my_calendar_upcoming_events( $args );
}

/**
 * Today's Events My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string Calendar.
 */
function my_calendar_insert_today( $atts ) {
	$args = shortcode_atts(
		array(
			'category' => 'default',
			'author'   => 'default',
			'host'     => 'default',
			'template' => 'default',
			'fallback' => '',
			'date'     => false,
			'site'     => false,
		),
		$atts,
		'my_calendar_today'
	);

	global $user_ID;
	if ( 'current' === $args['author'] ) {
		$args['author'] = apply_filters( 'mc_display_author', $user_ID, 'today' );
	}
	if ( 'current' === $args['host'] ) {
		$args['host'] = apply_filters( 'mc_display_host', $user_ID, 'today' );
	}

	return my_calendar_todays_events( $args );
}

/**
 * Locations List My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string locations.
 */
function my_calendar_show_locations_list( $atts ) {
	$args = shortcode_atts(
		array(
			'datatype' => 'name',
			'template' => '',
		),
		$atts,
		'my_calendar_locations_list'
	);

	return my_calendar_show_locations( $args['datatype'], $args['template'] );
}

/**
 * Location Filter My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string location filter.
 */
function my_calendar_locations( $atts ) {
	$args = shortcode_atts(
		array(
			'show'       => 'list',
			'datatype'   => 'name',
			'target_url' => '',
		),
		$atts,
		'my_calendar_locations'
	);

	return my_calendar_locations_list( $args['show'], $args['datatype'], 'single', $args['target_url'] );
}

/**
 * Category filter My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string category filter.
 */
function my_calendar_categories( $atts ) {
	$args = shortcode_atts(
		array(
			'show'       => 'list',
			'target_url' => '',
		),
		$atts,
		'my_calendar_categories'
	);

	return my_calendar_categories_list( $args['show'], 'public', 'single', $args['target_url'] );
}

/**
 * Accessibility Filter My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string accessibility filters.
 */
function my_calendar_access( $atts ) {
	$args = shortcode_atts(
		array(
			'show'       => 'list',
			'target_url' => '',
		),
		$atts,
		'my_calendar_access'
	);

	return mc_access_list( $args['show'], 'single', $args['target_url'] );
}

/**
 * All Filters My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string filters.
 */
function my_calendar_filters( $atts ) {
	$args = shortcode_atts(
		array(
			'show'       => 'categories,locations',
			'target_url' => '',
			'ltype'      => 'name',
		),
		$atts,
		'my_calendar_filters'
	);

	return mc_filters( $args['show'], $args['target_url'], $args['ltype'] );
}

/**
 * Single Event My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string event.
 */
function my_calendar_show_event( $atts ) {
	$args = shortcode_atts(
		array(
			'event'    => '',
			'template' => '<h3>{title}</h3>{description}',
			'list'     => '<li>{date}, {time}</li>',
			'before'   => '<ul>',
			'after'    => '</ul>',
			'instance' => false,
		),
		$atts,
		'my_calendar_event'
	);

	return mc_instance_list( $args );
}

/**
 * Search Form My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string search form.
 */
function my_calendar_search( $atts ) {
	$args = shortcode_atts(
		array(
			'type' => 'simple',
			'url'  => '',
		),
		$atts,
		'my_calendar_search'
	);

	return my_calendar_searchform( $args['type'], $args['url'] );
}

/**
 * Current Event My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string event.
 */
function my_calendar_now( $atts ) {
	$args = shortcode_atts(
		array(
			'category' => '',
			'template' => '<strong>{link_title}</strong> {timerange}',
			'site'     => false,
		),
		$atts,
		'my_calendar_now'
	);

	return my_calendar_events_now( $args['category'], $args['template'], $args['site'] );
}

/**
 * Next Event My Calendar shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string event.
 */
function my_calendar_next( $atts ) {
	$args = shortcode_atts(
		array(
			'category' => '',
			'template' => '<strong>{link_title}</strong> {timerange}',
			'skip'     => 0,
			'site'     => false,
		),
		$atts,
		'my_calendar_next'
	);

	return my_calendar_events_next( $args['category'], $args['template'], $args['skip'], $args['site'] );
}

