<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC File class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_file extends MEC_base
{
    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @return string
     */
	public static function getExt($file)
	{
	    $ex = explode('.', $file);
		return end($ex);
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @return string
     */
	public static function stripExt($file)
	{
		return preg_replace('#\.[^.]*$#', '', $file);
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @return string
     */
	public static function makeSafe($file)
	{
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
		return preg_replace($regex, '', $file);
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $src
     * @param string $dest
     * @param string $path
     * @return boolean
     */
	public static function copy($src, $dest, $path = null)
	{
		// Prepend a base path if it exists
		if ($path)
		{
			$src = MEC_path::clean($path . '/' . $src);
			$dest = MEC_path::clean($path . '/' . $dest);
		}

		// Check src path
		if (!is_readable($src))
		{
			return false;
		}

		if (!@ copy($src, $dest))
		{
			return false;
		}
		
		return true;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @return boolean
     */
	public static function delete($file)
	{
		if(is_array($file))
		{
			$files = $file;
		}
		else
		{
			$files[] = $file;
		}

		foreach($files as $file)
		{
			$file = MEC_path::clean($file);
			
			@chmod($file, 0777);
			@unlink($file);
		}

		return true;
	}

    /**
     * @author Webnus <info@webnus.biz>
     * @param string $src
     * @param string $dest
     * @param string $path
     * @return boolean
     */
	public static function move($src, $dest, $path = '')
	{
		if($path)
		{
			$src = MEC_path::clean($path . '/' . $src);
			$dest = MEC_path::clean($path . '/' . $dest);
		}

		// Check src path
		if(!is_readable($src)) return false;
		if(!@rename($src, $dest)) return false;
		
		return true;
	}

    /**
     * @author Webnus <info@webnus.biz>
     * @param string $filename
     * @return boolean
     */
	public static function read($filename)
	{
		// Initialise variables.
		$fh = fopen($filename, 'rb');
		
		if(false === $fh) return false;

		clearstatcache();

		if($fsize = @filesize($filename))
		{
			$data = fread($fh, $fsize);
			
			fclose($fh);
			return $data;
		}
		else
		{
			fclose($fh);
			return false;
		}
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @param string $buffer
     * @return string
     */
	public static function write($file, &$buffer)
	{
		@set_time_limit(ini_get('max_execution_time'));

		// If the destination directory doesn't exist we need to create it
		if (!file_exists(dirname($file)))
		{
			MEC_folder::create(dirname($file));
		}

		$file = MEC_path::clean($file);
		$ret = is_int(file_put_contents($file, $buffer)) ? true : false;

		return $ret;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $src
     * @param string $dest
     * @return boolean
     */
	public static function upload($src, $dest)
	{
		// Ensure that the path is valid and clean
		$dest = MEC_path::clean($dest);
		$baseDir = dirname($dest);

		if (!file_exists($baseDir))
		{
			MEC_folder::create($baseDir);
		}

		if (is_writable($baseDir) && move_uploaded_file($src, $dest))
		{
			// Short circuit to prevent file permission errors
			if (MEC_path::setPermissions($dest)) $ret = true;
			else $ret = false;
		}
		else $ret = false;

		return $ret;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @return string
     */
	public static function exists($file)
	{
		return is_file(MEC_path::clean($file));
	}

    /**
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @return string
     */
	public static function getName($file)
	{
		// Convert back slashes to forward slashes
		$file = str_replace('\\', '/', $file);
		$slash = strrpos($file, '/');
		
		if ($slash !== false)
		{
			return substr($file, $slash + 1);
		}
		else
		{
			return $file;
		}
	}
}

/**
 * Webnus MEC Folder class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_folder extends MEC_base
{
    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $src
     * @param string $dest
     * @param string $path
     * @param boolean $force
     * @return boolean
     */
	public static function copy($src, $dest, $path = '', $force = false)
	{
		@set_time_limit(ini_get('max_execution_time'));

		if ($path)
		{
			$src = MEC_path::clean($path . '/' . $src);
			$dest = MEC_path::clean($path . '/' . $dest);
		}

		// Eliminate trailing directory separators, if any
		$src = rtrim($src, DIRECTORY_SEPARATOR);
		$dest = rtrim($dest, DIRECTORY_SEPARATOR);

		if (!self::exists($src)) return false;
		if (self::exists($dest) && !$force) return false;

		// Make sure the destination exists
		if (!self::create($dest)) return false;
		if (!($dh = @opendir($src))) return false;
		
		// Walk through the directory copying files and recursing into folders.
		while (($file = readdir($dh)) !== false)
		{
			$sfid = $src . '/' . $file;
			$dfid = $dest . '/' . $file;
			
			switch (filetype($sfid))
			{
				case 'dir':
				
					if ($file != '.' && $file != '..')
					{
						$ret = self::copy($sfid, $dfid, null, $force);
						if ($ret !== true)
						{
							return $ret;
						}
					}
					break;

				case 'file':
				
					if (!@copy($sfid, $dfid))
					{
						return false;
					}
					break;
			}
		}
		
		return true;
	}
    
    /**
     * Create a folder -- and all necessary parent folders.
     * @author Webnus <info@webnus.biz>
     * @staticvar int $nested
     * @param string $path
     * @param int $mode
     * @return boolean
     */
	public static function create($path = '', $mode = 0755)
	{
		// Initialise variables.
		static $nested = 0;

		// Check to make sure the path valid and clean
		$path = MEC_path::clean($path);

		// Check if parent dir exists
		$parent = dirname($path);
		
		if (!self::exists($parent))
		{
			// Prevent infinite loops!
			$nested++;
			if (($nested > 20) || ($parent == $path))
			{
				$nested--;
				return false;
			}

			// Create the parent directory
			if (self::create($parent, $mode) !== true)
			{
				// MEC_folder::create throws an error
				$nested--;
				return false;
			}

			// OK, parent directory has been created
			$nested--;
		}

		// Check if dir already exists
		if (self::exists($path))
		{
			return true;
		}

		// We need to get and explode the open_basedir paths
		$obd = ini_get('open_basedir');

		// If open_basedir is set we need to get the open_basedir that the path is in
		if ($obd != null)
		{
			$obdSeparator = ":";
			
			// Create the array of open_basedir paths
			$obdArray = explode($obdSeparator, $obd);
			$inBaseDir = false;
			// Iterate through open_basedir paths looking for a match
			foreach ($obdArray as $test)
			{
				$test = MEC_path::clean($test);
				if (strpos($path, $test) === 0)
				{
					$inBaseDir = true;
					break;
				}
			}
			if ($inBaseDir == false)
			{
				return false;
			}
		}

		// First set umask
		$origmask = @umask(0);

		// Create the path
		if (!$ret = @mkdir($path, $mode))
		{
			@umask($origmask);
			return false;
		}

		// Reset umask
		@umask($origmask);
		
		return $ret;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @return boolean
     */
	public static function delete($path)
	{
		@set_time_limit(ini_get('max_execution_time'));

		// Sanity check
		if (!$path)
		{
			return false;
		}

		// Check to make sure the path valid and clean
		$path = MEC_path::clean($path);

		// Is this really a folder?
		if (!is_dir($path))
		{
			return false;
		}

		// Remove all the files in folder if they exist; disable all filtering
		$files = self::files($path, '.', false, true, array(), array());
		if (!empty($files))
		{
			if (MEC_file::delete($files) !== true)
			{
				return false;
			}
		}

		// Remove sub-folders of folder; disable all filtering
		$folders = self::folders($path, '.', false, true, array(), array());
		foreach ($folders as $folder)
		{
			if (is_link($folder))
			{
				if (MEC_file::delete($folder) !== true)
				{
					return false;
				}
			}
			elseif (self::delete($folder) !== true)
			{
				return false;
			}
		}

		// In case of restricted permissions we zap it one way or the other
		// as long as the owner is either the webserver or the ftp.
		if (@rmdir($path))
		{
			$ret = true;
		}
		else
		{
			$ret = false;
		}
		
		return $ret;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $src
     * @param string $dest
     * @param string $path
     * @return boolean
     */
	public static function move($src, $dest, $path = '')
	{
		if ($path)
		{
			$src = MEC_path::clean($path . '/' . $src);
			$dest = MEC_path::clean($path . '/' . $dest);
		}

		if (!self::exists($src)) return false;
		if (self::exists($dest)) return false;

		if (!@rename($src, $dest))
		{
			return false;
		}
		
		return true;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @return string
     */
	public static function exists($path)
	{
		return is_dir(MEC_path::clean($path));
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @param string $filter
     * @param boolean $recurse
     * @param boolean $full
     * @param array $exclude
     * @param array $excludefilter
     * @return boolean|array
     */
	public static function files($path, $filter = '.', $recurse = false, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $excludefilter = array('^\..*', '.*~'))
	{
		// Check to make sure the path valid and clean
		$path = MEC_path::clean($path);

		// Is the path a folder?
		if (!is_dir($path))
		{
			return false;
		}

		// Compute the excludefilter string
		if (count($excludefilter))
		{
			$excludefilter_string = '/(' . implode('|', $excludefilter) . ')/';
		}
		else
		{
			$excludefilter_string = '';
		}

		// Get the files
		$arr = self::_items($path, $filter, $recurse, $full, $exclude, $excludefilter_string, true);

		// Sort the files
		asort($arr);
		return array_values($arr);
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @param string $filter
     * @param boolean $recurse
     * @param boolean $full
     * @param array $exclude
     * @param array $excludefilter
     * @return boolean|array
     */
	public static function folders($path, $filter = '.', $recurse = false, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $excludefilter = array('^\..*'))
	{
		// Check to make sure the path valid and clean
		$path = MEC_path::clean($path);

		// Is the path a folder?
		if (!is_dir($path))
		{
			return false;
		}

		// Compute the excludefilter string
		if (count($excludefilter))
		{
			$excludefilter_string = '/(' . implode('|', $excludefilter) . ')/';
		}
		else
		{
			$excludefilter_string = '';
		}

		// Get the folders
		$arr = self::_items($path, $filter, $recurse, $full, $exclude, $excludefilter_string, false);

		// Sort the folders
		asort($arr);
		return array_values($arr);
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @param string $filter
     * @param boolean $recurse
     * @param boolean $full
     * @param array $exclude
     * @param array|string $excludefilter_string
     * @param boolean $findfiles
     * @return array
     */
	protected static function _items($path, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles)
	{
		@set_time_limit(ini_get('max_execution_time'));

		// Initialise variables.
		$arr = array();

		// Read the source directory
		if (!($handle = @opendir($path)))
		{
			return $arr;
		}

		while (($file = readdir($handle)) !== false)
		{
			if ($file != '.' && $file != '..' && !in_array($file, $exclude)
				&& (empty($excludefilter_string) || !preg_match($excludefilter_string, $file)))
			{
				// Compute the fullpath
				$fullpath = $path . '/' . $file;

				// Compute the isDir flag
				$isDir = is_dir($fullpath);

				if (($isDir xor $findfiles) && preg_match("/$filter/", $file))
				{
					// (fullpath is dir and folders are searched or fullpath is not dir and files are searched) and file matches the filter
					if ($full)
					{
						// Full path is requested
						$arr[] = $fullpath;
					}
					else
					{
						// Filename is requested
						$arr[] = $file;
					}
				}
				
				if ($isDir && $recurse)
				{
					// Search recursively
					if (is_integer($recurse))
					{
						// Until depth 0 is reached
						$arr = array_merge($arr, self::_items($fullpath, $filter, $recurse - 1, $full, $exclude, $excludefilter_string, $findfiles));
					}
					else
					{
						$arr = array_merge($arr, self::_items($fullpath, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles));
					}
				}
			}
		}
		
		closedir($handle);
		return $arr;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @return string
     */
	public static function makeSafe($path)
	{
		$regex = array('#[^A-Za-z0-9:_\\\/-]#');
		return preg_replace($regex, '', $path);
	}
}

/**
 * Webnus MEC Path class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_path extends MEC_base
{
    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @return boolean
     */
	public static function canChmod($path)
	{
		$perms = fileperms($path);
		if ($perms !== false)
		{
			if (@chmod($path, $perms ^ 0001))
			{
				@chmod($path, $perms);
				return true;
			}
		}

		return false;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @param string $filemode
     * @param string $foldermode
     * @return boolean
     */
	public static function setPermissions($path, $filemode = '0644', $foldermode = '0755')
	{
		// Initialise return value
		$ret = true;

		if (is_dir($path))
		{
			$dh = opendir($path);

			while ($file = readdir($dh))
			{
				if ($file != '.' && $file != '..')
				{
					$fullpath = $path . '/' . $file;
					if (is_dir($fullpath))
					{
						if (!MEC_path::setPermissions($fullpath, $filemode, $foldermode))
						{
							$ret = false;
						}
					}
					else
					{
						if (isset($filemode))
						{
							if (!@ chmod($fullpath, octdec($filemode)))
							{
								$ret = false;
							}
						}
					}
				}
			}
			
			closedir($dh);
			if (isset($foldermode))
			{
				if (!@ chmod($path, octdec($foldermode)))
				{
					$ret = false;
				}
			}
		}
		else
		{
			if (isset($filemode))
			{
				$ret = @ chmod($path, octdec($filemode));
			}
		}

		return $ret;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @return string
     */
	public static function getPermissions($path)
	{
		$path = MEC_path::clean($path);
		$mode = @ decoct(@ fileperms($path) & 0777);

		if(strlen($mode) < 3)
		{
			return '---------';
		}

		$parsed_mode = '';
		for($i = 0; $i < 3; $i++)
		{
			// read
			$parsed_mode .= ($mode[$i] & 04) ? "r" : "-";
			// write
			$parsed_mode .= ($mode[$i] & 02) ? "w" : "-";
			// execute
			$parsed_mode .= ($mode[$i] & 01) ? "x" : "-";
		}

		return $parsed_mode;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @param string $ds
     * @return string
     */
	public static function check($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = MEC_path::clean($path, $ds);
		return $path;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param string $path
     * @param string $ds
     * @return string
     */
	public static function clean($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = trim($path);
		if(empty($path))
		{
			$path = BASE_PATH;
		}
		else
		{
			// Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
			$path = preg_replace('#[/\\\\]+#', $ds, $path);
		}

		return $path;
	}
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param array $paths
     * @param string $file
     * @return boolean
     */
	public static function find($paths, $file)
	{
		settype($paths, 'array'); //force to array

		// Start looping through the path set
		foreach ($paths as $path)
		{
			// Get the path to the file
			$fullname = $path . '/' . $file;

			// Is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// Not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path = realpath($path); // needed for substr() later
				$fullname = realpath($fullname);
			}

			// The substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directories are not accessible via directory
			// traversal attempts.
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		return false;
	}
}