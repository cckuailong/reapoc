<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Submenu_Page_Calendars extends WPBS_Submenu_Page
{

    /**
     * Helper init method that runs on parent __construct
     *
     */
    protected function init()
    {

        add_action('admin_init', array($this, 'register_admin_notices'), 10);

    }

    /**
     * Callback method to register admin notices that are sent via URL parameters
     *
     */
    public function register_admin_notices()
    {

        if (empty($_GET['wpbs_message'])) {
            return;
        }

        // Calendar insert success
        wpbs_admin_notices()->register_notice('calendar_insert_success', '<p>' . __('Calendar created successfully.', 'wp-booking-system') . '</p>');

        // Calendar updated successfully
        wpbs_admin_notices()->register_notice('calendar_update_success', '<p>' . __('Calendar updated successfully.', 'wp-booking-system') . '</p>');

        // Calendar updated fail
        wpbs_admin_notices()->register_notice('calendar_update_fail', '<p>' . __('Something went wrong. Could not update the calendar.', 'wp-booking-system') . '</p>', 'error');

        // Calendar trash success
        wpbs_admin_notices()->register_notice('calendar_trash_success', '<p>' . __('Calendar successfully moved to Trash.', 'wp-booking-system') . '</p>');

        // Calendar restore success
        wpbs_admin_notices()->register_notice('calendar_restore_success', '<p>' . __('Calendar has been successfully restored.', 'wp-booking-system') . '</p>');

        // Calendar delete success
        wpbs_admin_notices()->register_notice('calendar_delete_success', '<p>' . __('Calendar has been successfully deleted.', 'wp-booking-system') . '</p>');

        // Booking Permanently Deleted
        wpbs_admin_notices()->register_notice('booking_permanently_delete_success', '<p>' . __('Booking successfully deleted.', 'wp-booking-system') . '</p>');

    }

    /**
     * Callback for the HTML output for the Calendar page
     *
     */
    public function output()
    {

        if (empty($this->current_subpage)) {
            include 'views/view-calendars.php';
        } else {

            if ($this->current_subpage == 'add-calendar') {
                if (count(wpbs_get_calendars()) > 0) {
                    include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium.php';
                } else {
                    include 'views/view-add-calendar.php';
                }

            }

            if ($this->current_subpage == 'edit-calendar') {
                include 'views/view-edit-calendar.php';
            }

            if ($this->current_subpage == 'view-legend') {
                include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium.php';
            }

            if ($this->current_subpage == 'ical-import-export') {
                include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium.php';
            }

            if ($this->current_subpage == 'upgrade-to-premium') {
               include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium.php';
            }

        }

    }

}
