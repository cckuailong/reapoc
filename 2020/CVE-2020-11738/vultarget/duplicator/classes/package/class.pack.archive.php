<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
// Exit if accessed directly
if (!defined('DUPLICATOR_VERSION')) exit;

require_once (DUPLICATOR_PLUGIN_PATH.'classes/package/duparchive/class.pack.archive.duparchive.php');
require_once (DUPLICATOR_PLUGIN_PATH.'classes/package/class.pack.archive.filters.php');
require_once (DUPLICATOR_PLUGIN_PATH.'classes/package/class.pack.archive.zip.php');
require_once (DUPLICATOR_PLUGIN_PATH.'lib/forceutf8/Encoding.php');

/**
 * Class for handling archive setup and build process
 *
 * Standard: PSR-2 (almost)
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP
 * @subpackage classes/package
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
class DUP_Archive
{
    //PUBLIC
    public $FilterDirs;
    public $FilterFiles;
    public $FilterExts;
    public $FilterDirsAll     = array();
    public $FilterFilesAll    = array();
    public $FilterExtsAll     = array();
    public $FilterOn;
    public $ExportOnlyDB;
    public $File;
    public $Format;
    public $PackDir;
    public $Size              = 0;
    public $Dirs              = array();
    public $Files             = array();

    /**
     *
     * @var DUP_Archive_Filter_Info
     */
    public $FilterInfo        = null;
    public $RecursiveLinks    = array();
    public $file_count        = -1;
    //PROTECTED
    protected $Package;
    private $tmpFilterDirsAll = array();
    private $wpCorePaths      = array();
    private $wpCoreExactPaths = array();

    /**
	 *  Init this object
	 */
	public function __construct($package)
	{
		$this->Package		 = $package;
		$this->FilterOn		 = false;
		$this->ExportOnlyDB	 = false;
		$this->FilterInfo	 = new DUP_Archive_Filter_Info();

		$homePath = duplicator_get_home_path();

		$this->wpCorePaths[] = DUP_Util::safePath("{$homePath}/wp-admin");
		$this->wpCorePaths[] = DUP_Util::safePath(WP_CONTENT_DIR."/uploads");
		$this->wpCorePaths[] = DUP_Util::safePath(WP_CONTENT_DIR."/languages");
		$this->wpCorePaths[] = DUP_Util::safePath(get_theme_root());
		$this->wpCorePaths[] = DUP_Util::safePath("{$homePath}/wp-includes");

		$this->wpCoreExactPaths[]	 = DUP_Util::safePath("{$homePath}");
		$this->wpCoreExactPaths[]	 = DUP_Util::safePath(WP_CONTENT_DIR);
	}

	/**
	 * Builds the archive based on the archive type
	 *
	 * @param obj $package The package object that started this process
	 *
	 * @return null
	 */
	public function build($package, $rethrow_exception = false)
	{
		DUP_LOG::trace("b1");
		$this->Package = $package;
		if (!isset($this->PackDir) && !is_dir($this->PackDir)) throw new Exception("The 'PackDir' property must be a valid directory.");
		if (!isset($this->File)) throw new Exception("A 'File' property must be set.");

		DUP_LOG::trace("b2");
		$completed = false;

		switch ($this->Format) {
			case 'TAR': break;
			case 'TAR-GZIP': break;
			case 'DAF':
				$completed = DUP_DupArchive::create($this, $this->Package->BuildProgress, $this->Package);

				$this->Package->Update();
				break;

			default:
				if (class_exists('ZipArchive')) {
					$this->Format	 = 'ZIP';
					DUP_Zip::create($this, $this->Package->BuildProgress);
					$completed		 = true;
				}
				break;
		}

		DUP_LOG::Trace("Completed build or build thread");

		if ($this->Package->BuildProgress === null) {
			// Zip path
			DUP_LOG::Trace("Completed Zip");
			$storePath	 = "{$this->Package->StorePath}/{$this->File}";
			$this->Size	 = @filesize($storePath);
			$this->Package->setStatus(DUP_PackageStatus::ARCDONE);
		} else if ($completed) {
			// Completed DupArchive path
			DUP_LOG::Trace("Completed DupArchive build");
			if ($this->Package->BuildProgress->failed) {
				DUP_LOG::Trace("Error building DupArchive");
				$this->Package->setStatus(DUP_PackageStatus::ERROR);
			} else {
				$filepath	 = DUP_Util::safePath("{$this->Package->StorePath}/{$this->File}");
				$this->Size	 = @filesize($filepath);
				$this->Package->setStatus(DUP_PackageStatus::ARCDONE);
				DUP_LOG::Trace("Done building archive");
			}
		} else {
			DUP_Log::trace("DupArchive chunk done but package not completed yet");
		}
	}

    /**
     *
     * @return int return  DUP_Archive_Build_Mode
     */
    public function getBuildMode()
    {
        switch ($this->Format) {
            case 'TAR': break;
            case 'TAR-GZIP': break;
            case 'DAF':
                return DUP_Archive_Build_Mode::DupArchive;
            default:
                if (class_exists('ZipArchive')) {
                    return DUP_Archive_Build_Mode::ZipArchive;
                } else {
                    return DUP_Archive_Build_Mode::Unconfigured;
                }
                break;
        }
    }

    /**
	 *  Builds a list of files and directories to be included in the archive
	 *
	 *  Get the directory size recursively, but don't calc the snapshot directory, exclusion directories
	 *  @link http://msdn.microsoft.com/en-us/library/aa365247%28VS.85%29.aspx Windows filename restrictions
	 *
	 *  @return obj Returns a DUP_Archive object
	 */
	public function getScannerData()
	{
		$this->createFilterInfo();
		$rootPath	 = duplicator_get_abs_path();

		$this->RecursiveLinks = array();
		//If the root directory is a filter then skip it all
		if (in_array($this->PackDir, $this->FilterDirsAll) || $this->Package->Archive->ExportOnlyDB) {
			$this->Dirs = array();
		} else {
			$this->Dirs[] = $this->PackDir;

			$this->getFileLists($rootPath);

			if ($this->isOuterWPContentDir()) {
				$this->Dirs[] = WP_CONTENT_DIR;
				$this->getFileLists(WP_CONTENT_DIR);
			}

			$this->setDirFilters();
			$this->setFileFilters();
			$this->setTreeFilters();
		}

		$this->FilterDirsAll	 = array_merge($this->FilterDirsAll, $this->FilterInfo->Dirs->Unreadable);
		$this->FilterFilesAll	 = array_merge($this->FilterFilesAll, $this->FilterInfo->Files->Unreadable);
		sort($this->FilterDirsAll);

		return $this;
	}

	/**
	 * Save any property of this class through reflection
	 *
	 * @param $property     A valid public property in this class
	 * @param $value        The value for the new dynamic property
	 *
	 * @return bool	Returns true if the value has changed.
	 */
	public function saveActiveItem($package, $property, $value)
	{
		$package		 = DUP_Package::getActive();
		$reflectionClass = new ReflectionClass($package->Archive);
		$reflectionClass->getProperty($property)->setValue($package->Archive, $value);
		return update_option(DUP_Package::OPT_ACTIVE, $package);
	}

	/**
	 *  Properly creates the directory filter list that is used for filtering directories
	 *
	 *  @param string $dirs A semi-colon list of dir paths
	 *  /path1_/path/;/path1_/path2/;
	 *
	 *  @returns string A cleaned up list of directory filters
	 */
	public function parseDirectoryFilter($dirs = "")
	{
		$dirs		 = str_replace(array("\n", "\t", "\r"), '', $dirs);
		$filters	 = "";
		$dir_array	 = array_unique(explode(";", $dirs));
		$clean_array = array();
		foreach ($dir_array as $val) {
			if (strlen($val) >= 2) {
				$clean_array[] = DUP_Util::safePath(trim(rtrim($val, "/\\")));
			}
		}

		if (count($clean_array)) {
			$clean_array = array_unique($clean_array);
			sort($clean_array);
			$filters	 = implode(';', $clean_array).';';
		}
		return $filters;
	}

	/**
	 *  Properly creates the file filter list that is used for filtering files
	 *
	 *  @param string $dirs A semi-colon list of dir paths
	 *  /path1_/path/file1.ext;/path1_/path2/file2.ext;
	 *
	 *  @returns string A cleaned up list of file filters
	 */
	public function parseFileFilter($files = "")
	{
		$files		 = str_replace(array("\n", "\t", "\r"), '', $files);
		$filters	 = "";
		$file_array	 = array_unique(explode(";", $files));
		$clean_array = array();
		foreach ($file_array as $val) {
			if (strlen($val) >= 2) {
				$clean_array[] = DUP_Util::safePath(trim(rtrim($val, "/\\")));
			}
		}

		if (count($clean_array)) {
			$clean_array = array_unique($clean_array);
			sort($clean_array);
			$filters	 = implode(';', $clean_array).';';
		}
		return $filters;
	}

	/**
	 *  Properly creates the extension filter list that is used for filtering extensions
	 *
	 *  @param string $dirs A semi-colon list of dir paths
	 *  .jpg;.zip;.gif;
	 *
	 *  @returns string A cleaned up list of extension filters
	 */
	public function parseExtensionFilter($extensions = "")
	{
		$filter_exts = "";
		if (strlen($extensions) >= 1 && $extensions != ";") {
			$filter_exts = str_replace(array(' ', '.'), '', $extensions);
			$filter_exts = str_replace(",", ";", $filter_exts);
			$filter_exts = DUP_Util::appendOnce($extensions, ";");
		}
		return $filter_exts;
	}

	/**
	 * Creates the filter info setup data used for filtering the archive
	 *
	 * @return null
	 */
	private function createFilterInfo()
	{
		//FILTER: INSTANCE ITEMS
		//Add the items generated at create time
		if ($this->FilterOn) {
			$this->FilterInfo->Dirs->Instance	 = array_map('DUP_Util::safePath', explode(";", $this->FilterDirs, -1));
			$this->FilterInfo->Files->Instance	 = array_map('DUP_Util::safePath', explode(";", $this->FilterFiles, -1));
			$this->FilterInfo->Exts->Instance	 = explode(";", $this->FilterExts, -1);
		}

		//FILTER: CORE ITMES
		//Filters Duplicator free packages & All pro local directories
		$wp_root						 = duplicator_get_abs_path();
		$upload_dir						 = wp_upload_dir();
		$upload_dir						 = isset($upload_dir['basedir']) ? basename($upload_dir['basedir']) : 'uploads';
		$wp_content						 = str_replace("\\", "/", WP_CONTENT_DIR);
		$wp_content_upload				 = "{$wp_content}/{$upload_dir}";
		$this->FilterInfo->Dirs->Core	 = array(
			//WP-ROOT
			$wp_root.'/wp-snapshots',
            $wp_root.'/.opcache',
			//WP-CONTENT
			$wp_content.'/backups-dup-pro',
			$wp_content.'/ai1wm-backups',
			$wp_content.'/backupwordpress',
			$wp_content.'/content/cache',
			$wp_content.'/contents/cache',
			$wp_content.'/infinitewp/backups',
			$wp_content.'/managewp/backups',
			$wp_content.'/old-cache',
			$wp_content.'/plugins/all-in-one-wp-migration/storage',
			$wp_content.'/updraft',
			$wp_content.'/wishlist-backup',
			$wp_content.'/wfcache',
			$wp_content.'/cache',
			//WP-CONTENT-UPLOADS
			$wp_content_upload.'/aiowps_backups',
			$wp_content_upload.'/backupbuddy_temp',
			$wp_content_upload.'/backupbuddy_backups',
			$wp_content_upload.'/ithemes-security/backups',
			$wp_content_upload.'/mainwp/backup',
			$wp_content_upload.'/pb_backupbuddy',
			$wp_content_upload.'/snapshots',
			$wp_content_upload.'/sucuri',
			$wp_content_upload.'/wp-clone',
			$wp_content_upload.'/wp_all_backup',
			$wp_content_upload.'/wpbackitup_backups'
		);

		if (class_exists('BackWPup')) {
			$upload_dir = wp_upload_dir(null, false, true);
			$this->FilterInfo->Dirs->Core[] = trailingslashit(str_replace( '\\',
					'/',
					$upload_dir['basedir'])).'backwpup-'.BackWPup::get_plugin_data('hash').'-backups/';
			
			$backwpup_cfg_logfolder = get_site_option('backwpup_cfg_logfolder');
			if (false !== $backwpup_cfg_logfolder) {
				$this->FilterInfo->Dirs->Core[] = $wp_content.'/'.$backwpup_cfg_logfolder;
			}
		}
		$duplicator_global_file_filters_on = apply_filters('duplicator_global_file_filters_on', $GLOBALS['DUPLICATOR_GLOBAL_FILE_FILTERS_ON']);
		if ($GLOBALS['DUPLICATOR_GLOBAL_FILE_FILTERS_ON']) {
			$duplicator_global_file_filters = apply_filters('duplicator_global_file_filters', $GLOBALS['DUPLICATOR_GLOBAL_FILE_FILTERS']);
			$this->FilterInfo->Files->Global = $duplicator_global_file_filters;
		}

		// Prevent adding double wp-content dir conflicts
        if ($this->isOuterWPContentDir()) {
            $default_wp_content_dir_path = DUP_Util::safePath(ABSPATH.'wp-content');
            if (file_exists($default_wp_content_dir_path)) {
                if (is_dir($default_wp_content_dir_path)) {
                    $this->FilterInfo->Dirs->Core[] = $default_wp_content_dir_path;
                } else {
                    $this->FilterInfo->Files->Core[] = $default_wp_content_dir_path;
                }
            }
        }        

		$this->FilterDirsAll	 = array_merge($this->FilterInfo->Dirs->Instance, $this->FilterInfo->Dirs->Core);
		$this->FilterExtsAll	 = array_merge($this->FilterInfo->Exts->Instance, $this->FilterInfo->Exts->Core);
		$this->FilterFilesAll	 = array_merge($this->FilterInfo->Files->Instance, $this->FilterInfo->Files->Global);

		$abs_path = duplicator_get_abs_path();
		$this->FilterFilesAll[]	 = $abs_path.'/.htaccess';
		$this->FilterFilesAll[]	 = $abs_path.'/web.config';
		$this->FilterFilesAll[]	 = $abs_path.'/wp-config.php';
		$this->tmpFilterDirsAll	 = $this->FilterDirsAll;

		//PHP 5 on windows decode patch
		if (!DUP_Util::$PHP7_plus && DUP_Util::isWindows()) {
			foreach ($this->tmpFilterDirsAll as $key => $value) {
				if (preg_match('/[^\x20-\x7f]/', $value)) {
					$this->tmpFilterDirsAll[$key] = utf8_decode($value);
				}
			}
		}
	}

	/**
     * Get All Directories then filter
     *
     * @return null
     */
    private function setDirFilters()
    {
        $this->FilterInfo->Dirs->Warning    = array();
        $this->FilterInfo->Dirs->Unreadable = array();
        $this->FilterInfo->Dirs->AddonSites = array();
        $skip_archive_scan                  = DUP_Settings::Get('skip_archive_scan');

        $utf8_key_list  = array();
        $unset_key_list = array();

        //Filter directories invalid test checks for:
        // - characters over 250
        // - invlaid characters
        // - empty string
        // - directories ending with period (Windows incompatable)
        foreach ($this->Dirs as $key => $val) {
            $name = basename($val);

            //Dir is not readble remove flag for removal
            if (!is_readable($this->Dirs[$key])) {
                $unset_key_list[]                     = $key;
                $this->FilterInfo->Dirs->Unreadable[] = DUP_Encoding::toUTF8($val);
            }

            if (!$skip_archive_scan) {
                //Locate invalid directories and warn
                $invalid_test = strlen($val) > PHP_MAXPATHLEN || preg_match('/(\/|\*|\?|\>|\<|\:|\\|\|)/', $name) || trim($name) == '' || (strrpos($name, '.') == strlen($name) - 1 && substr($name, -1)
                    == '.') || preg_match('/[^\x20-\x7f]/', $name);

                if ($invalid_test) {
                    $utf8_key_list[]                   = $key;
                    $this->FilterInfo->Dirs->Warning[] = DUP_Encoding::toUTF8($val);
                }
            }

            //Check for other WordPress installs
            if ($name === 'wp-admin') {
                $parent_dir = realpath(dirname($this->Dirs[$key]));
                if ($parent_dir != realpath(duplicator_get_abs_path())) {
                    if (file_exists("$parent_dir/wp-includes")) {
                        if (file_exists("$parent_dir/wp-config.php")) {
                            // Ensure we aren't adding any critical directories
                            $parent_name = basename($parent_dir);
                            if (($parent_name != 'wp-includes') && ($parent_name != 'wp-content') && ($parent_name != 'wp-admin')) {
                                $this->FilterInfo->Dirs->AddonSites[] = str_replace("\\", '/', $parent_dir);
                            }
                        }
                    }
                }
            }
        }

        //Try to repair utf8 paths
        foreach ($utf8_key_list as $key) {
            $this->Dirs[$key] = DUP_Encoding::toUTF8($this->Dirs[$key]);
        }

        //Remove unreadable items outside of main loop for performance
        if (count($unset_key_list)) {
            foreach ($unset_key_list as $key) {
                unset($this->Dirs[$key]);
            }
            $this->Dirs = array_values($this->Dirs);
        }
    }

    /**
     * Get all files and filter out error prone subsets
     *
     * @return null
     */
    private function setFileFilters()
    {
        //Init for each call to prevent concatination from stored entity objects
        $this->Size                          = 0;
        $this->FilterInfo->Files->Size       = array();
        $this->FilterInfo->Files->Warning    = array();
        $this->FilterInfo->Files->Unreadable = array();
        $skip_archive_scan                   = DUP_Settings::Get('skip_archive_scan');

        $utf8_key_list  = array();
        $unset_key_list = array();

        $wpconfig_filepath = $this->getWPConfigFilePath();
        if (!is_readable($wpconfig_filepath)) {
            $this->FilterInfo->Files->Unreadable[] = $wpconfig_filepath;
        }

        foreach ($this->Files as $key => $filePath) {

            $fileName = basename($filePath);

            if (!is_readable($filePath)) {
                $unset_key_list[]                      = $key;
                $this->FilterInfo->Files->Unreadable[] = $filePath;
                continue;
            }

            $fileSize   = @filesize($filePath);
            $fileSize   = empty($fileSize) ? 0 : $fileSize;
            $this->Size += $fileSize;

            if (!$skip_archive_scan) {
                $invalid_test = strlen($filePath) > PHP_MAXPATHLEN || preg_match('/(\/|\*|\?|\>|\<|\:|\\|\|)/', $fileName) || trim($fileName) == "" || preg_match('/[^\x20-\x7f]/', $fileName);

                if ($invalid_test) {
                    $utf8_key_list[]                    = $key;
                    $filePath                           = DUP_Encoding::toUTF8($filePath);
                    $fileName                           = basename($filePath);
                    $this->FilterInfo->Files->Warning[] = array(
                        'name' => $fileName,
                        'dir' => pathinfo($filePath, PATHINFO_DIRNAME),
                        'path' => $filePath);
                }

                if ($fileSize > DUPLICATOR_SCAN_WARNFILESIZE) {
                    //$ext = pathinfo($filePath, PATHINFO_EXTENSION);
                    $this->FilterInfo->Files->Size[] = array(
                        'ubytes' => $fileSize,
                        'bytes' => DUP_Util::byteSize($fileSize, 0),
                        'name' => $fileName,
                        'dir' => pathinfo($filePath, PATHINFO_DIRNAME),
                        'path' => $filePath);
                }
            }
        }

        //Try to repair utf8 paths
        foreach ($utf8_key_list as $key) {
            $this->Files[$key] = DUP_Encoding::toUTF8($this->Files[$key]);
        }

        //Remove unreadable items outside of main loop for performance
        if (count($unset_key_list)) {
            foreach ($unset_key_list as $key) {
                unset($this->Files[$key]);
            }
            $this->Files = array_values($this->Files);
        }
    }

    /**
	 * Recursive function to get all directories in a wp install
	 *
	 * @notes:
	 * 	Older PHP logic which is more stable on older version of PHP
	 * 	NOTE RecursiveIteratorIterator is problematic on some systems issues include:
	 *  - error 'too many files open' for recursion
	 *  - $file->getExtension() is not reliable as it silently fails at least in php 5.2.17
	 *  - issues with when a file has a permission such as 705 and trying to get info (had to fallback to pathinfo)
	 *  - basic conclusion wait on the SPL libs until after php 5.4 is a requirments
	 *  - tight recursive loop use caution for speed
	 *
	 * @return array	Returns an array of directories to include in the archive
	 */
	private function getFileLists($path) {
		$handle = @opendir($path);

		if ($handle) {
			while (($file = readdir($handle)) !== false) {

				if ($file == '.' || $file == '..') {
					continue;
				}

				$fullPath = str_replace("\\", '/', "{$path}/{$file}");

				// @todo: Don't leave it like this. Convert into an option on the package to not follow symbolic links
				// if (is_dir($fullPath) && (is_link($fullPath) == false))
				if (is_dir($fullPath)) {

                    $add = true;
                    if (!is_link($fullPath)){
                        foreach ($this->tmpFilterDirsAll as $key => $val) {
                            $trimmedFilterDir = rtrim($val, '/');
                            if ($fullPath == $trimmedFilterDir || strpos($fullPath, $trimmedFilterDir . '/') !== false) {
                                $add = false;
                                unset($this->tmpFilterDirsAll[$key]);
                                break;
                            }
                        }
                    } else{
                        //Convert relative path of link to absolute path
                        chdir($fullPath);
						$link_path = str_replace("\\", '/', realpath(readlink($fullPath)));
                        chdir(dirname(__FILE__));

                        $link_pos = strpos($fullPath,$link_path);
                        if($link_pos === 0 && (strlen($link_path) <  strlen($fullPath))){
                            $add = false;
                            $this->RecursiveLinks[] = $fullPath;
                            $this->FilterDirsAll[] = $fullPath;
                        } else {
							foreach ($this->tmpFilterDirsAll as $key => $val) {
								$trimmedFilterDir = rtrim($val, '/');
								if ($fullPath == $trimmedFilterDir || strpos($fullPath, $trimmedFilterDir . '/') !== false) {
									$add = false;
									unset($this->tmpFilterDirsAll[$key]);
									break;
								}
							}
						}
                    }

                    if ($add) {
                        $this->getFileLists($fullPath);
                        $this->Dirs[] = $fullPath;
                    }
				} else {
					if ( ! (in_array(pathinfo($file, PATHINFO_EXTENSION), $this->FilterExtsAll)
						|| in_array($fullPath, $this->FilterFilesAll)
						|| in_array($file, $this->FilterFilesAll))) {
						$this->Files[] = $fullPath;
					}
				}
			}
			closedir($handle);
		}
		return $this->Dirs;
	}

	/**
	 *  Builds a tree for both file size warnings and name check warnings
	 *  The trees are used to apply filters from the scan screen
	 *
	 *  @return null
	 */
	private function setTreeFilters()
	{
		//-------------------------
		//SIZE TREE
		//BUILD: File Size tree
		$dir_group = DUP_Util::array_group_by($this->FilterInfo->Files->Size, "dir");
		ksort($dir_group);
		foreach ($dir_group as $dir => $files) {
			$sum = 0;
			foreach ($files as $key => $value) {
				$sum += $value['ubytes'];
			}

			//Locate core paths, wp-admin, wp-includes, etc.
			$iscore = 0;
			foreach ($this->wpCorePaths as $core_dir) {
				if (strpos(DUP_Util::safePath($dir), DUP_Util::safePath($core_dir)) !== false) {
					$iscore = 1;
					break;
				}
			}
			// Check root and content exact dir
			if (!$iscore) {
				if (in_array($dir, $this->wpCoreExactPaths)) {
					$iscore = 1;
				}
			}

			$this->FilterInfo->TreeSize[] = array(
				'size' => DUP_Util::byteSize($sum, 0),
				'dir' => $dir,
				'sdir' => str_replace(duplicator_get_abs_path(), '/', $dir),
				'iscore' => $iscore,
				'files' => $files
			);
		}

		//-------------------------
		//NAME TREE
		//BUILD: Warning tree for file names
		$dir_group = DUP_Util::array_group_by($this->FilterInfo->Files->Warning, "dir");
		ksort($dir_group);
		foreach ($dir_group as $dir => $files) {

			//Locate core paths, wp-admin, wp-includes, etc.
			$iscore = 0;
			foreach ($this->wpCorePaths as $core_dir) {
				if (strpos($dir, $core_dir) !== false) {
					$iscore = 1;
					break;
				}
			}
			// Check root and content exact dir
			if (!$iscore) {
				if (in_array($dir, $this->wpCoreExactPaths)) {
					$iscore = 1;
				}
			}

			$this->FilterInfo->TreeWarning[] = array(
				'dir' => $dir,
				'sdir' => str_replace(duplicator_get_abs_path(), '/', $dir),
				'iscore' => $iscore,
				'count' => count($files),
				'files' => $files);
		}

		//BUILD: Warning tree for dir names
		foreach ($this->FilterInfo->Dirs->Warning as $dir) {
			$add_dir = true;
			foreach ($this->FilterInfo->TreeWarning as $key => $value) {
				if ($value['dir'] == $dir) {
					$add_dir = false;
					break;
				}
			}
			if ($add_dir) {

				//Locate core paths, wp-admin, wp-includes, etc.
				$iscore = 0;
				foreach ($this->wpCorePaths as $core_dir) {
					if (strpos(DUP_Util::safePath($dir), DUP_Util::safePath($core_dir)) !== false) {
						$iscore = 1;
						break;
					}
				}
				// Check root and content exact dir
				if (!$iscore) {
					if (in_array($dir, $this->wpCoreExactPaths)) {
						$iscore = 1;
					}
				}

				$this->FilterInfo->TreeWarning[] = array(
					'dir' => $dir,
					'sdir' => str_replace(duplicator_get_abs_path(), '/', $dir),
					'iscore' => $iscore,
					'count' => 0);
			}
		}

		function _sortDir($a, $b)
		{
			return strcmp($a["dir"], $b["dir"]);
		}
		usort($this->FilterInfo->TreeWarning, "_sortDir");
	}

	public function getWPConfigFilePath()
	{
		$wpconfig_filepath = '';
		$abs_path = duplicator_get_abs_path();
		if (file_exists($abs_path.'/wp-config.php')) {
			$wpconfig_filepath = $abs_path.'/wp-config.php';
		} elseif (@file_exists(dirname($abs_path).'/wp-config.php') && !@file_exists(dirname($abs_path).'/wp-settings.php')) {
			$wpconfig_filepath = dirname($abs_path).'/wp-config.php';
		}
		return $wpconfig_filepath;
	}

	public function isOuterWPContentDir()
	{
		if (!isset($this->isOuterWPContentDir)) {
			$abspath_normalize			 = wp_normalize_path(ABSPATH);
			$wp_content_dir_normalize	 = wp_normalize_path(WP_CONTENT_DIR);
			if (0 !== strpos($wp_content_dir_normalize, $abspath_normalize)) {
				$this->isOuterWPContentDir = true;
			} else {
				$this->isOuterWPContentDir = false;
			}
		}
		return $this->isOuterWPContentDir;
	}

	public function wpContentDirNormalizePath()
	{
		if (!isset($this->wpContentDirNormalizePath)) {
			$this->wpContentDirNormalizePath = trailingslashit(wp_normalize_path(WP_CONTENT_DIR));
		}
		return $this->wpContentDirNormalizePath;
	}

	public function getLocalDirPath($dir, $basePath = '')
	{
		$isOuterWPContentDir		 = $this->isOuterWPContentDir();
		$wpContentDirNormalizePath	 = $this->wpContentDirNormalizePath();
		$compressDir				 = rtrim(wp_normalize_path(DUP_Util::safePath($this->PackDir)), '/');

		$dir = trailingslashit(wp_normalize_path($dir));
		if ($isOuterWPContentDir && 0 === strpos($dir, $wpContentDirNormalizePath)) {
			$newWPContentDirPath = empty($basePath) ? 'wp-content/' : $basePath.'wp-content/';
			$emptyDir			 = ltrim(str_replace($wpContentDirNormalizePath, $newWPContentDirPath, $dir), '/');
		} else {
            $emptyDir = ltrim($basePath.preg_replace('/^'.preg_quote($compressDir, '/').'(.*)/m', '$1', $dir), '/');
		}
		return $emptyDir;
	}

	public function getLocalFilePath($file, $basePath = '')
	{
		$isOuterWPContentDir		 = $this->isOuterWPContentDir();
		$wpContentDirNormalizePath	 = $this->wpContentDirNormalizePath();
		$compressDir				 = rtrim(wp_normalize_path(DUP_Util::safePath($this->PackDir)), '/');

		$file = wp_normalize_path($file);
		if ($isOuterWPContentDir && 0 === strpos($file, $wpContentDirNormalizePath)) {
			$newWPContentDirPath = empty($basePath) ? 'wp-content/' : $basePath.'wp-content/';
			$localFileName		 = ltrim(str_replace($wpContentDirNormalizePath, $newWPContentDirPath, $file), '/');
		} else {            
			$localFileName = ltrim($basePath.preg_replace('/^'.preg_quote($compressDir, '/').'(.*)/m', '$1', $file), '/');
		}
		return $localFileName;
	}
}