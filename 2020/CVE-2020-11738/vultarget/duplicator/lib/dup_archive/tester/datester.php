<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
if (DupLiteSnapLibUtil::wp_is_ini_value_changeable('display_errors'))
    ini_set('display_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ALL);
set_error_handler("terminate_missing_variables");


define('LIB_DIR', dirname(__FILE__).'/../..');
define('DUPARCHIVE_DIR', dirname(__FILE__).'/..');

define("ABSPATH", DUPARCHIVE_DIR);
    
define('DUPARCHIVE_CLASSES_DIR', DUPARCHIVE_DIR.'/classes');
define('DUPARCHIVE_STATES_DIR', DUPARCHIVE_CLASSES_DIR.'/states');
define('DUPARCHIVE_UTIL_DIR', DUPARCHIVE_CLASSES_DIR.'/util');

require_once(LIB_DIR.'/snaplib/snaplib.all.php');
require_once(DUPARCHIVE_UTIL_DIR.'/class.duparchive.util.php');
require_once(DUPARCHIVE_CLASSES_DIR.'/class.duparchive.loggerbase.php');
require_once(DUPARCHIVE_CLASSES_DIR.'/class.duparchive.engine.php');
require_once(DUPARCHIVE_CLASSES_DIR.'/class.duparchive.mini.expander.php');
require_once(DUPARCHIVE_UTIL_DIR.'/class.duparchive.util.scan.php');
require_once(DUPARCHIVE_STATES_DIR.'/class.duparchive.state.simplecreate.php');
require_once(dirname(__FILE__).'/classes/class.datester.state.create.php');
require_once(dirname(__FILE__).'/classes/class.datester.state.expand.php');
require_once(dirname(__FILE__).'/classes/class.datester.logging.php');

DupArchiveUtil::$TRACE_ON = true;

class DaTesterPaths
{
    public $dataRoot;
    public $scanFilepath;
    public $processLockFilepath;
    public $dataDirectory;
    public $restoreDirectory;
    public $tempDirectory;
    public $archiveFilepath;
    public $logFilepath;

    function __construct($isSmallArchive)
    {
        $this->dataRoot = getenv("TEMP").'/duparchivetester';

        $this->scanFilepath        = "{$this->dataRoot}/scan.json";
        $this->processLockFilepath = "{$this->dataRoot}/lock.bin";

        if ($isSmallArchive) {

            $this->dataDirectory   = "{$this->dataRoot}/smalldata";
            $this->archiveFilepath = "{$this->dataRoot}/archivesmall.daf";
        } else {
            $this->dataDirectory   = "{$this->dataRoot}/data";
            $this->archiveFilepath = "{$this->dataRoot}/archive.daf";
        }

        $this->restoreDirectory = "{$this->dataRoot}/restore";
        $this->tempDirectory    = "{$this->dataRoot}/temp";
        $this->logFilepath      = "{$this->dataRoot}/tester.log";

        if (!file_exists($this->dataRoot)) {
            @mkdir($this->dataRoot);
        }

        if (!file_exists($this->dataDirectory)) {
            @mkdir($this->dataDirectory);
        }

        if (!file_exists($this->restoreDirectory)) {
            @mkdir($this->restoreDirectory);
        }

        if (!file_exists($this->tempDirectory)) {
            @mkdir($this->tempDirectory);
        }
    }
}

class DaTesterParams
{
    public $compress       = true;
    public $isSmallArchive = true;
    public $action;
    public $p1             = null;
    public $workerTime     = 10;
    public $throttleDelayInUs = 100000;

    function __construct()
    {
        if (isset($_REQUEST['worker_time'])) {
            $this->workerTime = (int) $_REQUEST['worker_time'];
        }

        if (isset($_REQUEST['small_archive'])) {
            $this->isSmallArchive = ($_REQUEST['small_archive'] == 1);
        }

        if (isset($_REQUEST['compress'])) {
            $this->compress = ($_REQUEST['compress'] == 1);
        }

        if (isset($_REQUEST['action'])) {
            $this->action = $_REQUEST['action'];
        } else {
            $this->action = 'get_archive_info';
        }

        if (isset($_REQUEST['p1'])) {
            $this->p1 = $_REQUEST['p1'];
        }
    }

    public function getQueryStringData()
    {
        $qsa = array();

        $qsa['worker_time']   = $this->workerTime;
        $qsa['small_archive'] = ($this->isSmallArchive ? 1 : 0);
        $qsa['compress']      = ($this->compress ? 1 : 0);
        $qsa['action']        = $this->action;

        if ($this->p1 != null) {
            $qsa['p1'] = $this->p1;
        }

        return $qsa;
    }
}

class DaTester
{
    private $paths;
    private $params;
    private $lockHandle;
    private $logger;

    public function __construct()
    {
        $this->params = new DaTesterParams();
        $this->paths  = new DaTesterPaths($this->params->isSmallArchive);
        
    }

    public function processRequest()
    {
        try {
            $this->lockHandle = DupLiteSnapLibIOU::fopen($this->paths->processLockFilepath, 'c+');
            
            DupLiteSnapLibIOU::flock($this->lockHandle, LOCK_EX);

            $this->logger = new DaTesterLogging($this->paths->logFilepath);

            $this->logger->log("incoming request");

            DupArchiveEngine::init($this->logger);

            $this->logger->log("Got file lock");

            $this->logger->log("Action set to {$this->params->action}");
            $initializeState = false;

            if ($this->params->action == 'start_create_test') {

                $initializeState = true;

                $this->params->action = 'create_test';
            } else if ($this->params->action == 'start_expand_test') {

                $initializeState = true;

                $this->params->action = 'expand_test';
            } else if ($this->params->action == 'start_validate_test') {

                $initializeState = true;

                $this->params->action = 'validate_test';
            }

            $this->logger->log("incoming request after lock");

            $spawnAnotherThread = false;

            echo "action={$this->params->action}<br/>";

            if ($this->params->action == 'get_status') {

                $this->get_status();
            } else if ($this->params->action == 'create_test') {

                /* @var $daTesterCreateState DaTesterCreateState */
                $daTesterCreateState = DaTesterCreateState::getInstance($initializeState);

                $daTesterState = &$daTesterCreateState;
                if ($initializeState) {

                    $this->logger->log("Clearing files");

                    $this->clearCreateFiles();

                    DupArchiveEngine::createArchive($this->paths->archiveFilepath, $this->params->compress);

                    $daTesterCreateState->archivePath     = $this->paths->archiveFilepath;
                    $daTesterCreateState->archiveOffset   = DupLiteSnapLibIOU::filesize($this->paths->archiveFilepath);
                    $daTesterCreateState->working         = true;
                    $daTesterCreateState->timeSliceInSecs = $this->params->workerTime;
                    $daTesterCreateState->basePath        = $this->paths->dataDirectory;
                    $daTesterCreateState->isCompressed    = $this->params->compress;
                    $daTesterCreateState->throttleDelayInUs = $this->params->throttleDelayInUs;

                    //   $daTesterCreateState->globSize        = self::GLOB_SIZE;

                    $daTesterCreateState->save();
                    $this->logger->log("Cleared files");

                    $scan = DupArchiveScanUtil::createScan($this->paths->scanFilepath, $this->paths->dataDirectory);
                } else {

                    $scan = DupArchiveScanUtil::getScan($this->paths->scanFilepath);
                }

                $this->logger->logObject("createstate", $daTesterCreateState);
                DupArchiveEngine::addItemsToArchive($daTesterCreateState, $scan);

                $spawnAnotherThread = $daTesterCreateState->working;

                if (!$spawnAnotherThread) {
                    $this->logger->logObject("Done. Failures:", $daTesterCreateState->failures, true);
                }
            } else if ($this->params->action == 'start_add_file_test') {
                //DupArchiveUtil::writeToPLog("Start add file test");
                $this->logger->clearLog();

                $tmpname = tempnam($this->paths->dataDirectory, 'tmp');

                $this->logger->log("tempname $tmpname");
                file_put_contents($tmpname, 'test');

                DupArchiveEngine::addFileToArchiveUsingBaseDirST($this->paths->archiveFilepath, $this->paths->dataDirectory, $tmpname);

                echo "$tmpname added";

                unlink($tmpname);
                exit(1);
            } else if ($this->params->action == 'mini_expand_test') {

                $this->logger->log("Clearing files");
                $this->clearExpandFiles();
                $this->logger->log("Cleared files");

                try {
                    DupArchiveMiniExpander::init("$this->logger->log");
                    DupArchiveMiniExpander::expandDirectory($this->paths->archiveFilepath, 'dup-installer', $this->paths->restoreDirectory);
                } catch (Exception $ex) {
                    $message = $ex->getMessage();

                    echo "Exception: {$ex} ".$ex->getTraceAsString();
                }

                echo "Mini-extract done.<br/>";
                exit(1);
            } else if ($this->params->action == 'expand_test') {
                /* @var $daTesterExpandState DaTesterExpandState */
                $daTesterExpandState = DaTesterExpandState::getInstance($initializeState);

                $daTesterState = &$daTesterExpandState;

                if ($initializeState) {

                    $this->logger->log("Clearing files");

                    $this->clearExpandFiles();
                    $this->logger->log("Cleared files");

                    $daTesterExpandState->archivePath     = $this->paths->archiveFilepath;
                    $daTesterExpandState->working         = true;
                    $daTesterExpandState->timeSliceInSecs = $this->params->workerTime;
                    $daTesterExpandState->basePath        = $this->paths->restoreDirectory;
                    $daTesterExpandState->working         = true;
                    $daTesterExpandState->throttleDelayInUs = $this->params->throttleDelayInUs;;
                    $daTesterExpandState->save();
                }

                DupArchiveEngine::expandArchive($daTesterExpandState);

                $spawnAnotherThread = $daTesterExpandState->working;

                if (!$spawnAnotherThread) {

                    if (count($daTesterExpandState->failures) > 0) {
                        $this->logger->log('Errors detected');
                        echo 'Expanson done, but errors detected!';
                        echo '<br/><br/>';

                        foreach ($daTesterExpandState->failures as $failure) {
                            $this->logger->log($failure->description);
                            echo $failure->description;
                            echo '<br/><br/>';
                        }
                    } else {
                        echo 'Expansion done, archive checks out!';
                        $this->logger->log('Expansion done, archive checks out!');
                    }
                }
            } else if ($this->params->action == 'validate_test') {

                $validationType = DupArchiveValidationTypes::Full;

                if ($this->params->p1 != null) {
                    if ($this->params->p1 == 's') {
                        $validationType = DupArchiveValidationTypes::Standard;
                    }
                }

                /* @var $daTesterExpandState DaTesterExpandState */
                $daTesterExpandState = DaTesterExpandState::getInstance($initializeState);

                $daTesterState = &$daTesterExpandState;

                if ($initializeState) {

                    $this->logger->log("Clearing files");
                    $this->clearExpandFiles();
                    $this->logger->log("Cleared files");

                    $this->logger->log("Validation Type:" . (($validationType == DupArchiveValidationTypes::Full) ? 'Full' : 'Quick'));
                    
                    $scan = DupArchiveScanUtil::getScan($this->paths->scanFilepath);

                    $daTesterExpandState->archivePath            = $this->paths->archiveFilepath;
                    $daTesterExpandState->working                = true;
                    $daTesterExpandState->timeSliceInSecs        = $this->params->workerTime;
                    $daTesterExpandState->basePath               = $this->paths->tempDirectory;
                    $daTesterExpandState->validateOnly           = true;
                    $daTesterExpandState->validationType         = $validationType;
                    $daTesterExpandState->working                = true;
                    $daTesterExpandState->expectedDirectoryCount = count($scan->Dirs);
                    $daTesterExpandState->expectedFileCount      = count($scan->Files);
                    $daTesterExpandState->save();
                }

                DupArchiveEngine::expandArchive($daTesterExpandState);

                $spawnAnotherThread = $daTesterExpandState->working;

                if (!$spawnAnotherThread) {

                    if (count($daTesterExpandState->failures) > 0) {
                        echo 'Errors detected!';
                        echo '<br/><br/>';

                        foreach ($daTesterExpandState->failures as $failure) {
                            echo esc_html($failure->description);
                            echo '<br/><br/>';
                        }
                    } else {
                        echo 'Archive checks out!';
                    }
                }
            } else if ($this->params->action == 'get_archive_info') {
                $this->logger->log("get_archive_info()");

                $this->logger->clearLog();

                $archiveInfo = DupArchiveEngine::getArchiveInfo($this->paths->archiveFilepath);

                $sizeInArchive = 0;
                
                foreach($archiveInfo->fileHeaders as $fileHeader) {
                    $sizeInArchive += $fileHeader->fileSize;
                }
                
                $archiveSize = filesize($this->paths->archiveFilepath);
                
                echo "Version: {$archiveInfo->archiveHeader->version}";
                echo '<br/>';
                echo "IsCompressed: ".DupArchiveUtil::boolToString($archiveInfo->archiveHeader->isCompressed);
                echo '<br/>';
                //    echo "Expected Directory Count: {$archiveInfo->archiveHeader->directoryCount}";
                //    echo '<br/>';
                echo "Total file size: {$sizeInArchive} bytes";
                echo '<br/>';
                echo "Archive size: {$archiveSize} bytes";
                echo '<br/>';
                $directoryCount = count($archiveInfo->directoryHeaders);
                echo "Actual Directory Count: {$directoryCount}";
                echo '<br/>';
                //   echo "Expected File Count: {$archiveInfo->archiveHeader->fileCount}";
                //   echo '<br/>';
                $fileCount      = count($archiveInfo->fileHeaders);
                echo "Actual File Count: {$fileCount}";
                echo '<br/>';
                echo '<br/>';
                echo 'DIRECTORIES';
                echo '<br/>';
                $c              = 1;
                //print_r($archiveInfo);
                foreach ($archiveInfo->directoryHeaders as $directoryHeader) {
                    /* @var $directoryHeader DupArchiveDirectoryHeader */
                    echo "{$c}:{$directoryHeader->relativePath} P:{$directoryHeader->permissions} <br/>";
                    $c++;
                }
                echo '<br/>';
                echo 'FILES';
                echo '<br/>';
                $c = 1;
                //print_r($archiveInfo);
                foreach ($archiveInfo->fileHeaders as $fileHeader) {
                    /* @var $fileHeader DupArchiveFileHeader */
                    echo "{$c}:{$fileHeader->relativePath} ({$fileHeader->fileSize} bytes)<br/>";
                    $c++;
                }
                exit(1);
            } else {
                echo 'unknown command.';
                exit(1);
            }

            DupLiteSnapLibIOU::flock($this->lockHandle, LOCK_UN);

            $this->logger->log("Unlocked file");

            session_write_close();
            if ($spawnAnotherThread) {

                $url = "http://$_SERVER[HTTP_HOST]".strtok($_SERVER["REQUEST_URI"], '?');

                $data = $this->params->getQueryStringData();

                $this->logger->logObject("SPAWNING CUSTOM WORKER AT $url FOR ACTION {$this->params->action}", $data);

                DupLiteSnapLibNetU::postWithoutWait($url, $data);

                $this->logger->log('After post without wait');
            } else {
                $this->logger->log("start timestamp {$daTesterState->startTimestamp}");
                $deltaTime = time() - $daTesterState->startTimestamp;
                $this->logger->log("###### Processing ended.  Seconds taken:$deltaTime");
                $this->logger->logObject("##### FAILURES:", $daTesterState->failures);
            }
        } catch (Exception $ex) {
            $error_message = "Error Encountered:".$ex->getMessage().'<br/>'.$ex->getTraceAsString();

            $this->logger->log($error_message);
            echo $error_message;
        }
    }

    // Returns json
    // {
    //   status: 0|-1 (success, failure)
    //   data : true|false (for working) || {failure message}
    // }
    function get_status()
    {
        $error_message = null;
        $ret_val       = new stdClass();

        try {
            $build_state = CompressExtractState::getInstance();

            $ret_val->status = 0;
            $ret_val->data   = $build_state;
        } catch (Exception $ex) {
            $ret_val->status = -1;
            $ret_val->data   = $error_message;
        }

        echo json_encode($ret_val);
        //  JSON_U::customEncode($ret_val);
    }

    private function clearCreateFiles()
    {
        if (file_exists($this->paths->scanFilepath)) {
            @unlink($this->paths->scanFilepath);
        }

        $handle = DupLiteSnapLibIOU::fopen($this->paths->archiveFilepath, 'w');
        DupLiteSnapLibIOU::fclose($handle);

        //$this->logger->clearLog();
    }

    private function clearExpandFiles()
    {
        if (file_exists($this->paths->restoreDirectory)) {
            DupLiteSnapLibIOU::rrmdir($this->paths->restoreDirectory);
        }

        if (file_exists($this->paths->tempDirectory)) {
            DupLiteSnapLibIOU::rrmdir($this->paths->tempDirectory);
        }

        mkdir($this->paths->restoreDirectory);
    //    $this->logger->clearLog();
    }
//    private function fake_crash($worker_string, $next_scan_index, $next_file_offset)
//    {
//        $url  = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//        $data = array('action' => $worker_string, 'next_scan_index' => $next_scan_index,
//            'next_file_offset' => $next_file_offset);
//
//        $this->logger->log("spawning new custom worker at $url");
//        $this->post_without_wait($url, $data);
//
//        exit();
//    }
//    private function try_crash($source_filepath, $next_file_offset)
//    {
//        $should_crash = (self::CRASH_PROBABILITY >= rand(1, 100));
//
//        $should_crash = false;
//
//        if ($should_crash) {
//            $this->logger->log("##### Crashing for $source_filepath at $next_file_offset");
//
//            $this->fake_crash('compress', $next_scan_index, $next_file_offset);
//        }
//    }
}

function generateCallTrace()
{
    $e      = new Exception();
    $trace  = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace  = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++) {
        $result[] = ($i + 1).')'.substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }

    return "\t".implode("\n\t", $result);
}

function terminate_missing_variables($errno, $errstr, $errfile, $errline)
{
    echo "<br/>ERROR: $errstr $errfile $errline<br/>";
    //  if (($errno == E_NOTICE) and ( strstr($errstr, "Undefined variable"))) die("$errstr in $errfile line $errline");

    $logfilepath =  getenv("TEMP").'/duparchivetester';
    $logfilepath = "{$logfilepath}/tester2.log";

    $logger = new DaTesterLogging($this->paths->logFilepath);


    $logger->log("ERROR $errno, $errstr, {$errfile}:{$errline}");
    $logger->log(generateCallTrace());
    //  $this->logger->clearLog();

    exit(1);
    //return false; // Let the PHP error handler handle all the rest
}      

$daTester = new DaTester();
$daTester->processRequest();
