<?php

namespace WP_STATISTICS;

/**
 * WordPress Statistics
 *
 * ## EXAMPLES
 *
 *      # show summary of statistics
 *      $ wp statistics summary
 *
 *      # get list of users online in WordPress
 *      $ wp statistics online
 *
 *      # show list of last visitors
 *      $ wp statistics visitors
 *
 * @package wp-cli
 */
class WP_STATISTICS_CLI extends \WP_CLI_Command
{

    /**
     * Show Summary of statistics.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *      # show summary of statistics
     *      $ wp statistics summary
     *
     * @alias overview
     * @throws \Exception
     */
    function summary($args, $assoc_args)
    {

        // Check Enable Command
        if (Option::get('wp_cli_summary') == false) {
            \WP_CLI::error("The `summary` command is not active.");
        }

        // Prepare Item
        \WP_CLI::line("Users Online: " . number_format(wp_statistics_useronline()));
        $items = array();
        foreach (array("Today", "Yesterday", "Week", "Month", "Year", "Total") as $time) {
            $item = array(
                'Time' => $time
            );
            foreach (array("Visitors", "Visits") as $state) {
                $item[$state] = number_format((strtolower($state) == "visitors" ? wp_statistics_visitor(strtolower($time), null, true) : wp_statistics_visit(strtolower($time))));
            }
            $items[] = $item;
        }

        \WP_CLI\Utils\format_items($assoc_args['format'], $items, array('Time', 'Visitors', 'Visits'));
    }

    /**
     * Show list of users online.
     *
     * ## OPTIONS
     *
     * [--number=<number>]
     * : Number of return user.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *      # show list of users online
     *      $ wp statistics online
     *
     *      # show list of five users online
     *      $ wp statistics online --number=5
     *
     * @throws \Exception
     */
    public function online($args, $assoc_args)
    {

        // Check Enable Command
        if (Option::get('wp_cli_user_online') == false) {
            \WP_CLI::error("The `online` command is not active.");
        }

        // Get Number Of result
        $number = \WP_CLI\Utils\get_flag_value($assoc_args, 'number', 15);

        // Get List Of Users Online
        $lists = UserOnline::get(array('per_page' => $number));
        if (count($lists) < 1) {
            \WP_CLI::error("There are no users online.");
        }

        // Set Column
        $column = array('IP', 'Browser', 'Online For', 'Referrer', 'Page', 'User ID');
        if (GeoIP::active() === true) {
            $column[] = 'Country';
        }

        // Show List
        $items = array();
        foreach ($lists as $row) {
            $item = array(
                'IP'         => (isset($row['hash_ip']) ? $row['hash_ip'] : $row['ip']['value']),
                'Browser'    => $row['browser']['name'],
                'Online For' => $row['online_for'],
                'Referrer'   => wp_strip_all_tags($row['referred']),
                'Page'       => $row['page']['title'],
                'User ID'    => ((isset($row['user']) and isset($row['user']['ID']) and $row['user']['ID'] > 0) ? $row['user']['ID'] : '-')
            );
            if (GeoIP::active() === true) {
                $item['Country'] = $row['country']['name'];
            }
            $items[] = $item;
        }

        \WP_CLI\Utils\format_items($assoc_args['format'], $items, $column);
    }

    /**
     * Show list of visitors.
     *
     * ## OPTIONS
     *
     * [--number=<number>]
     * : Number of return user.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *      # show list of visitors
     *      $ wp statistics visitors
     *
     *      # show list of last ten visitors
     *      $ wp statistics online --number=10
     *
     * @alias visitor
     * @throws \Exception
     */
    public function visitors($args, $assoc_args)
    {

        // Check Enable Command
        if (Option::get('wp_cli_visitors') == false) {
            \WP_CLI::error("The `visitors` command is not active.");
        }

        // Get Number Of result
        $number = \WP_CLI\Utils\get_flag_value($assoc_args, 'number', 15);

        // Get List Of Users Online
        $lists = Visitor::get(array('per_page' => $number));
        if (count($lists) < 1) {
            \WP_CLI::error("There are no visitors.");
        }

        // Set Column
        $column = array('IP', 'Date', 'Browser', 'Referrer', 'Platform', 'User ID');
        if (GeoIP::active() === true) {
            $column[] = 'Country';
        }

        // Show List
        $items = array();
        foreach ($lists as $row) {
            $item = array(
                'IP'       => (isset($row['hash_ip']) ? $row['hash_ip'] : $row['ip']['value']),
                'Date'     => $row['date'],
                'Browser'  => $row['browser']['name'],
                'Referrer' => wp_strip_all_tags($row['referred']),
                'Platform' => $row['platform'],
                'User ID'  => ((isset($row['user']) and isset($row['user']['ID']) and $row['user']['ID'] > 0) ? $row['user']['ID'] : '-')
            );
            if (GeoIP::active() === true) {
                $item['Country'] = $row['country']['name'];
            }
            $items[] = $item;
        }

        \WP_CLI\Utils\format_items($assoc_args['format'], $items, $column);
    }

}

/**
 * Register Command
 */
\WP_CLI::add_command('statistics', '\\WP_STATISTICS\WP_STATISTICS_CLI');