<?php
/*
Plugin Name: Tutor LMS
Plugin URI: https://www.themeum.com/product/tutor-lms/
Description: Tutor is a complete solution for creating a Learning Management System in WordPress way. It can help you to create small to large scale online education site very conveniently. Power features like report, certificate, course preview, private file sharing make Tutor a robust plugin for any educational institutes.
Author: Themeum
Version: 1.9.11
Author URI: https://themeum.com
Requires at least: 4.5
Tested up to: 5.8
License: GPLv2 or later
Text Domain: tutor
*/
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defined the tutor main file
 */
define( 'TUTOR_VERSION', '1.9.11' );
define( 'TUTOR_FILE', __FILE__ );

/**
 * Load tutor text domain for translation
 */
add_action( 'init', function() {
	load_plugin_textdomain( 'tutor', false, basename( dirname( __FILE__ ) ) . '/languages' );
});

/**
 * Tutor Helper function
 *
 * @since v.1.0.0
 */

if ( ! function_exists('tutor') ) {
	function tutor() {
		$path = plugin_dir_path( TUTOR_FILE );
		$hasPro = defined('TUTOR_PRO_VERSION');

		// Prepare the basepath
		$home_url = get_home_url();
		$parsed = parse_url($home_url);
		$base_path = (is_array( $parsed ) && isset( $parsed['path'] )) ? $parsed['path'] : '/';
		$base_path = rtrim($base_path, '/') . '/';

		// Get current URL
		$current_url = $home_url . '/' . substr($_SERVER['REQUEST_URI'], strlen($base_path));

		$info = array(
			'path'                  => $path,
			'url'                   => plugin_dir_url( TUTOR_FILE ),
			'current_url'			=> $current_url,
			'basename'              => plugin_basename( TUTOR_FILE ),
			'basepath'				=> $base_path,
			'version'               => TUTOR_VERSION,
			'nonce_action'          => 'tutor_nonce_action',
			'nonce'                 => '_tutor_nonce',
			'course_post_type'      => apply_filters( 'tutor_course_post_type', 'courses' ),
			'lesson_post_type'      => apply_filters( 'tutor_lesson_post_type', 'lesson' ),
			'instructor_role'       => apply_filters( 'tutor_instructor_role', 'tutor_instructor' ),
			'instructor_role_name'  => apply_filters( 'tutor_instructor_role_name', __( 'Tutor Instructor', 'tutor' ) ),
			'template_path'         => apply_filters( 'tutor_template_path', 'tutor/' ),
			'has_pro'               => apply_filters( 'tutor_has_pro', $hasPro),
		);

		return (object) $info;
	}
}

if ( ! class_exists('Tutor') ) {
	include_once 'classes/Tutor.php';
}

/**
 * @return \TUTOR\Utils
 *
 * Get all helper functions/methods
 *
 */

if ( ! class_exists('\TUTOR\Utils') ) {
	include_once 'classes/Utils.php';
}

if ( ! function_exists('tutor_utils') ) {
	function tutor_utils() {
		if(!isset($GLOBALS['tutor_utils_object'])) {
			// Use runtime cache
			$GLOBALS['tutor_utils_object'] = new \TUTOR\Utils();
		}
		
		return $GLOBALS['tutor_utils_object'];
	}
}

/**
 * @return \TUTOR\Utils
 *
 * alis of tutor_utils()
 *
 * @since v.1.3.4
 */

if ( ! function_exists('tutils') ) {
	function tutils() {
		return tutor_utils();
	}
}

/**
 * Do some task during activation
 * @moved here from Tutor Class
 * @since v.1.5.2
 */
register_activation_hook( TUTOR_FILE, array('\TUTOR\Tutor', 'tutor_activate') );
register_deactivation_hook( TUTOR_FILE, array('\TUTOR\Tutor', 'tutor_deactivation') );

/**
 * @return null|\TUTOR\Tutor
 * Run main instance of the Tutor
 *
 * @since v.1.2.0
 */
if ( ! function_exists('tutor_lms') ) {
	function tutor_lms() {
		return \TUTOR\Tutor::instance();
	}
}

//add_action('plugins_loaded', 'tutor_lms');
$GLOBALS['tutor'] = tutor_lms();
