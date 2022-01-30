<?php

add_action( 'init', 'wpcf7_init_block_editor_assets', 10, 0 );

function wpcf7_init_block_editor_assets() {
	$assets = array();

	$asset_file = wpcf7_plugin_path(
		'includes/block-editor/index.asset.php'
	);

	if ( file_exists( $asset_file ) ) {
		$assets = include( $asset_file );
	}

	$assets = wp_parse_args( $assets, array(
		'src' => wpcf7_plugin_url( 'includes/block-editor/index.js' ),
		'dependencies' => array(
			'wp-api-fetch',
			'wp-components',
			'wp-compose',
			'wp-blocks',
			'wp-element',
			'wp-i18n',
		),
		'version' => WPCF7_VERSION,
	) );

	wp_register_script(
		'contact-form-7-block-editor',
		$assets['src'],
		$assets['dependencies'],
		$assets['version']
	);

	wp_set_script_translations(
		'contact-form-7-block-editor',
		'contact-form-7'
	);

	register_block_type(
		'contact-form-7/contact-form-selector',
		array(
			'editor_script' => 'contact-form-7-block-editor',
		)
	);
}
