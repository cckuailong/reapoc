<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/**
 * Recursivly scans a directory and finds all sym-links and unreadable files
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 *
 */

// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

class DUP_ScanCheck
{
    /**
     * The number of files scanned
     */
    public $fileCount = 0;

    /**
     * The number of directories scanned
     */
    public $dirCount  = 0;

    /**
     * The maximum count of files before the recursive function stops
     */
    public $maxFiles = 1000000;

    /**
     * The maximum count of directories before the recursive function stops
     */
    public $maxDirs = 75000;

    /**
     * Recursivly scan the root directory provided
     */
    public $recursion = true;

    /**
     * Stores a list of symbolic link files
     */
    public $symLinks = array();

    /**
     *  Stores a list of files unreadable by PHP
     */
    public $unreadable = array();

	 /**
     *  Stores a list of dirs with utf8 settings
     */
    public $nameTestDirs = array();

	 /**
     *  Stores a list of files with utf8 settings
     */
    public $nameTestFiles = array();

    /**
     *  If the maxFiles or maxDirs limit is reached then true
     */
    protected $limitReached = false;

    /**
     *  Is the server running on Windows
     */
    private $isWindows = false;

    /**
     *  Init this instance of the object
     */
    function __construct()
    {
       $this->isWindows = defined('PHP_WINDOWS_VERSION_BUILD');
    }

    /**
     * Start the scan process
     *
     * @param string $dir A valid directory path where the scan will run
     * @param array $results Used for recursion, do not pass in value with calling
     *
     * @return obj  The scan check object with the results of the scan
     */
    public function run($dir, &$results = array())
    {
        //Stop Recursion if Max search is reached
        if ($this->fileCount > $this->maxFiles || $this->dirCount > $this->maxDirs) {
            $this->limitReached = true;
            return $results;
        }

        $files = @scandir($dir);
        if (is_array($files)) {
            foreach ($files as $key => $value) {
                $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
                if ($path) {
                    //Files
                    if (!is_dir($path)) {
                        if (!is_readable($path)) {
                            $results[]          = $path;
                            $this->unreadable[] = $path;
                        } else if ($this->isLink($path)) {
                            $results[]        = $path;
                            $this->symLinks[] = $path;
                        } else {
							$name = basename($path);
							$invalid_test =  preg_match('/(\/|\*|\?|\>|\<|\:|\\|\|)/', $name)
								|| trim($name) == ''
								|| (strrpos($name, '.') == strlen($name) - 1 && substr($name, -1) == '.')
								|| preg_match('/[^\x20-\x7f]/', $name);

							if ($invalid_test) {
								if (! DUP_Util::$PHP7_plus && DUP_Util::isWindows()) {
									$this->nameTestFiles[] = utf8_decode($path);
								} else {
									$this->nameTestFiles[] = $path;
								}
							}
						}
                        $this->fileCount++;
                    }
                    //Dirs
                    else if ($value != "." && $value != "..") {
                        if (!$this->isLink($path) && $this->recursion) {
                            $this->Run($path, $results);
                        }

                        if (!is_readable($path)) {
                            $results[]          = $path;
                            $this->unreadable[] = $path;
                        } else if ($this->isLink($path)) {
                            $results[]        = $path;
                            $this->symLinks[] = $path;
                        } else {

							$invalid_test = strlen($path) > 244
								|| trim($path) == ''
								|| preg_match('/[^\x20-\x7f]/', $path);

							if ($invalid_test) {
								if (! DUP_Util::$PHP7_plus && DUP_Util::isWindows()) {
									$this->nameTestDirs[] = utf8_decode($path);
								} else {
									$this->nameTestDirs[] = $path;
								}
							}
						}

                        $this->dirCount++;
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Separation logic for supporting how different operating systems work
     *
     * @param string $target A valid file path
     *
     * @return bool  Is the target a sym link
     */
    private function isLink($target)
    {
		//Currently Windows does not support sym-link detection
        if ($this->isWindows) {
           return false;
        } elseif (is_link($target)) {
            return true;
        }
        return false;
    }
}