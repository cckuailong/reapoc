<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

require_once (DUPLICATOR_PLUGIN_PATH.'classes/package/class.pack.archive.php');

/**
 *  Creates a zip file using the built in PHP ZipArchive class
 */
class DUP_Zip extends DUP_Archive
{
    //PRIVATE
    private static $compressDir;
    private static $countDirs  = 0;
    private static $countFiles = 0;
    private static $sqlPath;
    private static $zipPath;
    private static $zipFileSize;
    private static $zipArchive;
    private static $limitItems   = 0;
    private static $networkFlush = false;
    private static $scanReport;

    /**
     *  Creates the zip file and adds the SQL file to the archive
     */
    public static function create(DUP_Archive $archive, $buildProgress)
    {
        try {
            $timerAllStart     = DUP_Util::getMicrotime();
            $package_zip_flush = DUP_Settings::Get('package_zip_flush');

            self::$compressDir  = rtrim(wp_normalize_path(DUP_Util::safePath($archive->PackDir)), '/');
            self::$sqlPath      = DUP_Util::safePath("{$archive->Package->StorePath}/{$archive->Package->Database->File}");
            self::$zipPath      = DUP_Util::safePath("{$archive->Package->StorePath}/{$archive->File}");
            self::$zipArchive   = new ZipArchive();
            self::$networkFlush = empty($package_zip_flush) ? false : $package_zip_flush;

            $filterDirs       = empty($archive->FilterDirs)  ? 'not set' : $archive->FilterDirs;
            $filterExts       = empty($archive->FilterExts)  ? 'not set' : $archive->FilterExts;
			$filterFiles      = empty($archive->FilterFiles) ? 'not set' : $archive->FilterFiles;
            $filterOn         = ($archive->FilterOn) ? 'ON' : 'OFF';
            $filterDirsFormat  = rtrim(str_replace(';', "\n\t", $filterDirs));
			$filterFilesFormat = rtrim(str_replace(';', "\n\t", $filterFiles));
            $lastDirSuccess   = self::$compressDir;

            //LOAD SCAN REPORT
            $json             = file_get_contents(DUPLICATOR_SSDIR_PATH_TMP."/{$archive->Package->NameHash}_scan.json");
            self::$scanReport = json_decode($json);

            DUP_Log::Info("\n********************************************************************************");
            DUP_Log::Info("ARCHIVE (ZIP):");
            DUP_Log::Info("********************************************************************************");
            $isZipOpen = (self::$zipArchive->open(self::$zipPath, ZIPARCHIVE::CREATE) === TRUE);
            if (!$isZipOpen) {
                $error_message = "Cannot open zip file with PHP ZipArchive.";
                $buildProgress->set_failed($error_message);
                DUP_Log::Error($error_message, "Path location [".self::$zipPath."]", Dup_ErrorBehavior::LogOnly);
                $archive->Package->setStatus(DUP_PackageStatus::ERROR);
                return;
            }
            DUP_Log::Info("ARCHIVE DIR:  ".self::$compressDir);
            DUP_Log::Info("ARCHIVE FILE: ".basename(self::$zipPath));
            DUP_Log::Info("FILTERS: *{$filterOn}*");
            DUP_Log::Info("DIRS:\n\t{$filterDirsFormat}");
			DUP_Log::Info("FILES:\n\t{$filterFilesFormat}");
            DUP_Log::Info("EXTS:  {$filterExts}");

            DUP_Log::Info("----------------------------------------");
            DUP_Log::Info("COMPRESSING");
            DUP_Log::Info("SIZE:\t".self::$scanReport->ARC->Size);
            DUP_Log::Info("STATS:\tDirs ".self::$scanReport->ARC->DirCount." | Files ".self::$scanReport->ARC->FileCount);

            //ADD SQL
            $sql_ark_file_path = $archive->Package->getSqlArkFilePath();
            $isSQLInZip = self::$zipArchive->addFile(self::$sqlPath, $sql_ark_file_path);

            if ($isSQLInZip) {
                DUP_Log::Info("SQL ADDED: ".basename(self::$sqlPath));
            } else {
                $error_message = "Unable to add database.sql to archive.";
                DUP_Log::Error($error_message, "SQL File Path [".self::$sqlPath."]", Dup_ErrorBehavior::LogOnly);
                $buildProgress->set_failed($error_message);
                $archive->Package->setStatus(DUP_PackageStatus::ERROR);
                return;
            }
            self::$zipArchive->close();
            self::$zipArchive->open(self::$zipPath, ZipArchive::CREATE);

            //ZIP DIRECTORIES
            $info = '';
            foreach (self::$scanReport->ARC->Dirs as $dir) {
                $emptyDir = $archive->getLocalDirPath($dir);

                if (is_readable($dir) && self::$zipArchive->addEmptyDir($emptyDir)) {
                    self::$countDirs++;
                    $lastDirSuccess = $dir;
                } else {
                    //Don't warn when dirtory is the root path
                    if (strcmp($dir, rtrim(self::$compressDir, '/')) != 0) {
                        $dir_path = strlen($dir) ? "[{$dir}]" : "[Read Error] - last successful read was: [{$lastDirSuccess}]";
                        $info .= "DIR: {$dir_path}\n";
                    }
                }
            }

            //LOG Unreadable DIR info
            if (strlen($info)) {
                DUP_Log::Info("\nWARNING: Unable to zip directories:");
                DUP_Log::Info($info);
            }

            /**
             * count update for integrity check
             */
            $sumItems   = (self::$countDirs + self::$countFiles);

            /* ZIP FILES: Network Flush
             *  This allows the process to not timeout on fcgi
             *  setups that need a response every X seconds */
            $totalFileCount = count(self::$scanReport->ARC->Files);
            $info = '';
            if (self::$networkFlush) {
                foreach (self::$scanReport->ARC->Files as $file) {
                    $file_size = filesize($file);
                    $localFileName = $archive->getLocalFilePath($file);

                    if (is_readable($file)) {
                        if (defined('DUPLICATOR_ZIP_ARCHIVE_ADD_FROM_STR') && DUPLICATOR_ZIP_ARCHIVE_ADD_FROM_STR && $file_size < DUP_Constants::ZIP_STRING_LIMIT && self::$zipArchive->addFromString($localFileName, file_get_contents($file))) {
                            Dup_Log::Info("Adding {$file} to zip");
                            self::$limitItems++;
                            self::$countFiles++;
                        } elseif (self::$zipArchive->addFile($file, $localFileName)) {
                            Dup_Log::Info("Adding {$file} to zip");
                            self::$limitItems++;
                            self::$countFiles++;
                        } else {
                            $info .= "FILE: [{$file}]\n";
                        }
                    } else {
                        $info .= "FILE: [{$file}]\n";
                    }
                    //Trigger a flush to the web server after so many files have been loaded.
                    if (self::$limitItems > DUPLICATOR_ZIP_FLUSH_TRIGGER) {
                        self::$zipArchive->close();
                        self::$zipArchive->open(self::$zipPath);
                        self::$limitItems = 0;
                        DUP_Util::fcgiFlush();
                        DUP_Log::Info("Items archived [{$sumItems}] flushing response.");
                    }

                    if(self::$countFiles % 500 == 0) {
                        // Every so many files update the status so the UI can display
                        $archive->Package->Status = DupLiteSnapLibUtil::getWorkPercent(DUP_PackageStatus::ARCSTART, DUP_PackageStatus::ARCVALIDATION, $totalFileCount, self::$countFiles);
                        $archive->Package->update();
                    }
                }
            }
            //Normal
            else {
                foreach (self::$scanReport->ARC->Files as $file) {
                    $file_size = filesize($file);
                    $localFileName = $archive->getLocalFilePath($file);

                    if (is_readable($file)) {
                        if (defined('DUPLICATOR_ZIP_ARCHIVE_ADD_FROM_STR') && DUPLICATOR_ZIP_ARCHIVE_ADD_FROM_STR && $file_size < DUP_Constants::ZIP_STRING_LIMIT && self::$zipArchive->addFromString($localFileName, file_get_contents($file))) {
                            self::$countFiles++;
                        } elseif (self::$zipArchive->addFile($file, $localFileName)) {
                            self::$countFiles++;
                        } else {
                            $info .= "FILE: [{$file}]\n";
                        }
                    } else {
                        $info .= "FILE: [{$file}]\n";
                    }

                    if(self::$countFiles % 500 == 0) {
                        // Every so many files update the status so the UI can display
                        $archive->Package->Status = DupLiteSnapLibUtil::getWorkPercent(DUP_PackageStatus::ARCSTART, DUP_PackageStatus::ARCVALIDATION, $totalFileCount, self::$countFiles);
                        $archive->Package->update();
                    }
                }
            }

            //LOG Unreadable FILE info
            if (strlen($info)) {
                DUP_Log::Info("\nWARNING: Unable to zip files:");
                DUP_Log::Info($info);
                unset($info);
            }

            DUP_Log::Info(print_r(self::$zipArchive, true));

            /**
             * count update for integrity check
             */
            $archive->file_count = self::$countDirs + self::$countFiles;
            DUP_Log::Info("FILE ADDED TO ZIP: ".$archive->file_count);


            //--------------------------------
            //LOG FINAL RESULTS
            DUP_Util::fcgiFlush();
            $zipCloseResult = self::$zipArchive->close();
            if($zipCloseResult) {
                DUP_Log::Info("COMPRESSION RESULT: '{$zipCloseResult}'");
            } else {
                $error_message = "ZipArchive close failure.";
                DUP_Log::Error($error_message,
					"The ZipArchive engine is having issues zipping up the files on this server. For more details visit the FAQ\n"
					. "I'm getting a ZipArchive close failure when building. How can I resolve this?\n"
					. "[https://snapcreek.com/duplicator/docs/faqs-tech/#faq-package-165-q]",
                      Dup_ErrorBehavior::LogOnly);
                $buildProgress->set_failed($error_message);
                $archive->Package->setStatus(DUP_PackageStatus::ERROR);
                return;
            }

            $timerAllEnd = DUP_Util::getMicrotime();
            $timerAllSum = DUP_Util::elapsedTime($timerAllEnd, $timerAllStart);

            self::$zipFileSize = @filesize(self::$zipPath);
            DUP_Log::Info("COMPRESSED SIZE: ".DUP_Util::byteSize(self::$zipFileSize));
            DUP_Log::Info("ARCHIVE RUNTIME: {$timerAllSum}");
            DUP_Log::Info("MEMORY STACK: ".DUP_Server::getPHPMemory());
            

            
        } catch (Exception $e) {
            $error_message = "Runtime error in class.pack.archive.zip.php constructor.";
            DUP_Log::Error($error_message, "Exception: {$e}", Dup_ErrorBehavior::LogOnly);
            $buildProgress->set_failed($error_message);
            $archive->Package->setStatus(DUP_PackageStatus::ERROR);
            return;
        }
    }
}