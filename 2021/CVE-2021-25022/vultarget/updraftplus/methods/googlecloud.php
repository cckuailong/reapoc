<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

if (class_exists('UpdraftPlus_BackupModule_googlecloud')) return;

if (version_compare(PHP_VERSION, '5.2.4', '>=')) {

	if (class_exists('UpdraftPlus_Addons_RemoteStorage_googlecloud')) {
		class UpdraftPlus_BackupModule_googlecloud extends UpdraftPlus_Addons_RemoteStorage_googlecloud {
			public function __construct() {
				parent::__construct('googlecloud', 'Google Cloud', '5.2.4', 'googlecloud.png');
			}
		}
	} else {
		include_once(UPDRAFTPLUS_DIR.'/methods/addon-not-yet-present.php');
		/**
		 * N.B. UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule
		 */
		class UpdraftPlus_BackupModule_googlecloud extends UpdraftPlus_BackupModule_AddonNotYetPresent {
			public function __construct() {
				parent::__construct('googlecloud', 'Google Cloud', '5.2.4', 'googlecloud.png');
			}
		}
	}
} else {
	include_once(UPDRAFTPLUS_DIR.'/methods/insufficient.php');
	class UpdraftPlus_BackupModule_googlecloud extends UpdraftPlus_BackupModule_insufficientphp {
		public function __construct() {
			parent::__construct('googlecloud', 'Google Cloud', '5.2.4', 'googlecloud.png');
		}
	}
}
