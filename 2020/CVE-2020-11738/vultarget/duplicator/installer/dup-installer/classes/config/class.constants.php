<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Class used to group all global constants
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Constants
 *
 */
class DUPX_Constants
{
    const DEFAULT_MAX_STRLEN_SERIALIZED_CHECK_IN_M = 4; // 0 no limit

    /**
     *
     * @var int
     */
    public static $maxStrlenSerializeCheck = self::DEFAULT_MAX_STRLEN_SERIALIZED_CHECK;

	/**
	 * Init method used to auto initialize the global params
	 *
	 * @return null
	 */
	public static function init()
	{
		$dup_installer_dir_absolute_path = dirname(dirname(dirname(__FILE__)));
		$config_files = glob($dup_installer_dir_absolute_path.'/dup-archive__*.txt');
		$config_file_absolute_path = array_pop($config_files);
		$config_file_name = basename($config_file_absolute_path, '.txt');
		$archive_prefix_length = strlen('dup-archive__');
		$GLOBALS['PACKAGE_HASH'] = substr($config_file_name, $archive_prefix_length); 

		$GLOBALS['BOOTLOADER_NAME'] = isset($_POST['bootloader'])  ? $_POST['bootloader'] : 'installer.php';
        $GLOBALS['FW_PACKAGE_PATH'] = isset($_POST['archive'])     ? $_POST['archive']    : null; // '%fwrite_package_name%';
        $GLOBALS['FW_ENCODED_PACKAGE_PATH'] = urlencode($GLOBALS['FW_PACKAGE_PATH']);
        $GLOBALS['FW_PACKAGE_NAME'] = basename($GLOBALS['FW_PACKAGE_PATH']);

		$GLOBALS['FAQ_URL'] = 'https://snapcreek.com/duplicator/docs/faqs-tech';

		//DATABASE SETUP: all time in seconds
		//max_allowed_packet: max value 1073741824 (1268MB) see my.ini
		$GLOBALS['DB_MAX_TIME'] = 5000;
        $GLOBALS['DATABASE_PAGE_SIZE'] = 3500;
		$GLOBALS['DB_MAX_PACKETS'] = 268435456;
		$GLOBALS['DBCHARSET_DEFAULT'] = 'utf8';
		$GLOBALS['DBCOLLATE_DEFAULT'] = 'utf8_general_ci';
		$GLOBALS['DB_RENAME_PREFIX'] = 'x-bak-' . @date("dHis") . '__';

        if (!defined('MAX_SITES_TO_DEFAULT_ENABLE_CORSS_SEARCH')) {
            define('MAX_SITES_TO_DEFAULT_ENABLE_CORSS_SEARCH',  10);
        }

		//UPDATE TABLE SETTINGS
		$GLOBALS['REPLACE_LIST'] = array();
		$GLOBALS['DEBUG_JS'] = false;

		//PHP INI SETUP: all time in seconds
		if (!$GLOBALS['DUPX_ENFORCE_PHP_INI']) {
			if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('mysql.connect_timeout'))@ini_set('mysql.connect_timeout', '5000');
			if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('memory_limit'))  @ini_set('memory_limit', DUPLICATOR_PHP_MAX_MEMORY);
			if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))  @ini_set("max_execution_time", '5000');
			if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('max_input_time'))  @ini_set("max_input_time", '5000');
			if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('default_socket_timeout'))  @ini_set('default_socket_timeout', '5000');
			@set_time_limit(0);
		}

		//CONSTANTS
		define("DUPLICATOR_INIT", 1);
		define("DUPLICATOR_SSDIR_NAME", 'wp-snapshots-dup-pro');  //This should match DUPLICATOR_SSDIR_NAME in duplicator.php

		//SHARED POST PARMS
		$_GET['debug'] = isset($_GET['debug']) ? true : false;
		$_GET['basic'] = isset($_GET['basic']) ? true : false;
		$_POST['view'] = isset($_POST['view']) ? $_POST['view'] : "step1";

		//GLOBALS
		$GLOBALS["VIEW"]				= isset($_GET["view"]) ? $_GET["view"] : $_POST["view"];
		$GLOBALS['INIT']                = ($GLOBALS['VIEW'] === 'secure');
 		$GLOBALS["LOG_FILE_NAME"]		= "dup-installer-log__{$GLOBALS['PACKAGE_HASH']}.txt";
		$GLOBALS['SEPERATOR1']			= str_repeat("********", 10);
		$GLOBALS['LOGGING']				= isset($_POST['logging']) ? $_POST['logging'] : 1;
		$GLOBALS['CURRENT_ROOT_PATH']	= str_replace('\\', '/', realpath(dirname(__FILE__) . "/../../../"));
		$GLOBALS['LOG_FILE_PATH']		= $GLOBALS['DUPX_INIT'] . '/' . $GLOBALS["LOG_FILE_NAME"];
        $GLOBALS["NOTICES_FILE_NAME"]	= "dup-installer-notices__{$GLOBALS['PACKAGE_HASH']}.json";
        $GLOBALS["NOTICES_FILE_PATH"]	= $GLOBALS['DUPX_INIT'] . '/' . $GLOBALS["NOTICES_FILE_NAME"];
		$GLOBALS['CHOWN_ROOT_PATH']		= DupLiteSnapLibIOU::chmod("{$GLOBALS['CURRENT_ROOT_PATH']}", 'u+rwx');
		$GLOBALS['CHOWN_LOG_PATH']		= DupLiteSnapLibIOU::chmod("{$GLOBALS['LOG_FILE_PATH']}", 'u+rw');
        $GLOBALS['CHOWN_NOTICES_PATH']	= DupLiteSnapLibIOU::chmod("{$GLOBALS['NOTICES_FILE_PATH']}", 'u+rw');
		$GLOBALS['URL_SSL']				= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? true : false;
		$GLOBALS['URL_PATH']			= ($GLOBALS['URL_SSL']) ? "https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}" : "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
		$GLOBALS['PHP_MEMORY_LIMIT']	= ini_get('memory_limit') === false ? 'n/a' : ini_get('memory_limit');
		$GLOBALS['PHP_SUHOSIN_ON']		= extension_loaded('suhosin') ? 'enabled' : 'disabled';

        /**
         * Inizialize notices manager and load file
         */
        $noticesManager = DUPX_NOTICE_MANAGER::getInstance();

        //Restart log if user starts from step 1
        if ($GLOBALS["VIEW"] == "step1") {
            $GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_PATH'], "w+");
            $noticesManager->resetNotices();
        } else {
            $GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_PATH'], "a+");
        }

		// for ngrok url and Local by Flywheel Live URL
		if (isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
			$host = $_SERVER['HTTP_X_ORIGINAL_HOST'];
		} else {
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];//WAS SERVER_NAME and caused problems on some boxes
		}
        $GLOBALS['HOST_NAME'] = $host;

        if (!defined('MAX_STRLEN_SERIALIZED_CHECK')) { define('MAX_STRLEN_SERIALIZED_CHECK', 2000000); }
	}
}