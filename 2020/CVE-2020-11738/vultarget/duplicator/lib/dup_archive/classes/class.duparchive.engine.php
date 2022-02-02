<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(__FILE__) . '/class.duparchive.constants.php');

require_once(DupArchiveConstants::$LibRoot . '/snaplib/class.snaplib.u.io.php');
require_once(DupArchiveConstants::$LibRoot . '/snaplib/class.snaplib.u.stream.php');

require_once(dirname(__FILE__) . '/headers/class.duparchive.header.php');
require_once(dirname(__FILE__) . '/states/class.duparchive.state.create.php');
require_once(dirname(__FILE__) . '/states/class.duparchive.state.simplecreate.php');
require_once(dirname(__FILE__) . '/states/class.duparchive.state.simpleexpand.php');
require_once(dirname(__FILE__) . '/states/class.duparchive.state.expand.php');
require_once(dirname(__FILE__) . '/processors/class.duparchive.processor.file.php');
require_once(dirname(__FILE__) . '/processors/class.duparchive.processor.directory.php');
require_once(dirname(__FILE__) . '/class.duparchive.processing.failure.php');
require_once(dirname(__FILE__) . '/util/class.duparchive.util.php');
require_once(dirname(__FILE__) . '/util/class.duparchive.util.scan.php');

if(!class_exists('DupArchiveInfo')) {
class DupArchiveInfo
{

    public $archiveHeader;
    public $fileHeaders;
    public $directoryHeaders;

    public function __construct()
    {
        $this->fileHeaders = array();
        $this->directoryHeaders = array();
    }
}
}

if(!class_exists('DupArchiveItemAlias')) {
class DupArchiveItemAlias
{

    public $oldName;
    public $newName;

}
}

if(!class_exists('DupArchiveItemHeaderType')) {
class DupArchiveItemHeaderType
{

    const None = 0;
    const File = 1;
    const Directory = 2;
    const Glob = 3;

}
}

if(!class_exists('DupArchiveEngine')) {
class DupArchiveEngine
{

    public static $archive;
    public static function init($logger, $profilingFunction = null, $archive = null)
    {
        DupArchiveUtil::$logger = $logger;
        DupArchiveUtil::$profilingFunction = $profilingFunction;
        self::$archive = $archive;
    }

    public static function getNextHeaderType($archiveHandle)
    {
        $retVal = DupArchiveItemHeaderType::None;
        $marker = fgets($archiveHandle, 4);

        if (feof($archiveHandle) === false) {
            switch ($marker) {
                case '<D>':
                    $retVal = DupArchiveItemHeaderType::Directory;
                    break;

                case '<F>':
                    $retVal = DupArchiveItemHeaderType::File;
                    break;

                case '<G>':
                    $retVal = DupArchiveItemHeaderType::Glob;
                    break;

                default:
                    throw new Exception("Invalid header marker {$marker}. Location:" . ftell($archiveHandle));
            }
        }

        return $retVal;
    }

    public static function getArchiveInfo($filepath)
    {
        $archiveInfo = new DupArchiveInfo();

        DupArchiveUtil::log("archive size=" . filesize($filepath));
        $archiveHandle = DupLiteSnapLibIOU::fopen($filepath, 'rb');
        $moreFiles = true;

        $archiveInfo->archiveHeader = DupArchiveHeader::readFromArchive($archiveHandle);

        $moreToRead = true;

        while ($moreToRead) {

            $headerType = self::getNextHeaderType($archiveHandle);

            // DupArchiveUtil::log("next header type=$headerType: " . ftell($archiveHandle));

            switch ($headerType) {
                case DupArchiveItemHeaderType::File:

                    $fileHeader = DupArchiveFileHeader::readFromArchive($archiveHandle, true, true);
                    $archiveInfo->fileHeaders[] = $fileHeader;
                    DupArchiveUtil::log("file" . $fileHeader->relativePath);
                    break;

                case DupArchiveItemHeaderType::Directory:
                    $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle, true);

                    $archiveInfo->directoryHeaders[] = $directoryHeader;
                    break;

                case DupArchiveItemHeaderType::None:
                    $moreToRead = false;
            }
        }

        return $archiveInfo;
    }

    // can't span requests since create state can't store list of files
    public static function addDirectoryToArchiveST($archiveFilepath, $directory, $basepath, $includeFiles = false, $newBasepath = null, $globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE)
    {
        if ($includeFiles) {
            $scan = DupArchiveScanUtil::createScanObject($directory);
        } else {
            $scan->Files = array();
            $scan->Dirs = array();
        }

        $createState = new DupArchiveSimpleCreateState();

        $createState->archiveOffset = filesize($archiveFilepath);
        $createState->archivePath = $archiveFilepath;
        $createState->basePath = $basepath;
        $createState->timerEnabled = false;
        $createState->globSize = $globSize;
        $createState->newBasePath = $newBasepath;

        self::addItemsToArchive($createState, $scan);

        $retVal = new stdClass();
        $retVal->numDirsAdded = $createState->currentDirectoryIndex;
        $retVal->numFilesAdded = $createState->currentFileIndex;

        if($createState->skippedFileCount > 0) {

            throw new Exception("One or more files were were not able to be added when adding {$directory} to {$archiveFilepath}");
        }
        else if($createState->skippedDirectoryCount > 0) {
            
            throw new Exception("One or more directories were not able to be added when adding {$directory} to {$archiveFilepath}");
        }

        return $retVal;
    }

    public static function addRelativeFileToArchiveST($archiveFilepath, $filepath, $relativePath, $globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE)
    {
        $createState = new DupArchiveSimpleCreateState();

        $createState->archiveOffset = filesize($archiveFilepath);
        $createState->archivePath = $archiveFilepath;
        $createState->basePath = null;
        $createState->timerEnabled = false;
        $createState->globSize = $globSize;

        $scan = new stdClass();

        $scan->Files = array();
        $scan->Dirs = array();

        $scan->Files[] = $filepath;

        if ($relativePath != null) {

            $scan->FileAliases = array();
            $scan->FileAliases[$filepath] = $relativePath;
        }

        self::addItemsToArchive($createState, $scan);
    }

    public static function addFileToArchiveUsingBaseDirST($archiveFilepath, $basePath, $filepath, $globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE)
    {
        $createState = new DupArchiveSimpleCreateState();

        $createState->archiveOffset = filesize($archiveFilepath);
        $createState->archivePath = $archiveFilepath;
        $createState->basePath = $basePath;
        $createState->timerEnabled = false;
        $createState->globSize = $globSize;

        $scan = new stdClass();

        $scan->Files = array();
        $scan->Dirs = array();

        $scan->Files[] = $filepath;

        self::addItemsToArchive($createState, $scan);
    }

    public static function createArchive($archivePath, $isCompressed)
    {
        $archiveHandle = DupLiteSnapLibIOU::fopen($archivePath, 'w+b');

        /* @var $archiveHeader DupArchiveHeader */
        $archiveHeader = DupArchiveHeader::create($isCompressed);

        $archiveHeader->writeToArchive($archiveHandle);

        // Intentionally do not write build state since if something goes wrong we went it to start over on the archive

        DupLiteSnapLibIOU::fclose($archiveHandle);
    }

    public static function addItemsToArchive($createState, $scanFSInfo)
    {
        if ($createState->globSize == -1) {

            $createState->globSize = DupArchiveCreateState::DEFAULT_GLOB_SIZE;
        }
        /* @var $createState DupArchiveCreateState */
        DupArchiveUtil::tlogObject("addItemsToArchive start", $createState);

        $directoryCount = count($scanFSInfo->Dirs);
        $fileCount = count($scanFSInfo->Files);

        $createState->startTimer();

        /* @var $createState DupArchiveCreateState */
        $basepathLength = strlen($createState->basePath);

        $archiveHandle = DupLiteSnapLibIOU::fopen($createState->archivePath, 'r+b');

        DupArchiveUtil::tlog("Archive size=", filesize($createState->archivePath));
        DupArchiveUtil::tlog("Archive location is now " . DupLiteSnapLibIOU::ftell($archiveHandle));

        $archiveHeader = DupArchiveHeader::readFromArchive($archiveHandle);

        $createState->isCompressed = $archiveHeader->isCompressed;

        if ($createState->archiveOffset == filesize($createState->archivePath)) {
            DupArchiveUtil::tlog("Seeking to end of archive location because of offset {$createState->archiveOffset} for file size " . filesize($createState->archivePath));
            DupLiteSnapLibIOU::fseek($archiveHandle, 0, SEEK_END);
        } else {
            DupArchiveUtil::tlog("Seeking archive offset {$createState->archiveOffset} for file size " . filesize($createState->archivePath));
            DupLiteSnapLibIOU::fseek($archiveHandle, $createState->archiveOffset);
        }

        while (($createState->currentDirectoryIndex < $directoryCount) && (!$createState->timedOut())) {

            if ($createState->throttleDelayInUs !== 0) {
                usleep($createState->throttleDelayInUs);
            }

            $directory = $scanFSInfo->Dirs[$createState->currentDirectoryIndex];

            try {
                $relativeDirectoryPath = null;

                if (isset($scanFSInfo->DirectoryAliases) && array_key_exists($directory, $scanFSInfo->DirectoryAliases)) {
                    $relativeDirectoryPath = $scanFSInfo->DirectoryAliases[$directory];
                } else {
                    if (null === self::$archive) {
                        $relativeDirectoryPath = substr($directory, $basepathLength);
                        $relativeDirectoryPath = ltrim($relativeDirectoryPath, '/');
                        if ($createState->newBasePath !== null) {
                            $relativeDirectoryPath = $createState->newBasePath . $relativeDirectoryPath;
                        }
                    } else {
                        $relativeDirectoryPath = self::$archive->getLocalDirPath($directory, $createState->newBasePath);
                    }
                }

                if($relativeDirectoryPath !== '') {
                    DupArchiveDirectoryProcessor::writeDirectoryToArchive($createState, $archiveHandle, $directory, $relativeDirectoryPath);
                } else {
                    $createState->skippedDirectoryCount++;
                    $createState->currentDirectoryIndex++;
                }
            } catch (Exception $ex) {
                DupArchiveUtil::log("Failed to add {$directory} to archive. Error: " . $ex->getMessage(), true);

                $createState->addFailure(DupArchiveFailureTypes::Directory, $directory, $ex->getMessage(), false);
                $createState->currentDirectoryIndex++;
                $createState->skippedDirectoryCount++;
                $createState->save();
            }
        }

        $createState->archiveOffset = DupLiteSnapLibIOU::ftell($archiveHandle);

        $workTimestamp = time();
        while (($createState->currentFileIndex < $fileCount) && (!$createState->timedOut())) {

            $filepath = $scanFSInfo->Files[$createState->currentFileIndex];

            try {

                $relativeFilePath = null;

                if (isset($scanFSInfo->FileAliases) && array_key_exists($filepath, $scanFSInfo->FileAliases)) {
                    $relativeFilePath = $scanFSInfo->FileAliases[$filepath];
                } else {
                    if (null === self::$archive) {
                        $relativeFilePath = substr($filepath, $basepathLength);
                        $relativeFilePath = ltrim($relativeFilePath, '/');
                        if ($createState->newBasePath !== null) {
                            $relativeFilePath = $createState->newBasePath . $relativeFilePath;
                        }
                    } else {
                        $relativeFilePath = self::$archive->getLocalFilePath($filepath, $createState->newBasePath);
                    }
                }

                // Uncomment when testing error handling
//                   if((strpos($relativeFilePath, 'dup-installer') !== false) || (strpos($relativeFilePath, 'lib') !== false)) {
//                       Dup_Log::Trace("Was going to do intentional error to {$relativeFilePath} but skipping");
//                   } else {
//                        throw new Exception("#### intentional file error when writing " . $relativeFilePath);
//                   }
//                }

                DupArchiveFileProcessor::writeFilePortionToArchive($createState, $archiveHandle, $filepath, $relativeFilePath);

                if(($createState->isRobust) && (time() - $workTimestamp >= 1)){
                    DupArchiveUtil::log("Robust mode create state save");

                    // When in robustness mode save the state every second
                    $workTimestamp = time();
                    $createState->working = ($createState->currentDirectoryIndex < $directoryCount) || ($createState->currentFileIndex < $fileCount);
                    $createState->save();
                }
            } catch (Exception $ex) {
                DupArchiveUtil::log("Failed to add {$filepath} to archive. Error: " . $ex->getMessage() . $ex->getTraceAsString(), true);
                $createState->currentFileIndex++;
                $createState->skippedFileCount++;
                $createState->addFailure(DupArchiveFailureTypes::File, $filepath, $ex->getMessage(), ($ex->getCode() === DupArchiveExceptionCodes::Fatal));
                $createState->save();
            }
        }

        $createState->working = ($createState->currentDirectoryIndex < $directoryCount) || ($createState->currentFileIndex < $fileCount);
        $createState->save();

        DupLiteSnapLibIOU::fclose($archiveHandle);

        if (!$createState->working) {
            DupArchiveUtil::log("compress done");
        } else {
            DupArchiveUtil::tlog("compress not done so continuing later");
        }
    }
    public static function expandDirectory($archivePath, $relativePath, $destPath)
    {
        self::expandItems($archivePath, $relativePath, $destPath);
    }

    public static function expandArchive($expandState)
    {
        /* @var $expandState DupArchiveExpandState */
        $expandState->startTimer();

        $archiveHandle = DupLiteSnapLibIOU::fopen($expandState->archivePath, 'rb');

        DupLiteSnapLibIOU::fseek($archiveHandle, $expandState->archiveOffset);

        if ($expandState->archiveOffset == 0) {

            DupArchiveUtil::log("#### seeking to start of archive");

            $expandState->archiveHeader = DupArchiveHeader::readFromArchive($archiveHandle);
            $expandState->isCompressed = $expandState->archiveHeader->isCompressed;
            $expandState->archiveOffset = DupLiteSnapLibIOU::ftell($archiveHandle);

            $expandState->save();
        } else {

            DupArchiveUtil::log("#### seeking archive offset {$expandState->archiveOffset}");
        }

        if ((!$expandState->validateOnly) || ($expandState->validationType == DupArchiveValidationTypes::Full)) {
            $moreItems = self::expandItems($expandState, $archiveHandle);
        } else {
            // profile ok
            $moreItems = self::standardValidateItems($expandState, $archiveHandle);
            // end profile ok
        }

        $expandState->working = $moreItems;
        $expandState->save();

        DupLiteSnapLibIOU::fclose($archiveHandle, false);

        if (!$expandState->working) {

            DupArchiveUtil::log("expand done");
            DupArchiveUtil::logObject('expandstate', $expandState);

            if (($expandState->expectedFileCount != -1) && ($expandState->expectedFileCount != $expandState->fileWriteCount)) {

                $expandState->addFailure(DupArchiveFailureTypes::File, 'Archive', "Number of files expected ({$expandState->expectedFileCount}) doesn't equal number written ({$expandState->fileWriteCount}).");
            }

            if (($expandState->expectedDirectoryCount != -1) && ($expandState->expectedDirectoryCount != $expandState->directoryWriteCount)) {
                $expandState->addFailure(DupArchiveFailureTypes::Directory, 'Archive', "Number of directories expected ({$expandState->expectedDirectoryCount}) doesn't equal number written ({$expandState->directoryWriteCount}).");
            }
        } else {
            DupArchiveUtil::tlogObject("expand not done so continuing later", $expandState);
        }
    }

    private static function skipFileInArchive($archiveHandle, $fileHeader)
    {
        if ($fileHeader->fileSize > 0) {

            $dataSize = 0;

            do {
                $globHeader = DupArchiveGlobHeader::readFromArchive($archiveHandle, true);

                $dataSize += $globHeader->originalSize;

                $moreGlobs = ($dataSize < $fileHeader->fileSize);
            } while ($moreGlobs);
        }
    }

    // Assumes we are on one header and just need to get to the next
    private static function skipToNextHeader($archiveHandle)
    {
        $headerType = self::getNextHeaderType($archiveHandle);

        switch ($headerType) {
            case DupArchiveItemHeaderType::File:
                $fileHeader = DupArchiveFileHeader::readFromArchive($archiveHandle, false, true);

                self::skipFileInArchive($archiveHandle, $fileHeader);

                break;

            case DupArchiveItemHeaderType::Directory:

                $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle, true);

                break;

            case DupArchiveItemHeaderType::None:
                $moreToRead = false;
        }
    }

    // Single-threaded file expansion
    public static function expandFiles($archiveFilePath, $relativeFilePaths, $destPath)
    {
        // Not setting timeout timestamp so it will never timeout
        DupArchiveUtil::tlog("opening archive {$archiveFilePath}");

        $archiveHandle = DupLiteSnapLibIOU::fopen($archiveFilePath, 'r');

        /* @var $expandState DupArchiveSimpleExpandState */
        $expandState = new DupArchiveSimpleExpandState();

        $expandState->archiveHeader = DupArchiveHeader::readFromArchive($archiveHandle);
        $expandState->isCompressed  = $expandState->archiveHeader->isCompressed;
        $expandState->archiveOffset = DupLiteSnapLibIOU::ftell($archiveHandle);
        $expandState->includedFiles = $relativeFilePaths;
        $expandState->filteredDirectories = array('*');
        $expandState->filteredFiles = array('*');
//        $expandState->basePath    = $destPath . '/tempExtract';   // RSR remove once extract works
        $expandState->basePath      = $destPath;   // RSR remove once extract works
        
        // TODO: Filter out all directories/files except those in the list
        self::expandItems($expandState, $archiveHandle);

    }

    private static function expandItems(&$expandState, $archiveHandle)
    {
        /* @var $expandState DupArchiveExpandState */

        $moreToRead = true;

        $workTimestamp = time();

        while ($moreToRead && (!$expandState->timedOut())) {

            if ($expandState->throttleDelayInUs !== 0) {
                usleep($expandState->throttleDelayInUs);
            }

            if ($expandState->currentFileHeader != null) {

                DupArchiveUtil::tlog("Writing file {$expandState->currentFileHeader->relativePath}");

                if (self::filePassesFilters($expandState->filteredDirectories, $expandState->filteredFiles, $expandState->includedFiles, $expandState->currentFileHeader->relativePath)) {
                    try {
                        $fileCompleted = DupArchiveFileProcessor::writeToFile($expandState, $archiveHandle);
                    } catch (Exception $ex) {
                        DupArchiveUtil::log("Failed to write to {$expandState->currentFileHeader->relativePath}. Error: " . $ex->getMessage(), true);

                        // Reset things - skip over this file within the archive.

                        DupLiteSnapLibIOU::fseek($archiveHandle, $expandState->lastHeaderOffset);

                        self::skipToNextHeader($archiveHandle, $expandState->currentFileHeader);

                        $expandState->archiveOffset = ftell($archiveHandle);
                        
                        $expandState->addFailure(DupArchiveFailureTypes::File, $expandState->currentFileHeader->relativePath, $ex->getMessage(), false);

                        $expandState->resetForFile();

                        $expandState->lastHeaderOffset = -1;

                        $expandState->save();
                    }
                } else {
                    DupArchiveUtil::log("skipping {$expandState->currentFileHeader->relativePath} because its part of the exclusion filter");
                    self::skipFileInArchive($archiveHandle, $expandState->currentFileHeader);

                    $expandState->resetForFile();
                }
            } else {
                // Header is null so read in the next one

                $expandState->lastHeaderOffset = @ftell($archiveHandle);

                // profile ok
                $headerType = self::getNextHeaderType($archiveHandle);
                // end profile ok

                DupArchiveUtil::tlog('header type ' . $headerType);
                switch ($headerType) {
                    case DupArchiveItemHeaderType::File:
                        DupArchiveUtil::tlog('File header');
                        $expandState->currentFileHeader = DupArchiveFileHeader::readFromArchive($archiveHandle, false, true);

                        $expandState->archiveOffset = @ftell($archiveHandle);

                        DupArchiveUtil::tlog('Just read file header from archive');

                        break;

                    case DupArchiveItemHeaderType::Directory:
                        DupArchiveUtil::tlog('Directory Header');

                        $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle, true);

                        if (self::passesDirectoryExclusion($expandState->filteredDirectories, $directoryHeader->relativePath)) {

                            $createdDirectory = true;

                            if (!$expandState->validateOnly) {
                                $directory = $expandState->basePath . '/' . $directoryHeader->relativePath;
                                $mode = 'u+rwx';
                                if ($expandState->directoryModeOverride != -1) {
                                    $mode = $expandState->directoryModeOverride;
                                }
                                $createdDirectory = DupLiteSnapLibIOU::dirWriteCheckOrMkdir($directory, $mode, true);
                            }

                            if ($createdDirectory) {
                                $expandState->directoryWriteCount++;
                            } else {
                                $expandState->addFailure(DupArchiveFailureTypes::Directory, $directory, "Unable to create directory $directory", false);
                            }
                        }
                        $expandState->archiveOffset = ftell($archiveHandle);

                        DupArchiveUtil::tlog('Just read directory header ' . $directoryHeader->relativePath . ' from archive');
                        break;

                    case DupArchiveItemHeaderType::None:
                        $moreToRead = false;
                }
            }

            if(($expandState->isRobust) && (time() - $workTimestamp >= 1)){

                DupArchiveUtil::log("Robust mode extract state save for standard validate");

                // When in robustness mode save the state every second
                $workTimestamp = time();
                $expandState->save();
            }
        }

        $expandState->save();

        return $moreToRead;
    }

    private static function passesDirectoryExclusion($directoryFilters, $candidate)
    {
        foreach ($directoryFilters as $directoryFilter) {

            if($directoryFilter === '*') {
                return false;
            }

            if (substr($candidate, 0, strlen($directoryFilter)) == $directoryFilter) {

                return false;
            }
        }

        return true;
    }

    private static function filePassesFilters($excludedDirectories, $excludedFiles, $includedFiles, $candidate)
    {
        $retVal = true;
        
        // Included files trumps all exclusion filters
        foreach($includedFiles as $includedFile) {
            if($includedFile === $candidate) {
                return true;
            }
        }

        if (self::passesDirectoryExclusion($excludedDirectories, $candidate)) {

            foreach ($excludedFiles as $fileFilter) {

                if($fileFilter === '*') {
                    return false;
                }

                if ($fileFilter === $candidate) {

                    $retVal = false;
                    break;
                }
            }
        } else {
            
            $retVal = false;;
        }

        return $retVal;
    }

    private static function standardValidateItems(&$expandState, $archiveHandle)
    {
        $moreToRead = true;

        // profile ok
        $to = $expandState->timedOut();
        // end profile ok

        $workTimestamp = time();
        
        while ($moreToRead && (!$to)) {

            if ($expandState->throttleDelayInUs !== 0) {
                usleep($expandState->throttleDelayInUs);
            }

            if ($expandState->currentFileHeader != null) {

                try {

                    $fileCompleted = DupArchiveFileProcessor::standardValidateFileEntry($expandState, $archiveHandle);

                    if ($fileCompleted) {
                        $expandState->resetForFile();
                    }

                    // Expand state taken care of within the write to file to ensure consistency
                } catch (Exception $ex) {

                    DupArchiveUtil::log("Failed validate file in archive. Error: " . $ex->getMessage(), true);
                    DupArchiveUtil::logObject("expand state", $expandState, true);
                    //   $expandState->currentFileIndex++;
                    // RSR TODO: Need way to skip past that file

                    $expandState->addFailure(DupArchiveFailureTypes::File, $expandState->currentFileHeader->relativePath, $ex->getMessage());
                    $expandState->save();

                    $moreToRead = false;
                }
            } else {

                // profile ok
                $headerType = self::getNextHeaderType($archiveHandle);

                switch ($headerType) {
                    case DupArchiveItemHeaderType::File:

                        // profile ok
                        $expandState->currentFileHeader = DupArchiveFileHeader::readFromArchive($archiveHandle, false, true);

                        $expandState->archiveOffset = ftell($archiveHandle);

                        // end profile ok

                        break;

                    case DupArchiveItemHeaderType::Directory:

                        // profile ok
                        $directoryHeader = DupArchiveDirectoryHeader::readFromArchive($archiveHandle, true);

                        $expandState->directoryWriteCount++;
                        $expandState->archiveOffset = ftell($archiveHandle);

                        break;

                    case DupArchiveItemHeaderType::None:
                        $moreToRead = false;
                }
            }

            if(($expandState->isRobust) && (time() - $workTimestamp >= 1)){

                DupArchiveUtil::log("Robust mdoe extract state save for standard validate");

                // When in robustness mode save the state every second
                $workTimestamp = time();
                $expandState->save();
            }

            // profile ok
            $to = $expandState->timedOut();
        }

        // profile ok
        $expandState->save();

        return $moreToRead;
    }
}
}
