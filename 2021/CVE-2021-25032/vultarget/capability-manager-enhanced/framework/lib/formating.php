<?php
/**
 * General formating functions.
 * Used to format output data and send messages to user.
 *
 * @version		$Rev: 199485 $
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
 * Displays admin notices.
 *
 * @param $message	Message to display.
 * @return void
 */
function ak_admin_notify( $message = '' )
{
    if (is_admin() && !did_action('pp_capabilities_error')) {
	    if ( empty($message) ) {
		    $message = __('Settings saved.', 'capsman-enhanced');
    	}
    	echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
    }
}

/**
 * Displays admin ERRORS.
 *
 * @param $message	Message to display.
 * @return void
 */
function ak_admin_error( $message )
{
    if ( is_admin() ) {
        echo '<div id="error" class="error"><p><strong>' . $message . '</strong></p></div>';
    }

    do_action('pp_capabilities_error');
}