<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../class.duparchive.processing.failure.php');

if(!class_exists('DupArchiveStateBase')) {
abstract class DupArchiveStateBase
{
    public $basePath          = '';
    public $archivePath       = '';
    public $isCompressed      = false;
    public $currentFileOffset = -1;
    public $archiveOffset     = -1;
    public $timeSliceInSecs   = -1;
    public $working           = false;
    public $failures          = null;
    public $startTimestamp    = -1;
    public $throttleDelayInUs  = 0;
    public $timeoutTimestamp  = -1;
    public $timerEnabled      = true;
    public $isRobust          = false;

    public function __construct()
    {
        $this->failures = array();
    }

    public function isCriticalFailurePresent()
    {
        if(count($this->failures) > 0) {
            foreach($this->failures as $failure) {
                /* @var $failure DupArchiveProcessingFailure */
                if($failure->isCritical) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getFailureSummary($includeCritical = true, $includeWarnings = false)
    {        
        if(count($this->failures) > 0)
        {
            $message = '';
            
            foreach($this->failures as $failure)
            {
                /* @var $failure DupArchiveProcessingFailure */
                if($includeCritical || !$failure->isCritical) {

                    $message .= "\n" . $this->getFailureString($failure);
                }
            }

            return $message;
        }
        else
        {
            if($includeCritical)
            {
                if($includeWarnings) {
                    return 'No errors or warnings.';
                } else {
                    return 'No errors.';
                }
            } else {
                return 'No warnings.';
            }
        }
    }

    public function getFailureString($failure)
    {
        $s = '';

        if($failure->isCritical) {
            $s = 'CRITICAL: ';
        }

        return "{$s}{$failure->subject} : {$failure->description}";
    }

    public function addFailure($type, $subject, $description, $isCritical = true)
    {
        $failure = new DupArchiveProcessingFailure();

        $failure->type        = $type;
        $failure->subject     = $subject;
        $failure->description = $description;
        $failure->isCritical    = $isCritical;

        $this->failures[] = $failure;

        return $failure;
    }

    public function startTimer()
    {
        if ($this->timerEnabled) {
            $this->timeoutTimestamp = time() + $this->timeSliceInSecs;
        }
    }

    public function timedOut()
    {
        if ($this->timerEnabled) {
            if ($this->timeoutTimestamp != -1) {
                return time() >= $this->timeoutTimestamp;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    //   abstract public function save();
}
}