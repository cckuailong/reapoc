<?php
/**
 * Walks every table in db that then walks every row and column replacing searches with replaces
 * large tables are split into 50k row blocks to save on memory.
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\UpdateEngine
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_UpdateEngine
{
    private static $report = null;

    /**
     *  Used to report on all log errors into the installer-txt.log
     *
     * @return string Writes the results of the update engine tables to the log
     */
    public static function logErrors()
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();

        if (!empty($s3Funcs->report['errsql'])) {
            DUPX_Log::info("--------------------------------------");
            DUPX_Log::info("DATA-REPLACE ERRORS (MySQL)");
            foreach ($s3Funcs->report['errsql'] as $error) {
                DUPX_Log::info($error);
            }
            DUPX_Log::info("");
        }
        if (!empty($s3Funcs->report['errser'])) {
            DUPX_Log::info("--------------------------------------");
            DUPX_Log::info("DATA-REPLACE ERRORS (Serialization):");
            foreach ($s3Funcs->report['errser'] as $error) {
                DUPX_Log::info($error);
            }
            DUPX_Log::info("");
        }
        if (!empty($s3Funcs->report['errkey'])) {
            DUPX_Log::info("--------------------------------------");
            DUPX_Log::info("DATA-REPLACE ERRORS (Key):");
            DUPX_Log::info('Use SQL: SELECT @row := @row + 1 as row, t.* FROM some_table t, (SELECT @row := 0) r');
            foreach ($s3Funcs->report['errkey'] as $error) {
                DUPX_Log::info($error);
            }
        }
    }

    /**
     *  Used to report on all log stats into the installer-txt.log
     *
     * @return string Writes the results of the update engine tables to the log
     */
    public static function logStats()
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();
        DUPX_Log::resetIndent();

        if (!empty($s3Funcs->report) && is_array($s3Funcs->report)) {
            $stats = "--------------------------------------\n";
            $stats .= sprintf("SCANNED:\tTables:%d \t|\t Rows:%d \t|\t Cells:%d \n", $s3Funcs->report['scan_tables'], $s3Funcs->report['scan_rows'], $s3Funcs->report['scan_cells']);
            $stats .= sprintf("UPDATED:\tTables:%d \t|\t Rows:%d \t|\t Cells:%d \n", $s3Funcs->report['updt_tables'], $s3Funcs->report['updt_rows'], $s3Funcs->report['updt_cells']);
            $stats .= sprintf("ERRORS:\t\t%d \nRUNTIME:\t%f sec", $s3Funcs->report['err_all'], $s3Funcs->report['time']);
            DUPX_Log::info($stats);
        }
    }

    /**
     * Returns only the text type columns of a table ignoring all numeric types
     *
     * @param obj $conn A valid database link handle
     * @param string $table A valid table name
     *
     * @return array All the column names of a table
     */
    private static function getTextColumns($table)
    {
        $dbh = DUPX_S3_Funcs::getInstance()->getDbConnection();

        $type_where = '';
        $type_where .= "type LIKE '%char%' OR ";
        $type_where .= "type LIKE '%text' OR ";
        $type_where .= "type LIKE '%blob' ";
        $sql        = "SHOW COLUMNS FROM `".mysqli_real_escape_string($dbh, $table)."` WHERE {$type_where}";

        $result = DUPX_DB::mysqli_query($dbh, $sql, __FILE__, __LINE__);
        if (!$result) {
            return null;
        }

        $fields = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $fields[] = $row['Field'];
            }
        }

        //Return Primary which is needed for index lookup.  LIKE '%PRIMARY%' is less accurate with lookup
        //$result = mysqli_query($dbh, "SHOW INDEX FROM `{$table}` WHERE KEY_NAME LIKE '%PRIMARY%'");
        $result = mysqli_query($dbh, "SHOW INDEX FROM `".mysqli_real_escape_string($dbh, $table)."`");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $fields[] = $row['Column_name'];
            }
        }

        return (count($fields) > 0) ? array_unique($fields) : null;
    }

    public static function set_sql_column_safe(&$str)
    {
        $str = "`$str`";
    }

    public static function loadInit()
    {
        DUPX_Log::info('ENGINE LOAD INIT', DUPX_Log::LV_DEBUG);
        $s3Funcs                          = DUPX_S3_Funcs::getInstance();
        $s3Funcs->report['profile_start'] = DUPX_U::getMicrotime();

        $dbh = $s3Funcs->getDbConnection();
        @mysqli_autocommit($dbh, false);
    }

    /**
     * Begins the processing for replace logic
     *
     * @param array $tables The tables we want to look at
     *
     * @return array Collection of information gathered during the run.
     */
    public static function load($tables = array())
    {
        self::loadInit();

        if (is_array($tables)) {
            foreach ($tables as $table) {
                self::evaluateTalbe($table);
            }
        }

        self::commitAndSave();
        return self::loadEnd();
    }

    public static function commitAndSave()
    {
        DUPX_Log::info('ENGINE COMMIT AND SAVE', DUPX_Log::LV_DEBUG);

        $dbh = DUPX_S3_Funcs::getInstance()->getDbConnection();

        @mysqli_commit($dbh);
        @mysqli_autocommit($dbh, true);

        DUPX_NOTICE_MANAGER::getInstance()->saveNotices();
    }

    public static function loadEnd()
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();
        DUPX_Log::info('ENGINE LOAD END', DUPX_Log::LV_DEBUG);

        $s3Funcs->report['profile_end'] = DUPX_U::getMicrotime();
        $s3Funcs->report['time']        = DUPX_U::elapsedTime($s3Funcs->report['profile_end'], $s3Funcs->report['profile_start']);
        $s3Funcs->report['errsql_sum']  = empty($s3Funcs->report['errsql']) ? 0 : count($s3Funcs->report['errsql']);
        $s3Funcs->report['errser_sum']  = empty($s3Funcs->report['errser']) ? 0 : count($s3Funcs->report['errser']);
        $s3Funcs->report['errkey_sum']  = empty($s3Funcs->report['errkey']) ? 0 : count($s3Funcs->report['errkey']);
        $s3Funcs->report['err_all']     = $s3Funcs->report['errsql_sum'] + $s3Funcs->report['errser_sum'] + $s3Funcs->report['errkey_sum'];

        return $s3Funcs->report;
    }

    public static function getTableRowParamsDefault($table = '')
    {
        return array(
            'table' => $table,
            'updated' => false,
            'row_count' => 0,
            'columns' => array(),
            'colList' => '*',
            'colMsg' => 'every column',
            'columnsSRList' => array(),
            'pages' => 0,
            'page_size' => 0,
            'page' => 0,
            'current_row' => 0
        );
    }

    private static function getTableRowsParams($table)
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();
        $dbh     = $s3Funcs->getDbConnection();

        $rowsParams = self::getTableRowParamsDefault($table);

        // Count the number of rows we have in the table if large we'll split into blocks
        $rowsParams['row_count'] = mysqli_query($dbh, "SELECT COUNT(*) FROM `".mysqli_real_escape_string($dbh, $rowsParams['table'])."`");
        if (!$rowsParams['row_count']) {
            return null;
        }
        $rows_result             = mysqli_fetch_array($rowsParams['row_count']);
        @mysqli_free_result($rowsParams['row_count']);
        $rowsParams['row_count'] = $rows_result[0];
        if ($rowsParams['row_count'] == 0) {
            $rowsParams['colMsg'] = 'no columns  ';
            self::logEvaluateTable($rowsParams);
            return null;
        }

        // Get a list of columns in this table
        $sql    = 'DESCRIBE '.mysqli_real_escape_string($dbh, $rowsParams['table']);
        $fields = mysqli_query($dbh, $sql);
        if (!$fields) {
            return null;
        }
        while ($column = mysqli_fetch_array($fields)) {
            $rowsParams['columns'][$column['Field']] = $column['Key'] == 'PRI' ? true : false;
        }

        $rowsParams['page_size'] = $GLOBALS['DATABASE_PAGE_SIZE'];
        $rowsParams['pages']     = ceil($rowsParams['row_count'] / $rowsParams['page_size']);

        // Grab the columns of the table.  Only grab text based columns because
        // they are the only data types that should allow any type of search/replace logic
        if (!$s3Funcs->getPost('fullsearch')) {
            $rowsParams['colList'] = self::getTextColumns($rowsParams['table']);
            if ($rowsParams['colList'] != null && is_array($rowsParams['colList'])) {
                array_walk($rowsParams['colList'], array(__CLASS__, 'set_sql_column_safe'));
                $rowsParams['colList'] = implode(',', $rowsParams['colList']);
            }
            $rowsParams['colMsg'] = (empty($rowsParams['colList'])) ? 'every column' : 'text columns';
        }

        if (empty($rowsParams['colList'])) {
            $rowsParams['colMsg'] = 'no columns  ';
        }

        self::logEvaluateTable($rowsParams);

        if (empty($rowsParams['colList'])) {
            return null;
        } else {
            // PREPARE SEARCH AN REPLACE LISF FOR TABLES
            $rowsParams['columnsSRList'] = self::getColumnsSearchReplaceList($rowsParams['table'], $rowsParams['columns']);
            return $rowsParams;
        }
    }

    public static function logEvaluateTable($rowsParams)
    {
        DUPX_Log::resetIndent();
        $log = "\n".'EVALUATE TABLE: '.str_pad(DUPX_Log::varToString($rowsParams['table']), 50, '_', STR_PAD_RIGHT);
        $log .= '[ROWS:'.str_pad($rowsParams['row_count'], 6, " ", STR_PAD_LEFT).']';
        $log .= '[PG:'.str_pad($rowsParams['pages'], 4, " ", STR_PAD_LEFT).']';
        $log .= '[SCAN:'.$rowsParams['colMsg'].']';
        if (DUPX_Log::isLevel(DUPX_Log::LV_DETAILED)) {
            $log .= '[COLS: '.$rowsParams['colList'].']';
        }
        DUPX_Log::info($log);
        DUPX_Log::incIndent();
    }

    public static function evaluateTalbe($table)
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();

        // init table params if isn't inizialized
        if (!self::initTableParams($table)) {
            return false;
        }

        //Paged Records
        $pages = $s3Funcs->cTableParams['pages'];
        for ($page = 0; $page < $pages; $page ++) {
            self::evaluateTableRows($table, $page);
        }

        if ($s3Funcs->cTableParams['updated']) {
            $s3Funcs->report['updt_tables'] ++;
        }
    }

    public static function evaluateTableRows($table, $page)
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();

        // init table params if isn't inizialized
        if (!self::initTableParams($table)) {
            return false;
        }

        $s3Funcs->cTableParams['page'] = $page;
        if ($s3Funcs->cTableParams['page'] >= $s3Funcs->cTableParams['pages']) {
            DUPX_Log::info('ENGINE EXIT PAGE:'.DUPX_Log::varToString($table).' PAGES:'.$s3Funcs->cTableParams['pages'], DUPX_Log::LV_DEBUG);
            return false;
        }

        self::evaluatePagedRows($s3Funcs->cTableParams);
    }

    public static function initTableParams($table)
    {
        $s3Funcs = DUPX_S3_Funcs::getInstance();
        if (is_null($s3Funcs->cTableParams) || $s3Funcs->cTableParams['table'] !== $table) {
            DUPX_Log::info('ENGINE INIT TABLE PARAMS '.DUPX_Log::varToString($table), DUPX_Log::LV_DETAILED);
            $s3Funcs->report['scan_tables'] ++;

            if (($s3Funcs->cTableParams = self::getTableRowsParams($table)) === null) {
                DUPX_Log::info('ENGINE TABLE PARAMS EMPTY', DUPX_Log::LV_DEBUG);
                return false;
            }
        }

        return true;
    }

    /**
     * evaluate rows with pagination
     *
     * @param array $rowsParams
     *
     * @return boolean // if true table is modified and updated
     */
    private static function evaluatePagedRows(&$rowsParams)
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        $s3Funcs  = DUPX_S3_Funcs::getInstance();
        $dbh      = $s3Funcs->getDbConnection();
        $start    = $rowsParams['page'] * $rowsParams['page_size'];
        $end      = $start + $rowsParams['page_size'] - 1;

        $sql  = sprintf("SELECT {$rowsParams['colList']} FROM `%s` LIMIT %d, %d", $rowsParams['table'], $start, $rowsParams['page_size']);
        $data = DUPX_DB::mysqli_query($dbh, $sql, __FILE__, __LINE__);

        $scan_count = min($rowsParams['row_count'], $end);
        if (DUPX_Log::isLevel(DUPX_Log::LV_DETAILED)) {
            DUPX_Log::info('ENGINE EV TABLE '.str_pad(DUPX_Log::varToString($rowsParams['table']), 50, '_', STR_PAD_RIGHT).
                '[PAGE:'.str_pad($rowsParams['page'], 4, " ", STR_PAD_LEFT).']'.
                '[START:'.str_pad($start, 6, " ", STR_PAD_LEFT).']'.
                '[OF:'.str_pad($scan_count, 6, " ", STR_PAD_LEFT).']', DUPX_Log::LV_DETAILED);
        }

        if (!$data) {
            $errMsg                      = mysqli_error($dbh);
            $s3Funcs->report['errsql'][] = $errMsg;
            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'DATA-REPLACE ERRORS: MySQL',
                'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg' => $errMsg,
                'sections' => 'search_replace'
            ));
        }

        //Loops every row
        while ($row = mysqli_fetch_assoc($data)) {
            self::evaluateRow($rowsParams, $row);
        }

        //DUPX_U::fcgiFlush();
        @mysqli_free_result($data);

        return $rowsParams['updated'];
    }

    /**
     * evaluate single row columns
     *
     * @param array $rowsParams
     * @param array $row
     *
     * @return boolean true if row is modified and updated
     */
    private static function evaluateRow(&$rowsParams, $row)
    {
        $nManager             = DUPX_NOTICE_MANAGER::getInstance();
        $s3Funcs              = DUPX_S3_Funcs::getInstance();
        $dbh                  = $s3Funcs->getDbConnection();
        $maxSerializeLenCheck = $s3Funcs->getPost('maxSerializeStrlen');

        $s3Funcs->report['scan_rows'] ++;
        $rowsParams['current_row'] ++;

        $upd_col    = array();
        $upd_sql    = array();
        $where_sql  = array();
        $upd        = false;
        $serial_err = 0;
        $is_unkeyed = !in_array(true, $rowsParams['columns']);

        $rowErrors = array();


        //Loops every cell
        foreach ($rowsParams['columns'] as $column => $primary_key) {
            $s3Funcs->report['scan_cells'] ++;
            if (!isset($row[$column])) {
                continue;
            }

            $safe_column     = '`'.mysqli_real_escape_string($dbh, $column).'`';
            $edited_data     = $data_to_fix     = $row[$column];
            $base64converted = false;
            $txt_found       = false;

            //Unkeyed table code
            //Added this here to add all columns to $where_sql
            //The if statement with $txt_found would skip additional columns -TG
            if ($is_unkeyed && !empty($data_to_fix)) {
                $where_sql[] = $safe_column.' = "'.mysqli_real_escape_string($dbh, $data_to_fix).'"';
            }

            //Only replacing string values
            if (!empty($row[$column]) && !is_numeric($row[$column]) && $primary_key != 1) {
                // get search and reaplace list for column
                $tColList        = &$rowsParams['columnsSRList'][$column]['list'];
                $tColSearchList  = &$rowsParams['columnsSRList'][$column]['sList'];
                $tColreplaceList = &$rowsParams['columnsSRList'][$column]['rList'];
                $tColExactMatch  = $rowsParams['columnsSRList'][$column]['exactMatch'];

                // skip empty search col
                if (empty($tColSearchList)) {
                    continue;
                }

                // Search strings in data
                foreach ($tColList as $item) {
                    if (strpos($edited_data, $item['search']) !== false) {
                        $txt_found = true;
                        break;
                    }
                }

                if (!$txt_found) {
                    //if not found decetc Base 64
                    if (($decoded = DUPX_U::is_base64($row[$column])) !== false) {
                        $edited_data     = $decoded;
                        $base64converted = true;

                        // Search strings in data decoded
                        foreach ($tColList as $item) {
                            if (strpos($edited_data, $item['search']) !== false) {
                                $txt_found = true;
                                break;
                            }
                        }
                    }

                    //Skip table cell if match not found
                    if (!$txt_found) {
                        continue;
                    }
                }

                // 0 no limit
                if ($maxSerializeLenCheck > 0 && self::is_serialized_string($edited_data) && strlen($edited_data) > $maxSerializeLenCheck) {
                    $serial_err ++;
                    $trimLen            = DUPX_Log::isLevel(DUPX_Log::LV_HARD_DEBUG) ? 10000 : 200;
                    $rowErrors[$column] = 'ENGINE: serialize data too big to convert; data len:'.strlen($edited_data).' Max size:'.$maxSerializeLenCheck;
                    $rowErrors[$column] .= "\n\tDATA: ".mb_strimwidth($edited_data, 0, $trimLen, ' [...]');
                } else {
                    //Replace logic - level 1: simple check on any string or serlized strings
                    if ($tColExactMatch) {
                        // if is exact match search and replace the itentical string
                        if (($rIndex = array_search($edited_data, $tColSearchList)) !== false) {
                            DUPX_Log::info("ColExactMatch ".$column.' search:'.$edited_data.' replace:'.$tColreplaceList[$rIndex].' index:'.$rIndex, DUPX_Log::LV_DEBUG);
                            $edited_data = $tColreplaceList[$rIndex];
                        }
                    } else {
                        // search if column contain search list
                        $edited_data = self::searchAndReplaceItems($tColSearchList, $tColreplaceList, $edited_data);

                        //Replace logic - level 2: repair serialized strings that have become broken
                        // check value without unserialize it
                        if (self::is_serialized_string($edited_data)) {
                            $serial_check = self::fixSerialString($edited_data);
                            if ($serial_check['fixed']) {
                                $edited_data = $serial_check['data'];
                            } elseif ($serial_check['tried'] && !$serial_check['fixed']) {
                                $serial_err ++;
                                $trimLen            = DUPX_Log::isLevel(DUPX_Log::LV_HARD_DEBUG) ? 10000 : 200;
                                $rowErrors[$column] = 'ENGINE: serialize data serial check error';
                                $rowErrors[$column] .= "\n\tDATA: ".mb_strimwidth($edited_data, 0, $trimLen, ' [...]');
                            }
                        }
                    }
                }
            }

            //Change was made
            if ($serial_err > 0 || $edited_data != $data_to_fix) {
                $s3Funcs->report['updt_cells'] ++;
                //Base 64 encode
                if ($base64converted) {
                    $edited_data = base64_encode($edited_data);
                }
                $upd_col[] = $safe_column;
                $upd_sql[] = $safe_column.' = "'.mysqli_real_escape_string($dbh, $edited_data).'"';
                $upd       = true;
            }

            if ($primary_key) {
                $where_sql[] = $safe_column.' = "'.mysqli_real_escape_string($dbh, $data_to_fix).'"';
            }
        }

        //PERFORM ROW UPDATE
        if ($upd && !empty($where_sql)) {
            $sql    = "UPDATE `{$rowsParams['table']}` SET ".implode(', ', $upd_sql).' WHERE '.implode(' AND ', array_filter($where_sql));
            $result = DUPX_DB::mysqli_query($dbh, $sql, __FILE__, __LINE__);

            if ($result) {
                foreach ($rowErrors as $errCol => $msgCol) {
                    $longMsg                     = $msgCol."\n\tTABLE:".$rowsParams['table'].' COLUMN: '.$errCol.' WHERE: '.implode(' AND ', array_filter($where_sql));
                    $s3Funcs->report['errser'][] = $longMsg;

                    $nManager->addFinalReportNotice(array(
                        'shortMsg' => 'DATA-REPLACE ERROR: Serialization',
                        'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg' => $longMsg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                        'sections' => 'search_replace'
                    ));
                }
                $s3Funcs->report['updt_rows'] ++;
                $rowsParams['updated'] = true;
            } else {
                $errMsg                      = mysqli_error($dbh)."\n\tTABLE:".$rowsParams['table'].' COLUMN: '.$errCol.' WHERE: '.implode(' AND ', array_filter($where_sql));
                $s3Funcs->report['errsql'][] = ($GLOBALS['LOGGING'] == 1) ? 'DB ERROR: '.$errMsg : 'DB ERROR: '.$errMsg."\nSQL: [{$sql}]\n";
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'DATA-REPLACE ERRORS: MySQL',
                    'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg' => $errMsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                    'sections' => 'search_replace'
                ));
            }
        } elseif ($upd) {
            $errMsg                      = sprintf("Row [%s] on Table [%s] requires a manual update.", $rowsParams['current_row'], $rowsParams['table']);
            $s3Funcs->report['errkey'][] = $errMsg;

            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'DATA-REPLACE ERROR: Key',
                'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg' => $errMsg,
                'sections' => 'search_replace'
            ));
        }

        return $rowsParams['updated'];
    }

    private static function getColumnsSearchReplaceList($table, $columns)
    {
        // PREPARE SEARCH AN REPLACE LISF FOR TABLES
        $srManager   = DUPX_S_R_MANAGER::getInstance();
        $searchList  = array();
        $replaceList = array();
        $list        = $srManager->getSearchReplaceList($table);
        foreach ($list as $item) {
            $searchList[]  = $item['search'];
            $replaceList[] = $item['replace'];
        }

        $columnsSRList = array();
        foreach ($columns as $column => $primary_key) {
            if (($cScope = self::getSearchReplaceCustomScope($table, $column)) === false) {
                // if don't have custom scope get normal search and reaplce table list
                $columnsSRList[$column] = array(
                    'list' => &$list,
                    'sList' => &$searchList,
                    'rList' => &$replaceList,
                    'exactMatch' => false
                );
            } else {
                // if column have custom scope overvrite default table search/replace list
                $columnsSRList[$column] = array(
                    'list' => $srManager->getSearchReplaceList($cScope, true, false),
                    'sList' => array(),
                    'rList' => array(),
                    'exactMatch' => self::isExactMatch($table, $column)
                );
                foreach ($columnsSRList[$column]['list'] as $item) {
                    $columnsSRList[$column]['sList'][] = $item['search'];
                    $columnsSRList[$column]['rList'][] = $item['replace'];
                }
            }
        }

        return $columnsSRList;
    }

    /**
     * searches and replaces strings without deserializing
     * recursion for arrays
     *
     * @param array $search
     * @param array $replace
     * @param mixed $data
     *
     * @return mixed
     */
    public static function searchAndReplaceItems($search, $replace, $data)
    {

        if (empty($data) || is_numeric($data) || is_bool($data) || is_callable($data)) {

            /* do nothing */
        } else if (is_string($data)) {

            //  Multiple replace string. If the string is serialized will fixed with fixSerialString
            $data = str_replace($search, $replace, $data);
        } else if (is_array($data)) {

            $_tmp = array();
            foreach ($data as $key => $value) {

                // prevent recursion overhead
                if (empty($value) || is_numeric($value) || is_bool($value) || is_callable($value) || is_object($data)) {

                    $_tmp[$key] = $value;
                } else {

                    $_tmp[$key] = self::searchAndReplaceItems($search, $replace, $value, false);
                }
            }

            $data = $_tmp;
            unset($_tmp);
        } elseif (is_object($data)) {
            // it can never be an object type
            DUPX_Log::info("OBJECT DATA IMPOSSIBLE\n");
        }

        return $data;
    }

    /**
     * FROM WORDPRESS
     * Check value to find if it was serialized.
     *
     * If $data is not an string, then returned value will always be false.
     * Serialized data is always a string.
     *
     * @since 2.0.5
     *
     * @param string $data   Value to check to see if was serialized.
     * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
     * @return bool False if not serialized and true if it was.
     */
    public static function is_serialized_string($data, $strict = true)
    {
        // if it isn't a string, it isn't serialized.
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');
            // Either ; or } must exist.
            if (false === $semicolon && false === $brace) {
                return false;
            }
            // But neither must be in the first X characters.
            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }
            if (false !== $brace && $brace < 4) {
                return false;
            }
        }
        $token = $data[0];
        switch ($token) {
            case 's' :
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
            // or else fall through
            case 'a' :
            case 'O' :
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b' :
            case 'i' :
            case 'd' :
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }
        return false;
    }

    /**
     * Test if a string in properly serialized
     *
     * @param string $data Any string type
     *
     * @return bool Is the string a serialized string
     */
    public static function unserializeTest($data)
    {
        if (!is_string($data)) {
            return false;
        } else if ($data === 'b:0;') {
            return true;
        } else {
            try {
                DUPX_Handler::setMode(DUPX_Handler::MODE_OFF);
                $unserialize_ret = @unserialize($data);
                DUPX_Handler::setMode();
                return ($unserialize_ret !== false);
            } catch (Exception $e) {
                DUPX_Log::info("Unserialize exception: ".$e->getMessage());
                //DEBUG ONLY:
                DUPX_Log::info("Serialized data\n".$data, DUPX_Log::LV_DEBUG);
                return false;
            }
        }
    }
    /**
     * custom columns list
     * if the table / column pair exists in this array then the search scope will be overwritten with that contained in the array
     *
     * @var array
     */
    private static $customScopes = array(
        'signups' => array(
            'domain' => array(
                'scope' => 'domain_host',
                'exact' => true
            ),
            'path' => array(
                'scope' => 'domain_path',
                'exact' => true
            )
        ),
        'site' => array(
            'domain' => array(
                'scope' => 'domain_host',
                'exact' => true
            ),
            'path' => array(
                'scope' => 'domain_path',
                'exact' => true
            )
        )
    );

    /**
     *
     * @param string $table
     * @param string $column
     * @return boolean|string  false if custom scope not found or return custom scoper for table/column
     */
    private static function getSearchReplaceCustomScope($table, $column)
    {
        if (strpos($table, $GLOBALS['DUPX_AC']->wp_tableprefix) !== 0) {
            return false;
        }

        $table_key = substr($table, strlen($GLOBALS['DUPX_AC']->wp_tableprefix));

        if (!array_key_exists($table_key, self::$customScopes)) {
            return false;
        }

        if (!array_key_exists($column, self::$customScopes[$table_key])) {
            return false;
        }

        return self::$customScopes[$table_key][$column]['scope'];
    }

    /**
     *
     * @param string $table
     * @param string $column
     * @return boolean if true search a exact match in column if false search as LIKE
     */
    private static function isExactMatch($table, $column)
    {
        if (strpos($table, $GLOBALS['DUPX_AC']->wp_tableprefix) !== 0) {
            return false;
        }

        $table_key = substr($table, strlen($GLOBALS['DUPX_AC']->wp_tableprefix));

        if (!array_key_exists($table_key, self::$customScopes)) {
            return false;
        }

        if (!array_key_exists($column, self::$customScopes[$table_key])) {
            return false;
        }

        return self::$customScopes[$table_key][$column]['exact'];
    }

    /**
     *  Fixes the string length of a string object that has been serialized but the length is broken
     *
     * @param string $data The string object to recalculate the size on.
     *
     * @return string  A serialized string that fixes and string length types
     */
    public static function fixSerialString($data)
    {
        $result = array('data' => $data, 'fixed' => false, 'tried' => false);

        // check if serialized string must be fixed
        if (!self::unserializeTest($data)) {

            $serialized_fixed = self::recursiveFixSerialString($data);

            if (self::unserializeTest($serialized_fixed)) {

                $result['data'] = $serialized_fixed;

                $result['fixed'] = true;
            }

            $result['tried'] = true;
        }

        return $result;
    }

    /**
     *  Fixes the string length of a string object that has been serialized but the length is broken
     *  Work on nested serialized string recursively.
     *
     *  @param string $data	The string ojbect to recalculate the size on.
     *
     *  @return string  A serialized string that fixes and string length types
     */
    public static function recursiveFixSerialString($data)
    {

        if (!self::is_serialized_string($data)) {
            return $data;
        }

        $result = '';

        $matches = null;

        $openLevel     = 0;
        $openContent   = '';
        $openContentL2 = '';

        // parse every char
        for ($i = 0; $i < strlen($data); $i++) {

            $cChar = $data[$i];

            $addChar = true;

            if ($cChar == 's') {

                // test if is a open string
                if (preg_match('/^(s:\d+:")/', substr($data, $i), $matches)) {

                    $addChar = false;

                    $openLevel ++;

                    $i += strlen($matches[0]) - 1;
                }
            } else if ($openLevel > 0 && $cChar == '"') {

                // test if is a close string
                if (preg_match('/^";(?:}|a:|s:|S:|b:|d:|i:|o:|O:|C:|r:|R:|N;)/', substr($data, $i))) {

                    $addChar = false;

                    switch ($openLevel) {
                        case 1:
                            // level 1
                            // flush string content
                            $result .= 's:'.strlen($openContent).':"'.$openContent.'";';

                            $openContent = '';

                            break;
                        case 2;
                            // level 2
                            // fix serial string level2
                            $sublevelstr = self::recursiveFixSerialString($openContentL2);

                            // flush content on level 1
                            $openContent .= 's:'.strlen($sublevelstr).':"'.$sublevelstr.'";';

                            $openContentL2 = '';

                            break;
                        default:
                            // level > 2
                            // keep writing at level 2; it will be corrected with recursion
                            break;
                    }

                    $openLevel --;

                    $closeString = '";';

                    $i += strlen($closeString) - 1;
                }
            }


            if ($addChar) {

                switch ($openLevel) {
                    case 0:
                        // level 0
                        // add char on result
                        $result .= $cChar;

                        break;
                    case 1:
                        // level 1
                        // add char on content level1
                        $openContent .= $cChar;

                        break;
                    default:
                        // level > 1
                        // add char on content level2
                        $openContentL2 .= $cChar;

                        break;
                }
            }
        }

        return $result;
    }
}