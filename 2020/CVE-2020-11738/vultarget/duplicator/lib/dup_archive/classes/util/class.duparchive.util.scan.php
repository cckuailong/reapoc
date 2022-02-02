<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/class.duparchive.u.json.php');

if(!class_exists('DupArchiveScanUtil')) {
/**
 * Description of class
 *
 * @author Robert
 */
class DupArchiveScanUtil
{

    public static function getScan($scanFilepath)
//put your code here private function get_scan()
    {
        DupArchiveUtil::tlog("Getting scen");
        $scan_handle = fopen($scanFilepath, 'r');

        if ($scan_handle === false) {
            throw new Exception("Can't open {$scanFilepath}");
        }

        $scan_file = fread($scan_handle, filesize($scanFilepath));

        if ($scan_file === false) {
            throw new Exception("Can't read from {$scanFilepath}");
        }

        // $scan = json_decode($scan_file);
        $scan = DupArchiveJsonU::decode($scan_file);
        if ($scan == null) {
            throw new Exception("Error decoding scan file");
        }

        fclose($scan_handle);

        return $scan;
    }

    public static function createScanObject($sourceDirectory)
    {
        $scan = new stdClass();

        $scan->Dirs  = DupArchiveUtil::expandDirectories($sourceDirectory, true);
        $scan->Files = DupArchiveUtil::expandFiles($sourceDirectory, true);

        return $scan;
    }

    public static function createScan($scanFilepath, $sourceDirectory)
    {
        DupArchiveUtil::tlog("Creating scan");
//        $scan = new stdClass();
//
//        $scan->Dirs  = DupArchiveUtil::expandDirectories($sourceDirectory, true);
//        $scan->Files = DupArchiveUtil::expandFiles($sourceDirectory, true);
////$scan->Files = array();

        $scan = self::createScanObject($sourceDirectory);

        $scan_handle = fopen($scanFilepath, 'w');

        if ($scan_handle === false) {
            echo "Couldn't create scan file";
            die();
        }

        $jsn = DupArchiveJsonU::customEncode($scan);

        fwrite($scan_handle, $jsn);

        //  DupArchiveUtil::tlogObject('jsn', $jsn);

        return $scan;
    }
}
}