<?php
/**
 * N-Media Scripts Framework
 * 
 * It will register/enqueue all scripts & styles
*/


if ( ! defined( 'ABSPATH' ) ) { exit; }


class PPOM_SCRIPTS {
	
	private static $ins = null;
	
	/**
	 * Return scripts URL.
	 * 
	 * @var URL
	 *
	*/
	public static $scripts_url =  PPOM_URL;
	
	/**
	 * Return current ppom version.
	 * 
	 * @var string
	 *
	*/
	public static $version =  PPOM_VERSION;

	/**
	 * Main Init
	 */
	public static function init() {}


	/**
	 * Register Script
	 *
	*/
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = '', $in_footer = true ) {
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}
	

	/**
	 * Register and Enqueue Scripts.
	 *
	*/
	public static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = '', $in_footer = true ) {
		if (!wp_script_is( $handle, 'registered' ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}
	

	/**
	 * Register Styles.
	 *
	*/
	private static function register_style( $handle, $path, $deps = array(), $version, $media = 'all' ) {
		wp_register_style( $handle, $path, $deps, $version, $media );
	}
	

	/**
	 * Register and Enqueue Styles.
	 *
	*/
	public static function enqueue_style( $handle, $path = '', $deps = array(), $version='', $media = 'all') {
		if (!wp_script_is( $handle, 'registered' ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}
	

	/**
	 * Register all PPOM Scripts.
	 */
	public static function register_scripts($register_scripts) {
		
		foreach ( $register_scripts as $handle => $props ) {
			$is_footer = isset($props['footer']) ? $props['footer'] : true ;
			self::register_script( $handle, $props['src'], $props['deps'], $props['version'], $is_footer );
		}
	}
	

	/**
	 * Register Styles
	 */
	public static function register_styles($register_styles) {
		
		foreach ( $register_styles as $handle => $props ) {
			self::register_style( $handle, $props['src'], $props['deps'], $props['version'], 'all' );
		}
	}
	
	
	/**
	 * Localize Scripts Data
	 *
	*/
	public static function localize_script( $handle, $js_var_name, $js_var_data=array() ) {
		
		if ( wp_script_is( $handle ) ) {
			
			if ( empty($js_var_data) ) { return; }
			
			wp_localize_script( $handle, $js_var_name, $js_var_data );
		}
	}
	
	
	/**
	 * Add Inline CSS
	 *
	*/
	public static function inline_style( $handle, $css ) {
		
		if ( $css != '' ) {
			wp_add_inline_style( $handle, $css );
		}
	}
	
	
	/**
	 * Add Inline JS
	 *
	*/
	public static function inline_script( $handle, $js ) {
		
		if ( $js != '' ) {
			wp_add_inline_script( $handle, $js );
		}
	}
	
	
	/**
	 * get plugin url
	 *
	*/
	public static function get_url() {
		
		return self::$scripts_url;
	}
	
	/**
	 * get plugin version
	 *
	*/
	public static function get_version() {
		
		return self::$version;
	}
	
	public static function get_instance() {
	    
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        
        return self::$ins;
    }
}