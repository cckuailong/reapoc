<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('ZipArchive')) :
/**
 * We just add a last_error variable for comaptibility with our UpdraftPlus_PclZip object
 */
class UpdraftPlus_ZipArchive extends ZipArchive {

		public $last_error = 'Unknown: ZipArchive does not return error messages';
}
endif;

/**
 * A ZipArchive compatibility layer, with behaviour sufficient for our usage of ZipArchive
 */
class UpdraftPlus_PclZip {

	protected $pclzip;

	protected $path;

	protected $addfiles;

	protected $adddirs;

	private $statindex;

	private $include_mtime = false;

	public $last_error;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->addfiles = array();
		$this->adddirs = array();
		// Put this in a non-backed-up, writeable location, to make sure that huge temporary files aren't created and then added to the backup - and that we have somewhere writable
		global $updraftplus;
		if (!defined('PCLZIP_TEMPORARY_DIR')) define('PCLZIP_TEMPORARY_DIR', trailingslashit($updraftplus->backups_dir_location()));
	}

	/**
	 * Used to include mtime in statindex (by default, not done - to save memory; probably a bit paranoid)
	 *
	 * @return null
	 */
	public function ud_include_mtime() {
		if (empty($this->include_mtime)) $this->statindex = null;
		$this->include_mtime = true;
	}

	/**
	 * Magic function for getting an otherwise-undefined class variable
	 *
	 * @param String $name
	 *
	 * @return Boolean|Null|Integer - the value, or null if an unknown variable, or false if something goes wrong
	 */
	public function __get($name) {

		if ('numFiles' == $name) {

			if (empty($this->pclzip)) return false;

			if (!empty($this->statindex)) return count($this->statindex);

			$statindex = $this->pclzip->listContent();

			if (empty($statindex)) {
				$this->statindex = array();
				// We return a value that is == 0, but allowing a PclZip error to be detected (PclZip returns 0 in the case of an error).
				if (0 === $statindex) $this->last_error = $this->pclzip->errorInfo(true);
				return (0 === $statindex) ? false : 0;
			}

			// We used to exclude folders in the case of numFiles (and implemented a private alternative, numAll, that included them), because we had no use for them (we ran a loop over $statindex to build a result that excluded the folders); but that is no longer the case (Dec 2018)
			$this->statindex = $statindex;
			
			return count($this->statindex);
		}

		return null;

	}

	/**
	 * Get stat info for a file
	 *
	 * @param Integer $i The index of the file
	 *
	 * @return Array - the stat info
	 */
	public function statIndex($i) {
		if (empty($this->statindex[$i])) return array('name' => null, 'size' => 0);
		$v = array('name' => $this->statindex[$i]['filename'], 'size' => $this->statindex[$i]['size']);
		if ($this->include_mtime) $v['mtime'] = $this->statindex[$i]['mtime'];
		return $v;
	}

	/**
	 * Compatibility function for WP < 3.7; taken from WP 5.2.2
	 *
	 * @staticvar array $encodings
	 * @staticvar bool  $overloaded
	 *
	 * @param bool $reset - Whether to reset the encoding back to a previously-set encoding.
	 */
	private function mbstring_binary_safe_encoding($reset = false) {
	
		if (function_exists('mbstring_binary_safe_encoding')) return mbstring_binary_safe_encoding($reset);
	
		static $encodings  = array();
		static $overloaded = null;

		if (is_null($overloaded)) {
			$overloaded = function_exists('mb_internal_encoding') && (ini_get('mbstring.func_overload') & 2); // phpcs:ignore  PHPCompatibility.IniDirectives.RemovedIniDirectives.mbstring_func_overloadDeprecated
		}

		if (false === $overloaded) {
			return;
		}

		if (!$reset) {
			$encoding = mb_internal_encoding();
			array_push($encodings, $encoding);
			mb_internal_encoding('ISO-8859-1');
		}

		if ($reset && $encodings) {
			$encoding = array_pop($encodings);
			mb_internal_encoding($encoding);
		}
	}

	/**
	 * Compatibility function for WP < 3.7
	 */
	private function reset_mbstring_encoding() {
		return function_exists('reset_mbstring_encoding') ? reset_mbstring_encoding() : $this->mbstring_binary_safe_encoding(true);
	}
	
	/**
	 * Returns the entry contents using its index. This is used only in PclZip, to get better performance (i.e. no such method exists on other zip objects, so don't call it on them). The caller must be careful not to request more than will fit into available memory.
	 *
	 * @see https://php.net/manual/en/ziparchive.getfromindex.php
	 *
	 * @param Array $indexes - List of indexes for entries
	 *
	 * @return Boolean|Array - Returns a keyed list (keys matching $indexes) of contents of the entry on success or FALSE on failure.
	 */
	public function updraftplus_getFromIndexBulk($indexes) {
	
		$results = array();
	
		// This is just for crazy people with mbstring.func_overload enabled (deprecated from PHP 7.2)
		$this->mbstring_binary_safe_encoding();
		
		$contents = $this->pclzip->extract(PCLZIP_OPT_BY_INDEX, $indexes, PCLZIP_OPT_EXTRACT_AS_STRING);

		$this->reset_mbstring_encoding();
		
		if (0 === $contents) {
			$this->last_error = $this->pclzip->errorInfo(true);
			return false;
		}
		
		if (!is_array($contents)) {
			$this->last_error = 'PclZip::extract() did not return the expected information (1)';
			return false;
		}
		
		foreach ($contents as $item) {
			$index = $item['index'];
			$content = isset($item['content']) ? $item['content'] : '';
			$results[$index] = $content;
		}
		
		return $results;
	
	}
	
	/**
	 * Returns the entry contents using its index
	 *
	 * @see https://php.net/manual/en/ziparchive.getfromindex.php
	 *
	 * @param Integer $index  - Index of the entry
	 * @param Integer $length - The length to be read from the entry. If 0, then the entire entry is read.
	 * @param Integer $flags  - The flags to use to open the archive.
	 *
	 * @return String|Boolean - Returns the contents of the entry on success or FALSE on failure.
	 */
	public function getFromIndex($index, $length = 0, $flags = 0) {
		
		$contents = $this->pclzip->extract(PCLZIP_OPT_BY_INDEX, array($index), PCLZIP_OPT_EXTRACT_AS_STRING);
		
		if (0 === $contents) {
			$this->last_error = $this->pclzip->errorInfo(true);
			return false;
		}
		
		// This also prevents CI complaining about an unused parameter
		if ($flags) {
			error_log("A call to UpdraftPlus_PclZip::getFromIndex() set flags=$flags, but this is not implemented");
		}
		
		if (!is_array($contents)) {
			$this->last_error = 'PclZip::extract() did not return the expected information (1)';
			return false;
		}
		
		$content = array_pop($contents);
		
		if (!isset($content['content'])) {
			$this->last_error = 'PclZip::extract() did not return the expected information (2)';
			return false;
		}

		$results = $content['content'];
		
		return $length ? substr($results, 0, $length) : $results;
		
	}
	
	/**
	 * Open a zip file
	 *
	 * @param String  $path	 - the filesystem path to the zip file
	 * @param Integer $flags - flags for the open operation (see ZipArchive::open() - N.B. may not all be implemented)
	 *
	 * @return Boolean - success or failure. Failure will set self::last_error
	 */
	public function open($path, $flags = 0) {
	
		if (!class_exists('PclZip')) include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
		if (!class_exists('PclZip')) {
			$this->last_error = "No PclZip class was found";
			return false;
		}

		// Route around PHP bug (exact version with the problem not known)
		$ziparchive_create_match = (version_compare(PHP_VERSION, '5.2.12', '>') && defined('ZIPARCHIVE::CREATE')) ? ZIPARCHIVE::CREATE : 1;

		if ($flags == $ziparchive_create_match && file_exists($path)) @unlink($path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$this->pclzip = new PclZip($path);

		if (empty($this->pclzip)) {
			$this->last_error = 'Could not get a PclZip object';
			return false;
		}

		// Make the empty directory we need to implement add_empty_dir()
		global $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();
		if (!is_dir($updraft_dir.'/emptydir') && !mkdir($updraft_dir.'/emptydir')) {
			$this->last_error = "Could not create empty directory ($updraft_dir/emptydir)";
			return false;
		}

		$this->path = $path;

		return true;

	}

	/**
	 * Do the actual write-out - it is assumed that close() is where this is done. Needs to return true/false
	 *
	 * @return boolean
	 */
	public function close() {

		if (empty($this->pclzip)) {
			$this->last_error = 'Zip file was not opened';
			return false;
		}

		global $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();

		$activity = false;

		// Add the empty directories
		foreach ($this->adddirs as $dir) {
			if (false == $this->pclzip->add($updraft_dir.'/emptydir', PCLZIP_OPT_REMOVE_PATH, $updraft_dir.'/emptydir', PCLZIP_OPT_ADD_PATH, $dir)) {
				$this->last_error = $this->pclzip->errorInfo(true);
				return false;
			}
			$activity = true;
		}

		foreach ($this->addfiles as $rdirname => $adirnames) {
			foreach ($adirnames as $adirname => $files) {
				if (false == $this->pclzip->add($files, PCLZIP_OPT_REMOVE_PATH, $rdirname, PCLZIP_OPT_ADD_PATH, $adirname)) {
					$this->last_error = $this->pclzip->errorInfo(true);
					return false;
				}
				$activity = true;
			}
			unset($this->addfiles[$rdirname]);
		}

		$this->pclzip = false;
		$this->addfiles = array();
		$this->adddirs = array();

		clearstatcache();

		if ($activity && filesize($this->path) < 50) {
			$this->last_error = "Write failed - unknown cause (check your file permissions)";
			return false;
		}

		return true;
	}

	/**
	 * Note: basename($add_as) is irrelevant; that is, it is actually basename($file) that will be used. But these are always identical in our usage.
	 *
	 * @param string $file   Specific file to add
	 * @param string $add_as This is the name of the file that it is added as but it is usually the same as $file
	 */
	public function addFile($file, $add_as) {
		// Add the files. PclZip appears to do the whole (copy zip to temporary file, add file, move file) cycle for each file - so batch them as much as possible. We have to batch by dirname(). On a test with 1000 files of 25KB each in the same directory, this reduced the time needed on that directory from 120s to 15s (or 5s with primed caches).
		$rdirname = dirname($file);
		$adirname = dirname($add_as);
		$this->addfiles[$rdirname][$adirname][] = $file;
	}

	/**
	 * PclZip doesn't have a direct way to do this
	 *
	 * @param string $dir Specific Directory to empty
	 */
	public function addEmptyDir($dir) {
		$this->adddirs[] = $dir;
	}

	/**
	 * Extract a path
	 *
	 * @param String $path_to_extract
	 * @param String $path
	 *
	 * @see http://www.phpconcept.net/pclzip/user-guide/55
	 *
	 * @return Array|Integer - either an array with the extracted files or an error. N.B. "If one file extraction fail, the full extraction does not fail. The method does not return an error, but the file status is set with the error reason."
	 */
	public function extract($path_to_extract, $path) {
		return $this->pclzip->extract(PCLZIP_OPT_PATH, $path_to_extract, PCLZIP_OPT_BY_NAME, $path);
	}
}

class UpdraftPlus_BinZip extends UpdraftPlus_PclZip {

	private $binzip;

	/**
	 * Class constructor
	 */
	public function __construct() {
		global $updraftplus_backup;
		$this->binzip = $updraftplus_backup->binzip;
		if (!is_string($this->binzip)) {
			$this->last_error = "No binary zip was found";
			return false;
		}
		return parent::__construct();
	}

	public function addFile($file, $add_as) {

		global $updraftplus;
		// Get the directory that $add_as is relative to
		$base = UpdraftPlus_Manipulation_Functions::str_lreplace($add_as, '', $file);

		if ($file == $base) {
			// Shouldn't happen; but see: https://bugs.php.net/bug.php?id=62119
			$updraftplus->log("File skipped due to unexpected name mismatch (locale: ".setlocale(LC_CTYPE, "0")."): file=$file add_as=$add_as", 'notice', false, true);
		} else {
			$rdirname = untrailingslashit($base);
			// Note: $file equals $rdirname/$add_as
			$this->addfiles[$rdirname][] = $add_as;
		}

	}

	/**
	 * The standard zip binary cannot list; so we use PclZip for that
	 * Do the actual write-out - it is assumed that close() is where this is done. Needs to return true/false
	 *
	 * @return Boolean - success or failure state
	 */
	public function close() {

		if (empty($this->pclzip)) {
			$this->last_error = 'Zip file was not opened';
			return false;
		}

		global $updraftplus, $updraftplus_backup;

		// BinZip does not like zero-sized zip files
		if (file_exists($this->path) && 0 == filesize($this->path)) @unlink($this->path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$descriptorspec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);
		$exec = $this->binzip;
		if (defined('UPDRAFTPLUS_BINZIP_OPTS') && UPDRAFTPLUS_BINZIP_OPTS) $exec .= ' '.UPDRAFTPLUS_BINZIP_OPTS;
		$exec .= " -v -@ ".escapeshellarg($this->path);

		$last_recorded_alive = time();
		$something_useful_happened = $updraftplus->something_useful_happened;
		$orig_size = file_exists($this->path) ? filesize($this->path) : 0;
		$last_size = $orig_size;
		clearstatcache();

		$added_dirs_yet = false;

		// If there are no files to add, but there are empty directories, then we need to make sure the directories actually get added
		if (0 == count($this->addfiles) && 0 < count($this->adddirs)) {
			$dir = realpath($updraftplus_backup->make_zipfile_source);
			$this->addfiles[$dir] = '././.';
		}
		// Loop over each destination directory name
		foreach ($this->addfiles as $rdirname => $files) {

			$process = function_exists('proc_open') ? proc_open($exec, $descriptorspec, $pipes, $rdirname) : false;

			if (!is_resource($process)) {
				$updraftplus->log('BinZip error: proc_open failed');
				$this->last_error = 'BinZip error: proc_open failed';
				return false;
			}

			if (!$added_dirs_yet) {
				// Add the directories - (in fact, with binzip, non-empty directories automatically have their entries added; but it doesn't hurt to add them explicitly)
				foreach ($this->adddirs as $dir) {
					fwrite($pipes[0], $dir."/\n");
				}
				$added_dirs_yet = true;
			}

			$read = array($pipes[1], $pipes[2]);
			$except = null;

			if (!is_array($files) || 0 == count($files)) {
				fclose($pipes[0]);
				$write = array();
			} else {
				$write = array($pipes[0]);
			}

			while ((!feof($pipes[1]) || !feof($pipes[2]) || (is_array($files) && count($files)>0)) && false !== ($changes = @stream_select($read, $write, $except, 0, 200000))) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

				if (is_array($write) && in_array($pipes[0], $write) && is_array($files) && count($files)>0) {
					$file = array_pop($files);
					// Send the list of files on stdin
					fwrite($pipes[0], $file."\n");
					if (0 == count($files)) fclose($pipes[0]);
				}

				if (is_array($read) && in_array($pipes[1], $read)) {
					$w = fgets($pipes[1]);
					// Logging all this really slows things down; use debug to mitigate
					if ($w && $updraftplus_backup->debug) $updraftplus->log("Output from zip: ".trim($w), 'debug');
					if (time() > $last_recorded_alive + 5) {
						UpdraftPlus_Job_Scheduler::record_still_alive();
						$last_recorded_alive = time();
					}
					if (file_exists($this->path)) {
						$new_size = @filesize($this->path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						if (!$something_useful_happened && $new_size > $orig_size + 20) {
							UpdraftPlus_Job_Scheduler::something_useful_happened();
							$something_useful_happened = true;
						}
						clearstatcache();
						// Log when 20% bigger or at least every 50MB
						if ($new_size > $last_size*1.2 || $new_size > $last_size + 52428800) {
							$updraftplus->log(basename($this->path).sprintf(": size is now: %.2f MB", round($new_size/1048576, 1)));
							$last_size = $new_size;
						}
					}
				}

				if (is_array($read) && in_array($pipes[2], $read)) {
					$last_error = fgets($pipes[2]);
					if (!empty($last_error)) $this->last_error = rtrim($last_error);
				}

				// Re-set
				$read = array($pipes[1], $pipes[2]);
				$write = (is_array($files) && count($files) >0) ? array($pipes[0]) : array();
				$except = null;

			}

			fclose($pipes[1]);
			fclose($pipes[2]);

			$ret = function_exists('proc_close') ? proc_close($process) : -1;

			if (0 != $ret && 12 != $ret) {
				if ($ret < 128) {
					$updraftplus->log("Binary zip: error (code: $ret - look it up in the Diagnostics section of the zip manual at http://infozip.sourceforge.net/FAQ.html#error-codes for interpretation... and also check that your hosting account quota is not full)");
				} else {
					$updraftplus->log("Binary zip: error (code: $ret - a code above 127 normally means that the zip process was deliberately killed ... and also check that your hosting account quota is not full)");
				}
				if (!empty($w) && !$updraftplus_backup->debug) $updraftplus->log("Last output from zip: ".trim($w), 'debug');
				return false;
			}

			unset($this->addfiles[$rdirname]);
		}

		return true;
	}
}
