<?php
/**
 *
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package daws
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('display_errors')) {
    @ini_set('display_errors', 1);
}
error_reporting(E_ALL);
set_error_handler("terminate_missing_variables");

require_once(dirname(__FILE__) . '/class.daws.constants.php');

require_once(DAWSConstants::$LIB_DIR . '/snaplib/snaplib.all.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR . '/class.duparchive.loggerbase.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR . '/class.duparchive.engine.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR . '/class.duparchive.mini.expander.php');
require_once(DAWSConstants::$DUPARCHIVE_STATES_DIR . '/class.duparchive.state.simplecreate.php');
require_once(DAWSConstants::$DAWS_ROOT . '/class.daws.state.expand.php');

DupArchiveUtil::$TRACE_ON = false;

class DAWS_Logger extends DupArchiveLoggerBase
{
    public function log($s, $flush = false, $callingFunctionOverride = null)
    {
        DupLiteSnapLibLogger::log($s, $flush, $callingFunctionOverride);
    }
}

class DAWS
{

    private $lock_handle = null;

    function __construct()
    {
        date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here.

        DupLiteSnapLibLogger::init(DAWSConstants::$LOG_FILEPATH);

        DupArchiveEngine::init(new DAWS_Logger());
    }

    public function processRequest()
    {
        try {
			DupLiteSnapLibLogger::log('process request');
            $retVal = new StdClass();

            $retVal->pass = false;

            if (isset($_REQUEST['action'])) {
                $params = $_REQUEST;
                DupLiteSnapLibLogger::log('b');
            } else {
                $json = file_get_contents('php://input');
                $params = json_decode($json, true);
            }

            DupLiteSnapLibLogger::logObject('params', $params);
            DupLiteSnapLibLogger::logObject('keys', array_keys($params));

            $action = $params['action'];

            $initializeState = false;

            $isClientDriven = DupLiteSnapLibUtil::getArrayValue($params, 'client_driven', false);

            if ($action == 'start_expand') {

                $initializeState = true;

                DAWSExpandState::purgeStatefile();
                DupLiteSnapLibLogger::clearLog();

                DupLiteSnapLibIOU::rm(DAWSConstants::$PROCESS_CANCEL_FILEPATH);
                $archiveFilepath = DupLiteSnapLibUtil::getArrayValue($params, 'archive_filepath');
                $restoreDirectory = DupLiteSnapLibUtil::getArrayValue($params, 'restore_directory');
                $workerTime = DupLiteSnapLibUtil::getArrayValue($params, 'worker_time', false, DAWSConstants::$DEFAULT_WORKER_TIME);
                $filteredDirectories = DupLiteSnapLibUtil::getArrayValue($params, 'filtered_directories', false, array());
                $filteredFiles = DupLiteSnapLibUtil::getArrayValue($params, 'filtered_files', false, array()); 
                $fileRenames = DupLiteSnapLibUtil::getArrayValue($params, 'file_renames', false, array());

                $action = 'expand';

				DupLiteSnapLibLogger::log('startexpand->expand');
            } else if($action == 'start_create') {
             
                $archiveFilepath = DupLiteSnapLibUtil::getArrayValue($params, 'archive_filepath');
                $workerTime = DupLiteSnapLibUtil::getArrayValue($params, 'worker_time', false, DAWSConstants::$DEFAULT_WORKER_TIME);
                
                $createState->basePath        = $dataDirectory;
                $createState->isCompressed    = $isCompressed;
                
                $sourceDirectory = DupLiteSnapLibUtil::getArrayValue($params, 'source_directory');
                $isCompressed = DupLiteSnapLibUtil::getArrayValue($params, 'is_compressed') === 'true' ? true : false;
            }

			$throttleDelayInMs = DupLiteSnapLibUtil::getArrayValue($params, 'throttle_delay', false, 0);

            if ($action == 'expand') {

                DupLiteSnapLibLogger::log('expand action');

                /* @var $expandState DAWSExpandState */
                $expandState = DAWSExpandState::getInstance($initializeState);

				$this->lock_handle = DupLiteSnapLibIOU::fopen(DAWSConstants::$PROCESS_LOCK_FILEPATH, 'c+');
				DupLiteSnapLibIOU::flock($this->lock_handle, LOCK_EX);

				if($initializeState || $expandState->working) {

					if ($initializeState) {

                        DupLiteSnapLibLogger::logObject('file renames', $fileRenames);

						$expandState->archivePath = $archiveFilepath;
						$expandState->working = true;
						$expandState->timeSliceInSecs = $workerTime;
						$expandState->basePath = $restoreDirectory;
						$expandState->working = true;
						$expandState->filteredDirectories = $filteredDirectories;
                        $expandState->filteredFiles = $filteredFiles;
                        $expandState->fileRenames = $fileRenames;
                        $expandState->fileModeOverride = 0644;
                        $expandState->directoryModeOverride = 'u+rwx';

						$expandState->save();
					}

					$expandState->throttleDelayInUs = 1000 * $throttleDelayInMs;

                    DupLiteSnapLibLogger::logObject('Expand State In', $expandState);

					DupArchiveEngine::expandArchive($expandState);
				}

                if (!$expandState->working) {

                    $deltaTime = time() - $expandState->startTimestamp;
                    DupLiteSnapLibLogger::log("###### Processing ended.  Seconds taken:$deltaTime");

                    if (count($expandState->failures) > 0) {
                        DupLiteSnapLibLogger::log('Errors detected');

                        foreach ($expandState->failures as $failure) {
                            DupLiteSnapLibLogger::log("{$failure->subject}:{$failure->description}");
                        }
                    } else {
                        DupLiteSnapLibLogger::log('Expansion done, archive checks out!');
                    }
                }
				else {
					DupLiteSnapLibLogger::log("Processing will continue");
				}


                DupLiteSnapLibIOU::flock($this->lock_handle, LOCK_UN);

                $retVal->pass = true;
                $retVal->status = $this->getStatus($expandState);
            } else if ($action == 'create') {

                DupLiteSnapLibLogger::log('create action');

                /* @var $expandState DAWSExpandState */
                $createState = DAWSCreateState::getInstance($initializeState);

				$this->lock_handle = DupLiteSnapLibIOU::fopen(DAWSConstants::$PROCESS_LOCK_FILEPATH, 'c+');
				DupLiteSnapLibIOU::flock($this->lock_handle, LOCK_EX);

				if($initializeState || $createState->working) {

                    DupArchiveEngine::createArchive($archiveFilepath, $isCompressed);

                    $createState->archivePath     = $archiveFilepath;
                    $createState->archiveOffset   = DupLiteSnapLibIOU::filesize($archiveFilepath);
                    $createState->working         = true;
                    $createState->timeSliceInSecs = $workerTime;
                    $createState->basePath        = $dataDirectory;
                    $createState->isCompressed    = $isCompressed;
                    $createState->throttleDelayInUs = $throttleDelayInUs;

                    //   $daTesterCreateState->globSize        = self::GLOB_SIZE;

                    $createState->save();

                    $scan = DupArchiveScanUtil::createScan($this->paths->scanFilepath, $this->paths->dataDirectory);
				}
                
                $createState->throttleDelayInUs = 1000 * $throttleDelayInMs;

                if (!$createState->working) {

                    $deltaTime = time() - $createState->startTimestamp;
                    DupLiteSnapLibLogger::log("###### Processing ended.  Seconds taken:$deltaTime");

                    if (count($createState->failures) > 0) {
                        DupLiteSnapLibLogger::log('Errors detected');

                        foreach ($createState->failures as $failure) {
                            DupLiteSnapLibLogger::log("{$failure->subject}:{$failure->description}");
                        }
                    } else {
                        DupLiteSnapLibLogger::log('Creation done, archive checks out!');
                    }
                }
				else {
					DupLiteSnapLibLogger::log("Processing will continue");
				}

                DupLiteSnapLibIOU::flock($this->lock_handle, LOCK_UN);

                $retVal->pass = true;
                $retVal->status = $this->getStatus($createState);
            } else if ($action == 'get_status') {
                /* @var $expandState DAWSExpandState */
                $expandState = DAWSExpandState::getInstance($initializeState);

                $retVal->pass = true;
                $retVal->status = $this->getStatus($expandState);
            } else if ($action == 'cancel') {
                if (!DupLiteSnapLibIOU::touch(DAWSConstants::$PROCESS_CANCEL_FILEPATH)) {
                    throw new Exception("Couldn't update time on ".DAWSConstants::$PROCESS_CANCEL_FILEPATH);
                }
                $retVal->pass = true;
            } else {
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

		$jsonRetVal = DupLiteSnapJsonU::wp_json_encode($retVal);
		DupLiteSnapLibLogger::logObject("json encoded retval", $jsonRetVal);
        echo $jsonRetVal;
    }

    private function getStatus($state)
    {
        /* @var $state DupArchiveStateBase */

        $ret_val = new stdClass();

        $ret_val->archive_offset = $state->archiveOffset;
        $ret_val->archive_size = @filesize($state->archivePath);
        $ret_val->failures = $state->failures;
        $ret_val->file_index = $state->fileWriteCount;
        $ret_val->is_done = !$state->working;
        $ret_val->timestamp = time();

        return $ret_val;
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
    DupLiteSnapLibLogger::log("ERROR $errno, $errstr, {$errfile}:{$errline}");
    DupLiteSnapLibLogger::log(generateCallTrace());
    //  DaTesterLogging::clearLog();

    /**
     * INTERCEPT ON processRequest AND RETURN JSON STATUS
     */
    throw new Exception("ERROR:{$errfile}:{$errline} | ".$errstr , $errno);
}

$daws = new DAWS();

$daws->processRequest();