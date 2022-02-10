<?php

/**
 * Description of BCronJob
 *
 * @author nur
 */
class SwpmCronJob {

    public function __construct() {
        add_action('swpm_account_status_event', array(&$this, 'update_account_status'));
        add_action('swpm_delete_pending_account_event', array(&$this, 'delete_pending_account'));
        add_action('swpm_delete_pending_account_event', array($this, 'delete_pending_email_activation_data'));
    }

    public function update_account_status() {
        global $wpdb;
        for ($counter = 0;; $counter += 100) {
            $query = $wpdb->prepare("SELECT member_id, membership_level, subscription_starts, account_state
                    FROM {$wpdb->prefix}swpm_members_tbl
                    WHERE membership_level NOT IN ( SELECT id FROM {$wpdb->prefix}swpm_membership_tbl
                                                WHERE subscription_period = '' OR subscription_period = '0' )
                    LIMIT %d, 100", $counter);
            $results = $wpdb->get_results($query);
            if (empty($results)) {
                break;
            }
            $expired = array();
            foreach ($results as $result) {
                $timestamp = SwpmUtils::get_expiration_timestamp($result);
                if ($timestamp < time() && $result->account_state == 'active') {
                    $expired[] = $result->member_id;
                }
            }
            if (count($expired) > 0) {
                $query = "UPDATE {$wpdb->prefix}swpm_members_tbl
                SET account_state='expired'  WHERE member_id IN (" . implode(',', $expired) . ")";
                $wpdb->query($query);
            }
        }
    }

    public function delete_pending_account() {
        global $wpdb;
        $interval = SwpmSettings::get_instance()->get_value('delete-pending-account');
        if (empty($interval)) {
            return;
        }
        for ($counter = 0;; $counter += 100) {
            $query = $wpdb->prepare("SELECT member_id
                                     FROM
                                        {$wpdb->prefix}swpm_members_tbl
                                    WHERE account_state='pending'
                                         AND subscription_starts < DATE_SUB(NOW(), INTERVAL %d MONTH) LIMIT %d, 100", $interval, $counter);
            $results = $wpdb->get_results($query);
            if (empty($results)) {
                break;
            }
            $to_delete = array();
            foreach ($results as $result) {
                $to_delete[] = $result->member_id;
            }
            if (count($to_delete) > 0) {
                SwpmLog::log_simple_debug("Auto deleting pending account.", true);
                $query = "DELETE FROM {$wpdb->prefix}swpm_members_tbl
                          WHERE member_id IN (" . implode(',', $to_delete) . ")";
                $wpdb->query($query);
            }
        }
    }

    public function delete_pending_email_activation_data() {
        //Delete pending email activation data after 1 day (24 hours).
        global $wpdb;
        $q = "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE '%swpm_email_activation_data_usr_%'";
        $res = $wpdb->get_results($q);
        if (empty($res)) {
            return;
        }
        foreach ($res as $data) {
            $value = unserialize($data->option_value);
            $timestamp = isset($value['timestamp']) ? $value['timestamp'] : 0;
            $now = time();
            if ($now > $timestamp + (60 * 60 * 24) ) {
                delete_option($data->option_name);
            }
        }
    }

}
