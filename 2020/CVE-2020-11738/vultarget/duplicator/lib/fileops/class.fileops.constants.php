<?php
if (!defined("ABSPATH") && !defined("DUPXABSPATH"))
    die("");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FileOpsConstants
{
    public static $FILEOPS_ROOT;
    
    public static $DEFAULT_WORKER_TIME = 18;

    public static $LIB_DIR;

    public static $PROCESS_LOCK_FILEPATH;
    public static $PROCESS_CANCEL_FILEPATH;
    public static $KEY_FILEPATH;

    public static $LOG_FILEPATH;
          
    public static function init() {

        self::$FILEOPS_ROOT = dirname(__FILE__);

        self::$LIB_DIR = self::$FILEOPS_ROOT.'/..';

        self::$PROCESS_LOCK_FILEPATH = self::$FILEOPS_ROOT.'/fileops_lock.bin';
        self::$PROCESS_CANCEL_FILEPATH = self::$FILEOPS_ROOT.'/fileops_cancel.bin';
        self::$LOG_FILEPATH = dirname(__FILE__).'/fileops.log';
    }
}

FileOpsConstants::init();