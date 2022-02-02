<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

/**
 * Base controller class for installer controllers
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 */
//Enum used to define the various test statues 
final class DUPX_CTRL_Status
{
    const FAILED  = 0;
    const SUCCESS = 1;
}

/**
 * A class used to report on controller methods
 */
class DUPX_CTRL_Report
{
    //Properties
    public $runTime;
    public $outputType = 'JSON';
    public $status;

}

/**
 * Base class for all controllers
 */
class DUPX_CTRL_Out
{
    public $report  = null;
    public $payload = null;
    private $timeStart;
    private $timeEnd;

    /**
     *  Init this instance of the object
     */
    public function __construct()
    {
        $this->report  = new DUPX_CTRL_Report();
        $this->payload = null;
        $this->startProcessTime();
    }

    public function startProcessTime()
    {
        $this->timeStart = $this->microtimeFloat();
    }

    public function getProcessTime()
    {
        $this->timeEnd         = $this->microtimeFloat();
        $this->report->runTime = $this->timeEnd - $this->timeStart;
        return $this->report->runTime;
    }

    private function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
}

class DUPX_CTRL
{
    const NAME_MAX_SERIALIZE_STRLEN_IN_M = 'mstrlim';
}