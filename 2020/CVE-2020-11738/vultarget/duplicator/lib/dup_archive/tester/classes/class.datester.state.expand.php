<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(DUPARCHIVE_STATES_DIR.'/class.duparchive.state.expand.php');

class DaTesterExpandState extends DupArchiveExpandState
{
    public static $instance = null;

    const StateFilename = 'expandstate.json';

    public static function getInstance($reset = false)
    {
        if ((self::$instance == null) && (!$reset)) {
            $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

            self::$instance = new DaTesterExpandState();

            if (file_exists($stateFilepath)) {
                $stateHandle = DupLiteSnapLibIOU::fopen($stateFilepath, 'r');

                DupLiteSnapLibIOU::flock($stateHandle, LOCK_EX);

                $stateString = fread($stateHandle, filesize($stateFilepath));

                $data = json_decode($stateString);

                self::$instance->setFromData($data);

                DupLiteSnapLibIOU::flock($stateHandle, LOCK_UN);

                DupLiteSnapLibIOU::fclose($stateHandle);
            } else {
                $reset = true;
            }
        }

        if ($reset) {
            self::$instance = new DaTesterExpandState();

            self::$instance->reset();
        }

        return self::$instance;
    }

    private function setFromData($data)
    {
        $this->currentFileHeader     = $data->currentFileHeader;
        $this->archiveHeader         = $data->archiveHeader;
        $this->archiveOffset         = $data->archiveOffset;
        $this->archivePath           = $data->archivePath;
        $this->basePath              = $data->basePath;
        $this->currentFileOffset     = $data->currentFileOffset;
        $this->failures              = $data->failures;
        $this->isCompressed          = $data->isCompressed;
        $this->startTimestamp        = $data->startTimestamp;
        $this->timeSliceInSecs       = $data->timeSliceInSecs;
        $this->validateOnly          = $data->validateOnly;
        $this->fileWriteCount        = $data->fileWriteCount;
        $this->directoryWriteCount   = $data->directoryWriteCount;
        $this->working               = $data->working;
        $this->directoryModeOverride = $data->directoryModeOverride;
        $this->fileModeOverride      = $data->fileModeOverride;
        $this->throttleDelayInUs     = $data->throttleDelayInUs;
    }

    public function reset()
    {
        $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

        $stateHandle = DupLiteSnapLibIOU::fopen($stateFilepath, 'w');

        DupLiteSnapLibIOU::flock($stateHandle, LOCK_EX);

        $this->initMembers();

        DupLiteSnapLibIOU::fwrite($stateHandle, json_encode($this));

        DupLiteSnapLibIOU::fclose($stateHandle);
    }

    public function save()
    {
        $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

        $stateHandle = DupLiteSnapLibIOU::fopen($stateFilepath, 'w');

        DupLiteSnapLibIOU::flock($stateHandle, LOCK_EX);

        DupArchiveUtil::tlog("saving state");
        DupLiteSnapLibIOU::fwrite($stateHandle, json_encode($this));

        DupLiteSnapLibIOU::fclose($stateHandle);
    }

    private function initMembers()
    {
        $this->currentFileHeader = null;

        $this->archiveOffset         = 0;
        $this->archiveHeader         = 0;
        $this->archivePath           = null;
        $this->basePath              = null;
        $this->currentFileOffset     = 0;
        $this->failures              = array();
        $this->isCompressed          = false;
        $this->startTimestamp        = time();
        $this->timeSliceInSecs       = -1;
        $this->working               = false;
        $this->validateOnly          = false;
        $this->directoryModeOverride = -1;
        $this->fileModeOverride      = -1;
        $this->throttleDelayInUs     = 0;
    }
}