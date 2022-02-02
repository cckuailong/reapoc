<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
//---------- DUPARCHIVE MINI EXPANDER: The contents of this file will be injected into the installer bootlog at build time ------------------------

class DupArchiveHeaderMiniU
{
    const MaxStandardHeaderFieldLength = 128;

    public static function readStandardHeaderField($archiveHandle, $ename)
    {
        $expectedStart = "<{$ename}>";
        $expectedEnd = "</{$ename}>";

        $startingElement = fread($archiveHandle, strlen($expectedStart));

        if($startingElement !== $expectedStart) {
            throw new Exception("Invalid starting element. Was expecting {$expectedStart} but got {$startingElement}");
        }

        return stream_get_line($archiveHandle, self::MaxStandardHeaderFieldLength, $expectedEnd);
    }
}

class DupArchiveMiniItemHeaderType
{
    const None      = 0;
    const File      = 1;
    const Directory = 2;
    const Glob      = 3;
}

class DupArchiveMiniFileHeader
{
    public $fileSize;
    public $mtime;
    public $permissions;
    public $hash;
    public $relativePathLength;
    public $relativePath;

    static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveMiniFileHeader();

        $instance->fileSize           = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'FS');
        $instance->mtime              = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'MT');
        $instance->permissions        = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'P');
        $instance->hash                = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'HA');
        $instance->relativePathLength = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'RPL');

        // Skip <RP>
        fread($archiveHandle, 4);

        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip </RP>
        fread($archiveHandle, 5);

        // Skip the #F!
        //fread($archiveHandle, 3);
        // Skip the </F>
        fread($archiveHandle, 4);

        return $instance;
    }
}

class DupArchiveMiniDirectoryHeader
{
    public $mtime;
    public $permissions;
    public $relativePathLength;
    public $relativePath;

   // const MaxHeaderSize                = 8192;
   // const MaxStandardHeaderFieldLength = 128;

    static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveMiniDirectoryHeader();

        $instance->mtime              = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'MT');
        $instance->permissions        = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'P');
        $instance->relativePathLength = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'RPL');

        // Skip the <RP>
        fread($archiveHandle, 4);

        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip the </RP>
        fread($archiveHandle, 5);

        // Skip the </D>
        fread($archiveHandle, 4);

        return $instance;
    }
}

class DupArchiveMiniGlobHeader //extends HeaderBase
{
    public $originalSize;
    public $storedSize;
    public $hash;

 //   const MaxHeaderSize = 255;

   public static function readFromArchive($archiveHandle, $skipGlob)
    {
        $instance = new DupArchiveMiniGlobHeader();

      //  DupArchiveUtil::log('Reading glob starting at ' . ftell($archiveHandle));

        $startElement = fread($archiveHandle, 3);

        //if ($marker != '?G#') {
        if ($startElement != '<G>') {
            throw new Exception("Invalid glob header marker found {$startElement}. location:" . ftell($archiveHandle));
        }

        $instance->originalSize           = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'OS');
        $instance->storedSize             = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'SS');
        $instance->hash                    = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'HA');

        // Skip the </G>
        fread($archiveHandle, 4);

        if ($skipGlob) {
          //  DupLiteSnapLibIOU::fseek($archiveHandle, $instance->storedSize, SEEK_CUR);
		    if(fseek($archiveHandle, $instance->storedSize, SEEK_CUR) === -1)
			{
                throw new Exception("Can't fseek when skipping glob at location:".ftell($archiveHandle));
            }
        }

        return $instance;
    }
}

class DupArchiveMiniHeader
{
    public $version;
    public $isCompressed;

//    const MaxHeaderSize = 50;

    private function __construct()
    {
        // Prevent instantiation
        if (!class_exists('DUPX_Bootstrap')) {
            throw new Exception('Class DUPX_Bootstrap not found');
        }
    }

    public static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveMiniHeader();

        $startElement = fgets($archiveHandle, 4);

        if ($startElement != '<A>') {
            throw new Exception("Invalid archive header marker found {$startElement}");
        }

        $instance->version           = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'V');
        $instance->isCompressed      = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'C') == 'true' ? true : false;

        // Skip the </A>
        fgets($archiveHandle, 5);

        return $instance;
    }
}

class DupArchiveMiniWriteInfo
{
    public $archiveHandle       = null;
    public $currentFileHeader   = null;
    public $destDirectory       = null;
    public $directoryWriteCount = 0;
    public $fileWriteCount      = 0;
    public $isCompressed        = false;
    public $enableWrite         = false;

    public function getCurrentDestFilePath()
    {
        if($this->destDirectory != null)
        {
            return "{$this->destDirectory}/{$this->currentFileHeader->relativePath}";
        }
        else
        {
            return null;
        }
    }

}

class DupArchiveMiniExpander
{

    public static $loggingFunction     = null;

    public static function init($loggingFunction)
    {
        self::$loggingFunction = $loggingFunction;
    }

    public static function log($s, $flush=false)
    {
        if(self::$loggingFunction != null) {
            call_user_func(self::$loggingFunction, "MINI EXPAND:$s", $flush);
        }
    }

    public static function expandDirectory($archivePath, $relativePath, $destPath)
    {
        self::expandItems($archivePath, $relativePath, $destPath);
    }

    private static function expandItems($archivePath, $inclusionFilter, $destDirectory, $ignoreErrors = false)
    {
        $archiveHandle = fopen($archivePath, 'rb');

        if ($archiveHandle === false) {
            throw new Exception("Can’t open archive at $archivePath!");
        }

        $archiveHeader = DupArchiveMiniHeader::readFromArchive($archiveHandle);

        $writeInfo = new DupArchiveMiniWriteInfo();

        $writeInfo->destDirectory = $destDirectory;
        $writeInfo->isCompressed  = $archiveHeader->isCompressed;

        $moreToRead = true;

        while ($moreToRead) {

            if ($writeInfo->currentFileHeader != null) {

                try {
                    if (self::passesInclusionFilter($inclusionFilter, $writeInfo->currentFileHeader->relativePath)) {

                        self::writeToFile($archiveHandle, $writeInfo);

                        $writeInfo->fileWriteCount++;
                    }
                    else if($writeInfo->currentFileHeader->fileSize > 0) {
                      //  self::log("skipping {$writeInfo->currentFileHeader->relativePath} since it doesn’t match the filter");

                        // Skip the contents since the it isn't a match
                        $dataSize = 0;

                        do {
                            $globHeader = DupArchiveMiniGlobHeader::readFromArchive($archiveHandle, true);

                            $dataSize += $globHeader->originalSize;

                            $moreGlobs = ($dataSize < $writeInfo->currentFileHeader->fileSize);
                        } while ($moreGlobs);
                    }

                    $writeInfo->currentFileHeader = null;

                    // Expand state taken care of within the write to file to ensure consistency
                } catch (Exception $ex) {

                    if (!$ignoreErrors) {
                        throw $ex;
                    }
                }
            } else {

                $headerType = self::getNextHeaderType($archiveHandle);

                switch ($headerType) {
                    case DupArchiveMiniItemHeaderType::File:

                        //$writeInfo->currentFileHeader = DupArchiveMiniFileHeader::readFromArchive($archiveHandle, $inclusionFilter);
						$writeInfo->currentFileHeader = DupArchiveMiniFileHeader::readFromArchive($archiveHandle);

                        break;

                    case DupArchiveMiniItemHeaderType::Directory:

                        $directoryHeader = DupArchiveMiniDirectoryHeader::readFromArchive($archiveHandle);

                     //   self::log("considering $inclusionFilter and {$directoryHeader->relativePath}");
                        if (self::passesInclusionFilter($inclusionFilter, $directoryHeader->relativePath)) {

                        //    self::log("passed");
                            $directory = "{$writeInfo->destDirectory}/{$directoryHeader->relativePath}";

                          //  $mode = $directoryHeader->permissions;

                            // rodo handle this more elegantly @mkdir($directory, $directoryHeader->permissions, true);
                            DUPX_Bootstrap::mkdir($directory, 'u+rwx', true);


                            $writeInfo->directoryWriteCount++;
                        }
                        else {
                     //       self::log("didnt pass");
                        }


                        break;

                    case DupArchiveMiniItemHeaderType::None:
                        $moreToRead = false;
                }
            }
        }

        fclose($archiveHandle);
    }

    private static function getNextHeaderType($archiveHandle)
    {
        $retVal = DupArchiveMiniItemHeaderType::None;
        $marker = fgets($archiveHandle, 4);

        if (feof($archiveHandle) === false) {
            switch ($marker) {
                case '<D>':
                    $retVal = DupArchiveMiniItemHeaderType::Directory;
                    break;

                case '<F>':
                    $retVal = DupArchiveMiniItemHeaderType::File;
                    break;

                case '<G>':
                    $retVal = DupArchiveMiniItemHeaderType::Glob;
                    break;

                default:
                    throw new Exception("Invalid header marker {$marker}. Location:".ftell($archiveHandle));
            }
        }

        return $retVal;
    }

    private static function writeToFile($archiveHandle, $writeInfo)
    {
		$destFilePath = $writeInfo->getCurrentDestFilePath();

		if($writeInfo->currentFileHeader->fileSize > 0)
		{
			/* @var $writeInfo DupArchiveMiniWriteInfo */
			$parentDir = dirname($destFilePath);
			if (!file_exists($parentDir)) {
                if (!DUPX_Bootstrap::mkdir($parentDir, 'u+rwx', true)) {
                    throw new Exception("Couldn't create {$parentDir}");
                }
            }

            $destFileHandle = fopen($destFilePath, 'wb+');
			if ($destFileHandle === false) {
				throw new Exception("Couldn't open {$destFilePath} for writing.");
			}

			do {

				self::appendGlobToFile($archiveHandle, $destFileHandle, $writeInfo);

				$currentFileOffset = ftell($destFileHandle);

				$moreGlobstoProcess = $currentFileOffset < $writeInfo->currentFileHeader->fileSize;
			} while ($moreGlobstoProcess);

			fclose($destFileHandle);

            DUPX_Bootstrap::chmod($destFilePath, 'u+rw');

			self::validateExpandedFile($writeInfo);
		} else {
			if(touch($destFilePath) === false) {
				throw new Exception("Couldn't create $destFilePath");
			}
            DUPX_Bootstrap::chmod($destFilePath, 'u+rw');
		}
    }

    private static function validateExpandedFile($writeInfo)
    {
        /* @var $writeInfo DupArchiveMiniWriteInfo */

        if ($writeInfo->currentFileHeader->hash !== '00000000000000000000000000000000') {
            
            $hash = hash_file('crc32b', $writeInfo->getCurrentDestFilePath());

            if ($hash !== $writeInfo->currentFileHeader->hash) {

                throw new Exception("MD5 validation fails for {$writeInfo->getCurrentDestFilePath()}");
            }
        }
    }

    // Assumption is that archive handle points to a glob header on this call
    private static function appendGlobToFile($archiveHandle, $destFileHandle, $writeInfo)
    {
        /* @var $writeInfo DupArchiveMiniWriteInfo */
        $globHeader = DupArchiveMiniGlobHeader::readFromArchive($archiveHandle, false);

        $globContents = fread($archiveHandle, $globHeader->storedSize);

        if ($globContents === false) {

            throw new Exception("Error reading glob from {$writeInfo->getDestFilePath()}");
        }

        if ($writeInfo->isCompressed) {
            $globContents = gzinflate($globContents);
        }

        if (fwrite($destFileHandle, $globContents) === false) {
            throw new Exception("Error writing data glob to {$destFileHandle}");
        }
    }

    private static function passesInclusionFilter($filter, $candidate)
    {
        return (substr($candidate, 0, strlen($filter)) == $filter);
    }
}
?>