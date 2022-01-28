<?php

namespace WP_STATISTICS;

class Export
{

    /**
     * Setup an Export Report Class
     */
    public function __construct()
    {

        //Export Data
        add_action('admin_init', array($this, 'export_data'), 9);
    }

    /**
     * Set the headers to download the export file and then stop running WordPress.
     */
    public function export_data()
    {
        if (isset($_POST['wps_export'])) {
            global $wpdb;

            //Set Time Limit Script Run
            set_time_limit(0);

            //Check Wp Nonce and Require Field
            if (!isset($_POST['table-to-export']) || !isset($_POST['export-file-type']) || !isset($_POST['wps_export_file']) || !wp_verify_nonce($_POST['wps_export_file'], 'wp_statistics_export_nonce')) {
                exit;
            }

            // Load Library
            if (!class_exists('\ExportData')) {
                include(WP_STATISTICS_DIR . "includes/libraries/ExportData.php");
            }

            //Check Current User Capability
            if (User::Access('manage')) {
                $table = $_POST['table-to-export'];
                $type  = $_POST['export-file-type'];

                // Validate the table name the user passed to us.
                $allow_tbl = array("useronline", "visit", "visitor", "exclusions", "pages", "search");
                if (!in_array($table, $allow_tbl)) {
                    $table = false;
                }

                // Validate the file type the user passed to us.
                if (!($type == "xml" || $type == "csv" || $type == "tsv")) {
                    $table = false;
                }

                if ($table && $type) {
                    $file_name = 'wp-statistics' . '-' . TimeZone::getCurrentDate('Y-m-d-H-i');

                    switch ($type) {
                        case 'xml':
                            $exporter = new \ExportDataExcel('browser', "{$file_name}.xml");
                            break;
                        case 'csv':
                            $exporter = new \ExportDataCSV('browser', "{$file_name}.csv");
                            break;
                        case 'tsv':
                            $exporter = new \ExportDataTSV('browser', "{$file_name}.tsv");
                            break;
                    }

                    $exporter->initialize();

                    // We need to limit the number of results we retrieve to ensure we don't run out of memory
                    $query_base = "SELECT * FROM " . DB::table($table);
                    $query      = $query_base . ' LIMIT 0,1000';

                    $i            = 1;
                    $more_results = true;
                    $result       = $wpdb->get_results($query, ARRAY_A);

                    // If we didn't get any rows, don't output anything.
                    if (count($result) < 1) {
                        echo "No data in table!";
                        exit;
                    }

                    if (isset($_POST['export-headers']) and $_POST['export-headers']) {
                        foreach ($result[0] as $key => $col) {
                            $columns[] = $key;
                        }
                        $exporter->addRow($columns);
                    }

                    while ($more_results) {
                        foreach ($result as $row) {
                            $exporter->addRow($row);

                            // Make sure we've flushed the output buffer so we don't run out of memory on large exports.
                            ob_flush();
                            flush();
                        }

                        unset($result);
                        $wpdb->flush();

                        $query  = $query_base . ' LIMIT ' . ($i * 1000) . ',1000';
                        $result = $wpdb->get_results($query, ARRAY_A);

                        if (count($result) == 0) {
                            $more_results = false;
                        }

                        $i++;
                    }

                    $exporter->finalize();
                    exit;
                }
            }
        }
    }

}

new Export;