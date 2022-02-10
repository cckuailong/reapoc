<?php
/**
 * Users, Roles and Capabilities related functions.
 *
 * @version		$Rev: 203758 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2008, 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	Framework
 *

	Copyright 2008, 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Returns all valid roles.
 * The returned list can be translated or not.
 *
 * @uses apply_filters() Calls the 'alkivia_roles_translate' hook on translated roles array.
 * @since 0.5
 *
 * @param boolean $translate If the returned roles have to be translated or not.
 * @return array All defined roles. If translated, the key is the role name and value is the translated role.
 */
function ak_get_roles( $translate = false ) {
	global $wp_roles;
	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	$roles = $wp_roles->get_names();
	if ( $translate ) {
		foreach ($roles as $k => $r) {
			$roles[$k] = _x($r, 'User role');
		}
		asort($roles);
		return apply_filters('alkivia_roles_translate', $roles);
	} else {
		$roles = array_keys($roles);
		asort($roles);
		return $roles;
	}
}

/**
 * Generates the caps names from user level.
 *
 * @since 0.5
 *
 * @param int $level	Level to convert to caps
 * @return array		Generated caps
 */
function ak_level2caps( $level ) {
	$caps = array();
	$level = min(10, intval($level));

	for ( $i = $level; $i >= 0; $i--) {
		$caps["level_{$i}"] = true;
	}

	return $caps;
}

/**
 * Finds the proper level from a capabilities list.
 *
 * @since 0.5
 *
 * @uses _ak_level_reduce()
 * @param array $caps	List of capabilities.
 * @return int 			Level found, if no level found, will return 0.
 */
function ak_caps2level( $caps ) {
	if (!is_array($caps)) {
		return 0;
	}

	$level = array_reduce( array_keys( $caps ), '_ak_caps2level_CB', 0);
	return $level;
}

/**
 * Callback function to find the level from caps.
 * Taken from WordPress 2.7.1
 *
 * @since 0.5
 * @access private
 *
 * @return int level Level found.
 */
function _ak_caps2level_CB( $max, $item ) {
	if ( preg_match( '/^level_(10|[0-9])$/i', $item, $matches ) ) {
		$level = intval( $matches[1] );
		return max( $max, $level );
	} else {
		return $max;
	}
}
