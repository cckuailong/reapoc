<?php

namespace WP_STATISTICS;

class DB
{
    /**
     * List Of wp-statistics Mysql Table
     *
     * @var array
     */
    public static $db_table = array(
        'useronline',
        'visit',
        'visitor',
        'exclusions',
        'pages',
        'search',
        'historical',
        'visitor_relationships'
    );

    /**
     * WP-Statistics Table name Structure in Database
     *
     * @var string
     */
    public static $tbl_name = '[prefix]statistics_[name]';

    /**
     * Get WordPress Table Prefix
     */
    public static function prefix()
    {
        global $wpdb;
        return $wpdb->prefix;
    }

    /**
     * Get WordPress Table Collate
     *
     * @return mixed
     */
    public static function charset_collate()
    {
        global $wpdb;
        return $wpdb->get_charset_collate();
    }

    /**
     * Get WP-Statistics Table name
     *
     * @param $tbl
     * @return mixed
     */
    public static function getTableName($tbl)
    {
        return str_ireplace(array("[prefix]", "[name]"), array(self::prefix(), $tbl), self::$tbl_name);
    }

    /**
     * Check Exist Table in Database
     *
     * @param $tbl_name
     * @return bool
     */
    public static function ExistTable($tbl_name)
    {
        global $wpdb;
        return ($wpdb->get_var("SHOW TABLES LIKE '$tbl_name'") == $tbl_name);
    }

    /**
     * Table List WP-Statistics
     *
     * @param string $export
     * @param array $except
     * @return array|null|string
     */
    public static function table($export = 'all', $except = array())
    {

        # Create Empty Object
        $list = array();

        # Convert except String to array
        if (is_string($except)) {
            $except = array($except);
        }

        # Check Except List
        $mysql_list_table = array_diff(self::$db_table, $except);

        # Get List
        foreach ($mysql_list_table as $tbl) {

            # WP-Statistics table name
            $table_name = self::getTableName($tbl);

            if ($export == "all") {
                if (self::ExistTable($table_name)) {
                    $list[$tbl] = $table_name;
                }
            } else {
                $list[$tbl] = $table_name;
            }
        }

        # Export Data
        return ($export == 'all' ? $list : (array_key_exists($export, $list) ? $list[$export] : null));
    }

    /**
     * Delete All record From Table
     *
     * @param bool $table_name
     * @return string
     */
    public static function EmptyTable($table_name = false)
    {
        global $wpdb;

        if ($table_name) {
            // TRUNCATE TABLE
            $result = $wpdb->query('TRUNCATE TABLE ' . $table_name);

            // Check Result
            if ($result) {

                // add action
                do_action('wp_statistics_truncate_table', str_ireplace(self::prefix() . 'statistics_', "", $table_name));

                // Return
                return sprintf(__('%s table data deleted successfully.', 'wp-statistics'), '<code>' . $table_name . '</code>');
            }
        }

        return sprintf(__('Error, %s not emptied!', 'wp-statistics'), $table_name);
    }

    /**
     * Modify For IGNORE insert Query
     *
     * @hook add_filter('query', function_name, 10);
     * @param $query
     * @return string
     */
    public static function insert_ignore($query)
    {
        $count = 0;
        $query = preg_replace('/^(INSERT INTO)/i', 'INSERT IGNORE INTO', $query, 1, $count);
        return $query;
    }

    /**
     * Get Number of Table Rows
     */
    public static function getTableRows()
    {
        global $wpdb;
        $result = array();
        foreach (self::table('all') as $tbl_key => $tbl_name) {
            $result[$tbl_name] = $wpdb->get_var("SELECT COUNT(*) FROM `$tbl_name`");
        }

        return $result;
    }

    /**
     * Get Table information
     *
     * @param $table_name
     * @return mixed
     */
    public static function getTableInformation($table_name)
    {
        global $wpdb;
        return $wpdb->get_row("show table status like '$table_name';", ARRAY_A);
    }

    /**
     * Optimize MySQL Query
     *
     * @param $table_name
     */
    public static function optimizeTable($table_name)
    {
        global $wpdb;
        $wpdb->query("OPTIMIZE TABLE `{$table_name}`");
    }

    /**
     * Repair MySQL Table
     *
     * @param $table_name
     */
    public static function repairTable($table_name)
    {
        global $wpdb;
        $wpdb->query("REPAIR TABLE `{$table_name}`");
    }
}