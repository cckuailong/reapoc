<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/class.duparchive.state.base.php');

if (!class_exists('DupArchiveValidationTypes')) {

class DupArchiveValidationTypes
{
	const None	 = 0;
	const Standard = 1;
	const Full	 = 2;

}
}

if (!class_exists('DupArchiveExpandState')) {
	abstract class DupArchiveExpandState extends DupArchiveStateBase
	{
		public $archiveHeader			 = null;
		public $currentFileHeader		 = null;
		public $validateOnly			 = false;
		public $validationType			 = DupArchiveValidationTypes::Standard;
		public $fileWriteCount			 = 0;
		public $directoryWriteCount		 = 0;
		public $expectedFileCount		 = -1;
		public $expectedDirectoryCount	 = -1;
		public $filteredDirectories		 = array();
		public $filteredFiles			 = array();
		public $includedFiles			 = array();
		public $fileRenames				 = array();
		public $directoryModeOverride	 = -1;
		public $fileModeOverride		 = -1;
		public $lastHeaderOffset		 = -1;

		public function resetForFile()
		{
			$this->currentFileHeader = null;
			$this->currentFileOffset = 0;
		}
	}
}