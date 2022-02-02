<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DAWSConstants
{
    public static $DAWS_ROOT;

    public static $DUPARCHIVE_DIR;
    public static $DUPARCHIVE_CLASSES_DIR;
    public static $DUPARCHIVE_STATES_DIR;
    public static $DUPARCHIVE_UTIL_DIR;
    public static $DEFAULT_WORKER_TIME = 18;

    public static $LIB_DIR;

    public static $PROCESS_LOCK_FILEPATH;
    public static $PROCESS_CANCEL_FILEPATH;
    public static $LOG_FILEPATH;
          
    public static function init() {

        self::$DAWS_ROOT = dirname(__FILE__);

        self::$DUPARCHIVE_DIR = self::$DAWS_ROOT.'/..';
        self::$DUPARCHIVE_CLASSES_DIR = self::$DUPARCHIVE_DIR.'/classes';
        self::$DUPARCHIVE_STATES_DIR = self::$DUPARCHIVE_CLASSES_DIR.'/states';
        self::$DUPARCHIVE_UTIL_DIR = self::$DUPARCHIVE_CLASSES_DIR.'/util';

        self::$LIB_DIR = self::$DAWS_ROOT.'/../..';

        self::$PROCESS_LOCK_FILEPATH = self::$DAWS_ROOT.'/dawslock.bin';
        self::$PROCESS_CANCEL_FILEPATH = self::$DAWS_ROOT.'/dawscancel.bin';
        self::$LOG_FILEPATH = dirname(__FILE__).'/daws.log';
    }
}

DAWSConstants::init();