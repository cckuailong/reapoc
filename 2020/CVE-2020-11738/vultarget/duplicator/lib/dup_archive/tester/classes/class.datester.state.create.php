<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(DUPARCHIVE_STATES_DIR.'/class.duparchive.state.create.php');

/**
 * Description of BuildState
 *
 * @author Bob
 */
// Note: All this stuff needs to be stored in the processstate of the package
class DaTesterCreateState extends DupArchiveCreateState
{
    public static $instance = null;

    const StateFilename = 'createstate.json';

    public static function getInstance($reset = false)
    {
        if ((self::$instance == null) && (!$reset)) {
            $stateFilepath = dirname(__FILE__).'/'.self::StateFilename;

            self::$instance = new DaTesterCreateState();

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
            self::$instance = new DaTesterCreateState();

            self::$instance->reset();
        }

        return self::$instance;
    }

    private function setFromData($data)
    {
        $this->archiveOffset         = $data->archiveOffset;
        $this->archivePath           = $data->archivePath;
        $this->basePath              = $data->basePath;
        $this->globSize              = $data->globSize;
        $this->currentFileIndex      = $data->currentFileIndex;
        $this->currentDirectoryIndex = $data->currentDirectoryIndex;
        $this->currentFileOffset     = $data->currentFileOffset;
        $this->failures              = $data->failures;
        $this->isCompressed          = $data->isCompressed;
        $this->startTimestamp        = $data->startTimestamp;
        $this->timeSliceInSecs       = $data->timeSliceInSecs;
        $this->working               = $data->working;
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
        $this->archiveOffset         = 0;
        $this->archivePath           = null;
        $this->basePath              = null;
        $this->globSize              = -1;
        $this->currentFileIndex      = 0;
        $this->currentFileOffset     = 0;
        $this->currentDirectoryIndex = 0;
        $this->fileWriteCount        = 0;
        $this->directoryWriteCount   = 0;
        $this->failures              = array();
        $this->isCompressed          = false;
        $this->startTimestamp        = time();
        $this->timeSliceInSecs       = -1;
        $this->working               = false;
        $this->throttleDelayInUs     = 0;
    }
}