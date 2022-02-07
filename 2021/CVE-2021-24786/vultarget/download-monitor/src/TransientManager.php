<?php

class DLM_Transient_Manager {

	/**
	 * Clear download version transient
	 *
	 * @param int $download_id
	 *
	 * @return bool
	 */
	public function clear_versions_transient( $download_id ) {

		delete_transient( 'dlm_file_version_ids_' . $download_id );

		return true;
	}

	/**
	 * Clear all download version transients
	 *
	 * @return bool
	 */
	public function clear_all_version_transients() {
		global $wpdb;

		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_timeout_dlm_file_version_%';" );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE '_transient_dlm_file_version_%';" );

		return true;
	}

}