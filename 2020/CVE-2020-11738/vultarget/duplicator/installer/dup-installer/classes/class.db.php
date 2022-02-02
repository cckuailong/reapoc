<?php
/**
 * Lightweight abstraction layer for common simple database routines
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\DB
 * @link http://www.php-fig.org/psr/psr-2/
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_DB
{

    /**
     * MySQL connection wrapper with support for port
     *
     * @param string    $host       The server host name
     * @param string    $username   The server DB user name
     * @param string    $password   The server DB password
     * @param string    $dbname     The server DB name
     *
     * @return database connection handle
     */
    public static function connect($host, $username, $password, $dbname = '')
    {
        $dbh = null;
        try {
            //sock connections
            if ('sock' === substr($host, -4)) {
                $url_parts = parse_url($host);
                $dbh       = @mysqli_connect('localhost', $username, $password, $dbname, null, $url_parts['path']);
            } else {
                if (strpos($host, ':') !== false) {
                    $port = parse_url($host, PHP_URL_PORT);
                    $host = parse_url($host, PHP_URL_HOST);
                }

                if (isset($port)) {
                    $dbh = @mysqli_connect($host, $username, $password, $dbname, $port);
                } else {
                    $dbh = @mysqli_connect($host, $username, $password, $dbname);
                }
            }
            if (!$dbh) {
                DUPX_Log::info('DATABASE CONNECTION ERROR: '.mysqli_connect_error().'[ERRNO:'.mysqli_connect_errno().']');
            } else if (method_exists($dbh, 'options')) {
                $dbh->options(MYSQLI_OPT_LOCAL_INFILE, false);
            }
        } catch (Exception $e) {
            DUPX_Log::info('DATABASE CONNECTION EXCEPTION ERROR: '.$e->getMessage());
        }
        return $dbh;
    }

    /**
     *  Count the tables in a given database
     *
     * @param obj    $dbh       A valid database link handle
     * @param string $dbname    Database to count tables in
     *
     * @return int  The number of tables in the database
     */
    public static function countTables($dbh, $dbname)
    {
        $res = mysqli_query($dbh, "SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '".mysqli_real_escape_string($dbh, $dbname)."' ");
        $row = mysqli_fetch_row($res);
        return is_null($row) ? 0 : $row[0];
    }

    /**
     * Returns the number of rows in a table
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $name	A valid table name
     */
    public static function countTableRows($dbh, $name)
    {
        $total = mysqli_query($dbh, "SELECT COUNT(*) FROM `".mysqli_real_escape_string($dbh, $name)."`");
        if ($total) {
            $total = @mysqli_fetch_array($total);
            return $total[0];
        } else {
            return 0;
        }
    }

    /**
     * Drops the table given
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $name	A valid table name to remove
     *
     * @return null
     */
    public static function dropTable($dbh, $name)
    {
        self::queryNoReturn($dbh, "DROP TABLE IF EXISTS $name");
    }

    /**
     * Validates if the $collations exist in the current database
     *
     * @param obj $dbh   A valid database link handle
     * @param array $collations An array of collation names to search on
     *
     * @return array	Returns the original $collations array with the original names and a found status
     * 				    $status[name], $status[found]
     */
    public static function getCollationStatus($dbh, $collations)
    {
        $localhost = array();
        $status    = array();

        $query  = "SHOW COLLATION";
        if ($result = $dbh->query($query)) {

            while ($row = $result->fetch_assoc()) {
                $localhost[] = $row["Collation"];
            }

            if (DUPX_U::isTraversable($collations)) {
            foreach ($collations as $key => $val) {
                $status[$key]['name']  = $val;
                $status[$key]['found'] = (in_array($val, $localhost)) ? 1 : 0;
            }
        }
        }
        $result->free();

        return $status;
    }

    /**
     * Returns the database names as an array
     *
     * @param obj $dbh			A valid database link handle
     * @param string $dbuser  	An optional dbuser name to search by
     *
     * @return array  A list of all database names
     */
    public static function getDatabases($dbh, $dbuser = '')
    {
        $sql   = strlen($dbuser) ? "SHOW DATABASES LIKE '%".mysqli_real_escape_string($dbh, $dbuser)."%'" : 'SHOW DATABASES';
        $query = @mysqli_query($dbh, $sql);
        if ($query) {
            while ($db = @mysqli_fetch_array($query)) {
                $all_dbs[] = $db[0];
            }
            if (isset($all_dbs) && is_array($all_dbs)) {
                return $all_dbs;
            }
        }
        return array();
    }

    /**
     * Returns the tables for a database as an array
     *
     * @param obj $dbh   A valid database link handle
     *
     * @return array  A list of all table names
     */
    public static function getTables($dbh)
    {
        $query = @mysqli_query($dbh, 'SHOW TABLES');
        if ($query) {
            while ($table = @mysqli_fetch_array($query)) {
                $all_tables[] = $table[0];
            }
            if (isset($all_tables) && is_array($all_tables)) {
                return $all_tables;
            }
        }
        return array();
    }

    /**
     * Get the requested MySQL system variable
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $name  The database variable name to lookup
     *
     * @return string the server variable to query for
     */
    public static function getVariable($dbh, $name)
    {
        $result = @mysqli_query($dbh, "SHOW VARIABLES LIKE '".mysqli_real_escape_string($dbh, $name)."'");
        $row    = @mysqli_fetch_array($result);
        @mysqli_free_result($result);
        return isset($row[1]) ? $row[1] : null;
    }

    /**
     * Gets the MySQL database version number
     *
     * @param obj    $dbh   A valid database link handle
     * @param bool   $full  True:  Gets the full version
     *                      False: Gets only the numeric portion i.e. 5.5.6 or 10.1.2 (for MariaDB)
     *
     * @return false|string 0 on failure, version number on success
     */
    public static function getVersion($dbh, $full = false)
    {
        if ($full) {
            $version = self::getVariable($dbh, 'version');
        } else {
            $version = preg_replace('/[^0-9.].*/', '', self::getVariable($dbh, 'version'));
        }

        //Fall-back for servers that have restricted SQL for SHOW statement
        //Note: For MariaDB this will report something like 5.5.5 when it is really 10.2.1.
        //This mainly is due to mysqli_get_server_info method which gets the version comment
        //and uses a regex vs getting just the int version of the value.  So while the former
        //code above is much more accurate it may fail in rare situations
        if (empty($version)) {
            $version = mysqli_get_server_info($dbh);
            $version = preg_replace('/[^0-9.].*/', '', $version);
        }

        $version = is_null($version) ? null : $version;
        return empty($version) ? 0 : $version;
    }

    /**
     * Returns a more detailed string about the msyql server version
     * For example on some systems the result is 5.5.5-10.1.21-MariaDB
     * this format is helpful for providing the user a full overview
     *
     * @param conn $dbh Database connection handle
     *
     * @return string The full details of mysql
     */
    public static function getInfo($dbh)
    {
        return mysqli_get_server_info($dbh);
    }

    /**
     * Determine if a MySQL database supports a particular feature
     *
     * @param conn $dbh Database connection handle
     * @param string $feature the feature to check for
     * @return bool
     */
    public static function hasAbility($dbh, $feature)
    {
        $version = self::getVersion($dbh);

        switch (strtolower($feature)) {
            case 'collation' :
            case 'group_concat' :
            case 'subqueries' :
                return version_compare($version, '4.1', '>=');
            case 'set_charset' :
                return version_compare($version, '5.0.7', '>=');
        }
        return false;
    }

    /**
     * Runs a query and returns the results as an array with the column names
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $sql   The sql to run
     *
     * @return array    The result of the query as an array with the column name as the key
     */
    public static function queryColumnToArray($dbh, $sql, $column_index = 0)
    {
        $result_array      = array();
        $full_result_array = self::queryToArray($dbh, $sql);

        for ($i = 0; $i < count($full_result_array); $i++) {
            $result_array[] = $full_result_array[$i][$column_index];
        }
        return $result_array;
    }

    /**
     * Runs a query with no result
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $sql   The sql to run
     *
     * @return array    The result of the query as an array
     */
    public static function queryToArray($dbh, $sql)
    {
        $result = array();

        DUPX_Log::info("calling mysqli query on $sql", DUPX_Log::LV_HARD_DEBUG);
        $query_result = mysqli_query($dbh, $sql);

        if ($query_result !== false) {
            if (mysqli_num_rows($query_result) > 0) {
                while ($row = mysqli_fetch_row($query_result)) {
                    $result[] = $row;
                }
            }
        } else {
            $error = mysqli_error($dbh);

            throw new Exception("Error executing query {$sql}.<br/>{$error}");
        }

        return $result;
    }

    /**
     * Runs a query with no result
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $sql   The sql to run
     *
     * @return null
     */
    public static function queryNoReturn($dbh, $sql)
    {
        $query_result = mysqli_query($dbh, $sql);

        if ($query_result === false) {
            $error = mysqli_error($dbh);

            throw new Exception("Error executing query {$sql}.<br/>{$error}");
        }
    }

    /**
     * Renames an existing table
     *
     * @param obj    $dbh                   A valid database link handle
     * @param string $existing_name         The current tables name
     * @param string $new_name              The new table name to replace the existing name
     * @param string $delete_if_conflict    Delete the table name if there is a conflict
     *
     * @return null
     */
    public static function renameTable($dbh, $existing_name, $new_name, $delete_if_conflict = false)
    {
        if ($delete_if_conflict) {
            if (self::tableExists($dbh, $new_name)) {
                self::dropTable($dbh, $new_name);
            }
        }

        self::queryNoReturn($dbh, "RENAME TABLE $existing_name TO $new_name");
    }

    /**
     * Sets the MySQL connection's character set.
     *
     * @param resource $dbh     The resource given by mysqli_connect
     * @param string   $charset The character set (optional)
     * @param string   $collate The collation (optional)
     */
    public static function setCharset($dbh, $charset = null, $collate = null)
    {
        $charset = (!isset($charset) ) ? $GLOBALS['DBCHARSET_DEFAULT'] : $charset;
        $collate = (!isset($collate) ) ? $GLOBALS['DBCOLLATE_DEFAULT'] : $collate;

        if (self::hasAbility($dbh, 'collation') && !empty($charset)) {
            if (function_exists('mysqli_set_charset') && self::hasAbility($dbh, 'set_charset')) {
                if (($result = mysqli_set_charset($dbh, mysqli_real_escape_string($dbh, $charset))) === false) {
                    $errMsg = mysqli_error($dbh);
                    DUPX_Log::info('DATABASE ERROR: mysqli_set_charset '.DUPX_Log::varToString($charset).' MSG: '.$errMsg);
                } else {
                    DUPX_Log::info('DATABASE: mysqli_set_charset '.DUPX_Log::varToString($charset), DUPX_Log::LV_DETAILED);
                }
                return $result;
            } else {
                $sql = " SET NAMES ".mysqli_real_escape_string($dbh, $charset);
                if (!empty($collate)) {
                    $sql .= " COLLATE ".mysqli_real_escape_string($dbh, $collate);
                }

                if (($result = mysqli_query($dbh, $sql)) === false) {
                    $errMsg = mysqli_error($dbh);
                    DUPX_Log::info('DATABASE SQL ERROR: '.DUPX_Log::varToString($sql).' MSG: '.$errMsg);
                } else {
                    DUPX_Log::info('DATABASE SQL: '.DUPX_Log::varToString($sql), DUPX_Log::LV_DETAILED);
                }

                return $result;
            }
        }
    }

    /**
     *  If cached_table_names is null re-query the database, otherwise use those for the list
     *
     * @param obj    $dbh           A valid database link handle
     * @param string $table_name    Name of table to check for
     *
     * @return bool  Does the table name exist in the database
     */
    public static function tableExists($dbh, $table_name, $cached_table_names = null)
    {
        if ($cached_table_names === null) {
            // RSR TODO: retrieve full list of tables
            $cached_table_names = self::queryColumnToArray($dbh, "SHOW TABLES");
        }
        return in_array($table_name, $cached_table_names);
    }

    /**
     * mysqli_query wrapper with logging
     *
     * @param mysqli $link
     * @param string $sql
     * @return type
     */
    public static function mysqli_query($link, $sql, $file = '', $line = '')
    {
        if (($result = mysqli_query($link, $sql)) === false) {
            DUPX_Log::info('DB QUERY [ERROR]['.$file.':'.$line.'] SQL: '.DUPX_Log::varToString($sql)."\n\t MSG: ".mysqli_error($link));
        } else {
            DUPX_Log::info('DB QUERY ['.$file.':'.$line.']: '.DUPX_Log::varToString($sql), DUPX_Log::LV_HARD_DEBUG);
        }

        return $result;
    }
}