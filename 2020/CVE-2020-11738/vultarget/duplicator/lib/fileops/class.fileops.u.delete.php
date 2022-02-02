<?php
if (!defined("ABSPATH") && !defined("DUPXABSPATH"))
	die("");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FileOpsDeleteConfig
{
	public $workerTime;
	public $directories;
	public $throttleDelayInUs;
	public $excludedDirectories;
	public $excludedFiles;
	public $fileLock;

}

class FileOpsDeleteU
{

	// Move $directories, $files, $excludedFiles to $destination directory. Throws exception if it can't do something and $exceptionOnFaiure is true
	// $exludedFiles can include * wildcard
	// returns: array with list of failures
	public static function delete($currentDirectory, &$deleteConfig)
	{
		$timedOut = false;
		
		if (is_dir($currentDirectory)) {
			$objects = scandir($currentDirectory);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (is_dir($currentDirectory."/".$object)) {
						self::delete($currentDirectory."/".$object, $deleteConfig);
					}
					else {
						@unlink($currentDirectory."/".$object);
					}
				}
			}
			@rmdir($currentDirectory);
		}
	}
}