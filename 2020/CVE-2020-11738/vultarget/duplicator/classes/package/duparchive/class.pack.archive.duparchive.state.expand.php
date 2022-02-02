<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once (DUPLICATOR_PLUGIN_PATH.'lib/dup_archive/classes/states/class.duparchive.state.expand.php');

class DUP_DupArchive_Expand_State extends DupArchiveExpandState
{
    public static function getInstance($reset = false)
    {   
        $instance = new DUP_DupArchive_Expand_State();
        
        if ($reset) {            
            $instance->initMembers();
        } else {
            $instance->loadMembers();            
        }

        return $instance;
    }

    private function loadMembers()
    {
        $data = DUP_Settings::Get('duparchive_expand_state');

        DUP_LOG::traceObject("****RAW EXPAND STATE LOADED****", $data);

        if($data->currentFileHeaderString != null) {
            $this->currentFileHeader      = DUP_JSON::decode($data->currentFileHeaderString);
        } else {
            $this->currentFileHeader      = null;
        }

        if($data->archiveHeaderString != null) {
            $this->archiveHeader      = DUP_JSON::decode($data->archiveHeaderString);
        } else {
            $this->archiveHeader      = null;
        }
        
        if($data->failuresString)
        {
            $this->failures = DUP_JSON::decode($data->failuresString);
        }
        else
        {
            $this->failures = array();
        }

        DUP_Util::objectCopy($data, $this, array('archiveHeaderString', 'currentFileHeaderString', 'failuresString'));

//
//        $this->archiveOffset         = $data->archiveOffset;
//        $this->archivePath           = $data->archivePath;
//        $this->basePath              = $data->basePath;
//        $this->currentFileOffset     = $data->currentFileOffset;
//        $this->failures              = $data->failures;
//        $this->isCompressed          = $data->isCompressed;
//        $this->startTimestamp        = $data->startTimestamp;
//        $this->timeSliceInSecs       = $data->timeSliceInSecs;
//        $this->fileWriteCount        = $data->fileWriteCount;
//        $this->directoryWriteCount   = $data->directoryWriteCount;
//        $this->working               = $data->working;
//        $this->directoryModeOverride = $data->directoryModeOverride;
//        $this->fileModeOverride      = $data->fileModeOverride;
//        $this->throttleDelayInUs     = $data->throttleDelayInUs;
//        $this->validateOnly          = $data->validateOnly;
//        $this->validationType        = $data->validationType;
    }

    public function save()
    {
        $data = new stdClass();

        if($this->currentFileHeader != null) {
            $data->currentFileHeaderString      = DupLiteSnapJsonU::wp_json_encode($this->currentFileHeader);
        } else {
            $data->currentFileHeaderString      = null;
        }

        if($this->archiveHeader != null) {
            $data->archiveHeaderString      = DupLiteSnapJsonU::wp_json_encode($this->archiveHeader);
        } else {
            $data->archiveHeaderString      = null;
        }

        $data->failuresString = DupLiteSnapJsonU::wp_json_encode($this->failures, JSON_FORCE_OBJECT);

        // Object members auto skipped
        DUP_Util::objectCopy($this, $data);

//        $data->archiveOffset         = $this->archiveOffset;
//        $data->archivePath           = $this->archivePath;
//        $data->basePath              = $this->basePath;
//        $data->currentFileOffset     = $this->currentFileOffset;
//        $data->failures              = $this->failures;
//        $data->isCompressed          = $this->isCompressed;
//        $data->startTimestamp        = $this->startTimestamp;
//        $data->timeSliceInSecs       = $this->timeSliceInSecs;
//        $data->fileWriteCount        = $this->fileWriteCount;
//        $data->directoryWriteCount   = $this->directoryWriteCount;
//        $data->working               = $this->working;
//        $data->directoryModeOverride = $this->directoryModeOverride;
//        $data->fileModeOverride      = $this->fileModeOverride;
//        $data->throttleDelayInUs     = $this->throttleDelayInUs;
//        $data->validateOnly          = $this->validateOnly;
//        $data->validationType        = $this->validationType;

        DUP_LOG::traceObject("****SAVING EXPAND STATE****", $this);
        DUP_LOG::traceObject("****SERIALIZED STATE****", $data);
        DUP_Settings::Set('duparchive_expand_state', $data);
        DUP_Settings::Save();
    }

    private function initMembers()
    {
        $this->currentFileHeader = null;
        $this->archiveOffset         = 0;
        $this->archiveHeader         = null;
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
