<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

if (class_exists('UpdraftPlus_Addons_RemoteStorage_sftp')) {

	// Migrate options to standard-style - April 2017. This then enables them to get picked up by the multi-options settings translation code
	if (!is_array(UpdraftPlus_Options::get_updraft_option('updraft_sftp')) && '' != UpdraftPlus_Options::get_updraft_option('updraft_sftp_settings', '')) {
		$opts = UpdraftPlus_Options::get_updraft_option('updraft_sftp_settings');
		UpdraftPlus_Options::update_updraft_option('updraft_sftp', $opts);
		UpdraftPlus_Options::delete_updraft_option('updraft_sftp_settings');
	}

	class UpdraftPlus_BackupModule_sftp extends UpdraftPlus_Addons_RemoteStorage_sftp {
		public function __construct() {
			parent::__construct('sftp', 'SFTP/SCP');
		}
	}
	
} else {

	include_once(UPDRAFTPLUS_DIR.'/methods/addon-not-yet-present.php');
	
	/**
	 * N.B. UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule
	 */
	class UpdraftPlus_BackupModule_sftp extends UpdraftPlus_BackupModule_AddonNotYetPresent {
		public function __construct() {
			parent::__construct('sftp', 'SFTP/SCP');
		}
	}

}
