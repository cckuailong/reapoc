<?php
/**
 * This class sets the wp_cron with intervals
 */
class WooSEA_Cron {
        /**
         * Function for setting a cron job for regular creation of the feed
         * Will create a new event when an old one exists, which will be deleted first
         */
        function woosea_cron_scheduling ( $scheduling ) {
                if (!wp_next_scheduled( 'woosea_cron_hook' ) ) {
                        wp_schedule_event ( time(), 'hourly', 'woosea_cron_hook');
                } else {
                        wp_schedule_event ( time(), 'hourly', 'woosea_cron_hook');
                }
        }
}
