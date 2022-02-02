<?php
/**
 * Snap I/O utils
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DupLiteSnapLib
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!class_exists('DupLiteSnapLibIOU', false)) {

    require_once(dirname(__FILE__).'/class.snaplib.u.string.php');
    require_once(dirname(__FILE__).'/class.snaplib.u.os.php');

    class DupLiteSnapLibIOU
    {

        // Real upper bound of a signed int is 214748364.
        // The value chosen below, makes sure we have a buffer of ~4.7 million.
        const FileSizeLimit32BitPHP = 1900000000;

        public static function rmPattern($filePathPattern)
        {
            @array_map('unlink', glob($filePathPattern));
        }

        public static function chmodPattern($filePathPattern, $mode)
        {
            $filePaths = glob($filePathPattern);

            $modes = array();

            foreach ($filePaths as $filePath) {
                $modes[] = $mode;
            }
            array_map(array(__CLASS__, 'chmod'), $filePaths, $modes);
        }

        public static function copy($source, $dest, $overwriteIfExists = true)
        {
            if (file_exists($dest)) {
                if ($overwriteIfExists) {
                    self::rm($dest);
                } else {
                    throw new Exception("Can't copy {$source} to {$dest} because {$dest} already exists!");
                }
            }

            if (copy($source, $dest) === false) {
                throw new Exception("Error copying {$source} to {$dest}");
            }
        }

        public static function safePath($path, $real = false)
        {
            if ($real) {
                $path = realpath($path);
            }

            return str_replace("\\", "/", $path);
        }

        public static function massMove($fileSystemObjects, $destination, $exclusions = null, $exceptionOnError = true)
        {
            $failures = array();

            $destination = rtrim($destination, '/\\');

            if (!file_exists($destination) || !is_writeable($destination)) {
                self::mkdir($destination, 'u+rwx');
            }

            foreach ($fileSystemObjects as $fileSystemObject) {
                $shouldMove = true;

                if ($exclusions != null) {

                    foreach ($exclusions as $exclusion) {
                        if (preg_match($exclusion, $fileSystemObject) === 1) {
                            $shouldMove = false;
                            break;
                        }
                    }
                }

                if ($shouldMove) {

                    $newName = $destination.'/'.basename($fileSystemObject);

                    if (!file_exists($fileSystemObject)) {
                        $failures[] = "Tried to move {$fileSystemObject} to {$newName} but it didn't exist!";
                    } else if (!@rename($fileSystemObject, $newName)) {
                        $failures[] = "Couldn't move {$fileSystemObject} to {$newName}";
                    }
                }
            }

            if ($exceptionOnError && count($failures) > 0) {
                throw new Exception(implode(',', $failures));
            }

            return $failures;
        }

        public static function rename($oldname, $newname, $removeIfExists = false)
        {
            if ($removeIfExists) {
                if (file_exists($newname)) {
                    if (is_dir($newname)) {
                        self::rmdir($newname);
                    } else {
                        self::rm($newname);
                    }
                }
            }

            if (!@rename($oldname, $newname)) {
                throw new Exception("Couldn't rename {$oldname} to {$newname}");
            }
        }

        public static function fopen($filepath, $mode, $throwOnError = true)
        {
            if (strlen($filepath) > DupLiteSnapLibOSU::maxPathLen()) {
                throw new Exception('Skipping a file that exceeds allowed max path length ['.DupLiteSnapLibOSU::maxPathLen().']. File: '.$filepath);
            }

            if (DupLiteSnapLibStringU::startsWith($mode, 'w') || DupLiteSnapLibStringU::startsWith($mode, 'c') || file_exists($filepath)) {
                $file_handle = @fopen($filepath, $mode);
            } else {
                if ($throwOnError) {
                    throw new Exception("$filepath doesn't exist");
                } else {
                    return false;
                }
            }

            if ($file_handle === false) {
                if ($throwOnError) {
                    throw new Exception("Error opening $filepath");
                } else {
                    return false;
                }
            } else {
                return $file_handle;
            }
        }

        public static function touch($filepath, $time = null)
        {
            if (!function_exists('touch')) {
                return false;
            }

            if (!is_writeable($filepath)) {
                return false;
            }

            if ($time === null) {
                $time = time();
            }
            return @touch($filepath, $time);
        }

        public static function rmdir($dirname, $mustExist = false)
        {
            if (file_exists($dirname)) {
                self::chmod($dirname, 'u+rwx');
                if (@rmdir($dirname) === false) {
                    throw new Exception("Couldn't remove {$dirname}");
                }
            } else if ($mustExist) {
                throw new Exception("{$dirname} doesn't exist");
            }
        }

        public static function rm($filepath, $mustExist = false)
        {
            if (file_exists($filepath)) {
                self::chmod($filepath, 'u+rw');
                if (@unlink($filepath) === false) {
                    throw new Exception("Couldn't remove {$filepath}");
                }
            } else if ($mustExist) {
                throw new Exception("{$filepath} doesn't exist");
            }
        }

        public static function fwrite($handle, $string)
        {
            $bytes_written = @fwrite($handle, $string);

            if ($bytes_written === false) {
                throw new Exception('Error writing to file.');
            } else {
                return $bytes_written;
            }
        }

        public static function fgets($handle, $length)
        {
            $line = fgets($handle, $length);

            if ($line === false) {
                throw new Exception('Error reading line.');
            }

            return $line;
        }

        public static function fclose($handle, $exception_on_fail = true)
        {
            if ((@fclose($handle) === false) && $exception_on_fail) {
                throw new Exception("Error closing file");
            }
        }

        public static function flock($handle, $operation)
        {
            if (@flock($handle, $operation) === false) {
                throw new Exception("Error locking file");
            }
        }

        public static function ftell($file_handle)
        {
            $position = @ftell($file_handle);

            if ($position === false) {
                throw new Exception("Couldn't retrieve file offset for $filepath");
            } else {
                return $position;
            }
        }

        /**
         * Safely remove a directory and recursively files adnd directory upto multiple sublevels
         *
         * @param path $dir The full path to the directory to remove
         *
         * @return bool Returns true if all content was removed
         */
        public static function rrmdir($path)
        {
            if (is_dir($path)) {
                if (($dh = opendir($path)) === false) {
                    return false;
                }
                while (($object = readdir($dh)) !== false) {
                    if ($object == "." || $object == "..") {
                        continue;
                    }
                    if (!self::rrmdir($path."/".$object)) {
                        closedir($dh);
                        return false;
                    }
                }
                closedir($dh);
                return @rmdir($path);
            } else {
                if (is_writable($path)) {
                    return @unlink($path);
                } else {
                    return false;
                }
            }
        }

        public static function filesize($filename)
        {
            $file_size = @filesize($filename);

            if ($file_size === false) {
                throw new Exception("Error retrieving file size of $filename");
            }

            return $file_size;
        }

        public static function fseek($handle, $offset, $whence = SEEK_SET)
        {
            $ret_val = @fseek($handle, $offset, $whence);

            if ($ret_val !== 0) {
                $filepath = stream_get_meta_data($handle);
                $filepath = $filepath["uri"];
                $filesize = self::filesize($filepath);
                if ($ret_val === false) {
                    throw new Exception("Trying to fseek($offset, $whence) and came back false");
                }
                //This check is not strict, but in most cases 32 Bit PHP will be the issue
                else if (abs($offset) > self::FileSizeLimit32BitPHP || $filesize > self::FileSizeLimit32BitPHP || ($offset < 0 && ($whence == SEEK_SET || $whence == SEEK_END))) {
                    throw new DupLiteSnapLib_32BitSizeLimitException("Trying to seek on a file beyond the capability of 32 bit PHP. offset=$offset filesize=$filesize");
                } else {
                    throw new Exception("Error seeking to file offset $offset. Retval = $ret_val");
                }
            }
        }

        public static function filemtime($filename)
        {
            $mtime = filemtime($filename);

            if ($mtime === E_WARNING) {
                throw new Exception("Cannot retrieve last modified time of $filename");
            }

            return $mtime;
        }

        /**
         * exetute a file put contents after some checks. throw exception if fail.
         *
         * @param string $filename
         * @param mixed $data
         * @return boolean
         * @throws Exception if putcontents fails
         */
        public static function filePutContents($filename, $data)
        {
            if (($dirFile = realpath(dirname($filename))) === false) {
                throw new Exception('FILE ERROR: put_content for file '.$filename.' failed [realpath fail]');
            }
            if (!is_dir($dirFile)) {
                throw new Exception('FILE ERROR: put_content for file '.$filename.' failed [dir '.$dirFile.' don\'t exists]');
            }
            if (!is_writable($dirFile)) {
                throw new Exception('FILE ERROR: put_content for file '.$filename.' failed [dir '.$dirFile.' exists but isn\'t writable]');
            }
            $realFileName = $dirFile.basename($filename);
            if (file_exists($realFileName) && !is_writable($realFileName)) {
                throw new Exception('FILE ERROR: put_content for file '.$filename.' failed [file exist '.$realFileName.' but isn\'t writable');
            }
            if (file_put_contents($filename, $data) === false) {
                throw new Exception('FILE ERROR: put_content for file '.$filename.' failed [Couldn\'t write data to '.$realFileName.']');
            }
            return true;
        }

        public static function getFileName($file_path)
        {
            $info = new SplFileInfo($file_path);
            return $info->getFilename();
        }

        public static function getPath($file_path)
        {
            $info = new SplFileInfo($file_path);
            return $info->getPath();
        }

        /**
         * this function make a chmod only if the are different from perms input and if chmod function is enabled
         *
         * this function handles the variable MODE in a way similar to the chmod of lunux
         * So the MODE variable can be
         * 1) an octal number (0755)
         * 2) a string that defines an octal number ("644")
         * 3) a string with the following format [ugoa]*([-+=]([rwx]*)+
         *
         * examples
         * u+rw         add read and write at the user
         * u+rw,uo-wx   add read and write ad the user and remove wx at groupd and other
         * a=rw         is equal at 666
         * u=rwx,go-rwx is equal at 700
         *
         * @param string $file
         * @param int|string $mode
         * @return boolean
         */
        public static function chmod($file, $mode)
        {
            if (!file_exists($file)) {
                return false;
            }

            $octalMode = 0;

            if (is_int($mode)) {
                $octalMode = $mode;
            } else if (is_string($mode)) {
                $mode = trim($mode);
                if (preg_match('/([0-7]{1,3})/', $mode)) {
                    $octalMode = intval(('0'.$mode), 8);
                } else if (preg_match_all('/(a|[ugo]{1,3})([-=+])([rwx]{1,3})/', $mode, $gMatch, PREG_SET_ORDER)) {
                    if (!function_exists('fileperms')) {
                        return false;
                    }

                    // start by file permission
                    $octalMode = (fileperms($file) & 0777);

                    foreach ($gMatch as $matches) {
                        // [ugo] or a = ugo
                        $group = $matches[1];
                        if ($group === 'a') {
                            $group = 'ugo';
                        }
                        // can be + - =
                        $action = $matches[2];
                        // [rwx]
                        $gPerms = $matches[3];

                        // reset octal group perms
                        $octalGroupMode = 0;

                        // Init sub perms
                        $subPerm = 0;
                        $subPerm += strpos($gPerms, 'x') !== false ? 1 : 0; // mask 001
                        $subPerm += strpos($gPerms, 'w') !== false ? 2 : 0; // mask 010
                        $subPerm += strpos($gPerms, 'r') !== false ? 4 : 0; // mask 100

                        $ugoLen = strlen($group);

                        if ($action === '=') {
                            // generate octal group permsissions and ugo mask invert
                            $ugoMaskInvert = 0777;
                            for ($i = 0; $i < $ugoLen; $i++) {
                                switch ($group[$i]) {
                                    case 'u':
                                        $octalGroupMode = $octalGroupMode | $subPerm << 6; // mask xxx000000
                                        $ugoMaskInvert  = $ugoMaskInvert & 077;
                                        break;
                                    case 'g':
                                        $octalGroupMode = $octalGroupMode | $subPerm << 3; // mask 000xxx000
                                        $ugoMaskInvert  = $ugoMaskInvert & 0707;
                                        break;
                                    case 'o':
                                        $octalGroupMode = $octalGroupMode | $subPerm; // mask 000000xxx
                                        $ugoMaskInvert  = $ugoMaskInvert & 0770;
                                        break;
                                }
                            }
                            // apply = action
                            $octalMode = $octalMode & ($ugoMaskInvert | $octalGroupMode);
                        } else {
                            // generate octal group permsissions
                            for ($i = 0; $i < $ugoLen; $i++) {
                                switch ($group[$i]) {
                                    case 'u':
                                        $octalGroupMode = $octalGroupMode | $subPerm << 6; // mask xxx000000
                                        break;
                                    case 'g':
                                        $octalGroupMode = $octalGroupMode | $subPerm << 3; // mask 000xxx000
                                        break;
                                    case 'o':
                                        $octalGroupMode = $octalGroupMode | $subPerm; // mask 000000xxx
                                        break;
                                }
                            }
                            // apply + or - action
                            switch ($action) {
                                case '+':
                                    $octalMode = $octalMode | $octalGroupMode;
                                    break;
                                case '-':
                                    $octalMode = $octalMode & ~$octalGroupMode;
                                    break;
                            }
                        }
                    }
                }
            }

            // if input permissions are equal at file permissions return true without performing chmod
            if (function_exists('fileperms') && $octalMode === (fileperms($file) & 0777)) {
                return true;
            }

            if (!function_exists('chmod')) {
                return false;
            }

            return @chmod($file, $octalMode);
        }

        /**
         * this function creates a folder if it does not exist and performs a chmod.
         * it is different from the normal mkdir function to which an umask is applied to the input permissions.
         * 
         * this function handles the variable MODE in a way similar to the chmod of lunux
         * So the MODE variable can be
         * 1) an octal number (0755)
         * 2) a string that defines an octal number ("644")
         * 3) a string with the following format [ugoa]*([-+=]([rwx]*)+
         *
         * @param string $path
         * @param int|string $mode
         * @param bool $recursive
         * @param resource $context // not used fo windows bug
         * @return boolean bool TRUE on success or FALSE on failure.
         *
         * @todo check recursive true and multiple chmod
         */
        public static function mkdir($path, $mode = 0777, $recursive = false, $context = null)
        {
            if (strlen($path) > DupLiteSnapLibOSU::maxPathLen()) {
                throw new Exception('Skipping a file that exceeds allowed max path length ['.DupLiteSnapLibOSU::maxPathLen().']. File: '.$filepath);
            }

            if (!file_exists($path)) {
                if (!function_exists('mkdir')) {
                    return false;
                }
                if (!@mkdir($path, 0777, $recursive)) {
                    return false;
                }
            }

            return self::chmod($path, $mode);
        }

        /**
         * this function call snap mkdir if te folder don't exists od don't have write or exec permissions
         *
         * this function handles the variable MODE in a way similar to the chmod of lunux
         * The mode variable can be set to have more flexibility but not giving the user write and read and exec permissions doesn't make much sense
         *
         * @param string $path
         * @param int|string $mode
         * @param bool $recursive
         * @param resource $context
         * @return boolean
         */
        public static function dirWriteCheckOrMkdir($path, $mode = 'u+rwx', $recursive = false, $context = null)
        {
            if (!is_writable($path) || !is_executable($path)) {
                return self::mkdir($path, $mode, $recursive, $context);
            } else {
                return true;
            }
        }
    }
}