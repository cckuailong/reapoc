<?php

if (!defined('ABSPATH')) die('No direct access allowed');

class UpdraftPlus_Temporary_Clone_Restore {
	
	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		add_action('updraftplus_temporary_clone_ready_for_restore', array($this, 'clone_ready_for_restore'));
		add_action('updraftplus_restored_db', array($this, 'remove_maintenance_file'));
	}

	/**
	 * This function will add a ready_for_restore file in the updraft backup directory to indicate that we are ready to restore the received backup set
	 *
	 * @param String|Null $job_id - the job that is ready to restore, if known.
	 *
	 * @return void
	 */
	public function clone_ready_for_restore($job_id = null) {
		global $updraftplus, $wp_filesystem;

		$state_file = trailingslashit($updraftplus->backups_dir_location()). 'ready_for_restore';
		
		error_log("UpdraftPlus_Temporary_Clone_Restore::clone_ready_for_restore($job_id): touching flag file");
		
		if ($job_id) {
			file_put_contents($state_file, $job_id);
		} else {
			touch($state_file);
		}

		if (!function_exists('WP_Filesystem')) require_once ABSPATH.'wp-admin/includes/file.php';
		WP_Filesystem();

		// Create maintenance file with current clone status contents
		if (!$wp_filesystem->exists(trailingslashit(WP_CONTENT_DIR).'maintenance.php')) {
			ob_start();
			if (!class_exists('UpdraftPlus_Temporary_Clone_Status')) {
				include_once trailingslashit(plugin_dir_path(__FILE__)).'temporary-clone-status.php';
			}
			$updraftplus_temporary_clone_status = new UpdraftPlus_Temporary_Clone_Status();
			$updraftplus_temporary_clone_status->output_status_page(false);
			$contents = ob_get_clean();
			$wp_filesystem->put_contents(
				trailingslashit(WP_CONTENT_DIR).'maintenance.php',
				$contents,
				FS_CHMOD_FILE
			);
		}
	}

	/**
	 * Remove maintenance file created before the DB restoration.
	 */
	public function remove_maintenance_file() {
		global $updraftplus, $wp_filesystem;

		$updraft_dir = trailingslashit($updraftplus->backups_dir_location());

		if (!file_exists($updraft_dir . 'ready_for_restore')) return;

		if (!function_exists('WP_Filesystem')) require_once ABSPATH.'wp-admin/includes/file.php';
		WP_Filesystem();

		$wp_filesystem->delete(trailingslashit(WP_CONTENT_DIR).'maintenance.php');
	}
}

if (defined('UPDRAFTPLUS_THIS_IS_CLONE') && UPDRAFTPLUS_THIS_IS_CLONE) {
	new UpdraftPlus_Temporary_Clone_Restore();
}
