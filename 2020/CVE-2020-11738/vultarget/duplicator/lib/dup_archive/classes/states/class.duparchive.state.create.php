<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/class.duparchive.state.base.php');

if (!class_exists('DupArchiveCreateState')) {
abstract class DupArchiveCreateState extends DupArchiveStateBase
{
    //const DEFAULT_GLOB_SIZE = 4180000; //512000;
    const DEFAULT_GLOB_SIZE = 1048576;

    public $currentDirectoryIndex = -1;
    public $currentFileIndex = -1;
    public $globSize = self::DEFAULT_GLOB_SIZE;
    public $newBasePath = null;
    public $skippedFileCount = 0;
    public $skippedDirectoryCount = 0;
}
}