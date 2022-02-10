<?php
/**
 * Integration class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.0.0
 */


namespace TUTOR;


if ( ! defined( 'ABSPATH' ) )
	exit;

class Theme_Compatibility {

	public function __construct() {
		$template = trailingslashit(get_template());
		$tutor_path = tutor()->path;

		$compatibility_theme_path = $tutor_path.'includes/theme-compatibility/'.$template.'functions.php';

		if (file_exists($compatibility_theme_path)){
			include $compatibility_theme_path;
		}

	}

}