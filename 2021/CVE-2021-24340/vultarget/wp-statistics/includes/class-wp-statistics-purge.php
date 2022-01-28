<?php

namespace WP_STATISTICS;

class Purge
{

    public static function purge_data($purge_days)
    {
        global $wpdb;

        // Table Name
        $historical_table = DB::table('historical');

        // If it's less than 30 days, don't do anything.
        if ($purge_days > 30) {

            // Purge the visit data.
            $table_name = DB::table('visit');
            $date_string = TimeZone::getCurrentDate('Y-m-d', '-' . $purge_days);

            $result = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE `last_counter` < %s", $date_string));

            if ($result) {
                // Update the historical count with what we purged.
                $historical_result = $wpdb->query($wpdb->prepare("UPDATE {$historical_table} SET value = value + %d WHERE `category` = 'visits'", $result));

                // Insert
                if ($historical_result == 0) {
                    $insert = $wpdb->insert(
                        DB::table('historical'),
                        array(
                            'value' => $result,
                            'category' => 'visits',
                            'page_id' => -2,
                            'uri' => '-2',
                        )
                    );
                    if (!$insert) {
                        if (!empty($wpdb->last_error)) {
                            \WP_Statistics::log($wpdb->last_error);
                        }
                    }
                }

                $result_string = sprintf(__('%s data older than %s days purged successfully.', 'wp-statistics'), '<code>' . $table_name . '</code>', '<code>' . $purge_days . '</code>');
            } else {
                $result_string = sprintf(__('No records found to purge from %s!', 'wp-statistics'), '<code>' . $table_name . '</code>');
            }

            // Purge the visitors data.
            $table_name = DB::table('visitor');

            $result = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE `last_counter` < %s", $date_string));

            if ($result) {
                // Update the historical count with what we purged.
                $historical_result = $wpdb->query($wpdb->prepare("UPDATE {$historical_table} SET value = value + %d WHERE `category` = 'visitors'", $result));
                if ($historical_result == 0) {
                    $insert = $wpdb->insert(
                        DB::table('historical'),
                        array(
                            'value' => $result,
                            'category' => 'visitors',
                            'page_id' => -1,
                            'uri' => '-1',
                        )
                    );
                    if (!$insert) {
                        if (!empty($wpdb->last_error)) {
                            \WP_Statistics::log($wpdb->last_error);
                        }
                    }
                }

                $result_string .= '<br>' . sprintf(__('%s data older than %s days purged successfully.', 'wp-statistics'), '<code>' . $table_name . '</code>', '<code>' . $purge_days . '</code>');
            } else {
                $result_string .= '<br>' . sprintf(__('No records found to purge from %s!', 'wp-statistics'), '<code>' . $table_name . '</code>');
            }

            // Purge the exclusions data.
            $table_name = DB::table('exclusions');

            $result = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE `date` < %s", $date_string));

            if ($result) {
                $result_string .= '<br>' . sprintf(__('%s data older than %s days purged successfully.', 'wp-statistics'), '<code>' . $table_name . '</code>', '<code>' . $purge_days . '</code>');
            } else {
                $result_string .= '<br>' . sprintf(__('No records found to purge from %s!', 'wp-statistics'), '<code>' . $table_name . '</code>');
            }

            // Purge the search data.
            $table_name = DB::table('search');
            $result = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE `last_counter` < %s", $date_string));

            if ($result) {
                $result_string .= '<br>' . sprintf(__('%s data older than %s days purged successfully.', 'wp-statistics'), '<code>' . $table_name . '</code>', '<code>' . $purge_days . '</code>');
            } else {
                $result_string .= '<br>' . sprintf(__('No records found to purge from %s!', 'wp-statistics'), '<code>' . $table_name . '</code>');
            }

            // Purge the pages data, this is more complex as we want to save the historical data per page.
            $table_name = DB::table('pages');
            $historical = 0;

            // The first thing we need to do is update the historical data by finding all the unique pages.
            $result = $wpdb->get_results(
                $wpdb->prepare("SELECT DISTINCT uri FROM {$table_name} WHERE `date` < %s", $date_string)
            );

            // If we have a result, let's store the historical data.
            if ($result) {

                // Loop through all the unique rows that were returned.
                foreach ($result as $row) {

                    // Use the unique rows to get a total count from the database of all the data from the given URIs/Pageids that we're going to delete later.
                    $historical = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT sum(count) FROM {$table_name} WHERE `uri` = %s AND `date` < %s",
                            $row->uri,
                            $date_string
                        )
                    );

                    // Do an update of the historical data.
                    $uresult = $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE {$historical_table} SET `value` = value + %d WHERE `uri` = %s AND `category` = 'uri'",
                            $historical,
                            $row->uri,
                            $date_string
                        )
                    );

                    // If we failed it's because this is the first time we've seen this URI/pageid so let's create a historical row for it.
                    if ($uresult == 0) {
                        $insert = $wpdb->insert(
                            DB::table('historical'),
                            array(
                                'value' => $historical,
                                'category' => 'uri',
                                'uri' => $row->uri,
                                'page_id' => Pages::uri_to_id($row->uri),
                            )
                        );
                        if (!$insert) {
                            if (!empty($wpdb->last_error)) {
                                \WP_Statistics::log($wpdb->last_error);
                            }
                        }
                    }
                }
            }

            // Now that we've done all of the required historical data storage, we can actually delete the data from the database.
            $result = $wpdb->query($wpdb->prepare("DELETE FROM {$table_name} WHERE `date` < %s", $date_string));

            if ($result) {
                $result_string .= '<br>' . sprintf(__('%s data older than %s days purged successfully.', 'wp-statistics'), '<code>' . $table_name . '</code>', '<code>' . $purge_days . '</code>');
            } else {
                $result_string .= '<br>' . sprintf(__('No records found to purge from %s!', 'wp-statistics'), '<code>' . $table_name . '</code>');
            }

            // Send Email
            if (Option::get('prune_report') == true) {
                Helper::send_mail(Option::getEmailNotification(), __('Database pruned on', 'wp-statistics') . ' ' . get_bloginfo('name'), $result_string);
            }

            return $result_string;
        } else {
            return __('Please select a value over 30 days.', 'wp-statistics');
        }
    }

    public static function purge_visitor_hits($purge_hits)
    {
        global $wpdb;
        $visitor_table = DB::table('visitor');
        $visit_table = DB::table('visit');

        // If it's less than 10 hits, don't do anything.
        if ($purge_hits > 9) {

            // Purge the visitor's with more than the defined hits.
            $result = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$visitor_table} WHERE `hits` > %s", $purge_hits)
            );

            $to_delete = array();

            // Loop through the results and store the requried information in an array.  We don't just process it now as deleting
            // the rows from the visitor table will mess up the results from our first query.
            foreach ($result as $row) {
                $to_delete[] = array($row->ID, $row->last_counter, $row->hits);
            }
            if (count($to_delete) > 0) {
                foreach ($to_delete as $item) {

                    // First update the daily hit count.
                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE {$visit_table} SET `visit` = `visit` - %d WHERE `last_counter` = %s;",
                            $item[2],
                            $item[1]
                        )
                    );

                    // Next remove the visitor.  Note we can't do both in a single query, looks like $wpdb doesn't like executing them together.
                    $wpdb->query(
                        $wpdb->prepare("DELETE FROM {$visitor_table} WHERE `id` = %s;", $item[0])
                    );
                }

                $result_string = sprintf(
                    __('%s records purged successfully.', 'wp-statistics'),
                    '<code>' . count($to_delete) . '</code>'
                );
            } else {
                $result_string = __('No visitors found to purge.', 'wp-statistics');
            }
        } else {
            $result_string = __('Number of hits must be greater than or equal to 10!', 'wp-statistics');
        }

        if (Option::get('prune_report') == true) {
            Helper::send_mail(Option::getEmailNotification(), __('Database pruned on', 'wp-statistics') . ' ' . get_bloginfo('name'), $result_string);
        }

        return $result_string;
    }

}