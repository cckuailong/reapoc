<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
require_once(dirname(__FILE__).'/../util/class.duparchive.util.php');
require_once(dirname(__FILE__).'/class.duparchive.header.u.php');

if(!class_exists('DupArchiveFileHeader')) {
// Format
class DupArchiveFileHeader// extends HeaderBase
{
    public $fileSize;
    public $mtime;
    public $permissions;
    public $hash;
    public $relativePathLength;
    public $relativePath;

    const MaxHeaderSize                = 8192;
    const MaxPathLength                = 4100;
//    const MaxStandardHeaderFieldLength = 128;

    private function __construct()
    {
        // Prevent direct instantiation
    }

    static function createFromFile($filepath, $relativeFilePath)
    {
        $instance = new DupArchiveFileHeader();

        // RSR TODO Populate fields based on file already on system
        // profile ok
        $instance->fileSize           = DupLiteSnapLibIOU::filesize($filepath);
        // end profile ok

        // profile ok
        $instance->permissions        = substr(sprintf('%o', fileperms($filepath)), -4);
        // end profile ok

        // profile ok
        $instance->mtime              = DupLiteSnapLibIOU::filemtime($filepath);
        // end profile ok

		if($instance->fileSize > DupArchiveConstants::$MaxFilesizeForHashing) {
			$instance->hash = false;
		}
		else {
			$instance->hash = hash_file('crc32b', $filepath);
		}
		
        $instance->relativePath       = $relativeFilePath;
        $instance->relativePathLength = strlen($instance->relativePath);

      //  DupArchiveUtil::tlog("paths=$filepath, {$instance->relativePath}");
        if ($instance->hash === false) {
            // RSR TODO: Best thing to do here?
            $instance->hash = "00000000000000000000000000000000";
        }
        
        return $instance;
        
    }

    /*
     * delta = 84-22 = 62 bytes per file -> 20000 files -> 1.2MB larger
     * <F><FS>x</FS><MT>x</<MT><FP>x</FP><HA>x</HA><RFPL>x</RFPL><RFP>x</RFP></F>
     # F#x#x#x#x#x#x!
     *
     */
    static function readFromArchive($archiveHandle, $skipContents, $skipMarker = false)
    {
        // RSR TODO Read header from archive handle and populate members
        // TODO: return null if end of archive or throw exception if can read something but its not a file header

        $instance = new DupArchiveFileHeader();

        if (!$skipMarker) {
            $marker = @fread($archiveHandle, 3);

            if ($marker === false) {
                if (feof($archiveHandle)) {
                    return false;
                } else {
                    throw new Exception('Error reading file header');
                }
            }

            if ($marker != '<F>') {
                throw new Exception("Invalid file header marker found [{$marker}] : location ".ftell($archiveHandle));
            }
        }

        $instance->fileSize           = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'FS');
        $instance->mtime              = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'MT');
        $instance->permissions        = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'P');
        $instance->hash                = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'HA');
        $instance->relativePathLength = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'RPL');

        // Skip <RP>
        fread($archiveHandle, 4);

        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip </RP>
      //  fread($archiveHandle, 5);
       
        // Skip the </F>
//        fread($archiveHandle, 4);

        // Skip the </RP> and the </F>
        fread($archiveHandle, 9);

        if ($skipContents && ($instance->fileSize > 0)) {

            $dataSize = 0;

            $moreGlobs = true;
            while ($moreGlobs) {
                //echo 'read glob<br/>';
                /* @var $globHeader DupArchiveGlobHeader */
                $globHeader = DupArchiveGlobHeader::readFromArchive($archiveHandle, true);

                $dataSize += $globHeader->originalSize;

                $moreGlobs = ($dataSize < $instance->fileSize);
            }
        }

        return $instance;
    }

    public function writeToArchive($archiveHandle)
    {
        $headerString = '<F><FS>'.$this->fileSize.'</FS><MT>'.$this->mtime.'</MT><P>'.$this->permissions.'</P><HA>'.$this->hash.'</HA><RPL>'.$this->relativePathLength.'</RPL><RP>'.$this->relativePath.'</RP></F>';
        
        //DupLiteSnapLibIOU::fwrite($archiveHandle, $headerString);
        $bytes_written = @fwrite($archiveHandle, $headerString);

        if ($bytes_written === false) {
            throw new Exception('Error writing to file.');
        } else {
            return $bytes_written;
        }
    }
}
}