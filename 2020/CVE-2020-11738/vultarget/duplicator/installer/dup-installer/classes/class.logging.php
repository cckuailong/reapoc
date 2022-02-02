<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

define('ERR_ZIPNOTFOUND', 'The packaged zip file was not found or has become unreadable. Be sure the zip package is in the same directory as the installer file.  If you are trying to reinstall a package you can copy the package from the "' . DUPLICATOR_SSDIR_NAME . '" directory back up to your root which is the same location as your installer file.');
define('ERR_SHELLEXEC_ZIPOPEN', 'Failed to extract the archive using shell_exec unzip');
define('ERR_ZIPOPEN', 'Failed to open the zip archive file. Please be sure the archive is completely downloaded before running the installer. Try to extract the archive manually to make sure the file is not corrupted.');
define('ERR_ZIPEXTRACTION', 'Errors extracting the zip file.  Portions or part of the zip archive did not extract correctly.    Try to extract the archive manually with a client side program like unzip/win-zip/winrar to make sure the file is not corrupted.  If the file extracts correctly then there is an invalid file or directory that PHP is unable to extract.  This can happen if your moving from one operating system to another where certain naming conventions work on one environment and not another. <br/><br/> Workarounds: <br/> 1. Create a new package and be sure to exclude any directories that have name checks or files in them.   This warning will be displayed on the scan results under "Name Checks". <br/> 2. Manually extract the zip file with a client side program.  Then under options in step 1 of the installer select the "Manual Archive Extraction" option and perform the install.');
define('ERR_ZIPMANUAL', 'When choosing "Manual Archive Extraction", the contents of the package must already be extracted for the process to continue.  Please manually extract the package into the current directory before continuing in manual extraction mode.  Also validate that the wp-config.php files are present.');
define('ERR_MAKELOG', 'PHP is having issues writing to the log file <b>' . $GLOBALS['DUPX_INIT'] . '\dup-installer-log__'.$GLOBALS['DUPX_AC']->package_hash.'.txt .</b> In order for the Duplicator to proceed validate your owner/group and permission settings for PHP on this path. Try temporarily setting you permissions to 777 to see if the issue gets resolved.  If you are on a shared hosting environment please contact your hosting company and tell them you are getting errors writing files to the path above when using PHP.');
define('ERR_ZIPARCHIVE', 'In order to extract the archive.zip file, the PHP ZipArchive module must be installed.  Please read the FAQ for more details.  You can still install this package but you will need to select the "Manual Archive Extraction" options found under Options.  Please read the online user guide for details in performing a manual archive extraction.');
define('ERR_MYSQLI_SUPPORT', 'In order to complete an install the mysqli extension for PHP is required. If you are on a hosted server please contact your host and request that mysqli be enabled.  For more information visit: http://php.net/manual/en/mysqli.installation.php');
define('ERR_DBCONNECT', 'DATABASE CONNECTION FAILED!<br/>');
define('ERR_DBCONNECT_CREATE', 'DATABASE CREATION FAILURE!<br/> Unable to create database "%s". Check to make sure the user has "Create" privileges.  Some hosts will restrict the creation of a database only through the cpanel.  Try creating the database manually to proceed with installation.  If the database already exists select the action "Connect and Remove All Data" which will remove all existing tables.');
define('ERR_DBTRYCLEAN', 'DATABASE CREATION FAILURE!<br/> Unable to remove table from database "%s".<br/>  Please remove all tables from this database and try the installation again.  If no tables show in the database, then Drop the database and re-create it.');
define('ERR_DBTRYRENAME', 'DATABASE CREATION FAILURE!<br/> Unable to rename a table from database "%s".<br/> Be sure the database user has RENAME privileges for this specific database on all tables.');
define('ERR_DBCREATE', 'The database "%s" does not exist.<br/>  Change the action to create in order to "Create New Database" to create the database.  Some hosting providers do not allow database creation except through their control panels. In this case, you will need to login to your hosting providers\' control panel and create the database manually.  Please contact your hosting provider for further details on how to create the database.');
define('ERR_DBEMPTY', 'The database "%s" already exists and has "%s" tables.  When using the "Create New Database" action the database should not exist.  Select the action "Connect and Remove All Data" or "Connect and Backup Any Existing Data" to remove or backup the existing tables or choose a database name that does not already exist. Some hosting providers do not allow table removal or renaming from scripts.  In this case, you will need to login to your hosting providers\' control panel and remove or rename the tables manually.  Please contact your hosting provider for further details.  Always backup all your data before proceeding!');
define('ERR_DBMANUAL', 'The database "%s" has "%s" tables. This does not look to be a valid WordPress database.  The base WordPress install has 12 tables.  Please validate that this database is indeed pre-populated with a valid WordPress database.  The "Manual SQL execution" mode requires that you have a valid WordPress database already installed.');
define('ERR_TESTDB_VERSION_INFO',	'The current version detected was released prior to MySQL 5.5.3 which had a release date of April 8th, 2010.  WordPress 4.2 included support for utf8mb4 which is only supported in MySQL server 5.5.3+.  It is highly recommended to upgrade your version of MySQL server on this server to be more compatible with recent releases of WordPress and avoid issues with install errors.');
define('ERR_TESTDB_VERSION_COMPAT',	'In order to avoid database incompatibility issues make sure the database versions between the build and installer servers are as close as possible. If the package was created on a newer database version than where it is being installed then you might run into issues.<br/><br/> It is best to make sure the server where the installer is running has the same or higher version number than where it was built.  If the major and minor version are the same or close for example [5.7 to 5.6], then the migration should work without issues.  A version pair of [5.7 to 5.1] is more likely to cause issues unless you have a very simple setup.  If the versions are too far apart work with your hosting provider to upgrade the MySQL engine on this server.<br/><br/>   <b>MariaDB:</b> If see a version of 10.N.N then the database distribution is a MariaDB flavor of MySQL.   While the distributions are very close there are some subtle differences.   Some operating systems will report the version such as "5.5.5-10.1.21-MariaDB" showing the correlation of both.  Please visit the online <a href="https://mariadb.com/kb/en/mariadb/mariadb-vs-mysql-compatibility/" target="_blank">MariaDB versus MySQL - Compatibility</a> page for more details.<br/><br/> Please note these messages are simply notices.  It is highly recommended that you continue with the install process and closely monitor the dup-installer-log.txt file along with the install report found on step 3 of the installer.  Be sure to look for any notices/warnings/errors in these locations to validate the install process did not detect any errors. If any issues are found please visit the FAQ pages and see the question <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-260-q" target="_blank">What if I get database errors or general warnings on the install report?</a>.');

/**
 * DUPX_Log
 * Class used to log information  */
class DUPX_Log
{
    /**
     * if true throw exception on error else die on error
     * @var bool
     */
    private static $thowExceptionOnError = false;

    const LV_DEFAULT    = 1;
    const LV_DETAILED   = 2;
    const LV_DEBUG      = 3;
    const LV_HARD_DEBUG = 4;

    /**
     * num of \t before log string.
     * @var int
     */
    private static $indentation = 0;

    /** METHOD: LOG
     *  Used to write debug info to the text log file
     *  @param string $msg		Any text data
     *  @param int $logging	Log level
     *  @param bool if true flush file log
     *
     */
    public static function info($msg, $logging = self::LV_DEFAULT, $flush = false)
    {
        if ($logging <= $GLOBALS["LOGGING"]) {
            if (is_resource($GLOBALS['LOG_FILE_HANDLE'])) {
                @fwrite($GLOBALS["LOG_FILE_HANDLE"], str_repeat("\t", self::$indentation).$msg."\n");
            }

            if ($flush) {
                self::flush();
            }
        }
    }

    public static function incIndent()
    {
        self::$indentation ++;
    }

    public static function decIndent()
    {
        if (self::$indentation > 0) {
            self::$indentation --;
        }
    }

    public static function resetIndent()
    {
        self::$indentation = 0;
    }

    public static function isLevel($logging)
    {
        return $logging <= $GLOBALS["LOGGING"];
    }

    public static function infoObject($msg, $object, $logging = self::LV_DEFAULT)
    {
        $msg = $msg."\n".print_r($object, true);
        self::info($msg, $logging);
    }

    public static function flush()
    {
        if (is_resource($GLOBALS['LOG_FILE_HANDLE'])) {
            @fflush($GLOBALS['LOG_FILE_HANDLE']);
        }
    }

    public static function close()
    {
        if (is_resource($GLOBALS['LOG_FILE_HANDLE'])) {
            @fclose($GLOBALS["LOG_FILE_HANDLE"]);
            $GLOBALS["LOG_FILE_HANDLE"] = null;
        }
    }

    public static function getFileHandle()
    {
        return is_resource($GLOBALS["LOG_FILE_HANDLE"]) ? $GLOBALS["LOG_FILE_HANDLE"] : false;
    }

    public static function error($errorMessage)
    {
        $breaks  = array("<br />", "<br>", "<br/>");
        $spaces  = array("&nbsp;");
        $log_msg = str_ireplace($breaks, "\r\n", $errorMessage);
        $log_msg = str_ireplace($spaces, " ", $log_msg);
        $log_msg = strip_tags($log_msg);

        self::info("\nINSTALLER ERROR:\n{$log_msg}\n");

        if (self::$thowExceptionOnError) {
            throw new Exception('INSTALL ERROR: '.$errorMessage);
        } else {
            self::close();
            die("<div class='dupx-ui-error'><hr size='1' /><b style='color:#B80000;'>INSTALL ERROR!</b><br/>{$errorMessage}</div>");
        }
    }

    /**
     *
     * @param boolean $set
     */
    public static function setThrowExceptionOnError($set)
    {
        self::$thowExceptionOnError = (bool) $set;
    }

    /**
     *
     * @param mixed $var
     * @param bool $checkCallable // if true check if var is callable and display it
     * @return string
     */
    public static function varToString($var, $checkCallable = false)
    {
        if ($checkCallable && is_callable($var)) {
            return '(callable) '.print_r($var, true);
        }
        switch (gettype($var)) {
            case "boolean":
                return $var ? 'true' : 'false';
            case "integer":
            case "double":
                return (string) $var;
            case "string":
                return '"'.$var.'"';
            case "array":
            case "object":
                return print_r($var, true);
            case "resource":
            case "resource (closed)":
            case "NULL":
            case "unknown type":
            default:
                return gettype($var);
        }
    }
}

class DUPX_Handler
{
    const MODE_OFF         = 0; // don't write in log
    const MODE_LOG         = 1; // write errors in log file
    const MODE_VAR         = 2; // put php errors in $varModeLog static var
    const SHUTDOWN_TIMEOUT = 'tm';

    /**
     *
     * @var array
     */
    private static $shutdownReturns = array(
        'tm' => 'timeout'
    );

    /**
     *
     * @var int
     */
    private static $handlerMode = self::MODE_LOG;

    /**
     *
     * @var bool // print code reference and errno at end of php error line  [CODE:10|FILE:test.php|LINE:100]
     */
    private static $codeReference = true;

    /**
     *
     * @var bool // print prefix in php error line [PHP ERR][WARN] MSG: .....
     */
    private static $errPrefix = true;

    /**
     *
     * @var string // php errors in MODE_VAR
     */
    private static $varModeLog = '';

    /**
     * Error handler
     *
     * @param  integer $errno   Error level
     * @param  string  $errstr  Error message
     * @param  string  $errfile Error file
     * @param  integer $errline Error line
     * @return void
     */
    public static function error($errno, $errstr, $errfile, $errline)
    {
        switch (self::$handlerMode) {
            case self::MODE_OFF:
                if ($errno == E_ERROR) {
                    $log_message = self::getMessage($errno, $errstr, $errfile, $errline);
                    DUPX_Log::error($log_message);
                }
                break;
            case self::MODE_VAR:
                self::$varModeLog .= self::getMessage($errno, $errstr, $errfile, $errline)."\n";
                break;
            case self::MODE_LOG:
            default:
                switch ($errno) {
                    case E_ERROR :
                        $log_message = self::getMessage($errno, $errstr, $errfile, $errline);
                        DUPX_Log::error($log_message);
                        break;
                    case E_NOTICE :
                    case E_WARNING :
                    default :
                        $log_message = self::getMessage($errno, $errstr, $errfile, $errline);
                        DUPX_Log::info($log_message);
                        break;
                }
        }
    }

    private static function getMessage($errno, $errstr, $errfile, $errline)
    {
        $result = '';

        if (self::$errPrefix) {
            $result = '[PHP ERR]';
            switch ($errno) {
                case E_ERROR :
                    $result .= '[FATAL]';
                    break;
                case E_WARNING :
                    $result .= '[WARN]';
                    break;
                case E_NOTICE :
                    $result .= '[NOTICE]';
                    break;
                default :
                    $result .= '[ISSUE]';
                    break;
            }
            $result .= ' MSG:';
        }

        $result .= $errstr;

        if (self::$codeReference) {
            $result .= ' [CODE:'.$errno.'|FILE:'.$errfile.'|LINE:'.$errline.']';
        }

        return $result;
    }

    /**
     * if setMode is called without params set as default
     *
     * @param int $mode
     * @param bool $errPrefix // print prefix in php error line [PHP ERR][WARN] MSG: .....
     * @param bool $codeReference // print code reference and errno at end of php error line  [CODE:10|FILE:test.php|LINE:100]
     */
    public static function setMode($mode = self::MODE_LOG, $errPrefix = true, $codeReference = true)
    {
        switch ($mode) {
            case self::MODE_OFF:
            case self::MODE_VAR:
                self::$handlerMode = $mode;
                break;
            case self::MODE_LOG:
            default:
                self::$handlerMode = self::MODE_LOG;
        }

        self::$varModeLog    = '';
        self::$errPrefix     = $errPrefix;
        self::$codeReference = $codeReference;
    }

    /**
     *
     * @return string // return var log string in MODE_VAR
     */
    public static function getVarLog()
    {
        return self::$varModeLog;
    }

    /**
     *
     * @return string // return var log string in MODE_VAR and clean var
     */
    public static function getVarLogClean()
    {
        $result           = self::$varModeLog;
        self::$varModeLog = '';
        return $result;
    }

    /**
     *
     * @param string $status // timeout
     * @param string $string
     */
    public static function setShutdownReturn($status, $str)
    {
        self::$shutdownReturns[$status] = $str;
    }

    /**
     * Shutdown handler
     *
     * @return void
     */
    public static function shutdown()
    {
        if (($error = error_get_last())) {
            if (preg_match('/^Maximum execution time (?:.+) exceeded$/i', $error['message'])) {
                echo self::$shutdownReturns[self::SHUTDOWN_TIMEOUT];
            }
            DUPX_Handler::error($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}
@set_error_handler('DUPX_Handler::error');
@register_shutdown_function('DUPX_Handler::shutdown');