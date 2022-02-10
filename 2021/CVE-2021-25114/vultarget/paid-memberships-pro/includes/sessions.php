<?php

/*
	Copyright 2011	Stranger Studios	(email : jason@strangerstudios.com)
	GPLv2 Full license details in license.txt
*/


/**
 * A general function to start sessions for Paid Memberships Pro.
 * @since 1.9.2
 */
function pmpro_start_session() {
    // If headers were already sent, we can't use sessions.
	if ( headers_sent() ) {
		return;
    }

    //if the session hasn't been started yet, start it (ignore if running from command line)
    if (!defined('PMPRO_USE_SESSIONS') || PMPRO_USE_SESSIONS == true) {
        if (defined('STDIN')) {
            //command line
        } else {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
            } else {
                if (!session_id()) {
                    session_start();
                }
            }
        }
    }
}

add_action('pmpro_checkout_preheader_before_get_level_at_checkout', 'pmpro_start_session', -1);

/**
 * Close the session object for new updates
 * @since 1.9.2
 */
function pmpro_close_session() {
    if (!defined('PMPRO_USE_SESSIONS') || PMPRO_USE_SESSIONS == true) {
        if (defined('STDIN')) {
            //command line
        } else {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                if (session_status() == PHP_SESSION_ACTIVE) {
                    session_write_close();
                }
            } else {
                if (session_id()) {
                    session_write_close();
                }
            }
        }
    }
}
add_action('pmpro_after_checkout', 'pmpro_close_session', 32768);

/**
 * Set a session variable.
 *
 * @since 2.1.0
 *
 * TODO: Update docblock.
 */
function pmpro_set_session_var($key, $value) {
    pmpro_start_session();
    $_SESSION[$key] = $value;
}

/**
 * Get a session variable.
 *
 * @since 2.1.0
 *
 * TODO: Update docblock.
 */
function pmpro_get_session_var( $key ) {
    pmpro_start_session();
	if ( ! empty( $_SESSION ) && isset( $_SESSION[$key] ) ) {
		return  $_SESSION[$key];
	} else {
		return false;
	}
}

/**
 * Unset a session variable.
 *
 * @since 2.1.0
 *
 * TODO: Update docblock.
 */
function pmpro_unset_session_var($key) {
    pmpro_start_session();
    unset($_SESSION[$key]);
}
