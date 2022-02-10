<?php
/**
 * WPRMenu FrameWork
 *
 * @package     WP Responsive Menu
 * @author      MagniGenie
 * @copyright   Copyright (c) 2019, WP Responsive Menu
 * @link        https://magnigenie.com/
 * @since       WP Responsive Menu 3.1.4
 */

defined( 'ABSPATH' ) || exit;

class WPRMenu_Framework {

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.7.0
	 */
	public function init() {

		// Needs to run every time in case theme has been changed
		add_action( 'admin_init', array( $this, 'set_theme_option' ) );

	}

	/**
	 * Sets option defaults
	 *
	 * @since 1.7.0
	 */
	function set_theme_option() {

    // Load settings
    $wpr_optionsframework_settings = get_option( 'wpr_optionsframework' );

    // Updates the unique option id in the database if it has changed
    if ( function_exists( 'wpr_optionsframework_option_name' ) ) {
      wpr_optionsframework_option_name();
    }
    elseif ( has_action( 'wpr_optionsframework_option_name' ) ) {
		  do_action( 'wpr_optionsframework_option_name' );
    }
    // If the developer hasn't explicitly set an option id, we'll use a default
    else {
      $default_themename = get_option( 'stylesheet' );
      $default_themename = preg_replace( "/\W/", "_", strtolower($default_themename ) );
      $default_themename = 'wpr_optionsframework_' . $default_themename;
            
      if ( isset( $wpr_optionsframework_settings['id'] ) ) {
			 if ( $wpr_optionsframework_settings['id'] == $default_themename ) {
					// All good, using default theme id
				} else {
					$wpr_optionsframework_settings['id'] = $default_themename;
					update_option( 'wpr_optionsframework', $wpr_optionsframework_settings );
				}
      }
      else {
			 $wpr_optionsframework_settings['id'] = $default_themename;
        update_option( 'wpr_optionsframework', $wpr_optionsframework_settings );
      }
    }

	}

	static function &_wpr_optionsframework_options() {
		static $options = null;

		if ( !$options ) {
	   // Load options from options.php file (if it exists)
		
		  $maybe_options = require_once WPRMENU_OPTIONS_FRAMEWORK_PATH . 'wprmenu-options.php';;
		  
      if ( is_array( $maybe_options ) ) {
		    $options = $maybe_options;
		  }
		  else if ( function_exists( 'wpr_optionsframework_options' ) ) {
		    $options = wpr_optionsframework_options();
		  }

      // Allow setting/manipulating options via filters
      $options = apply_filters( 'wpr_of_options', $options );
		}

		return $options;
	}

}