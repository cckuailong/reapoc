<?php
/**
 * Mysqldump File Doc Comment
 *
 * PHP version 5
 *
 * @category Library
 * @package  Ifsnop\Mysqldump
 * @author   Michael J. Calkins <clouddueling@github.com>
 * @author   Diego Torres <ifsnop@github.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/ifsnop/mysqldump-php
 *
 */



use Exception as Exception;


/**
 * Mysqldump Class Doc Comment
 *
 * @category Library
 * @package  Ifsnop\Mysqldump
 * @author   Michael J. Calkins <clouddueling@github.com>
 * @author   Diego Torres <ifsnop@github.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/ifsnop/mysqldump-php
 *
 */
class WPvivid_Mysqldump
{

    // Same as mysqldump
    const MAXLINESIZE = 1000000;

    // Available compression methods as constants
    const GZIP = 'Gzip';
    const BZIP2 = 'Bzip2';
    const NONE = 'None';

    // Available connection strings
    const UTF8 = 'utf8';
    const UTF8MB4 = 'utf8mb4';

    /**
    * Database username
    * @var string
    */
    public $user;
    /**
    * Database password
    * @var string
    */
    public $pass;
    /**
    * Destination filename, defaults to stdout
    * @var string
    */
    public $fileName = 'php://output';

    // Internal stuff
    private $tables = array();
    private $views = array();
    private $triggers = array();
    private $procedures = array();
    private $events = array();
    //private $dbHandler = null;
    private $dbType;
    private $compressManager;
    private $typeAdapter;
    private $dumpSettings = array();
    private $version;
    private $tableColumnTypes = array();
    public $log=false;
    public $task_id='';
    /**
    * database name, parsed from dsn
    * @var string
    */
    private $dbName;
    /**
    * host name, parsed from dsn
    * @var string
    */
    private $host;
    /**
    * dsn string parsed as an array
    * @var array
    */
    private $dsnArray = array();

    public $last_query_string='';

    public function __construct(
        $host = '',
        $dbname='',
        $user = '',
        $pass = '',
        $is_additional_db = false,
        $dumpSettings = array()
    ) {
        $dumpSettingsDefault = array(
            'include-tables' => array(),
            'exclude-tables' => array(),
            'compress' => WPvivid_Mysqldump::NONE,
            'init_commands' => array(),
            'no-data' => array(),
            'reset-auto-increment' => false,
            'add-drop-database' => false,
            'add-drop-table' => false,
            'add-drop-trigger' => true,
            'add-locks' => true,
            'complete-insert' => false,
            'databases' => false,
            'default-character-set' => WPvivid_Mysqldump::UTF8,
            'disable-keys' => true,
            'extended-insert' => true,
            'events' => false,
            'hex-blob' => true, /* faster than escaped content */
            'net_buffer_length' => self::MAXLINESIZE,
            'no-autocommit' => false,
            'no-create-info' => false,
            'lock-tables' => false,
            'routines' => false,
            'single-transaction' => true,
            'skip-triggers' => false,
            'skip-tz-utc' => false,
            'skip-comments' => false,
            'skip-dump-date' => false,
            'where' => '',
            /* deprecated */
            'disable-foreign-keys-check' => true,
            'site_url'=>'',
            'home_url'=>'',
            'content_url'=>'',
            'prefix'=>''
        );

        if(defined('DB_CHARSET'))
        {
            $dumpSettingsDefault['default-character-set']=DB_CHARSET;
        }

        $this->dbType=$this->get_db_type($is_additional_db);
        $this->user = $user;
        $this->pass = $pass;
        $this->host=$host;
        $this->dbName=$dbname;
        $this->dumpSettings = self::array_replace_recursive($dumpSettingsDefault, $dumpSettings);

        $this->dumpSettings['init_commands'][] = "SET NAMES " . $this->dumpSettings['default-character-set'];

        if (false === $this->dumpSettings['skip-tz-utc'])
        {
            $this->dumpSettings['init_commands'][] = "SET TIME_ZONE='+00:00'";
        }

        $diff = array_diff(array_keys($this->dumpSettings), array_keys($dumpSettingsDefault));
        if (count($diff)>0) {
            throw new Exception("Unexpected value in dumpSettings: (" . implode(",", $diff) . ")");
        }

        if ( !is_array($this->dumpSettings['include-tables']) ||
            !is_array($this->dumpSettings['exclude-tables']) ) {
            throw new Exception("Include-tables and exclude-tables should be arrays");
        }

        // Dump the same views as tables, mimic mysqldump behaviour
        $this->dumpSettings['include-views'] = $this->dumpSettings['include-tables'];

        // Create a new compressManager to manage compressed output
        $this->compressManager = CompressManagerFactory::create($this->dumpSettings['compress']);
    }

    public function get_db_type($is_additional_db)
    {
        if($is_additional_db){
            return 'mysql';
        }
        else {
            $common_setting = WPvivid_Setting::get_setting(false, 'wpvivid_common_setting');
            $db_connect_method = isset($common_setting['options']['wpvivid_common_setting']['db_connect_method']) ? $common_setting['options']['wpvivid_common_setting']['db_connect_method'] : 'wpdb';
            if ($db_connect_method === 'wpdb') {
                return 'wpdb';
            } else {
                return 'mysql';
            }
        }
    }

    /**
     * Destructor of Mysqldump. Unsets dbHandlers and database objects.
     *
     */
    public function __destruct()
    {
        //$this->dbHandler = null;
    }

    /**
     * Custom array_replace_recursive to be used if PHP < 5.3
     * Replaces elements from passed arrays into the first array recursively
     *
     * @param array $array1 The array in which elements are replaced
     * @param array $array2 The array from which elements will be extracted
     *
     * @return array Returns an array, or NULL if an error occurs.
     */
    public static function array_replace_recursive($array1, $array2)
    {
        if (function_exists('array_replace_recursive')) {
            return array_replace_recursive($array1, $array2);
        }

        foreach ($array2 as $key => $value) {
            if (is_array($value)) {
                $array1[$key] = self::array_replace_recursive($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    /**
     * Parse DSN string and extract dbname value
     * Several examples of a DSN string
     *   mysql:host=localhost;dbname=testdb
     *   mysql:host=localhost;port=3307;dbname=testdb
     *   mysql:unix_socket=/tmp/mysql.sock;dbname=testdb
     *
     * @param string $dsn dsn string to parse
     */
    private function parseDsn($dsn)
    {
        if (empty($dsn) || (false === ($pos = strpos($dsn, ":")))) {
            throw new Exception("Empty DSN string");
        }

        $this->dsn = $dsn;
        $this->dbType = strtolower(substr($dsn, 0, $pos));

        if (empty($this->dbType)) {
            throw new Exception("Missing database type from DSN string");
        }

        $dsn = substr($dsn, $pos + 1);

        foreach(explode(";", $dsn) as $kvp) {
            $kvpArr = explode("=", $kvp);
            $this->dsnArray[strtolower($kvpArr[0])] = $kvpArr[1];
        }

        if (empty($this->dsnArray['host']) &&
            empty($this->dsnArray['unix_socket'])) {
            throw new Exception("Missing host from DSN string");
        }
        $this->host = (!empty($this->dsnArray['host'])) ?
            $this->dsnArray['host'] :
            $this->dsnArray['unix_socket'];

        if (empty($this->dsnArray['dbname'])) {
            throw new Exception("Missing database name from DSN string");
        }

        $this->dbName = $this->dsnArray['dbname'];

        return true;
    }

    /**
     * Connect with PDO
     *
     * @return null
     */
    private function connect()
    {
        // Connecting with PDO
        /*
        try {
            switch ($this->dbType) {
                case 'sqlite':
                    $this->dbHandler = @new PDO("sqlite:" . $this->dbName, null, null, $this->pdoSettings);
                    break;
                case 'mysql':
                case 'pgsql':
                case 'dblib':
                    $this->dbHandler = @new PDO(
                        $this->dsn,
                        $this->user,
                        $this->pass,
                        $this->pdoSettings
                    );
                    // Execute init commands once connected
                    foreach($this->dumpSettings['init_commands'] as $stmt) {
                        $this->dbHandler->exec($stmt);
                    }
                    // Store server version
                    $this->version = $this->dbHandler->getAttribute(PDO::ATTR_SERVER_VERSION);
                    break;
                default:
                    throw new Exception("Unsupported database type (" . $this->dbType . ")");
            }
        } catch (PDOException $e) {
            throw new Exception(
                "Connection to " . $this->dbType . " failed with message: " .
                $e->getMessage()
            );
        }*/

        $this->typeAdapter = TypeAdapterFactory::create($this->dbType, null);
        $this->typeAdapter->connect($this->host,$this->dbName,$this->user,$this->pass,$this->dumpSettings['init_commands']);
    }

    /**
     * Main call
     *
     * @param string $filename  Name of file to write sql dump to
     * @return null
     */
    public function start($filename = '')
    {
        // Output file can be redefined here
        if (!empty($filename)) {
            $this->fileName = $filename;
        }

        // Connect to database
        $this->connect();

        // Create output file
        $this->compressManager->open($this->fileName);

        // Write some basic info to output file
        $this->compressManager->write($this->getDumpFileHeader());

        $this->compressManager->write('/* # site_url: '.$this->dumpSettings['site_url'].' */;'.PHP_EOL);
        $this->compressManager->write('/* # home_url: '.$this->dumpSettings['home_url'].' */;'.PHP_EOL);
        $this->compressManager->write('/* # content_url: '.$this->dumpSettings['content_url'].' */;'.PHP_EOL);
        $upload_dir  = wp_upload_dir();
        $this->compressManager->write('/* # upload_url: '.$upload_dir['baseurl'].' */;'.PHP_EOL);
        $this->compressManager->write('/* # table_prefix: '.$this->dumpSettings['prefix'].' */;'.PHP_EOL.PHP_EOL.PHP_EOL);

        // Store server settings and use sanner defaults to dump
        $this->compressManager->write(
            $this->typeAdapter->backup_parameters($this->dumpSettings)
        );

        if ($this->dumpSettings['databases']) {
            $this->compressManager->write(
                $this->typeAdapter->getDatabaseHeader($this->dbName)
            );
            if ($this->dumpSettings['add-drop-database']) {
                $this->compressManager->write(
                    $this->typeAdapter->add_drop_database($this->dbName)
                );
            }
        }

        // Get table, view and trigger structures from database
        $this->getDatabaseStructure();

        if ($this->dumpSettings['databases']) {
            $this->compressManager->write(
                $this->typeAdapter->databases($this->dbName)
            );
        }

        // If there still are some tables/views in include-tables array,
        // that means that some tables or views weren't found.
        // Give proper error and exit.
        // This check will be removed once include-tables supports regexps
        if (0 < count($this->dumpSettings['include-tables'])) {
            $name = implode(",", $this->dumpSettings['include-tables']);
            throw new Exception("Table (" . $name . ") not found in database");
        }
        $this->exportTables();
        /*
        global $wpvivid_plugin;

        $this->exportTables();
        if($this -> privileges['SHOW VIEW'] == 0){
            $wpvivid_plugin->wpvivid_log->WriteLog('The lack of SHOW VIEW privilege, the backup will skip exportViews() to continue.','notice');
        }else{
            $this->exportViews();
        }

        if($this -> privileges['TRIGGER'] == 0){
            $wpvivid_plugin->wpvivid_log->WriteLog('The lack of TRIGGER privilege, the backup will skip exportTriggers() to continue.','notice');
        }else{
            $this->exportTriggers();
        }

        if($this -> privileges['CREATE ROUTINE'] == 0){
            $wpvivid_plugin->wpvivid_log->WriteLog('The lack of CREATE ROUTINE privilege, the backup will skip exportProcedures() to continue.','notice');
        }else{
            $this->exportProcedures();
        }

        if($this -> privileges['EVENT'] == 0){
            $wpvivid_plugin->wpvivid_log->WriteLog('The lack of EVENT privilege, the backup will skip exportEvents() to continue.','notice');
        }else{
            $this->exportEvents();
        }
        */

        // Restore saved parameters
        $this->compressManager->write(
            $this->typeAdapter->restore_parameters($this->dumpSettings)
        );
        // Write some stats to output file
        $this->compressManager->write($this->getDumpFileFooter());
        // Close output file
        $this->compressManager->close();
    }

    /**
     * Returns header for dump file
     *
     * @return string
     */
    private function getDumpFileHeader()
    {
        $header = '';
        if ( !$this->dumpSettings['skip-comments'] ) {
            // Some info about software, source and time
            $header = "-- mysqldump-php https://github.com/ifsnop/mysqldump-php" . PHP_EOL .
                    "--" . PHP_EOL .
                    "-- Host: {$this->host}\tDatabase: {$this->dbName}" . PHP_EOL .
                    "-- ------------------------------------------------------" . PHP_EOL;

            if ( !empty($this->version) ) {
                $header .= "-- Server version \t" . $this->version . PHP_EOL;
            }

            if ( !$this->dumpSettings['skip-dump-date'] ) {
                $header .= "-- Date: " . date('r') . PHP_EOL . PHP_EOL;
            }
        }
        return $header;
    }

    /**
     * Returns footer for dump file
     *
     * @return string
     */
    private function getDumpFileFooter()
    {
        $footer = '';
        if (!$this->dumpSettings['skip-comments']) {
            $footer .= '-- Dump completed';
            if (!$this->dumpSettings['skip-dump-date']) {
                $footer .= ' on: ' . date('r');
            }
            $footer .= PHP_EOL;
        }

        return $footer;
    }

    /**
     * Reads table and views names from database.
     * Fills $this->tables array so they will be dumped later.
     *
     * @return null
     */
    private function getDatabaseStructure()
    {
        // Listing all tables from database
        if (empty($this->dumpSettings['include-tables'])) {
            // include all tables for now, blacklisting happens later

            foreach ( $this->query($this->typeAdapter->show_tables($this->dbName)) as $row) {
                array_push($this->tables, current($row));
            }
        } else {
            // include only the tables mentioned in include-tables
            foreach ($this->query($this->typeAdapter->show_tables($this->dbName)) as $row) {
                if (in_array(current($row), $this->dumpSettings['include-tables'], true)) {
                    array_push($this->tables, current($row));
                    $elem = array_search(
                        current($row),
                        $this->dumpSettings['include-tables']
                    );
                    unset($this->dumpSettings['include-tables'][$elem]);
                }
            }
        }

        // Listing all views from database
        if (empty($this->dumpSettings['include-views'])) {
            // include all views for now, blacklisting happens later
            foreach ($this->query($this->typeAdapter->show_views($this->dbName)) as $row) {
                array_push($this->views, current($row));
            }
        } else {
            // include only the tables mentioned in include-tables
            foreach ($this->query($this->typeAdapter->show_views($this->dbName)) as $row) {
                if (in_array(current($row), $this->dumpSettings['include-views'], true)) {
                    array_push($this->views, current($row));
                    $elem = array_search(
                        current($row),
                        $this->dumpSettings['include-views']
                    );
                    unset($this->dumpSettings['include-views'][$elem]);
                }
            }
        }

        // Listing all triggers from database
        if (false === $this->dumpSettings['skip-triggers']) {
            foreach ($this->query($this->typeAdapter->show_triggers($this->dbName)) as $row) {
                array_push($this->triggers, $row['Trigger']);
            }
        }

        // Listing all procedures from database
        if ($this->dumpSettings['routines']) {
            foreach ($this->query($this->typeAdapter->show_procedures($this->dbName)) as $row) {
                array_push($this->procedures, $row['procedure_name']);
            }
        }

        // Listing all events from database
        if ($this->dumpSettings['events']) {
            foreach ($this->query($this->typeAdapter->show_events($this->dbName)) as $row) {
                array_push($this->events, $row['event_name']);
            }
        }
    }

    /**
     * Compare if $table name matches with a definition inside $arr
     * @param $table string
     * @param $arr array with strings or patterns
     * @return bool
     */
    private function matches($table, $arr) {
        $match = false;

        foreach ($arr as $pattern) {
            if ( '/' != $pattern[0] ) {
                continue;
            }
            if ( 1 == preg_match($pattern, $table) ) {
                $match = true;
            }
        }

        return in_array($table, $arr) || $match;
    }

    /**
     * Exports all the tables selected from database
     *
     * @return null
     */
    private function exportTables()
    {
        // Exporting tables one by one
        $i=0;
        $i_step=0;
        if($this->task_id!=='')
        {
            $options_name[]='backup_options';
            //$options_name[]='ismerge';
            $options=WPvivid_taskmanager::get_task_options($this->task_id,$options_name);
            if($options['backup_options']['ismerge'])
            {
                if(isset($options['backup_options']['backup']['backup_type'])) {
                    $i_step = intval(1 / (sizeof($options['backup_options']['backup']['backup_type']) + 1) * 100);
                }
                else{
                    $i_step = intval(1 / (sizeof($options['backup_options']['backup']) + 1) * 100);
                }
            }
            else
            {
                if(isset($options['backup_options']['backup']['backup_type'])) {
                    $i_step = intval(1 / sizeof($options['backup_options']['backup']['backup_type']) * 100);
                }
                else{
                    $i_step = intval(1 / sizeof($options['backup_options']['backup']) * 100);
                }
            }
        }

        foreach ($this->tables as $table)
        {
            if ( $this->matches($table, $this->dumpSettings['exclude-tables']) )
            {
                continue;
            }

            if($this->task_id!=='')
            {
                $message='Preparing to dump table '.$table;
                global $wpvivid_plugin;
                $wpvivid_plugin->wpvivid_log->WriteLog($message,'notice');
                WPvivid_taskmanager::update_backup_sub_task_progress($this->task_id,'backup',WPVIVID_BACKUP_TYPE_DB,0,$message);
            }

            $this->getTableStructure($table);
            if($this->tableColumnTypes[$table]===false)
            {
                global $wpvivid_plugin;
                $message='get Table Structure failed. table:'.$table;
                $wpvivid_plugin->wpvivid_log->WriteLog($message,'notice');
                continue;
            }
            if ( false === $this->dumpSettings['no-data'] ) { // don't break compatibility with old trigger
                $this->listValues($table);
            } else if ( true === $this->dumpSettings['no-data']
                 || $this->matches($table, $this->dumpSettings['no-data']) ) {
                continue;
            } else {
                $this->listValues($table);
            }
            $i++;
            if($this->task_id!=='')
            {
                $i_progress=intval($i/sizeof($this->tables)*$i_step);
                WPvivid_taskmanager::update_backup_main_task_progress($this->task_id,'backup',$i_progress,0);
            }
        }
        return ;
    }

    /**
     * Exports all the views found in database
     *
     * @return null
     */
    private function exportViews()
    {
        if (false === $this->dumpSettings['no-create-info']) {
            // Exporting views one by one
            foreach ($this->views as $view) {
                if ( $this->matches($view, $this->dumpSettings['exclude-tables']) ) {
                    continue;
                }
                $this->tableColumnTypes[$view] = $this->getTableColumnTypes($view);
                $this->getViewStructureTable($view);
            }
            foreach ($this->views as $view) {
                if ( $this->matches($view, $this->dumpSettings['exclude-tables']) ) {
                    continue;
                }
                $this->getViewStructureView($view);
            }
        }
    }

    /**
     * Exports all the triggers found in database
     *
     * @return null
     */
    private function exportTriggers()
    {
        // Exporting triggers one by one
        foreach ($this->triggers as $trigger) {
            $this->getTriggerStructure($trigger);
        }
    }

    /**
     * Exports all the procedures found in database
     *
     * @return null
     */
    private function exportProcedures()
    {
        // Exporting triggers one by one
        foreach ($this->procedures as $procedure) {
            $this->getProcedureStructure($procedure);
        }
    }

    /**
     * Exports all the events found in database
     *
     * @return null
     */
    private function exportEvents()
    {
        // Exporting triggers one by one
        foreach ($this->events as $event) {
            $this->getEventStructure($event);
        }
    }

    /**
     * Table structure extractor
     *
     * @todo move specific mysql code to typeAdapter
     * @param string $tableName  Name of table to export
     * @return null
     */
    private function getTableStructure($tableName)
    {
        if (!$this->dumpSettings['no-create-info']) {
            $ret = '';
            if (!$this->dumpSettings['skip-comments']) {
                $ret = "--" . PHP_EOL .
                    "-- Table structure for table `$tableName`" . PHP_EOL .
                    "--" . PHP_EOL . PHP_EOL;
            }
            $stmt = $this->typeAdapter->show_create_table($tableName);

            foreach ($this->query($stmt) as $r)
            {
                $this->compressManager->write($ret);
                if ($this->dumpSettings['add-drop-table']) {
                    $this->compressManager->write(
                        $this->typeAdapter->drop_table($tableName)
                    );
                }

                $this->compressManager->write(
                    $this->typeAdapter->create_table($r, $this->dumpSettings)
                );
                break;
            }
        }
        $this->tableColumnTypes[$tableName] = $this->getTableColumnTypes($tableName);
        return;
    }

    /**
     * Store column types to create data dumps and for Stand-In tables
     *
     * @param string $tableName  Name of table to export
     * @return array type column types detailed
     */

    private function getTableColumnTypes($tableName) {
        $columnTypes = array();
        $columns = $this->query(
            $this->typeAdapter->show_columns($tableName)
        );
        if($columns===false)
        {
            global $wpvivid_plugin;
            $error=$this->typeAdapter->errorInfo();
            if(isset($error[2])){
                $error = 'Error: '.$error[2];
            }
            else{
                $error = '';
            }
            $message='Show columns failed. '.$error;
            $wpvivid_plugin->wpvivid_log->WriteLog($message, 'warning');
            $columns = $this->query(
                'DESCRIBE '.$tableName
            );
            if($columns===false)
            {
                $error=$this->typeAdapter->errorInfo();
                if(isset($error[2])){
                    $error = 'Error: '.$error[2];
                }
                else{
                    $error = '';
                }
                $message='DESCRIBE failed. '.$error;
                $wpvivid_plugin->wpvivid_log->WriteLog($message, 'warning');
                return false;
            }
        }

        foreach($columns as $key => $col) {
            $types = $this->typeAdapter->parseColumnType($col);
            $columnTypes[$col['Field']] = array(
                'is_numeric'=> $types['is_numeric'],
                'is_blob' => $types['is_blob'],
                'type' => $types['type'],
                'type_sql' => $col['Type'],
                'is_virtual' => $types['is_virtual']
            );
        }

        return $columnTypes;
    }

    /**
     * View structure extractor, create table (avoids cyclic references)
     *
     * @todo move mysql specific code to typeAdapter
     * @param string $viewName  Name of view to export
     * @return null
     */
    private function getViewStructureTable($viewName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--" . PHP_EOL .
                "-- Stand-In structure for view `${viewName}`" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_view($viewName);

        // create views as tables, to resolve dependencies
        foreach ($this->query($stmt) as $r) {
            if ($this->dumpSettings['add-drop-table']) {
                $this->compressManager->write(
                    $this->typeAdapter->drop_view($viewName)
                );
            }

            $this->compressManager->write(
                $this->createStandInTable($viewName)
            );
            break;
        }
    }

    /**
     * Write a create table statement for the table Stand-In, show create
     * table would return a create algorithm when used on a view
     *
     * @param string $viewName  Name of view to export
     * @return string create statement
     */
    function createStandInTable($viewName) {
        $ret = array();
        foreach($this->tableColumnTypes[$viewName] as $k => $v) {
            $ret[] = "`${k}` ${v['type_sql']}";
        }

        $ret = implode(PHP_EOL . ",", $ret);

        $ret = "CREATE TABLE IF NOT EXISTS `$viewName` (" .
            PHP_EOL . $ret . PHP_EOL . ");" . PHP_EOL;

        return $ret;
    }

    /**
     * View structure extractor, create view
     *
     * @todo move mysql specific code to typeAdapter
     * @param string $viewName  Name of view to export
     * @return null
     */
    private function getViewStructureView($viewName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--" . PHP_EOL .
                "-- View structure for view `${viewName}`" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_view($viewName);

        // create views, to resolve dependencies
        // replacing tables with views
        foreach ($this->query($stmt) as $r) {
            // because we must replace table with view, we should delete it
            $this->compressManager->write(
                $this->typeAdapter->drop_view($viewName)
            );
            $this->compressManager->write(
                $this->typeAdapter->create_view($r)
            );
            break;
        }
    }

    /**
     * Trigger structure extractor
     *
     * @param string $triggerName  Name of trigger to export
     * @return null
     */
    private function getTriggerStructure($triggerName)
    {
        $stmt = $this->typeAdapter->show_create_trigger($triggerName);
        foreach ($this->query($stmt) as $r) {
            if ($this->dumpSettings['add-drop-trigger']) {
                $this->compressManager->write(
                    $this->typeAdapter->add_drop_trigger($triggerName)
                );
            }
            $this->compressManager->write(
                $this->typeAdapter->create_trigger($r)
            );
            return;
        }
    }

    /**
     * Procedure structure extractor
     *
     * @param string $procedureName  Name of procedure to export
     * @return null
     */
    private function getProcedureStructure($procedureName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--" . PHP_EOL .
                "-- Dumping routines for database '" . $this->dbName . "'" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_procedure($procedureName);
        foreach ($this->query($stmt) as $r) {
            $this->compressManager->write(
                $this->typeAdapter->create_procedure($r, $this->dumpSettings)
            );
            return;
        }
    }

    /**
     * Event structure extractor
     *
     * @param string $eventName  Name of event to export
     * @return null
     */
    private function getEventStructure($eventName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--" . PHP_EOL .
                "-- Dumping events for database '" . $this->dbName . "'" . PHP_EOL .
                "--" . PHP_EOL . PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_event($eventName);
        foreach ($this->query($stmt) as $r) {
            $this->compressManager->write(
                $this->typeAdapter->create_event($r, $this->dumpSettings)
            );
            return;
        }
    }

    /**
     * Escape values with quotes when needed
     *
     * @param string $tableName Name of table which contains rows
     * @param array $row Associative array of column names and values to be quoted
     *
     * @return string
     */
    private function escape($tableName, $row)
    {
        $ret = array();
        $columnTypes = $this->tableColumnTypes[$tableName];
        foreach ($row as $colName => $colValue) {
            if (is_null($colValue)) {
                $ret[] = "NULL";
            } elseif ($this->dumpSettings['hex-blob'] && $columnTypes[$colName]['is_blob']) {
                if ($columnTypes[$colName]['type'] == 'bit' || !empty($colValue)) {
                    $ret[] = "0x${colValue}";
                } else {
                    $ret[] = "''";
                }
            } elseif ($columnTypes[$colName]['is_numeric']) {
                $ret[] = $colValue;
            } else {
                $ret[] = $this->typeAdapter->quote($colValue);
            }
        }
        return $ret;
    }

    /**
     * Table rows extractor
     *
     * @param string $tableName  Name of table to export
     *
     * @return null
     */
    private function listValues($tableName)
    {
        $this->prepareListValues($tableName);

        $onlyOnce = true;
        $lineSize = 0;

        $colStmt = $this->getColumnStmt($tableName);

        global $wpdb;
        $prefix=$wpdb->base_prefix;

        if($this->dbType=='wpdb')
        {
            $start=0;
            $limit_count=5000;
            $sum =$wpdb->get_var("SELECT COUNT(1) FROM `{$tableName}`");
            if(substr($tableName, strlen($prefix))=='options')
            {
                $stmt = "SELECT " . implode(",", $colStmt) . " FROM `$tableName` WHERE option_name !='wpvivid_task_list'";
            }
            else
            {
                $stmt = "SELECT " . implode(",", $colStmt) . " FROM `$tableName`";
            }

            if ($this->dumpSettings['where']) {
                $stmt .= " WHERE {$this->dumpSettings['where']}";
            }

            $i=0;
            $i_check_cancel=0;
            $count=0;

            while($sum > $start)
            {
                $limit = " LIMIT {$limit_count} OFFSET {$start}";

                $query=$stmt.$limit;
                $resultSet = $this->query($query);

                if($resultSet===false)
                {
                    global $wpvivid_plugin;
                    $error=$this->typeAdapter->errorInfo();
                    if(isset($error[2])){
                        $error = 'Error: '.$error[2];
                    }
                    else{
                        $error = '';
                    }

                    $message='listValues failed. '.$error;
                    $wpvivid_plugin->wpvivid_log->WriteLog($message, 'warning');

                    $this->endListValues($tableName);
                    return ;
                }

                foreach ($resultSet as $row)
                {
                    $i++;
                    $vals = $this->escape($tableName, $row);

                    foreach($vals as $key => $value){
                        if($value === '\'0000-00-00 00:00:00\'')
                            $vals[$key] = '\'1999-01-01 00:00:00\'';
                    }

                    if ($onlyOnce || !$this->dumpSettings['extended-insert'])
                    {

                        if ($this->dumpSettings['complete-insert'])
                        {
                            $lineSize += $this->compressManager->write(
                                "INSERT INTO `$tableName` (" .
                                implode(", ", $colStmt) .
                                ") VALUES (" . implode(",", $vals) . ")"
                            );
                        } else {
                            $lineSize += $this->compressManager->write(
                                "INSERT INTO `$tableName` VALUES (" . implode(",", $vals) . ")"
                            );
                        }
                        $onlyOnce = false;
                    } else {
                        $lineSize += $this->compressManager->write(",(" . implode(",", $vals) . ")");
                    }
                    if (($lineSize > $this->dumpSettings['net_buffer_length']) ||
                        !$this->dumpSettings['extended-insert']) {
                        $onlyOnce = true;
                        $lineSize = $this->compressManager->write(";" . PHP_EOL);
                    }

                    if($i>=200000)
                    {
                        $count+=$i;
                        $i=0;
                        if($this->task_id!=='')
                        {
                            $i_check_cancel++;
                            if($i_check_cancel>5)
                            {
                                $i_check_cancel=0;
                                global $wpvivid_plugin;
                                $wpvivid_plugin->check_cancel_backup($this->task_id);
                            }
                            $message='Dumping table '.$tableName.', rows dumped: '.$count.' rows.';
                            WPvivid_taskmanager::update_backup_sub_task_progress($this->task_id,'backup',WPVIVID_BACKUP_TYPE_DB,0,$message);
                        }
                    }
                }

                $this->typeAdapter->closeCursor($resultSet);

                $start += $limit_count;
            }

            if (!$onlyOnce) {
                $this->compressManager->write(";" . PHP_EOL);
            }

            $this->endListValues($tableName);
        }
        else
        {
            if(substr($tableName, strlen($prefix))=='options')
            {
                $stmt = "SELECT " . implode(",", $colStmt) . " FROM `$tableName` WHERE option_name !='wpvivid_task_list'";
            }
            else
            {
                $stmt = "SELECT " . implode(",", $colStmt) . " FROM `$tableName`";
            }

            if ($this->dumpSettings['where']) {
                $stmt .= " WHERE {$this->dumpSettings['where']}";
            }

            $resultSet = $this->query($stmt);

            if($resultSet===false)
            {
                global $wpvivid_plugin;
                $error=$this->typeAdapter->errorInfo();
                if(isset($error[2])){
                    $error = 'Error: '.$error[2];
                }
                else{
                    $error = '';
                }

                $message='listValues failed. '.$error;
                $wpvivid_plugin->wpvivid_log->WriteLog($message, 'warning');

                $this->endListValues($tableName);
                return ;
            }

            $i=0;
            $i_check_cancel=0;
            $count=0;
            foreach ($resultSet as $row)
            {
                $i++;
                $vals = $this->escape($tableName, $row);

                foreach($vals as $key => $value){
                    if($value === '\'0000-00-00 00:00:00\'')
                        $vals[$key] = '\'1999-01-01 00:00:00\'';
                }

                if ($onlyOnce || !$this->dumpSettings['extended-insert'])
                {

                    if ($this->dumpSettings['complete-insert'])
                    {
                        $lineSize += $this->compressManager->write(
                            "INSERT INTO `$tableName` (" .
                            implode(", ", $colStmt) .
                            ") VALUES (" . implode(",", $vals) . ")"
                        );
                    } else {
                        $lineSize += $this->compressManager->write(
                            "INSERT INTO `$tableName` VALUES (" . implode(",", $vals) . ")"
                        );
                    }
                    $onlyOnce = false;
                } else {
                    $lineSize += $this->compressManager->write(",(" . implode(",", $vals) . ")");
                }
                if (($lineSize > $this->dumpSettings['net_buffer_length']) ||
                    !$this->dumpSettings['extended-insert']) {
                    $onlyOnce = true;
                    $lineSize = $this->compressManager->write(";" . PHP_EOL);
                }

                if($i>=200000)
                {
                    $count+=$i;
                    $i=0;
                    if($this->task_id!=='')
                    {
                        $i_check_cancel++;
                        if($i_check_cancel>5)
                        {
                            $i_check_cancel=0;
                            global $wpvivid_plugin;
                            $wpvivid_plugin->check_cancel_backup($this->task_id);
                        }
                        $message='Dumping table '.$tableName.', rows dumped: '.$count.' rows.';
                        WPvivid_taskmanager::update_backup_sub_task_progress($this->task_id,'backup',WPVIVID_BACKUP_TYPE_DB,0,$message);
                    }
                }
            }

            $this->typeAdapter->closeCursor($resultSet);
            //$resultSet->closeCursor();

            if (!$onlyOnce) {
                $this->compressManager->write(";" . PHP_EOL);
            }

            $this->endListValues($tableName);
        }
    }

    /**
     * Table rows extractor, append information prior to dump
     *
     * @param string $tableName  Name of table to export
     *
     * @return null
     */
    function prepareListValues($tableName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $this->compressManager->write(
                "--" . PHP_EOL .
                "-- Dumping data for table `$tableName`" .  PHP_EOL .
                "--" . PHP_EOL . PHP_EOL
            );
        }

        if ($this->dumpSettings['single-transaction']) {
            $this->exec($this->typeAdapter->setup_transaction());
            $this->exec($this->typeAdapter->start_transaction());
        }

        if ($this->dumpSettings['lock-tables'])
        {
            $this->typeAdapter->lock_table($tableName);

            //if($this -> privileges['LOCK TABLES'] == 0)
            //{
            //global $wpvivid_plugin;
            //    $wpvivid_plugin->wpvivid_log->WriteLog('The lack of LOCK TABLES privilege, the backup will skip lock_tables() to continue.','notice');
            //}else{
            //    $this->typeAdapter->lock_table($tableName);
            //}
        }

        if ($this->dumpSettings['add-locks']) {
            $this->compressManager->write(
                $this->typeAdapter->start_add_lock_table($tableName)
            );
        }

        if ($this->dumpSettings['disable-keys']) {
            $this->compressManager->write(
                $this->typeAdapter->start_add_disable_keys($tableName)
            );
        }

        // Disable autocommit for faster reload
        if ($this->dumpSettings['no-autocommit']) {
            $this->compressManager->write(
                $this->typeAdapter->start_disable_autocommit()
            );
        }

        return;
    }

    /**
     * Table rows extractor, close locks and commits after dump
     *
     * @param string $tableName  Name of table to export
     *
     * @return null
     */
    function endListValues($tableName)
    {
        if ($this->dumpSettings['disable-keys']) {
            $this->compressManager->write(
                $this->typeAdapter->end_add_disable_keys($tableName)
            );
        }

        if ($this->dumpSettings['add-locks']) {
            $this->compressManager->write(
                $this->typeAdapter->end_add_lock_table($tableName)
            );
        }

        if ($this->dumpSettings['single-transaction']) {
            $this->exec($this->typeAdapter->commit_transaction());
        }

        if ($this->dumpSettings['lock-tables']) {
            $this->typeAdapter->unlock_table($tableName);
        }

        // Commit to enable autocommit
        if ($this->dumpSettings['no-autocommit']) {
            $this->compressManager->write(
                $this->typeAdapter->end_disable_autocommit()
            );
        }

        $this->compressManager->write(PHP_EOL);

        return;
    }

    /**
     * Build SQL List of all columns on current table
     *
     * @param string $tableName  Name of table to get columns
     *
     * @return string SQL sentence with columns
     */
    function getColumnStmt($tableName)
    {
        $colStmt = array();
        foreach($this->tableColumnTypes[$tableName] as $colName => $colType) {
            if ($colType['type'] == 'bit' && $this->dumpSettings['hex-blob']) {
                $colStmt[] = "LPAD(HEX(`${colName}`),2,'0') AS `${colName}`";
            } else if ($colType['is_blob'] && $this->dumpSettings['hex-blob']) {
                $colStmt[] = "HEX(`${colName}`) AS `${colName}`";
            } else if ($colType['is_virtual']) {
                $this->dumpSettings['complete-insert'] = true;
                continue;
            } else {
                $colStmt[] = "`${colName}`";
            }
        }

        return $colStmt;
    }

    public function query($query_string)
    {
        $this->last_query_string=$query_string;
        return  $this->typeAdapter->query($query_string);
    }

    private function exec($query_string)
    {
        $this->last_query_string=$query_string;
        return  $this->typeAdapter->query($query_string);
    }
}


