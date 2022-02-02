<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
// Exit if accessed directly
if (!defined('DUPLICATOR_VERSION')) exit;

/**
 * Helper Class for logging
 * @package Duplicator\classes
 */
abstract class Dup_ErrorBehavior
{
	const LogOnly			 = 0;
	const ThrowException	 = 1;
	const Quit			 = 2;
}

class DUP_Log
{
    /**
     * The file handle used to write to the package log file
     */
    private static $logFileHandle = null;

    /**
     * Get the setting which indicates if tracing is enabled
     */
    private static $traceEnabled = false;

    public static $profileLogs = null;
    
	/**
	 * Init this static object
	 */
	public static function Init()
	{
		self::$traceEnabled = (DUP_Settings::Get('trace_log_enabled') == 1);
	}

    /**
     * Open a log file connection for writing to the package log file
     *
     * @param string $nameHas The Name of the log file to create
     *
     * @return nul
     */
    public static function Open($nameHash)
    {
        if (!isset($nameHash)) {
            throw new Exception("A name value is required to open a file log.");
        }
        self::Close();
        if ((self::$logFileHandle = @fopen(DUPLICATOR_SSDIR_PATH."/{$nameHash}.log", "a+")) === false) {
            self::$logFileHandle = null;
            return false;
        } else {
            /**
             * By initializing the error_handler on opening the log, I am sure that when a package is processed, the handler is active.
             */
            DUP_Handler::init_error_handler();
            return true;
        }
    }

    /**
     * Close the package log file connection if is opened
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public static function Close()
    {
        $result = true;
        
        if (!is_null(self::$logFileHandle)) {
            $result              = @fclose(self::$logFileHandle);
            self::$logFileHandle = null;
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     *  General information send to the package log if opened
     *  @param string $msg	The message to log
     *
     *  REPLACE TO DEBUG: Memory consumption as script runs
     * 	$results = DUP_Util::byteSize(memory_get_peak_usage(true)) . "\t" . $msg;
     * 	@fwrite(self::$logFileHandle, "{$results} \n");
     *
     *  @param string $msg	The message to log
     *
     *  @return null
     */
    public static function Info($msg)
    {
        if (self::$traceEnabled) {
            self::Trace($msg);
        }
        if (!is_null(self::$logFileHandle)) {
            @fwrite(self::$logFileHandle, $msg."\n");
        }
    }

    public static function print_r_info($val, $name = '')
    {
        $msg = empty($name) ? '' : 'VALUE '.$name.': ';
        $msg .= print_r($val, true);
        self::info($msg);
    }

	/**
	 * Does the trace file exists
	 *
	 * @return bool Returns true if an active trace file exists
	 */
	public static function TraceFileExists()
	{
		$file_path = self::getTraceFilepath();
		return file_exists($file_path);
	}

	/**
	 * Gets the current file size of the active trace file
	 *
	 * @return string   Returns a human readable file size of the active trace file
	 */
	public static function getTraceStatus()
	{
		$file_path	 = DUP_Log::getTraceFilepath();
		$backup_path = DUP_Log::getBackupTraceFilepath();

		if (file_exists($file_path)) {
			$filesize = filesize($file_path);

			if (file_exists($backup_path)) {
				$filesize += filesize($backup_path);
			}

			$message = sprintf('%1$s', DUP_Util::byteSize($filesize));
		} else {
			$message = esc_html__('No Log', 'duplicator');
		}

		return $message;
	}

	// RSR TODO: Swap trace logic out for real trace later
	public static function Trace($message, $calling_function_override = null)
	{

		if (self::$traceEnabled) {
			$unique_id = sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'].$_SERVER['REQUEST_TIME'].$_SERVER['REMOTE_PORT'])));

			if ($calling_function_override == null) {
				$calling_function = DupLiteSnapLibUtil::getCallingFunctionName();
			} else {
				$calling_function = $calling_function_override;
			}

			if (is_object($message)) {
				$ov		 = get_object_vars($message);
				$message = print_r($ov, true);
			} else if (is_array($message)) {
				$message = print_r($message, true);
			}

			$logging_message			 = "{$unique_id}|{$calling_function} | {$message}";
			$ticks						 = time() + ((int) get_option('gmt_offset') * 3600);
			$formatted_time				 = date('d-m-H:i:s', $ticks);
			$formatted_logging_message	 = "{$formatted_time}|DUP|{$logging_message} \r\n";

			// Always write to error log - if they don't want the info they can turn off WordPress error logging or tracing
			self::ErrLog($logging_message);

			// Everything goes to the plugin log, whether it's part of package generation or not.
			self::WriteToTrace($formatted_logging_message);
		}
	}

    public static function print_r_trace($val, $name = '', $calling_function_override = null)
    {
        $msg = empty($name) ? '' : 'VALUE '.$name.': ';
        $msg .= print_r($val, true);
        self::trace($msg, $calling_function_override);
    }

	public static function errLog($message)
	{
		$message = 'DUP:'.$message;
		error_log($message);
	}

	public static function TraceObject($msg, $o, $log_private_members = true)
	{
		if (self::$traceEnabled) {
			if (!$log_private_members) {
				$o = get_object_vars($o);
			}
			self::Trace($msg.':'.print_r($o, true));
		}
	}

	public static function GetDefaultKey()
	{
		$auth_key	 = defined('AUTH_KEY') ? AUTH_KEY : 'atk';
		$auth_key	 .= defined('DB_HOST') ? DB_HOST : 'dbh';
		$auth_key	 .= defined('DB_NAME') ? DB_NAME : 'dbn';
		$auth_key	 .= defined('DB_USER') ? DB_USER : 'dbu';
		return hash('md5', $auth_key);
	}

	public static function GetBackupTraceFilepath()
	{
		$default_key		 = self::getDefaultKey();
		$backup_log_filename = "dup_$default_key.log1";
		$backup_path		 = DUPLICATOR_SSDIR_PATH."/".$backup_log_filename;
		return $backup_path;
	}

	/**
	 * Gets the active trace file path
	 *
	 * @return string   Returns the full path to the active trace file (i.e. dup-pro_hash.log)
	 */
	public static function GetTraceFilepath()
	{
		$default_key	 = self::getDefaultKey();
		$log_filename	 = "dup_$default_key.log";
		$file_path		 = DUPLICATOR_SSDIR_PATH."/".$log_filename;
		return $file_path;
	}

	/**
	 * Deletes the trace log and backup trace log files
	 *
	 * @return null
	 */
	public static function DeleteTraceLog()
	{
		$file_path	 = self::GetTraceFilepath();
		$backup_path = self::GetBackupTraceFilepath();
		self::trace("deleting $file_path");
		@unlink($file_path);
		self::trace("deleting $backup_path");
		@unlink($backup_path);
	}

	/**
	 *  Called when an error is detected and no further processing should occur
	 *  @param string $msg The message to log
	 *  @param string $details Additional details to help resolve the issue if possible
	 */
	public static function Error($msg, $detail, $behavior = Dup_ErrorBehavior::Quit)
	{

		error_log($msg.' DETAIL:'.$detail);
		$source = self::getStack(debug_backtrace());

		$err_msg = "\n==================================================================================\n";
		$err_msg .= "DUPLICATOR ERROR\n";
		$err_msg .= "Please try again! If the error persists see the Duplicator 'Help' menu.\n";
		$err_msg .= "---------------------------------------------------------------------------------\n";
		$err_msg .= "MESSAGE:\n\t{$msg}\n";
		if (strlen($detail)) {
			$err_msg .= "DETAILS:\n\t{$detail}\n";
		}
		$err_msg .= "TRACE:\n{$source}";
		$err_msg .= "==================================================================================\n\n";
		@fwrite(self::$logFileHandle, "{$err_msg}");

		switch ($behavior) {
            
			case Dup_ErrorBehavior::ThrowException:
				DUP_LOG::trace("throwing exception");
				throw new Exception("DUPLICATOR ERROR: Please see the 'Package Log' file link below.");
				break;

			case Dup_ErrorBehavior::Quit:
				DUP_LOG::trace("quitting");
				die("DUPLICATOR ERROR: Please see the 'Package Log' file link below.");
				break;

			default:
			// Nothing
		}
	}

	/**
	 * The current stack trace of a PHP call
	 * @param $stacktrace The current debug stack
	 * @return string
	 */
	public static function getStack($stacktrace)
	{
		$output	 = "";
		$i		 = 1;
		foreach ($stacktrace as $node) {
			$output .= "\t $i. ".basename($node['file'])." : ".$node['function']." (".$node['line'].")\n";
			$i++;
		}
		return $output;
	}

	/**
	 * Manages writing the active or backup log based on the size setting
	 *
	 * @return null
	 */
	private static function WriteToTrace($formatted_logging_message)
	{
		$log_filepath = self::GetTraceFilepath();

		if (@filesize($log_filepath) > DUPLICATOR_MAX_LOG_SIZE) {
			$backup_log_filepath = self::GetBackupTraceFilepath();

			if (file_exists($backup_log_filepath)) {
				if (@unlink($backup_log_filepath) === false) {
					self::errLog("Couldn't delete backup log $backup_log_filepath");
				}
			}

			if (@rename($log_filepath, $backup_log_filepath) === false) {
				self::errLog("Couldn't rename log $log_filepath to $backup_log_filepath");
			}
		}

		if (@file_put_contents($log_filepath, $formatted_logging_message, FILE_APPEND) === false) {
			// Not en error worth reporting
		}
	}
}

class DUP_Handler
{
    const MODE_OFF         = 0; // don't write in log
    const MODE_LOG         = 1; // write errors in log file
    const MODE_VAR         = 2; // put php errors in $varModeLog static var
    const SHUTDOWN_TIMEOUT = 'tm';

    /**
     *
     * @var bool
     */
    private static $inizialized = false;

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
     * This function only initializes the error handler the first time it is called
     */
    public static function init_error_handler()
    {
        if (!self::$inizialized) {
            @set_error_handler(array(__CLASS__, 'error'));
            @register_shutdown_function(array(__CLASS__, 'shutdown'));
            self::$inizialized = true;
        }
    }

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
                    DUP_Log::Error($log_message);
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
                        DUP_Log::Error($log_message);
                        break;
                    case E_NOTICE :
                    case E_WARNING :
                    default :
                        $log_message = self::getMessage($errno, $errstr, $errfile, $errline);
                        DUP_Log::Info($log_message);
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
            ob_start();
            debug_print_backtrace();
            $result .= "\n".ob_get_clean();  
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
     * @param string
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
            self::error($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}
