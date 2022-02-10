<?php
/**
 * Construct widgets. Incorporate widget classes & supporting widget functions.
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

include( dirname( __FILE__ ) . '/includes/widgets/class-my-calendar-simple-search.php' );
include( dirname( __FILE__ ) . '/includes/widgets/class-my-calendar-filters.php' );
include( dirname( __FILE__ ) . '/includes/widgets/class-my-calendar-today-widget.php' );
include( dirname( __FILE__ ) . '/includes/widgets/class-my-calendar-upcoming-widget.php' );
include( dirname( __FILE__ ) . '/includes/widgets/class-my-calendar-mini-widget.php' );

/**
 * Generate the widget output for upcoming events.
 *
 * @param array $args Event selection arguments.
 *
 * @return String HTML output list.
 */
function my_calendar_upcoming_events( $args ) {
	$before     = ( isset( $args['before'] ) ) ? $args['before'] : 'default';
	$after      = ( isset( $args['after'] ) ) ? $args['after'] : 'default';
	$type       = ( isset( $args['type'] ) ) ? $args['type'] : 'default';
	$category   = ( isset( $args['category'] ) ) ? $args['category'] : 'default';
	$template   = ( isset( $args['template'] ) ) ? $args['template'] : 'default';
	$substitute = ( isset( $args['fallback'] ) ) ? $args['fallback'] : '';
	$order      = ( isset( $args['order'] ) ) ? $args['order'] : 'asc';
	$skip       = ( isset( $args['skip'] ) ) ? $args['skip'] : 0;
	$show_today = ( isset( $args['show_today'] ) ) ? $args['show_today'] : 'yes';
	$author     = ( isset( $args['author'] ) ) ? $args['author'] : 'default';
	$host       = ( isset( $args['host'] ) ) ? $args['host'] : 'default';
	$ltype      = ( isset( $args['ltype'] ) ) ? $args['ltype'] : '';
	$lvalue     = ( isset( $args['lvalue'] ) ) ? $args['lvalue'] : '';
	$from       = ( isset( $args['from'] ) ) ? $args['from'] : '';
	$to         = ( isset( $args['to'] ) ) ? $args['to'] : '';
	$site       = ( isset( $args['site'] ) ) ? $args['site'] : false;

	if ( $site ) {
		$site = ( 'global' === $site ) ? BLOG_ID_CURRENT_SITE : $site;
		switch_to_blog( $site );
	}

	$hash         = md5( implode( ',', $args ) );
	$output       = '';
	$defaults     = mc_widget_defaults();
	$display_type = ( 'default' === $type ) ? $defaults['upcoming']['type'] : $type;
	$display_type = ( '' === $display_type ) ? 'events' : $display_type;

	// Get number of units we should go into the future.
	$after = ( 'default' === $after ) ? $defaults['upcoming']['after'] : $after;
	$after = ( '' === $after ) ? 10 : $after;

	// Get number of units we should go into the past.
	$before   = ( 'default' === $before ) ? $defaults['upcoming']['before'] : $before;
	$before   = ( '' === $before ) ? 0 : $before;
	$category = ( 'default' === $category ) ? '' : $category;

	// allow reference by file to external template.
	if ( '' !== $template && mc_file_exists( $template ) ) {
		$template = file_get_contents( mc_get_file( $template ) );
	}

	$template = ( ! $template || 'default' === $template ) ? $defaults['upcoming']['template'] : $template;
	if ( mc_key_exists( $template ) ) {
		$template = mc_get_custom_template( $template );
	}

	$template       = apply_filters( 'mc_upcoming_events_template', $template );
	$no_event_text  = ( '' === $substitute ) ? $defaults['upcoming']['text'] : $substitute;
	$header         = "<ul id='upcoming-events-$hash' class='upcoming-events'>";
	$footer         = '</ul>';
	$display_events = ( 'events' === $display_type || 'event' === $display_type ) ? true : false;
	if ( ! $display_events ) {
		$temp_array = array();
		if ( 'days' === $display_type ) {
			$from = mc_date( 'Y-m-d', strtotime( "-$before days" ), false );
			$to   = mc_date( 'Y-m-d', strtotime( "+$after days" ), false );
		}
		if ( 'month' === $display_type ) {
			$from = mc_date( 'Y-m-1' );
			$to   = mc_date( 'Y-m-t' );
		}
		if ( 'custom' === $display_type && '' !== $from && '' !== $to ) {
			$from = mc_date( 'Y-m-d', strtotime( $from ), false );
			$to   = ( 'today' === $to ) ? current_time( 'Y-m-d' ) : mc_date( 'Y-m-d', strtotime( $to ), false );
		}
		/* Yes, this is crude. But sometimes simplicity works best. There are only 12 possibilities, after all. */
		if ( 'month+1' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+1 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+1 month' ), false );
		}
		if ( 'month+2' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+2 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+2 month' ), false );
		}
		if ( 'month+3' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+3 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+3 month' ), false );
		}
		if ( 'month+4' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+4 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+4 month' ), false );
		}
		if ( 'month+5' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+5 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+5 month' ), false );
		}
		if ( 'month+6' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+6 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+6 month' ), false );
		}
		if ( 'month+7' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+7 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+7 month' ), false );
		}
		if ( 'month+8' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+8 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+8 month' ), false );
		}
		if ( 'month+9' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+9 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+9 month' ), false );
		}
		if ( 'month+10' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+10 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+10 month' ), false );
		}
		if ( 'month+11' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+11 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+11 month' ), false );
		}
		if ( 'month+12' === $display_type ) {
			$from = mc_date( 'Y-m-1', strtotime( '+12 month' ), false );
			$to   = mc_date( 'Y-m-t', strtotime( '+12 month' ), false );
		}
		if ( 'year' === $display_type ) {
			$from = mc_date( 'Y-1-1' );
			$to   = mc_date( 'Y-12-31' );
		}
		$from = apply_filters( 'mc_upcoming_date_from', $from, $args );
		$to   = apply_filters( 'mc_upcoming_date_to', $to, $args );

		$query       = array(
			'from'     => $from,
			'to'       => $to,
			'category' => $category,
			'ltype'    => $ltype,
			'lvalue'   => $lvalue,
			'author'   => $author,
			'host'     => $host,
			'search'   => '',
			'source'   => 'upcoming',
			'site'     => $site,
		);
		$query       = apply_filters( 'mc_upcoming_attributes', $query, $args );
		$event_array = my_calendar_events( $query );

		if ( 0 !== count( $event_array ) ) {
			foreach ( $event_array as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $k => $v ) {
						if ( mc_private_event( $v ) ) {
							// this event is private.
						} else {
							$temp_array[] = $v;
						}
					}
				}
			}
		}
		$i         = 0;
		$last_item = '';
		$last_id   = '';
		$last_date = '';
		$skips     = array();
		foreach ( reverse_array( $temp_array, true, $order ) as $event ) {
			$details = mc_create_tags( $event );
			$item    = apply_filters( 'mc_draw_upcoming_event', '', $details, $template, $args );
			if ( '' === $item ) {
				$item = mc_draw_template( $details, $template );
			}
			if ( $i < $skip && 0 !== $skip ) {
				$i ++;
			} else {
				$today    = current_time( 'Y-m-d H:i' );
				$date     = mc_date( 'Y-m-d H:i', strtotime( $details['dtstart'], false ) );
				$class    = ( true === my_calendar_date_comp( $date, $today ) ) ? 'past-event' : 'future-event';
				$category = mc_category_class( $details, 'mc_' );
				$classes  = mc_event_classes( $event, 'upcoming' );

				$prepend = apply_filters( 'mc_event_upcoming_before', "<li class='$class $category $classes'>", $class, $category );
				$append  = apply_filters( 'mc_event_upcoming_after', '</li>', $class, $category );
				// If same group, and same date, use it.
				if ( ( $details['group'] !== $last_id || $details['date'] === $last_date ) || '0' === $details['group'] ) {
					if ( ! in_array( $details['dateid'], $skips, true ) ) {
						$output .= ( $item === $last_item ) ? '' : $prepend . $item . $append;
					}
				}
			}
			$skips[]   = $details['dateid']; // Prevent the same event from showing more than once.
			$last_id   = $details['group']; // Prevent group events from displaying in a row. Not if there are intervening events.
			$last_item = $item;
			$last_date = $details['date'];
		}
	} else {
		$query  = array(
			'category' => $category,
			'before'   => $before,
			'after'    => $after,
			'today'    => $show_today,
			'author'   => $author,
			'host'     => $host,
			'ltype'    => $ltype,
			'lvalue'   => $lvalue,
			'site'     => $site,
		);
		$events = mc_get_all_events( $query );

		$holidays      = mc_get_all_holidays( $before, $after, $show_today );
		$holiday_array = mc_set_date_array( $holidays );

		if ( is_array( $events ) && ! empty( $events ) ) {
			$event_array = mc_set_date_array( $events );
			if ( is_array( $holidays ) && count( $holidays ) > 0 ) {
				$event_array = mc_holiday_limit( $event_array, $holiday_array ); // if there are holidays, rejigger.
			}
		}
		if ( ! empty( $event_array ) ) {
			$output .= mc_produce_upcoming_events( $event_array, $template, 'list', $order, $skip, $before, $after, $show_today );
		} else {
			$output = '';
		}
	}
	if ( '' !== $output ) {
		$output = apply_filters( 'mc_upcoming_events_header', $header ) . $output . apply_filters( 'mc_upcoming_events_footer', $footer );
		$return = mc_run_shortcodes( $output );
	} else {
		$return = '<div class="no-events-fallback upcoming-events">' . stripcslashes( $no_event_text ) . '</div>';
	}

	if ( $site ) {
		restore_current_blog();
	}

	return $return;
}

/**
 * For a set of grouped events, get the total time spanned by the group of events.
 *
 * @param int $group_id Event Group ID.
 *
 * @return array beginning and ending dates
 */
function mc_span_time( $group_id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$group_id = (int) $group_id;
	$dates    = $mcdb->get_results( $wpdb->prepare( 'SELECT event_begin, event_time, event_end, event_endtime FROM ' . my_calendar_table() . ' WHERE event_group_id = %d ORDER BY event_begin ASC', $group_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$count    = count( $dates );
	$last     = $count - 1;
	$begin    = $dates[0]->event_begin . ' ' . $dates[0]->event_time;
	$end      = $dates[ $last ]->event_end . ' ' . $dates[ $last ]->event_endtime;

	return array( $begin, $end );
}

/**
 * Generates the list of upcoming events when counting by events rather than a date pattern
 *
 * @param array  $events (Array of events to analyze).
 * @param string $template Custom template to use for display.
 * @param string $type Usually 'list', but also RSS or export.
 * @param string $order 'asc' or 'desc'.
 * @param int    $skip Number of events to skip over.
 * @param int    $before How many past events to show.
 * @param int    $after How many future events to show.
 * @param string $show_today 'yes' (anything else is false); whether to include events happening today.
 * @param string $context Display context.
 *
 * @return string; HTML output of list
 */
function mc_produce_upcoming_events( $events, $template, $type = 'list', $order = 'asc', $skip = 0, $before, $after, $show_today = 'yes', $context = 'filters' ) {
	// $events has +5 before and +5 after if those values are non-zero.
	// $events equals array of events based on before/after queries. Nothing skipped, order is not set, holiday conflicts removed.
	$output      = array();
	$near_events = array();
	$temp_array  = array();
	$past        = 1;
	$future      = 1;
	$today       = current_time( 'Y-m-d' );
	uksort( $events, 'mc_timediff_cmp' ); // Sort all events by proximity to current date.
	$count = count( $events );
	$group = array();
	$spans = array();
	$occur = array();
	$extra = 0;
	$i     = 0;
	// Create near_events array.
	$last_events = array();
	$last_group  = array();
	if ( is_array( $events ) ) {
		foreach ( $events as $k => $event ) {
			if ( $i < $count ) {
				if ( is_array( $event ) ) {
					foreach ( $event as $e ) {
						if ( mc_private_event( $e ) ) {

						} else {
							$beginning = $e->occur_begin;
							$end       = $e->occur_end;
							// Store span time in an array to avoid repeating database query.
							if ( '1' === $e->event_span && ( ! isset( $spans[ $e->occur_group_id ] ) ) ) {
								// This is a multi-day event: treat each event as if it spanned the entire range of the group.
								$span_time                   = mc_span_time( $e->occur_group_id );
								$beginning                   = $span_time[0];
								$end                         = $span_time[1];
								$spans[ $e->occur_group_id ] = $span_time;
							} elseif ( '1' === $e->event_span && ( isset( $spans[ $e->occur_group_id ] ) ) ) {
								$span_time = $spans[ $e->occur_group_id ];
								$beginning = $span_time[0];
								$end       = $span_time[1];
							}
							$current = current_time( 'Y-m-d H:i:00' );
							if ( $e ) {
								// If a multi-day event, show only once.
								if ( '0' !== $e->occur_group_id && '1' === $e->event_span && in_array( $e->occur_group_id, $group, true ) || in_array( $e->occur_id, $occur, true ) ) {
									$md = true;
								} else {
									$group[] = $e->occur_group_id;
									$occur[] = $e->occur_id;
									$md      = false;
								}
								// end multi-day reduction.
								if ( ! $md ) {
									// check if this event instance or this event group has already been displayed.
									$same_event = ( in_array( $e->occur_id, $last_events, true ) ) ? true : false;
									$same_group = ( in_array( $e->occur_group_id, $last_group, true ) ) ? true : false;
									if ( 'yes' === $show_today && my_calendar_date_equal( $beginning, $current ) ) {
										$in_total = apply_filters( 'mc_include_today_in_total', 'yes' ); // count todays events in total.
										if ( 'no' !== $in_total ) {
											$near_events[] = $e;
											if ( $before > $after ) {
												$future ++;
											} else {
												$past ++;
											}
										} else {
											$near_events[] = $e;
										}
									} elseif ( ( $past <= $before && $future <= $after ) ) {
										$near_events[] = $e; // If neither limit is reached, split off ly.
									} elseif ( $past <= $before && ( my_calendar_date_comp( $beginning, $current ) ) ) {
										$near_events[] = $e; // Split off another past event.
									} elseif ( $future <= $after && ( ! my_calendar_date_comp( $end, $current ) ) ) {
										$near_events[] = $e; // Split off another future event.
									}

									if ( my_calendar_date_comp( $beginning, $current ) ) {
										$past ++;
									} elseif ( my_calendar_date_equal( $beginning, $current ) ) {
										if ( 'yes' === $show_today ) {
											$extra ++;
										}
									} elseif ( ! my_calendar_date_comp( $end, $current ) ) {
										$future ++;
									}

									$last_events[] = $e->occur_id;
									$last_group[]  = $e->occur_group_id;
									$last_date     = $beginning;
								}
								if ( $past > $before && $future > $after && 'yes' !== $show_today ) {
									break;
								}
							}
						}
					}
				}
			}
		}
	}
	$events = $near_events;
	usort( $events, 'mc_datetime_cmp' ); // Sort split events by date.

	if ( is_array( $events ) ) {
		foreach ( array_keys( $events ) as $key ) {
			$event        =& $events[ $key ];
			$temp_array[] = $event;
		}
		$i      = 0;
		$groups = array();
		$skips  = array();

		foreach ( reverse_array( $temp_array, true, $order ) as $event ) {
			$details = mc_create_tags( $event, $context );
			if ( ! in_array( $details['group'], $groups, true ) ) {
				// dtstart is already in current time zone.
				$date     = mc_date( 'Y-m-d H:i:s', strtotime( $details['dtstart'] ), false );
				$class    = ( true === my_calendar_date_comp( $date, $today . ' ' . current_time( 'H:i' ) ) ) ? 'past-event' : 'future-event';
				$category = mc_category_class( $details, 'mc_' );
				$classes  = mc_event_classes( $event, 'upcoming' );

				if ( my_calendar_date_equal( $date, $today ) ) {
					$class = 'today';
				}
				if ( '1' === $details['event_span'] ) {
					$class = 'multiday';
				}
				if ( 'list' === $type ) {
					$prepend = "\n<li class=\"$class $category $classes\">";
					$append  = "</li>\n";
				} else {
					$prepend = '';
					$append  = '';
				}
				$prepend = apply_filters( 'mc_event_upcoming_before', $prepend, $class, $category, $date );
				$append  = apply_filters( 'mc_event_upcoming_after', $append, $class, $category, $date );

				if ( $i < $skip && 0 !== $skip ) {
					$i ++;
				} else {
					if ( ! in_array( $details['dateid'], $skips, true ) ) {

						$item = apply_filters( 'mc_draw_upcoming_event', '', $details, $template, $type );
						if ( '' === $item ) {
							$item = mc_draw_template( $details, $template, $type );
						}

						$output[] = apply_filters( 'mc_event_upcoming', $prepend . $item . $append, $event );
						$skips[]  = $details['dateid'];
					}
				}
				if ( '1' === $details['event_span'] ) {
					$groups[] = $details['group'];
				}
			}
		}
	}
	// If more items than there should be (due to handling of current-day's events), pop off.
	$intended = $before + $after + $extra;
	$actual   = count( $output );
	if ( $actual > $intended ) {
		for ( $i = 0; $i < ( $actual - $intended ); $i ++ ) {
			array_pop( $output );
		}
	}
	$html = '';
	foreach ( $output as $out ) {
		$html .= $out;
	}

	return $html;
}

/**
 * Process the Today's Events widget.
 *
 * @param array $args Event & output construction parameters.
 *
 * @return string HTML.
 */
function my_calendar_todays_events( $args ) {
	$category   = ( isset( $args['category'] ) ) ? $args['category'] : 'default';
	$template   = ( isset( $args['template'] ) ) ? $args['template'] : 'default';
	$substitute = ( isset( $args['fallback'] ) ) ? $args['fallback'] : '';
	$author     = ( isset( $args['author'] ) ) ? $args['author'] : 'all';
	$host       = ( isset( $args['host'] ) ) ? $args['host'] : 'all';
	$date       = ( isset( $args['date'] ) ) ? $args['date'] : false;
	$site       = ( isset( $args['site'] ) ) ? $args['site'] : false;

	if ( $site ) {
		$site = ( 'global' === $site ) ? BLOG_ID_CURRENT_SITE : $site;
		switch_to_blog( $site );
	}

	$params = array(
		'category'   => $category,
		'template'   => $template,
		'substitute' => $substitute,
		'author'     => $author,
		'host'       => $host,
		'date'       => $date,
	);
	$hash   = md5( implode( ',', $params ) );
	$output = '';

	// allow reference by file to external template.
	if ( '' !== $template && mc_file_exists( $template ) ) {
		$template = file_get_contents( mc_get_file( $template ) );
	}
	$defaults = mc_widget_defaults();
	$template = ( ! $template || 'default' === $template ) ? $defaults['today']['template'] : $template;

	if ( mc_key_exists( $template ) ) {
		$template = mc_get_custom_template( $template );
	}

	$category      = ( 'default' === $category ) ? $defaults['today']['category'] : $category;
	$no_event_text = ( '' === $substitute ) ? $defaults['today']['text'] : $substitute;
	if ( $date ) {
		$from = mc_date( 'Y-m-d', strtotime( $date ), false );
		$to   = mc_date( 'Y-m-d', strtotime( $date ), false );
	} else {
		$from = current_time( 'Y-m-d' );
		$to   = current_time( 'Y-m-d' );
	}

	$args   = array(
		'from'     => $from,
		'to'       => $to,
		'category' => $category,
		'ltype'    => '',
		'lvalue'   => '',
		'author'   => $author,
		'host'     => $host,
		'search'   => '',
		'source'   => 'upcoming',
		'site'     => $site,
	);
	$args   = apply_filters( 'mc_upcoming_attributes', $args, $params );
	$events = my_calendar_events( $args );

	$today         = ( isset( $events[ $from ] ) ) ? $events[ $from ] : false;
	$header        = "<ul id='todays-events-$hash' class='todays-events'>";
	$footer        = '</ul>';
	$groups        = array();
	$todays_events = array();
	// quick loop through all events today to check for holidays.
	if ( is_array( $today ) ) {
		foreach ( $today as $e ) {
			if ( ! mc_private_event( $e ) && ! in_array( $e->event_group_id, $groups, true ) ) {
				$event_details = mc_create_tags( $e );
				$ts            = $e->ts_occur_begin;
				$end           = $e->ts_occur_end;
				$now           = time();
				$category      = mc_category_class( $e, 'mc_' );
				if ( $ts < $now && $end > $now ) {
					$class = 'on-now';
				} elseif ( $now < $ts ) {
					$class = 'future-event';
				} elseif ( $now > $ts ) {
					$class = 'past-event';
				}

				$prepend = apply_filters( 'mc_todays_events_before', "<li class='$class $category'>", $class, $category );
				$append  = apply_filters( 'mc_todays_events_after', '</li>' );

				$item = apply_filters( 'mc_draw_todays_event', '', $event_details, $template );
				if ( '' === $item ) {
					$item = mc_draw_template( $event_details, $template );
				}
				$todays_events[ $ts ][] = $prepend . $item . $append;
			}
		}
		$todays_events = apply_filters( 'mc_event_today', $todays_events, $events );
		foreach ( $todays_events as $k => $t ) {
			foreach ( $t as $now ) {
				$output .= $now;
			}
		}
		if ( 0 !== count( $events ) ) {
			$return = apply_filters( 'mc_todays_events_header', $header ) . $output . apply_filters( 'mc_todays_events_footer', $footer );
		} else {
			$return = '<div class="no-events-fallback todays-events">' . stripcslashes( $no_event_text ) . '</div>';
		}
	} else {
		$return = '<div class="no-events-fallback todays-events">' . stripcslashes( $no_event_text ) . '</div>';
	}

	if ( $site ) {
		restore_current_blog();
	}

	return mc_run_shortcodes( $return );
}
