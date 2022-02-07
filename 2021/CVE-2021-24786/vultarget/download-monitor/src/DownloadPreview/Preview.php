<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_DownloadPreview_Preview {

	/**
	 * Setup the preview hooks
	 */
	public function setup() {
		add_action( 'template_redirect', array( $this, 'catch_preview_request' ), 999 );
	}

	/**
	 * This method will setup a new DLM_DownloadPreview_Config object based on parameters in the URL ($_GET)
	 *
	 * @return DLM_DownloadPreview_Config
	 */
	private function get_config_from_url() {


		$config = new DLM_DownloadPreview_Config();

		if ( isset( $_GET['download_id'] ) ) {
			
			try {
				/** @var DLM_Download $download */
				$download = download_monitor()->service( 'download_repository' )->retrieve_single( absint( $_GET['download_id'] ) );


				if ( isset( $_GET['version_id'] ) ) {

					try {
						/** @var DLM_Download_Version $version */
						$version = download_monitor()->service( 'version_repository' )->retrieve_single( absint( $_GET['version_id'] ) );
						$download->set_version( $version );
					} catch ( Exception $exception ) {
						// no version found, don't do anything.
					}
				}

				$config->set_download( $download );


			} catch ( Exception $exception ) {
				// no download found, don't do anything.
			}
		}

		if ( isset( $_GET['template'] ) ) {
			$config->set_template( $_GET['template'] );
		}

		if ( isset( $_GET['custom_template'] ) ) {
			$config->set_template( $_GET['custom_template'] );
		}

		return $config;
	}

	/**
	 * Output the button preview HTML
	 */
	private function output_html() {
		echo '<!DOCTYPE html>
<html lang="en-US" class="no-js">
<head>';
		do_action( 'wp_head' );
		echo '</head>
			<body><table><tr><td valign="middle"><div id="dlmPreviewContainer">';


		$config = $this->get_config_from_url();

		if ( $config->get_download() != null ) {

			$template_handler = new DLM_Template_Handler();

			$template_handler->get_template_part( 'content-download', $config->get_template(), '', array( 'dlm_download' => $config->get_download() ) );

		} else {
			echo "<p>" . __( "Select a download first", 'download-monitor' ) . "</p>";
		}

		echo '</div></td></tr></table></body>
			</html>';
	}


	/**
	 * Catch the preview request. Setup custom HTML but output WordPress head part.
	 */
	public function catch_preview_request() {
		// check if this is a buttons preview request
		if ( isset( $_GET['dlm_gutenberg_download_preview'] ) ) {

			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}

			// remove the admin bar styling
			remove_action( 'wp_head', '_admin_bar_bump_cb' );

			// it is, output HTML
			$this->output_html();
			exit;
		}

	}
}