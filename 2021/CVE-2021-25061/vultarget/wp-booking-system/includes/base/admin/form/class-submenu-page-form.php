<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Submenu_Page_Forms extends WPBS_Submenu_Page
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

        // Form insert success
        wpbs_admin_notices()->register_notice('form_insert_success', '<p>' . __('Form created successfully.', 'wp-booking-system') . '</p>');

        // Form trash success
        wpbs_admin_notices()->register_notice('form_trash_success', '<p>' . __('Form successfully moved to Trash.', 'wp-booking-system') . '</p>');

        // Form restore success
        wpbs_admin_notices()->register_notice('form_restore_success', '<p>' . __('Form has been successfully restored.', 'wp-booking-system') . '</p>');

        // Form delete success
        wpbs_admin_notices()->register_notice('form_delete_success', '<p>' . __('Form has been successfully deleted.', 'wp-booking-system') . '</p>');

        // Form edit success
        wpbs_admin_notices()->register_notice('form_edit_success', '<p>' . __('Form updated successfully.', 'wp-booking-system') . '</p>');

    }

    /**
     * Returns an array with the page tabs that should be displayed on the page
     *
     * @return array
     *
     */
    protected function get_tabs()
    {

        $tabs = array(
            'form-builder' => __('Form Builder', 'wp-booking-system'),
            'form-options' => __('Form Options', 'wp-booking-system'),
            'admin-notification' => __('Admin Notification', 'wp-booking-system'),
            'user-notification' => __('User Notification', 'wp-booking-system'),
            'form-confirmation' => __('Form Confirmation', 'wp-booking-system'),
            'form-strings' => __('Form Strings', 'wp-booking-system'),
        );

        /**
         * Filter the tabs before returning
         *
         * @param array $tabs
         *
         */
        return apply_filters('wpbs_submenu_page_edit_form_tabs', $tabs);

    }

    /**
     * Callback for the HTML output for the Calendar page
     *
     */
    public function output()
    {

        if (empty($this->current_subpage)) {
            include 'views/view-forms.php';
        } else {

            if ($this->current_subpage == 'add-form') {
				if (count(wpbs_get_forms()) > 0) {
                    include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium.php';
                } else {
                    include 'views/view-add-form.php';
                }
                
            }

            if ($this->current_subpage == 'edit-form') {
                include 'views/view-edit-form.php';
            }

        }

    }

}
