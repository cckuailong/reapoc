<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

//Prevent directly browsing to the file
if (function_exists('plugin_dir_url')) 
{		
    define('DUPLICATOR_VERSION',        '1.3.26');
	define('DUPLICATOR_VERSION_BUILD',  '2020-02-07_13:50');
    define('DUPLICATOR_PLUGIN_URL',     plugin_dir_url(__FILE__));
	define('DUPLICATOR_SITE_URL',		get_site_url());
	
    /* Paths should ALWAYS read "/"
      uni: /home/path/file.txt
      win:  D:/home/path/file.txt
      SSDIR = SnapShot Directory */
    if (!defined('ABSPATH')) {
		define('ABSPATH', dirname(__FILE__));
    }
	
	//PATH CONSTANTS
	if (! defined('DUPLICATOR_WPROOTPATH')) {
		define('DUPLICATOR_WPROOTPATH', str_replace('\\', '/', ABSPATH));
	}

	define('DUPLICATOR_PLUGIN_PATH',				str_replace("\\", "/", plugin_dir_path(__FILE__)));
    define('DUPLICATOR_SSDIR_NAME',					'wp-snapshots');
	define('DUPLICATOR_SSDIR_PATH',					duplicator_get_abs_path() . '/' . DUPLICATOR_SSDIR_NAME);
    define('DUPLICATOR_SSDIR_PATH_TMP',				DUPLICATOR_SSDIR_PATH . '/tmp');
    define("DUPLICATOR_SSDIR_PATH_INSTALLER",		DUPLICATOR_SSDIR_PATH . '/installer');
	define('DUPLICATOR_SSDIR_URL',					DUPLICATOR_SITE_URL . "/" . DUPLICATOR_SSDIR_NAME);
	define('DUPLICATOR_ZIPPED_LOG_FILENAME',		'duplicator_lite_log.zip');
    define('DUPLICATOR_INSTALL_PHP',				'installer.php');
	define('DUPLICATOR_INSTALL_BAK',				'installer-backup.php');
    define('DUPLICATOR_INSTALLER_HASH_PATTERN',		'[a-z0-9][a-z0-9][a-z0-9][a-z0-9][a-z0-9][a-z0-9][a-z0-9]-[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]');
	define('DUPLICATOR_INSTALL_SITE_OVERWRITE_ON',	false);
	
	//GENERAL CONSTRAINTS
    define('DUPLICATOR_PHP_MAX_MEMORY',  4294967296); // 4096MB
    define('DUPLICATOR_DB_MAX_TIME',     5000);
	define('DUPLICATOR_DB_EOF_MARKER',   'DUPLICATOR_MYSQLDUMP_EOF');
	//SCANNER CONSTRAINTS 
	define('DUPLICATOR_SCAN_SIZE_DEFAULT',	157286400);	//150MB
	define('DUPLICATOR_SCAN_WARNFILESIZE',	3145728);	//3MB
	define('DUPLICATOR_SCAN_CACHESIZE',		1048576);	//1MB
	define('DUPLICATOR_SCAN_DB_ALL_ROWS',	500000);	//500k per DB
	define('DUPLICATOR_SCAN_DB_ALL_SIZE',	52428800);	//50MB DB
	define('DUPLICATOR_SCAN_DB_TBL_ROWS',	100000);    //100K rows per table
	define('DUPLICATOR_SCAN_DB_TBL_SIZE',	10485760);  //10MB Table
	define('DUPLICATOR_SCAN_TIMEOUT',		150);		//Seconds
	define('DUPLICATOR_SCAN_MIN_WP',		'4.7.0');
    define('DUPLICATOR_MAX_DUPARCHIVE_SIZE', 524288000); // 500 GB

	define('DUPLICATOR_TEMP_CLEANUP_SECONDS', 900);     // 15 min = How many seconds to keep temp files around when delete is requested
	define('DUPLICATOR_MAX_BUILD_RETRIES', 10);			// Max times to try a part of progressive build work
	define('DUPLICATOR_WEBCONFIG_ORIG_FILENAME', 'web.config.orig');
	define("DUPLICATOR_INSTALLER_DIRECTORY", duplicator_get_abs_path() . '/dup-installer');
    define('DUPLICATOR_MAX_LOG_SIZE', 400000);    // The higher this is the more overhead
    define('DUPLICATOR_ZIP_ARCHIVE_ADD_FROM_STR', false); 
    define('DUPLICATOR_DEACTIVATION_FEEDBACK', false); 
    define("DUPLICATOR_BUFFER_READ_WRITE_SIZE", 4377);
    define("DUPLICATOR_ADMIN_NOTICES_USER_META_KEY", 'duplicator_admin_notices');
    define("DUPLICATOR_FEEDBACK_NOTICE_SHOW_AFTER_NO_PACKAGE", 5);

    $GLOBALS['DUPLICATOR_SERVER_LIST'] = array('Apache','LiteSpeed', 'Nginx', 'Lighttpd', 'IIS', 'WebServerX', 'uWSGI');
	$GLOBALS['DUPLICATOR_OPTS_DELETE'] = array('duplicator_ui_view_state', 'duplicator_package_active', 'duplicator_settings');
	$GLOBALS['DUPLICATOR_GLOBAL_FILE_FILTERS_ON'] = true;
    $GLOBALS['DUPLICATOR_GLOBAL_FILE_FILTERS'] = array(
        'error_log',
        'error.log',
        'debug_log',
        'ws_ftp.log',
        'dbcache',
        'pgcache',
        'objectcache',
		'.DS_Store'
    );

	
	/* Used to flush a response every N items. 
	 * Note: This value will cause the Zip file to double in size durning the creation process only*/
	define("DUPLICATOR_ZIP_FLUSH_TRIGGER", 1000);

    /* Let's setup few things to cover all PHP versions */
    if(!defined('PHP_VERSION'))
    {
        define('PHP_VERSION', phpversion());
    }
    if (!defined('PHP_VERSION_ID')) {
        $version = explode('.', PHP_VERSION);
        define('PHP_VERSION_ID', (($version[0] * 10000) + ($version[1] * 100) + $version[2]));
    }
    if (PHP_VERSION_ID < 50207) {
        if(!(isset($version))) $version = explode('.', PHP_VERSION);
        if(!defined('PHP_MAJOR_VERSION'))   define('PHP_MAJOR_VERSION',   $version[0]);
        if(!defined('PHP_MINOR_VERSION'))   define('PHP_MINOR_VERSION',   $version[1]);
        if(!defined('PHP_RELEASE_VERSION')) define('PHP_RELEASE_VERSION', $version[2]);
    }

} else {
    error_reporting(0);
    $port = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") ? "https://" : "http://";
    $url = $port . $_SERVER["HTTP_HOST"];
    header("HTTP/1.1 404 Not Found", true, 404);
    header("Status: 404 Not Found");
    exit();
}