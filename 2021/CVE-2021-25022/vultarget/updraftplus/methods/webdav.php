<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

if (class_exists('UpdraftPlus_Addons_RemoteStorage_webdav')) {

	// Migrate options to new-style storage - April 2017
	if (!is_array(UpdraftPlus_Options::get_updraft_option('updraft_webdav')) && '' != UpdraftPlus_Options::get_updraft_option('updraft_webdav_settings', '')) {
		$opts = UpdraftPlus_Options::get_updraft_option('updraft_webdav_settings');
		UpdraftPlus_Options::update_updraft_option('updraft_webdav', $opts);
		UpdraftPlus_Options::delete_updraft_option('updraft_webdav_settings');
	}
	
	class UpdraftPlus_BackupModule_webdav extends UpdraftPlus_Addons_RemoteStorage_webdav {
		public function __construct() {
			parent::__construct('webdav', 'WebDAV');
		}
	}
	
} else {

	include_once(UPDRAFTPLUS_DIR.'/methods/addon-not-yet-present.php');
	
	/**
	 * N.B. UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule
	 */
	class UpdraftPlus_BackupModule_webdav extends UpdraftPlus_BackupModule_AddonNotYetPresent {
		public function __construct() {
			parent::__construct('webdav', 'WebDAV');
		}
	}

}
