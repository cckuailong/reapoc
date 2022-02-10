<?php

/**
 * Check if certain plugins or themes are installed and activated
 * and if found dynamically load the relevant /includes/compatibility/ files.
 */
function pmpro_compatibility_checker() {
	$compat_checks = [
		[
			'file'        => 'siteorigin.php',
			'check_type'  => 'constant',
			'check_value' => 'SITEORIGIN_PANELS_VERSION',
		],
		[
			'file'        => 'elementor.php',
			'check_type'  => 'constant',
			'check_value' => 'ELEMENTOR_VERSION',
		],
		[
			'file'        => 'beaver-builder.php',
			'check_type'  => 'constant',
			'check_value' => 'FL_BUILDER_VERSION',
		],
		[
			'file'        => 'theme-my-login.php',
			'check_type'  => 'class',
			'check_value' => 'Theme_My_Login',
		],
		[
			'file'        => 'woocommerce.php',
			'check_type'  => 'constant',
			'check_value' => 'WC_PLUGIN_FILE',
		],
		[
			'file'        => 'wp-engine.php',
			'check_type'  => 'function',
			'check_value' => 'wpe_filter_site_url',
		],
		[
			'file'        => 'divi.php',
			'check_type'  => 'constant',
			'check_value' => 'ET_BUILDER_PLUGIN_DIR',
		],
		[
			'file'        => 'jetpack.php',
			'check_type'  => 'class',
			'check_value' => 'Jetpack',
		],
	];

	foreach ( $compat_checks as $value ) {
		if ( pmpro_compatibility_checker_is_requirement_met( $value ) ) {
			include_once( PMPRO_DIR . '/includes/compatibility/' . $value['file'] ) ;
		}
	}
}
add_action( 'plugins_loaded', 'pmpro_compatibility_checker' );

/**
 * Check whether the requirement is met.
 *
 * @since 2.6.4
 *
 * @param array $requirement The requirement config (check_type, check_value, check_constant_true).
 *
 * @return bool Whether the requirement is met.
 */
function pmpro_compatibility_checker_is_requirement_met( $requirement ) {
	// Make sure we have the keys that we expect.
	if ( ! isset( $requirement['check_type'], $requirement['check_value'] ) ) {
		return false;
	}

	// Check for a constant and maybe check if the constant is true-ish.
	if ( 'constant' === $requirement['check_type'] ) {
		return (
			defined( $requirement['check_value'] )
			&& (
				empty( $requirement['check_constant_true'] )
				|| constant( $requirement['check_value'] )
			)
		);
	}

	// Check for a function.
	if ( 'function' === $requirement['check_type'] ) {
		return function_exists( $requirement['check_value'] );
	}

	// Check for a class.
	if ( 'class' === $requirement['check_type'] ) {
		return class_exists( $requirement['check_value'] );
	}

	return false;
}

function pmpro_compatibility_checker_themes(){

	$compat_checks = array(
		array(
			'file' => 'divi.php',
			'check_type' => 'constant',
			'check_value' => 'ET_BUILDER_THEME' //Adds support for the Divi theme.
		)
	);

	foreach ( $compat_checks as $key => $value ) {
		if ( pmpro_compatibility_checker_is_requirement_met( $value ) ) {
			include_once( PMPRO_DIR . '/includes/compatibility/' . $value['file'] ) ;
		}
	}


}
add_action( 'after_setup_theme', 'pmpro_compatibility_checker_themes' );