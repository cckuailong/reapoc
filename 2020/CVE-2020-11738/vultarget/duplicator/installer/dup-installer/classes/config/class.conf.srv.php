<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Class for server type enum setup
 * .htaccess, web.config and .user.ini
 *
 */
abstract class DUPX_ServerConfigTypes
{
    const Apache		= 0;
    const IIS			= 1;
    const WordFence     = 2;
}


/**
 * Class used to update and edit web server configuration files
 * for .htaccess, web.config and .user.ini
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 */
class DUPX_ServerConfig
{
	protected static $fileHash;
	protected static $timeStamp;
	protected static $confFileApache;
	protected static $confFileApacheOrig;
	protected static $confFileIIS;
	protected static $confFileIISOrig;
	protected static $confFileWordFence;
	protected static $configMode;
	protected static $newSiteURL;
	protected static $rootPath;

	/**
	 *  Setup this classes properties
	 */
	public static function init()
	{
		self::$fileHash				= date("ymdHis") . '-' . uniqid();
		self::$timeStamp			= date("Y-m-d H:i:s");
		self::$rootPath				= "{$GLOBALS['DUPX_ROOT']}";
		self::$confFileApache		= "{$GLOBALS['DUPX_ROOT']}/.htaccess";
		self::$confFileApacheOrig	= "{$GLOBALS['DUPX_ROOT']}/.htaccess__".$GLOBALS['DUPX_AC']->package_hash;
		self::$confFileIIS			= "{$GLOBALS['DUPX_ROOT']}/web.config";
		self::$confFileIISOrig		= "{$GLOBALS['DUPX_ROOT']}/web.config.orig";
		self::$confFileWordFence	= "{$GLOBALS['DUPX_ROOT']}/.user.ini";
		self::$configMode           = isset($_POST['config_mode']) ? DUPX_U::sanitize_text_field($_POST['config_mode'])  : null;
		self::$newSiteURL           = isset($_POST['url_new']) ? DUPX_U::sanitize_text_field($_POST['url_new']) : null;
	}

	/**
	 * After the archive is extracted run setup checks
	 *
	 * @return null
	 */
	public static function afterExtractionSetup()
	{
		if (self::$configMode  != 'IGNORE') {
			//WORDFENCE: Only the WordFence file needs to be removed
			//completly from setup to avoid any issues
			self::removeFile(self::$confFileWordFence, DUPX_ServerConfigTypes::WordFence);
		} else {
			DUPX_Log::info("** CONFIG FILE SET TO IGNORE ALL CHANGES **");
		}
	}

	/**
	 * Before the archive is extracted run a series of back and remove checks
	 * This is for existing config files that may exist before the ones in the
	 * archive are extracted.
	 *
	 * @return void
	 */
	public static function beforeExtractionSetup()
	{
		if (self::$configMode != 'IGNORE') {
			//---------------------
			//APACHE
			if (self::createBackup(self::$confFileApache, DUPX_ServerConfigTypes::Apache))
				self::removeFile(self::$confFileApache, DUPX_ServerConfigTypes::Apache);

			//---------------------
			//MICROSOFT IIS
			if (self::createBackup(self::$confFileIIS, DUPX_ServerConfigTypes::IIS))
				 self::removeFile(self::$confFileIIS, DUPX_ServerConfigTypes::IIS);

			//---------------------
			//WORDFENCE
			if (self::createBackup(self::$confFileWordFence, DUPX_ServerConfigTypes::WordFence))
				 self::removeFile(self::$confFileWordFence, DUPX_ServerConfigTypes::WordFence);
		}
	}

    /**
     * Copies the code in .htaccess__[HASH] and web.config.orig
	 * to .htaccess and web.config
     *
	 * @return void
     */
	public static function renameOrigConfigs()
	{
		//APACHE
		if(rename(self::$confFileApacheOrig, self::$confFileApache)){
			DUPX_Log::info("\n- PASS: The orginal .htaccess__[HASH] was renamed");
		} else {
			DUPX_Log::info("\n- WARN: The orginal .htaccess__[HASH] was NOT renamed");
		}

		//IIS
		if(rename(self::$confFileIISOrig, self::$confFileIIS)){
			DUPX_Log::info("\n- PASS: The orginal .htaccess__[HASH] was renamed");
		} else {
			DUPX_Log::info("\n- WARN: The orginal .htaccess__[HASH] was NOT renamed");
		}
    }

	 /**
     * Creates the new config file
     *
	 * @return void
     */
	public static function createNewConfigs()
	{
		$config_made = false;

		//APACHE
		if(file_exists(self::$confFileApacheOrig)){
			self::createNewApacheConfig();
			self::removeFile(self::$confFileApacheOrig, DUPX_ServerConfigTypes::Apache);
			$config_made = true;
		}

		if(file_exists(self::$confFileIISOrig)){
			self::createNewIISConfig();
			self::removeFile(self::$confFileIISOrig, DUPX_ServerConfigTypes::IIS);
			$config_made = true;
		}

		//No config was made so try to guess which one
		//95% of the time it will be Apache
		if (! $config_made) {
			if (DUPX_Server::isIISRunning()) {
				self::createNewIISConfig();
			} else {
				self::createNewApacheConfig();
			}
		}
    }

	/**
	 * Sets up the web config file based on the inputs from the installer forms.
	 *
	 * @return void
	 */
	private static function createNewApacheConfig()
	{
		$timestamp   = self::$timeStamp;
		$newdata	 = parse_url(self::$newSiteURL);
		$newpath	 = DUPX_U::addSlash(isset($newdata['path']) ? $newdata['path'] : "");
		$update_msg  = "#This Apache config file was created by Duplicator Installer on {$timestamp}.\n";
		$update_msg .= "#The original can be found in archived file with the name .htaccess__[HASH]\n";

        $tmp_htaccess = <<<HTACCESS
{$update_msg}
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . {$newpath}index.php [L]
</IfModule>
# END WordPress
HTACCESS;

		if (@file_put_contents(self::$confFileApache, $tmp_htaccess) === FALSE) {
			DUPX_Log::info("- WARN: Unable to create the .htaccess file! Please check the permission on the root directory and make sure the .htaccess exists.");
		} else {
			DUPX_Log::info("- PASS: Successfully created a new .htaccess file.");
			@chmod(self::$confFileApache, 0644);
		}
    }

	/**
	 * Sets up the web config file based on the inputs from the installer forms.
	 *
	 * @return void
	 */
	private static function createNewIISConfig()
	{
		$timestamp = self::$timeStamp;
		$xml_contents  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml_contents .= "<!-- This new IIS config file was created by Duplicator Installer on {$timestamp}.\n"
					  .  "The original can be found in archived file with the name web.config.orig -->\n";
		$xml_contents .=  "<configuration></configuration>\n";
		@file_put_contents(self::$confFileIIS, $xml_contents);
    }
	
	/**
	 * Creates a copy of any existing file and hashes it with a .bak extension
	 *
	 * @param string $file_path					The full path of the config file
	 * @param DUPX_ServerConfigTypes $type		A valid DUPX_ServerConfigTypes
	 *
	 * @return bool		Returns true if the file was backed-up.
	 */
	private static function createBackup($file_path, $type)
	{
		$status		= false;
		$file_name  = DupLiteSnapLibIOU::getFileName($file_path);
		$hash		= self::$fileHash;
		$source = self::getTypeName($type);
		if (is_file($file_path)) {
			if (! self::backupExists($type)) {
				$status = copy($file_path, "{$file_path}-{$hash}-duplicator.bak");
				$status ? DUPX_Log::info("- PASS: {$source} '{$file_name}' backed-up to {$file_name}-{$hash}-duplicator.bak")
						: DUPX_Log::info("- WARN: {$source} '{$file_name}' unable to create backup copy, a possible permission error?");
			}
		} else {
			DUPX_Log::info("- PASS: {$source} '{$file_name}' not found - no backup needed.");
		}

		return $status;
	}

	/**
	 * Removes the specified file
	 *
	 * @param string $file_path					The full path of the config file
	 * @param DUPX_ServerConfigTypes $type		A valid DUPX_ServerConfigTypes
	 *
	 * @return bool		Returns true if the file was removed
	 */
	private static function removeFile($file_path, $type)
	{
		$status = false;
		if (is_file($file_path)) {
			$source		= self::getTypeName($type);
			$file_name  = DupLiteSnapLibIOU::getFileName($file_path);
			$status = @unlink($file_path);
			if ($status === FALSE) {
				@chmod($file_path, 0777);
				$status = @unlink($file_path);
			}
			$status ? DUPX_Log::info("- PASS: Existing {$source} '{$file_name}' was removed")
					: DUPX_Log::info("- WARN: Existing {$source} '{$file_path}' not removed, a possible permission error?");
		}
		return $status;
	}

	/**
	 * Check if a backup file already exists
	 *
	 * @param DUPX_ServerConfigTypes $type		A valid DUPX_ServerConfigTypes
	 *
	 * @return bool		Returns true if the file was removed
	 */
	private static function backupExists($type)
	{
		$pattern = 'unknown-duplicator-type-set';

		switch ($type) {
			case DUPX_ServerConfigTypes::Apache:
				$pattern = '/.htaccess-.*-duplicator.bak/';
				break;
			case DUPX_ServerConfigTypes::IIS:
				$pattern = '/web.config-.*-duplicator.bak/';
				break;
			case DUPX_ServerConfigTypes::WordFence:
				$pattern = '/.user.ini-.*-duplicator.bak/';
				break;
		}

		if (is_dir(self::$rootPath)) {
            $dir = new DirectoryIterator(self::$rootPath);
            foreach ($dir as $file) {
                if ($file->isDot()) {
                    continue;
                }
                if ($file->isFile()) {
                    $name = $file->getFilename();
                    if (strpos($name, '-duplicator.bak')) {
                        if (preg_match($pattern, $name)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
	}

	/**
	 * Gets the friendly type name
	 *
	 * @param DUPX_ServerConfigTypes $type		A valid DUPX_ServerConfigTypes
	 *
	 * @return string		The friendly enum name
	 */
	private static function getTypeName($type)
	{
		switch ($type) {
			case DUPX_ServerConfigTypes::Apache:
				return 'Apache';
				break;
			case DUPX_ServerConfigTypes::IIS:
				return 'Microsoft IIS';
				break;
			case DUPX_ServerConfigTypes::WordFence:
				return 'WordFence';
				break;			

			default:
				throw new Exception('The param $type must be of type DUPX_ServerConfigTypes');
				break;
		}
	}

    /**
     * Get AddHadler line from existing WP .htaccess file
     *
     * @param $path string root path
     * @return string
     */
    private static function getOldHtaccessAddhandlerLine($path)
    {
        $backupHtaccessPath = $path.'/.htaccess-'.$GLOBALS['DUPX_AC']->package_hash.'.orig';
        if (file_exists($backupHtaccessPath)) {
            $htaccessContent = file_get_contents($backupHtaccessPath);
            if (!empty($htaccessContent)) {
                // match and trim non commented line  "AddHandler application/x-httpd-XXXX .php" case insenstive
                $re      = '/^[\s\t]*[^#]?[\s\t]*(AddHandler[\s\t]+.+\.php[ \t]?.*?)[\s\t]*$/mi';
                $matches = array();
                if (preg_match($re, $htaccessContent, $matches)) {
                    return "\n".$matches[1];
                }
            }
        }
        return '';
    }

    /**
     * Copies the code in .htaccess__[HASH] to .htaccess
     *
     * @param $path					The root path to the location of the server config files
     * @param $new_htaccess_name	New name of htaccess (either .htaccess or a backup name)
     *
     * @return bool					Returns true if the .htaccess file was retained successfully
     */
    public static function renameHtaccess($path, $new_htaccess_name)
    {
        $status = false;

        if (!@rename($path.'/.htaccess__'.$GLOBALS['DUPX_AC']->package_hash, $path.'/'.$new_htaccess_name)) {
            $status = true;
        }

        return $status;
    }

    /**
	 * Sets up the web config file based on the inputs from the installer forms.
	 *
	 * @param int $mu_mode		Is this site a specific multi-site mode
	 * @param object $dbh		The database connection handle for this request
	 * @param string $path		The path to the config file
	 *
	 * @return null
	 */
	public static function setup($mu_mode, $mu_generation, $dbh, $path)
    {
        DUPX_Log::info("\nWEB SERVER CONFIGURATION FILE UPDATED:");

        $timestamp    = date("Y-m-d H:i:s");
        $post_url_new = DUPX_U::sanitize_text_field($_POST['url_new']);
        $newdata      = parse_url($post_url_new);
        $newpath      = DUPX_U::addSlash(isset($newdata['path']) ? $newdata['path'] : "");
        $update_msg   = "# This file was updated by Duplicator Pro on {$timestamp}.\n";
        $update_msg   .= (file_exists("{$path}/.htaccess")) ? "# See .htaccess__[HASH] for the .htaccess original file." : "";
        $update_msg   .= self::getOldHtaccessAddhandlerLine($path);


        // no multisite
        $empty_htaccess = false;
        $query_result   = @mysqli_query($dbh, "SELECT option_value FROM `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` WHERE option_name = 'permalink_structure' ");

        if ($query_result) {
            $row = @mysqli_fetch_array($query_result);
            if ($row != null) {
                $permalink_structure = trim($row[0]);
                $empty_htaccess      = empty($permalink_structure);
            }
        }


        if ($empty_htaccess) {
            $tmp_htaccess = '';
        } else {
            $tmp_htaccess = <<<HTACCESS
{$update_msg}
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . {$newpath}index.php [L]
</IfModule>
# END WordPress
HTACCESS;
            DUPX_Log::info("- Preparing .htaccess file with basic setup.");
        }

        if (@file_put_contents("{$path}/.htaccess", $tmp_htaccess) === FALSE) {
            DUPX_Log::info("WARNING: Unable to update the .htaccess file! Please check the permission on the root directory and make sure the .htaccess exists.");
        } else {
            DUPX_Log::info("- Successfully updated the .htaccess file setting.");
        }
        @chmod("{$path}/.htaccess", 0644);
    }
}
