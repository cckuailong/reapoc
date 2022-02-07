<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

if (version_compare(phpversion(), '5.3.3', '>=')) {

	if (class_exists('UpdraftPlus_Addons_RemoteStorage_azure')) {
		class UpdraftPlus_BackupModule_azure extends UpdraftPlus_Addons_RemoteStorage_azure {
			public function __construct() {
				parent::__construct('azure', 'Microsoft Azure', true, true);
			}
		}
		
	} else {
	
		include_once(UPDRAFTPLUS_DIR.'/methods/addon-not-yet-present.php');
		/**
		 * N.B. UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule
		 */
		class UpdraftPlus_BackupModule_azure extends UpdraftPlus_BackupModule_AddonNotYetPresent {
			public function __construct() {
				parent::__construct('azure', 'Microsoft Azure', '5.3.3', 'azure.png');
			}
		}
		
	}
	
} else {

	include_once(UPDRAFTPLUS_DIR.'/methods/insufficient.php');
	/**
	 * N.B. UpdraftPlus_BackupModule_insufficientphp extends UpdraftPlus_BackupModule
	 */
	class UpdraftPlus_BackupModule_azure extends UpdraftPlus_BackupModule_insufficientphp {
		public function __construct() {
			parent::__construct('azure', 'Microsoft Azure', '5.3.3', 'azure.png');
		}
	}
	
}
