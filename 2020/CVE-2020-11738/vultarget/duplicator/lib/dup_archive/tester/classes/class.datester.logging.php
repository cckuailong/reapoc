<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Robert
 */
class DaTesterLogging extends DupArchiveLoggerBase
{
    private $logFilepath = null;
    private $logHandle          = null;

    public function __construct($logFilepath)
    {
        $this->logFilepath = $logFilepath;
    }

    public function clearLog()
    {
        if (file_exists($this->logFilepath)) {
            if ($this->logHandle !== null) {
                fflush($this->logHandle);
                fclose($this->logHandle);
                $this->logHandle = null;
            }
            @unlink($this->logFilepath);
        }
    }

    public function logObject($s, $o, $flush = false, $callingFunctionOverride = null)
    {
        $this->log($s, false, $callingFunctionOverride);
        $this->log(print_r($o, true), false, $callingFunctionOverride);

        if ($flush) {
            fflush($this->logHandle);
        }
    }

    public function log($s, $flush = false, $callingFunctionOverride = null)
    {
        $lfp = $this->logFilepath;

        if ($this->logFilepath === null) {
            error_log('logging not initialized');
            throw new Exception('Logging not initialized');
        }

        if(isset($_SERVER['REQUEST_TIME_FLOAT'])){
            $timepart = $_SERVER['REQUEST_TIME_FLOAT'];
        } else {
            $timepart = $_SERVER['REQUEST_TIME'];
        }

        $thread_id = sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'].$timepart.$_SERVER['REMOTE_PORT'])));

        $s = $thread_id.' '.date('h:i:s').":$s";

        if ($this->logHandle === null) {

            $this->logHandle = fopen($this->logFilepath, 'a');
        }

        fwrite($this->logHandle, "$s\n");

        if ($flush) {
            fflush($this->logHandle);
            fclose($this->logHandle);

            $this->logHandle = fopen($this->logFilepath, 'a');
        }
    }
    private static $profileLogArray = null;

    public static function initProfiling()
    {
        $this->profileLogArray = array();
    }  
}