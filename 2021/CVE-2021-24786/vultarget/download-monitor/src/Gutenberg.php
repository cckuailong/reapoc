<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_Gutenberg {

	public function setup() {

		add_action( 'init', array( $this, 'load' ) );

	}

	public function load() {

		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		// register Gutenberg JS
		wp_register_script(
			'dlm_gutenberg_blocks',
			plugins_url( '/assets/blocks/dist/blocks.build.js', download_monitor()->get_plugin_file() ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			DLM_VERSION
		);

		wp_register_style(
			'dlm_gutenberg_blocks-editor',
			plugins_url( '/assets/css/gb-editor.css', download_monitor()->get_plugin_file() ),
			array( 'wp-edit-blocks' ),
			DLM_VERSION
		);

		// register the block in PHP
		register_block_type( 'download-monitor/download-button', array(
//			'style' => 'gutenberg-examples-03-esnext',
			'editor_style'    => 'dlm_gutenberg_blocks-editor',
			'editor_script'   => 'dlm_gutenberg_blocks',
			'render_callback' => array( $this, 'render_download_button' )
		) );

		wp_set_script_translations( 'dlm_gutenberg_blocks', 'download-monitor', plugin_dir_path( DLM_PLUGIN_FILE ) . 'languages' );

		$templates = array(
			array(
				'value' => 'settings',
				'label' => __( 'Default from settings', 'download-monitor' )
			)
		);
		foreach ( download_monitor()->service( 'template_handler' )->get_available_templates() as $template_key => $template_value ) {
			$templates[] = array( 'value' => $template_key, 'label' => $template_value );
		}

		wp_localize_script( 'dlm_gutenberg_blocks', 'dlmBlocks', array(
			'ajax_getDownloads' => DLM_Ajax_Manager::get_ajax_url( 'get_downloads' ),
			'ajax_getVersions'  => DLM_Ajax_Manager::get_ajax_url( 'get_versions' ),
			'urlButtonPreview'  => add_query_arg( array(
				'dlm_gutenberg_download_preview' => '1',
			), site_url( '/', 'admin' ) ),
			'templates'         => json_encode( $templates )
		) );


	}

	public function render_download_button( $attributes, $content ) {

		$download = null;
		$template = dlm_get_default_download_template();

		// try fetching the download from the attributes
		if ( isset( $attributes['download_id'] ) ) {

			try {
				/** @var DLM_Download $download */
				$download = download_monitor()->service( 'download_repository' )->retrieve_single( absint( $attributes['download_id'] ) );


				if ( isset( $attributes['version_id'] ) ) {

					try {
						/** @var DLM_Download_Version $version */
						$version = download_monitor()->service( 'version_repository' )->retrieve_single( absint( $attributes['version_id'] ) );
						$download->set_version( $version );
					} catch ( Exception $exception ) {
						// no version found, don't do anything.
					}
				}


			} catch ( Exception $exception ) {
				// no download found, don't do anything.
			}
		}

		if ( isset( $attributes['template'] ) ) {
			$template = $attributes['template'];
		}

		if ( isset( $attributes['custom_template'] ) ) {
			$template = $attributes['custom_template'];
		}

		$template_handler = new DLM_Template_Handler();

		// do the output
		ob_start();
		$template_handler->get_template_part( 'content-download', $template, '', array( 'dlm_download' => $download ) );
		$output = ob_get_clean();

		if ( isset( $attributes['autop'] ) && $attributes['autop'] == "1" ) {
			$output = wpautop( $output );
		}

		return $output;
	}
}