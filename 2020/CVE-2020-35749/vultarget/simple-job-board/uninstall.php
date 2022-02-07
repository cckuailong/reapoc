<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       https://wordpress.org/plugins/simple-job-board 
 *
 * @package    Simple_Job_Board
 * @since      1.0.0
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) { exit; }

// Delete Options -> Plugin Version
delete_option('sjb_version');

// Delete Options -> General Settings
delete_option('job_board_jobpost_slug');
delete_option('job_board_job_category_slug');
delete_option('job_board_job_type_slug');
delete_option('job_board_job_location_slug');

// Delete Options-> Appearance Settings 
delete_option('job_board_pages_layout');
delete_option('job_board_listing');
delete_option('job_board_listing_view');
delete_option('job_board_jobpost_content');
delete_option('job_board_container_class');
delete_option('job_board_container_id');
delete_option('job_board_typography');

// Delete Options-> Settings Feature & Application Form
delete_option('jobapp_settings_options');
delete_option('jobfeature_settings_options');

// Delete Options-> Search Filters
delete_option('job_board_category_filter');
delete_option('job_board_jobtype_filter');
delete_option('job_board_location_filter');
delete_option('job_board_search_bar');

// Delete Options-> Notifications
delete_option('job_board_admin_notification');
delete_option('job_board_applicant_notification');
delete_option('job_board_hr_notification');
delete_option('settings_hr_email');

// Delete Options-> Uploaded File Extension
delete_option('job_board_all_extensions_check');
delete_option('job_board_allowed_extensions');
delete_option('job_board_upload_file_ext');
delete_option('job_board_anti_hotlinking');