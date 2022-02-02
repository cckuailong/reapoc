<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Util
 *
 * @author Bob
 */

require_once(dirname(__FILE__).'/../class.duparchive.constants.php');
require_once(DupArchiveConstants::$LibRoot.'/snaplib/class.snaplib.u.util.php');

if(!class_exists('DupArchiveUtil')) {
class DupArchiveUtil
{
    public static $TRACE_ON = false;    //rodo rework this
    public static $logger = null;
    public static $profilingFunction = null;

    public static function boolToString($b)
    {
        return ($b ? 'true' : 'false');
    }

    public static function expandFiles($base_dir, $recurse)
    {
        $files = array();

        foreach (scandir($base_dir) as $file) {
            if (($file == '.') || ($file == '..')) {
                continue;
            }

            $file = "{$base_dir}/{$file}";

            if (is_file($file)) {
                $files [] = $file;
            } else if (is_dir($file) && $recurse) {
                $files = array_merge($files, self::expandFiles($file, $recurse));
            }
        }

        return $files;
    }

    public static function expandDirectories($base_dir, $recurse)
    {
        $directories = array();

        foreach (scandir($base_dir) as $candidate) {

            if (($candidate == '.') || ($candidate == '..')) {
                continue;
            }

            $candidate = "{$base_dir}/{$candidate}";

            // if (is_file($file)) {
            //     $directories [] = $file;
            if (is_dir($candidate)) {

                $directories[] = $candidate;

                if ($recurse) {

                    $directories = array_merge($directories, self::expandDirectories($candidate, $recurse));
                }
            }
        }

        return $directories;
    }

    public static function getRelativePath($from, $to, $newBasePath = null)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/').'/' : $from;
        $to   = is_dir($to) ? rtrim($to, '\/').'/' : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from    = explode('/', $from);
        $to      = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath   = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    //$relPath[0] = './' . $relPath[0];
                }
            }
        }
        
        $r = implode('/', $relPath);

        if($newBasePath != null) {
            $r = $newBasePath . $r;
        }

        return $r;
    }

    public static function log($s, $flush = false, $callingFunctionName = null)
    {
        if(self::$logger != null)
        {
            if($callingFunctionName === null)
            {
                $callingFunctionName = DupLiteSnapLibUtil::getCallingFunctionName();
            }

            self::$logger->log($s, $flush, $callingFunctionName);
        }
        else
        {
         //   throw new Exception('Logging object not initialized');
        }
    }

    // rodo fold into log
    public static function tlog($s, $flush = false, $callingFunctionName = null)
    {
        if (self::$TRACE_ON) {

            if($callingFunctionName === null)
            {
                $callingFunctionName = DupLiteSnapLibUtil::getCallingFunctionName();
            }

            self::log("####{$s}", $flush, $callingFunctionName);
        }
    }

    public static function profileEvent($s, $start)
    {
        if(self::$profilingFunction != null)
        {
            call_user_func(self::$profilingFunction, $s, $start);
        }
    }

    // rodo fold into logObject
    public static function tlogObject($s, $o, $flush = false, $callingFunctionName = null)
    {
        if(is_object($o))
        {
            $o = get_object_vars($o);
        }

        $ostring = print_r($o, true);

        if($callingFunctionName === null)
        {
            $callingFunctionName = DupLiteSnapLibUtil::getCallingFunctionName();
        }

        self::tlog($s, $flush, $callingFunctionName);
        self::tlog($ostring, $flush, $callingFunctionName);
    }

    public static function logObject($s, $o, $flush = false, $callingFunctionName = null)
    {
        $ostring = print_r($o, true);

        if($callingFunctionName === null)
        {
            $callingFunctionName = DupLiteSnapLibUtil::getCallingFunctionName();
        }

        self::log($s, $flush, $callingFunctionName);
        self::log($ostring, $flush, $callingFunctionName);
    }
}
}