<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
// Exit if accessed directly
if (!defined('DUPLICATOR_VERSION'))
    exit;

/**
 * Class for gathering system information about a database
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 */
class DUP_DatabaseInfo
{

    /**
     * The SQL file was built with mysqldump or PHP
     */
    public $buildMode;

    /**
     * A unique list of all the collation table types used in the database
     */
    public $collationList;

    /**
     * Does any filtered table have an upper case character in it
     */
    public $isTablesUpperCase;

    /**
     * Does the database name have any filtered characters in it
     */
    public $isNameUpperCase;

    /**
     * The real name of the database
     */
    public $name;

    /**
     * The full count of all tables in the database
     */
    public $tablesBaseCount;

    /**
     * The count of tables after the tables filter has been applied
     */
    public $tablesFinalCount;

    /**
     * The number of rows from all filtered tables in the database
     */
    public $tablesRowCount;

    /**
     * The estimated data size on disk from all filtered tables in the database
     */
    public $tablesSizeOnDisk;

    /**
     * Gets the server variable lower_case_table_names
     *
     * 0 store=lowercase;	compare=sensitive	(works only on case sensitive file systems )
     * 1 store=lowercase;	compare=insensitive
     * 2 store=exact;		compare=insensitive	(works only on case INsensitive file systems )
     * default is 0/Linux ; 1/Windows
     */
    public $varLowerCaseTables;

    /**
     * The simple numeric version number of the database server
     * @exmaple: 5.5
     */
    public $version;

    /**
     * The full text version number of the database server
     * @exmaple: 10.2 mariadb.org binary distribution
     */
    public $versionComment;

    /**
     * table wise row counts array, Key as table name and value as row count
     *  table name => row count
     */
    public $tableWiseRowCounts;

    /**
     * Integer field file structure of table, table name as key
     */
    private $intFieldsStruct = array();

    /**
     * $currentIndex => processedSchemaSize
     */
    private $indexProcessedSchemaSize = array();

    //CONSTRUCTOR
    function __construct()
    {
        $this->collationList      = array();
        $this->tableWiseRowCounts = array();
    }
}

class DUP_Database
{

    //PUBLIC
    public $Type = 'MySQL';
    public $Size;
    public $File;
    public $Path;
    public $FilterTables;
    public $FilterOn;
    public $Name;
    public $Compatible;
    public $Comments;

    /**
     *
     * @var DUP_DatabaseInfo 
     */
    public $info = null;
    //PROTECTED
    protected $Package;
    //PRIVATE
    private $dbStorePath;
    private $EOFMarker;
    private $networkFlush;

    /**
     *  Init this object
     */
    function __construct($package)
    {
        $this->Package                  = $package;
        $this->EOFMarker                = "";
        $package_zip_flush              = DUP_Settings::Get('package_zip_flush');
        $this->networkFlush             = empty($package_zip_flush) ? false : $package_zip_flush;
        $this->info                     = new DUP_DatabaseInfo();
        $this->info->varLowerCaseTables = DUP_Util::isWindows() ? 1 : 0;
    }

    /**
     *  Build the database script
     *
     *  @param DUP_Package $package A reference to the package that this database object belongs in
     *
     *  @return null
     */
    public function build($package, $errorBehavior = Dup_ErrorBehavior::ThrowException)
    {
        try {

            $this->Package = $package;
            do_action('duplicator_lite_build_database_before_start', $package);

            $time_start        = DUP_Util::getMicrotime();
            $this->Package->setStatus(DUP_PackageStatus::DBSTART);
            $this->dbStorePath = "{$this->Package->StorePath}/{$this->File}";

            $package_mysqldump        = DUP_Settings::Get('package_mysqldump');
            $package_phpdump_qrylimit = DUP_Settings::Get('package_phpdump_qrylimit');

            $mysqlDumpPath        = DUP_DB::getMySqlDumpPath();
            $mode                 = DUP_DB::getBuildMode();
            $reserved_db_filepath = duplicator_get_abs_path().'/database.sql';

            $log = "\n********************************************************************************\n";
            $log .= "DATABASE:\n";
            $log .= "********************************************************************************\n";
            $log .= "BUILD MODE:   {$mode}";
            $log .= ($mode == 'PHP') ? "(query limit - {$package_phpdump_qrylimit})\n" : "\n";
            $log .= "MYSQLTIMEOUT: ".DUPLICATOR_DB_MAX_TIME."\n";
            $log .= "MYSQLDUMP:    ";
            $log .= ($mysqlDumpPath) ? "Is Supported" : "Not Supported";
            DUP_Log::Info($log);
            $log = null;

            do_action('duplicator_lite_build_database_start', $package);

            switch ($mode) {
                case 'MYSQLDUMP':
                    $this->mysqlDump($mysqlDumpPath);
                    break;
                case 'PHP' :
                    $this->phpDump($package);
                    break;
            }

            DUP_Log::Info("SQL CREATED: {$this->File}");
            $time_end = DUP_Util::getMicrotime();
            $time_sum = DUP_Util::elapsedTime($time_end, $time_start);

            //File below 10k considered incomplete
            $sql_file_size = filesize($this->dbStorePath);
            DUP_Log::Info("SQL FILE SIZE: ".DUP_Util::byteSize($sql_file_size)." ({$sql_file_size})");

            if ($sql_file_size < 1350) {
                $error_message   = "SQL file size too low.";
                $package->BuildProgress->set_failed($error_message);
                $package->Status = DUP_PackageStatus::ERROR;
                $package->Update();
                DUP_Log::Error($error_message, "File does not look complete.  Check permission on file and parent directory at [{$this->dbStorePath}]", $errorBehavior);
                do_action('duplicator_lite_build_database_fail', $package);
            } else {
                do_action('duplicator_lite_build_database_completed', $package);
            }

            DUP_Log::Info("SQL FILE TIME: ".date("Y-m-d H:i:s"));
            DUP_Log::Info("SQL RUNTIME: {$time_sum}");

            $this->Size = @filesize($this->dbStorePath);

            $this->Package->setStatus(DUP_PackageStatus::DBDONE);
        }
        catch (Exception $e) {
            do_action('duplicator_lite_build_database_fail', $package);
            DUP_Log::Error("Runtime error in DUP_Database::Build", "Exception: {$e}", $errorBehavior);
        }
    }

    /**
     *  Get the database meta-data such as tables as all there details
     *
     *  @return array Returns an array full of meta-data about the database
     */
    public function getScannerData()
    {
        global $wpdb;

        $filterTables = isset($this->FilterTables) ? explode(',', $this->FilterTables) : array();
        $tblBaseCount = 0;
        $tblCount     = 0;

        $tables                     = $wpdb->get_results("SHOW TABLE STATUS", ARRAY_A);
        $info                       = array();
        $info['Status']['Success']  = is_null($tables) ? false : true;
        //DB_Case for the database name is never checked on
        $info['Status']['DB_Case']  = 'Good';
        $info['Status']['DB_Rows']  = 'Good';
        $info['Status']['DB_Size']  = 'Good';
        $info['Status']['TBL_Case'] = 'Good';
        $info['Status']['TBL_Rows'] = 'Good';
        $info['Status']['TBL_Size'] = 'Good';

        $info['Size']       = 0;
        $info['Rows']       = 0;
        $info['TableCount'] = 0;
        $info['TableList']  = array();
        $tblCaseFound       = 0;
        $tblRowsFound       = 0;
        $tblSizeFound       = 0;

        //Grab Table Stats
        foreach ($tables as $table) {
            $tblBaseCount++;
            $name = $table["Name"];
            if ($this->FilterOn && is_array($filterTables)) {
                if (in_array($name, $filterTables)) {
                    continue;
                }
            }

            $size = ($table["Data_length"] + $table["Index_length"]);
            $rows = empty($table["Rows"]) ? '0' : $table["Rows"];

            $info['Size']                      += $size;
            $info['Rows']                      += ($table["Rows"]);
            $info['TableList'][$name]['Case']  = preg_match('/[A-Z]/', $name) ? 1 : 0;
            $info['TableList'][$name]['Rows']  = number_format($rows);
            $info['TableList'][$name]['Size']  = DUP_Util::byteSize($size);
            $info['TableList'][$name]['USize'] = $size;
            $tblCount++;

            //Table Uppercase
            if ($info['TableList'][$name]['Case']) {
                if (!$tblCaseFound) {
                    $tblCaseFound = 1;
                }
            }

            //Table Row Count
            if ($rows > DUPLICATOR_SCAN_DB_TBL_ROWS) {
                if (!$tblRowsFound) {
                    $tblRowsFound = 1;
                }
            }

            //Table Size
            if ($size > DUPLICATOR_SCAN_DB_TBL_SIZE) {
                if (!$tblSizeFound) {
                    $tblSizeFound = 1;
                }
            }
        }

        $info['Status']['DB_Case'] = preg_match('/[A-Z]/', $wpdb->dbname) ? 'Warn' : 'Good';
        $info['Status']['DB_Rows'] = ($info['Rows'] > DUPLICATOR_SCAN_DB_ALL_ROWS) ? 'Warn' : 'Good';
        $info['Status']['DB_Size'] = ($info['Size'] > DUPLICATOR_SCAN_DB_ALL_SIZE) ? 'Warn' : 'Good';

        $info['Status']['TBL_Case'] = ($tblCaseFound) ? 'Warn' : 'Good';
        $info['Status']['TBL_Rows'] = ($tblRowsFound) ? 'Warn' : 'Good';
        $info['Status']['TBL_Size'] = ($tblSizeFound) ? 'Warn' : 'Good';

        $info['RawSize']    = $info['Size'];
        $info['Size']       = DUP_Util::byteSize($info['Size']) or "unknown";
        $info['Rows']       = number_format($info['Rows']) or "unknown";
        $info['TableList']  = $info['TableList'] or "unknown";
        $info['TableCount'] = $tblCount;

        $this->setInfoObj();
        $this->info->isTablesUpperCase = $tblCaseFound;
        $this->info->tablesBaseCount   = $tblBaseCount;
        $this->info->tablesFinalCount  = $tblCount;
        $this->info->tablesRowCount    = $info['Rows'];
        $this->info->tablesSizeOnDisk  = $info['Size'];

        return $info;
    }

    public function setInfoObj()
    {
        global $wpdb;
        $filterTables = isset($this->FilterTables) ? explode(',', $this->FilterTables) : array();

        $this->info->buildMode          = DUP_DB::getBuildMode();
        $this->info->version            = DUP_DB::getVersion();
        $this->info->versionComment     = DUP_DB::getVariable('version_comment');
        $this->info->varLowerCaseTables = DUP_DB::getVariable('lower_case_table_names');
        $this->info->name               = $wpdb->dbname;
        $this->info->isNameUpperCase    = preg_match('/[A-Z]/', $wpdb->dbname) ? 1 : 0;
        $this->info->collationList      = DUP_DB::getTableCollationList($filterTables);
    }

    /**
     * Unset tableWiseRowCounts table key for which row count is unstable
     *
     * @param object $package The reference to the current package being built     *
     * @return void
     */
    public function validateTableWiseRowCounts()
    {
        foreach ($this->Package->Database->info->tableWiseRowCounts as $rewriteTableAs => $rowCount) {
            $newRowCount = $GLOBALS['wpdb']->get_var("SELECT Count(*) FROM `{$rewriteTableAs}`");
            if ($rowCount != $newRowCount) {
                unset($this->Package->Database->info->tableWiseRowCounts[$rewriteTableAs]);
            }
        }
    }

    /**
     *  Build the database script using mysqldump
     *
     *  @return bool  Returns true if the sql script was successfully created
     */
    private function mysqlDump($exePath)
    {
        global $wpdb;
        require_once (DUPLICATOR_PLUGIN_PATH.'classes/utilities/class.u.shell.php');

        $host           = explode(':', DB_HOST);
        $host           = reset($host);
        $port           = strpos(DB_HOST, ':') ? end(explode(':', DB_HOST)) : '';
        $name           = DB_NAME;
        $mysqlcompat_on = isset($this->Compatible) && strlen($this->Compatible);

        //Build command
        $cmd = escapeshellarg($exePath);
        $cmd .= ' --no-create-db';
        $cmd .= ' --single-transaction';
        $cmd .= ' --hex-blob';
        $cmd .= ' --skip-add-drop-table';
        $cmd .= ' --routines';
        $cmd .= ' --quote-names';
        $cmd .= ' --skip-comments';
        $cmd .= ' --skip-set-charset';
        $cmd .= ' --allow-keywords';

        //Compatibility mode
        if ($mysqlcompat_on) {
            DUP_Log::Info("COMPATIBLE: [{$this->Compatible}]");
            $cmd .= " --compatible={$this->Compatible}";
        }

        //Filter tables
        $res        = $wpdb->get_results('SHOW FULL TABLES', ARRAY_N);
        $tables     = array();
        $baseTables = array();
        foreach ($res as $row) {
            if (DUP_Util::isTableExists($row[0])) {
                $tables[] = $row[0];
                if ('BASE TABLE' == $row[1]) {
                    $baseTables[] = $row[0];
                }
            }
        }
        $filterTables = isset($this->FilterTables) ? explode(',', $this->FilterTables) : null;
        $tblAllCount  = count($tables);

        foreach ($tables as $table) {
            if (in_array($table, $baseTables)) {
                $row_count                                                            = $GLOBALS['wpdb']->get_var("SELECT Count(*) FROM `{$table}`");
                $rewrite_table_as                                                     = $this->rewriteTableNameAs($table);
                $this->Package->Database->info->tableWiseRowCounts[$rewrite_table_as] = $row_count;
            }
        }
        //$tblFilterOn  = ($this->FilterOn) ? 'ON' : 'OFF';

        if (is_array($filterTables) && $this->FilterOn) {
            foreach ($tables as $key => $val) {
                if (in_array($tables[$key], $filterTables)) {
                    $cmd .= " --ignore-table={$name}.{$tables[$key]} ";
                    unset($tables[$key]);
                }
            }
        }

        $cmd .= ' -u '.escapeshellarg(DB_USER);
        $cmd .= (DB_PASSWORD) ?
            ' -p'.DUP_Shell_U::escapeshellargWindowsSupport(DB_PASSWORD) : '';

        $cmd .= ' -h '.escapeshellarg($host);
        $cmd .= (!empty($port) && is_numeric($port) ) ?
            ' -P '.$port : '';

        $isPopenEnabled = DUP_Shell_U::isPopenEnabled();

        if (!$isPopenEnabled) {
            $cmd .= ' -r '.escapeshellarg($this->dbStorePath);
        }

        $cmd .= ' '.escapeshellarg(DB_NAME);
        $cmd .= ' 2>&1';

        if ($isPopenEnabled) {
            $needToRewrite = false;
            foreach ($tables as $tableName) {
                $rewriteTableAs = $this->rewriteTableNameAs($tableName);
                if ($tableName != $rewriteTableAs) {
                    $needToRewrite = true;
                    break;
                }
            }

            if ($needToRewrite) {
                $findReplaceTableNames = array(); // orignal table name => rewrite table name

                foreach ($tables as $tableName) {
                    $rewriteTableAs = $this->rewriteTableNameAs($tableName);
                    if ($tableName != $rewriteTableAs) {
                        $findReplaceTableNames[$tableName] = $rewriteTableAs;
                    }
                }
            }

            $firstLine = '';
            DUP_LOG::trace("Executing mysql dump command by popen: $cmd");
            $handle    = popen($cmd, "r");
            if ($handle) {
                $sql_header = "/* DUPLICATOR-LITE (MYSQL-DUMP BUILD MODE) MYSQL SCRIPT CREATED ON : ".@date("Y-m-d H:i:s")." */\n\n";
                file_put_contents($this->dbStorePath, $sql_header, FILE_APPEND);
                while (!feof($handle)) {
                    $line = fgets($handle); //get ony one line
                    if ($line) {
                        if (empty($firstLine)) {
                            $firstLine = $line;
                            if (false !== stripos($line, 'Using a password on the command line interface can be insecure'))
                                continue;
                        }

                        if ($needToRewrite) {
                            $replaceCount = 1;

                            if (preg_match('/CREATE TABLE `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('CREATE TABLE `'.$tableName.'`', 'CREATE TABLE `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            } elseif (preg_match('/INSERT INTO `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('INSERT INTO `'.$tableName.'`', 'INSERT INTO `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            } elseif (preg_match('/LOCK TABLES `(.*?)`/', $line, $matches)) {
                                $tableName = $matches[1];
                                if (isset($findReplaceTableNames[$tableName])) {
                                    $rewriteTableAs = $findReplaceTableNames[$tableName];
                                    $line           = str_replace('LOCK TABLES `'.$tableName.'`', 'LOCK TABLES `'.$rewriteTableAs.'`', $line, $replaceCount);
                                }
                            }
                        }

                        file_put_contents($this->dbStorePath, $line, FILE_APPEND);
                        $output = "Ran from {$exePath}";
                    }
                }
                $mysqlResult = pclose($handle);
            } else {
                $output = '';
            }

            // Password bug > 5.6 (@see http://bugs.mysql.com/bug.php?id=66546)
            if (empty($output) && trim($firstLine) === 'Warning: Using a password on the command line interface can be insecure.') {
                $output = '';
            }
        } else {
            DUP_LOG::trace("Executing mysql dump command $cmd");
            exec($cmd, $output, $mysqlResult);
            $output = implode("\n", $output);

            // Password bug > 5.6 (@see http://bugs.mysql.com/bug.php?id=66546)
            if (trim($output) === 'Warning: Using a password on the command line interface can be insecure.') {
                $output = '';
            }
            $output = (strlen($output)) ? $output : "Ran from {$exePath}";

            $tblCreateCount = count($tables);
            $tblFilterCount = $tblAllCount - $tblCreateCount;

            //DEBUG
            //DUP_Log::Info("COMMAND: {$cmd}");
            DUP_Log::Info("FILTERED: [{$this->FilterTables}]");
            DUP_Log::Info("RESPONSE: {$output}");
            DUP_Log::Info("TABLES: total:{$tblAllCount} | filtered:{$tblFilterCount} | create:{$tblCreateCount}");
        }

        $sql_footer = "\n\n/* Duplicator WordPress Timestamp: ".date("Y-m-d H:i:s")."*/\n";
        $sql_footer .= "/* ".DUPLICATOR_DB_EOF_MARKER." */\n";
        file_put_contents($this->dbStorePath, $sql_footer, FILE_APPEND);
        if ($mysqlResult !== 0) {
            /**
             * -1 error command shell 
             * mysqldump return 
             * 0 - Success
             * 1 - Warning
             * 2 - Exception
             */
            DUP_Log::Info('MYSQL DUMP ERROR '.print_r($mysqlResult, true));
            DUP_Log::error(__('Shell mysql dump error', 'duplicator'), __('change Mysql dump engine in PHP mode', 'duplicator'), Dup_ErrorBehavior::ThrowException);
            return false;
        }

        return true;
    }

    /**
     *  Build the database script using php
     *
     *  @return bool  Returns true if the sql script was successfully created
     */
    private function phpDump($package)
    {
        global $wpdb;

        $wpdb->query("SET session wait_timeout = ".DUPLICATOR_DB_MAX_TIME);
        if (($handle = fopen($this->dbStorePath, 'w+')) == false) {
            DUP_Log::error('[PHP DUMP] ERROR Can\'t open sbStorePath "'.$this->dbStorePath.'"', Dup_ErrorBehavior::ThrowException);
        }
        $tables = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");

        $filterTables = isset($this->FilterTables) ? explode(',', $this->FilterTables) : null;
        $tblAllCount  = count($tables);
        //$tblFilterOn  = ($this->FilterOn) ? 'ON' : 'OFF';
        $qryLimit     = DUP_Settings::Get('package_phpdump_qrylimit');

        if (is_array($filterTables) && $this->FilterOn) {
            foreach ($tables as $key => $val) {
                if (in_array($tables[$key], $filterTables)) {
                    unset($tables[$key]);
                }
            }
        }
        $tblCreateCount = count($tables);
        $tblFilterCount = $tblAllCount - $tblCreateCount;

        DUP_Log::Info("TABLES: total:{$tblAllCount} | filtered:{$tblFilterCount} | create:{$tblCreateCount}");
        DUP_Log::Info("FILTERED: [{$this->FilterTables}]");

        //Added 'NO_AUTO_VALUE_ON_ZERO' at plugin version 1.2.12 to fix :
        //**ERROR** database error write 'Invalid default value for for older mysql versions
        $sql_header = "/* DUPLICATOR-LITE (PHP BUILD MODE) MYSQL SCRIPT CREATED ON : ".@date("Y-m-d H:i:s")." */\n\n";
        $sql_header .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n\n";
        $sql_header .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        fwrite($handle, $sql_header);

        //BUILD CREATES:
        //All creates must be created before inserts do to foreign key constraints
        foreach ($tables as $table) {
            $rewrite_table_as   = $this->rewriteTableNameAs($table);
            $create             = $wpdb->get_row("SHOW CREATE TABLE `{$table}`", ARRAY_N);
            $count              = 1;
            $create_table_query = str_replace($table, $rewrite_table_as, $create[1], $count);
            @fwrite($handle, "{$create_table_query};\n\n");
        }

        $procedures = $wpdb->get_col("SHOW PROCEDURE STATUS WHERE `Db` = '{$wpdb->dbname}'", 1);
        if (count($procedures)) {
            foreach ($procedures as $procedure) {
                @fwrite($handle, "DELIMITER ;;\n");
                $create = $wpdb->get_row("SHOW CREATE PROCEDURE `{$procedure}`", ARRAY_N);
                @fwrite($handle, "{$create[2]} ;;\n");
                @fwrite($handle, "DELIMITER ;\n\n");
            }
        }

        $views = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
        if (count($views)) {
            foreach ($views as $view) {
                $create = $wpdb->get_row("SHOW CREATE VIEW `{$view}`", ARRAY_N);
                @fwrite($handle, "{$create[1]};\n\n");
            }
        }

        $table_count  = count($tables);
        $table_number = 0;

        //BUILD INSERTS:
        //Create Insert in 100 row increments to better handle memory
        foreach ($tables as $table) {

            $table_number++;
            if ($table_number % 2 == 0) {
                $this->Package->Status = DupLiteSnapLibUtil::getWorkPercent(DUP_PackageStatus::DBSTART, DUP_PackageStatus::DBDONE, $table_count, $table_number);
                $this->Package->update();
            }

            $row_count        = $wpdb->get_var("SELECT Count(*) FROM `{$table}`");
            $rewrite_table_as = $this->rewriteTableNameAs($table);

            $this->Package->Database->info->tableWiseRowCounts[$rewrite_table_as] = $row_count;

            if ($row_count > $qryLimit) {
                $row_count = ceil($row_count / $qryLimit);
            } else if ($row_count > 0) {
                $row_count = 1;
            }

            if ($row_count >= 1) {
                fwrite($handle, "\n/* INSERT TABLE DATA: {$table} */\n");
            }

            for ($i = 0; $i < $row_count; $i++) {
                $sql   = "";
                $limit = $i * $qryLimit;
                $query = "SELECT * FROM `{$table}` LIMIT {$limit}, {$qryLimit}";
                $rows  = $wpdb->get_results($query, ARRAY_A);

                $select_last_error = $wpdb->last_error;
                if ('' !== $select_last_error) {
                    $fix                            = esc_html__('Please contact your DataBase administrator to fix the error.', 'duplicator');
                    $errorMessage                   = $select_last_error.' '.$fix.'.';
                    $package->BuildProgress->set_failed($errorMessage);
                    $package->BuildProgress->failed = true;
                    $package->failed                = true;
                    $package->Status                = DUP_PackageStatus::ERROR;
                    $package->Update();
                    DUP_Log::error($select_last_error, $fix, Dup_ErrorBehavior::ThrowException);
                    return;
                }

                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $sql         .= "INSERT INTO `{$rewrite_table_as}` VALUES(";
                        $num_values  = count($row);
                        $num_counter = 1;
                        foreach ($row as $value) {
                            if (is_null($value) || !isset($value)) {
                                ($num_values == $num_counter) ? $sql .= 'NULL' : $sql .= 'NULL, ';
                            } else {
                                ($num_values == $num_counter) ? $sql .= '"'.DUP_DB::escSQL($value, true).'"' : $sql .= '"'.DUP_DB::escSQL($value, true).'", ';
                            }
                            $num_counter++;
                        }
                        $sql .= ");\n";
                    }
                    fwrite($handle, $sql);
                }
            }

            //Flush buffer if enabled
            if ($this->networkFlush) {
                DUP_Util::fcgiFlush();
            }
            $sql  = null;
            $rows = null;
        }

        $sql_footer = "\nSET FOREIGN_KEY_CHECKS = 1; \n\n";
        $sql_footer .= "/* Duplicator WordPress Timestamp: ".date("Y-m-d H:i:s")."*/\n";
        $sql_footer .= "/* ".DUPLICATOR_DB_EOF_MARKER." */\n";
        fwrite($handle, $sql_footer);
        $wpdb->flush();
        fclose($handle);
    }

    private function rewriteTableNameAs($table)
    {
        $table_prefix = $this->getTablePrefix();
        if (!isset($this->sameNameTableExists)) {
            global $wpdb;
            $this->sameNameTableExists = false;
            $all_tables                = $wpdb->get_col("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
            foreach ($all_tables as $table_name) {
                if (strtolower($table_name) != $table_name && in_array(strtolower($table_name), $all_tables)) {
                    $this->sameNameTableExists = true;
                    break;
                }
            }
        }
        if (false === $this->sameNameTableExists && 0 === stripos($table, $table_prefix) && 0 !== strpos($table, $table_prefix)) {
            $post_fix           = substr($table, strlen($table_prefix));
            $rewrite_table_name = $table_prefix.$post_fix;
        } else {
            $rewrite_table_name = $table;
        }
        return $rewrite_table_name;
    }

    private function getTablePrefix()
    {
        global $wpdb;
        $table_prefix = (is_multisite() && !defined('MULTISITE')) ? $wpdb->base_prefix : $wpdb->get_blog_prefix(0);
        return $table_prefix;
    }
}