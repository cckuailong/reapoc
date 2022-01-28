<?php

namespace WP_STATISTICS;

class optimization_page
{

    public function __construct()
    {

        // Add Notice Save
        add_action('admin_notices', array($this, 'save'));

        // Check Access Level
        if (Menus::in_page('optimization') and !User::Access('manage')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Optimize and Repair Database MySQL
        add_action('admin_notices', array($this, 'optimize_table'));
    }

    /**
     * This function displays the HTML for the settings page.
     */
    public static function view()
    {

        // Add Class inf
        $args['class'] = 'wp-statistics-settings';

        // Get List Table
        $args['list_table'] = DB::table('all');
        $args['result'] = DB::getTableRows();

        Admin_Template::get_template(array('layout/header', 'layout/title', 'optimization', 'layout/footer'), $args);
    }

    /**
     * Save Setting
     */
    public function save()
    {
        global $wpdb;

        // Check Hash IP Update
        if (isset($_GET['hash-ips']) and intval($_GET['hash-ips']) == 1) {
            IP::Update_HashIP_Visitor();
            Helper::wp_admin_notice(__('IP Addresses replaced with hash values.', "wp-statistics"), "success");
        }

        // Update All GEO IP Country
        if (isset($_GET['populate']) and intval($_GET['populate']) == 1) {
            $result = GeoIP::Update_GeoIP_Visitor();
            Helper::wp_admin_notice($result['data'], ($result['status'] === false ? "error" : "success"));
        }

        // Re-install All DB Table
        if (isset($_GET['install']) and intval($_GET['install']) == 1) {
            Install::create_table(false);
            Helper::wp_admin_notice(__('Install routine complete.', "wp-statistics"), "success");
        }

        // Update Historical Value
        if (isset($_POST['historical-submit'])) {
            $historical_table = DB::table('historical');

            // Historical Visitors
            if (isset($_POST['wps_historical_visitors'])) {

                // Update DB
                $result = $wpdb->update($historical_table, array('value' => $_POST['wps_historical_visitors']), array('category' => 'visitors'));
                if ($result == 0) {
                    $result = $wpdb->insert($historical_table, array('value' => $_POST['wps_historical_visitors'], 'category' => 'visitors', 'page_id' => -1, 'uri' => '-1'));
                }
            }

            // Historical Visits
            if (isset($_POST['wps_historical_visits'])) {
                // Update DB
                $result = $wpdb->update($historical_table, array('value' => $_POST['wps_historical_visits']), array('category' => 'visits'));

                if ($result == 0) {
                    $result = $wpdb->insert($historical_table, array('value' => $_POST['wps_historical_visits'], 'category' => 'visits', 'page_id' => -2, 'uri' => '-2'));
                }
            }

            // Show Notice
            Helper::wp_admin_notice(__('Updated Historical Values.', "wp-statistics"), "success");
        }
    }

    /**
     * Optimize MySQL Table
     */
    public function optimize_table()
    {
        global $wpdb;

        if (Menus::in_page('optimization') and isset($_GET['optimize-table']) and !empty($_GET['optimize-table'])) {
            $tbl = trim($_GET['optimize-table']);
            if ($tbl == "all") {
                $tables = array_filter(array_values(DB::table('all')));
            } else {
                $tables = array_filter(array(DB::table($tbl)));
            }

            if (!empty($tables)) {
                $notice = '';
                $okay = true;

                // Use wp-admin/maint/repair.php
                foreach ($tables as $table) {
                    $check = $wpdb->get_row("CHECK TABLE $table");

                    if ('OK' === $check->Msg_text) {
                        /* translators: %s: Table name. */
                        $notice .= sprintf(__('The %s table is okay.', "wp-statistics"), "<code>$table</code>");
                        $notice .= '<br />';
                    } else {
                        $notice .= sprintf(__('The %1$s table is not okay. It is reporting the following error: %2$s. WordPress will attempt to repair this table&hellip;', "wp-statistics"), "<code>$table</code>", "<code>$check->Msg_text</code>");
                        $repair = $wpdb->get_row("REPAIR TABLE $table");

                        $notice .= '<br />';
                        if ('OK' === $repair->Msg_text) {
                            $notice .= sprintf(__('Successfully repaired the %s table.', "wp-statistics"), "<code>$table</code>");
                        } else {
                            $notice .= sprintf(__('Failed to repair the %1$s table. Error: %2$s', "wp-statistics"), "<code>$table</code>", "<code>$check->Msg_text</code>") . '<br />';
                            $problems[$table] = $check->Msg_text;
                            $okay = false;
                        }
                    }

                    if ($okay) {
                        $check = $wpdb->get_row("ANALYZE TABLE $table");
                        if ('Table is already up to date' === $check->Msg_text) {
                            $notice .= sprintf(__('The %s table is already optimized.', "wp-statistics"), "<code>$table</code>");
                            $notice .= '<br />';
                        } else {
                            $check = $wpdb->get_row("OPTIMIZE TABLE $table");
                            if ('OK' === $check->Msg_text || 'Table is already up to date' === $check->Msg_text) {
                                $notice .= sprintf(__('Successfully optimized the %s table.', 'wp-statistics'), "<code>$table</code>");
                                $notice .= '<br />';
                            } else {
                                $notice .= sprintf(__('The %1$s table does not support optimize, doing recreate + analyze instead.'), "<code>$table</code>");
                                $notice .= '<br />';
                            }
                        }
                    }
                }

                Helper::wp_admin_notice($notice, "info", $close_button = true, $id = false, $echo = true, $style_extra = 'padding:12px; line-height: 25px;');
            }
        }
    }
}

new optimization_page;