<?php

namespace Never5\DownloadMonitor\Shop\Admin;

class DownloadOption {

	const OPTION_KEY = '_paid_only';

	/**
	 * Setup the download option
	 */
	public function setup() {

		// Add option
		add_action( 'dlm_options_end', array( $this, 'add_download_option' ), 10, 1 );

		// Save download options
		add_action( 'dlm_save_metabox', array( $this, 'save_download_option' ), 10, 1 );
	}

	/**
	 * Add mail lock to download options
	 *
	 * @param $post_id
	 */
	public function add_download_option( $post_id ) {
		echo '<p class="form-field form-field-checkbox">
			<input type="checkbox" name="' . self::OPTION_KEY . '" id="' . self::OPTION_KEY . '" ' . checked( get_post_meta( $post_id, self::OPTION_KEY, true ), '1', false ) . ' />
			<label for="' . self::OPTION_KEY . '">' . __( 'Paid Only', 'download-monitor' ) . '</label>
			<span class="dlm-description">' . __( 'Only users who purchased a product that contains this download will be able to access the file.', 'download-monitor' ) . '</span>
		</p>';
	}

	/**
	 * Save download option
	 *
	 * @param $post_id
	 */
	public function save_download_option( $post_id ) {
		$enabled = ( isset( $_POST[ self::OPTION_KEY ] ) );
		delete_post_meta( $post_id, self::OPTION_KEY );
		if ( $enabled ) {
			add_post_meta( $post_id, self::OPTION_KEY, 1 );
		}
	}
}