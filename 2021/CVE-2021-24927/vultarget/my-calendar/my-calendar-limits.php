<?php
/**
 * Generate limits to event queries.
 *
 * @category Events
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prepare search query.
 *
 * @param string $query search term.
 *
 * @return string query params for SQL
 */
function mc_prepare_search_query( $query ) {
	global $wpdb;
	$db_type = mc_get_db_type();
	$search  = '';
	if ( '' !== trim( $query ) ) {
		if ( 'MyISAM' === $db_type ) {
			$query  = esc_sql( $query );
			$search = ' AND MATCH(' . apply_filters( 'mc_search_fields', 'event_title,event_desc,event_short,event_label,event_city,event_postcode,event_registration' ) . ") AGAINST ( '$query' IN BOOLEAN MODE ) ";
		} else {
			$query  = esc_sql( $query );
			$search = " AND ( event_title LIKE '%$query%' OR event_desc LIKE '%$query%' OR event_short LIKE '%$query%' OR event_label LIKE '%$query%' OR event_city LIKE '%$query%' OR event_postcode LIKE '%$query%' OR event_registration LIKE '%$query%' ) ";
		}
	}

	return $search;
}


/**
 * Generate WHERE pattern for a given category passed
 *
 * @param mixed  int/string $category Single or list of categories separated by commas using IDs or names.
 * @param string            $type context of query.
 * @param string            $group context of query.
 *
 * @return string SQL modifiers.
 */
function mc_select_category( $category, $type = 'event', $group = 'events' ) {
	if ( '' === trim( $category ) ) {
		return '';
	}
	$category      = urldecode( $category );
	$select_clause = '';
	$data          = ( 'category' === $group ) ? 'category_id' : 'r.category_id';
	if ( preg_match( '/^all$|^all,|,all$|,all,/i', $category ) > 0 ) {

		return '';
	} else {

		$categories = mc_category_select_ids( $category );
		if ( count( $categories ) > 0 ) {
			$cats          = implode( ',', $categories );
			$select_clause = "AND $data IN ($cats)";
		}

		$join = '';
		if ( '' !== $select_clause ) {
			$join = ' JOIN ' . my_calendar_category_relationships_table() . ' AS r ON r.event_id = e.event_id ';
		}

		return array( $join, $select_clause );
	}
}

/**
 * Get array of category IDs from passed comma-separated data
 *
 * @param string $category numeric or string-based category tokens.
 *
 * @return array category IDs
 */
function mc_category_select_ids( $category ) {
	global $wpdb;
	$mcdb   = $wpdb;
	$select = array();

	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}

	if ( strpos( $category, '|' ) || strpos( $category, ',' ) ) {
		if ( strpos( $category, '|' ) ) {
			$categories = explode( '|', $category );
		} else {
			$categories = explode( ',', $category );
		}
		$numcat = count( $categories );
		foreach ( $categories as $key ) {
			$key = trim( $key );
			if ( is_numeric( $key ) ) {
				$add = (int) $key;
			} else {
				$key = esc_sql( $key );
				$cat = $mcdb->get_row( 'SELECT category_id FROM ' . my_calendar_categories_table() . " WHERE category_name = '$key'" );
				if ( is_object( $cat ) ) {
					$add = $cat->category_id;
				}
			}
			$select[] = $add;
		}
	} else {
		$category = trim( $category );
		if ( is_numeric( $category ) ) {
			$select[] = absint( $category );
		} else {
			$cat = $mcdb->get_row( $mcdb->prepare( 'SELECT category_id FROM ' . my_calendar_categories_table() . ' WHERE category_name = %s', trim( $category ) ) );
			if ( is_object( $cat ) ) {
				$select[] = $cat->category_id;
			}
		}
	}

	return $select;
}

/**
 * Get select parameter values for authors & hosts
 *
 * @param string $author numeric or string tokens for authors or list of authors.
 * @param string $type context of query.
 * @param string $context context of data.
 *
 * @return string WHERE limits
 */
function mc_select_author( $author, $type = 'event', $context = 'author' ) {
	if ( '' === trim( (string) $author ) ) {
		return '';
	}
	$author = urldecode( $author );
	if ( '' === $author || 'all' === $author || 'default' === $author || null === $author ) {
		return '';
	}
	$select_author = '';
	$data          = ( 'author' === $context ) ? 'event_author' : 'event_host';

	if ( preg_match( '/^all$|^all,|,all$|,all,/i', $author ) > 0 ) {
		return '';
	} else {
		$authors = mc_author_select_ids( $author );
		if ( count( $authors ) > 0 ) {
			$auths         = implode( ',', $authors );
			$select_author = "AND $data IN ($auths)";
		}

		return $select_author;
	}
}

/**
 * Get array of author IDs from passed comma-separated data
 *
 * @param string $author numeric or string-based author tokens.
 *
 * @return array author IDs
 */
function mc_author_select_ids( $author ) {
	$authors = array();
	if ( strpos( $author, '|' ) || strpos( $author, ',' ) ) {
		if ( strpos( $author, '|' ) ) {
			$authors = explode( '|', $author );
		} else {
			$authors = explode( ',', $author );
		}
		foreach ( $authors as $key ) {
			$key = trim( $key );
			if ( is_numeric( $key ) ) {
				$add = absint( $key );
			} else {
				$author = get_user_by( 'login', $key ); // Get author by username.
				$add    = $author->ID;
			}

			$authors[] = $add;
		}
	} else {
		if ( is_numeric( $author ) ) {
			$authors[] = absint( $author );
		} else {
			$author = trim( $author );
			$author = get_user_by( 'login', $author ); // Get author by username.

			if ( is_object( $author ) ) {
				$authors[] = $author->ID;
			}
		}
	}

	return $authors;
}

/**
 * Select host params.
 *
 * @uses mc_select_author()
 *
 * @param mixed int/string $host Host ID or name..
 * @param string           $type context.
 *
 * @return string SQL
 */
function mc_select_host( $host, $type = 'event' ) {

	return mc_select_author( $host, $type, 'host' );
}


/**
 * Function to limit event query by location.
 *
 * @param string               $ltype {location type}.
 * @param mixed string/integer $lvalue {location value}.
 *
 * @return string
 */
function mc_select_location( $ltype = '', $lvalue = '' ) {
	global $user_ID;
	$limit_string     = '';
	$location         = '';
	$current_location = '';
	if ( '' !== $ltype && '' !== $lvalue ) {
		$location         = $ltype;
		$current_location = $lvalue;
		switch ( $location ) {
			case 'name':
				$location_type = 'event_label';
				break;
			case 'city':
				$location_type = 'event_city';
				break;
			case 'state':
				$location_type = 'event_state';
				break;
			case 'zip':
				$location_type = 'event_postcode';
				break;
			case 'country':
				$location_type = 'event_country';
				break;
			case 'region':
				$location_type = 'event_region';
				break;
			default:
				$location_type = $location;
		}
		if ( in_array( $location_type, array( 'event_label', 'event_city', 'event_state', 'event_postcode', 'event_country', 'event_region', 'event_location', 'event_street', 'event_street2', 'event_url', 'event_longitude', 'event_latitude', 'event_zoom', 'event_phone', 'event_phone2' ), true ) ) {
			if ( 'all' !== $current_location && '' !== $current_location ) {
				$current_location = trim( $current_location );
				if ( is_numeric( $current_location ) ) {
					$limit_string = 'AND ' . $location_type . ' = ' . absint( $current_location );
				} else {
					$limit_string = 'AND ' . $location_type . " = '" . esc_sql( $current_location ) . "'";
				}
			}
		}
	}
	if ( '' !== $limit_string ) {
		if ( isset( $_GET['loc2'] ) && isset( $_GET['ltype2'] ) ) {
			$limit_string .= mc_secondary_limit( $_GET['ltype2'], $_GET['loc2'] );
		}
	}

	return apply_filters( 'mc_location_limit_sql', $limit_string, $ltype, $lvalue );
}

/**
 * Get events based on accessibility features available
 *
 * @param string $access type of accessibility feature.
 *
 * @return string limits to add to query
 */
function mc_access_limit( $access ) {
	global $wpdb;
	$options      = mc_event_access();
	$format       = ( isset( $options[ $access ] ) ) ? esc_sql( $options[ $access ] ) : false;
	$limit_string = ( $format ) ? "AND event_access LIKE '%$format%'" : '';

	return $limit_string;
}

/**
 * SQL modifiers for published vs. preview
 *
 * @return boolean
 */
function mc_select_published() {
	if ( mc_is_preview() ) {
		$published = 'event_flagged <> 1 AND ( event_approved = 1 OR event_approved = 0 )';
	} else {
		$published = 'event_flagged <> 1 AND event_approved = 1';
	}

	return $published;
}

/**
 * Set up a secondary limit on location
 *
 * @param string $ltype type of limit.
 * @param string $lvalue value.
 *
 * @return string SQL.
 */
function mc_secondary_limit( $ltype = '', $lvalue = '' ) {
	$limit_string     = '';
	$current_location = urldecode( $lvalue );
	$location         = urldecode( $ltype );
	switch ( $location ) {
		case 'name':
			$location_type = 'event_label';
			break;
		case 'city':
			$location_type = 'event_city';
			break;
		case 'state':
			$location_type = 'event_state';
			break;
		case 'zip':
			$location_type = 'event_postcode';
			break;
		case 'country':
			$location_type = 'event_country';
			break;
		case 'region':
			$location_type = 'event_region';
			break;
		default:
			$location_type = 'event_label';
	}
	if ( 'all' !== $current_location && '' !== $current_location ) {
		$limit_string = "OR $location_type='$current_location'";
	}

	return $limit_string;
}
