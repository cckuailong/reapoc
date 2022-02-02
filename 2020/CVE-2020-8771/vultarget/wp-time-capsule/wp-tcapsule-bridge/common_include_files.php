<?php

class Common_Include_Files{
	protected $type;
	protected $dir_path;
	public function __construct($type){
		$this->type = $type;
		$this->dir_path = dirname(__FILE__). '/';
	}
	public function init($init_type = false){

		$this->include_config();

		//Bridge does not have config file so returning after some included some core files
		if ($this->type === 'WPTC_Bridge_Core') {
			$this->include_file( $this->dir_path . 'wptc-restore-app-functions.php' );
			$this->include_file( $this->dir_path . 'Classes/Processed/Base.php' );

			$this->include_wp_files();
			$this->init_constants();
			$this->include_common_functions();
			$this->include_heart();
			$this->include_external_fs();
			return ;
		}

		$this->include_wp_files();
		$this->init_constants();

		switch ($this->type) {
			case 'wptc-ajax':
			case 'restore-progress':
				$this->include_file( $this->dir_path . 'Classes/Processed/Base.php');
				break;
			case 'wptc-copy':
				$this->include_file( $this->dir_path . "Base.php");
				$this->include_file( $this->dir_path . "pclzip.class.php");
				break;
		}

		$this->include_common_functions();
		$this->include_heart();
		$this->include_external_fs();
		$this->include_sdk();
		$this->include_primary_files_wptc();
	}

	private function include_common_functions(){
		$this->include_file( $this->dir_path . 'common-functions.php');
	}

	private function include_wp_files(){
		$this->include_file( $this->dir_path . "wp-db-custom.php" );
	}

	private function include_config(){
		//Loading wp functions first to use some core functions in config file.
		$this->include_file( $this->dir_path . "wp-modified-functions.php" );
		$this->include_file( $this->dir_path . "wp-tc-config.php");
	}

	private function include_heart(){
		$this->include_file( $this->dir_path . 'utils/g-wrapper-utils.php');
		$this->include_file( $this->dir_path . 'Classes/class-file-iterator.php');
		$this->include_file( $this->dir_path . 'Classes/Extension/Base.php');
		$this->include_file( $this->dir_path . 'Classes/Extension/Manager.php');
		$this->include_file( $this->dir_path . 'Classes/Extension/DefaultOutput.php');
		$this->include_file( $this->dir_path . 'Classes/Extension/GdriveOutput.php');
		$this->include_file( $this->dir_path . 'Classes/Processed/Files.php');
		$this->include_file( $this->dir_path . 'Classes/Processed/Restoredfiles.php');
		$this->include_file( $this->dir_path . 'Classes/Processed/iterator.php');
		$this->include_file( $this->dir_path . 'Classes/DatabaseBackup.php');
		$this->include_file( $this->dir_path . 'Classes/FileList.php');
		$this->include_file( $this->dir_path . 'Classes/DropboxFacade.php');
		$this->include_file( $this->dir_path . 'Classes/Config.php');
		$this->include_file( $this->dir_path . 'Classes/BackupController.php');
		$this->include_file( $this->dir_path . 'Classes/Logger.php');
		$this->include_file( $this->dir_path . 'Classes/DebugLog.php');
		$this->include_file( $this->dir_path . 'Classes/Factory.php');
		$this->include_file( $this->dir_path . 'Classes/UploadTracker.php');
		$this->include_file( $this->dir_path . 'Classes/class-replace-db-links.php');
	}

	private function include_external_fs(){
		$this->include_file( $this->dir_path . 'wp-files/class-wp-error.php');
		$this->include_file( $this->dir_path . 'wp-files/file.php');
		$this->include_file( $this->dir_path . 'wp-files/class-wp-filesystem-base.php');
		$this->include_file( $this->dir_path . 'wp-files/class-wp-filesystem-direct.php');
		$this->include_file( $this->dir_path . 'wp-files/class-wp-filesystem-ftpext.php');
		$this->include_file( $this->dir_path . 'wp-files/class-wp-filesystem-ssh2.php');
		$this->include_file( $this->dir_path . 'wp-files/class-wp-filesystem-ftpsockets.php');
	}

	public function include_sdk(){
		$this->include_file( $this->dir_path . 'Dropbox/Dropbox/API.php');
		$this->include_file( $this->dir_path . 'Dropbox/Dropbox/Exception.php');
		$this->include_file( $this->dir_path . 'Dropbox/Dropbox/OAuth/Consumer/ConsumerAbstract.php');
		$this->include_file( $this->dir_path . 'Dropbox/Dropbox/OAuth/Consumer/Curl.php');

		if (is_php_version_compatible_for_g_drive_wptc()) {
			$this->include_file( $this->dir_path . 'Google/autoload.php');
			$this->include_file( $this->dir_path . 'Google/GoogleWPTCWrapper.php');
			$this->include_file( $this->dir_path . 'Classes/GdriveFacade.php');
		}

		if (is_php_version_compatible_for_s3_wptc()) {
			$this->include_file( $this->dir_path . 'S3/autoload.php');
			$this->include_file( $this->dir_path . 'S3/s3WPTCWrapper.php');
			$this->include_file( $this->dir_path . 'Classes/S3Facade.php');
			$this->include_file( $this->dir_path . 'Classes/WasabiFacade.php');
		}
	}

	public function include_primary_files_wptc(){
		$this->include_file( $this->dir_path . 'Base/Factory.php');
		$this->include_file( $this->dir_path . 'Base/init.php');
		$this->include_file( $this->dir_path . 'Base/Hooks.php');
		$this->include_file( $this->dir_path . 'Base/HooksHandler.php');
		$this->include_file( $this->dir_path . 'Base/Config.php');

		$this->include_file( $this->dir_path . 'Base/CurlWrapper.php');

		$this->include_file( $this->dir_path . 'Classes/CronServer/Config.php');
		$this->include_file( $this->dir_path . 'Classes/CronServer/CurlWrapper.php');

		$this->include_file( $this->dir_path . 'Classes/WptcBackup/init.php');
		$this->include_file( $this->dir_path . 'Classes/WptcBackup/Hooks.php');
		$this->include_file( $this->dir_path . 'Classes/WptcBackup/HooksHandler.php');
		$this->include_file( $this->dir_path . 'Classes/WptcBackup/Config.php');

		$this->include_file( $this->dir_path . 'Classes/Common/init.php');
		$this->include_file( $this->dir_path . 'Classes/Common/Hooks.php');
		$this->include_file( $this->dir_path . 'Classes/Common/HooksHandler.php');
		$this->include_file( $this->dir_path . 'Classes/Common/Config.php');

		$this->include_file( $this->dir_path . 'Classes/Analytics/init.php');
		$this->include_file( $this->dir_path . 'Classes/Analytics/Hooks.php');
		$this->include_file( $this->dir_path . 'Classes/Analytics/HooksHandler.php');
		$this->include_file( $this->dir_path . 'Classes/Analytics/Config.php');
		$this->include_file( $this->dir_path . 'Classes/Analytics/BackupAnalytics.php');

		$this->include_file( $this->dir_path . 'Classes/ExcludeOption/init.php');
		$this->include_file( $this->dir_path . 'Classes/ExcludeOption/Hooks.php');
		$this->include_file( $this->dir_path . 'Classes/ExcludeOption/HooksHandler.php');
		$this->include_file( $this->dir_path . 'Classes/ExcludeOption/Config.php');
		$this->include_file( $this->dir_path . 'Classes/ExcludeOption/ExcludeOption.php');

		$this->include_file( $this->dir_path . 'Classes/AppFunctions/init.php');
		$this->include_file( $this->dir_path . 'Classes/AppFunctions/Hooks.php');
		$this->include_file( $this->dir_path . 'Classes/AppFunctions/HooksHandler.php');
		$this->include_file( $this->dir_path . 'Classes/AppFunctions/Config.php');
		$this->include_file( $this->dir_path . 'Classes/AppFunctions/AppFunctions.php');

		$this->include_file( $this->dir_path . 'Classes/Triggers/DeleteTrigger.php' );
		$this->include_file( $this->dir_path . 'Classes/Triggers/InsertTrigger.php' );
		$this->include_file( $this->dir_path . 'Classes/Triggers/TriggerCommon.php' );
		$this->include_file( $this->dir_path . 'Classes/Triggers/TriggerInit.php' );
		$this->include_file( $this->dir_path . 'Classes/Triggers/UpdateTrigger.php' );

		WPTC_Base_Factory::get('Wptc_Base')->init();
	}

	public function include_file($file){
		if(!file_exists($file)){
			return false;
		}

		require_once $file;
	}

	public function init_constants(){
		$this->include_file( $this->dir_path . 'wptc-constants.php' );
		$constants = new WPTC_Constants();
		if ($this->type === 'WPTC_Bridge_Core') {
			$constants->bridge_restore();
		} else {
			$constants->init_restore();
		}
	}
}
