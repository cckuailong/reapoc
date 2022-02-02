<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
// Exit if accessed directly
/* @var $global DUP_Global_Entity */
if (!defined('DUPLICATOR_VERSION')) exit;

require_once(DUPLICATOR_PLUGIN_PATH.'/classes/class.archive.config.php');
require_once(DUPLICATOR_PLUGIN_PATH.'/classes/utilities/class.u.zip.php');
require_once(DUPLICATOR_PLUGIN_PATH.'/classes/utilities/class.u.multisite.php');
require_once(DUPLICATOR_PLUGIN_PATH.'/classes/class.password.php');

class DUP_Installer
{
	//PUBLIC
	public $File;
	public $Size			 = 0;
	public $OptsDBHost;
	public $OptsDBPort;
	public $OptsDBName;
	public $OptsDBUser;
	public $OptsSecureOn	 = 0;
	public $OptsSecurePass;
	public $numFilesAdded	 = 0;
	public $numDirsAdded	 = 0;
	//PROTECTED
	protected $Package;

	/**
	 *  Init this object
	 */
	function __construct($package)
	{
		$this->Package = $package;
	}

	public function build($package, $error_behavior = Dup_ErrorBehavior::Quit)
	{
		DUP_Log::Info("building installer");

		$this->Package	 = $package;
		$success		 = false;

		if ($this->create_enhanced_installer_files()) {
			$success = $this->add_extra_files($package);
		} else {
			DUP_Log::Info("error creating enhanced installer files");
		}


		if ($success) {
			$package->BuildProgress->installer_built = true;
		} else {
			$error_message = 'Error adding installer';
			$package->BuildProgress->set_failed($error_message);
			$package->Status = DUP_PackageStatus::ERROR;
			$package->Update();

			DUP_Log::error($error_message, "Marking build progress as failed because couldn't add installer files", $error_behavior);
			//$package->BuildProgress->failed = true;
			//$package->setStatus(DUP_PackageStatus::ERROR);
		}

		return $success;
	}

	private function create_enhanced_installer_files()
	{
		$success = false;
		if ($this->create_enhanced_installer()) {
			$success = $this->create_archive_config_file();
		}
		return $success;
	}

	private function create_enhanced_installer()
	{
		$success = true;
		$archive_filepath		 = DUP_Util::safePath("{$this->Package->StorePath}/{$this->Package->Archive->File}");
		$installer_filepath		 = apply_filters('duplicator_installer_file_path', DUP_Util::safePath(DUPLICATOR_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_installer.php");
		$template_filepath		 = DUPLICATOR_PLUGIN_PATH.'/installer/installer.tpl';
		$mini_expander_filepath	 = DUPLICATOR_PLUGIN_PATH.'/lib/dup_archive/classes/class.duparchive.mini.expander.php';

		// Replace the @@ARCHIVE@@ token
		$installer_contents = file_get_contents($template_filepath);

		if (DUP_Settings::Get('archive_build_mode') == DUP_Archive_Build_Mode::DupArchive) {
			$mini_expander_string = file_get_contents($mini_expander_filepath);

			if ($mini_expander_string === false) {
				DUP_Log::error(DUP_U::__('Error reading DupArchive mini expander'), DUP_U::__('Error reading DupArchive mini expander'), Dup_ErrorBehavior::LogOnly);
				return false;
			}
		} else {
			$mini_expander_string = '';
		}

		$search_array	 = array('@@ARCHIVE@@', '@@VERSION@@', '@@ARCHIVE_SIZE@@', '@@PACKAGE_HASH@@', '@@DUPARCHIVE_MINI_EXPANDER@@');
		$package_hash	 = $this->Package->getPackageHash();
		$replace_array	 = array($this->Package->Archive->File, DUPLICATOR_VERSION, @filesize($archive_filepath), $package_hash, $mini_expander_string);
		$installer_contents = str_replace($search_array, $replace_array, $installer_contents);

		if (@file_put_contents($installer_filepath, $installer_contents) === false) {
			DUP_Log::error(esc_html__('Error writing installer contents', 'duplicator'), esc_html__("Couldn't write to $installer_filepath", 'duplicator'));
			$success = false;
		}

		if ($success) {
			$storePath	 = "{$this->Package->StorePath}/{$this->File}";
			$this->Size	 = @filesize($storePath);
		}

		return $success;
	}

	/**
	 * Create archive.txt file */
	private function create_archive_config_file()
	{
		global $wpdb;

		$success				 = true;
		$archive_config_filepath = DUP_Util::safePath(DUPLICATOR_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_archive.txt";
		$ac						 = new DUP_Archive_Config();
		$extension				 = strtolower($this->Package->Archive->Format);

		$hasher		 = new DUP_PasswordHash(8, FALSE);
		$pass_hash	 = $hasher->HashPassword($this->Package->Installer->OptsSecurePass);

        $this->Package->Database->getScannerData();

		//READ-ONLY: COMPARE VALUES
		$ac->created	 = $this->Package->Created;
		$ac->version_dup = DUPLICATOR_VERSION;
		$ac->version_wp	 = $this->Package->VersionWP;
		$ac->version_db	 = $this->Package->VersionDB;
		$ac->version_php = $this->Package->VersionPHP;
		$ac->version_os	 = $this->Package->VersionOS;
		$ac->dbInfo		 = $this->Package->Database->info;

		//READ-ONLY: GENERAL
		// $ac->installer_base_name  = $global->installer_base_name;
		$ac->installer_base_name    = 'installer.php';
        $ac->package_name           = "{$this->Package->NameHash}_archive.{$extension}";
        $ac->package_hash           = $this->Package->getPackageHash();
        $ac->package_notes          = $this->Package->Notes;
        $ac->url_old                = get_option('siteurl');
        $ac->opts_delete            = DupLiteSnapJsonU::wp_json_encode_pprint($GLOBALS['DUPLICATOR_OPTS_DELETE']);
		$ac->blogname               = esc_html(get_option('blogname'));
		
		$abs_path					= duplicator_get_abs_path();
        $ac->wproot                 = $abs_path;
        $ac->relative_content_dir   = str_replace($abs_path, '', WP_CONTENT_DIR);
        $ac->exportOnlyDB           = $this->Package->Archive->ExportOnlyDB;
        $ac->installSiteOverwriteOn = DUPLICATOR_INSTALL_SITE_OVERWRITE_ON;
        $ac->wplogin_url            = wp_login_url();

        //PRE-FILLED: GENERAL
		$ac->secure_on		 = $this->Package->Installer->OptsSecureOn;
		$ac->secure_pass	 = $pass_hash;
		$ac->skipscan		 = false;
		$ac->dbhost			 = $this->Package->Installer->OptsDBHost;
		$ac->dbname			 = $this->Package->Installer->OptsDBName;
		$ac->dbuser			 = $this->Package->Installer->OptsDBUser;
		$ac->dbpass			 = '';
		$ac->wp_tableprefix	 = $wpdb->base_prefix;

		$ac->mu_mode						 = DUP_MU::getMode();
		$ac->is_outer_root_wp_config_file	 = (!file_exists($abs_path . '/wp-config.php')) ? true : false;
		$ac->is_outer_root_wp_content_dir	 = $this->Package->Archive->isOuterWPContentDir();

        $json = DupLiteSnapJsonU::wp_json_encode_pprint($ac);
        DUP_Log::TraceObject('json', $json);

		if (file_put_contents($archive_config_filepath, $json) === false) {
			DUP_Log::error("Error writing archive config", "Couldn't write archive config at $archive_config_filepath", Dup_ErrorBehavior::LogOnly);
			$success = false;
		}

		return $success;
	}

	/**
	 *  Puts an installer zip file in the archive for backup purposes.
	 */
	private function add_extra_files($package)
	{
		$success				 = false;
		$installer_filepath		 = apply_filters('duplicator_installer_file_path', DUP_Util::safePath(DUPLICATOR_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_installer.php");
		$scan_filepath			 = DUP_Util::safePath(DUPLICATOR_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_scan.json";
		$sql_filepath			 = DUP_Util::safePath("{$this->Package->StorePath}/{$this->Package->Database->File}");
		$archive_filepath		 = DUP_Util::safePath("{$this->Package->StorePath}/{$this->Package->Archive->File}");
		$archive_config_filepath = DUP_Util::safePath(DUPLICATOR_SSDIR_PATH_TMP)."/{$this->Package->NameHash}_archive.txt";

		DUP_Log::Info("add_extra_files1");

		if (file_exists($installer_filepath) == false) {
			DUP_Log::error("Installer $installer_filepath not present", '', Dup_ErrorBehavior::LogOnly);
			return false;
		}

		DUP_Log::Info("add_extra_files2");
		if (file_exists($sql_filepath) == false) {
			DUP_Log::error("Database SQL file $sql_filepath not present", '', Dup_ErrorBehavior::LogOnly);
			return false;
		}

		DUP_Log::Info("add_extra_files3");
		if (file_exists($archive_config_filepath) == false) {
			DUP_Log::error("Archive configuration file $archive_config_filepath not present", '', Dup_ErrorBehavior::LogOnly);
			return false;
		}

		DUP_Log::Info("add_extra_files4");
		if ($package->Archive->file_count != 2) {
			DUP_Log::Info("Doing archive file check");
			// Only way it's 2 is if the root was part of the filter in which case the archive won't be there
			DUP_Log::Info("add_extra_files5");
			if (file_exists($archive_filepath) == false) {

				DUP_Log::error("$error_text. **RECOMMENDATION: $fix_text", '', Dup_ErrorBehavior::LogOnly);

				return false;
			}
			DUP_Log::Info("add_extra_files6");
		}

		$wpconfig_filepath = $package->Archive->getWPConfigFilePath();

		if ($package->Archive->Format == 'DAF') {
			DUP_Log::Info("add_extra_files7");
			$success = $this->add_extra_files_using_duparchive($installer_filepath, $scan_filepath, $sql_filepath, $archive_filepath, $archive_config_filepath, $wpconfig_filepath);
		} else {
			DUP_Log::Info("add_extra_files8");
			$success = $this->add_extra_files_using_ziparchive($installer_filepath, $scan_filepath, $sql_filepath, $archive_filepath, $archive_config_filepath, $wpconfig_filepath);
		}

		// No sense keeping the archive config around
		@unlink($archive_config_filepath);
		$package->Archive->Size = @filesize($archive_filepath);

		return $success;
	}

	private function add_extra_files_using_duparchive($installer_filepath, $scan_filepath, $sql_filepath, $archive_filepath, $archive_config_filepath, $wpconfig_filepath)
	{
		$success = false;

		try {
			DUP_Log::Info("add_extra_files_using_da1");
			$htaccess_filepath	 = $this->getHtaccessFilePath();
			$webconf_filepath	 = duplicator_get_abs_path() . '/web.config';

			$logger = new DUP_DupArchive_Logger();

			DupArchiveEngine::init($logger, 'DUP_Log::profile');

			$embedded_scan_ark_file_path = $this->getEmbeddedScanFilePath();
			DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $scan_filepath, $embedded_scan_ark_file_path);
			$this->numFilesAdded++;

			if (file_exists($htaccess_filepath)) {
				$htaccess_ark_file_path = $this->getHtaccessArkFilePath();
				try {
					DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $htaccess_filepath, $htaccess_ark_file_path);
					$this->numFilesAdded++;
				} catch (Exception $ex) {
					// Non critical so bury exception
				}
			}

			if (file_exists($webconf_filepath)) {
				try {
					DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $webconf_filepath, DUPLICATOR_WEBCONFIG_ORIG_FILENAME);
					$this->numFilesAdded++;
				} catch (Exception $ex) {
					// Non critical so bury exception
				}
			}

			if (file_exists($wpconfig_filepath)) {
				$conf_ark_file_path = $this->getWPConfArkFilePath();
				$temp_conf_ark_file_path = $this->getTempWPConfArkFilePath();
				if (copy($wpconfig_filepath, $temp_conf_ark_file_path)) {
                    $this->cleanTempWPConfArkFilePath($temp_conf_ark_file_path);					
					DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $temp_conf_ark_file_path, $conf_ark_file_path);
                } else {
                    DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $wpconfig_filepath, $conf_ark_file_path);
				}
				$this->numFilesAdded++;
			}

			$this->add_installer_files_using_duparchive($archive_filepath, $installer_filepath, $archive_config_filepath);

			$success = true;
		} catch (Exception $ex) {
			DUP_Log::Error("Error adding installer files to archive. ", $ex->getMessage(), Dup_ErrorBehavior::ThrowException);
		}

		return $success;
	}

	private function add_installer_files_using_duparchive($archive_filepath, $installer_filepath, $archive_config_filepath)
	{
		$installer_backup_filename = 'installer-backup.php';
		

		DUP_Log::Info('Adding enhanced installer files to archive using DupArchive');
		DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $installer_filepath, $installer_backup_filename);

		$this->numFilesAdded++;

		$base_installer_directory	 = DUPLICATOR_PLUGIN_PATH.'installer';
		$installer_directory		 = "$base_installer_directory/dup-installer";

		$counts				 = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $installer_directory, $base_installer_directory, true);
		$this->numFilesAdded += $counts->numFilesAdded;
		$this->numDirsAdded	 += $counts->numDirsAdded;

		$archive_config_relative_path = $this->getArchiveTxtFilePath();

		DupArchiveEngine::addRelativeFileToArchiveST($archive_filepath, $archive_config_filepath, $archive_config_relative_path);
		$this->numFilesAdded++;

		// Include dup archive
		$duparchive_lib_directory	 = DUPLICATOR_PLUGIN_PATH.'lib/dup_archive';
		$duparchive_lib_counts		 = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $duparchive_lib_directory, DUPLICATOR_PLUGIN_PATH, true, 'dup-installer/');
		$this->numFilesAdded		 += $duparchive_lib_counts->numFilesAdded;
		$this->numDirsAdded			 += $duparchive_lib_counts->numDirsAdded;

		// Include snaplib
		$snaplib_directory	 = DUPLICATOR_PLUGIN_PATH.'lib/snaplib';
		$snaplib_counts		 = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $snaplib_directory, DUPLICATOR_PLUGIN_PATH, true, 'dup-installer/');
		$this->numFilesAdded += $snaplib_counts->numFilesAdded;
		$this->numDirsAdded	 += $snaplib_counts->numDirsAdded;

		// Include fileops
		$fileops_directory	 = DUPLICATOR_PLUGIN_PATH.'lib/fileops';
		$fileops_counts		 = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $fileops_directory, DUPLICATOR_PLUGIN_PATH, true, 'dup-installer/');
		$this->numFilesAdded += $fileops_counts->numFilesAdded;
		$this->numDirsAdded	 += $fileops_counts->numDirsAdded;

		// Include config
		$config_directory	 = DUPLICATOR_PLUGIN_PATH.'lib/config';
		$config_counts		 = DupArchiveEngine::addDirectoryToArchiveST($archive_filepath, $config_directory, DUPLICATOR_PLUGIN_PATH, true, 'dup-installer/');
		$this->numFilesAdded += $config_counts->numFilesAdded;
		$this->numDirsAdded	 += $fileops_counts->numDirsAdded;
	}

	private function add_extra_files_using_ziparchive($installer_filepath, $scan_filepath, $sql_filepath, $zip_filepath, $archive_config_filepath, $wpconfig_filepath)
	{
		$htaccess_filepath = $this->getHtaccessFilePath();
		$webconfig_filepath	 = duplicator_get_abs_path() . '/web.config';

		$success	 = false;
		$zipArchive	 = new ZipArchive();

		if ($zipArchive->open($zip_filepath, ZIPARCHIVE::CREATE) === TRUE) {
			DUP_Log::Info("Successfully opened zip $zip_filepath");

			if (file_exists($htaccess_filepath)) {
				$htaccess_ark_file_path = $this->getHtaccessArkFilePath();
				DUP_Zip_U::addFileToZipArchive($zipArchive, $htaccess_filepath, $htaccess_ark_file_path, true);
			}

			if (file_exists($webconfig_filepath)) {
				DUP_Zip_U::addFileToZipArchive($zipArchive, $webconfig_filepath, DUPLICATOR_WEBCONFIG_ORIG_FILENAME, true);
			}

			if (!empty($wpconfig_filepath)) {
				$conf_ark_file_path = $this->getWPConfArkFilePath();
				$temp_conf_ark_file_path = $this->getTempWPConfArkFilePath();
                if (copy($wpconfig_filepath, $temp_conf_ark_file_path)) {
                    $this->cleanTempWPConfArkFilePath($temp_conf_ark_file_path);					
					DUP_Zip_U::addFileToZipArchive($zipArchive, $temp_conf_ark_file_path, $conf_ark_file_path, true);
                } else {
                    DUP_Zip_U::addFileToZipArchive($zipArchive, $wpconfig_filepath, $conf_ark_file_path, true);
                }
			}

			$embedded_scan_file_path = $this->getEmbeddedScanFilePath();
			if (DUP_Zip_U::addFileToZipArchive($zipArchive, $scan_filepath, $embedded_scan_file_path, true)) {
				if ($this->add_installer_files_using_zip_archive($zipArchive, $installer_filepath, $archive_config_filepath, true)) {
					DUP_Log::info("Installer files added to archive");
					DUP_Log::info("Added to archive");

					$success = true;
				} else {
					DUP_Log::error("Unable to add enhanced enhanced installer files to archive.", '', Dup_ErrorBehavior::LogOnly);
				}
			} else {
				DUP_Log::error("Unable to add scan file to archive.", '', Dup_ErrorBehavior::LogOnly);
			}

			if ($zipArchive->close() === false) {
				DUP_Log::error("Couldn't close archive when adding extra files.", '');
				$success = false;
			}

			DUP_Log::Info('After ziparchive close when adding installer');
		}

		return $success;
	}

	// Add installer directory to the archive and the archive.cfg
	private function add_installer_files_using_zip_archive(&$zip_archive, $installer_filepath, $archive_config_filepath, $is_compressed)
	{
		$success = false;
		$installer_backup_filename = 'installer-backup.php';

		DUP_Log::Info('Adding enhanced installer files to archive using ZipArchive');

		if (DUP_Zip_U::addFileToZipArchive($zip_archive, $installer_filepath, $installer_backup_filename, true)) {
			DUPLICATOR_PLUGIN_PATH.'installer/';
			$installer_directory = DUPLICATOR_PLUGIN_PATH.'installer/dup-installer';

			if (DUP_Zip_U::addDirWithZipArchive($zip_archive, $installer_directory, true, '', $is_compressed)) {
				$archive_config_local_name = $this->getArchiveTxtFilePath();

				if (DUP_Zip_U::addFileToZipArchive($zip_archive, $archive_config_filepath, $archive_config_local_name, true)) {

					$snaplib_directory = DUPLICATOR_PLUGIN_PATH.'lib/snaplib';
					$config_directory = DUPLICATOR_PLUGIN_PATH . 'lib/config';

					if (DUP_Zip_U::addDirWithZipArchive($zip_archive, $snaplib_directory, true, 'dup-installer/lib/', $is_compressed)
						&& 
						DUP_Zip_U::addDirWithZipArchive($zip_archive, $config_directory, true, 'dup-installer/lib/', $is_compressed)
					) {
						$success = true;
					} else {
						DUP_Log::error("Error adding directory {$snaplib_directory} and {$config_directory} to zipArchive", '', Dup_ErrorBehavior::LogOnly);
					}
				} else {
					DUP_Log::error("Error adding $archive_config_filepath to zipArchive", '', Dup_ErrorBehavior::LogOnly);
				}
			} else {
				DUP_Log::error("Error adding directory $installer_directory to zipArchive", '', Dup_ErrorBehavior::LogOnly);
			}
		} else {
			DUP_Log::error("Error adding backup installer file to zipArchive", '', Dup_ErrorBehavior::LogOnly);
		}

		return $success;
	}

	/**
     * Get .htaccess file path
     * 
     * @return string
     */
    private function getHtaccessFilePath() {
        return duplicator_get_abs_path().'/.htaccess';
    }

    /**
     * Get .htaccss in archive file
     * 
     * @return string
     */
    private function getHtaccessArkFilePath()
    {
        $packageHash         = $this->Package->getPackageHash();
        $htaccessArkFilePath = '.htaccess__'.$packageHash;
        return $htaccessArkFilePath;
    }

	/**
	 * Get wp-config.php file path along with name in archive file
	 */
	private function getWPConfArkFilePath()
	{
		if (DUPLICATOR_INSTALL_SITE_OVERWRITE_ON) {
			$package_hash		 = $this->Package->getPackageHash();
			$conf_ark_file_path	 = 'dup-wp-config-arc__'.$package_hash.'.txt';
		} else {
			$conf_ark_file_path = 'wp-config.php';
		}
		return $conf_ark_file_path;
	}

	/**
     * Get temp wp-config.php file path along with name in temp folder
     */
    private function getTempWPConfArkFilePath() {
        $temp_conf_ark_file_path = DUP_Util::safePath(DUPLICATOR_SSDIR_PATH_TMP).'/'.$this->Package->NameHash.'_wp-config.txt';
        return $temp_conf_ark_file_path;
    }

    /**
     * Clear out sensitive database connection information
     *
     * @param $temp_conf_ark_file_path Temp config file path
     */
    private static function cleanTempWPConfArkFilePath($temp_conf_ark_file_path) {
		if (function_exists('token_get_all')) {
			require_once(DUPLICATOR_PLUGIN_PATH . 'lib/config/class.wp.config.tranformer.php');
			$transformer = new WPConfigTransformer($temp_conf_ark_file_path);
			$constants = array('DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST');
			foreach ($constants as $constant) {
				if ($transformer->exists('constant', $constant)) {
					$transformer->update('constant', $constant, '');
				}
			}
		}
    }

	/**
	 * Get scan.json file path along with name in archive file
	 */
	private function getEmbeddedScanFilePath()
	{
		$package_hash				 = $this->Package->getPackageHash();
		$embedded_scan_ark_file_path = 'dup-installer/dup-scan__'.$package_hash.'.json';
		return $embedded_scan_ark_file_path;
	}

	/**
	 * Get archive.txt file path along with name in archive file
	 */
	private function getArchiveTxtFilePath()
	{
		$package_hash			 = $this->Package->getPackageHash();
		$archive_txt_file_path	 = 'dup-installer/dup-archive__'.$package_hash.'.txt';
		return $archive_txt_file_path;
	}
}