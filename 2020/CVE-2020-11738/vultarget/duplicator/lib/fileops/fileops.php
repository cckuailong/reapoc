<?php
/** Absolute path to the DAWS directory. - necessary for php protection */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('display_errors'))
    @ini_set('display_errors', 1);
error_reporting(E_ALL);
set_error_handler("terminate_missing_variables");


require_once(dirname(__FILE__) . '/class.fileops.constants.php');
require_once(dirname(__FILE__) . '/class.fileops.u.move.php');

require_once(FileOpsConstants::$LIB_DIR . '/snaplib/snaplib.all.php');


class FileOps
{
    private $lock_handle = null;

    function __construct()
    {
        date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here.

        DupLiteSnapLibLogger::init(FileOpsConstants::$LOG_FILEPATH);
    }

    public function processRequest()
    {
        try {
            DupLiteSnapLibLogger::clearLog();
            /* @var $state FileOpsState */
			DupLiteSnapLibLogger::log('process request');
            $retVal = new StdClass();

            $retVal->pass = false;


           if (isset($_REQUEST['action'])) {
                //$params = $_REQUEST;
                $params = array();
                DupLiteSnapLibLogger::logObject('REQUEST', $_REQUEST);
                
                foreach($_REQUEST as $key => $value) 
                {
                    $params[$key] = json_decode($value, true);    
                }

            } else {
                $json = file_get_contents('php://input');
                DupLiteSnapLibLogger::logObject('json1', $json);
                $params = json_decode($json, true);
                DupLiteSnapLibLogger::logObject('json2', $json);
           }

            DupLiteSnapLibLogger::logObject('params', $params);
            DupLiteSnapLibLogger::logObject('keys', array_keys($params));

            $action = $params['action'];
            
            if ($action == 'deltree') {

				DupLiteSnapLibLogger::log('deltree');



				$config = DeleteConfig();

				$config->workerTime = DupLiteSnapLibUtil::GetArrayValue($params, 'worker_time');
				$config->directories = DupLiteSnapLibUtil::getArrayValue($params, 'directories');
				$config->throttleDelayInUs = DupLiteSnapLibUtil::getArrayValue($params, 'throttleDelay', false, 0) * 1000000;
				$config->excludedDirectories = DupLiteSnapLibUtil::getArrayValue($params, 'excluded_directories', false, array());
				$config->excludedFiles = DupLiteSnapLibUtil::getArrayValue($params, 'excluded_files', false, array());
				$config->fileLock = DupLiteSnapLibUtil::GetArrayValue($params, 'fileLock');

				DupLiteSnapLibLogger::logObject('Config', $config);



				// TODO use appropriate lock type
				DupLiteSnapLibIOU::flock($this->lock_handle, LOCK_EX);

                $this->lock_handle = DupLiteSnapLibIOU::fopen(FileOpsConstants::$PROCESS_LOCK_FILEPATH, 'c+');
				




                DupLiteSnapLibIOU::flock($this->lock_handle, LOCK_UN);

                $retVal->pass = true;
                $retVal->status = new stdClass;
				//todo $retVal->status->errors = $moveErrors;  // RSR TODO ensure putting right thing in here

            } else if($action === 'move_files') {

                $directories = DupLiteSnapLibUtil::getArrayValue($params, 'directories', false, array());
                $files =  DupLiteSnapLibUtil::getArrayValue($params, 'files', false, array());
                $excludedFiles =  DupLiteSnapLibUtil::getArrayValue($params, 'excluded_files', false, array());
                $destination = DupLiteSnapLibUtil::getArrayValue($params, 'destination');                    

                DupLiteSnapLibLogger::log('before move');
                $moveErrors = FileOpsMoveU::move($directories, $files, $excludedFiles, $destination);

                DupLiteSnapLibLogger::log('after move');

                $retVal->pass = true;
                $retVal->status = new stdClass();
                $retVal->status->errors = $moveErrors;  // RSR TODO ensure putting right thing in here
            }
            else {

                throw new Exception('Unknown command.');
            }

            session_write_close();

        } catch (Exception $ex) {
            $error_message = "Error Encountered:" . $ex->getMessage() . '<br/>' . $ex->getTraceAsString();

            DupLiteSnapLibLogger::log($error_message);

            $retVal->pass = false;
            $retVal->error = $error_message;
        }

		DupLiteSnapLibLogger::logObject("before json encode retval", $retVal);

		$jsonRetVal = json_encode($retVal);
		DupLiteSnapLibLogger::logObject("json encoded retval", $jsonRetVal);
        echo $jsonRetVal;
    }
}

function generateCallTrace()
{
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++) {
        $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }

    return "\t" . implode("\n\t", $result);
}

function terminate_missing_variables($errno, $errstr, $errfile, $errline)
{
//    echo "<br/>ERROR: $errstr $errfile $errline<br/>";
    //  if (($errno == E_NOTICE) and ( strstr($errstr, "Undefined variable"))) die("$errstr in $errfile line $errline");


    DupLiteSnapLibLogger::log("ERROR $errno, $errstr, {$errfile}:{$errline}");
    DupLiteSnapLibLogger::log(generateCallTrace());
    //  DaTesterLogging::clearLog();

   // exit(1);
    //return false; // Let the PHP error handler handle all the rest
}

$fileOps = new FileOps();

$fileOps->processRequest();