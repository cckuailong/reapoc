<?php
/**
 * Class used to control values about the package meta data
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\ArchiveConfig
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_ArchiveConfig
{
	//READ-ONLY: COMPARE VALUES
	public $created;
	public $version_dup;
	public $version_wp;
	public $version_db;
	public $version_php;
	public $version_os;
	public $dbInfo;

	//GENERAL
	public $secure_on;
	public $secure_pass;
	public $skipscan;
	public $package_name;
	public $package_hash;
	public $package_notes;
	public $wp_tableprefix;
	public $blogname;
    public $wplogin_url;
	public $relative_content_dir;
	public $blogNameSafe;
	public $exportOnlyDB;

	//BASIC DB
	public $dbhost;
	public $dbname;
	public $dbuser;
	public $dbpass;

	//ADV OPTS	
	public $wproot;
	public $url_old;
	public $opts_delete;

	public $debug_mode = false;

	private static $instance = null;

	/**
	 * Loads a usable object from the archive.txt file found in the dup-installer root
	 *
	 * @param string $path		The root path to the location of the server config files
	 *
	 * @return obj	Returns an instance of DUPX_ArchiveConfig
	 */
	public static function getInstance()
	{
		if (self::$instance == null) {
			$config_filepath = realpath(dirname(__FILE__).'/../../dup-archive__'.$GLOBALS['PACKAGE_HASH'].'.txt');
			if (file_exists($config_filepath )) {
				self::$instance = new DUPX_ArchiveConfig();

				$file_contents = file_get_contents($config_filepath);
				$ac_data = json_decode($file_contents);

				foreach ($ac_data as $key => $value) {
					self::$instance->{$key} = $value;
				}

				if (isset($_GET['debug']) && ($_GET['debug'] == 1)) {
					self::$instance->debug_mode = true;
				}
                
 			} else {
				echo "$config_filepath doesn't exist<br/>";
			}
		}

		//Instance Updates:
		self::$instance->blogNameSafe	= preg_replace("/[^A-Za-z0-9?!]/", '', self::$instance->blogname);
		self::$instance->dbhost			= empty(self::$instance->dbhost)       ? 'localhost' : self::$instance->dbhost;

		return self::$instance;
	}

    public function isZipArchive()
    {
        //$extension = strtolower(pathinfo($this->package_name)['extension']);
		$extension = strtolower(pathinfo($this->package_name, PATHINFO_EXTENSION));
        
        return ($extension == 'zip');
    }
}