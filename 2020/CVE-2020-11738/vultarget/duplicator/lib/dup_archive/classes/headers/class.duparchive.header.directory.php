<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
require_once(dirname(__FILE__).'/../util/class.duparchive.util.php');
require_once(dirname(__FILE__).'/class.duparchive.header.u.php');

if(!class_exists('DupArchiveDirectoryHeader')) {
// Format
class DupArchiveDirectoryHeader// extends HeaderBase
{
    public $mtime;
    public $permissions;
    public $relativePathLength;
    public $relativePath;

    const MaxHeaderSize                = 8192;
    const MaxPathLength                = 4100;
    //const MaxStandardHeaderFieldLength = 128;

    public function __construct()
    {
        // Prevent direct instantiation
    }

//    static function createFromDirectory($directoryPath, $relativePath)
//    {
//        $instance = new DupArchiveDirectoryHeader();
//
//        $instance->permissions        = substr(sprintf('%o', fileperms($directoryPath)), -4);
//        $instance->mtime              = DupLiteSnapLibIOU::filemtime($directoryPath);
//        $instance->relativePath       = $relativePath;
//        $instance->relativePathLength = strlen($instance->relativePath);
//
//        return $instance;
//    }

    static function readFromArchive($archiveHandle, $skipStartElement = false)
    {
        $instance = new DupArchiveDirectoryHeader();

        if(!$skipStartElement)
        {
            // <A>
           $startElement = fread($archiveHandle, 3);

            if ($startElement === false) {
                if (feof($archiveHandle)) {
                    return false;
                } else {
                    throw new Exception('Error reading directory header');
                }
            }

            if ($startElement != '<D>') {
                throw new Exception("Invalid directory header marker found [{$startElement}] : location ".ftell($archiveHandle));
            }
        }

        $instance->mtime              = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'MT');
        $instance->permissions        = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'P');
        $instance->relativePathLength = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'RPL');

        // Skip the <RP>
        fread($archiveHandle, 4);

        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip the </RP>
//        fread($archiveHandle, 5);
//
//        // Skip the </D>
//        fread($archiveHandle, 4);

        // Skip the </RP> and the </D>
        fread($archiveHandle, 9);

        return $instance;
    }

    public function writeToArchive($archiveHandle)
    {
        if($this->relativePathLength == 0)
        {
            // Don't allow a base path to be written to the archive
            return;
        }

        $headerString = '<D><MT>'.$this->mtime.'</MT><P>'.$this->permissions.'</P><RPL>'.$this->relativePathLength.'</RPL><RP>'.$this->relativePath.'</RP></D>';

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